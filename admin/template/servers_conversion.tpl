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
	<div class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
		<div class="err_header">{{if is_array($smarty.post.errors)}}{{$lang.validation.common_header}}{{/if}}</div>
		<div class="err_content">
			{{if is_array($smarty.post.errors)}}
				<ul>
				{{foreach name=data_err item=item_err from=$smarty.post.errors|smarty:nodefaults}}
					<li>{{$item_err}}</li>
				{{/foreach}}
				</ul>
			{{/if}}
		</div>
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
			<col width="95"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_conversion_servers_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.conversion_server_add}}{{else}}{{$lang.settings.conversion_server_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/538-video-conversion-engine-and-video-conversion-speed">Video conversion engine and video conversion speed</a></span>
					<br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1026-how-to-add-remote-conversion-server-in-kvs">How to add remote conversion server in KVS</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.conversion_server_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_field_status}}:</td>
				<td class="de_control">
					{{if $smarty.post.status_id==2}}
						{{$lang.settings.conversion_server_field_status_init}}
						<input type="hidden" name="status_id" value="2"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.conversion_server_field_status_hint}}</span>
						{{/if}}
					{{else}}
						<select name="status_id">
							<option value="0" {{if $smarty.post.status_id==0}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_status_disabled}}</option>
							<option value="1" {{if $smarty.post.status_id==1}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_status_active}}</option>
						</select>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_field_api_version}}:</td>
				<td class="de_control">{{$smarty.post.api_version|default:$lang.common.undefined}}</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_max_tasks}} (*):</td>
			<td class="de_control">
				<input type="text" name="max_tasks" maxlength="10" class="fixed_200" value="{{$smarty.post.max_tasks}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_max_tasks_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.conversion_server_field_priority}}:</td>
			<td class="de_control">
				<select name="process_priority">
					<option value="0" {{if $smarty.post.process_priority==0}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_priority_realtime}}</option>
					<option value="4" {{if $smarty.post.process_priority==4}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_priority_high}}</option>
					<option value="9" {{if $smarty.post.process_priority==9}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_priority_medium}}</option>
					<option value="14" {{if $smarty.post.process_priority==14}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_priority_low}}</option>
					<option value="19" {{if $smarty.post.process_priority==19}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_priority_very_low}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_priority_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.conversion_server_option_optimization}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="option_storage_servers" value="1" {{if $smarty.post.option_storage_servers==1}}checked="checked"{{/if}}/><label>{{$lang.settings.conversion_server_option_optimization_storage}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.conversion_server_option_optimization_storage_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="connection_type_id_1 connection_type_id_2">
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="option_pull_source_files" value="1" {{if $smarty.post.option_pull_source_files==1}}checked="checked"{{/if}}/><label>{{$lang.settings.conversion_server_option_optimization_source}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.conversion_server_option_optimization_source_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		{{if $smarty.post.server_id>0}}
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_field_log}}:</td>
				<td class="de_control">
					<textarea class="dyn_full_size" cols="40" rows="3" readonly="readonly">{{$smarty.post.log}}</textarea>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.conversion_server_divider_connection}}</div></td>
		</tr>
		{{if $smarty.get.action=='add_new' && $smarty.session.userdata.is_expert_mode==0}}
			<tr class="connection_type_id_1 connection_type_id_2">
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.conversion_server_divider_connection_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.conversion_server_field_connection_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="connection_type_id" name="connection_type_id">
						<option value="0" {{if $smarty.post.connection_type_id==0}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_connection_type_local}}</option>
						<option value="1" {{if $smarty.post.connection_type_id==1}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_connection_type_mount}}</option>
						<option value="2" {{if $smarty.post.connection_type_id==2}}selected="selected"{{/if}}>{{$lang.settings.conversion_server_field_connection_type_ftp}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.conversion_server_field_connection_type_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="connection_type_id_0 connection_type_id_1">
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_path}} (*):</td>
			<td class="de_control">
				<input type="text" name="path" maxlength="150" class="dyn_full_size" value="{{$smarty.post.path}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_path_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_host}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_host" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_host}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_port}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_port" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_port|default:'21'}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_user}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_user" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_user}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_2">
			{{if $smarty.post.connection_type_id==2}}
				<td class="de_label">{{$lang.settings.conversion_server_field_ftp_password}}:</td>
			{{else}}
				<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_password}} (*):</td>
			{{/if}}
			<td class="de_control">
				{{if $smarty.post.ftp_pass!=''}}
					<div class="de_passw">
						<input type="text" name="h_ftp_pass" value="{{$lang.common.password_hidden}}" maxlength="150" class="dyn_full_size"/>
					</div>
				{{else}}
					<input type="password" name="ftp_pass" maxlength="150" class="dyn_full_size"/>
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.post.connection_type_id==2}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_ftp_password_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label">{{$lang.settings.conversion_server_field_ftp_folder}}:</td>
			<td class="de_control">
				<input type="text" name="ftp_folder" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_folder}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_ftp_folder_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_ftp_timeout}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_timeout" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_timeout|default:'20'}}"/>
			</td>
		</tr>
		{{if $smarty.post.server_id>0}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.conversion_server_divider_config}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.settings.conversion_server_divider_config_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.conversion_server_field_libraries}}:</td>
				<td class="de_table_control">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.settings.conversion_server_field_libraries_name}}</td>
							<td>{{$lang.settings.conversion_server_field_libraries_path}}</td>
							<td>{{$lang.settings.conversion_server_field_libraries_response}}</td>
						</tr>
						{{if is_array($smarty.post.libraries)}}
							{{foreach key=key item=item from=$smarty.post.libraries|smarty:nodefaults}}
								<tr class="eg_data fixed_height_30">
									<td>{{$key}}</td>
									<td>{{$item.path|default:$lang.common.undefined}}</td>
									<td>
										{{if $item.is_error==1}}
											<span class="highlighted_text">{{$lang.settings.conversion_server_field_libraries_response_error}}</span>
										{{else}}
											{{$item.message}}
										{{/if}}
									</td>
								</tr>
							{{/foreach}}
						{{else}}
							<tr class="eg_data fixed_height_30">
								<td colspan="3">{{$lang.settings.conversion_server_field_libraries_empty}}</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
			{{if $smarty.post.config!=''}}
				<tr>
					<td class="de_label">{{$lang.settings.conversion_server_field_configuration}}:</td>
					<td class="de_control">
						<textarea name="config" class="dyn_full_size" cols="40" rows="10">{{$smarty.post.config}}</textarea>
					</td>
				</tr>
			{{/if}}
		{{/if}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text==''}}disabled="disabled"{{/if}}/>
					</td>
				</tr>
			</table>
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
					<col/>
					<col/>
					<col/>
					<col/>
					<col/>
					<col/>
					<col/>
					<col/>
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/></td>
					<td>{{$lang.settings.dg_conversion_servers_col_title}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_status}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_api_version}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_tasks_count}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_load_average}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_free_space}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_heartbeat}}</td>
					<td>{{$lang.settings.dg_conversion_servers_col_debug_mode}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" {{if $item.status_id!=0 && $item.error_iteration>1}}class="highlighted_text"{{elseif $item.is_logging_enabled==1}}class="warning_text"{{/if}}>{{$item.title}}</a>
							{{if $item.status_id!=0 && $item.error_iteration>1}}
								{{if $item.error_id==1}}
									<span class="highlighted_text">({{$lang.settings.dg_conversion_servers_error_write}})</span>
								{{elseif $item.error_id==2}}
									<span class="highlighted_text">({{$lang.settings.dg_conversion_servers_error_heartbeat}})</span>
								{{elseif $item.error_id==3}}
									<span class="highlighted_text">({{$lang.settings.dg_conversion_servers_error_heartbeat2}})</span>
								{{elseif $item.error_id==4}}
									<span class="highlighted_text">({{$lang.settings.dg_conversion_servers_error_path_error}})</span>
								{{elseif $item.error_id==5}}
									<span class="highlighted_text">({{$lang.settings.dg_conversion_servers_error_api_version}})</span>
								{{elseif $item.error_id==6}}
									<span class="highlighted_text">({{$lang.settings.dg_conversion_servers_error_locked_too_long}})</span>
								{{/if}}
							{{elseif $item.is_logging_enabled==1}}
								<span class="warning_text">({{$lang.settings.dg_conversion_servers_warning_debug_enabled}})</span>
							{{/if}}
						</td>
						<td class="nowrap">{{if $item.status_id==0}}{{$lang.settings.dg_conversion_servers_col_status_disabled}}{{elseif $item.status_id==1}}{{$lang.settings.dg_conversion_servers_col_status_active}}{{elseif $item.status_id==2}}{{$lang.settings.dg_conversion_servers_col_status_init}}{{/if}}</td>
						<td class="nowrap">
							{{$item.api_version|default:$lang.common.undefined}}
						</td>
						<td class="nowrap">{{$item.tasks_amount}}</td>
						<td class="nowrap">{{$item.load|number_format:2}}</td>
						<td class="nowrap">{{$item.free_space_percent}}% ({{$item.free_space_string}})</td>
						<td class="nowrap">
							{{if $item.heartbeat_date=='0000-00-00 00:00:00'}}
								{{$lang.common.undefined}}
							{{else}}
								{{$item.heartbeat_date|date_format:$smarty.session.userdata.full_date_format}}
							{{/if}}
						</td>
						<td class="nowrap">
							{{if $item.is_logging_enabled==1}}{{$lang.common.yes}}{{else}}{{$lang.common.no}}{{/if}}
						</td>
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.title}}</span>
									{{if $item.has_old_api!=1}}
										<span class="js_param">update_api_hide=true</span>
									{{/if}}
									{{if $item.status_id==1}}
										<span class="js_param">activate_hide=true</span>
									{{elseif $item.status_id==0}}
										<span class="js_param">deactivate_hide=true</span>
									{{else}}
										<span class="js_param">activate_hide=true</span>
										<span class="js_param">deactivate_hide=true</span>
									{{/if}}
									{{if $item.is_logging_enabled==1}}
										<span class="js_param">enable_debug_hide=true</span>
									{{else}}
										<span class="js_param">disable_debug_hide=true</span>
										<span class="js_param">view_debug_log_hide=true</span>
									{{/if}}
									{{if $item.has_debug_log!=1}}
										<span class="js_param">view_debug_log_disable=true</span>
									{{/if}}
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
					<span class="js_param">confirm={{$lang.settings.dg_conversion_servers_action_delete_confirm|replace:"%1%":'${name}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
					<span class="js_param">hide=${activate_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
					<span class="js_param">hide=${deactivate_hide}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=update_api&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.dg_conversion_servers_actions_update_api}}</span>
					<span class="js_param">hide=${update_api_hide}</span>
					<span class="js_param">confirm={{$lang.settings.dg_conversion_servers_actions_update_api_confirm|replace:"%1%":'${name}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=enable_debug&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
					<span class="js_param">hide=${enable_debug_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=disable_debug&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_disable_debug}}</span>
					<span class="js_param">hide=${disable_debug_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=view_debug_log&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_view_debug_log}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">hide=${view_debug_log_hide}</span>
					<span class="js_param">disable=${view_debug_log_disable}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=view_conversion_log&amp;id=${id}</span>
					<span class="js_param">title={{$lang.settings.dg_conversion_servers_actions_view_remote_log}}</span>
					<span class="js_param">plain_link=true</span>
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
							<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
							<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
							<option value="enable_debug">{{$lang.common.dg_batch_actions_enable_debug}}</option>
							<option value="disable_debug">{{$lang.common.dg_batch_actions_disable_debug}}</option>
							<option value="update_api">{{$lang.settings.dg_conversion_servers_batch_update_api}}</option>
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
				<li class="js_params">
					<span class="js_param">value=update_api</span>
					<span class="js_param">confirm={{$lang.settings.dg_conversion_servers_batch_update_api_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}