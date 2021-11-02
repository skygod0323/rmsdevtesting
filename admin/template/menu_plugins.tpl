{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="left_menu">
	<h1>{{$lang.main_menu.plugins}}</h1>
	<ul>
		<li>
			{{if $smarty.request.plugin_id==''}}
				<span>{{$lang.plugins.submenu_plugins_home}}</span>
			{{else}}
				<a href="plugins.php">{{$lang.plugins.submenu_plugins_home}}</a>
			{{/if}}
		</li>
		{{foreach item=item from=$plugins|smarty:nodefaults}}
			<li>
				{{if $item.id==$smarty.request.plugin_id}}
					<span>{{$item.title}}</span>
				{{else}}
					<a href="plugins.php?plugin_id={{$item.id}}">{{$item.title}}</a>
				{{/if}}
			</li>
		{{/foreach}}
	</ul>
</div>