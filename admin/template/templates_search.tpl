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
	<form action="{{$page_name}}" method="get" class="form_dgf">
		<div class="dgf">
			<table>
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_filename!=''}}dgf_selected{{/if}}">{{$lang.website_ui.dg_search_filter_in_name}}:</td>
					<td class="dgf_control"><input type="text" name="se_filename" size="20" value="{{$smarty.session.save.$page_name.se_filename}}"/></td>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_contents!=''}}dgf_selected{{/if}}">{{$lang.website_ui.dg_search_filter_in_template}}:</td>
					<td class="dgf_control"><input type="text" name="se_contents" size="20" value="{{$smarty.session.save.$page_name.se_contents}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_filename=='' && $smarty.session.save.$page_name.se_contents=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
				</tr>
			</table>
		</div>
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col width="60%"/>
					<col/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					<td>{{$lang.website_ui.dg_search_col_type}}</td>
					<td>{{$lang.website_ui.dg_search_col_filename}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
						<td>
							{{if $item.type=='page'}}
								<a href="project_pages.php?action=change&amp;item_id={{$item.external_id}}">
									{{$lang.website_ui.dg_search_col_type_page|replace:"%1%":$item.page_name}}
								</a>
							{{elseif $item.type=='component'}}
								<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}">
									{{$lang.website_ui.dg_search_col_type_component}}
								</a>
							{{elseif $item.type=='block_template'}}
								<a href="project_pages.php?action=change_block&amp;item_id={{$item.page_external_id}}||{{$item.block_id}}||{{$item.block_internal_name}}&amp;item_name={{$item.block_name}}">
									{{$lang.website_ui.dg_search_col_type_block|replace:"%1%":$item.block_name|replace:"%2%":$item.page_name}} ({{$lang.website_ui.dg_search_col_type_modifier_template}})
								</a>
							{{elseif $item.type=='block_params'}}
								<a href="project_pages.php?action=change_block&amp;item_id={{$item.page_external_id}}||{{$item.block_id}}||{{$item.block_internal_name}}&amp;item_name={{$item.block_name}}">
									{{$lang.website_ui.dg_search_col_type_block|replace:"%1%":$item.block_name|replace:"%2%":$item.page_name}} ({{$lang.website_ui.dg_search_col_type_modifier_params}})
								</a>
							{{elseif $item.type=='global_block_template'}}
								<a href="project_pages.php?action=change_block&amp;item_id=$global||{{$item.block_id}}||{{$item.block_internal_name}}&amp;item_name={{$item.block_name}}">
									{{$lang.website_ui.dg_search_col_type_global_block|replace:"%1%":$item.block_name}} ({{$lang.website_ui.dg_search_col_type_modifier_template}})
								</a>
							{{elseif $item.type=='global_block_params'}}
								<a href="project_pages.php?action=change_block&amp;item_id=$global||{{$item.block_id}}||{{$item.block_internal_name}}&amp;item_name={{$item.block_name}}">
									{{$lang.website_ui.dg_search_col_type_global_block|replace:"%1%":$item.block_name}} ({{$lang.website_ui.dg_search_col_type_modifier_params}})
								</a>
							{{elseif $item.type=='lang_text'}}
								<a href="project_pages_lang_texts.php?action=change&amp;item_id={{$item.external_id}}">
									{{$lang.website_ui.dg_search_col_type_text_item|replace:"%1%":$item.external_id}}
									{{if $item.language_title}}
										({{$item.language_title}})
									{{elseif $item.language_code=='default'}}
										({{$lang.website_ui.dg_search_col_type_modifier_default_lang}})
									{{/if}}
								</a>
							{{elseif $item.type=='ad_spot'}}
								<a href="project_spots.php?action=change_spot&amp;item_id={{$item.external_id}}">
									{{$lang.website_ui.dg_search_col_type_advertisement_spot|replace:"%1%":$item.spot_name}}
								</a>
							{{elseif $item.type=='ad'}}
								<a href="project_spots.php?action=change&amp;item_id={{$item.advertisement_id}}">
									{{$lang.website_ui.dg_search_col_type_advertisement|replace:"%1%":$item.advertisement_name}}
								</a>
							{{/if}}
						</td>
						<td>
							{{$item.filename}}
						</td>
					</tr>
				{{/foreach}}
			</table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>