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
		<input type="hidden" name="action" value="start_audit"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.audit.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.audit.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.audit.divider_parameters}}</div></td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair"><input type="checkbox" name="check_installation" value="1" {{if $smarty.post.check_installation==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_installation}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.audit.field_check_installation_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair"><input type="checkbox" name="check_database" value="1" {{if $smarty.post.check_database==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_database}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.audit.field_check_database_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.is_clone_db!="true"}}
			<tr>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair"><input type="checkbox" name="check_formats" value="1" {{if $smarty.post.check_formats==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_formats}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.audit.field_check_formats_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair"><input type="checkbox" name="check_servers" value="1" {{if $smarty.post.check_servers==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_servers}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.audit.field_check_servers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair"><input type="checkbox" name="check_website_ui" value="1" {{if $smarty.post.check_website_ui==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_website_ui}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.audit.field_check_website_ui_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.is_clone_db!="true"}}
			<tr>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="check_video_content" name="check_video_content" value="1" {{if $smarty.post.check_video_content==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_video_content}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.audit.field_check_video_content_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<table class="control_group">
						<tr>
							<td>
								&nbsp;&nbsp;
								<div class="de_lv_pair"><input type="checkbox" name="check_video_stream" value="1" class="check_video_content_on" {{if $smarty.post.check_video_stream==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_video_content_stream}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/>&nbsp;&nbsp;&nbsp;<span class="de_hint">{{$lang.plugins.audit.field_check_video_content_stream_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;&nbsp;
								<div class="de_lv_pair"><input type="checkbox" name="check_video_embed" value="1" class="check_video_content_on" {{if $smarty.post.check_video_embed==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_video_content_embed}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/>&nbsp;&nbsp;&nbsp;<span class="de_hint">{{$lang.plugins.audit.field_check_video_content_embed_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								&nbsp;&nbsp;
								{{$lang.plugins.audit.field_check_video_content_range_from}}: <input type="text" name="video_id_range_from" class="fixed_100 check_video_content_on" {{if $smarty.post.check_video_content!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.video_id_range_from}}"/>
								{{$lang.plugins.audit.field_check_video_content_range_to}}: <input type="text" name="video_id_range_to" class="fixed_100 check_video_content_on" {{if $smarty.post.check_video_content!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.video_id_range_to}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/>&nbsp;&nbsp;&nbsp;<span class="de_hint">{{$lang.plugins.audit.field_check_video_content_range_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</td>
			</tr>
			{{if $config.installation_type==4}}
				<tr>
					<td class="de_control" colspan="2">
						<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="check_album_content" name="check_album_content" value="1" {{if $smarty.post.check_album_content==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_album_content}}</label></div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.audit.field_check_album_content_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_control" colspan="2">
						<table class="control_group">
							<tr>
								<td>
									&nbsp;&nbsp;
									{{$lang.plugins.audit.field_check_album_content_range_from}}: <input type="text" name="album_id_range_from" class="fixed_100 check_album_content_on" {{if $smarty.post.check_album_content!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.album_id_range_from}}"/>
									{{$lang.plugins.audit.field_check_album_content_range_to}}: <input type="text" name="album_id_range_to" class="fixed_100 check_album_content_on" {{if $smarty.post.check_album_content!=1}}disabled="disabled"{{/if}} value="{{$smarty.post.album_id_range_to}}"/>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/>&nbsp;&nbsp;&nbsp;<span class="de_hint">{{$lang.plugins.audit.field_check_album_content_range_hint}}</span>
									{{/if}}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair"><input type="checkbox" name="check_auxiliary_content" value="1" {{if $smarty.post.check_auxiliary_content==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_auxiliary_content}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.audit.field_check_auxiliary_content_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_control" colspan="2">
					<div class="de_lv_pair"><input type="checkbox" name="check_content_protection" value="1" {{if $smarty.post.check_content_protection==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_content_protection}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.audit.field_check_content_protection_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_control" colspan="2">
				<div class="de_lv_pair"><input type="checkbox" name="check_security" value="1" {{if $smarty.post.check_security==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit.field_check_security}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.audit.field_check_security_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.audit.btn_start}}"/>
			</td>
		</tr>
		{{if $smarty.post.is_displayed==1}}
			<tr>
				<td class="de_separator" colspan="2">
					<div>
						{{assign var="audit_time" value=$smarty.post.audit_time|date_format:$smarty.session.userdata.full_date_format}}
						{{$lang.plugins.audit.divider_result|replace:"%1%":$audit_time}}
						{{if $smarty.post.errors_count>0 || $smarty.post.warnings_count>0 || $smarty.post.infos_count>0}}
							{{if $smarty.post.errors_count>0}}
								({{$lang.plugins.audit.dg_recent_audits_col_results_errors|replace:"%1%":$smarty.post.errors_count}}{{if $smarty.post.warnings_count==0 && $smarty.post.infos_count==0}}){{else}}, {{/if}}
							{{/if}}
							{{if $smarty.post.warnings_count>0}}
								{{if $smarty.post.errors_count==0}}({{/if}}{{$lang.plugins.audit.dg_recent_audits_col_results_warnings|replace:"%1%":$smarty.post.warnings_count}}{{if $smarty.post.infos_count==0}}){{else}}, {{/if}}
							{{/if}}
							{{if $smarty.post.infos_count>0}}
								{{if $smarty.post.errors_count==0 && $smarty.post.warnings_count==0}}({{/if}}{{$lang.plugins.audit.dg_recent_audits_col_results_infos|replace:"%1%":$smarty.post.infos_count}})
							{{/if}}
						{{/if}}
						/
						<a href="?plugin_id=audit&amp;action=log&amp;task_id={{$smarty.post.task_id}}" rel="external">{{$lang.plugins.audit.divider_result_log_file}}</a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					{{if $smarty.post.has_finished==1}}
						{{if count($smarty.post.audit_messages)>0}}
							<table class="de_edit_grid">
								<colgroup>
									<col width="15%"/>
									<col/>
									<col/>
								</colgroup>
								<tr class="eg_header fixed_height_30">
									<td>{{$lang.plugins.audit.dg_errors_col_error_type}}</td>
									<td>{{$lang.plugins.audit.dg_errors_col_resource}}</td>
									<td>{{$lang.plugins.audit.dg_errors_col_message}}</td>
								</tr>
								{{foreach item=item from=$smarty.post.audit_messages|smarty:nodefaults}}
									<tr class="eg_data fixed_height_30">
										<td>
											{{if $item.is_info==1}}
												{{$lang.plugins.audit.dg_errors_col_error_type_info}}
											{{elseif $item.is_warning==1}}
												<span class="warning_text">{{$lang.plugins.audit.dg_errors_col_error_type_warning}}</span>
											{{else}}
												<span class="highlighted_text">{{$lang.plugins.audit.dg_errors_col_error_type_error}}</span>
											{{/if}}
										</td>
										<td>
											{{if $item.message_type==12 || $item.message_type==302}}
												{{if in_array('system|formats',$smarty.session.permissions)}}
													{{if $config.installation_type>=2}}
														<a href="formats_videos.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_format_video|replace:"%1%":$item.resource}}</a>
													{{else}}
														<a href="formats_videos_basic.php" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_format_video|replace:"%1%":$item.resource}}</a>
													{{/if}}
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_format_video|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==13}}
												{{if in_array('system|formats',$smarty.session.permissions)}}
													<a href="formats_screenshots.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_format_screenshot|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_format_screenshot|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==14}}
												{{if in_array('system|formats',$smarty.session.permissions) && $config.installation_type==4}}
													<a href="formats_albums.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_format_album|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_format_album|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==29}}
												<a href="https://www.php.net/manual/{{$lang.system.language_code}}/{{$item.resource_id}}.installation.php" rel="external">PHP {{$item.resource}}</a>
											{{elseif $item.message_type==30}}
												{{if in_array('website_ui|view',$smarty.session.permissions)}}
													<a href="project_blocks.php" rel="external">{{$lang.plugins.audit.dg_errors_col_resource_block|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_block|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==51}}
												{{if in_array('system|player_settings',$smarty.session.permissions)}}
													<a href="{{$item.resource_id}}" rel="external">{{$lang.plugins.audit.dg_errors_col_resource_player_settings}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_player_settings}}
												{{/if}}
											{{elseif $item.message_type==52}}
												{{if in_array('system|player_settings',$smarty.session.permissions)}}
													<a href="{{$item.resource_id}}" rel="external">{{$lang.plugins.audit.dg_errors_col_resource_embed_player_settings}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_embed_player_settings}}
												{{/if}}
											{{elseif $item.message_type==61 || $item.message_type==62}}
												{{$lang.plugins.audit.dg_errors_col_resource_table|replace:"%1%":$item.resource}}
											{{elseif $item.message_type==63}}
												{{$lang.plugins.audit.dg_errors_col_resource_language|replace:"%1%":$item.resource}}
											{{elseif $item.message_type==71 || $item.message_type==72}}
												{{assign var="plugin_id" value=$item.plugin_id}}
												{{assign var="plugin_permission" value="plugins|`$plugin_id`"}}
												{{if in_array($plugin_permission,$smarty.session.permissions)}}
													<a href="plugins.php?plugin_id={{$item.plugin_id}}">{{$lang.plugins.$plugin_id.title}}</a>
												{{else}}
													{{$lang.plugins.$plugin_id.title}}
												{{/if}}
											{{elseif $item.message_type==100 || $item.message_type==101 || $item.message_type==103 || $item.message_type==104 || $item.message_type==105 || $item.message_type==106 || $item.message_type==107 || $item.message_type==108 || $item.message_type==109 || $item.message_type==110 || $item.message_type==111 || $item.message_type==112 || $item.message_type==113 || $item.message_type==114}}
												{{if in_array('videos|view',$smarty.session.permissions)}}
													<a href="videos.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_video|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_video|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==120}}
												{{if in_array('system|formats',$smarty.session.permissions)}}
													{{if $config.installation_type>=2}}
														<a href="formats_videos.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_format_video|replace:"%1%":$item.resource}}</a>
													{{else}}
														<a href="formats_videos_basic.php" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_format_video|replace:"%1%":$item.resource}}</a>
													{{/if}}
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_format_video|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==200}}
												{{if in_array('albums|view',$smarty.session.permissions)}}
													<a href="albums.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_album|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_album|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==301}}
												{{if in_array('system|system_settings',$smarty.session.permissions)}}
													<a href="options.php?page=general_settings" rel="external">{{$lang.plugins.audit.dg_errors_col_resource_settings}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_settings}}
												{{/if}}
											{{elseif $item.message_type==300 || $item.message_type==304 || $item.message_type==305 || $item.message_type==306 || $item.message_type==400 || $item.message_type==401 || $item.message_type==402 || $item.message_type==403 || $item.message_type==404  || $item.message_type==405 || $item.message_type==406 || $item.message_type==407 || $item.message_type==408}}
												{{if in_array('system|servers',$smarty.session.permissions)}}
													{{if $item.message_type==406}}
														<a href="servers_test.php?server_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_storage_server|replace:"%1%":$item.resource}}</a>
													{{else}}
														<a href="servers.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_storage_server|replace:"%1%":$item.resource}}</a>
													{{/if}}
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_storage_server|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==500 || $item.message_type==501 || $item.message_type==502 || $item.message_type==503 || $item.message_type==504 || $item.message_type==505}}
												{{if in_array('system|servers',$smarty.session.permissions)}}
													{{if $config.installation_type>=3}}
														<a href="servers_conversion.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_conversion_server|replace:"%1%":$item.resource}}</a>
													{{else}}
														<a href="servers_conversion_basic.php" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_conversion_server|replace:"%1%":$item.resource}}</a>
													{{/if}}
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_conversion_server|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==700 || $item.message_type==701 || $item.message_type==702 || $item.message_type==703}}
												{{if in_array('website_ui|view',$smarty.session.permissions)}}
													<a href="project_pages_components.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_page_component|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_page_component|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==710 || $item.message_type==711 || $item.message_type==712 || $item.message_type==713 || $item.message_type==714 || $item.message_type==715 || $item.message_type==716 || $item.message_type==717 || $item.message_type==718}}
												{{if in_array('website_ui|view',$smarty.session.permissions)}}
													<a href="project_pages.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_page|replace:"%1%":$item.resource}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_page|replace:"%1%":$item.resource}}
												{{/if}}
											{{elseif $item.message_type==720 || $item.message_type==721 || $item.message_type==722 || $item.message_type==723 || $item.message_type==724 || $item.message_type==725}}
												{{if in_array('website_ui|view',$smarty.session.permissions)}}
													<a href="project_pages_global.php" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_global_blocks}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_global_blocks}}
												{{/if}}
											{{elseif $item.message_type==730 || $item.message_type==731}}
												{{if in_array('advertising|view',$smarty.session.permissions)}}
													<a href="project_spots.php?action=change_spot&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_advertising_spot|replace:"%1%":$item.resource_id}}</a>
												{{else}}
													{{$lang.plugins.audit.dg_errors_col_resource_advertising_spot|replace:"%1%":$item.resource_id}}
												{{/if}}
											{{elseif $item.message_type==800 || $item.message_type==801 || $item.message_type==802}}
												{{if $item.resource=='video'}}
													{{if in_array('videos|view',$smarty.session.permissions)}}
														<a href="videos.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_video|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_video|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='album'}}
													{{if in_array('albums|view',$smarty.session.permissions)}}
														<a href="albums.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_album|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_album|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='category'}}
													{{if in_array('categories|view',$smarty.session.permissions)}}
														<a href="categories.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_category|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_category|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='category_group'}}
													{{if in_array('category_groups|view',$smarty.session.permissions)}}
														<a href="categories_groups.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_category_group|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_category_group|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='model'}}
													{{if in_array('models|view',$smarty.session.permissions)}}
														<a href="models.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_model|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_model|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='model_group'}}
													{{if in_array('models_groups|view',$smarty.session.permissions)}}
														<a href="models_groups.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_model_group|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_model_group|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='content_source'}}
													{{if in_array('content_sources|view',$smarty.session.permissions)}}
														<a href="content_sources.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_content_source|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_content_source|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='dvd'}}
													{{if in_array('dvds|view',$smarty.session.permissions)}}
														<a href="dvds.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_dvd|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_dvd|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='dvd_group'}}
													{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
														<a href="dvds_groups.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_dvd_group|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_dvd_group|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='post'}}
													{{if in_array('posts|view',$smarty.session.permissions)}}
														<a href="posts.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_post|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_post|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{elseif $item.resource=='user'}}
													{{if in_array('users|view',$smarty.session.permissions)}}
														<a href="users.php?action=change&amp;item_id={{$item.resource_id}}" class="popup">{{$lang.plugins.audit.dg_errors_col_resource_user|replace:"%1%":$item.resource_id}}</a>
													{{else}}
														{{$lang.plugins.audit.dg_errors_col_resource_user|replace:"%1%":$item.resource_id}}
													{{/if}}
												{{/if}}
											{{else}}
												{{$item.resource}}
											{{/if}}
										</td>
										<td>
											{{if $item.message_type==1}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_db_version_mismatch`"}}
											{{elseif $item.message_type==2}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_system_file_removed`"}}
											{{elseif $item.message_type==3}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_cron_folder`"}}
											{{elseif $item.message_type==4}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_cron_last_exec`"}}
											{{elseif $item.message_type==5}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_memory_limit`"}}
											{{elseif $item.message_type==6}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_ip_detection`"}}
											{{elseif $item.message_type==7}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_system_file_changed`"}}
											{{elseif $item.message_type==8}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_php_config_parameter_value`"}}
											{{elseif $item.message_type==9}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_db_installation`"}}
											{{elseif $item.message_type==10}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_permissions`"}}
											{{elseif $item.message_type==11}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_library_problem`"}}
											{{elseif $item.message_type==12}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_format`"}}
											{{elseif $item.message_type==13}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_screenshot_format`"}}
											{{elseif $item.message_type==14}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_album_format`"}}
											{{elseif $item.message_type==15}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_true_type_fonts`"}}
											{{elseif $item.message_type==16}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_mysql_strict_mode`"}}
											{{elseif $item.message_type==17}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_path_slashes`"}}
											{{elseif $item.message_type==18}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_folder_permissions`"}}
											{{elseif $item.message_type==19}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_system_file_invalid`"}}
											{{elseif $item.message_type==20}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_www_missing`"}}
											{{elseif $item.message_type==21}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_www_redundant`"}}
											{{elseif $item.message_type==22}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_satellite_for`"}}
											{{elseif $item.message_type==23}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_iframe_embed`"}}
											{{elseif $item.message_type==25}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_gzip`"}}
											{{elseif $item.message_type==26}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_transliteration_rules`"}}
											{{elseif $item.message_type==27}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_http_host`"}}
											{{elseif $item.message_type==29}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_php_module`"}}
											{{elseif $item.message_type==30}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_block_implementation`"}}
											{{elseif $item.message_type==31}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_memcache_module`"}}
											{{elseif $item.message_type==32}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_memcache_connection`"}}
											{{elseif $item.message_type==34}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_file_creation_failed`"}}
											{{elseif $item.message_type==35}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_temp_file`"}}
											{{elseif $item.message_type==36}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_custom_blocks`"}}
											{{elseif $item.message_type==37}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_ydl_not_installed`"}}
											{{elseif $item.message_type==38}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_php_5_api_used`"}}
											{{elseif $item.message_type==39}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_screenshot_sources`"}}
											{{elseif $item.message_type==40}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_cron_duplicate`"}}
											{{elseif $item.message_type==51}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_player_settings_errors`"}}
											{{elseif $item.message_type==52}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_embed_player_settings_errors`"}}
											{{elseif $item.message_type==61}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_table_status_check_warning`"}}
											{{elseif $item.message_type==62}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_table_status_check_error`"}}
											{{elseif $item.message_type==63}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_language_column_error`"}}
											{{elseif $item.message_type==71 || $item.message_type==72}}
												{{assign var="plugin_id" value=$item.plugin_id}}
												{{assign var="plugin_message" value=$item.plugin_message}}
												{{assign var="text" value=$lang.plugins.$plugin_id.$plugin_message}}
											{{elseif $item.message_type==100}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_file_missing`"}}
											{{elseif $item.message_type==101}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_screenshot_missing`"}}
											{{elseif $item.message_type==104}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_cuepoints_file_missing`"}}
											{{elseif $item.message_type==105}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_preview_file_missing`"}}
											{{elseif $item.message_type==106}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_screenshot_size_invalid`"}}
											{{elseif $item.message_type==107}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_screenshot_zip_missing`"}}
											{{elseif $item.message_type==108}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_file_cannot_be_streamed`"}}
											{{elseif $item.message_type==109}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_hotlink_required`"}}
											{{elseif $item.message_type==110}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_hotlink_invalid`"}}
											{{elseif $item.message_type==111}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_embed_required`"}}
											{{elseif $item.message_type==112}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_embed_invalid`"}}
											{{elseif $item.message_type==113}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_pseudo_required`"}}
											{{elseif $item.message_type==114}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_pseudo_invalid`"}}
											{{elseif $item.message_type==120}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_format_required`"}}
											{{elseif $item.message_type==200}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_album_file_missing`"}}
											{{elseif $item.message_type==300}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_hotlink_protection1`"}}
											{{elseif $item.message_type==301}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_hotlink_protection2`"}}
											{{elseif $item.message_type==302}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_format_hotlink_possible`"}}
											{{elseif $item.message_type==303}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_sources_accessible`"}}
											{{elseif $item.message_type==304}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_video_files_accessible`"}}
											{{elseif $item.message_type==305}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_album_sources_accessible`"}}
											{{elseif $item.message_type==306}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_album_images_accessible`"}}
											{{elseif $item.message_type==400}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_connection1`"}}
											{{elseif $item.message_type==401}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_connection2`"}}
											{{elseif $item.message_type==402}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_connection3`"}}
											{{elseif $item.message_type==403}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_no_ftp_extension`"}}
											{{elseif $item.message_type==404}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_control_script`"}}
											{{elseif $item.message_type==405}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_time`"}}
											{{elseif $item.message_type==406}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_availability`"}}
											{{elseif $item.message_type==407}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_api_script`"}}
											{{elseif $item.message_type==408}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_https`"}}
											{{elseif $item.message_type==500}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_connection1`"}}
											{{elseif $item.message_type==501}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_connection2`"}}
											{{elseif $item.message_type==502}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_connection3`"}}
											{{elseif $item.message_type==503}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_no_ftp_extension`"}}
											{{elseif $item.message_type==504}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_heartbeat`"}}
											{{elseif $item.message_type==505}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_server_heartbeat2`"}}
											{{elseif $item.message_type==600}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_mysql_select_into_outfile`"}}
											{{elseif $item.message_type==601}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_suspicious_code_found`"}}
											{{elseif $item.message_type==602}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_suspicious_file_found`"}}
											{{elseif $item.message_type==603}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_file_changes_found`"}}
											{{elseif $item.message_type==604}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_folder_allows_php`"}}
											{{elseif $item.message_type==605}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_folder_allows_public_access`"}}
											{{elseif $item.message_type==606}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_suspicious_folder_found`"}}
											{{elseif $item.message_type==700}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_component_error`"}}
											{{elseif $item.message_type==701}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_component_permissions`"}}
											{{elseif $item.message_type==702}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_component_php`"}}
											{{elseif $item.message_type==703}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_component_empty_template`"}}
											{{elseif $item.message_type==710}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_disabled`"}}
											{{elseif $item.message_type==711}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_error`"}}
											{{elseif $item.message_type==712}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_caching_error`"}}
											{{elseif $item.message_type==713}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_blocks_cache`"}}
											{{elseif $item.message_type==714}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_permissions`"}}
											{{elseif $item.message_type==715}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_php`"}}
											{{elseif $item.message_type==716}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_var_from_equal_names`"}}
											{{elseif $item.message_type==717}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_var_from_equal_names`"}}
											{{elseif $item.message_type==718}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_page_caching_warning`"}}
											{{elseif $item.message_type==720}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_global_blocks_error`"}}
											{{elseif $item.message_type==721}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_global_blocks_caching_error`"}}
											{{elseif $item.message_type==722}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_global_blocks_cache`"}}
											{{elseif $item.message_type==723}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_global_blocks_permissions`"}}
											{{elseif $item.message_type==724}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_global_blocks_php`"}}
											{{elseif $item.message_type==725}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_global_blocks_caching_warning`"}}
											{{elseif $item.message_type==730}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_advertising_spot_file_invalid`"}}
											{{elseif $item.message_type==731}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_advertising_spot_permissions`"}}
											{{elseif $item.message_type==800}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_data_file_missing`"}}
											{{elseif $item.message_type==801}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_data_file_size_invalid`"}}
											{{elseif $item.message_type==802}}
												{{assign var="text" value="`$lang.plugins.audit.dg_errors_col_message_data_required_field_missing`"}}
											{{elseif $item.message_type==1001}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"1"}}
											{{elseif $item.message_type==1002}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"2"}}
											{{elseif $item.message_type==1003}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"3"}}
											{{elseif $item.message_type==1004}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"4"}}
											{{elseif $item.message_type==1005}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"5"}}
											{{elseif $item.message_type==1007}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"7"}}
											{{elseif $item.message_type==1008}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"8"}}
											{{elseif $item.message_type==1009}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"9"}}
											{{elseif $item.message_type==1010}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"10"}}
											{{elseif $item.message_type==1011}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"11"}}
											{{elseif $item.message_type==1012}}
												{{assign var="text" value=$lang.plugins.audit.dg_errors_col_message_known_issue|replace:"%1%":"12"}}
											{{/if}}
											{{if $item.detail!=''}}
												<div class="details_link">
													<a href="javascript:stub()">{{$text}}</a>
													<span class="js_params">
														<span class="js_param">text={{$item.detail}}</span>
													</span>
												</div>
											{{elseif $item.resource_path!='' && ($smarty.session.userdata.is_superadmin==1 || $smarty.session.userdata.is_superadmin==2)}}
												<a href="?plugin_id=audit&amp;action=file&amp;task_id={{$smarty.post.task_id}}&amp;file_path={{$item.resource_path}}" rel="external">{{$text}}</a>
											{{else}}
												{{$text}}
											{{/if}}
										</td>
									</tr>
								{{/foreach}}
							</table>
						{{else}}
							{{$lang.plugins.audit.divider_result_none}}
						{{/if}}
					{{else}}
						{{$lang.plugins.audit.divider_result_not_finished}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.audit.divider_recent_audits}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				{{if count($smarty.post.recent_audits)>0}}
					<table class="de_edit_grid">
						<colgroup>
							<col width="15%"/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.plugins.audit.dg_recent_audits_col_time}}</td>
							<td>{{$lang.plugins.audit.dg_recent_audits_col_results}}</td>
							<td>{{$lang.plugins.audit.dg_recent_audits_col_log}}</td>
						</tr>
						{{foreach item=item from=$smarty.post.recent_audits|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30 {{if $item.is_displayed==1}}eg_selected{{/if}}">
								<td>
									{{$item.time|date_format:$smarty.session.userdata.full_date_format}}
								</td>
								<td>
									{{if $item.has_finished==1}}
										{{if $item.errors_count==0 && $item.warnings_count==0 && $item.infos_count==0}}
											<a href="?plugin_id=audit&amp;action=display_result&amp;task_id={{$item.key}}">
												{{$lang.plugins.audit.dg_recent_audits_col_results_messages|replace:"%1%":0}}
											</a>
										{{else}}
											<a href="?plugin_id=audit&amp;action=display_result&amp;task_id={{$item.key}}">
												{{if $item.errors_count>0}}
													{{$lang.plugins.audit.dg_recent_audits_col_results_errors|replace:"%1%":$item.errors_count}}{{if $item.warnings_count>0 || $item.infos_count>0}}, {{/if}}
												{{/if}}
												{{if $item.warnings_count>0}}
													{{$lang.plugins.audit.dg_recent_audits_col_results_warnings|replace:"%1%":$item.warnings_count}}{{if $item.infos_count>0}}, {{/if}}
												{{/if}}
												{{if $item.infos_count>0}}
													{{$lang.plugins.audit.dg_recent_audits_col_results_infos|replace:"%1%":$item.infos_count}}
												{{/if}}
											</a>
										{{/if}}
									{{elseif $item.has_process==1}}
										{{if $item.process>0}}
											{{$lang.plugins.audit.dg_recent_audits_col_results_in_process_pc|replace:"%1%":$item.process}}
										{{else}}
											{{$lang.plugins.audit.dg_recent_audits_col_results_in_process}}
										{{/if}}
									{{else}}
										<span class="highlighted_text">{{$lang.plugins.audit.dg_recent_audits_col_results_error}}</span>
									{{/if}}
								</td>
								<td>
									<a href="?plugin_id=audit&amp;action=log&amp;task_id={{$item.key}}" rel="external">task-log-{{$item.key}}.dat</a>
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.plugins.audit.divider_recent_audits_none}}
				{{/if}}
			</td>
		</tr>
	</table>
</form>