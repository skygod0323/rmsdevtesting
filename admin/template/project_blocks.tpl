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

{{if $smarty.get.action=='show_long_desc'}}

<form action="{{$page_name}}" method="get">
	<table class="de">
		<colgroup>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header"><div><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_blocks_list}}</a> / {{$lang.website_ui.block_view|replace:"%1%":$smarty.post.external_id}}</div></td>
		</tr>
		<tr>
			<td class="de_separator"><div>{{$lang.website_ui.block_divider_description}}</div></td>
		</tr>
		<tr>
			<td class="de_simple_text">{{$smarty.post.desc}}</td>
		</tr>
		<tr>
			<td class="de_separator"><div>{{$lang.website_ui.block_divider_template}}</div></td>
		</tr>
		<tr>
			<td class="de_control">
				<textarea class="html_code_editor dyn_full_size" rows="10" cols="40" readonly="readonly">{{$smarty.post.template}}</textarea>
			</td>
		</tr>
		<tr>
			<td class="de_separator"><div>{{$lang.website_ui.block_divider_params}}</div></td>
		</tr>
		{{if count($smarty.post.params)>0}}
			<tr>
				<td class="de_table_control">
					<table class="de_edit_grid">
						<colgroup>
							<col width="10%"/>
							<col width="12%"/>
							<col width="10%"/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.website_ui.block_params_col_name}}</td>
							<td>{{$lang.website_ui.block_params_col_type}}</td>
							<td>{{$lang.website_ui.block_params_col_required}}</td>
							<td>{{$lang.website_ui.block_params_col_description}}</td>
						</tr>
						{{assign var="last_group" value=""}}
						{{foreach item=item from=$smarty.post.params|smarty:nodefaults}}
							{{if ($last_group=='' || $last_group!=$item.group) && $item.group!=''}}
								{{assign var="last_group" value=$item.group}}
								<tr class="eg_group_header">
									<td colspan="4">{{$item.group_desc}}</td>
								</tr>
							{{/if}}
							<tr class="eg_data fixed_height_30">
								<td {{if $item.is_deprecated==1}}class="deprecated_text"{{/if}}>{{if $item.is_required==1}}<b>{{$item.name}}</b>{{else}}{{$item.name}}{{/if}}</td>
								<td>{{$item.type}}</td>
								<td><input type="checkbox" {{if $item.is_required==1}}checked="checked"{{/if}} disabled="disabled"/></td>
								<td>{{$item.desc}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_simple_text">{{$lang.website_ui.block_divider_params_nothing}}</td>
			</tr>
		{{/if}}
		{{if $smarty.post.examples!=''}}
			<tr>
				<td class="de_separator"><div>{{$lang.website_ui.block_divider_examples}}</div></td>
			</tr>
			<tr>
				<td class="de_simple_text">{{$smarty.post.examples}}</td>
			</tr>
		{{/if}}
	</table>
</form>

{{else}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf">
		<div class="dgf">
			<table>
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
					<td class="dgf_label">{{$lang.website_ui.dg_blocks_filter_group_by}}:</td>
					<td class="dgf_control">
						<select name="se_group_by" class="dgf_switcher">
							<option value="functionality" {{if $smarty.session.save.$page_name.se_group_by=='functionality'}}selected="selected"{{/if}}>{{$lang.website_ui.dg_blocks_filter_group_by_functionality}}</option>
							<option value="type" {{if $smarty.session.save.$page_name.se_group_by=='type'}}selected="selected"{{/if}}>{{$lang.website_ui.dg_blocks_filter_group_by_type}}</option>
						</select>
					</td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<div class="dg">
		<table>
			<colgroup>
				<col width="1%"/>
				<col/>
				<col/>
				<col/>
				<col/>
				<col/>
				<col/>
				<col/>
			</colgroup>
			<tr class="dg_header">
				<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
				<td>{{$lang.website_ui.dg_blocks_col_block_id}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_description}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_author}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_version}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_type}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_functionality}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_package}}</td>
				<td>{{$lang.website_ui.dg_blocks_col_state}}</td>
			</tr>
			{{foreach name="data_blocks" key="blocks_grouping" item="item_blocks" from=$data|smarty:nodefaults}}
				<tr class="dg_group_header">
					<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
					<td colspan="8">
						{{if $blocks_grouping=='type:list'}}
							{{$lang.website_ui.dg_blocks_col_type_list}}
						{{elseif $blocks_grouping=='type:context'}}
							{{$lang.website_ui.dg_blocks_col_type_context}}
						{{elseif $blocks_grouping=='type:form'}}
							{{$lang.website_ui.dg_blocks_col_type_form}}
						{{elseif $blocks_grouping=='type:misc'}}
							{{$lang.website_ui.dg_blocks_col_type_misc}}
						{{elseif $blocks_grouping=='type:custom'}}
							{{$lang.website_ui.dg_blocks_col_type_custom}}
						{{elseif $blocks_grouping=='functionality:videos'}}
							{{$lang.website_ui.dg_blocks_col_functionality_videos}}
						{{elseif $blocks_grouping=='functionality:albums'}}
							{{$lang.website_ui.dg_blocks_col_functionality_albums}}
						{{elseif $blocks_grouping=='functionality:posts'}}
							{{$lang.website_ui.dg_blocks_col_functionality_posts}}
						{{elseif $blocks_grouping=='functionality:categorization'}}
							{{$lang.website_ui.dg_blocks_col_functionality_categorization}}
						{{elseif $blocks_grouping=='functionality:memberzone'}}
							{{$lang.website_ui.dg_blocks_col_functionality_memberzone}}
						{{elseif $blocks_grouping=='functionality:community'}}
							{{$lang.website_ui.dg_blocks_col_functionality_community}}
						{{elseif $blocks_grouping=='functionality:misc'}}
							{{$lang.website_ui.dg_blocks_col_functionality_misc}}
						{{else}}
							{{$blocks_grouping|substr:14}}
						{{/if}}
					</td>
				</tr>
				{{foreach name="data" item="item" from=$item_blocks|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.package>$config.installation_type}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
						<td>
							{{if $item.is_invalid==1}}
								<span class="highlighted_text">{{$item.external_id}}</span>
							{{else}}
								{{if $item.package>$config.installation_type}}
									{{$item.external_id}}
								{{else}}
									<a href="{{$page_name}}?action=show_long_desc&amp;block_id={{$item.external_id}}">{{$item.external_id}}</a>
								{{/if}}
							{{/if}}
						</td>
						<td>{{$item.short_desc}}</td>
						<td class="nowrap">{{$item.author}}</td>
						<td class="nowrap">{{$item.version}}</td>
						<td class="nowrap">
							{{foreach name="data_types" item="item_type" from=$item.types}}
								{{if $item_type=='list'}}{{$lang.website_ui.dg_blocks_col_type_list}}{{elseif $item_type=='context'}}{{$lang.website_ui.dg_blocks_col_type_context}}{{elseif $item_type=='form'}}{{$lang.website_ui.dg_blocks_col_type_form}}{{elseif $item_type=='custom'}}{{$lang.website_ui.dg_blocks_col_type_custom}}{{else}}{{$lang.website_ui.dg_blocks_col_type_misc}}{{/if}}{{if !$smarty.foreach.data_types.last}}, {{/if}}
							{{/foreach}}
						</td>
						<td class="nowrap">
							{{foreach name="data_functionalities" item="item_functionality" from=$item.functionalities}}{{if $item_functionality=='videos'}}{{$lang.website_ui.dg_blocks_col_functionality_videos}}{{elseif $item_functionality=='albums'}}{{$lang.website_ui.dg_blocks_col_functionality_albums}}{{elseif $item_functionality=='posts'}}{{$lang.website_ui.dg_blocks_col_functionality_posts}}{{elseif $item_functionality=='categorization'}}{{$lang.website_ui.dg_blocks_col_functionality_categorization}}{{elseif $item_functionality=='memberzone'}}{{$lang.website_ui.dg_blocks_col_functionality_memberzone}}{{elseif $item_functionality=='community'}}{{$lang.website_ui.dg_blocks_col_functionality_community}}{{else}}{{$lang.website_ui.dg_blocks_col_functionality_misc}}{{/if}}{{if !$smarty.foreach.data_functionalities.last}}, {{/if}}
							{{/foreach}}
						</td>
						<td class="nowrap">
							{{if $item.package==4}}
								{{$lang.website_ui.dg_blocks_col_package_ultimate}}
							{{elseif $item.package==3}}
								{{$lang.website_ui.dg_blocks_col_package_premium}}
							{{elseif $item.package==2}}
								{{$lang.website_ui.dg_blocks_col_package_advanced}}
							{{else}}
								{{$lang.website_ui.dg_blocks_col_package_basic}}
							{{/if}}
						</td>
						<td class="nowrap">
							{{if $item.is_invalid==1}}
								<span class="highlighted_text">{{$lang.website_ui.dg_blocks_col_state_invalid}}</span>
							{{elseif $item.package>$config.installation_type}}
								{{$lang.website_ui.dg_blocks_col_state_disabled}}
							{{else}}
								{{$lang.website_ui.dg_blocks_col_state_valid}}
							{{/if}}
						</td>
					</tr>
				{{/foreach}}
			{{/foreach}}
		</table>
	</div>
	<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
</div>

{{/if}}