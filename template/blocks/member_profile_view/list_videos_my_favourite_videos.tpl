<div id="{{$block_uid}}">
	{{if $playlist!=''}}
		{{assign var="list_videos_title" value=$lang.memberzone.profile_my_playlist_videos|replace:"%1%":$playlist}}
	{{else}}
		{{assign var="list_videos_title" value=$lang.memberzone.profile_my_fav_videos[$fav_type]}}
	{{/if}}
	{{assign var="list_videos_header_level" value="1"}}
	{{include file="include_list_videos_block_common.tpl"}}
	{{include file="include_pagination_block_common.tpl"}}
</div>