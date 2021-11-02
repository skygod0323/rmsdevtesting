<?xml version="1.0" encoding="utf-8"?>

{{php}}
setlocale(LC_ALL, 'en_US.UTF-8');
{{/php}}

{{insert name="getBlock" block_id="list_albums" block_name="List Albums" assign="list_albums_result"}}

{{if $storage.list_albums_list_albums.list_type=='tags'}}
	{{assign var="page_title" value=$lang.html.albums_by_tag_title|replace_tokens:$storage.list_albums_list_albums.tag_info|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_list_albums.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_by_tag_description|replace_tokens:$storage.list_albums_list_albums.tag_info}}
	{{assign var="page_canonical" value=$lang.urls.albums_by_tag|replace:"%DIR%":$storage.list_albums_list_albums.tag_info.tag_dir|replace:"%ID%":$storage.list_albums_list_albums.tag_info.tag_id}}

{{elseif $storage.list_albums_list_albums.list_type=='categories'}}
	{{assign var="page_title" value=$lang.html.albums_by_category_title|replace_tokens:$storage.list_albums_list_albums.category_info|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_list_albums.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_by_category_description|replace_tokens:$storage.list_albums_list_albums.category_info}}
	{{assign var="page_canonical" value=$lang.urls.albums_by_category|replace:"%DIR%":$storage.list_albums_list_albums.category_info.dir|replace:"%ID%":$storage.list_albums_list_albums.category_info.category_id}}

{{elseif $storage.list_albums_list_albums.list_type=='models'}}
	{{assign var="page_title" value=$lang.html.albums_by_model_title|replace_tokens:$storage.list_albums_list_albums.model_info|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_list_albums.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_by_model_description|replace_tokens:$storage.list_albums_list_albums.model_info}}
	{{assign var="page_canonical" value=$lang.urls.albums_by_model|replace:"%DIR%":$storage.list_albums_list_albums.model_info.dir|replace:"%ID%":$storage.list_albums_list_albums.model_info.model_id}}

{{else}}
	{{assign var="page_title" value=$lang.html.albums_title|replace:"%sort_by%":$lang.albums.list_sorting[$storage.list_albums_list_albums.sort_by]}}
	{{assign var="page_description" value=$lang.html.albums_description}}
	{{assign var="page_canonical" value=$lang.urls.albums}}

{{/if}}

<rss version="2.0">
<channel>
	<title><![CDATA[{{$page_title|mb_ucfirst}}]]></title>
	<link>{{$page_canonical|default:$config.project_url}}</link>
	<description><![CDATA[{{$page_description|mb_ucfirst}}]]></description>
	<lastBuildDate>{{$smarty.now|date_format:"%a %d %b %Y %H:%M:%S +0200"}}</lastBuildDate>
	{{$list_albums_result|smarty:nodefaults}}
</channel>
</rss>