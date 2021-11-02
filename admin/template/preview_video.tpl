{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{$lang.system.language_code}}">
<head>
	<title>{{$lang.common.software|replace:"%1%":$config.project_title|replace:"%2%":$config.project_version}}</title>
	<style type="text/css">
		body {
			background: #717b83;
			margin: 0;
		}

		iframe {
			display: block;
			position: fixed;
			left: 0;
			top: 0;
			bottom: 0;
			right: 0;
			width: 100%;
			height: 100%;
		}
	</style>
	<script type="text/javascript" src="{{$config.project_url}}/player/kt_player.js?v={{$config.project_version}}"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

{{if $preview_data.load_type_id==3}}
	{{$preview_data.embed|smarty:nodefaults}}
{{else}}
	<div style="width: 100%; height: 100%;"><div id="kt_player"></div></div>
	<script type="text/javascript">
		/* <![CDATA[ */
		var flashvars = {
			{{foreach key="name" item="value" name="flashvars" from=$preview_data.flashvars|smarty:nodefaults}}
				{{$name}}: '{{$value|replace:"'":"\'"|smarty:nodefaults}}'{{if !$smarty.foreach.flashvars.last}}, {{/if}}
			{{/foreach}}
		};
		var playerObj = kt_player('kt_player', '{{$config.project_url}}/player/kt_player.swf?v={{$config.project_version}}', '100%', '100%', flashvars);
		/* ]]> */
	</script>
{{/if}}
</body>
</html>