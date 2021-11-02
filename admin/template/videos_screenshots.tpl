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

{{if in_array('videos|manage_screenshots',$smarty.session.permissions)}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}
{{if in_array('videos|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_screenshots"/>
		<input type="hidden" name="item_id" value="{{$data_video.video_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a> / <a href="videos.php?action=change&amp;item_id={{$data_video.video_id}}">{{if $data_video.title!=''}}{{$lang.videos.video_edit|replace:"%1%":$data_video.title}}{{else}}{{$lang.videos.video_edit|replace:"%1%":$data_video.video_id}}{{/if}}</a> / {{$lang.videos.screenshots_header_mgmt}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.screenshots_mgmt_field_display}}:</td>
			<td class="de_control">
				<select id="group_id" name="group_id">
					<option value="1" {{if $group_id==1}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_group_overview}} ({{$overview_amount}})</option>
					<option value="2" {{if $group_id==2}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_group_timeline}} ({{$timeline_amount}})</option>
					<option value="3" {{if $group_id==3}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_group_posters}} ({{$poster_amount}})</option>
				</select>
				<span id="overview_formats" class="hidden">
					&nbsp;
					<select id="overview_format_id" name="overview_format_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						<option value="sources" {{if $smarty.session.save.$page_name.overview_format_id=='sources'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_format_sources}}</option>
						{{foreach item=item from=$list_formats_overview|smarty:nodefaults}}
							<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.$page_name.overview_format_id==$item.format_screenshot_id}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</span>
				<span id="timeline_video_formats" class="hidden">
					&nbsp;
					<select id="timeline_video_format_id" name="timeline_video_format_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						{{foreach item=item from=$list_formats_videos_timelined|smarty:nodefaults}}
							<option value="{{$item.format_video_id}}" {{if $smarty.session.save.$page_name.timeline_video_format_id==$item.format_video_id}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</span>
				<span id="timeline_formats" class="hidden">
					&nbsp;
					<select id="timeline_format_id" name="timeline_format_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						<option value="sources" {{if $smarty.session.save.$page_name.timeline_format_id=='sources'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_format_sources}}</option>
						{{foreach item=item from=$list_formats_timeline|smarty:nodefaults}}
							<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.$page_name.timeline_format_id==$item.format_screenshot_id}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</span>
				<span id="poster_formats" class="hidden">
					&nbsp;
					<select id="poster_format_id" name="poster_format_id">
						<option value="">{{$lang.common.select_default_option}}</option>
						<option value="sources" {{if $smarty.session.save.$page_name.poster_format_id=='sources'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_format_sources}}</option>
						{{foreach item=item from=$list_formats_posters|smarty:nodefaults}}
							<option value="{{$item.format_screenshot_id}}" {{if $smarty.session.save.$page_name.poster_format_id==$item.format_screenshot_id}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</span>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.screenshots_mgmt_field_display_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $screen_amount>0}}
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_mgmt_field_source_zip_file}}:</td>
				<td class="de_control">
					<a href="?action=sources_zip&amp;item_id={{$data_video.video_id}}">{{$data_video.dir|default:$data_video.video_id}}-sources.zip</a>
				</td>
			</tr>
		{{/if}}
		{{if ($group_id==1 || $group_id==3) && $can_edit_all==1}}
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_mgmt_field_replace}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.screenshots_mgmt_field_replace}}</span>
							<span class="js_param">accept={{$config.jpeg_image_or_group_allowed_ext}}</span>
							<span class="js_param">multiple=true</span>
						</div>
						<input type="text" name="replace_screenshots" maxlength="100" class="fixed_500" readonly="readonly"/>
						<input type="hidden" name="replace_screenshots_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.screenshots_mgmt_field_replace_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $group_id==1 && $grabbing_possible==1}}
				<tr>
					<td class="de_label">{{$lang.videos.screenshots_mgmt_field_manual_grabbing}}:</td>
					<td class="de_control">
						<a href="videos_screenshots_grabbing.php?item_id={{$data_video.video_id}}">{{$lang.videos.screenshots_mgmt_field_manual_grabbing_link}}</a>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2">
				<div>
					{{if $group_id==1}}
						{{if $format_id=='sources'}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_overview_sources}}
						{{else}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_overview|replace:"%1%":$data_format.size}}
						{{/if}}
					{{elseif $group_id==2}}
						{{if $format_id=='sources'}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_timeline_sources|replace:"%1%":$timeline_video_format_title}}
						{{else}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_timeline|replace:"%1%":$data_format.size|replace:"%2%":$timeline_video_format_title}}
						{{/if}}
					{{elseif $group_id==3}}
						{{if $format_id=='sources'}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_posters_sources}}
						{{else}}
							{{$lang.videos.screenshots_mgmt_divider_screenshots_posters|replace:"%1%":$data_format.size}}
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $screen_amount>0}}
			{{if $group_id==1 || $group_id==3}}
				<tr>
					<td class="de_label" colspan="2">
						{{$lang.videos.screenshots_mgmt_field_display_mode}}:
						<select id="screenshots_display_mode">
							<option value="full" {{if $smarty.session.save.options.screenshots_display_mode=='full'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_mode_full}}</option>
							<option value="basic" {{if $smarty.session.save.options.screenshots_display_mode=='basic'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_display_mode_basic}}</option>
						</select>
						&nbsp;&nbsp;
						{{$lang.videos.screenshots_mgmt_field_click_mode}}:
						<select id="screenshots_click_mode">
							<option value="viewer" {{if $smarty.session.save.options.screenshots_click_mode=='viewer'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_click_mode_viewer}}</option>
							<option value="select" {{if $smarty.session.save.options.screenshots_click_mode=='select'}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_click_mode_select}}</option>
						</select>
						{{if $can_edit_all==1}}
							&nbsp;&nbsp;
							<div class="de_lv_pair"><input id="delete_all" type="checkbox" name="delete_all" autocomplete="off"/><label>{{$lang.videos.screenshots_mgmt_field_select_all}}</label></div>
							<div class="de_lv_pair"><input id="delete_do_not_fade" type="checkbox" name="delete_do_not_fade" autocomplete="off" value="1" {{if $smarty.session.save.options.screenshots_select_fade_disabled=='1'}}checked="checked"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_select_do_not_fade}}</label></div>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_control" colspan="2">
					<div id="screenshots_container" class="de_img_list_preview de_img_list_delete_on_selection">
						<div class="js_params de_img_list_preview_callbacks">
							<span class="js_param">imageListPreviewHook=call</span>
						</div>
						<div class="de_img_list">
							{{assign var="pos" value=1}}
							{{section name="screenshots" start="0" step="1" loop=$screen_amount}}
								<div id="item_{{$pos}}" class="de_img_list_item {{if $screen_main==$pos}}main{{/if}}">
									<a class="de_img_list_thumb" id="link_{{$pos}}" href="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}">
										{{if $group_id==1}}
											{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_auto}}
											{{if $screenshots_data[$pos].type=='uploaded'}}
												{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_uploaded}}
											{{/if}}
											{{if $screen_url}}
												<img src="{{$screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}, {{$screenshot_type}}"/>
											{{else}}
												<img src="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}"/>
											{{/if}}
											<i>{{$screenshot_type}}</i>
										{{elseif $group_id==2}}
											{{if $screen_url}}
												<img src="{{$screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{if $timeline_titles[$pos].text}}{{$timeline_titles[$pos].text}}{{else}}{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}{{/if}}"/>
											{{else}}
												<img src="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}" alt="{{if $timeline_titles[$pos].text}}{{$timeline_titles[$pos].text}}{{else}}{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}{{/if}}"/>
											{{/if}}
											<i>{{$lang.videos.screenshots_mgmt_field_type_auto}}</i>
										{{elseif $group_id==3}}
											{{if $screen_url}}
												<img src="{{$screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}"/>
											{{else}}
												<img src="?action=source&amp;item_id={{$data_video.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$screen_amount}}"/>
											{{/if}}
											<i>{{$lang.videos.screenshots_mgmt_field_type_uploaded}}</i>
										{{/if}}
									</a>
									{{if $can_edit_all==1}}
										{{if $group_id==1 || $group_id==3}}
											<div class="de_img_list_options">
												<div class="de_fu">
													<div class="js_params">
														<span class="js_param">title={{if $group_id==1}}{{$lang.videos.screenshots_mgmt_file_title_screenshot|replace:"%1%":$pos}}{{elseif $group_id==3}}{{$lang.videos.screenshots_mgmt_file_title_poster|replace:"%1%":$pos}}{{/if}}</span>
														<span class="js_param">accept=jpg</span>
													</div>
													<input type="text" class="fixed_100" maxlength="100" name="file_{{$pos}}" readonly="readonly"/>
													<input type="hidden" name="file_{{$pos}}_hash"/>
													<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_replace}}"/>
												</div>
											</div>
											<div class="de_img_list_options basic">
												<div class="de_lv_pair"><input type="radio" id="main_{{$pos}}" name="main" value="{{$pos}}" {{if $screen_main==$pos}}checked="checked"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_main}}</label></div>
												<div class="de_lv_pair"><input type="checkbox" id="delete_{{$pos}}" name="delete[]" value="{{$pos}}" autocomplete="off"/><label>{{$lang.videos.screenshots_mgmt_field_delete}}</label></div>
											</div>
											{{if is_array($rotator_data)}}
												<div class="de_img_list_options">
													<span>
														{{$lang.videos.screenshots_mgmt_field_ctr}}: {{$rotator_data[$pos].ctr|default:0|number_format:2}}
														&nbsp;/&nbsp;
														{{$lang.videos.screenshots_mgmt_field_clicks}}: {{$rotator_data[$pos].clicks|default:0}}
													</span>
												</div>
											{{/if}}
										{{elseif $group_id==2}}
											<div class="de_img_list_options">
												<input type="text" id="title_{{$pos}}" name="title_{{$pos}}" class="dyn_full_size" size="10" value="{{$timeline_titles[$pos].text}}"/>
											</div>
										{{/if}}
									{{/if}}
								</div>
								{{assign var="pos" value=$pos+1}}
							{{/section}}
						</div>
					</div>
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_control" colspan="2">
					{{if $group_id==2}}
						{{$lang.videos.screenshots_mgmt_divider_screenshots_timeline_none}}
					{{elseif $group_id==3}}
						{{$lang.videos.screenshots_mgmt_divider_screenshots_posters_none}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.videos.screenshots_mgmt_divider_video_data}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_mgmt_field_status}}:</td>
				<td class="de_control">
					{{if in_array('videos|edit_status',$smarty.session.permissions) || in_array('videos|edit_all',$smarty.session.permissions)}}
						<div class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $data_video.status_id=='1'}}checked="checked"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_status_active}}</label></div>
					{{else}}
						{{if $data_video.status_id==0}}{{$lang.videos.screenshots_mgmt_field_status_disabled}}{{elseif $data_video.status_id==1}}{{$lang.videos.screenshots_mgmt_field_status_active}}{{/if}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.screenshots_mgmt_field_admin_flag}}:</td>
				<td class="de_control">
					{{if in_array('videos|edit_admin_flag',$smarty.session.permissions) || in_array('videos|edit_all',$smarty.session.permissions)}}
						<select name="admin_flag_id">
							<option value="0" {{if 0==$data_video.admin_flag_id}}selected="selected"{{/if}}>{{$lang.videos.screenshots_mgmt_field_admin_flag_reset}}</option>
							{{foreach name=data item=item from=$list_flags_admins|smarty:nodefaults}}
								<option value="{{$item.flag_id}}" {{if $item.flag_id==$data_video.admin_flag_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					{{else}}
						{{if $data_video.admin_flag_id>0}}
							{{foreach name=data item=item from=$list_flags_admins|smarty:nodefaults}}
								{{if $item.flag_id==$data_video.admin_flag_id}}{{$item.title}}{{/if}}
							{{/foreach}}
						{{else}}
							{{$lang.videos.screenshots_mgmt_field_admin_flag_reset}}
						{{/if}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					{{if $smarty.session.save.options.default_save_button==1}}
						<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					{{else}}
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
						<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					{{/if}}
					{{if $can_delete}}
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="submit" name="delete_and_edit" value="{{$lang.videos.screenshots_mgmt_btn_delete_and_edit_next}}" class="de_confirm" alt="{{$lang.videos.screenshots_mgmt_btn_delete_and_edit_next_confirm}}"/>
					{{/if}}
				</td>
			</tr>
		{{/if}}
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildScreenshotsFormatLogic=call({{$data_video.video_id}})</span>
	<span class="js_param">buildScreenshotsDeleteLogic=call()</span>
</div>