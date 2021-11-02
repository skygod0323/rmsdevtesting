<?xml version="1.0" encoding="utf-8"?>

{{php}}
setlocale(LC_ALL, 'en_US.UTF-8');
{{/php}}

{{insert name="getBlock" block_id="list_videos" block_name="List Videos" assign="list_videos_result"}}

{{if $storage.list_videos_list_videos.list_type=='tags'}}
	{{assign var="page_title" value=$lang.html.videos_by_tag_title|replace_tokens:$storage.list_videos_list_videos.tag_info|replace:"%sort_by%":$lang.videos.list_sorting[$storage.list_videos_list_videos.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_by_tag_description|replace_tokens:$storage.list_videos_list_videos.tag_info}}
	{{assign var="page_canonical" value=$lang.urls.videos_by_tag|replace:"%DIR%":$storage.list_videos_list_videos.tag_info.tag_dir|replace:"%ID%":$storage.list_videos_list_videos.tag_info.tag_id}}

{{elseif $storage.list_videos_list_videos.list_type=='categories'}}
	{{assign var="page_title" value=$lang.html.videos_by_category_title|replace_tokens:$storage.list_videos_list_videos.category_info|replace:"%sort_by%":$lang.videos.list_sorting[$storage.list_videos_list_videos.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_by_category_description|replace_tokens:$storage.list_videos_list_videos.category_info}}
	{{assign var="page_canonical" value=$lang.urls.videos_by_category|replace:"%DIR%":$storage.list_videos_list_videos.category_info.dir|replace:"%ID%":$storage.list_videos_list_videos.category_info.category_id}}

{{elseif $storage.list_videos_list_videos.list_type=='models'}}
	{{assign var="page_title" value=$lang.html.videos_by_model_title|replace_tokens:$storage.list_videos_list_videos.model_info|replace:"%sort_by%":$lang.videos.list_sorting[$storage.list_videos_list_videos.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_by_model_description|replace_tokens:$storage.list_videos_list_videos.model_info}}
	{{assign var="page_canonical" value=$lang.urls.videos_by_model|replace:"%DIR%":$storage.list_videos_list_videos.model_info.dir|replace:"%ID%":$storage.list_videos_list_videos.model_info.model_id}}

{{else}}
	{{assign var="page_title" value=$lang.html.videos_title|replace:"%sort_by%":$lang.videos.list_sorting[$storage.list_videos_list_videos.sort_by]}}
	{{assign var="page_description" value=$lang.html.videos_description}}
	{{assign var="page_canonical" value=$lang.urls.videos}}

{{/if}}

<rss version="2.0">
<channel>
	<title><![CDATA[{{$page_title|mb_ucfirst}}]]></title>
	<link>{{$page_canonical|default:$config.project_url}}</link>
	<description><![CDATA[{{$page_description|mb_ucfirst}}]]></description>
	<lastBuildDate>{{$smarty.now|date_format:"%a %d %b %Y %H:%M:%S +0200"}}</lastBuildDate>
	{{$list_videos_result|smarty:nodefaults}}
</channel>
</rss>