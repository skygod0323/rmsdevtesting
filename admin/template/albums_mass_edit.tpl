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
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="edit_id" value="{{$smarty.get.edit_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.mass_edit_albums_header}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_selected_albums}}:</td>
			<td class="de_control">
				{{if $albums_count_all==1}}
					<strong><span class="highlighted_text">{{$lang.albums.mass_edit_albums_field_selected_albums_all}} ({{$albums_count}})</span></strong>
				{{else}}
					{{$albums_count}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.mass_edit_albums_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_directory}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="regenerate_directories" name="regenerate_directories" value="1" {{if $disallow_directory_change==1}}disabled="disabled"{{/if}}/> <label>{{$lang.albums.mass_edit_albums_field_directory_regenerate}}</label></div>
				{{if count($list_languages)>0}}
					<select name="regenerate_directories_language" class="regenerate_directories_on">
						<option value="">{{$lang.albums.mass_edit_albums_field_directory_default_language}}</option>
						{{foreach item=item from=$list_languages|smarty:nodefaults}}
							{{if $item.is_directories_localize==1}}
								<option value="{{$item.code}}">{{$item.title}}</option>
							{{/if}}
						{{/foreach}}
					</select>
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_directory_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_status}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="status_id">
						<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
						<option value="1">{{$lang.albums.mass_edit_albums_field_status_active}}</option>
						<option value="0">{{$lang.albums.mass_edit_albums_field_status_disabled}}</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="is_private">
						<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
						<option value="0">{{$lang.albums.mass_edit_albums_field_type_public}}</option>
						<option value="1">{{$lang.albums.mass_edit_albums_field_type_private}}</option>
						<option value="2">{{$lang.albums.mass_edit_albums_field_type_premium}}</option>
					</select>
				</div>
			</td>
		</tr>
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_label">{{$lang.albums.mass_edit_albums_field_access_level}}:</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select name="access_level_id">
							<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
							<option value="0">{{$lang.albums.mass_edit_albums_field_access_level_inherit}}</option>
							<option value="1">{{$lang.albums.mass_edit_albums_field_access_level_all}}</option>
							<option value="2">{{$lang.albums.mass_edit_albums_field_access_level_members}}</option>
							<option value="3">{{$lang.albums.mass_edit_albums_field_access_level_premium}}</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.mass_edit_albums_field_tokens_cost}}:</td>
				<td class="de_control">
					<input type="text" name="tokens_required" size="10" maxlength="10"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_tokens_cost_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_users}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users_noid.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_users_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="users"/>
					<div class="controls">
						<input type="text" name="new_user" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_users_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.albums.mass_edit_albums_field_users_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if in_array('system|administration',$smarty.session.permissions)}}
			<tr>
				<td class="de_label">{{$lang.albums.mass_edit_albums_field_admins}}:</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_admins.php</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=admin_user_ids[]</span>
							<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_admins_empty}}</span>
						</div>
						<div class="list"></div>
						<div class="controls">
							<input type="text" name="new_admin" class="fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_admins_all}}"/>
						</div>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.albums.mass_edit_albums_field_admins_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_content_source}}:</td>
			<td class="de_control">
				<select name="content_source_id">
					<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
					<option value="-1">{{$lang.albums.mass_edit_albums_field_content_source_reset}}</option>
					{{foreach name=data_groups item=item_group from=$list_content_sources|smarty:nodefaults}}
						<optgroup label="{{$item_group[0].content_source_group_title|default:$lang.albums.album_field_content_source_no_group}}">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.content_source_id}}">{{$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_post_date}}:</td>
			<td class="de_control">
				{{if $config.relative_post_dates=='true'}}
					<div class="de_vis_sw_radio">
						<table class="control_group">
							<tr>
								<td>
									<div class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" checked="checked"/><label>{{$lang.albums.mass_edit_albums_field_post_date_option_fixed}}</label></div>
									{{$lang.albums.mass_edit_albums_field_post_date_from}}: {{html_select_date prefix='post_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time='0000-00-00' all_extra='class="post_date_option_fixed"'}}&nbsp;&nbsp;
									{{$lang.albums.mass_edit_albums_field_post_date_to}}: {{html_select_date prefix='post_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time='0000-00-00' all_extra='class="post_date_option_fixed"'}}
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_date_hint}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1"/><label>{{$lang.albums.mass_edit_albums_field_post_date_option_relative}}</label></div>
									{{$lang.albums.mass_edit_albums_field_post_date_from}}: <input type="text" name="relative_post_date_from" class="fixed_100 post_date_option_relative" maxlength="5"/>&nbsp;&nbsp;
									{{$lang.albums.mass_edit_albums_field_post_date_to}}: <input type="text" name="relative_post_date_to" class="fixed_100 post_date_option_relative" maxlength="5"/>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_date_hint2}}</span>
									{{/if}}
								</td>
							</tr>
						</table>
					</div>
				{{else}}
					{{$lang.albums.mass_edit_albums_field_post_date_from}}: {{html_select_date prefix='post_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time='0000-00-00'}}&nbsp;&nbsp;
					{{$lang.albums.mass_edit_albums_field_post_date_to}}: {{html_select_date prefix='post_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time='0000-00-00'}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_date_hint}}</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_post_time}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input id="post_time_change" type="checkbox" name="post_time_change" value="1"/><label>{{$lang.albums.mass_edit_albums_field_post_time_change}}</label></div>
				{{$lang.albums.mass_edit_albums_field_post_time_from}}: <input type="text" name="post_time_from" maxlength="5" class="post_time_change_on" size="4" value="00:00"/>&nbsp;&nbsp;
				{{$lang.albums.mass_edit_albums_field_post_time_to}}: <input type="text" name="post_time_to" maxlength="5" class="post_time_change_on" size="4" value="23:59"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_post_time_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_rating}}:</td>
			<td class="de_control">
				{{$lang.albums.mass_edit_albums_field_rating_min}}:
				<input type="text" name="rating_min" class="fixed_100"/>
				{{$lang.albums.mass_edit_albums_field_rating_max}}:
				<input type="text" name="rating_max" class="fixed_100"/>&nbsp;&nbsp;
				{{$lang.albums.mass_edit_albums_field_rating_votes_from}}:
				<input type="text" name="rating_amount_min" class="fixed_50" value="1"/>
				{{$lang.albums.mass_edit_albums_field_rating_votes_to}}:
				<input type="text" name="rating_amount_max" class="fixed_50" value="1"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_rating_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_visits}}:</td>
			<td class="de_control">
				{{$lang.albums.mass_edit_albums_field_visits_min}}:
				<input type="text" name="visits_min" class="fixed_100"/>
				{{$lang.albums.mass_edit_albums_field_visits_max}}:
				<input type="text" name="visits_max" class="fixed_100"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_visits_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_lock_website}}:</td>
			<td class="de_control">
				<select name="is_locked">
					<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
					<option value="1">{{$lang.albums.mass_edit_albums_field_lock_website_locked}}</option>
					<option value="0">{{$lang.albums.mass_edit_albums_field_lock_website_unlocked}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_lock_website_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_review_flag}}:</td>
			<td class="de_control">
				<select name="is_review_needed">
					<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
					<option value="1">{{$lang.albums.mass_edit_albums_field_review_flag_set}}</option>
					<option value="0">{{$lang.albums.mass_edit_albums_field_review_flag_unset}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.mass_edit_albums_field_review_flag_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_admin_flag}}:</td>
			<td class="de_control">
				<select name="admin_flag_id">
					<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
					<option value="-1">{{$lang.albums.mass_edit_albums_field_admin_flag_reset}}</option>
					{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
						<option value="{{$item.flag_id}}">{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.mass_edit_albums_divider_categorization}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_tags_add}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="tags_add"/>
					<div class="controls">
						<input type="text" name="new_tag_add" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_tags_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_categories_add}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=category_ids_add[]</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_categories_empty}}</span>
					</div>
					<div class="list"></div>
					<div class="controls">
						<input type="text" name="new_category_add" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_categories_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_models_add}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=model_ids_add[]</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_models_empty}}</span>
					</div>
					<div class="list"></div>
					<div class="controls">
						<input type="text" name="new_model_add" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_models_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_tags_delete}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="tags_delete"/>
					<div class="controls">
						<input type="text" name="new_tag_delete" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_tags_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_categories_delete}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=category_ids_delete[]</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_categories_empty}}</span>
					</div>
					<div class="list"></div>
					<div class="controls">
						<input type="text" name="new_category_delete" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_categories_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_models_delete}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=model_ids_delete[]</span>
						<span class="js_param">empty_message={{$lang.albums.mass_edit_albums_field_models_empty}}</span>
					</div>
					<div class="list"></div>
					<div class="controls">
						<input type="text" name="new_model_delete" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.mass_edit_albums_field_models_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_flag_clear}}:</td>
			<td class="de_control">
				<select name="flag_id">
					<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
					{{foreach item=item from=$list_flags_albums|smarty:nodefaults}}
						<option value="{{$item.flag_id}}">{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.mass_edit_albums_divider_content}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_change_storage_group}}:</td>
			<td class="de_control">
				<select name="new_storage_group_id">
					<option value="">{{$lang.albums.mass_edit_albums_do_not_change}}</option>
					{{foreach item=item from=$list_server_groups|smarty:nodefaults}}
						<option value="{{$item.group_id}}">{{$item.title}} ({{$lang.albums.mass_edit_albums_field_change_storage_group_free|replace:"%1%":$item.free_space|replace:"%2%":$item.total_space}})</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.mass_edit_albums_field_format_album_create}}:</td>
			<td class="de_control">
				<table class="control_group">
					<colgroup>
						<col width="5%"/>
						<col width="95%"/>
					</colgroup>
					<tr>
						<td class="nowrap">{{$lang.albums.mass_edit_albums_field_format_album_create_main}}:&nbsp;&nbsp;</td>
						<td>
							{{foreach item="item" from=$list_formats_albums_main|smarty:nodefaults}}
								<div class="de_lv_pair"><input type="checkbox" name="album_format_recreate_ids[]" value="{{$item.format_album_id}}"/><label>{{$item.title}}</label></div>
							{{/foreach}}
						</td>
					</tr>
					<tr>
						<td class="nowrap">{{$lang.albums.mass_edit_albums_field_format_album_create_preview}}:&nbsp;&nbsp;</td>
						<td>
							{{foreach item="item" from=$list_formats_albums_preview|smarty:nodefaults}}
								<div class="de_lv_pair"><input type="checkbox" name="album_format_recreate_ids[]" value="{{$item.format_album_id}}"/><label>{{$item.title}}</label></div>
							{{/foreach}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		{{if count($list_post_process_plugins)>0}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.albums.mass_edit_albums_divider_plugins}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.albums.mass_edit_albums_field_plugins}}:</td>
				<td class="de_control">
					<table class="control_group">
						{{foreach item=item from=$list_post_process_plugins|smarty:nodefaults}}
							<tr><td>
								<div class="de_lv_pair"><input type="checkbox" name="post_process_plugins[]" value="{{$item.plugin_id}}"/> <label>{{$lang.albums.mass_edit_albums_field_plugins_execute|replace:"%1%":$item.title}}</label></div>
							</td></tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.albums.mass_edit_albums_btn_apply}}"/>
			</td>
		</tr>
	</table>
</form>