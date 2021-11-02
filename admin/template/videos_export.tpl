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

<form action="{{$page_name}}" method="post" class="hide_errors_on_success">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="start_export"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="videos.php">{{$lang.videos.submenu_option_videos_list}}</a> / {{$lang.videos.export_header_export}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.import_export_field_preset}}:</td>
			<td class="de_control">
				<select id="preset_id" name="preset_id" class="fixed_250">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach key=key item=item from=$list_presets|smarty:nodefaults}}
						<option value="{{$key}}" {{if $smarty.get.preset_id==$key}}selected="selected"{{/if}}>{{$key}}</option>
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
			<td class="de_label"></td>
			<td class="de_control">
				<input type="submit" id="delete_preset" name="delete_preset" value="{{$lang.videos.import_export_btn_delete_preset}}" {{if $smarty.get.preset_id==''}}disabled="disabled"{{/if}}/>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.export_divider_fields}}</div></td>
		</tr>
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
				<select id="ei_field_{{$smarty.section.data.iteration}}" name="field{{$smarty.section.data.iteration}}">
					<option value="">{{$lang.common.select_default_option}}</option>
					<optgroup label="{{$lang.videos.import_export_group_general}}">
						<option value="video_id" {{if $smarty.post.$field_value=='video_id'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_id}}</option>
						<option value="title" {{if $smarty.post.$field_value=='title'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_title}}</option>
						<option value="description" {{if $smarty.post.$field_value=='description'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_description}}</option>
						<option value="directory" {{if $smarty.post.$field_value=='directory'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_directory}}</option>
						<option value="content_source" {{if $smarty.post.$field_value=='content_source'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_content_source}}</option>
						<option value="content_source/url" {{if $smarty.post.$field_value=='content_source/url'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_content_source_url}}</option>
						<option value="dvd" {{if $smarty.post.$field_value=='dvd'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_dvd}}</option>
						<option value="website_link" {{if $smarty.post.$field_value=='website_link'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_website_link}}</option>
						{{foreach item=item from=$list_satellites|smarty:nodefaults}}
							{{if is_array($item.website_ui_data)}}
								<option value="website_link/{{$item.multi_prefix}}" {{if $smarty.post.$field_value=="website_link/`$item.multi_prefix`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_satellite_link}} ({{$item.project_url}})</option>
							{{/if}}
						{{/foreach}}
						<option value="post_date" {{if $smarty.post.$field_value=='post_date'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_post_date}}</option>
						<option value="added_date" {{if $smarty.post.$field_value=='added_date'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_added_date}}</option>
						<option value="rating" {{if $smarty.post.$field_value=='rating'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_rating}}</option>
						<option value="rating_percent" {{if $smarty.post.$field_value=='rating_percent'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_rating_percent}}</option>
						<option value="rating_amount" {{if $smarty.post.$field_value=='rating_amount'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_rating_amount}}</option>
						<option value="video_viewed" {{if $smarty.post.$field_value=='video_viewed'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_visits}}</option>
						<option value="user" {{if $smarty.post.$field_value=='user'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_user}}</option>
						<option value="status" {{if $smarty.post.$field_value=='status'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_status}}</option>
						<option value="type" {{if $smarty.post.$field_value=='type'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_type}}</option>
						{{if $config.installation_type>=2}}
							<option value="tokens" {{if $smarty.post.$field_value=='tokens'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_tokens_cost}}</option>
						{{/if}}
						<option value="release_year" {{if $smarty.post.$field_value=='release_year'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_release_year}}</option>
						<option value="admin_flag" {{if $smarty.post.$field_value=='admin_flag'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_admin_flag}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_categorization}}">
						<option value="categories" {{if $smarty.post.$field_value=='categories'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_categories}}</option>
						<option value="models" {{if $smarty.post.$field_value=='models'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_models}}</option>
						<option value="tags" {{if $smarty.post.$field_value=='tags'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_tags}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_content}}">
						{{foreach item=item from=$list_formats_videos|smarty:nodefaults}}
							<option value="format_video_{{$item.format_video_id}}" {{if $smarty.post.$field_value=="format_video_`$item.format_video_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_video_file_temp_link|replace:"%1%":$item.title}}</option>
							<option value="hotlink_video_{{$item.format_video_id}}" {{if $smarty.post.$field_value=="hotlink_video_`$item.format_video_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_video_file_hotlink|replace:"%1%":$item.title}}</option>
							<option value="dimensions_video_{{$item.format_video_id}}" {{if $smarty.post.$field_value=="dimensions_video_`$item.format_video_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_video_file_dimensions|replace:"%1%":$item.title}}</option>
							<option value="duration_video_{{$item.format_video_id}}" {{if $smarty.post.$field_value=="duration_video_`$item.format_video_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_video_file_duration|replace:"%1%":$item.title}}</option>
							<option value="filesize_video_{{$item.format_video_id}}" {{if $smarty.post.$field_value=="filesize_video_`$item.format_video_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_video_file_filesize|replace:"%1%":$item.title}}</option>
						{{/foreach}}
						<option value="video_url" {{if $smarty.post.$field_value=='video_url'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_video_url}}</option>
						<option value="embed_code" {{if $smarty.post.$field_value=='embed_code'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_embed_code}}</option>
						<option value="gallery_url" {{if $smarty.post.$field_value=='gallery_url'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_gallery_url}}</option>
						<option value="pseudo_url" {{if $smarty.post.$field_value=='pseudo_url'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_pseudo_url}}</option>
						<option value="source_file" {{if $smarty.post.$field_value=='source_file'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_source_file_download_link}}</option>
						<option value="duration" {{if $smarty.post.$field_value=='duration'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_duration}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_custom}}">
						<option value="custom_1" {{if $smarty.post.$field_value=='custom_1'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_1_NAME}}</option>
						<option value="custom_2" {{if $smarty.post.$field_value=='custom_2'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_2_NAME}}</option>
						<option value="custom_3" {{if $smarty.post.$field_value=='custom_3'}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_3_NAME}}</option>
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_screenshots}}">
						<option value="screenshot_main_number" {{if $smarty.post.$field_value=='screenshot_main_number'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_screenshot_main_number}}</option>
						<option value="screenshot_main_source" {{if $smarty.post.$field_value=='screenshot_main_source'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_screenshot_main_source}}</option>
						{{foreach item=item from=$list_formats_screenshots_overview|smarty:nodefaults}}
							<option value="screenshot_main_{{$item.format_screenshot_id}}" {{if $smarty.post.$field_value=="screenshot_main_`$item.format_screenshot_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_screenshot_main_format|replace:"%1%":$item.title}}</option>
						{{/foreach}}
						<option value="overview_screenshots_sources" {{if $smarty.post.$field_value=='overview_screenshots_sources'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_screenshots_overview_sources}}</option>
						{{foreach item=item from=$list_formats_screenshots_overview|smarty:nodefaults}}
							<option value="overview_screenshots_{{$item.format_screenshot_id}}" {{if $smarty.post.$field_value=="overview_screenshots_`$item.format_screenshot_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_screenshots_overview_format|replace:"%1%":$item.title}}</option>
							{{if $item.is_create_zip=='1'}}
								<option value="overview_screenshots_zip_{{$item.format_screenshot_id}}" {{if $smarty.post.$field_value=="overview_screenshots_zip_`$item.format_screenshot_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_screenshots_overview_format_zip|replace:"%1%":$item.title}}</option>
							{{/if}}
						{{/foreach}}
					</optgroup>
					<optgroup label="{{$lang.videos.import_export_group_posters}}">
						<option value="poster_main_number" {{if $smarty.post.$field_value=='poster_main_number'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_poster_main_number}}</option>
						<option value="poster_main_source" {{if $smarty.post.$field_value=='poster_main_source'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_poster_main_source}}</option>
						{{foreach item=item from=$list_formats_screenshots_posters|smarty:nodefaults}}
							<option value="poster_main_{{$item.format_screenshot_id}}" {{if $smarty.post.$field_value=="poster_main_`$item.format_screenshot_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_poster_main_format|replace:"%1%":$item.title}}</option>
						{{/foreach}}
						<option value="posters_sources" {{if $smarty.post.$field_value=='posters_sources'}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_posters_sources}}</option>
						{{foreach item=item from=$list_formats_screenshots_posters|smarty:nodefaults}}
							<option value="posters_{{$item.format_screenshot_id}}" {{if $smarty.post.$field_value=="posters_`$item.format_screenshot_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_posters_format|replace:"%1%":$item.title}}</option>
							{{if $item.is_create_zip=='1'}}
								<option value="posters_zip_{{$item.format_screenshot_id}}" {{if $smarty.post.$field_value=="posters_zip_`$item.format_screenshot_id`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_posters_format_zip|replace:"%1%":$item.title}}</option>
							{{/if}}
						{{/foreach}}
					</optgroup>
					{{if count($list_languages)>0}}
						<optgroup label="{{$lang.videos.import_export_group_localization}}">
							{{foreach item=item from=$list_languages|smarty:nodefaults}}
								<option value="title_{{$item.code}}" {{if $smarty.post.$field_value=="title_`$item.code`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_title}} ({{$item.title}})</option>
								<option value="description_{{$item.code}}" {{if $smarty.post.$field_value=="description_`$item.code`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_description}} ({{$item.title}})</option>
								<option value="directory_{{$item.code}}" {{if $smarty.post.$field_value=="directory_`$item.code`"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_directory}} ({{$item.title}})</option>
							{{/foreach}}
						</optgroup>
					{{/if}}
				</select>
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
			<td class="de_label">{{$lang.videos.export_field_header_row}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_header_row" value="1" {{if $smarty.post.is_header_row==1}}checked="checked"{{/if}}/><label>{{$lang.videos.export_field_header_row_yes}}</label></div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_order}}:</td>
			<td class="de_control">
				<select name="order_by">
					<option value="post_date" {{if $smarty.post.order_by=="post_date"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_post_date}}</option>
					<option value="video_id" {{if $smarty.post.order_by=="video_id"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_id}}</option>
					<option value="title" {{if $smarty.post.order_by=="title"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_title}}</option>
					<option value="description" {{if $smarty.post.order_by=="description"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_description}}</option>
					<option value="content_source" {{if $smarty.post.order_by=="content_source"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_content_source}}</option>
					<option value="dvd" {{if $smarty.post.order_by=="dvd"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_dvd}}</option>
					<option value="duration" {{if $smarty.post.order_by=="duration"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_duration}}</option>
					<option value="rating" {{if $smarty.post.order_by=="rating"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_rating}}</option>
					<option value="video_viewed" {{if $smarty.post.order_by=="video_viewed"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_visits}}</option>
					<option value="user" {{if $smarty.post.order_by=="user"}}selected="selected"{{/if}}>{{$lang.videos.import_export_field_user}}</option>
					<option value="custom_1" {{if $smarty.post.order_by=="custom_1"}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_1_NAME}}</option>
					<option value="custom_2" {{if $smarty.post.order_by=="custom_2"}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_2_NAME}}</option>
					<option value="custom_3" {{if $smarty.post.order_by=="custom_3"}}selected="selected"{{/if}}>{{$options.VIDEO_FIELD_3_NAME}}</option>
					<option value="ctr" {{if $smarty.post.order_by=="ctr"}}selected="selected"{{/if}}>{{$lang.videos.export_field_order_ctr}}</option>
					<option value="rand" {{if $smarty.post.order_by=="rand"}}selected="selected"{{/if}}>{{$lang.videos.export_field_order_random}}</option>
				</select>
				<select name="order_direction">
					<option value="desc" {{if $smarty.post.order_direction=="desc"}}selected="selected"{{/if}}>{{$lang.common.order_desc}}</option>
					<option value="asc" {{if $smarty.post.order_direction=="asc"}}selected="selected"{{/if}}>{{$lang.common.order_asc}}</option>
				</select>
			</td>
		</tr>
		{{if count($list_languages)>0}}
			<tr>
				<td class="de_label">{{$lang.videos.export_field_language}}:</td>
				<td class="de_control">
					<select name="language">
						<option value="">{{$lang.videos.export_field_language_default}}</option>
						{{foreach name=data item=item from=$list_languages|smarty:nodefaults}}
							<option value="{{$item.code}}" {{if $smarty.post.language==$item.code}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
		{{/if}}
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
			<td class="de_label">{{$lang.videos.export_field_duration_format}}:</td>
			<td class="de_control">
				<select name="duration_format">
					<option value="number" {{if $smarty.post.duration_format=="number"}}selected="selected"{{/if}}>{{$lang.videos.export_field_duration_format_number}}</option>
					<option value="human" {{if $smarty.post.duration_format=="human"}}selected="selected"{{/if}}>{{$lang.videos.export_field_duration_format_human}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.export_divider_embed_code}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">{{$lang.videos.export_divider_embed_code_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.export_field_embed_code_width}}:</td>
			<td class="de_control">
				<input type="text" name="embed_width" class="fixed_200" value="{{$smarty.post.embed_width|default:"0"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.export_field_embed_code_width_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_embed_code_height}}:</td>
			<td class="de_control">
				<input type="text" name="embed_height" class="fixed_200" value="{{$smarty.post.embed_height|default:"0"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.export_field_embed_code_height_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_embed_code_skin}}:</td>
			<td class="de_control">
				<select name="embed_skin">
					<option value="default" {{if $smarty.post.embed_skin=="default"}}selected="selected"{{/if}}>{{$lang.videos.export_field_embed_code_skin_default}}</option>
					<option value="black" {{if $smarty.post.embed_skin=="black"}}selected="selected"{{/if}}>{{$lang.videos.export_field_embed_code_skin_dark}}</option>
					<option value="white" {{if $smarty.post.embed_skin=="white"}}selected="selected"{{/if}}>{{$lang.videos.export_field_embed_code_skin_light}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_embed_code_autoplay}}:</td>
			<td class="de_control">
				<select name="embed_autoplay">
					<option value="default" {{if $smarty.post.embed_autoplay=="default"}}selected="selected"{{/if}}>{{$lang.videos.export_field_embed_code_autoplay_default}}</option>
					<option value="true" {{if $smarty.post.embed_autoplay=="true"}}selected="selected"{{/if}}>{{$lang.videos.export_field_embed_code_autoplay_true}}</option>
					<option value="false" {{if $smarty.post.embed_autoplay=="false"}}selected="selected"{{/if}}>{{$lang.videos.export_field_embed_code_autoplay_false}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_embed_code_url_pattern}}:</td>
			<td class="de_control">
				<input type="text" name="embed_url_pattern" class="dyn_full_size" value="{{$smarty.post.embed_url_pattern}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.export_field_embed_code_url_pattern_hint|replace:"%1%":$config.project_url}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.videos.export_divider_filters}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_search_string}}:</td>
			<td class="de_control">
				<input type="text" name="se_text" class="fixed_200" value="{{$smarty.post.se_text}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.export_field_search_string_hint|replace:"%1%":$lang.videos.import_export_field_title|replace:"%2%":$lang.videos.import_export_field_description}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_status}}:</td>
			<td class="de_control">
				<select name="se_status_id">
					<option value="">{{$lang.videos.export_field_status_all}}</option>
					<option value="0" {{if $smarty.post.se_status_id=="0"}}selected="selected"{{/if}}>{{$lang.videos.export_field_status_disabled}}</option>
					<option value="1" {{if $smarty.post.se_status_id=="1"}}selected="selected"{{/if}}>{{$lang.videos.export_field_status_active}}</option>
					<option value="2" {{if $smarty.post.se_status_id=="2"}}selected="selected"{{/if}}>{{$lang.videos.export_field_status_error}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_review_flag}}:</td>
			<td class="de_control">
				<select name="se_review_flag">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					<option value="1" {{if $smarty.post.se_review_flag=="1"}}selected="selected"{{/if}}>{{$lang.videos.export_field_review_flag_yes}}</option>
					<option value="2" {{if $smarty.post.se_review_flag=="2"}}selected="selected"{{/if}}>{{$lang.videos.export_field_review_flag_no}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_admins}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_admins.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_admin_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_admins_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.admins|smarty:nodefaults}}
						<input type="hidden" name="se_admin_ids[]" value="{{$item.user_id}}" alt="{{$item.login}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_admin" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_admins_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_users}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_user_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_users_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.users|smarty:nodefaults}}
						<input type="hidden" name="se_user_ids[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_user" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_users_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_categories}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_category_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_categories_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="se_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_category" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_categories_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_models}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_model_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_models_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.models|smarty:nodefaults}}
						<input type="hidden" name="se_model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_model" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_models_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_tags}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="se_tags" value="{{$smarty.post.se_tags}}"/>
					<div class="controls">
						<input type="text" name="new_tag" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_tags_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_content_sources}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_content_sources.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_cs_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_content_sources_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.content_sources|smarty:nodefaults}}
						<input type="hidden" name="se_cs_ids[]" value="{{$item.content_source_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_cs" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_content_sources_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_dvds}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_dvds.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_dvd_ids[]</span>
						<span class="js_param">empty_message={{$lang.videos.export_field_dvds_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.dvds|smarty:nodefaults}}
						<input type="hidden" name="se_dvd_ids[]" value="{{$item.dvd_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_dvd" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.videos.export_field_dvds_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_loaded_as}}:</td>
			<td class="de_control">
				<select name="se_load_type_id">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					<option value="1" {{if $smarty.post.se_load_type_id=="1"}}selected="selected"{{/if}}>{{$lang.videos.export_field_loaded_as_file}}</option>
					<option value="2" {{if $smarty.post.se_load_type_id=="2"}}selected="selected"{{/if}}>{{$lang.videos.export_field_loaded_as_url}}</option>
					<option value="3" {{if $smarty.post.se_load_type_id=="3"}}selected="selected"{{/if}}>{{$lang.videos.export_field_loaded_as_embed}}</option>
					<option value="5" {{if $smarty.post.se_load_type_id=="5"}}selected="selected"{{/if}}>{{$lang.videos.export_field_loaded_as_pseudo}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_type}}:</td>
			<td class="de_control">
				<select name="se_is_private">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					<option value="0" {{if $smarty.post.se_is_private=="0"}}selected="selected"{{/if}}>{{$lang.videos.export_field_type_public}}</option>
					<option value="1" {{if $smarty.post.se_is_private=="1"}}selected="selected"{{/if}}>{{$lang.videos.export_field_type_private}}</option>
					<option value="2" {{if $smarty.post.se_is_private=="2"}}selected="selected"{{/if}}>{{$lang.videos.export_field_type_premium}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_admin_flag}}:</td>
			<td class="de_control">
				<select name="se_admin_flag_id">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					{{foreach item="item" from=$list_flags_admins|smarty:nodefaults}}
						<option value="{{$item.flag_id}}" {{if $item.flag_id==$smarty.post.se_admin_flag_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="pd_filter_off {{if $smarty.post.is_post_date_range_enabled==1}}hidden{{/if}}">{{$lang.videos.export_field_post_date_range}}:</div>
				<div class="de_required pd_filter_on {{if $smarty.post.is_post_date_range_enabled!=1}}hidden{{/if}}">{{$lang.videos.export_field_post_date_range}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pd_filter" name="is_post_date_range_enabled" value="1" {{if $smarty.post.is_post_date_range_enabled==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_date_range_enabled==1}}class="selected"{{/if}}>{{$lang.videos.export_field_post_date_range_enable}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{if $smarty.post.is_post_date_range_enabled==1}}
								{{$lang.videos.export_field_post_date_range_from}}: {{html_select_date prefix='post_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_from all_extra='class="pd_filter_on"'}}
								{{$lang.videos.export_field_post_date_range_to}}: {{html_select_date prefix='post_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_to all_extra='class="pd_filter_on"'}}
							{{else}}
								{{$lang.videos.export_field_post_date_range_from}}: {{html_select_date prefix='post_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_from all_extra='class="pd_filter_on" disabled="disabled"'}}
								{{$lang.videos.export_field_post_date_range_to}}: {{html_select_date prefix='post_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_to all_extra='class="pd_filter_on" disabled="disabled"'}}
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_post_time}}:</td>
			<td class="de_control"><div class="de_lv_pair"><input type="checkbox" name="is_post_time_considered" value="1" {{if $smarty.post.is_post_time_considered==1}}checked="checked"{{/if}} /><span {{if $smarty.post.is_post_time_considered==1}}class="selected"{{/if}}>{{$lang.videos.export_field_post_time_enabled}}</span></div></td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="id_filter_off {{if $smarty.post.is_id_range_enabled==1}}hidden{{/if}}">{{$lang.videos.export_field_id_range}}:</div>
				<div class="de_required id_filter_on {{if $smarty.post.is_id_range_enabled!=1}}hidden{{/if}}">{{$lang.videos.export_field_id_range}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="id_filter" name="is_id_range_enabled" value="1" {{if $smarty.post.is_id_range_enabled==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_id_range_enabled==1}}class="selected"{{/if}}>{{$lang.videos.export_field_id_range_enable}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{$lang.videos.export_field_id_range_from}}: <input type="text" name="id_range_from" class="fixed_100 id_filter_on" {{if $smarty.post.is_id_range_enabled!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.id_range_from}}"/>
							{{$lang.videos.export_field_id_range_to}}: <input type="text" name="id_range_to" class="fixed_100 id_filter_on" {{if $smarty.post.is_id_range_enabled!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.id_range_to}}"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="ad_filter_off {{if $smarty.post.is_added_date_range_enabled==1}}hidden{{/if}}">{{$lang.videos.export_field_added_date_range}}:</div>
				<div class="de_required ad_filter_on {{if $smarty.post.is_added_date_range_enabled!=1}}hidden{{/if}}">{{$lang.videos.export_field_added_date_range}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="ad_filter" name="is_added_date_range_enabled" value="1" {{if $smarty.post.is_added_date_range_enabled==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_added_date_range_enabled==1}}class="selected"{{/if}}>{{$lang.videos.export_field_added_date_range_enable}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{if $smarty.post.is_added_date_range_enabled==1}}
								{{$lang.videos.export_field_added_date_range_from}}: {{html_select_date prefix='added_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_from all_extra='class="ad_filter_on"'}}
								{{$lang.videos.export_field_added_date_range_to}}: {{html_select_date prefix='added_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_to all_extra='class="ad_filter_on"'}}
							{{else}}
								{{$lang.videos.export_field_added_date_range_from}}: {{html_select_date prefix='added_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_from all_extra='class="ad_filter_on" disabled="disabled"'}}
								{{$lang.videos.export_field_added_date_range_to}}: {{html_select_date prefix='added_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_to all_extra='class="ad_filter_on" disabled="disabled"'}}
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.export_field_limit}}:</td>
			<td class="de_control">
				<input type="text" name="limit" class="fixed_100" value="{{$smarty.post.limit}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" name="save_default" value="{{$lang.videos.export_btn_export}}"/></td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildExportImportFieldsLogic=call</span>
	<span class="js_param">buildExportImportPresetLogic=call</span>
</div>