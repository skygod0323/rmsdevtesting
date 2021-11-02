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
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="task_id" value="{{$smarty.request.task_id}}"/>
		{{if $smarty.get.action!='confirm' && $smarty.get.action!='complete'}}
			<input type="hidden" name="action" value="validate"/>
		{{elseif $smarty.get.action=='confirm'}}
			<input type="hidden" name="action" value="import"/>
		{{elseif $smarty.get.action=='complete'}}
			<input type="hidden" name="action" value="close"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2">
				<div>
					<a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / <a href="{{$page_name}}?plugin_id=upload_folder">{{$lang.plugins.upload_folder.title}}</a> /
					{{if $smarty.get.action=='confirm'}}
						{{$lang.plugins.upload_folder.divider_validation_results}}
					{{elseif $smarty.get.action=='complete'}}
						{{$lang.plugins.upload_folder.divider_import_results}}
					{{/if}}
					&nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]
				</div>
			</td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.upload_folder.long_desc}}
			</td>
		</tr>
		{{if $smarty.get.action!='confirm' && $smarty.get.action!='complete'}}
			{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/591-6-ways-to-add-videos-into-kvs">6 ways to add videos into KVS</a></span><br/>
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/577-different-ways-to-upload-video-files-into-kvs">Different ways to upload video files into KVS</a></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_folder_standard_videos}}:</td>
				<td class="de_control">
					<input type="text" name="folder_standard_videos" maxlength="400" class="dyn_full_size" value="{{$smarty.post.folder_standard_videos}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_folder_standard_videos_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_folder_premium_videos}}:</td>
				<td class="de_control">
					<input type="text" name="folder_premium_videos" maxlength="400" class="dyn_full_size" value="{{$smarty.post.folder_premium_videos}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_folder_premium_videos_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_label">{{$lang.plugins.upload_folder.field_folder_albums}}:</td>
					<td class="de_control">
						<input type="text" name="folder_albums" maxlength="400" class="dyn_full_size" value="{{$smarty.post.folder_albums}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_folder_albums_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_video_formats}}:</td>
				<td class="de_control">
					<select name="video_formats">
						<option value="2" {{if $smarty.post.video_formats==2}}selected="selected"{{/if}}>{{$lang.plugins.upload_folder.field_video_formats_ignore}}</option>
						<option value="1" {{if $smarty.post.video_formats==1}}selected="selected"{{/if}}>{{$lang.plugins.upload_folder.field_video_formats_analyze}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_video_formats_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_video_screenshots}}:</td>
				<td class="de_control">
					<select name="video_screenshots">
						<option value="1" {{if $smarty.post.video_screenshots==1}}selected="selected"{{/if}}>{{$lang.plugins.upload_folder.field_video_screenshots_overview}}</option>
						<option value="2" {{if $smarty.post.video_screenshots==2}}selected="selected"{{/if}}>{{$lang.plugins.upload_folder.field_video_screenshots_posters}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_video_screenshots_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_filenames_encoding}}:</td>
				<td class="de_control">
					<input type="text" name="charset" maxlength="400" class="dyn_full_size" value="{{$smarty.post.charset}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_filenames_encoding_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_delete_files}}:</td>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair"><input type="checkbox" name="delete_files" value="1" {{if $smarty.post.delete_files==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.upload_folder.field_delete_files_yes}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_delete_files_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_randomize}}:</td>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair"><input type="checkbox" name="randomize" value="1" {{if $smarty.post.randomize==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.upload_folder.field_randomize_yes}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.upload_folder.field_randomize_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_content_status}}:</td>
				<td class="de_control">
					<select name="content_status">
						<option value="0" {{if $smarty.post.content_status==0}}selected="selected"{{/if}}>{{$lang.plugins.upload_folder.field_content_status_disabled}}</option>
						<option value="1" {{if $smarty.post.content_status==1}}selected="selected"{{/if}}>{{$lang.plugins.upload_folder.field_content_status_active}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.plugins.upload_folder.btn_analyze}}"/>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='confirm'}}
			<tr>
				<td class="de_label">{{$lang.plugins.upload_folder.field_analyze_result}}:</td>
				<td class="de_control">
					{{$lang.plugins.upload_folder.field_analyze_result_found_objects|replace:"%1%":$smarty.post.found_objects}},
					{{$lang.plugins.upload_folder.field_analyze_result_existing_objects|replace:"%1%":$smarty.post.existing_objects}},
					{{$lang.plugins.upload_folder.field_analyze_result_errors|replace:"%1%":$smarty.post.errors}}
				</td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col width="1%"/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td class="eg_selector"><div><input type="checkbox" checked="checked"/> {{$lang.plugins.upload_folder.dg_contents_col_import}}</div></td>
							<td>{{$lang.plugins.upload_folder.dg_contents_col_object_type}}</td>
							<td>{{$lang.plugins.upload_folder.dg_contents_col_file_name}}</td>
							<td>{{$lang.plugins.upload_folder.dg_contents_col_file_usage}}</td>
						</tr>
						{{if count($smarty.post.content)>0}}
							{{foreach item=item key=key from=$smarty.post.content|smarty:nodefaults}}
								<tr class="eg_data fixed_height_30 {{if $key % 2==0}}eg_even{{/if}}">
									<td class="eg_selector" rowspan="{{$item.files|@count}}"><input type="checkbox" name="import_items[]" value="{{$item.external_key}}" {{if $item.has_error==1}}disabled="disabled"{{else}}checked="checked"{{/if}}/></td>
									<td rowspan="{{$item.files|@count}}" {{if $item.has_error==1}}class="highlighted_text"{{/if}}>
										{{if $item.type==1}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_std_video}}
										{{elseif $item.type==2}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_pre_video}}
										{{elseif $item.type==3}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_album}}
										{{/if}}
										{{if $item.error!=''}}({{$lang.plugins.upload_folder[$item.error]}}){{/if}}
										{{if $item.folder_title!=''}}
											<br/>/{{$item.folder_title}}
										{{/if}}
									</td>
									<td class="nowrap {{if $item.files[0].file_type==0}}de_grayed{{/if}} {{if count($item.files)>1}}eg_row_group_upper{{/if}}">{{$item.files[0].file_title}}</td>
									<td class="{{if $item.files[0].file_type==-1}}highlighted_text{{elseif $item.files[0].file_type==0}}de_grayed{{/if}} {{if count($item.files)>1}}eg_row_group_upper{{/if}}" {{if $item.files[0].error!=''}}{{assign var="error_key" value=$item.files[0].error}}title="{{$lang.plugins.upload_folder[$error_key]|replace:"%1%":$item.files[0].file_title}}"{{/if}}>
										{{if $item.files[0].file_type==-1}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_error|replace:"%1%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==0}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_ignored|replace:"%1%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==1}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_file|replace:"%1%":$item.files[0].file_duration|replace:"%2%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==2}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_format_file|replace:"%1%":$item.files[0].format_title|replace:"%2%":$item.files[0].file_duration|replace:"%3%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==3}}
											{{if $smarty.post.video_screenshots==2}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters_zip|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
											{{else}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots_zip|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
											{{/if}}
										{{elseif $item.files[0].file_type==4}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_main_screenshot|replace:"%1%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==5}}
											{{if $smarty.post.video_screenshots==2}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
											{{else}}
												{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
											{{/if}}
										{{elseif $item.files[0].file_type==6}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images_zip|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==7}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images|replace:"%1%":$item.files[0].file_count|replace:"%2%":$item.files[0].file_size}}
										{{elseif $item.files[0].file_type==8}}
											{{$lang.plugins.upload_folder.dg_contents_col_file_usage_description|replace:"%1%":$item.files[0].file_size}}
										{{/if}}
									</td>
								</tr>
								{{foreach item=item_file key=key_file from=$item.files|smarty:nodefaults}}
									{{if $key_file>0}}
										<tr class="eg_data fixed_height_30 {{if $key % 2==0}}eg_even{{/if}}">
											<td class="nowrap {{if $item_file.file_type==0}}de_grayed{{/if}} eg_row_group_lower {{if count($item.files)>1 && $key_file!=$item.files|@count-1}}eg_row_group_upper{{/if}}">{{$item_file.file_title}}</td>
											<td class="{{if $item_file.file_type==-1}}highlighted_text{{elseif $item_file.file_type==0}}de_grayed{{/if}} eg_row_group_lower {{if count($item.files)>1 && $key_file!=$item.files|@count-1}}eg_row_group_upper{{/if}}" {{if $item_file.error!=''}}title="{{$lang.plugins.upload_folder[$item_file.error]|replace:"%1%":$item_file.file_title}}"{{/if}}>
												{{if $item_file.file_type==-1}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_error|replace:"%1%":$item_file.file_size}}
												{{elseif $item_file.file_type==0}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_ignored|replace:"%1%":$item_file.file_size}}
												{{elseif $item_file.file_type==1}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_file|replace:"%1%":$item_file.file_duration|replace:"%2%":$item_file.file_size}}
												{{elseif $item_file.file_type==2}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_format_file|replace:"%1%":$item_file.format_title|replace:"%2%":$item_file.file_duration|replace:"%3%":$item_file.file_size}}
												{{elseif $item_file.file_type==3}}
													{{if $smarty.post.video_screenshots==2}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters_zip|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
													{{else}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots_zip|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
													{{/if}}
												{{elseif $item_file.file_type==4}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_main_screenshot|replace:"%1%":$item_file.file_size}}
												{{elseif $item_file.file_type==5}}
													{{if $smarty.post.video_screenshots==2}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_posters|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
													{{else}}
														{{$lang.plugins.upload_folder.dg_contents_col_file_usage_screenshots|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
													{{/if}}
												{{elseif $item_file.file_type==6}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images_zip|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
												{{elseif $item_file.file_type==7}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_source_images|replace:"%1%":$item_file.file_count|replace:"%2%":$item_file.file_size}}
												{{elseif $item_file.file_type==8}}
													{{$lang.plugins.upload_folder.dg_contents_col_file_usage_description|replace:"%1%":$item_file.file_size}}
												{{/if}}
											</td>
										</tr>
									{{/if}}
								{{/foreach}}
							{{/foreach}}
						{{/if}}
						{{foreach item=item key=key from=$smarty.post.duplicates|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td><input type="checkbox" disabled="disabled"/></td>
								<td class="de_grayed">
									{{if $item.type==1}}
										{{$lang.plugins.upload_folder.dg_contents_col_object_type_std_video}}
									{{elseif $item.type==2}}
										{{$lang.plugins.upload_folder.dg_contents_col_object_type_pre_video}}
									{{elseif $item.type==3}}
										{{$lang.plugins.upload_folder.dg_contents_col_object_type_album}}
									{{/if}}
								</td>
								<td class="de_grayed">{{$item.title}}</td>
								<td class="de_grayed">{{$lang.plugins.upload_folder.dg_contents_col_file_usage_duplicate|replace:"%1%":$item.duplicate_id}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="action_back" value="{{$lang.plugins.upload_folder.btn_back}}"/>
					<input type="submit" name="save_default" value="{{$lang.plugins.upload_folder.btn_import}}" {{if count($smarty.post.content)==0}}disabled="disabled"{{/if}}/>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='complete'}}
			<tr>
				<td class="de_table_control" colspan="2">
					{{if count($smarty.post.processed_content)>0}}
						<table class="de_edit_grid">
							<colgroup>
								<col/>
								<col/>
								<col/>
							</colgroup>
							<tr class="eg_header fixed_height_30">
								<td>{{$lang.plugins.upload_folder.dg_contents_col_object_type}}</td>
								<td>{{$lang.plugins.upload_folder.dg_contents_col_object_id}}</td>
								<td>{{$lang.plugins.upload_folder.dg_contents_col_title}}</td>
							</tr>
							{{foreach item=item key=key from=$smarty.post.processed_content|smarty:nodefaults}}
								<tr class="eg_data fixed_height_30 {{if $key % 2==0}}eg_even{{/if}}">
									<td>
										{{if $item.type==1}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_std_video}}
										{{elseif $item.type==2}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_pre_video}}
										{{elseif $item.type==3}}
											{{$lang.plugins.upload_folder.dg_contents_col_object_type_album}}
										{{/if}}
									</td>
									<td>{{$item.item_id}}</td>
									<td>{{$item.title}}</td>
								</tr>
							{{/foreach}}
						</table>
					{{else}}
						{{$lang.plugins.upload_folder.divider_import_results_none}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.plugins.upload_folder.btn_close}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>