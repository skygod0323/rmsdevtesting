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
		<input type="hidden" name="action" value="calculate"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.content_stats.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.content_stats.long_desc}}
			</td>
		</tr>
		{{if is_array($smarty.post.result)}}
			<tr>
				<td class="de_separator" colspan="2">
					<div>
						{{assign var="start_date" value=$smarty.post.result.start_date|date_format:$smarty.session.userdata.full_date_format}}
						{{$lang.plugins.content_stats.divider_result|replace:"%1%":$start_date}}
						/
						<a href="?plugin_id=content_stats&amp;action=log&amp;task_id={{$smarty.post.result.task_id}}" rel="external">{{$lang.plugins.content_stats.divider_result_log_file}}</a>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.plugins.content_stats.dg_results_col_type}}</td>
							<td>{{$lang.plugins.content_stats.dg_results_col_storage}}</td>
							<td>{{$lang.plugins.content_stats.dg_results_col_files}}</td>
							<td>{{$lang.plugins.content_stats.dg_results_col_size}}</td>
						</tr>
						{{foreach key="key" item="item" from=$smarty.post.result.stats|smarty:nodefaults}}
							{{if $item.is_group==1}}
								<tr class="eg_group_header">
									<td colspan="4">{{if $item.type=='videos'}}{{$lang.plugins.content_stats.dg_results_col_type_group_videos|replace:"%1%":$item.total}}{{elseif $item.type=='albums'}}{{$lang.plugins.content_stats.dg_results_col_type_group_albums|replace:"%1%":$item.total}}{{/if}}</td>
								</tr>
							{{else}}
								{{assign var="key" value=$key|md5}}
								{{assign var="parent_key" value=$item.parent_key|md5}}
								<tr class="{{if $item.type=='total_main' || $item.type=='total_content'}}eg_header{{else}}eg_data{{/if}} fixed_height_30 {{if $item.storage=='content_server' && $item.server_group_id>0}}{{$parent_key}}_servers hidden{{/if}}">
									<td {{if $item.server_group_id>0}}class="eg_padding"{{/if}}>
										{{if $item.storage=='content_server' && $item.server_group_id<1}}<a class="de_expand" href="javascript:stub()" id="{{$key}}_servers">{{/if}}
										{{if $item.type=='video_sources'}}
											{{$lang.plugins.content_stats.dg_results_col_type_video_sources}}
										{{elseif $item.type=='video_formats'}}
											{{$lang.plugins.content_stats.dg_results_col_type_video_formats|replace:"%1%":$item.format}}
										{{elseif $item.type=='video_timelines'}}
											{{$lang.plugins.content_stats.dg_results_col_type_video_timelines|replace:"%1%":$item.format}}
										{{elseif $item.type=='video_screenshots_sources'}}
											{{$lang.plugins.content_stats.dg_results_col_type_screenshots_sources}}
										{{elseif $item.type=='video_screenshots_formats'}}
											{{$lang.plugins.content_stats.dg_results_col_type_screenshots_formats|replace:"%1%":$item.format}}
										{{elseif $item.type=='video_screenshots_zip'}}
											{{$lang.plugins.content_stats.dg_results_col_type_screenshots_zip|replace:"%1%":$item.format}}
										{{elseif $item.type=='video_logs'}}
											{{$lang.plugins.content_stats.dg_results_col_type_video_logs}}
										{{elseif $item.type=='album_images_zip'}}
											{{$lang.plugins.content_stats.dg_results_col_type_album_images_zip|replace:"%1%":$item.format}}
										{{elseif $item.type=='album_images_sources_zip'}}
											{{$lang.plugins.content_stats.dg_results_col_type_album_images_sources_zip}}
										{{elseif $item.type=='album_images_sources'}}
											{{$lang.plugins.content_stats.dg_results_col_type_album_images_sources}}
										{{elseif $item.type=='album_images_formats'}}
											{{$lang.plugins.content_stats.dg_results_col_type_album_images_formats|replace:"%1%":$item.format}}
										{{elseif $item.type=='album_logs'}}
											{{$lang.plugins.content_stats.dg_results_col_type_album_logs}}
										{{elseif $item.type=='total_main' || $item.type=='total_content'}}
											{{$lang.plugins.content_stats.dg_results_col_type_total}}
										{{/if}}
										{{if $item.storage=='content_server' && $item.server_group_id<1}}</a>{{/if}}
									</td>
									<td>
										{{if $item.storage=='main_server'}}
											{{$lang.plugins.content_stats.dg_results_col_storage_local}}
										{{elseif $item.storage=='content_server'}}
											{{if $item.server_group_id>0}}
												{{assign var="server_group_id" value=$item.server_group_id}}
												{{$smarty.post.server_groups[$server_group_id].title}}
											{{else}}
												{{$lang.plugins.content_stats.dg_results_col_storage_content}}
											{{/if}}
										{{/if}}
									</td>
									<td>{{$item.files}}</td>
									<td>{{$item.size|sizeToHumanString:2}}</td>
								</tr>
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.content_stats.divider_recent_calculations}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				{{if count($smarty.post.recent_calculations)>0}}
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.plugins.content_stats.dg_recent_calculations_col_time}}</td>
							<td>{{$lang.plugins.content_stats.dg_recent_calculations_col_results}}</td>
							<td>{{$lang.plugins.content_stats.dg_recent_calculations_col_log}}</td>
						</tr>
						{{foreach item="item" from=$smarty.post.recent_calculations|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30 {{if $item.task_id==$smarty.post.result.task_id}}eg_selected{{/if}}">
								<td>
									{{$item.start_date|date_format:$smarty.session.userdata.full_date_format}}
								</td>
								<td>
									{{if $item.end_date>0}}
										<a href="?plugin_id=content_stats&amp;action=results&amp;task_id={{$item.task_id}}">
											{{assign var="key1" value="900/main"}}
											{{assign var="size1" value=$item.stats.$key1.size|sizeToHumanString:2}}
											{{assign var="key2" value="900/content"}}
											{{assign var="size2" value=$item.stats.$key2.size|sizeToHumanString:2}}
											{{$lang.plugins.content_stats.dg_recent_calculations_col_results_value|replace:"%1%":$size1|replace:"%2%":$size2}}
										</a>
									{{else}}
										{{$lang.plugins.content_stats.dg_recent_calculations_col_results_in_process|replace:"%1%":$item.progress}}
									{{/if}}
								</td>
								<td>
									<a href="?plugin_id=content_stats&amp;action=log&amp;task_id={{$item.task_id}}" rel="external">task-log-{{$item.task_id}}.dat</a>
								</td>
							</tr>
						{{/foreach}}
					</table>
				{{else}}
					{{$lang.plugins.content_stats.divider_recent_calculations_none}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.content_stats.btn_calculate}}"/>
			</td>
		</tr>
	</table>
</form>