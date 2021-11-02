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
			margin: 10px;
		}

		#preview_dialog {
			text-align: center;
		}

		.item {
			float: left;
			padding: 5px;
			height: {{$image_bounds[1]}}px;
		}

		.item img {
			border: 1px solid #000;
		}
	</style>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>

<div id="preview_dialog">
	{{foreach item=item from=$preview_data|smarty:nodefaults}}
		<div class="item">
			<img src="{{$item}}" alt=""/>
		</div>
	{{/foreach}}
	<div style="clear: both"></div>
</div>
</body>
</html>