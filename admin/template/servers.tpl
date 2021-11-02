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

{{if $smarty.get.action=='add_new_group' || $smarty.get.action=='change_group'}}

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
		{{if $smarty.get.action=='add_new_group'}}
			<input type="hidden" name="action" value="add_new_group_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_group_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_storage_servers_list}}</a> / {{if $smarty.get.action=='add_new_group'}}{{$lang.settings.server_group_add}}{{else}}{{$lang.settings.server_group_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/377-storage-system-in-kvs-tube-script">Storage system in KVS</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.server_group_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.server_group_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.server_group_field_content_type}}:</td>
			<td class="de_control">
				<select name="content_type_id" {{if $smarty.get.action!='add_new_group'}}disabled="disabled"{{/if}}>
					<option value="1" {{if $smarty.post.content_type_id==1}}selected="selected"{{/if}}>{{$lang.settings.server_group_field_content_type_videos}}</option>
					<option value="2" {{if $smarty.post.content_type_id==2}}selected="selected"{{/if}}>{{$lang.settings.server_group_field_content_type_albums}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.server_group_field_status}}:</td>
			<td class="de_control">
				<select name="status_id">
					<option value="0" {{if $smarty.post.status_id==0}}selected="selected"{{/if}}>{{$lang.settings.server_group_field_status_disabled}}</option>
					<option value="1" {{if $smarty.post.status_id==1}}selected="selected"{{/if}}>{{$lang.settings.server_group_field_status_active}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_group_field_status_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.server_group_divider_load_balancing}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header">
						<td>{{$lang.settings.server_group_servers_name}}</td>
						<td>{{$lang.settings.server_group_servers_status}}</td>
						<td>{{$lang.settings.server_group_servers_weight}}</td>
						<td>{{$lang.settings.server_group_servers_countries}}</td>
					</tr>
					{{if count($smarty.post.servers)<2}}
						<tr class="eg_data fixed_height_30">
							<td colspan="4">{{$lang.settings.server_group_divider_load_balancing_empty}}</td>
						</tr>
					{{else}}
						{{foreach item=item from=$smarty.post.servers|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30 {{if $item.status_id==0}}disabled{{/if}}">
								<td><a href="servers.php?action=change&amp;item_id={{$item.server_id}}">{{$item.title}}</a></td>
								<td>
									<select name="status_id_{{$item.server_id}}">
										<option value="0" {{if $item.status_id==0}}selected="selected"{{/if}}>{{$lang.settings.server_group_servers_status_disabled}}</option>
										<option value="1" {{if $item.status_id==1}}selected="selected"{{/if}}>{{$lang.settings.server_group_servers_status_active}}</option>
									</select>
								</td>
								<td><input type="text" class="fixed_100" name="weight_{{$item.server_id}}" value="{{$item.lb_weight}}" maxlength="5"/></td>
								<td><input type="text" class="dyn_full_size" name="countries_{{$item.server_id}}" value="{{$item.lb_countries}}" maxlength="1000"/></td>
							</tr>
						{{/foreach}}
					{{/if}}
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				{{if $smarty.get.action=='add_new_group'}}
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

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

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
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_storage_servers_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.server_add}}{{else}}<a href="{{$page_name}}?action=change_group&amp;item_id={{$smarty.post.group_id}}">{{$smarty.post.group_title}}</a> / {{$lang.settings.server_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/377-storage-system-in-kvs-tube-script">Storage system in KVS</a></span><br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/410-how-to-add-remote-content-servers-for-videos-and-photos-into-kvs-tube-script">How to add remote content servers for videos and photos</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.server_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.server_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		{{assign var="group_vis_str_videos" value=""}}
		{{assign var="group_vis_str_albums" value=""}}
		{{if $smarty.get.action=='add_new'}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.server_field_group}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="group_id" name="group_id">
							{{foreach name=data item=item from=$list_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $smarty.post.group_id==$item.group_id}}selected="selected"{{/if}}>
									{{$item.title}}
									{{if $item.content_type_id==1}}
										({{$lang.settings.server_field_group_videos}})
										{{assign var="group_vis_str_videos" value="`$group_vis_str_videos` group_id_`$item.group_id`"}}
									{{elseif $item.content_type_id==2}}
										({{$lang.settings.server_field_group_albums}})
										{{assign var="group_vis_str_albums" value="`$group_vis_str_albums` group_id_`$item.group_id`"}}
									{{/if}}
								</option>
							{{/foreach}}
						</select>
					</div>
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_label">{{$lang.settings.server_field_group}}:</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="group_id" disabled="disabled">
							{{foreach name=data item=item from=$list_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $smarty.post.group_id==$item.group_id}}selected="selected"{{/if}}>
									{{$item.title}}
									{{if $item.content_type_id==1}}
										({{$lang.settings.server_field_group_videos}})
										{{assign var="group_vis_str_videos" value="`$group_vis_str_videos` group_id_`$item.group_id`"}}
									{{elseif $item.content_type_id==2}}
										({{$lang.settings.server_field_group_albums}})
										{{assign var="group_vis_str_albums" value="`$group_vis_str_albums` group_id_`$item.group_id`"}}
									{{/if}}
								</option>
							{{/foreach}}
						</select>
					</div>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.server_field_urls}} (*):</td>
			<td class="de_control">
				<input id="urls" type="text" name="urls" class="dyn_full_size" value="{{$smarty.post.urls}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/>
					<span class="de_hint {{$group_vis_str_videos}}">{{$lang.settings.server_field_urls_videos_hint}}</span>
					<span class="de_hint {{$group_vis_str_albums}}">{{$lang.settings.server_field_urls_albums_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.server_field_streaming_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="streaming_type_id" name="streaming_type_id">
						<option value="0" {{if $smarty.post.streaming_type_id==0}}selected="selected"{{/if}}>{{$lang.settings.server_field_streaming_type_nginx}}</option>
						<option value="1" {{if $smarty.post.streaming_type_id==1}}selected="selected"{{/if}}>{{$lang.settings.server_field_streaming_type_apache}}</option>
						<option value="4" {{if $smarty.post.streaming_type_id==4}}selected="selected"{{/if}}>{{$lang.settings.server_field_streaming_type_cdn}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.server_field_streaming_type_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.post.nginx_config_rules!=''}}
			<tr class="streaming_type_id_0">
				<td class="de_label">{{$lang.settings.server_field_nginx_config_rules}}:</td>
				<td class="de_control">
					<textarea class="dyn_full_size html_code_editor readonly_field" rows="{{$smarty.post.nginx_config_rules_rows|default:"3"}}" readonly>{{$smarty.post.nginx_config_rules}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.post.content_type_id==1}}
							<span class="de_hint">{{$lang.settings.server_field_nginx_config_rules_videos_hint}}</span>
						{{elseif $smarty.post.content_type_id==2}}
							<span class="de_hint">{{$lang.settings.server_field_nginx_config_rules_albums_hint}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="streaming_type_id_4">
			<td class="de_label de_required">{{$lang.settings.server_field_streaming_script}} (*):</td>
			<td class="de_control">
				<input type="text" name="streaming_script" class="fixed_200" value="{{$smarty.post.streaming_script}}" maxlength="255"/>
				&nbsp;
				<a href="{{$page_name}}?action=download_api_cdn">{{$lang.settings.server_field_streaming_script_dl}}</a>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_streaming_script_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="streaming_type_id_4">
			<td class="de_label de_required">{{$lang.settings.server_field_streaming_secret_key}} (*):</td>
			<td class="de_control">
				<input type="text" name="streaming_key" class="dyn_full_size" value="{{$smarty.post.streaming_key}}" maxlength="255"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_streaming_secret_key_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.server_divider_connection}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.server_field_connection_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="connection_type_id" name="connection_type_id">
						<option value="0" {{if $smarty.post.connection_type_id==0}}selected="selected"{{/if}}>{{$lang.settings.server_field_connection_type_local}}</option>
						<option value="1" {{if $smarty.post.connection_type_id==1}}selected="selected"{{/if}}>{{$lang.settings.server_field_connection_type_mount}}</option>
						<option value="2" {{if $smarty.post.connection_type_id==2}}selected="selected"{{/if}}>{{$lang.settings.server_field_connection_type_ftp}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.server_field_connection_type_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="connection_type_id_0 connection_type_id_1">
			<td class="de_label de_required">{{$lang.settings.server_field_path}} (*):</td>
			<td class="de_control">
				<input type="text" name="path" maxlength="150" class="dyn_full_size" value="{{$smarty.post.path}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/>
					<span class="de_hint {{$group_vis_str_videos}}">{{$lang.settings.server_field_path_videos_hint}}</span>
					<span class="de_hint {{$group_vis_str_albums}}">{{$lang.settings.server_field_path_albums_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.server_field_ftp_host}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_host" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_host}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.server_field_ftp_port}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_port" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_port|default:'21'}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.server_field_ftp_user}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_user" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_user}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_2">
			{{if $smarty.post.connection_type_id==2}}
				<td class="de_label">{{$lang.settings.server_field_ftp_password}}:</td>
			{{else}}
				<td class="de_label de_required">{{$lang.settings.server_field_ftp_password}} (*):</td>
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
					<br/><span class="de_hint">{{$lang.settings.server_field_ftp_password_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label">{{$lang.settings.server_field_ftp_folder}}:</td>
			<td class="de_control">
				<input type="text" name="ftp_folder" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_folder}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_ftp_folder_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_2">
			<td class="de_label de_required">{{$lang.settings.server_field_ftp_timeout}} (*):</td>
			<td class="de_control">
				<input type="text" name="ftp_timeout" maxlength="150" class="dyn_full_size" value="{{$smarty.post.ftp_timeout|default:'20'}}"/>
			</td>
		</tr>
		<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
			<td class="de_label de_required">{{$lang.settings.server_field_control_script_url}} (*):</td>
			<td class="de_control">
				<input id="control_script_url" type="text" name="control_script_url" maxlength="150" class="dyn_full_size readonly_field" readonly="readonly"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_control_script_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
			<td class="de_label">{{$lang.settings.server_field_control_script_api_version}}:</td>
			<td class="de_control">
				{{$smarty.post.control_script_url_version|default:$lang.common.undefined}}
				&nbsp;
				<a href="{{$page_name}}?action=download_api">{{$lang.settings.server_field_control_script_api_version_dl|replace:"%1%":$latest_api_version}}</a>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_control_script_api_version_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
			<td class="de_label">{{$lang.settings.server_field_control_script_lock_ip}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="control_script_url_lock_ip" value="1" {{if $smarty.post.control_script_url_lock_ip==1}}checked="checked"{{/if}} {{if $smarty.post.control_script_url_version!='' && $smarty.post.control_script_url_lock_ip==0 && $smarty.post.numeric_control_script_url_version<391}}disabled="disabled"{{/if}}/><label>{{$lang.settings.server_field_control_script_lock_ip_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_control_script_lock_ip_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="connection_type_id_1 connection_type_id_2 streaming_type_id_0 streaming_type_id_1">
			<td class="de_label">{{$lang.settings.server_field_time_offset}}:</td>
			<td class="de_control">
				<input type="text" name="time_offset" maxlength="5" class="dyn_full_size" value="{{$smarty.post.time_offset|default:0}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_time_offset_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.server_divider_advanced}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.server_field_replace_domain_on_satellite}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_replace_domain_on_satellite" value="1" {{if $smarty.post.is_replace_domain_on_satellite==1}}checked="checked"{{/if}}/><label>{{$lang.settings.server_field_replace_domain_on_satellite_yes}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.server_field_replace_domain_on_satellite_hint}}</span>
				{{/if}}
			</td>
		</tr>
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
<div id="custom_js" class="js_params">
	<span class="js_param">buildServerLogic=call</span>
</div>

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
					<td>{{$lang.settings.dg_servers_col_title}}</td>
					<td>{{$lang.settings.dg_servers_col_status}}</td>
					<td>{{$lang.settings.dg_servers_col_api_version}}</td>
					<td>{{$lang.settings.dg_servers_col_content}}</td>
					<td>{{$lang.settings.dg_servers_col_total_space}}</td>
					<td>{{$lang.settings.dg_servers_col_free_space}}</td>
					<td>{{$lang.settings.dg_servers_col_load_average}}</td>
					<td>{{$lang.settings.dg_servers_col_debug_mode}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_group_header {{if $item.status_id==0}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.group_id}}" disabled="disabled"/></td>
						<td>
							<a href="{{$page_name}}?action=change_group&amp;item_id={{$item.group_id}}" class="{{if $item.status_id==1 && $item.free_space<$options.SERVER_GROUP_MIN_FREE_SPACE_MB*1024*1024}}warning_text{{/if}}">{{$item.title}}</a>
							{{if $item.status_id==1 && $item.free_space<$options.SERVER_GROUP_MIN_FREE_SPACE_MB*1024*1024}}
								<span class="warning_text">({{$lang.settings.dg_servers_warning_free_space}})</span>
							{{/if}}
						</td>
						<td></td>
						<td></td>
						<td>
							{{if $item.servers_amount>0}}
								{{if $item.content_type_id==1}}
									{{if in_array('videos|view',$smarty.session.permissions)}}
										<a href="videos.php?no_filter=true&amp;se_storage_group_id={{$item.group_id}}" class="no_popup">{{$item.videos_amount}} {{$lang.settings.dg_servers_col_content_videos}}</a>
									{{else}}
										{{$item.videos_amount}} {{$lang.settings.dg_servers_col_content_videos}}
									{{/if}}
								{{elseif $item.content_type_id==2}}
									{{if in_array('albums|view',$smarty.session.permissions)}}
										<a href="albums.php?no_filter=true&amp;se_storage_group_id={{$item.group_id}}" class="no_popup">{{$item.albums_amount}} {{$lang.settings.dg_servers_col_content_albums}}</a>
									{{else}}
										{{$item.albums_amount}} {{$lang.settings.dg_servers_col_content_albums}}
									{{/if}}
								{{/if}}
							{{/if}}
						</td>
						<td class="nowrap">{{if $item.servers_amount>0}}{{$item.total_space_string}}{{/if}}</td>
						<td class="nowrap {{if $item.status_id==1 && $item.free_space<$options.SERVER_GROUP_MIN_FREE_SPACE_MB*1024*1024}}warning_text{{/if}}">{{if $item.servers_amount>0}}{{$item.free_space_string}} ({{$item.free_space_percent}}%){{/if}}</td>
						<td class="nowrap">{{if $item.servers_amount>0}}{{$item.load|number_format:2}}{{/if}}</td>
						<td class="nowrap"></td>
						<td>
							<a href="{{$page_name}}?action=change_group&amp;item_id={{$item.group_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if $item.servers_amount==0}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id=0</span>
										<span class="js_param">g_id={{$item.group_id}}</span>
										<span class="js_param">a_id=0</span>
										<span class="js_param">name={{$item.title}}</span>
										<span class="js_param">enable_debug_hide=true</span>
										<span class="js_param">disable_debug_hide=true</span>
										<span class="js_param">view_debug_log_hide=true</span>
										<span class="js_param">activate_hide=true</span>
										<span class="js_param">deactivate_hide=true</span>
										<span class="js_param">sync_hide=true</span>
										<span class="js_param">test_hide=true</span>
									</span>
								</a>
							{{/if}}
						</td>
					</tr>
					{{foreach name=data_servers item=item_servers from=$item.servers|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data_servers.iteration % 2==0}} dg_even{{/if}} {{if $item_servers.status_id==0}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.group_id}}/{{$item.server_id}}" disabled="disabled"/></td>
							<td>
								<a href="{{$page_name}}?action=change&amp;item_id={{$item_servers.server_id}}" {{if $item_servers.error_iteration>1}}class="highlighted_text"{{elseif $item_servers.is_logging_enabled==1}}class="warning_text"{{/if}}>{{$item_servers.title}}</a>
								{{if $item_servers.error_iteration>1}}
									{{if $item_servers.error_id==1}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_write}})</span>
									{{elseif $item_servers.error_id==2}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_control_script}})</span>
									{{elseif $item_servers.error_id==3}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_control_script_key}})</span>
									{{elseif $item_servers.error_id==4}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_time_sync}})</span>
									{{elseif $item_servers.error_id==5}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_content_availability|replace:"%1%":"<a class=\"highlighted_text\" rel=\"external\" href=\"servers_test.php?server_id=`$item_servers.server_id`\">`$lang.settings.dg_servers_error_content_availability2`</a>"|smarty:nodefaults}})</span>
									{{elseif $item_servers.error_id==6}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_cdn_api}})</span>
									{{elseif $item_servers.error_id==7}}
										<span class="highlighted_text">({{$lang.settings.dg_servers_error_https}})</span>
									{{/if}}
								{{elseif $item_servers.is_logging_enabled==1}}
									<span class="warning_text">({{$lang.settings.dg_servers_warning_debug_enabled}})</span>
								{{/if}}
							</td>
							<td class="nowrap">{{if $item_servers.status_id==0}}{{$lang.settings.dg_servers_col_status_disabled}}{{elseif $item_servers.status_id==1}}{{$lang.settings.dg_servers_col_status_active}}{{/if}}</td>
							<td class="nowrap">{{if $item_servers.streaming_type_id!=4 && ($item_servers.connection_type_id==1 || $item_servers.connection_type_id==2)}}{{$item_servers.control_script_url_version}}{{else}}{{$lang.common.undefined}}{{/if}}</td>
							<td class="nowrap">-</td>
							<td class="nowrap">
								{{if $item_servers.streaming_type_id==0 || $item_servers.streaming_type_id==1}}
									{{$item_servers.total_space_string}}
								{{elseif $item_servers.streaming_type_id==4 && ($item_servers.connection_type_id==0 || $item_servers.connection_type_id==1)}}
									{{$item_servers.total_space_string}}
								{{elseif $item_servers.free_space==1073741824000}}
									{{$lang.settings.dg_servers_na}}
								{{else}}
									{{$item_servers.total_space_string}}
								{{/if}}
							</td>
							<td class="nowrap {{if $item.status_id==1 && $item_servers.free_space<$options.SERVER_GROUP_MIN_FREE_SPACE_MB*1024*1024}}warning_text{{/if}}">
								{{if $item_servers.streaming_type_id==0 || $item_servers.streaming_type_id==1}}
									{{$item_servers.free_space_string}} ({{$item_servers.free_space_percent}}%)
								{{elseif $item_servers.streaming_type_id==4 && ($item_servers.connection_type_id==0 || $item_servers.connection_type_id==1)}}
									{{$item_servers.free_space_string}} ({{$item_servers.free_space_percent}}%)
								{{elseif $item_servers.free_space==1073741824000}}
									{{$lang.settings.dg_servers_na}}
								{{else}}
									{{$item_servers.free_space_string}}
								{{/if}}
							</td>
							<td class="nowrap">
								{{if $item_servers.streaming_type_id==0 || $item_servers.streaming_type_id==1}}
									{{$item_servers.load|number_format:2}}
								{{elseif $item_servers.streaming_type_id==4 && ($item_servers.connection_type_id==0 || $item_servers.connection_type_id==1)}}
									{{$item_servers.load|number_format:2}}
								{{elseif $item_servers.free_space==1073741824000}}
									{{$lang.settings.dg_servers_na}}
								{{else}}
									{{$item_servers.load|number_format:2}}
								{{/if}}
							</td>
							<td class="nowrap">
								{{if $item_servers.is_logging_enabled==1}}{{$lang.common.yes}}{{else}}{{$lang.common.no}}{{/if}}
							</td>
							<td>
								<a href="{{$page_name}}?action=change&amp;item_id={{$item_servers.server_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item_servers.server_id}}</span>
										<span class="js_param">name={{$item_servers.title}}</span>
										<span class="js_param">g_id=0</span>
										<span class="js_param">a_id=0</span>
										{{if $item_servers.status_id==1 && $item.active_servers_amount==1 && ($item.videos_amount>0 || $item.albums_amount>0)}}
											<span class="js_param">delete_disable=true</span>
										{{/if}}
										{{if $item.active_servers_amount==1}}
											<span class="js_param">deactivate_disable=true</span>
										{{/if}}
										{{if $item_servers.status_id==1}}
											<span class="js_param">activate_hide=true</span>
										{{else}}
											<span class="js_param">deactivate_hide=true</span>
										{{/if}}
										{{if $item.total_servers_amount<=1}}
											<span class="js_param">sync_hide=true</span>
										{{/if}}
										{{if $sync_tasks_count>0}}
											<span class="js_param">sync_disable=true</span>
										{{/if}}
										{{if $item.videos_amount==0 && $item.albums_amount==0}}
											<span class="js_param">test_disable=true</span>
										{{/if}}
										{{if $item_servers.is_logging_enabled==1}}
											<span class="js_param">enable_debug_hide=true</span>
										{{else}}
											<span class="js_param">disable_debug_hide=true</span>
										{{/if}}
										{{if $item_servers.has_debug_log!=1}}
											<span class="js_param">view_debug_log_hide=true</span>
										{{/if}}
									</span>
								</a>
							</td>
						</tr>
					{{/foreach}}
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?action=delete&amp;id=${id}&amp;g_id=${g_id}&amp;a_id=${a_id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.settings.dg_servers_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">disable=${delete_disable}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=activate&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
					<span class="js_param">hide=${activate_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=deactivate&amp;id=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
					<span class="js_param">hide=${deactivate_hide}</span>
					<span class="js_param">disable=${deactivate_disable}</span>
					<span class="js_param">confirm={{$lang.settings.dg_servers_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=sync&amp;id=${id}</span>
					<span class="js_param">title={{$lang.settings.dg_servers_actions_sync}}</span>
					<span class="js_param">hide=${sync_hide}</span>
					<span class="js_param">disable=${sync_disable}</span>
					<span class="js_param">confirm={{$lang.settings.dg_servers_actions_sync_confirm|replace:"%1%":'${name}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=enable_debug&amp;id=${id}&amp;a_id=${a_id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
					<span class="js_param">hide=${enable_debug_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=disable_debug&amp;id=${id}&amp;a_id=${a_id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_disable_debug}}</span>
					<span class="js_param">hide=${disable_debug_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=view_debug_log&amp;id=${id}&amp;a_id=${a_id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_view_debug_log}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">hide=${view_debug_log_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=servers_test.php?server_id=${id}</span>
					<span class="js_param">title={{$lang.settings.dg_servers_actions_test_content}}</span>
					<span class="js_param">plain_link=true</span>
					<span class="js_param">hide=${test_hide}</span>
					<span class="js_param">disable=${test_disable}</span>
				</li>
			</ul>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}