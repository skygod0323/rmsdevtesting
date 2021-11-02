{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

<div id="general_error">
	{{if $smarty.get.error=='permission_denied'}}
		{{$lang.validation.access_denied_error}}
	{{elseif $smarty.get.error=='page_doesnt_exist'}}
		{{$lang.validation.page_doesnt_exist_error}}
	{{else}}
		{{$lang.validation.unexpected_error}}
	{{/if}}
</div>