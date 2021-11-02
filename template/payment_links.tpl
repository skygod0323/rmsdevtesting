{{assign var="page_title" value=$lang.html.payment_links_title}}
{{assign var="page_description" value=$lang.html.payment_links_description}}
{{assign var="page_keywords" value=$lang.html.payment_links_keywords}}

{{include file="include_header_general.tpl"}}

<div class="text-block">
	<div class="container">
		{{if $smarty.get.action=='payment_done'}}
			<h1 class="title">{{$lang.billing_links.title_payment_successful}}</h1>
			<p>
				{{$lang.billing_links.message_payment_successful|replace:"%1%":$lang.project_name}}
			</p>
		{{elseif $smarty.get.action=='payment_failed'}}
			<h1 class="title">{{$lang.billing_links.title_payment_failed}}</h1>
			<p>
				{{$lang.billing_links.message_payment_failed|replace:"%1%":$lang.project_name}}
			</p>
		{{/if}}
	</div>
</div>

{{assign var="footer_hide_advertisement" value="true"}}
{{include file="include_footer_general.tpl"}}