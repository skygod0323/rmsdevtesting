<div id="{{$block_uid}}">
	{{if $list_type=='tags'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_tag|replace:"%1%":$tag|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
		{{assign var="pagination_direct_link" value=$lang.urls.albums_by_tag|replace:"%DIR%":$tag_info.tag_dir|replace:"%ID%":$tag_info.tag_id}}
	{{elseif $list_type=='categories'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_category|replace:"%1%":$category|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
		{{assign var="pagination_direct_link" value=$lang.urls.albums_by_category|replace:"%DIR%":$category_info.dir|replace:"%ID%":$category_info.category_id}}
	{{elseif $list_type=='models'}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_model|replace:"%1%":$model|replace:"%2%":$lang.albums.list_sorting[$sort_by]}}
		{{assign var="pagination_direct_link" value=$lang.urls.albums_by_model|replace:"%DIR%":$model_info.dir|replace:"%ID%":$model_info.model_id}}
	{{else}}
		{{assign var="list_albums_title" value=$lang.albums.list_title_by_sorting|replace:"%1%":$lang.albums.list_sorting[$sort_by]}}
		{{assign var="pagination_direct_link" value=$lang.urls.albums}}
	{{/if}}

	{{assign var="list_albums_show_sorting" value="true"}}
	{{include file="include_list_albums_block_common.tpl"}}

	{{assign var="pagination_direct_link" value="`$pagination_direct_link`?by=`$sort_by`"}}
	{{include file="include_pagination_block_common.tpl"}}
</div>