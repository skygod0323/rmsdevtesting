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
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="select_complete"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.albums.submenu_option_select_albums}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.select_field_by}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="select_by" name="select_by">
						<option value="urls" {{if $smarty.post.select_by=='urls'}}selected{{/if}}>{{$lang.albums.select_field_by_urls}}</option>
						<option value="ids" {{if $smarty.post.select_by=='ids'}}selected{{/if}}>{{$lang.albums.select_field_by_ids}}</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">
				<div class="select_by_urls">{{$lang.albums.select_field_list_urls}} (*):</div>
				<div class="select_by_ids">{{$lang.albums.select_field_list_ids}} (*):</div>
			</td>
			<td class="de_control">
				<textarea name="selector" class="dyn_full_size" cols="30" rows="5">{{$smarty.post.selector}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/>
					<span class="de_hint select_by_urls">{{$lang.albums.select_field_list_urls_hint}}</span>
					<span class="de_hint select_by_ids">{{$lang.albums.select_field_list_ids_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.albums.select_field_operation}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input id="operation_list" type="radio" name="operation" value="list"/><label>{{$lang.albums.select_field_operation_list}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.albums.select_field_operation_list_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="operation_massedit" type="radio" name="operation" value="mass_edit" {{if !in_array('albums|edit_all',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.albums.select_field_operation_mass_edit}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.albums.select_field_operation_mass_edit_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="operation_mark_deleted" type="radio" name="operation" value="mark_deleted" {{if !in_array('albums|delete',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.albums.select_field_operation_mark_deleted}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.albums.select_field_operation_mark_deleted_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="operation_delete" type="radio" name="operation" value="delete" {{if !in_array('albums|delete',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.albums.select_field_operation_delete}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.albums.select_field_operation_delete_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr class="operation_delete">
			<td class="de_label de_required">{{$lang.albums.select_field_operation_confirm}} (*):</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="confirm" value="1"/><label>{{$lang.albums.select_field_operation_confirm_value}}</label></div>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>