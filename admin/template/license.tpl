<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{$lang.system.language_code}}">
<head>
	<title>{{$page_title}} / {{$config.project_version}}</title>
	{{assign var="hash" value="`$config.project_version``$config.ahv`"}}
	{{assign var="hash" value=$hash|md5|substr:0:16}}
	<link type="text/css" rel="stylesheet" href="styles/{{$smarty.session.userdata.skin}}.css?v={{$hash}}"/>
	<script type="text/javascript" src="js/admin.js?v={{$hash}}"></script>
	<script type="text/javascript" src="js/custom.js?v={{$hash}}"></script>
	{{if $smarty.session.userdata.is_popups_enabled=='1' && $supports_popups=='1'}}
		<script type="text/javascript" src="js/config.php?v={{$hash}}&amp;is_popup=true"></script>
	{{else}}
		<script type="text/javascript" src="js/config.php?v={{$hash}}"></script>
	{{/if}}

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>
	<div id="content">
		<table id="layout_root">
			<tr>
				<td id="layout_main_left"><img src="images/t.gif" alt="" width="1" height="500"/></td>
				<td id="layout_main_start">
					<div id="header"><div id="header_inner">
						<div id="server_info">
							{{$lang.common.website}}: <a href="{{$smarty.session.admin_panel_project_url|default:$config.project_url}}" rel="external">{{$config.project_url}}</a>&nbsp;&nbsp;/&nbsp;
							{{$lang.common.server_time}}: <span class="value">{{$smarty.session.server_time|date_format:$smarty.session.userdata.full_date_format}}</span>&nbsp;&nbsp;/&nbsp;
							{{if in_array('system|administration',$smarty.session.permissions)}}
								{{$lang.common.server_la}}: <span class="value">{{$smarty.session.server_la|number_format:2}}</span>&nbsp;&nbsp;/&nbsp;
								{{$lang.common.server_free_space}}: <span class="value">{{$smarty.session.server_free_space}}</span>&nbsp;&nbsp;/&nbsp;
							{{/if}}
							{{if in_array('system|background_tasks',$smarty.session.permissions)}}
								{{$lang.common.server_processes}}:
								{{if $smarty.session.server_processes>0}}
									<a class="value" href="background_tasks.php?no_filter=true">{{$smarty.session.server_processes}}</a>
								{{else}}
									<span class="value">{{$smarty.session.server_processes}}</span>
								{{/if}}
								{{if $smarty.session.server_processes_error>0}}
									(<a class="value" href="background_tasks.php?no_filter=true&amp;se_status_id=2">{{$smarty.session.server_processes_error}}</a>)
								{{/if}}
								{{if $smarty.session.server_processes_paused==1}}
									{{$lang.common.server_processes_paused}}
								{{/if}}
								&nbsp;/&nbsp;
							{{/if}}
							<a href="documentation.php" rel="external">{{$lang.common.documentation}}</a>&nbsp;&nbsp;/&nbsp;
							<a href="https://www.kernel-video-sharing.com/forum/" rel="external">{{$lang.common.forum}}</a>&nbsp;&nbsp;/&nbsp;
							<a href="https://www.kernel-scripts.com/support/index.php?/Tickets/Submit/" rel="external">{{$lang.common.support}}</a>
						</div>
						<div id="user_info"><b>{{$smarty.session.userdata.login}}</b> [<a href="logout.php">{{$lang.common.log_off}}</a>]</div>
					</div></div>
					<div id="license_page">
						{{if $curl_error==1}}
							{{$lang.validation.rental_curl_error}}
							<br/>
							{{$curl_details|replace:"\n":"<br/>"}}
						{{elseif $exec_error==1}}
							{{$lang.validation.rental_exec_error}}
						{{else}}
							{{$lang.validation.rental_stopped|smarty:nodefaults}}
						{{/if}}
					</div>
				</td>
				<td id="layout_main_right"><img src="images/t.gif" alt="" width="1" height="500"/></td>
			</tr>
			<tr>
				<td id="layout_bottom_left"><img src="images/t.gif" alt="" width="10" height="1"/></td>
				<td id="layout_bottom_main"><img src="images/t.gif" alt="" width="1220" height="1"/></td>
				<td id="layout_bottom_right"><img src="images/t.gif" alt="" width="10" height="1"/></td>
			</tr>
		</table>
	</div>
	<div id="block_ui"></div>
	<script type="text/javascript">
		prepareAdminPanel();
	</script>
</body>
</html>