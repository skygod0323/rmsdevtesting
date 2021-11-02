{{insert name="getBlock" block_id="list_categories" block_name="Common Categories List" assign="list_categories_result"}}

{{assign var="page_title" value=$lang.html.categories_title|replace:"%sort_by%":$lang.categories.list_sorting[$storage.list_categories_common_categories_list.sort_by]}}
{{assign var="page_description" value=$lang.html.categories_description}}
{{assign var="page_keywords" value=$lang.html.categories_keywords}}
{{assign var="page_next" value=$storage.list_categories_common_categories_list.page_next}}
{{assign var="page_prev" value=$storage.list_categories_common_categories_list.page_prev}}
{{assign var="page_canonical" value=$lang.urls.categories}}

{{if $storage.list_categories_common_categories_list.page_now>1}}
	{{if $page_title!=''}}
		{{assign var="page_title" value=$lang.html.default_paginated_title|replace:"%1%":$page_title|replace:"%2%":$storage.list_categories_common_categories_list.page_now}}
	{{/if}}
	{{if $page_description!=''}}
		{{assign var="page_description" value=$lang.html.default_paginated_description|replace:"%1%":$page_description|replace:"%2%":$storage.list_categories_common_categories_list.page_now}}
	{{/if}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{$list_categories_result|smarty:nodefaults}}

{{include file="include_footer_general.tpl"}}