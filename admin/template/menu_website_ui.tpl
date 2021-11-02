{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
{{if in_array('website_ui|view',$smarty.session.permissions)}}
	<h1 data-children="website_theme" class="lm_collapse">{{$lang.website_ui.submenu_group_theme}}</h1>
	<ul id="website_theme">
		{{if $supports_theme==1}}
			{{if $page_name=='project_theme.php'}}
				<li><span>{{$lang.website_ui.submenu_option_theme_settings}}</span></li>
			{{else}}
				<li><a href="project_theme.php">{{$lang.website_ui.submenu_option_theme_settings}}</a></li>
			{{/if}}
		{{/if}}

		{{if $page_name=='project_pages_history.php'}}
			<li><span>{{$lang.website_ui.submenu_option_theme_history}}</span></li>
		{{else}}
			<li><a href="project_pages_history.php">{{$lang.website_ui.submenu_option_theme_history}}</a></li>
		{{/if}}

		{{if $page_name=='templates_search.php'}}
			<li><span>{{$lang.website_ui.submenu_option_template_search}}</span></li>
		{{else}}
			<li><a href="templates_search.php">{{$lang.website_ui.submenu_option_template_search}}</a></li>
		{{/if}}
	</ul>
	<h1 data-children="website_pages" class="lm_collapse">{{$lang.website_ui.submenu_group_pages}}</h1>
	<ul id="website_pages">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='restore_pages' && $smarty.get.action!='restore_blocks' && $smarty.get.action!='change' && $smarty.get.action!='change_block' && $page_name=='project_pages.php'}}
			<li><span>{{$lang.website_ui.submenu_option_pages_list}}</span></li>
		{{else}}
			<li><a href="project_pages.php">{{$lang.website_ui.submenu_option_pages_list}}</a></li>
		{{/if}}

		{{if in_array('website_ui|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new'  && $page_name=='project_pages.php'}}
				<li><span>{{$lang.website_ui.submenu_option_add_page}}</span></li>
			{{else}}
				<li><a href="project_pages.php?action=add_new">{{$lang.website_ui.submenu_option_add_page}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('website_ui|add',$smarty.session.permissions) || in_array('website_ui|delete',$smarty.session.permissions)}}
			{{if count($deleted_pages)>0}}
				{{assign var=number value=$deleted_pages|@count}}
				{{if $smarty.get.action=='restore_pages' && $page_name=='project_pages.php'}}
					<li><span>{{$lang.website_ui.submenu_option_restore_pages|replace:"%1%":$number}}</span></li>
				{{else}}
					<li><a href="project_pages.php?action=restore_pages">{{$lang.website_ui.submenu_option_restore_pages|replace:"%1%":$number}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}

		{{if in_array('website_ui|edit_all',$smarty.session.permissions) || in_array('website_ui|delete',$smarty.session.permissions)}}
			{{if $deleted_blocks_count>0}}
				{{assign var=number value=$deleted_blocks_count}}
				{{if $smarty.get.action=='restore_blocks' && $page_name=='project_pages.php'}}
					<li><span>{{$lang.website_ui.submenu_option_restore_blocks|replace:"%1%":$number}}</span></li>
				{{else}}
					<li><a href="project_pages.php?action=restore_blocks">{{$lang.website_ui.submenu_option_restore_blocks|replace:"%1%":$number}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
	<h1 data-children="website_infrastructure" class="lm_collapse">{{$lang.website_ui.submenu_group_page_infrastructure}}</h1>
	<ul id="website_infrastructure">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='project_pages_components.php'}}
			<li><span>{{$lang.website_ui.submenu_option_page_components}}</span></li>
		{{else}}
			<li><a href="project_pages_components.php">{{$lang.website_ui.submenu_option_page_components}}</a></li>
		{{/if}}

		{{if in_array('website_ui|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='project_pages_components.php'}}
				<li><span>{{$lang.website_ui.submenu_option_add_page_component}}</span></li>
			{{else}}
				<li><a href="project_pages_components.php?action=add_new">{{$lang.website_ui.submenu_option_add_page_component}}</a></li>
			{{/if}}
		{{/if}}

		{{if $page_name=='project_pages_global.php' && $smarty.get.action!='restore_blocks'}}
			<li><span>{{$lang.website_ui.submenu_option_global_blocks}}</span></li>
		{{else}}
			<li><a href="project_pages_global.php">{{$lang.website_ui.submenu_option_global_blocks}}</a></li>
		{{/if}}

		{{if in_array('website_ui|edit_all',$smarty.session.permissions) || in_array('website_ui|delete',$smarty.session.permissions)}}
			{{if $deleted_global_blocks_count>0}}
				{{assign var=number value=$deleted_global_blocks_count}}
				{{if $smarty.get.action=='restore_blocks' && $page_name=='project_pages_global.php'}}
					<li><span>{{$lang.website_ui.submenu_option_restore_global_blocks|replace:"%1%":$number}}</span></li>
				{{else}}
					<li><a href="project_pages_global.php?action=restore_blocks">{{$lang.website_ui.submenu_option_restore_global_blocks|replace:"%1%":$number}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}

		{{if $smarty.get.action!='show_long_desc' && $page_name=='project_blocks.php'}}
			<li><span>{{$lang.website_ui.submenu_option_blocks_list}}</span></li>
		{{else}}
			<li><a href="project_blocks.php">{{$lang.website_ui.submenu_option_blocks_list}}</a></li>
		{{/if}}
	</ul>
{{/if}}
{{if in_array('advertising|view',$smarty.session.permissions)}}
	<h1 data-children="website_advertising" class="lm_collapse">{{$lang.website_ui.submenu_group_advertising}}</h1>
	<ul id="website_advertising">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $smarty.get.action!='add_new_spot' && $smarty.get.action!='change_spot' && $page_name=='project_spots.php'}}
			<li><span>{{$lang.website_ui.submenu_option_advertisements_list}}</span></li>
		{{else}}
			<li><a href="project_spots.php">{{$lang.website_ui.submenu_option_advertisements_list}}</a></li>
		{{/if}}

		{{if in_array('advertising|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='project_spots.php'}}
				<li><span>{{$lang.website_ui.submenu_option_add_advertisement}}</span></li>
			{{else}}
				<li><a href="project_spots.php?action=add_new">{{$lang.website_ui.submenu_option_add_advertisement}}</a></li>
			{{/if}}
		{{/if}}

		{{if in_array('advertising|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new_spot' && $page_name=='project_spots.php'}}
				<li><span>{{$lang.website_ui.submenu_option_add_spot}}</span></li>
			{{else}}
				<li><a href="project_spots.php?action=add_new_spot">{{$lang.website_ui.submenu_option_add_spot}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('website_ui|view',$smarty.session.permissions) && $supports_langs==1}}
	<h1 data-children="website_texts" class="lm_collapse">{{$lang.website_ui.submenu_group_page_texts}}</h1>
	<ul id="website_texts">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='project_pages_lang_files.php'}}
			<li><span>{{$lang.website_ui.submenu_option_lang_files}}</span></li>
		{{else}}
			<li><a href="project_pages_lang_files.php">{{$lang.website_ui.submenu_option_lang_files}}</a></li>
		{{/if}}

		{{if in_array('website_ui|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='project_pages_lang_files.php'}}
				<li><span>{{$lang.website_ui.submenu_option_add_lang_file}}</span></li>
			{{else}}
				<li><a href="project_pages_lang_files.php?action=add_new">{{$lang.website_ui.submenu_option_add_lang_file}}</a></li>
			{{/if}}
		{{/if}}

		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='project_pages_lang_texts.php'}}
			<li><span>{{$lang.website_ui.submenu_option_text_items}}</span></li>
		{{else}}
			<li><a href="project_pages_lang_texts.php">{{$lang.website_ui.submenu_option_text_items}}</a></li>
		{{/if}}

		{{if in_array('website_ui|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='project_pages_lang_texts.php'}}
				<li><span>{{$lang.website_ui.submenu_option_add_text_item}}</span></li>
			{{else}}
				<li><a href="project_pages_lang_texts.php?action=add_new">{{$lang.website_ui.submenu_option_add_text_item}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('system|administration',$smarty.session.permissions)}}
	<h1 data-children="website_cache" class="lm_collapse">{{$lang.website_ui.submenu_group_cache}}</h1>
	<ul id="website_cache">
		<li><a id="link_file_cache" href="project_pages.php?action=reset_file_cache">{{$lang.website_ui.submenu_option_reset_file_cache}}</a></li>
		{{if $config.memcache_server && class_exists('Memcached')}}
			<li><a id="link_mem_cache" href="project_pages.php?action=reset_mem_cache">{{$lang.website_ui.submenu_option_reset_mem_cache}}</a></li>
		{{/if}}
		<li><a id="link_perf_stats" href="project_pages.php?action=reset_perf_stats">{{$lang.website_ui.submenu_option_reset_performance_stats}}</a></li>
	</ul>
	<ul class="links_configuration">
		<li class="js_params">
			<span class="js_param">id=link_file_cache</span>
			<span class="js_param">confirm={{$lang.website_ui.submenu_option_reset_file_cache_confirm}}</span>
		</li>
		{{if $config.memcache_server && class_exists('Memcached')}}
			<li class="js_params">
				<span class="js_param">id=link_mem_cache</span>
				<span class="js_param">confirm={{$lang.website_ui.submenu_option_reset_mem_cache_confirm}}</span>
			</li>
		{{/if}}
		<li class="js_params">
			<span class="js_param">id=link_perf_stats</span>
			<span class="js_param">confirm={{$lang.website_ui.submenu_option_reset_performance_stats_confirm}}</span>
		</li>
	</ul>
{{/if}}
</div>