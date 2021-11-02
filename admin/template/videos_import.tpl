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

{{if $import_id>0 && $smarty.get.action=='import_start'}}

<form action="{{$page_name}}" method="post">
	<div>
		<input type="hidden" name="import_id" value="{{$import_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="videos.php">{{$lang.videos.submenu_option_videos_list}}</a> / <a href="{{$page_name}}?action=back_import&amp;import_id={{$import_id}}">{{$lang.videos.import_header_import}}</a> / {{$lang.videos.import_header_preview}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_preview_total_items}}:</td>
			<td class="de_control">{{$import_stats.items}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_preview_total_empty_lines}}:</td>
			<td class="de_control">{{$import_stats.empty_lines}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_preview_total_errors}}:</td>
			<td class="de_control">{{$import_stats.errors}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_preview_total_warnings}}:</td>
			<td class="de_control">{{$import_stats.warnings}}</td>
		</tr>
		{{if $import_stats.errors!=0 || $import_stats.warnings!=0 || $import_stats.info!=0}}
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col width="10%"/>
							<col width="15%"/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.videos.import_preview_col_row}}</td>
							<td>{{$lang.videos.import_preview_col_type}}</td>
							<td>{{$lang.videos.import_preview_col_message}}</td>
						</tr>
						{{assign var=row_num value=1}}
						{{foreach key=key item=item from=$import_result|smarty:nodefaults}}
						{{if is_array($item.errors)}}
							{{foreach item=item2 from=$item.errors|smarty:nodefaults}}
								<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
									<td>{{$key}}</td>
									<td class="highlighted_text">{{$lang.videos.import_preview_col_type_error}}</td>
									<td>{{$item2}}</td>
								</tr>
								{{assign var=row_num value=$row_num+1}}
							{{/foreach}}
							{{if is_array($item.warnings)}}
								{{foreach item=item2 from=$item.warnings|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
										<td>{{$key}}</td>
										<td class="warning_text">{{$lang.videos.import_preview_col_type_warning}}</td>
										<td>{{$item2}}</td>
									</tr>
									{{assign var=row_num value=$row_num+1}}
								{{/foreach}}
							{{/if}}
							{{if is_array($item.info)}}
								{{foreach item=item2 from=$item.info|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
										<td>{{$key}}</td>
										<td>{{$lang.videos.import_preview_col_type_info}}</td>
										<td>{{$item2}}</td>
									</tr>
									{{assign var=row_num value=$row_num+1}}
								{{/foreach}}
							{{/if}}
						{{/if}}
						{{/foreach}}

						{{foreach key=key item=item from=$import_result|smarty:nodefaults}}
							{{if is_array($item.warnings) && !is_array($item.errors)}}
								{{foreach item=item2 from=$item.warnings|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
										<td>{{$key}}</td>
										<td class="warning_text">{{$lang.videos.import_preview_col_type_warning}}</td>
										<td>{{$item2}}</td>
									</tr>
									{{assign var=row_num value=$row_num+1}}
								{{/foreach}}
							{{/if}}
						{{/foreach}}

						{{foreach key=key item=item from=$import_result|smarty:nodefaults}}
							{{if is_array($item.info) && !is_array($item.errors)}}
								{{foreach item=item2 from=$item.info|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
										<td>{{$key}}</td>
										<td>{{$lang.videos.import_preview_col_type_info}}</td>
										<td>{{$item2}}</td>
									</tr>
									{{assign var=row_num value=$row_num+1}}
								{{/foreach}}
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="back_import" value="{{$lang.videos.import_btn_back}}"/>
				{{if $import_stats.errors==0}}
					<input type="submit" name="save_default" value="{{$lang.videos.import_btn_confirmed}}"/>
				{{else}}
					<input type="submit" name="save_default" value="{{$lang.videos.import_btn_skip}}" {{if $import_stats.ok_lines==0}}disabled="disabled"{{/if}}/>
				{{/if}}
			</td>
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
		<input type="hidden" name="action" value="start_import"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="videos.php">{{$lang.videos.submenu_option_videos_list}}</a> / {{$lang.videos.import_header_import}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/591-6-ways-to-add-videos-into-kvs">6 ways to add videos into KVS</a></span><br/>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.import_export_field_preset}}:</td>
			<td class="de_control">
				<select id="preset_id" name="preset_id" class="fixed_250">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach key=key item=item from=$list_presets|smarty:nodefaults}}
						<option value="{{$key}}" {{if $smarty.get.preset_id==$key || $smarty.post.preset_id==$key || $smarty.post.preset_name==$key}}selected="selected"{{/if}}>{{$key}}</option>
					{{/foreach}}
				</select>
				&nbsp;&nbsp;
				{{$lang.videos.import_export_field_preset_create}}:
				<input type="text" name="preset_name" maxlength="50" class="fixed_150"/>
				&nbsp;&nbsp;
				<div class="de_lv_pair"><input type="checkbox" name="is_default_preset" value="1" {{if $smarty.post.is_default_preset==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_export_field_preset_default}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_export_field_preset_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_export_field_preset_description}}:</td>
			<td class="de_control">
				<textarea name="preset_description" class="dyn_full_size" cols="40" rows="3">{{$smarty.post.preset_description}}</textarea>
			</td>
		</tr>
		<tr>
			<td class="de_label"></td>
			<td class="de_control">
				<input type="submit" id="delete_preset" name="delete_preset" value="{{$lang.videos.import_export_btn_delete_preset}}" {{if $smarty.get.preset_id==''}}disabled="disabled"{{/if}}/>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.import_divider_data}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.import_field_data_text}} (*):</td>
			<td class="de_control">
				<textarea name="data" class="dyn_full_size html_code_editor" cols="40" rows="8">{{$smarty.post.data}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_data_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.import_field_data_file}} (*):</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.videos.import_field_data_file}}</span>
					</div>
					<input type="text" name="file" class="fixed_500" value="{{$smarty.post.file}}" readonly="readonly"/>
					<input type="hidden" name="file_hash" value="{{$smarty.post.file_hash}}"/>
					<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
					<input type="button" class="de_fu_remove{{if $smarty.post.file_hash==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.import_export_field_separator_fields}} (*):</td>
			<td class="de_control">
				<input type="text" name="separator" class="fixed_100" value="{{$smarty.post.separator|default:"\\t"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_export_field_separator_fields_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.import_export_field_separator_lines}} (*):</td>
			<td class="de_control">
				<input type="text" name="line_separator" class="fixed_100" value="{{$smarty.post.line_separator|default:"\\r\\n"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_export_field_separator_lines_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.import_divider_fields}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/250-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different">What video types are supported in KVS and how they are different</a></span><br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/577-different-ways-to-upload-video-files-into-kvs">Different ways to upload video files into KVS</a></span>
				</td>
			</tr>
		{{/if}}

		{{if $smarty.post.fields_amount>5}}
			{{assign var="loop_to" value=$smarty.post.fields_amount}}
		{{else}}
			{{assign var="loop_to" value=5}}
		{{/if}}

		{{section name=data start=0 step=1 loop=$loop_to}}
		<tr>
			<td class="de_label">{{$lang.videos.import_export_field|replace:"%1%":$smarty.section.data.iteration}}:</td>
			<td class="de_control">
				{{assign var="field_value" value="field`$smarty.section.data.iteration`"}}
				<select id="ei_field_{{$smarty.section.data.iteration}}" name="field{{$smarty.section.data.iteration}}" {{if $smarty.post.is_import_all==1}}disabled="disabled"{{/if}}>
					<option value="">{{$lang.common.select_default_option}}</option>
					<option value="skip" {{if $smarty.post.$field_value=='skip'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_skip}}</option>
					<optgroup label="{{$lang.videos.import_export_group_general}}">
						<option value="video_id" {{if $smarty.post.$field_value=='video_id'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_id_hint}}">{{$lang.videos.import_export_field_id}}</option>
						<option value="title" {{if $smarty.post.$field_value=='title'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_title_hint}}">{{$lang.videos.import_export_field_title}}</option>
						<option value="description" {{if $smarty.post.$field_value=='description'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_description_hint}}">{{$lang.videos.import_export_field_description}}</option>
						<option value="directory" {{if $smarty.post.$field_value=='directory'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_directory_hint}}">{{$lang.videos.import_export_field_directory}}</option>
						<option value="post_date" {{if $smarty.post.$field_value=='post_date'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_post_date_hint}}">{{$lang.videos.import_export_field_post_date}}</option>
						{{if $config.relative_post_dates=='true'}}
							<option value="relative_post_date" {{if $smarty.post.$field_value=='relative_post_date'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_post_date_relative_hint}}">{{$lang.videos.import_export_field_post_date_relative}}</option>
						{{/if}}
						<option value="rating" {{if $smarty.post.$field_value=='rating'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_rating_hint}}">{{$lang.videos.import_export_field_rating}}</option>
						<option value="rating_percent" {{if $smarty.post.$field_value=='rating_percent'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_rating_percent_hint}}">{{$lang.videos.import_export_field_rating_percent}}</option>
						<option value="rating_amount" {{if $smarty.post.$field_value=='rating_amount'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_rating_amount_hint}}">{{$lang.videos.import_export_field_rating_amount}}</option>
						<option value="video_viewed" {{if $smarty.post.$field_value=='video_viewed'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_visits_hint}}">{{$lang.videos.import_export_field_visits}}</option>
						<option value="user" {{if $smarty.post.$field_value=='user'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_user_hint}}">{{$lang.videos.import_export_field_user}}</option>
						<option value="status" {{if $smarty.post.$field_value=='status'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_status_hint}}">{{$lang.videos.import_export_field_status}}</option>
						<option value="type" {{if $smarty.post.$field_value=='type'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_type_hint}}">{{$lang.videos.import_export_field_type}}</option>
						<option value="access_level" {{if $smarty.post.$field_value=='access_level'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_access_level_hint}}">{{$lang.videos.import_export_field_access_level}}</option>
						{{if $config.installation_type>=2}}
							<option value="tokens" {{if $smarty.post.$field_value=='tokens'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_tokens_cost_hint}}">{{$lang.videos.import_export_field_tokens_cost}}</option>
						{{/if}}
						<option value="release_year" {{if $smarty.post.$field_value=='release_year'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_release_year_hint}}">{{$lang.videos.import_export_field_release_year}}</option>
						<option value="admin_flag" {{if $smarty.post.$field_value=='admin_flag'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_admin_flag_hint}}">{{$lang.videos.import_export_field_admin_flag}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_categorization}}">
						<option value="categories" {{if $smarty.post.$field_value=='categories'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_categories_hint}}">{{$lang.videos.import_export_field_categories}}</option>
						{{foreach item=item from=$list_categories_groups|smarty:nodefaults}}
							<option value="categoty_group_{{$item.category_group_id}}" {{if $smarty.post.$field_value=="categoty_group_`$item.category_group_id`"}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_categories_hint}}">{{$lang.videos.import_export_field_categories}} ({{$item.title}})</option>
						{{/foreach}}
						<option value="models" {{if $smarty.post.$field_value=='models'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_models_hint}}">{{$lang.videos.import_export_field_models}}</option>
						<option value="tags" {{if $smarty.post.$field_value=='tags'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_tags_hint}}">{{$lang.videos.import_export_field_tags}}</option>
						<option value="content_source" {{if $smarty.post.$field_value=='content_source'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_content_source_hint}}">{{$lang.videos.import_export_field_content_source}}</option>
						<option value="content_source/url" {{if $smarty.post.$field_value=='content_source/url'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_content_source_url_hint}}">{{$lang.videos.import_export_field_content_source_url}}</option>
						<option value="content_source/group" {{if $smarty.post.$field_value=='content_source/group'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_content_source_group_hint}}">{{$lang.videos.import_export_field_content_source_group}}</option>
						<option value="dvd" {{if $smarty.post.$field_value=='dvd'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_dvd_hint}}">{{$lang.videos.import_export_field_dvd}}</option>
						<option value="dvd/group" {{if $smarty.post.$field_value=='dvd/group'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_dvd_group_hint}}">{{$lang.videos.import_export_field_dvd_group}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_content}}">
						<option value="video_file" {{if $smarty.post.$field_value=='video_file'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_source_file_hint}}">{{$lang.videos.import_export_field_source_file}}</option>
						{{foreach item=item from=$list_formats_videos|smarty:nodefaults}}
							<option value="format_video_{{$item.format_video_id}}" {{if $smarty.post.$field_value=="format_video_`$item.format_video_id`"}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_video_file_hint}}">{{$lang.videos.import_export_field_video_file|replace:"%1%":$item.title}}</option>
						{{/foreach}}
						<option value="video_url" {{if $smarty.post.$field_value=='video_url'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_video_url_hint}}">{{$lang.videos.import_export_field_video_url}}</option>
						<option value="embed_code" {{if $smarty.post.$field_value=='embed_code'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_embed_code_hint}}">{{$lang.videos.import_export_field_embed_code}}</option>
						<option value="gallery_url" {{if $smarty.post.$field_value=='gallery_url'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_gallery_url_hint}}">{{$lang.videos.import_export_field_gallery_url}}</option>
						<option value="pseudo_url" {{if $smarty.post.$field_value=='pseudo_url'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_pseudo_url_hint}}">{{$lang.videos.import_export_field_pseudo_url}}</option>
						<option value="duration" {{if $smarty.post.$field_value=='duration'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_duration_hint}}">{{$lang.videos.import_export_field_duration}}</option>
						<option value="server_group" {{if $smarty.post.$field_value=='server_group'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_server_group_hint}}">{{$lang.videos.import_export_field_server_group}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_custom}}">
						<option value="custom_1" {{if $smarty.post.$field_value=='custom_1'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_custom_field_hint}}">{{$options.VIDEO_FIELD_1_NAME}}</option>
						<option value="custom_2" {{if $smarty.post.$field_value=='custom_2'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_custom_field_hint}}">{{$options.VIDEO_FIELD_2_NAME}}</option>
						<option value="custom_3" {{if $smarty.post.$field_value=='custom_3'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_custom_field_hint}}">{{$options.VIDEO_FIELD_3_NAME}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_screenshots}}">
						<option value="screenshot_main_number" {{if $smarty.post.$field_value=='screenshot_main_number'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_screenshot_main_number_hint}}">{{$lang.videos.import_export_field_screenshot_main_number}}</option>
						<option value="screenshot_main_source" {{if $smarty.post.$field_value=='screenshot_main_source'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_screenshot_main_source_hint}}">{{$lang.videos.import_export_field_screenshot_main_source}}</option>
						<option value="overview_screenshots_zip" {{if $smarty.post.$field_value=='overview_screenshots_zip'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_screenshots_overview_zip_hint}}">{{$lang.videos.import_export_field_screenshots_overview_zip}}</option>
						<option value="overview_screenshots_sources" {{if $smarty.post.$field_value=='overview_screenshots_sources'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_screenshots_overview_sources_hint}}">{{$lang.videos.import_export_field_screenshots_overview_sources}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_posters}}">
						<option value="poster_main_number" {{if $smarty.post.$field_value=='poster_main_number'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_poster_main_number_hint}}">{{$lang.videos.import_export_field_poster_main_number}}</option>
						<option value="posters_zip" {{if $smarty.post.$field_value=='posters_zip'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_posters_zip_hint}}">{{$lang.videos.import_export_field_posters_zip}}</option>
						<option value="posters_sources" {{if $smarty.post.$field_value=='posters_sources'}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_posters_sources_hint}}">{{$lang.videos.import_export_field_posters_sources}}</option>
					</optgroup>
					{{if count($list_languages)>0}}
						<optgroup label="{{$lang.videos.import_export_group_localization}}">
							{{foreach item=item from=$list_languages|smarty:nodefaults}}
								<option value="title_{{$item.code}}" {{if $smarty.post.$field_value=="title_`$item.code`"}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_title_hint}}">{{$lang.videos.import_export_field_title}} ({{$item.title}})</option>
								<option value="description_{{$item.code}}" {{if $smarty.post.$field_value=="description_`$item.code`"}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_description_hint}}">{{$lang.videos.import_export_field_description}} ({{$item.title}})</option>
								<option value="directory_{{$item.code}}" {{if $smarty.post.$field_value=="directory_`$item.code`"}}selected="selected"{{/if}} title="{{$lang.videos.import_export_field_directory_hint}}">{{$lang.videos.import_export_field_directory}} ({{$item.title}})</option>
							{{/foreach}}
						</optgroup>
					{{/if}}
				</select>
				&nbsp;
				<span id="ei_field_desc_{{$smarty.section.data.iteration}}"></span>
			</td>
		</tr>
		{{/section}}
		<tr>
			<td class="de_label"></td>
			<td class="de_control">{{$lang.videos.import_export_field_more}}</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.import_export_divider_options}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_threads}}:</td>
			<td class="de_control">
				<select name="threads">
					{{section name="threads" start="1" loop="21"}}
						<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.threads}}selected="selected"{{/if}}>{{$smarty.section.threads.iteration}}</option>
					{{/section}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_threads_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_limit_title}}:</td>
			<td class="de_control">
				<input type="text" name="title_limit" value="{{$smarty.post.title_limit}}" maxlength="10" size="4"/>
				<select name="title_limit_type_id">
					<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.videos.import_field_limit_title_words}}</option>
					<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.videos.import_field_limit_title_characters}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_limit_title_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_limit_description}}:</td>
			<td class="de_control">
				<input type="text" name="description_limit" value="{{$smarty.post.description_limit}}" maxlength="10" size="4"/>
				<select name="description_limit_type_id">
					<option value="1" {{if $smarty.post.description_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.videos.import_field_limit_description_words}}</option>
					<option value="2" {{if $smarty.post.description_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.videos.import_field_limit_description_characters}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_limit_description_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_status_after_import}}:</td>
			<td class="de_control">
				<select name="status_after_import_id">
					<option value="0" {{if $smarty.post.status_after_import_id=="0"}}selected="selected"{{/if}}>{{$lang.videos.import_field_status_after_import_active}}</option>
					<option value="1" {{if $smarty.post.status_after_import_id=="1"}}selected="selected"{{/if}}>{{$lang.videos.import_field_status_after_import_disabled}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_video_type}}:</td>
			<td class="de_control">
				<select name="default_video_type">
					<option value="public" {{if $smarty.post.default_video_type=="public"}}selected="selected"{{/if}}>{{$lang.videos.import_field_video_type_public}}</option>
					<option value="private" {{if $smarty.post.default_video_type=="private"}}selected="selected"{{/if}}>{{$lang.videos.import_field_video_type_private}}</option>
					<option value="premium" {{if $smarty.post.default_video_type=="premium"}}selected="selected"{{/if}}>{{$lang.videos.import_field_video_type_premium}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_video_type_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_content_source}}:</td>
			<td class="de_control">
				<select name="content_source_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data_groups item=item_group from=$list_content_sources|smarty:nodefaults}}
						<optgroup label="{{$item_group[0].content_source_group_title|default:$lang.videos.import_field_content_source_no_group}}">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.content_source_id}}" {{if $smarty.post.content_source_id==$item.content_source_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_content_source_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_content_source_categories}}:</td>
			<td class="de_control">
				<select name="content_source_categories_id">
					<option value="0" {{if $smarty.post.content_source_categories_id==0}}selected="selected"{{/if}}>{{$lang.videos.import_field_content_source_categories_no}}</option>
					<option value="1" {{if $smarty.post.content_source_categories_id==1}}selected="selected"{{/if}}>{{$lang.videos.import_field_content_source_categories_empty}}</option>
					<option value="2" {{if $smarty.post.content_source_categories_id==2}}selected="selected"{{/if}}>{{$lang.videos.import_field_content_source_categories_always}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_content_source_categories_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_model_categories}}:</td>
			<td class="de_control">
				<select name="model_categories_id">
					<option value="0" {{if $smarty.post.model_categories_id==0}}selected="selected"{{/if}}>{{$lang.videos.import_field_model_categories_no}}</option>
					<option value="1" {{if $smarty.post.model_categories_id==1}}selected="selected"{{/if}}>{{$lang.videos.import_field_model_categories_empty}}</option>
					<option value="2" {{if $smarty.post.model_categories_id==2}}selected="selected"{{/if}}>{{$lang.videos.import_field_model_categories_always}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.import_field_model_categories_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_users}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users_noid.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.videos.import_field_users_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="users" value="{{$smarty.post.users}}"/>
					<div class="controls">
						<input type="text" name="new_user" class="preserve_editing fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.import_field_users_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.videos.import_field_users_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_post_date}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_post_time_randomization" name="is_post_time_randomization" value="1" {{if $smarty.post.is_post_time_randomization==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_time_randomization==1 || $smarty.post.fields_amount<1}}class="selected"{{/if}}>{{$lang.videos.import_field_post_date_enable_time_random}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{$lang.videos.import_field_post_date_interval_from}}: <input type="text" name="post_time_randomization_from" maxlength="5" class="is_post_time_randomization_on" size="4" value="{{$smarty.post.post_time_randomization_from}}"/>&nbsp;
							{{$lang.videos.import_field_post_date_interval_to}}: <input type="text" name="post_time_randomization_to" maxlength="5" class="is_post_time_randomization_on" size="4" value="{{$smarty.post.post_time_randomization_to}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.import_field_post_date_enable_time_random_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pd_random" name="is_post_date_randomization" value="1" {{if $smarty.post.is_post_date_randomization==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_date_randomization==1}}class="selected"{{/if}}>{{$lang.videos.import_field_post_date_enable_date_random}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{if $config.relative_post_dates=='true'}}
								<div class="de_vis_sw_radio">
									<table class="control_group">
										<tr>
											<td>
												<div class="de_lv_pair"><input id="post_date_randomization_option_fixed" type="radio" name="post_date_randomization_option" class="pd_random_on" value="0" {{if $smarty.post.post_date_randomization_option!='1'}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_post_date_option_fixed}}</label></div>
												{{$lang.videos.import_field_post_date_interval_from}}: {{html_select_date prefix='post_date_randomization_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_from all_extra='class="pd_random_on post_date_randomization_option_fixed"'}}&nbsp;
												{{$lang.videos.import_field_post_date_interval_to}}: {{html_select_date prefix='post_date_randomization_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_to all_extra='class="pd_random_on post_date_randomization_option_fixed"'}}
												{{if $smarty.session.userdata.is_expert_mode==0}}
													<br/><span class="de_hint">{{$lang.videos.import_field_post_date_enable_date_random_hint}}</span>
												{{/if}}
											</td>
										</tr>
										<tr>
											<td>
												<div class="de_lv_pair"><input id="post_date_randomization_option_relative" type="radio" name="post_date_randomization_option" class="pd_random_on" value="1" {{if $smarty.post.post_date_randomization_option=='1'}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_post_date_option_relative}}</label></div>
												{{$lang.videos.import_field_post_date_interval_from}}: <input type="text" name="relative_post_date_randomization_from" class="fixed_100 pd_random_on post_date_randomization_option_relative" value="{{$smarty.post.relative_post_date_randomization_from}}" maxlength="5"/>&nbsp;&nbsp;
												{{$lang.videos.import_field_post_date_interval_to}}: <input type="text" name="relative_post_date_randomization_to" class="fixed_100 pd_random_on post_date_randomization_option_relative" value="{{$smarty.post.relative_post_date_randomization_to}}" maxlength="5"/>
												{{if $smarty.session.userdata.is_expert_mode==0}}
													<br/><span class="de_hint">{{$lang.videos.import_field_post_date_enable_date_random_hint2}}</span>
												{{/if}}
											</td>
										</tr>
									</table>
								</div>
							{{else}}
								{{$lang.videos.import_field_post_date_interval_from}}: {{html_select_date prefix='post_date_randomization_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_from all_extra='class="pd_random_on"'}}&nbsp;
								{{$lang.videos.import_field_post_date_interval_to}}: {{html_select_date prefix='post_date_randomization_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_to all_extra='class="pd_random_on"'}}
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.videos.import_field_post_date_enable_date_random_hint}}</span>
								{{/if}}
							{{/if}}
						</td>
					</tr>
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pd_random_days" name="is_post_date_randomization_days" value="1" {{if $smarty.post.is_post_date_randomization_days==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_date_randomization_days==1 || $smarty.post.fields_amount<1}}class="selected"{{/if}}>{{$lang.videos.import_field_post_date_enable_date_random2}}</span></div></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="post_date_randomization_days" class="pd_random_days_on fixed_100" value="{{$smarty.post.post_date_randomization_days}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.import_field_post_date_enable_date_random2_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_duplicates}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_duplicates_skip_duplicate_titles}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_urls" value="1" {{if $smarty.post.is_skip_duplicate_urls==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_duplicates_skip_duplicate_urls}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_embeds" value="1" {{if $smarty.post.is_skip_duplicate_embeds==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_duplicates_skip_duplicate_embeds}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_validation}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_validate_video_urls" value="1" {{if $smarty.post.is_validate_video_urls==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_validation_validate_video_urls}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_validate_screenshot_urls" value="1" {{if $smarty.post.is_validate_screenshot_urls==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_validation_validate_screenshot_urls}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_validate_grabber_urls" value="1" {{if $smarty.post.is_validate_grabber_urls==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_validation_validate_grabber_urls}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_new_objects}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_new_objects_categories}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_new_objects_models}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_new_objects_content_sources}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_dvds" value="1" {{if $smarty.post.is_skip_new_dvds==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_new_objects_dvds}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_field_options}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.is_review_needed==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_options_need_review}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_make_directories" value="1" {{if $smarty.post.is_make_directories==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_options_directories_from_title}}</label></div></td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_use_rename_as_copy" value="1" {{if $smarty.post.is_use_rename_as_copy==1}}checked="checked"{{/if}}/><label>{{$lang.videos.import_field_options_use_rename_as_copy}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.videos.import_field_options_use_rename_as_copy_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" name="save_default" value="{{$lang.videos.import_btn_import}}"/></td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildExportImportFieldsLogic=call</span>
	<span class="js_param">buildExportImportPresetLogic=call</span>
	<span class="js_param">buildImportDateRandomizationLogic=call</span>
</div>

{{/if}}