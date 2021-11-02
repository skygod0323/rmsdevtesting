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
		{{if $smarty.request.page=='embed'}}
			{{assign var="player_path" value="`$config.content_url_other`/player/embed"}}
			<input type="hidden" name="is_embed" value="1"/>
			{{if $smarty.request.embed_profile_id!=''}}
				<input type="hidden" name="embed_profile_id" value="{{$smarty.request.embed_profile_id}}"/>
				{{if $smarty.request.embed_profile_id!='new'}}
					{{assign var="player_path" value=$smarty.request.embed_profile_id|md5}}
					{{assign var="player_path" value="`$config.content_url_other`/player/embed/`$player_path`"}}
				{{/if}}
			{{/if}}
		{{else}}
			{{if $player_data.access_level==0}}
				{{assign var="player_path" value="`$config.content_url_other`/player"}}
			{{elseif $player_data.access_level==2}}
				{{assign var="player_path" value="`$config.content_url_other`/player/active"}}
			{{elseif $player_data.access_level==3}}
				{{assign var="player_path" value="`$config.content_url_other`/player/premium"}}
			{{/if}}
		{{/if}}
		<input type="hidden" name="page" value="{{$smarty.request.page}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{if $smarty.request.page=='embed'}}{{$lang.settings.player_embed_header}}{{else}}{{$lang.settings.player_header}}{{/if}}</div></td>
		</tr>
		{{if $smarty.request.page=='embed'}}
			{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/416-how-to-disable-embedding-your-content-on-other-sites-and-redirect-embeds-to-your-site">How to disable embedding your content on other sites and redirect embeds to your site</a></span><br/>
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1198-how-to-allow-embedding-your-videos-from-a-whitelisted-set-of-sites">How to allow embedding your videos from a whitelisted set of sites</a></span><br/>
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1008-theme-customization-how-to-build-embed-code-for-video-playlist">How to build embed code for video playlist</a></span><br/>
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/34-how-to-save-bandwidth-with-kvs-tube-script">How to save bandwidth from embed codes</a></span><br/>
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/829-how-to-maximize-your-tube-revenue-with-kvs-advertising-system">How to maximize your tube revenue with KVS advertising system</a></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.player_field_embed_profile}}:</td>
				<td class="de_control">
					<select id="embed_profile_id" name="embed_profile_id">
						<option value="">{{$lang.settings.player_field_embed_profile_default}}</option>
						{{foreach key=key item=item from=$list_embed_profiles|smarty:nodefaults}}
							<option value="{{$key}}" {{if $smarty.get.embed_profile_id==$key}}selected="selected"{{/if}}>{{$item.embed_profile_name}}</option>
						{{/foreach}}
						<option value="new" {{if $smarty.get.embed_profile_id=='new'}}selected="selected"{{/if}}>{{$lang.settings.player_field_embed_profile_new}}</option>
					</select>
					{{if $smarty.get.embed_profile_id!='' && $smarty.get.embed_profile_id!='new'}}
						<input type="submit" id="delete_profile" name="delete_profile" value="{{$lang.settings.player_btn_delete_profile}}"/>
					{{/if}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_embed_profile_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.player_field_settings_applied_for}}:</td>
				<td class="de_control">
					{{if count($list_embed_profiles)>0}}
						{{if $smarty.get.embed_profile_id==''}}
							{{$lang.settings.player_field_embed_profile_default}},
						{{else}}
							<span class="disabled"><s>{{$lang.settings.player_field_embed_profile_default}}</s></span>,
						{{/if}}
						{{foreach key=key item=item name=data from=$list_embed_profiles|smarty:nodefaults}}
							{{if $smarty.get.embed_profile_id==$key}}
								{{$item.embed_profile_name}}{{if !$smarty.foreach.data.last}},{{/if}}
							{{else}}
								<span class="disabled"><s>{{$item.embed_profile_name}}</s></span>{{if !$smarty.foreach.data.last}},{{/if}}
							{{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.get.embed_profile_id!=''}}
				<tr>
					<td class="de_label de_required">{{$lang.settings.player_field_embed_profile_name}} (*):</td>
					<td class="de_control">
						<input type="text" name="embed_profile_name" maxlength="100" class="dyn_full_size" value="{{$player_data.embed_profile_name}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.player_field_embed_profile_name_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label de_required">{{$lang.settings.player_field_embed_profile_domains}} (*):</td>
					<td class="de_control">
						<textarea name="embed_profile_domains" class="dyn_full_size" cols="40" rows="3">{{$player_data.embed_profile_domains}}</textarea>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.player_field_embed_profile_domains_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		{{elseif $config.installation_type>=2}}
			{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/829-how-to-maximize-your-tube-revenue-with-kvs-advertising-system">How to maximize your tube revenue with KVS advertising system</a></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.player_field_access_level}}:</td>
				<td class="de_control">
					<select id="access_level" name="access_level">
						<option value="0" {{if $player_data.access_level==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_access_level_default}}</option>
						<option value="2" {{if $player_data.access_level==2}}selected="selected"{{/if}}>{{$lang.settings.player_field_access_level_member}}</option>
						<option value="3" {{if $player_data.access_level==3}}selected="selected"{{/if}}>{{$lang.settings.player_field_access_level_premium}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_access_level_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $player_data.access_level!=0}}
				<tr>
					<td class="de_label">{{$lang.settings.player_field_overwrite_settings}}:</td>
					<td class="de_control">
						<div class="de_lv_pair de_vis_sw_checkbox"><input id="overwrite_settings" type="checkbox" name="overwrite_settings" value="1" {{if $player_data.no_settings!=1}}checked="checked"{{/if}}/><span {{if $player_data.no_settings!=1}}class="selected"{{/if}}>{{$lang.settings.player_field_overwrite_settings_yes}}</span></div>
					</td>
				</tr>
			{{/if}}
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_settings_applied_for}}:</td>
				<td class="de_control">
					{{if in_array(1,$applied)}}
						{{$lang.settings.player_field_access_level_unknown}},
					{{else}}
						<span class="disabled"><s>{{$lang.settings.player_field_access_level_unknown}}</s></span>,
					{{/if}}
					{{if in_array(2,$applied)}}
						{{$lang.settings.player_field_access_level_member}},
					{{else}}
						<span class="disabled"><s>{{$lang.settings.player_field_access_level_member}}</s></span>,
					{{/if}}
					{{if in_array(3,$applied)}}
						{{$lang.settings.player_field_access_level_premium}}
					{{else}}
						<span class="disabled"><s>{{$lang.settings.player_field_access_level_premium}}</s></span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_general_settings}}</div></td>
		</tr>
		{{if $smarty.request.page=='embed'}}
			<tr class="overwrite_settings_on">
				<td class="de_label de_required">{{$lang.settings.player_field_embed_template}} (*):</td>
				<td class="de_control">
					<textarea name="embed_template" class="html_code_editor dyn_full_size" rows="10" cols="40">{{$player_data.embed_template}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_embed_template_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_error_template}}:</td>
				<td class="de_control">
					<textarea name="error_template" class="html_code_editor dyn_full_size" rows="10" cols="40">{{$player_data.error_template}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_error_template_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="overwrite_settings_on">
				<td class="de_label de_required">{{$lang.settings.player_field_embed_cache_time}} (*):</td>
				<td class="de_control">
					<input type="text" name="embed_cache_time" maxlength="10" class="fixed_100" value="{{$player_data.embed_cache_time|default:"86400"}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_embed_cache_time_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="overwrite_settings_on">
			<td class="de_label de_required">{{$lang.settings.player_field_size}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					{{if $smarty.request.page=='embed'}}
						<select id="embed_size_option" name="embed_size_option">
							<option value="0" {{if $player_data.embed_size_option==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_size_embed_as_video}}</option>
							<option value="1" {{if $player_data.embed_size_option==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_size_embed_as_options}}</option>
						</select>
					{{/if}}
					<input type="text" name="width" maxlength="5" class="fixed_100 embed_size_option_1" value="{{$player_data.width}}"/>
					x
					<input type="text" name="height" maxlength="5" class="fixed_100 embed_size_option_1" value="{{$player_data.height}}"/>
					<select name="height_option" class="embed_size_option_1">
						<option value="0" {{if $player_data.height_option==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_size_height_dynamic}}</option>
						<option value="1" {{if $player_data.height_option==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_size_height_fixed}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.request.page=='embed'}}
							<span class="de_hint">{{$lang.settings.player_field_size_hint_embed}}</span>
						{{else}}
							<span class="de_hint">{{$lang.settings.player_field_size_hint}}</span>
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.request.page!='embed' && $player_data.access_level==0}}
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_adjust_embed_codes}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="adjust_embed_codes" value="1" {{if $player_data.adjust_embed_codes==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_adjust_embed_codes_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_adjust_embed_codes_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_skin}}:</td>
			<td class="de_control">
				<select name="skin">
					{{assign var="found_skin" value="false"}}
					{{foreach item="item" from=$list_skins|smarty:nodefaults}}
						<option value="{{$item}}" {{if $player_data.skin==$item}}{{assign var="found_skin" value="true"}}selected="selected"{{/if}}>{{$item}}</option>
					{{/foreach}}
					{{if $found_skin=='false'}}
						<option value="{{$player_data.skin}}" selected="selected">{{$lang.settings.player_field_skin_missing}}</option>
					{{/if}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_skin_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_controlbar_mode}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
				<select id="controlbar" name="controlbar">
					<option value="0" {{if $player_data.controlbar==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_controlbar_mode_show_always}}</option>
					<option value="1" {{if $player_data.controlbar==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_controlbar_mode_autohide}}</option>
					<option value="2" {{if $player_data.controlbar==2}}selected="selected"{{/if}}>{{$lang.settings.player_field_controlbar_mode_hide_always}}</option>
				</select>
				<select name="controlbar_hide_style" class="controlbar_1">
					<option value="0" {{if $player_data.controlbar_hide_style==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_controlbar_mode_hide_move}}</option>
					<option value="1" {{if $player_data.controlbar_hide_style==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_controlbar_mode_hide_fade}}</option>
				</select>
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_preload}}:</td>
			<td class="de_control">
				<select name="preload_metadata">
					<option value="0" {{if $player_data.preload_metadata==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_preload_none}}</option>
					<option value="1" {{if $player_data.preload_metadata==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_preload_metadata}}</option>
					<option value="2" {{if $player_data.preload_metadata==2}}selected="selected"{{/if}}>{{$lang.settings.player_field_preload_auto}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_preload_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_volume}}:</td>
			<td class="de_control">
				<select name="volume">
					<option value="muted" {{if $player_data.volume=='muted'}}selected="selected"{{/if}}>{{$lang.settings.player_field_volume_muted}}</option>
					<option value="0.1" {{if $player_data.volume=='0.1'}}selected="selected"{{/if}}>10%</option>
					<option value="0.2" {{if $player_data.volume=='0.2'}}selected="selected"{{/if}}>20%</option>
					<option value="0.3" {{if $player_data.volume=='0.3'}}selected="selected"{{/if}}>30%</option>
					<option value="0.4" {{if $player_data.volume=='0.4'}}selected="selected"{{/if}}>40%</option>
					<option value="0.5" {{if $player_data.volume=='0.5'}}selected="selected"{{/if}}>50%</option>
					<option value="0.6" {{if $player_data.volume=='0.6'}}selected="selected"{{/if}}>60%</option>
					<option value="0.7" {{if $player_data.volume=='0.7'}}selected="selected"{{/if}}>70%</option>
					<option value="0.8" {{if $player_data.volume=='0.8'}}selected="selected"{{/if}}>80%</option>
					<option value="0.9" {{if $player_data.volume=='0.9'}}selected="selected"{{/if}}>90%</option>
					<option value="1" {{if $player_data.volume=='1' || $player_data.volume==''}}selected="selected"{{/if}}>100%</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_volume_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_loop}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="loop" name="loop">
						<option value="0">{{$lang.settings.player_field_loop_disabled}}</option>
						<option value="1" {{if $player_data.loop=='1'}}selected="selected"{{/if}}>{{$lang.settings.player_field_loop_all_videos}}</option>
						<option value="2" {{if $player_data.loop=='2'}}selected="selected"{{/if}}>{{$lang.settings.player_field_loop_duration}}</option>
					</select>
					&nbsp;
					<input type="text" name="loop_duration" class="fixed_100 loop_2" value="{{$player_data.loop_duration}}"/>
					{{$lang.settings.player_field_loop_duration_seconds}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_field_loop_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_timeline_screenshots}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					{{$lang.settings.player_field_timeline_screenshots_jpg}}:
					{{assign var="vis_sw_timeline" value=""}}
					<select id="timeline_screenshots_size" name="timeline_screenshots_size">
						{{if $player_data.timeline_screenshots_size==''}}
							{{assign var="found_format" value="true"}}
						{{else}}
							{{assign var="found_format" value="false"}}
						{{/if}}
						<option value="">{{$lang.settings.player_field_timeline_screenshots_no}}</option>
						{{foreach item=item from=$list_formats_timeline_screenshots_jpg|smarty:nodefaults}}
							<option value="{{$item.size}}" {{if $player_data.timeline_screenshots_size==$item.size}}{{assign var="found_format" value="true"}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{assign var="vis_sw_timeline" value="`$vis_sw_timeline` timeline_screenshots_size_`$item.size`"}}
						{{/foreach}}
						{{if $found_format=='false'}}
							<option value="{{$player_data.timeline_screenshots_size}}" selected="selected">{{$lang.settings.player_field_timeline_screenshots_missing}}</option>
						{{/if}}
					</select>
					&nbsp;&nbsp;
					<span class="{{$vis_sw_timeline}}">
						{{$lang.settings.player_field_timeline_screenshots_webp}}:
						<select name="timeline_screenshots_webp_size">
							{{if $player_data.timeline_screenshots_webp_size==''}}
								{{assign var="found_format" value="true"}}
							{{else}}
								{{assign var="found_format" value="false"}}
							{{/if}}
							<option value="">{{$lang.settings.player_field_timeline_screenshots_no}}</option>
							{{foreach item=item from=$list_formats_timeline_screenshots_webp|smarty:nodefaults}}
								<option value="{{$item.size}}" {{if $player_data.timeline_screenshots_webp_size==$item.size}}{{assign var="found_format" value="true"}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
							{{if $found_format=='false'}}
								<option value="{{$player_data.timeline_screenshots_webp_size}}" selected="selected">{{$lang.settings.player_field_timeline_screenshots_missing}}</option>
							{{/if}}
						</select>
						&nbsp;&nbsp;
					</span>
					<div class="de_lv_pair {{$vis_sw_timeline}}"><input type="checkbox" name="timeline_screenshots_cuepoints" value="1" {{if $player_data.timeline_screenshots_cuepoints==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_timeline_screenshots_cue}}</label></div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">
						{{$lang.settings.player_field_timeline_screenshots_hint}}
						{{if $smarty.session.userdata.is_hide_forum_hints==0}}
							<br/>Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1212-theme-customization-how-to-show-player-timeline-screenshots-outside-player-and-make-them-clickable">How to show player timeline screenshots outside player and make them clickable</a>
						{{/if}}
					</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.request.page=='embed'}}
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_affiliate_param_name}}:</td>
				<td class="de_control">
					<input type="text" name="affiliate_param_name" maxlength="100" class="dyn_full_size" value="{{$player_data.affiliate_param_name}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_affiliate_param_name_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="overwrite_settings_on">
			<td class="de_label">
				<div class="adblock_protection_off">{{$lang.settings.player_field_adblock_protection}}:</div>
				<div class="adblock_protection_on de_required">{{$lang.settings.player_field_adblock_protection}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="adblock_protection" name="enable_adblock_protection" value="1" {{if $player_data.enable_adblock_protection==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_adblock_protection_enabled}}</label></div>
							<input type="text" name="adblock_protection_html_after" size="4" class="adblock_protection_on" value="{{$player_data.adblock_protection_html_after|default:"10"}}"/> {{$lang.settings.player_field_adblock_protection_enabled2}}
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_adblock_protection_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<textarea name="adblock_protection_html" class="html_code_editor dyn_full_size adblock_protection_on" rows="4">{{$player_data.adblock_protection_html}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_adblock_protection_hint2}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_poster}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="disable_preview_resize" value="1" {{if $player_data.disable_preview_resize==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_poster_disable_preview_resize}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="use_uploaded_poster" value="1" {{if $player_data.use_uploaded_poster==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_poster_use_uploaded_poster}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="use_preview_source" value="1" {{if $player_data.use_preview_source==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_poster_use_preview_source}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_options}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="enable_stream" value="1" {{if $player_data.enable_stream==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_options_stream}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="enable_autoplay" value="1" {{if $player_data.enable_autoplay==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_options_autoplay}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="enable_related_videos" name="enable_related_videos" value="1" {{if $player_data.enable_related_videos==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_options_related_videos}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="enable_related_videos_on_pause" value="1" {{if $player_data.enable_related_videos_on_pause==1}}checked="checked"{{/if}} class="enable_related_videos_on"/><label>{{$lang.settings.player_field_options_related_videos_pause}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="enable_urls_in_same_window" value="1" {{if $player_data.enable_urls_in_same_window==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_options_urls_same_window}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="disable_embed_code" value="1" {{if $player_data.disable_embed_code==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_options_disable_embed_code}}</label></div></td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="error_logging" value="1" {{if $player_data.error_logging==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_options_error_logging}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_options_error_logging_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_vast}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_vast_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_advertising_vast_key}}:</td>
			<td class="de_control">
				<input type="text" class="fixed_300" value="{{$primary_vast_key|default:$lang.common.undefined}}" readonly="readonly"/>
				{{if $primary_vast_key_invalid==1}}
					&nbsp;&nbsp;<span class="highlighted_text">{{$lang.settings.player_field_advertising_vast_key_invalid}}</span>
				{{elseif $primary_vast_key_valid>0}}
					&nbsp;&nbsp;
					{{if $primary_vast_key_valid<=3}}
						<span class="warning_text">{{$lang.settings.player_field_advertising_vast_key_valid|replace:"%1%":$primary_vast_key_valid}}</span>
					{{else}}
						{{$lang.settings.player_field_advertising_vast_key_valid|replace:"%1%":$primary_vast_key_valid}}
					{{/if}}
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_advertising_vast_key_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_advertising_vast_timeout}}:</td>
			<td class="de_control">
				<input type="text" name="pre_roll_vast_timeout" size="10" maxlength="10" value="{{$player_data.pre_roll_vast_timeout|default:"10"}}"/>
				{{$lang.settings.player_field_advertising_vast_timeout_s}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_advertising_vast_timeout_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_branding_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_branding_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_logo}}:</td>
			<td class="de_control">
				<select name="logo_source" id="logo_source" class="fixed_300">
					<option value="0" {{if $player_data.logo_source==0}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global_image}}</option>
					{{section name="data" start="1" loop="11"}}
						{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
						<option value="{{$smarty.section.data.index}}" {{if $player_data.logo_source==$smarty.section.data.index}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
					{{/section}}
				</select>
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.settings.player_field_logo}}</span>
						<span class="js_param">accept={{$config.image_allowed_ext}}</span>
						{{if $player_data.logo!=''}}
							<span class="js_param">preview_url={{$player_path}}/{{$player_data.logo}}</span>
						{{/if}}
					</div>
					<input type="text" name="logo" maxlength="100" class="fixed_150" {{if $player_data.logo!=''}}value="{{$player_data.logo}}"{{/if}} readonly="readonly"/>
					<input type="hidden" name="logo_hash"/>
					<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
					<input type="button" class="de_fu_remove {{if $player_data.logo==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					{{if $player_data.logo!=''}}
						<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_logo_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_logo_text}}:</td>
			<td class="de_control">
				<select name="logo_text_source" id="logo_text_source" class="fixed_300">
					<option value="0" {{if $player_data.logo_text_source==0}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global_text}}</option>
					{{section name="data" start="1" loop="11"}}
						{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
						<option value="{{$smarty.section.data.index}}" {{if $player_data.logo_text_source==$smarty.section.data.index}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
					{{/section}}
				</select>
				<input type="text" name="logo_text" maxlength="255" class="fixed_400 logo_text_source_1 logo_text_source_3" value="{{$player_data.logo_text}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_logo_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">
				<div class="logo_url_source_1">{{$lang.settings.player_field_logo_url}}:</div>
				{{if $smarty.request.page=='embed'}}
					<div class="logo_url_source_2">{{$lang.settings.player_field_logo_url}}:</div>
				{{/if}}
				<div class="logo_url_source_3 de_required">{{$lang.settings.player_field_logo_url_default}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="logo_url_source" id="logo_url_source" class="fixed_300">
						<option value="1" {{if $player_data.logo_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.logo_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<option value="3" {{if $player_data.logo_url_source==3}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="logo_url" maxlength="255" class="fixed_400 logo_url_source_1 logo_url_source_3" value="{{$player_data.logo_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.request.page=='embed'}}
							<span class="de_hint">{{$lang.settings.player_field_logo_url_embed_hint}}</span>
						{{else}}
							<span class="de_hint">{{$lang.settings.player_field_logo_url_hint}}</span>
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_logo_position}}:</td>
			<td class="de_control">
				<select id="logo_anchor" name="logo_anchor">
					<option value="topleft" {{if $player_data.logo_anchor=='topleft'}}selected="selected"{{/if}}>{{$lang.settings.player_field_logo_position_topleft}}</option>
					<option value="topright" {{if $player_data.logo_anchor=='topright'}}selected="selected"{{/if}}>{{$lang.settings.player_field_logo_position_topright}}</option>
					<option value="bottomright" {{if $player_data.logo_anchor=='bottomright'}}selected="selected"{{/if}}>{{$lang.settings.player_field_logo_position_bottomright}}</option>
					<option value="bottomleft" {{if $player_data.logo_anchor=='bottomleft'}}selected="selected"{{/if}}>{{$lang.settings.player_field_logo_position_bottomleft}}</option>
				</select>
				&nbsp;
				{{$lang.settings.player_field_logo_position_offset_x}}:
				<input type="text" name="logo_position_x" maxlength="5" class="fixed_100" value="{{$player_data.logo_position_x}}"/>
				&nbsp;
				{{$lang.settings.player_field_logo_position_offset_y}}:
				<input type="text" name="logo_position_y" maxlength="5" class="fixed_100" value="{{$player_data.logo_position_y}}"/>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_logo_autohide}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="logo_hide" value="1" {{if $player_data.logo_hide==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_logo_autohide_enable}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_logo_autohide_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_controlbar_ad_text}}:</td>
			<td class="de_control">
				<input type="text" name="controlbar_ad_text" maxlength="100" class="dyn_full_size" value="{{$player_data.controlbar_ad_text}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_controlbar_ad_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">
				<div class="controlbar_ad_url_source_1">{{$lang.settings.player_field_controlbar_ad_url}}:</div>
				{{if $smarty.request.page=='embed'}}
					<div class="controlbar_ad_url_source_2">{{$lang.settings.player_field_controlbar_ad_url}}:</div>
				{{/if}}
				<div class="controlbar_ad_url_source_3 de_required">{{$lang.settings.player_field_controlbar_ad_url_default}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="controlbar_ad_url_source" id="controlbar_ad_url_source" class="fixed_300">
						<option value="1" {{if $player_data.controlbar_ad_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.controlbar_ad_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<option value="3" {{if $player_data.controlbar_ad_url_source==3}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="controlbar_ad_url" maxlength="255" class="fixed_400 controlbar_ad_url_source_1 controlbar_ad_url_source_3" value="{{$player_data.controlbar_ad_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.request.page=='embed'}}
							<span class="de_hint">{{$lang.settings.player_field_controlbar_ad_url_embed_hint}}</span>
						{{else}}
							<span class="de_hint">{{$lang.settings.player_field_controlbar_ad_url_hint}}</span>
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_formats_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_formats_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">
				<div class="de_required format_redirect_url_source_1 format_redirect_url_source_3">{{$lang.settings.player_field_format_redirect}} (*):</div>
				{{if $smarty.request.page=='embed'}}
					<div class="format_redirect_url_source_2">{{$lang.settings.player_field_format_redirect}}:</div>
				{{/if}}
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="format_redirect_url_source" id="format_redirect_url_source" class="fixed_300">
						<option value="1" {{if $player_data.format_redirect_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.format_redirect_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<option value="3" {{if $player_data.format_redirect_url_source==3}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="format_redirect_url" maxlength="255" class="fixed_400 format_redirect_url_source_1 format_redirect_url_source_3" value="{{$player_data.format_redirect_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.request.page=='embed'}}
							<span class="de_hint">{{$lang.settings.player_field_format_redirect_embed_hint}}</span>
						{{else}}
							<span class="de_hint">{{$lang.settings.player_field_format_redirect_hint}}</span>
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_restoring_selected_slot}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="disable_selected_slot_restoring" value="1" {{if $player_data.disable_selected_slot_restoring==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_restoring_selected_slot_no}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_restoring_selected_slot_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_global_duration}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="show_global_duration" value="1" {{if $player_data.show_global_duration==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_global_duration_enable}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_global_duration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col width="1%"/>
						<col width="15%"/>
						<col width="15%"/>
						<col width="10%"/>
						<col/>
					</colgroup>
					<tr class="eg_header">
						<td>{{$lang.settings.player_formats_col_order}}</td>
						<td>{{$lang.settings.player_formats_col_format}}</td>
						<td>{{$lang.settings.player_formats_col_player_title}}</td>
						<td>{{$lang.settings.player_formats_col_default}}</td>
						<td>{{$lang.settings.player_formats_col_options}}</td>
					</tr>
					{{assign var=group_id value=0}}
					{{section name=groups start=0 step=1 loop=2}}
						{{if count($formats[$group_id])>0}}
							<tr class="eg_header">
								<td colspan="5">
									{{if $group_id==0}}
										{{$lang.settings.player_formats_group_standard}}
									{{elseif $group_id==1}}
										{{$lang.settings.player_formats_group_premium}}
									{{/if}}
								</td>
							</tr>
							{{assign var="slot_id" value=1}}
							{{assign var="global_vis_sw" value=""}}
							{{section name=slots start=0 step=1 loop=7}}
								<tr class="eg_data fixed_height_30 {{$global_vis_sw}}">
									<td>{{$slot_id}}</td>
									<td>
										<div class="de_vis_sw_select">
											{{assign var="option_id" value="group`$group_id`_slot`$slot_id`"}}
											<select name="{{$option_id}}" id="{{$option_id}}" class="fixed_200">
												{{if $selected_slots[$option_id]=='' || $selected_slots[$option_id]=='redirect'}}
													{{assign var="found_format" value="true"}}
												{{else}}
													{{assign var="found_format" value="false"}}
												{{/if}}
												<option value="">{{$lang.settings.player_formats_col_format_slot|replace:"%1%":$slot_id}}</option>
												{{assign var="vis_sw" value=""}}
												{{foreach item=item from=$formats[$group_id]|smarty:nodefaults}}
													{{assign var="vis_sw" value="`$vis_sw` group`$group_id`_slot`$slot_id`_`$item.postfix`"}}
													<option value="{{$item.postfix}}" {{if $selected_slots[$option_id]==$item.postfix}}{{assign var="found_format" value="true"}}selected="selected"{{/if}}>{{$item.title}}</option>
												{{/foreach}}
												{{if $slot_id>=2}}
													{{assign var="vis_sw" value="`$vis_sw` group`$group_id`_slot`$slot_id`_redirect"}}
													<option value="redirect" {{if $selected_slots[$option_id]=='redirect'}}selected="selected"{{/if}}>{{$lang.settings.player_formats_col_format_redirect}}</option>
												{{/if}}
												{{if $found_format=='false'}}
													{{assign var="vis_sw" value="`$vis_sw` group`$group_id`_slot`$slot_id`_`$selected_slots[$option_id]`"}}
													<option value="{{$selected_slots[$option_id]}}" selected="selected">{{$lang.settings.player_formats_col_format_missing}}</option>
												{{/if}}
												{{assign var="global_vis_sw" value="`$global_vis_sw` `$vis_sw`"}}
											</select>
										</div>
									</td>
									<td>
										{{assign var="option_id" value="group`$group_id`_slot_title`$slot_id`"}}
										<input type="text" name="{{$option_id}}" value="{{$selected_slots[$option_id]}}" class="fixed_150 {{$vis_sw}}"/>
									</td>
									<td>
										{{assign var="vis_sw_default" value=""}}
										{{foreach item=item from=$formats[$group_id]|smarty:nodefaults}}
											{{assign var="vis_sw_default" value="`$vis_sw_default` group`$group_id`_slot`$slot_id`_`$item.postfix`"}}
										{{/foreach}}
										{{assign var="option_id" value="group`$group_id`_default"}}
										<input type="radio" class="{{$vis_sw_default}}" name="{{$option_id}}" value="{{$slot_id}}" {{if $selected_slots[$option_id]==$slot_id}}checked="checked"{{/if}}/>
									</td>
									<td>
										{{foreach item=item from=$formats[$group_id]|smarty:nodefaults}}
											<div class="group{{$group_id}}_slot{{$slot_id}}_{{$item.postfix}}">
												{{if $item.access_level_id==0}}
													{{$lang.settings.player_formats_col_options_video}}:
													{{foreach name=data item=item from=$applied|smarty:nodefaults}}
														{{if $item==1}}
															{{$lang.settings.player_formats_col_options_user_unknown}}{{if !$smarty.foreach.data.last}},{{/if}}
														{{elseif $item==2}}
															{{$lang.settings.player_formats_col_options_user_active}}{{if !$smarty.foreach.data.last}},{{/if}}
														{{elseif $item==3}}
															{{$lang.settings.player_formats_col_options_user_premium}}
														{{/if}}
													{{/foreach}}
												{{elseif $item.access_level_id==1}}
													{{if in_array(2,$applied) || in_array(3,$applied)}}
														{{$lang.settings.player_formats_col_options_video}}:
														{{foreach name=data item=item from=$applied|smarty:nodefaults}}
															{{if $item==2}}
																{{$lang.settings.player_formats_col_options_user_active}}{{if !$smarty.foreach.data.last}},{{/if}}
															{{elseif $item==3}}
																{{$lang.settings.player_formats_col_options_user_premium}}
															{{/if}}
														{{/foreach}}
														&nbsp;
													{{/if}}
													{{if in_array(1,$applied)}}
														{{$lang.settings.player_formats_col_options_redirect}}:
														{{foreach name=data item=item from=$applied|smarty:nodefaults}}
															{{if $item==1}}
																{{$lang.settings.player_formats_col_options_user_unknown}}
															{{/if}}
														{{/foreach}}
													{{/if}}
												{{elseif $item.access_level_id==2}}
													{{if in_array(3,$applied)}}
														{{$lang.settings.player_formats_col_options_video}}:
														{{foreach name=data item=item from=$applied|smarty:nodefaults}}
															{{if $item==3}}
																{{$lang.settings.player_formats_col_options_user_premium}}
															{{/if}}
														{{/foreach}}
														&nbsp;
													{{/if}}
													{{if in_array(1,$applied) || in_array(2,$applied)}}
														{{$lang.settings.player_formats_col_options_redirect}}:
														{{foreach name=data item=item from=$applied|smarty:nodefaults}}
															{{if $item==1}}
																{{$lang.settings.player_formats_col_options_user_unknown}}{{if !$smarty.foreach.data.last}},{{/if}}
															{{elseif $item==2}}
																{{$lang.settings.player_formats_col_options_user_active}}
															{{/if}}
														{{/foreach}}
													{{/if}}
												{{/if}}
											</div>
										{{/foreach}}
										{{if $slot_id>=2}}
											<div class="group{{$group_id}}_slot{{$slot_id}}_redirect">
												{{$lang.settings.player_formats_col_options_redirect}}:
												{{foreach name=data item=item from=$applied|smarty:nodefaults}}
													{{if $item==1}}
														{{$lang.settings.player_formats_col_options_user_unknown}}{{if !$smarty.foreach.data.last}},{{/if}}
													{{elseif $item==2}}
														{{$lang.settings.player_formats_col_options_user_active}}{{if !$smarty.foreach.data.last}},{{/if}}
													{{elseif $item==3}}
														{{$lang.settings.player_formats_col_options_user_premium}}
													{{/if}}
												{{/foreach}}
											</div>
										{{/if}}
									</td>
								</tr>
								{{assign var=slot_id value=$slot_id+1}}
							{{/section}}
						{{/if}}
						{{assign var=group_id value=$group_id+1}}
					{{/section}}
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_click_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_click_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_video_click_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="video_click" name="enable_video_click" value="1" {{if $player_data.enable_video_click==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_video_click_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_video_click_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on video_click_on">
			<td class="de_label de_dependent">
				<div class="de_required video_click_on video_click_url_source_1 video_click_url_source_3">{{$lang.settings.player_field_video_click_url}} (*):</div>
				{{if $smarty.request.page=='embed'}}
					<div class="video_click_on video_click_url_source_2">{{$lang.settings.player_field_video_click_url}}:</div>
				{{/if}}
				<div class="video_click_off">{{$lang.settings.player_field_video_click_url}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="video_click_url_source" id="video_click_url_source" class="video_click_on fixed_300">
						<option value="1" {{if $player_data.video_click_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						{{if $smarty.request.page=='embed'}}
							<option value="2" {{if $player_data.video_click_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_video}}</option>
						{{/if}}
						<option value="3" {{if $player_data.video_click_url_source==3}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="video_click_url" maxlength="255" class="fixed_400 video_click_on video_click_url_source_1 video_click_url_source_3" value="{{$player_data.video_click_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.request.page=='embed'}}
							<span class="de_hint">{{$lang.settings.player_field_video_click_url_embed_hint}}</span>
						{{else}}
							<span class="de_hint">{{$lang.settings.player_field_video_click_url_hint}}</span>
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.request.page!='embed'}}
			<tr class="overwrite_settings_on">
				<td class="de_label">{{$lang.settings.player_field_popunder_enable}}:</td>
				<td class="de_control">
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="popunder" name="enable_popunder" value="1" {{if $player_data.enable_popunder==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_popunder_enable_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_popunder_enable_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="overwrite_settings_on popunder_on">
				<td class="de_label de_dependent">
					<div class="de_required popunder_on popunder_url_source_1 popunder_url_source_3">{{$lang.settings.player_field_popunder_url}} (*):</div>
					<div class="popunder_off">{{$lang.settings.player_field_popunder_url}}:</div>
				</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select name="popunder_url_source" id="popunder_url_source" class="popunder_on fixed_300">
							<option value="1" {{if $player_data.popunder_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
							<option value="3" {{if $player_data.popunder_url_source==3}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
						</select>
						<input type="text" name="popunder_url" maxlength="255" class="fixed_400 popunder_on popunder_url_source_1 popunder_url_source_3" value="{{$player_data.popunder_url}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.player_field_popunder_url_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr class="overwrite_settings_on popunder_on">
				<td class="de_label de_dependent de_required">{{$lang.settings.player_field_popunder_duration}} (*):</td>
				<td class="de_control">
					<input type="text" name="popunder_duration" maxlength="10" class="fixed_100" value="{{$player_data.popunder_duration|default:"60"}}"/>
					{{$lang.settings.player_field_popunder_duration_minutes}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_popunder_duration_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="overwrite_settings_on popunder_on">
				<td class="de_label de_dependent">{{$lang.settings.player_field_popunder_autoplay_only}}:</td>
				<td class="de_control">
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="popunder_autoplay_only" value="1" {{if $player_data.popunder_autoplay_only==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_popunder_autoplay_only_enabled}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_popunder_autoplay_only_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_start_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_start_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_start_html_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="start_html" name="enable_start_html" value="1" {{if $player_data.enable_start_html==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_start_html_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_start_html_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on start_html_on">
			<td class="de_label de_dependent">
				<div class="de_required start_html_on start_html_source_1">{{$lang.settings.player_field_start_html_code}} (*):</div>
				<div class="start_html_on start_html_source_2 start_html_source_3 start_html_source_4 start_html_source_5 start_html_source_6 start_html_source_7 start_html_source_8 start_html_source_9 start_html_source_10 start_html_source_11">{{$lang.settings.player_field_start_html_code_default}}:</div>
				<div class="start_html_off">{{$lang.settings.player_field_start_html_code}}:</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_vis_sw_select">
								<select id="start_html_source" name="start_html_source" class="start_html_on fixed_300">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.start_html_source==1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.start_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.start_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.start_html_source!=''}}
											<option value="{{$player_data.start_html_source}}" selected="selected">{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="start_html_source_1 start_html_source_2 start_html_source_3 start_html_source_4 start_html_source_5 start_html_source_6 start_html_source_7 start_html_source_8 start_html_source_9 start_html_source_10 start_html_source_11">
						<td>
							<textarea name="start_html_code" class="html_code_editor dyn_full_size start_html_on" rows="4">{{$player_data.start_html_code}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_start_html_code_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on start_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}:</td>
			<td class="de_control">
				<input type="text" name="start_html_bg" maxlength="20" class="dyn_full_size start_html_on" value="{{$player_data.start_html_bg|default:"#000000"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on start_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="start_html_adaptive" name="start_html_adaptive" class="start_html_on" value="1" {{if $player_data.start_html_adaptive==1}}checked="checked"{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></div>
				<input type="text" name="start_html_adaptive_width" maxlength="3" class="fixed_50 start_html_on start_html_adaptive_on" value="{{$player_data.start_html_adaptive_width}}"/>%
				x
				<input type="text" name="start_html_adaptive_height" maxlength="3" class="fixed_50 start_html_on start_html_adaptive_on" value="{{$player_data.start_html_adaptive_height}}"/>%
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_pre_roll_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_pre_roll_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pre_roll_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pre_roll" name="enable_pre_roll" value="1" {{if $player_data.enable_pre_roll==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_pre_roll==1}}class="selected"{{/if}}>{{$lang.settings.player_field_pre_roll_enable_enabled}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_on">
			<td class="de_label de_dependent">
				<div class="de_required pre_roll_on pre_roll_file_source_1">{{$lang.settings.player_field_pre_roll_file}} (*):</div>
				<div class="pre_roll_on pre_roll_file_source_2 pre_roll_file_source_3 pre_roll_file_source_4 pre_roll_file_source_5 pre_roll_file_source_6 pre_roll_file_source_7 pre_roll_file_source_8 pre_roll_file_source_9 pre_roll_file_source_10 pre_roll_file_source_11">{{$lang.settings.player_field_pre_roll_file_default}}:</div>
				<div class="pre_roll_off">{{$lang.settings.player_field_pre_roll_file}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="pre_roll_file_source" id="pre_roll_file_source" class="pre_roll_on fixed_300">
						<option value="1" {{if $player_data.pre_roll_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pre_roll_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
						{{/section}}
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_pre_roll_file}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}},mp4</span>
							{{if $player_data.pre_roll_file!=''}}
								{{if in_array(end(explode(".",$player_data.pre_roll_file)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$player_path}}/{{$player_data.pre_roll_file}}</span>
								{{else}}
									<span class="js_param">download_url={{$player_path}}/{{$player_data.pre_roll_file}}</span>
								{{/if}}
							{{/if}}
						</div>
						<input type="text" name="pre_roll_file" maxlength="100" class="fixed_150 pre_roll_on" {{if $player_data.pre_roll_file!=''}}value="{{$player_data.pre_roll_file}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="pre_roll_file_hash"/>
						<input type="button" class="de_fu_upload pre_roll_on" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove pre_roll_on {{if $player_data.pre_roll_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{if $player_data.pre_roll_file!=''}}
							{{if in_array(end(explode(".",$player_data.pre_roll_file)),explode(",",$config.image_allowed_ext))}}
								<input type="button" class="de_fu_preview pre_roll_on" value="{{$lang.common.attachment_btn_preview}}"/>
							{{else}}
								<input type="button" class="de_fu_download pre_roll_on" value="{{$lang.common.attachment_btn_download}}"/>
							{{/if}}
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_file_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_on">
			<td class="de_label de_dependent">
				<div class="de_required pre_roll_on pre_roll_url_source_1">{{$lang.settings.player_field_pre_roll_url}} (*):</div>
				<div class="de_required pre_roll_on pre_roll_url_source_2">{{$lang.settings.player_field_pre_roll_url_default}} (*):</div>
				<div class="pre_roll_off">{{$lang.settings.player_field_pre_roll_url}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="pre_roll_url_source" name="pre_roll_url_source" class="fixed_300 pre_roll_on">
						<option value="1" {{if $player_data.pre_roll_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						<option value="2" {{if $player_data.pre_roll_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="pre_roll_url" maxlength="255" class="fixed_400 pre_roll_on" value="{{$player_data.pre_roll_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_url_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pre_roll_html_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pre_roll_html" name="enable_pre_roll_html" value="1" {{if $player_data.enable_pre_roll_html==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_pre_roll_html_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_html_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_html_on">
			<td class="de_label de_dependent">
				<div class="de_required pre_roll_html_on pre_roll_html_source_1">{{$lang.settings.player_field_pre_roll_html_code}} (*):</div>
				<div class="pre_roll_html_on pre_roll_html_source_2 pre_roll_html_source_3 pre_roll_html_source_4 pre_roll_html_source_5 pre_roll_html_source_6 pre_roll_html_source_7 pre_roll_html_source_8 pre_roll_html_source_9 pre_roll_html_source_10 pre_roll_html_source_11">{{$lang.settings.player_field_pre_roll_html_code_default}}:</div>
				<div class="pre_roll_html_off">{{$lang.settings.player_field_pre_roll_html_code}}:</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_vis_sw_select">
								<select id="pre_roll_html_source" name="pre_roll_html_source" class="pre_roll_html_on fixed_300">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.pre_roll_html_source==1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pre_roll_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.pre_roll_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.pre_roll_html_source!=''}}
											<option value="{{$player_data.pre_roll_html_source}}" selected="selected">{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="pre_roll_html_source_1 pre_roll_html_source_2 pre_roll_html_source_3 pre_roll_html_source_4 pre_roll_html_source_5 pre_roll_html_source_6 pre_roll_html_source_7 pre_roll_html_source_8 pre_roll_html_source_9 pre_roll_html_source_10 pre_roll_html_source_11">
						<td>
							<textarea name="pre_roll_html_code" class="html_code_editor dyn_full_size pre_roll_html_on" rows="4">{{$player_data.pre_roll_html_code}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_html_code_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}:</td>
			<td class="de_control">
				<input type="text" name="pre_roll_html_bg" maxlength="20" class="dyn_full_size pre_roll_html_on" value="{{$player_data.pre_roll_html_bg|default:"#000000"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pre_roll_html_adaptive" name="pre_roll_html_adaptive" class="pre_roll_html_on" value="1" {{if $player_data.pre_roll_html_adaptive==1}}checked="checked"{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></div>
				<input type="text" name="pre_roll_html_adaptive_width" maxlength="3" class="fixed_50 pre_roll_html_on pre_roll_html_adaptive_on" value="{{$player_data.pre_roll_html_adaptive_width}}"/>%
				x
				<input type="text" name="pre_roll_html_adaptive_height" maxlength="3" class="fixed_50 pre_roll_html_on pre_roll_html_adaptive_on" value="{{$player_data.pre_roll_html_adaptive_height}}"/>%
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pre_roll_vast_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pre_roll_vast" name="enable_pre_roll_vast" value="1" {{if $player_data.enable_pre_roll_vast==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_pre_roll_vast_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_vast_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_vast_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_provider}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="pre_roll_vast_provider" name="pre_roll_vast_provider">
						<option value="3" {{if $player_data.pre_roll_vast_provider=='3'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_3}}</option>
						<option value="1" {{if $player_data.pre_roll_vast_provider=='1'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_1}}</option>
						<option value="2" {{if $player_data.pre_roll_vast_provider=='2'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_2}}</option>
						<option value="c" {{if $player_data.pre_roll_vast_provider=='c'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_c}}</option>
						{{assign var="found_profile" value="false"}}
						{{foreach from=$vast_profiles item="vast_profile"}}
							<option value="vast_profile_{{$vast_profile.profile_id}}" {{if $player_data.pre_roll_vast_provider=="vast_profile_`$vast_profile.profile_id`"}}{{assign var="found_profile" value="true"}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_profile|replace:"%1%":$vast_profile.title}}</option>
						{{/foreach}}
						{{if $found_profile=='false' && $player_data.pre_roll_vast_provider|strpos:"vast_profile_"!==false}}
							<option value="{{$player_data.pre_roll_vast_provider}}" selected="selected">{{$lang.settings.common_field_advertising_vast_provider_missing}}</option>
						{{/if}}
					</select>
					&nbsp;&nbsp;
					<a href="{{$lang.settings.common_field_advertising_vast_provider_1_url}}" class="pre_roll_vast_provider_1" rel="external">{{$lang.settings.common_field_advertising_vast_provider_1_url2}}</a>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_2_url}}" class="pre_roll_vast_provider_2" rel="external">{{$lang.settings.common_field_advertising_vast_provider_2_url2}}</a>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_3_url}}" class="pre_roll_vast_provider_3" rel="external">{{$lang.settings.common_field_advertising_vast_provider_3_url2}}</a>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint pre_roll_vast_provider_1">{{$lang.settings.common_field_advertising_vast_provider_1_hint}}</span>
					<span class="de_hint pre_roll_vast_provider_2">{{$lang.settings.common_field_advertising_vast_provider_2_hint}}</span>
					<span class="de_hint pre_roll_vast_provider_3">{{$lang.settings.common_field_advertising_vast_provider_3_hint}}</span>
					<span class="de_hint pre_roll_vast_provider_c">{{$lang.settings.common_field_advertising_vast_provider_c_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_vast_on pre_roll_vast_provider_1 pre_roll_vast_provider_2 pre_roll_vast_provider_3 pre_roll_vast_provider_c">
			<td class="de_label de_required de_dependent">{{$lang.settings.common_field_advertising_vast_url}} (*):</td>
			<td class="de_control">
				<input type="text" name="pre_roll_vast_url" class="dyn_full_size" value="{{$player_data.pre_roll_vast_url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_vast_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_vast_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_alt_url}}:</td>
			<td class="de_control">
				<textarea name="pre_roll_vast_alt_url" class="dyn_full_size" rows="3" cols="40">{{$player_data.pre_roll_vast_alt_url}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_vast_alt_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_vast_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_logo}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pre_roll_vast_logo" name="pre_roll_vast_logo" value="1" {{if $player_data.pre_roll_vast_logo==1}}checked="checked"{{/if}}/><label>{{$lang.settings.common_field_advertising_vast_logo_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_vast_logo_hint}}</span>
				{{/if}}
				<div class="pre_roll_vast_logo_on">
					<br/>
					<div class="de_lv_pair"><input type="checkbox" name="pre_roll_vast_logo_click" value="1" {{if $player_data.pre_roll_vast_logo_click==1}}checked="checked"{{/if}}/><label>{{$lang.settings.common_field_advertising_vast_logo_enabled2}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.common_field_advertising_vast_logo_hint2}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">
				<div class="pre_roll_html_off pre_roll_off">{{$lang.settings.player_field_pre_roll_duration}}:</div>
				<div class="de_required pre_roll_html_on pre_roll_off">{{$lang.settings.player_field_pre_roll_duration}} (*):</div>
				<div class="de_required pre_roll_html_off pre_roll_on">{{$lang.settings.player_field_pre_roll_duration}} (*):</div>
				<div class="de_required pre_roll_html_on pre_roll_on">{{$lang.settings.player_field_pre_roll_duration}} (*):</div>
			</td>
			<td class="de_control">
				<input type="text" name="pre_roll_duration" maxlength="5" class="dyn_full_size" value="{{$player_data.pre_roll_duration}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_duration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pre_roll_duration_text}}:</td>
			<td class="de_control">
				<input type="text" name="pre_roll_duration_text" maxlength="100" class="dyn_full_size" value="{{$player_data.pre_roll_duration_text}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_duration_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">
				<div class="pre_roll_replay_option_0">{{$lang.settings.player_field_pre_roll_frequency}}:</div>
				<div class="pre_roll_replay_option_1 de_required">{{$lang.settings.player_field_pre_roll_frequency}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="pre_roll_replay_option" name="pre_roll_replay_option">
						<option value="0">{{$lang.settings.player_field_pre_roll_frequency_each}}</option>
						<option value="1" {{if $player_data.pre_roll_replay_after>0}}selected="selected"{{/if}}>{{$lang.settings.player_field_pre_roll_frequency_interval}}</option>
					</select>
					<span class="pre_roll_replay_option_1">
						&nbsp;&nbsp;
						<input type="text" name="pre_roll_replay_after" maxlength="5" size="5" value="{{$player_data.pre_roll_replay_after|default:''}}"/>
						&nbsp;&nbsp;
						<select name="pre_roll_replay_after_type">
							<option value="0">{{$lang.settings.player_field_pre_roll_frequency_videos}}</option>
							<option value="1" {{if $player_data.pre_roll_replay_after_type>0}}selected="selected"{{/if}}>{{$lang.settings.player_field_pre_roll_frequency_minutes}}</option>
						</select>
					</span>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_field_pre_roll_frequency_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pre_roll_skip}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pre_roll_skip" name="enable_pre_roll_skip" value="1" {{if $player_data.enable_pre_roll_skip==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_pre_roll_skip_after}}</label></div>
				<input type="text" name="pre_roll_skip_duration" maxlength="5" size="5" class="pre_roll_skip_on" value="{{$player_data.pre_roll_skip_duration}}"/>
				{{$lang.settings.player_field_pre_roll_skip_after_seconds}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_skip_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_skip_on">
			<td class="de_label de_dependent">{{$lang.settings.player_field_pre_roll_skip_text1}}:</td>
			<td class="de_control">
				<input type="text" name="pre_roll_skip_text1" maxlength="100" class="dyn_full_size" value="{{$player_data.pre_roll_skip_text1}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_skip_text1_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pre_roll_skip_on">
			<td class="de_label de_required de_dependent">{{$lang.settings.player_field_pre_roll_skip_text2}} (*):</td>
			<td class="de_control">
				<input type="text" name="pre_roll_skip_text2" maxlength="100" class="dyn_full_size" value="{{$player_data.pre_roll_skip_text2}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pre_roll_skip_text2_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_post_roll_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_post_roll_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_mode}}:</td>
			<td class="de_control">
				<select name="post_roll_mode">
					<option value="0" {{if $player_data.post_roll_mode=='0'}}selected="selected"{{/if}}>{{$lang.settings.player_field_post_roll_mode_finish}}</option>
					<option value="1" {{if $player_data.post_roll_mode=='1'}}selected="selected"{{/if}}>{{$lang.settings.player_field_post_roll_mode_pause}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_mode_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="post_roll" name="enable_post_roll" value="1" {{if $player_data.enable_post_roll==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_post_roll==1}}class="selected"{{/if}}>{{$lang.settings.player_field_post_roll_enable_enabled}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_on">
			<td class="de_label de_dependent">
				<div class="de_required post_roll_on post_roll_file_source_1">{{$lang.settings.player_field_post_roll_file}} (*):</div>
				<div class="post_roll_on post_roll_file_source_2 post_roll_file_source_3 post_roll_file_source_4 post_roll_file_source_5 post_roll_file_source_6 post_roll_file_source_7 post_roll_file_source_8 post_roll_file_source_9 post_roll_file_source_10 post_roll_file_source_11">{{$lang.settings.player_field_post_roll_file_default}}:</div>
				<div class="post_roll_off">{{$lang.settings.player_field_post_roll_file}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="post_roll_file_source" id="post_roll_file_source" class="post_roll_on fixed_300">
						<option value="1" {{if $player_data.post_roll_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							<option value="{{$smarty.section.data.index+1}}" {{if $player_data.post_roll_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
						{{/section}}
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_post_roll_file}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}},mp4</span>
							{{if $player_data.post_roll_file!=''}}
								{{if in_array(end(explode(".",$player_data.post_roll_file)),explode(",",$config.image_allowed_ext))}}
									<span class="js_param">preview_url={{$player_path}}/{{$player_data.post_roll_file}}</span>
								{{else}}
									<span class="js_param">download_url={{$player_path}}/{{$player_data.post_roll_file}}</span>
								{{/if}}
							{{/if}}
						</div>
						<input type="text" name="post_roll_file" maxlength="100" class="fixed_150 post_roll_on" {{if $player_data.post_roll_file!=''}}value="{{$player_data.post_roll_file}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="post_roll_file_hash"/>
						<input type="button" class="de_fu_upload post_roll_on" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove post_roll_on {{if $player_data.post_roll_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{if $player_data.post_roll_file!=''}}
							{{if in_array(end(explode(".",$player_data.post_roll_file)),explode(",",$config.image_allowed_ext))}}
								<input type="button" class="de_fu_preview post_roll_on" value="{{$lang.common.attachment_btn_preview}}"/>
							{{else}}
								<input type="button" class="de_fu_download post_roll_on" value="{{$lang.common.attachment_btn_download}}"/>
							{{/if}}
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_file_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_on">
			<td class="de_label de_dependent">
				<div class="de_required post_roll_on post_roll_url_source_1">{{$lang.settings.player_field_post_roll_url}} (*):</div>
				<div class="de_required post_roll_on post_roll_url_source_2">{{$lang.settings.player_field_post_roll_url_default}} (*):</div>
				<div class="post_roll_off">{{$lang.settings.player_field_post_roll_url}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="post_roll_url_source" name="post_roll_url_source" class="fixed_300 post_roll_on">
						<option value="1" {{if $player_data.post_roll_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						<option value="2" {{if $player_data.post_roll_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="post_roll_url" maxlength="255" class="fixed_400 post_roll_on" value="{{$player_data.post_roll_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_url_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_html_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="post_roll_html" name="enable_post_roll_html" value="1" {{if $player_data.enable_post_roll_html==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_post_roll_html_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_html_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_html_on">
			<td class="de_label de_dependent">
				<div class="de_required post_roll_html_on post_roll_html_source_1">{{$lang.settings.player_field_post_roll_html_code}} (*):</div>
				<div class="post_roll_html_on post_roll_html_source_2 post_roll_html_source_3 post_roll_html_source_4 post_roll_html_source_5 post_roll_html_source_6 post_roll_html_source_7 post_roll_html_source_8 post_roll_html_source_9 post_roll_html_source_10 post_roll_html_source_11">{{$lang.settings.player_field_post_roll_html_code_default}}:</div>
				<div class="post_roll_html_off">{{$lang.settings.player_field_post_roll_html_code}}:</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_vis_sw_select">
								<select id="post_roll_html_source" name="post_roll_html_source" class="post_roll_html_on fixed_300">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.post_roll_html_source==1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.post_roll_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.post_roll_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.post_roll_html_source!=''}}
											<option value="{{$player_data.post_roll_html_source}}" selected="selected">{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="post_roll_html_source_1 post_roll_html_source_2 post_roll_html_source_3 post_roll_html_source_4 post_roll_html_source_5 post_roll_html_source_6 post_roll_html_source_7 post_roll_html_source_8 post_roll_html_source_9 post_roll_html_source_10 post_roll_html_source_11">
						<td>
							<textarea name="post_roll_html_code" class="html_code_editor dyn_full_size post_roll_html_on" rows="4">{{$player_data.post_roll_html_code}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_html_code_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}:</td>
			<td class="de_control">
				<input type="text" name="post_roll_html_bg" maxlength="20" class="dyn_full_size post_roll_html_on" value="{{$player_data.post_roll_html_bg|default:"#000000"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="post_roll_html_adaptive" name="post_roll_html_adaptive" class="post_roll_html_on" value="1" {{if $player_data.post_roll_html_adaptive==1}}checked="checked"{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></div>
				<input type="text" name="post_roll_html_adaptive_width" maxlength="3" class="fixed_50 post_roll_html_on post_roll_html_adaptive_on" value="{{$player_data.post_roll_html_adaptive_width}}"/>%
				x
				<input type="text" name="post_roll_html_adaptive_height" maxlength="3" class="fixed_50 post_roll_html_on post_roll_html_adaptive_on" value="{{$player_data.post_roll_html_adaptive_height}}"/>%
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_vast_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="post_roll_vast" name="enable_post_roll_vast" value="1" {{if $player_data.enable_post_roll_vast==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_post_roll_vast_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_vast_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_vast_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_provider}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="post_roll_vast_provider" name="post_roll_vast_provider">
						<option value="3" {{if $player_data.post_roll_vast_provider=='3'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_3}}</option>
						<option value="1" {{if $player_data.post_roll_vast_provider=='1'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_1}}</option>
						<option value="2" {{if $player_data.post_roll_vast_provider=='2'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_2}}</option>
						<option value="c" {{if $player_data.post_roll_vast_provider=='c'}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_c}}</option>
						{{assign var="found_profile" value="false"}}
						{{foreach from=$vast_profiles item="vast_profile"}}
							<option value="vast_profile_{{$vast_profile.profile_id}}" {{if $player_data.post_roll_vast_provider=="vast_profile_`$vast_profile.profile_id`"}}{{assign var="found_profile" value="true"}}selected{{/if}}>{{$lang.settings.common_field_advertising_vast_provider_profile|replace:"%1%":$vast_profile.title}}</option>
						{{/foreach}}
						{{if $found_profile=='false' && $player_data.post_roll_vast_provider|strpos:"vast_profile_"!==false}}
							<option value="{{$player_data.post_roll_vast_provider}}" selected="selected">{{$lang.settings.common_field_advertising_vast_provider_missing}}</option>
						{{/if}}
					</select>
					&nbsp;&nbsp;
					<a href="{{$lang.settings.common_field_advertising_vast_provider_1_url}}" class="post_roll_vast_provider_1" rel="external">{{$lang.settings.common_field_advertising_vast_provider_1_url2}}</a>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_2_url}}" class="post_roll_vast_provider_2" rel="external">{{$lang.settings.common_field_advertising_vast_provider_2_url2}}</a>
					<a href="{{$lang.settings.common_field_advertising_vast_provider_3_url}}" class="post_roll_vast_provider_3" rel="external">{{$lang.settings.common_field_advertising_vast_provider_3_url2}}</a>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint post_roll_vast_provider_1">{{$lang.settings.common_field_advertising_vast_provider_1_hint}}</span>
					<span class="de_hint post_roll_vast_provider_2">{{$lang.settings.common_field_advertising_vast_provider_2_hint}}</span>
					<span class="de_hint post_roll_vast_provider_3">{{$lang.settings.common_field_advertising_vast_provider_3_hint}}</span>
					<span class="de_hint post_roll_vast_provider_c">{{$lang.settings.common_field_advertising_vast_provider_c_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_vast_on post_roll_vast_provider_1 post_roll_vast_provider_2 post_roll_vast_provider_3 post_roll_vast_provider_c">
			<td class="de_label de_required de_dependent">{{$lang.settings.common_field_advertising_vast_url}} (*):</td>
			<td class="de_control">
				<input type="text" name="post_roll_vast_url" class="dyn_full_size" value="{{$player_data.post_roll_vast_url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_vast_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_vast_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_vast_alt_url}}:</td>
			<td class="de_control">
				<textarea name="post_roll_vast_alt_url" class="dyn_full_size" rows="3" cols="40">{{$player_data.post_roll_vast_alt_url}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_vast_alt_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_duration}}:</td>
			<td class="de_control">
				<input type="text" name="post_roll_duration" maxlength="5" class="dyn_full_size" value="{{$player_data.post_roll_duration}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_duration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_duration_text}}:</td>
			<td class="de_control">
				<input type="text" name="post_roll_duration_text" maxlength="100" class="dyn_full_size" value="{{$player_data.post_roll_duration_text}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_duration_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_post_roll_skip}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="post_roll_skip" name="enable_post_roll_skip" value="1" {{if $player_data.enable_post_roll_skip==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_post_roll_skip_after}}</label></div>
				<input type="text" name="post_roll_skip_duration" maxlength="5" size="5" class="post_roll_skip_on" value="{{$player_data.post_roll_skip_duration}}"/>
				{{$lang.settings.player_field_post_roll_skip_after_seconds}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_skip_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_skip_on">
			<td class="de_label de_dependent">{{$lang.settings.player_field_post_roll_skip_text1}}:</td>
			<td class="de_control">
				<input type="text" name="post_roll_skip_text1" maxlength="100" class="dyn_full_size" value="{{$player_data.post_roll_skip_text1}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_skip_text1_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on post_roll_skip_on">
			<td class="de_label de_required de_dependent">{{$lang.settings.player_field_post_roll_skip_text2}} (*):</td>
			<td class="de_control">
				<input type="text" name="post_roll_skip_text2" maxlength="100" class="dyn_full_size" value="{{$player_data.post_roll_skip_text2}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_post_roll_skip_text2_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_pause_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_pause_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pause_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pause" name="enable_pause" value="1" {{if $player_data.enable_pause==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_pause==1}}class="selected"{{/if}}>{{$lang.settings.player_field_pause_enable_enabled}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pause_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pause_on">
			<td class="de_label de_dependent">
				<div class="de_required pause_on pause_file_source_1">{{$lang.settings.player_field_pause_file}} (*):</div>
				<div class="pause_on pause_file_source_2 pause_file_source_3 pause_file_source_4 pause_file_source_5 pause_file_source_6 pause_file_source_7 pause_file_source_8 pause_file_source_9 pause_file_source_10 pause_file_source_11">{{$lang.settings.player_field_pause_file_default}}:</div>
				<div class="pause_off">{{$lang.settings.player_field_pause_file}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="pause_file_source" id="pause_file_source" class="pause_on fixed_300">
						<option value="1" {{if $player_data.pause_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
						{{section name="data" start="1" loop="11"}}
							{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
							<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pause_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
						{{/section}}
					</select>
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.player_field_pause_file}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $player_data.pause_file!=''}}
								<span class="js_param">preview_url={{$player_path}}/{{$player_data.pause_file}}</span>
							{{/if}}
						</div>
						<input type="text" name="pause_file" maxlength="100" class="fixed_150 pause_on" {{if $player_data.pause_file!=''}}value="{{$player_data.pause_file}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="pause_file_hash"/>
						<input type="button" class="de_fu_upload pause_on" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove pause_on {{if $player_data.pause_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{if $player_data.pause_file!=''}}
							<input type="button" class="de_fu_preview pause_on" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_pause_file_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on pause_on">
			<td class="de_label de_dependent">
				<div class="de_required pause_on pause_url_source_1">{{$lang.settings.player_field_pause_url}} (*):</div>
				<div class="de_required pause_on pause_url_source_2">{{$lang.settings.player_field_pause_url_default}} (*):</div>
				<div class="pause_off">{{$lang.settings.player_field_pause_url}}:</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="pause_url_source" name="pause_url_source" class="fixed_300 pause_on">
						<option value="1" {{if $player_data.pause_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
						<option value="2" {{if $player_data.pause_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
					</select>
					<input type="text" name="pause_url" maxlength="255" class="fixed_400 pause_on" value="{{$player_data.pause_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_pause_url_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_pause_html_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pause_html" name="enable_pause_html" value="1" {{if $player_data.enable_pause_html==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_pause_html_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.player_field_pause_html_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pause_html_on">
			<td class="de_label de_dependent">
				<div class="de_required pause_html_on pause_html_source_1">{{$lang.settings.player_field_pause_html_code}} (*):</div>
				<div class="pause_html_on pause_html_source_2 pause_html_source_3 pause_html_source_4 pause_html_source_5 pause_html_source_6 pause_html_source_7 pause_html_source_8 pause_html_source_9 pause_html_source_10 pause_html_source_11">{{$lang.settings.player_field_pause_html_code_default}}:</div>
				<div class="pause_html_off">{{$lang.settings.player_field_pause_html_code}}:</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_vis_sw_select">
								<select id="pause_html_source" name="pause_html_source" class="pause_html_on fixed_300">
									{{assign var="found_spot" value="false"}}
									<option value="1" {{if $player_data.pause_html_source==1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
									<optgroup label="{{$lang.settings.common_advertising_source_cs_grouping}}">
										{{section name="data" start="1" loop="11"}}
											{{assign var="custom_field_name" value="CS_FIELD_`$smarty.section.data.index`_NAME"}}
											<option value="{{$smarty.section.data.index+1}}" {{if $player_data.pause_html_source==$smarty.section.data.index+1}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
										{{/section}}
									</optgroup>
									<optgroup label="{{$lang.settings.common_advertising_source_spot_grouping}}">
										{{foreach from=$list_spots item="ad_spot"}}
											<option value="spot_{{$ad_spot.external_id}}" {{if $player_data.pause_html_source=="spot_`$ad_spot.external_id`"}}{{assign var="found_spot" value="true"}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_spot|replace:"%1%":$ad_spot.title}}</option>
										{{/foreach}}
										{{if $found_spot=='false' && $player_data.pause_html_source!=''}}
											<option value="{{$player_data.pause_html_source}}" selected="selected">{{$lang.settings.common_advertising_source_spot_missing}}</option>
										{{/if}}
									</optgroup>
								</select>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_hint}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="pause_html_source_1 pause_html_source_2 pause_html_source_3 pause_html_source_4 pause_html_source_5 pause_html_source_6 pause_html_source_7 pause_html_source_8 pause_html_source_9 pause_html_source_10 pause_html_source_11">
						<td>
							<textarea name="pause_html_code" class="html_code_editor dyn_full_size pause_html_on" rows="4">{{$player_data.pause_html_code}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.settings.player_field_pause_html_code_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on pause_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_bg}}:</td>
			<td class="de_control">
				<input type="text" name="pause_html_bg" maxlength="20" class="dyn_full_size pause_html_on" value="{{$player_data.pause_html_bg|default:"#000000"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_bg_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on pause_html_on">
			<td class="de_label de_dependent">{{$lang.settings.common_field_advertising_html_adaptive}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pause_html_adaptive" name="pause_html_adaptive" class="pause_html_on" value="1" {{if $player_data.pause_html_adaptive==1}}checked="checked"{{/if}}/><label>{{$lang.settings.common_field_advertising_html_adaptive_enabled}}</label></div>
				<input type="text" name="pause_html_adaptive_width" maxlength="3" class="fixed_50 pause_html_on pause_html_adaptive_on" value="{{$player_data.pause_html_adaptive_width}}"/>%
				x
				<input type="text" name="pause_html_adaptive_height" maxlength="3" class="fixed_50 pause_html_on pause_html_adaptive_on" value="{{$player_data.pause_html_adaptive_height}}"/>%
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.common_field_advertising_html_adaptive_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_float_settings}}</div></td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_simple_text" colspan="2">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.settings.player_divider_float_settings_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_label">{{$lang.settings.player_field_float_options}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="enable_float_replay" value="1" {{if $player_data.enable_float_replay==1}}checked="checked"{{/if}}/><label>{{$lang.settings.player_field_float_options_replay}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr class="overwrite_settings_on">
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col width="20%"/>
						<col/>
					</colgroup>
					<tr class="eg_header fixed_height_30">
						<td colspan="2"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="float1" name="enable_float1" value="1" {{if $player_data.enable_float1==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_float1==1}}class="selected"{{/if}}>{{$lang.settings.player_field_float_enable|replace:"%1%":"1"}}</span></div></td>
					</tr>
					<tr class="eg_data fixed_height_30 float1_on">
						<td>{{$lang.settings.player_field_float_time}}:</td>
						<td>
							<input type="text" name="float1_time" class="float1_on dyn_full_size" maxlength="5" value="{{$player_data.float1_time}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float1_on">
						<td>{{$lang.settings.player_field_float_duration}}:</td>
						<td>
							<input type="text" name="float1_duration" class="float1_on dyn_full_size" maxlength="5" value="{{$player_data.float1_duration}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float1_on">
						<td>{{$lang.settings.player_field_float_location}}:</td>
						<td>
							<select name="float1_location" class="float1_on fixed_200">
								<option value="bottom" {{if $player_data.float1_location=='bottom'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
								<option value="top" {{if $player_data.float1_location=='top'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
							</select>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float1_on">
						<td>{{$lang.settings.player_field_float_size}}:</td>
						<td>
							<div class="de_vis_sw_select">
								<select id="float1_size" name="float1_size" class="float1_on">
									<option value="0" {{if $player_data.float1_size==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float1_size==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								&nbsp;
								<span class="float1_size_1">
									<input type="text" name="float1_size_width" maxlength="5" size="5" class="float1_on float1_size_1" value="{{$player_data.float1_size_width}}"/>
									x
									<input type="text" name="float1_size_height" maxlength="5" size="5" class="float1_on float1_size_1" value="{{$player_data.float1_size_height}}"/>
								</span>
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float1_on">
						<td>{{$lang.settings.player_field_float_file}}:</td>
						<td>
							<select name="float1_file_source" class="float1_on fixed_300">
								<option value="1" {{if $player_data.float1_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
								{{section name="data" start="1" loop="11"}}
									{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
									<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float1_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
								{{/section}}
							</select>
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
									<span class="js_param">accept={{$config.image_allowed_ext}}</span>
									{{if $player_data.float1_file!=''}}
										{{if in_array(end(explode(".",$player_data.float1_file)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$player_path}}/{{$player_data.float1_file}}</span>
										{{else}}
											<span class="js_param">download_url={{$player_path}}/{{$player_data.float1_file}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="float1_file" maxlength="100" class="fixed_150 float1_on" {{if $player_data.float1_file!=''}}value="{{$player_data.float1_file}}"{{/if}} readonly="readonly"/>
								<input type="hidden" name="float1_file_hash"/>
								<input type="button" class="de_fu_upload float1_on" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove float1_on {{if $player_data.float1_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								{{if $player_data.float1_file!=''}}
									{{if in_array(end(explode(".",$player_data.float1_file)),explode(",",$config.image_allowed_ext))}}
										<input type="button" class="de_fu_preview float1_on" value="{{$lang.common.attachment_btn_preview}}"/>
									{{else}}
										<input type="button" class="de_fu_download float1_on" value="{{$lang.common.attachment_btn_download}}"/>
									{{/if}}
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float1_on">
						<td>{{$lang.settings.player_field_float_url}}:</td>
						<td class="de_control">
							<select name="float1_url_source" id="float1_url_source" class="fixed_300 float1_on">
								<option value="1" {{if $player_data.float1_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
								<option value="2" {{if $player_data.float1_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							</select>
							<input type="text" name="float1_url" maxlength="255" class="fixed_400 float1_on" value="{{$player_data.float1_url}}"/>
						</td>
					</tr>
					<tr class="eg_header fixed_height_30">
						<td colspan="2"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="float2" name="enable_float2" value="1" {{if $player_data.enable_float2==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_float2==1}}class="selected"{{/if}}>{{$lang.settings.player_field_float_enable|replace:"%1%":"2"}}</span></div></td>
					</tr>
					<tr class="eg_data fixed_height_30 float2_on">
						<td>{{$lang.settings.player_field_float_time}}:</td>
						<td>
							<input type="text" name="float2_time" class="float2_on dyn_full_size" maxlength="5" value="{{$player_data.float2_time}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float2_on">
						<td>{{$lang.settings.player_field_float_duration}}:</td>
						<td>
							<input type="text" name="float2_duration" class="float2_on dyn_full_size" maxlength="5" value="{{$player_data.float2_duration}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float2_on">
						<td>{{$lang.settings.player_field_float_location}}:</td>
						<td>
							<select name="float2_location" class="float2_on fixed_200">
								<option value="bottom" {{if $player_data.float2_location=='bottom'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
								<option value="top" {{if $player_data.float2_location=='top'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
							</select>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float2_on">
						<td>{{$lang.settings.player_field_float_size}}:</td>
						<td>
							<div class="de_vis_sw_select">
								<select id="float2_size" name="float2_size" class="float2_on">
									<option value="0" {{if $player_data.float2_size==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float2_size==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								&nbsp;
								<span class="float2_size_1">
									<input type="text" name="float2_size_width" maxlength="5" size="5" class="float2_on float2_size_1" value="{{$player_data.float2_size_width}}"/>
									x
									<input type="text" name="float2_size_height" maxlength="5" size="5" class="float2_on float2_size_1" value="{{$player_data.float2_size_height}}"/>
								</span>
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float2_on">
						<td>{{$lang.settings.player_field_float_file}}:</td>
						<td>
							<select name="float2_file_source" class="float2_on fixed_300">
								<option value="1" {{if $player_data.float2_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
								{{section name="data" start="1" loop="11"}}
									{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
									<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float2_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
								{{/section}}
							</select>
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
									<span class="js_param">accept={{$config.image_allowed_ext}}</span>
									{{if $player_data.float2_file!=''}}
										{{if in_array(end(explode(".",$player_data.float2_file)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$player_path}}/{{$player_data.float2_file}}</span>
										{{else}}
											<span class="js_param">download_url={{$player_path}}/{{$player_data.float2_file}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="float2_file" maxlength="100" class="fixed_150 float2_on" {{if $player_data.float2_file!=''}}value="{{$player_data.float2_file}}"{{/if}} readonly="readonly"/>
								<input type="hidden" name="float2_file_hash"/>
								<input type="button" class="de_fu_upload float2_on" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove float2_on {{if $player_data.float2_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								{{if $player_data.float2_file!=''}}
									{{if in_array(end(explode(".",$player_data.float2_file)),explode(",",$config.image_allowed_ext))}}
										<input type="button" class="de_fu_preview float2_on" value="{{$lang.common.attachment_btn_preview}}"/>
									{{else}}
										<input type="button" class="de_fu_download float2_on" value="{{$lang.common.attachment_btn_download}}"/>
									{{/if}}
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float2_on">
						<td>{{$lang.settings.player_field_float_url}}:</td>
						<td class="de_control">
							<select name="float2_url_source" id="float2_url_source" class="fixed_300 float2_on">
								<option value="1" {{if $player_data.float2_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
								<option value="2" {{if $player_data.float2_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							</select>
							<input type="text" name="float2_url" maxlength="255" class="fixed_400 float2_on" value="{{$player_data.float2_url}}"/>
						</td>
					</tr>
					<tr class="eg_header fixed_height_30">
						<td colspan="2"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="float3" name="enable_float3" value="1" {{if $player_data.enable_float3==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_float3==1}}class="selected"{{/if}}>{{$lang.settings.player_field_float_enable|replace:"%1%":"3"}}</span></div></td>
					</tr>
					<tr class="eg_data fixed_height_30 float3_on">
						<td>{{$lang.settings.player_field_float_time}}:</td>
						<td>
							<input type="text" name="float3_time" class="float3_on dyn_full_size" maxlength="5" value="{{$player_data.float3_time}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float3_on">
						<td>{{$lang.settings.player_field_float_duration}}:</td>
						<td>
							<input type="text" name="float3_duration" class="float3_on dyn_full_size" maxlength="5" value="{{$player_data.float3_duration}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float3_on">
						<td>{{$lang.settings.player_field_float_location}}:</td>
						<td>
							<select name="float3_location" class="float3_on fixed_200">
								<option value="bottom" {{if $player_data.float3_location=='bottom'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
								<option value="top" {{if $player_data.float3_location=='top'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
							</select>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float3_on">
						<td>{{$lang.settings.player_field_float_size}}:</td>
						<td>
							<div class="de_vis_sw_select">
								<select id="float3_size" name="float3_size" class="float3_on">
									<option value="0" {{if $player_data.float3_size==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float3_size==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								&nbsp;
								<span class="float3_size_1">
									<input type="text" name="float3_size_width" maxlength="5" size="5" class="float3_on float3_size_1" value="{{$player_data.float3_size_width}}"/>
									x
									<input type="text" name="float3_size_height" maxlength="5" size="5" class="float3_on float3_size_1" value="{{$player_data.float3_size_height}}"/>
								</span>
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float3_on">
						<td>{{$lang.settings.player_field_float_file}}:</td>
						<td>
							<select name="float3_file_source" class="float3_on fixed_300">
								<option value="1" {{if $player_data.float3_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
								{{section name="data" start="1" loop="11"}}
									{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
									<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float3_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
								{{/section}}
							</select>
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
									<span class="js_param">accept={{$config.image_allowed_ext}}</span>
									{{if $player_data.float3_file!=''}}
										{{if in_array(end(explode(".",$player_data.float3_file)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$player_path}}/{{$player_data.float3_file}}</span>
										{{else}}
											<span class="js_param">download_url={{$player_path}}/{{$player_data.float3_file}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="float3_file" maxlength="100" class="fixed_150 float3_on" {{if $player_data.float3_file!=''}}value="{{$player_data.float3_file}}"{{/if}} readonly="readonly"/>
								<input type="hidden" name="float3_file_hash"/>
								<input type="button" class="de_fu_upload float3_on" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove float3_on {{if $player_data.float3_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								{{if $player_data.float3_file!=''}}
									{{if in_array(end(explode(".",$player_data.float3_file)),explode(",",$config.image_allowed_ext))}}
										<input type="button" class="de_fu_preview float3_on" value="{{$lang.common.attachment_btn_preview}}"/>
									{{else}}
										<input type="button" class="de_fu_download float3_on" value="{{$lang.common.attachment_btn_download}}"/>
									{{/if}}
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float3_on">
						<td>{{$lang.settings.player_field_float_url}}:</td>
						<td class="de_control">
							<select name="float3_url_source" id="float3_url_source" class="fixed_300 float3_on">
								<option value="1" {{if $player_data.float3_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
								<option value="2" {{if $player_data.float3_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							</select>
							<input type="text" name="float3_url" maxlength="255" class="fixed_400 float3_on" value="{{$player_data.float3_url}}"/>
						</td>
					</tr>
					<tr class="eg_header fixed_height_30">
						<td colspan="2"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="float4" name="enable_float4" value="1" {{if $player_data.enable_float4==1}}checked="checked"{{/if}}/><span {{if $player_data.enable_float4==1}}class="selected"{{/if}}>{{$lang.settings.player_field_float_enable|replace:"%1%":"4"}}</span></div></td>
					</tr>
					<tr class="eg_data fixed_height_30 float4_on">
						<td>{{$lang.settings.player_field_float_time}}:</td>
						<td>
							<input type="text" name="float4_time" class="float4_on dyn_full_size" maxlength="5" value="{{$player_data.float4_time}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float4_on">
						<td>{{$lang.settings.player_field_float_duration}}:</td>
						<td>
							<input type="text" name="float4_duration" class="float4_on dyn_full_size" maxlength="5" value="{{$player_data.float4_duration}}"/>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float4_on">
						<td>{{$lang.settings.player_field_float_location}}:</td>
						<td>
							<select name="float4_location" class="float4_on fixed_200">
								<option value="bottom" {{if $player_data.float4_location=='bottom'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_bottom}}</option>
								<option value="top" {{if $player_data.float4_location=='top'}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_location_top}}</option>
							</select>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float4_on">
						<td>{{$lang.settings.player_field_float_size}}:</td>
						<td>
							<div class="de_vis_sw_select">
								<select id="float4_size" name="float4_size" class="float4_on">
									<option value="0" {{if $player_data.float4_size==0}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_auto}}</option>
									<option value="1" {{if $player_data.float4_size==1}}selected="selected"{{/if}}>{{$lang.settings.player_field_float_size_resize}}</option>
								</select>
								&nbsp;
								<span class="float4_size_1">
									<input type="text" name="float4_size_width" maxlength="5" size="5" class="float4_on float4_size_1" value="{{$player_data.float4_size_width}}"/>
									x
									<input type="text" name="float4_size_height" maxlength="5" size="5" class="float4_on float4_size_1" value="{{$player_data.float4_size_height}}"/>
								</span>
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float4_on">
						<td>{{$lang.settings.player_field_float_file}}:</td>
						<td>
							<select name="float4_file_source" class="float4_on fixed_300">
								<option value="1" {{if $player_data.float4_file_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_global}}</option>
								{{section name="data" start="1" loop="11"}}
									{{assign var="custom_field_name" value="CS_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
									<option value="{{$smarty.section.data.index+1}}" {{if $player_data.float4_file_source==$smarty.section.data.index+1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_source_cs|replace:"%1%":$options[$custom_field_name]}}</option>
								{{/section}}
							</select>
							<div class="de_fu">
								<div class="js_params">
									<span class="js_param">title={{$lang.settings.player_field_float_file}}</span>
									<span class="js_param">accept={{$config.image_allowed_ext}}</span>
									{{if $player_data.float4_file!=''}}
										{{if in_array(end(explode(".",$player_data.float4_file)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$player_path}}/{{$player_data.float4_file}}</span>
										{{else}}
											<span class="js_param">download_url={{$player_path}}/{{$player_data.float4_file}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="float4_file" maxlength="100" class="fixed_150 float4_on" {{if $player_data.float4_file!=''}}value="{{$player_data.float4_file}}"{{/if}} readonly="readonly"/>
								<input type="hidden" name="float4_file_hash"/>
								<input type="button" class="de_fu_upload float4_on" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove float4_on {{if $player_data.float4_file==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								{{if $player_data.float4_file!=''}}
									{{if in_array(end(explode(".",$player_data.float4_file)),explode(",",$config.image_allowed_ext))}}
										<input type="button" class="de_fu_preview float4_on" value="{{$lang.common.attachment_btn_preview}}"/>
									{{else}}
										<input type="button" class="de_fu_download float4_on" value="{{$lang.common.attachment_btn_download}}"/>
									{{/if}}
								{{/if}}
							</div>
						</td>
					</tr>
					<tr class="eg_data fixed_height_30 float4_on">
						<td>{{$lang.settings.player_field_float_url}}:</td>
						<td class="de_control">
							<select name="float4_url_source" id="float4_url_source" class="fixed_300 float4_on">
								<option value="1" {{if $player_data.float4_url_source==1}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_global}}</option>
								<option value="2" {{if $player_data.float4_url_source==2}}selected="selected"{{/if}}>{{$lang.settings.common_advertising_url_source_cs|replace:"%1%":$lang.categorization.content_source_field_url}}</option>
							</select>
							<input type="text" name="float4_url" maxlength="255" class="fixed_400 float4_on" value="{{$player_data.float4_url}}"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		{{if $smarty.request.page=='embed' && $smarty.get.embed_profile_id==''}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.player_divider_embed_access_control}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.player_field_black_list_countries}}:</td>
				<td class="de_control">
					<textarea name="black_list_countries" class="dyn_full_size" cols="40" rows="3">{{$player_data.black_list_countries}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_black_list_countries_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.player_field_black_list_domains}}:</td>
				<td class="de_control">
					<textarea name="black_list_domains" class="dyn_full_size" cols="40" rows="3">{{$player_data.black_list_domains}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_black_list_domains_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.player_field_player_replacement_html}}:</td>
				<td class="de_control">
					<textarea name="player_replacement_html" class="html_code_editor dyn_full_size" cols="40" rows="10">{{$player_data.player_replacement_html}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.player_field_player_replacement_html_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/></td>
		</tr>
	</table>
</form>

<div id="custom_js" class="js_params">
	<span class="js_param">buildPlayerAccessLevelLogic=call</span>
	<span class="js_param">buildPlayerEmbedProfileLogic=call</span>
</div>