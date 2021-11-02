<?php
function list_members_tokensShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	if ($_SESSION['user_id']>0)
	{
		$user_id=intval($_SESSION['user_id']);
		$smarty->assign("display_name",$_SESSION['display_name']);
		$smarty->assign("avatar",$_SESSION['avatar']);
		$smarty->assign("user_id",$user_id);
		$storage[$object_id]['user_id']=$user_id;
		$storage[$object_id]['display_name']=$_SESSION['display_name'];
		$storage[$object_id]['avatar']=$_SESSION['avatar'];
	} else {
		if ($_GET['mode']=='async')
		{
			header('HTTP/1.0 403 Forbidden');die;
		}

		$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
		if (isset($block_config['redirect_unknown_user_to']))
		{
			$url=process_url($block_config['redirect_unknown_user_to']);
			return "status_302: $url";
		} else
		{
			return "status_302: $config[project_url]";
		}
	}

	$payouts=mr2array(sql("
		select p.payout_id, p.comment, p.added_date, pu.tokens, pu.amount, pu.amount_currency
		from $config[tables_prefix]users_payouts_users pu inner join $config[tables_prefix]users_payouts p on pu.payout_id=p.payout_id
		where pu.user_id=1 and p.status_id=2
	"));

	$flow_group=intval($block_config['flow_group']);
	if (isset($block_config['var_flow_group']) && in_array(intval($_REQUEST[$block_config['var_flow_group']]),array(1,2,3,4,5)))
	{
		$flow_group=intval($_REQUEST[$block_config['var_flow_group']]);
	}
	if ($flow_group>0)
	{
		$smarty->assign('flow_group',$flow_group);
		$storage[$object_id]['flow_group']=$flow_group;
	}

	$where_awards_payout_filter='';
	if (isset($block_config['var_payout_id']) && trim($_REQUEST[$block_config['var_payout_id']])!='')
	{
		$payout_id=intval(trim($_REQUEST[$block_config['var_payout_id']]));
		foreach ($payouts as $payout)
		{
			if ($payout['payout_id']==$payout_id || $payout_id==0)
			{
				$where_awards_payout_filter=" and $config[tables_prefix]log_awards_users.payout_id=$payout_id";
				$flow_group=3;
				break;
			}
		}
		if ($where_awards_payout_filter=='')
		{
			return 'status_404';
		}
	}

	$metadata=list_members_tokensMetaData();
	foreach ($metadata as $res)
	{
		if ($res['name']=='sort_by')
		{
			preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
			$sorting_available=explode(",",$temp[1]);
			break;
		}
	}
	$sorting_available[]="rand()";

	$sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));
	if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));
	if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
	if ($sort_by_clear=='') {$sort_by_clear="date";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$sort_by="$sort_by_clear $direction";
	if ($sort_by_clear=='flow_group')
	{
		$sort_by="$sort_by_clear $direction, date desc";
	}

	$queries[1]="
		select
			1 as flow_group,
			case when $config[tables_prefix]users_purchases.video_id>0 then 'purchase_video' when $config[tables_prefix]users_purchases.album_id>0 then 'purchase_album' when $config[tables_prefix]users_purchases.dvd_id>0 then 'purchase_dvd' when $config[tables_prefix]users_purchases.profile_id>0 then 'purchase_user' else '' end as flow_type,
			-$config[tables_prefix]users_purchases.tokens as tokens,
			$config[tables_prefix]users_purchases.added_date as date,
			case when $config[tables_prefix]users_purchases.video_id>0 then $config[tables_prefix]users_purchases.video_id when $config[tables_prefix]users_purchases.album_id>0 then $config[tables_prefix]users_purchases.album_id when $config[tables_prefix]users_purchases.dvd_id>0 then $config[tables_prefix]users_purchases.dvd_id when $config[tables_prefix]users_purchases.profile_id>0 then $config[tables_prefix]users_purchases.profile_id end as object_id,
			case when $config[tables_prefix]users_purchases.video_id>0 then $database_selectors[videos_selector_title] when $config[tables_prefix]users_purchases.album_id>0 then $database_selectors[albums_selector_title] when $config[tables_prefix]users_purchases.dvd_id>0 then $database_selectors[dvds_selector_title] when $config[tables_prefix]users_purchases.profile_id>0 then u2.display_name end as object_info,
			'' as notes
		from
			$config[tables_prefix]users_purchases left join $config[tables_prefix]videos on $config[tables_prefix]users_purchases.video_id=$config[tables_prefix]videos.video_id left join $config[tables_prefix]albums on $config[tables_prefix]users_purchases.album_id=$config[tables_prefix]albums.album_id left join $config[tables_prefix]dvds on $config[tables_prefix]users_purchases.dvd_id=$config[tables_prefix]dvds.dvd_id left join $config[tables_prefix]users u2 on $config[tables_prefix]users_purchases.profile_id=u2.user_id
		where
			$config[tables_prefix]users_purchases.user_id=$user_id
		union all select
			1 as flow_group,
			'purchase_access_package',
			-$config[tables_prefix]bill_transactions.price as tokens,
			$config[tables_prefix]bill_transactions.access_start_date as date,
			$config[tables_prefix]bill_transactions.external_package_id as object_id,
			$config[tables_prefix]card_bill_packages.title as object_info,
			'' as notes
		from
			$config[tables_prefix]bill_transactions left join $config[tables_prefix]card_bill_packages on $config[tables_prefix]bill_transactions.external_package_id=$config[tables_prefix]card_bill_packages.package_id
		where
			$config[tables_prefix]bill_transactions.user_id=$user_id and $config[tables_prefix]bill_transactions.internal_provider_id='tokens'
	";

	$queries[2]="
		select
			2 as flow_group,
			'purchase_tokens' as flow_type,
			tokens_granted as tokens,
			access_start_date as date,
			0 as object_id,
			'' as object_info,
			'' as notes
		from
			$config[tables_prefix]bill_transactions
		where
			user_id=$user_id and tokens_granted>0
	";

	$own_username=sql_escape($_SESSION['username']);
	$queries[3]="
		select
			3 as flow_group,
			case $config[tables_prefix]log_awards_users.award_type when 1 then 'award_signup' when 2 then 'award_avatar' when 3 then 'award_comment' when 4 then 'award_video_upload' when 5 then 'award_album_upload' when 6 then 'award_video_sale' when 7 then 'award_album_sale' when 8 then 'award_referral' when 9 then 'award_post_upload' when 10 then 'award_donation' when 11 then 'award_video_traffic' when 12 then 'award_album_traffic' when 13 then 'award_user_sale' when 14 then 'award_dvd_sale' when 15 then 'award_login' when 16 then 'award_cover' when 17 then 'award_embed_traffic' else '' end as flow_type,
			$config[tables_prefix]log_awards_users.tokens_granted as tokens,
			$config[tables_prefix]log_awards_users.added_date as date,
			case when $config[tables_prefix]log_awards_users.video_id>0 then $config[tables_prefix]log_awards_users.video_id when $config[tables_prefix]log_awards_users.album_id>0 then $config[tables_prefix]log_awards_users.album_id when $config[tables_prefix]log_awards_users.post_id>0 then $config[tables_prefix]log_awards_users.post_id when $config[tables_prefix]log_awards_users.comment_id>0 then $config[tables_prefix]log_awards_users.comment_id when $config[tables_prefix]log_awards_users.ref_id>0 then $config[tables_prefix]log_awards_users.ref_id when $config[tables_prefix]log_awards_users.profile_id>0 then $config[tables_prefix]log_awards_users.profile_id when $config[tables_prefix]log_awards_users.dvd_id>0 then $config[tables_prefix]log_awards_users.dvd_id end as object_id,
			case when $config[tables_prefix]log_awards_users.video_id>0 then $database_selectors[videos_selector_title] when $config[tables_prefix]log_awards_users.album_id>0 then $database_selectors[albums_selector_title] when $config[tables_prefix]log_awards_users.post_id>0 then $database_selectors[posts_selector_title] when $config[tables_prefix]log_awards_users.comment_id>0 then $config[tables_prefix]comments.comment when $config[tables_prefix]log_awards_users.ref_id>0 then $config[tables_prefix]users.username when $config[tables_prefix]log_awards_users.profile_id>0 then '$own_username' when $config[tables_prefix]log_awards_users.dvd_id>0 then $database_selectors[dvds_selector_title] end as object_info,
			case when $config[tables_prefix]log_awards_users.donation_id>0 then $config[tables_prefix]log_donations_users.comment end as notes
		from
			$config[tables_prefix]log_awards_users left join $config[tables_prefix]videos on $config[tables_prefix]log_awards_users.video_id=$config[tables_prefix]videos.video_id left join $config[tables_prefix]albums on $config[tables_prefix]log_awards_users.album_id=$config[tables_prefix]albums.album_id left join $config[tables_prefix]posts on $config[tables_prefix]log_awards_users.post_id=$config[tables_prefix]posts.post_id left join $config[tables_prefix]comments on $config[tables_prefix]log_awards_users.comment_id=$config[tables_prefix]comments.comment_id left join $config[tables_prefix]users on $config[tables_prefix]log_awards_users.ref_id=$config[tables_prefix]users.user_id left join $config[tables_prefix]log_donations_users on $config[tables_prefix]log_awards_users.donation_id=$config[tables_prefix]log_donations_users.donation_id left join $config[tables_prefix]dvds on $config[tables_prefix]log_awards_users.dvd_id=$config[tables_prefix]dvds.dvd_id
		where
			$config[tables_prefix]log_awards_users.user_id=$user_id $where_awards_payout_filter
	";

	$queries[4]="
		select
			4 as flow_group,
			'payout' as flow_type,
			-$config[tables_prefix]users_payouts_users.tokens as tokens,
			$config[tables_prefix]users_payouts_users.added_date as date,
			0 as object_id,
			concat($config[tables_prefix]users_payouts_users.amount, ' ', $config[tables_prefix]users_payouts_users.amount_currency) as object_info,
			$config[tables_prefix]users_payouts.comment as notes
		from
			$config[tables_prefix]users_payouts_users inner join $config[tables_prefix]users_payouts on $config[tables_prefix]users_payouts_users.payout_id=$config[tables_prefix]users_payouts.payout_id
		where
			$config[tables_prefix]users_payouts_users.user_id=$user_id and $config[tables_prefix]users_payouts.status_id in (1,2)
	";

	$queries[5]="
		select
			5 as flow_group,
			'donation' as flow_type,
			-$config[tables_prefix]log_donations_users.tokens as tokens,
			$config[tables_prefix]log_donations_users.added_date as date,
			$config[tables_prefix]log_donations_users.user_id as object_id,
			$config[tables_prefix]users.username as object_info,
			$config[tables_prefix]log_donations_users.comment as notes
		from
			$config[tables_prefix]log_donations_users left join $config[tables_prefix]users on $config[tables_prefix]log_donations_users.user_id=$config[tables_prefix]users.user_id
		where
			$config[tables_prefix]log_donations_users.donator_id=$user_id
	";

	if ($flow_group>0)
	{
		$query_projector="$queries[$flow_group]";
	} else {
		$query_projector="$queries[1] union all $queries[2] union all $queries[3] union all $queries[4] union all $queries[5]";
	}

	$where='';
	if (isset($block_config['var_date_from']) && trim($_REQUEST[$block_config['var_date_from']])!='')
	{
		$filter_date=date('Y-m-d', strtotime(trim($_REQUEST[$block_config['var_date_from']])));
		$where.=" and date>='$filter_date'";
	}
	if (isset($block_config['var_date_to']) && trim($_REQUEST[$block_config['var_date_to']])!='')
	{
		$filter_date=date('Y-m-d', strtotime(trim($_REQUEST[$block_config['var_date_to']])));
		$where.=" and date<='$filter_date'";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from ($query_projector) X where 1=1 $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select * from ($query_projector) X where 1=1 $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	} else {
		$limit='';
		if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

		$data=mr2array(sql("select * from ($query_projector) X where 1=1 $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	$smarty->assign("data",$data);
	$smarty->assign("payouts",$payouts);

	return '';
}

function list_members_tokensGetHash($block_config)
{
	return "nocache";
}

function list_members_tokensCacheControl($block_config)
{
	return "nocache";
}

function list_members_tokensMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[date,tokens,flow_group]", "is_required"=>1, "default_value"=>"date desc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"flow_group", "group"=>"static_filters", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0),

		// dynamic filters
		array("name"=>"var_flow_group", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group"),
		array("name"=>"var_date_from",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"date_from"),
		array("name"=>"var_date_to",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"date_to"),
		array("name"=>"var_payout_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"payout_id"),

		// navigation
		array("name"=>"redirect_unknown_user_to", "type"=>"STRING", "group"=>"navigation", "is_required"=>1, "default_value"=>"/?login"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
