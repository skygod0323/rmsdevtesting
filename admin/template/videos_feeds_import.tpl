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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.videos.submenu_option_feeds_import}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.videos.feed_add}}{{else}}{{$lang.videos.feed_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/591-6-ways-to-add-videos-into-kvs">6 ways to add videos into KVS</a></span><br/>
				</td>
			</tr>
		{{/if}}
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
			<td class="de_label de_required">{{$lang.videos.feed_field_url}} (*):</td>
			<td class="de_control">
				<input type="text" name="url" class="dyn_full_size" value="{{$smarty.post.url}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="feed_type_id" name="feed_type_id">
						<option value="csv" {{if $smarty.post.feed_type_id=='csv'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_type_csv}}</option>
						<option value="kvs" {{if $smarty.post.feed_type_id=='kvs'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_type_kvs}}</option>
						<option value="rss" {{if $smarty.post.feed_type_id=='rss'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_type_rss}}</option>
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.videos.feed_field_type_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_direction}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select name="direction_id">
						<option value="0" {{if $smarty.post.direction_id=='0'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_direction_forward}}</option>
						<option value="1" {{if $smarty.post.direction_id=='1'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_direction_reverse}}</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_encoding}}:</td>
			<td class="de_control">
				<input type="text" name="feed_charset" maxlength="50" class="dyn_full_size" value="{{$smarty.post.feed_charset}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_encoding_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_new_objects}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_new_objects_categories}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_new_objects_models}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_new_objects_content_sources}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_dvds" value="1" {{if $smarty.post.is_skip_new_dvds==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_new_objects_dvds}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_duplicates}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.feed_field_key_prefix}} (*):</td>
			<td class="de_control">
				<input type="text" id="feed_key_prefix" name="key_prefix" maxlength="255" class="dyn_full_size" value="{{$smarty.post.key_prefix}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_key_prefix_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_duplicate_options}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_duplicate_options_titles}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_duplicate_options_titles_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_skip_deleted_videos" value="1" {{if $smarty.post.is_skip_deleted_videos==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_duplicate_options_deleted}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.feed_field_duplicate_options_deleted_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_scheduling}}</div></td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="de_required exec_interval_only_once_off">{{$lang.videos.feed_field_exec_interval}} (*):</div>
				<div class="exec_interval_only_once_on">{{$lang.videos.feed_field_exec_interval}}:</div>
			</td>
			<td class="de_control">
				{{$lang.videos.feed_field_exec_interval_hours}}:
				<input type="text" name="exec_interval_hours" maxlength="10" size="4" class="exec_interval_only_once_off" value="{{$smarty.post.exec_interval_hours}}"/>
				&nbsp;
				{{$lang.videos.feed_field_exec_interval_minutes}}:
				<input type="text" name="exec_interval_minutes" maxlength="10" size="4" class="exec_interval_only_once_off" value="{{$smarty.post.exec_interval_minutes}}"/>
				&nbsp;&nbsp;
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="exec_interval_only_once" name="exec_interval_only_once" value="1" {{if $smarty.post.exec_interval_hours==0 && $smarty.post.exec_interval_minutes==0}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_exec_interval_only_once}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_exec_interval_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_max_videos_per_exec}}:</td>
			<td class="de_control">
				<input type="text" name="max_videos_per_exec" maxlength="10" class="dyn_full_size" value="{{if $smarty.post.max_videos_per_exec>0}}{{$smarty.post.max_videos_per_exec}}{{/if}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_max_videos_per_exec_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_last_exec_date}}:</td>
				<td class="de_control">
					{{if $smarty.post.last_exec_date!='0000-00-00 00:00:00'}}
						{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}} (<a rel="external" href="log_feeds.php?no_filter=true&se_feed_id={{$smarty.post.feed_id}}&se_show_id=2">{{$lang.videos.feed_field_last_exec_date_stats|replace:"%1%":$smarty.post.last_exec_duration|replace:"%2%":$smarty.post.last_exec_videos_added|replace:"%3%":$smarty.post.last_exec_videos_skipped|replace:"%4%":$smarty.post.last_exec_videos_errored}}</a>)
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.feed_field_next_exec_date}}:</td>
				<td class="de_control">
					{{if $smarty.post.next_exec_date!='0000-00-00 00:00:00'}}
						{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_logging}}:</td>
			<td class="de_control">
				<input type="text" name="keep_log_days" maxlength="10" class="fixed_100" value="{{if $smarty.post.keep_log_days>0}}{{$smarty.post.keep_log_days}}{{/if}}"/>
				{{$lang.videos.feed_field_logging_days}}
				&nbsp;&nbsp;
				<div class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_logging_debug}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_logging_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_autodelete}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_autodelete" name="is_autodelete" value="1" {{if $smarty.post.is_autodelete==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_autodelete_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_autodelete_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_csv feed_type_id_rss is_autodelete_on">
			<td class="de_label de_required de_dependent">{{$lang.videos.feed_field_autodelete_url}} (*):</td>
			<td class="de_control">
				<input type="text" name="autodelete_url" class="dyn_full_size" value="{{$smarty.post.autodelete_url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_autodelete_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="is_autodelete_on">
			<td class="de_label de_required de_dependent">{{$lang.videos.feed_field_autodelete_exec_interval}} (*):</td>
			<td class="de_control">
				<input type="text" name="autodelete_exec_interval" class="fixed_100" value="{{if $smarty.post.autodelete_exec_interval>0}}{{$smarty.post.autodelete_exec_interval}}{{/if}}"/>
				{{$lang.videos.feed_field_autodelete_exec_interval_hours}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_autodelete_exec_interval_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="is_autodelete_on">
			<td class="de_label de_dependent">{{$lang.videos.feed_field_autodelete_mode}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="autodelete_mode" name="autodelete_mode">
						<option value="0" {{if $smarty.post.autodelete_mode=='0'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_autodelete_mode_delete}}</option>
						<option value="1" {{if $smarty.post.autodelete_mode=='1'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_autodelete_mode_mark}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.feed_field_autodelete_mode_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="is_autodelete_on autodelete_mode_1">
			<td class="de_label de_dependent">{{$lang.videos.feed_field_autodelete_reason}}:</td>
			<td class="de_control">
				<textarea name="autodelete_reason" class="dyn_full_size" cols="30" rows="3">{{$smarty.post.autodelete_reason}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_autodelete_reason_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr class="is_autodelete_on">
				<td class="de_label de_dependent">{{$lang.videos.feed_field_autodelete_last_exec_date}}:</td>
				<td class="de_control">
					{{if $smarty.post.autodelete_last_exec_date!='0000-00-00 00:00:00'}}
						{{$smarty.post.autodelete_last_exec_date|date_format:$smarty.session.userdata.full_date_format}} (<a rel="external" href="log_feeds.php?no_filter=true&se_feed_id={{$smarty.post.feed_id}}&se_show_id=2">{{$lang.videos.feed_field_autodelete_last_exec_date_stats|replace:"%1%":$smarty.post.autodelete_last_exec_duration|replace:"%2%":$smarty.post.autodelete_last_exec_videos}}</a>)
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr class="feed_type_id_csv">
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_data}}</div></td>
		</tr>
		<tr class="feed_type_id_csv">
			<td class="de_label">{{$lang.videos.feed_field_skip_first_row}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="csv_skip_first_row" value="1" {{if $smarty.post.csv_skip_first_row==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_skip_first_row_yes}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_skip_first_row_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_csv">
			<td class="de_label de_required">{{$lang.videos.feed_field_separator_fields}} (*):</td>
			<td class="de_control">
				<input type="text" name="separator" class="fixed_100" value="{{$smarty.post.separator|default:"|"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_separator_fields_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_csv">
			<td class="de_label de_required">{{$lang.videos.feed_field_separator_list_items}} (*):</td>
			<td class="de_control">
				<select name="separator_list_items">
					<option value="," {{if $smarty.post.separator_list_items==','}}selected="selected"{{/if}}>{{$lang.videos.feed_field_separator_list_items_comma}}</option>
					<option value=";" {{if $smarty.post.separator_list_items==';'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_separator_list_items_semicolon}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_separator_list_items_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.fields_amount>5}}
			{{assign var="loop_to" value=$smarty.post.fields_amount+1}}
		{{elseif $smarty.post.fields_amount==5}}
			{{assign var="loop_to" value=6}}
		{{else}}
			{{assign var="loop_to" value=5}}
		{{/if}}
		{{section name=data start=0 step=1 loop=$loop_to}}
			<tr class="feed_type_id_csv">
				<td class="de_label">{{$lang.videos.feed_field_data_field|replace:"%1%":$smarty.section.data.iteration}}:</td>
				<td class="de_control">
					{{assign var="field_value" value="field`$smarty.section.data.iteration`"}}
					<select id="csv_field_{{$smarty.section.data.iteration}}" name="field{{$smarty.section.data.iteration}}">
						<option value="">{{$lang.common.select_default_option}}</option>
						<option value="pass" {{if $smarty.post.$field_value=='pass'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_pass}}</option>
						<optgroup label="{{$lang.videos.feed_field_data_group_general}}">
							<option value="external_key_field" {{if $smarty.post.$field_value=='external_key_field'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_external_key}}</option>
							<option value="id" {{if $smarty.post.$field_value=='id'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_id}}</option>
							<option value="title" {{if $smarty.post.$field_value=='title'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_title}}</option>
							<option value="description" {{if $smarty.post.$field_value=='description'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_description}}</option>
							<option value="dir" {{if $smarty.post.$field_value=='dir'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_directory}}</option>
							<option value="post_date" {{if $smarty.post.$field_value=='post_date'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_post_date}}</option>
							<option value="rating" {{if $smarty.post.$field_value=='rating'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_rating}} (0 - 5)</option>
							<option value="rating_percent" {{if $smarty.post.$field_value=='rating_percent'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_rating}} (0 - 100%)</option>
							<option value="votes" {{if $smarty.post.$field_value=='votes'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_rating_votes}}</option>
							<option value="popularity" {{if $smarty.post.$field_value=='popularity'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_visits}}</option>
							<option value="release_year" {{if $smarty.post.$field_value=='release_year'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_release_year}}</option>
							<option value="user" {{if $smarty.post.$field_value=='user'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_user}}</option>
						</optgroup>
						<optgroup label="{{$lang.videos.feed_field_data_group_categorization}}">
							<option value="categories" {{if $smarty.post.$field_value=='categories'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_categories}}</option>
							<option value="models" {{if $smarty.post.$field_value=='models'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_models}}</option>
							<option value="tags" {{if $smarty.post.$field_value=='tags'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_tags}}</option>
							<option value="content_source" {{if $smarty.post.$field_value=='content_source'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_content_source}}</option>
							<option value="content_source_url" {{if $smarty.post.$field_value=='content_source_url'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_content_source_url}}</option>
							<option value="content_source_group" {{if $smarty.post.$field_value=='content_source_group'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_content_source_group}}</option>
							<option value="dvd" {{if $smarty.post.$field_value=='dvd'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_dvd}}</option>
							<option value="dvd_group" {{if $smarty.post.$field_value=='dvd_group'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_dvd_group}}</option>
						</optgroup>
						<optgroup label="{{$lang.videos.feed_field_data_group_content}}">
							<option value="duration" {{if $smarty.post.$field_value=='duration'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_duration}}</option>
							<option value="video_file" {{if $smarty.post.$field_value=='video_file'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_video_file}}</option>
							<option value="website_link" {{if $smarty.post.$field_value=='website_link'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_website_link}}</option>
							<option value="embed_code" {{if $smarty.post.$field_value=='embed_code'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_embed_code}}</option>
						</optgroup>
						<optgroup label="{{$lang.videos.feed_field_data_group_custom}}">
							<option value="custom1" {{if $smarty.post.$field_value=='custom1'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_1_NAME}}</option>
							<option value="custom2" {{if $smarty.post.$field_value=='custom2'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_2_NAME}}</option>
							<option value="custom3" {{if $smarty.post.$field_value=='custom3'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_3_NAME}}</option>
						</optgroup>
						<optgroup label="{{$lang.videos.feed_field_data_group_screenshots}}">
							<option value="screenshot_main_source" {{if $smarty.post.$field_value=='screenshot_main_source'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_screenshot_main_source}}</option>
							<option value="overview_screenshots_sources" {{if $smarty.post.$field_value=='overview_screenshots_sources'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_screenshots_overview_sources}}</option>
							<option value="screen_main" {{if $smarty.post.$field_value=='screen_main'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_screenshot_main_number}}</option>
						</optgroup>
						<optgroup label="{{$lang.videos.feed_field_data_group_posters}}">
							<option value="posters_sources" {{if $smarty.post.$field_value=='posters_sources'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_posters_sources}}</option>
							<option value="poster_main" {{if $smarty.post.$field_value=='poster_main'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_poster_main_number}}</option>
						</optgroup>
						{{if count($list_languages)>0}}
							<optgroup label="{{$lang.videos.feed_field_data_group_localization}}">
								{{foreach item="item" from=$list_languages|smarty:nodefaults}}
									<option value="title_{{$item.code}}" {{if $smarty.post.$field_value=="title_`$item.code`"}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_title}} ({{$item.title}})</option>
									<option value="description_{{$item.code}}" {{if $smarty.post.$field_value=="description_`$item.code`"}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_description}} ({{$item.title}})</option>
									<option value="dir_{{$item.code}}" {{if $smarty.post.$field_value=="dir_`$item.code`"}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_directory}} ({{$item.title}})</option>
								{{/foreach}}
							</optgroup>
						{{/if}}
					</select>
				</td>
			</tr>
		{{/section}}
		<tr class="feed_type_id_csv">
			<td class="de_label"></td>
			<td class="de_control">{{$lang.videos.feed_field_data_field_more}}</td>
		</tr>
		<tr class="feed_type_id_csv">
			<td class="de_label de_required">{{$lang.videos.feed_field_data_key_field}} (*):</td>
			<td class="de_control">
				<select name="key_field">
					<option value="">{{$lang.common.select_default_option}}</option>
					<option value="external_key_field" {{if $smarty.post.key_field=='external_key_field'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_external_key}}</option>
					<option value="id" {{if $smarty.post.key_field=='id'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_id}}</option>
					<option value="title" {{if $smarty.post.key_field=='title'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_title}}</option>
					<option value="description" {{if $smarty.post.key_field=='description'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_description}}</option>
					<option value="dir" {{if $smarty.post.key_field=='dir'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_directory}}</option>
					<option value="video_file" {{if $smarty.post.key_field=='video_file'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_video_file}}</option>
					<option value="website_link" {{if $smarty.post.key_field=='website_link'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_website_link}}</option>
					<option value="embed_code" {{if $smarty.post.key_field=='embed_code'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_embed_code}}</option>
					<option value="screenshot_main_source" {{if $smarty.post.key_field=='screenshot_main_source'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_screenshot_main_source}}</option>
					<option value="overview_screenshots_sources" {{if $smarty.post.key_field=='overview_screenshots_sources'}}selected="selected"{{/if}}>{{$lang.videos.feed_field_data_screenshots_overview_sources}}</option>
					<option value="custom1" {{if $smarty.post.key_field=='custom1'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_1_NAME}}</option>
					<option value="custom2" {{if $smarty.post.key_field=='custom2'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_2_NAME}}</option>
					<option value="custom3" {{if $smarty.post.key_field=='custom3'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_3_NAME}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_data_key_field_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_kvs">
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_data}}</div></td>
		</tr>
		<tr class="feed_type_id_kvs">
			<td class="de_label">{{$lang.videos.feed_field_data_fields}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="description" {{if in_array('description', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_description}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="dir" {{if in_array('dir', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_directory}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="rating" {{if in_array('rating', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_rating}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="votes" {{if in_array('votes', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_rating_votes}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="popularity" {{if in_array('popularity', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_visits}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="release_year" {{if in_array('release_year', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_release_year}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="post_date" {{if in_array('post_date', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_post_date}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="user" {{if in_array('user', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_user}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="tags" {{if in_array('tags', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_tags}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="categories" {{if in_array('categories', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_categories}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="models" {{if in_array('models', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_models}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="content_source" {{if in_array('content_source', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_content_source}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="content_source_group" {{if in_array('content_source_group', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_content_source_group}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="dvd" {{if in_array('dvd', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_dvd}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="dvd_group" {{if in_array('dvd_group', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_dvd_group}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="posters" {{if in_array('posters', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_group_posters}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="customization" {{if in_array('customization', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_group_custom}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" name="fields[]" value="localization" {{if in_array('localization', $smarty.post.fields) || in_array('all', $smarty.post.fields)}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_data_group_localization}}</label></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_filters}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_limit_duration}}:</td>
			<td class="de_control">
				{{$lang.videos.feed_field_limit_duration_from}}:
				<input type="text" name="limit_duration_from" maxlength="10" class="fixed_50" value="{{if $smarty.post.limit_duration_from>0}}{{$smarty.post.limit_duration_from}}{{/if}}"/>
				&nbsp;&nbsp;&nbsp;
				{{$lang.videos.feed_field_limit_duration_to}}:
				<input type="text" name="limit_duration_to" maxlength="10" class="fixed_50" value="{{if $smarty.post.limit_duration_to>0}}{{$smarty.post.limit_duration_to}}{{/if}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_limit_duration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_limit_rating}}:</td>
			<td class="de_control">
				{{$lang.videos.feed_field_limit_rating_from}}:
				<input type="text" name="limit_rating_from" maxlength="10" class="fixed_50" value="{{if $smarty.post.limit_rating_from>0}}{{$smarty.post.limit_rating_from}}{{/if}}"/>
				&nbsp;&nbsp;&nbsp;
				{{$lang.videos.feed_field_limit_rating_to}}:
				<input type="text" name="limit_rating_to" maxlength="10" class="fixed_50" value="{{if $smarty.post.limit_rating_to>0}}{{$smarty.post.limit_rating_to}}{{/if}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_limit_rating_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_limit_views}}:</td>
			<td class="de_control">
				{{$lang.videos.feed_field_limit_views_from}}:
				<input type="text" name="limit_views_from" maxlength="10" class="fixed_50" value="{{if $smarty.post.limit_views_from>0}}{{$smarty.post.limit_views_from}}{{/if}}"/>
				&nbsp;&nbsp;&nbsp;
				{{$lang.videos.feed_field_limit_views_to}}:
				<input type="text" name="limit_views_to" maxlength="10" class="fixed_50" value="{{if $smarty.post.limit_views_to>0}}{{$smarty.post.limit_views_to}}{{/if}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_limit_views_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_limit_terminology}}:</td>
			<td class="de_control">
				<input type="text" name="limit_terminology" class="dyn_full_size" value="{{$smarty.post.limit_terminology}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_limit_terminology_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.feed_divider_videos}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/250-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different">What video types are supported in KVS and how they are different</a></span><br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/577-different-ways-to-upload-video-files-into-kvs">Different ways to upload video files into KVS</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_limit_title}}:</td>
			<td class="de_control">
				<input type="text" name="title_limit" value="{{$smarty.post.title_limit}}" maxlength="10" size="4"/>
				<select name="title_limit_type_id">
					<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.videos.feed_field_limit_title_words}}</option>
					<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.videos.feed_field_limit_title_characters}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_limit_title_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_videos_status}}:</td>
			<td class="de_control">
				<select name="videos_status_id">
					<option value="0" {{if $smarty.post.videos_status_id==0}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_status_disabled}}</option>
					<option value="1" {{if $smarty.post.videos_status_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_status_active}}</option>
				</select>
				&nbsp;&nbsp;
				<div class="de_lv_pair"><input type="checkbox" name="videos_is_review_needed" value="1" {{if $smarty.post.videos_is_review_needed==1}}checked="checked"{{/if}}/><label>{{$lang.videos.feed_field_videos_need_review}}</label></div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_videos_type}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="videos_is_private" name="videos_is_private">
						<option value="0" {{if $smarty.post.videos_is_private==0}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_type_public}}</option>
						<option value="1" {{if $smarty.post.videos_is_private==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_type_private}}</option>
						<option value="2" {{if $smarty.post.videos_is_private==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_type_premium}}</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_videos_content_source}}:</td>
			<td class="de_control">
				<select name="videos_content_source_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data_groups item=item_group from=$list_content_sources|smarty:nodefaults}}
						<optgroup label="{{$item_group[0].content_source_group_title|default:$lang.videos.feed_field_videos_content_source_no_group}}">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.content_source_id}}" {{if $smarty.post.videos_content_source_id==$item.content_source_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_videos_content_source_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_videos_dvd}}:</td>
			<td class="de_control">
				<select name="videos_dvd_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data_groups item=item_group from=$list_dvds|smarty:nodefaults}}
						<optgroup label="{{$item_group[0].dvd_group_title|default:$lang.videos.feed_field_videos_dvd_no_group}}">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.dvd_id}}" {{if $smarty.post.videos_dvd_id==$item.dvd_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_videos_dvd_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_kvs feed_type_id_csv">
			<td class="de_label">{{$lang.videos.feed_field_videos_mode}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="videos_adding_mode_id" name="videos_adding_mode_id">
						<option value="1" {{if $smarty.post.videos_adding_mode_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_mode_embed}}</option>
						<option value="2" {{if $smarty.post.videos_adding_mode_id==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_mode_pseudo_video}}</option>
						<option value="3" {{if $smarty.post.videos_adding_mode_id==3}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_mode_hotlink}}</option>
						<option value="4" {{if $smarty.post.videos_adding_mode_id==4}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_mode_download}}</option>
						<option value="6" {{if $smarty.post.videos_adding_mode_id==6}}selected="selected"{{/if}}>{{$lang.videos.feed_field_videos_mode_grabbers}}</option>
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint videos_adding_mode_id_1">{{$lang.videos.feed_field_videos_mode_embed_hint}}</span>
					<span class="de_hint videos_adding_mode_id_2">{{$lang.videos.feed_field_videos_mode_pseudo_video_hint}}</span>
					<span class="de_hint videos_adding_mode_id_3">{{$lang.videos.feed_field_videos_mode_hotlink_hint}}</span>
					<span class="de_hint videos_adding_mode_id_4">{{$lang.videos.feed_field_videos_mode_download_hint}}</span>
					<span class="de_hint videos_adding_mode_id_6">{{$lang.videos.feed_field_videos_mode_grabbers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_rss">
			<td class="de_label">{{$lang.videos.feed_field_videos_mode}}:</td>
			<td class="de_control">
				{{$lang.videos.feed_field_videos_mode_grabbers}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_videos_mode_grabbers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="videos_adding_mode_id_4 feed_type_id_kvs feed_type_id_csv">
			<td class="de_label de_dependent">{{$lang.videos.feed_field_format}}:</td>
			<td class="de_control">
				<select name="format_video_id">
					<option value="0">{{$lang.videos.feed_field_format_source}}</option>
					<option value="9999999" {{if $smarty.post.format_video_id==9999999}}selected="selected"{{/if}}>{{$lang.videos.feed_field_format_multiple}}</option>
					{{foreach item=item_group from=$list_formats_videos|smarty:nodefaults}}
						<optgroup label="{{if $item_group[0].video_type_id==0}}{{$lang.videos.common_formats_standard}}{{elseif $item_group[0].video_type_id==1}}{{$lang.videos.common_formats_premium}}{{/if}}">
							{{foreach item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.format_video_id}}" {{if $smarty.post.format_video_id==$item.format_video_id}}selected="selected"{{/if}}>{{$lang.videos.feed_field_format_format|replace:"%1%":$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_format_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="feed_type_id_kvs feed_type_id_csv">
			<td class="de_label">{{$lang.videos.feed_field_screenshots_mode}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="screenshots_mode_id" name="screenshots_mode_id">
						<option value="1" {{if $smarty.post.screenshots_mode_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_screenshots_mode_feed}}</option>
						<option value="2" {{if $smarty.post.screenshots_mode_id==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_screenshots_mode_create}}</option>
						<option value="3" {{if $smarty.post.screenshots_mode_id==3}}selected="selected"{{/if}}>{{$lang.videos.feed_field_screenshots_mode_feed_main}}</option>
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint screenshots_mode_id_1">{{$lang.videos.feed_field_screenshots_mode_feed_hint}}</span>
					<span class="de_hint screenshots_mode_id_2">{{$lang.videos.feed_field_screenshots_mode_create_hint}}</span>
					<span class="de_hint screenshots_mode_id_3">{{$lang.videos.feed_field_screenshots_mode_feed_main_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.feed_field_post_date_mode}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="post_date_mode_id" name="post_date_mode_id">
						<option value="1" {{if $smarty.post.post_date_mode_id==1}}selected="selected"{{/if}}>{{$lang.videos.feed_field_post_date_mode_current}}</option>
						<option value="2" {{if $smarty.post.post_date_mode_id==2}}selected="selected"{{/if}}>{{$lang.videos.feed_field_post_date_mode_feed}}</option>
						<option value="3" {{if $smarty.post.post_date_mode_id==3}}selected="selected"{{/if}}>{{$lang.videos.feed_field_post_date_mode_uniform}}</option>
						<option value="4" {{if $smarty.post.post_date_mode_id==4}}selected="selected"{{/if}}>{{$lang.videos.feed_field_post_date_mode_random}}</option>
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint post_date_mode_id_1">{{$lang.videos.feed_field_post_date_mode_current_hint}}</span>
					<span class="de_hint post_date_mode_id_2">{{$lang.videos.feed_field_post_date_mode_feed_hint}}</span>
					<span class="de_hint post_date_mode_id_3">{{$lang.videos.feed_field_post_date_mode_uniform_hint}}</span>
					<span class="de_hint post_date_mode_id_4">{{$lang.videos.feed_field_post_date_mode_random_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="post_date_mode_id_3">
			<td class="de_label de_required">{{$lang.videos.feed_field_future_interval}} (*):</td>
			<td class="de_control">
				<input type="text" name="end_date_offset" maxlength="10" class="dyn_full_size" value="{{if $smarty.post.end_date_offset>0}}{{$smarty.post.end_date_offset}}{{/if}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_future_interval_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="post_date_mode_id_4">
			<td class="de_label de_required">{{$lang.videos.feed_field_date_interval}} (*):</td>
			<td class="de_control">
				{{$lang.videos.feed_field_date_interval_from}}: {{html_select_date prefix='start_date_interval_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.start_date_interval}}&nbsp;
				{{$lang.videos.feed_field_date_interval_to}}: {{html_select_date prefix='end_date_interval_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.end_date_interval}}
			</td>
		</tr>
		<tr class="post_date_mode_id_3 post_date_mode_id_4">
			<td class="de_label">
				<div class="de_required post_date_mode_id_3">{{$lang.videos.feed_field_max_videos_per_day}} (*):</div>
				<div class="post_date_mode_id_4">{{$lang.videos.feed_field_max_videos_per_day}}:</div>
			</td>
			<td class="de_control">
				<input type="text" name="max_videos_per_day" maxlength="10" class="dyn_full_size" value="{{if $smarty.post.max_videos_per_day>0}}{{$smarty.post.max_videos_per_day}}{{/if}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.feed_field_max_videos_per_day_hint}}</span>
				{{/if}}
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
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}" id="feed_save_submit1"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}" id="feed_save_submit2"/>
				{{/if}}
			</td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildFeedFieldsLogic=call('{{$smarty.post.key_prefix|replace:"'":"\'"}}')</span>
</div>

{{else}}

{{assign var=can_delete value=1}}

{{if $can_delete==1}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
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
									{{if $item.status_id==1}}
										<span class="js_param">activate_hide=true</span>
									{{else}}
										<span class="js_param">deactivate_hide=true</span>
									{{/if}}
									{{if $item.is_debug_enabled==1}}
										<span class="js_param">enable_debug_hide=true</span>
										<span class="js_param">log_type=2</span>
									{{else}}
										<span class="js_param">disable_debug_hide=true</span>
										<span class="js_param">log_type=0</span>
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
					<li class="js_params">
						<span class="js_param">href=?batch_action=execute&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.videos.feed_action_run_feed}}</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=enable_debug&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
						<span class="js_param">hide=${enable_debug_hide}</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=?batch_action=disable_debug&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_disable_debug}}</span>
						<span class="js_param">hide=${disable_debug_hide}</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=log_feeds.php?no_filter=true&amp;se_feed_id=${id}&amp;se_show_id=${log_type}</span>
						<span class="js_param">title={{$lang.videos.feed_action_view_log}}</span>
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
								<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
								<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
								<option value="execute">{{$lang.videos.feed_batch_action_run_selected}}</option>
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