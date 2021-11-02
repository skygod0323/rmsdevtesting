{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	{{if in_array('posts|view',$smarty.session.permissions)}}
		<h1 data-children="posts_main" class="lm_collapse">{{$lang.posts.submenu_group_posts}}</h1>
		<ul id="posts_main">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='posts.php'}}
				<li><span>{{$lang.posts.submenu_option_posts_list}}</span></li>
			{{else}}
				<li><a href="posts.php">{{$lang.posts.submenu_option_posts_list}}</a></li>
			{{/if}}

			{{if $locked_post_type_support!=1}}
				{{if in_array('posts|add',$smarty.session.permissions)}}
					{{if $smarty.get.action=='add_new' && $page_name=='posts.php'}}
						<li><span>{{$lang.posts.submenu_option_add_post}}</span></li>
					{{else}}
						<li><a href="posts.php?action=add_new">{{$lang.posts.submenu_option_add_post}}</a></li>
					{{/if}}
				{{/if}}
			{{/if}}
		</ul>
		{{if $locked_post_type_support==1}}
			{{foreach item="item" from=$list_types|smarty:nodefaults}}
				<h1 data-children="posts_{{$item.external_id}}" class="lm_collapse">{{$item.title}}</h1>
				<ul id="posts_{{$item.external_id}}">
					{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=="posts_for_`$item.external_id`.php"}}
						<li><span>{{$item.title}}</span></li>
					{{else}}
						<li><a href="posts_for_{{$item.external_id}}.php">{{$item.title}}</a></li>
					{{/if}}

					{{if in_array('posts|add',$smarty.session.permissions)}}
						{{if $smarty.get.action=='add_new' && $page_name=="posts_for_`$item.external_id`.php"}}
							<li><span>{{$lang.posts.submenu_option_add_post}}</span></li>
						{{else}}
							<li><a href="posts_for_{{$item.external_id}}.php?action=add_new">{{$lang.posts.submenu_option_add_post}}</a></li>
						{{/if}}
					{{/if}}
				</ul>
			{{/foreach}}
		{{/if}}
	{{/if}}
	{{if in_array('posts_types|view',$smarty.session.permissions)}}
		<h1 data-children="post_types" class="lm_collapse">{{$lang.posts.submenu_group_post_types}}</h1>
		<ul id="post_types">
			{{if $smarty.get.action!='add_new' && $smarty.get.action!='change' && $page_name=='posts_types.php'}}
				<li><span>{{$lang.posts.submenu_option_post_types_list}}</span></li>
			{{else}}
				<li><a href="posts_types.php">{{$lang.posts.submenu_option_post_types_list}}</a></li>
			{{/if}}

			{{if in_array('posts_types|add',$smarty.session.permissions)}}
				{{if $smarty.get.action=='add_new' && $page_name=='posts_types.php'}}
					<li><span>{{$lang.posts.submenu_option_add_post_type}}</span></li>
				{{else}}
					<li><a href="posts_types.php?action=add_new">{{$lang.posts.submenu_option_add_post_type}}</a></li>
				{{/if}}
			{{/if}}
		</ul>
	{{/if}}
</div>