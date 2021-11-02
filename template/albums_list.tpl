{{insert name="getBlock" block_id="list_albums" block_name="Common Albums List" assign="common_albums_list_result"}}

{{if $storage.list_albums_common_albums_list.list_type=='tags'}}
	{{assign var="page_title" value=$lang.html.albums_by_tag_title|replace_tokens:$storage.list_albums_common_albums_list.tag_info|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_common_albums_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_by_tag_description|replace_tokens:$storage.list_albums_common_albums_list.tag_info}}
	{{assign var="page_keywords" value=$lang.html.albums_by_tag_keywords|replace_tokens:$storage.list_albums_common_albums_list.tag_info}}
	{{assign var="page_rss" value=$lang.urls.rss_albums_by_tag|replace:"%DIR%":$storage.list_albums_common_albums_list.tag_info.tag_dir|replace:"%ID%":$storage.list_albums_common_albums_list.tag_info.tag_id}}
	{{assign var="page_canonical" value=$lang.urls.albums_by_tag|replace:"%DIR%":$storage.list_albums_common_albums_list.tag_info.tag_dir|replace:"%ID%":$storage.list_albums_common_albums_list.tag_info.tag_id}}

{{elseif $storage.list_albums_common_albums_list.list_type=='categories'}}
	{{assign var="page_title" value=$lang.html.albums_by_category_title|replace_tokens:$storage.list_albums_common_albums_list.category_info|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_common_albums_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_by_category_description|replace_tokens:$storage.list_albums_common_albums_list.category_info}}
	{{assign var="page_keywords" value=$lang.html.albums_by_category_keywords|replace_tokens:$storage.list_albums_common_albums_list.category_info}}
	{{assign var="page_rss" value=$lang.urls.rss_albums_by_category|replace:"%DIR%":$storage.list_albums_common_albums_list.category_info.dir|replace:"%ID%":$storage.list_albums_common_albums_list.category_info.category_id}}
	{{assign var="page_canonical" value=$lang.urls.albums_by_category|replace:"%DIR%":$storage.list_albums_common_albums_list.category_info.dir|replace:"%ID%":$storage.list_albums_common_albums_list.category_info.category_id}}

{{elseif $storage.list_albums_common_albums_list.list_type=='models'}}
	{{assign var="page_title" value=$lang.html.albums_by_model_title|replace_tokens:$storage.list_albums_common_albums_list.model_info|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_common_albums_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_by_model_description|replace_tokens:$storage.list_albums_common_albums_list.model_info}}
	{{assign var="page_keywords" value=$lang.html.albums_by_model_keywords|replace_tokens:$storage.list_albums_common_albums_list.model_info}}
	{{assign var="page_rss" value=$lang.urls.rss_albums_by_model|replace:"%DIR%":$storage.list_albums_common_albums_list.model_info.dir|replace:"%ID%":$storage.list_albums_common_albums_list.model_info.model_id}}
	{{assign var="page_canonical" value=$lang.urls.content_by_model|replace:"%DIR%":$storage.list_albums_common_albums_list.model_info.dir|replace:"%ID%":$storage.list_albums_common_albums_list.model_info.model_id}}

{{elseif $storage.list_albums_common_albums_list.list_type=='search'}}
	{{assign var="page_title" value=$lang.html.search_albums_title|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_common_albums_list.sort_by]|replace_tokens:$storage.list_albums_common_albums_list}}
	{{assign var="page_description" value=$lang.html.search_albums_description|replace_tokens:$storage.list_albums_common_albums_list}}
	{{assign var="page_keywords" value=$lang.html.search_albums_keywords|replace_tokens:$storage.list_albums_common_albums_list}}

	{{assign var="search_type" value="albums"}}
	{{assign var="search_query" value=$storage.list_albums_common_albums_list.search_keyword}}

	{{assign var="query_url" value=$search_query|replace:" ":"-"|replace:"&":"%26"|replace:"?":"%3F"|replace:"/":"%2F"|rawurlencode}}
	{{assign var="page_canonical" value=$lang.urls.search_query_albums|replace:"%QUERY%":$query_url}}

{{else}}
	{{assign var="page_title" value=$lang.html.albums_title|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_common_albums_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_description}}
	{{assign var="page_keywords" value=$lang.html.albums_keywords}}
	{{assign var="page_next" value=$storage.list_albums_common_albums_list.page_next}}
	{{assign var="page_prev" value=$storage.list_albums_common_albums_list.page_prev}}
	{{assign var="page_rss" value=$lang.urls.rss_albums}}
	{{assign var="page_canonical" value=$lang.urls.albums}}

{{/if}}

{{if $storage.list_albums_common_albums_list.page_now>1}}
	{{if $page_title!=''}}
		{{assign var="page_title" value=$lang.html.default_paginated_title|replace:"%1%":$page_title|replace:"%2%":$storage.list_albums_common_albums_list.page_now}}
	{{/if}}
	{{if $page_description!=''}}
		{{assign var="page_description" value=$lang.html.default_paginated_description|replace:"%1%":$page_description|replace:"%2%":$storage.list_albums_common_albums_list.page_now}}
	{{/if}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{$common_albums_list_result|smarty:nodefaults}}

{{include file="include_footer_general.tpl"}}