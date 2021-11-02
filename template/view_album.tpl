{{insert name="getBlock" block_id="album_view" block_name="Album View" assign="album_view_result"}}

{{assign var="page_title" value=$lang.html.album_details_title|replace_tokens:$storage.album_view_album_view}}
{{assign var="page_description" value=$lang.html.album_details_description|replace_tokens:$storage.album_view_album_view}}
{{assign var="page_keywords" value=$lang.html.album_details_keywords|replace_tokens:$storage.album_view_album_view}}

{{assign var="page_og_title" value=$storage.album_view_album_view.title}}
{{assign var="page_og_image" value=$storage.album_view_album_view.preview_formats[$lang.albums.thumb_size]}}
{{assign var="page_og_description" value=$storage.album_view_album_view.description}}

{{if $storage.album_view_album_view.canonical_url!=''}}
	{{assign var="page_canonical" value=$storage.album_view_album_view.canonical_url}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{$album_view_result|smarty:nodefaults}}
{{insert name="getBlock" block_id="album_comments" block_name="Album Comments"}}
{{insert name="getBlock" block_id="list_albums" block_name="Related Albums"}}

{{include file="include_footer_general.tpl"}}