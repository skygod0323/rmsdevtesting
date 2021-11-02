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

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<table class="de de_readonly">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2">
				<div>
					<a href="{{$page_name}}">{{$lang.website_ui.submenu_option_theme_history}}</a>
					/
					{{$lang.website_ui.page_history_edit|replace:"%1%":$smarty.post.$table_key_name}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_history_field_path}}:</td>
			<td class="de_control">{{$smarty.post.path}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_history_field_version}}:</td>
			<td class="de_control">{{$smarty.post.version}} ({{$smarty.post.username}})</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_history_field_added_date}}:</td>
			<td class="de_control">{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_history_field_old_content}}:</td>
			<td class="de_control">
				<textarea class="html_code_editor dyn_full_size" rows="10" cols="40">{{$smarty.post.prev_version.file_content}}</textarea>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_history_field_new_content}}:</td>
			<td class="de_control">
				<textarea class="html_code_editor dyn_full_size" rows="10" cols="40">{{$smarty.post.file_content}}</textarea>
			</td>
		</tr>
	</table>
</form>

{{else}}

<div class="dg_wrapper">
<form action="{{$page_name}}" method="get" class="form_dgf">
	<div class="dgf">
		<table>
			<tr>
				<td class="dgf_label">{{$lang.common.dg_filter_show_on_page}}:</td>
				<td class="dgf_control"><input type="text" name="num_on_page" size="3" value="{{$smarty.session.save.$page_name.num_on_page}}"/></td>
				<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
				<td class="dgf_control dgf_search">
					<input type="text" name="se_text" size="20" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}"/>
					{{if count($search_fields)>0}}
						<div class="dgf_search_layer hidden">
							<span>{{$lang.common.dg_filter_search_in}}:</span>
							<ul>
								{{assign var="search_everywhere" value="true"}}
								{{foreach from=$search_fields|smarty:nodefaults item="field"}}
									<li>
										{{assign var="option_id" value="se_text_`$field.id`"}}
										<input type="hidden" name="{{$option_id}}" value="0"/>
										<div class="dg_lv_pair"><input type="checkbox" name="{{$option_id}}" value="1" {{if $smarty.session.save.$page_name[$option_id]==1}}checked="checked"{{/if}}/><label>{{$field.title}}</label></div>
										{{if $smarty.session.save.$page_name[$option_id]!=1}}
											{{assign var="search_everywhere" value="false"}}
										{{/if}}
									</li>
								{{/foreach}}
								<li class="dgf_everywhere">
									<div class="dg_lv_pair"><input type="checkbox" name="se_text_all" value="1" {{if $search_everywhere=='true'}}checked="checked"{{/if}} class="dgf_everywhere"/><label>{{$lang.common.dg_filter_search_in_everywhere}}</label></div>
								</li>
							</ul>
						</div>
					{{/if}}
				</td>
				<td class="dgf_control">
					<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
					<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
				</td>
				<td class="dgf_advanced_link">
					<a href="javascript:stub()" class="dgf_filters {{if $table_filtered==1}}dgf_selected{{/if}}">{{$lang.common.dg_filter_filters}}</a>
					<a href="javascript:stub()" class="dgf_columns">{{$lang.common.dg_filter_columns}}</a>
				</td>
			</tr>
		</table>
	</div>
	<div class="dgf dgf_advanced_filters {{if $table_filtered==0}}hidden{{/if}}">
		<table class="dgf_filter">
			<tr>
				<td class="dgf_label {{if $smarty.session.save.$page_name.se_username!=''}}dgf_selected{{/if}}">{{$lang.website_ui.page_history_field_author}}:</td>
				<td class="dgf_control">
					<select name="se_username">
						<option value="">{{$lang.common.dg_filter_option_all}}</option>
						{{foreach from=$list_usernames item="item"}}
							<option value="{{$item}}" {{if $smarty.session.save.$page_name.se_username==$item}}selected="selected"{{/if}}>{{$item}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
		</table>
		<table class="dgf_filter">
			<tr>
				<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_from>0}}dgf_selected{{/if}}">{{$lang.website_ui.page_history_filter_added_date_from}}:</td>
				{{if $smarty.session.save.$page_name.se_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
				<td class="dgf_control">{{html_select_date prefix='se_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
			</tr>
		</table>
		<table class="dgf_filter">
			<tr>
				<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_to>0}}dgf_selected{{/if}}">{{$lang.website_ui.page_history_filter_added_date_to}}:</td>
				{{if $smarty.session.save.$page_name.se_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
				<td class="dgf_control">{{html_select_date prefix='se_date_to_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
			</tr>
		</table>
	</div>
	<div class="dgf dgf_advanced_columns hidden">
		{{assign var="table_columns_display_mode" value="selector"}}
		{{include file="table_columns_inc.tpl"}}
	</div>
</form>
<form action="{{$page_name}}" method="post" class="form_dg">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div class="dg">
		<table>
			<colgroup>
				<col width="1%"/>
				{{assign var="table_columns_display_mode" value="sizes"}}
				{{include file="table_columns_inc.tpl"}}
				<col width="1%"/>
			</colgroup>
			<tr class="dg_header">
				<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
				{{assign var="table_columns_display_mode" value="header"}}
				{{include file="table_columns_inc.tpl"}}
				<td>{{$lang.common.dg_actions}}</td>
			</tr>
			{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
					</td>
				</tr>
			{{/foreach}}
		</table>
	</div>
	<div class="dgb">
		<table>
			<tr>
				<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
			</tr>
		</table>
	</div>
</form>
</div>

{{include file="navigation.tpl"}}

{{/if}}