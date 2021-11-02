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
	{{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1' || $smarty.session.userdata.is_wysiwyg_enabled_albums=='1' || $smarty.session.userdata.is_wysiwyg_enabled_posts=='1' || $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}
		<script type="text/javascript" src="tinymce/tinymce.min.js"></script>
		<script type="text/javascript" src="js/TinyMCEConfig.js"></script>
	{{/if}}

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>
	<div id="content">
		{{if $smarty.session.userdata.is_popups_enabled=='1' && $supports_popups=='1'}}
			<table id="layout_root">
				<tr>
					<td id="layout_main_left"><img src="images/t.gif" alt="" width="1" height="500"/></td>
					<td id="layout_main_popup">
						<div id="header_popup"><div id="header_popup_inner"></div></div>
						{{include file=$template}}
					</td>
					<td id="layout_main_right"><img src="images/t.gif" alt="" width="1" height="500"/></td>
				</tr>
				<tr>
					<td id="layout_bottom_left"><img src="images/t.gif" alt="" width="10" height="1"/></td>
					<td id="layout_bottom_main"><img src="images/t.gif" alt="" width="1000" height="1"/></td>
					<td id="layout_bottom_right"><img src="images/t.gif" alt="" width="10" height="1"/></td>
				</tr>
			</table>
		{{else}}
			<table id="layout_root">
				<tr>
					<td id="layout_main_left"><img src="images/t.gif" alt="" width="1" height="500"/></td>
					{{if $left_menu!='no'}}
					<td id="layout_main_main">
					{{else}}
					<td id="layout_main_start">
					{{/if}}
						<div id="header"><div id="header_inner">
							<div id="server_info">
								{{$lang.common.website}}: <a href="{{$smarty.session.admin_panel_project_url|default:$config.project_url}}" rel="external">{{$config.project_url}}</a>&nbsp;&nbsp;/&nbsp;
								{{$lang.common.server_time}}: <span class="value">{{$smarty.session.server_time|date_format:$smarty.session.userdata.full_date_format}}</span>&nbsp;&nbsp;/&nbsp;
								{{if in_array('system|administration',$smarty.session.permissions)}}
									{{$lang.common.server_la}}: <span class="value">{{$smarty.session.server_la|number_format:2}}</span>&nbsp;&nbsp;/&nbsp;
									{{$lang.common.server_free_space}}: <span class="value">{{$smarty.session.server_free_space}} ({{$smarty.session.server_free_space_pc|intval}}%)</span>&nbsp;&nbsp;/&nbsp;
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
								{{include file="ap_custom_header.tpl"}}
							</div>
							<div id="user_info"><b>{{$smarty.session.userdata.login}}</b> [<a href="logout.php">{{$lang.common.log_off}}</a>]</div>
						</div></div>
						<div id="main_menu">
							<div>
								<a href="start.php" class="first {{if $page_name=='start.php'}}active{{/if}}">{{$lang.main_menu.home}}</a>

								{{if in_array('videos|view',$smarty.session.permissions) || in_array('dvds|view',$smarty.session.permissions) || in_array('dvds_groups|view',$smarty.session.permissions)}}
									{{if in_array('videos|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='videos.php'}}
									{{elseif in_array('dvds|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='dvds.php'}}
									{{elseif in_array('dvds_groups|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='dvds_groups.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='videos.php' || $page_name=='videos_screenshots.php' || $page_name=='videos_screenshots_grabbing.php' || $page_name=='dvds.php' || $page_name=='dvds_groups.php' || $page_name=='videos_import.php' || $page_name=='videos_export.php' || $page_name=='videos_select.php' || $page_name=='videos_mass_edit.php' || $page_name=='videos_feeds_import.php' || $page_name=='videos_feeds_export.php'}}class="active"{{/if}}>{{$lang.main_menu.videos}}</a>
								{{/if}}

								{{if in_array('albums|view',$smarty.session.permissions)}}
									<a href="albums.php" {{if $page_name=='albums.php' || $page_name=='albums_import.php' || $page_name=='albums_export.php' || $page_name=='albums_select.php' || $page_name=='albums_mass_edit.php'}}class="active"{{/if}}>{{$lang.main_menu.albums}}</a>
								{{/if}}

								{{if in_array('posts|view',$smarty.session.permissions) || in_array('posts_types|view',$smarty.session.permissions)}}
									{{if in_array('posts|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='posts.php'}}
									{{else}}
										{{assign var=menu_url value='posts_types.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='posts.php' || $page_name=='posts_types.php' || $locked_post_type_id>0}}class="active"{{/if}}>{{$lang.main_menu.posts}}</a>
								{{/if}}

								{{if in_array('users|view',$smarty.session.permissions) || in_array('feedbacks|view',$smarty.session.permissions) || in_array('messages|view',$smarty.session.permissions) || in_array('playlists|view',$smarty.session.permissions) || in_array('billing|view',$smarty.session.permissions) || in_array('payouts|view',$smarty.session.permissions)}}
									{{if in_array('users|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='users.php'}}
									{{elseif in_array('feedbacks|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='feedbacks.php'}}
									{{elseif in_array('messages|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='messages.php'}}
									{{elseif in_array('playlists|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='playlists.php'}}
									{{elseif in_array('payouts|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='payouts.php'}}
									{{else}}
										{{assign var=menu_url value='card_bill_configurations.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='users.php' || $page_name=='emailing.php' || $page_name=='comments.php' || $page_name=='feedbacks.php' || $page_name=='flags_messages.php' || $page_name=='users_blogs.php' || $page_name=='messages.php' || $page_name=='playlists.php' || $page_name=='card_bill_configurations.php' || $page_name=='sms_bill_configurations.php' || $page_name=='bill_transactions.php' || $page_name=='payouts.php'}}class="active"{{/if}}>{{$lang.main_menu.memberzone}}</a>
								{{/if}}

								{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) || in_array('stats|view_content_stats',$smarty.session.permissions) || in_array('stats|view_user_stats',$smarty.session.permissions) || in_array('stats|manage_referers',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
									{{if in_array('stats|view_traffic_stats',$smarty.session.permissions)}}
										{{assign var=menu_url value='stats_in.php'}}
									{{elseif in_array('stats|view_content_stats',$smarty.session.permissions)}}
										{{assign var=menu_url value='stats_videos.php'}}
									{{elseif in_array('stats|view_user_stats',$smarty.session.permissions)}}
										{{assign var=menu_url value='stats_transactions.php'}}
									{{elseif in_array('stats|manage_referers',$smarty.session.permissions)}}
										{{assign var=menu_url value='stats_referers_list.php'}}
									{{elseif in_array('system|administration',$smarty.session.permissions)}}
										{{assign var=menu_url value='stats_cleanup.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='stats_in.php' || $page_name=='stats_country.php' || $page_name=='stats_out.php' || $page_name=='stats_player.php' || $page_name=='stats_referer.php' || $page_name=='stats_referers_list.php' || $page_name=='stats_search.php' || $page_name=='stats_embed.php' || $page_name=='stats_overload.php' || $page_name=='stats_videos.php' || $page_name=='stats_albums.php' || $page_name=='stats_transactions.php' || $page_name=='stats_users.php' || $page_name=='stats_users_logins.php' || $page_name=='stats_users_content.php' || $page_name=='stats_users_purchases.php' || $page_name=='stats_users_sellings.php' || $page_name=='stats_users_awards.php' || $page_name=='stats_users_initial_transactions.php' || $page_name=='stats_cleanup.php'}}class="active"{{/if}}>{{$lang.main_menu.stats}}</a>
								{{/if}}

								{{if in_array('categories|view',$smarty.session.permissions) || in_array('category_groups|view',$smarty.session.permissions) || in_array('models|view',$smarty.session.permissions) || in_array('models_groups|view',$smarty.session.permissions) || in_array('tags|view',$smarty.session.permissions) || in_array('content_sources|view',$smarty.session.permissions) || in_array('content_sources_groups|view',$smarty.session.permissions) || in_array('flags|view',$smarty.session.permissions)}}
									{{if in_array('categories|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='categories.php'}}
									{{elseif in_array('category_groups|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='categories_groups.php'}}
									{{elseif in_array('models|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='models.php'}}
									{{elseif in_array('models_groups|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='models_groups.php'}}
									{{elseif in_array('content_sources|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='content_sources.php'}}
									{{elseif in_array('content_sources_groups|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='content_sources_groups.php'}}
									{{elseif in_array('tags|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='tags.php'}}
									{{else}}
										{{assign var=menu_url value='flags.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='categories.php' || $page_name=='categories_groups.php' || $page_name=='models.php' || $page_name=='models_groups.php' || $page_name=='tags.php' || $page_name=='content_sources.php' || $page_name=='content_sources_groups.php' || $page_name=='flags.php'}}class="active"{{/if}}>{{$lang.main_menu.categorization}}</a>
								{{/if}}

								{{if in_array('website_ui|view',$smarty.session.permissions) || in_array('advertising|view',$smarty.session.permissions)}}
									{{if in_array('website_ui|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='project_pages.php'}}
									{{else}}
										{{assign var=menu_url value='project_spots.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='project_spots.php' || $page_name=='project_blocks.php' || $page_name=='project_pages_components.php' || $page_name=='project_pages_lang_files.php' || $page_name=='project_pages_lang_texts.php' || $page_name=='project_pages.php' || $page_name=='templates_search.php' || $page_name=='project_pages_global.php' || $page_name=='project_pages_history.php' || $page_name=='project_theme.php'}}class="active"{{/if}}>{{$lang.main_menu.website_ui}}</a>
								{{/if}}

								{{if in_array('plugins|view',$smarty.session.permissions)}}
									<a href="plugins.php" {{if $page_name=='plugins.php'}}class="active"{{/if}}>{{$lang.main_menu.plugins}}</a>
								{{/if}}

								{{if in_array('system|background_tasks',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions) || in_array('localization|view',$smarty.session.permissions)}}
									{{if in_array('system|background_tasks',$smarty.session.permissions)}}
										{{assign var=menu_url value='background_tasks.php'}}
									{{elseif in_array('localization|view',$smarty.session.permissions)}}
										{{assign var=menu_url value='translations_summary.php'}}
									{{else}}
										{{assign var=menu_url value='installation.php'}}
									{{/if}}
									<a href="{{$menu_url}}" {{if $page_name=='admin_users.php' || $page_name=='admin_users_groups.php' || $page_name=='log_logins.php' || $page_name=='log_audit.php' || $page_name=='log_bill.php' || $page_name=='log_feeds.php' || $page_name=='log_imports.php' || $page_name=='log_background_tasks.php' || $page_name=='background_tasks.php' || $page_name=='installation.php' || $page_name=='translations.php' || $page_name=='translations_summary.php'}}class="active"{{/if}}>{{$lang.main_menu.administration}}</a>
								{{/if}}

								<a href="options.php" {{if $page_name=='formats_videos_basic.php' || $page_name=='formats_videos.php' || $page_name=='formats_screenshots.php' || $page_name=='formats_albums.php' || $page_name=='options.php' || $page_name=='player.php' || $page_name=='vast_profiles.php' || $page_name=='servers.php' || $page_name=='servers_test.php' || $page_name=='servers_conversion.php' || $page_name=='servers_conversion_basic.php' || $page_name=='languages.php'}}class="active"{{/if}}>{{$lang.main_menu.settings}}</a>

								{{include file="ap_custom_menu.tpl"}}
							</div>
						</div>
						<div id="main_menu_margin"></div>
						{{if $left_menu=='no'}}
							{{include file="ap_custom_main.tpl"}}
							{{include file=$template}}
							{{include file="ap_custom_footer.tpl"}}
						{{else}}
							<table id="main_pane">
								<tr>
									<td id="left_pane">{{if $left_menu!=''}}{{include file=$left_menu}}{{else}}{{include file="menu_`$template`"}}{{/if}}</td>
									<td id="center_pane">
										{{include file="ap_custom_main.tpl"}}
										{{include file=$template}}
										{{include file="ap_custom_footer.tpl"}}
									</td>
								</tr>
								<tr>
									<td id="left_pane_spacer"><img src="images/t.gif" alt="" width="231" height="1"/></td>
									<td></td>
								</tr>
							</table>
						{{/if}}
					</td>
					<td id="layout_main_right"><img src="images/t.gif" alt="" width="1" height="500"/></td>
				</tr>
				<tr>
					<td id="layout_bottom_left"><img src="images/t.gif" alt="" width="10" height="1"/></td>
					<td id="layout_bottom_main">
						{{assign var="admin_page_generation_time_end" value=1|microtime}}
						{{assign var="admin_page_generation_memory_end" value=0|memory_get_peak_usage}}
						{{assign var="admin_page_generation_time" value=$admin_page_generation_time_end-$smarty.session.admin_page_generation_time_start}}
						{{assign var="admin_page_generation_time" value=$admin_page_generation_time|number_format:2:".":""}}
						{{assign var="admin_page_generation_memory" value=$admin_page_generation_memory_end-$smarty.session.admin_page_generation_memory_start}}
						{{assign var="admin_page_generation_memory" value=$admin_page_generation_memory/1024/1024|number_format:2:".":""}}
						<div id="layout_bottom_info">{{$lang.common.generated_message|replace:"%1%":$admin_page_generation_time|replace:"%2%":$admin_page_generation_memory}}</div>
					</td>
					<td id="layout_bottom_right"><img src="images/t.gif" alt="" width="10" height="1"/></td>
				</tr>
			</table>
		{{/if}}
	</div>
	<div id="block_ui"></div>
	<script type="text/javascript">
		prepareAdminPanel();
	</script>
</body>
</html>