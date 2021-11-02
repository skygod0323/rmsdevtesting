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

{{if $smarty.get.action=='mark_deleted' || $smarty.get.action=='change_deleted'}}
<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		{{if $smarty.get.action=='mark_deleted'}}
			<input type="hidden" name="action" value="mark_deleted_complete"/>
			<input type="hidden" name="delete_id" value="{{$smarty.get.delete_id}}"/>
		{{else}}
			<input type="hidden" name="action" value="change_deleted_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2">
				<div>
					<a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a>
					/
					{{if $smarty.get.action=='mark_deleted'}}
						{{$lang.videos.video_mark_deleted}}
					{{else}}
						{{if $smarty.post.title!=''}}
							{{$lang.videos.video_edit_deleted|replace:"%1%":$smarty.post.title}}
						{{else}}
							{{$lang.videos.video_edit_deleted|replace:"%1%":$smarty.post.video_id}}
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if count($smarty.post.delete_videos)>0}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_delete_count}}:</td>
				<td class="de_control" colspan="3">
					{{$smarty.post.delete_videos|@count}}
					{{if count($smarty.post.delete_videos)<=20}}
						{{assign var="delete_titles" value=""}}
						{{foreach from=$smarty.post.delete_videos|smarty:nodefaults item="item" name="deleted"}}
							{{assign var="delete_titles" value="`$delete_titles``$item.video_id` / `$item.title`"}}
							{{if !$smarty.foreach.deleted.last}}
								{{assign var="delete_titles" value="`$delete_titles`, "}}
							{{/if}}
						{{/foreach}}
						({{$delete_titles}})
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.website_link!=''}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_website_link}}:</td>
				<td class="de_control" colspan="3">
					<a href="{{$smarty.post.website_link}}" rel="external">{{$smarty.post.website_link}}</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.video_field_delete_reason}}:</td>
			<td class="de_control" colspan="3">
				<select id="top_delete_reasons" class="fixed_400">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach from=$smarty.post.top_delete_reasons|smarty:nodefaults item="item"}}
						<option value="{{$item.delete_reason}}">{{$item.delete_reason}} ({{$item.total_videos}})</option>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.video_field_delete_reason_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.video_field_delete_reason_text}}:</td>
			<td class="de_control" colspan="3">
				<textarea id="delete_reason" name="delete_reason" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}}" cols="40" rows="3">{{$smarty.post.delete_reason}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.videos.video_field_delete_reason_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action=='mark_deleted'}}
			<tr>
				<td class="de_label"></td>
				<td class="de_control"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="delete_send_email" name="delete_send_email" value="1"/><span>{{$lang.videos.video_field_delete_email}}</span></div></td>
			</tr>
			<tr class="delete_send_email_on">
				<td class="de_label de_required">{{$lang.videos.video_field_delete_email_to}} (*):</td>
				<td class="de_control"><input type="text" name="delete_send_email_to" class="dyn_full_size"/></td>
			</tr>
			<tr class="delete_send_email_on">
				<td class="de_label de_required">{{$lang.videos.video_field_delete_email_subject}} (*):</td>
				<td class="de_control">
					<input type="text" name="delete_send_email_subject" class="dyn_full_size" value="{{$smarty.session.save.$page_name.delete_send_email_subject}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.videos.video_field_delete_email_subject_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="delete_send_email_on">
				<td class="de_label de_required">{{$lang.videos.video_field_delete_email_body}} (*):</td>
				<td class="de_control">
					<textarea name="delete_send_email_body" class="dyn_full_size" rows="10" cols="40">{{$smarty.session.save.$page_name.delete_send_email_body}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.videos.video_field_delete_email_body_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2">
				{{if $smarty.get.action=='mark_deleted'}}
					<input type="submit" name="save_default" value="{{$lang.videos.video_btn_mark_deleted}}"/>
				{{else}}
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
				{{/if}}
			</td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildDeleteReasonChangeLogic=call()</span>
</div>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('videos|edit_all',$smarty.session.permissions) || (in_array('videos|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}
{{if in_array('videos|edit_title',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_title value=1}}
{{else}}
	{{assign var=can_edit_title value=0}}
{{/if}}
{{if in_array('videos|edit_dir',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_dir value=1}}
{{else}}
	{{assign var=can_edit_dir value=0}}
{{/if}}
{{if in_array('videos|edit_description',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_desc value=1}}
{{else}}
	{{assign var=can_edit_desc value=0}}
{{/if}}
{{if in_array('videos|edit_tags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_tags value=1}}
{{else}}
	{{assign var=can_edit_tags value=0}}
{{/if}}
{{if in_array('videos|edit_content_source',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_cs value=1}}
{{else}}
	{{assign var=can_edit_cs value=0}}
{{/if}}
{{if in_array('videos|edit_dvd',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_dvd value=1}}
{{else}}
	{{assign var=can_edit_dvd value=0}}
{{/if}}
{{if in_array('videos|edit_post_date',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_date value=1}}
{{else}}
	{{assign var=can_edit_date value=0}}
{{/if}}
{{if in_array('videos|edit_user',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_user value=1}}
{{else}}
	{{assign var=can_edit_user value=0}}
{{/if}}
{{if in_array('videos|edit_status',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_status value=1}}
{{else}}
	{{assign var=can_edit_status value=0}}
{{/if}}
{{if in_array('videos|edit_type',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_type value=1}}
{{else}}
	{{assign var=can_edit_type value=0}}
{{/if}}
{{if in_array('videos|edit_access_level',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_access_level value=1}}
{{else}}
	{{assign var=can_edit_access_level value=0}}
{{/if}}
{{if in_array('videos|edit_tokens',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_tokens value=1}}
{{else}}
	{{assign var=can_edit_tokens value=0}}
{{/if}}
{{if in_array('videos|edit_embed',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_embed value=1}}
{{else}}
	{{assign var=can_edit_embed value=0}}
{{/if}}
{{if in_array('videos|edit_url',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_url value=1}}
{{else}}
	{{assign var=can_edit_url value=0}}
{{/if}}
{{if in_array('videos|edit_pseudo_url',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_pseudo_url value=1}}
{{else}}
	{{assign var=can_edit_pseudo_url value=0}}
{{/if}}
{{if in_array('videos|edit_duration',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_duration value=1}}
{{else}}
	{{assign var=can_edit_duration value=0}}
{{/if}}
{{if in_array('videos|edit_categories',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_categories value=1}}
{{else}}
	{{assign var=can_edit_categories value=0}}
{{/if}}
{{if in_array('videos|edit_models',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_models value=1}}
{{else}}
	{{assign var=can_edit_models value=0}}
{{/if}}
{{if in_array('videos|edit_flags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_flags value=1}}
{{else}}
	{{assign var=can_edit_flags value=0}}
{{/if}}
{{if in_array('videos|edit_release_year',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_release_year value=1}}
{{else}}
	{{assign var=can_edit_release_year value=0}}
{{/if}}
{{if in_array('videos|edit_custom',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_custom value=1}}
{{else}}
	{{assign var=can_edit_custom value=0}}
{{/if}}
{{if in_array('videos|edit_admin_flag',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_admin_flag value=1}}
{{else}}
	{{assign var=can_edit_admin_flag value=0}}
{{/if}}
{{if in_array('videos|manage_screenshots',$smarty.session.permissions)}}
	{{assign var=can_manage_screenshots value=1}}
{{else}}
	{{assign var=can_manage_screenshots value=0}}
{{/if}}
{{if in_array('videos|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}

{{assign var=can_edit_translations value=0}}
{{foreach name=data item=item from=$list_languages|smarty:nodefaults}}
	{{assign var="permission_id" value="localization|`$item.code`"}}
	{{if in_array($permission_id,$smarty.session.permissions)}}
		{{assign var=can_edit_translations value=1}}
	{{/if}}
{{/foreach}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
		<input type="hidden" name="custom1" value="{{$smarty.post.custom1}}"/>
		<input type="hidden" name="custom2" value="{{$smarty.post.custom2}}"/>
		<input type="hidden" name="custom3" value="{{$smarty.post.custom3}}"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="60%"/>
			<col width="5%"/>
			<col width="30%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="4">
				<div>
					<a href="{{$page_name}}">{{$lang.videos.submenu_option_videos_list}}</a>
					/
					{{if $smarty.get.action=='add_new'}}
						{{$lang.videos.video_add}}
					{{else}}
						{{if $smarty.post.dvd_id>0}}
							{{if in_array('dvds|view',$smarty.session.permissions)}}
								<a href="dvds.php?action=change&amp;item_id={{$smarty.post.dvd_id}}">{{$smarty.post.dvd.title}}</a>
							{{else}}
								{{$smarty.post.dvd.title}}
							{{/if}}
							/
						{{/if}}
						{{if $smarty.post.title!=''}}
							{{$lang.videos.video_edit|replace:"%1%":$smarty.post.title}}
						{{else}}
							{{$lang.videos.video_edit|replace:"%1%":$smarty.post.video_id}}
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/591-6-ways-to-add-videos-into-kvs">6 ways to add videos into KVS</a></span><br/>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.is_review_needed==1 && $can_edit_all==1}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.videos.video_divider_review}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.videos.video_divider_review_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_review_action}}:</td>
				<td class="de_control" colspan="3">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_vis_sw_select">
									<select id="is_reviewed" name="is_reviewed">
										<option value="0">{{$lang.videos.video_field_review_action_none}}</option>
										<option value="1">{{$lang.videos.video_field_review_action_approve}}</option>
										<option value="2" {{if $can_delete==0}}disabled="disabled"{{/if}}>{{$lang.videos.video_field_review_action_delete}}</option>
									</select>
								</div>
							</td>
						</tr>
						{{if $smarty.post.status_id==0}}
							<tr class="is_reviewed_1">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_activate" value="1"/><label>{{$lang.videos.video_field_review_action_activate}}</label></div>
								</td>
							</tr>
						{{/if}}
						<tr class="is_reviewed_2">
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_disable_user" value="1" class="is_reviewed_delete" {{if !in_array('users|edit_all',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_review_action_disable_user|replace:"%1%":$smarty.post.user}}</label></div>
							</td>
						</tr>
						{{if $smarty.post.user_domain!='' && $smarty.post.user_domain_blocked!=1}}
							<tr class="is_reviewed_2">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_domain" value="1" class="is_reviewed_delete" {{if !in_array('system|memberzone_settings',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_review_action_block_domain|replace:"%1%":$smarty.post.user_domain}}</label></div>
								</td>
							</tr>
						{{/if}}
						{{if $smarty.post.ip_mask!='0.0.0.*' && $smarty.post.ip_mask_blocked!=1}}
							<tr class="is_reviewed_2">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_mask" value="1" class="is_reviewed_delete" {{if !in_array('system|memberzone_settings',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_review_action_block_mask|replace:"%1%":$smarty.post.ip_mask}}</label></div>
								</td>
							</tr>
						{{/if}}
						{{if $smarty.post.ip!='0.0.0.0' && $smarty.post.ip_blocked!=1 && $smarty.post.ip_mask_blocked!=1}}
							<tr class="is_reviewed_2">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_ip" value="1" class="is_reviewed_delete" {{if !in_array('system|memberzone_settings',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_review_action_block_ip|replace:"%1%":$smarty.post.ip}}</label></div>
								</td>
							</tr>
						{{/if}}
						{{if $smarty.post.other_videos_need_review>0}}
							<tr class="is_reviewed_2">
								<td>
									{{assign var="max_delete_on_review" value=$config.max_delete_on_review|intval}}
									{{if $max_delete_on_review==0}}
										{{assign var="max_delete_on_review" value=30}}
									{{/if}}
									<div class="de_lv_pair"><input type="checkbox" name="is_delete_all_videos_from_user" value="1" class="is_reviewed_delete" {{if $can_delete!=1 || $smarty.post.other_videos_need_review>$max_delete_on_review}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_review_action_delete_other|replace:"%1%":$smarty.post.other_videos_need_review}}</label></div>
								</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
			{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_af_upload_zone}}:</td>
					<td class="de_control">
						<select name="af_upload_zone">
							<option value="0" {{if $smarty.post.af_upload_zone==0}}selected="selected"{{/if}}>{{$lang.videos.video_field_af_upload_zone_site}}</option>
							<option value="1" {{if $smarty.post.af_upload_zone==1}}selected="selected"{{/if}}>{{$lang.videos.video_field_af_upload_zone_memberarea}}</option>
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.video_field_af_upload_zone_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_separator" colspan="4">
				<div>
					{{$lang.videos.video_divider_general}}
					{{if $smarty.get.action=='change'}}
						/
						<a href="?action=video_log&amp;item_id={{$smarty.post.video_id}}" rel="external">{{$lang.videos.video_action_view_log}}</a>
						{{if in_array('system|background_tasks',$smarty.session.permissions)}}
							/
							<a href="log_background_tasks.php?no_filter=true&amp;se_video_id={{$smarty.post.video_id}}" rel="external">{{$lang.videos.video_action_view_tasks}}</a>
						{{/if}}
						{{if in_array('system|administration',$smarty.session.permissions)}}
							/
							<a href="log_audit.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id={{$smarty.post.video_id}}" rel="external">{{$lang.common.dg_actions_additional_view_audit_log}}</a>
						{{/if}}
						{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
							/
							<a href="stats_videos.php?no_filter=true&amp;se_group_by=date&amp;se_id={{$smarty.post.video_id}}" rel="external">{{$lang.videos.video_action_view_stats}}</a>
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="status_id_off {{if $smarty.post.status_id=='1'}}hidden{{/if}}">{{$lang.videos.video_field_title}}:</div>
				<div class="de_required status_id_on {{if $smarty.post.status_id=='0'}}hidden{{/if}}">{{$lang.videos.video_field_title}} (*):</div>
			</td>
			<td class="de_control" colspan="3">
				<div class="de_str_len">
					<input type="text" name="title" maxlength="255" class="dyn_full_size {{if $can_edit_title==1}}preserve_editing{{/if}}" value="{{$smarty.post.title}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_title_hint}}, <span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action=='change'}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_directory}}:</td>
				<td class="de_control" colspan="3">
					<input type="text" name="dir" maxlength="255" class="dyn_full_size {{if $options.VIDEO_REGENERATE_DIRECTORIES==1}}readonly_field{{elseif $can_edit_dir==1}}preserve_editing{{/if}}" value="{{$smarty.post.dir}}" {{if $options.VIDEO_REGENERATE_DIRECTORIES==1}}readonly="readonly"{{/if}}/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						{{if $options.VIDEO_REGENERATE_DIRECTORIES==1}}
							<br/><span class="de_hint">{{$lang.videos.video_field_directory_hint2|replace:"%1%":$lang.videos.video_field_title}}</span>
						{{else}}
							<br/><span class="de_hint">{{$lang.videos.video_field_directory_hint|replace:"%1%":$lang.videos.video_field_title}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.website_link!=''}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.videos.video_field_website_link}}:</td>
					<td class="de_control" colspan="3">
						<a href="{{$smarty.post.website_link}}" rel="external">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.video_field_description}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_str_len">
					<textarea name="description" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_desc==1}}preserve_editing{{/if}}" cols="40" rows="3">{{$smarty.post.description}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_label">{{$lang.videos.video_field_content_source}}:</td>
			<td class="de_control">
				<select name="content_source_id" {{if $can_edit_cs==1}}class="preserve_editing"{{/if}}>
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data_groups item=item_group from=$list_content_sources|smarty:nodefaults}}
						<optgroup label="{{$item_group[0].content_source_group_title|default:$lang.videos.video_field_content_source_no_group}}">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.content_source_id}}" {{if $smarty.post.content_source_id==$item.content_source_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.video_field_content_source_hint}}</span>
				{{/if}}
			</td>
			<td class="de_label de_required">{{$lang.videos.video_field_user}} (*):</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
					</div>
					<input type="text" name="user" maxlength="255" class="fixed_150 {{if $can_edit_user==1}}preserve_editing{{/if}}" value="{{$smarty.post.user}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_user_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_label">{{$lang.videos.video_field_dvd}}:</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_dvds.php</span>
					</div>
					<input type="text" name="dvd" maxlength="255" class="fixed_300 {{if $can_edit_dvd==1}}preserve_editing{{/if}}" value="{{$smarty.post.dvd.title}}"/>
				</div>
			</td>
			<td class="de_label">{{$lang.videos.video_field_release_year}}:</td>
			<td class="de_control" colspan="3">
				<input type="text" name="release_year" maxlength="10" size="10" class="fixed_150 {{if $can_edit_release_year==1}}preserve_editing{{/if}}" value="{{$smarty.post.release_year}}"/>
			</td>
		</tr>
		<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_label de_required">{{$lang.videos.video_field_post_date}} (*):</td>
			<td class="de_control">
				{{if $config.relative_post_dates=='true'}}
					<div class="de_vis_sw_radio">
						<table class="control_group">
							<tr>
								<td class="nowrap">
									<div class="de_lv_pair"><input id="post_date_option_fixed" type="radio" name="post_date_option" value="0" {{if $smarty.post.post_date_option!='1'}}checked="checked"{{/if}}/></div>
									{{if $can_edit_date==1}}
										{{html_select_date prefix='post_date_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date all_extra='class="preserve_editing post_date_option_fixed"' day_extra='id="post_date_day"' month_extra='id="post_date_month"' year_extra='id="post_date_year"'}}
									{{else}}
										{{html_select_date prefix='post_date_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date all_extra='class="post_date_option_fixed"'}}
									{{/if}}
									<input id="post_date_time" type="text" name="post_time" maxlength="5" size="4" class="post_date_option_fixed {{if $can_edit_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.post_date|date_format:"%H:%M"}}"/>
									{{if $smarty.get.action!='add_new' && $can_edit_date==1}}
										<input id="post_date_now" type="button" value="{{$lang.videos.video_field_post_date_now}}" class="post_date_option_fixed"/>
									{{/if}}
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.videos.video_field_post_date_hint1}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1" {{if $smarty.post.post_date_option=='1'}}checked="checked"{{/if}}/></div>
									<input type="text" name="relative_post_date" size="4" maxlength="5" class="fixed_100 post_date_option_relative {{if $can_edit_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.relative_post_date}}"/>
									{{$lang.videos.video_field_post_date_relative}}
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.videos.video_field_post_date_hint2}}</span>
									{{/if}}
								</td>
							</tr>
						</table>
					</div>
				{{else}}
					<div class="nowrap">
						{{if $can_edit_date==1}}
							{{html_select_date prefix='post_date_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date all_extra='class="preserve_editing"' day_extra='id="post_date_day"' month_extra='id="post_date_month"' year_extra='id="post_date_year"'}}
						{{else}}
							{{html_select_date prefix='post_date_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date}}
						{{/if}}
						<input id="post_date_time" type="text" name="post_time" maxlength="5" size="4" class="{{if $can_edit_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.post_date|date_format:"%H:%M"}}"/>
						{{if $smarty.get.action!='add_new' && $can_edit_date==1}}
							<input id="post_date_now" type="button" value="{{$lang.videos.video_field_post_date_now}}"/>
						{{/if}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.video_field_post_date_hint}}</span>
						{{/if}}
					</div>
				{{/if}}
			</td>
			<td class="de_label de_required">{{$lang.videos.video_field_type}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="is_private" name="is_private" {{if $can_edit_type==1}}class="preserve_editing"{{/if}}>
						{{if $smarty.get.action=='add_new' || ($smarty.get.action!='add_new' && $smarty.post.is_private=='2')}}
							<option value="2">{{$lang.videos.video_field_type_premium}}</option>
						{{/if}}
						{{if $smarty.get.action=='add_new' || ($smarty.get.action!='add_new' && ($smarty.post.is_private=='0' || $smarty.post.is_private=='1'))}}
							<option value="1" {{if $smarty.post.is_private=='1'}}selected="selected"{{/if}}>{{$lang.videos.video_field_type_private}}</option>
							<option value="0" {{if $smarty.post.is_private=='0'}}selected="selected"{{/if}}>{{$lang.videos.video_field_type_public}}</option>
						{{/if}}
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					{{if $smarty.get.action=='add_new' || ($smarty.get.action!='add_new' && ($smarty.post.is_private=='0' || $smarty.post.is_private=='1'))}}
						<span class="de_hint is_private_0">
							{{if $options.PUBLIC_VIDEOS_ACCESS==0}}{{$lang.videos.video_field_type_hint_all}}{{elseif $options.PUBLIC_VIDEOS_ACCESS==1}}{{$lang.videos.video_field_type_hint_members}}{{elseif $options.PUBLIC_VIDEOS_ACCESS==2}}{{$lang.videos.video_field_type_hint_premium}}{{/if}}
							{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
								<br/><a href="options.php?page=memberzone_settings" rel="external">{{$lang.videos.video_field_type_hint_configure}}</a>
							{{/if}}
						</span>
						<span class="de_hint is_private_1">
							{{if $options.PRIVATE_VIDEOS_ACCESS==3}}{{$lang.videos.video_field_type_hint_all}}{{elseif $options.PRIVATE_VIDEOS_ACCESS==0}}{{$lang.videos.video_field_type_hint_members}}{{elseif $options.PRIVATE_VIDEOS_ACCESS==1}}{{$lang.videos.video_field_type_hint_friends}}{{elseif $options.PRIVATE_VIDEOS_ACCESS==2}}{{$lang.videos.video_field_type_hint_premium}}{{/if}}
							{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
								<br/><a href="options.php?page=memberzone_settings" rel="external">{{$lang.videos.video_field_type_hint_configure}}</a>
							{{/if}}
						</span>
					{{/if}}
					{{if $smarty.get.action=='add_new' || ($smarty.get.action!='add_new' && $smarty.post.is_private=='2')}}
						<span class="de_hint is_private_2">
							{{if $options.PREMIUM_VIDEOS_ACCESS==0}}{{$lang.videos.video_field_type_hint_all}}{{elseif $options.PREMIUM_VIDEOS_ACCESS==1}}{{$lang.videos.video_field_type_hint_members}}{{elseif $options.PREMIUM_VIDEOS_ACCESS==2}}{{$lang.videos.video_field_type_hint_premium}}{{/if}}
							{{if in_array('system|memberzone_settings',$smarty.session.permissions)}}
								<br/><a href="options.php?page=memberzone_settings" rel="external">{{$lang.videos.video_field_type_hint_configure}}</a>
							{{/if}}
						</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_label">{{$lang.videos.video_field_server_group}}:</td>
			<td class="de_control">
				<div class="nowrap option_video option_gallery">
					<select id="server_group_id" name="server_group_id" class="fixed_300" {{if $smarty.get.action=='change'}}disabled="disabled"{{/if}}>
						{{if $smarty.get.action=='add_new'}}
							<option value="">{{$lang.videos.video_field_server_group_auto}}</option>
						{{/if}}
						{{foreach name=data item=item from=$list_server_groups|smarty:nodefaults}}
							<option value="{{$item.group_id}}" {{if $item.group_id==$smarty.post.server_group_id}}selected="selected"{{/if}}>{{$item.title}} ({{$lang.videos.video_field_server_group_free_space|replace:"%1%":$item.free_space|replace:"%2%":$item.total_space}})</option>
						{{/foreach}}
					</select>
					{{if $smarty.get.action!='add_new' && $can_edit_all==1}}
						<input id="change_storage_group" type="button" value="{{$lang.videos.video_field_server_group_change}}" {{if $smarty.post.server_group_migration_not_finished>0}}disabled="disabled"{{/if}}/>
					{{/if}}
					{{if $smarty.get.action=='add_new'}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.video_field_server_group_hint1}}</span>
						{{/if}}
					{{/if}}
				</div>
				<div class="option_video_url option_embed option_pseudo">
					{{$lang.common.undefined}}
				</div>
			</td>
			<td class="de_label">{{$lang.videos.video_field_access_level}}:</td>
			<td class="de_control">
				<select name="access_level_id" {{if $can_edit_access_level==1}}class="preserve_editing"{{/if}}>
					<option value="0" {{if $smarty.post.access_level_id==0}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_inherit}}</option>
					<option value="1" {{if $smarty.post.access_level_id==1}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_all}}</option>
					<option value="2" {{if $smarty.post.access_level_id==2}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_members}}</option>
					<option value="3" {{if $smarty.post.access_level_id==3}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_premium}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.video_field_access_level_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.video_field_admin_flag}}:</td>
			<td class="de_control">
				<select name="admin_flag_id" class="{{if $can_edit_admin_flag==1}}preserve_editing{{/if}}">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data item=item from=$list_flags_admins|smarty:nodefaults}}
						<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.admin_flag_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.video_field_admin_flag_hint}}</span>
				{{/if}}
			</td>
			<td class="de_label">{{$lang.videos.video_field_status}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_checkbox">
					<div class="de_lv_pair"><input id="status_id" type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked="checked"{{/if}} {{if $can_edit_status==1}}class="preserve_editing"{{/if}}/><label>{{$lang.videos.video_field_status_active}}</label></div>
				</div>
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_lock_website}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked="checked"{{/if}} {{if $can_edit_all==1}}class="preserve_editing"{{/if}}/><label>{{$lang.videos.video_field_lock_website_locked}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_lock_website_hint}}</span>
					{{/if}}
				</td>
				<td class="de_label">{{$lang.videos.video_field_ip}}:</td>
				<td class="de_control">
					{{if $config.safe_mode!='true'}}
						{{$smarty.post.ip}}
					{{else}}
						0.0.0.0
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type>=2 && (($smarty.post.is_private==2 && $memberzone_data.ENABLE_TOKENS_PREMIUM_VIDEO==1) || ($smarty.post.is_private!=2 && $memberzone_data.ENABLE_TOKENS_STANDARD_VIDEO==1))}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_tokens_cost}}:</td>
				<td class="de_control" colspan="3">
					<input type="text" name="tokens_required" maxlength="10" size="10" class="{{if $can_edit_tokens==1}}preserve_editing{{/if}}" value="{{$smarty.post.tokens_required}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						{{if $smarty.get.action=='add_new' || $smarty.post.is_private=='0' || $smarty.post.is_private=='1'}}
							<span class="de_hint is_private_0 is_private_1">{{$lang.videos.video_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_STANDARD_VIDEO}}</span>
						{{/if}}
						{{if $smarty.get.action=='add_new' || $smarty.post.is_private=='2'}}
							<span class="de_hint is_private_2">{{$lang.videos.video_field_tokens_cost_hint|replace:"%1%":$memberzone_data.DEFAULT_TOKENS_PREMIUM_VIDEO}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type==4 && $existing_albums_count>0}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_connected_albums}}:</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_albums.php</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=connected_album_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.video_field_connected_albums_empty}}</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name=data item=item from=$smarty.post.connected_albums|smarty:nodefaults}}
							<input type="hidden" name="connected_album_ids[]" value="{{$item.album_id}}" alt="{{if $item.title!=''}}{{$item.album_id}} / {{$item.title}}{{else}}{{$item.album_id}}{{/if}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_album" class="fixed_400" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
		{{/if}}
		{{if $config.installation_type>=3 && $existing_posts_count>0}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_connected_posts}}:</td>
				<td class="de_control" colspan="3">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_posts.php</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=connected_post_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.video_field_connected_posts_empty}}</span>
							{{if $can_edit_all!=1}}
								<span class="js_param">forbid_delete=true</span>
							{{/if}}
						</div>
						<div class="list"></div>
						{{foreach name=data item=item from=$smarty.post.connected_posts|smarty:nodefaults}}
							<input type="hidden" name="connected_post_ids[]" value="{{$item.post_id}}" alt="{{if $item.title!=''}}{{$item.post_type}} - {{$item.post_id}} / {{$item.title}}{{else}}{{$item.post_type}} - {{$item.post_id}}{{/if}}"/>
						{{/foreach}}
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_post" class="fixed_400" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_show_player==1}}
			<tr>
				<td class="de_label">{{$lang.videos.video_field_preview}}:</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.show_preview==1}}
						{{if $smarty.post.load_type_id==3}}
							<div style="width: {{$smarty.post.preview_dimensions.0}}px; height: {{$smarty.post.preview_dimensions.1}}px">
								{{$smarty.post.preview_embed|smarty:nodefaults}}
							</div>
						{{else}}
							<script type="text/javascript" src="{{$config.project_url}}/player/kt_player.js?v={{$config.project_version}}"></script>
							<div style="width: {{$smarty.post.preview_dimensions.0}}px; height: {{$smarty.post.preview_dimensions.1}}px"><div id="kt_player"></div></div>
							<script type="text/javascript">
								/* <![CDATA[ */
								var flashvars = {
									{{foreach key="name" item="value" name="flashvars" from=$smarty.post.preview_flashvars|smarty:nodefaults}}
										{{$name}}: '{{$value|replace:"'":"\'"|smarty:nodefaults}}'{{if !$smarty.foreach.flashvars.last}}, {{/if}}
									{{/foreach}}
								};
								kt_player('kt_player', '{{$config.project_url}}/player/kt_player.swf?v={{$config.project_version}}', '{{$smarty.post.preview_dimensions.0}}', '{{$smarty.post.preview_dimensions.1}}', flashvars);
								/* ]]> */
							</script>
						{{/if}}
					{{else}}
						{{$lang.videos.video_field_preview_no}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='change' && in_array('localization|view',$smarty.session.permissions) && $smarty.session.save.options.video_edit_show_translations=='1'}}
			{{assign var="header_output" value="1"}}
			{{foreach name=data item=item from=$list_languages|smarty:nodefaults}}
				{{assign var="permission_id" value="localization|`$item.code`"}}
				{{assign var="title_selector" value="title_`$item.code`"}}
				{{assign var="dir_selector" value="dir_`$item.code`"}}
				{{assign var="desc_selector" value="description_`$item.code`"}}
				{{if in_array($permission_id,$smarty.session.permissions)}}
					{{if $header_output==1}}
						<tr>
							<td class="de_separator" colspan="4"><div>{{$lang.videos.video_divider_localization}}</div></td>
						</tr>
						{{assign var="header_output" value="0"}}
					{{/if}}
					<tr>
						<td class="de_label">{{$lang.common.title_translation|replace:"%1%":$item.title}}:</td>
						<td class="de_control" colspan="3">
							<div class="de_str_len">
								<input type="text" name="{{$title_selector}}" maxlength="255" class="dyn_full_size preserve_editing" value="{{$smarty.post.$title_selector}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
					{{if $item.is_directories_localize==1}}
						<tr>
							<td class="de_label">{{$lang.common.directory_translation|replace:"%1%":$item.title}}:</td>
							<td class="de_control" colspan="3">
								<div class="de_str_len">
									<input type="text" name="{{$dir_selector}}" maxlength="255" class="dyn_full_size preserve_editing" value="{{$smarty.post.$dir_selector}}"/>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.videos.video_field_directory_hint_translation|replace:"%1%":$item.title|replace:"%2%":$lang.common.title_translation|replace:"%1%":$item.title}}</span>
									{{/if}}
								</div>
							</td>
						</tr>
					{{/if}}
					{{if $item.translation_scope_videos==0}}
						<tr>
							<td class="de_label">{{$lang.common.description_translation|replace:"%1%":$item.title}}:</td>
							<td class="de_control" colspan="3">
								<div class="de_str_len">
									<textarea name="{{$desc_selector}}" class="dyn_full_size preserve_editing {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}}" cols="40" rows="3">{{$smarty.post.$desc_selector}}</textarea>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
									{{/if}}
								</div>
							</td>
						</tr>
					{{/if}}
				{{/if}}
			{{/foreach}}
		{{/if}}
		<tr>
			<td class="de_separator" colspan="4"><div>{{$lang.videos.video_divider_categorization}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.video_field_tags}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						{{if $can_edit_tags!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.videos.video_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
					{{if $can_edit_tags==1}}
						<div class="controls">
							<input type="text" name="new_tag" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.video_field_tags_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div>{{$lang.videos.video_field_categories}}:</div>
			</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=category_ids[]</span>
						{{if in_array('categories|add',$smarty.session.permissions)}}
							<span class="js_param">allow_creation=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.videos.video_field_categories_empty}}</span>
						{{if $can_edit_categories!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_categories==1}}
						<div class="controls">
							<input type="text" name="new_category" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.video_field_categories_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div>{{$lang.videos.video_field_models}}:</div>
			</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=model_ids[]</span>
						{{if in_array('models|add',$smarty.session.permissions)}}
							<span class="js_param">allow_creation=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.videos.video_field_models_empty}}</span>
						{{if $can_edit_models!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.models|smarty:nodefaults}}
						<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_models==1}}
						<div class="controls">
							<input type="text" name="new_model" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.video_field_models_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">
					<div>{{$lang.videos.video_field_flags}}:</div>
				</td>
				<td class="de_control" colspan="3">
					<div class="de_deletable_list">
						<div class="js_params">
							<span class="js_param">submit_name=delete_flags[]</span>
							<span class="js_param">empty_message={{$lang.videos.video_field_flags_empty}}</span>
						</div>
						<div class="list">
							{{if count($smarty.post.flags)>0}}
								{{foreach name=data item=item from=$smarty.post.flags|smarty:nodefaults}}
									{{if $can_edit_flags==1}}
										<a name="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}}){{if !$smarty.foreach.data.last}}, {{/if}}</a>
									{{else}}
										<span>{{$item.title}} ({{$item.votes}}){{if !$smarty.foreach.data.last}}, {{/if}}</span>
									{{/if}}
								{{/foreach}}
								<div class="clear_both"></div>
							{{else}}
								{{$lang.videos.video_field_flags_empty}}
							{{/if}}
						</div>
					</div>
				</td>
			</tr>
			{{if count($list_post_process_plugins)>0 && $can_edit_all==1}}
				<tr>
					<td class="de_label">{{$lang.videos.video_field_categorization_plugins}}:</td>
					<td class="de_control">
						<table class="control_group">
							{{foreach item=item from=$list_post_process_plugins|smarty:nodefaults}}
								<tr><td>
									<div class="de_lv_pair"><input type="checkbox" name="post_process_plugins[]" value="{{$item.plugin_id}}"/> <label>{{$lang.videos.video_field_categorization_plugins_run|replace:"%1%":$item.title}}</label></div>
								</td></tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_separator" colspan="4"><div>{{$lang.videos.video_divider_content}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/250-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different">What video types are supported in KVS and how they are different</a></span><br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/577-different-ways-to-upload-video-files-into-kvs">Different ways to upload video files into KVS</a></span>
				</td>
			</tr>
		{{/if}}
		<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_label">
				{{if $smarty.get.action=='add_new'}}
					<div class="de_required">{{$lang.videos.video_field_load_type}} (*):</div>
				{{else}}
					<div>{{$lang.videos.video_field_load_type}}:</div>
				{{/if}}
			</td>
			<td class="de_control" colspan="3">
				<div class="de_vis_sw_radio">
					<div class="de_lv_pair"><input id="option_video" type="radio" name="video_adding_option" value="1" {{if $smarty.post.load_type_id==1 || $smarty.post.load_type_id==4}}checked="checked"{{/if}} {{if $smarty.get.action=='change'}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_load_type_file}}</label></div>
					<div class="de_lv_pair"><input id="option_video_url" type="radio" name="video_adding_option" value="2" {{if $smarty.post.load_type_id==2}}checked="checked"{{/if}} {{if $smarty.get.action=='change'}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_load_type_url}}</label></div>
					<div class="de_lv_pair"><input id="option_embed" type="radio" name="video_adding_option" value="3" {{if $smarty.post.load_type_id==3}}checked="checked"{{/if}} {{if $smarty.get.action=='change'}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_load_type_embed}}</label></div>
					<div class="de_lv_pair"><input id="option_pseudo" type="radio" name="video_adding_option" value="5" {{if $smarty.post.load_type_id==5}}checked="checked"{{/if}} {{if $smarty.get.action=='change'}}disabled="disabled"{{/if}}/><label>{{$lang.videos.video_field_load_type_pseudo}}</label></div>
				</div>
			</td>
		</tr>
		{{if $smarty.post.gallery_url!=''}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_gallery_url}}:</td>
				<td class="de_control" colspan="3">
					<a href="{{$smarty.post.gallery_url}}" rel="external">{{$smarty.post.gallery_url}}</a>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==1 || $smarty.post.source_file.url!=''}}
			<tr {{if $smarty.get.action=='add_new'}}class="option_video"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">
					{{if $smarty.get.action=='add_new'}}
						<div class="de_required">{{$lang.videos.video_field_video_file}} (*):</div>
					{{else}}
						<div>{{$lang.videos.video_field_video_file}}:</div>
					{{/if}}
				</td>
				<td class="de_control" colspan="3">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.video_field_video_file}}</span>
							<span class="js_param">accept={{$config.video_allowed_ext}}</span>
							{{if $smarty.get.action!='add_new'}}
								<span class="js_param">on_upload_finished=videoSourceFileUploadFinished</span>
							{{/if}}
							{{if $smarty.post.source_file.url!=''}}
								<span class="js_param">download_url={{$smarty.post.source_file.url}}</span>
							{{/if}}
						</div>
						<input type="text" name="video" class="fixed_400" maxlength="100" {{if $smarty.post.source_file.url!=''}}value="{{$smarty.post.video_id}}.tmp [{{$smarty.post.source_file.dimensions.0}}x{{$smarty.post.source_file.dimensions.1}}, {{$smarty.post.source_file.duration_string}}, {{$smarty.post.source_file.file_size_string}}]"{{/if}} readonly="readonly"/>
						<input type="hidden" name="video_hash"/>
						{{if $can_edit_all==1}}
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove {{if $smarty.post.source_file.url==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{/if}}
						<input type="button" class="de_fu_download {{if $smarty.post.source_file.url==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_download}}"/>
					</div>
					{{if $smarty.get.action=='add_new'}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.video_field_video_file_hint}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.get.action!='add_new'}}
				<tr id="video_source_file_options" {{if $smarty.post.source_file.url=='' || $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td></td>
					<td class="de_control" colspan="3">
						<table class="control_group">
							<tr>
								<td>
									{{if $smarty.post.load_type_id==1}}
										<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="video_recreate_formats" name="video_recreate_formats" value="1" {{if $can_edit_all==1}}class="preserve_editing"{{/if}} {{if $smarty.post.source_file.url==''}}checked="checked"{{/if}}/><label>{{$lang.videos.video_field_video_file_formats}}</label></div>
									{{/if}}
									<div class="de_lv_pair"><input type="checkbox" name="video_recreate_screenshots" value="1" {{if $can_edit_all==1}}class="preserve_editing"{{/if}} {{if $smarty.post.source_file.url==''}}checked="checked"{{/if}}/><label>{{$lang.videos.video_field_video_file_screenshots}}</label></div>
								</td>
							</tr>
							{{if $smarty.post.load_type_id==1}}
								<tr class="video_recreate_formats_on">
									<td>
										{{foreach name=data item=item from=$smarty.post.formats_videos|smarty:nodefaults}}
											{{assign var="is_format_visible" value="0"}}
											{{if $item.video_type_id==0}}
												{{if $smarty.post.is_private==0 || $smarty.post.is_private==1}}
													{{assign var="is_format_visible" value="1"}}
												{{/if}}
											{{elseif $item.video_type_id==1}}
												{{if $smarty.post.is_private==2}}
													{{assign var="is_format_visible" value="1"}}
												{{/if}}
											{{/if}}
											{{if $is_format_visible}}
												<div class="de_lv_pair"><input type="checkbox" name="video_recreate_format_{{$item.format_video_id}}" value="1" {{if $can_edit_all==1}}class="preserve_editing"{{/if}} {{if $item.video.file_path!='' && $item.video.task.task_id==0}}checked="checked"{{/if}} {{if $item.video.task.task_id>0}}disabled="disabled"{{/if}}/><label>{{$item.title}}</label></div>
											{{/if}}
										{{/foreach}}
									</td>
								</tr>
							{{/if}}
						</table>
					</td>
				</tr>
			{{/if}}
			{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==1}}
				{{foreach name=data item=item from=$smarty.post.formats_videos|smarty:nodefaults}}
					{{assign var="is_format_visible" value="0"}}
					{{assign var="vis_class" value=""}}
					{{if $item.video_type_id==0}}
						{{if $smarty.post.is_private==0 || $smarty.post.is_private==1}}
							{{assign var="is_format_visible" value="1"}}
						{{/if}}
						{{assign var="vis_class" value="is_private_0 is_private_1"}}
					{{elseif $item.video_type_id==1}}
						{{if $smarty.post.is_private==2}}
							{{assign var="is_format_visible" value="1"}}
						{{/if}}
						{{assign var="vis_class" value="is_private_2"}}
					{{/if}}
					{{if $smarty.get.action=='add_new' || $is_format_visible}}
						<tr {{if $smarty.get.action=='add_new'}}class="option_video {{$vis_class}}"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
							{{assign var="is_format_required" value="0"}}
							{{if $smarty.get.action!='add_new' && $item.status_id==1}}
								{{assign var="is_format_required" value="1"}}
							{{/if}}
							<td class="de_label {{if $is_format_required==1}}de_required{{/if}}">{{$lang.videos.video_field_format_video|replace:"%1%":$item.title}}{{if $is_format_required==1}} (*){{/if}}:</td>
							<td class="de_control" colspan="3">
								{{if $item.video.task.task_id>0}}
									{{if $item.video.task.type_id==3}}
										<input type="text" class="fixed_400" maxlength="100" value="{{$lang.videos.video_field_format_video_upload}}" readonly="readonly"/>
									{{elseif $item.video.task.type_id==4}}
										<input type="text" class="fixed_400" maxlength="100" value="{{$lang.videos.video_field_format_video_create}}" readonly="readonly"/>
									{{elseif $item.video.task.type_id==5}}
										<input type="text" class="fixed_400" maxlength="100" value="{{$lang.videos.video_field_format_video_delete}}" readonly="readonly"/>
									{{/if}}
								{{else}}
									<div class="de_fu">
										<div class="js_params">
											<span class="js_param">title={{$lang.videos.video_field_format_video|replace:"%1%":$item.title}}</span>
											<span class="js_param">accept={{$config.video_allowed_ext}}{{if end(explode(".",$item.postfix))=='gif'}},gif{{/if}}</span>
											{{if $item.video.file_path!=''}}
												{{assign var="time" value=$smarty.now}}
												<span class="js_param">download_url={{$config.project_url}}/get_file/{{$smarty.post.server_group_id}}/{{$item.video.file_path}}/?ttl={{$time}}&amp;dsc={{"`$config.cv`/`$item.video.file_path`/`$time`"|md5}}&amp;download=true</span>
											{{/if}}
											{{if in_array(end(explode(".",$item.video.file_path)),explode(",",$config.player_allowed_ext))}}
												<span class="js_param">preview_url=preview_video.php?video_id={{$smarty.post.video_id}}&amp;postfix={{$item.postfix|urlencode}}</span>
												<span class="js_param">preview_use_window=true</span>
												{{if $item.video.dimensions.0>1 && $item.video.dimensions.1>1}}
													<span class="js_param">preview_window_size={{$item.video.dimensions.0}}x{{$item.video.dimensions.1}}</span>
												{{/if}}
											{{elseif end(explode(".",$item.video.file_path))=='gif'}}
												{{assign var="time" value=$smarty.now}}
												<span class="js_param">preview_url={{$config.project_url}}/get_file/{{$smarty.post.server_group_id}}/{{$item.video.file_path}}/?ttl={{$time}}&amp;dsc={{"`$config.cv`/`$item.video.file_path`/`$time`"|md5}}&amp;download=true</span>
											{{/if}}
										</div>
										<input type="text" name="format_video_{{$item.format_video_id}}" class="fixed_400" maxlength="100" {{if $item.video.file_path!=''}}value="{{$smarty.post.video_id}}{{$item.postfix}} [{{$item.video.dimensions.0}}x{{$item.video.dimensions.1}}, {{$item.video.duration_string}}, {{$item.video.file_size_string}}{{if $item.video.timeline_screen_amount>0}}, {{$lang.videos.video_field_format_video_timelines|replace:"%1%":$item.video.timeline_screen_amount}}{{/if}}]"{{/if}} readonly="readonly"/>
										<input type="hidden" name="format_video_{{$item.format_video_id}}_hash"/>
										{{if $can_edit_all==1}}
											<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
											{{if $is_format_required==0 || $smarty.get.action=='add_new'}}
												<input type="button" class="de_fu_remove {{if $item.video.file_path==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
											{{/if}}
										{{/if}}
										{{if in_array(end(explode(".",$item.video.file_path)),explode(",",$config.player_allowed_ext)) || end(explode(".",$item.video.file_path))=='gif'}}
											<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
										{{/if}}
										<input type="button" class="de_fu_download {{if $item.video.file_path==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_download}}"/>
									</div>
								{{/if}}
							</td>
						</tr>
					{{/if}}
				{{/foreach}}
			{{/if}}
		{{/if}}
		{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==3}}
			<tr {{if $smarty.get.action=='add_new'}}class="option_embed"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label de_required">{{$lang.videos.video_field_embed_code}} (*):</td>
				<td class="de_control" colspan="3"><textarea name="embed" class="dyn_full_size html_code_editor {{if $can_edit_embed==1}}preserve_editing{{/if}}" cols="40" rows="9">{{$smarty.post.embed}}</textarea></td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==5}}
			<tr {{if $smarty.get.action=='add_new'}}class="option_pseudo"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">
					<div class="de_required">{{$lang.videos.video_field_pseudo_url}} (*):</div>
				</td>
				<td class="de_control" colspan="3">
					<input type="text" name="pseudo_url" maxlength="255" class="dyn_full_size {{if $can_edit_pseudo_url==1}}preserve_editing{{/if}}" value="{{$smarty.post.pseudo_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_pseudo_url_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='add_new' || $smarty.post.load_type_id==5 || $smarty.post.load_type_id==3 || $smarty.post.load_type_id==2}}
			<tr {{if $smarty.get.action=='add_new'}}class="option_video_url option_embed option_pseudo"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">
					{{if $smarty.post.load_type_id==3 || $smarty.post.load_type_id==5}}
						<div>{{$lang.videos.video_field_video_url}}:</div>
					{{else}}
						<div class="de_required">{{$lang.videos.video_field_video_url}} (*):</div>
					{{/if}}
				</td>
				<td class="de_control" colspan="3">
					<input type="text" name="video_url" maxlength="255" class="dyn_full_size {{if $can_edit_url==1}}preserve_editing{{/if}}" value="{{$smarty.post.file_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						<span class="de_hint option_video_url">{{$lang.videos.video_field_video_url_hotlink_hint}}</span>
						<span class="de_hint option_embed">{{$lang.videos.video_field_video_url_embed_hint}}</span>
						<span class="de_hint option_pseudo">{{$lang.videos.video_field_video_url_pseudo_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr {{if $smarty.get.action=='add_new'}}class="option_embed option_video_url option_pseudo"{{/if}} {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
			<td class="de_label">
				{{if $smarty.get.action=='add_new'}}
					<div class="de_required option_embed option_pseudo">{{$lang.videos.video_field_duration}} (*):</div>
					<div class="option_video_url">{{$lang.videos.video_field_duration}}:</div>
				{{else}}
					<div class="de_required">{{$lang.videos.video_field_duration}} (*):</div>
				{{/if}}
			</td>
			<td class="de_control" colspan="3">
				<input type="text" name="duration" maxlength="32" class="fixed_100 {{if $can_edit_duration==1}}preserve_editing{{/if}}" value="{{$smarty.post.duration}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.video_field_duration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action=='add_new'}}
			<tr>
				<td class="de_label">
					<div class="option_video option_video_url">{{$lang.videos.video_field_screenshots_overview}}:</div>
					<div class="de_required option_embed option_pseudo hidden">{{$lang.videos.video_field_screenshots_overview}} (*):</div>
				</td>
				<td class="de_control" colspan="3">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.video_field_screenshots_overview}}</span>
							<span class="js_param">accept={{$config.jpeg_image_or_group_allowed_ext}}</span>
							<span class="js_param">multiple=true</span>
						</div>
						<input type="text" name="screenshots" maxlength="100" class="fixed_400" readonly="readonly"/>
						<input type="hidden" name="screenshots_hash"/>
						{{if $can_edit_all==1}}
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_screenshots_overview_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.video_field_screenshots_posters}}:</td>
				<td class="de_control" colspan="3">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.video_field_screenshots_posters}}</span>
							<span class="js_param">accept={{$config.jpeg_image_or_group_allowed_ext}}</span>
							<span class="js_param">multiple=true</span>
						</div>
						<input type="text" name="posters" maxlength="100" class="fixed_400" readonly="readonly"/>
						<input type="hidden" name="posters_hash"/>
						{{if $can_edit_all==1}}
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_screenshots_posters_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{elseif $smarty.post.screen_url==''}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_screenshots}}:</td>
				<td class="de_control" colspan="3">
					<a href="videos_screenshots.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manage_screenshots}}</a>
					{{if $can_manage_screenshots==1 && $smarty.post.grabbing_possible==1}}
						&nbsp;|&nbsp;
						<a href="videos_screenshots_grabbing.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manual_grabbing}}</a>
					{{/if}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.video_field_screenshots_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='change' && $smarty.post.rotator_enabled==1}}
			<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_separator" colspan="4"><div>{{$lang.videos.video_divider_rotator}}</div></td>
			</tr>
			<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_rotator_main}}:</td>
				<td class="de_control" colspan="3">
					{{$lang.videos.video_field_rotator_main_value|replace:"%1%":$smarty.post.rotator_views|replace:"%2%":$smarty.post.rotator_clicks|replace:"%3%":$smarty.post.rotator_ctr|replace:"%4%":$smarty.post.rotator_rank}}
				</td>
			</tr>
			<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_rotator_categories}}:</td>
				<td class="de_control" colspan="3">
					{{if count($smarty.post.rotator_categories)>0}}
						{{foreach name="data" item="item" from=$smarty.post.rotator_categories|smarty:nodefaults}}
							{{$item.title}}: <b>{{if $item.ctr==0}}0{{else}}{{$item.ctr|default:0|number_format:2}}{{/if}}</b>{{if !$smarty.foreach.data.last}},{{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.videos.video_field_rotator_categories_no}}
					{{/if}}
					{{if $smarty.post.rotator_categories_enabled==0}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.video_field_rotator_categories_hint}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
			<tr {{if $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.videos.video_field_rotator_tags}}:</td>
				<td class="de_control" colspan="3">
					{{if count($smarty.post.rotator_tags)>0}}
						{{foreach name="data" item="item" from=$smarty.post.rotator_tags|smarty:nodefaults}}
							{{$item.title}}: <b>{{if $item.ctr==0}}0{{else}}{{$item.ctr|default:0|number_format:2}}{{/if}}</b>{{if !$smarty.foreach.data.last}},{{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.videos.video_field_rotator_tags_no}}
					{{/if}}
					{{if $smarty.post.rotator_tags_enabled==0}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.video_field_rotator_tags_hint}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='change' && $smarty.post.screen_url!=''}}
			<tr>
				<td class="de_separator" colspan="4">
					<div>
						{{$lang.videos.video_divider_screenshots}}&nbsp;&nbsp;
						[
						<a href="videos_screenshots.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manage_screenshots}}</a>
						{{if $can_manage_screenshots==1 && $smarty.post.grabbing_possible==1}}
							&nbsp;|&nbsp;
							<a href="videos_screenshots_grabbing.php?item_id={{$smarty.post.video_id}}">{{$lang.videos.video_action_manual_grabbing}}</a>
						{{/if}}
						]
					</div>
				</td>
			</tr>
			{{if $smarty.post.screen_amount>0}}
				<tr>
					<td class="de_label" colspan="4">
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
						{{if $can_manage_screenshots==1}}
							&nbsp;&nbsp;
							<div class="de_lv_pair"><input id="delete_all" type="checkbox" name="delete_all" class="preserve_editing" autocomplete="off"/><label>{{$lang.videos.screenshots_mgmt_field_select_all}}</label></div>
							<div class="de_lv_pair"><input id="delete_do_not_fade" type="checkbox" name="delete_do_not_fade" value="1" class="preserve_editing" autocomplete="off" {{if $smarty.session.save.options.screenshots_select_fade_disabled=='1'}}checked="checked"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_select_do_not_fade}}</label></div>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_control" colspan="4">
						<div id="screenshots_container" class="de_img_list_preview de_img_list_delete_on_selection">
							<div class="js_params de_img_list_preview_callbacks">
								<span class="js_param">imageListPreviewHook=call</span>
							</div>
							<div class="de_img_list">
								<input type="hidden" name="screen_group" value="{{$smarty.post.screen_group}}"/>
								{{assign var="pos" value=1}}
								{{section name="screenshots" start="0" step="1" loop=$smarty.post.screen_amount}}
									<div id="item_{{$pos}}" class="de_img_list_item {{if $smarty.post.screen_main==$pos}}main{{/if}}">
										<a class="de_img_list_thumb" id="link_{{$pos}}" href="?action=screenshot_source&amp;group_id={{$smarty.post.screen_group}}&amp;item_id={{$smarty.post.video_id}}&amp;index={{$pos}}&amp;rnd={{$smarty.now}}">
											{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_auto}}
											{{if $smarty.post.info_screenshots[$pos].type=='uploaded'}}
												{{assign var="screenshot_type" value=$lang.videos.screenshots_mgmt_field_type_uploaded}}
											{{/if}}
											<img src="{{$smarty.post.screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt="{{$lang.javascript.image_list_text|replace:"%1%":$pos|replace:"%2%":$smarty.post.screen_amount}}, {{$screenshot_type}}"/>
											<i>{{$screenshot_type}}</i>
										</a>
										{{if $can_manage_screenshots==1}}
											<div class="de_img_list_options basic">
												<div class="de_lv_pair"><input type="radio" id="main_{{$pos}}" name="screen_main" value="{{$pos}}" {{if $smarty.post.screen_main==$pos}}checked="checked"{{/if}} {{if $can_manage_screenshots==1}}class="preserve_editing"{{/if}} {{if $can_manage_screenshots==0}}disabled="disabled"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_main}}</label></div>
												<div class="de_lv_pair"><input type="checkbox" id="delete_{{$pos}}" name="screen_delete[]" value="{{$pos}}" autocomplete="off" {{if $can_manage_screenshots==1}}class="preserve_editing"{{/if}} {{if $can_manage_screenshots==0}}disabled="disabled"{{/if}}/><label>{{$lang.videos.screenshots_mgmt_field_delete}}</label></div>
											</div>
										{{/if}}
										{{if is_array($smarty.post.rotator_screenshots)}}
											<div class="de_img_list_options">
												{{$lang.videos.screenshots_mgmt_field_ctr}}: {{$smarty.post.rotator_screenshots[$pos].ctr|default:0|number_format:2}}
												&nbsp;/&nbsp;
												{{$lang.videos.screenshots_mgmt_field_clicks}}: {{$smarty.post.rotator_screenshots[$pos].clicks|default:0}}
											</div>
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
					<td class="de_control" colspan="4">{{$lang.videos.screenshots_mgmt_divider_screenshots_posters_none}}</td>
				</tr>
			{{/if}}
		{{/if}}
		{{if $options.ENABLE_VIDEO_FIELD_1==1 || $options.ENABLE_VIDEO_FIELD_2==1 || $options.ENABLE_VIDEO_FIELD_3==1}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_separator" colspan="4"><div>{{$lang.videos.video_divider_customization}}</div></td>
			</tr>
			{{if $options.ENABLE_VIDEO_FIELD_1==1}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$options.VIDEO_FIELD_1_NAME}}:</td>
					<td class="de_control" colspan="3">
						<div class="de_str_len">
							<textarea name="custom1" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_VIDEO_FIELD_2==1}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$options.VIDEO_FIELD_2_NAME}}:</td>
					<td class="de_control" colspan="3">
						<div class="de_str_len">
							<textarea name="custom2" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_VIDEO_FIELD_3==1}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_label">{{$options.VIDEO_FIELD_3_NAME}}:</td>
					<td class="de_control" colspan="3">
						<div class="de_str_len">
							<textarea name="custom3" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_videos=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		{{if $can_edit_all || $can_edit_title || $can_edit_dir || $can_edit_desc || $can_edit_tags || $can_edit_cs || $can_edit_dvd || $can_edit_date || $can_edit_user ||
			 $can_edit_status || $can_edit_type || $can_edit_access_level || $can_edit_tokens || $can_edit_embed || $can_edit_url || $can_edit_pseudo_url || $can_edit_duration || $can_edit_categories || $can_edit_models || $can_edit_flags || $can_edit_release_year || $can_edit_custom || $can_edit_admin_flag || ($smarty.post.screen_url!='' && $can_manage_screenshots) || ($smarty.session.save.options.video_edit_show_translations=='1' && $can_edit_translations)}}
		<tr>
			<td class="de_action_group" colspan="4">
				{{if $smarty.get.action=='add_new'}}
					{{if $smarty.session.save.options.default_save_button==1}}
						<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
					{{else}}
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
					{{/if}}
				{{else}}
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
						<input type="submit" name="delete_and_edit" value="{{$lang.videos.video_btn_delete_and_edit_next}}" class="de_confirm" alt="{{$lang.videos.video_btn_delete_and_edit_next_confirm}}"/>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{/if}}
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildPostDateResetLogic=call('{{$smarty.now|date_format:"%Y"}}', '{{$smarty.now|date_format:"%m"}}', '{{$smarty.now|date_format:"%e"|trim}}', '{{$smarty.now|date_format:"%H"}}', '{{$smarty.now|date_format:"%M"}}')</span>
	<span class="js_param">buildServerGroupChangeLogic=call('{{$lang.videos.video_field_server_group_change_warning}}')</span>
	<span class="js_param">buildScreenshotsDeleteLogic=call()</span>
</div>

{{else}}

{{if in_array('videos|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('videos|edit_status',$smarty.session.permissions)}}
	{{assign var=can_edit_status value=1}}
{{else}}
	{{assign var=can_edit_status value=0}}
{{/if}}
{{if in_array('videos|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit_all value=1}}
	{{assign var=can_edit_status value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}
{{if in_array('system|background_tasks',$smarty.session.permissions)}}
	{{assign var=can_restart value=1}}
{{else}}
	{{assign var=can_restart value=0}}
{{/if}}
{{assign var=can_invoke_additional value=1}}
{{if $can_delete==1 || $can_edit_status==1 || $can_edit_all==1 || $can_restart==1}}
	{{assign var=can_invoke_batch value=1}}
{{else}}
	{{assign var=can_invoke_batch value=0}}
{{/if}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf">
		<div class="dgf">
			<table>
				<tr>
					<td class="dgf_label">{{$lang.common.dg_filter_show_on_page}}:</td>
					<td class="dgf_control"><input type="text" name="num_on_page" size="3" value="{{$smarty.session.save.$page_name.num_on_page}}"/></td>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control dgf_search">
						<input type="text" name="se_text" size="20" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}"/>
						{{if count($search_fields)>0}}
							<div class="dgf_search_layer hidden">
								<span>{{$lang.common.dg_filter_search_in}}:</span>
								<ul>
									{{assign var="search_everywhere" value="true"}}
									{{foreach from=$search_fields|smarty:nodefaults item="field"}}
										<li>
											{{assign var="option_id" value="se_text_`$field.id`"}}
											<input type="hidden" name="{{$option_id}}" value="0"/>
											<div class="dg_lv_pair"><input type="checkbox" name="{{$option_id}}" value="1" {{if $smarty.session.save.$page_name[$option_id]==1}}checked="checked"{{/if}}/><label>{{$field.title}}</label></div>
											{{if $smarty.session.save.$page_name[$option_id]!=1}}
												{{assign var="search_everywhere" value="false"}}
											{{/if}}
										</li>
									{{/foreach}}
									<li class="dgf_everywhere">
										<div class="dg_lv_pair"><input type="checkbox" name="se_text_all" value="1" {{if $search_everywhere=='true'}}checked="checked"{{/if}} class="dgf_everywhere"/><label>{{$lang.common.dg_filter_search_in_everywhere}}</label></div>
									</li>
								</ul>
							</div>
						{{/if}}
					</td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
					<td class="dgf_advanced_link">
						<a href="javascript:stub()" class="dgf_filters {{if $table_filtered==1}}dgf_selected{{/if}}">{{$lang.common.dg_filter_filters}}</a>
						<a href="javascript:stub()" class="dgf_columns">{{$lang.common.dg_filter_columns}}</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_filters {{if $table_filtered==0}}hidden{{/if}}">
			<input type="hidden" name="se_is_locked" value="0"/>
			<input type="hidden" name="se_has_errors" value="0"/>
			{{if $smarty.session.save.$page_name.se_ids!=''}}
				<table class="dgf_filter">
					<tr>
						<td class="dgf_label dgf_selected">{{$lang.videos.video_field_ids}}:</td>
						<td class="dgf_control">
							<input type="text" name="se_ids" size="20" value="{{$smarty.session.save.$page_name.se_ids}}"/>
						</td>
					</tr>
				</table>
			{{/if}}
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.videos.video_field_status_disabled}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.videos.video_field_status_active}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected="selected"{{/if}}>{{$lang.videos.video_field_status_error}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected="selected"{{/if}}>{{$lang.videos.video_field_status_in_process}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_status_id=='4'}}selected="selected"{{/if}}>{{$lang.videos.video_field_status_deleting}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_status_id=='5'}}selected="selected"{{/if}}>{{$lang.videos.video_field_status_deleted}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_load_type_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_load_type}}:</td>
					<td class="dgf_control">
						<select name="se_load_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_load_type_id=='1'}}selected="selected"{{/if}}>{{$lang.videos.video_field_load_type_file}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_load_type_id=='2'}}selected="selected"{{/if}}>{{$lang.videos.video_field_load_type_url}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_load_type_id=='3'}}selected="selected"{{/if}}>{{$lang.videos.video_field_load_type_embed}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_load_type_id=='5'}}selected="selected"{{/if}}>{{$lang.videos.video_field_load_type_pseudo}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_is_private!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_type}}:</td>
					<td class="dgf_control">
						<select name="se_is_private">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_is_private=='0'}}selected="selected"{{/if}}>{{$lang.videos.video_field_type_public}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_is_private=='1'}}selected="selected"{{/if}}>{{$lang.videos.video_field_type_private}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_is_private=='2'}}selected="selected"{{/if}}>{{$lang.videos.video_field_type_premium}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_access_level_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_access_level}}:</td>
					<td class="dgf_control">
						<select name="se_access_level_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_access_level_id=='0'}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_inherit}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_access_level_id=='1'}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_all}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_access_level_id=='2'}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_members}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_access_level_id=='3'}}selected="selected"{{/if}}>{{$lang.videos.video_field_access_level_premium}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_user}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_users.php</span>
							</div>
							<input type="text" name="se_user" size="20" value="{{$smarty.session.save.$page_name.se_user}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_content_source!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_content_source}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_content_sources.php</span>
							</div>
							<input type="text" name="se_content_source" size="20" value="{{$smarty.session.save.$page_name.se_content_source}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_dvd!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_dvd}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_dvds.php</span>
							</div>
							<input type="text" name="se_dvd" size="20" value="{{$smarty.session.save.$page_name.se_dvd}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_category!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_category}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_categories.php</span>
							</div>
							<input type="text" name="se_category" size="20" value="{{$smarty.session.save.$page_name.se_category}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_tag!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_tag}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_tags.php</span>
							</div>
							<input type="text" name="se_tag" size="20" value="{{$smarty.session.save.$page_name.se_tag}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_model!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_model}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_models.php</span>
							</div>
							<input type="text" name="se_model" size="20" value="{{$smarty.session.save.$page_name.se_model}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_flag_id!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_flag}}:</td>
					<td class="dgf_control">
						<select name="se_flag_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item=item_flag from=$list_flags_videos|smarty:nodefaults}}
								<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected="selected"{{/if}}>{{$item_flag.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_field!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_field}}:</td>
					<td class="dgf_control">
						<select name="se_field">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="empty/title" {{if $smarty.session.save.$page_name.se_field=="empty/title"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_title}}</option>
							<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_description}}</option>
							<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_rating}}</option>
							<option value="empty/video_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/video_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_visits}}</option>
							<option value="empty/video_viewed_unique" {{if $smarty.session.save.$page_name.se_field=="empty/video_viewed_unique"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_unique_visits}}</option>
							<option value="empty/tokens_required" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_required"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_tokens_cost}}</option>
							<option value="empty/content_source" {{if $smarty.session.save.$page_name.se_field=="empty/content_source"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_content_source}}</option>
							<option value="empty/dvd" {{if $smarty.session.save.$page_name.se_field=="empty/dvd"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_dvd}}</option>
							<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_tags}}</option>
							<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_categories}}</option>
							<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_models}}</option>
							<option value="empty/admin" {{if $smarty.session.save.$page_name.se_field=="empty/admin"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_admin}}</option>
							<option value="empty/admin_flag" {{if $smarty.session.save.$page_name.se_field=="empty/admin_flag"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_admin_flag}}</option>
							<option value="empty/gallery_url" {{if $smarty.session.save.$page_name.se_field=="empty/gallery_url"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_gallery_url}}</option>
							<option value="empty/comments" {{if $smarty.session.save.$page_name.se_field=="empty/comments"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_comments}}</option>
							<option value="empty/favourites" {{if $smarty.session.save.$page_name.se_field=="empty/favourites"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_favourites}}</option>
							<option value="empty/purchases" {{if $smarty.session.save.$page_name.se_field=="empty/purchases"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.video_field_purchases}}</option>
							{{section name="data" start="1" loop="4"}}
								{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="VIDEO_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_VIDEO_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
							<option value="filled/title" {{if $smarty.session.save.$page_name.se_field=="filled/title"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_title}}</option>
							<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_description}}</option>
							<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_rating}}</option>
							<option value="filled/video_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/video_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_visits}}</option>
							<option value="filled/video_viewed_unique" {{if $smarty.session.save.$page_name.se_field=="filled/video_viewed_unique"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_unique_visits}}</option>
							<option value="filled/tokens_required" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_required"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_tokens_cost}}</option>
							<option value="filled/content_source" {{if $smarty.session.save.$page_name.se_field=="filled/content_source"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_content_source}}</option>
							<option value="filled/dvd" {{if $smarty.session.save.$page_name.se_field=="filled/dvd"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_dvd}}</option>
							<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_tags}}</option>
							<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_categories}}</option>
							<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_models}}</option>
							<option value="filled/admin" {{if $smarty.session.save.$page_name.se_field=="filled/admin"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_admin}}</option>
							<option value="filled/admin_flag" {{if $smarty.session.save.$page_name.se_field=="filled/admin_flag"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_admin_flag}}</option>
							<option value="filled/gallery_url" {{if $smarty.session.save.$page_name.se_field=="filled/gallery_url"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_gallery_url}}</option>
							<option value="filled/comments" {{if $smarty.session.save.$page_name.se_field=="filled/comments"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_comments}}</option>
							<option value="filled/favourites" {{if $smarty.session.save.$page_name.se_field=="filled/favourites"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_favourites}}</option>
							<option value="filled/purchases" {{if $smarty.session.save.$page_name.se_field=="filled/purchases"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.video_field_purchases}}</option>
							{{section name="data" start="1" loop="4"}}
								{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="VIDEO_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_VIDEO_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_show_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_filter_other}}:</td>
					<td class="dgf_control">
						<select name="se_show_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="15" {{if $smarty.session.save.$page_name.se_show_id==15}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_from_admin}}</option>
							<option value="16" {{if $smarty.session.save.$page_name.se_show_id==16}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_from_website}}</option>
							<option value="17" {{if $smarty.session.save.$page_name.se_show_id==17}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_from_webmasters}}</option>
							<option value="18" {{if $smarty.session.save.$page_name.se_show_id==18}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_from_feed}}</option>
							<option value="21" {{if $smarty.session.save.$page_name.se_show_id==21}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_main_screen_1}}</option>
							<option value="22" {{if $smarty.session.save.$page_name.se_show_id==22}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_main_screen_not_1}}</option>
							<option value="23" {{if $smarty.session.save.$page_name.se_show_id==23}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_rotation_finished}}</option>
							<option value="24" {{if $smarty.session.save.$page_name.se_show_id==24}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_rotation_not_finished}}</option>
							{{foreach item=item_format from=$list_formats_videos|smarty:nodefaults}}
								<option value="wf/{{$item_format.postfix}}" {{if $smarty.session.save.$page_name.se_show_id=="wf/`$item_format.postfix`"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_format_w|replace:"%1%":$item_format.title}}</option>
							{{/foreach}}
							{{foreach item=item_format from=$list_formats_videos|smarty:nodefaults}}
								<option value="wof/{{$item_format.postfix}}" {{if $smarty.session.save.$page_name.se_show_id=="wof/`$item_format.postfix`"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_format_wo|replace:"%1%":$item_format.title}}</option>
							{{/foreach}}
							<option value="wq/360" {{if $smarty.session.save.$page_name.se_show_id=="wq/360"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"360p"}}</option>
							<option value="wq/480" {{if $smarty.session.save.$page_name.se_show_id=="wq/480"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"480p"}}</option>
							<option value="wq/720" {{if $smarty.session.save.$page_name.se_show_id=="wq/720"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"720p"}}</option>
							<option value="wq/1080" {{if $smarty.session.save.$page_name.se_show_id=="wq/1080"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"1080p"}}</option>
							<option value="wq/2160" {{if $smarty.session.save.$page_name.se_show_id=="wq/2160"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_w|replace:"%1%":"2160p"}}</option>
							<option value="woq/360" {{if $smarty.session.save.$page_name.se_show_id=="woq/360"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"360p"}}</option>
							<option value="woq/480" {{if $smarty.session.save.$page_name.se_show_id=="woq/480"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"480p"}}</option>
							<option value="woq/720" {{if $smarty.session.save.$page_name.se_show_id=="woq/720"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"720p"}}</option>
							<option value="woq/1080" {{if $smarty.session.save.$page_name.se_show_id=="woq/1080"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"1080p"}}</option>
							<option value="woq/2160" {{if $smarty.session.save.$page_name.se_show_id=="woq/2160"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_quality_wo|replace:"%1%":"2160p"}}</option>
							{{foreach item=item_lang from=$list_languages|smarty:nodefaults}}
								<option value="wl/{{$item_lang.code}}" {{if $smarty.session.save.$page_name.se_show_id=="wl/`$item_lang.code`"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_language_w|replace:"%1%":$item_lang.title}}</option>
							{{/foreach}}
							{{foreach item=item_lang from=$list_languages|smarty:nodefaults}}
								<option value="wol/{{$item_lang.code}}" {{if $smarty.session.save.$page_name.se_show_id=="wol/`$item_lang.code`"}}selected="selected"{{/if}}>{{$lang.videos.video_filter_other_language_wo|replace:"%1%":$item_lang.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_feed_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_filter_feed}}:</td>
					<td class="dgf_control">
						<select name="se_feed_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_feeds_import|smarty:nodefaults}}
								<option value="{{$item.feed_id}}" {{if $smarty.session.save.$page_name.se_feed_id==$item.feed_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_storage_group_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_server_group}}:</td>
					<td class="dgf_control">
						<select name="se_storage_group_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_server_groups|smarty:nodefaults}}
								<option value="{{$item.group_id}}" {{if $smarty.session.save.$page_name.se_storage_group_id==$item.group_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_review_flag!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_review_flag}}:</td>
					<td class="dgf_control">
						<select name="se_review_flag">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_review_flag=='1'}}selected="selected"{{/if}}>{{$lang.common.dg_filter_review_flag_yes}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_review_flag=='2'}}selected="selected"{{/if}}>{{$lang.common.dg_filter_review_flag_no}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_admin_user_id!=''}}dgf_selected{{/if}}">{{$lang.videos.video_field_admin}}:</td>
					<td class="dgf_control">
						<select name="se_admin_user_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_admin_users|smarty:nodefaults}}
								<option value="{{$item.user_id}}" {{if $smarty.session.save.$page_name.se_admin_user_id==$item.user_id}}selected="selected"{{/if}}>{{$item.login}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_posted!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_posted}}:</td>
					<td class="dgf_control">
						<select name="se_posted">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="yes" {{if $smarty.session.save.$page_name.se_posted=="yes"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_posted_yes}}</option>
							<option value="no" {{if $smarty.session.save.$page_name.se_posted=="no"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_posted_no}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_post_date_from>0}}dgf_selected{{/if}}">{{$lang.common.dg_filter_post_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_post_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_post_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_post_date_from_' start_year='+3' end_year='2000' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_post_date_to>0}}dgf_selected{{/if}}">{{$lang.common.dg_filter_post_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_post_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_post_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_post_date_to_' start_year='+3' end_year='2000' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_control">
						<div class="dg_lv_pair">
							<input type="checkbox" name="se_is_locked" value="1" {{if $smarty.session.save.$page_name.se_is_locked=='1'}}checked="checked"{{/if}}/>
							<label>{{$lang.videos.video_field_lock_website}}</label>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_control">
						<div class="dg_lv_pair">
							<input type="checkbox" name="se_has_errors" value="1" {{if $smarty.session.save.$page_name.se_has_errors=='1'}}checked="checked"{{/if}}/>
							<label>{{$lang.videos.video_field_has_errors}}</label>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_columns hidden">
			{{assign var="table_columns_display_mode" value="selector"}}
			{{include file="table_columns_inc.tpl"}}
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="err_list hidden">
			<div class="err_header"></div>
			<div class="err_content"></div>
		</div>
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					{{assign var="table_columns_display_mode" value="sizes"}}
					{{include file="table_columns_inc.tpl"}}
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0 || $item.status_id==4 || $item.status_id==5}}disabled{{/if}}">
						<td class="dg_selector">
							<input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/>
							<input type="hidden" name="row_all[]" value="{{$item.$table_key_name}}"/>
						</td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
						{{if $item.status_id==4}}
							<td></td>
						{{else}}
							<td>
								{{if $item.status_id==5 || $item.status_id==4 || $item.status_id==3 || $item.status_id==2}}
									{{assign var=functionality_disabled value=1}}
								{{else}}
									{{assign var=functionality_disabled value=0}}
								{{/if}}
								{{if $functionality_disabled==0}}
									<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								{{elseif $item.status_id==5}}
									<a href="{{$page_name}}?action=change_deleted&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								{{/if}}

								{{if $can_invoke_additional==1}}
									<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
										<span class="js_params">
											<span class="js_param">id={{$item.$table_key_name}}</span>
											<span class="js_param">name={{if $item.title!=''}}{{$item.title}}{{else}}{{$item.$table_key_name}}{{/if}}</span>
											{{if $item.status_id!=2}}
												<span class="js_param">restart_hide=true</span>
											{{/if}}
											{{if $item.status_id!=0 && $item.status_id!=1 && $item.status_id!=2}}
												<span class="js_param">soft_delete_hide=true</span>
											{{/if}}
											{{if $item.website_link==''}}
												<span class="js_param">website_link_disable=true</span>
											{{else}}
												<span class="js_param">website_link={{$item.website_link}}</span>
											{{/if}}
											{{if $functionality_disabled==0}}
												{{if $item.status_id==1}}
													<span class="js_param">activate_hide=true</span>
												{{else}}
													<span class="js_param">deactivate_hide=true</span>
												{{/if}}
												{{if $item.is_review_needed!=1}}
													<span class="js_param">mark_reviewed_hide=true</span>
												{{/if}}
												{{if $item.load_type_id==5}}
													<span class="js_param">preview_hide=true</span>
												{{/if}}
											{{else}}
												<span class="js_param">activate_hide=true</span>
												<span class="js_param">deactivate_hide=true</span>
												<span class="js_param">mark_reviewed_hide=true</span>
												<span class="js_param">preview_hide=true</span>
												<span class="js_param">validate_hide=true</span>
											{{/if}}
										</span>
									</a>
								{{/if}}
							</td>
						{{/if}}
					</tr>
				{{/foreach}}
			</table>
			{{if $can_invoke_additional==1}}
				<ul class="dg_additional_menu_template">
					{{if $can_delete==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=soft_delete&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_soft_delete}}</span>
							<span class="js_param">hide=${soft_delete_hide}</span>
						</li>
					{{/if}}
					{{if $can_restart==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=restart&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.videos.video_action_restart}}</span>
							<span class="js_param">hide=${restart_hide}</span>
						</li>
					{{/if}}
					{{if $can_edit_status==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
							<span class="js_param">hide=${activate_hide}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
							<span class="js_param">hide=${deactivate_hide}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						</li>
					{{/if}}
					{{if $can_edit_all==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.videos.video_action_mark_reviewed}}</span>
							<span class="js_param">hide=${mark_reviewed_hide}</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=${website_link}</span>
						<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
						<span class="js_param">disable=${website_link_disable}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					{{if in_array('users|manage_comments',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=1&amp;object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=?action=video_log&amp;item_id=${id}</span>
						<span class="js_param">title={{$lang.videos.video_action_view_log}}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					{{if in_array('system|background_tasks',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=log_background_tasks.php?no_filter=true&amp;se_video_id=${id}</span>
							<span class="js_param">title={{$lang.videos.video_action_view_tasks}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					{{if in_array('system|administration',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=1&amp;se_object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					{{if in_array('stats|view_content_stats',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=stats_videos.php?no_filter=true&amp;se_group_by=date&amp;se_id=${id}</span>
							<span class="js_param">title={{$lang.videos.video_action_view_stats}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=preview_video.php?video_id=${id}</span>
						<span class="js_param">title={{$lang.videos.video_action_preview}}</span>
						<span class="js_param">hide=${preview_hide}</span>
						<span class="js_param">popup=true</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?action=video_validate&amp;item_id=${id}</span>
						<span class="js_param">title={{$lang.videos.video_action_validate}}</span>
						<span class="js_param">hide=${validate_hide}</span>
						<span class="js_param">plain_link=true</span>
					</li>
				</ul>
			{{/if}}
		</div>
		<div class="dgb">
			<table>
				<tr>
					{{if $can_invoke_batch==1}}
						<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
						<td class="dgb_control">
							<select name="batch_action">
								<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
								{{if $can_delete==1}}
									<option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
									<option value="soft_delete">{{$lang.common.dg_batch_actions_soft_delete}}</option>
								{{/if}}
								{{if $can_edit_status==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
								{{/if}}
								{{if $can_edit_all==1}}
									<option value="mark_reviewed">{{$lang.videos.video_batch_mark_reviewed}}</option>
								{{/if}}
								{{if $can_edit_all==1}}
									<option value="mass_edit">{{$lang.videos.video_batch_mass_edit}}</option>
									<option value="mass_edit_all">{{$lang.videos.video_batch_mass_edit_all|replace:"%1%":$mass_edit_all_count}}</option>
									{{if $total_num>0}}
										<option value="mass_edit_filtered">{{$lang.videos.video_batch_mass_edit_filtered|replace:"%1%":$total_num}}</option>
									{{/if}}
								{{/if}}
								{{if $can_restart==1}}
									<option value="restart">{{$lang.videos.video_batch_restart}}</option>
									<option value="inc_priority">{{$lang.videos.video_batch_inc_priority}}</option>
								{{/if}}
								{{if $can_edit_status==1 && $can_delete==1}}
									<option value="activate_and_delete">{{$lang.videos.video_batch_activate_and_delete}}</option>
									<option value="delete_and_activate">{{$lang.videos.video_batch_delete_and_activate}}</option>
								{{/if}}
							</select>
						</td>
						<td class="dgb_control">
							<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
						</td>
					{{/if}}
					<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
				</tr>
			</table>
			<ul class="dgb_actions_configuration">
				{{assign var="displayed_count" value=$data|@count}}
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=activate_and_delete</span>
					<span class="js_param">confirm={{$lang.videos.video_batch_activate_and_delete_confirm|replace:"%1%":'${count}'|replace:"%2%":$displayed_count}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_and_activate</span>
					<span class="js_param">confirm={{$lang.videos.video_batch_delete_and_activate_confirm|replace:"%1%":'${count}'|replace:"%2%":$displayed_count}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=mass_edit_all</span>
					<span class="js_param">requires_selection=false</span>
					<span class="js_param">confirm={{$lang.videos.video_batch_mass_edit_all_confirm}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=mass_edit_filtered</span>
					<span class="js_param">requires_selection=false</span>
					{{if $mass_edit_all_count==$total_num}}
						<span class="js_param">confirm={{$lang.videos.video_batch_mass_edit_all_confirm}}</span>
					{{/if}}
				</li>
				<li class="js_params">
					<span class="js_param">value=restart</span>
					<span class="js_param">requires_selection=false</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}