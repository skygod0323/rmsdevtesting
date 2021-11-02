{{if $smarty.get.playlist>0}}
	{{insert name="getBlock" block_id="list_videos" block_name="Playlist Videos" assign="playlist_result"}}
{{/if}}
{{insert name="getBlock" block_id="custom_video_view" block_name="Video View" assign="video_view_result" var_playlist=$playlist_result|smarty:nodefaults}}

{{assign var="page_title" value=$lang.html.video_details_title|replace_tokens:$storage.custom_video_view_video_view}}
{{assign var="page_description" value=$lang.html.video_details_description|replace_tokens:$storage.custom_video_view_video_view}}
{{assign var="page_keywords" value=$lang.html.video_details_keywords|replace_tokens:$storage.custom_video_view_video_view}}

{{assign var="page_og_title" value=$storage.custom_video_view_video_view.title}}
{{assign var="page_og_image" value=$storage.custom_video_view_video_view.preview_url}}
{{assign var="page_og_description" value=$storage.custom_video_view_video_view.description}}

{{if $storage.custom_video_view_video_view.canonical_url!=''}}
	{{assign var="page_canonical" value=$storage.custom_video_view_video_view.canonical_url}}
{{/if}}

{{include file="include_header_general.tpl"}}

{{$video_view_result|smarty:nodefaults}}
{{insert name="getBlock" block_id="video_comments" block_name="Video Comments"}}
{{insert name="getBlock" block_id="custom_list_videos" block_name="Related Videos"}}

{{include file="include_footer_general.tpl"}}