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
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="4"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_imports_log}}</a> / {{$lang.settings.import_view|replace:"%1%":$smarty.post.import_id}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.import_field_status}}:</td>
			<td class="de_control">
				{{if $smarty.post.status_id=='0'}}
					{{$lang.settings.import_field_status_scheduled}}
				{{elseif $smarty.post.status_id=='1'}}
					{{$lang.settings.import_field_status_in_process}}
				{{elseif $smarty.post.status_id=='2'}}
					{{$lang.settings.import_field_status_completed}}
				{{elseif $smarty.post.status_id=='3'}}
					{{$lang.settings.import_field_status_cancelled}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.import_field_type}}:</td>
			<td class="de_control">
				{{if $smarty.post.type_id=='1'}}
					{{$lang.settings.import_field_type_videos}}
				{{elseif $smarty.post.type_id=='2'}}
					{{$lang.settings.import_field_type_albums}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.import_field_data}}:</td>
			<td class="de_control"><textarea name="data" class="dyn_full_size" rows="10" cols="40">{{$smarty.post.data}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.import_field_log}}:</td>
			<td class="de_control"><textarea name="data" class="dyn_full_size" rows="10" cols="40">{{$smarty.post.log}}</textarea></td>
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
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.settings.import_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.settings.import_field_status_scheduled}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.settings.import_field_status_in_process}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected="selected"{{/if}}>{{$lang.settings.import_field_status_completed}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected="selected"{{/if}}>{{$lang.settings.import_field_status_cancelled}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_type_id>0}}dgf_selected{{/if}}">{{$lang.settings.import_field_type}}:</td>
					<td class="dgf_control">
						<select name="se_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_type_id=='1'}}selected="selected"{{/if}}>{{$lang.settings.import_field_type_videos}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_type_id=='2'}}selected="selected"{{/if}}>{{$lang.settings.import_field_type_albums}}</option>
						</select>
					</td>
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
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									{{if $item.has_import_log!=1}}
										<span class="js_param">log_disable=true</span>
									{{/if}}
								</span>
							</a>
						</td>
					</tr>
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?action=import_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.import_action_view_log}}</span>
					<span class="js_param">disable=${log_disable}</span>
					<span class="js_param">plain_link=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=new_import&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.import_action_new_import}}</span>
				</li>
			</ul>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}