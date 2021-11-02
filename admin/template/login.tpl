<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title>{{$lang.login.page_tilte}}{{if $show_version==1}} / {{$config.project_version}}{{/if}}</title>
	{{assign var="hash" value="`$config.project_version``$config.ahv`"}}
	{{assign var="hash" value=$hash|md5|substr:0:16}}
	<link type="text/css" rel="stylesheet" href="styles/default.css?v={{$hash}}"/>
	<script type="text/javascript" src="js/admin.js?v={{$hash}}"></script>
	<script type="text/javascript" src="js/config.php?v={{$hash}}"></script>

	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
</head>
<body onload="prepareAdminPanel()">
	<div id="content">
		<table id="layout_root">
			<tr>
				<td id="layout_main_left"><img src="images/t.gif" alt="" width="1" height="500"/></td>
				<td id="layout_main_login">
					<div id="login_form">
						<form id="login" action="log_in.php" method="post">
							<div>
								<input type="hidden" name="action" value="login"/>
							</div>
							<div class="err_list {{if $session_error==''}}hidden{{/if}}">
								<div class="err_header">{{if $session_error!=''}}{{$lang.validation.common_header}}{{/if}}</div>
								<div class="err_content">
									{{if $session_error!=''}}
										<ul>
											<li>{{$session_error}}</li>
										</ul>
									{{/if}}
								</div>
							</div>
							<table class="de">
								<colgroup>
									<col width="10%"/>
									<col/>
								</colgroup>
								<tr>
									<td class="de_label">{{$lang.login.field_username}}:</td>
									<td class="de_control login_field"><input type="text" name="username"/></td>
								</tr>
								<tr>
									<td class="de_label">{{$lang.login.field_password}}:</td>
									<td class="de_control login_field"><input type="password" name="password"/></td>
								</tr>
								<tr>
									<td class="de_label">{{$lang.login.field_ip}}:</td>
									<td class="de_control login_field">{{$ip_address}}</td>
								</tr>
								<tr>
									<td class="de_action_group" colspan="2"><input type="submit" value="{{$lang.login.btn_log_on}}"/></td>
								</tr>
							</table>
						</form>
					</div>
				</td>
				<td id="layout_main_right"><img src="images/t.gif" alt="" width="1" height="500"/></td>
			</tr>
			<tr>
				<td id="layout_bottom_left"><img src="images/t.gif" alt="" width="10" height="1"/></td>
				<td id="layout_bottom_main"><img src="images/t.gif" alt="" width="1220" height="1"/></td>
				<td id="layout_bottom_right"><img src="images/t.gif" alt="" width="10" height="1"/></td>
			</tr>
		</table>
	</div>
</body>
</html>