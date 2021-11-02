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

	{{if in_array('playlists|edit_all',$smarty.session.permissions) || (in_array('playlists|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
		{{assign var=can_edit_all value=1}}
	{{else}}
		{{assign var=can_edit_all value=0}}
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
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_playlists_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.playlist_add}}{{else}}{{$lang.users.playlist_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1008-theme-customization-how-to-build-embed-code-for-video-playlist">How to build embed code for video playlist</a></span>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.is_review_needed==1 && $can_edit_all==1}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.users.playlist_divider_review}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.users.playlist_divider_review_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_reviewed}}:</td>
				<td class="de_control"><div class="de_lv_pair"><input type="checkbox" name="is_reviewed" value="1"/><label>{{$lang.users.playlist_field_reviewed_yes}}</label></div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.playlist_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.playlist_field_title}} (*):</td>
			<td class="de_control">
				<div class="de_str_len">
					<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action=='change'}}
			<tr>
				<td class="de_label">{{$lang.users.playlist_field_directory}}:</td>
				<td class="de_control">
					<input type="text" name="dir" maxlength="255" class="dyn_full_size" value="{{$smarty.post.dir}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.playlist_field_directory_hint|replace:"%1%":$lang.users.playlist_field_title}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.website_link!=''}}
				<tr>
					<td class="de_label">{{$lang.users.playlist_field_website_link}}:</td>
					<td class="de_control" colspan="3">
						<a href="{{$smarty.post.website_link}}" rel="external">{{$smarty.post.website_link}}</a>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.playlist_field_description}}:</td>
			<td class="de_control">
				<div class="de_str_len">
					<textarea name="description" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.playlist_field_user}} (*):</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
					</div>
					<input type="text" name="user" maxlength="255" class="fixed_200" value="{{$smarty.post.user}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.playlist_field_user_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.playlist_field_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="is_private" name="is_private">
						<option value="0" {{if $smarty.post.is_private=='0'}}selected="selected"{{/if}}>{{$lang.users.playlist_field_type_public}}</option>
						<option value="1" {{if $smarty.post.is_private=='1'}}selected="selected"{{/if}}>{{$lang.users.playlist_field_type_private}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.playlist_field_type_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="is_private_0">
			<td class="de_label">{{$lang.users.playlist_field_status}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked="checked"{{/if}}/><label>{{$lang.users.playlist_field_status_active}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.playlist_field_status_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="is_private_0">
			<td class="de_label">{{$lang.users.playlist_field_lock_website}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_locked" value="1" {{if $smarty.post.is_locked==1}}checked="checked"{{/if}}/><label>{{$lang.users.playlist_field_lock_website_locked}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.playlist_field_lock_website_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.playlist_divider_categorization}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.playlist_field_tags}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.users.playlist_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_tag" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.users.playlist_field_tags_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.playlist_field_categories}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=category_ids[]</span>
						{{if in_array('categories|add',$smarty.session.permissions)}}
							<span class="js_param">allow_creation=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.users.playlist_field_categories_empty}}</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_category" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.users.playlist_field_categories_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">
					<div>{{$lang.users.playlist_field_flags}}:</div>
				</td>
				<td class="de_control" colspan="3">
					<div class="de_deletable_list">
						<div class="js_params">
							<span class="js_param">submit_name=delete_flags[]</span>
							<span class="js_param">empty_message={{$lang.users.playlist_field_flags_empty}}</span>
						</div>
						<div class="list">
							{{if count($smarty.post.flags)>0}}
								{{foreach name=data item=item from=$smarty.post.flags|smarty:nodefaults}}
									{{if $can_edit_all==1}}
										<a name="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}}){{if !$smarty.foreach.data.last}}, {{/if}}</a>
									{{else}}
										<span>{{$item.title}} ({{$item.votes}}){{if !$smarty.foreach.data.last}}, {{/if}}</span>
									{{/if}}
								{{/foreach}}
								<div class="clear_both"></div>
							{{else}}
								{{$lang.users.playlist_field_flags_empty}}
							{{/if}}
						</div>
					</div>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.playlist_divider_videos}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.playlist_field_add_videos}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_videos.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=add_video_ids[]</span>
						<span class="js_param">empty_message={{$lang.users.playlist_field_add_videos_empty}}</span>
					</div>
					<div class="list"></div>
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_video" class="fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if count($smarty.post.videos)>0}}
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">{{$lang.users.playlist_divider_videos_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<tr class="eg_header">
							<td class="eg_selector"><div><input type="checkbox"/> {{$lang.common.dg_actions_detach}}</div></td>
							<td>{{$lang.videos.video_field_id}}</td>
							<td>{{$lang.videos.video_field_title}}</td>
							<td>{{$lang.videos.video_field_duration}}</td>
							<td>{{$lang.videos.video_field_status}}</td>
							<td>{{$lang.videos.video_field_type}}</td>
							<td>{{$lang.videos.video_field_visits}}</td>
							<td>{{$lang.videos.video_field_rating}}</td>
							<td>{{$lang.videos.video_field_order}}</td>
						</tr>
						{{foreach item=item from=$smarty.post.videos|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30 {{if $item.status_id==0}}disabled{{/if}}">
								<td class="eg_selector"><input type="checkbox" name="delete_video_ids[]" value="{{$item.video_id}}"/></td>
								<td class="nowrap">
									{{if in_array('videos|view',$smarty.session.permissions)}}
										<a href="videos.php?action=change&amp;item_id={{$item.video_id}}">{{$item.video_id}}</a>
									{{else}}
										{{$item.video_id}}
									{{/if}}
								</td>
								<td>{{$item.title}}</td>
								<td class="nowrap">{{$item.duration}}</td>
								<td class="nowrap">{{if $item.status_id==0}}{{$lang.videos.video_field_status_disabled}}{{elseif $item.status_id==1}}{{$lang.videos.video_field_status_active}}{{elseif $item.status_id==2}}<span class="highlighted_text">{{$lang.videos.video_field_status_error}}</span>{{elseif $item.status_id==3}}{{$lang.videos.video_field_status_in_process}}{{elseif $item.status_id==4}}{{$lang.videos.video_field_status_deleting}}{{elseif $item.status_id==5}}{{$lang.videos.video_field_status_deleted}}{{/if}}</td>
								<td class="nowrap">{{if $item.is_private==2}}{{$lang.videos.video_field_type_premium}}{{elseif $item.is_private==1}}{{$lang.videos.video_field_type_private}}{{elseif $item.is_private==0}}{{$lang.videos.video_field_type_public}}{{/if}}</td>
								<td class="nowrap {{if $item.video_viewed==0}}disabled{{/if}}">{{if $item.video_viewed>999}}{{$item.video_viewed/1000|number_format:1:".":""}}{{$lang.common.traffic_k}}{{else}}{{$item.video_viewed}}{{/if}}</td>
								<td class="nowrap {{if $item.rating==0}}disabled{{/if}}">{{$item.rating|number_format:1:".":" "}}</td>
								<td>
									<input type="text" name="video_sorting_{{$item.video_id}}" maxlength="32" value="{{$item.sort_id}}" size="3" autocomplete="off"/>
								</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{/if}}
		{{if $can_edit_all==1}}
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
		{{/if}}
	</table>
</form>

{{else}}

{{if in_array('playlists|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('playlists|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
{{/if}}
{{assign var=can_invoke_additional value=1}}
{{if $can_delete==1 || $can_edit==1}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.users.playlist_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.users.playlist_field_status_disabled}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.playlist_field_status_active}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_is_private!=''}}dgf_selected{{/if}}">{{$lang.users.playlist_field_type}}:</td>
					<td class="dgf_control">
						<select name="se_is_private">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_is_private=='0'}}selected="selected"{{/if}}>{{$lang.users.playlist_field_type_public}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_is_private=='1'}}selected="selected"{{/if}}>{{$lang.users.playlist_field_type_private}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_tag!=''}}dgf_selected{{/if}}">{{$lang.users.playlist_field_tag}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_category!=''}}dgf_selected{{/if}}">{{$lang.users.playlist_field_category}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.users.playlist_field_user}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_field!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_field}}:</td>
					<td class="dgf_control">
						<select name="se_field">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_description}}</option>
							<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_rating}}</option>
							<option value="empty/playlist_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/playlist_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_visits}}</option>
							<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_tags}}</option>
							<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_categories}}</option>
							<option value="empty/videos" {{if $smarty.session.save.$page_name.se_field=="empty/videos"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.playlist_field_videos_count}}</option>
							<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_description}}</option>
							<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_rating}}</option>
							<option value="filled/playlist_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/playlist_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_visits}}</option>
							<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_tags}}</option>
							<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_categories}}</option>
							<option value="filled/videos" {{if $smarty.session.save.$page_name.se_field=="filled/videos"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.playlist_field_videos_count}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_flag_id>0}}dgf_selected{{/if}}">{{$lang.common.dg_filter_flag}}:</td>
					<td class="dgf_control">
						<select name="se_flag_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item=item_flag from=$list_flags_playlists|smarty:nodefaults}}
								<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected="selected"{{/if}}>{{$item_flag.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_review_flag>0}}dgf_selected{{/if}}">{{$lang.common.dg_filter_review_flag}}:</td>
					<td class="dgf_control">
						<select id="se_review_flag" name="se_review_flag">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_review_flag=='1'}}selected="selected"{{/if}}>{{$lang.common.dg_filter_review_flag_yes}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_review_flag=='2'}}selected="selected"{{/if}}>{{$lang.common.dg_filter_review_flag_no}}</option>
						</select>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_batch==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_batch==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.title}}</span>
									{{if $item.status_id==1}}
										<span class="js_param">activate_hide=true</span>
									{{else}}
										<span class="js_param">deactivate_hide=true</span>
									{{/if}}
									{{if $item.is_private==1}}
										<span class="js_param">deactivate_disable=true</span>
									{{/if}}
									{{if $item.is_review_needed!=1}}
										<span class="js_param">mark_reviewed_hide=true</span>
									{{/if}}
									{{if $item.website_link==''}}
										<span class="js_param">website_link_disable=true</span>
									{{else}}
										<span class="js_param">website_link={{$item.website_link}}</span>
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
					{{if $can_edit==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
							<span class="js_param">hide=${activate_hide}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
							<span class="js_param">hide=${deactivate_hide}</span>
							<span class="js_param">disable=${deactivate_disable}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
							<span class="js_param">hide=${mark_reviewed_hide}</span>
						</li>
					{{/if}}
					{{if in_array('users|manage_comments',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=13&amp;object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=${website_link}</span>
						<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
						<span class="js_param">disable=${website_link_disable}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					{{if in_array('system|administration',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=13&amp;se_object_id=${id}</span>
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
								{{if $can_edit==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
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