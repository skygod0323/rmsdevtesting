{{insert name="getBlock" block_id="list_models" block_name="Common Models List" assign="list_models_result"}}

{{if $storage.list_models_common_models_list.list_type=='search'}}
	{{assign var="page_title" value=$lang.html.search_models_title|replace:"%sort_by%":$lang.models.list_sorting[$storage.list_models_common_models_list.sort_by]|replace_tokens:$storage.list_models_common_models_list}}
	{{assign var="page_description" value=$lang.html.search_models_description|replace_tokens:$storage.list_models_common_models_list}}
	{{assign var="page_keywords" value=$lang.html.search_models_keywords|replace_tokens:$storage.list_models_common_models_list}}

	{{assign var="search_type" value="models"}}
	{{assign var="search_query" value=$storage.list_models_common_models_list.search_keyword}}

	{{assign var="query_url" value=$search_query|replace:" ":"-"|replace:"&":"%26"|replace:"?":"%3F"|replace:"/":"%2F"|rawurlencode}}
	{{assign var="page_canonical" value=$lang.urls.search_query_models|replace:"%QUERY%":$query_url}}

{{else}}
	{{assign var="page_title" value=$lang.html.models_title|replace:"%sort_by%":$lang.models.list_sorting[$storage.list_models_common_models_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.models_description}}
	{{assign var="page_keywords" value=$lang.html.models_keywords}}
	{{assign var="page_next" value=$storage.list_models_common_models_list.page_next}}
	{{assign var="page_prev" value=$storage.list_models_common_models_list.page_prev}}
	{{assign var="page_canonical" value=$lang.urls.models}}

{{/if}}

{{if $storage.list_models_common_models_list.page_now>1}}
	{{if $page_title!=''}}
		{{assign var="page_title" value=$lang.html.default_paginated_title|replace:"%1%":$page_title|replace:"%2%":$storage.list_models_common_models_list.page_now}}
	{{/if}}
	{{if $page_description!=''}}
		{{assign var="page_description" value=$lang.html.default_paginated_description|replace:"%1%":$page_description|replace:"%2%":$storage.list_models_common_models_list.page_now}}
	{{/if}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{$list_models_result|smarty:nodefaults}}

{{include file="include_footer_general.tpl"}}