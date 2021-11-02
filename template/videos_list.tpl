{{insert name="getBlock" block_id="custom_list_videos" block_name="Common Videos List" assign="common_videos_list_result"}}

{{if $storage.custom_list_videos_common_videos_list.list_type=='tags'}}
	{{assign var="page_title" value=$lang.html.videos_by_tag_title|replace_tokens:$storage.custom_list_videos_common_videos_list.tag_info|replace:"%sort_by%":$lang.videos.list_sorting[$storage.custom_list_videos_common_videos_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_by_tag_description|replace_tokens:$storage.custom_list_videos_common_videos_list.tag_info}}
	{{assign var="page_keywords" value=$lang.html.videos_by_tag_keywords|replace_tokens:$storage.custom_list_videos_common_videos_list.tag_info}}
	{{assign var="page_rss" value=$lang.urls.rss_videos_by_tag|replace:"%DIR%":$storage.custom_list_videos_common_videos_list.tag_info.tag_dir|replace:"%ID%":$storage.custom_list_videos_common_videos_list.tag_info.tag_id}}
	{{assign var="page_canonical" value=$lang.urls.videos_by_tag|replace:"%DIR%":$storage.custom_list_videos_common_videos_list.tag_info.tag_dir|replace:"%ID%":$storage.custom_list_videos_common_videos_list.tag_info.tag_id}}

{{elseif $storage.custom_list_videos_common_videos_list.list_type=='categories'}}
	{{assign var="page_title" value=$lang.html.videos_by_category_title|replace_tokens:$storage.custom_list_videos_common_videos_list.category_info|replace:"%sort_by%":$lang.videos.list_sorting[$storage.custom_list_videos_common_videos_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_by_category_description|replace_tokens:$storage.custom_list_videos_common_videos_list.category_info}}
	{{assign var="page_keywords" value=$lang.html.videos_by_category_keywords|replace_tokens:$storage.custom_list_videos_common_videos_list.category_info}}
	{{assign var="page_rss" value=$laang.urls.rss_videos_by_category|replace:"%DIR%":$storage.custom_list_videos_common_videos_list.category_info.dir|replace:"%ID%":$storage.custom_list_videos_common_videos_list.category_info.category_id}}
	{{assign var="page_canonical" value=$lang.urls.videos_by_category|replace:"%DIR%":$storage.custom_list_videos_common_videos_list.category_info.dir|replace:"%ID%":$storage.custom_list_videos_common_videos_list.category_info.category_id}}

{{elseif $storage.custom_list_videos_common_videos_list.list_type=='models'}}
	{{assign var="page_title" value=$lang.html.videos_by_model_title|replace_tokens:$storage.custom_list_videos_common_videos_list.model_info|replace:"%sort_by%":$lang.videos.list_sorting[$storage.custom_list_videos_common_videos_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_by_model_description|replace_tokens:$storage.custom_list_videos_common_videos_list.model_info}}
	{{assign var="page_keywords" value=$lang.html.videos_by_model_keywords|replace_tokens:$storage.custom_list_videos_common_videos_list.model_info}}
	{{assign var="page_rss" value=$lang.urls.rss_videos_by_model|replace:"%DIR%":$storage.custom_list_videos_common_videos_list.model_info.dir|replace:"%ID%":$storage.custom_list_videos_common_videos_list.model_info.model_id}}
	{{assign var="page_canonical" value=$lang.urls.content_by_model|replace:"%DIR%":$storage.custom_list_videos_common_videos_list.model_info.dir|replace:"%ID%":$storage.custom_list_videos_common_videos_list.model_info.model_id}}

{{elseif $storage.custom_list_videos_common_videos_list.list_type=='search'}}
	{{assign var="page_title" value=$lang.html.search_videos_title|replace:"%sort_by%":$lang.videos.list_sorting[$storage.custom_list_videos_common_videos_list.sort_by]|replace_tokens:$storage.custom_list_videos_common_videos_list}}
	{{assign var="page_description" value=$lang.html.search_videos_description|replace_tokens:$storage.custom_list_videos_common_videos_list}}
	{{assign var="page_keywords" value=$lang.html.search_videos_keywords|replace_tokens:$storage.custom_list_videos_common_videos_list}}

	{{assign var="search_type" value="videos"}}
	{{assign var="search_query" value=$storage.custom_list_videos_common_videos_list.search_keyword}}

	{{assign var="query_url" value=$search_query|replace:" ":"-"|replace:"&":"%26"|replace:"?":"%3F"|replace:"/":"%2F"|rawurlencode}}
	{{assign var="page_canonical" value=$lang.urls.search_query_videos|replace:"%QUERY%":$query_url}}

{{else}}
	{{assign var="page_title" value=$lang.html.videos_title|replace:"%sort_by%":$lang.videos.list_sorting[$storage.custom_list_videos_common_videos_list.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_description}}
	{{assign var="page_keywords" value=$lang.html.videos_keywords}}
	{{assign var="page_next" value=$storage.custom_list_videos_common_videos_list.page_next}}
	{{assign var="page_prev" value=$storage.custom_list_videos_common_videos_list.page_prev}}
	{{assign var="page_rss" value=$lang.urls.rss_videos}}
	{{assign var="page_canonical" value=$lang.urls.videos}}

{{/if}}

{{if $storage.custom_list_videos_common_videos_list.page_now>1}}
	{{if $page_title!=''}}
		{{assign var="page_title" value=$lang.html.default_paginated_title|replace:"%1%":$page_title|replace:"%2%":$storage.custom_list_videos_common_videos_list.page_now}}
	{{/if}}
	{{if $page_description!=''}}
		{{assign var="page_description" value=$lang.html.default_paginated_description|replace:"%1%":$page_description|replace:"%2%":$storage.custom_list_videos_common_videos_list.page_now}}
	{{/if}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{$common_videos_list_result|smarty:nodefaults}}

{{include file="include_footer_general.tpl"}}