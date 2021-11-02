<?php

function upgradeShow($block_config,$object_id)
{
    global $config,$smarty;

    $errors = null;
    $errors_async = null;

    if ($_GET['action']=='payment_done')
    {
        if ($_SESSION['user_id']>0)
        {
            $user_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
            login_user($user_data,0);
        }
    } elseif ($_POST['action']=='upgrade')
    {
        if ($_SESSION['user_id']<1)
        {
            if ($_POST['mode']=='async')
            {
                async_return_request_status(array(array('error_code'=>'not_logged_in')));
            } else {
                return "status_302: $config[project_url]";
            }
        }

        $user_id=intval($_SESSION['user_id']);
        $user_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$user_id));

        $card_package_id=intval($_POST['card_package_id']);
        $sms_package_id=intval($_POST['sms_package_id']);
        $sms_passcode=trim($_POST['sms_passcode']);
        $access_code=trim($_POST['access_code']);

        $payment_option=0;
        if (isset($block_config['enable_sms_payment']) || isset($block_config['enable_card_payment']))
        {
            $payment_option=intval($_POST['payment_option']);
        }

        if ($access_code!='')
        {
            if (isset($block_config['enable_access_codes']))
            {
                if (mr2number(sql_pr("select count(*) from $config[tables_prefix]bill_transactions where status_id=4 and access_code=?",$access_code))==0)
                {
                    $errors['access_code']=2;
                    $errors_async[]=array('error_field_name'=>'access_code','error_code'=>'invalid','block'=>'upgrade');
                }
            } else
            {
                $errors['access_code']=2;
                $errors_async[]=array('error_field_name'=>'access_code','error_code'=>'invalid','block'=>'upgrade');
            }
            $payment_option=1;
        } elseif (!in_array($payment_option,array(2,3)))
        {
            $errors['payment_option']=1;
            $errors_async[]=array('error_field_name'=>'payment_option','error_code'=>'required','block'=>'upgrade');
        }

        if ($payment_option==2)
        {
            if (mr2number(sql_pr("select count(*) from $config[tables_prefix]card_bill_packages where package_id=?",$card_package_id))==0)
            {
                $errors['card_package_id']=1;
                $errors_async[]=array('error_field_name'=>'card_package_id','error_code'=>'required','block'=>'upgrade');
            } else
            {
                $bill_internal_id=mr2string(sql_pr("select internal_id from $config[tables_prefix]card_bill_providers where provider_id=(select provider_id from $config[tables_prefix]card_bill_packages where package_id=?)",$card_package_id));
                if ($bill_internal_id=='tokens')
                {
                    $package_data=mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_packages where package_id=?",$card_package_id));
                    if (intval($package_data['price_initial'])>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$user_id)))
                    {
                        $errors['card_package_id']=2;
                        $errors_async[]=array('error_field_name'=>'card_package_id','error_code'=>'not_enough_tokens','block'=>'upgrade');
                    }
                }
            }
        } elseif ($payment_option==3)
        {
            if (mr2number(sql_pr("select count(*) from $config[tables_prefix]sms_bill_packages where package_id=?",$sms_package_id))==0)
            {
                $errors['sms_package_id']=1;
                $errors_async[]=array('error_field_name'=>'sms_package_id','error_code'=>'required','block'=>'upgrade');
            }

            if (strlen($sms_passcode)==0)
            {
                $errors['sms_passcode']=1;
                $errors_async[]=array('error_field_name'=>'sms_passcode','error_code'=>'required','block'=>'upgrade');
            } elseif (mr2number(sql_pr("select transaction_id from $config[tables_prefix]bill_transactions where access_code=? and user_id=0 and type_id in (1,10)",$sms_passcode))==0)
            {
                $errors['sms_passcode']=2;
                $errors_async[]=array('error_field_name'=>'sms_passcode','error_code'=>'invalid','block'=>'upgrade');
            }
        }

        if (!is_array($errors))
        {
            if ($access_code != '')
            {
                if (isset($block_config['enable_access_codes']))
                {
                    $access_code_transaction = mr2array_single(sql_pr("select * from $config[tables_prefix]bill_transactions where status_id=4 and access_code=?", $access_code));
                    if ($access_code_transaction['transaction_id'] > 0)
                    {
                        if ($access_code_transaction['tokens_granted'] > 0)
                        {
                            sql_pr("update $config[tables_prefix]bill_transactions set user_id=?, ip=?, country_code=lower(?), status_id=2, access_start_date=?, access_end_date=?, access_code='' where transaction_id=?", $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $access_code_transaction['transaction_id']);
                            sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $access_code_transaction['tokens_granted'], $user_id);
                            $_SESSION['tokens_available'] = mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", $user_id));
                        } else
                        {
                            sql_pr("update $config[tables_prefix]bill_transactions set user_id=?, ip=?, country_code=lower(?), status_id=1, access_start_date=?, access_end_date=(case when is_unlimited_access=1 then '2070-01-01 00:00:00' else date_add(?, interval duration_rebill day) end), duration_rebill=0, access_code='' where transaction_id=?", $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $access_code_transaction['transaction_id']);
                            sql_pr("update $config[tables_prefix]users set status_id=3 where user_id=?", $user_id);
                            $_SESSION['status_id'] = 3;
                        }
                        if ($access_code_transaction['access_code_referral_award'] > 0 && $_SESSION['user_info']['reseller_code'])
                        {
                            $referring_user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where user_id=?", intval($_SESSION['user_info']['reseller_code'])));
                            if ($referring_user_id > 0)
                            {
                                sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=8, user_id=?, ref_id=?, tokens_granted=?, added_date=?", $referring_user_id, $user_id, intval($access_code_transaction['access_code_referral_award']), date("Y-m-d H:i:s"));
                                sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($access_code_transaction['access_code_referral_award']), $referring_user_id);
                            }
                        }
                    }
                }
            } elseif ($payment_option == 2)
            {
                $username = $user_data['username'];
                $pass = strtolower(generate_password());
                $email = $user_data['email'];

                $package_data = mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_packages where package_id=?", $card_package_id));

                $bill_internal_id = mr2string(sql_pr("select internal_id from $config[tables_prefix]card_bill_providers where provider_id=(select provider_id from $config[tables_prefix]card_bill_packages where package_id=?)", $card_package_id));
                if ($bill_internal_id == 'tokens')
                {
                    $tokens_available = mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", $user_id));
                    if (intval($package_data['price_initial']) <= $tokens_available)
                    {
                        $access_start_date = date("Y-m-d H:i:s");
                        $access_is_unlimited = 0;
                        if (intval($package_data['duration_initial']) > 0)
                        {
                            $access_end_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + intval($package_data['duration_initial']), date("Y")));
                        } else
                        {
                            $access_end_date = "2070-01-01 00:00:00";
                            $access_is_unlimited = 1;
                        }

                        sql_pr("update $config[tables_prefix]users set status_id=3, tokens_available=GREATEST(tokens_available-?, 0) where user_id=?", intval($package_data['price_initial']), $user_id);
                        sql_pr("insert into $config[tables_prefix]bill_transactions set internal_provider_id='tokens', bill_type_id=2, status_id=1, type_id=1, external_package_id=?, duration_rebill=?, access_start_date=?, access_end_date=?, is_unlimited_access=?, user_id=?, ip=?, country_code=lower(?), price=?, currency_code='TOK'",
                            $card_package_id, intval($package_data['duration_rebill']), $access_start_date, $access_end_date, $access_is_unlimited, $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), intval($package_data['price_initial'])
                        );

                        $_SESSION['status_id'] = 3;
                        $_SESSION['tokens_available'] = $tokens_available - intval($package_data['price_initial']);
                    }
                } else
                {
                    $back_link = "$config[project_url]$_SERVER[SCRIPT_NAME]";
                    if ($_POST['back_link'] != '' && strpos($_POST['back_link'], $config['project_url']) === 0)
                    {
                        $back_link = $_POST['back_link'];
                    }

                    require_once("$config[project_path]/admin/billings/KvsPaymentProcessor.php");
                    require_once("$config[project_path]/admin/billings/$bill_internal_id/$bill_internal_id.php");
                    $payment_processor = KvsPaymentProcessorFactory::create_instance($bill_internal_id);
                    if ($payment_processor instanceof KvsPaymentProcessor)
                    {
                        $url = $payment_processor->get_payment_page_url($package_data, $back_link, array('username' => $username, 'pass' => $pass, 'email' => $email));
                    } else
                    {
                        $redirect_func = "{$bill_internal_id}_get_redirect_url";
                        $url = $redirect_func($package_data, $back_link, array('username' => $username, 'pass' => $pass, 'email' => $email));
                    }
                    $url = upgradeReplaceRuntimeParams($url);

                    if (sql_update("update $config[tables_prefix]bill_outs set outs_amount=outs_amount+1 where added_date=?", date('Y-m-d')) == 0)
                    {
                        sql_pr("insert into $config[tables_prefix]bill_outs set outs_amount=1, added_date=?", date('Y-m-d'));
                    }
                    if ($_POST['mode'] == 'async')
                    {
                        async_return_request_status(null, $url);
                    } else
                    {
                        header("Location: $url");
                        die;
                    }
                }
            } elseif ($payment_option == 3)
            {
                $sms_transaction = mr2array_single(sql_pr("select * from $config[tables_prefix]bill_transactions where access_code=? and type_id in (1,10)", $sms_passcode));
                if ($sms_transaction['transaction_id'] > 0)
                {
                    $sms_transaction_id = $sms_transaction['transaction_id'];
                    if ($sms_transaction['tokens_granted'] > 0)
                    {
                        $status_id = 2;
                    } else
                    {
                        $status_id = 3;
                    }

                    $transaction_status_id = 1;
                    if ($sms_transaction['tokens_granted'] > 0)
                    {
                        $transaction_status_id = 2;
                    }
                    sql_pr("update $config[tables_prefix]users set status_id=?, tokens_available=tokens_available+? where user_id=?", $status_id, $sms_transaction['tokens_granted'], $user_id);
                    sql_pr("update $config[tables_prefix]bill_transactions set user_id=?, ip=?, country_code=lower(?), status_id=$transaction_status_id, access_code='' where transaction_id=?", $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), $sms_transaction_id);

                    $_SESSION['status_id'] = $status_id;
                    $_SESSION['tokens_available'] = mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?", $user_id));
                }
            }

            if ($_POST['mode']=='async')
            {
                $smarty->assign('async_submit_successful','true');
                return '';
            } else {
                header("Location: ?action=payment_done");die;
            }
        } elseif ($_POST['mode']=='async')
        {
            async_return_request_status($errors_async);
        }
        $smarty->assign('errors',$errors);
    }

    if (isset($block_config['enable_card_payment']))
    {
        $service_id=intval($_REQUEST['service_id']);
        $service_provider=mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where status_id=1 and provider_id=?",$service_id));
        if (intval($service_provider['provider_id'])==0)
        {
            $service_provider=mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where status_id=1 and is_default=1"));
            $service_id=intval($service_provider['provider_id']);
        }
        $card_packages=mr2array(sql_pr("select * from $config[tables_prefix]card_bill_packages where status_id=1 and scope_id in (0,2) and provider_id=? order by sort_id asc",$service_id));
        if (count($card_packages)>0)
        {
            foreach ($card_packages as $k=>$v)
            {
                $card_packages[$k]['payment_page_url']=upgradeReplaceRuntimeParams($card_packages[$k]['payment_page_url']);
                if ($v['include_countries']!='')
                {
                    $include_countries=array_map('strtolower',array_map('trim',explode(',',$v['include_countries'])));
                    if (!in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$include_countries))
                    {
                        unset($card_packages[$k]);
                    }
                }
                if ($v['exclude_countries']!='')
                {
                    $exclude_countries=array_map('strtolower',array_map('trim',explode(',',$v['exclude_countries'])));
                    if (in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$exclude_countries))
                    {
                        unset($card_packages[$k]);
                    }
                }
            }
            $smarty->assign('card_packages',$card_packages);
        }
        if (intval($service_provider['provider_id'])>0)
        {
            $smarty->assign('service_id',$service_id);
            $smarty->assign('service_provider',$service_provider);
        }

        $card_providers=mr2array(sql_pr("select provider_id, title from $config[tables_prefix]card_bill_providers where status_id=1 order by is_default desc"));
        if (count($card_providers))
        {
            foreach ($card_providers as $k=>$card_provider)
            {
                $card_providers[$k]['packages']=mr2array(sql_pr("select * from $config[tables_prefix]card_bill_packages where status_id=1 and scope_id in (0,2) and provider_id=? order by sort_id asc",$card_provider['provider_id']));
                foreach ($card_providers[$k]['packages'] as $k2=>$package)
                {
                    $card_providers[$k]['packages'][$k2]['payment_page_url']=upgradeReplaceRuntimeParams($package['payment_page_url']);
                    if ($package['include_countries']!='')
                    {
                        $include_countries=array_map('strtolower',array_map('trim',explode(',',$package['include_countries'])));
                        if (!in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$include_countries))
                        {
                            unset($card_providers[$k]['packages'][$k2]);
                        }
                    }
                    if ($package['exclude_countries']!='')
                    {
                        $exclude_countries=array_map('strtolower',array_map('trim',explode(',',$package['exclude_countries'])));
                        if (in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$exclude_countries))
                        {
                            unset($card_providers[$k]['packages'][$k2]);
                        }
                    }
                }
            }
            $smarty->assign('card_providers',$card_providers);
        }
    }
    if (isset($block_config['enable_sms_payment']))
    {
        $sms_packages=mr2array(sql_pr("select * from $config[tables_prefix]sms_bill_packages where status_id=1 and provider_id=(select provider_id from $config[tables_prefix]sms_bill_providers where status_id=1) order by sort_id asc"));
        if (count($sms_packages)>0)
        {
            $provider_internal_id=mr2string(sql_pr("select internal_id from $config[tables_prefix]sms_bill_providers where status_id=1"));
            require_once("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php");
            $message_text_func="{$provider_internal_id}_get_sms_message_text";
            foreach ($sms_packages as $k=>$v)
            {
                $sms_packages[$k]['countries']=mr2array(sql_pr("select * from $config[tables_prefix]sms_bill_countries where package_id=? order by sort_id asc",$v['package_id']));
                foreach ($sms_packages[$k]['countries'] as $k1=>$v1)
                {
                    $sms_packages[$k]['countries'][$k1]['operators']=mr2array(sql_pr("select * from $config[tables_prefix]sms_bill_operators where country_id=? order by sort_id asc",$v1['country_id']));
                    foreach ($sms_packages[$k]['countries'][$k1]['operators'] as $k2=>$v2)
                    {
                        $sms_packages[$k]['countries'][$k1]['operators'][$k2]['sms_message_text']=$message_text_func($v2['prefix'],$v['external_id']);
                    }
                }
            }
            $smarty->assign('sms_packages',$sms_packages);
            $smarty->assign('geo_code',strtolower($_SERVER['GEOIP_COUNTRY_CODE']));
        }
    }
    if (isset($block_config['enable_access_codes']))
    {
        $smarty->assign('access_codes',mr2array_list(sql_pr("select access_code from $config[tables_prefix]bill_transactions where status_id=4 and access_code!=''")));
    }

    if (($_SESSION['user_id']<1 || $_SESSION['status_id']>=3) && !isset($_GET['action']))
    {
        if ($_GET['mode']=='async')
        {
            header('HTTP/1.0 403 Forbidden');die;
        } else {
            return "status_302: $config[project_url]";
        }
    }

    if (isset($block_config['default_access_option']))
    {
        if (count($_POST)==0)
        {
            $_POST['payment_option']=$block_config['default_access_option'];
        }
    }

    return '';
}

function upgradeGetHash($block_config)
{
    return "nocache";
}

function upgradeCacheControl($block_config)
{
    return "nocache";
}

function upgradeMetaData()
{
    return array(
        // paid access
        array("name"=>"enable_card_payment",   "group"=>"paid_access", "type"=>"",            "is_required"=>0),
        array("name"=>"enable_sms_payment",    "group"=>"paid_access", "type"=>"",            "is_required"=>0, "is_deprecated"=>1),
        array("name"=>"enable_access_codes",   "group"=>"paid_access", "type"=>"",            "is_required"=>0),
        array("name"=>"default_access_option", "group"=>"paid_access", "type"=>"CHOICE[2,3]", "is_required"=>0),
    );
}

function upgradeJavascript($block_config)
{
    global $config;

    return "KernelTeamVideoSharingForms.js?v={$config['project_version']}";
}

function upgradeReplaceRuntimeParams($url)
{
    global $runtime_params;

    if (is_array($runtime_params))
    {
        foreach ($runtime_params as $param)
        {
            $var=trim($param['name']);
            $val=$_SESSION['runtime_params'][$var];
            if (strlen($val)==0)
            {
                $val=trim($param['default_value']);
            }
            if ($var<>'')
            {
                $url=str_replace("%$var%",$val,$url);
            }
        }
    }
    return $url;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
