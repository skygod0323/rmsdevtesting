{{if $smarty.get.action=='unblock'}}
	{{insert name="getBlock" block_id="logon" block_name="Logon Confirmations" assign="logon_result"}}
{{elseif $smarty.get.action=='confirm' || $smarty.get.action=='confirm_restore_pass'}}
	{{insert name="getBlock" block_id="signup" block_name="Signup Confirmations" assign="signup_result"}}
{{elseif $smarty.get.action=='confirm_email'}}
	{{insert name="getBlock" block_id="member_profile_edit" block_name="Profile Confirmations" assign="member_profile_edit_result"}}
{{/if}}

{{assign var="page_title" value=$lang.html.email_links_title}}
{{assign var="page_description" value=$lang.html.email_links_description}}
{{assign var="page_keywords" value=$lang.html.email_links_keywords}}

{{include file="include_header_general.tpl"}}

<div class="text-block">
	<div class="container">
		{{if $smarty.get.action=='unblock'}}
			{{$logon_result|smarty:nodefaults}}
		{{elseif $smarty.get.action=='confirm' || $smarty.get.action=='confirm_restore_pass'}}
			{{$signup_result|smarty:nodefaults}}
		{{elseif $smarty.get.action=='confirm_email'}}
			{{$member_profile_edit_result|smarty:nodefaults}}
		{{/if}}
	</div>
</div>

{{assign var="footer_hide_advertisement" value="true"}}
{{include file="include_footer_general.tpl"}}