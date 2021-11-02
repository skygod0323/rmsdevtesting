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
		<input type="hidden" name="item_id" value="{{$data_video.video_id}}"/>
		<input type="hidden" name="action" value="start_grabbing"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a> / <a href="videos.php?action=change&amp;item_id={{$data_video.video_id}}">{{if $data_video.title!=''}}{{$lang.videos.video_edit|replace:"%1%":$data_video.title}}{{else}}{{$lang.videos.video_edit|replace:"%1%":$data_video.video_id}}{{/if}}</a> / <a href="videos_screenshots.php?item_id={{$data_video.video_id}}">{{$lang.videos.screenshots_header_mgmt}}</a> / {{$lang.videos.screenshots_header_grabbing}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.screenshots_grabbing_field_grab_from}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="source_file_id" id="source_file_id">
						{{if $source_file!=''}}
							<option value="" {{if $smarty.session.save.$page_name.source_file_id==0}}selected="selected"{{/if}}>{{$lang.videos.screenshots_grabbing_field_grab_from_source_file}} [{{$source_file.dimensions.0}}x{{$source_file.dimensions.1}}, {{$source_file.duration_string}}]</option>
						{{elseif ($data_video.load_type_id==2 || $data_video.load_type_id==3) && $data_video.file_url!=''}}
							<option value="" {{if $smarty.session.save.$page_name.source_file_id==0}}selected="selected"{{/if}}>{{$lang.videos.screenshots_grabbing_field_grab_from_source_file}} ({{$lang.videos.screenshots_grabbing_field_grab_from_download}})</option>
						{{/if}}
						{{assign var="timelined_format_classes" value=""}}
						{{foreach item=item from=$formats|smarty:nodefaults}}
							<option value="{{$item.format_video_id}}" {{if $smarty.session.save.$page_name.source_file_id==$item.format_video_id}}selected="selected"{{/if}}>{{$item.title}} [{{$item.dimensions.0}}x{{$item.dimensions.1}}, {{$item.duration_string}}{{if $item.timeline_screen_amount>0}}, {{$lang.videos.screenshots_grabbing_field_grab_from_timelines|replace:"%1%":$item.timeline_screen_amount}}{{/if}}]</option>
							{{if $item.timeline_screen_amount>0}}
								{{assign var="timelined_format_classes" value="`$timelined_format_classes` source_file_id_`$item.format_video_id`"}}
							{{/if}}
						{{/foreach}}
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.screenshots_grabbing_field_method}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td><div class="de_lv_pair"><input id="method_use_timelines" type="radio" name="method" value="1" class="{{$timelined_format_classes}}" {{if $smarty.session.save.$page_name.method==1}}checked="checked"{{/if}}/><label class="{{$timelined_format_classes}}">{{$lang.videos.screenshots_grabbing_field_method_use_timeline_screenshots}}</label></div></td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="method_new_screenshots" type="radio" name="method" value="2" {{if $smarty.session.save.$page_name.method==2}}checked="checked"{{/if}}/><label>{{$lang.videos.screenshots_grabbing_field_method_new_screenshots}}:</label></div><input class="method_new_screenshots" type="text" name="interval" maxlength="32" size="3" value="{{$smarty.session.save.$page_name.interval}}"/> {{$lang.common.second_truncated}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.screenshots_grabbing_field_screenshots_crop}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							{{$lang.videos.screenshots_grabbing_field_screenshots_crop_left}}:
							<input type="text" name="screenshots_crop_left" maxlength="1000" class="fixed_50" value="{{$smarty.session.save.$page_name.screenshots_crop_left}}"/>
							<select name="screenshots_crop_left_unit">
								<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_left_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_left_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							&nbsp;
							{{$lang.videos.screenshots_grabbing_field_screenshots_crop_top}}:
							<input type="text" name="screenshots_crop_top" maxlength="1000" class="fixed_50" value="{{$smarty.session.save.$page_name.screenshots_crop_top}}"/>
							<select name="screenshots_crop_top_unit">
								<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_top_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_top_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							&nbsp;
							{{$lang.videos.screenshots_grabbing_field_screenshots_crop_right}}:
							<input type="text" name="screenshots_crop_right" maxlength="1000" class="fixed_50" value="{{$smarty.session.save.$page_name.screenshots_crop_right}}"/>
							<select name="screenshots_crop_right_unit">
								<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_right_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_right_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
							&nbsp;
							{{$lang.videos.screenshots_grabbing_field_screenshots_crop_bottom}}:
							<input type="text" name="screenshots_crop_bottom" maxlength="1000" class="fixed_50" value="{{$smarty.session.save.$page_name.screenshots_crop_bottom}}"/>
							<select name="screenshots_crop_bottom_unit">
								<option value="1" {{if $smarty.session.save.$page_name.screenshots_crop_bottom_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
								<option value="2" {{if $smarty.session.save.$page_name.screenshots_crop_bottom_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="screenshots_crop_trim" value="0"/>
							<div class="de_lv_pair"><input type="checkbox" name="screenshots_crop_trim" value="1" {{if $smarty.session.save.$page_name.screenshots_crop_trim==1}}checked="checked"{{/if}}/><label>{{$lang.videos.screenshots_grabbing_field_screenshots_crop_trim}}</label></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.screenshots_grabbing_field_screenshots_offset}}:</td>
			<td class="de_control">
				<input id="display_size" type="text" name="screenshots_offset" maxlength="9" size="9" value="{{$smarty.session.save.$page_name.screenshots_offset|default:"0"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.screenshots_grabbing_field_screenshots_offset_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.screenshots_grabbing_field_screenshots_slow_method}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="slow_method" value="1"/><label>{{$lang.videos.screenshots_grabbing_field_screenshots_slow_method_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.screenshots_grabbing_field_screenshots_slow_method_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.screenshots_grabbing_field_display_size}}:</td>
			<td class="de_control">
				<input id="display_size" type="text" name="display_size" maxlength="9" size="9" value="{{$smarty.session.save.$page_name.display_size}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.screenshots_grabbing_field_display_size_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<input type="submit" {{if $data_amount==0}}name="save_default"{{/if}} value="{{$lang.videos.screenshots_grabbing_btn_start}}"/>
			</td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildScreenshotsGrabbingLogic=call()</span>
</div>

{{if $smarty.get.action=='grabbing_complete'}}
<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="save_screenshots"/>
		<input type="hidden" name="item_id" value="{{$data_video.video_id}}"/>
		<input type="hidden" name="grabbing_id" value="{{$smarty.request.grabbing_id}}"/>
		<input type="hidden" name="data_amount" value="{{$data_amount}}"/>
	</div>
	<table class="de">
		<tr>
			<td class="de_header"><div><a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a> / <a href="videos.php?action=change&amp;item_id={{$data_video.video_id}}">{{if $data_video.title!=''}}{{$lang.videos.video_edit|replace:"%1%":$data_video.title}}{{else}}{{$lang.videos.video_edit|replace:"%1%":$data_video.video_id}}{{/if}}</a> / <a href="videos_screenshots.php?item_id={{$data_video.video_id}}">{{$lang.videos.screenshots_header_mgmt}}</a> / {{$lang.videos.screenshots_header_grabbing_images}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text">
					<span class="de_hint">{{$lang.videos.screenshots_header_grabbing_images_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_control">
				<div class="de_img_list">
					{{assign var="pos" value=0}}
					{{section name="screenshots" start="0" step="1" loop=$data_amount}}
						<div class="de_img_list_item">
							<a id="image_cell_{{$pos}}" class="de_img_list_thumb">
								<img id="image_{{$pos}}" src="{{$data[$pos]}}?rnd={{$smarty.now}}" alt="" style="cursor: pointer"/>
								<i></i>
							</a>
							<div class="de_img_list_options">
								<select id="save_as_screenshot_{{$pos}}" name="save_as_screenshot_{{$pos}}">
									<option value="">{{$lang.videos.screenshots_grabbing_images_field_save_as}}</option>
									{{section name="sp" start="0" step="1" loop=$data_video.screen_amount}}
										<option value="{{$smarty.section.sp.iteration}}">{{$lang.videos.screenshots_grabbing_images_field_save_as_screenshot|replace:"%1%":$smarty.section.sp.iteration}}</option>
									{{/section}}
									<option value="new">{{$lang.videos.screenshots_grabbing_images_field_save_as_new_screenshot}}</option>
								</select>
							</div>
						</div>
						{{assign var="pos" value=$pos+1}}
					{{/section}}
				</div>
			</td>
		</tr>
		{{if $data_amount>0}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>
{{/if}}