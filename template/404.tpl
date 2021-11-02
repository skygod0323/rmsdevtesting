{{assign var="page_title" value=$lang.html.error_404_title}}
{{assign var="page_description" value=$lang.html.error_404_description}}
{{assign var="page_keywords" value=$lang.html.error_404_keywords}}

{{include file="include_header_general.tpl"}}

<div class="page-error">
	<h1 class="title title__huge">{{$lang.error_404.title}}</h1>
</div>
{{insert name="getBlock" block_id="list_videos" block_name="Recommended Videos"}}
{{if $lang.enable_albums=='true'}}
	{{insert name="getBlock" block_id="list_albums" block_name="Recommended Albums"}}
{{/if}}

{{include file="include_footer_general.tpl"}}