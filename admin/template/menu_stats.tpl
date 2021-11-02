{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('stats|view_traffic_stats',$smarty.session.permissions) || in_array('system|administration',$smarty.session.permissions)}}
		<h1 data-children="stats_traffic" class="lm_collapse">{{$lang.stats.submenu_group_stats}}</h1>
		<ul id="stats_traffic">
			{{if in_array('stats|view_traffic_stats',$smarty.session.permissions)}}
				{{if $page_name=='stats_in.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_in}}</span></li>
				{{else}}
					<li><a href="stats_in.php">{{$lang.stats.submenu_option_stats_in}}</a></li>
				{{/if}}
				{{if $page_name=='stats_country.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_country}}</span></li>
				{{else}}
					<li><a href="stats_country.php">{{$lang.stats.submenu_option_stats_country}}</a></li>
				{{/if}}
				{{if $page_name=='stats_referer.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_referer}}</span></li>
				{{else}}
					<li><a href="stats_referer.php">{{$lang.stats.submenu_option_stats_referer}}</a></li>
				{{/if}}
				{{if $page_name=='stats_out.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_out}}</span></li>
				{{else}}
					<li><a href="stats_out.php">{{$lang.stats.submenu_option_stats_out}}</a></li>
				{{/if}}
				{{if $page_name=='stats_player.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_player}}</span></li>
				{{else}}
					<li><a href="stats_player.php">{{$lang.stats.submenu_option_stats_player}}</a></li>
				{{/if}}
				{{if $page_name=='stats_embed.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_embed}}</span></li>
				{{else}}
					<li><a href="stats_embed.php">{{$lang.stats.submenu_option_stats_embed}}</a></li>
				{{/if}}
				{{if $page_name=='stats_overload.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_overload}}</span></li>
				{{else}}
					<li><a href="stats_overload.php">{{$lang.stats.submenu_option_stats_overload}}</a></li>
				{{/if}}
			{{/if}}
			{{if in_array('system|administration',$smarty.session.permissions)}}
				{{if $page_name=='stats_cleanup.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_cleanup}}</span></li>
				{{else}}
					<li><a href="stats_cleanup.php">{{$lang.stats.submenu_option_stats_cleanup}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|view_traffic_stats',$smarty.session.permissions)}}
		<h1 data-children="stats_search" class="lm_collapse">{{$lang.stats.submenu_group_stats_search}}</h1>
		<ul id="stats_search">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='stats_search.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_search}}</span></li>
			{{else}}
				<li><a href="stats_search.php">{{$lang.stats.submenu_option_stats_search}}</a></li>
			{{/if}}
			{{if in_array('stats|manage_search_queries',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='stats_search.php'}}
					<li><span>{{$lang.stats.submenu_option_add_searches}}</span></li>
				{{else}}
					<li><a href="stats_search.php?action=add_new">{{$lang.stats.submenu_option_add_searches}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
		<h1 data-children="stats_content" class="lm_collapse">{{$lang.stats.submenu_group_stats_content}}</h1>
		<ul id="stats_content">
			{{if $page_name=='stats_videos.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_videos}}</span></li>
			{{else}}
				<li><a href="stats_videos.php">{{$lang.stats.submenu_option_stats_videos}}</a></li>
			{{/if}}
			{{if $config.installation_type==4}}
				{{if $page_name=='stats_albums.php'}}
					<li><span>{{$lang.stats.submenu_option_stats_albums}}</span></li>
				{{else}}
					<li><a href="stats_albums.php">{{$lang.stats.submenu_option_stats_albums}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|view_user_stats',$smarty.session.permissions)}}
		<h1 data-children="stats_users" class="lm_collapse">{{$lang.stats.submenu_group_stats_users}}</h1>
		<ul id="stats_users">
			{{if $page_name=='stats_transactions.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_transactions}}</span></li>
			{{else}}
				<li><a href="stats_transactions.php">{{$lang.stats.submenu_option_stats_transactions}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users}}</span></li>
			{{else}}
				<li><a href="stats_users.php">{{$lang.stats.submenu_option_stats_users}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_logins.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users_logins}}</span></li>
			{{else}}
				<li><a href="stats_users_logins.php">{{$lang.stats.submenu_option_stats_users_logins}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_content.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users_content}}</span></li>
			{{else}}
				<li><a href="stats_users_content.php">{{$lang.stats.submenu_option_stats_users_content}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_purchases.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users_purchases}}</span></li>
			{{else}}
				<li><a href="stats_users_purchases.php">{{$lang.stats.submenu_option_stats_users_purchases}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_sellings.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users_sellings}}</span></li>
			{{else}}
				<li><a href="stats_users_sellings.php">{{$lang.stats.submenu_option_stats_users_sellings}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_awards.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users_awards}}</span></li>
			{{else}}
				<li><a href="stats_users_awards.php">{{$lang.stats.submenu_option_stats_users_awards}}</a></li>
			{{/if}}
			{{if $page_name=='stats_users_initial_transactions.php'}}
				<li><span>{{$lang.stats.submenu_option_stats_users_initial_transactions}}</span></li>
			{{else}}
				<li><a href="stats_users_initial_transactions.php">{{$lang.stats.submenu_option_stats_users_initial_transactions}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
	{{if in_array('stats|manage_referers',$smarty.session.permissions)}}
		<h1 data-children="stats_referers" class="lm_collapse">{{$lang.stats.submenu_group_referers}}</h1>
		<ul id="stats_referers">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='stats_referers_list.php'}}
				<li><span>{{$lang.stats.submenu_option_referers_list}}</span></li>
			{{else}}
				<li><a href="stats_referers_list.php">{{$lang.stats.submenu_option_referers_list}}</a></li>
			{{/if}}

			{{if $smarty.get.action=='add_new' && $page_name=='stats_referers_list.php'}}
				<li><span>{{$lang.stats.submenu_option_add_referer}}</span></li>
			{{else}}
				<li><a href="stats_referers_list.php?action=add_new">{{$lang.stats.submenu_option_add_referer}}</a></li>
			{{/if}}
		</ul>
	{{/if}}
</div>