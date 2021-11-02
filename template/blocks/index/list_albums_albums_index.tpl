<div id="{{$block_uid}}">
	{{assign var="list_albums_title" value=$lang.albums.list_title_by_sorting|replace:"%1%":$lang.albums.list_sorting[$sort_by]}}

	{{assign var="list_albums_show_sorting" value="true"}}
	{{include file="include_list_albums_block_common.tpl"}}

	{{assign var="pagination_direct_link" value="`$lang.urls.albums`?by=`$sort_by`"}}
	{{include file="include_pagination_block_common.tpl"}}
</div>