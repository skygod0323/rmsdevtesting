{{if $smarty.get.action=='confirm_email'}}
	<h1 class="title">{{$lang.email_links.title_confirm_change_email}}</h1>
	<p>
		{{if $activated==1}}
			{{$lang.email_links.message_confirm_email_change_successful}}
		{{else}}
			{{$lang.email_links.message_confirm_error}}
		{{/if}}
	</p>
{{/if}}