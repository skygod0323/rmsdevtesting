{{if $smarty.get.action=='unblock'}}
	<h1 class="title">{{$lang.email_links.title_confirm_unblock}}</h1>
	<p>
		{{if $activated==1}}
			{{$lang.email_links.message_confirm_unblock_successful}}
		{{else}}
			{{$lang.email_links.message_confirm_error}}
		{{/if}}
	</p>
{{/if}}