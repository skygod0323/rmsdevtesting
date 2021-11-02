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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.audit_log_analyzer.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.audit_log_analyzer.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.audit_log_analyzer.divider_parameters}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.audit_log_analyzer.field_period}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td><div class="de_lv_pair"><input type="radio" id="pt_today" name="period_type" value="1" {{if $smarty.post.period_type==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_today}} ({{$smarty.post.today|date_format:$smarty.session.userdata.short_date_format}})</label></div></td>
						</tr>
						<tr>
							<td><div class="de_lv_pair"><input type="radio" id="pt_yesterday" name="period_type" value="2" {{if $smarty.post.period_type==2}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_yesterday}} ({{$smarty.post.yesterday|date_format:$smarty.session.userdata.short_date_format}})</label></div></td>
						</tr>
						<tr>
							<td><div class="de_lv_pair"><input type="radio" id="pt_last_week" name="period_type" value="3" {{if $smarty.post.period_type==3}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_last_7days}} ({{$smarty.post.week|date_format:$smarty.session.userdata.short_date_format}} - {{$smarty.post.yesterday|date_format:$smarty.session.userdata.short_date_format}})</label></div></td>
						</tr>
						<tr>
							<td><div class="de_lv_pair"><input type="radio" id="pt_last_month" name="period_type" value="4" {{if $smarty.post.period_type==4}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_last_30days}} ({{$smarty.post.month|date_format:$smarty.session.userdata.short_date_format}} - {{$smarty.post.yesterday|date_format:$smarty.session.userdata.short_date_format}})</label></div></td>
						</tr>
						<tr>
							<td><div class="de_lv_pair"><input type="radio" id="pt_prev_month" name="period_type" value="6" {{if $smarty.post.period_type==6}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_prev_month}} ({{$smarty.post.month_start|date_format:$smarty.session.userdata.short_date_format}} - {{$smarty.post.month_end|date_format:$smarty.session.userdata.short_date_format}})</label></div></td>
						</tr>
						<tr>
							<td><div class="de_lv_pair"><input type="radio" id="pt_custom" name="period_type" value="5" {{if $smarty.post.period_type==5}}checked="checked"{{/if}}/><label>{{$lang.plugins.audit_log_analyzer.field_period_type_custom}}</label></div></td>
						</tr>
						<tr>
							<td>
								{{if $smarty.post.period_type==5}}
									{{$lang.plugins.audit_log_analyzer.field_period_type_custom_from}}: {{html_select_date prefix='period_custom_date_from_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$smarty.post.period_custom_date_from all_extra='class="pt_custom"'}}&nbsp;
									{{$lang.plugins.audit_log_analyzer.field_period_type_custom_to}}: {{html_select_date prefix='period_custom_date_to_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$smarty.post.period_custom_date_to all_extra='class="pt_custom"'}}
								{{else}}
									{{$lang.plugins.audit_log_analyzer.field_period_type_custom_from}}: {{html_select_date prefix='period_custom_date_from_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$smarty.post.period_custom_date_from all_extra='class="pt_custom" disabled="disabled"'}}&nbsp;
									{{$lang.plugins.audit_log_analyzer.field_period_type_custom_to}}: {{html_select_date prefix='period_custom_date_to_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$smarty.post.period_custom_date_to all_extra='class="pt_custom" disabled="disabled"'}}
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.audit_log_analyzer.field_admins}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_admins.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=admin_ids[]</span>
						<span class="js_param">empty_message={{$lang.plugins.audit_log_analyzer.field_admins_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.admins|smarty:nodefaults}}
						<input type="hidden" name="admin_ids[]" value="{{$item.user_id}}" alt="{{$item.login}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_user" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.plugins.audit_log_analyzer.field_admins_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.audit_log_analyzer.field_users}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=user_ids[]</span>
						<span class="js_param">empty_message={{$lang.plugins.audit_log_analyzer.field_users_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.users|smarty:nodefaults}}
						<input type="hidden" name="user_ids[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_user" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.plugins.audit_log_analyzer.field_users_all}}"/>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.audit_log_analyzer.btn_calculate}}"/>
			</td>
		</tr>
		{{if $smarty.get.action=='results'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.audit_log_analyzer.divider_summary}}</div></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<tr class="eg_header fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.field_results_period}}: {{$smarty.post.period_start|date_format:$smarty.session.userdata.full_date_format}} - {{$smarty.post.period_end|date_format:$smarty.session.userdata.full_date_format}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.username}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_added}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.videos_added}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_added}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.albums_added}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_posts_added}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.posts_added}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_added}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.other_added}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_modified}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.videos_modified}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_modified}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.albums_modified}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_posts_modified}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.posts_modified}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_modified}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.other_modified}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_vs_modified}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.vs_modified}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_ai_modified}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.ai_modified}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_deleted}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.videos_deleted}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_deleted}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.albums_deleted}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_posts_deleted}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.posts_deleted}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_deleted}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.other_deleted}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_videos_translated}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.videos_translated}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_albums_translated}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.albums_translated}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_other_translated}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.other_translated}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_text_symbols}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.text_symbols}}</td>
							{{/foreach}}
						</tr>
						<tr class="eg_data fixed_height_30">
							<td>{{$lang.plugins.audit_log_analyzer.dg_results_col_translation_symbols}}</td>
							{{foreach item=item from=$smarty.post.result|smarty:nodefaults}}
								<td>{{$item.translation_symbols}}</td>
							{{/foreach}}
						</tr>
					</table>
				</td>
			</tr>
		{{/if}}
	</table>
</form>