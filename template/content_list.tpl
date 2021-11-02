{{insert name="getBlock" block_id="custom_list_videos" block_name="Common Videos Content List" assign="common_videos_content_list_result"}}
{{if $lang.enable_albums=='true'}}
	{{insert name="getBlock" block_id="list_albums" block_name="Common Albums Content List" assign="common_albums_content_list_result"}}
{{/if}}

{{if $storage.custom_list_videos_common_videos_content_list.list_type=='tags'}}
	{{assign var="page_title" value=$lang.html.content_by_tag_title|replace_tokens:$storage.custom_list_videos_common_videos_content_list.tag_info}}
	{{assign var="page_description" value=$lang.html.content_by_tag_description|replace_tokens:$storage.custom_list_videos_common_videos_content_list.tag_info}}
	{{assign var="page_keywords" value=$lang.html.content_by_tag_keywords|replace_tokens:$storage.custom_list_videos_common_videos_content_list.tag_info}}
	{{assign var="page_canonical" value=$lang.urls.content_by_tag|replace:"%DIR%":$storage.custom_list_videos_common_videos_content_list.tag_info.tag_dir|replace:"%ID%":$storage.custom_list_videos_common_videos_content_list.tag_info.tag_id}}

{{elseif $storage.custom_list_videos_common_videos_content_list.list_type=='categories'}}
	{{assign var="page_title" value=$lang.html.content_by_category_title|replace_tokens:$storage.custom_list_videos_common_videos_content_list.category_info}}
	{{assign var="page_description" value=$lang.html.content_by_category_description|replace_tokens:$storage.custom_list_videos_common_videos_content_list.category_info}}
	{{assign var="page_keywords" value=$lang.html.content_by_category_keywords|replace_tokens:$storage.custom_list_videos_common_videos_content_list.category_info}}
	{{assign var="page_canonical" value=$lang.urls.content_by_category|replace:"%DIR%":$storage.custom_list_videos_common_videos_content_list.category_info.dir|replace:"%ID%":$storage.custom_list_videos_common_videos_content_list.category_info.category_id}}

{{elseif $storage.custom_list_videos_common_videos_content_list.list_type=='models'}}
	{{assign var="page_title" value=$lang.html.content_by_model_title|replace_tokens:$storage.custom_list_videos_common_videos_content_list.model_info}}
	{{assign var="page_description" value=$lang.html.content_by_model_description|replace_tokens:$storage.custom_list_videos_common_videos_content_list.model_info}}
	{{assign var="page_keywords" value=$lang.html.content_by_model_keywords|replace_tokens:$storage.custom_list_videos_common_videos_content_list.model_info}}
	{{assign var="page_canonical" value=$lang.urls.content_by_model|replace:"%DIR%":$storage.custom_list_videos_common_videos_content_list.model_info.dir|replace:"%ID%":$storage.custom_list_videos_common_videos_content_list.model_info.model_id}}

{{else}}
	{{assign var="page_title" value=$lang.html.content_title}}
	{{assign var="page_description" value=$lang.html.content_description}}
	{{assign var="page_keywords" value=$lang.html.content_keywords}}
	{{assign var="page_next" value=$storage.custom_list_videos_common_videos_content_list.page_next}}
	{{assign var="page_prev" value=$storage.custom_list_videos_common_videos_content_list.page_prev}}
	{{assign var="page_canonical" value=$lang.urls.content}}

{{/if}}

{{if $storage.custom_list_videos_common_videos_content_list.page_now>1}}
	{{if $page_title!=''}}
		{{assign var="page_title" value=$lang.html.default_paginated_title|replace:"%1%":$page_title|replace:"%2%":$storage.custom_list_videos_common_videos_content_list.page_now}}
	{{/if}}
	{{if $page_description!=''}}
		{{assign var="page_description" value=$lang.html.default_paginated_description|replace:"%1%":$page_description|replace:"%2%":$storage.custom_list_videos_common_videos_content_list.page_now}}
	{{/if}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{if $storage.custom_list_videos_common_videos_content_list.list_type=='models'}}
	{{insert name="getBlock" block_id="model_view" block_name="Model View"}}
	{{insert name="getBlock" block_id="model_comments" block_name="Model Comments"}}
{{/if}}

{{$common_videos_content_list_result|smarty:nodefaults}}
{{if $smarty.session.status_id!=3}}
	{{insert name="getGlobal" global_id="global_stats_banner"}}
{{/if}}

{{if $lang.enable_albums=='true'}}
	{{$common_albums_content_list_result|smarty:nodefaults}}
	{{include file="include_join_banner_2.tpl"}}
{{/if}}

{{include file="include_footer_general.tpl"}}