{{if $smarty.get.action=='unblock'}}
	<div class="header">Unblock Account</div>

	{{if $activated==1}}
		<p class="message success">
			Thank you! Your account has been unblocked.
		</p>
	{{else}}
		<p class="message error">
			Oops, it looks like the link you are using is invalid. Please contact support.
		</p>
	{{/if}}

{{else}}
	<div class="header">Login Form</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="login"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="logon_username" class="required">Username</label>
			<input id="logon_username" type="text" name="username"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="logon_pass" class="required">Password</label>
			<input id="logon_pass" type="password" name="pass"/>
			<span class="field-error"></span>
		</div>

		{{if $use_captcha==1}}
			<div class="row">
				<label>Please confirm that you are a Human.</label>

				{{if $recaptcha_site_key!=''}}
					<div data-name="code">
						<div data-recaptcha-key="{{$recaptcha_site_key}}"></div>
						<div class="field-error"></div>
					</div>
				{{else}}
					<div class="captcha-control">
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=logon&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="logon_code" class="required">Security code</label>
						<input id="logon_code" type="text" name="code" autocomplete="off"/>
						<span class="field-error"></span>
					</div>
				{{/if}}
			</div>
		{{/if}}

		{{if $enable_remember_me==1}}
			<div class="row">
				<input id="logon_remember_me" type="checkbox" name="remember_me" value="1"/>
				<label for="logon_remember_me">remember me</label>
			</div>
		{{/if}}

		<div class="buttons">
			<input type="submit" value="Log in"/>
		</div>
	</form>

{{/if}}