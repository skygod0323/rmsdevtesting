<div id="{{$block_uid}}">
	{{assign var="list_albums_title" value=$lang.albums.list_title_by_sorting|replace:"%1%":$lang.albums.list_sorting[$sort_by]}}
	{{assign var="list_albums_show_advertisement" value="true"}}
	{{include file="include_list_albums_block_common.tpl"}}
</div>