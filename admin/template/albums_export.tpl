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
			<td class="de_header" colspan="2"><div><a href="albums.php">{{$lang.albums.submenu_option_albums_list}}</a> / {{$lang.albums.export_header_export}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.import_export_field_preset}}:</td>
			<td class="de_control">
				<select id="preset_id" name="preset_id" class="fixed_250">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach key=key item=item from=$list_presets|smarty:nodefaults}}
						<option value="{{$key}}" {{if $smarty.get.preset_id==$key}}selected="selected"{{/if}}>{{$key}}</option>
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
			<td class="de_label"></td>
			<td class="de_control">
				<input type="submit" id="delete_preset" name="delete_preset" value="{{$lang.albums.import_export_btn_delete_preset}}" {{if $smarty.get.preset_id==''}}disabled="disabled"{{/if}}/>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.albums.export_divider_fields}}</div></td>
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
				<select id="ei_field_{{$smarty.section.data.iteration}}" name="field{{$smarty.section.data.iteration}}">
					<option value="">{{$lang.common.select_default_option}}</option>
					<optgroup label="{{$lang.albums.import_export_group_general}}">
						<option value="album_id" {{if $smarty.post.$field_value=='album_id'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_id}}</option>
						<option value="title" {{if $smarty.post.$field_value=='title'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_title}}</option>
						<option value="description" {{if $smarty.post.$field_value=='description'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_description}}</option>
						<option value="directory" {{if $smarty.post.$field_value=='directory'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_directory}}</option>
						<option value="content_source" {{if $smarty.post.$field_value=='content_source'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_content_source}}</option>
						<option value="content_source/url" {{if $smarty.post.$field_value=='content_source/url'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_content_source_url}}</option>
						<option value="website_link" {{if $smarty.post.$field_value=='website_link'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_website_link}}</option>
						{{foreach item=item from=$list_satellites|smarty:nodefaults}}
							{{if is_array($item.website_ui_data)}}
								<option value="website_link/{{$item.multi_prefix}}" {{if $smarty.post.$field_value=="website_link/`$item.multi_prefix`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_website_link}} ({{$item.project_url}})</option>
							{{/if}}
						{{/foreach}}
						<option value="post_date" {{if $smarty.post.$field_value=='post_date'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_post_date}}</option>
						<option value="added_date" {{if $smarty.post.$field_value=='added_date'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_added_date}}</option>
						<option value="rating" {{if $smarty.post.$field_value=='rating'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_rating}}</option>
						<option value="rating_percent" {{if $smarty.post.$field_value=='rating_percent'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_rating_percent}}</option>
						<option value="rating_amount" {{if $smarty.post.$field_value=='rating_amount'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_rating_amount}}</option>
						<option value="album_viewed" {{if $smarty.post.$field_value=='album_viewed'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_visits}}</option>
						<option value="user" {{if $smarty.post.$field_value=='user'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_user}}</option>
						<option value="status" {{if $smarty.post.$field_value=='status'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_status}}</option>
						<option value="type" {{if $smarty.post.$field_value=='type'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_type}}</option>
						{{if $config.installation_type>=2}}
							<option value="tokens" {{if $smarty.post.$field_value=='tokens'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_tokens_cost}}</option>
						{{/if}}
						<option value="admin_flag" {{if $smarty.post.$field_value=='admin_flag'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_admin_flag}}</option>
					</optgroup>
					<optgroup label="{{$lang.albums.import_export_group_categorization}}">
						<option value="categories" {{if $smarty.post.$field_value=='categories'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_categories}}</option>
						<option value="models" {{if $smarty.post.$field_value=='models'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_models}}</option>
						<option value="tags" {{if $smarty.post.$field_value=='tags'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_tags}}</option>
					</optgroup>
					<optgroup label="{{$lang.albums.import_export_group_content}}">
						<option value="image_preview_source" {{if $smarty.post.$field_value=="image_preview_source"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_image_preview_source}}</option>
						{{foreach item=item from=$list_formats_images_preview|smarty:nodefaults}}
							<option value="image_preview_{{$item.format_album_id}}" {{if $smarty.post.$field_value=="image_preview_`$item.format_album_id`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_image_preview_format|replace:"%1%":$item.title}}</option>
						{{/foreach}}
						<option value="main_images_sources" {{if $smarty.post.$field_value=="main_images_sources"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_images_main_sources}}</option>
						{{foreach item=item from=$list_formats_images_main|smarty:nodefaults}}
							<option value="main_images_{{$item.format_album_id}}" {{if $smarty.post.$field_value=="main_images_`$item.format_album_id`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_images_main_format|replace:"%1%":$item.title}}</option>
							{{if $item.is_create_zip==1}}
								<option value="main_images_zip_{{$item.format_album_id}}" {{if $smarty.post.$field_value=="main_images_zip_`$item.format_album_id`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_images_main_format_zip|replace:"%1%":$item.title}}</option>
							{{/if}}
						{{/foreach}}
						<option value="gallery_url" {{if $smarty.post.$field_value=='gallery_url'}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_gallery_url}}</option>
					</optgroup>
					<optgroup label="{{$lang.albums.import_export_group_custom}}">
						<option value="custom_1" {{if $smarty.post.$field_value=='custom_1'}}selected="selected"{{/if}}>{{$options.ALBUM_FIELD_1_NAME}}</option>
						<option value="custom_2" {{if $smarty.post.$field_value=='custom_2'}}selected="selected"{{/if}}>{{$options.ALBUM_FIELD_2_NAME}}</option>
						<option value="custom_3" {{if $smarty.post.$field_value=='custom_3'}}selected="selected"{{/if}}>{{$options.ALBUM_FIELD_3_NAME}}</option>
					</optgroup>
					{{if count($list_languages)>0}}
						<optgroup label="{{$lang.albums.import_export_group_localization}}">
							{{foreach item=item from=$list_languages|smarty:nodefaults}}
								<option value="title_{{$item.code}}" {{if $smarty.post.$field_value=="title_`$item.code`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_title}} ({{$item.title}})</option>
								<option value="description_{{$item.code}}" {{if $smarty.post.$field_value=="description_`$item.code`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_description}} ({{$item.title}})</option>
								<option value="directory_{{$item.code}}" {{if $smarty.post.$field_value=="directory_`$item.code`"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_directory}} ({{$item.title}})</option>
							{{/foreach}}
						</optgroup>
					{{/if}}
				</select>
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
			<td class="de_label">{{$lang.albums.export_field_header_row}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_header_row" value="1" {{if $smarty.post.is_header_row==1}}checked="checked"{{/if}}/><label>{{$lang.albums.export_field_header_row_yes}}</label></div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_order}}:</td>
			<td class="de_control">
				<select name="order_by">
					<option value="post_date" {{if $smarty.post.order_by=="post_date"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_post_date}}</option>
					<option value="album_id" {{if $smarty.post.order_by=="album_id"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_id}}</option>
					<option value="title" {{if $smarty.post.order_by=="title"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_title}}</option>
					<option value="description" {{if $smarty.post.order_by=="description"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_description}}</option>
					<option value="content_source" {{if $smarty.post.order_by=="content_source"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_content_source}}</option>
					<option value="rating" {{if $smarty.post.order_by=="rating"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_rating}}</option>
					<option value="album_viewed" {{if $smarty.post.order_by=="album_viewed"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_visits}}</option>
					<option value="user" {{if $smarty.post.order_by=="user"}}selected="selected"{{/if}}>{{$lang.albums.import_export_field_user}}</option>
					<option value="custom_1" {{if $smarty.post.order_by=="custom_1"}}selected="selected"{{/if}}>{{$options.ALBUM_FIELD_1_NAME}}</option>
					<option value="custom_2" {{if $smarty.post.order_by=="custom_2"}}selected="selected"{{/if}}>{{$options.ALBUM_FIELD_2_NAME}}</option>
					<option value="custom_3" {{if $smarty.post.order_by=="custom_3"}}selected="selected"{{/if}}>{{$options.ALBUM_FIELD_3_NAME}}</option>
					<option value="rand" {{if $smarty.post.order_by=="rand"}}selected="selected"{{/if}}>{{$lang.albums.export_field_order_random}}</option>
				</select>
				<select name="order_direction">
					<option value="desc" {{if $smarty.post.order_direction=="desc"}}selected="selected"{{/if}}>{{$lang.common.order_desc}}</option>
					<option value="asc" {{if $smarty.post.order_direction=="asc"}}selected="selected"{{/if}}>{{$lang.common.order_asc}}</option>
				</select>
			</td>
		</tr>
		{{if count($list_languages)>0}}
			<tr>
				<td class="de_label">{{$lang.albums.export_field_language}}:</td>
				<td class="de_control">
					<select name="language">
						<option value="">{{$lang.albums.export_field_language_default}}</option>
						{{foreach name=data item=item from=$list_languages|smarty:nodefaults}}
							<option value="{{$item.code}}" {{if $smarty.post.language==$item.code}}selected="selected"{{/if}}>{{$item.title}}</option>
						{{/foreach}}
					</select>
				</td>
			</tr>
		{{/if}}
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
			<td class="de_separator" colspan="2"><div>{{$lang.albums.export_divider_filters}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_search_string}}:</td>
			<td class="de_control">
				<input type="text" name="se_text" class="fixed_200" value="{{$smarty.post.se_text}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.albums.export_field_search_string_hint|replace:"%1%":$lang.albums.import_export_field_title|replace:"%2%":$lang.albums.import_export_field_description}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_status}}:</td>
			<td class="de_control">
				<select name="se_status_id">
					<option value="">{{$lang.albums.export_field_status_all}}</option>
					<option value="0" {{if $smarty.post.se_status_id=="0"}}selected="selected"{{/if}}>{{$lang.albums.export_field_status_disabled}}</option>
					<option value="1" {{if $smarty.post.se_status_id=="1"}}selected="selected"{{/if}}>{{$lang.albums.export_field_status_active}}</option>
					<option value="2" {{if $smarty.post.se_status_id=="2"}}selected="selected"{{/if}}>{{$lang.albums.export_field_status_error}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_review_flag}}:</td>
			<td class="de_control">
				<select name="se_review_flag">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					<option value="1" {{if $smarty.post.se_review_flag=="1"}}selected="selected"{{/if}}>{{$lang.albums.export_field_review_flag_yes}}</option>
					<option value="2" {{if $smarty.post.se_review_flag=="2"}}selected="selected"{{/if}}>{{$lang.albums.export_field_review_flag_no}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_admins}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_admins.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_admin_ids[]</span>
						<span class="js_param">empty_message={{$lang.albums.export_field_admins_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.admins|smarty:nodefaults}}
						<input type="hidden" name="se_admin_ids[]" value="{{$item.user_id}}" alt="{{$item.login}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_admin" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.export_field_admins_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_users}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_user_ids[]</span>
						<span class="js_param">empty_message={{$lang.albums.export_field_users_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.users|smarty:nodefaults}}
						<input type="hidden" name="se_user_ids[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_user" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.export_field_users_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_categories}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_category_ids[]</span>
						<span class="js_param">empty_message={{$lang.albums.export_field_categories_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="se_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_category" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.export_field_categories_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_models}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_model_ids[]</span>
						<span class="js_param">empty_message={{$lang.albums.export_field_models_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.models|smarty:nodefaults}}
						<input type="hidden" name="se_model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_model" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.export_field_models_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_tags}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">submit_mode=simple</span>
						<span class="js_param">empty_message={{$lang.albums.export_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="se_tags" value="{{$smarty.post.se_tags}}"/>
					<div class="controls">
						<input type="text" name="new_tag" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.export_field_tags_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_content_sources}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_content_sources.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=se_cs_ids[]</span>
						<span class="js_param">empty_message={{$lang.albums.export_field_content_sources_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.content_sources|smarty:nodefaults}}
						<input type="hidden" name="se_cs_ids[]" value="{{$item.content_source_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_cs" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.albums.export_field_content_sources_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_type}}:</td>
			<td class="de_control">
				<select name="se_is_private">
					<option value="">{{$lang.common.dg_filter_option_all}}</option>
					<option value="0" {{if $smarty.post.se_is_private=="0"}}selected="selected"{{/if}}>{{$lang.albums.export_field_type_public}}</option>
					<option value="1" {{if $smarty.post.se_is_private=="1"}}selected="selected"{{/if}}>{{$lang.albums.export_field_type_private}}</option>
					<option value="2" {{if $smarty.post.se_is_private=="2"}}selected="selected"{{/if}}>{{$lang.albums.export_field_type_premium}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_admin_flag}}:</td>
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
				<div class="pd_filter_off {{if $smarty.post.is_post_date_range_enabled==1}}hidden{{/if}}">{{$lang.albums.export_field_post_date_range}}:</div>
				<div class="de_required pd_filter_on {{if $smarty.post.is_post_date_range_enabled!=1}}hidden{{/if}}">{{$lang.albums.export_field_post_date_range}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="pd_filter" name="is_post_date_range_enabled" value="1" {{if $smarty.post.is_post_date_range_enabled==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_post_date_range_enabled==1}}class="selected"{{/if}}>{{$lang.albums.export_field_post_date_range_enable}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{if $smarty.post.is_post_date_range_enabled==1}}
								{{$lang.albums.export_field_post_date_range_from}}: {{html_select_date prefix='post_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_from all_extra='class="pd_filter_on"'}}
								{{$lang.albums.export_field_post_date_range_to}}: {{html_select_date prefix='post_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_to all_extra='class="pd_filter_on"'}}
							{{else}}
								{{$lang.albums.export_field_post_date_range_from}}: {{html_select_date prefix='post_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_from all_extra='class="pd_filter_on" disabled="disabled"'}}
								{{$lang.albums.export_field_post_date_range_to}}: {{html_select_date prefix='post_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.post_date_to all_extra='class="pd_filter_on" disabled="disabled"'}}
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_post_time}}:</td>
			<td class="de_control"><div class="de_lv_pair"><input type="checkbox" name="is_post_time_considered" value="1" {{if $smarty.post.is_post_time_considered==1}}checked="checked"{{/if}} /><span {{if $smarty.post.is_post_time_considered==1}}class="selected"{{/if}}>{{$lang.albums.export_field_post_time_enabled}}</span></div></td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="id_filter_off {{if $smarty.post.is_id_range_enabled==1}}hidden{{/if}}">{{$lang.albums.export_field_id_range}}:</div>
				<div class="de_required id_filter_on {{if $smarty.post.is_id_range_enabled!=1}}hidden{{/if}}">{{$lang.albums.export_field_id_range}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="id_filter" name="is_id_range_enabled" value="1" {{if $smarty.post.is_id_range_enabled==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_id_range_enabled==1}}class="selected"{{/if}}>{{$lang.albums.export_field_id_range_enable}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{$lang.albums.export_field_id_range_from}}: <input type="text" name="id_range_from" class="fixed_100 id_filter_on" {{if $smarty.post.is_id_range_enabled!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.id_range_from}}"/>
							{{$lang.albums.export_field_id_range_to}}: <input type="text" name="id_range_to" class="fixed_100 id_filter_on" {{if $smarty.post.is_id_range_enabled!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.id_range_to}}"/>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="ad_filter_off {{if $smarty.post.is_added_date_range_enabled==1}}hidden{{/if}}">{{$lang.albums.export_field_added_date_range}}:</div>
				<div class="de_required ad_filter_on {{if $smarty.post.is_added_date_range_enabled!=1}}hidden{{/if}}">{{$lang.albums.export_field_added_date_range}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="ad_filter" name="is_added_date_range_enabled" value="1" {{if $smarty.post.is_added_date_range_enabled==1}}checked="checked"{{/if}}/><span {{if $smarty.post.is_added_date_range_enabled==1}}class="selected"{{/if}}>{{$lang.albums.export_field_added_date_range_enable}}</span></div></td>
					</tr>
					<tr>
						<td>
							{{if $smarty.post.is_added_date_range_enabled==1}}
								{{$lang.albums.export_field_added_date_range_from}}: {{html_select_date prefix='added_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_from all_extra='class="ad_filter_on"'}}
								{{$lang.albums.export_field_added_date_range_to}}: {{html_select_date prefix='added_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_to all_extra='class="ad_filter_on"'}}
							{{else}}
								{{$lang.albums.export_field_added_date_range_from}}: {{html_select_date prefix='added_date_from_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_from all_extra='class="ad_filter_on" disabled="disabled"'}}
								{{$lang.albums.export_field_added_date_range_to}}: {{html_select_date prefix='added_date_to_' start_year='+2' end_year='2000' reverse_years="1" field_order=DMY time=$smarty.post.added_date_to all_extra='class="ad_filter_on" disabled="disabled"'}}
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.albums.export_field_limit}}:</td>
			<td class="de_control">
				<input type="text" name="limit" class="fixed_100" value="{{$smarty.post.limit}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" name="save_default" value="{{$lang.albums.export_btn_export}}"/></td>
		</tr>
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildExportImportFieldsLogic=call</span>
	<span class="js_param">buildExportImportPresetLogic=call</span>
</div>