{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
{{if in_array('categories|view',$smarty.session.permissions) || in_array('category_groups|view',$smarty.session.permissions)}}
	<h1 data-children="categorization_categories" class="lm_collapse">{{$lang.categorization.submenu_group_categories}}</h1>
	<ul id="categorization_categories">
		{{if in_array('categories|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='categories.php'}}
				<li><span>{{$lang.categorization.submenu_option_categories_list}}</span></li>
			{{else}}
				<li><a href="categories.php">{{$lang.categorization.submenu_option_categories_list}}</a></li>
			{{/if}}

			{{if in_array('categories|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='categories.php'}}
					<li><span>{{$lang.categorization.submenu_option_add_category}}</span></li>
				{{else}}
					<li><a href="categories.php?action=add_new">{{$lang.categorization.submenu_option_add_category}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
		{{if in_array('category_groups|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='categories_groups.php'}}
				<li><span>{{$lang.categorization.submenu_option_category_groups_list}}</span></li>
			{{else}}
				<li><a href="categories_groups.php">{{$lang.categorization.submenu_option_category_groups_list}}</a></li>
			{{/if}}

			{{if in_array('category_groups|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='categories_groups.php'}}
					<li><span>{{$lang.categorization.submenu_option_add_category_group}}</span></li>
				{{else}}
					<li><a href="categories_groups.php?action=add_new">{{$lang.categorization.submenu_option_add_category_group}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('models|view',$smarty.session.permissions) || in_array('models_groups|view',$smarty.session.permissions)}}
	<h1 data-children="categorization_models" class="lm_collapse">{{$lang.categorization.submenu_group_models}}</h1>
	<ul id="categorization_models">
		{{if in_array('models|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='models.php'}}
				<li><span>{{$lang.categorization.submenu_option_models_list}}</span></li>
			{{else}}
				<li><a href="models.php">{{$lang.categorization.submenu_option_models_list}}</a></li>
			{{/if}}

			{{if in_array('models|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='models.php'}}
					<li><span>{{$lang.categorization.submenu_option_add_model}}</span></li>
				{{else}}
					<li><a href="models.php?action=add_new">{{$lang.categorization.submenu_option_add_model}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}

		{{if in_array('models_groups|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='models_groups.php'}}
				<li><span>{{$lang.categorization.submenu_option_model_groups_list}}</span></li>
			{{else}}
				<li><a href="models_groups.php">{{$lang.categorization.submenu_option_model_groups_list}}</a></li>
			{{/if}}

			{{if in_array('models_groups|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='models_groups.php'}}
					<li><span>{{$lang.categorization.submenu_option_add_model_group}}</span></li>
				{{else}}
					<li><a href="models_groups.php?action=add_new">{{$lang.categorization.submenu_option_add_model_group}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('content_sources|view',$smarty.session.permissions) || in_array('content_sources_groups|view',$smarty.session.permissions)}}
	<h1 data-children="categorization_content_sources" class="lm_collapse">{{$lang.categorization.submenu_group_content_sources}}</h1>
	<ul id="categorization_content_sources">
		{{if in_array('content_sources|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='content_sources.php'}}
				<li><span>{{$lang.categorization.submenu_option_content_sources_list}}</span></li>
			{{else}}
				<li><a href="content_sources.php">{{$lang.categorization.submenu_option_content_sources_list}}</a></li>
			{{/if}}

			{{if in_array('content_sources|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='content_sources.php'}}
					<li><span>{{$lang.categorization.submenu_option_add_content_source}}</span></li>
				{{else}}
					<li><a href="content_sources.php?action=add_new">{{$lang.categorization.submenu_option_add_content_source}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}

		{{if in_array('content_sources_groups|view',$smarty.session.permissions)}}
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='content_sources_groups.php'}}
				<li><span>{{$lang.categorization.submenu_option_content_source_groups_list}}</span></li>
			{{else}}
				<li><a href="content_sources_groups.php">{{$lang.categorization.submenu_option_content_source_groups_list}}</a></li>
			{{/if}}

			{{if in_array('content_sources_groups|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='content_sources_groups.php'}}
					<li><span>{{$lang.categorization.submenu_option_add_content_source_group}}</span></li>
				{{else}}
					<li><a href="content_sources_groups.php?action=add_new">{{$lang.categorization.submenu_option_add_content_source_group}}</a></li>
				{{/if}}
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('tags|view',$smarty.session.permissions)}}
	<h1 data-children="categorization_tags" class="lm_collapse">{{$lang.categorization.submenu_group_tags}}</h1>
	<ul id="categorization_tags">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='tags.php'}}
			<li><span>{{$lang.categorization.submenu_option_tags_list}}</span></li>
		{{else}}
			<li><a href="tags.php">{{$lang.categorization.submenu_option_tags_list}}</a></li>
		{{/if}}

		{{if in_array('tags|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='tags.php'}}
				<li><span>{{$lang.categorization.submenu_option_add_tags}}</span></li>
			{{else}}
				<li><a href="tags.php?action=add_new">{{$lang.categorization.submenu_option_add_tags}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
{{if in_array('flags|view',$smarty.session.permissions)}}
	<h1 data-children="categorization_flags" class="lm_collapse">{{$lang.categorization.submenu_group_flags}}</h1>
	<ul id="categorization_flags">
		{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='flags.php'}}
			<li><span>{{$lang.categorization.submenu_option_flags_list}}</span></li>
		{{else}}
			<li><a href="flags.php">{{$lang.categorization.submenu_option_flags_list}}</a></li>
		{{/if}}

		{{if in_array('flags|add',$smarty.session.permissions)}}
			{{if $smarty.get.action=='add_new' && $page_name=='flags.php'}}
				<li><span>{{$lang.categorization.submenu_option_add_flag}}</span></li>
			{{else}}
				<li><a href="flags.php?action=add_new">{{$lang.categorization.submenu_option_add_flag}}</a></li>
			{{/if}}
		{{/if}}
	</ul>
{{/if}}
</div>