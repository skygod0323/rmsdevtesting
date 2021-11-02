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

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

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
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.videos.submenu_option_feeds_export}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.videos.feed_add}}{{else}}{{$lang.videos.feed_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.feed_field_title}} (*):</td>
			<td class="de_control">
				<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_status}}:</td>
			<td class="de_control">
				<select name="status_id">
					<option value="0" {{if $smarty.post.status_id==0}}selected="selected"{{/if}}>{{$lang.videos.feed_field_status_disabled}}</option>
					<option value="1" {{if $smarty.post.status_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_status_active}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.feed_field_external_id}} (*):</td>
			<td class="de_control">
				<input type="text" name="external_id" maxlength="100" class="dyn_full_size" value="{{$smarty.post.external_id}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_external_id_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.feed_field_max_limit}} (*):</td>
			<td class="de_control">
				<input type="text" name="max_limit" maxlength="10" class="fixed_100" value="{{$smarty.post.max_limit}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_max_limit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.feed_field_cache}} (*):</td>
			<td class="de_control">
				<input type="text" name="cache" maxlength="10" class="fixed_100" value="{{$smarty.post.cache}}"/>
				{{$lang.videos.feed_field_cache_seconds}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_cache_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_password}}:</td>
			<td class="de_control">
				<input type="text" name="password" maxlength="100" class="dyn_full_size" value="{{$smarty.post.password}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_password_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_affiliate_param_name}}:</td>
			<td class="de_control">
				<input type="text" name="affiliate_param_name" maxlength="100" class="dyn_full_size" value="{{$smarty.post.affiliate_param_name}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_affiliate_param_name_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_feed_url}}:</td>
				<td class="de_control"><a href="{{$config.project_url}}/admin/feeds/{{$smarty.post.external_id}}/" rel="external">{{$config.project_url}}/admin/feeds/{{$smarty.post.external_id}}/</a></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_last_exec_date}}:</td>
				<td class="de_control">
					{{if $smarty.post.last_exec_date=='0000-00-00 00:00:00'}}
						{{$lang.common.undefined}}
					{{else}}
						{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}} ({{$smarty.post.last_exec_duration|number_format:4:".":""}}{{$lang.common.second_truncated}})
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_filters}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_status}}:</td>
			<td class="de_control">
				<select name="video_status_id">
					<option value="0" {{if $smarty.post.video_status_id==0}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_status_active}}</option>
					<option value="1" {{if $smarty.post.video_status_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_status_disabled}}</option>
					<option value="2" {{if $smarty.post.video_status_id==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_status_active_disabled}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_type}}:</td>
			<td class="de_control">
				<select name="video_type_id">
					<option value="0" {{if $smarty.post.video_type_id==0}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_type_all}}</option>
					<option value="1" {{if $smarty.post.video_type_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_type_standard}}</option>
					<option value="2" {{if $smarty.post.video_type_id==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_type_premium}}</option>
					<option value="3" {{if $smarty.post.video_type_id==3}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_type_public}}</option>
					<option value="4" {{if $smarty.post.video_type_id==4}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_type_private}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_categories}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=video_category_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.feed_field_video_categories_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.video_categories|smarty:nodefaults}}
						<input type="hidden" name="video_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_category" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.feed_field_video_categories_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_models}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=video_model_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.feed_field_video_models_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.video_models|smarty:nodefaults}}
						<input type="hidden" name="video_model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_model" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.feed_field_video_models_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_tags}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags2.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=video_tag_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.feed_field_video_tags_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.video_tags|smarty:nodefaults}}
						<input type="hidden" name="video_tag_ids[]" value="{{$item.tag_id}}" alt="{{$item.tag}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_tag" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.feed_field_video_tags_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_content_sources}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_content_sources.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=video_content_source_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.feed_field_video_content_sources_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.video_content_sources|smarty:nodefaults}}
						<input type="hidden" name="video_content_source_ids[]" value="{{$item.content_source_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_cs" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.feed_field_video_content_sources_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_dvds}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_dvds.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=video_dvd_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.feed_field_video_dvds_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.video_dvds|smarty:nodefaults}}
						<input type="hidden" name="video_dvd_ids[]" value="{{$item.dvd_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_dvd" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.feed_field_video_dvds_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_admin_flag}}:</td>
			<td class="de_control">
				<select name="video_admin_flag_id">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
						<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.video_admin_flag_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_data}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_video_content_type}}:</td>
			<td class="de_control">
				<select name="video_content_type_id">
					<option value="1" {{if $smarty.post.video_content_type_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_content_type_pseudo}}</option>
					<option value="2" {{if $smarty.post.video_content_type_id==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_content_type_hotlink}}</option>
					<option value="3" {{if $smarty.post.video_content_type_id==3}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_content_type_embed}}</option>
					<option value="4" {{if $smarty.post.video_content_type_id==4}}selected="selected"{{/if}}>{{$lang.videos.feed_field_video_content_type_temp_link}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_video_content_type_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_options}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_search" value="1" {{if $smarty.post.enable_search==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_search}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_search_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_categories" value="1" {{if $smarty.post.enable_categories==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_categories}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_categories_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_tags" value="1" {{if $smarty.post.enable_tags==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_tags}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_tags_hint}}</span>
							{{/if}}
						</td>
					</tr>
					{{if $config.installation_type>=2}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="enable_models" value="1" {{if $smarty.post.enable_models==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_models}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_models_hint}}</span>
								{{/if}}
							</td>
						</tr>
					{{/if}}
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_content_sources" value="1" {{if $smarty.post.enable_content_sources==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_content_sources}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_content_sources_hint}}</span>
							{{/if}}
						</td>
					</tr>
					{{if $config.installation_type==4}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="enable_dvds" value="1" {{if $smarty.post.enable_dvds==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_dvds}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_dvds_hint}}</span>
								{{/if}}
							</td>
						</tr>
					{{/if}}
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_screenshot_sources" value="1" {{if $smarty.post.enable_screenshot_sources==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_screen_sources}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_screen_sources_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_custom_fields" value="1" {{if $smarty.post.enable_custom_fields==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_custom_fields}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_custom_fields_hint}}</span>
							{{/if}}
						</td>
					</tr>
					{{if count($list_languages)>0}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="enable_localization" value="1" {{if $smarty.post.enable_localization==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_localization}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_localization_hint}}</span>
								{{/if}}
							</td>
						</tr>
					{{/if}}
					{{if count($list_satellites)>0}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="enable_satellites" value="1" {{if $smarty.post.enable_satellites==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_satellites}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_satellites_hint}}</span>
								{{/if}}
							</td>
						</tr>
					{{/if}}
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="enable_future_dates" value="1" {{if $smarty.post.enable_future_dates==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_enable_future_dates}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_enable_future_dates_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="with_rotation_finished" value="1" {{if $smarty.post.with_rotation_finished==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_with_rotation_finished}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_options_with_rotation_finished_hint}}</span>
							{{/if}}
						</td>
					</tr>
					{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="with_upload_zone_site" value="1" {{if $smarty.post.with_upload_zone_site==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_options_with_upload_zone_site}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.videos.feed_field_options_with_upload_zone_site_hint}}</span>
								{{/if}}
							</td>
						</tr>
					{{/if}}
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				{{if $smarty.get.action=='add_new'}}
					{{if $smarty.session.save.options.default_save_button==1}}
						<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
					{{else}}
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
					{{/if}}
				{{else}}
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
				{{/if}}
			</td>
		</tr>
	</table>
</form>

{{else}}

{{assign var=can_delete value=1}}
{{assign var=can_invoke_additional value=1}}

{{if $can_delete==1}}
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
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text==''}}disabled="disabled"{{/if}}/>
					</td>
					<td class="dgf_advanced_link">
						<a href="javascript:stub()" class="dgf_columns">{{$lang.common.dg_filter_columns}}</a>
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
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if $can_invoke_additional==1}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.title}}</span>
										<span class="js_param">external_id={{$item.external_id}}</span>
										<span class="js_param">password={{$item.password}}</span>
									</span>
								</a>
							{{/if}}
						</td>
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
					{{/if}}
					<li class="js_params">
						<span class="js_param">href={{$config.project_url}}/admin/feeds/${external_id}/</span>
						<span class="js_param">title={{$lang.videos.feed_action_view_doc}}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					<li class="js_params">
						<span class="js_param">href={{$config.project_url}}/admin/feeds/${external_id}/?feed_format=kvs&amp;limit=10&amp;password=${password}</span>
						<span class="js_param">title={{$lang.videos.feed_action_test_feed}}</span>
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
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{include file="navigation.tpl"}}
{{/if}}