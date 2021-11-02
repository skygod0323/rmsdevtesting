<div id="{{$block_uid}}">
	{{if $list_type=='tags'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_tag|replace:"%1%":$tag|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
	{{elseif $list_type=='categories'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_category|replace:"%1%":$category|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
	{{elseif $list_type=='models'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_model|replace:"%1%":$model|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
	{{elseif $list_type=='search'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_search|replace:"%1%":$search_keyword|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
	{{else}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_sorting|replace:"%1%":$lang.albums.list_sorting[$sort_by]}}
	{{/if}}

	{{assign var="list_albums_show_advertisement" value="true"}}
	{{assign var="list_albums_show_sorting" value="true"}}
	{{assign var="list_albums_header_level" value="1"}}
	{{include file="include_list_albums_block_common.tpl"}}

	{{include file="include_pagination_block_common.tpl"}}
</div>