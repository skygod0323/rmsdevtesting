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

{{if $smarty.request.page=='general_settings'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.system_header}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_file_upload_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_file_upload_local}}:</td>
			<td class="de_control">
				<select name="FILE_UPLOAD_DISK_OPTION">
					<option value="public" {{if $data.FILE_UPLOAD_DISK_OPTION=='public'}}selected="selected"{{/if}}>{{$lang.settings.system_field_file_upload_local_public}}</option>
					<option value="members" {{if $data.FILE_UPLOAD_DISK_OPTION=='members'}}selected="selected"{{/if}}>{{$lang.settings.system_field_file_upload_local_members}}</option>
					<option value="admins" {{if $data.FILE_UPLOAD_DISK_OPTION=='admins'}}selected="selected"{{/if}}>{{$lang.settings.system_field_file_upload_local_admins}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_file_upload_url}}:</td>
			<td class="de_control">
				<select name="FILE_UPLOAD_URL_OPTION">
					<option value="public" {{if $data.FILE_UPLOAD_URL_OPTION=='public'}}selected="selected"{{/if}}>{{$lang.settings.system_field_file_upload_url_public}}</option>
					<option value="members" {{if $data.FILE_UPLOAD_URL_OPTION=='members'}}selected="selected"{{/if}}>{{$lang.settings.system_field_file_upload_url_members}}</option>
					<option value="admins" {{if $data.FILE_UPLOAD_URL_OPTION=='admins'}}selected="selected"{{/if}}>{{$lang.settings.system_field_file_upload_url_admins}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_file_upload_size_limit}}:</td>
			<td class="de_control">
				<input type="text" name="FILE_UPLOAD_SIZE_LIMIT" maxlength="20" size="8" value="{{if $data.FILE_UPLOAD_SIZE_LIMIT>0}}{{$data.FILE_UPLOAD_SIZE_LIMIT}}{{/if}}"/>
				{{$lang.settings.system_field_file_upload_size_limit_units}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_file_upload_size_limit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_file_download_speed_limit}}:</td>
			<td class="de_control">
				<input type="text" name="FILE_DOWNLOAD_SPEED_LIMIT" maxlength="20" size="8" value="{{if $data.FILE_DOWNLOAD_SPEED_LIMIT>0}}{{$data.FILE_DOWNLOAD_SPEED_LIMIT}}{{/if}}"/>
				{{$lang.settings.system_field_file_download_speed_limit_units}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_file_download_speed_limit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_images_settings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="4">
					<span class="de_hint">{{$lang.settings.system_divider_images_settings_hint|replace:"%1%":$lang.settings.common_screenshot_resize_option_fixed_size|replace:"%2%":$lang.settings.common_screenshot_resize_option_dyn_size|replace:"%3%":$lang.settings.common_screenshot_resize_option_dyn_height|replace:"%4%":$lang.settings.common_screenshot_resize_option_dyn_width}}</span>
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_user_avatar_size}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<input type="text" name="USER_AVATAR_SIZE" maxlength="20" size="8" value="{{$data.USER_AVATAR_SIZE}}"/>
						<select name="USER_AVATAR_TYPE">
							<option value="need_size" {{if $data.USER_AVATAR_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.USER_AVATAR_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.USER_AVATAR_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.USER_AVATAR_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;
						{{$lang.settings.common_screenshot2_option}}:
						<select id="USER_COVER_OPTION" name="USER_COVER_OPTION">
							<option value="0" {{if $data.USER_COVER_OPTION==0}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
							<option value="1" {{if $data.USER_COVER_OPTION==1}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
							<option value="2" {{if $data.USER_COVER_OPTION==2}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
						</select>
						&nbsp;
						<span class="USER_COVER_OPTION_1 USER_COVER_OPTION_2">
							<input type="text" name="USER_COVER_SIZE" maxlength="20" size="8" value="{{$data.USER_COVER_SIZE}}"/>
							<select name="USER_COVER_TYPE">
								<option value="need_size" {{if $data.USER_COVER_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.USER_COVER_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.USER_COVER_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.USER_COVER_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_category_screenshot_size}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<input type="text" name="CATEGORY_AVATAR_SIZE" maxlength="20" size="8" value="{{$data.CATEGORY_AVATAR_SIZE}}"/>
					<select name="CATEGORY_AVATAR_TYPE">
						<option value="need_size" {{if $data.CATEGORY_AVATAR_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
						<option value="max_size" {{if $data.CATEGORY_AVATAR_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
						<option value="max_width" {{if $data.CATEGORY_AVATAR_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
						<option value="max_height" {{if $data.CATEGORY_AVATAR_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;
					{{$lang.settings.common_screenshot2_option}}:
					<select id="CATEGORY_AVATAR_OPTION" name="CATEGORY_AVATAR_OPTION">
						<option value="0" {{if $data.CATEGORY_AVATAR_OPTION==0}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
						<option value="1" {{if $data.CATEGORY_AVATAR_OPTION==1}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
						<option value="2" {{if $data.CATEGORY_AVATAR_OPTION==2}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
					</select>
					&nbsp;
						<span class="CATEGORY_AVATAR_OPTION_1 CATEGORY_AVATAR_OPTION_2">
							<input type="text" name="CATEGORY_AVATAR_2_SIZE" maxlength="20" size="8" value="{{$data.CATEGORY_AVATAR_2_SIZE}}"/>
							<select name="CATEGORY_AVATAR_2_TYPE">
								<option value="need_size" {{if $data.CATEGORY_AVATAR_2_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.CATEGORY_AVATAR_2_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.CATEGORY_AVATAR_2_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.CATEGORY_AVATAR_2_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_cs_screenshot_size}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<input type="text" name="CS_SCREENSHOT_1_SIZE" maxlength="20" size="8" value="{{$data.CS_SCREENSHOT_1_SIZE}}"/>
					<select name="CS_SCREENSHOT_1_TYPE">
						<option value="need_size" {{if $data.CS_SCREENSHOT_1_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
						<option value="max_size" {{if $data.CS_SCREENSHOT_1_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
						<option value="max_width" {{if $data.CS_SCREENSHOT_1_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
						<option value="max_height" {{if $data.CS_SCREENSHOT_1_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
					</select>
					&nbsp;&nbsp;&nbsp;&nbsp;
					{{$lang.settings.common_screenshot2_option}}:
					<select id="CS_SCREENSHOT_OPTION" name="CS_SCREENSHOT_OPTION">
						<option value="0" {{if $data.CS_SCREENSHOT_OPTION==0}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
						<option value="1" {{if $data.CS_SCREENSHOT_OPTION==1}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
						<option value="2" {{if $data.CS_SCREENSHOT_OPTION==2}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
					</select>
					&nbsp;
					<span class="CS_SCREENSHOT_OPTION_1 CS_SCREENSHOT_OPTION_2">
						<input type="text" name="CS_SCREENSHOT_2_SIZE" maxlength="20" size="8" value="{{$data.CS_SCREENSHOT_2_SIZE}}"/>
						<select name="CS_SCREENSHOT_2_TYPE">
							<option value="need_size" {{if $data.CS_SCREENSHOT_2_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.CS_SCREENSHOT_2_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.CS_SCREENSHOT_2_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.CS_SCREENSHOT_2_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
					</span>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_model_screenshot_size}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<input type="text" name="MODELS_SCREENSHOT_1_SIZE" maxlength="20" size="8" value="{{$data.MODELS_SCREENSHOT_1_SIZE}}"/>
						<select name="MODELS_SCREENSHOT_1_TYPE">
							<option value="need_size" {{if $data.MODELS_SCREENSHOT_1_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.MODELS_SCREENSHOT_1_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.MODELS_SCREENSHOT_1_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.MODELS_SCREENSHOT_1_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;
						{{$lang.settings.common_screenshot2_option}}:
						<select id="MODELS_SCREENSHOT_OPTION" name="MODELS_SCREENSHOT_OPTION">
							<option value="0" {{if $data.MODELS_SCREENSHOT_OPTION==0}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
							<option value="1" {{if $data.MODELS_SCREENSHOT_OPTION==1}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
							<option value="2" {{if $data.MODELS_SCREENSHOT_OPTION==2}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
						</select>
						&nbsp;
						<span class="MODELS_SCREENSHOT_OPTION_1 MODELS_SCREENSHOT_OPTION_2">
							<input type="text" name="MODELS_SCREENSHOT_2_SIZE" maxlength="20" size="8" value="{{$data.MODELS_SCREENSHOT_2_SIZE}}"/>
							<select name="MODELS_SCREENSHOT_2_TYPE">
								<option value="need_size" {{if $data.MODELS_SCREENSHOT_2_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.MODELS_SCREENSHOT_2_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.MODELS_SCREENSHOT_2_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.MODELS_SCREENSHOT_2_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_dvd_cover_size}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<input type="text" name="DVD_COVER_1_SIZE" maxlength="20" size="8" value="{{$data.DVD_COVER_1_SIZE}}"/>
						<select name="DVD_COVER_1_TYPE">
							<option value="need_size" {{if $data.DVD_COVER_1_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.DVD_COVER_1_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.DVD_COVER_1_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.DVD_COVER_1_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;
						{{$lang.settings.common_screenshot2_option}}:
						<select id="DVD_COVER_OPTION" name="DVD_COVER_OPTION">
							<option value="0" {{if $data.DVD_COVER_OPTION==0}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
							<option value="1" {{if $data.DVD_COVER_OPTION==1}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
							<option value="2" {{if $data.DVD_COVER_OPTION==2}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
						</select>
						&nbsp;
						<span class="DVD_COVER_OPTION_1 DVD_COVER_OPTION_2">
							<input type="text" name="DVD_COVER_2_SIZE" maxlength="20" size="8" value="{{$data.DVD_COVER_2_SIZE}}"/>
							<select name="DVD_COVER_2_TYPE">
								<option value="need_size" {{if $data.DVD_COVER_2_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.DVD_COVER_2_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.DVD_COVER_2_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.DVD_COVER_2_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.system_field_dvd_group_cover_size}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<input type="text" name="DVD_GROUP_COVER_1_SIZE" maxlength="20" size="8" value="{{$data.DVD_GROUP_COVER_1_SIZE}}"/>
						<select name="DVD_GROUP_COVER_1_TYPE">
							<option value="need_size" {{if $data.DVD_GROUP_COVER_1_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
							<option value="max_size" {{if $data.DVD_GROUP_COVER_1_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
							<option value="max_width" {{if $data.DVD_GROUP_COVER_1_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
							<option value="max_height" {{if $data.DVD_GROUP_COVER_1_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
						</select>
						&nbsp;&nbsp;&nbsp;&nbsp;
						{{$lang.settings.common_screenshot2_option}}:
						<select id="DVD_GROUP_COVER_OPTION" name="DVD_GROUP_COVER_OPTION">
							<option value="0" {{if $data.DVD_GROUP_COVER_OPTION==0}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_disabled}}</option>
							<option value="1" {{if $data.DVD_GROUP_COVER_OPTION==1}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_autocreate}}</option>
							<option value="2" {{if $data.DVD_GROUP_COVER_OPTION==2}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot2_option_upload}}</option>
						</select>
						&nbsp;
						<span class="DVD_GROUP_COVER_OPTION_1 DVD_GROUP_COVER_OPTION_2">
							<input type="text" name="DVD_GROUP_COVER_2_SIZE" maxlength="20" size="8" value="{{$data.DVD_GROUP_COVER_2_SIZE}}"/>
							<select name="DVD_GROUP_COVER_2_TYPE">
								<option value="need_size" {{if $data.DVD_GROUP_COVER_2_TYPE=='need_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_fixed_size}}</option>
								<option value="max_size" {{if $data.DVD_GROUP_COVER_2_TYPE=='max_size'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_size}}</option>
								<option value="max_width" {{if $data.DVD_GROUP_COVER_2_TYPE=='max_width'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_height}}</option>
								<option value="max_height" {{if $data.DVD_GROUP_COVER_2_TYPE=='max_height'}}selected="selected"{{/if}}>{{$lang.settings.common_screenshot_resize_option_dyn_width}}</option>
							</select>
						</span>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_categorization_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_tags_disable}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<input type="hidden" name="TAGS_DISABLE_ALL" value="0"/>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="tags_disable_all" type="checkbox" name="TAGS_DISABLE_ALL" value="1" {{if $data.TAGS_DISABLE_ALL==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_tags_disable_new}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_tags_disable_new_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="tags_disable_all_off">
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="tags_disable_compound" type="checkbox" name="ENABLE_TAGS_DISABLE_COMPOUND" value="1" {{if $data.TAGS_DISABLE_COMPOUND>0}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_tags_disable_compound}}</label></div>
							<input type="text" name="TAGS_DISABLE_COMPOUND" class="tags_disable_compound_on" value="{{if $data.TAGS_DISABLE_COMPOUND==0}}{{else}}{{$data.TAGS_DISABLE_COMPOUND}}{{/if}}" maxlength="5" size="10"/>
							{{$lang.settings.system_field_tags_disable_words}}
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_tags_disable_compound_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="tags_disable_all_off">
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="tags_disable_length_min" type="checkbox" name="ENABLE_TAGS_DISABLE_LENGTH_MIN" value="1" {{if $data.TAGS_DISABLE_LENGTH_MIN>0}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_tags_disable_size_min}}</label></div>
							<input type="text" name="TAGS_DISABLE_LENGTH_MIN" class="tags_disable_length_min_on" value="{{if $data.TAGS_DISABLE_LENGTH_MIN==0}}{{else}}{{$data.TAGS_DISABLE_LENGTH_MIN}}{{/if}}" maxlength="5" size="10"/>
							{{$lang.settings.system_field_tags_disable_characters}}
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_tags_disable_size_min_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="tags_disable_all_off">
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="tags_disable_length_max" type="checkbox" name="ENABLE_TAGS_DISABLE_LENGTH_MAX" value="1" {{if $data.TAGS_DISABLE_LENGTH_MAX>0}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_tags_disable_size_max}}</label></div>
							<input type="text" name="TAGS_DISABLE_LENGTH_MAX" class="tags_disable_length_max_on" value="{{if $data.TAGS_DISABLE_LENGTH_MAX==0}}{{else}}{{$data.TAGS_DISABLE_LENGTH_MAX}}{{/if}}" maxlength="5" size="10"/>
							{{$lang.settings.system_field_tags_disable_characters}}
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_tags_disable_size_max_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="tags_disable_all_off">
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="tags_disable_characters" type="checkbox" name="ENABLE_TAGS_DISABLE_CHARACTERS" value="1" {{if $data.TAGS_DISABLE_CHARACTERS}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_tags_disable_contains}}</label></div>
							<input type="text" name="TAGS_DISABLE_CHARACTERS" class="tags_disable_characters_on" value="{{$data.TAGS_DISABLE_CHARACTERS}}" maxlength="1000" size="10"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_tags_disable_contains_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="tags_disable_all_off">
						<td>
							<input type="hidden" name="TAGS_DISABLE_LIST_ENABLED" value="0"/>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="tags_disable_list" type="checkbox" name="TAGS_DISABLE_LIST_ENABLED" value="1" {{if $data.TAGS_DISABLE_LIST_ENABLED==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_tags_disable_list}}</label></div>
						</td>
					</tr>
					<tr class="tags_disable_all_off">
						<td>
							<textarea name="TAGS_DISABLE_LIST" rows="3" class="dyn_full_size tags_disable_list_on">{{$data.TAGS_DISABLE_LIST}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_tags_disable_list_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_new_tags}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<input type="hidden" name="TAGS_FORCE_LOWERCASE" value="0"/>
							<div class="de_lv_pair"><input type="checkbox" name="TAGS_FORCE_LOWERCASE" value="1" {{if $data.TAGS_FORCE_LOWERCASE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_new_tags_lowercase}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_new_tags_lowercase_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="TAGS_FORCE_DISABLED" value="0"/>
							<div class="de_lv_pair"><input type="checkbox" name="TAGS_FORCE_DISABLED" value="1" {{if $data.TAGS_FORCE_DISABLED==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_new_tags_deactivate}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_new_tags_deactivate_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_renamed_tags}}:</td>
			<td class="de_control">
				<input type="hidden" name="TAGS_ADD_SYNONYMS_ON_RENAME" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="TAGS_ADD_SYNONYMS_ON_RENAME" value="1" {{if $data.TAGS_ADD_SYNONYMS_ON_RENAME==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_renamed_tags_add_synonyms}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_renamed_tags_add_synonyms_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_label">{{$lang.settings.system_field_rank_models}}:</td>
				<td class="de_control">
					<select name="MODELS_RANK_BY">
						<option value="" {{if $data.MODELS_RANK_BY==''}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_disabled}}</option>
						<option value="rating" {{if $data.MODELS_RANK_BY=='rating'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_rating}}</option>
						<option value="model_viewed" {{if $data.MODELS_RANK_BY=='model_viewed'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_visits}}</option>
						<option value="comments_count" {{if $data.MODELS_RANK_BY=='comments_count'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_comments}}</option>
						<option value="subscribers_count" {{if $data.MODELS_RANK_BY=='subscribers_count'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_subscribers}}</option>
						<option value="total_videos" {{if $data.MODELS_RANK_BY=='total_videos'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_videos}}</option>
						<option value="avg_videos_rating" {{if $data.MODELS_RANK_BY=='avg_videos_rating'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_videos_rating}}</option>
						<option value="avg_videos_popularity" {{if $data.MODELS_RANK_BY=='avg_videos_popularity'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_videos_visits}}</option>
						<option value="total_albums" {{if $data.MODELS_RANK_BY=='total_albums'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_albums}}</option>
						<option value="avg_albums_rating" {{if $data.MODELS_RANK_BY=='avg_albums_rating'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_albums_rating}}</option>
						<option value="avg_albums_popularity" {{if $data.MODELS_RANK_BY=='avg_albums_popularity'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_albums_visits}}</option>
						<option value="added_date" {{if $data.MODELS_RANK_BY=='added_date'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_added_date}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_rank_models_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.system_field_rank_cs}}:</td>
			<td class="de_control">
				<select name="CS_RANK_BY">
					<option value="" {{if $data.CS_RANK_BY==''}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_disabled}}</option>
					<option value="rating" {{if $data.CS_RANK_BY=='rating'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_rating}}</option>
					<option value="cs_viewed" {{if $data.CS_RANK_BY=='cs_viewed'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_visits}}</option>
					<option value="comments_count" {{if $data.CS_RANK_BY=='comments_count'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_comments}}</option>
					<option value="subscribers_count" {{if $data.CS_RANK_BY=='subscribers_count'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_subscribers}}</option>
					<option value="total_videos" {{if $data.CS_RANK_BY=='total_videos'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_videos}}</option>
					<option value="avg_videos_rating" {{if $data.CS_RANK_BY=='avg_videos_rating'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_videos_rating}}</option>
					<option value="avg_videos_popularity" {{if $data.CS_RANK_BY=='avg_videos_popularity'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_videos_visits}}</option>
					<option value="total_albums" {{if $data.CS_RANK_BY=='total_albums'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_albums}}</option>
					<option value="avg_albums_rating" {{if $data.CS_RANK_BY=='avg_albums_rating'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_albums_rating}}</option>
					<option value="avg_albums_popularity" {{if $data.CS_RANK_BY=='avg_albums_popularity'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_albums_visits}}</option>
					<option value="added_date" {{if $data.CS_RANK_BY=='added_date'}}selected="selected"{{/if}}>{{$lang.settings.system_field_rank_by_added_date}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_rank_cs_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_directories_settings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="4">
					<span class="de_hint">{{$lang.settings.system_divider_directories_settings_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_directories_max_length}} (*):</td>
			<td class="de_control">
				<input type="text" name="DIRECTORIES_MAX_LENGTH" maxlength="1000" class="dyn_full_size" value="{{$data.DIRECTORIES_MAX_LENGTH}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_directories_max_length_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_directories_translit}}:</td>
			<td class="de_control">
				<input type="hidden" name="DIRECTORIES_TRANSLIT" value="0"/>
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="directories_translit" name="DIRECTORIES_TRANSLIT" value="1" {{if $data.DIRECTORIES_TRANSLIT==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_directories_translit_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_directories_translit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_directories_translit_rules}}:</td>
			<td class="de_control">
				<textarea id="directories_translit_rules" name="DIRECTORIES_TRANSLIT_RULES" rows="3" class="dyn_full_size directories_translit_on">{{$data.DIRECTORIES_TRANSLIT_RULES}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_directories_translit_rules_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_conversion_settings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/538-video-conversion-engine-and-video-conversion-speed">Video conversion engine and video conversion speed</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.system_field_pause_tasks_processing}}:</td>
			<td class="de_control">
				<input type="hidden" name="ENABLE_BACKGROUND_TASKS_PAUSE" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="ENABLE_BACKGROUND_TASKS_PAUSE" value="1" {{if $data.ENABLE_BACKGROUND_TASKS_PAUSE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_pause_tasks_processing_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_pause_tasks_processing_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_conversion_limit}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="limit_conversion_la" type="checkbox" name="LIMIT_CONVERSION_LA_ENABLE" value="1" {{if $data.LIMIT_CONVERSION_LA!=''}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_conversion_limit_la}}</label></div>
							<input type="text" name="LIMIT_CONVERSION_LA" class="limit_conversion_la_on" value="{{$data.LIMIT_CONVERSION_LA}}" maxlength="5" size="5"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_conversion_limit_la_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input id="limit_conversion_time" type="checkbox" name="LIMIT_CONVERSION_TIME_ENABLE" value="1" {{if $data.LIMIT_CONVERSION_TIME_FROM!='' || $data.LIMIT_CONVERSION_TIME_TO!=''}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_conversion_limit_time_from}}</label></div>
							<input type="text" name="LIMIT_CONVERSION_TIME_FROM" class="limit_conversion_time_on" value="{{$data.LIMIT_CONVERSION_TIME_FROM}}" maxlength="5" size="5"/>
							&nbsp;{{$lang.settings.system_field_conversion_limit_time_to}}&nbsp;
							<input type="text" name="LIMIT_CONVERSION_TIME_TO" class="limit_conversion_time_on" value="{{$data.LIMIT_CONVERSION_TIME_TO}}" maxlength="5" size="5"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_conversion_limit_time_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_nice}}:</td>
			<td class="de_control">
				<select name="GLOBAL_CONVERTATION_PRIORITY">
					<option value="0" {{if $data.GLOBAL_CONVERTATION_PRIORITY==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_nice_realtime}}</option>
					<option value="4" {{if $data.GLOBAL_CONVERTATION_PRIORITY==4}}selected="selected"{{/if}}>{{$lang.settings.system_field_nice_high}}</option>
					<option value="9" {{if $data.GLOBAL_CONVERTATION_PRIORITY==9}}selected="selected"{{/if}}>{{$lang.settings.system_field_nice_medium}}</option>
					<option value="14" {{if $data.GLOBAL_CONVERTATION_PRIORITY==14}}selected="selected"{{/if}}>{{$lang.settings.system_field_nice_low}}</option>
					<option value="19" {{if $data.GLOBAL_CONVERTATION_PRIORITY==19}}selected="selected"{{/if}}>{{$lang.settings.system_field_nice_very_low}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_nice_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_user_content_priority}}:</td>
			<td class="de_control">
				{{$lang.settings.system_field_user_content_priority_standard}}:
				<select name="USER_TASKS_PRIORITY_STANDARD">
					<option value="14" {{if $data.USER_TASKS_PRIORITY_STANDARD==14}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
					<option value="12" {{if $data.USER_TASKS_PRIORITY_STANDARD==12}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
					<option value="10" {{if $data.USER_TASKS_PRIORITY_STANDARD==10}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
					<option value="8" {{if $data.USER_TASKS_PRIORITY_STANDARD==8}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
					<option value="6" {{if $data.USER_TASKS_PRIORITY_STANDARD==6}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.system_field_user_content_priority_trusted}}:
				<select name="USER_TASKS_PRIORITY_TRUSTED">
					<option value="14" {{if $data.USER_TASKS_PRIORITY_TRUSTED==14}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
					<option value="12" {{if $data.USER_TASKS_PRIORITY_TRUSTED==12}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
					<option value="10" {{if $data.USER_TASKS_PRIORITY_TRUSTED==10}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
					<option value="8" {{if $data.USER_TASKS_PRIORITY_TRUSTED==8}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
					<option value="6" {{if $data.USER_TASKS_PRIORITY_TRUSTED==6}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.system_field_user_content_priority_webmaster}}:
				<select name="USER_TASKS_PRIORITY_WEBMASTER">
					<option value="14" {{if $data.USER_TASKS_PRIORITY_WEBMASTER==14}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
					<option value="12" {{if $data.USER_TASKS_PRIORITY_WEBMASTER==12}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
					<option value="10" {{if $data.USER_TASKS_PRIORITY_WEBMASTER==10}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
					<option value="8" {{if $data.USER_TASKS_PRIORITY_WEBMASTER==8}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
					<option value="6" {{if $data.USER_TASKS_PRIORITY_WEBMASTER==6}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.system_field_user_content_priority_premium}}:
				<select name="USER_TASKS_PRIORITY_PREMIUM">
					<option value="14" {{if $data.USER_TASKS_PRIORITY_PREMIUM==14}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_high}}</option>
					<option value="12" {{if $data.USER_TASKS_PRIORITY_PREMIUM==12}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_higher}}</option>
					<option value="10" {{if $data.USER_TASKS_PRIORITY_PREMIUM==10}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_normal}}</option>
					<option value="8" {{if $data.USER_TASKS_PRIORITY_PREMIUM==8}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_lower}}</option>
					<option value="6" {{if $data.USER_TASKS_PRIORITY_PREMIUM==6}}selected="selected"{{/if}}>{{$lang.settings.system_field_user_content_priority_low}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_user_content_priority_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_min_server_space_to_alert}} (*):</td>
			<td class="de_control">
				<input type="text" name="MAIN_SERVER_MIN_FREE_SPACE_MB" maxlength="1000" class="fixed_200" value="{{$data.MAIN_SERVER_MIN_FREE_SPACE_MB}}"/>
				{{$lang.settings.system_field_min_server_space_to_alert_mb}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_min_server_space_to_alert_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_min_server_group_space_to_alert}} (*):</td>
			<td class="de_control">
				<input type="text" name="SERVER_GROUP_MIN_FREE_SPACE_MB" maxlength="1000" class="fixed_200" value="{{$data.SERVER_GROUP_MIN_FREE_SPACE_MB}}"/>
				{{$lang.settings.system_field_min_server_group_space_to_alert_mb}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_min_server_group_space_to_alert_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_memory_limit}} (*):</td>
			<td class="de_control">
				<input type="text" name="LIMIT_MEMORY" maxlength="1000" class="fixed_200" value="{{$data.LIMIT_MEMORY}}"/>
				{{$lang.settings.system_field_memory_limit_mb}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_memory_limit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_videos_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_videos_half_processing}}:</td>
			<td class="de_control">
				<input type="hidden" name="VIDEOS_HALF_PROCESSING" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="VIDEOS_HALF_PROCESSING" value="1" {{if $data.VIDEOS_HALF_PROCESSING==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_videos_half_processing_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_videos_half_processing_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_videos_initial_rating}}:</td>
			<td class="de_control">
				<select name="VIDEO_INITIAL_RATING">
					<option value="0" {{if $data.VIDEO_INITIAL_RATING==0}}selected="selected"{{/if}}>0&nbsp;&nbsp;</option>
					<option value="1" {{if $data.VIDEO_INITIAL_RATING==1}}selected="selected"{{/if}}>1&nbsp;&nbsp;</option>
					<option value="2" {{if $data.VIDEO_INITIAL_RATING==2}}selected="selected"{{/if}}>2&nbsp;&nbsp;</option>
					<option value="3" {{if $data.VIDEO_INITIAL_RATING==3}}selected="selected"{{/if}}>3&nbsp;&nbsp;</option>
					<option value="4" {{if $data.VIDEO_INITIAL_RATING==4}}selected="selected"{{/if}}>4&nbsp;&nbsp;</option>
					<option value="5" {{if $data.VIDEO_INITIAL_RATING==5}}selected="selected"{{/if}}>5&nbsp;&nbsp;</option>
					<option value="6" {{if $data.VIDEO_INITIAL_RATING==6}}selected="selected"{{/if}}>6&nbsp;&nbsp;</option>
					<option value="7" {{if $data.VIDEO_INITIAL_RATING==7}}selected="selected"{{/if}}>7&nbsp;&nbsp;</option>
					<option value="8" {{if $data.VIDEO_INITIAL_RATING==8}}selected="selected"{{/if}}>8&nbsp;&nbsp;</option>
					<option value="9" {{if $data.VIDEO_INITIAL_RATING==9}}selected="selected"{{/if}}>9&nbsp;&nbsp;</option>
					<option value="10" {{if $data.VIDEO_INITIAL_RATING==10}}selected="selected"{{/if}}>10&nbsp;&nbsp;</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_videos_initial_rating_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_videos_default_server_group}}:</td>
			<td class="de_control">
				<select name="DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO">
					<option value="auto" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO=='auto'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_default_server_group_auto}}</option>
					<option value="rand" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO=='rand'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_default_server_group_rand}}</option>
					{{foreach name=data item=item from=$list_server_groups_videos|smarty:nodefaults}}
						<option value="{{$item.group_id}}" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO==$item.group_id}}selected="selected"{{/if}}>{{$item.title}} ({{$item.free_space}})</option>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_videos_default_server_group_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="VIDEOS_DUPLICATE_TITLE_OPTION_0">{{$lang.settings.system_field_videos_duplicate_title}}:</div>
				<div class="VIDEOS_DUPLICATE_TITLE_OPTION_1 de_required">{{$lang.settings.system_field_videos_duplicate_title}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="VIDEOS_DUPLICATE_TITLE_OPTION" name="VIDEOS_DUPLICATE_TITLE_OPTION">
						<option value="0" {{if $data.VIDEOS_DUPLICATE_TITLE_OPTION=='0'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_duplicate_title_ignore}}</option>
						<option value="1" {{if $data.VIDEOS_DUPLICATE_TITLE_OPTION=='1'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_duplicate_title_postfix}}</option>
					</select>
					&nbsp;
					<input type="text" name="VIDEOS_DUPLICATE_TITLE_POSTFIX" class="VIDEOS_DUPLICATE_TITLE_OPTION_1 fixed_200" value="{{$data.VIDEOS_DUPLICATE_TITLE_POSTFIX}}" maxlength="1000"/>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.system_field_videos_duplicate_title_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_videos_duplicate_file}}:</td>
			<td class="de_control">
				<select name="VIDEOS_DUPLICATE_FILE_OPTION">
					<option value="0" {{if $data.VIDEOS_DUPLICATE_FILE_OPTION=='0'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_duplicate_file_ignore}}</option>
					<option value="1" {{if $data.VIDEOS_DUPLICATE_FILE_OPTION=='1'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_duplicate_file_ignore_if_deleted}}</option>
					<option value="2" {{if $data.VIDEOS_DUPLICATE_FILE_OPTION=='2'}}selected="selected"{{/if}}>{{$lang.settings.system_field_videos_duplicate_file_disallow}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_videos_duplicate_file_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_save_source_files}}:</td>
			<td class="de_control">
				<input type="hidden" name="KEEP_VIDEO_SOURCE_FILES" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="KEEP_VIDEO_SOURCE_FILES" value="1" {{if $data.KEEP_VIDEO_SOURCE_FILES==1}}checked="checked"{{/if}}/><span {{if $data.KEEP_VIDEO_SOURCE_FILES==1}}class="selected"{{/if}}>{{$lang.settings.system_field_save_source_files_yes}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_save_source_files_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_duration_from}}:</td>
			<td class="de_control">
				{{$lang.settings.system_field_duration_from_standard_videos}}:
				<select name="TAKE_VIDEO_DURATION_FROM_FORMAT_STD">
					<option value="" {{if $data.TAKE_VIDEO_DURATION_FROM_FORMAT_STD==''}}selected="selected"{{/if}}>{{$lang.settings.system_field_duration_from_source}}</option>
					{{foreach item=item from=$list_formats_videos_std|smarty:nodefaults}}
						{{if $item.status_id==1}}
							<option value="{{$item.postfix}}" {{if $data.TAKE_VIDEO_DURATION_FROM_FORMAT_STD==$item.postfix}}selected="selected"{{/if}}>{{$lang.settings.system_field_duration_from_format|replace:"%1%":$item.title}}</option>
						{{/if}}
					{{/foreach}}
				</select>
				&nbsp;
				{{$lang.settings.system_field_duration_from_premium_videos}}:
				<select name="TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM">
					<option value="" {{if $data.TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM==''}}selected="selected"{{/if}}>{{$lang.settings.system_field_duration_from_source}}</option>
					{{foreach item=item from=$list_formats_videos_premium|smarty:nodefaults}}
						{{if $item.status_id==1}}
							<option value="{{$item.postfix}}" {{if $data.TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM==$item.postfix}}selected="selected"{{/if}}>{{$lang.settings.system_field_duration_from_format|replace:"%1%":$item.title}}</option>
						{{/if}}
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_duration_from_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_videos_screenshots_settings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.system_divider_videos_screenshots_settings_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_screenshots_count}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_fixed" type="radio" name="SCREENSHOTS_COUNT_UNIT" value="1" {{if $data.SCREENSHOTS_COUNT_UNIT==1}}checked="checked"{{/if}}/><span>{{$lang.settings.system_field_screenshots_count_fixed}}</span></div>
								<input type="text" name="SCREENSHOTS_COUNT_FIXED" class="option_fixed" value="{{$data.SCREENSHOTS_COUNT_FIXED}}" maxlength="5"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_count_fixed_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_dynamic" type="radio" name="SCREENSHOTS_COUNT_UNIT" value="2" {{if $data.SCREENSHOTS_COUNT_UNIT==2}}checked="checked"{{/if}}/><span>{{$lang.settings.system_field_screenshots_count_dynamic}}</span></div>
								<input type="text" name="SCREENSHOTS_COUNT_DYNAMIC" class="option_dynamic" value="{{$data.SCREENSHOTS_COUNT_DYNAMIC}}" maxlength="5"/>
								{{$lang.settings.system_field_screenshots_count_dynamic_seconds}}
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_count_dynamic_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_screenshots_crop}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							{{$lang.settings.system_field_screenshots_crop_left}}:
							<input type="text" name="SCREENSHOTS_CROP_LEFT" maxlength="1000" class="fixed_50" value="{{$data.SCREENSHOTS_CROP_LEFT}}"/>
							<select name="SCREENSHOTS_CROP_LEFT_UNIT">
								<option value="1" {{if $data.SCREENSHOTS_CROP_LEFT_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $data.SCREENSHOTS_CROP_LEFT_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							&nbsp;
							{{$lang.settings.system_field_screenshots_crop_top}}:
							<input type="text" name="SCREENSHOTS_CROP_TOP" maxlength="1000" class="fixed_50" value="{{$data.SCREENSHOTS_CROP_TOP}}"/>
							<select name="SCREENSHOTS_CROP_TOP_UNIT">
								<option value="1" {{if $data.SCREENSHOTS_CROP_TOP_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $data.SCREENSHOTS_CROP_TOP_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							&nbsp;
							{{$lang.settings.system_field_screenshots_crop_right}}:
							<input type="text" name="SCREENSHOTS_CROP_RIGHT" maxlength="1000" class="fixed_50" value="{{$data.SCREENSHOTS_CROP_RIGHT}}"/>
							<select name="SCREENSHOTS_CROP_RIGHT_UNIT">
								<option value="1" {{if $data.SCREENSHOTS_CROP_RIGHT_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $data.SCREENSHOTS_CROP_RIGHT_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							&nbsp;
							{{$lang.settings.system_field_screenshots_crop_bottom}}:
							<input type="text" name="SCREENSHOTS_CROP_BOTTOM" maxlength="1000" class="fixed_50" value="{{$data.SCREENSHOTS_CROP_BOTTOM}}"/>
							<select name="SCREENSHOTS_CROP_BOTTOM_UNIT">
								<option value="1" {{if $data.SCREENSHOTS_CROP_BOTTOM_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $data.SCREENSHOTS_CROP_BOTTOM_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_crop_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="SCREENSHOTS_CROP_TRIM_SIDES" value="0"/>
							<div class="de_lv_pair"><input type="checkbox" name="SCREENSHOTS_CROP_TRIM_SIDES" value="1" {{if $data.SCREENSHOTS_CROP_TRIM_SIDES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_screenshots_crop_trim_sides}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_crop_trim_sides_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_screenshots_crop_customize}}:</td>
			<td class="de_control">
				<select name="SCREENSHOTS_CROP_CUSTOMIZE">
					<option value="0" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_no}}</option>
					<option value="1" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
					<option value="2" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==2}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
					<option value="3" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==3}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
					<option value="4" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==4}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
					<option value="5" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==5}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
					<option value="6" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==6}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
					<option value="7" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==7}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
					<option value="8" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==8}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
					<option value="9" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==9}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
					<option value="10" {{if $data.SCREENSHOTS_CROP_CUSTOMIZE==10}}selected="selected"{{/if}}>{{$lang.settings.system_field_screenshots_crop_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_crop_customize_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_screenshots_uploaded}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<input type="hidden" name="SCREENSHOTS_UPLOADED_CROP" value="0"/>
							<div class="de_lv_pair"><input type="checkbox" name="SCREENSHOTS_UPLOADED_CROP" value="1" {{if $data.SCREENSHOTS_UPLOADED_CROP==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_screenshots_uploaded_crop}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_uploaded_crop_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="SCREENSHOTS_UPLOADED_WATERMARK" value="0"/>
							<div class="de_lv_pair"><input type="checkbox" name="SCREENSHOTS_UPLOADED_WATERMARK" value="1" {{if $data.SCREENSHOTS_UPLOADED_WATERMARK==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_screenshots_uploaded_watermark}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_uploaded_watermark_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_screenshots_seconds_offset}} (*):</td>
			<td class="de_control">
				<input type="text" name="SCREENSHOTS_SECONDS_OFFSET" maxlength="1000" class="dyn_full_size" value="{{$data.SCREENSHOTS_SECONDS_OFFSET}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_seconds_offset_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_screenshots_seconds_offset_end}} (*):</td>
			<td class="de_control">
				<input type="text" name="SCREENSHOTS_SECONDS_OFFSET_END" maxlength="1000" class="dyn_full_size" value="{{$data.SCREENSHOTS_SECONDS_OFFSET_END}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_seconds_offset_end_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.system_field_screenshots_main_number}} (*):</td>
			<td class="de_control">
				<input type="text" name="SCREENSHOTS_MAIN_NUMBER" maxlength="1000" class="dyn_full_size" value="{{$data.SCREENSHOTS_MAIN_NUMBER}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_screenshots_main_number_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_video_file_protection_settings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/39-how-to-protect-your-videos-from-being-downloaded-or-parsed">How to protect your videos from being downloaded or parsed</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.system_field_antihotlink_enable}}:</td>
			<td class="de_control">
				<input type="hidden" name="ENABLE_ANTI_HOTLINK" value="0"/>
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="anti_hotlink" name="ENABLE_ANTI_HOTLINK" value="1" {{if $data.ENABLE_ANTI_HOTLINK==1}}checked="checked"{{/if}}/><span {{if $data.ENABLE_ANTI_HOTLINK==1}}class="selected"{{/if}}>{{$lang.settings.system_field_antihotlink_enable_enabled}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="anti_hotlink_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="anti_hotlink_type" name="ANTI_HOTLINK_TYPE">
						<option value="0" {{if $data.ANTI_HOTLINK_TYPE==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_antihotlink_type_referer}}</option>
						<option value="1" {{if $data.ANTI_HOTLINK_TYPE==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_antihotlink_type_ip}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_type_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="anti_hotlink_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_formats_disabled}}:</td>
			<td class="de_control">
				{{assign var="has_video_formats_where_protection_disabled" value="false"}}
				{{foreach item="item" from=$list_formats_videos_std|smarty:nodefaults}}
					{{if $item.is_hotlink_protection_disabled==1}}
						{{if $has_video_formats_where_protection_disabled=="true"}},{{/if}}
						{{if in_array('system|formats',$smarty.session.permissions)}}
							<a href="{{if $config.installation_type>=2}}formats_videos.php?action=change&amp;item_id={{$item.format_video_id}}{{else}}formats_videos_basic.php{{/if}}">{{$item.title}}</a>
						{{else}}
							{{$item.title}}
						{{/if}}
						{{assign var="has_video_formats_where_protection_disabled" value="true"}}
					{{/if}}
				{{/foreach}}
				{{foreach item="item" from=$list_formats_videos_premium|smarty:nodefaults}}
					{{if $item.is_hotlink_protection_disabled==1}}
						{{if $has_video_formats_where_protection_disabled=="true"}},{{/if}}
						{{if in_array('system|formats',$smarty.session.permissions)}}
							<a href="{{if $config.installation_type>=2}}formats_videos.php?action=change&amp;item_id={{$item.format_video_id}}{{else}}formats_videos_basic.php{{/if}}">{{$item.title}}</a>
						{{else}}
							{{$item.title}}
						{{/if}}
						{{assign var="has_video_formats_where_protection_disabled" value="true"}}
					{{/if}}
				{{/foreach}}
				{{if $has_video_formats_where_protection_disabled=="false"}}
					{{$lang.settings.system_field_antihotlink_formats_disabled_none}}
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_formats_disabled_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="anti_hotlink_type_0 anti_hotlink_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_white_domains}}:</td>
			<td class="de_control">
				<input type="text" name="ANTI_HOTLINK_WHITE_DOMAINS" class="dyn_full_size" maxlength="1000" value="{{$data.ANTI_HOTLINK_WHITE_DOMAINS}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_white_domains_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if !$config.project_url|strpos:"/":10}}
			<tr class="anti_hotlink_on">
				<td class="de_label">{{$lang.settings.system_field_antihotlink_encode_links}}:</td>
				<td class="de_control">
					<input type="hidden" name="ANTI_HOTLINK_ENCODE_LINKS" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="ANTI_HOTLINK_ENCODE_LINKS" value="1" {{if $data.ANTI_HOTLINK_ENCODE_LINKS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_antihotlink_encode_links_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_encode_links_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="anti_hotlink_on">
			<td class="de_label">
				<div class="antihotlink_ip_off">{{$lang.settings.system_field_antihotlink_limitation}}:</div>
				<div class="de_required antihotlink_ip_on">{{$lang.settings.system_field_antihotlink_limitation}} (*):</div>
			</td>
			<td class="de_control">
				<input type="hidden" name="ANTI_HOTLINK_ENABLE_IP_LIMIT" value="0"/>
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="antihotlink_ip" name="ANTI_HOTLINK_ENABLE_IP_LIMIT" value="1" {{if $data.ANTI_HOTLINK_ENABLE_IP_LIMIT==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_antihotlink_limitation_enabled}}</label></div>
				<input type="text" name="ANTI_HOTLINK_N_VIDEOS" maxlength="1000" class="fixed_100 antihotlink_ip_on" value="{{$data.ANTI_HOTLINK_N_VIDEOS}}"/>
				{{$lang.settings.system_field_antihotlink_limitation_videos}}
				<input type="text" name="ANTI_HOTLINK_N_HOURS" maxlength="1000" class="fixed_100 antihotlink_ip_on" value="{{$data.ANTI_HOTLINK_N_HOURS}}"/>
				{{$lang.settings.system_field_antihotlink_limitation_minutes}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_limitation_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="anti_hotlink_on antihotlink_ip_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_own_ip}}:</td>
			<td class="de_control">
				{{$data.ANTI_HOTLINK_OWN_IP}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_own_ip_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="anti_hotlink_on antihotlink_ip_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_blocked_ips}}:</td>
			<td class="de_control">
				{{$data.BLOCKED_IPS|default:$lang.common.undefined}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_blocked_ips_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="anti_hotlink_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_white_ips}}:</td>
			<td class="de_control">
				<input type="text" name="ANTI_HOTLINK_WHITE_IPS" class="dyn_full_size" maxlength="1000" value="{{$data.ANTI_HOTLINK_WHITE_IPS}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_white_ips_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="anti_hotlink_on">
			<td class="de_label">{{$lang.settings.system_field_antihotlink_custom_file}}:</td>
			<td class="de_control">
				<input type="text" name="ANTI_HOTLINK_FILE" class="dyn_full_size" maxlength="1000" value="{{$data.ANTI_HOTLINK_FILE}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_antihotlink_custom_file_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.disable_rotator!='true'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_rotator_settings}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_rotator_videos_enable}}:</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<input type="hidden" name="ROTATOR_VIDEOS_ENABLE" value="0"/>
								<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="rotator_videos" name="ROTATOR_VIDEOS_ENABLE" value="1" {{if $data.ROTATOR_VIDEOS_ENABLE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_rotator_videos_enable_enabled}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.system_field_rotator_videos_enable_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr class="rotator_videos_on">
							<td>
								<input type="hidden" name="ROTATOR_VIDEOS_CATEGORIES_ENABLE" value="0"/>
								<div class="de_lv_pair"><input type="checkbox" name="ROTATOR_VIDEOS_CATEGORIES_ENABLE" class="rotator_videos_on" value="1" {{if $data.ROTATOR_VIDEOS_CATEGORIES_ENABLE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_rotator_videos_enable_categories}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.system_field_rotator_videos_enable_categories_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr class="rotator_videos_on">
							<td>
								<input type="hidden" name="ROTATOR_VIDEOS_TAGS_ENABLE" value="0"/>
								<div class="de_lv_pair"><input type="checkbox" name="ROTATOR_VIDEOS_TAGS_ENABLE" class="rotator_videos_on" value="1" {{if $data.ROTATOR_VIDEOS_TAGS_ENABLE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_rotator_videos_enable_tags}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.system_field_rotator_videos_enable_tags_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr class="rotator_videos_on">
				<td class="de_label">{{$lang.settings.system_field_rotator_screenshots_enable}}:</td>
				<td class="de_control">
					<input type="hidden" name="ROTATOR_SCREENSHOTS_ENABLE" value="0"/>
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="rotator_screenshots" name="ROTATOR_SCREENSHOTS_ENABLE" value="1" {{if $data.ROTATOR_SCREENSHOTS_ENABLE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_rotator_screenshots_enable_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_enable_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="rotator_videos_on rotator_screenshots_on">
				<td class="de_label de_dependent">{{$lang.settings.system_field_rotator_screenshots_only_one_enable}}:</td>
				<td class="de_control">
					<input type="hidden" name="ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE" value="1" {{if $data.ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_rotator_screenshots_only_one_enable_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_only_one_enable_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="rotator_videos_on rotator_screenshots_on">
				<td class="de_label de_dependent de_required">{{$lang.settings.system_field_rotator_screenshots_min_shows}} (*):</td>
				<td class="de_control">
					<input type="text" name="ROTATOR_SCREENSHOTS_MIN_SHOWS" maxlength="1000" class="dyn_full_size" value="{{$data.ROTATOR_SCREENSHOTS_MIN_SHOWS}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_min_shows_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="rotator_videos_on rotator_screenshots_on">
				<td class="de_label de_dependent de_required">{{$lang.settings.system_field_rotator_screenshots_min_clicks}} (*):</td>
				<td class="de_control">
					<input type="text" name="ROTATOR_SCREENSHOTS_MIN_CLICKS" maxlength="1000" class="dyn_full_size" value="{{$data.ROTATOR_SCREENSHOTS_MIN_CLICKS}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_rotator_screenshots_min_clicks_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="rotator_videos_on rotator_screenshots_on">
				<td class="de_label de_dependent">
					<div class="de_required delete_screenshots_1">{{$lang.settings.system_field_rotator_screenshots_delete}} (*):</div>
					<div class="delete_screenshots_0">{{$lang.settings.system_field_rotator_screenshots_delete}}:</div>
				</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="delete_screenshots" name="ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT_OPTION">
							<option value="0" {{if $data.ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_rotator_screenshots_delete_no}}</option>
							<option value="1" {{if $data.ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT!=0}}selected="selected"{{/if}}>{{$lang.settings.system_field_rotator_screenshots_delete_yes}}</option>
						</select>
						&nbsp;
						<input type="text" name="ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT" class="fixed_50 delete_screenshots_1" value="{{$data.ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT}}"/>
					</div>
				</td>
			</tr>
			{{if count($rotator_completeness)>0}}
				<tr class="rotator_videos_on rotator_screenshots_on">
					<td class="de_label de_dependent">{{$lang.settings.system_field_rotator_screenshots_completeness}}:</td>
					<td class="de_control">
						{{foreach name=data item=item from=$rotator_completeness|smarty:nodefaults}}
							{{assign var="width" value=`$item.value*300`}}
							<div style="height: 12px; width: 80px; font-size: 10px; float: left">
								{{if $smarty.foreach.data.iteration==1}}
									0% - 20%
								{{elseif $smarty.foreach.data.iteration==2}}
									21% - 40%
								{{elseif $smarty.foreach.data.iteration==3}}
									41% - 60%
								{{elseif $smarty.foreach.data.iteration==4}}
									61% - 80%
								{{elseif $smarty.foreach.data.iteration==5}}
									81% - 100%
								{{elseif $smarty.foreach.data.iteration==6}}
									{{if $item.amount>0}}
										<a href="videos.php?no_filter=true&amp;se_show_id=23">100%</a>
									{{else}}
										100%
									{{/if}}
								{{/if}}
							</div>
							<div style="height: 10px; width: {{if $width<1}}1{{else}}{{$width|string_format:"%d"}}{{/if}}px; background: #aeaeae; float: left; margin: 1px 0"></div>
							<div style="height: 12px; padding-left: 5px; font-size: 10px; float: left">{{$item.percent}}% ({{$item.shows}}, {{$item.clicks}})</div>
							<div style="clear: both"></div>
						{{/foreach}}
					</td>
				</tr>
			{{/if}}
			<tr class="rotator_videos_on">
				<td class="de_label de_required">{{$lang.settings.system_field_rotator_schedule}} (*):</td>
				<td class="de_control">
					<input type="text" name="ROTATOR_SCHEDULE_INTERVAL" value="{{$data.ROTATOR_SCHEDULE_INTERVAL}}" size="4"/>
					&nbsp;{{$lang.settings.system_field_rotator_schedule_pause_from}}&nbsp;
					<input type="text" name="ROTATOR_SCHEDULE_PAUSE_FROM" value="{{$data.ROTATOR_SCHEDULE_PAUSE_FROM}}" size="5"/>
					&nbsp;{{$lang.settings.system_field_rotator_schedule_pause_to}}&nbsp;
					<input type="text" name="ROTATOR_SCHEDULE_PAUSE_TO" value="{{$data.ROTATOR_SCHEDULE_PAUSE_TO}}" size="5"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_rotator_schedule_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_albums_settings}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_albums_initial_rating}}:</td>
				<td class="de_control">
					<select name="ALBUM_INITIAL_RATING">
						<option value="0" {{if $data.ALBUM_INITIAL_RATING==0}}selected="selected"{{/if}}>0&nbsp;&nbsp;</option>
						<option value="1" {{if $data.ALBUM_INITIAL_RATING==1}}selected="selected"{{/if}}>1&nbsp;&nbsp;</option>
						<option value="2" {{if $data.ALBUM_INITIAL_RATING==2}}selected="selected"{{/if}}>2&nbsp;&nbsp;</option>
						<option value="3" {{if $data.ALBUM_INITIAL_RATING==3}}selected="selected"{{/if}}>3&nbsp;&nbsp;</option>
						<option value="4" {{if $data.ALBUM_INITIAL_RATING==4}}selected="selected"{{/if}}>4&nbsp;&nbsp;</option>
						<option value="5" {{if $data.ALBUM_INITIAL_RATING==5}}selected="selected"{{/if}}>5&nbsp;&nbsp;</option>
						<option value="6" {{if $data.ALBUM_INITIAL_RATING==6}}selected="selected"{{/if}}>6&nbsp;&nbsp;</option>
						<option value="7" {{if $data.ALBUM_INITIAL_RATING==7}}selected="selected"{{/if}}>7&nbsp;&nbsp;</option>
						<option value="8" {{if $data.ALBUM_INITIAL_RATING==8}}selected="selected"{{/if}}>8&nbsp;&nbsp;</option>
						<option value="9" {{if $data.ALBUM_INITIAL_RATING==9}}selected="selected"{{/if}}>9&nbsp;&nbsp;</option>
						<option value="10" {{if $data.ALBUM_INITIAL_RATING==10}}selected="selected"{{/if}}>10&nbsp;&nbsp;</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_albums_initial_rating_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_albums_default_server_group}}:</td>
				<td class="de_control">
					<select name="DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM">
						<option value="auto" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM=='auto'}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_default_server_group_auto}}</option>
						<option value="rand" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM=='rand'}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_default_server_group_rand}}</option>
						{{foreach name=data item=item from=$list_server_groups_albums|smarty:nodefaults}}
							<option value="{{$item.group_id}}" {{if $data.DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM==$item.group_id}}selected="selected"{{/if}}>{{$item.title}} ({{$item.free_space}})</option>
						{{/foreach}}
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_albums_default_server_group_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="ALBUMS_DUPLICATE_TITLE_OPTION_0">{{$lang.settings.system_field_albums_duplicate_title}}:</div>
					<div class="ALBUMS_DUPLICATE_TITLE_OPTION_1 de_required">{{$lang.settings.system_field_albums_duplicate_title}} (*):</div>
				</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="ALBUMS_DUPLICATE_TITLE_OPTION" name="ALBUMS_DUPLICATE_TITLE_OPTION">
							<option value="0" {{if $data.ALBUMS_DUPLICATE_TITLE_OPTION=='0'}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_duplicate_title_ignore}}</option>
							<option value="1" {{if $data.ALBUMS_DUPLICATE_TITLE_OPTION=='1'}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_duplicate_title_postfix}}</option>
						</select>
						&nbsp;
						<input type="text" name="ALBUMS_DUPLICATE_TITLE_POSTFIX" class="ALBUMS_DUPLICATE_TITLE_OPTION_1 fixed_200" value="{{$data.ALBUMS_DUPLICATE_TITLE_POSTFIX}}" maxlength="1000"/>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.settings.system_field_albums_duplicate_title_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_albums_crop}}:</td>
				<td class="de_control">
					{{$lang.settings.system_field_albums_crop_left}}:
					<input type="text" name="ALBUMS_CROP_LEFT" maxlength="1000" class="fixed_50" value="{{$data.ALBUMS_CROP_LEFT}}"/>
					<select name="ALBUMS_CROP_LEFT_UNIT">
						<option value="1" {{if $data.ALBUMS_CROP_LEFT_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
						<option value="2" {{if $data.ALBUMS_CROP_LEFT_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
					</select>
					&nbsp;
					{{$lang.settings.system_field_albums_crop_top}}:
					<input type="text" name="ALBUMS_CROP_TOP" maxlength="1000" class="fixed_50" value="{{$data.ALBUMS_CROP_TOP}}"/>
					<select name="ALBUMS_CROP_TOP_UNIT">
						<option value="1" {{if $data.ALBUMS_CROP_TOP_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
						<option value="2" {{if $data.ALBUMS_CROP_TOP_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
					</select>
					&nbsp;
					{{$lang.settings.system_field_albums_crop_right}}:
					<input type="text" name="ALBUMS_CROP_RIGHT" maxlength="1000" class="fixed_50" value="{{$data.ALBUMS_CROP_RIGHT}}"/>
					<select name="ALBUMS_CROP_RIGHT_UNIT">
						<option value="1" {{if $data.ALBUMS_CROP_RIGHT_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
						<option value="2" {{if $data.ALBUMS_CROP_RIGHT_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
					</select>
					&nbsp;
					{{$lang.settings.system_field_albums_crop_bottom}}:
					<input type="text" name="ALBUMS_CROP_BOTTOM" maxlength="1000" class="fixed_50" value="{{$data.ALBUMS_CROP_BOTTOM}}"/>
					<select name="ALBUMS_CROP_BOTTOM_UNIT">
						<option value="1" {{if $data.ALBUMS_CROP_BOTTOM_UNIT==1}}selected="selected"{{/if}}>px&nbsp;</option>
						<option value="2" {{if $data.ALBUMS_CROP_BOTTOM_UNIT==2}}selected="selected"{{/if}}>%&nbsp;</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_albums_crop_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_albums_crop_customize}}:</td>
				<td class="de_control">
					<select name="ALBUMS_CROP_CUSTOMIZE">
						<option value="0" {{if $data.ALBUMS_CROP_CUSTOMIZE==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_no}}</option>
						<option value="1" {{if $data.ALBUMS_CROP_CUSTOMIZE==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
						<option value="2" {{if $data.ALBUMS_CROP_CUSTOMIZE==2}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
						<option value="3" {{if $data.ALBUMS_CROP_CUSTOMIZE==3}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
						<option value="4" {{if $data.ALBUMS_CROP_CUSTOMIZE==4}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
						<option value="5" {{if $data.ALBUMS_CROP_CUSTOMIZE==5}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
						<option value="6" {{if $data.ALBUMS_CROP_CUSTOMIZE==6}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
						<option value="7" {{if $data.ALBUMS_CROP_CUSTOMIZE==7}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
						<option value="8" {{if $data.ALBUMS_CROP_CUSTOMIZE==8}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
						<option value="9" {{if $data.ALBUMS_CROP_CUSTOMIZE==9}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
						<option value="10" {{if $data.ALBUMS_CROP_CUSTOMIZE==10}}selected="selected"{{/if}}>{{$lang.settings.system_field_albums_crop_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_albums_crop_customize_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_video_edit_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_add_video_default_user}}:</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
					</div>
					<input type="text" name="DEFAULT_USER_IN_ADMIN_ADD_VIDEO" maxlength="1000" class="dyn_full_size" value="{{$data.DEFAULT_USER_IN_ADMIN_ADD_VIDEO}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_add_video_default_user_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_add_video_default_status}}:</td>
			<td class="de_control">
				<select name="DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO">
					<option value="1" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_video_default_status_active}}</option>
					<option value="0" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_VIDEO!=1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_video_default_status_disabled}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_add_video_post_date_time}}:</td>
			<td class="de_control">
				<select name="USE_POST_DATE_RANDOMIZATION">
					<option value="0" {{if $data.USE_POST_DATE_RANDOMIZATION==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_video_post_date_time_none}}</option>
					<option value="1" {{if $data.USE_POST_DATE_RANDOMIZATION==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_video_post_date_time_random}}</option>
					<option value="2" {{if $data.USE_POST_DATE_RANDOMIZATION==2}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_video_post_date_time_current}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_add_video_post_date_time_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_edit_video_directory_autogeneration}}:</td>
			<td class="de_control">
				<input type="hidden" name="VIDEO_REGENERATE_DIRECTORIES" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="VIDEO_REGENERATE_DIRECTORIES" value="1" {{if $data.VIDEO_REGENERATE_DIRECTORIES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_video_directory_autogeneration_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_edit_video_directory_autogeneration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_edit_video_check_duplicate_titles}}:</td>
			<td class="de_control">
				<input type="hidden" name="VIDEO_CHECK_DUPLICATE_TITLES" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="VIDEO_CHECK_DUPLICATE_TITLES" value="1" {{if $data.VIDEO_CHECK_DUPLICATE_TITLES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_video_check_duplicate_titles_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_edit_video_check_duplicate_titles_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_edit_video_screenshot_size_validation}}:</td>
			<td class="de_control">
				<input type="hidden" name="VIDEO_VALIDATE_SCREENSHOT_SIZES" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" name="VIDEO_VALIDATE_SCREENSHOT_SIZES" value="1" {{if $data.VIDEO_VALIDATE_SCREENSHOT_SIZES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_video_screenshot_size_validation_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_edit_video_screenshot_size_validation_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_album_edit_settings}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_album_default_user}}:</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight_users.php</span>
						</div>
						<input type="text" name="DEFAULT_USER_IN_ADMIN_ADD_ALBUM" maxlength="1000" class="dyn_full_size" value="{{$data.DEFAULT_USER_IN_ADMIN_ADD_ALBUM}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.system_field_add_album_default_user_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_album_default_status}}:</td>
				<td class="de_control">
					<select name="DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM">
						<option value="1" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_album_default_status_active}}</option>
						<option value="0" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM!=1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_album_default_status_disabled}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_album_post_date_time}}:</td>
				<td class="de_control">
					<select name="USE_POST_DATE_RANDOMIZATION_ALBUM">
						<option value="0" {{if $data.USE_POST_DATE_RANDOMIZATION_ALBUM==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_album_post_date_time_none}}</option>
						<option value="1" {{if $data.USE_POST_DATE_RANDOMIZATION_ALBUM==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_album_post_date_time_random}}</option>
						<option value="2" {{if $data.USE_POST_DATE_RANDOMIZATION_ALBUM==2}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_album_post_date_time_current}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_add_album_post_date_time_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_album_directory_autogeneration}}:</td>
				<td class="de_control">
					<input type="hidden" name="ALBUM_REGENERATE_DIRECTORIES" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="ALBUM_REGENERATE_DIRECTORIES" value="1" {{if $data.ALBUM_REGENERATE_DIRECTORIES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_album_directory_autogeneration_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_edit_album_directory_autogeneration_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_album_check_duplicate_titles}}:</td>
				<td class="de_control">
					<input type="hidden" name="ALBUM_CHECK_DUPLICATE_TITLES" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="ALBUM_CHECK_DUPLICATE_TITLES" value="1" {{if $data.ALBUM_CHECK_DUPLICATE_TITLES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_album_check_duplicate_titles_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_edit_album_check_duplicate_titles_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_album_image_size_validation}}:</td>
				<td class="de_control">
					<input type="hidden" name="ALBUM_VALIDATE_IMAGE_SIZES" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="ALBUM_VALIDATE_IMAGE_SIZES" value="1" {{if $data.ALBUM_VALIDATE_IMAGE_SIZES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_album_image_size_validation_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_edit_album_image_size_validation_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type>=3}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_post_edit_settings}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_posts_initial_rating}}:</td>
				<td class="de_control">
					<select name="POST_INITIAL_RATING">
						<option value="0" {{if $data.POST_INITIAL_RATING==0}}selected="selected"{{/if}}>0&nbsp;&nbsp;</option>
						<option value="1" {{if $data.POST_INITIAL_RATING==1}}selected="selected"{{/if}}>1&nbsp;&nbsp;</option>
						<option value="2" {{if $data.POST_INITIAL_RATING==2}}selected="selected"{{/if}}>2&nbsp;&nbsp;</option>
						<option value="3" {{if $data.POST_INITIAL_RATING==3}}selected="selected"{{/if}}>3&nbsp;&nbsp;</option>
						<option value="4" {{if $data.POST_INITIAL_RATING==4}}selected="selected"{{/if}}>4&nbsp;&nbsp;</option>
						<option value="5" {{if $data.POST_INITIAL_RATING==5}}selected="selected"{{/if}}>5&nbsp;&nbsp;</option>
						<option value="6" {{if $data.POST_INITIAL_RATING==6}}selected="selected"{{/if}}>6&nbsp;&nbsp;</option>
						<option value="7" {{if $data.POST_INITIAL_RATING==7}}selected="selected"{{/if}}>7&nbsp;&nbsp;</option>
						<option value="8" {{if $data.POST_INITIAL_RATING==8}}selected="selected"{{/if}}>8&nbsp;&nbsp;</option>
						<option value="9" {{if $data.POST_INITIAL_RATING==9}}selected="selected"{{/if}}>9&nbsp;&nbsp;</option>
						<option value="10" {{if $data.POST_INITIAL_RATING==10}}selected="selected"{{/if}}>10&nbsp;&nbsp;</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_posts_initial_rating_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_post_default_user}}:</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight_users.php</span>
						</div>
						<input type="text" name="DEFAULT_USER_IN_ADMIN_ADD_POST" maxlength="1000" class="dyn_full_size" value="{{$data.DEFAULT_USER_IN_ADMIN_ADD_POST}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.system_field_add_post_default_user_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_post_default_status}}:</td>
				<td class="de_control">
					<select name="DEFAULT_STATUS_IN_ADMIN_ADD_POST">
						<option value="1" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_POST==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_post_default_status_active}}</option>
						<option value="0" {{if $data.DEFAULT_STATUS_IN_ADMIN_ADD_POST!=1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_post_default_status_disabled}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_add_post_post_date_time}}:</td>
				<td class="de_control">
					<select name="USE_POST_DATE_RANDOMIZATION_POST">
						<option value="0" {{if $data.USE_POST_DATE_RANDOMIZATION_POST==0}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_post_post_date_time_none}}</option>
						<option value="1" {{if $data.USE_POST_DATE_RANDOMIZATION_POST==1}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_post_post_date_time_random}}</option>
						<option value="2" {{if $data.USE_POST_DATE_RANDOMIZATION_POST==2}}selected="selected"{{/if}}>{{$lang.settings.system_field_add_post_post_date_time_current}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_add_post_post_date_time_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_post_directory_autogeneration}}:</td>
				<td class="de_control">
					<input type="hidden" name="POST_REGENERATE_DIRECTORIES" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="POST_REGENERATE_DIRECTORIES" value="1" {{if $data.POST_REGENERATE_DIRECTORIES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_post_directory_autogeneration_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_edit_post_directory_autogeneration_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.system_field_edit_post_check_duplicate_titles}}:</td>
				<td class="de_control">
					<input type="hidden" name="POST_CHECK_DUPLICATE_TITLES" value="0"/>
					<div class="de_lv_pair"><input type="checkbox" name="POST_CHECK_DUPLICATE_TITLES" value="1" {{if $data.POST_CHECK_DUPLICATE_TITLES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_edit_post_check_duplicate_titles_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.system_field_edit_post_check_duplicate_titles_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.system_divider_api_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_api_enable}}:</td>
			<td class="de_control">
				<input type="hidden" name="API_ENABLE" value="0"/>
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="api_enable" name="API_ENABLE" value="1" {{if $data.API_ENABLE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.system_field_api_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_api_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="api_enable_on de_required">{{$lang.settings.system_field_api_password}} (*):</div>
				<div class="api_enable_off">{{$lang.settings.system_field_api_password}}:</div>
			</td>
			<td class="de_control">
				<input type="text" name="API_PASSWORD" maxlength="1000" class="dyn_full_size api_enable_on" value="{{$data.API_PASSWORD}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.system_field_api_password_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.system_field_api_url}}:</td>
			<td class="de_control">
				{{$config.project_url}}/admin/api/{{$config.billing_scripts_name}}.php
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input id="system_settings_submit" type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildContentSettingsConfirmLogic=call</span>
</div>

{{elseif $smarty.request.page=='website_settings'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_website_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.website_header}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.website_divider_general_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_disable_website}}:</td>
			<td class="de_control">
				<input type="hidden" name="DISABLE_WEBSITE" value="0"/>
				<div class="de_lv_pair"><input type="checkbox" id="disable_website" name="DISABLE_WEBSITE" value="1" {{if $data.DISABLE_WEBSITE==1}}checked="checked"{{/if}}/><label>{{$lang.settings.website_field_disable_website_disabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_disable_website_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_website_caching}}:</td>
			<td class="de_control">
				<select id="website_caching" name="WEBSITE_CACHING">
					<option value="">{{$lang.settings.website_field_website_caching_full}}</option>
					<option value="1" {{if $data.WEBSITE_CACHING=='1'}}selected="selected"{{/if}}>{{$lang.settings.website_field_website_caching_file}}</option>
					<option value="2" {{if $data.WEBSITE_CACHING=='2'}}selected="selected"{{/if}}>{{$lang.settings.website_field_website_caching_disabled}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_website_caching_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_dynamic_params}}:</td>
			<td class="de_control">
				<table>
					<tr>
						<td class="nowrap">{{$lang.settings.website_field_dynamic_params_names}}:&nbsp;</td>
						<td>
							<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" class="fixed_100" value="{{$data.DYNAMIC_PARAMS.0}}"/>
							<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" class="fixed_100" value="{{$data.DYNAMIC_PARAMS.1}}"/>
							<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" class="fixed_100" value="{{$data.DYNAMIC_PARAMS.2}}"/>
							<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" class="fixed_100" value="{{$data.DYNAMIC_PARAMS.3}}"/>
							<input type="text" name="DYNAMIC_PARAMS[]" maxlength="25" class="fixed_100" value="{{$data.DYNAMIC_PARAMS.4}}"/>
						</td>
					</tr>
					<tr>
						<td class="nowrap">{{$lang.settings.website_field_dynamic_params_default_values}}:&nbsp;</td>
						<td>
							<input type="text" name="DYNAMIC_PARAMS_VALUES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_VALUES.0}}"/>
							<input type="text" name="DYNAMIC_PARAMS_VALUES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_VALUES.1}}"/>
							<input type="text" name="DYNAMIC_PARAMS_VALUES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_VALUES.2}}"/>
							<input type="text" name="DYNAMIC_PARAMS_VALUES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_VALUES.3}}"/>
							<input type="text" name="DYNAMIC_PARAMS_VALUES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_VALUES.4}}"/>
						</td>
					</tr>
					<tr>
						<td class="nowrap">{{$lang.settings.website_field_dynamic_params_lifetimes}}:&nbsp;</td>
						<td>
							<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.0|default:"360"}}"/>
							<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.1|default:"360"}}"/>
							<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.2|default:"360"}}"/>
							<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.3|default:"360"}}"/>
							<input type="text" name="DYNAMIC_PARAMS_LIFETIMES[]" class="fixed_100" value="{{$data.DYNAMIC_PARAMS_LIFETIMES.4|default:"360"}}"/>
						</td>
					</tr>
				</table>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.website_field_dynamic_params_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_disabled_content_availability}}:</td>
			<td class="de_control">
				<select name="DISABLED_CONTENT_AVAILABILITY">
					<option value="2" {{if $data.DISABLED_CONTENT_AVAILABILITY=='2'}}selected="selected"{{/if}}>{{$lang.settings.website_field_disabled_content_availability_full}}</option>
					<option value="0" {{if $data.DISABLED_CONTENT_AVAILABILITY=='0'}}selected="selected"{{/if}}>{{$lang.settings.website_field_disabled_content_availability_yes}}</option>
					<option value="1" {{if $data.DISABLED_CONTENT_AVAILABILITY=='1'}}selected="selected"{{/if}}>{{$lang.settings.website_field_disabled_content_availability_no}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_disabled_content_availability_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.website_divider_url_patterns}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.website_field_video_website_link_pattern}} (*):</td>
			<td class="de_control">
				<input type="text" name="WEBSITE_LINK_PATTERN" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_video_website_link_pattern_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_label de_required">{{$lang.settings.website_field_album_website_link_pattern}} (*):</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_ALBUM" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_ALBUM}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_album_website_link_pattern_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.website_field_album_image_website_link_pattern}} (*):</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_IMAGE" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_IMAGE}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_album_image_website_link_pattern_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_label">{{$lang.settings.website_field_playlist_website_link_pattern}}:</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_PLAYLIST" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_PLAYLIST}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_playlist_website_link_pattern_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_model_website_link_pattern}}:</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_MODEL" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_MODEL}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_model_website_link_pattern_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.website_field_content_source_website_link_pattern}}:</td>
			<td class="de_control">
				<input type="text" name="WEBSITE_LINK_PATTERN_CS" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_CS}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_content_source_website_link_pattern_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_label">{{$lang.settings.website_field_dvd_website_link_pattern}}:</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_DVD" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_DVD}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_dvd_website_link_pattern_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_dvd_group_website_link_pattern}}:</td>
				<td class="de_control">
					<input type="text" name="WEBSITE_LINK_PATTERN_DVD_GROUP" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_DVD_GROUP}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_dvd_group_website_link_pattern_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.website_field_search_website_link_pattern}}:</td>
			<td class="de_control">
				<input type="text" name="WEBSITE_LINK_PATTERN_SEARCH" maxlength="1000" class="dyn_full_size" value="{{$data.WEBSITE_LINK_PATTERN_SEARCH}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_search_website_link_pattern_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.website_divider_optimization}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.website_field_user_online_status_refresh}}:</td>
				<td class="de_control">
					<input type="hidden" name="ENABLE_USER_ONLINE_STATUS_REFRESH" value="0"/>
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="user_online_status_refresh" name="ENABLE_USER_ONLINE_STATUS_REFRESH" value="1" {{if $data.ENABLE_USER_ONLINE_STATUS_REFRESH==1}}checked="checked"{{/if}}/><span {{if $data.ENABLE_USER_ONLINE_STATUS_REFRESH==1}}class="selected"{{/if}}>{{$lang.settings.website_field_user_online_status_refresh_enabled}}</span></div>
					<input type="text" name="USER_ONLINE_STATUS_REFRESH_INTERVAL" maxlength="10" class="fixed_100 user_online_status_refresh_on" value="{{$data.USER_ONLINE_STATUS_REFRESH_INTERVAL}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_user_online_status_refresh_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_label">{{$lang.settings.website_field_user_new_messages_refresh}}:</td>
				<td class="de_control">
					<input type="hidden" name="ENABLE_USER_MESSAGES_REFRESH" value="0"/>
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="user_messages_refresh" name="ENABLE_USER_MESSAGES_REFRESH" value="1" {{if $data.ENABLE_USER_MESSAGES_REFRESH==1}}checked="checked"{{/if}}/><span {{if $data.ENABLE_USER_MESSAGES_REFRESH==1}}class="selected"{{/if}}>{{$lang.settings.website_field_user_new_messages_refresh_enabled}}</span></div>
					<input type="text" name="USER_MESSAGES_REFRESH_INTERVAL" maxlength="10" class="fixed_100 user_messages_refresh_on" value="{{$data.USER_MESSAGES_REFRESH_INTERVAL}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.website_field_user_new_messages_refresh_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.website_divider_blocked_words}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.website_divider_blocked_words_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.website_field_blocked_words}}:</td>
			<td class="de_control">
				<textarea name="BLOCKED_WORDS" class="dyn_full_size" cols="30" rows="3">{{$data.BLOCKED_WORDS}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_blocked_words_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_blocked_words_replacement}}:</td>
			<td class="de_control">
				<input type="text" name="BLOCKED_WORDS_REPLACEMENT" maxlength="1000" class="dyn_full_size" value="{{$data.BLOCKED_WORDS_REPLACEMENT}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_blocked_words_replacement_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_regexp_replacements}}:</td>
			<td class="de_control">
				<textarea name="REGEX_REPLACEMENTS" class="dyn_full_size" cols="30" rows="3">{{$data.REGEX_REPLACEMENTS}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_regexp_replacements_hint|smarty:nodefalts}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.website_divider_other}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.website_field_pseudo_video_behavior}}:</td>
			<td class="de_control">
				<select name="PSEUDO_VIDEO_BEHAVIOR">
					<option value="0" {{if $data.PSEUDO_VIDEO_BEHAVIOR==0}}selected="selected"{{/if}}>{{$lang.settings.website_field_pseudo_video_behavior_redirect}}</option>
					<option value="1" {{if $data.PSEUDO_VIDEO_BEHAVIOR==1}}selected="selected"{{/if}}>{{$lang.settings.website_field_pseudo_video_behavior_show_page}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.website_field_pseudo_video_behavior_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input id="website_settings_submit" type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildWebsiteSettingsConfirmLogic=call</span>
</div>

{{elseif $smarty.request.page=='memberzone_settings'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_memberzone_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.memberzone_header}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.memberzone_divider_general_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_status_after_premium}}:</td>
			<td class="de_control">
				<select name="STATUS_AFTER_PREMIUM">
					<option value="0" {{if $data.STATUS_AFTER_PREMIUM==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_status_after_premium_disabled}}</option>
					<option value="2" {{if $data.STATUS_AFTER_PREMIUM==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_status_after_premium_active}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_status_after_premium_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_affiliate_param_name}}:</td>
			<td class="de_control">
				<input type="text" name="AFFILIATE_PARAM_NAME" maxlength="100" class="dyn_full_size" value="{{$data.AFFILIATE_PARAM_NAME}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_affiliate_param_name_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.memberzone_divider_access_rules}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.memberzone_divider_access_rules_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_videos_access}}:</td>
			<td class="de_control">
				{{$lang.settings.memberzone_field_videos_access_type_public}}:
				<select name="PUBLIC_VIDEOS_ACCESS">
					<option value="0" {{if $data.PUBLIC_VIDEOS_ACCESS==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_all}}</option>
					<option value="1" {{if $data.PUBLIC_VIDEOS_ACCESS==1}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_members}}</option>
					<option value="2" {{if $data.PUBLIC_VIDEOS_ACCESS==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_premium}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.memberzone_field_videos_access_type_private}}:
				<select name="PRIVATE_VIDEOS_ACCESS">
					<option value="3" {{if $data.PRIVATE_VIDEOS_ACCESS==3}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_all}}</option>
					<option value="0" {{if $data.PRIVATE_VIDEOS_ACCESS==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_members}}</option>
					<option value="1" {{if $data.PRIVATE_VIDEOS_ACCESS==1}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_friends}}</option>
					<option value="2" {{if $data.PRIVATE_VIDEOS_ACCESS==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_premium}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.memberzone_field_videos_access_type_premium}}:
				<select name="PREMIUM_VIDEOS_ACCESS">
					<option value="0" {{if $data.PREMIUM_VIDEOS_ACCESS==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_all}}</option>
					<option value="1" {{if $data.PREMIUM_VIDEOS_ACCESS==1}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_members}}</option>
					<option value="2" {{if $data.PREMIUM_VIDEOS_ACCESS==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_videos_access_premium}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_videos_access_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr {{if $config.installation_type!=4}}class="hidden"{{/if}}>
			<td class="de_label">{{$lang.settings.memberzone_field_albums_access}}:</td>
			<td class="de_control">
				{{$lang.settings.memberzone_field_albums_access_type_public}}:
				<select name="PUBLIC_ALBUMS_ACCESS">
					<option value="0" {{if $data.PUBLIC_ALBUMS_ACCESS==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_all}}</option>
					<option value="1" {{if $data.PUBLIC_ALBUMS_ACCESS==1}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_members}}</option>
					<option value="2" {{if $data.PUBLIC_ALBUMS_ACCESS==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_premium}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.memberzone_field_albums_access_type_private}}:
				<select name="PRIVATE_ALBUMS_ACCESS">
					<option value="3" {{if $data.PRIVATE_ALBUMS_ACCESS==3}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_all}}</option>
					<option value="0" {{if $data.PRIVATE_ALBUMS_ACCESS==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_members}}</option>
					<option value="1" {{if $data.PRIVATE_ALBUMS_ACCESS==1}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_friends}}</option>
					<option value="2" {{if $data.PRIVATE_ALBUMS_ACCESS==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_premium}}</option>
				</select>
				&nbsp;&nbsp;
				{{$lang.settings.memberzone_field_albums_access_type_premium}}:
				<select name="PREMIUM_ALBUMS_ACCESS">
					<option value="0" {{if $data.PREMIUM_ALBUMS_ACCESS==0}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_all}}</option>
					<option value="1" {{if $data.PREMIUM_ALBUMS_ACCESS==1}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_members}}</option>
					<option value="2" {{if $data.PREMIUM_ALBUMS_ACCESS==2}}selected="selected"{{/if}}>{{$lang.settings.memberzone_field_albums_access_premium}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_albums_access_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_purchase_videos}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="standard_video_tokens_enable" name="ENABLE_TOKENS_STANDARD_VIDEO" value="1" {{if $data.ENABLE_TOKENS_STANDARD_VIDEO==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_videos_type_standard}}</label></div>
				<input type="text" name="DEFAULT_TOKENS_STANDARD_VIDEO" maxlength="10" size="10" class="standard_video_tokens_enable_on" value="{{$data.DEFAULT_TOKENS_STANDARD_VIDEO}}"/>
				{{$lang.settings.memberzone_field_tokens_purchase_videos_tokens}}
				&nbsp;&nbsp;&nbsp;&nbsp;
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="premium_video_tokens_enable" name="ENABLE_TOKENS_PREMIUM_VIDEO" value="1" {{if $data.ENABLE_TOKENS_PREMIUM_VIDEO==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_videos_type_premium}}</label></div>
				<input type="text" name="DEFAULT_TOKENS_PREMIUM_VIDEO" maxlength="10" size="10" class="premium_video_tokens_enable_on" value="{{$data.DEFAULT_TOKENS_PREMIUM_VIDEO}}"/>
				{{$lang.settings.memberzone_field_tokens_purchase_videos_tokens}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_purchase_videos_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr {{if $config.installation_type!=4}}class="hidden"{{/if}}>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_purchase_albums}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="standard_album_tokens_enable" name="ENABLE_TOKENS_STANDARD_ALBUM" value="1" {{if $data.ENABLE_TOKENS_STANDARD_ALBUM==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_albums_type_standard}}</label></div>
				<input type="text" name="DEFAULT_TOKENS_STANDARD_ALBUM" maxlength="10" size="10" class="standard_album_tokens_enable_on" value="{{$data.DEFAULT_TOKENS_STANDARD_ALBUM}}"/>
				{{$lang.settings.memberzone_field_tokens_purchase_albums_tokens}}
				&nbsp;&nbsp;&nbsp;&nbsp;
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="premium_album_tokens_enable" name="ENABLE_TOKENS_PREMIUM_ALBUM" value="1" {{if $data.ENABLE_TOKENS_PREMIUM_ALBUM==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_purchase_albums_type_premium}}</label></div>
				<input type="text" name="DEFAULT_TOKENS_PREMIUM_ALBUM" maxlength="10" size="10" class="premium_album_tokens_enable_on" value="{{$data.DEFAULT_TOKENS_PREMIUM_ALBUM}}"/>
				{{$lang.settings.memberzone_field_tokens_purchase_albums_tokens}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_purchase_albums_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_purchase_expiry}}:</td>
			<td class="de_control">
				<input type="text" name="TOKENS_PURCHASE_EXPIRY" maxlength="10" size="10" value="{{$data.TOKENS_PURCHASE_EXPIRY}}"/>
				{{$lang.settings.memberzone_field_purchase_expiry_days}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_purchase_expiry_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_enable_internal_messages}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="internal_messages_tokens_enable" name="ENABLE_TOKENS_INTERNAL_MESSAGES" value="1" {{if $data.ENABLE_TOKENS_INTERNAL_MESSAGES==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_enable_internal_messages_enabled}}</label></div>
				<input type="text" name="TOKENS_INTERNAL_MESSAGES" maxlength="10" size="10" class="internal_messages_tokens_enable_on" value="{{$data.TOKENS_INTERNAL_MESSAGES}}"/>
				{{$lang.settings.memberzone_field_tokens_enable_internal_messages_tokens}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_enable_internal_messages_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.memberzone_divider_paid_subscriptions}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.memberzone_divider_paid_subscriptions_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">
				<div class="members_subscribe_tokens_enable_on de_required">{{$lang.settings.memberzone_field_tokens_subscribe_members}} (*):</div>
				<div class="members_subscribe_tokens_enable_off">{{$lang.settings.memberzone_field_tokens_subscribe_members}}:</div>
			</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="members_subscribe_tokens_enable" name="ENABLE_TOKENS_SUBSCRIBE_MEMBERS" value="1" {{if $data.ENABLE_TOKENS_SUBSCRIBE_MEMBERS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_subscribe_members_enabled}}</label></div>
				<input type="text" name="TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE" class="members_subscribe_tokens_enable_on" maxlength="10" size="10" value="{{$data.TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE}}"/>
				{{$lang.settings.memberzone_field_tokens_subscribe_members_tokens}}
				&nbsp;&nbsp;
				<input type="text" name="TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD" class="members_subscribe_tokens_enable_on" maxlength="10" size="10" value="{{$data.TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD}}"/>
				{{$lang.settings.memberzone_field_tokens_subscribe_members_days}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_subscribe_members_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="dvds_subscribe_tokens_enable_on de_required">{{$lang.settings.memberzone_field_tokens_subscribe_dvds}} (*):</div>
				<div class="dvds_subscribe_tokens_enable_off">{{$lang.settings.memberzone_field_tokens_subscribe_dvds}}:</div>
			</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="dvds_subscribe_tokens_enable" name="ENABLE_TOKENS_SUBSCRIBE_DVDS" value="1" {{if $data.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_subscribe_dvds_enabled}}</label></div>
				<input type="text" name="TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE" class="dvds_subscribe_tokens_enable_on" maxlength="10" size="10" value="{{$data.TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE}}"/>
				{{$lang.settings.memberzone_field_tokens_subscribe_dvds_tokens}}
				&nbsp;&nbsp;
				<input type="text" name="TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD" class="dvds_subscribe_tokens_enable_on" maxlength="10" size="10" value="{{$data.TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD}}"/>
				{{$lang.settings.memberzone_field_tokens_subscribe_dvds_days}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_subscribe_dvds_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.memberzone_divider_tokens_earnings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.settings.memberzone_divider_tokens_earnings_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_sale}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_VIDEOS" value="1" {{if $data.ENABLE_TOKENS_SALE_VIDEOS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_videos}}</label></div>
				{{if $config.installation_type==4}}
					<div class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_ALBUMS" value="1" {{if $data.ENABLE_TOKENS_SALE_ALBUMS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_albums}}</label></div>
				{{/if}}
				<div class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_MEMBERS" value="1" {{if $data.ENABLE_TOKENS_SALE_MEMBERS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_members}}</label></div>
				{{if $config.installation_type==4 && $config.dvds_mode=='channels'}}
					<div class="de_lv_pair"><input type="checkbox" name="ENABLE_TOKENS_SALE_DVDS" value="1" {{if $data.ENABLE_TOKENS_SALE_DVDS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_sale_enable_dvds}}</label></div>
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_sale_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_sale_interest}}:</td>
			<td class="de_control">
				<input type="text" name="TOKENS_SALE_INTEREST" maxlength="10" size="10" value="{{$data.TOKENS_SALE_INTEREST}}"/>
				{{$lang.settings.memberzone_field_tokens_sale_interest_percent}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_sale_interest_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="traffic_videos_tokens_enable_off">{{$lang.settings.memberzone_field_tokens_traffic_enable_videos}}:</div>
				<div class="traffic_videos_tokens_enable_on de_required">{{$lang.settings.memberzone_field_tokens_traffic_enable_videos}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="traffic_videos_tokens_enable" name="ENABLE_TOKENS_TRAFFIC_VIDEOS" value="1" {{if $data.ENABLE_TOKENS_TRAFFIC_VIDEOS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_enabled}}</label></div>
				<input type="text" name="TOKENS_TRAFFIC_VIDEOS_TOKENS" maxlength="10" size="10" class="traffic_videos_tokens_enable_on" value="{{$data.TOKENS_TRAFFIC_VIDEOS_TOKENS}}"/>
				{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_tokens}}
				<input type="text" name="TOKENS_TRAFFIC_VIDEOS_UNIQUE" maxlength="10" size="10" class="traffic_videos_tokens_enable_on" value="{{$data.TOKENS_TRAFFIC_VIDEOS_UNIQUE}}"/>
				{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_unique}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_traffic_enable_videos_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr {{if $config.installation_type!=4}}class="hidden"{{/if}}>
			<td class="de_label">
				<div class="traffic_albums_tokens_enable_off">{{$lang.settings.memberzone_field_tokens_traffic_enable_albums}}:</div>
				<div class="traffic_albums_tokens_enable_on de_required">{{$lang.settings.memberzone_field_tokens_traffic_enable_albums}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="traffic_albums_tokens_enable" name="ENABLE_TOKENS_TRAFFIC_ALBUMS" value="1" {{if $data.ENABLE_TOKENS_TRAFFIC_ALBUMS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_enabled}}</label></div>
				<input type="text" name="TOKENS_TRAFFIC_ALBUMS_TOKENS" maxlength="10" size="10" class="traffic_albums_tokens_enable_on" value="{{$data.TOKENS_TRAFFIC_ALBUMS_TOKENS}}"/>
				{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_tokens}}
				<input type="text" name="TOKENS_TRAFFIC_ALBUMS_UNIQUE" maxlength="10" size="10" class="traffic_albums_tokens_enable_on" value="{{$data.TOKENS_TRAFFIC_ALBUMS_UNIQUE}}"/>
				{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_unique}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_traffic_enable_albums_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="traffic_embeds_tokens_enable_off">{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds}}:</div>
				<div class="traffic_embeds_tokens_enable_on de_required">{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="traffic_embeds_tokens_enable" name="ENABLE_TOKENS_TRAFFIC_EMBEDS" value="1" {{if $data.ENABLE_TOKENS_TRAFFIC_EMBEDS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_enabled}}</label></div>
				<input type="text" name="TOKENS_TRAFFIC_EMBEDS_TOKENS" maxlength="10" size="10" class="traffic_embeds_tokens_enable_on" value="{{$data.TOKENS_TRAFFIC_EMBEDS_TOKENS}}"/>
				{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_tokens}}
				<input type="text" name="TOKENS_TRAFFIC_EMBEDS_UNIQUE" maxlength="10" size="10" class="traffic_embeds_tokens_enable_on" value="{{$data.TOKENS_TRAFFIC_EMBEDS_UNIQUE}}"/>
				{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_unique}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_traffic_enable_embeds_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_sale_excludes}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users_noid.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="TOKENS_SALE_EXCLUDES" value="{{$data.TOKENS_SALE_EXCLUDES}}"/>
					<div class="controls">
						<input type="text" name="new_user" class="preserve_editing fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.common.users_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.memberzone_field_tokens_sale_excludes_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_enable_donations}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="donations_tokens_enable" name="ENABLE_TOKENS_DONATIONS" value="1" {{if $data.ENABLE_TOKENS_DONATIONS==1}}checked="checked"{{/if}}/><label>{{$lang.settings.memberzone_field_tokens_enable_donations_enabled}}</label></div>
				<input type="text" name="TOKENS_DONATION_MIN" maxlength="10" size="10" class="donations_tokens_enable_on" value="{{$data.TOKENS_DONATION_MIN}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_enable_donations_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_tokens_donation_interest}}:</td>
			<td class="de_control">
				<input type="text" name="TOKENS_DONATION_INTEREST" maxlength="10" size="10" value="{{$data.TOKENS_DONATION_INTEREST}}"/>
				{{$lang.settings.memberzone_field_tokens_donation_interest_percent}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_tokens_donation_interest_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.memberzone_divider_activity_awards}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_activity_index_formula}}:</td>
			<td class="de_control">
				<input type="text" name="ACTIVITY_INDEX_FORMULA" maxlength="1000" class="dyn_full_size" value="{{$data.ACTIVITY_INDEX_FORMULA}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.memberzone_field_activity_index_formula_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label"></td>
			<td class="de_control">
				<a id="formula_details_expander" class="de_expand" href="javascript:stub()">{{$lang.settings.memberzone_field_activity_index_formula_hint2_show}}</a>
				<div class="formula_details_expander hidden"><br/>{{$lang.settings.memberzone_field_activity_index_formula_hint2}}</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.memberzone_field_activity_index_excludes}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users_noid.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="ACTIVITY_INDEX_INCLUDES" value="{{$data.ACTIVITY_INDEX_INCLUDES}}"/>
					<div class="controls">
						<input type="text" name="new_user" class="preserve_editing fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.common.users_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.memberzone_field_activity_index_excludes_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header">
						<td>{{$lang.settings.memberzone_awards_col_action}}</td>
						<td>{{$lang.settings.memberzone_awards_col_condition}}</td>
						<td>{{$lang.settings.memberzone_awards_col_tokens}}</td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_signup}}</td>
						<td class="nowrap"><input type="text" maxlength="10" size="5" disabled="disabled"/> {{$lang.common.undefined}}</td>
						<td><input type="text" name="AWARDS_SIGNUP" maxlength="10" size="10" value="{{$data.AWARDS_SIGNUP}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_avatar}}</td>
						<td class="nowrap"><input type="text" maxlength="10" size="5" disabled="disabled"/> {{$lang.common.undefined}}</td>
						<td><input type="text" name="AWARDS_AVATAR" maxlength="10" size="10" value="{{$data.AWARDS_AVATAR}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_cover}}</td>
						<td class="nowrap"><input type="text" maxlength="10" size="5" disabled="disabled"/> {{$lang.common.undefined}}</td>
						<td><input type="text" name="AWARDS_COVER" maxlength="10" size="10" value="{{$data.AWARDS_COVER}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_login}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_LOGIN_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_LOGIN_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_interval}}</td>
						<td><input type="text" name="AWARDS_LOGIN" maxlength="10" size="10" value="{{$data.AWARDS_LOGIN}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_video}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_VIDEO_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_VIDEO_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_VIDEO" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_VIDEO}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_album}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_ALBUM_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_ALBUM_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_ALBUM" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_ALBUM}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_content_source}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_CS_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_CS_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_CS" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_CS}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_model}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_MODEL_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_MODEL_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_MODEL" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_MODEL}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_dvd}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_DVD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_DVD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_DVD" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_DVD}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_post}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_POST_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_POST_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_POST" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_POST}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_comment_playlist}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_COMMENT_PLAYLIST_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_COMMENT_PLAYLIST_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_COMMENT_PLAYLIST" maxlength="10" size="10" value="{{$data.AWARDS_COMMENT_PLAYLIST}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_video_upload}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_VIDEO_UPLOAD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_VIDEO_UPLOAD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_duration}}</td>
						<td><input type="text" name="AWARDS_VIDEO_UPLOAD" maxlength="10" size="10" value="{{$data.AWARDS_VIDEO_UPLOAD}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_album_upload}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_ALBUM_UPLOAD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_ALBUM_UPLOAD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_images}}</td>
						<td><input type="text" name="AWARDS_ALBUM_UPLOAD" maxlength="10" size="10" value="{{$data.AWARDS_ALBUM_UPLOAD}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_post_upload}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_POST_UPLOAD_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_POST_UPLOAD_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_characters}}</td>
						<td><input type="text" name="AWARDS_POST_UPLOAD" maxlength="10" size="10" value="{{$data.AWARDS_POST_UPLOAD}}"/></td>
					</tr>
					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_col_action_referral_signup}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_REFERRAL_SIGNUP_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_REFERRAL_SIGNUP_CONDITION}}"/> {{$lang.settings.memberzone_awards_col_condition_ip_unique}}</td>
						<td><input type="text" name="AWARDS_REFERRAL_SIGNUP" maxlength="10" size="10" value="{{$data.AWARDS_REFERRAL_SIGNUP}}"/></td>
					</tr>

					<tr class="eg_data">
						<td class="nowrap">{{$lang.settings.memberzone_awards_earning_unique_views}}</td>
						<td class="nowrap"><input type="text" name="AWARDS_EARNING_UNIQUE_VIEWS_CONDITION" maxlength="10" size="5" value="{{$data.AWARDS_EARNING_UNIQUE_VIEWS_CONDITION}}"/> {{$lang.settings.memberzone_awards_earning_unique_views_condition}}</td>
						<td><input type="text" name="AWARDS_EARNING_UNIQUE_VIEWS" maxlength="10" size="10" value="{{$data.AWARDS_EARNING_UNIQUE_VIEWS}}"/></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input id="memberzone_settings_submit" type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>

{{elseif $smarty.request.page=='antispam_settings'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_antispam_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.antispam_header}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="4">
					<span class="de_hint">{{$lang.settings.antispam_header_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_blacklisting}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.antispam_field_blacklisted_words}}:</td>
			<td class="de_control">
				<textarea name="ANTISPAM_BLACKLIST_WORDS" class="dyn_full_size" cols="30" rows="3">{{$data.ANTISPAM_BLACKLIST_WORDS}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.antispam_field_blacklisted_words_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.antispam_field_blacklisted_domains}}:</td>
			<td class="de_control">
				<textarea name="ANTISPAM_BLACKLIST_DOMAINS" class="dyn_full_size" cols="30" rows="3">{{$data.ANTISPAM_BLACKLIST_DOMAINS}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.antispam_field_blacklisted_domains_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.antispam_field_blacklisted_ips}}:</td>
			<td class="de_control">
				<textarea name="ANTISPAM_BLACKLIST_IPS" class="dyn_full_size" cols="30" rows="3">{{$data.ANTISPAM_BLACKLIST_IPS}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.antispam_field_blacklisted_ips_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.antispam_field_blacklisted_action}}:</td>
			<td class="de_control">
				<select name="ANTISPAM_BLACKLIST_ACTION">
					<option value="0" {{if $data.ANTISPAM_BLACKLIST_ACTION==0}}selected{{/if}}>{{$lang.settings.antispam_field_blacklisted_action_delete}}</option>
					<option value="1" {{if $data.ANTISPAM_BLACKLIST_ACTION==1}}selected{{/if}}>{{$lang.settings.antispam_field_blacklisted_action_deactivate}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.antispam_field_blacklisted_action_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.installation_type>=3}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_videos}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
				<td class="de_control">
					<select name="ANTISPAM_VIDEOS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_VIDEOS_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_VIDEOS_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_VIDEOS"}}
			{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}:</td>
					<td class="de_control">
						{{assign var="action_variable" value="`$section`_`$action`_1"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_videos}}
						&nbsp;&nbsp;
						{{assign var="action_variable" value="`$section`_`$action`_2"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_seconds}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_albums}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
				<td class="de_control">
					<select name="ANTISPAM_ALBUMS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_ALBUMS_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_ALBUMS_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_ALBUMS"}}
			{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}:</td>
					<td class="de_control">
						{{assign var="action_variable" value="`$section`_`$action`_1"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_albums}}
						&nbsp;&nbsp;
						{{assign var="action_variable" value="`$section`_`$action`_2"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_seconds}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		{{if $config.installation_type>=3}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_posts}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
				<td class="de_control">
					<select name="ANTISPAM_POSTS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_POSTS_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_POSTS_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_POSTS"}}
			{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}:</td>
					<td class="de_control">
						{{assign var="action_variable" value="`$section`_`$action`_1"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_posts}}
						&nbsp;&nbsp;
						{{assign var="action_variable" value="`$section`_`$action`_2"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_seconds}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		{{if $config.installation_type>=2}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_playlists}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
				<td class="de_control">
					<select name="ANTISPAM_PLAYLISTS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_PLAYLISTS_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_PLAYLISTS_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_PLAYLISTS"}}
			{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}:</td>
					<td class="de_control">
						{{assign var="action_variable" value="`$section`_`$action`_1"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_playlists}}
						&nbsp;&nbsp;
						{{assign var="action_variable" value="`$section`_`$action`_2"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_seconds}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_dvds}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
				<td class="de_control">
					<select name="ANTISPAM_DVDS_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_DVDS_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_DVDS_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_DVDS"}}
			{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}:</td>
					<td class="de_control">
						{{assign var="action_variable" value="`$section`_`$action`_1"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_dvds}}
						&nbsp;&nbsp;
						{{assign var="action_variable" value="`$section`_`$action`_2"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_seconds}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_comments}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
			<td class="de_control">
				<select name="ANTISPAM_COMMENTS_ANALYZE_HISTORY">
					<option value="0" {{if $data.ANTISPAM_COMMENTS_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
					<option value="1" {{if $data.ANTISPAM_COMMENTS_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{assign var="section" value="ANTISPAM_COMMENTS"}}
		{{assign var="actions" value=","|explode:"FORCE_CAPTCHA,FORCE_DISABLED,AUTODELETE,ERROR,DUPLICATES"}}
		{{foreach from=$actions item="action"}}
			{{assign var="action_key" value=""}}
			{{if $action=='FORCE_CAPTCHA'}}
				{{assign var="action_key" value='antispam_field_action_force_captcha'}}
			{{elseif $action=='FORCE_DISABLED'}}
				{{assign var="action_key" value='antispam_field_action_deactivate'}}
			{{elseif $action=='AUTODELETE'}}
				{{assign var="action_key" value='antispam_field_action_autodelete'}}
			{{elseif $action=='ERROR'}}
				{{assign var="action_key" value='antispam_field_action_show_error'}}
			{{elseif $action=='DUPLICATES'}}
				{{assign var="action_key" value='antispam_field_action_duplicates'}}
			{{/if}}
			{{assign var="action_hint_key" value="`$action_key`_hint"}}
			<tr>
				<td class="de_label">{{$lang.settings.$action_key}}:</td>
				<td class="de_control">
					{{if $action=='DUPLICATES'}}
						{{assign var="action_variable" value="`$section`_`$action`"}}
						{{assign var="action_label_key" value="`$action_key`_delete"}}
						<div class="de_lv_pair"><input type="checkbox" name="{{$action_variable}}" value="1" {{if $data.$action_variable==1}}checked="checked"{{/if}}/><label>{{$lang.settings.$action_label_key}}</label></div>
					{{else}}
						{{assign var="action_variable" value="`$section`_`$action`_1"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_comments}}
						&nbsp;&nbsp;
						{{assign var="action_variable" value="`$section`_`$action`_2"}}
						<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
						&nbsp;
						{{$lang.settings.antispam_field_unit_seconds}}
					{{/if}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
					{{/if}}
				</td>
			</tr>
		{{/foreach}}
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.antispam_divider_messages}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.antispam_field_analyze_history}}:</td>
				<td class="de_control">
					<select name="ANTISPAM_MESSAGES_ANALYZE_HISTORY">
						<option value="0" {{if $data.ANTISPAM_MESSAGES_ANALYZE_HISTORY=='0'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_all}}</option>
						<option value="1" {{if $data.ANTISPAM_MESSAGES_ANALYZE_HISTORY=='1'}}selected="selected"{{/if}}>{{$lang.settings.antispam_field_analyze_history_user}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.antispam_field_analyze_history_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{assign var="section" value="ANTISPAM_MESSAGES"}}
			{{assign var="actions" value=","|explode:"AUTODELETE,ERROR,DUPLICATES"}}
			{{foreach from=$actions item="action"}}
				{{assign var="action_key" value=""}}
				{{if $action=='FORCE_CAPTCHA'}}
					{{assign var="action_key" value='antispam_field_action_force_captcha'}}
				{{elseif $action=='FORCE_DISABLED'}}
					{{assign var="action_key" value='antispam_field_action_deactivate'}}
				{{elseif $action=='AUTODELETE'}}
					{{assign var="action_key" value='antispam_field_action_autodelete'}}
				{{elseif $action=='ERROR'}}
					{{assign var="action_key" value='antispam_field_action_show_error'}}
				{{elseif $action=='DUPLICATES'}}
					{{assign var="action_key" value='antispam_field_action_duplicates'}}
				{{/if}}
				{{assign var="action_hint_key" value="`$action_key`_hint"}}
				<tr>
					<td class="de_label">{{$lang.settings.$action_key}}:</td>
					<td class="de_control">
						{{if $action=='DUPLICATES'}}
							{{assign var="action_variable" value="`$section`_`$action`"}}
							{{assign var="action_label_key" value="`$action_key`_delete"}}
							<div class="de_lv_pair"><input type="checkbox" name="{{$action_variable}}" value="1" {{if $data.$action_variable==1}}checked="checked"{{/if}}/><label>{{$lang.settings.$action_label_key}}</label></div>
						{{else}}
							{{assign var="action_variable" value="`$section`_`$action`_1"}}
							<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
							&nbsp;
							{{$lang.settings.antispam_field_unit_messages}}
							&nbsp;&nbsp;
							{{assign var="action_variable" value="`$section`_`$action`_2"}}
							<input type="text" name="{{$action_variable}}" maxlength="10" size="10" value="{{if $data.$action_variable>0}}{{$data.$action_variable}}{{/if}}"/>
							&nbsp;
							{{$lang.settings.antispam_field_unit_seconds}}
						{{/if}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.$action_hint_key}}</span>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2"><input id="antispam_settings_submit" type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>

{{elseif $smarty.request.page=='stats_settings'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_stats_settings_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.stats_header}}</div></td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="collect_traffic_stats" name="collect_traffic_stats" value="1" {{if $data.collect_traffic_stats==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="collect_traffic_stats_on">
			<td class="de_control" colspan="2">
				<table class="control_group">
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_traffic_stats_countries" value="1" {{if $data.collect_traffic_stats_countries==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats_countries}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_countries_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_traffic_stats_devices" value="1" {{if $data.collect_traffic_stats_devices==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats_devices}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_devices_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_traffic_stats_embed_domains" value="1" {{if $data.collect_traffic_stats_embed_domains==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_traffic_stats_embed_domains}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_traffic_stats_embed_domains_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_keep_stats_for}}: <input type="text" name="keep_traffic_stats_period" class="fixed_100 collect_traffic_stats_on" value="{{$data.keep_traffic_stats_period}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="collect_player_stats" name="collect_player_stats" value="1" {{if $data.collect_player_stats==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="collect_player_stats_on">
			<td class="de_control" colspan="2">
				<table class="control_group">
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_player_stats_countries" value="1" {{if $data.collect_player_stats_countries==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats_countries}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_countries_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_player_stats_devices" value="1" {{if $data.collect_player_stats_devices==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats_devices}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_devices_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_player_stats_embed_profiles" value="1" {{if $data.collect_player_stats_embed_profiles==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_player_stats_embed_profiles}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_embed_profiles_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_collect_player_stats_reporting}}:
							<select name="player_stats_reporting">
								<option value="0" {{if $data.player_stats_reporting==0}}selected="selected"{{/if}}>{{$lang.settings.stats_field_collect_player_stats_reporting_kvs}}</option>
								<option value="1" {{if $data.player_stats_reporting==1}}selected="selected"{{/if}}>{{$lang.settings.stats_field_collect_player_stats_reporting_ga}}</option>
								<option value="2" {{if $data.player_stats_reporting==2}}selected="selected"{{/if}}>{{$lang.settings.stats_field_collect_player_stats_reporting_both}}</option>
							</select>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_player_stats_reporting_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_keep_stats_for}}: <input type="text" name="keep_player_stats_period" class="fixed_100 collect_player_stats_on" value="{{$data.keep_player_stats_period}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="collect_videos_stats" name="collect_videos_stats" value="1" {{if $data.collect_videos_stats==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="collect_videos_stats_on">
			<td class="de_control" colspan="2">
				<table class="control_group">
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_videos_stats_unique" value="1" {{if $data.collect_videos_stats_unique==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats_unique}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_unique_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_videos_stats_video_plays" value="1" {{if $data.collect_videos_stats_video_plays==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats_video_plays}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_video_plays_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_videos_stats_video_files" value="1" {{if $data.collect_videos_stats_video_files==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_videos_stats_video_files}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_videos_stats_video_files_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_keep_stats_for}}: <input type="text" name="keep_videos_stats_period" class="fixed_100 collect_videos_stats_on" value="{{$data.keep_videos_stats_period}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="collect_albums_stats" name="collect_albums_stats" value="1" {{if $data.collect_albums_stats==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_albums_stats}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.stats_field_collect_albums_stats_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="collect_albums_stats_on">
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td class="de_label de_dependent">
								<div class="de_lv_pair"><input type="checkbox" name="collect_albums_stats_unique" value="1" {{if $data.collect_albums_stats_unique==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_albums_stats_unique}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.stats_field_collect_albums_stats_unique_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td class="de_label de_dependent">
								<div class="de_lv_pair"><input type="checkbox" name="collect_albums_stats_album_images" value="1" {{if $data.collect_albums_stats_album_images==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_albums_stats_album_images}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.stats_field_collect_albums_stats_album_images_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td class="de_label de_dependent">
								{{$lang.settings.stats_field_keep_stats_for}}: <input type="text" name="keep_albums_stats_period" class="fixed_100 collect_albums_stats_on" value="{{$data.keep_albums_stats_period}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="collect_memberzone_stats" name="collect_memberzone_stats" value="1" {{if $data.collect_memberzone_stats==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_memberzone_stats}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.stats_field_collect_memberzone_stats_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="collect_memberzone_stats_on">
			<td class="de_control" colspan="2">
				<table class="control_group">
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_memberzone_stats_video_files" value="1" {{if $data.collect_memberzone_stats_video_files==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_memberzone_stats_video_files}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_memberzone_stats_video_files_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="collect_memberzone_stats_album_images" value="1" {{if $data.collect_memberzone_stats_album_images==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_memberzone_stats_album_images}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_collect_memberzone_stats_album_images_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_keep_stats_for}}: <input type="text" name="keep_memberzone_stats_period" class="fixed_100 collect_memberzone_stats_on" value="{{$data.keep_memberzone_stats_period}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="collect_search_stats" name="collect_search_stats" value="1" {{if $data.collect_search_stats==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_collect_search_stats}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.stats_field_collect_search_stats_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="collect_search_stats_on">
			<td class="de_control" colspan="2">
				<table class="control_group">
					<tr>
						<td class="de_label de_dependent">
							<div class="de_lv_pair"><input type="checkbox" name="search_to_lowercase" value="1" {{if $data.search_to_lowercase==1}}checked="checked"{{/if}}/><label>{{$lang.settings.stats_field_search_to_lowercase}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_search_to_lowercase_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_keep_stats_for}}: <input type="text" name="keep_search_stats_period" class="fixed_100 collect_search_stats_on" value="{{$data.keep_search_stats_period}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_keep_stats_for_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_search_max_length}}: <input type="text" name="search_max_length" class="fixed_100 collect_search_stats_on" value="{{if $data.search_max_length>0}}{{$data.search_max_length}}{{/if}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_search_max_length_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td class="de_label de_dependent">
							{{$lang.settings.stats_field_search_stop_symbols}}: <input type="text" name="search_stop_symbols" class="fixed_100 collect_search_stats_on" value="{{$data.search_stop_symbols}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.stats_field_search_stop_symbols_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input id="stats_settings_submit" type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>

{{elseif $smarty.request.page=='customization'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_customization_complete"/>
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>

		<input type="hidden" name="ENABLE_VIDEO_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_VIDEO_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_ALBUM_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_GROUP_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_GROUP_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CATEGORY_GROUP_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_TAG_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_CS_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_CS_FILE_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_CS_GROUP_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_MODEL_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_DVD_FILE_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_DVD_GROUP_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_5" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_6" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_7" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_8" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_9" value="0"/>
		<input type="hidden" name="ENABLE_USER_FIELD_10" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FILE_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FILE_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_REFERER_FILE_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_1" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_2" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_3" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_4" value="0"/>
		<input type="hidden" name="ENABLE_FEEDBACK_FIELD_5" value="0"/>
		{{foreach name="data" item="item" from=$list_posts_types}}
			{{section name="fields" start="1" loop=11}}
				<input type="hidden" name="ENABLE_POST_{{$item.post_type_id}}_FIELD_{{$smarty.section.fields.index}}" value="0"/>
				<input type="hidden" name="ENABLE_POST_{{$item.post_type_id}}_FILE_FIELD_{{$smarty.section.fields.index}}" value="0"/>
			{{/section}}
		{{/foreach}}
	</div>
	<table class="de">
		<tr>
			<td class="de_header"><div>{{$lang.settings.customization_header}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control">
				<table class="de_edit_grid">
					<colgroup>
						<col width="15%"/>
						<col width="10%"/>
						<col width="10%"/>
						<col/>
					</colgroup>
					<tr class="eg_header">
						<td>{{$lang.settings.customization_col_id}}</td>
						<td>{{$lang.settings.customization_col_type}}</td>
						<td>{{$lang.settings.customization_col_enabled}}</td>
						<td>{{$lang.settings.customization_col_field_name}}</td>
					</tr>
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_video}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="video_1" type="checkbox" name="ENABLE_VIDEO_FIELD_1" value="1" {{if $data.ENABLE_VIDEO_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="VIDEO_FIELD_1_NAME" class="dyn_full_size video_1_on" value="{{$data.VIDEO_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="video_2" type="checkbox" name="ENABLE_VIDEO_FIELD_2" value="1" {{if $data.ENABLE_VIDEO_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="VIDEO_FIELD_2_NAME" class="dyn_full_size video_2_on" value="{{$data.VIDEO_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="video_3" type="checkbox" name="ENABLE_VIDEO_FIELD_3" value="1" {{if $data.ENABLE_VIDEO_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="VIDEO_FIELD_3_NAME" class="dyn_full_size video_3_on" value="{{$data.VIDEO_FIELD_3_NAME}}"/></td>
					</tr>
					{{if $config.installation_type==4}}
						<tr class="eg_group_header">
							<td colspan="4">{{$lang.settings.customization_divider_album}}</td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_1}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="album_1" type="checkbox" name="ENABLE_ALBUM_FIELD_1" value="1" {{if $data.ENABLE_ALBUM_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="ALBUM_FIELD_1_NAME" class="dyn_full_size album_1_on" value="{{$data.ALBUM_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_2}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="album_2" type="checkbox" name="ENABLE_ALBUM_FIELD_2" value="1" {{if $data.ENABLE_ALBUM_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="ALBUM_FIELD_2_NAME" class="dyn_full_size album_2_on" value="{{$data.ALBUM_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_3}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="album_3" type="checkbox" name="ENABLE_ALBUM_FIELD_3" value="1" {{if $data.ENABLE_ALBUM_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="ALBUM_FIELD_3_NAME" class="dyn_full_size album_3_on" value="{{$data.ALBUM_FIELD_3_NAME}}"/></td>
						</tr>
					{{/if}}
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_category}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_1" type="checkbox" name="ENABLE_CATEGORY_FIELD_1" value="1" {{if $data.ENABLE_CATEGORY_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_1_NAME" class="dyn_full_size category_1_on" value="{{$data.CATEGORY_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_2" type="checkbox" name="ENABLE_CATEGORY_FIELD_2" value="1" {{if $data.ENABLE_CATEGORY_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_2_NAME" class="dyn_full_size category_2_on" value="{{$data.CATEGORY_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_3" type="checkbox" name="ENABLE_CATEGORY_FIELD_3" value="1" {{if $data.ENABLE_CATEGORY_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_3_NAME" class="dyn_full_size category_3_on" value="{{$data.CATEGORY_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_4}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_4" type="checkbox" name="ENABLE_CATEGORY_FIELD_4" value="1" {{if $data.ENABLE_CATEGORY_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_4_NAME" class="dyn_full_size category_4_on" value="{{$data.CATEGORY_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_5}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_5" type="checkbox" name="ENABLE_CATEGORY_FIELD_5" value="1" {{if $data.ENABLE_CATEGORY_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_5_NAME" class="dyn_full_size category_5_on" value="{{$data.CATEGORY_FIELD_5_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_6}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_6" type="checkbox" name="ENABLE_CATEGORY_FIELD_6" value="1" {{if $data.ENABLE_CATEGORY_FIELD_6==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_6_NAME" class="dyn_full_size category_6_on" value="{{$data.CATEGORY_FIELD_6_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_7}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_7" type="checkbox" name="ENABLE_CATEGORY_FIELD_7" value="1" {{if $data.ENABLE_CATEGORY_FIELD_7==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_7_NAME" class="dyn_full_size category_7_on" value="{{$data.CATEGORY_FIELD_7_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_8}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_8" type="checkbox" name="ENABLE_CATEGORY_FIELD_8" value="1" {{if $data.ENABLE_CATEGORY_FIELD_8==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_8_NAME" class="dyn_full_size category_8_on" value="{{$data.CATEGORY_FIELD_8_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_9}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_9" type="checkbox" name="ENABLE_CATEGORY_FIELD_9" value="1" {{if $data.ENABLE_CATEGORY_FIELD_9==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_9_NAME" class="dyn_full_size category_9_on" value="{{$data.CATEGORY_FIELD_9_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_10}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_10" type="checkbox" name="ENABLE_CATEGORY_FIELD_10" value="1" {{if $data.ENABLE_CATEGORY_FIELD_10==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FIELD_10_NAME" class="dyn_full_size category_10_on" value="{{$data.CATEGORY_FIELD_10_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_1}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_file1" type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_1" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FILE_FIELD_1_NAME" class="dyn_full_size category_file1_on" value="{{$data.CATEGORY_FILE_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_2}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_file2" type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_2" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FILE_FIELD_2_NAME" class="dyn_full_size category_file2_on" value="{{$data.CATEGORY_FILE_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_3}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_file3" type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_3" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FILE_FIELD_3_NAME" class="dyn_full_size category_file3_on" value="{{$data.CATEGORY_FILE_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_4}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_file4" type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_4" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FILE_FIELD_4_NAME" class="dyn_full_size category_file4_on" value="{{$data.CATEGORY_FILE_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_5}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_file5" type="checkbox" name="ENABLE_CATEGORY_FILE_FIELD_5" value="1" {{if $data.ENABLE_CATEGORY_FILE_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_FILE_FIELD_5_NAME" class="dyn_full_size category_file5_on" value="{{$data.CATEGORY_FILE_FIELD_5_NAME}}"/></td>
					</tr>
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_category_group}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_group_1" type="checkbox" name="ENABLE_CATEGORY_GROUP_FIELD_1" value="1" {{if $data.ENABLE_CATEGORY_GROUP_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_GROUP_FIELD_1_NAME" class="dyn_full_size category_group_1_on" value="{{$data.CATEGORY_GROUP_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_group_2" type="checkbox" name="ENABLE_CATEGORY_GROUP_FIELD_2" value="1" {{if $data.ENABLE_CATEGORY_GROUP_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_GROUP_FIELD_2_NAME" class="dyn_full_size category_group_2_on" value="{{$data.CATEGORY_GROUP_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="category_group_3" type="checkbox" name="ENABLE_CATEGORY_GROUP_FIELD_3" value="1" {{if $data.ENABLE_CATEGORY_GROUP_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CATEGORY_GROUP_FIELD_3_NAME" class="dyn_full_size category_group_3_on" value="{{$data.CATEGORY_GROUP_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_tag}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="tag1" type="checkbox" name="ENABLE_TAG_FIELD_1" value="1" {{if $data.ENABLE_TAG_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="TAG_FIELD_1_NAME" class="dyn_full_size tag1_on" value="{{$data.TAG_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="tag2" type="checkbox" name="ENABLE_TAG_FIELD_2" value="1" {{if $data.ENABLE_TAG_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="TAG_FIELD_2_NAME" class="dyn_full_size tag2_on" value="{{$data.TAG_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="tag3" type="checkbox" name="ENABLE_TAG_FIELD_3" value="1" {{if $data.ENABLE_TAG_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="TAG_FIELD_3_NAME" class="dyn_full_size tag3_on" value="{{$data.TAG_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_4}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="tag4" type="checkbox" name="ENABLE_TAG_FIELD_4" value="1" {{if $data.ENABLE_TAG_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="TAG_FIELD_4_NAME" class="dyn_full_size tag4_on" value="{{$data.TAG_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_5}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="tag5" type="checkbox" name="ENABLE_TAG_FIELD_5" value="1" {{if $data.ENABLE_TAG_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="TAG_FIELD_5_NAME" class="dyn_full_size tag5_on" value="{{$data.TAG_FIELD_5_NAME}}"/></td>
					</tr>
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_content_source}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_1" type="checkbox" name="ENABLE_CS_FIELD_1" value="1" {{if $data.ENABLE_CS_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_1_NAME" class="dyn_full_size cs_1_on" value="{{$data.CS_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_2" type="checkbox" name="ENABLE_CS_FIELD_2" value="1" {{if $data.ENABLE_CS_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_2_NAME" class="dyn_full_size cs_2_on" value="{{$data.CS_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_3" type="checkbox" name="ENABLE_CS_FIELD_3" value="1" {{if $data.ENABLE_CS_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_3_NAME" class="dyn_full_size cs_3_on" value="{{$data.CS_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_4}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_4" type="checkbox" name="ENABLE_CS_FIELD_4" value="1" {{if $data.ENABLE_CS_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_4_NAME" class="dyn_full_size cs_4_on" value="{{$data.CS_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_5}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_5" type="checkbox" name="ENABLE_CS_FIELD_5" value="1" {{if $data.ENABLE_CS_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_5_NAME" class="dyn_full_size cs_5_on" value="{{$data.CS_FIELD_5_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_6}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_6" type="checkbox" name="ENABLE_CS_FIELD_6" value="1" {{if $data.ENABLE_CS_FIELD_6==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_6_NAME" class="dyn_full_size cs_6_on" value="{{$data.CS_FIELD_6_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_7}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_7" type="checkbox" name="ENABLE_CS_FIELD_7" value="1" {{if $data.ENABLE_CS_FIELD_7==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_7_NAME" class="dyn_full_size cs_7_on" value="{{$data.CS_FIELD_7_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_8}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_8" type="checkbox" name="ENABLE_CS_FIELD_8" value="1" {{if $data.ENABLE_CS_FIELD_8==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_8_NAME" class="dyn_full_size cs_8_on" value="{{$data.CS_FIELD_8_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_9}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_9" type="checkbox" name="ENABLE_CS_FIELD_9" value="1" {{if $data.ENABLE_CS_FIELD_9==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_9_NAME" class="dyn_full_size cs_9_on" value="{{$data.CS_FIELD_9_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_10}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_10" type="checkbox" name="ENABLE_CS_FIELD_10" value="1" {{if $data.ENABLE_CS_FIELD_10==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FIELD_10_NAME" class="dyn_full_size cs_10_on" value="{{$data.CS_FIELD_10_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_1}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file1" type="checkbox" name="ENABLE_CS_FILE_FIELD_1" value="1" {{if $data.ENABLE_CS_FILE_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_1_NAME" class="dyn_full_size cs_file1_on" value="{{$data.CS_FILE_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_2}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file2" type="checkbox" name="ENABLE_CS_FILE_FIELD_2" value="1" {{if $data.ENABLE_CS_FILE_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_2_NAME" class="dyn_full_size cs_file2_on" value="{{$data.CS_FILE_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_3}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file3" type="checkbox" name="ENABLE_CS_FILE_FIELD_3" value="1" {{if $data.ENABLE_CS_FILE_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_3_NAME" class="dyn_full_size cs_file3_on" value="{{$data.CS_FILE_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_4}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file4" type="checkbox" name="ENABLE_CS_FILE_FIELD_4" value="1" {{if $data.ENABLE_CS_FILE_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_4_NAME" class="dyn_full_size cs_file4_on" value="{{$data.CS_FILE_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_5}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file5" type="checkbox" name="ENABLE_CS_FILE_FIELD_5" value="1" {{if $data.ENABLE_CS_FILE_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_5_NAME" class="dyn_full_size cs_file5_on" value="{{$data.CS_FILE_FIELD_5_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_6}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file6" type="checkbox" name="ENABLE_CS_FILE_FIELD_6" value="1" {{if $data.ENABLE_CS_FILE_FIELD_6==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_6_NAME" class="dyn_full_size cs_file6_on" value="{{$data.CS_FILE_FIELD_6_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_7}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file7" type="checkbox" name="ENABLE_CS_FILE_FIELD_7" value="1" {{if $data.ENABLE_CS_FILE_FIELD_7==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_7_NAME" class="dyn_full_size cs_file7_on" value="{{$data.CS_FILE_FIELD_7_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_8}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file8" type="checkbox" name="ENABLE_CS_FILE_FIELD_8" value="1" {{if $data.ENABLE_CS_FILE_FIELD_8==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_8_NAME" class="dyn_full_size cs_file8_on" value="{{$data.CS_FILE_FIELD_8_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_9}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file9" type="checkbox" name="ENABLE_CS_FILE_FIELD_9" value="1" {{if $data.ENABLE_CS_FILE_FIELD_9==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_9_NAME" class="dyn_full_size cs_file9_on" value="{{$data.CS_FILE_FIELD_9_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_10}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_file10" type="checkbox" name="ENABLE_CS_FILE_FIELD_10" value="1" {{if $data.ENABLE_CS_FILE_FIELD_10==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_FILE_FIELD_10_NAME" class="dyn_full_size cs_file10_on" value="{{$data.CS_FILE_FIELD_10_NAME}}"/></td>
					</tr>
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_content_source_group}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_group1" type="checkbox" name="ENABLE_CS_GROUP_FIELD_1" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_GROUP_FIELD_1_NAME" class="dyn_full_size cs_group1_on" value="{{$data.CS_GROUP_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_group2" type="checkbox" name="ENABLE_CS_GROUP_FIELD_2" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_GROUP_FIELD_2_NAME" class="dyn_full_size cs_group2_on" value="{{$data.CS_GROUP_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_group3" type="checkbox" name="ENABLE_CS_GROUP_FIELD_3" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_GROUP_FIELD_3_NAME" class="dyn_full_size cs_group3_on" value="{{$data.CS_GROUP_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_4}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_group4" type="checkbox" name="ENABLE_CS_GROUP_FIELD_4" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_GROUP_FIELD_4_NAME" class="dyn_full_size cs_group4_on" value="{{$data.CS_GROUP_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_5}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="cs_group5" type="checkbox" name="ENABLE_CS_GROUP_FIELD_5" value="1" {{if $data.ENABLE_CS_GROUP_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="CS_GROUP_FIELD_5_NAME" class="dyn_full_size cs_group5_on" value="{{$data.CS_GROUP_FIELD_5_NAME}}"/></td>
					</tr>
					{{if $config.installation_type>=2}}
						<tr class="eg_group_header">
							<td colspan="4">{{$lang.settings.customization_divider_model}}</td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_1}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model1" type="checkbox" name="ENABLE_MODEL_FIELD_1" value="1" {{if $data.ENABLE_MODEL_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_1_NAME" class="dyn_full_size model1_on" value="{{$data.MODEL_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_2}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model2" type="checkbox" name="ENABLE_MODEL_FIELD_2" value="1" {{if $data.ENABLE_MODEL_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_2_NAME" class="dyn_full_size model2_on" value="{{$data.MODEL_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_3}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model3" type="checkbox" name="ENABLE_MODEL_FIELD_3" value="1" {{if $data.ENABLE_MODEL_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_3_NAME" class="dyn_full_size model3_on" value="{{$data.MODEL_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_4}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model4" type="checkbox" name="ENABLE_MODEL_FIELD_4" value="1" {{if $data.ENABLE_MODEL_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_4_NAME" class="dyn_full_size model4_on" value="{{$data.MODEL_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_5}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model5" type="checkbox" name="ENABLE_MODEL_FIELD_5" value="1" {{if $data.ENABLE_MODEL_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_5_NAME" class="dyn_full_size model5_on" value="{{$data.MODEL_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_6}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model6" type="checkbox" name="ENABLE_MODEL_FIELD_6" value="1" {{if $data.ENABLE_MODEL_FIELD_6==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_6_NAME" class="dyn_full_size model6_on" value="{{$data.MODEL_FIELD_6_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_7}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model7" type="checkbox" name="ENABLE_MODEL_FIELD_7" value="1" {{if $data.ENABLE_MODEL_FIELD_7==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_7_NAME" class="dyn_full_size model7_on" value="{{$data.MODEL_FIELD_7_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_8}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model8" type="checkbox" name="ENABLE_MODEL_FIELD_8" value="1" {{if $data.ENABLE_MODEL_FIELD_8==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_8_NAME" class="dyn_full_size model8_on" value="{{$data.MODEL_FIELD_8_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_9}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model9" type="checkbox" name="ENABLE_MODEL_FIELD_9" value="1" {{if $data.ENABLE_MODEL_FIELD_9==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_9_NAME" class="dyn_full_size model9_on" value="{{$data.MODEL_FIELD_9_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_10}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model10" type="checkbox" name="ENABLE_MODEL_FIELD_10" value="1" {{if $data.ENABLE_MODEL_FIELD_10==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FIELD_10_NAME" class="dyn_full_size model10_on" value="{{$data.MODEL_FIELD_10_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_1}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model_file1" type="checkbox" name="ENABLE_MODEL_FILE_FIELD_1" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FILE_FIELD_1_NAME" class="dyn_full_size model_file1_on" value="{{$data.MODEL_FILE_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_2}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model_file2" type="checkbox" name="ENABLE_MODEL_FILE_FIELD_2" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FILE_FIELD_2_NAME" class="dyn_full_size model_file2_on" value="{{$data.MODEL_FILE_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_3}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model_file3" type="checkbox" name="ENABLE_MODEL_FILE_FIELD_3" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FILE_FIELD_3_NAME" class="dyn_full_size model_file3_on" value="{{$data.MODEL_FILE_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_4}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model_file4" type="checkbox" name="ENABLE_MODEL_FILE_FIELD_4" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FILE_FIELD_4_NAME" class="dyn_full_size model_file4_on" value="{{$data.MODEL_FILE_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_5}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="model_file5" type="checkbox" name="ENABLE_MODEL_FILE_FIELD_5" value="1" {{if $data.ENABLE_MODEL_FILE_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="MODEL_FILE_FIELD_5_NAME" class="dyn_full_size model_file5_on" value="{{$data.MODEL_FILE_FIELD_5_NAME}}"/></td>
						</tr>
					{{/if}}
					{{if $config.installation_type==4}}
						<tr class="eg_group_header">
							<td colspan="4">{{$lang.settings.customization_divider_dvd}}</td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_1}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd1" type="checkbox" name="ENABLE_DVD_FIELD_1" value="1" {{if $data.ENABLE_DVD_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_1_NAME" class="dyn_full_size dvd1_on" value="{{$data.DVD_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_2}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd2" type="checkbox" name="ENABLE_DVD_FIELD_2" value="1" {{if $data.ENABLE_DVD_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_2_NAME" class="dyn_full_size dvd2_on" value="{{$data.DVD_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_3}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd3" type="checkbox" name="ENABLE_DVD_FIELD_3" value="1" {{if $data.ENABLE_DVD_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_3_NAME" class="dyn_full_size dvd3_on" value="{{$data.DVD_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_4}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd4" type="checkbox" name="ENABLE_DVD_FIELD_4" value="1" {{if $data.ENABLE_DVD_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_4_NAME" class="dyn_full_size dvd4_on" value="{{$data.DVD_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_5}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd5" type="checkbox" name="ENABLE_DVD_FIELD_5" value="1" {{if $data.ENABLE_DVD_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_5_NAME" class="dyn_full_size dvd5_on" value="{{$data.DVD_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_6}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd6" type="checkbox" name="ENABLE_DVD_FIELD_6" value="1" {{if $data.ENABLE_DVD_FIELD_6==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_6_NAME" class="dyn_full_size dvd6_on" value="{{$data.DVD_FIELD_6_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_7}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd7" type="checkbox" name="ENABLE_DVD_FIELD_7" value="1" {{if $data.ENABLE_DVD_FIELD_7==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_7_NAME" class="dyn_full_size dvd7_on" value="{{$data.DVD_FIELD_7_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_8}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd8" type="checkbox" name="ENABLE_DVD_FIELD_8" value="1" {{if $data.ENABLE_DVD_FIELD_8==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_8_NAME" class="dyn_full_size dvd8_on" value="{{$data.DVD_FIELD_8_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_9}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd9" type="checkbox" name="ENABLE_DVD_FIELD_9" value="1" {{if $data.ENABLE_DVD_FIELD_9==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_9_NAME" class="dyn_full_size dvd9_on" value="{{$data.DVD_FIELD_9_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_10}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd10" type="checkbox" name="ENABLE_DVD_FIELD_10" value="1" {{if $data.ENABLE_DVD_FIELD_10==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FIELD_10_NAME" class="dyn_full_size dvd10_on" value="{{$data.DVD_FIELD_10_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_1}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_file1" type="checkbox" name="ENABLE_DVD_FILE_FIELD_1" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FILE_FIELD_1_NAME" class="dyn_full_size dvd_file1_on" value="{{$data.DVD_FILE_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_2}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_file2" type="checkbox" name="ENABLE_DVD_FILE_FIELD_2" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FILE_FIELD_2_NAME" class="dyn_full_size dvd_file2_on" value="{{$data.DVD_FILE_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_3}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_file3" type="checkbox" name="ENABLE_DVD_FILE_FIELD_3" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FILE_FIELD_3_NAME" class="dyn_full_size dvd_file3_on" value="{{$data.DVD_FILE_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_4}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_file4" type="checkbox" name="ENABLE_DVD_FILE_FIELD_4" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FILE_FIELD_4_NAME" class="dyn_full_size dvd_file4_on" value="{{$data.DVD_FILE_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_file_field_5}}</td>
							<td>{{$lang.settings.custom_type_file}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_file5" type="checkbox" name="ENABLE_DVD_FILE_FIELD_5" value="1" {{if $data.ENABLE_DVD_FILE_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_FILE_FIELD_5_NAME" class="dyn_full_size dvd_file5_on" value="{{$data.DVD_FILE_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_group_header">
							<td colspan="4">{{$lang.settings.customization_divider_dvd_group}}</td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_1}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_group1" type="checkbox" name="ENABLE_DVD_GROUP_FIELD_1" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_GROUP_FIELD_1_NAME" class="dyn_full_size dvd_group1_on" value="{{$data.DVD_GROUP_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_2}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_group2" type="checkbox" name="ENABLE_DVD_GROUP_FIELD_2" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_GROUP_FIELD_2_NAME" class="dyn_full_size dvd_group2_on" value="{{$data.DVD_GROUP_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_3}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_group3" type="checkbox" name="ENABLE_DVD_GROUP_FIELD_3" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_GROUP_FIELD_3_NAME" class="dyn_full_size dvd_group3_on" value="{{$data.DVD_GROUP_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_4}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_group4" type="checkbox" name="ENABLE_DVD_GROUP_FIELD_4" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_GROUP_FIELD_4_NAME" class="dyn_full_size dvd_group4_on" value="{{$data.DVD_GROUP_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_5}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="dvd_group5" type="checkbox" name="ENABLE_DVD_GROUP_FIELD_5" value="1" {{if $data.ENABLE_DVD_GROUP_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="DVD_GROUP_FIELD_5_NAME" class="dyn_full_size dvd_group5_on" value="{{$data.DVD_GROUP_FIELD_5_NAME}}"/></td>
						</tr>
					{{/if}}
					{{if $config.installation_type>=2}}
						<tr class="eg_group_header">
							<td colspan="4">{{$lang.settings.customization_divider_user}}</td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_1}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user1" type="checkbox" name="ENABLE_USER_FIELD_1" value="1" {{if $data.ENABLE_USER_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_1_NAME" class="dyn_full_size user1_on" value="{{$data.USER_FIELD_1_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_2}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user2" type="checkbox" name="ENABLE_USER_FIELD_2" value="1" {{if $data.ENABLE_USER_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_2_NAME" class="dyn_full_size user2_on" value="{{$data.USER_FIELD_2_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_3}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user3" type="checkbox" name="ENABLE_USER_FIELD_3" value="1" {{if $data.ENABLE_USER_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_3_NAME" class="dyn_full_size user3_on" value="{{$data.USER_FIELD_3_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_4}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user4" type="checkbox" name="ENABLE_USER_FIELD_4" value="1" {{if $data.ENABLE_USER_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_4_NAME" class="dyn_full_size user4_on" value="{{$data.USER_FIELD_4_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_5}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user5" type="checkbox" name="ENABLE_USER_FIELD_5" value="1" {{if $data.ENABLE_USER_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_5_NAME" class="dyn_full_size user5_on" value="{{$data.USER_FIELD_5_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_6}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user6" type="checkbox" name="ENABLE_USER_FIELD_6" value="1" {{if $data.ENABLE_USER_FIELD_6==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_6_NAME" class="dyn_full_size user6_on" value="{{$data.USER_FIELD_6_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_7}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user7" type="checkbox" name="ENABLE_USER_FIELD_7" value="1" {{if $data.ENABLE_USER_FIELD_7==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_7_NAME" class="dyn_full_size user7_on" value="{{$data.USER_FIELD_7_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_8}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user8" type="checkbox" name="ENABLE_USER_FIELD_8" value="1" {{if $data.ENABLE_USER_FIELD_8==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_8_NAME" class="dyn_full_size user8_on" value="{{$data.USER_FIELD_8_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_9}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user9" type="checkbox" name="ENABLE_USER_FIELD_9" value="1" {{if $data.ENABLE_USER_FIELD_9==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_9_NAME" class="dyn_full_size user9_on" value="{{$data.USER_FIELD_9_NAME}}"/></td>
						</tr>
						<tr class="eg_data">
							<td>{{$lang.settings.custom_field_10}}</td>
							<td>{{$lang.settings.custom_type_text}}</td>
							<td><div class="de_vis_sw_checkbox"><input id="user10" type="checkbox" name="ENABLE_USER_FIELD_10" value="1" {{if $data.ENABLE_USER_FIELD_10==1}}checked="checked"{{/if}}/></div></td>
							<td><input type="text" name="USER_FIELD_10_NAME" class="dyn_full_size user10_on" value="{{$data.USER_FIELD_10_NAME}}"/></td>
						</tr>
					{{/if}}
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_referer}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="referer1" type="checkbox" name="ENABLE_REFERER_FIELD_1" value="1" {{if $data.ENABLE_REFERER_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="REFERER_FIELD_1_NAME" class="dyn_full_size referer1_on" value="{{$data.REFERER_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="referer2" type="checkbox" name="ENABLE_REFERER_FIELD_2" value="1" {{if $data.ENABLE_REFERER_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="REFERER_FIELD_2_NAME" class="dyn_full_size referer2_on" value="{{$data.REFERER_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="referer3" type="checkbox" name="ENABLE_REFERER_FIELD_3" value="1" {{if $data.ENABLE_REFERER_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="REFERER_FIELD_3_NAME" class="dyn_full_size referer3_on" value="{{$data.REFERER_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_1}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="referer_file1" type="checkbox" name="ENABLE_REFERER_FILE_FIELD_1" value="1" {{if $data.ENABLE_REFERER_FILE_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="REFERER_FILE_FIELD_1_NAME" class="dyn_full_size referer_file1_on" value="{{$data.REFERER_FILE_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_2}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="referer_file2" type="checkbox" name="ENABLE_REFERER_FILE_FIELD_2" value="1" {{if $data.ENABLE_REFERER_FILE_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="REFERER_FILE_FIELD_2_NAME" class="dyn_full_size referer_file2_on" value="{{$data.REFERER_FILE_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_file_field_3}}</td>
						<td>{{$lang.settings.custom_type_file}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="referer_file3" type="checkbox" name="ENABLE_REFERER_FILE_FIELD_3" value="1" {{if $data.ENABLE_REFERER_FILE_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="REFERER_FILE_FIELD_3_NAME" class="dyn_full_size referer_file3_on" value="{{$data.REFERER_FILE_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_group_header">
						<td colspan="4">{{$lang.settings.customization_divider_feedback}}</td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_1}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="feedback1" type="checkbox" name="ENABLE_FEEDBACK_FIELD_1" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_1==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="FEEDBACK_FIELD_1_NAME" class="dyn_full_size feedback1_on" value="{{$data.FEEDBACK_FIELD_1_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_2}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="feedback2" type="checkbox" name="ENABLE_FEEDBACK_FIELD_2" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_2==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="FEEDBACK_FIELD_2_NAME" class="dyn_full_size feedback2_on" value="{{$data.FEEDBACK_FIELD_2_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_3}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="feedback3" type="checkbox" name="ENABLE_FEEDBACK_FIELD_3" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_3==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="FEEDBACK_FIELD_3_NAME" class="dyn_full_size feedback3_on" value="{{$data.FEEDBACK_FIELD_3_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_4}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="feedback4" type="checkbox" name="ENABLE_FEEDBACK_FIELD_4" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_4==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="FEEDBACK_FIELD_4_NAME" class="dyn_full_size feedback4_on" value="{{$data.FEEDBACK_FIELD_4_NAME}}"/></td>
					</tr>
					<tr class="eg_data">
						<td>{{$lang.settings.custom_field_5}}</td>
						<td>{{$lang.settings.custom_type_text}}</td>
						<td><div class="de_vis_sw_checkbox"><input id="feedback5" type="checkbox" name="ENABLE_FEEDBACK_FIELD_5" value="1" {{if $data.ENABLE_FEEDBACK_FIELD_5==1}}checked="checked"{{/if}}/></div></td>
						<td><input type="text" name="FEEDBACK_FIELD_5_NAME" class="dyn_full_size feedback5_on" value="{{$data.FEEDBACK_FIELD_5_NAME}}"/></td>
					</tr>
					{{foreach name="data" item="item" from=$list_posts_types}}
						<tr class="eg_group_header">
							<td colspan="4">{{$lang.settings.customization_divider_post_type|replace:"%1%":$item.title}}</td>
						</tr>
						{{section name="fields" start="1" loop=11}}
							{{assign var="lang_key" value="custom_field_`$smarty.section.fields.index`"}}
							{{assign var="data_key_enable" value="ENABLE_POST_`$item.post_type_id`_FIELD_`$smarty.section.fields.index`"}}
							{{assign var="data_key_name" value="POST_`$item.post_type_id`_FIELD_`$smarty.section.fields.index`_NAME"}}
							<tr class="eg_data">
								<td>{{$lang.settings.$lang_key}}</td>
								<td>{{$lang.settings.custom_type_text}}</td>
								<td><div class="de_vis_sw_checkbox"><input id="post_{{$item.post_type_id}}{{$smarty.section.fields.index}}" type="checkbox" name="{{$data_key_enable}}" value="1" {{if $data[$data_key_enable]==1}}checked="checked"{{/if}}/></div></td>
								<td><input type="text" name="{{$data_key_name}}" class="dyn_full_size post_{{$item.post_type_id}}{{$smarty.section.fields.index}}_on" value="{{$data[$data_key_name]}}"/></td>
							</tr>
						{{/section}}
						{{section name="fields" start="1" loop=11}}
							{{assign var="lang_key" value="custom_file_field_`$smarty.section.fields.index`"}}
							{{assign var="data_key_enable" value="ENABLE_POST_`$item.post_type_id`_FILE_FIELD_`$smarty.section.fields.index`"}}
							{{assign var="data_key_name" value="POST_`$item.post_type_id`_FILE_FIELD_`$smarty.section.fields.index`_NAME"}}
							<tr class="eg_data">
								<td>{{$lang.settings.$lang_key}}</td>
								<td>{{$lang.settings.custom_type_file}}</td>
								<td><div class="de_vis_sw_checkbox"><input id="post_{{$item.post_type_id}}_file{{$smarty.section.fields.index}}" type="checkbox" name="{{$data_key_enable}}" value="1" {{if $data[$data_key_enable]==1}}checked="checked"{{/if}}/></div></td>
								<td><input type="text" name="{{$data_key_name}}" class="dyn_full_size post_{{$item.post_type_id}}_file{{$smarty.section.fields.index}}_on" value="{{$data[$data_key_name]}}"/></td>
							</tr>
						{{/section}}
					{{/foreach}}
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group"><input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>

{{else}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_personal_setting_complete"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.personal_header}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.personal_divider_user_settings}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_superadmin==1}}
		   <tr>
			   <td class="de_label de_required">{{$lang.settings.personal_field_username}} (*):</td>
			   <td class="de_control"><input type="text" name="login" maxlength="100" class="dyn_full_size" value="{{$personal_data.login}}"/></td>
		   </tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_password}}:</td>
			<td class="de_control">
				<div class="de_passw">
					<input type="text" name="h_pass" value="{{$lang.common.password_hidden}}" maxlength="255" class="dyn_full_size"/>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_password_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_password_confirm}}:</td>
			<td class="de_control">
				<input type="password" name="pass_confirm" maxlength="255" class="dyn_full_size"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_password_confirm_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.personal_field_short_date_format}} (*):</td>
			<td class="de_control">
				<input type="text" name="short_date_format" maxlength="30" class="dyn_full_size" value="{{$personal_data.short_date_format}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_short_date_format_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.personal_field_full_date_format}} (*):</td>
			<td class="de_control">
				<input type="text" name="full_date_format" maxlength="30" class="dyn_full_size" value="{{$personal_data.full_date_format}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_full_date_format_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.personal_field_language}} (*):</td>
			<td class="de_control">
				<select name="lang">
					{{foreach item="item" from=$list_langs|smarty:nodefaults}}
						<option value="{{$item}}" {{if $item==$personal_data.lang}}selected{{/if}}>{{$item|mb_ucfirst}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.personal_field_skin}} (*):</td>
			<td class="de_control">
				<select name="skin">
					{{foreach item="item" from=$list_skins|smarty:nodefaults}}
						<option value="{{$item}}" {{if $item==$personal_data.skin}}selected{{/if}}>{{$item|mb_ucfirst}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_content_scheduler_days}}:</td>
			<td class="de_control">
				<input type="text" name="content_scheduler_days" maxlength="3" class="fixed_50" value="{{$personal_data.content_scheduler_days}}"/>
				<select name="content_scheduler_days_option">
					<option value="0" {{if $personal_data.content_scheduler_days_option=='0'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_content_scheduler_days_last}}</option>
					<option value="1" {{if $personal_data.content_scheduler_days_option=='1'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_content_scheduler_days_next}}</option>
				</select>
				{{$lang.settings.personal_field_content_scheduler_days_period}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_content_scheduler_days_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.personal_field_maximum_thumb_size}}:</td>
			<td class="de_control">
				<input type="text" name="maximum_thumb_size" maxlength="10" class="fixed_100" value="{{$smarty.session.save.options.maximum_thumb_size|default:"150x150"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_maximum_thumb_size_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_default_save_button}}:</td>
			<td class="de_control">
				<select name="default_save_button">
					<option value="0" {{if $smarty.session.save.options.default_save_button=='0'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_default_save_button0}}</option>
					<option value="1" {{if $smarty.session.save.options.default_save_button=='1'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_default_save_button1}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_default_save_button_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_enable_popups}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_popups_enabled" value="1" {{if $personal_data.is_popups_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_enable_popups_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_enable_popups_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_enable_wysiwyg}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_videos" value="1" {{if $personal_data.is_wysiwyg_enabled_videos==1}}checked="checked"{{/if}} {{if $tinymce_enabled!='1'}}disabled="disabled"{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_videos}}</label></div>
						</td>
					</tr>
					{{if in_array('albums|view',$smarty.session.permissions)}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_albums" value="1" {{if $personal_data.is_wysiwyg_enabled_albums==1}}checked="checked"{{/if}} {{if $tinymce_enabled!='1'}}disabled="disabled"{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_albums}}</label></div>
							</td>
						</tr>
					{{/if}}
					{{if in_array('posts|view',$smarty.session.permissions)}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_posts" value="1" {{if $personal_data.is_wysiwyg_enabled_posts==1}}checked="checked"{{/if}} {{if $tinymce_enabled!='1'}}disabled="disabled"{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_posts}}</label></div>
							</td>
						</tr>
					{{/if}}
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_wysiwyg_enabled_other" value="1" {{if $personal_data.is_wysiwyg_enabled_other==1}}checked="checked"{{/if}} {{if $tinymce_enabled!='1'}}disabled="disabled"{{/if}}/><label>{{$lang.settings.personal_field_enable_wysiwyg_enabled_other}}</label></div>
						</td>
					</tr>
				</table>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.personal_field_enable_wysiwyg_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_ip_protection}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_ip_protection_disabled" value="1" {{if $personal_data.is_ip_protection_disabled==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_ip_protection_disabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_ip_protection_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.personal_field_expert_mode}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_expert_mode" value="1" {{if $personal_data.is_expert_mode==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_expert_mode_hide_hints}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="is_hide_forum_hints" value="1" {{if $personal_data.is_hide_forum_hints==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_expert_mode_hide_forum}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.personal_field_expert_mode_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if in_array('videos|view',$smarty.session.permissions)}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.personal_divider_videos_display_settings}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_video_edit_display_mode}}:</td>
				<td class="de_control">
					<select name="video_edit_display_mode">
						<option value="full" {{if $smarty.session.save.options.video_edit_display_mode=='full'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_video_edit_display_mode_full}}</option>
						<option value="descwriter" {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_video_edit_display_mode_descwriter}}</option>
					</select>
					&nbsp;&nbsp;
					{{if in_array('localization|view',$smarty.session.permissions)}}
						<div class="de_lv_pair"><input type="checkbox" name="video_edit_show_translations" value="1" {{if $smarty.session.save.options.video_edit_show_translations==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_video_edit_display_mode_localization}}</label></div>
					{{/if}}
					<div class="de_lv_pair"><input type="checkbox" name="video_edit_show_player" value="1" {{if $smarty.session.save.options.video_edit_show_player==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_video_edit_display_mode_player}}</label></div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_screenshots_on_video_edit}}:</td>
				<td class="de_control">
					<select name="screenshots_on_video_edit">
						<option value="0">{{$lang.settings.personal_field_screenshots_on_video_edit_no}}</option>
						{{foreach item=item from=$list_formats_screenshots_overview|smarty:nodefaults}}
							<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.options.screenshots_on_video_edit==$item.format_screenshot_id}}selected="selected"{{/if}}>{{$lang.settings.personal_field_screenshots_on_video_edit_overview|replace:"%1%":$item.title}}</option>
						{{/foreach}}
						{{foreach item=item from=$list_formats_screenshots_posters|smarty:nodefaults}}
							<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.options.screenshots_on_video_edit==$item.format_screenshot_id}}selected="selected"{{/if}}>{{$lang.settings.personal_field_screenshots_on_video_edit_posters|replace:"%1%":$item.title}}</option>
						{{/foreach}}
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.personal_field_screenshots_on_video_edit_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if in_array('albums|view',$smarty.session.permissions)}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.personal_divider_albums_display_settings}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.personal_field_album_edit_display_mode}}:</td>
				<td class="de_control">
					<select name="album_edit_display_mode">
						<option value="full" {{if $smarty.session.save.options.album_edit_display_mode=='full'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_album_edit_display_mode_full}}</option>
						<option value="descwriter" {{if $smarty.session.save.options.album_edit_display_mode=='descwriter'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_album_edit_display_mode_descwriter}}</option>
					</select>
					{{if in_array('localization|view',$smarty.session.permissions)}}
						&nbsp;&nbsp;
						<div class="de_lv_pair"><input type="checkbox" name="album_edit_show_translations" value="1" {{if $smarty.session.save.options.album_edit_show_translations==1}}checked="checked"{{/if}}/><label>{{$lang.settings.personal_field_album_edit_display_mode_localization}}</label></div>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="images_on_album_edit_no">{{$lang.settings.personal_field_images_on_album_edit}}:</div>
					<div class="de_required {{foreach item=item from=$list_formats_albums|smarty:nodefaults}}images_on_album_edit_{{$item.size}} {{/foreach}}">{{$lang.settings.personal_field_images_on_album_edit}} (*):</div>
				</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="images_on_album_edit" name="images_on_album_edit">
							<option value="no" {{if $smarty.session.save.options.images_on_album_edit=='no'}}selected="selected"{{/if}}>{{$lang.settings.personal_field_images_on_album_edit_no}}</option>
							{{foreach item=item from=$list_formats_albums|smarty:nodefaults}}
								<option value="{{$item.size}}" {{if $smarty.session.save.options.images_on_album_edit==$item.size}}selected="selected"{{/if}}>{{$lang.settings.personal_field_images_on_album_edit_format|replace:"%1%":$item.title}}</option>
							{{/foreach}}
						</select>
						<span class="{{foreach item=item from=$list_formats_albums|smarty:nodefaults}}images_on_album_edit_{{$item.size}} {{/foreach}}">
							<input type="text" name="images_on_album_edit_count" class="{{foreach item=item from=$list_formats_albums|smarty:nodefaults}}images_on_album_edit_{{$item.size}} {{/foreach}} fixed_100" maxlength="2" value="{{$smarty.session.save.options.images_on_album_edit_count}}"/>
						</span>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.settings.personal_field_images_on_album_edit_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>
{{/if}}