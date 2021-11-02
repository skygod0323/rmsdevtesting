<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id']<1)
	{
		header("HTTP/1.0 403 Forbidden");
		die('Access denied');
	}
	header("Content-Type: text/plain; charset=utf8");
}

require_once "setup.php";
require_once "functions_base.php";
require_once "functions_servers.php";
require_once "functions.php";
require_once "database_selectors.php";
require_once "setup_smarty_site.php";

if (!is_file("$config[project_path]/admin/data/system/cron_stats.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_stats.lock", "1", LOCK_EX);
}

$lock=fopen("$config[project_path]/admin/data/system/cron_stats.lock","r+");
if (!flock($lock,LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors',1);
sql("set low_priority_updates=1");

$options=get_options(array('LIMIT_MEMORY','ANTI_HOTLINK_N_HOURS','ANTI_HOTLINK_N_VIDEOS','ANTI_HOTLINK_OWN_IP','ANTI_HOTLINK_WHITE_IPS','ACTIVITY_INDEX_FORMULA','ENABLE_TOKENS_TRAFFIC_VIDEOS','ENABLE_TOKENS_TRAFFIC_ALBUMS','ENABLE_TOKENS_TRAFFIC_EMBEDS'));

$memory_limit=intval($options['LIMIT_MEMORY']);
if ($memory_limit==0)
{
	$memory_limit=512;
}
ini_set('memory_limit',"{$memory_limit}M");

$list_referers=mr2array(sql("select referer_id, referer from $config[tables_prefix_multi]stats_referers_list"));
foreach ($list_referers as $k=>$v)
{
	$list_referers[$k]['referer']=str_replace(array("https://", "http://www."),"http://",$list_referers[$k]['referer']);
}

$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
if ($stats_settings['collect_traffic_stats']==0)
{
	$stats_settings['collect_traffic_stats_countries']=0;
	$stats_settings['collect_traffic_stats_embed_domains']=0;
}
if ($stats_settings['collect_videos_stats']==0)
{
	$stats_settings['collect_videos_stats_unique']=0;
	$stats_settings['collect_videos_stats_video_files']=0;
}
if ($stats_settings['collect_albums_stats']==0)
{
	$stats_settings['collect_albums_stats_unique']=0;
	$stats_settings['collect_albums_stats_album_images']=0;
}
if ($stats_settings['collect_memberzone_stats']==0)
{
	$stats_settings['collect_memberzone_stats_video_files']=0;
	$stats_settings['collect_memberzone_stats_album_images']=0;
}
if (strpos($options['ACTIVITY_INDEX_FORMULA'], "%unique_videos_visited%")!==false || strpos($options['ACTIVITY_INDEX_FORMULA'], "%unique_albums_visited%")!==false)
{
	$stats_settings['collect_memberzone_stats']=1;
}
if (intval($options['ENABLE_TOKENS_TRAFFIC_VIDEOS'])==1)
{
	$stats_settings['collect_videos_stats_unique']=1;
}
if (intval($options['ENABLE_TOKENS_TRAFFIC_ALBUMS'])==1)
{
	$stats_settings['collect_albums_stats_unique']=1;
}
if (intval($options['ENABLE_TOKENS_TRAFFIC_EMBEDS'])==1)
{
	$stats_settings['collect_embeds_stats_unique']=1;
}

$file="$config[project_path]/admin/data/stats/temp.dat";
$max_filesize=150*1024*1024;

log_output("INFO  Stats processor started");
log_output("INFO  Memory limit: ".ini_get('memory_limit'));

$now_date=date("Y-m-d H:i:s");

// =====================================================================================================================
// close sms, manual, tokens, expired and api transactions
// =====================================================================================================================

if ($config['is_clone_db']<>"true")
{
	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));

	$list_ids=mr2array_list(sql("select distinct user_id from $config[tables_prefix]bill_transactions where status_id=1 and access_end_date<'$now_date' and is_unlimited_access=0 and (bill_type_id in (1,3,4) or is_auto_expire=1)"));
	if (count($list_ids)>0)
	{
		sql("update $config[tables_prefix]bill_transactions set status_id=2 where status_id=1 and access_end_date<'$now_date' and is_unlimited_access=0 and (bill_type_id in (1,3,4) or is_auto_expire=1)");
		foreach ($list_ids as $user_id)
		{
			sql_pr("update $config[tables_prefix]users set status_id=? where status_id=3 and user_id=?",intval($memberzone_data['STATUS_AFTER_PREMIUM']),$user_id);
			sql_pr("insert into $config[tables_prefix]bill_log set internal_provider_id='cron', message_type=1, message_text=?, added_date=?", "Expire of subscription for User_$user_id ", date("Y-m-d H:i:s"));
		}
		log_output("INFO  Expired ".count($list_ids)." transactions");
	}

	$list_internal_transactions=mr2array(sql("select transaction_id, external_package_id, user_id, duration_rebill, (select price_rebill from $config[tables_prefix]card_bill_packages where package_id=$config[tables_prefix]bill_transactions.external_package_id) as price_rebill, (select tokens_available from $config[tables_prefix]users where user_id=$config[tables_prefix]bill_transactions.user_id) as tokens_available from $config[tables_prefix]bill_transactions where status_id=1 and internal_provider_id='tokens' and access_end_date<'$now_date' and is_unlimited_access=0"));
	foreach ($list_internal_transactions as $transaction)
	{
		if (intval($transaction['duration_rebill'])>0 && intval($transaction['price_rebill'])>0 && intval($transaction['price_rebill'])<=intval($transaction['tokens_available']))
		{
			$access_start_date=date("Y-m-d H:i:s");
			$access_end_date=date("Y-m-d H:i:s",mktime(date("H"),date("i"),date("s"),date("m"),date("d")+intval($transaction['duration_rebill']),date("Y")));

			sql_pr("insert into $config[tables_prefix]bill_transactions set internal_provider_id='tokens', bill_type_id=2, status_id=1, type_id=3, external_package_id=?, duration_rebill=?, access_start_date=?, access_end_date=?, user_id=?, price=?, currency_code='TOK'",
				intval($transaction['external_package_id']),intval($transaction['duration_rebill']),$access_start_date,$access_end_date,intval($transaction['user_id']),intval($transaction['price_rebill'])
			);
			sql_pr("update $config[tables_prefix]bill_transactions set status_id=2 where transaction_id=?",intval($transaction['transaction_id']));
			sql_pr("update $config[tables_prefix]users set status_id=3, tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",intval($transaction['price_rebill']),intval($transaction['user_id']));
		} else {
			sql_pr("update $config[tables_prefix]bill_transactions set status_id=2 where transaction_id=?",intval($transaction['transaction_id']));
			sql_pr("update $config[tables_prefix]users set status_id=? where status_id=3 and user_id=?",intval($memberzone_data['STATUS_AFTER_PREMIUM']),intval($transaction['user_id']));
		}
	}

	$subscriptions = mr2array(sql_pr("
		select
			up.*
		from
			$config[tables_prefix]users_purchases up
			inner join $config[tables_prefix]users_subscriptions us on up.user_id=us.user_id and ((us.subscribed_type_id=1 and us.subscribed_object_id=up.profile_id) or (us.subscribed_type_id=5 and us.subscribed_object_id=up.dvd_id))
		where
			up.is_recurring=1 and up.expiry_date<=? and up.expiry_date>?",
		date("Y-m-d H:i:s", time() + 3600), date("Y-m-d H:i:s", time() - 86400)
	));

	foreach ($subscriptions as $subscription)
	{
		$s_tokens = intval($subscription['tokens']);
		$s_assign_tokens = intval($subscription['tokens']) - intval($subscription['tokens_revenue']);
		$s_duration = strtotime($subscription['expiry_date']) - strtotime($subscription['added_date']);

		if ($s_tokens > 0)
		{
			$s_tokens_available = mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", intval($subscription['user_id'])));
			if ($s_tokens_available >= $s_tokens)
			{
				$s_award_type_id = 0;
				$s_purchase_table_key = '';
				$s_object = array();
				if (intval($subscription['profile_id']) > 0)
				{
					$s_award_type_id = 13;
					$s_purchase_table_key = 'profile_id';
					$s_object = mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?", intval($subscription['profile_id'])));
					if (intval($s_object['user_id'])<1)
					{
						continue;
					}
				} elseif (intval($subscription['dvd_id']) > 0)
				{
					$s_award_type_id = 14;
					$s_purchase_table_key = 'dvd_id';
					$s_object = mr2array_single(sql_pr("select * from $config[tables_prefix]dvds where dvd_id=?", intval($subscription['dvd_id'])));
					if (intval($s_object['dvd_id'])<1)
					{
						continue;
					}
				}
				$s_added_date = $subscription['expiry_date'];
				$s_expiry_date = date('Y-m-d H:i:s', strtotime($s_added_date) + $s_duration);
				if ($s_purchase_table_key)
				{
					if ($s_assign_tokens > 0 && intval($s_object['user_id']) > 0 && $s_award_type_id)
					{
						$s_exclude_users = array_map('trim', explode(",", $memberzone_data['TOKENS_SALE_EXCLUDES']));
						$s_username = mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?", intval($s_object['user_id'])));
						if ($s_username && in_array($s_username, $s_exclude_users))
						{
							$s_assign_tokens = 0;
						}

						if ($s_assign_tokens > 0)
						{
							sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $s_assign_tokens, intval($s_object['user_id']));
							sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=?, user_id=?, $s_purchase_table_key=?, tokens_granted=?, added_date='$now_date'", $s_award_type_id, $s_object['user_id'], intval($subscription[$s_purchase_table_key]), $s_assign_tokens);
						}
					}

					sql_pr("insert into $config[tables_prefix]users_purchases set is_recurring=1, $s_purchase_table_key=?, subscription_id=?, user_id=?, owner_user_id=?, tokens=?, tokens_revenue=?, added_date=?, expiry_date=?", intval($subscription[$s_purchase_table_key]), intval($subscription['subscription_id']), intval($subscription['user_id']), intval($s_object['user_id']), $s_tokens, $s_tokens - $s_assign_tokens, $s_added_date, $s_expiry_date);
					sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$s_tokens, 0) where user_id=?", intval($subscription['user_id']));
					sql_pr("update $config[tables_prefix]users_purchases set is_recurring=0, subscription_id=0 where purchase_id=?", intval($subscription['purchase_id']));
					log_output("INFO  Rebilled subscription $subscription[purchase_id]");
				}
			} else
			{
				log_output("WARNING  Not enough tokens to rebill purchase $subscription[purchase_id]");
			}
		}
	}
}

// =====================================================================================================================
// incoming stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/in.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of in.dat is more than allowed, skipping");
	@unlink($file);
}

if (intval($stats_settings['collect_traffic_stats'])==0)
{
	log_output("INFO  Incoming traffic stats are disabled");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("|",$res,7);

	$date=trim($res[0]);
	$is_uniq=intval($res[1]);
	$country_code=trim($res[2]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[3]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[4]);
	$stats_mode=intval($res[5]);
	$device=intval($res[6]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	$referer_id=0;
	foreach ($list_referers as $ref)
	{
		if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
		{
			$referer_id=$ref['referer_id'];
			break;
		}
	}

	if ($stats_mode==0)
	{
		$result[$date][$country_code][$referer_id][$device]['summary_amount']++;
	} elseif ($is_uniq==1)
	{
		$result[$date][$country_code][$referer_id][$device]['uniq_amount']++;
	} else {
		$result[$date][$country_code][$referer_id][$device]['raw_amount']++;
	}
}

foreach ($result as $date => $result_date)
{
	foreach ($result_date as $country_code => $result_country_code)
	{
		foreach ($result_country_code as $referer_id => $result_referer)
		{
			foreach ($result_referer as $device => $amount)
			{
				settype($referer_id, "integer");
				settype($device, "integer");

				$uniq_amount = intval($amount['uniq_amount']);
				$raw_amount = intval($amount['raw_amount']);
				$summary_amount = intval($amount['summary_amount']);

				if (sql_update("update $config[tables_prefix_multi]stats_in set uniq_amount=uniq_amount+?, raw_amount=raw_amount+?, summary_amount=summary_amount+? where referer_id=? and country_code=? and device=? and added_date=?", $uniq_amount, $raw_amount, $summary_amount, $referer_id, $country_code, $device, $date) == 0)
				{
					sql_insert("insert into $config[tables_prefix_multi]stats_in set uniq_amount=?, raw_amount=?, summary_amount=?, referer_id=?, country_code=?, device=?, added_date=?", $uniq_amount, $raw_amount, $summary_amount, $referer_id, $country_code, $device, $date);
				}
			}
		}
	}
}
log_output("INFO  Processed incoming stats: " . (count($data)-1));

// =====================================================================================================================
// embed code stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/embed.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of embed.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$result_domain=array();
$result_video=array();
$result_video_unique=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("|",$res,6);

	$date=trim($res[0]);
	$country_code=trim($res[1]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[2]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$video_id=intval($res[3]);
	$ip=ip2int(trim($res[4]));
	$device=intval($res[5]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	$referer_id=0;
	foreach ($list_referers as $ref)
	{
		if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
		{
			$referer_id=$ref['referer_id'];
			break;
		}
	}

	$result[$date][$country_code][$referer_id][$device]++;
	if ($video_id>0)
	{
		$result_video[$date][$video_id]++;

		if (intval($stats_settings['collect_embeds_stats_unique'])==1)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_visits where ip=? and video_id=? and flag=2",$ip,$video_id))==0)
			{
				$result_video_unique[$date][$video_id]++;
				sql_pr("insert into $config[tables_prefix]videos_visits set ip=?, video_id=?, flag=2, added_date='$now_date'",$ip,$video_id);
			}
		}
	}

	if (intval($stats_settings['collect_traffic_stats_embed_domains'])==1)
	{
		$parsed_ref=parse_url($referer);
		if (is_array($parsed_ref))
		{
			$result_domain[$date][$parsed_ref['host']]++;
		}
	}
}

if (intval($stats_settings['collect_traffic_stats'])==1)
{
	foreach ($result as $date => $result_date)
	{
		foreach ($result_date as $country_code => $result_country_code)
		{
			foreach ($result_country_code as $referer_id => $result_referer)
			{
				foreach ($result_referer as $device => $amount)
				{
					settype($referer_id, "integer");
					settype($device, "integer");
					settype($amount, "integer");

					if (sql_update("update $config[tables_prefix_multi]stats_in set view_embed_amount=view_embed_amount+? where referer_id=? and country_code=? and device=? and added_date=?", $amount, $referer_id, $country_code, $device, $date) == 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_in set view_embed_amount=?, referer_id=?, country_code=?, device=?, added_date=?", $amount, $referer_id, $country_code, $device, $date);
					}
				}
			}
		}
	}

	foreach ($result_domain as $date=>$result_date)
	{
		foreach ($result_date as $domain=>$amount)
		{
			settype($amount,"integer");

			if (sql_update("update $config[tables_prefix_multi]stats_embed set amount=amount+? where domain=? and added_date=?",$amount,$domain,$date)==0)
			{
				sql_pr("insert into $config[tables_prefix_multi]stats_embed set amount=?, domain=?, added_date=?",$amount,$domain,$date);
			}
		}
	}
}

foreach ($result_video as $date=>$result_date)
{
	foreach ($result_date as $video_id=>$amount)
	{
		settype($amount,"integer");
		$unique_viewed=intval($result_video_unique[$date][$video_id]);

		sql_pr("update $config[tables_prefix]videos set embed_viewed=embed_viewed+?, embed_viewed_unique=embed_viewed_unique+? where video_id=?",$amount,$unique_viewed,$video_id);

		if (intval($stats_settings['collect_traffic_stats'])==1)
		{
			if (sql_update("update $config[tables_prefix]stats_videos set embed_requested=embed_requested+?, unique_embed_requested=unique_embed_requested+? where video_id=? and added_date=?",$amount,$unique_viewed,$video_id,$date)==0)
			{
				sql_pr("insert into $config[tables_prefix]stats_videos set embed_requested=?, unique_embed_requested=?, video_id=?, added_date=?",$amount,$unique_viewed,$video_id,$date);
			}
		}
	}
}

log_output("INFO  Processed embed code stats: " . (count($data)-1));

// =====================================================================================================================
// cs out stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/cs_out.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of cs_out.dat is more than allowed, skipping");
	@unlink($file);
}

if (intval($stats_settings['collect_traffic_stats'])==0)
{
	log_output("INFO  Content source out traffic stats are disabled");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("|",$res,6);

	$date=trim($res[0]);
	$cs_id=intval($res[1]);
	$country_code=trim($res[2]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[3]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[4]);
	$device=intval($res[5]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400 || $cs_id < 1)
	{
		continue;
	}

	$referer_id=0;
	foreach ($list_referers as $ref)
	{
		if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
		{
			$referer_id=$ref['referer_id'];
			break;
		}
	}

	$result[$date][$cs_id][$country_code][$referer_id][$device]++;
}

foreach ($result as $date => $result_date)
{
	foreach ($result_date as $cs_id => $result_cs)
	{
		foreach ($result_cs as $country_code => $result_country_code)
		{
			foreach ($result_country_code as $referer_id => $result_referer)
			{
				foreach ($result_referer as $device => $amount)
				{
					settype($referer_id, "integer");
					settype($amount, "integer");
					settype($cs_id, "integer");
					settype($device, "integer");

					if (sql_update("update $config[tables_prefix_multi]stats_cs_out set amount=amount+? where content_source_id=? and referer_id=? and country_code=? and device=? and added_date=?", $amount, $cs_id, $referer_id, $country_code, $device, $date) == 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_cs_out set amount=?, content_source_id=?, referer_id=?, country_code=?, device=?, added_date=?", $amount, $cs_id, $referer_id, $country_code, $device, $date);
					}

					if (sql_update("update $config[tables_prefix_multi]stats_in set cs_out_amount=cs_out_amount+? where referer_id=? and country_code=? and device=? and added_date=?", $amount, $referer_id, $country_code, $device, $date) == 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_in set cs_out_amount=?, referer_id=?, country_code=?, device=?, added_date=?", $amount, $referer_id, $country_code, $device, $date);
					}
				}
			}
		}
	}
}

log_output("INFO  Processed content source out stats: " . (count($data)-1));

// =====================================================================================================================
// advertising out stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/adv_out.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of adv_out.dat is more than allowed, skipping");
	@unlink($file);
}

if (intval($stats_settings['collect_traffic_stats'])==0)
{
	log_output("INFO  Advertising out traffic stats are disabled");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("|",$res,6);

	$date=trim($res[0]);
	$adv_id=intval($res[1]);
	$country_code=trim($res[2]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[3]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[4]);
	$device=intval($res[5]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400 || $adv_id < 1)
	{
		continue;
	}

	$referer_id=0;
	foreach ($list_referers as $ref)
	{
		if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
		{
			$referer_id=$ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
		{
			$referer_id=$ref['referer_id'];
			break;
		}
	}

	$result[$date][$adv_id][$country_code][$referer_id][$device]++;
}

foreach ($result as $date => $result_date)
{
	foreach ($result_date as $adv_id => $result_adv)
	{
		foreach ($result_adv as $country_code => $result_country_code)
		{
			foreach ($result_country_code as $referer_id => $result_referer)
			{
				foreach ($result_referer as $device => $amount)
				{
					settype($referer_id, "integer");
					settype($amount, "integer");
					settype($adv_id, "integer");
					settype($device, "integer");

					if (sql_update("update $config[tables_prefix_multi]stats_adv_out set amount=amount+? where advertisement_id=? and referer_id=? and country_code=? and device=? and added_date=?", $amount, $adv_id, $referer_id, $country_code, $device, $date) == 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_adv_out set amount=?, advertisement_id=?, referer_id=?, country_code=?, device=?, added_date=?", $amount, $adv_id, $referer_id, $country_code, $device, $date);
					}

					if (sql_update("update $config[tables_prefix_multi]stats_in set adv_out_amount=adv_out_amount+? where referer_id=? and country_code=? and device=? and added_date=?", $amount, $referer_id, $country_code, $device, $date) == 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_in set adv_out_amount=?, referer_id=?, country_code=?, device=?, added_date=?", $amount, $referer_id, $country_code, $device, $date);
					}
				}
			}
		}
	}
}

log_output("INFO  Processed advertising out stats: " . (count($data)-1));

// =====================================================================================================================
// player stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/player.dat", $file);
if (@filesize($file) > $max_filesize)
{
	log_output("ERROR  Size of player.dat is more than allowed, skipping");
	@unlink($file);
}

if (intval($stats_settings['collect_player_stats']) == 0)
{
	log_output("INFO  Player traffic stats are disabled");
	@unlink($file);
}

$result = array();
$data = explode("\n", @file_get_contents($file));

foreach ($data as $res)
{
	[$date, $is_embed, $event, $adv_id, $country_code, $referer, $query_params, $device, $embed_profile_id] = array_map('trim', explode("|", trim($res), 9));
	if (intval($stats_settings['collect_player_stats_countries']) == 0)
	{
		$country_code = '';
	}
	$referer = str_replace(array("https://", "http://www."), "http://", $referer);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	$referer_id = 0;
	foreach ($list_referers as $ref)
	{
		if (strtolower($ref['referer']) == '<bookmarks>' && $referer == '')
		{
			$referer_id = $ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'], "http://") === 0 && strpos($referer, $ref['referer']) === 0)
		{
			$referer_id = $ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'], "http://") !== 0 && strpos($query_params, $ref['referer']) !== false)
		{
			$referer_id = $ref['referer_id'];
			break;
		}
	}

	$result[$date][intval($is_embed)][$embed_profile_id][$country_code][intval($device)][$referer_id][strtolower($event)][strtolower($adv_id)]++;
}

foreach ($result as $date => $result_date)
{
	foreach ($result_date as $is_embed => $result_embed_profiles)
	{
		foreach ($result_embed_profiles as $embed_profile_id => $result_country_code)
		{
			foreach ($result_country_code as $country_code => $result_device)
			{
				foreach ($result_device as $device => $result_referer)
				{
					foreach ($result_referer as $referer_id => $result_event)
					{
						$player_loads = 0;
						$player_fullscreens = 0;
						$player_mutes = 0;
						$player_unmutes = 0;
						$video_starts = 0;
						$video_pauses = 0;
						$video_skips = 0;
						$video_ends = 0;
						$video_errors = 0;
						$start_ad_views = 0;
						$start_ad_clicks = 0;
						$start_ad_errors = 0;
						$pre_ad_views = 0;
						$pre_ad_clicks = 0;
						$pre_ad_skips = 0;
						$pre_ad_errors = 0;
						$post_ad_views = 0;
						$post_ad_clicks = 0;
						$post_ad_skips = 0;
						$post_ad_errors = 0;
						$pause_ad_views = 0;
						$pause_ad_clicks = 0;
						$pause_ad_errors = 0;

						foreach ($result_event as $event => $result_adv_id)
						{
							foreach ($result_adv_id as $adv_id => $amount)
							{
								settype($amount, 'integer');

								switch ($event)
								{
									case 'playerload':
										$player_loads += $amount;
										break;
									case 'playermute':
										$player_mutes += $amount;
										break;
									case 'playerunmute':
										$player_unmutes += $amount;
										break;
									case 'playerfullscreen':
										$player_fullscreens += $amount;
										break;
									case 'videoplay':
										$video_starts += $amount;
										break;
									case 'videopause':
										$video_pauses += $amount;
										break;
									case 'videoskip':
										$video_skips += $amount;
										break;
									case 'videocomplete':
										$video_ends += $amount;
										break;
									case 'videoerror':
										$video_errors += $amount;
										break;
									case 'advertisingshow':
										if ($adv_id == 'startad')
										{
											$start_ad_views += $amount;
										} elseif ($adv_id == 'prerollad')
										{
											$pre_ad_views += $amount;
										} elseif ($adv_id == 'postrollad' || $adv_id == 'postpauserollad')
										{
											$post_ad_views += $amount;
										} elseif ($adv_id == 'pausead')
										{
											$pause_ad_views += $amount;
										}
										break;
									case 'advertisingclick':
										if ($adv_id == 'startad')
										{
											$start_ad_clicks += $amount;
										} elseif ($adv_id == 'prerollad')
										{
											$pre_ad_clicks += $amount;
										} elseif ($adv_id == 'postrollad' || $adv_id == 'postpauserollad')
										{
											$post_ad_clicks += $amount;
										} elseif ($adv_id == 'pausead')
										{
											$pause_ad_clicks += $amount;
										}
										break;
									case 'advertisingerror':
										if ($adv_id == 'startad')
										{
											$start_ad_errors += $amount;
										} elseif ($adv_id == 'prerollad')
										{
											$pre_ad_errors += $amount;
										} elseif ($adv_id == 'postrollad' || $adv_id == 'postpauserollad')
										{
											$post_ad_errors += $amount;
										} elseif ($adv_id == 'pausead')
										{
											$pause_ad_errors += $amount;
										}
										break;
									case 'advertisingskip':
										if ($adv_id == 'prerollad')
										{
											$pre_ad_skips += $amount;
										} elseif ($adv_id == 'postrollad' || $adv_id == 'postpauserollad')
										{
											$post_ad_skips += $amount;
										}
										break;
								}
							}
						}

						if (sql_update("update $config[tables_prefix_multi]stats_player set player_loads=player_loads+?, player_fullscreens=player_fullscreens+?, player_mutes=player_mutes+?, player_unmutes=player_unmutes+?, video_starts=video_starts+?, video_pauses=video_pauses+?, video_skips=video_skips+?, video_ends=video_ends+?, video_errors=video_errors+?, start_ad_views=start_ad_views+?, start_ad_clicks=start_ad_clicks+?, start_ad_errors=start_ad_errors+?, pre_ad_views=pre_ad_views+?, pre_ad_clicks=pre_ad_clicks+?, pre_ad_skips=pre_ad_skips+?, pre_ad_errors=pre_ad_errors+?, post_ad_views=post_ad_views+?, post_ad_clicks=post_ad_clicks+?, post_ad_skips=post_ad_skips+?, post_ad_errors=post_ad_errors+?, pause_ad_views=pause_ad_views+?, pause_ad_clicks=pause_ad_clicks+?, pause_ad_errors=pause_ad_errors+? where is_embed=? and embed_profile_id=? and country_code=? and device=? and referer_id=? and added_date=?",
										$player_loads, $player_fullscreens, $player_mutes, $player_unmutes, $video_starts, $video_pauses, $video_skips, $video_ends, $video_errors, $start_ad_views, $start_ad_clicks, $start_ad_errors, $pre_ad_views, $pre_ad_clicks, $pre_ad_skips, $pre_ad_errors, $post_ad_views, $post_ad_clicks, $post_ad_skips, $post_ad_errors, $pause_ad_views, $pause_ad_clicks, $pause_ad_errors, $is_embed, $embed_profile_id, $country_code, $device, $referer_id, $date) == 0)
						{
							sql_insert("insert into $config[tables_prefix_multi]stats_player set player_loads=?, player_fullscreens=?, player_mutes=?, player_unmutes=?, video_starts=?, video_pauses=?, video_skips=?, video_ends=?, video_errors=?, start_ad_views=?, start_ad_clicks=?, start_ad_errors=?, pre_ad_views=?, pre_ad_clicks=?, pre_ad_skips=?, pre_ad_errors=?, post_ad_views=?, post_ad_clicks=?, post_ad_skips=?, post_ad_errors=?, pause_ad_views=?, pause_ad_clicks=?, pause_ad_errors=?, is_embed=?, embed_profile_id=?, country_code=?, device=?, referer_id=?, added_date=?",
									$player_loads, $player_fullscreens, $player_mutes, $player_unmutes, $video_starts, $video_pauses, $video_skips, $video_ends, $video_errors, $start_ad_views, $start_ad_clicks, $start_ad_errors, $pre_ad_views, $pre_ad_clicks, $pre_ad_skips, $pre_ad_errors, $post_ad_views, $post_ad_clicks, $post_ad_skips, $post_ad_errors, $pause_ad_views, $pause_ad_clicks, $pause_ad_errors, $is_embed, $embed_profile_id, $country_code, $device, $referer_id, $date);
						}
					}
				}
			}
		}
	}
}

log_output("INFO  Processed player stats: " . (count($data) - 1));

// =====================================================================================================================
// videos stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/videos_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of videos_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$result_date=array();
$result_video_viewed=array();
$result_video_viewed_unique=array();
$result_video_viewed_player=array();
$result_user_video_viewed=array();
$result_user_video_watched=array();
$result_user_video_watched_log=array();
$result_user_video_watched_unique=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res,9);
	$res[0]=trim($res[0]);

	settype($res[1],"integer");
	if (strlen($res[0])<1) {continue;}

	$stats_mode=intval($res[2]);
	$country_code=trim($res[3]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[4]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[5]);
	$date_full=trim($res[6]);
	$date=date("Y-m-d",strtotime($date_full));
	$ip=ip2int(trim($res[7]));
	$device=intval($res[8]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	if ($stats_mode==1)
	{
		$referer_id=0;
		foreach ($list_referers as $ref)
		{
			if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
			{
				$referer_id=$ref['referer_id'];
				break;
			}
		}
		$result_video_viewed[$date][$country_code][$referer_id][$device]['view_video_amount']++;

		if (intval($stats_settings['collect_videos_stats_unique'])==1)
		{
			$video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir]) limit 1",$res[0],$res[0]));
			if ($video_id>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_visits where ip=? and video_id=? and flag=0",$ip,$video_id))==0)
			{
				$result_video_viewed_unique[$video_id]++;
				sql_pr("insert into $config[tables_prefix]videos_visits set ip=?, video_id=?, flag=0, added_date='$now_date'",$ip,$video_id);
			}
		}
	} else {
		$result[$res[0]]++;
		$result_date[$res[0]]=$date_full;

		if ($res[1]>0)
		{
			if (intval($stats_settings['collect_memberzone_stats'])==1)
			{
				$video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir]) limit 1",$res[0],$res[0]));
				$result_user_video_watched[$res[1]]++;
				$result_user_video_watched_log[]=array('video_id'=>$video_id,'user_id'=>$res[1],'added_date'=>$date_full);
			}
		}
	}
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$video_info=mr2array_single(sql_pr("select video_id, user_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",$k,$k));
	if ($video_info['video_id']>0)
	{
		$result[$video_info['video_id']]=$v;
		if (isset($result_date[$k]))
		{
			$value=$result_date[$k];
			unset($result_date[$k]);
			$result_date[$video_info['video_id']]=$value;
		}
		if (intval($stats_settings['collect_memberzone_stats'])==1)
		{
			$result_user_video_viewed[$video_info['user_id']]+=$v;
		}
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/videos_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of videos_id.dat is more than allowed, skipping");
	@unlink($file);
}

$result_video_temp=array();
$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res,9);
	settype($res[0],"integer");
	settype($res[1],"integer");
	if ($res[0]<1) {continue;}

	$stats_mode=intval($res[2]);
	$country_code=trim($res[3]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[4]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[5]);
	$date_full=trim($res[6]);
	$date=date("Y-m-d",strtotime($date_full));
	$ip=ip2int(trim($res[7]));
	$device=intval($res[8]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	if ($stats_mode==1)
	{
		$referer_id=0;
		foreach ($list_referers as $ref)
		{
			if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
			{
				$referer_id=$ref['referer_id'];
				break;
			}
		}
		$result_video_viewed[$date][$country_code][$referer_id][$device]['view_video_amount']++;

		if (intval($stats_settings['collect_videos_stats_unique'])==1)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_visits where ip=? and video_id=? and flag=0",$ip,$res[0]))==0)
			{
				$result_video_viewed_unique[$res[0]]++;
				sql_pr("insert into $config[tables_prefix]videos_visits set ip=?, video_id=?, flag=0, added_date='$now_date'",$ip,$res[0]);
			}
		}
	} else {
		$result[$res[0]]++;
		$result_date[$res[0]]=$date_full;
		$result_video_temp[$res[0]]++;

		if ($res[1]>0)
		{
			if (intval($stats_settings['collect_memberzone_stats'])==1)
			{
				$result_user_video_watched[$res[1]]++;
				$result_user_video_watched_log[]=array('video_id'=>$res[0],'user_id'=>$res[1],'added_date'=>$date_full);
			}
		}
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/video_plays.dat", $file);
if (@filesize($file) > $max_filesize)
{
	log_output("ERROR  Size of video_plays.dat is more than allowed, skipping");
	@unlink($file);
}
$data = explode("\n", @file_get_contents($file));
foreach ($data as $res)
{
	[$date, $video_id, $country_code, $referer, $query_params, $device] = array_map('trim', explode("|", trim($res), 6));
	$video_id = intval($video_id);
	$device = intval($device);
	if (intval($stats_settings['collect_traffic_stats_countries']) == 0)
	{
		$country_code = '';
	}
	$referer = str_replace(array("https://", "http://www."), "http://", $referer);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	if ($video_id > 0)
	{
		$result_video_viewed_player[$video_id]++;
	}

	$referer_id = 0;
	foreach ($list_referers as $ref)
	{
		if (strtolower($ref['referer']) == '<bookmarks>' && $referer == '')
		{
			$referer_id = $ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'], "http://") === 0 && strpos($referer, $ref['referer']) === 0)
		{
			$referer_id = $ref['referer_id'];
			break;
		} elseif (strpos($ref['referer'], "http://") !== 0 && strpos($query_params, $ref['referer']) !== false)
		{
			$referer_id = $ref['referer_id'];
			break;
		}
	}
	$result_video_viewed[$date][$country_code][$referer_id][$device]['view_player_amount']++;
}
$data_count3 = count($data) - 1;

if (intval($stats_settings['collect_memberzone_stats'])==1)
{
	foreach ($result_video_temp as $k=>$v)
	{
		$video_info=mr2array_single(sql_pr("select video_id, user_id from $config[tables_prefix]videos where video_id=?",$k));
		if ($video_info['video_id']>0)
		{
			$result_user_video_viewed[$video_info['user_id']]+=$v;
		}
	}
}

foreach ($result as $video_id=>$amount)
{
	$stats_date = $result_date[$video_id] ?? date("Y-m-d H:i:s");
	$unique_viewed=intval($result_video_viewed_unique[$video_id]);
	$player_viewed=intval($result_video_viewed_player[$video_id]);

	sql_pr("update $config[tables_prefix]videos set video_viewed=video_viewed+?, video_viewed_player=video_viewed_player+?, video_viewed_unique=video_viewed_unique+?, last_time_view_date=? where video_id=?",$amount,$player_viewed,$unique_viewed,$stats_date,$video_id);

	if (intval($stats_settings['collect_videos_stats'])==1)
	{
		$stats_date=date("Y-m-d");
		if (sql_update("update $config[tables_prefix]stats_videos set viewed=viewed+?, player_viewed=player_viewed+?, unique_viewed=unique_viewed+? where video_id=? and added_date=?",$amount,$player_viewed,$unique_viewed,$video_id,$stats_date)==0)
		{
			sql_pr("insert into $config[tables_prefix]stats_videos set viewed=?, player_viewed=?, unique_viewed=?, video_id=?, added_date=?",$amount,$player_viewed,$unique_viewed,$video_id,$stats_date);
		}
	}
}

foreach ($result_user_video_viewed as $user_id=>$amount)
{
	sql_pr("update $config[tables_prefix]users set video_viewed=video_viewed+? where user_id=?",$amount,$user_id);
}

foreach ($result_user_video_watched_log as $data)
{
	if (sql_update("update $config[tables_prefix]log_content_users set is_old=1 where video_id=? and user_id=?",$data['video_id'],$data['user_id'])==0)
	{
		$result_user_video_watched_unique[$data['user_id']]++;
	}
	sql_pr("insert into $config[tables_prefix]log_content_users set video_id=?, user_id=?, added_date=?",$data['video_id'],$data['user_id'],$data['added_date']);
}
foreach ($result_user_video_watched as $user_id=>$amount)
{
	$uniq_amount=intval($result_user_video_watched_unique[$user_id]);
	sql_pr("update $config[tables_prefix]users set video_watched=video_watched+?, video_watched_unique=video_watched_unique+? where user_id=?",$amount,$uniq_amount,$user_id);
}

if (intval($stats_settings['collect_traffic_stats']) == 1)
{
	foreach ($result_video_viewed as $date => $result_date)
	{
		foreach ($result_date as $country_code => $result_country_code)
		{
			foreach ($result_country_code as $referer_id => $result_referer)
			{
				foreach ($result_referer as $device => $amount)
				{
					settype($referer_id, "integer");
					settype($device, "integer");

					if (sql_update("update $config[tables_prefix_multi]stats_in set view_video_amount=view_video_amount+?, view_player_amount=view_player_amount+? where referer_id=? and country_code=? and device=? and added_date=?", intval($amount['view_video_amount']), intval($amount['view_player_amount']), $referer_id, $country_code, $device, $date) == 0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_in set view_video_amount=?, view_player_amount=?, referer_id=?, country_code=?, device=?, added_date=?", intval($amount['view_video_amount']), intval($amount['view_player_amount']), $referer_id, $country_code, $device, $date);
					}
				}
			}
		}
	}
}

log_output("INFO  Processed video stats: " . ($data_count1+$data_count2+$data_count3));

// =====================================================================================================================
// videos file stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/video_files.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of video_files.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("||",$res,5);
	$video_id=intval($res[0]);
	$date_full=trim($res[1]);
	$date=date("Y-m-d",strtotime($date_full));
	$postfix=trim($res[2]);
	$user_id=intval($res[3]);
	$start=intval($res[4]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400 || $video_id < 1)
	{
		continue;
	}

	if (intval($stats_settings['collect_videos_stats_video_files'])==1)
	{
		$result[$video_id][$date]++;
	}
	if ($user_id>0 && intval($stats_settings['collect_memberzone_stats_video_files'])==1)
	{
		sql_pr("insert into $config[tables_prefix]log_content_users set video_id=?, user_id=?, stream_to=?, added_date=?, is_old=1, is_file=1",$video_id,$user_id,$start,$date_full);
	}
}

foreach ($result as $video_id=>$result_date)
{
	foreach ($result_date as $date=>$amount)
	{
		settype($amount,"integer");

		if ($amount>0)
		{
			if (sql_update("update $config[tables_prefix]stats_videos set files_requested=files_requested+? where video_id=? and added_date=?",$amount,$video_id,$date)==0)
			{
				sql_pr("insert into $config[tables_prefix]stats_videos set files_requested=?, video_id=?, added_date=?",$amount,$video_id,$date);
			}
		}
	}
}

log_output("INFO  Processed video file stats: " . (count($data)-1));

// =====================================================================================================================
// albums stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/albums_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of albums_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$result_date=array();
$result_album_viewed=array();
$result_album_viewed_unique=array();
$result_user_album_viewed=array();
$result_user_album_watched=array();
$result_user_album_watched_log=array();
$result_user_album_watched_unique=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res,9);
	$res[0]=trim($res[0]);

	settype($res[1],"integer");
	if (strlen($res[0])<1) {continue;}

	$stats_mode=intval($res[2]);
	$country_code=trim($res[3]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[4]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[5]);
	$date_full=trim($res[6]);
	$date=date("Y-m-d",strtotime($date_full));
	$ip=ip2int(trim($res[7]));
	$device=intval($res[8]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	if ($stats_mode==1)
	{
		$referer_id=0;
		foreach ($list_referers as $ref)
		{
			if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
			{
				$referer_id=$ref['referer_id'];
				break;
			}
		}
		$result_album_viewed[$date][$country_code][$referer_id][$device]++;

		if (intval($stats_settings['collect_albums_stats_unique'])==1)
		{
			$album_id=mr2number(sql_pr("select album_id from $config[tables_prefix]albums where (dir=? or $database_selectors[where_locale_dir]) limit 1",$res[0],$res[0]));
			if ($album_id>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]albums_visits where ip=? and album_id=? and flag=0",$ip,$album_id))==0)
			{
				$result_album_viewed_unique[$album_id]++;
				sql_pr("insert into $config[tables_prefix]albums_visits set ip=?, album_id=?, flag=0, added_date='$now_date'",$ip,$album_id);
			}
		}
	} else {
		$result[$res[0]]++;
		$result_date[$res[0]]=$date_full;

		if ($res[1]>0)
		{
			if (intval($stats_settings['collect_memberzone_stats'])==1)
			{
				$album_id=mr2number(sql_pr("select album_id from $config[tables_prefix]albums where (dir=? or $database_selectors[where_locale_dir]) limit 1",$res[0],$res[0]));
				$result_user_album_watched[$res[1]]++;
				$result_user_album_watched_log[]=array('album_id'=>$album_id,'user_id'=>$res[1],'added_date'=>$date_full);
			}
		}
	}
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$album_info=mr2array_single(sql_pr("select album_id, user_id from $config[tables_prefix]albums where (dir=? or $database_selectors[where_locale_dir])",$k,$k));
	if ($album_info['album_id']>0)
	{
		$result[$album_info['album_id']]=$v;
		if (isset($result_date[$k]))
		{
			$value=$result_date[$k];
			unset($result_date[$k]);
			$result_date[$album_info['album_id']]=$value;
		}
		if (intval($stats_settings['collect_memberzone_stats'])==1)
		{
			$result_user_album_viewed[$album_info['user_id']]+=$v;
		}
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/albums_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of albums_id.dat is more than allowed, skipping");
	@unlink($file);
}

$result_album_temp=array();
$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res,9);
	settype($res[0],"integer");
	settype($res[1],"integer");
	if ($res[0]<1) {continue;}

	$stats_mode=intval($res[2]);
	$country_code=trim($res[3]);
	if (intval($stats_settings['collect_traffic_stats_countries'])==0)
	{
		$country_code='';
	}
	$referer=trim($res[4]);
	$referer=str_replace(array("https://", "http://www."), "http://", $referer);
	$query_params=trim($res[5]);
	$date_full=trim($res[6]);
	$date=date("Y-m-d",strtotime($date_full));
	$ip=ip2int(trim($res[7]));
	$device=intval($res[8]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	if ($stats_mode==1)
	{
		$referer_id=0;
		foreach ($list_referers as $ref)
		{
			if (strtolower($ref['referer'])=='<bookmarks>' && $referer=='')
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")===0 && strpos($referer,$ref['referer'])===0)
			{
				$referer_id=$ref['referer_id'];
				break;
			} elseif (strpos($ref['referer'],"http://")!==0 && strpos($query_params,$ref['referer'])!==false)
			{
				$referer_id=$ref['referer_id'];
				break;
			}
		}
		$result_album_viewed[$date][$country_code][$referer_id][$device]++;

		if (intval($stats_settings['collect_albums_stats_unique'])==1)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums_visits where ip=? and album_id=? and flag=0",$ip,$res[0]))==0)
			{
				$result_album_viewed_unique[$res[0]]++;
				sql_pr("insert into $config[tables_prefix]albums_visits set ip=?, album_id=?, flag=0, added_date='$now_date'",$ip,$res[0]);
			}
		}
	} else {
		$result[$res[0]]++;
		$result_date[$res[0]]=$date_full;
		$result_album_temp[$res[0]]++;

		if ($res[1]>0)
		{
			if (intval($stats_settings['collect_memberzone_stats'])==1)
			{
				$result_user_album_watched[$res[1]]++;
				$result_user_album_watched_log[]=array('album_id'=>$res[0],'user_id'=>$res[1],'added_date'=>$date_full);
			}
		}
	}
}

if (intval($stats_settings['collect_memberzone_stats'])==1)
{
	foreach ($result_album_temp as $k=>$v)
	{
		$album_info=mr2array_single(sql_pr("select album_id, user_id from $config[tables_prefix]albums where album_id=?",$k));
		if ($album_info['album_id']>0)
		{
			$result_user_album_viewed[$album_info['user_id']]+=$v;
		}
	}
}

foreach ($result as $album_id=>$amount)
{
	$stats_date = $result_date[$album_id] ?? date("Y-m-d H:i:s");
	$unique_viewed=intval($result_album_viewed_unique[$album_id]);

	sql_pr("update $config[tables_prefix]albums set album_viewed=album_viewed+?, album_viewed_unique=album_viewed_unique+?, last_time_view_date=? where album_id=?",$amount,$unique_viewed,$stats_date,$album_id);

	if (intval($stats_settings['collect_albums_stats'])==1)
	{
		$stats_date=date("Y-m-d");
		if (sql_update("update $config[tables_prefix]stats_albums set viewed=viewed+?, unique_viewed=unique_viewed+? where album_id=? and added_date=?",$amount,$unique_viewed,$album_id,$stats_date)==0)
		{
			sql_pr("insert into $config[tables_prefix]stats_albums set viewed=?, unique_viewed=unique_viewed+?, album_id=?, added_date=?",$amount,$unique_viewed,$album_id,$stats_date);
		}
	}
}

foreach ($result_user_album_viewed as $user_id=>$amount)
{
	sql_pr("update $config[tables_prefix]users set album_viewed=album_viewed+? where user_id=?",$amount,$user_id);
}

foreach ($result_user_album_watched_log as $data)
{
	if (sql_update("update $config[tables_prefix]log_content_users set is_old=1 where album_id=? and user_id=?",$data['album_id'],$data['user_id'])==0)
	{
		$result_user_album_watched_unique[$data['user_id']]++;
	}
	sql_pr("insert into $config[tables_prefix]log_content_users set album_id=?, user_id=?, added_date=?",$data['album_id'],$data['user_id'],$data['added_date']);
}
foreach ($result_user_album_watched as $user_id=>$amount)
{
	$uniq_amount=intval($result_user_album_watched_unique[$user_id]);
	sql_pr("update $config[tables_prefix]users set album_watched=album_watched+?, album_watched_unique=album_watched_unique+? where user_id=?",$amount,$uniq_amount,$user_id);
}

if (intval($stats_settings['collect_traffic_stats'])==1)
{
	foreach ($result_album_viewed as $date=>$result_date)
	{
		foreach ($result_date as $country_code=>$result_country_code)
		{
			foreach ($result_country_code as $referer_id=>$result_referer)
			{
				foreach ($result_referer as $device=>$amount)
				{
					settype($amount,"integer");
					settype($referer_id,"integer");
					settype($device,"integer");

					if (sql_update("update $config[tables_prefix_multi]stats_in set view_album_amount=view_album_amount+? where referer_id=? and country_code=? and device=? and added_date=?",$amount,$referer_id,$country_code,$device,$date)==0)
					{
						sql_insert("insert into $config[tables_prefix_multi]stats_in set view_album_amount=?, referer_id=?, country_code=?, device=?, added_date=?",$amount,$referer_id,$country_code,$device,$date);
					}
				}
			}
		}
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/images_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of images_id.dat is more than allowed, skipping");
	@unlink($file);
}

$result_images=array();
$result_albums=array();
$data=explode("\r\n",@file_get_contents($file));
$data_count3=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res,2);
	if ($res[0]=='i')
	{
		$result_images[intval($res[1])]++;
	} else {
		$result_albums[intval($res[1])]++;
	}
}

foreach ($result_images as $image_id=>$amount)
{
	sql_pr("update $config[tables_prefix]albums_images set image_viewed=image_viewed+? where image_id=?",$amount,$image_id);
}
foreach ($result_albums as $album_id=>$amount)
{
	sql_pr("update $config[tables_prefix]albums_images set image_viewed=image_viewed+? where image_id=(select main_photo_id from $config[tables_prefix]albums where album_id=?)",$amount,$album_id);
}

log_output("INFO  Processed album stats: " . ($data_count1+$data_count2+$data_count3));

// =====================================================================================================================
// albums file stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/album_files.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of album_files.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("||",$res,5);
	$album_id=intval($res[0]);
	$date_full=trim($res[1]);
	$date=date("Y-m-d",strtotime($date_full));
	$size=trim($res[2]);
	$user_id=intval($res[3]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400 || $album_id < 1)
	{
		continue;
	}

	if (intval($stats_settings['collect_albums_stats_album_images'])==1)
	{
		$result[$album_id][$date]++;
	}
	if ($user_id>0 && intval($stats_settings['collect_memberzone_stats_album_images'])==1)
	{
		sql_pr("insert into $config[tables_prefix]log_content_users set album_id=?, user_id=?, added_date=?, is_old=1, is_file=1",$album_id,$user_id,$date_full);
	}
}

foreach ($result as $album_id=>$result_date)
{
	foreach ($result_date as $date=>$amount)
	{
		settype($amount,"integer");

		if ($amount>0)
		{
			if (sql_update("update $config[tables_prefix]stats_albums set files_requested=files_requested+? where album_id=? and added_date=?",$amount,$album_id,$date)==0)
			{
				sql_pr("insert into $config[tables_prefix]stats_albums set files_requested=?, album_id=?, added_date=?",$amount,$album_id,$date);
			}
		}
	}
}

log_output("INFO  Processed album files stats:" . (count($data)-1));

// =====================================================================================================================
// posts stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/posts_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of posts_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	$res[0]=trim($res[0]);
	if (strlen($res[0])<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$post_info=mr2array_single(sql_pr("select post_id from $config[tables_prefix]posts where dir=?",$k));
	if ($post_info['post_id']>0)
	{
		$result[$post_info['post_id']]=$v;
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/posts_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of posts_id.dat is more than allowed, skipping");
	@unlink($file);
}

$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	settype($res[0],"integer");
	if ($res[0]<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $post_id=>$amount)
{
	$stats_date=date("Y-m-d H:i:s");
	sql_pr("update $config[tables_prefix]posts set post_viewed=post_viewed+?, last_time_view_date=? where post_id=?",$amount,$stats_date,$post_id);
}

log_output("INFO  Processed post stats: " . ($data_count1+$data_count2));

// =====================================================================================================================
// cs stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/cs_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of cs_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	$res[0]=trim($res[0]);
	if (strlen($res[0])<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$cs_info=mr2array_single(sql_pr("select content_source_id from $config[tables_prefix]content_sources where (dir=? or $database_selectors[where_locale_dir])",$k,$k));
	if ($cs_info['content_source_id']>0)
	{
		$result[$cs_info['content_source_id']]=$v;
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/cs_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of cs_id.dat is more than allowed, skipping");
	@unlink($file);
}

$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	settype($res[0],"integer");
	if ($res[0]<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $cs_id=>$amount)
{
	sql_pr("update $config[tables_prefix]content_sources set cs_viewed=cs_viewed+? where content_source_id=?",$amount,$cs_id);
}

log_output("INFO  Processed content source stats: " . ($data_count1+$data_count2));

// =====================================================================================================================
// models stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/models_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of models_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	$res[0]=trim($res[0]);
	if (strlen($res[0])<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$model_info=mr2array_single(sql_pr("select model_id from $config[tables_prefix]models where (dir=? or $database_selectors[where_locale_dir])",$k,$k));
	if ($model_info['model_id']>0)
	{
		$result[$model_info['model_id']]=$v;
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/models_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of models_id.dat is more than allowed, skipping");
	@unlink($file);
}

$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	settype($res[0],"integer");
	if ($res[0]<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $model_id=>$amount)
{
	sql_pr("update $config[tables_prefix]models set model_viewed=model_viewed+? where model_id=?",$amount,$model_id);
}

log_output("INFO  Processed model stats: " . ($data_count1+$data_count2));

// =====================================================================================================================
// dvds stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/dvds_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of dvds_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	$res[0]=trim($res[0]);
	if (strlen($res[0])<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$dvd_info=mr2array_single(sql_pr("select dvd_id from $config[tables_prefix]dvds where (dir=? or $database_selectors[where_locale_dir])",$k,$k));
	if ($dvd_info['dvd_id']>0)
	{
		$result[$dvd_info['dvd_id']]=$v;
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/dvds_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of dvds_id.dat is more than allowed, skipping");
	@unlink($file);
}

$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	settype($res[0],"integer");
	if ($res[0]<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $dvd_id=>$amount)
{
	sql_pr("update $config[tables_prefix]dvds set dvd_viewed=dvd_viewed+? where dvd_id=?",$amount,$dvd_id);
}

log_output("INFO  Processed DVD / channel stats: " . ($data_count1+$data_count2));

// =====================================================================================================================
// profile stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/profiles_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of profiles_id.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=intval($res);
	if ($res<1) {continue;}
	$result[$res]++;
}

foreach ($result as $user_id=>$amount)
{
	sql_pr("update $config[tables_prefix]users set profile_viewed=profile_viewed+? where user_id=?",$amount,$user_id);
}

log_output("INFO  Processed profile stats: " . (count($data)-1));

// =====================================================================================================================
// playlists stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/playlists_dir.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of playlists_dir.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();

$data=explode("\r\n",@file_get_contents($file));
$data_count1=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	$res[0]=trim($res[0]);
	if (strlen($res[0])<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $k=>$v)
{
	unset($result[$k]);

	$playlist_info=mr2array_single(sql_pr("select playlist_id from $config[tables_prefix]playlists where dir=?",$k));
	if ($playlist_info['playlist_id']>0)
	{
		$result[$playlist_info['playlist_id']]=$v;
	}
}

@unlink($file);
@rename("$config[project_path]/admin/data/stats/playlists_id.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of playlists_id.dat is more than allowed, skipping");
	@unlink($file);
}

$data=explode("\r\n",@file_get_contents($file));
$data_count2=(count($data)-1);
foreach ($data as $res)
{
	$res=explode("||",$res);
	settype($res[0],"integer");
	if ($res[0]<1) {continue;}
	$result[$res[0]]++;
}

foreach ($result as $playlist_id=>$amount)
{
	sql_pr("update $config[tables_prefix]playlists set playlist_viewed=playlist_viewed+? where playlist_id=?",$amount,$playlist_id);
}

log_output("INFO  Processed playlist stats: " . ($data_count1+$data_count2));

// =====================================================================================================================
// se stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/search.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of search.dat is more than allowed, skipping");
	@unlink($file);
}

if (intval($stats_settings['collect_search_stats'])==0)
{
	log_output("INFO  Search stats are disabled");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("|",$res,4);
	$date=trim($res[0]);
	$query=trim($res[1]);
	$query_results_videos=intval($res[2]);
	$query_results_albums=intval($res[3]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	if ($stats_settings['search_to_lowercase'] == 1)
	{
		$query = mb_lowercase($query);
	}

	if (intval($stats_settings['search_max_length'])>0)
	{
		if (strlen($query)>intval($stats_settings['search_max_length']))
		{
			continue;
		}
	}
	if ($stats_settings['search_stop_symbols']!='')
	{
		for ($i=0;$i<strlen($stats_settings['search_stop_symbols']);$i++)
		{
			if (strpos($query, $stats_settings['search_stop_symbols'][$i])!==false)
			{
				continue 2;
			}
		}
	}

	if ($query_results_videos+$query_results_albums==0)
	{
		$result[$date][$query]['amount']++;
	} else {
		if (intval($result[$date][$query]['query_results_videos'])==0)
		{
			$result[$date][$query]['query_results_videos']=$query_results_videos;
		}
		if (intval($result[$date][$query]['query_results_albums'])==0)
		{
			$result[$date][$query]['query_results_albums']=$query_results_albums;
		}
	}
}

foreach ($result as $date=>$result_date)
{
	foreach ($result_date as $query=>$result_query)
	{
		$sql_query_results='';
		if (intval($result_query['query_results_videos'])>0)
		{
			$sql_query_results.='query_results_videos='.intval($result_query['query_results_videos']).', ';
		}
		if (intval($result_query['query_results_albums'])>0)
		{
			$sql_query_results.='query_results_albums='.intval($result_query['query_results_albums']).', ';
		}
		$sql_query_results.='query_results_total=query_results_videos+query_results_albums';
		if (!$result_query['amount'])
		{
			$result_query['amount']=0;
		}
		if (sql_update("update $config[tables_prefix_multi]stats_search set amount=amount+?, added_date=?, $sql_query_results where query_md5=md5(lower(?))",$result_query['amount'],$date,$query)==0)
		{
			sql_pr("insert into $config[tables_prefix_multi]stats_search set amount=?, query=?, query_md5=md5(lower(query)), query_length=length(query), added_date=?, $sql_query_results",$result_query['amount'],$query,$date);
		}
	}
}

log_output("INFO  Processed search stats: " . (count($data)-1));

// =====================================================================================================================
// update ip stats
// =====================================================================================================================

$minutes=$options['ANTI_HOTLINK_N_HOURS'];
$movies=$options['ANTI_HOTLINK_N_VIDEOS'];

$config['sql_safe_mode'] = 1;

$admin_ips=mr2array_list(sql("select distinct last_ip from $config[tables_prefix_multi]admin_users"));
foreach ($admin_ips as $k=>$v)
{
	$admin_ips[$k]=int2ip($v);
}

unset($config['sql_safe_mode']);

@unlink($file);
@rename("$config[project_path]/admin/data/stats/ip_data.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of ip_data.dat is more than allowed, skipping");
	@unlink($file);
}

$data=explode("\r\n",@file_get_contents($file));
$stats=array();
$good_ips=array();
foreach ($data as $res)
{
	$res=explode("|",$res);
	$ip=trim($res[0]);
	$date=intval($res[1]);

	if ($date>=time()-$minutes*60)
	{
		$stats[$ip]++;
		$good_ips[]="$ip|$date";
	}
}

$ip_whitelist=explode(',',$options['ANTI_HOTLINK_WHITE_IPS']);

$fh=fopen("$config[project_path]/admin/data/stats/ip_blocked.dat","a+");
flock($fh,LOCK_EX);
ftruncate($fh,0);
foreach ($stats as $k=>$v)
{
	if ($v>=$movies)
	{
		foreach ($ip_whitelist as $ip_mask)
		{
			if (trim($ip_mask)<>'')
			{
				if (strpos($k,trim($ip_mask))===0)
				{
					log_output("INFO  IP $k is not blocked as a white IP");
					continue 2;
				}
			}
		}
		if ($options['ANTI_HOTLINK_OWN_IP']==$k)
		{
			log_output("INFO  IP $k is not blocked as project fallback IP");
			continue;
		}
		if (in_array($k,$admin_ips))
		{
			log_output("INFO  IP $k is not blocked as admin IP");
			continue;
		}
		log_output("INFO  IP $k is blocked");
		fwrite($fh,"$k\r\n");

		file_put_contents("$config[project_path]/admin/logs/blocked_ips.txt", date("[Y-m-d H:i:s] ")."Blocked IP $k\n", FILE_APPEND | LOCK_EX);
	}
}
flock($fh,LOCK_UN);
fclose($fh);

$fh=fopen("$config[project_path]/admin/data/stats/ip_data.dat","w");
flock($fh,LOCK_EX);
foreach ($good_ips as $ip)
{
	fwrite($fh,"$ip\r\n");
}
flock($fh,LOCK_UN);
fclose($fh);

log_output("INFO  Processed IP protection stats:" . (count($data)-1));

// =====================================================================================================================
// overload protection stats
// =====================================================================================================================

@unlink($file);
@rename("$config[project_path]/admin/data/stats/overload.dat",$file);
if (@filesize($file)>$max_filesize)
{
	log_output("ERROR  Size of overload.dat is more than allowed, skipping");
	@unlink($file);
}

$result=array();
$data=explode("\r\n",@file_get_contents($file));
foreach ($data as $res)
{
	$res=explode("|",$res,3);

	$date=trim($res[0]);
	$type=intval($res[1]);

	if (strlen($date) < 8 || strtotime($date) > time() + 86400)
	{
		continue;
	}

	$result[$date][$type]++;
}

foreach ($result as $date=>$result_date)
{
	$types=array(0,0,0,0,0,0);
	foreach ($result_date as $type=>$amount)
	{
		settype($amount,"integer");
		$types[$type-1]=$amount;
	}
	if (sql_update("update $config[tables_prefix_multi]stats_overload_protection set amount_max_la_pages=amount_max_la_pages+?, amount_max_sleep_processes=amount_max_sleep_processes+?, amount_max_la_blocks=amount_max_la_blocks+?, amount_max_mysql_processes=amount_max_mysql_processes+?, amount_max_la_cron=amount_max_la_cron+?, amount_max_timeout_blocks=amount_max_timeout_blocks+? where added_date=?",$types[0],$types[1],$types[2],$types[3],$types[4],$types[5],$date)==0)
	{
		sql_pr("insert into $config[tables_prefix_multi]stats_overload_protection set amount_max_la_pages=?, amount_max_sleep_processes=?, amount_max_la_blocks=?, amount_max_mysql_processes=?, amount_max_la_cron=?, amount_max_timeout_blocks=?, added_date=?, is_warning=1",$types[0],$types[1],$types[2],$types[3],$types[4],$types[5],$date);
	}
}

log_output("INFO  Processed overload stats:"  . (count($data)-1));

log_output("INFO  Stats processor finished");

@unlink($file);
flock($lock, LOCK_UN);
fclose($lock);

function log_output($message)
{
	if ($message=='')
	{
		echo "\n";
	} else {
		echo date("[Y-m-d H:i:s] ").$message."\n";
	}
}
