{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('videos|view',$smarty.session.permissions)}}
		<h1 data-children="videos_main" class="lm_collapse">{{$lang.videos.submenu_group_videos}}</h1>
		<ul id="videos_main">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $smarty.get.action!='mark_deleted' && $smarty.get.action!='change_deleted' && $page_name=='videos.php'}}
				<li><span>{{$lang.videos.submenu_option_videos_list}}</span></li>
			{{else}}
				<li><a href="videos.php">{{$lang.videos.submenu_option_videos_list}}</a></li>
			{{/if}}

			{{if in_array('videos|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='videos.php'}}
					<li><span>{{$lang.videos.submenu_option_add_video}}</span></li>
				{{else}}
					<li><a href="videos.php?action=add_new">{{$lang.videos.submenu_option_add_video}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('videos|import',$smarty.session.permissions)}}
				{{if $page_name=='videos_import.php'}}
					<li><span>{{$lang.videos.submenu_option_import_videos}}</span></li>
				{{else}}
					<li><a href="videos_import.php">{{$lang.videos.submenu_option_import_videos}}</a></li>
				{{/if}}
			{{/if}}

			{{if in_array('videos|export',$smarty.session.permissions)}}
				{{if $page_name=='videos_export.php'}}
					<li><span>{{$lang.videos.submenu_option_export_videos}}</span></li>
				{{else}}
					<li><a href="videos_export.php">{{$lang.videos.submenu_option_export_videos}}</a></li>
				{{/if}}
			{{/if}}

			{{if $page_name=='videos_select.php'}}
				<li><span>{{$lang.videos.submenu_option_select_videos}}</span></li>
			{{else}}
				<li><a href="videos_select.php">{{$lang.videos.submenu_option_select_videos}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('videos|feeds_import',$smarty.session.permissions) || in_array('videos|feeds_export',$smarty.session.permissions)}}
		<h1 data-children="videos_feeds" class="lm_collapse">{{$lang.videos.submenu_group_feeds}}</h1>
		<ul id="videos_feeds">
			{{if in_array('videos|feeds_import',$smarty.session.permissions)}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='videos_feeds_import.php'}}
					<li><span>{{$lang.videos.submenu_option_feeds_import}}</span></li>
				{{else}}
					<li><a href="videos_feeds_import.php">{{$lang.videos.submenu_option_feeds_import}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='videos_feeds_import.php'}}
					<li><span>{{$lang.videos.submenu_option_add_feed_import}}</span></li>
				{{else}}
					<li><a href="videos_feeds_import.php?action=add_new">{{$lang.videos.submenu_option_add_feed_import}}</a></li>
				{{/if}}
			{{/if}}
			{{if in_array('videos|feeds_export',$smarty.session.permissions)}}
				{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='videos_feeds_export.php'}}
					<li><span>{{$lang.videos.submenu_option_feeds_export}}</span></li>
				{{else}}
					<li><a href="videos_feeds_export.php">{{$lang.videos.submenu_option_feeds_export}}</a></li>
				{{/if}}

				{{if $smarty.get.action=='add_new' && $page_name=='videos_feeds_export.php'}}
					<li><span>{{$lang.videos.submenu_option_add_feed_export}}</span></li>
				{{else}}
					<li><a href="videos_feeds_export.php?action=add_new">{{$lang.videos.submenu_option_add_feed_export}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('dvds|view',$smarty.session.permissions) || in_array('dvds_groups|view',$smarty.session.permissions)}}
		<h1 data-children="videos_dvds" class="lm_collapse">{{$lang.videos.submenu_group_dvds}}</h1>
		<ul id="videos_dvds">
			{{if $config.dvds_mode=='series'}}
				{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
					{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='dvds_groups.php'}}
						<li><span>{{$lang.videos.submenu_option_dvd_groups_list}}</span></li>
					{{else}}
						<li><a href="dvds_groups.php">{{$lang.videos.submenu_option_dvd_groups_list}}</a></li>
					{{/if}}

					{{if in_array('dvds_groups|add',$smarty.session.permissions)}}
						{{if $smarty.get.action=='add_new' && $page_name=='dvds_groups.php'}}
							<li><span>{{$lang.videos.submenu_option_add_dvd_group}}</span></li>
						{{else}}
							<li><a href="dvds_groups.php?action=add_new">{{$lang.videos.submenu_option_add_dvd_group}}</a></li>
						{{/if}}
					{{/if}}
				{{/if}}
				{{if in_array('dvds|view',$smarty.session.permissions)}}
					{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='dvds.php'}}
						<li><span>{{$lang.videos.submenu_option_dvds_list}}</span></li>
					{{else}}
						<li><a href="dvds.php">{{$lang.videos.submenu_option_dvds_list}}</a></li>
					{{/if}}

					{{if in_array('dvds|add',$smarty.session.permissions)}}
						{{if $smarty.get.action=='add_new' && $page_name=='dvds.php'}}
							<li><span>{{$lang.videos.submenu_option_add_dvd}}</span></li>
						{{else}}
							<li><a href="dvds.php?action=add_new">{{$lang.videos.submenu_option_add_dvd}}</a></li>
						{{/if}}
					{{/if}}
				{{/if}}
			{{else}}
				{{if in_array('dvds|view',$smarty.session.permissions)}}
					{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='dvds.php'}}
						<li><span>{{$lang.videos.submenu_option_dvds_list}}</span></li>
					{{else}}
						<li><a href="dvds.php">{{$lang.videos.submenu_option_dvds_list}}</a></li>
					{{/if}}

					{{if in_array('dvds|add',$smarty.session.permissions)}}
						{{if $smarty.get.action=='add_new' && $page_name=='dvds.php'}}
							<li><span>{{$lang.videos.submenu_option_add_dvd}}</span></li>
						{{else}}
							<li><a href="dvds.php?action=add_new">{{$lang.videos.submenu_option_add_dvd}}</a></li>
						{{/if}}
					{{/if}}
				{{/if}}

				{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
					{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='dvds_groups.php'}}
						<li><span>{{$lang.videos.submenu_option_dvd_groups_list}}</span></li>
					{{else}}
						<li><a href="dvds_groups.php">{{$lang.videos.submenu_option_dvd_groups_list}}</a></li>
					{{/if}}

					{{if in_array('dvds_groups|add',$smarty.session.permissions)}}
						{{if $smarty.get.action=='add_new' && $page_name=='dvds_groups.php'}}
							<li><span>{{$lang.videos.submenu_option_add_dvd_group}}</span></li>
						{{else}}
							<li><a href="dvds_groups.php?action=add_new">{{$lang.videos.submenu_option_add_dvd_group}}</a></li>
						{{/if}}
					{{/if}}
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if count($list_updates)>0}}
		<div class="left_dt">
			<h1 data-children="videos_updates" class="lm_collapse">{{$lang.videos.submenu_group_videos_by_date}}</h1>
			<table id="videos_updates">
				{{foreach item=item from=$list_updates}}
					<tr>
						<td>{{$item.post_date|date_format:$smarty.session.userdata.short_date_format}}</td>
						<td>{{$item.updates}}</td>
					</tr>
				{{/foreach}}
			</table>
		</div>
	{{/if}}
</div>