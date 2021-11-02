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

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_groups_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.admin_user_group_add}}{{else}}{{$lang.settings.admin_user_group_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.admin_user_group_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.admin_user_group_field_description}}:</td>
			<td class="de_control">
				<div class="de_str_len">
					<textarea name="description" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="4"><div>{{$lang.settings.admin_user_group_field_permissions}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.admin_user_group_field_access_to_content}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_access_to_own_content" value="1" {{if $smarty.post.is_access_to_own_content==1}}checked="checked"{{/if}}/><label>{{$lang.settings.admin_user_group_field_access_to_content_own}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.admin_user_group_field_access_to_content_own_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_access_to_disabled_content" value="1" {{if $smarty.post.is_access_to_disabled_content==1}}checked="checked"{{/if}}/><label>{{$lang.settings.admin_user_group_field_access_to_content_disabled}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.admin_user_group_field_access_to_content_disabled_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_access_to_content_flagged_with" name="is_access_to_content_flagged_with" value="1" {{if count($smarty.post.is_access_to_content_flagged_with)>0}}checked="checked"{{/if}}/><label>{{$lang.settings.admin_user_group_field_access_to_content_flagged}}</label></div>
							{{foreach from=$list_flags_admins|smarty:nodefaults item="item"}}
								<div class="de_lv_pair is_access_to_content_flagged_with_on"><input type="checkbox" name="is_access_to_content_flagged_with_flags[]" value="{{$item.flag_id}}" {{if in_array($item.flag_id, $smarty.post.is_access_to_content_flagged_with)}}checked="checked"{{/if}}/><label>{{$item.title}}</label></div>
							{{/foreach}}
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.admin_user_group_field_access_to_content_flagged_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		{{foreach key=key_gr item=item_gr from=$list_permissions|smarty:nodefaults}}
			{{assign var=is_no_access value=1}}
			{{assign var=is_read_only value=0}}
			{{assign var=is_full_access value=1}}

			{{foreach name=data key=key item=item from=$list_permissions.$key_gr|smarty:nodefaults}}
				{{if $item=="`$key_gr`|view" && is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}
					{{assign var=is_read_only value=1}}
					{{assign var=is_no_access value=0}}
				{{/if}}

				{{if is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}
					{{assign var=is_no_access value=0}}
					{{if $item!="`$key_gr`|view"}}
						{{assign var=is_read_only value=0}}
					{{/if}}
				{{else}}
					{{assign var=is_full_access value=0}}
				{{/if}}
			{{/foreach}}
			{{if $is_full_access==1}}
				{{assign var=is_read_only value=0}}
			{{/if}}
			<tr>
				<td class="de_label">
					<div class="access_level_{{$key_gr}}_no disabled">{{$lang.permissions.$key_gr}}:</div>
					{{if count($list_permissions.$key_gr)>1}}
						<div class="access_level_{{$key_gr}}_rw access_level_{{$key_gr}}_read">{{$lang.permissions.$key_gr}}:</div>
					{{/if}}
					<div class="access_level_{{$key_gr}}_full"><strong>{{$lang.permissions.$key_gr}}</strong>:</div>
				</td>
				<td class="de_control" colspan="3">
					<div class="de_vis_sw_select">
						<select id="access_level_{{$key_gr}}" name="access_level_{{$key_gr}}">
							<option value="no" {{if $is_no_access==1}}selected="selected"{{/if}}>{{$lang.permissions.access_none}}</option>
							{{if count($list_permissions.$key_gr)>1}}
								{{assign var="read_check" value="`$key_gr`|view"}}
								{{if in_array($read_check,$list_permissions.$key_gr)}}
									<option value="read" {{if $is_read_only==1}}selected="selected"{{/if}}>{{$lang.permissions.access_readonly}}</option>
								{{/if}}
								<option value="rw" {{if $is_no_access!=1 && $is_read_only!=1 && $is_full_access!=1}}selected="selected"{{/if}}>{{$lang.permissions.access_read_write}}</option>
							{{/if}}
							<option value="full" {{if $is_full_access==1}}selected="selected"{{/if}}>{{$lang.permissions.access_full}}</option>
						</select>
						<br/>
						<div class="access_level_{{$key_gr}}_rw{{if $is_no_access==1 || $is_read_only==1 || $is_full_access==1}} hidden{{/if}}">
							{{if count($list_permissions.$key_gr)>1}}
								<table class="control_group">
									<colgroup>
										<col width="25%"/>
										<col width="25%"/>
										<col width="25%"/>
										<col width="25%"/>
									</colgroup>
									<tr class="group_data">
									{{assign var=iteration value=1}}
									{{foreach name=data key=key item=item from=$list_permissions.$key_gr|smarty:nodefaults}}
										{{if $item!="`$key_gr`|view" && $lang.permissions.$item!=''}}
											<td><div class="de_lv_pair wrap"><input type="checkbox" name="permissions_ids[]" value="{{$key}}" {{if is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}checked="checked"{{/if}}/><span {{if is_array($smarty.post.permissions_ids) && in_array($key,$smarty.post.permissions_ids)}}class="selected"{{/if}}>{{$lang.permissions.$item}}</span></div></td>
											{{if $iteration%4==0 && !$smarty.foreach.data.last}}</tr><tr class="group_data">{{/if}}
											{{assign var=iteration value=$iteration+1}}
										{{/if}}
									{{/foreach}}
									{{if $iteration%4==2}}<td></td><td></td><td></td>{{/if}}
									{{if $iteration%4==3}}<td></td><td></td>{{/if}}
									{{if $iteration%4==0}}<td></td>{{/if}}
									</tr>
								</table>
							{{/if}}
						</div>
					</div>
				</td>
			</tr>
		{{/foreach}}
		<tr>
			<td class="de_action_group" colspan="2">
				{{if $smarty.get.action=='add_new'}}
					{{if $smarty.session.save.options.default_save_button==1}}
						<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
					{{else}}
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
					{{/if}}
				{{else}}
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
				{{/if}}
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
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
					<td class="dgf_advanced_link">
						<a href="javascript:stub()" class="dgf_columns">{{$lang.common.dg_filter_columns}}</a>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
							<span class="js_params">
								<span class="js_param">id={{$item.$table_key_name}}</span>
								<span class="js_param">name={{$item.title}}</span>
							</span>
						</a>
					</td>
				</tr>
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<table>
				<tr>
					<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
					<td class="dgb_control">
						<select name="batch_action">
							<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
							<option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
						</select>
					</td>
					<td class="dgb_control">
						<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
					</td>
					<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
				</tr>
			</table>
			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}