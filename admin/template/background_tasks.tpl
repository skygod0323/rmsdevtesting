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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.settings.background_task_field_status}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_type_id>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_field_type}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_error_code>0}}dgf_selected{{/if}}">{{$lang.settings.background_task_field_error_code}}:</td>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}"/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
						<td>
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									{{if $item.status_id!=2}}
										<span class="js_param">restart_hide=true</span>
									{{/if}}
									{{if $item.status_id!=0}}
										<span class="js_param">inc_priority_hide=true</span>
									{{/if}}
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
									{{if $item.server_id<1}}
										<span class="js_param">conversion_log_disable=true</span>
									{{/if}}
								</span>
							</a>
						</td>
					</tr>
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.settings.background_task_action_delete_confirm|replace:"%1%":'${id}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=restart&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_restart}}</span>
					<span class="js_param">hide=${restart_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=inc_priority&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_inc_priority}}</span>
					<span class="js_param">hide=${inc_priority_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=task_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_task}}</span>
					<span class="js_param">disable=${task_log_disable}</span>
					<span class="js_param">plain_link=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=videos.php?action=video_log&amp;item_id=${video_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_video}}</span>
					<span class="js_param">hide=${video_log_hide}</span>
					<span class="js_param">plain_link=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=albums.php?action=album_log&amp;item_id=${album_id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_album}}</span>
					<span class="js_param">hide=${album_log_hide}</span>
					<span class="js_param">plain_link=true</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?action=conversion_log&amp;item_id=${id}</span>
					<span class="js_param">title={{$lang.settings.background_task_action_view_log_conversion}}</span>
					<span class="js_param">disable=${conversion_log_disable}</span>
					<span class="js_param">plain_link=true</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<table>
				<tr>
					<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
					<td class="dgb_control">
						<select name="batch_action">
							<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
							<option value="restart">{{$lang.settings.background_task_batch_restart_selected}}</option>
							<option value="restart_all">{{$lang.settings.background_task_batch_restart_all}}</option>
							<option value="inc_priority">{{$lang.settings.background_task_batch_inc_priority}}</option>
							<option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
							<option value="delete_all">{{$lang.settings.background_task_batch_delete_all}}</option>
							<option value="delete_failed">{{$lang.settings.background_task_batch_delete_failed}}</option>
						</select>
					</td>
					<td class="dgb_control">
						<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
					</td>
					<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
				</tr>
			</table>
			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=restart</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_restart_selected_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=restart_all</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_restart_all_confirm}}</span>
					<span class="js_param">requires_selection=false</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_all</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_delete_all_confirm}}</span>
					<span class="js_param">requires_selection=false</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_failed</span>
					<span class="js_param">confirm={{$lang.settings.background_task_batch_delete_failed_confirm}}</span>
					<span class="js_param">requires_selection=false</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}