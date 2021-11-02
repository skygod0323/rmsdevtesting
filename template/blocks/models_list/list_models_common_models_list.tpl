<div id="{{$block_uid}}">
	{{if $list_type=='section'}}
		{{assign var="list_models_title" value=$lang.models.list_title_by_section_sorting|replace:"%1%":$lang.models.list_sorting[$sort_by]|replace:"%2%":$section}}
	{{elseif $list_type=='search'}}
		{{assign var="list_models_title" value=$lang.models.list_title_by_search|replace:"%1%":$search_keyword|replace:"%2%":$lang.models.list_sorting[$sort_by]}}
	{{else}}
		{{assign var="list_models_title" value=$lang.models.list_title_by_sorting|replace:"%1%":$lang.models.list_sorting[$sort_by]}}
	{{/if}}

	{{assign var="list_models_show_sorting" value="true"}}
	{{assign var="list_models_show_advertisement" value="true"}}
	{{include file="include_list_models_block_common.tpl"}}

	{{include file="include_pagination_block_common.tpl"}}
</div>