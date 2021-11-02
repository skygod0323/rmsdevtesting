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
		{{if $smarty.post.current_step=='pre'}}
			<input type="hidden" name="action" value="validate_pre"/>
			{{if count($smarty.post.custom_changes)>0}}
				<input type="hidden" name="has_custom_changed" value="1"/>
			{{/if}}
		{{elseif $smarty.post.current_step>0}}
			<input type="hidden" name="action" value="validate_step"/>
			<input type="hidden" name="step" value="{{$smarty.post.current_step}}"/>
		{{else}}
			<input type="hidden" name="action" value="upload"/>
		{{/if}}
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.kvs_update.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.kvs_update.long_desc}}
			</td>
		</tr>
		{{if $smarty.post.current_step=='pre'}}
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_update.field_update_version}}:</td>
				<td class="de_control">
					{{$smarty.post.update_version}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_update.field_update_info}}:</td>
				<td class="de_control">
					{{if $smarty.post.update_info!=''}}
						{{$smarty.post.update_info}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			{{if count($smarty.post.custom_changes)>0}}
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_custom_changes}}:</td>
					<td class="de_control">
						<table class="de_edit_grid">
							<colgroup>
								<col/>
							</colgroup>
							<tr class="eg_header fixed_height_30">
								<td>{{$lang.plugins.kvs_update.field_custom_changes_notice}}</td>
							</tr>
							{{foreach item=item from=$smarty.post.custom_changes|smarty:nodefaults}}
								<tr class="eg_data fixed_height_30">
									<td>{{$item}}</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
				<tr>
					<td class="de_label"></td>
					<td class="de_control">
						<div class="de_lv_pair"><input type="checkbox" name="confirm_continue" value="1"/><label>{{$lang.plugins.kvs_update.field_custom_changes_confirm}}</label></div>
					</td>
				</tr>
			{{/if}}
		{{elseif $smarty.post.current_step>0}}
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_update.field_step}}:</td>
				<td class="de_control">
					{{$lang.plugins.kvs_update.field_step_value|replace:"%1%":$smarty.post.current_step|replace:"%2%":$smarty.post.total_steps}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_update.field_description}}:</td>
				<td class="de_control">
					{{$smarty.post.step_description}}
				</td>
			</tr>
			{{if $smarty.post.mysql_update_log!=''}}
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_mysql_update_summary}}:</td>
					<td class="de_control">
						{{$lang.plugins.kvs_update.field_mysql_update_summary_value|replace:"%1%":$smarty.post.mysql_update_success_count|replace:"%2%":$smarty.post.mysql_update_errors_count}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.kvs_update.field_mysql_update_log}}:</td>
					<td class="de_control">
						<textarea class="html_code_editor dyn_full_size" rows="20" cols="40" readonly="readonly">{{$smarty.post.mysql_update_log}}</textarea>
					</td>
				</tr>
			{{/if}}
		{{else}}
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_update.field_get_update}}:</td>
				<td class="de_control">
					<a rel="external" href="https://www.kernel-video-sharing.com/{{$smarty.session.userdata.lang|substr:0:2}}/">https://www.kernel-video-sharing.com/{{$smarty.session.userdata.lang|substr:0:2}}/</a>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.kvs_update.field_get_update_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.kvs_update.field_update_archive}} (*):</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.plugins.kvs_update.field_update_archive}}</span>
							<span class="js_param">accept=zip</span>
						</div>
						<input type="text" name="update_archive" class="fixed_500" maxlength="100" readonly="readonly"/>
						<input type="hidden" name="update_archive_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.kvs_update.field_update_archive_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.kvs_update.field_validation_hash}} (*):</td>
				<td class="de_control">
					<input type="text" name="validation_hash" class="dyn_full_size" maxlength="32"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.kvs_update.field_validation_hash_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.kvs_update.field_backup}} (*):</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="backup_done" value="1"/><label>{{$lang.plugins.kvs_update.field_backup_text}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.kvs_update.field_backup_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.kvs_update.field_update_logs}}:</td>
				<td class="de_control">
					<a href="?plugin_id=kvs_update&amp;action=kvs_update_log" rel="external">kvs_update.log</a>&nbsp;
					<a href="?plugin_id=kvs_update&amp;action=mysql_update_log" rel="external">mysql_update.log</a>&nbsp;
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2">
				{{if $smarty.post.current_step>0 && $smarty.post.current_step<$smarty.post.total_steps}}
					<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_validate_and_next}}"/>
				{{elseif $smarty.post.current_step>0 && $smarty.post.current_step>=$smarty.post.total_steps}}
					<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_finish}}"/>
				{{elseif $smarty.post.current_step=='pre'}}
					<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_continue}}"/>
					<input type="submit" name="cancel" value="{{$lang.plugins.kvs_update.btn_cancel}}"/>
				{{else}}
					<input type="submit" name="save_default" value="{{$lang.plugins.kvs_update.btn_start}}"/>
				{{/if}}
			</td>
		</tr>
	</table>
</form>