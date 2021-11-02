{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if is_array($list_messages)}}
	<div class="message">
	{{foreach item=item from=$list_messages|smarty:nodefaults}}
		<p>{{$item}}</p>
	{{/foreach}}
	</div>
{{/if}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col/>
					<col/>
					{{foreach item=item from=$list_languages|smarty:nodefaults}}
						<col/>
					{{/foreach}}
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					<td>{{$lang.common.object_type}}</td>
					<td>{{$lang.common.total}}</td>
					{{foreach item=item from=$list_languages|smarty:nodefaults}}
						<td>{{$item.title}}</td>
					{{/foreach}}
				</tr>
				{{foreach name=data key=key item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
					<td>
						<a href="translations.php?no_filter=true&amp;se_object_type={{$item.object_type}}" class="no_popup">
							{{if $key=='videos'}}
								{{$lang.common.object_type_videos}}
							{{elseif $key=='albums'}}
								{{$lang.common.object_type_albums}}
							{{elseif $key=='content_sources'}}
								{{$lang.common.object_type_content_sources}}
							{{elseif $key=='models'}}
								{{$lang.common.object_type_models}}
							{{elseif $key=='dvds'}}
								{{$lang.common.object_type_dvds}}
							{{elseif $key=='categories'}}
								{{$lang.common.object_type_categories}}
							{{elseif $key=='categories_groups'}}
								{{$lang.common.object_type_category_groups}}
							{{elseif $key=='content_sources_groups'}}
								{{$lang.common.object_type_content_source_groups}}
							{{elseif $key=='tags'}}
								{{$lang.common.object_type_tags}}
							{{elseif $key=='dvds_groups'}}
								{{$lang.common.object_type_dvd_groups}}
							{{elseif $key=='models_groups'}}
								{{$lang.common.object_type_model_groups}}
							{{/if}}
						</a>
					</td>
					<td>{{$item.total}}</td>
					{{foreach item=item_lang from=$list_languages|smarty:nodefaults}}
						{{assign var="pc_key" value="`$item_lang.code`_pc"}}
						<td>
							{{if $item[$pc_key]<100}}
								<a href="translations.php?no_filter=true&amp;se_object_type={{$item.object_type}}&amp;se_translation_missing_for={{$item_lang.code}}" class="no_popup">{{$item[$pc_key]}}%</a>
							{{else}}
								{{$item[$pc_key]}}%
							{{/if}}
						</td>
					{{/foreach}}
				</tr>
				{{/foreach}}
		   </table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}