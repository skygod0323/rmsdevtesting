<div id="{{$block_uid}}">
	{{assign var="list_videos_title" value=$lang.videos.list_title_by_sorting|replace:"%1%":$lang.videos.list_sorting[$sort_by]}}
	{{assign var="list_videos_show_advertisement" value="true"}}
	{{include file="include_list_videos_block_common.tpl"}}
</div>