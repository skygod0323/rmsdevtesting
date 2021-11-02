<div id="{{$block_uid}}">
	{{assign var="list_albums_title" value=$lang.memberzone.profile_my_fav_albums[$fav_type]}}
	{{assign var="list_albums_header_level" value="1"}}
	{{include file="include_list_albums_block_common.tpl"}}
	{{include file="include_pagination_block_common.tpl"}}
</div>