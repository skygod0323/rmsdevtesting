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

<form action="{{$page_name}}" method="post">
	<div class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
		<div class="err_header">{{if is_array($smarty.post.errors)}}{{$lang.validation.common_header}}{{/if}}</div>
		<div class="err_content">
			{{if is_array($smarty.post.errors)}}
				<ul>
				{{foreach item="item" from=$smarty.post.errors|smarty:nodefaults}}
					<li>{{$item}}</li>
				{{/foreach}}
				</ul>
			{{/if}}
		</div>
	</div>
	<div>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		{{if $smarty.post.grabber_info.grabber_id!=''}}
			<input type="hidden" name="action" value="save_grabber"/>
			<input type="hidden" name="grabber_id" value="{{$smarty.post.grabber_info.grabber_id}}"/>
		{{elseif $smarty.get.action=='upload' || $smarty.get.action=='back_upload'}}
			<input type="hidden" name="action" value="mass_import"/>
		{{elseif $smarty.get.action=='upload_confirm'}}
			<input type="hidden" name="action" value="mass_import_confirm"/>
			<input type="hidden" name="task_id" value="{{$smarty.post.task_id}}"/>
		{{else}}
			<input type="hidden" name="action" value="manage_grabbers"/>
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
					<a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a>
					/
					{{if $smarty.post.grabber_info.grabber_id!=''}}
						<a href="{{$page_name}}?plugin_id=grabbers">{{$lang.plugins.grabbers.title}}</a>
						/
						{{$smarty.post.grabber_info.grabber_name}}
					{{elseif $smarty.get.action=='upload' || $smarty.get.action=='upload_confirm' || $smarty.get.action=='back_upload'}}
						<a href="{{$page_name}}?plugin_id=grabbers">{{$lang.plugins.grabbers.title}}</a>
						/
						{{$lang.plugins.grabbers.upload}}
					{{else}}
						{{$lang.plugins.grabbers.title}}
					{{/if}}
					&nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]
				</div>
			</td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.grabbers.long_desc}}
			</td>
		</tr>
		{{if $smarty.post.grabber_info.grabber_id!=''}}
			<tr>
				<td class="de_separator" colspan="2">
					<div>
						{{$lang.plugins.grabbers.divider_grabber_settings|replace:"%1%":$smarty.post.grabber_info.grabber_name}}
						/
						<a href="?plugin_id=grabbers&amp;action=log&amp;grabber_id={{$smarty.post.grabber_info.grabber_id}}" rel="external">{{$lang.plugins.grabbers.divider_grabber_log}}</a>
					</div>
				</td>
			</tr>
			{{if $smarty.post.grabber_info.is_default==1}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<tr>
						<td class="de_simple_text" colspan="2">
							<span class="de_hint">{{$lang.plugins.grabbers.divider_grabber_settings_default}}</span>
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/250-what-video-types-are-supported-in-kvs-tube-script-and-how-they-are-different">What video types are supported in KVS and how they are different</a></span><br/>
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/577-different-ways-to-upload-video-files-into-kvs">Different ways to upload video files into KVS</a></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label de_required">{{$lang.plugins.grabbers.field_mode}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="mode" name="mode">
							{{if $smarty.post.grabber_info.settings.mode==''}}
								<option value=""></option>
							{{/if}}
							{{foreach from=$smarty.post.grabber_info.supported_modes item="mode"}}
								{{assign var="mode_title_key" value="field_mode_`$mode`"}}
								<option value="{{$mode}}" {{if $smarty.post.grabber_info.settings.mode==$mode}}selected="selected"{{/if}}>{{$lang.plugins.grabbers[$mode_title_key]}}</option>
							{{/foreach}}
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_mode_hint|replace:"%1%":$lang.plugins.grabbers.field_mode_download|replace:"%2%":$lang.plugins.grabbers.field_mode_embed|replace:"%3%":$lang.plugins.grabbers.field_mode_pseudo}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $smarty.post.grabber_info.grabber_type=='videos'}}
				<tr class="mode_embed mode_pseudo">
					<td class="de_label">{{$lang.plugins.grabbers.field_url_postfix}}:</td>
					<td class="de_control">
						<input type="text" name="url_postfix" class="dyn_full_size" value="{{$smarty.post.grabber_info.settings.url_postfix}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_url_postfix_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_data}}:</td>
				<td class="de_control">
					<table class="control_group">
						{{foreach from=$smarty.post.grabber_info.supported_data item="data"}}
							<tr>
								{{assign var="data_title_key" value="field_data_`$data`"}}
								<td><div class="de_lv_pair"><input type="checkbox" name="data[]" value="{{$data}}" {{if in_array($data,$smarty.post.grabber_info.settings.data)}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers[$data_title_key]}}</label></div></td>
							</tr>
						{{/foreach}}
					</table>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.plugins.grabbers.field_data_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if in_array('categories',$smarty.post.grabber_info.supported_data)}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_import_categories_as_tags}}:</td>
					<td class="de_control">
						<div class="de_lv_pair"><input type="checkbox" name="is_import_categories_as_tags" value="1" {{if $smarty.post.grabber_info.settings.is_import_categories_as_tags==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_import_categories_as_tags_enabled}}</label></div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_import_categories_as_tags_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.grabber_info.grabber_type!='models'}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_content_source}}:</td>
					<td class="de_control">
						<select name="content_source_id">
							<option value="">{{$lang.common.select_default_option}}</option>
							{{foreach item="item_group" from=$smarty.post.content_sources|smarty:nodefaults}}
								<optgroup label="{{$item_group[0].content_source_group_title|default:$lang.plugins.grabbers.field_content_source_no_group}}">
									{{foreach item="item" from=$item_group|smarty:nodefaults}}
										<option value="{{$item.content_source_id}}" {{if $smarty.post.grabber_info.settings.content_source_id==$item.content_source_id}}selected="selected"{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								</optgroup>
							{{/foreach}}
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_content_source_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.grabber_info.grabber_type=='videos'}}
				<tr class="mode_download">
					<td class="de_label">{{$lang.plugins.grabbers.field_quality}}:</td>
					<td class="de_control">
						<div class="de_vis_sw_select">
							{{assign var="quality_vis_selector" value="quality_"}}
							<select id="quality" name="quality">
								<option value="">{{$lang.plugins.grabbers.field_quality_none}}</option>
								{{if count($smarty.post.grabber_info.supported_qualities)>1}}
									{{foreach item="item" from=$smarty.post.grabber_info.supported_qualities}}
										{{assign var="quality_vis_selector" value="`$quality_vis_selector` quality_`$item`"}}
										<option value="{{$item}}" {{if $smarty.post.grabber_info.settings.quality==$item}}selected="selected"{{/if}}>{{$item}}</option>
									{{/foreach}}
								{{/if}}
								{{if count($smarty.post.grabber_info.supported_video_formats)>0 && count($smarty.post.grabber_info.supported_qualities)>1}}
									<option value="*" {{if $smarty.post.grabber_info.settings.quality=='*'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_quality_multiple}}</option>
								{{/if}}
							</select>
							<span class="{{$quality_vis_selector}}">
								&nbsp;&nbsp;{{$lang.plugins.grabbers.field_quality_missing}}:&nbsp;
								<select name="quality_missing" class="{{$quality_vis_selector}}">
									<option value="error" {{if $smarty.post.grabber_info.settings.quality_missing=='error'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_quality_missing_error}}</option>
									<option value="lower" {{if $smarty.post.grabber_info.settings.quality_missing=='lower'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_quality_missing_lower}}</option>
									<option value="higher" {{if $smarty.post.grabber_info.settings.quality_missing=='higher'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_quality_missing_higher}}</option>
								</select>
								{{if count($smarty.post.grabber_info.supported_video_formats)>0}}
									&nbsp;&nbsp;{{$lang.plugins.grabbers.field_download_format}}:&nbsp;
									<select name="download_format" class="{{$quality_vis_selector}}">
										<option value="">{{$lang.plugins.grabbers.field_download_format_source}}</option>
										{{foreach item="item" from=$smarty.post.grabber_info.supported_video_formats}}
											<option value="{{$item.postfix}}" {{if $smarty.post.grabber_info.settings.download_format==$item.postfix}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_download_format_format|replace:"%1%":$item.title}}</option>
										{{/foreach}}
									</select>
								{{/if}}
							</span>
							{{if count($smarty.post.grabber_info.supported_video_formats)>0 && count($smarty.post.grabber_info.supported_qualities)>1}}
								<span class="quality_*">
									{{foreach item="item" from=$smarty.post.grabber_info.supported_qualities}}
										&nbsp;&nbsp;{{$item}}:&nbsp;
										<select name="download_format_{{$item}}" class="quality_*">
											<option value="">{{$lang.plugins.grabbers.field_download_format_skip}}</option>
											{{foreach item="item2" from=$smarty.post.grabber_info.supported_video_formats}}
												<option value="{{$item2.postfix}}" {{if $smarty.post.grabber_info.settings.download_formats_mapping[$item]==$item2.postfix}}selected="selected"{{/if}}>{{$item2.title}}</option>
											{{/foreach}}
										</select>
									{{/foreach}}
								</span>
							{{/if}}
						</div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<span class="de_hint">{{$lang.plugins.grabbers.field_quality_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.grabber_info.grabber_type!='models'}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_replacements}}:</td>
					<td class="de_control">
						<textarea name="replacements" rows="3" class="dyn_full_size">{{$smarty.post.grabber_info.settings.replacements}}</textarea>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_replacements_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_timeout}}:</td>
				<td class="de_control">
					<input type="text" name="timeout" size="4" value="{{$smarty.post.grabber_info.settings.timeout}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_timeout_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_proxies}}:</td>
				<td class="de_control">
					<textarea name="proxies" rows="3" class="dyn_full_size">{{$smarty.post.grabber_info.settings.proxies}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_proxies_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.grabber_info.is_ydl==1}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_account}}:</td>
					<td class="de_control">
						<input type="text" name="account" class="dyn_full_size" value="{{$smarty.post.grabber_info.settings.account}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_account_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.grabber_info.is_autodelete_supported==1}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_autodelete}}:</td>
					<td class="de_control">
						<div class="de_lv_pair"><input type="checkbox" name="is_autodelete" value="1" {{if $smarty.post.grabber_info.settings.is_autodelete=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_autodelete_enabled}}</label></div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_autodelete_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			{{if $smarty.post.grabber_info.grabber_type!='models'}}
				<tr>
					<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_filters}}</div></td>
				</tr>
				{{if $smarty.post.grabber_info.grabber_type=='videos'}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_quantity_filter_videos}}:</td>
						<td class="de_control">
							{{$lang.plugins.grabbers.field_quantity_filter_from}}
							<input type="text" name="filter_quantity_from" value="{{if $smarty.post.grabber_info.settings.filter_quantity_from!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_from}}{{/if}}" size="4"/>
							&nbsp;
							{{$lang.plugins.grabbers.field_quantity_filter_to}}
							<input type="text" name="filter_quantity_to" value="{{if $smarty.post.grabber_info.settings.filter_quantity_to!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_to}}{{/if}}" size="4"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.grabbers.field_quantity_filter_videos_hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{elseif $smarty.post.grabber_info.grabber_type=='albums'}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_quantity_filter_albums}}:</td>
						<td class="de_control">
							{{$lang.plugins.grabbers.field_quantity_filter_from}}
							<input type="text" name="filter_quantity_from" value="{{if $smarty.post.grabber_info.settings.filter_quantity_from!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_from}}{{/if}}" size="4"/>
							&nbsp;
							{{$lang.plugins.grabbers.field_quantity_filter_to}}
							<input type="text" name="filter_quantity_to" value="{{if $smarty.post.grabber_info.settings.filter_quantity_to!='0'}}{{$smarty.post.grabber_info.settings.filter_quantity_to}}{{/if}}" size="4"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.grabbers.field_quantity_filter_albums_hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{/if}}
				{{if in_array('rating',$smarty.post.grabber_info.supported_data)}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_rating_filter}}:</td>
						<td class="de_control">
							{{$lang.plugins.grabbers.field_rating_filter_from}}
							<input type="text" name="filter_rating_from" value="{{if $smarty.post.grabber_info.settings.filter_rating_from!='0'}}{{$smarty.post.grabber_info.settings.filter_rating_from}}{{/if}}" size="4"/>
							&nbsp;
							{{$lang.plugins.grabbers.field_rating_filter_to}}
							<input type="text" name="filter_rating_to" value="{{if $smarty.post.grabber_info.settings.filter_rating_to!='0'}}{{$smarty.post.grabber_info.settings.filter_rating_to}}{{/if}}" size="4"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.grabbers.field_rating_filter_hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{/if}}
				{{if in_array('views',$smarty.post.grabber_info.supported_data)}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_views_filter}}:</td>
						<td class="de_control">
							{{$lang.plugins.grabbers.field_views_filter_from}}
							<input type="text" name="filter_views_from" value="{{if $smarty.post.grabber_info.settings.filter_views_from!='0'}}{{$smarty.post.grabber_info.settings.filter_views_from}}{{/if}}" size="4"/>
							&nbsp;
							{{$lang.plugins.grabbers.field_views_filter_to}}
							<input type="text" name="filter_views_to" value="{{if $smarty.post.grabber_info.settings.filter_views_to!='0'}}{{$smarty.post.grabber_info.settings.filter_views_to}}{{/if}}" size="4"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.grabbers.field_views_filter_hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{/if}}
				{{if in_array('date',$smarty.post.grabber_info.supported_data)}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_date_filter}}:</td>
						<td class="de_control">
							{{$lang.plugins.grabbers.field_date_filter_from}}
							<input type="text" name="filter_date_from" value="{{if $smarty.post.grabber_info.settings.filter_date_from!='0'}}{{$smarty.post.grabber_info.settings.filter_date_from}}{{/if}}" size="4"/>
							&nbsp;
							{{$lang.plugins.grabbers.field_date_filter_to}}
							<input type="text" name="filter_date_to" value="{{if $smarty.post.grabber_info.settings.filter_date_to!='0'}}{{$smarty.post.grabber_info.settings.filter_date_to}}{{/if}}" size="4"/>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.grabbers.field_date_filter_hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_terminology_filter}}:</td>
					<td class="de_control">
						<input type="text" name="filter_terminology" value="{{$smarty.post.grabber_info.settings.filter_terminology}}" class="dyn_full_size"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_terminology_filter_hint}}</span>
						{{/if}}
					</td>
				</tr>
				{{if $smarty.post.grabber_info.grabber_type=='videos' && count($smarty.post.grabber_info.supported_qualities)>1}}
					<tr>
						<td class="de_label">{{$lang.plugins.grabbers.field_quality_from_filter}}:</td>
						<td class="de_control">
							<select name="filter_quality_from">
								<option value="">{{$lang.common.select_default_option}}</option>
								{{foreach item="item" from=$smarty.post.grabber_info.supported_qualities}}
									<option value="{{$item}}" {{if $smarty.post.grabber_info.settings.filter_quality_from==$item}}selected="selected"{{/if}}>{{$item}}</option>
								{{/foreach}}
							</select>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.grabbers.field_quality_from_filter_hint}}</span>
							{{/if}}
						</td>
					</tr>
				{{/if}}
			{{/if}}
			{{if $smarty.post.grabber_info.is_autopilot_supported==1}}
				<tr>
					<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_autopilot}}</div></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_autopilot}}:</td>
					<td class="de_control">
						<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_autopilot" name="is_autopilot" value="1" {{if $smarty.post.grabber_info.settings.is_autopilot=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_autopilot_enabled}}</label></div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_autopilot_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label de_required">{{$lang.plugins.grabbers.field_autopilot_interval}} (*):</td>
					<td class="de_control">
						<input type="text" name="autopilot_interval" value="{{if $smarty.post.grabber_info.settings.autopilot_interval>0}}{{$smarty.post.grabber_info.settings.autopilot_interval}}{{/if}}" maxlength="2" size="4"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_autopilot_interval_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label">{{$lang.plugins.grabbers.field_threads}}:</td>
					<td class="de_control">
						<select name="threads">
							{{section name="threads" start="1" loop="21"}}
								<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.grabber_info.settings.threads}}selected="selected"{{/if}}>{{$smarty.section.threads.iteration}}</option>
							{{/section}}
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_threads_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label">{{$lang.plugins.grabbers.field_limit_title}}:</td>
					<td class="de_control">
						<input type="text" name="title_limit" value="{{if $smarty.post.grabber_info.settings.title_limit>0}}{{$smarty.post.grabber_info.settings.title_limit}}{{/if}}" maxlength="10" size="4"/>
						<select name="title_limit_type_id">
							<option value="1" {{if $smarty.post.grabber_info.settings.title_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_title_words}}</option>
							<option value="2" {{if $smarty.post.grabber_info.settings.title_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_title_characters}}</option>
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_limit_title_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label">{{$lang.plugins.grabbers.field_limit_description}}:</td>
					<td class="de_control">
						<input type="text" name="description_limit" value="{{if $smarty.post.grabber_info.settings.description_limit>0}}{{$smarty.post.grabber_info.settings.description_limit}}{{/if}}" maxlength="10" size="4"/>
						<select name="description_limit_type_id">
							<option value="1" {{if $smarty.post.grabber_info.settings.description_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_description_words}}</option>
							<option value="2" {{if $smarty.post.grabber_info.settings.description_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_description_characters}}</option>
						</select>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_limit_description_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label">{{$lang.plugins.grabbers.field_status_after_import}}:</td>
					<td class="de_control">
						<select name="status_after_import_id">
							<option value="0" {{if $smarty.post.grabber_info.settings.status_after_import_id=='0'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_active}}</option>
							<option value="1" {{if $smarty.post.grabber_info.settings.status_after_import_id=='1'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_disabled}}</option>
						</select>
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label">{{$lang.plugins.grabbers.field_options_categorization}}:</td>
					<td class="de_control">
						<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_categories=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_categories}}</label></div>
						<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_models=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_models}}</label></div>
						<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_content_sources=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_cs}}</label></div>
						{{if $smarty.post.grabber_info.grabber_type=='videos'}}
							<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_channels" value="1" {{if $smarty.post.grabber_info.settings.is_skip_new_channels=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_channels}}</label></div>
						{{/if}}
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_categorization_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label">{{$lang.plugins.grabbers.field_options_other}}:</td>
					<td class="de_control">
						<table class="control_group">
							<tr>
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.grabber_info.settings.is_skip_duplicate_titles=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_duplicates}}</label></div>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_other_duplicates_hint}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.grabber_info.settings.is_review_needed==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_need_review}}</label></div>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_other_need_review_hint}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input type="checkbox" name="is_randomize_time" value="1" {{if $smarty.post.grabber_info.settings.is_randomize_time==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_randomize_time}}</label></div>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_other_randomize_time_hint}}</span>
									{{/if}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="is_autopilot_on">
					<td class="de_label de_required">{{$lang.plugins.grabbers.field_upload_list}} (*):</td>
					<td class="de_control">
						<textarea name="upload_list" rows="5" cols="30" class="dyn_full_size">{{$smarty.post.grabber_info.settings.upload_list}}</textarea>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.grabbers.field_upload_list_hint_autopilot}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.grabbers.field_last_exec}}:</td>
					<td class="de_control">
						{{if $smarty.post.grabber_info.settings.autopilot_last_exec_time==0}}
							{{$lang.plugins.grabbers.field_last_exec_none}}
						{{else}}
							{{$smarty.post.grabber_info.settings.autopilot_last_exec_time|date_format:$smarty.session.userdata.full_date_format}}
							{{$lang.plugins.grabbers.field_last_exec_info|replace:"%1%":$smarty.post.grabber_info.settings.autopilot_last_exec_duration|replace:"%2%":$smarty.post.grabber_info.settings.autopilot_last_exec_added|replace:"%3%":$smarty.post.grabber_info.settings.autopilot_last_exec_duplicates}}
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.plugins.grabbers.btn_save}}"/>
				</td>
			</tr>
		{{elseif $smarty.get.action=='upload' || $smarty.get.action=='back_upload'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_upload}}</div></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.grabbers.field_upload_type}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="upload_type" name="upload_type">
							<option value="videos" {{if $smarty.post.upload_type=='videos'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_upload_type_videos}}</option>
							{{if $config.installation_type>=4}}
								<option value="albums" {{if $smarty.post.upload_type=='albums'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_upload_type_albums}}</option>
							{{/if}}
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.plugins.grabbers.field_upload_list}} (*):</td>
				<td class="de_control">
					<textarea name="upload_list" rows="5" cols="30" class="dyn_full_size">{{$smarty.post.upload_list}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_upload_list_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_upload_options}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_threads}}:</td>
				<td class="de_control">
					<select name="threads">
						{{section name="threads" start="1" loop="21"}}
							<option value="{{$smarty.section.threads.iteration}}" {{if $smarty.section.threads.iteration==$smarty.post.threads}}selected="selected"{{/if}}>{{$smarty.section.threads.iteration}}</option>
						{{/section}}
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_threads_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_limit_title}}:</td>
				<td class="de_control">
					<input type="text" name="title_limit" value="{{if $smarty.post.title_limit>0}}{{$smarty.post.title_limit}}{{/if}}" maxlength="10" size="4"/>
					<select name="title_limit_type_id">
						<option value="1" {{if $smarty.post.title_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_title_words}}</option>
						<option value="2" {{if $smarty.post.title_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_title_characters}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_limit_title_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_limit_description}}:</td>
				<td class="de_control">
					<input type="text" name="description_limit" value="{{if $smarty.post.description_limit>0}}{{$smarty.post.description_limit}}{{/if}}" maxlength="10" size="4"/>
					<select name="description_limit_type_id">
						<option value="1" {{if $smarty.post.description_limit_type_id=="1"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_description_words}}</option>
						<option value="2" {{if $smarty.post.description_limit_type_id=="2"}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_limit_description_characters}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_limit_description_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_status_after_import}}:</td>
				<td class="de_control">
					<select name="status_after_import_id">
						<option value="0" {{if $smarty.post.status_after_import_id=='0'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_active}}</option>
						<option value="1" {{if $smarty.post.status_after_import_id=='1'}}selected="selected"{{/if}}>{{$lang.plugins.grabbers.field_status_after_import_disabled}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_options_categorization}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_categories" value="1" {{if $smarty.post.is_skip_new_categories=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_categories}}</label></div>
					<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_models" value="1" {{if $smarty.post.is_skip_new_models=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_models}}</label></div>
					<div class="de_lv_pair"><input type="checkbox" name="is_skip_new_content_sources" value="1" {{if $smarty.post.is_skip_new_content_sources=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_cs}}</label></div>
					<div class="de_lv_pair upload_type_videos"><input type="checkbox" name="is_skip_new_channels" value="1" {{if $smarty.post.is_skip_new_channels=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_categorization_channels}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_categorization_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_options_other}}:</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_skip_duplicate_titles" value="1" {{if $smarty.post.is_skip_duplicate_titles=='1'}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_duplicates}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_other_duplicates_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_review_needed" value="1" {{if $smarty.post.is_review_needed==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_need_review}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_other_need_review_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input type="checkbox" name="is_randomize_time" value="1" {{if $smarty.post.is_randomize_time==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.grabbers.field_options_other_randomize_time}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.plugins.grabbers.field_options_other_randomize_time_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.plugins.grabbers.btn_upload}}"/>
				</td>
			</tr>
		{{elseif $smarty.get.action=='upload_confirm'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_upload_confirm}}</div></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.plugins.grabbers.field_name}}</td>
							<td>{{$lang.plugins.grabbers.field_mode}}</td>
							<td>
								{{if $smarty.post.upload_type=='videos'}}
									{{$lang.plugins.grabbers.field_videos_amount}}
								{{elseif $smarty.post.upload_type=='albums'}}
									{{$lang.plugins.grabbers.field_albums_amount}}
								{{/if}}
							</td>
						</tr>
						{{assign var="total_download" value="0"}}
						{{assign var="total_embed" value="0"}}
						{{assign var="total_pseudo" value="0"}}
						{{foreach item="item" key="key" from=$smarty.post.grabbers_usage|smarty:nodefaults}}
							{{assign var="expander_id" value="expander_`$key`"}}
							<tr class="eg_data fixed_height_30">
								<td>
									{{if $item.type=='valid'}}
										{{$item.name}}
										{{assign var="total_inc" value=$item.urls|@count}}
										{{if $item.mode=='download'}}
											{{assign var="total_download" value=$total_download+$total_inc}}
										{{elseif $item.mode=='embed'}}
											{{assign var="total_embed" value=$total_embed+$total_inc}}
										{{elseif $item.mode=='pseudo'}}
											{{assign var="total_pseudo" value=$total_pseudo+$total_inc}}
										{{/if}}
									{{elseif $item.type=='missing'}}
										{{$lang.plugins.grabbers.field_name_missing_grabber}}
									{{elseif $item.type=='error'}}
										{{$lang.plugins.grabbers.field_name_error_grabber}}
									{{elseif $item.type=='duplicates'}}
										{{$lang.plugins.grabbers.field_name_duplicates}}
									{{/if}}
								</td>
								<td>
									{{if $item.mode=='' || $item.name==''}}
										{{$lang.plugins.grabbers.field_mode_skip}}
									{{else}}
										{{assign var="mode_title_key" value="field_mode_`$item.mode`"}}
										{{$lang.plugins.grabbers[$mode_title_key]}}
									{{/if}}
								</td>
								<td>
									<a id="{{$expander_id}}" class="de_expand" href="javascript:stub()">{{$item.urls|@count}}</a>
								</td>
							</tr>
							{{if count($item.urls)>0}}
								<tr class="eg_data fixed_height_30 {{$expander_id}} hidden">
									<td colspan="3" class="eg_padding">
										{{foreach item="url" from=$item.urls|smarty:nodefaults}}
											{{$url}}<br/>
										{{/foreach}}
									</td>
								</tr>
							{{/if}}
							{{if count($item.errors)>0}}
								<tr class="eg_data fixed_height_30 {{$expander_id}} hidden">
									<td colspan="3" class="eg_padding">
										{{foreach item="errors" from=$item.errors|smarty:nodefaults}}
											{{$errors}}<br/>
										{{/foreach}}
									</td>
								</tr>
							{{/if}}
						{{/foreach}}
						<tr class="eg_header">
							<td rowspan="3">{{$lang.plugins.grabbers.field_total}}</td>
							<td>{{$lang.plugins.grabbers.field_mode_download}}</td>
							<td>
								{{$total_download}}
							</td>
						</tr>
						<tr class="eg_header">
							<td>{{$lang.plugins.grabbers.field_mode_embed}}</td>
							<td>
								{{$total_embed}}
							</td>
						</tr>
						<tr class="eg_header">
							<td>{{$lang.plugins.grabbers.field_mode_pseudo}}</td>
							<td>
								{{$total_pseudo}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="back_mass_import" value="{{$lang.plugins.grabbers.btn_back}}"/>
					<input type="submit" name="save_default" value="{{$lang.plugins.grabbers.btn_confirm}}"/>
				</td>
			</tr>
		{{else}}
			{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
				<tr {{if $smarty.get.action=='change' && $smarty.session.save.options.video_edit_display_mode=='descwriter'}}class="hidden"{{/if}}>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/591-6-ways-to-add-videos-into-kvs">6 ways to add videos into KVS</a></span><br/>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_upload}}</div></td>
			</tr>
			<tr>
				<td class="de_label" colspan="2">
					<a href="plugins.php?plugin_id=grabbers&amp;action=upload">{{$lang.plugins.grabbers.field_upload}}</a>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_upload_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_grabbers}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_ydl_binary}}:</td>
				<td class="de_control">
					<input type="text" name="ydl_binary" value="{{$smarty.post.ydl_binary}}" size="50"/>
					&nbsp;&nbsp;{{$lang.plugins.grabbers.field_version}}: {{$smarty.post.ydl_version|default:$lang.common.undefined}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_ydl_binary_hint}}</span>
					{{/if}}
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
							<col/>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td class="eg_selector"><div><input type="checkbox"/> {{$lang.plugins.grabbers.field_delete}}</div></td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_name}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_version}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_mode}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_data}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_filters}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_quality}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_autopilot}}</td>
							<td class="nowrap">{{$lang.plugins.grabbers.field_autodelete}}</td>
						</tr>
						{{foreach item="grabbers" key="grabber_type" from=$smarty.post.grabbers|smarty:nodefaults}}
							<tr class="eg_header">
								{{assign var="grabber_type_title_key" value="divider_grabbers_`$grabber_type`"}}
								<td colspan="9">{{$lang.plugins.grabbers.$grabber_type_title_key}}</td>
							</tr>
							{{if count($grabbers)>0}}
								{{foreach item="item" from=$grabbers|smarty:nodefaults}}
									<tr class="eg_data fixed_height_30">
										<td class="eg_selector"><input type="checkbox" name="delete[]" value="{{$item.grabber_id}}"/></td>
										<td><a href="?plugin_id=grabbers&amp;grabber_id={{$item.grabber_id}}" class="nowrap {{if $item.settings.mode=='' || $item.settings.is_broken=='1' || ($item.is_ydl==1 && $smarty.post.ydl_version=='')}}highlighted_text{{/if}}">{{if $item.settings.is_autopilot==1}}<b>{{$item.grabber_name}}</b>{{else}}{{$item.grabber_name}}{{/if}}</a></td>
										<td>
											{{$item.grabber_version}}
										</td>
										<td {{if $item.settings.mode==''}}class="highlighted_text"{{/if}}>
											{{if $item.settings.mode==''}}
												{{$lang.plugins.grabbers.field_mode_none}}
											{{else}}
												{{assign var="mode_title_key" value="field_mode_`$item.settings.mode`"}}
												{{$lang.plugins.grabbers[$mode_title_key]}}
											{{/if}}
										</td>
										<td>
											{{if count($item.settings.data)>0}}
												{{foreach name="data" item="item_data" from=$item.settings.data|smarty:nodefaults}}
													{{assign var="data_title_key" value="field_data_`$item_data`"}}
													{{$lang.plugins.grabbers[$data_title_key]}}{{if !$smarty.foreach.data.last}},{{/if}}
												{{/foreach}}
											{{else}}
												{{$lang.plugins.grabbers.field_data_none}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{assign var="needs_break" value="false"}}
											{{if $item.settings.filter_quantity_from>0 && $item.settings.filter_quantity_to>0}}
												{{$item.settings.filter_quantity_from}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}} - {{$item.settings.filter_quantity_to}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}}
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_quantity_from>0}}
												{{$item.settings.filter_quantity_from}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}}+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_quantity_to>0}}
												{{$item.settings.filter_quantity_to}}{{if $grabber_type=='videos'}}s{{else}}i{{/if}}-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_rating_from>0 && $item.settings.filter_rating_to>0}}
												{{$item.settings.filter_rating_from}}% - {{$item.settings.filter_rating_to}}%
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_rating_from>0}}
												{{$item.settings.filter_rating_from}}%+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_rating_to>0}}
												{{$item.settings.filter_rating_to}}%-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_views_from>0 && $item.settings.filter_views_to>0}}
												{{$item.settings.filter_views_from}} - {{$item.settings.filter_views_to}}
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_views_from>0}}
												{{$item.settings.filter_views_from}}+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_views_to>0}}
												{{$item.settings.filter_views_to}}-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_date_from>0 && $item.settings.filter_date_to>0}}
												{{$item.settings.filter_date_from}}d - {{$item.settings.filter_date_to}}d
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_date_from>0}}
												{{$item.settings.filter_date_from}}d+
												{{assign var="needs_break" value="true"}}
											{{elseif $item.settings.filter_date_to>0}}
												{{$item.settings.filter_date_to}}d-
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
											{{if $item.settings.filter_quality_from}}
												{{$item.settings.filter_quality_from}}+
												{{assign var="needs_break" value="true"}}
											{{/if}}
											{{if $needs_break=='true'}}
												<br/>
												{{assign var="needs_break" value="false"}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{if $item.settings.mode=='download' && $item.settings.quality!=''}}
												{{if $item.settings.quality=='*'}}
													{{foreach name="quality" item="item_quality" key="key_quality" from=$item.settings.download_formats_mapping}}
														{{$key_quality}}{{if !$smarty.foreach.quality.last}},{{/if}}
													{{/foreach}}
												{{else}}
													{{$item.settings.quality}}
													{{if $item.settings.quality_missing=='lower'}}-{{elseif $item.settings.quality_missing=='higher'}}+{{/if}}
												{{/if}}
											{{else}}
												{{$lang.plugins.grabbers.field_quality_none}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{if $item.settings.is_autopilot=='1'}}
												{{$item.settings.autopilot_interval}}{{$lang.common.hour_truncated}}
											{{/if}}
										</td>
										<td class="nowrap">
											{{if $item.settings.is_autodelete=='1'}}
												{{$lang.common.yes}}
											{{/if}}
										</td>
									</tr>
								{{/foreach}}
							{{else}}
								<tr class="eg_data fixed_height_30">
									<td colspan="9">
										{{$lang.plugins.grabbers.divider_grabbers_none}}
									</td>
								</tr>
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.grabbers.divider_install}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
				<tr>
					<td class="de_simple_text" colspan="2">
						<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/302-how-to-create-custom-tube-video-grabber-for-kvs">How to create custom tube video grabber for KVS</a></span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_kvs_repository}}:</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url={{$page_name}}?plugin_id=grabbers&amp;action=grabbers_list</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=grabber_ids[]</span>
							<span class="js_param">empty_message={{$lang.plugins.grabbers.field_kvs_repository_empty}}</span>
						</div>
						<div class="list"></div>
						<div class="controls">
							<input type="text" name="new_grabber" class="fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.plugins.grabbers.field_kvs_repository_all}}"/>
						</div>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.plugins.grabbers.field_kvs_repository_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.grabbers.field_custom_grabber}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.plugins.grabbers.field_custom_grabber}}</span>
							<span class="js_param">accept=php</span>
						</div>
						<input type="text" name="custom_grabber" maxlength="100" class="fixed_500" readonly="readonly"/>
						<input type="hidden" name="custom_grabber_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.grabbers.field_custom_grabber_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.plugins.grabbers.btn_save}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>