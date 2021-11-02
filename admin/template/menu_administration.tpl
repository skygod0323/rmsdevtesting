{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('system|background_tasks',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
		<h1 data-children="administration_main" class="lm_collapse">{{$lang.settings.submenu_group_administration}}</h1>
		<ul id="administration_main">
			{{if in_array('system|background_tasks',$smarty.session.permissions)}}
				{{if $page_name=='background_tasks.php'}}
					<li><span>{{$lang.settings.submenu_option_background_tasks}}</span></li>
				{{else}}
					<li><a href="background_tasks.php">{{$lang.settings.submenu_option_background_tasks}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('system|administration',$smarty.session.permissions)}}
				{{if $page_name=='installation.php'}}
					<li><span>{{$lang.settings.submenu_option_installation}}</span></li>
				{{else}}
					<li><a href="installation.php">{{$lang.settings.submenu_option_installation}}</a></li>
				{{/if}}

				{{if $page_name=='log_logins.php'}}
					<li><span>{{$lang.settings.submenu_option_activity_log}}</span></li>
				{{else}}
					<li><a href="log_logins.php">{{$lang.settings.submenu_option_activity_log}}</a></li>
				{{/if}}

				{{if $config.is_clone_db!="true"}}
					{{if $page_name=='log_audit.php'}}
						<li><span>{{$lang.settings.submenu_option_audit_log}}</span></li>
					{{else}}
						<li><a href="log_audit.php">{{$lang.settings.submenu_option_audit_log}}</a></li>
					{{/if}}

					{{if $config.installation_type>=2}}
						{{if $page_name=='log_bill.php' && $smarty.get.action!='change'}}
							<li><span>{{$lang.settings.submenu_option_bill_log}}</span></li>
						{{else}}
							<li><a href="log_bill.php">{{$lang.settings.submenu_option_bill_log}}</a></li>
						{{/if}}
					{{/if}}

					{{if $page_name=='log_feeds.php'}}
						<li><span>{{$lang.settings.submenu_option_feeds_log}}</span></li>
					{{else}}
						<li><a href="log_feeds.php">{{$lang.settings.submenu_option_feeds_log}}</a></li>
					{{/if}}

					{{if $page_name=='log_imports.php' && $smarty.get.action!='change'}}
						<li><span>{{$lang.settings.submenu_option_imports_log}}</span></li>
					{{else}}
						<li><a href="log_imports.php">{{$lang.settings.submenu_option_imports_log}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}

			{{if in_array('system|background_tasks',$smarty.session.permissions)}}
				{{if $page_name=='log_background_tasks.php' && $smarty.get.action!='change'}}
					<li><span>{{$lang.settings.submenu_option_background_tasks_log}}</span></li>
				{{else}}
					<li><a href="log_background_tasks.php">{{$lang.settings.submenu_option_background_tasks_log}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if $smarty.session.userdata.is_superadmin>0}}
		<h1 data-children="administration_admins" class="lm_collapse">{{$lang.settings.submenu_group_admin_access}}</h1>
		<ul id="administration_admins">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='admin_users.php'}}
				<li><span>{{$lang.settings.submenu_option_admins_list}}</span></li>
			{{else}}
				<li><a href="admin_users.php">{{$lang.settings.submenu_option_admins_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='admin_users.php'}}
				<li><span>{{$lang.settings.submenu_option_add_admin}}</span></li>
			{{else}}
				<li><a href="admin_users.php?action=add_new">{{$lang.settings.submenu_option_add_admin}}</a></li>
			{{/if}}

			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='admin_users_groups.php'}}
				<li><span>{{$lang.settings.submenu_option_groups_list}}</span></li>
			{{else}}
				<li><a href="admin_users_groups.php">{{$lang.settings.submenu_option_groups_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='admin_users_groups.php'}}
				<li><span>{{$lang.settings.submenu_option_add_group}}</span></li>
			{{else}}
				<li><a href="admin_users_groups.php?action=add_new">{{$lang.settings.submenu_option_add_group}}</a></li>
			{{/if}}

			<li><a href="admin_users.php?action=reset_admin_cache">{{$lang.settings.submenu_option_reset_admin_cache}}</a></li>
		</ul>
	{{/if}}
	{{if in_array('localization|view',$smarty.session.permissions)}}
		<h1 data-children="administration_localization" class="lm_collapse">{{$lang.settings.submenu_group_localization}}</h1>
		<ul id="administration_localization">
			{{if $page_name=='translations_summary.php'}}
				<li><span>{{$lang.settings.submenu_option_translations_summary}}</span></li>
			{{else}}
				<li><a href="translations_summary.php">{{$lang.settings.submenu_option_translations_summary}}</a></li>
			{{/if}}
			{{if $smarty.get.action!='change' && $page_name=='translations.php'}}
				<li><span>{{$lang.settings.submenu_option_translations_list}}</span></li>
			{{else}}
				<li><a href="translations.php">{{$lang.settings.submenu_option_translations_list}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
</div>