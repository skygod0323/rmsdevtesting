{{if $smarty.get.action=='confirm'}}
	<h1 class="title">{{$lang.email_links.title_confirm_signup}}</h1>
	<p>
		{{if $activated==1}}
			{{$lang.email_links.message_confirm_signup_successful|replace:"%1%":$config.project_name}}
		{{else}}
			{{$lang.email_links.message_confirm_error}}
		{{/if}}
	</p>
{{elseif $smarty.get.action=='confirm_restore_pass'}}
	<h1 class="title">{{$lang.email_links.title_confirm_reset_password}}</h1>
	<p>
		{{if $activated==1}}
			{{$lang.email_links.message_confirm_reset_password_successful}}
		{{else}}
			{{$lang.email_links.message_confirm_error}}
		{{/if}}
	</p>
{{/if}}