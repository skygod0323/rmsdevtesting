{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	<h1 data-children="options_main" class="lm_collapse">{{$lang.settings.submenu_group_settings}}</h1>
	<ul id="options_main">
		{{if $page_name=='options.php' && $smarty.request.page==''}}
			<li><span>{{$lang.settings.submenu_option_personal_settings}}</span></li>
		{{else}}
			<li><a href="options.php">{{$lang.settings.submenu_option_personal_settings}}</a></li>
		{{/if}}

		{{if in_array('system|system_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='general_settings'}}
				<li><span>{{$lang.settings.submenu_option_system_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=general_settings">{{$lang.settings.submenu_option_system_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='memberzone_settings'}}
				<li><span>{{$lang.settings.submenu_option_memberzone_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=memberzone_settings">{{$lang.settings.submenu_option_memberzone_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|antispam_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='antispam_settings'}}
				<li><span>{{$lang.settings.submenu_option_antispam_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=antispam_settings">{{$lang.settings.submenu_option_antispam_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|website_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='website_settings'}}
				<li><span>{{$lang.settings.submenu_option_website_ui_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=website_settings">{{$lang.settings.submenu_option_website_ui_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|stats_settings',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='stats_settings'}}
				<li><span>{{$lang.settings.submenu_option_stats_settings}}</span></li>
			{{else}}
				<li><a href="options.php?page=stats_settings">{{$lang.settings.submenu_option_stats_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('system|customization',$smarty.session.permissions)}}
			{{if $page_name=='options.php' && $smarty.request.page=='customization'}}
				<li><span>{{$lang.settings.submenu_option_customization}}</span></li>
			{{else}}
				<li><a href="options.php?page=customization">{{$lang.settings.submenu_option_customization}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
	{{if in_array('system|player_settings',$smarty.session.permissions) || in_array('system|vast_profiles',$smarty.session.permissions)}}
		<h1 data-children="options_player" class="lm_collapse">{{$lang.settings.submenu_group_player}}</h1>
		<ul id="options_player">
			{{if in_array('system|player_settings',$smarty.session.permissions)}}
				{{if $page_name=='player.php' && $smarty.request.page!='embed'}}
					<li><span>{{$lang.settings.submenu_option_player_settings}}</span></li>
				{{else}}
					<li><a href="player.php">{{$lang.settings.submenu_option_player_settings}}</a></li>
				{{/if}}

				{{if $page_name=='player.php' && $smarty.request.page=='embed'}}
					<li><span>{{$lang.settings.submenu_option_embed_settings}}</span></li>
				{{else}}
					<li><a href="player.php?page=embed">{{$lang.settings.submenu_option_embed_settings}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('system|vast_profiles',$smarty.session.permissions)}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='vast_profiles.php'}}
					<li><span>{{$lang.settings.submenu_option_vast_profiles_list}}</span></li>
				{{else}}
					<li><a href="vast_profiles.php">{{$lang.settings.submenu_option_vast_profiles_list}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='vast_profiles.php'}}
					<li><span>{{$lang.settings.submenu_option_add_vast_profile}}</span></li>
				{{else}}
					<li><a href="vast_profiles.php?action=add_new">{{$lang.settings.submenu_option_add_vast_profile}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|formats',$smarty.session.permissions)}}
		<h1 data-children="options_formats" class="lm_collapse">{{$lang.settings.submenu_group_formats}}</h1>
		<ul id="options_formats">
			{{if $config.installation_type>=2}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='formats_videos.php'}}
					<li><span>{{$lang.settings.submenu_option_formats_videos_list}}</span></li>
				{{else}}
					<li><a href="formats_videos.php">{{$lang.settings.submenu_option_formats_videos_list}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='formats_videos.php'}}
					<li><span>{{$lang.settings.submenu_option_add_format_video}}</span></li>
				{{else}}
					<li><a href="formats_videos.php?action=add_new">{{$lang.settings.submenu_option_add_format_video}}</a></li>
				{{/if}}
			{{else}}
				{{if $page_name=='formats_videos_basic.php'}}
					<li><span>{{$lang.settings.submenu_option_main_format_video}}</span></li>
				{{else}}
					<li><a href="formats_videos_basic.php">{{$lang.settings.submenu_option_main_format_video}}</a></li>
				{{/if}}
			{{/if}}

			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='formats_screenshots.php'}}
				<li><span>{{$lang.settings.submenu_option_formats_screenshots_list}}</span></li>
			{{else}}
				<li><a href="formats_screenshots.php">{{$lang.settings.submenu_option_formats_screenshots_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='formats_screenshots.php'}}
				<li><span>{{$lang.settings.submenu_option_add_format_screenshot}}</span></li>
			{{else}}
				<li><a href="formats_screenshots.php?action=add_new">{{$lang.settings.submenu_option_add_format_screenshot}}</a></li>
			{{/if}}

			{{if $config.installation_type==4}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='formats_albums.php'}}
					<li><span>{{$lang.settings.submenu_option_formats_albums_list}}</span></li>
				{{else}}
					<li><a href="formats_albums.php">{{$lang.settings.submenu_option_formats_albums_list}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='formats_albums.php'}}
					<li><span>{{$lang.settings.submenu_option_add_format_album}}</span></li>
				{{else}}
					<li><a href="formats_albums.php?action=add_new">{{$lang.settings.submenu_option_add_format_album}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|servers',$smarty.session.permissions)}}
		<h1 data-children="options_storage_servers" class="lm_collapse">{{$lang.settings.submenu_group_storage_servers}}</h1>
		<ul id="options_storage_servers">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='add_new_group' && $smarty.get.action!='change' && $smarty.get.action!='change_group' && $page_name=='servers.php'}}
				<li><span>{{$lang.settings.submenu_option_storage_servers_list}}</span></li>
			{{else}}
				<li><a href="servers.php">{{$lang.settings.submenu_option_storage_servers_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='servers.php'}}
				<li><span>{{$lang.settings.submenu_option_add_storage_server}}</span></li>
			{{else}}
				<li><a href="servers.php?action=add_new">{{$lang.settings.submenu_option_add_storage_server}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new_group' && $page_name=='servers.php'}}
				<li><span>{{$lang.settings.submenu_option_add_storage_server_group}}</span></li>
			{{else}}
				<li><a href="servers.php?action=add_new_group">{{$lang.settings.submenu_option_add_storage_server_group}}</a></li>
			{{/if}}
		</ul>
		<h1 data-children="options_conversion_servers" class="lm_collapse">{{$lang.settings.submenu_group_conversion_servers}}</h1>
		<ul id="options_conversion_servers">
			{{if $config.installation_type>=3}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='servers_conversion.php'}}
					<li><span>{{$lang.settings.submenu_option_conversion_servers_list}}</span></li>
				{{else}}
					<li><a href="servers_conversion.php">{{$lang.settings.submenu_option_conversion_servers_list}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='servers_conversion.php'}}
					<li><span>{{$lang.settings.submenu_option_add_conversion_server}}</span></li>
				{{else}}
					<li><a href="servers_conversion.php?action=add_new">{{$lang.settings.submenu_option_add_conversion_server}}</a></li>
				{{/if}}
			{{else}}
				{{if $page_name=='servers_conversion_basic.php'}}
					<li><span>{{$lang.settings.submenu_option_main_conversion_server}}</span></li>
				{{else}}
					<li><a href="servers_conversion_basic.php">{{$lang.settings.submenu_option_main_conversion_server}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('system|localization',$smarty.session.permissions)}}
		<h1 data-children="options_localization" class="lm_collapse">{{$lang.settings.submenu_group_localization}}</h1>
		<ul id="options_localization">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='languages.php'}}
				<li><span>{{$lang.settings.submenu_option_languages_list}}</span></li>
			{{else}}
				<li><a href="languages.php">{{$lang.settings.submenu_option_languages_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='languages.php'}}
				<li><span>{{$lang.settings.submenu_option_add_language}}</span></li>
			{{else}}
				<li><a href="languages.php?action=add_new">{{$lang.settings.submenu_option_add_language}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
</div>