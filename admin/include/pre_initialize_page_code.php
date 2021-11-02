<?php
/* Developed by Kernel Team.
   http://kernel-team.com

   All php code within this file will be executed before every website page is initialized.
*/

global $page_id, $config;

$action = $_POST['action'];
$type = $_REQUEST['type'];

if ($page_id === 'member_profile_view' && $type === 'upload_video' && $action == 'add_new_complete') {
    $user_info = $_SESSION['user_info'];
    $country_id = $user_info['country_id'];

    $_POST['custom1'] = $country_id;
}

if ($_SESSION['user_id']>0)
{
    $count = mr2number(sql_pr("select count(*) from ktvs_dvds where user_id=?",$_SESSION['user_id']));
    if ($count>0)
    {
        $config['dvds_count'] = $count;
        $config['dvds'] = mr2array_single(sql_pr("select * from ktvs_dvds where user_id=?",$_SESSION['user_id']));
    } else {
        $config['dvds_count'] = 0;
    }

}