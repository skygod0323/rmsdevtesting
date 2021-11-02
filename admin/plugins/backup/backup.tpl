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
		<input type="hidden" name="action" value="save_backup"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.backup.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.backup.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.backup.divider_parameters}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.plugins.backup.divider_parameters_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.plugins.backup.field_backup_folder}} (*):</td>
			<td class="de_control">
				<input type="text" name="backup_folder" class="dyn_full_size" value="{{$smarty.post.backup_folder}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_folder_hint}}</span>
					{{if $smarty.post.open_basedir!=''}}
						<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_folder_hint2|replace:"%1%":$smarty.post.open_basedir}}</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.backup.field_backup_auto}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="auto_backup_daily" value="1" {{if $smarty.post.auto_backup_daily==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_daily}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="auto_backup_weekly" value="1" {{if $smarty.post.auto_backup_weekly==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_weekly}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="auto_backup_monthly" value="1" {{if $smarty.post.auto_backup_monthly==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_monthly}}</label></div>
						</td>
					</tr>
					<tr>
						<td></td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="auto_skip_content_auxiliary" value="1" {{if $smarty.post.auto_skip_content_auxiliary==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.backup.field_backup_auto_skip_content_auxiliary}}</label></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.backup.field_schedule}}:</td>
			<td class="de_control">
				{{$lang.plugins.backup.field_schedule_interval}}:
				<input type="text" name="interval" maxlength="10" class="fixed_50" value="24" readonly/>
				&nbsp;&nbsp;
				{{$lang.plugins.backup.field_schedule_tod}}:
				<select name="tod">
					<option value="0" {{if $smarty.post.tod==0}}selected="selected"{{/if}}>{{$lang.plugins.backup.field_schedule_tod_any}}</option>
					<option value="1" {{if $smarty.post.tod==1}}selected="selected"{{/if}}>00:00-01:00</option>
					<option value="2" {{if $smarty.post.tod==2}}selected="selected"{{/if}}>01:00-02:00</option>
					<option value="3" {{if $smarty.post.tod==3}}selected="selected"{{/if}}>02:00-03:00</option>
					<option value="4" {{if $smarty.post.tod==4}}selected="selected"{{/if}}>03:00-04:00</option>
					<option value="5" {{if $smarty.post.tod==5}}selected="selected"{{/if}}>04:00-05:00</option>
					<option value="6" {{if $smarty.post.tod==6}}selected="selected"{{/if}}>05:00-06:00</option>
					<option value="7" {{if $smarty.post.tod==7}}selected="selected"{{/if}}>06:00-07:00</option>
					<option value="8" {{if $smarty.post.tod==8}}selected="selected"{{/if}}>07:00-08:00</option>
					<option value="9" {{if $smarty.post.tod==9}}selected="selected"{{/if}}>08:00-09:00</option>
					<option value="10" {{if $smarty.post.tod==10}}selected="selected"{{/if}}>09:00-10:00</option>
					<option value="11" {{if $smarty.post.tod==11}}selected="selected"{{/if}}>10:00-11:00</option>
					<option value="12" {{if $smarty.post.tod==12}}selected="selected"{{/if}}>11:00-12:00</option>
					<option value="13" {{if $smarty.post.tod==13}}selected="selected"{{/if}}>12:00-13:00</option>
					<option value="14" {{if $smarty.post.tod==14}}selected="selected"{{/if}}>13:00-14:00</option>
					<option value="15" {{if $smarty.post.tod==15}}selected="selected"{{/if}}>14:00-15:00</option>
					<option value="16" {{if $smarty.post.tod==16}}selected="selected"{{/if}}>15:00-16:00</option>
					<option value="17" {{if $smarty.post.tod==17}}selected="selected"{{/if}}>16:00-17:00</option>
					<option value="18" {{if $smarty.post.tod==18}}selected="selected"{{/if}}>17:00-18:00</option>
					<option value="19" {{if $smarty.post.tod==19}}selected="selected"{{/if}}>18:00-19:00</option>
					<option value="20" {{if $smarty.post.tod==20}}selected="selected"{{/if}}>19:00-20:00</option>
					<option value="21" {{if $smarty.post.tod==21}}selected="selected"{{/if}}>20:00-21:00</option>
					<option value="22" {{if $smarty.post.tod==22}}selected="selected"{{/if}}>21:00-22:00</option>
					<option value="23" {{if $smarty.post.tod==23}}selected="selected"{{/if}}>22:00-23:00</option>
					<option value="24" {{if $smarty.post.tod==24}}selected="selected"{{/if}}>23:00-00:00</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.backup.field_schedule_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.backup.field_last_exec}}:</td>
			<td class="de_control">
				{{if !$smarty.post.last_exec_date || $smarty.post.last_exec_date=='0000-00-00 00:00:00'}}
					{{$lang.plugins.backup.field_last_exec_none}}
				{{else}}
					{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
					(<a href="{{$page_name}}?plugin_id=backup&amp;action=get_log" rel="external">{{$smarty.post.duration|default:0}} {{$lang.plugins.backup.field_last_exec_seconds}}</a>)
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.backup.field_next_exec}}:</td>
			<td class="de_control">
				{{if !$smarty.post.next_exec_date || $smarty.post.next_exec_date=='0000-00-00 00:00:00'}}
					{{$lang.plugins.backup.field_next_exec_none}}
				{{else}}
					{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.backup.btn_save}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.backup.divider_manual_backup}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.backup.field_backup_options}} (*):</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="backup_mysql" value="1" {{if $smarty.post.has_mysqldump_error==1}}disabled="disabled"{{/if}}/><label>{{$lang.plugins.backup.field_backup_options_mysql}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_options_mysql_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="backup_website" value="1"/><label>{{$lang.plugins.backup.field_backup_options_website}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_options_website_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="backup_player" value="1"/><label>{{$lang.plugins.backup.field_backup_options_player}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_options_player_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="backup_kvs" value="1"/><label>{{$lang.plugins.backup.field_backup_options_kvs}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_options_kvs_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="backup_content_auxiliary" value="1"/><label>{{$lang.plugins.backup.field_backup_options_content_auxiliary}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_options_content_auxiliary_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" disabled="disabled"/><label>{{$lang.plugins.backup.field_backup_options_content_main}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.backup.field_backup_options_content_main_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="do_backup" value="{{$lang.plugins.backup.btn_backup}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.backup.divider_backups}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				{{if count($smarty.post.backups)>0}}
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.plugins.backup.dg_backups_col_filename}}</td>
							<td>{{$lang.plugins.backup.dg_backups_col_filedate}}</td>
							<td>{{$lang.plugins.backup.dg_backups_col_filesize}}</td>
							<td>{{$lang.plugins.backup.dg_backups_col_backup_type}}</td>
						</tr>
						{{foreach item=item from=$smarty.post.backups|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td>{{$item.filename}}</td>
								<td>{{$item.filedate|date_format:$smarty.session.userdata.full_date_format}}</td>
								<td>{{$item.filesize}}</td>
								<td>
									{{foreach item=item2 name=data2 from=$item.contents|smarty:nodefaults}}
										{{$item2}}{{if !$smarty.foreach.data2.last}}, {{/if}}
									{{/foreach}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.plugins.backup.divider_backups_none}}
				{{/if}}
			</td>
		</tr>
	</table>
</form>