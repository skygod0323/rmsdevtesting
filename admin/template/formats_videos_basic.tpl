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
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.post.format_video_id}}"/>
	</div>
	<table class="de {{if $smarty.post.status_id==3}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.format_video_edit|replace:"%1%":$smarty.post.title}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.format_video_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="100" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_postfix}}:</td>
			<td class="de_control">
				<input type="text" name="postfix" maxlength="32" class="dyn_full_size" value="{{$smarty.post.postfix}}" {{if $smarty.post.videos_count>0}}disabled="disabled"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_postfix_hint|replace:"%1%":$allowed_formats}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="de_required resize_option_1">{{$lang.settings.format_video_field_size}} (*):</div>
				<div class="resize_option_2">{{$lang.settings.format_video_field_size}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="resize_option" name="resize_option">
						<option value="1" {{if $smarty.post.resize_option==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_size_resize}}</option>
						<option value="2" {{if $smarty.post.resize_option==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_size_use_source}}</option>
					</select>
					<input type="text" name="size" maxlength="9" class="fixed_100 resize_option_1" value="{{$smarty.post.size}}"/>
					<select name="resize_option2" class="resize_option_1">
						<option value="0" {{if $smarty.post.resize_option2==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_size_keep_source_proportions_width}}</option>
						<option value="2" {{if $smarty.post.resize_option2==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_size_keep_source_proportions_height}}</option>
						<option value="1" {{if $smarty.post.resize_option2==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_size_force_to_size}}</option>
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.common.size_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.format_video_field_ffmpeg_options}} (*):</td>
			<td class="de_control">
				<textarea name="ffmpeg_options" class="dyn_full_size" cols="30" rows="3">{{$smarty.post.ffmpeg_options}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_ffmpeg_options_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_watermark}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/323-how-to-use-custom-watermarks-for-each-video">How to use custom watermarks for each video</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_watermark_image}}:</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.settings.format_video_field_watermark_image}}</span>
						<span class="js_param">accept=png</span>
						{{if $smarty.post.watermark_image_url!=''}}
							<span class="js_param">preview_url={{$smarty.post.watermark_image_url}}</span>
						{{/if}}
					</div>
					<input type="text" name="watermark_image" maxlength="100" class="fixed_400" {{if $smarty.post.watermark_image!=''}}value="{{$smarty.post.watermark_image}}"{{/if}} readonly="readonly"/>
					<input type="hidden" name="watermark_image_hash"/>
					{{if $smarty.post.status_id!=3}}
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $smarty.post.watermark_image==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					{{/if}}
					{{if $smarty.post.watermark_image_url!=''}}
						<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_watermark_image_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="watermark_position_id_0 watermark_position_id_1 watermark_position_id_2 watermark_position_id_3 watermark_position_id_4">{{$lang.settings.format_video_field_watermark_position}}:</div>
				<div class="de_required watermark_position_id_5 watermark_position_id_6 watermark_position_id_7">{{$lang.settings.format_video_field_watermark_position}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="watermark_position_id" name="watermark_position_id">
						<option value="0" {{if $smarty.post.watermark_position_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_random}}</option>
						<option value="1" {{if $smarty.post.watermark_position_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_top_left}}</option>
						<option value="2" {{if $smarty.post.watermark_position_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_top_right}}</option>
						<option value="3" {{if $smarty.post.watermark_position_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_bottom_right}}</option>
						<option value="4" {{if $smarty.post.watermark_position_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_bottom_left}}</option>
						<option value="5" {{if $smarty.post.watermark_position_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_scrolling_top}}</option>
						<option value="6" {{if $smarty.post.watermark_position_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_scrolling_bottom}}</option>
						<option value="7" {{if $smarty.post.watermark_position_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_scrolling_top_bottom}}</option>
					</select>
					<span class="watermark_position_id_5 watermark_position_id_6 watermark_position_id_7">
						&nbsp;&nbsp;
						<select name="watermark_scrolling_direction">
							<option value="0" {{if $smarty.post.watermark_scrolling_direction==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_scrolling_ltr}}</option>
							<option value="1" {{if $smarty.post.watermark_scrolling_direction==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_position_scrolling_rtl}}</option>
						</select>
						&nbsp;
						{{$lang.settings.format_video_field_watermark_position_scrolling_duration}}:
						<input type="text" name="watermark_scrolling_duration" maxlength="10" class="fixed_50" value="{{$smarty.post.watermark_scrolling_duration}}"/>
						&nbsp;
						{{$lang.settings.format_video_field_watermark_position_scrolling_times}}:
						<input type="text" name="watermark_scrolling_times" maxlength="100" class="fixed_150" value="{{$smarty.post.watermark_scrolling_times}}"/>
					</span>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.format_video_field_watermark_position_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_watermark_max_width}}:</td>
			<td class="de_control">
				{{$lang.settings.format_video_field_watermark_max_width_horizontal}}: <input type="text" name="watermark_max_width" maxlength="10" class="fixed_100" value="{{$smarty.post.watermark_max_width}}"/> %
				&nbsp;&nbsp;&nbsp;&nbsp;
				{{$lang.settings.format_video_field_watermark_max_width_vertical}}: <input type="text" name="watermark_max_width_vertical" maxlength="10" class="fixed_100" value="{{$smarty.post.watermark_max_width_vertical}}"/> %
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_watermark_max_width_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_watermark_customize}}:</td>
			<td class="de_control">
				<select name="customize_watermark_id">
					<option value="0" {{if $smarty.post.customize_watermark_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_no}}</option>
					<option value="1" {{if $smarty.post.customize_watermark_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_1_NAME}}</option>
					<option value="2" {{if $smarty.post.customize_watermark_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_2_NAME}}</option>
					<option value="3" {{if $smarty.post.customize_watermark_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_3_NAME}}</option>
					<option value="4" {{if $smarty.post.customize_watermark_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_4_NAME}}</option>
					<option value="5" {{if $smarty.post.customize_watermark_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_5_NAME}}</option>
					<option value="6" {{if $smarty.post.customize_watermark_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_6_NAME}}</option>
					<option value="7" {{if $smarty.post.customize_watermark_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_7_NAME}}</option>
					<option value="8" {{if $smarty.post.customize_watermark_id==8}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_8_NAME}}</option>
					<option value="9" {{if $smarty.post.customize_watermark_id==9}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_9_NAME}}</option>
					<option value="10" {{if $smarty.post.customize_watermark_id==10}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark_customize_option|replace:"%1%":$options.CS_FILE_FIELD_10_NAME}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_watermark_customize_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_watermark2}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/323-how-to-use-custom-watermarks-for-each-video">How to use custom watermarks for each video</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_watermark2_image}}:</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.settings.format_video_field_watermark2_image}}</span>
						<span class="js_param">accept=png</span>
						{{if $smarty.post.watermark2_image_url!=''}}
							<span class="js_param">preview_url={{$smarty.post.watermark2_image_url}}</span>
						{{/if}}
					</div>
					<input type="text" name="watermark2_image" maxlength="100" class="fixed_400" {{if $smarty.post.watermark2_image!=''}}value="{{$smarty.post.watermark2_image}}"{{/if}} readonly="readonly"/>
					<input type="hidden" name="watermark2_image_hash"/>
					{{if $smarty.post.status_id!=3}}
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $smarty.post.watermark2_image==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					{{/if}}
					{{if $smarty.post.watermark2_image_url!=''}}
						<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_watermark2_image_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="watermark2_position_id_0 watermark2_position_id_1 watermark2_position_id_2 watermark2_position_id_3 watermark2_position_id_4">{{$lang.settings.format_video_field_watermark2_position}}:</div>
				<div class="de_required watermark2_position_id_5 watermark2_position_id_6 watermark2_position_id_7">{{$lang.settings.format_video_field_watermark2_position}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="watermark2_position_id" name="watermark2_position_id">
						<option value="0" {{if $smarty.post.watermark2_position_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_random}}</option>
						<option value="1" {{if $smarty.post.watermark2_position_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_top_left}}</option>
						<option value="2" {{if $smarty.post.watermark2_position_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_top_right}}</option>
						<option value="3" {{if $smarty.post.watermark2_position_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_bottom_right}}</option>
						<option value="4" {{if $smarty.post.watermark2_position_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_bottom_left}}</option>
						<option value="5" {{if $smarty.post.watermark2_position_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_scrolling_top}}</option>
						<option value="6" {{if $smarty.post.watermark2_position_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_scrolling_bottom}}</option>
						<option value="7" {{if $smarty.post.watermark2_position_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_scrolling_top_bottom}}</option>
					</select>
					<span class="watermark2_position_id_5 watermark2_position_id_6 watermark2_position_id_7">
						&nbsp;&nbsp;
						<select name="watermark2_scrolling_direction">
							<option value="0" {{if $smarty.post.watermark2_scrolling_direction==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_scrolling_ltr}}</option>
							<option value="1" {{if $smarty.post.watermark2_scrolling_direction==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_position_scrolling_rtl}}</option>
						</select>
						&nbsp;
						{{$lang.settings.format_video_field_watermark2_position_scrolling_duration}}:
						<input type="text" name="watermark2_scrolling_duration" maxlength="10" class="fixed_50" value="{{$smarty.post.watermark2_scrolling_duration}}"/>
						&nbsp;
						{{$lang.settings.format_video_field_watermark2_position_scrolling_times}}:
						<input type="text" name="watermark2_scrolling_times" maxlength="100" class="fixed_150" value="{{$smarty.post.watermark2_scrolling_times}}"/>
					</span>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.format_video_field_watermark2_position_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_watermark2_max_height}}:</td>
			<td class="de_control">
				{{$lang.settings.format_video_field_watermark2_max_height_horizontal}}: <input type="text" name="watermark2_max_height" maxlength="10" class="fixed_100" value="{{$smarty.post.watermark2_max_height}}"/> %
				&nbsp;&nbsp;&nbsp;&nbsp;
				{{$lang.settings.format_video_field_watermark2_max_height_vertical}}: <input type="text" name="watermark2_max_height_vertical" maxlength="10" class="fixed_100" value="{{$smarty.post.watermark2_max_height_vertical}}"/> %
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_watermark2_max_height_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_watermark2_customize}}:</td>
			<td class="de_control">
				<select name="customize_watermark2_id">
					<option value="0" {{if $smarty.post.customize_watermark2_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_no}}</option>
					<option value="1" {{if $smarty.post.customize_watermark2_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_1_NAME}}</option>
					<option value="2" {{if $smarty.post.customize_watermark2_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_2_NAME}}</option>
					<option value="3" {{if $smarty.post.customize_watermark2_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_3_NAME}}</option>
					<option value="4" {{if $smarty.post.customize_watermark2_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_4_NAME}}</option>
					<option value="5" {{if $smarty.post.customize_watermark2_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_5_NAME}}</option>
					<option value="6" {{if $smarty.post.customize_watermark2_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_6_NAME}}</option>
					<option value="7" {{if $smarty.post.customize_watermark2_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_7_NAME}}</option>
					<option value="8" {{if $smarty.post.customize_watermark2_id==8}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_8_NAME}}</option>
					<option value="9" {{if $smarty.post.customize_watermark2_id==9}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_9_NAME}}</option>
					<option value="10" {{if $smarty.post.customize_watermark2_id==10}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_watermark2_customize_option|replace:"%1%":$options.CS_FILE_FIELD_10_NAME}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_watermark2_customize_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_access}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_enable_download}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_download_enabled" value="1" {{if $smarty.post.is_download_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_video_field_enable_download_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_enable_download_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_disable_hotlink_protection}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_hotlink_protection_disabled" value="1" {{if $smarty.post.is_hotlink_protection_disabled==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_video_field_disable_hotlink_protection_disabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_disable_hotlink_protection_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_duration_limitation}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/32-how-to-create-video-previews-on-mouse-over-with-kvs-tube-script">How to create video previews on mouse over</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_limit_duration}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<input type="text" name="limit_total_duration" maxlength="10" class="fixed_100" value="{{$smarty.post.limit_total_duration}}"/>
					<select id="limit_total_duration_unit_id" name="limit_total_duration_unit_id">
						<option value="0" {{if $smarty.post.limit_total_duration_unit_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_seconds}}</option>
						<option value="1" {{if $smarty.post.limit_total_duration_unit_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_percent}}</option>
					</select>
					<span class="limit_total_duration_unit_id_1">
						&nbsp;&nbsp;&nbsp;&nbsp;
						{{$lang.settings.format_video_field_limit_duration_min}}
						<input type="text" name="limit_total_min_duration_sec" maxlength="10" class="fixed_50" value="{{$smarty.post.limit_total_min_duration_sec}}"/>
						{{$lang.settings.format_video_field_limit_duration_seconds}}
						&nbsp;&nbsp;
						{{$lang.settings.format_video_field_limit_duration_max}}
						<input type="text" name="limit_total_max_duration_sec" maxlength="10" class="fixed_50" value="{{$smarty.post.limit_total_max_duration_sec}}"/>
						{{$lang.settings.format_video_field_limit_duration_seconds}}
					</span>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<select name="customize_duration_id">
						<option value="0" {{if $smarty.post.customize_duration_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_no}}</option>
						<option value="1" {{if $smarty.post.customize_duration_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
						<option value="2" {{if $smarty.post.customize_duration_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
						<option value="3" {{if $smarty.post.customize_duration_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
						<option value="4" {{if $smarty.post.customize_duration_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
						<option value="5" {{if $smarty.post.customize_duration_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
						<option value="6" {{if $smarty.post.customize_duration_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
						<option value="7" {{if $smarty.post.customize_duration_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
						<option value="8" {{if $smarty.post.customize_duration_id==8}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
						<option value="9" {{if $smarty.post.customize_duration_id==9}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
						<option value="10" {{if $smarty.post.customize_duration_id==10}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_duration_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_video_field_limit_duration_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_offset_start}}:</td>
			<td class="de_control">
				<input type="text" name="limit_offset_start" maxlength="10" class="fixed_100" value="{{$smarty.post.limit_offset_start}}"/>
				<select name="limit_offset_start_unit_id">
					<option value="0" {{if $smarty.post.limit_offset_start_unit_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_seconds}}</option>
					<option value="1" {{if $smarty.post.limit_offset_start_unit_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_percent}}</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<select name="customize_offset_start_id">
					<option value="0" {{if $smarty.post.customize_offset_start_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_no}}</option>
					<option value="1" {{if $smarty.post.customize_offset_start_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
					<option value="2" {{if $smarty.post.customize_offset_start_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
					<option value="3" {{if $smarty.post.customize_offset_start_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
					<option value="4" {{if $smarty.post.customize_offset_start_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
					<option value="5" {{if $smarty.post.customize_offset_start_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
					<option value="6" {{if $smarty.post.customize_offset_start_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
					<option value="7" {{if $smarty.post.customize_offset_start_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
					<option value="8" {{if $smarty.post.customize_offset_start_id==8}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
					<option value="9" {{if $smarty.post.customize_offset_start_id==9}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
					<option value="10" {{if $smarty.post.customize_offset_start_id==10}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_start_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_offset_start_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_offset_end}}:</td>
			<td class="de_control">
				<input type="text" name="limit_offset_end" maxlength="10" class="fixed_100" value="{{$smarty.post.limit_offset_end}}"/>
				<select name="limit_offset_end_unit_id">
					<option value="0" {{if $smarty.post.limit_offset_end_unit_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_seconds}}</option>
					<option value="1" {{if $smarty.post.limit_offset_end_unit_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_percent}}</option>
				</select>
				&nbsp;&nbsp;&nbsp;&nbsp;
				<select name="customize_offset_end_id">
					<option value="0" {{if $smarty.post.customize_offset_end_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_no}}</option>
					<option value="1" {{if $smarty.post.customize_offset_end_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_1_NAME}}</option>
					<option value="2" {{if $smarty.post.customize_offset_end_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_2_NAME}}</option>
					<option value="3" {{if $smarty.post.customize_offset_end_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_3_NAME}}</option>
					<option value="4" {{if $smarty.post.customize_offset_end_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_4_NAME}}</option>
					<option value="5" {{if $smarty.post.customize_offset_end_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_5_NAME}}</option>
					<option value="6" {{if $smarty.post.customize_offset_end_id==6}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_6_NAME}}</option>
					<option value="7" {{if $smarty.post.customize_offset_end_id==7}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_7_NAME}}</option>
					<option value="8" {{if $smarty.post.customize_offset_end_id==8}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_8_NAME}}</option>
					<option value="9" {{if $smarty.post.customize_offset_end_id==9}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_9_NAME}}</option>
					<option value="10" {{if $smarty.post.customize_offset_end_id==10}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_offset_end_customize_option|replace:"%1%":$options.CS_FIELD_10_NAME}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_offset_end_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_number_of_parts}}:</td>
			<td class="de_control">
				<input type="text" name="limit_number_parts" maxlength="10" class="fixed_100" value="{{$smarty.post.limit_number_parts}}"/>
				<select name="limit_number_parts_crossfade">
					<option value="0" {{if $smarty.post.limit_number_parts_crossfade==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_number_of_parts_crossfade_0}}</option>
					<option value="1" {{if $smarty.post.limit_number_parts_crossfade==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_number_of_parts_crossfade_1}}</option>
					<option value="2" {{if $smarty.post.limit_number_parts_crossfade==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_number_of_parts_crossfade_2}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_number_of_parts_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_last_part_from_end}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="limit_is_last_part_from_end" value="1" {{if $smarty.post.limit_is_last_part_from_end==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_video_field_last_part_from_end_yes}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_last_part_from_end_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_speed_limitation}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/34-how-to-save-bandwidth-with-kvs-tube-script">How to save bandwidth</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_limit_speed_global}}:</td>
			<td class="de_control">
				<table>
					<tr>
						<td>
							<div class="de_vis_sw_select">
								<select name="limit_speed_option" id="limit_speed_option">
									<option value="0" {{if $smarty.post.limit_speed_option==0}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_speed_option_unlimited}}</option>
									<option value="1" {{if $smarty.post.limit_speed_option==1}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_speed_option_fixed}}</option>
									<option value="2" {{if $smarty.post.limit_speed_option==2}}selected="selected"{{/if}}>{{$lang.settings.format_video_field_limit_speed_option_dynamic}}</option>
								</select>
								<input type="text" name="limit_speed_value" maxlength="10" class="fixed_100 limit_speed_option_1 limit_speed_option_2" value="{{$smarty.post.limit_speed_value}}"/>
								<span class="limit_speed_option_1">{{$lang.settings.format_video_field_limit_speed_option_fixed_kbps}}</span>
								<span class="limit_speed_option_2">{{$lang.settings.format_video_field_limit_speed_option_dynamic_mult}}</span>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/>
									<span class="de_hint limit_speed_option_1">{{$lang.settings.format_video_field_limit_speed_option_fixed_hint}}</span>
									<span class="de_hint limit_speed_option_2">{{$lang.settings.format_video_field_limit_speed_option_dynamic_hint}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_limit_speed_countries}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_countries.php</span>
						<span class="js_param">validate_input=true</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=limit_speed_countries[]</span>
						<span class="js_param">empty_message={{$lang.settings.format_video_field_limit_speed_countries_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.limit_speed_countries|smarty:nodefaults}}
						<input type="hidden" name="limit_speed_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_country_{{$index}}" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.settings.format_video_field_limit_speed_countries_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.format_video_field_limit_speed_countries_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.format_video_divider_timeline_screenshots}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.format_video_field_create_timeline_screenshots}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="is_timeline_enabled" id="is_timeline_enabled" value="1" {{if $smarty.post.is_timeline_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_video_field_create_timeline_screenshots_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_create_timeline_screenshots_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="de_required is_timeline_enabled_on">{{$lang.settings.format_video_field_timeline_screenshots_option}} (*):</div>
				<div class="is_timeline_enabled_off">{{$lang.settings.format_video_field_timeline_screenshots_option}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input id="timeline_option_fixed" type="radio" name="timeline_option" class="is_timeline_enabled_on" value="1" {{if $smarty.post.timeline_option==1}}checked="checked"{{/if}}/><span>{{$lang.settings.format_video_field_timeline_screenshots_option_fixed}}</span></div>
								<input type="text" name="timeline_amount" maxlength="10" class="fixed_100 is_timeline_enabled_on timeline_option_fixed" value="{{$smarty.post.timeline_amount}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.format_video_field_timeline_screenshots_option_fixed_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="timeline_option_dynamic" type="radio" name="timeline_option" class="is_timeline_enabled_on" value="2" {{if $smarty.post.timeline_option==2}}checked="checked"{{/if}}/><span>{{$lang.settings.format_video_field_timeline_screenshots_option_dynamic}}</span></div>
								<input type="text" name="timeline_interval" maxlength="10" class="fixed_100 is_timeline_enabled_on timeline_option_dynamic" value="{{$smarty.post.timeline_interval}}"/>
								{{$lang.settings.format_video_field_timeline_screenshots_option_sec}}
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.format_video_field_timeline_screenshots_option_dynamic_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				{{if $smarty.post.timeline_directory!=''}}
					{{$lang.settings.format_video_field_timeline_screenshots_directory}}:
				{{else}}
					<div class="de_required is_timeline_enabled_on">{{$lang.settings.format_video_field_timeline_screenshots_directory}} (*):</div>
					<div class="is_timeline_enabled_off">{{$lang.settings.format_video_field_timeline_screenshots_directory}}:</div>
				{{/if}}
			</td>
			<td class="de_control">
				<input type="text" name="timeline_directory" maxlength="32" class="dyn_full_size {{if $smarty.post.timeline_directory==''}}is_timeline_enabled_on{{/if}}" {{if $smarty.post.timeline_directory!=''}}disabled="disabled"{{/if}} value="{{$smarty.post.timeline_directory}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.format_video_field_timeline_screenshots_directory_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>