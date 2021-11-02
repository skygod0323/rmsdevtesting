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

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<table class="de de_readonly">
		<colgroup>
			<col width="5%"/>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="4"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_background_tasks_log}}</a> / {{$lang.settings.background_task_log_view}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/538-video-conversion-engine-and-video-conversion-speed">Video conversion engine and video conversion speed</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.background_task_log_field_status}}:</td>
			<td class="de_control">
				{{if $smarty.post.status_id==2}}
					{{$lang.settings.background_task_log_field_status_error}} ({{$smarty.post.message}})
				{{elseif $smarty.post.status_id==3}}
					{{$lang.settings.background_task_log_field_status_completed}}
				{{elseif $smarty.post.status_id==4}}
					{{$lang.settings.background_task_log_field_status_cancelled}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.background_task_log_field_type}}:</td>
			<td class="de_control">
				{{if $smarty.post.type_id==1}}
					{{$lang.settings.common_background_task_type_new_video}}
				{{elseif $smarty.post.type_id==2}}
					{{$lang.settings.common_background_task_type_delete_video}}
				{{elseif $smarty.post.type_id==3}}
					{{$lang.settings.common_background_task_type_upload_video_format_file}} ({{$smarty.post.format_postfix}})
				{{elseif $smarty.post.type_id==4}}
					{{$lang.settings.common_background_task_type_create_video_format_file}} ({{$smarty.post.format_postfix}})
				{{elseif $smarty.post.type_id==5}}
					{{$lang.settings.common_background_task_type_delete_video_format_file}} ({{$smarty.post.format_postfix}})
				{{elseif $smarty.post.type_id==6}}
					{{$lang.settings.common_background_task_type_delete_video_format}} ({{$smarty.post.format_postfix}})
				{{elseif $smarty.post.type_id==7}}
					{{$lang.settings.common_background_task_type_create_screenshot_format}} ({{$smarty.post.format_size}})
				{{elseif $smarty.post.type_id==8}}
					{{$lang.settings.common_background_task_type_create_timeline_screenshots}}
				{{elseif $smarty.post.type_id==9}}
					{{$lang.settings.common_background_task_type_delete_screenshot_format}} ({{$smarty.post.format_size}})
				{{elseif $smarty.post.type_id==10}}
					{{$lang.settings.common_background_task_type_new_album}}
				{{elseif $smarty.post.type_id==11}}
					{{$lang.settings.common_background_task_type_delete_album}}
				{{elseif $smarty.post.type_id==12}}
					{{$lang.settings.common_background_task_type_create_album_format}} ({{$smarty.post.format_size}})
				{{elseif $smarty.post.type_id==13}}
					{{$lang.settings.common_background_task_type_delete_album_format}} ({{$smarty.post.format_size}})
				{{elseif $smarty.post.type_id==14}}
					{{$lang.settings.common_background_task_type_upload_album_images}}
				{{elseif $smarty.post.type_id==15}}
					{{$lang.settings.common_background_task_type_change_storage_group_video}}
				{{elseif $smarty.post.type_id==16}}
					{{$lang.settings.common_background_task_type_create_screenshots_zip}}
				{{elseif $smarty.post.type_id==17}}
					{{$lang.settings.common_background_task_type_delete_screenshots_zip}}
				{{elseif $smarty.post.type_id==18}}
					{{$lang.settings.common_background_task_type_create_images_zip}}
				{{elseif $smarty.post.type_id==19}}
					{{$lang.settings.common_background_task_type_delete_images_zip}}
				{{elseif $smarty.post.type_id==20}}
					{{$lang.settings.common_background_task_type_delete_timeline_screenshots}}
				{{elseif $smarty.post.type_id==21}}
					{{$lang.settings.common_background_task_type_create_images_zip}}
				{{elseif $smarty.post.type_id==22}}
					{{$lang.settings.common_background_task_type_album_images_manipulation}}
				{{elseif $smarty.post.type_id==23}}
					{{$lang.settings.common_background_task_type_change_storage_group_album}}
				{{elseif $smarty.post.type_id==24}}
					{{$lang.settings.common_background_task_type_create_overview_screenshots}}
				{{elseif $smarty.post.type_id==27}}
					{{$lang.settings.common_background_task_type_sync_storage_server}}
				{{elseif $smarty.post.type_id==28}}
					{{$lang.settings.common_background_task_type_delete_overview_screenshots}}
				{{elseif $smarty.post.type_id==29}}
					{{$lang.settings.common_background_task_type_recreate_screenshot_formats}}
				{{elseif $smarty.post.type_id==30}}
					{{$lang.settings.common_background_task_type_recreate_album_formats}}
				{{elseif $smarty.post.type_id==50}}
					{{$lang.settings.common_background_task_type_videos_import}}
				{{elseif $smarty.post.type_id==51}}
					{{$lang.settings.common_background_task_type_albums_import}}
				{{elseif $smarty.post.type_id==52}}
					{{$lang.settings.common_background_task_type_videos_mass_edit}}
				{{elseif $smarty.post.type_id==53}}
					{{$lang.settings.common_background_task_type_albums_mass_edit}}
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.server_id>0}}
			<tr>
				<td class="de_label">{{$lang.settings.background_task_log_field_server}}:</td>
				<td class="de_control">
					{{if in_array('system|servers',$smarty.session.permissions)}}
						<a href="{{if $config.installation_type>=3}}servers_conversion.php?action=change&item_id={{$smarty.post.server_id}}{{else}}servers_conversion_basic.php{{/if}}">{{$smarty.post.server|default:$smarty.post.server_id}}</a>
					{{else}}
						{{$smarty.post.server|default:$smarty.post.server_id}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.settings.background_task_log_field_start_date}}:</td>
			<td class="de_control">
				{{if $smarty.post.start_date=='0000-00-00 00:00:00'}}
					{{$lang.common.undefined}}
				{{else}}
					{{$smarty.post.start_date|date_format:$smarty.session.userdata.full_date_format}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.background_task_log_field_end_date}}:</td>
			<td class="de_control">
				{{if $smarty.post.end_date=='0000-00-00 00:00:00'}}
					{{$lang.common.undefined}}
				{{else}}
					{{$smarty.post.end_date|date_format:$smarty.session.userdata.full_date_format}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.background_task_log_field_duration}}:</td>
			<td class="de_control">
				{{if $smarty.post.start_date=='0000-00-00 00:00:00'}}
					{{$lang.common.undefined}}
				{{else}}
					{{$smarty.post.duration}}
				{{/if}}
				&nbsp;
				<a href="{{$page_name}}?action=task_log&amp;item_id={{$smarty.post.task_id}}" rel="external">{{$lang.settings.background_task_log_action_view_log_task}}</a>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.background_task_log_field_details}}:</td>
			<td class="de_control">
				{{if count($smarty.post.phases)>0}}
					{{assign var="current_level" value=0}}
					{{section loop=$smarty.post.phases|@count name="phases"}}
						{{assign var="index" value=$smarty.section.phases.index}}
						{{assign var="index_next" value=$smarty.section.phases.index+1}}
						{{assign var="index_prev" value=$smarty.section.phases.index-1}}

						{{if !$smarty.section.phases.first && $smarty.post.phases[$index_prev].level<$smarty.post.phases[$index].level}}
							<div style="padding-left: 20px; padding-top: 5px; padding-bottom: 5px" {{if $smarty.post.phases[$index_prev].level==0}}class="phase-{{$smarty.post.phases[$index_prev].id}} hidden"{{/if}}>
							{{assign var="current_level" value=$smarty.post.phases[$index].level}}
						{{/if}}

						{{if $current_level==0}}
							<div style="margin-bottom: 5px;">
								{{if $smarty.post.phases[$index].id!='PE' && $smarty.post.phases[$index].id!='CE' && $smarty.post.phases[$index].id!='FE' && $smarty.post.phases[$index].id!='E' && $smarty.post.phases[$index].id!='I' && $smarty.post.phases[$index].id!='IE'}}
									<a id="phase-{{$smarty.post.phases[$index].id}}" class="de_expand" href="javascript:stub()">{{$smarty.post.phases[$index].duration}} - {{$smarty.post.phases[$index].description}}</a><br/>
								{{else}}
									<span>{{$smarty.post.phases[$index].duration}} - {{$smarty.post.phases[$index].description}}</span><br/>
								{{/if}}
							</div>
						{{else}}
							<span>{{$smarty.post.phases[$index].duration}} - {{$smarty.post.phases[$index].description}}</span><br/>
						{{/if}}

						{{if !$smarty.section.phases.last && $smarty.post.phases[$index_next].level<$smarty.post.phases[$index].level}}
							{{section name="closing_phase" loop=$smarty.post.phases[$index].level-$smarty.post.phases[$index_next].level}}
								</div>
							{{/section}}
							{{assign var="current_level" value=$smarty.post.phases[$index_next].level}}
						{{/if}}
					{{/section}}
					{{section name="closing_phase" loop=$current_level}}
						</div>
					{{/section}}
				{{else}}
					{{$lang.settings.background_task_log_field_details_empty}}
				{{/if}}
			</td>
		</tr>
	</table>
</form>

{{else}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf">
		<div class="dgf">
			<table>
				<tr>
					<td class="dgf_label">{{$lang.common.dg_filter_show_on_page}}:</td>
					<td class="dgf_control"><input type="text" name="num_on_page" size="3" value="{{$smarty.session.save.$page_name.num_on_page}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $table_filtered==0}}disabled="disabled"{{/if}}/>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_log_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$list_status_values|smarty:nodefaults key="id" item="value"}}
								<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_status_id!='' && $smarty.session.save.$page_name.se_status_id=="`$id`"}}selected="selected"{{/if}}>{{$value}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_type_id>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_log_field_type}}:</td>
					<td class="dgf_control">
						<select name="se_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$list_type_values|smarty:nodefaults key="id" item="value"}}
								<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_type_id>0 && $smarty.session.save.$page_name.se_type_id=="`$id`"}}selected="selected"{{/if}}>{{$value}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_error_code>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_log_field_error_code}}:</td>
					<td class="dgf_control">
						<select name="se_error_code">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$list_error_code_values|smarty:nodefaults key="id" item="value"}}
								<option value="{{$id}}" {{if $smarty.session.save.$page_name.se_error_code>0 && $smarty.session.save.$page_name.se_error_code=="`$id`"}}selected="selected"{{/if}}>{{$value}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_video_id>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_log_field_video}}:</td>
					<td class="dgf_control"><input type="text" name="se_video_id" size="10" value="{{if $smarty.session.save.$page_name.se_video_id>0}}{{$smarty.session.save.$page_name.se_video_id}}{{/if}}"/></td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_album_id>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_log_field_album}}:</td>
					<td class="dgf_control"><input type="text" name="se_album_id" size="10" value="{{if $smarty.session.save.$page_name.se_album_id>0}}{{$smarty.session.save.$page_name.se_album_id}}{{/if}}"/></td>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									{{if $item.video_id>0}}
										<span class="js_param">video_id={{$item.video_id}}</span>
									{{else}}
										<span class="js_param">video_log_hide=true</span>
									{{/if}}
									{{if $item.album_id>0}}
										<span class="js_param">album_id={{$item.album_id}}</span>
									{{else}}
										<span class="js_param">album_log_hide=true</span>
									{{/if}}
								</span>
							</a>
						</td>
					</tr>
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?action=task_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_log_action_view_log_task}}</span>
					<span class="js_param">disable=${task_log_disable}</span>
					<span class="js_param">plain_link=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=videos.php?action=video_log&amp;item_id=${video_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_log_action_view_log_video}}</span>
					<span class="js_param">hide=${video_log_hide}</span>
					<span class="js_param">plain_link=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=albums.php?action=album_log&amp;item_id=${album_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_log_action_view_log_album}}</span>
					<span class="js_param">hide=${album_log_hide}</span>
					<span class="js_param">plain_link=true</span>
				</li>
			</ul>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}