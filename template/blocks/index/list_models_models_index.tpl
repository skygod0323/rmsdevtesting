<div id="{{$block_uid}}">
	{{assign var="list_models_title" value=$lang.models.list_title_by_sorting|replace:"%1%":$lang.models.list_sorting[$sort_by]}}

	{{assign var="list_models_show_sorting" value="true"}}
	{{include file="include_list_models_block_common.tpl"}}

	{{assign var="pagination_direct_link" value="`$lang.urls.models`?by=`$sort_by`"}}
	{{include file="include_pagination_block_common.tpl"}}
</div>