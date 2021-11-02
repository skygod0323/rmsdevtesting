{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if is_array($list_messages)}}
	<div class="message">
	{{foreach item="item" from=$list_messages|smarty:nodefaults}}
		<p>{{$item}}</p>
	{{/foreach}}
	</div>
{{/if}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('posts|edit_all',$smarty.session.permissions) || (in_array('posts|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}
{{if in_array('posts|edit_title',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_title value=1}}
{{else}}
	{{assign var=can_edit_title value=0}}
{{/if}}
{{if in_array('posts|edit_dir',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_dir value=1}}
{{else}}
	{{assign var=can_edit_dir value=0}}
{{/if}}
{{if in_array('posts|edit_description',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_desc value=1}}
{{else}}
	{{assign var=can_edit_desc value=0}}
{{/if}}
{{if in_array('posts|edit_content',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_content value=1}}
{{else}}
	{{assign var=can_edit_content value=0}}
{{/if}}
{{if in_array('posts|edit_tags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_tags value=1}}
{{else}}
	{{assign var=can_edit_tags value=0}}
{{/if}}
{{if in_array('posts|edit_post_date',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_date value=1}}
{{else}}
	{{assign var=can_edit_date value=0}}
{{/if}}
{{if in_array('posts|edit_user',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_user value=1}}
{{else}}
	{{assign var=can_edit_user value=0}}
{{/if}}
{{if in_array('posts|edit_status',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_status value=1}}
{{else}}
	{{assign var=can_edit_status value=0}}
{{/if}}
{{if in_array('posts|edit_type',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_type value=1}}
{{else}}
	{{assign var=can_edit_type value=0}}
{{/if}}
{{if in_array('posts|edit_categories',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_categories value=1}}
{{else}}
	{{assign var=can_edit_categories value=0}}
{{/if}}
{{if in_array('posts|edit_models',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_models value=1}}
{{else}}
	{{assign var=can_edit_models value=0}}
{{/if}}
{{if in_array('posts|edit_flags',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_flags value=1}}
{{else}}
	{{assign var=can_edit_flags value=0}}
{{/if}}
{{if in_array('posts|edit_custom',$smarty.session.permissions) || $can_edit_all==1}}
	{{assign var=can_edit_custom value=1}}
{{else}}
	{{assign var=can_edit_custom value=0}}
{{/if}}
{{if in_array('posts|delete',$smarty.session.permissions)}}
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
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
		{{section name="fields" start="1" loop=11}}
			{{assign var="custom_data_name" value="custom`$smarty.section.fields.index`"}}
			<input type="hidden" name="{{$custom_data_name}}" value="{{$smarty.post.$custom_data_name}}"/>
			{{assign var="custom_data_name" value="custom_file`$smarty.section.fields.index`"}}
			<input type="hidden" name="{{$custom_data_name}}" value="{{$smarty.post.$custom_data_name}}"/>
		{{/section}}
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col/>
			<col width="5%"/>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="4">
				<div>
					<a href="{{$page_name}}">{{if $locked_post_type_id>0}}{{$locked_post_type.title}}{{else}}{{$lang.posts.submenu_option_posts_list}}{{/if}}</a>
					/
					{{if $smarty.get.action=='add_new'}}
						{{$lang.posts.post_add}}
					{{else}}
						{{if $smarty.post.title!=''}}
							{{$lang.posts.post_edit|replace:"%1%":$smarty.post.title}}
						{{else}}
							{{$lang.posts.post_edit|replace:"%1%":$smarty.post.post_id}}
						{{/if}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="4">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1148-theme-customization-how-to-display-text-posts-in-kvs">How to display text posts in KVS</a></span>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.is_review_needed==1 && $can_edit_all==1}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.posts.post_divider_review}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.posts.post_divider_review_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.posts.post_field_review_action}}:</td>
				<td class="de_control" colspan="3">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_vis_sw_select">
									<select id="is_reviewed" name="is_reviewed">
										<option value="0">{{$lang.posts.post_field_review_action_none}}</option>
										<option value="1">{{$lang.posts.post_field_review_action_approve}}</option>
										<option value="2" {{if $can_delete==0}}disabled="disabled"{{/if}}>{{$lang.posts.post_field_review_action_delete}}</option>
									</select>
								</div>
							</td>
						</tr>
						{{if $smarty.post.status_id==0}}
							<tr class="is_reviewed_1">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_activate" value="1"/><label>{{$lang.posts.post_field_review_action_activate}}</label></div>
								</td>
							</tr>
						{{/if}}
						<tr class="is_reviewed_2">
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_disable_user" value="1" class="is_reviewed_delete" {{if !in_array('users|edit_all',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.posts.post_field_review_action_disable_user|replace:"%1%":$smarty.post.user}}</label></div>
							</td>
						</tr>
						{{if $smarty.post.user_domain!='' && $smarty.post.user_domain_blocked!=1}}
							<tr class="is_reviewed_2">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_domain" value="1" class="is_reviewed_delete" {{if !in_array('system|memberzone_settings',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.posts.post_field_review_action_block_domain|replace:"%1%":$smarty.post.user_domain}}</label></div>
								</td>
							</tr>
						{{/if}}
						{{if $smarty.post.ip_mask!='0.0.0.*' && $smarty.post.ip_mask_blocked!=1}}
							<tr class="is_reviewed_2">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_mask" value="1" class="is_reviewed_delete" {{if !in_array('system|memberzone_settings',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.posts.post_field_review_action_block_mask|replace:"%1%":$smarty.post.ip_mask}}</label></div>
								</td>
							</tr>
						{{/if}}
						{{if $smarty.post.ip!='0.0.0.0' && $smarty.post.ip_blocked!=1 && $smarty.post.ip_mask_blocked!=1}}
							<tr class="is_reviewed_2">
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_reviewed_block_ip" value="1" class="is_reviewed_delete" {{if !in_array('system|memberzone_settings',$smarty.session.permissions)}}disabled="disabled"{{/if}}/><label>{{$lang.posts.post_field_review_action_block_ip|replace:"%1%":$smarty.post.ip}}</label></div>
								</td>
							</tr>
						{{/if}}
						{{if $smarty.post.other_posts_need_review>0}}
							<tr class="is_reviewed_2">
								<td>
									{{assign var="max_delete_on_review" value=$config.max_delete_on_review|intval}}
									{{if $max_delete_on_review==0}}
										{{assign var="max_delete_on_review" value=30}}
									{{/if}}
									<div class="de_lv_pair"><input type="checkbox" name="is_delete_all_posts_from_user" value="1" class="is_reviewed_delete" {{if $can_delete!=1 || $smarty.post.other_posts_need_review>$max_delete_on_review}}disabled="disabled"{{/if}}/><label>{{$lang.posts.post_field_review_action_delete_other|replace:"%1%":$smarty.post.other_posts_need_review}}</label></div>
								</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
			{{if is_array($config.advanced_filtering) && in_array('upload_zone',$config.advanced_filtering)}}
				<tr>
					<td class="de_label">{{$lang.posts.post_field_af_upload_zone}}:</td>
					<td class="de_control">
						<select name="af_upload_zone">
							<option value="0" {{if $smarty.post.af_upload_zone==0}}selected="selected"{{/if}}>{{$lang.posts.post_field_af_upload_zone_site}}</option>
							<option value="1" {{if $smarty.post.af_upload_zone==1}}selected="selected"{{/if}}>{{$lang.posts.post_field_af_upload_zone_memberarea}}</option>
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.posts.post_field_af_upload_zone_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_separator" colspan="4"><div>{{$lang.posts.post_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="status_id_off {{if $smarty.post.status_id=='1'}}hidden{{/if}}">{{$lang.posts.post_field_title}}:</div>
				<div class="de_required status_id_on {{if $smarty.post.status_id=='0'}}hidden{{/if}}">{{$lang.posts.post_field_title}} (*):</div>
			</td>
			<td class="de_control" colspan="3">
				<div class="de_str_len">
					<input type="text" name="title" maxlength="255" class="dyn_full_size {{if $can_edit_title==1}}preserve_editing{{/if}}" value="{{$smarty.post.title}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.posts.post_field_title_hint}}, <span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action=='change'}}
			<tr>
				<td class="de_label">{{$lang.posts.post_field_directory}}:</td>
				<td class="de_control" colspan="3">
					<input type="text" name="dir" maxlength="255" class="dyn_full_size {{if $options.POST_REGENERATE_DIRECTORIES==1}}readonly_field{{elseif $can_edit_dir==1}}preserve_editing{{/if}}" value="{{$smarty.post.dir}}" {{if $options.POST_REGENERATE_DIRECTORIES==1}}readonly="readonly"{{/if}}/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						{{if $options.POST_REGENERATE_DIRECTORIES==1}}
							<br/><span class="de_hint">{{$lang.posts.post_field_directory_hint2|replace:"%1%":$lang.posts.post_field_title}}</span>
						{{else}}
							<br/><span class="de_hint">{{$lang.posts.post_field_directory_hint|replace:"%1%":$lang.posts.post_field_title}}</span>
						{{/if}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.website_link!=''}}
				<tr>
					<td class="de_label">{{$lang.posts.post_field_website_link}}:</td>
					<td class="de_control" colspan="3">
						<a href="{{$smarty.post.website_link}}" rel="external">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.posts.post_field_description}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_str_len">
					<textarea name="description" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_posts=='1'}}tinymce{{/if}} {{if $can_edit_desc==1}}preserve_editing{{/if}}" cols="40" rows="3">{{$smarty.post.description}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.posts.post_field_content}} (*):</td>
			<td class="de_control" colspan="3">
				<div class="de_str_len">
					<textarea name="content" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_posts=='1'}}tinymce{{/if}} {{if $can_edit_content==1}}preserve_editing{{/if}}" cols="40" rows="6">{{$smarty.post.content}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.posts.post_field_type}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="post_type_id" name="post_type_id" {{if $can_edit_type==1}}class="preserve_editing"{{/if}}>
						{{foreach from=$list_types|smarty:nodefaults item="item"}}
							<option value="{{$item.post_type_id}}" {{if $smarty.post.post_type_id==$item.post_type_id}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</div>
			</td>
			<td class="de_label de_required">{{$lang.posts.post_field_user}} (*):</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
					</div>
					<input type="text" name="user" maxlength="255" class="fixed_150 {{if $can_edit_user==1}}preserve_editing{{/if}}" value="{{$smarty.post.user}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.posts.post_field_user_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.posts.post_field_post_date}} (*):</td>
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
										<input id="post_date_now" type="button" value="{{$lang.posts.post_field_post_date_now}}" class="post_date_option_fixed"/>
									{{/if}}
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.posts.post_field_post_date_hint}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input id="post_date_option_relative" type="radio" name="post_date_option" value="1" {{if $smarty.post.post_date_option=='1'}}checked="checked"{{/if}}/></div>
									<input type="text" name="relative_post_date" size="4" maxlength="5" class="fixed_100 post_date_option_relative {{if $can_edit_date==1}}preserve_editing{{/if}}" value="{{$smarty.post.relative_post_date}}"/>
									{{$lang.posts.post_field_post_date_relative}}
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.posts.post_field_post_date_hint2}}</span>
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
							<input id="post_date_now" type="button" value="{{$lang.posts.post_field_post_date_now}}"/>
						{{/if}}
					</div>
				{{/if}}
			</td>
			<td class="de_label">{{$lang.posts.post_field_status}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_checkbox">
					<div class="de_lv_pair"><input id="status_id" type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked="checked"{{/if}} {{if $can_edit_status==1}}class="preserve_editing"{{/if}}/><label>{{$lang.posts.post_field_status_active}}</label></div>
				</div>
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.posts.post_field_lock_website}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked="checked"{{/if}} {{if $can_edit_all==1}}class="preserve_editing"{{/if}}/><label>{{$lang.posts.post_field_lock_website_locked}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.posts.post_field_lock_website_hint}}</span>
					{{/if}}
				</td>
				<td class="de_label">{{$lang.posts.post_field_ip}}:</td>
				<td class="de_control">
					{{if $config.safe_mode!='true'}}
						{{$smarty.post.ip}}
					{{else}}
						0.0.0.0
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.connected_video_title!=''}}
			<tr>
				<td class="de_label">{{$lang.posts.post_field_connected_video}}:</td>
				<td class="de_control" colspan="3">
					<a href="videos.php?action=change&amp;item_id={{$smarty.post.connected_video_id}}">{{$smarty.post.connected_video_title}}</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="4"><div>{{$lang.posts.post_divider_categorization}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.posts.post_field_tags}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						{{if $can_edit_tags!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.posts.post_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
					{{if $can_edit_tags==1}}
						<div class="controls">
							<input type="text" name="new_tag" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.posts.post_field_tags_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div>{{$lang.posts.post_field_categories}}:</div>
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
						<span class="js_param">empty_message={{$lang.posts.post_field_categories_empty}}</span>
						{{if $can_edit_categories!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_categories==1}}
						<div class="controls">
							<input type="text" name="new_category" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.posts.post_field_categories_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div>{{$lang.posts.post_field_models}}:</div>
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
						<span class="js_param">empty_message={{$lang.posts.post_field_models_empty}}</span>
						{{if $can_edit_models!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
						<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_models==1}}
						<div class="controls">
							<input type="text" name="new_model" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.posts.post_field_models_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">
					<div>{{$lang.posts.post_field_flags}}:</div>
				</td>
				<td class="de_control" colspan="3">
					<div class="de_deletable_list">
						<div class="js_params">
							<span class="js_param">submit_name=delete_flags[]</span>
							<span class="js_param">empty_message={{$lang.posts.post_field_flags_empty}}</span>
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
								{{$lang.posts.post_field_flags_empty}}
							{{/if}}
						</div>
					</div>
				</td>
			</tr>
		{{/if}}
		{{if count($list_custom_fields) > 0}}
			<tr class="{{foreach item="item" from=$post_types_with_custom_fields|smarty:nodefaults}}post_type_id_{{$item}} {{/foreach}}">
				<td class="de_separator" colspan="4"><div>{{$lang.posts.post_divider_customization}}</div></td>
			</tr>
			{{foreach name="data" item="item" from=$list_custom_fields|smarty:nodefaults}}
				<tr class="{{foreach name="data_enabled" key="key_enabled" item="item_enabled" from=$item.enabled|smarty:nodefaults}}post_type_id_{{$key_enabled}} {{/foreach}}">
					<td class="de_label">
						{{foreach name="data_titles" key="key_titles" item="item_titles" from=$item.titles|smarty:nodefaults}}
							<div class="post_type_id_{{$key_titles}}">{{$item_titles}}:</div>
						{{/foreach}}
					</td>
					<td class="de_control" colspan="3">
						{{if $item.is_text==1}}
							<div class="de_str_len">
								{{assign var="field_name" value=$item.field_name}}
								<textarea name="{{$field_name}}" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_posts=='1'}}tinymce{{/if}} {{if $can_edit_custom==1}}preserve_editing{{/if}}" cols="40" rows="4">{{$smarty.post.$field_name}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						{{else}}
							<div class="de_fu">
								<div class="js_params">
									{{foreach name="data_titles" key="key_titles" item="item_titles" from=$item.titles|smarty:nodefaults}}
										<span class="js_param post_type_id_{{$key_titles}}">title={{$item_titles}}:</span>
									{{/foreach}}
									{{assign var="field_name" value=$item.field_name}}
									{{if $smarty.get.action=='change' && $smarty.post.$field_name!=''}}
										{{if in_array(end(explode(".",$smarty.post.$field_name)),explode(",",$config.image_allowed_ext))}}
											<span class="js_param">preview_url={{$config.content_url_posts}}/{{$smarty.post.dir_path}}/{{$smarty.post.post_id}}/{{$smarty.post.$field_name}}</span>
										{{else}}
											<span class="js_param">download_url={{$config.content_url_posts}}/{{$smarty.post.dir_path}}/{{$smarty.post.post_id}}/{{$smarty.post.$field_name}}</span>
										{{/if}}
									{{/if}}
								</div>
								<input type="text" name="{{$field_name}}" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change'}}value="{{$smarty.post.$field_name}}"{{/if}} readonly="readonly"/>
								<input type="hidden" name="{{$field_name}}_hash"/>
								{{if $can_edit_custom==1}}
									<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
									<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.$field_name==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
								{{/if}}
								{{if $smarty.get.action=='change' && $smarty.post.$field_name!=''}}
									{{if in_array(end(explode(".",$smarty.post.$field_name)),explode(",",$config.image_allowed_ext))}}
										<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
									{{else}}
										<input type="button" class="de_fu_download" value="{{$lang.common.attachment_btn_download}}"/>
									{{/if}}
								{{/if}}
							</div>
						{{/if}}
					</td>
				</tr>
			{{/foreach}}
		{{/if}}
		{{if $can_edit_all || $can_edit_title || $can_edit_dir || $can_edit_desc || $can_edit_content || $can_edit_tags || $can_edit_date || $can_edit_user ||
			 $can_edit_status || $can_edit_type || $can_edit_categories || $can_edit_models || $can_edit_flags || $can_edit_custom}}
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
						<input type="submit" name="delete_and_edit" value="{{$lang.posts.post_btn_delete_and_edit_next}}" class="de_confirm" alt="{{$lang.posts.post_btn_delete_and_edit_next_confirm}}"/>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{/if}}
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildPostDateResetLogic=call('{{$smarty.now|date_format:"%Y"}}', '{{$smarty.now|date_format:"%m"}}', '{{$smarty.now|date_format:"%e"|trim}}', '{{$smarty.now|date_format:"%H"}}', '{{$smarty.now|date_format:"%M"}}')</span>
</div>

{{else}}

{{if in_array('posts|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('posts|edit_status',$smarty.session.permissions)}}
	{{assign var=can_edit_status value=1}}
{{else}}
	{{assign var=can_edit_status value=0}}
{{/if}}
{{if in_array('posts|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit_all value=1}}
	{{assign var=can_edit_status value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}
{{if in_array('system|administration',$smarty.session.permissions)}}
	{{assign var=can_see_audit_log value=1}}
{{else}}
	{{assign var=can_see_audit_log value=0}}
{{/if}}
{{if in_array('users|manage_comments',$smarty.session.permissions)}}
	{{assign var=can_add_comments value=1}}
{{else}}
	{{assign var=can_add_comments value=0}}
{{/if}}
{{assign var=can_invoke_additional value=1}}
{{if $can_delete==1 || $can_edit_status==1 || $can_edit_all==1}}
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
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_post_type_id!=''}}dgf_selected{{/if}}">{{$lang.posts.post_field_type}}:</td>
					<td class="dgf_control">
						<select name="se_post_type_id" {{if $locked_post_type_id>0}}disabled="disabled"{{/if}}>
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$list_types|smarty:nodefaults item="item"}}
								<option value="{{$item.post_type_id}}" {{if $smarty.session.save.$page_name.se_post_type_id==$item.post_type_id || $locked_post_type_id==$item.post_type_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.posts.post_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.posts.post_field_status_disabled}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.posts.post_field_status_active}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_admin_user_id!=''}}dgf_selected{{/if}}"><label for="se_admin_user_id">{{$lang.posts.post_field_admin}}:</label></td>
					<td class="dgf_control">
						<select id="se_admin_user_id" name="se_admin_user_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$list_admin_users|smarty:nodefaults item="item"}}
								<option value="{{$item.user_id}}" {{if $smarty.session.save.$page_name.se_admin_user_id==$item.user_id}}selected="selected"{{/if}}>{{$item.login}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.posts.post_field_user}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_category!=''}}dgf_selected{{/if}}">{{$lang.posts.post_field_category}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_tag!=''}}dgf_selected{{/if}}">{{$lang.posts.post_field_tag}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_model!=''}}dgf_selected{{/if}}">{{$lang.posts.post_field_model}}:</td>
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
							{{foreach item=item_flag from=$list_flags_posts|smarty:nodefaults}}
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
							<option value="empty/title" {{if $smarty.session.save.$page_name.se_field=="empty/title"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_title}}</option>
							<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_description}}</option>
							<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_rating}}</option>
							<option value="empty/post_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/post_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_visits}}</option>
							<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_tags}}</option>
							<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_categories}}</option>
							<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.posts.post_field_models}}</option>
							{{if $locked_post_type_id>0}}
								{{foreach from=$list_custom_fields|smarty:nodefaults item="custom_field"}}
									{{if $custom_field.enabled[$locked_post_type_id]==1}}
										<option value="empty/{{$custom_field.field_name}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field.field_name`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$custom_field.titles[$locked_post_type_id]}}</option>
									{{/if}}
								{{/foreach}}
							{{/if}}
							<option value="filled/title" {{if $smarty.session.save.$page_name.se_field=="filled/title"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_title}}</option>
							<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_description}}</option>
							<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_rating}}</option>
							<option value="filled/post_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/post_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_visits}}</option>
							<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_tags}}</option>
							<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_categories}}</option>
							<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.posts.post_field_models}}</option>
							{{if $locked_post_type_id>0}}
								{{foreach from=$list_custom_fields|smarty:nodefaults item="custom_field"}}
									{{if $custom_field.enabled[$locked_post_type_id]==1}}
										<option value="filled/{{$custom_field.field_name}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field.field_name`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$custom_field.titles[$locked_post_type_id]}}</option>
									{{/if}}
								{{/foreach}}
							{{/if}}
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
				{{foreach name="data" item="item" from=$data|smarty:nodefaults}}
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
									<span class="js_param">name={{if $item.title!=''}}{{$item.title}}{{else}}{{$item.$table_key_name}}{{/if}}</span>
									{{if $item.website_link==''}}
										<span class="js_param">website_link_disable=true</span>
									{{else}}
										<span class="js_param">website_link={{$item.website_link}}</span>
									{{/if}}
									{{if $item.is_review_needed!=1}}
										<span class="js_param">mark_reviewed_hide=true</span>
									{{/if}}
									{{if $item.status_id==1}}
										<span class="js_param">activate_hide=true</span>
									{{else}}
										<span class="js_param">deactivate_hide=true</span>
									{{/if}}
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
							<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
							<span class="js_param">hide=${mark_reviewed_hide}</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=${website_link}</span>
						<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
						<span class="js_param">disable=${website_link_disable}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					{{if $can_add_comments==1}}
						<li class="js_params">
							<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=12&amp;object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					{{if $can_see_audit_log==1}}
						<li class="js_params">
							<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=12&amp;se_object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
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
								{{if $can_edit_status==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
								{{/if}}
								{{if $can_edit_all==1}}
									<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed}}</option>
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
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}