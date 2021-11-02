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
			<td class="de_header" colspan="2"><div><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / <a href="{{$page_name}}?action=back_import&amp;import_id={{$import_id}}">{{$lang.albums.import_header_import}}</a> / {{$lang.albums.import_header_preview}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_preview_total_items}}:</td>
			<td class="de_control">{{$import_stats.items}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_preview_total_empty_lines}}:</td>
			<td class="de_control">{{$import_stats.empty_lines}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_preview_total_errors}}:</td>
			<td class="de_control">{{$import_stats.errors}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_preview_total_warnings}}:</td>
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
							<td>{{$lang.albums.import_preview_col_row}}</td>
							<td>{{$lang.albums.import_preview_col_type}}</td>
							<td>{{$lang.albums.import_preview_col_message}}</td>
						</tr>
						{{assign var=row_num value=1}}
						{{foreach key=key item=item from=$import_result|smarty:nodefaults}}
						{{if is_array($item.errors)}}
							{{foreach item=item2 from=$item.errors|smarty:nodefaults}}
								<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
									<td>{{$key}}</td>
									<td class="highlighted_text">{{$lang.albums.import_preview_col_type_error}}</td>
									<td>{{$item2}}</td>
								</tr>
								{{assign var=row_num value=$row_num+1}}
							{{/foreach}}
							{{if is_array($item.warnings)}}
								{{foreach item=item2 from=$item.warnings|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
										<td>{{$key}}</td>
										<td class="warning_text">{{$lang.albums.import_preview_col_type_warning}}</td>
										<td>{{$item2}}</td>
									</tr>
									{{assign var=row_num value=$row_num+1}}
								{{/foreach}}
							{{/if}}
							{{if is_array($item.info)}}
								{{foreach item=item2 from=$item.info|smarty:nodefaults}}
									<tr class="eg_data {{if $row_num % 2==0}}eg_even{{/if}}">
										<td>{{$key}}</td>
										<td>{{$lang.albums.import_preview_col_type_info}}</td>
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
										<td class="warning_text">{{$lang.albums.import_preview_col_type_warning}}</td>
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
										<td>{{$lang.albums.import_preview_col_type_info}}</td>
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
				<input type="submit" name="back_import" value="{{$lang.albums.import_btn_back}}"/>
				{{if $import_stats.errors==0}}
					<input type="submit" name="save_default" value="{{$lang.albums.import_btn_confirmed}}"/>
				{{else}}
					<input type="submit" name="save_default" value="{{$lang.albums.import_btn_skip}}" {{if $import_stats.ok_lines==0}}disabled="disabled"{{/if}}/>
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
			<td class="de_header" colspan="2"><div><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.import_header_import}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_export_field_preset}}:</td>
			<td class="de_control">
				<select id="preset_id" name="preset_id" class="fixed_250">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach key=key item=item from=$list_presets|smarty:nodefaults}}
						<option value="{{$key}}" {{if $smarty.get.preset_id==$key || $smarty.post.preset_id==$key || $smarty.post.preset_name==$key}}selected="selected"{{/if}}>{{$key}}</option>
					{{/foreach}}
				</select>
				&nbsp;&nbsp;
				{{$lang.albums.import_export_field_preset_create}}:
				<input type="text" name="preset_name" maxlength="50" class="fixed_150"/>
				&nbsp;&nbsp;
				<div class="de_lv_pair"><input type="checkbox" name="is_default_preset" value="1" {{if $smarty.post.is_default_preset==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_export_field_preset_default}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_export_field_preset_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_export_field_preset_description}}:</td>
			<td class="de_control">
				<textarea name="preset_description" class="dyn_full_size" cols="40" rows="3">{{$smarty.post.preset_description}}</textarea>
			</td>
		</tr>
		<tr>
			<td class="de_label"></td>
			<td class="de_control">
				<input type="submit" id="delete_preset" name="delete_preset" value="{{$lang.albums.import_export_btn_delete_preset}}" {{if $smarty.get.preset_id==''}}disabled="disabled"{{/if}}/>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.import_divider_data}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.albums.import_field_data_text}} (*):</td>
			<td class="de_control">
				<textarea name="data" class="dyn_full_size html_code_editor" cols="40" rows="8">{{$smarty.post.data}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_data_text_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.albums.import_field_data_file}} (*):</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.albums.import_field_data_file}}</span>
					</div>
					<input type="text" name="file" class="fixed_500" value="{{$smarty.post.file}}" readonly="readonly"/>
					<input type="hidden" name="file_hash" value="{{$smarty.post.file_hash}}"/>
					<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
					<input type="button" class="de_fu_remove{{if $smarty.post.file_hash==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.albums.import_export_field_separator_fields}} (*):</td>
			<td class="de_control">
				<input type="text" name="separator" class="fixed_100" value="{{$smarty.post.separator|default:"\\t"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_export_field_separator_fields_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.albums.import_export_field_separator_lines}} (*):</td>
			<td class="de_control">
				<input type="text" name="line_separator" class="fixed_100" value="{{$smarty.post.line_separator|default:"\\r\\n"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_export_field_separator_lines_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.import_divider_fields}}</div></td>
		</tr>

		{{if $smarty.post.fields_amount>5}}
			{{assign var="loop_to" value=$smarty.post.fields_amount}}
		{{else}}
			{{assign var="loop_to" value=5}}
		{{/if}}

		{{section name=data start=0 step=1 loop=$loop_to}}
		<tr>
			<td class="de_label">{{$lang.albums.import_export_field|replace:"%1%":$smarty.section.data.iteration}}:</td>
			<td class="de_control">
				{{assign var="field_value" value="field`$smarty.section.data.iteration`"}}
				<select id="ei_field_{{$smarty.section.data.iteration}}" name="field{{$smarty.section.data.iteration}}" {{if $smarty.post.is_import_all==1}}disabled="disabled"{{/if}}>
					<option value="">{{$lang.common.select_default_option}}</option>
					<option value="skip" {{if $smarty.post.$field_value=='skip'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_skip}}</option>
					<optgroup label="{{$lang.albums.import_export_group_general}}">
						<option value="album_id" {{if $smarty.post.$field_value=='album_id'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_id_hint}}">{{$lang.albums.import_export_field_id}}</option>
						<option value="title" {{if $smarty.post.$field_value=='title'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_title_hint}}">{{$lang.albums.import_export_field_title}}</option>
						<option value="description" {{if $smarty.post.$field_value=='description'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_description_hint}}">{{$lang.albums.import_export_field_description}}</option>
						<option value="directory" {{if $smarty.post.$field_value=='directory'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_directory_hint}}">{{$lang.albums.import_export_field_directory}}</option>
						<option value="post_date" {{if $smarty.post.$field_value=='post_date'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_post_date_hint}}">{{$lang.albums.import_export_field_post_date}}</option>
						{{if $config.relative_post_dates=='true'}}
							<option value="relative_post_date" {{if $smarty.post.$field_value=='relative_post_date'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_post_date_relative_hint}}">{{$lang.albums.import_export_field_post_date_relative}}</option>
						{{/if}}
						<option value="rating" {{if $smarty.post.$field_value=='rating'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_rating_hint}}">{{$lang.albums.import_export_field_rating}}</option>
						<option value="rating_percent" {{if $smarty.post.$field_value=='rating_percent'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_rating_percent_hint}}">{{$lang.albums.import_export_field_rating_percent}}</option>
						<option value="rating_amount" {{if $smarty.post.$field_value=='rating_amount'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_rating_amount_hint}}">{{$lang.albums.import_export_field_rating_amount}}</option>
						<option value="album_viewed" {{if $smarty.post.$field_value=='album_viewed'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_visits_hint}}">{{$lang.albums.import_export_field_visits}}</option>
						<option value="user" {{if $smarty.post.$field_value=='user'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_user_hint}}">{{$lang.albums.import_export_field_user}}</option>
						<option value="status" {{if $smarty.post.$field_value=='status'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_status_hint}}">{{$lang.albums.import_export_field_status}}</option>
						<option value="type" {{if $smarty.post.$field_value=='type'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_type_hint}}">{{$lang.albums.import_export_field_type}}</option>
						<option value="access_level" {{if $smarty.post.$field_value=='access_level'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_access_level_hint}}">{{$lang.albums.import_export_field_access_level}}</option>
						{{if $config.installation_type>=2}}
							<option value="tokens" {{if $smarty.post.$field_value=='tokens'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_tokens_cost_hint}}">{{$lang.albums.import_export_field_tokens_cost}}</option>
						{{/if}}
						<option value="admin_flag" {{if $smarty.post.$field_value=='admin_flag'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_admin_flag_hint}}">{{$lang.albums.import_export_field_admin_flag}}</option>
					</optgroup>
					<optgroup label="{{$lang.albums.import_export_group_categorization}}">
						<option value="categories" {{if $smarty.post.$field_value=='categories'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_categories_hint}}">{{$lang.albums.import_export_field_categories}}</option>
						{{foreach item=item from=$list_categories_groups|smarty:nodefaults}}
							<option value="categoty_group_{{$item.category_group_id}}" {{if $smarty.post.$field_value=="categoty_group_`$item.category_group_id`"}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_categories_hint}}">{{$lang.albums.import_export_field_categories}} ({{$item.title}})</option>
						{{/foreach}}
						<option value="models" {{if $smarty.post.$field_value=='models'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_models_hint}}">{{$lang.albums.import_export_field_models}}</option>
						<option value="tags" {{if $smarty.post.$field_value=='tags'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_tags_hint}}">{{$lang.albums.import_export_field_tags}}</option>
						<option value="content_source" {{if $smarty.post.$field_value=='content_source'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_content_source_hint}}">{{$lang.albums.import_export_field_content_source}}</option>
						<option value="content_source/url" {{if $smarty.post.$field_value=='content_source/url'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_content_source_url_hint}}">{{$lang.albums.import_export_field_content_source_url}}</option>
						<option value="content_source/group" {{if $smarty.post.$field_value=='content_source/group'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_content_source_group_hint}}">{{$lang.albums.import_export_field_content_source_group}}</option>
					</optgroup>
					<optgroup label="{{$lang.albums.import_export_group_content}}">
						<option value="images_zip" {{if $smarty.post.$field_value=='images_zip'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_images_zip_hint}}">{{$lang.albums.import_export_field_images_zip}}</option>
						<option value="images_sources" {{if $smarty.post.$field_value=='images_sources'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_images_sources_hint}}">{{$lang.albums.import_export_field_images_sources}}</option>
						<option value="image_main_number" {{if $smarty.post.$field_value=='image_main_number'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_image_main_number_hint}}">{{$lang.albums.import_export_field_image_main_number}}</option>
						<option value="image_preview" {{if $smarty.post.$field_value=='image_preview'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_image_preview_source_hint}}">{{$lang.albums.import_export_field_image_preview_source}}</option>
						<option value="gallery_url" {{if $smarty.post.$field_value=='gallery_url'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_gallery_url_hint}}">{{$lang.albums.import_export_field_gallery_url}}</option>
						<option value="server_group" {{if $smarty.post.$field_value=='server_group'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_server_group_hint}}">{{$lang.albums.import_export_field_server_group}}</option>
					</optgroup>
					<optgroup label="{{$lang.albums.import_export_group_custom}}">
						<option value="custom_1" {{if $smarty.post.$field_value=='custom_1'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_custom_field_hint}}">{{$options.ALBUM_FIELD_1_NAME}}</option>
						<option value="custom_2" {{if $smarty.post.$field_value=='custom_2'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_custom_field_hint}}">{{$options.ALBUM_FIELD_2_NAME}}</option>
						<option value="custom_3" {{if $smarty.post.$field_value=='custom_3'}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_custom_field_hint}}">{{$options.ALBUM_FIELD_3_NAME}}</option>
					</optgroup>
					{{if count($list_languages)>0}}
						<optgroup label="{{$lang.albums.import_export_group_localization}}">
							{{foreach item=item from=$list_languages|smarty:nodefaults}}
								<option value="title_{{$item.code}}" {{if $smarty.post.$field_value=="title_`$item.code`"}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_title_hint}}">{{$lang.albums.import_export_field_title}} ({{$item.title}})</option>
								<option value="description_{{$item.code}}" {{if $smarty.post.$field_value=="description_`$item.code`"}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_description_hint}}">{{$lang.albums.import_export_field_description}} ({{$item.title}})</option>
								<option value="directory_{{$item.code}}" {{if $smarty.post.$field_value=="directory_`$item.code`"}}selected="selected"{{/if}} title="{{$lang.albums.import_export_field_directory_hint}}">{{$lang.albums.import_export_field_directory}} ({{$item.title}})</option>
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
			<td class="de_control">{{$lang.albums.import_export_field_more}}</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.import_export_divider_options}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_threads}}:</td>
			<td class="de_control">
				<select name="threads">
					{{section name="threads" start="1" loop="21"}}
						<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.threads}}selected="selected"{{/if}}>{{$smarty.section.threads.iteration}}</option>
					{{/section}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_threads_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_limit_title}}:</td>
			<td class="de_control">
				<input type="text" name="title_limit" value="{{$smarty.post.title_limit}}" maxlength="10" size="4"/>
				<select name="title_limit_type_id">
					<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.albums.import_field_limit_title_words}}</option>
					<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.albums.import_field_limit_title_characters}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_limit_title_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_limit_description}}:</td>
			<td class="de_control">
				<input type="text" name="description_limit" value="{{$smarty.post.description_limit}}" maxlength="10" size="4"/>
				<select name="description_limit_type_id">
					<option value="1" {{if $smarty.post.description_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.albums.import_field_limit_description_words}}</option>
					<option value="2" {{if $smarty.post.description_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.albums.import_field_limit_description_characters}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_limit_description_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_status_after_import}}:</td>
			<td class="de_control">
				<select name="status_after_import_id">
					<option value="0" {{if $smarty.post.status_after_import_id=="0"}}selected="selected"{{/if}}>{{$lang.albums.import_field_status_after_import_active}}</option>
					<option value="1" {{if $smarty.post.status_after_import_id=="1"}}selected="selected"{{/if}}>{{$lang.albums.import_field_status_after_import_disabled}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_album_type}}:</td>
			<td class="de_control">
				<select name="default_album_type">
					<option value="public" {{if $smarty.post.default_album_type=="public"}}selected="selected"{{/if}}>{{$lang.albums.import_field_album_type_public}}</option>
					<option value="private" {{if $smarty.post.default_album_type=="private"}}selected="selected"{{/if}}>{{$lang.albums.import_field_album_type_private}}</option>
					<option value="premium" {{if $smarty.post.default_album_type=="premium"}}selected="selected"{{/if}}>{{$lang.albums.import_field_album_type_premium}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_album_type_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_content_source}}:</td>
			<td class="de_control">
				<select name="content_source_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data_groups item=item_group from=$list_content_sources|smarty:nodefaults}}
						<optgroup label="{{$item_group[0].content_source_group_title|default:$lang.albums.import_field_content_source_no_group}}">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<option value="{{$item.content_source_id}}" {{if $smarty.post.content_source_id==$item.content_source_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</optgroup>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_content_source_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_content_source_categories}}:</td>
			<td class="de_control">
				<select name="content_source_categories_id">
					<option value="0" {{if $smarty.post.content_source_categories_id==0}}selected="selected"{{/if}}>{{$lang.albums.import_field_content_source_categories_no}}</option>
					<option value="1" {{if $smarty.post.content_source_categories_id==1}}selected="selected"{{/if}}>{{$lang.albums.import_field_content_source_categories_empty}}</option>
					<option value="2" {{if $smarty.post.content_source_categories_id==2}}selected="selected"{{/if}}>{{$lang.albums.import_field_content_source_categories_always}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_content_source_categories_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_model_categories}}:</td>
			<td class="de_control">
				<select name="model_categories_id">
					<option value="0" {{if $smarty.post.model_categories_id==0}}selected="selected"{{/if}}>{{$lang.albums.import_field_model_categories_no}}</option>
					<option value="1" {{if $smarty.post.model_categories_id==1}}selected="selected"{{/if}}>{{$lang.albums.import_field_model_categories_empty}}</option>
					<option value="2" {{if $smarty.post.model_categories_id==2}}selected="selected"{{/if}}>{{$lang.albums.import_field_model_categories_always}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.import_field_model_categories_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_users}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users_noid.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.albums.import_field_users_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="users" value="{{$smarty.post.users}}"/>
					<div class="controls">
						<input type="text" name="new_user" class="preserve_editing fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.import_field_users_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.albums.import_field_users_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_post_date}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_post_time_randomization" name="is_post_time_randomization" value="1" {{if $smarty.post.is_post_time_randomization==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_time_randomization==1 || $smarty.post.fields_amount<1}}class="selected"{{/if}}>{{$lang.albums.import_field_post_date_enable_time_random}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{$lang.albums.import_field_post_date_interval_from}}: <input type="text" name="post_time_randomization_from" maxlength="5" class="is_post_time_randomization_on" size="4" value="{{$smarty.post.post_time_randomization_from}}"/>&nbsp;
							{{$lang.albums.import_field_post_date_interval_to}}: <input type="text" name="post_time_randomization_to" maxlength="5" class="is_post_time_randomization_on" size="4" value="{{$smarty.post.post_time_randomization_to}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.albums.import_field_post_date_enable_time_random_hint}}</span>
							{{/if}}
						</td>
					</tr>
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pd_random" name="is_post_date_randomization" value="1" {{if $smarty.post.is_post_date_randomization==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_date_randomization==1}}class="selected"{{/if}}>{{$lang.albums.import_field_post_date_enable_date_random}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{if $config.relative_post_dates=='true'}}
								<div class="de_vis_sw_radio">
									<table class="control_group">
										<tr>
											<td>
												<div class="de_lv_pair"><input id="post_date_randomization_option_fixed" type="radio" name="post_date_randomization_option" class="pd_random_on" value="0" {{if $smarty.post.post_date_randomization_option!='1'}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_post_date_option_fixed}}</label></div>
												{{$lang.albums.import_field_post_date_interval_from}}: {{html_select_date prefix='post_date_randomization_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_from all_extra='class="pd_random_on post_date_randomization_option_fixed"'}}&nbsp;
												{{$lang.albums.import_field_post_date_interval_to}}: {{html_select_date prefix='post_date_randomization_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_to all_extra='class="pd_random_on post_date_randomization_option_fixed"'}}
												{{if $smarty.session.userdata.is_expert_mode==0}}
													<br/><span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random_hint}}</span>
												{{/if}}
											</td>
										</tr>
										<tr>
											<td>
												<div class="de_lv_pair"><input id="post_date_randomization_option_relative" type="radio" name="post_date_randomization_option" class="pd_random_on" value="1" {{if $smarty.post.post_date_randomization_option=='1'}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_post_date_option_relative}}</label></div>
												{{$lang.albums.import_field_post_date_interval_from}}: <input type="text" name="relative_post_date_randomization_from" class="fixed_100 pd_random_on post_date_randomization_option_relative" value="{{$smarty.post.relative_post_date_randomization_from}}" maxlength="5"/>&nbsp;&nbsp;
												{{$lang.albums.import_field_post_date_interval_to}}: <input type="text" name="relative_post_date_randomization_to" class="fixed_100 pd_random_on post_date_randomization_option_relative" value="{{$smarty.post.relative_post_date_randomization_to}}" maxlength="5"/>
												{{if $smarty.session.userdata.is_expert_mode==0}}
													<br/><span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random_hint2}}</span>
												{{/if}}
											</td>
										</tr>
									</table>
								</div>
							{{else}}
								{{$lang.albums.import_field_post_date_interval_from}}: {{html_select_date prefix='post_date_randomization_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_from all_extra='class="pd_random_on"'}}&nbsp;
								{{$lang.albums.import_field_post_date_interval_to}}: {{html_select_date prefix='post_date_randomization_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_randomization_to all_extra='class="pd_random_on"'}}
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random_hint}}</span>
								{{/if}}
							{{/if}}
						</td>
					</tr>
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pd_random_days" name="is_post_date_randomization_days" value="1" {{if $smarty.post.is_post_date_randomization_days==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_date_randomization_days==1 || $smarty.post.fields_amount<1}}class="selected"{{/if}}>{{$lang.albums.import_field_post_date_enable_date_random2}}</span></div></td>
					</tr>
					<tr>
						<td>
							<input type="text" name="post_date_randomization_days" class="pd_random_days_on fixed_100" value="{{$smarty.post.post_date_randomization_days}}"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.albums.import_field_post_date_enable_date_random2_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_duplicates}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_duplicates_skip_duplicate_titles}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_urls" value="1" {{if $smarty.post.is_skip_duplicate_urls==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_duplicates_skip_duplicate_urls}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_validation}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_validate_image_urls" value="1" {{if $smarty.post.is_validate_image_urls==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_validation_validate_image_urls}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_validate_grabber_urls" value="1" {{if $smarty.post.is_validate_grabber_urls==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_validation_validate_grabber_urls}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_new_objects}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_new_objects_categories}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_new_objects_models}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_new_objects_content_sources}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_field_options}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.is_review_needed==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_options_need_review}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="is_make_directories" value="1" {{if $smarty.post.is_make_directories==1 || $smarty.post.fields_amount<1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_options_directories_from_title}}</label></div></td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="is_use_rename_as_copy" value="1" {{if $smarty.post.is_use_rename_as_copy==1}}checked="checked"{{/if}}/><label>{{$lang.albums.import_field_options_use_rename_as_copy}}</label></div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.albums.import_field_options_use_rename_as_copy_hint}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" name="save_default" value="{{$lang.albums.import_btn_import}}"/></td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildExportImportFieldsLogic=call</span>
	<span class="js_param">buildExportImportPresetLogic=call</span>
	<span class="js_param">buildImportDateRandomizationLogic=call</span>
</div>

{{/if}}