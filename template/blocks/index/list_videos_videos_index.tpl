<div id="{{$block_uid}}">
	{{assign var="list_videos_title" value=$lang.videos.list_title_by_sorting|replace:"%1%":$lang.videos.list_sorting[$sort_by]}}

	{{assign var="list_videos_show_sorting" value="true"}}
	{{assign var="hide_rated_sorting" value="false"}}

	{{assign var="list_videos_header_level" value="1"}}
	{{include file="include_list_videos_block_main.tpl"}}

	{{assign var="pagination_direct_link" value="`$lang.urls.videos`?by=`$sort_by`"}}
	{{include file="include_pagination_block_common.tpl"}}
</div>