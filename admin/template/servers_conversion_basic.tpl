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
		{{if $smarty.post.server_id<1}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.post.server_id}}"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{if $smarty.post.server_id<1}}{{$lang.settings.conversion_server_add}}{{else}}{{$lang.settings.conversion_server_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
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
		{{if $smarty.post.server_id>0}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.conversion_server_divider_general}}</div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.conversion_server_field_api_version}}:</td>
			<td class="de_control">{{$smarty.post.api_version|default:$lang.common.undefined}}</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_path}} (*):</td>
			<td class="de_control">
				<input type="text" name="path" maxlength="150" class="dyn_full_size" value="{{$smarty.post.path}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_path_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.conversion_server_field_max_tasks}} (*):</td>
			<td class="de_control">
				<input type="text" name="max_tasks" maxlength="10" class="dyn_full_size" value="{{$smarty.post.max_tasks|default:5}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_field_max_tasks_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.conversion_server_option_optimization}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="option_storage_servers" value="1" {{if $smarty.post.option_storage_servers==1}}checked="checked"{{/if}}/><label>{{$lang.settings.conversion_server_option_optimization_storage}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.conversion_server_option_optimization_storage_hint}}</span>
				{{/if}}
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
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
				{{if $smarty.post.has_old_api==1}}
					<input type="submit" name="update_api_version" value="{{$lang.settings.dg_conversion_servers_actions_update_api}}"/>
				{{/if}}
			</td>
		</tr>
	</table>
</form>