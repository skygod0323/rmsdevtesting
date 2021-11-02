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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.settings.dg_activity_log_filter_ip}}:</td>
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
					<td class="dgf_advanced_link">
						<a href="javascript:stub()" class="dgf_filters {{if $table_filtered==1}}dgf_selected{{/if}}">{{$lang.common.dg_filter_filters}}</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_filters {{if $table_filtered==0}}hidden{{/if}}">
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user_id>0}}dgf_selected{{/if}}">{{$lang.settings.dg_activity_log_filter_user}}:</td>
					<td class="dgf_control">
						<select name="se_user_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_users|smarty:nodefaults}}
								<option value="{{$item.user_id}}" {{if $item.user_id==$smarty.session.save.$page_name.se_user_id}}selected="selected"{{/if}}>{{$item.login}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_from>0}}dgf_selected{{/if}}">{{$lang.settings.dg_activity_log_filter_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_to>0}}dgf_selected{{/if}}">{{$lang.settings.dg_activity_log_filter_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_to_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				 </tr>
			</table>
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
					<col/>
					<col width="20%"/>
					<col width="20%"/>
					<col width="15%"/>
					<col width="15%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					{{assign var=sort_field value="user_login"}}{{assign var=sort_field_name value=$lang.settings.dg_activity_log_col_user}}
					<td><a href="{{$page_name}}?sort_by={{$sort_field}}&amp;sort_direction={{if $smarty.session.save.$page_name.sort_by!=$sort_field}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}" class="dg_sort{{if $smarty.session.save.$page_name.sort_by==$sort_field}}_{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{/if}}">{{$sort_field_name}}</a></td>
					{{assign var=sort_field value="login_date"}}{{assign var=sort_field_name value=$lang.settings.dg_activity_log_col_login_time}}
					<td><a href="{{$page_name}}?sort_by={{$sort_field}}&amp;sort_direction={{if $smarty.session.save.$page_name.sort_by!=$sort_field}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}" class="dg_sort{{if $smarty.session.save.$page_name.sort_by==$sort_field}}_{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{/if}}">{{$sort_field_name}}</a></td>
					{{assign var=sort_field value="last_request_date"}}{{assign var=sort_field_name value=$lang.settings.dg_activity_log_col_last_request_time}}
					<td><a href="{{$page_name}}?sort_by={{$sort_field}}&amp;sort_direction={{if $smarty.session.save.$page_name.sort_by!=$sort_field}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}" class="dg_sort{{if $smarty.session.save.$page_name.sort_by==$sort_field}}_{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{/if}}">{{$sort_field_name}}</a></td>
					{{assign var=sort_field value="duration"}}{{assign var=sort_field_name value=$lang.settings.dg_activity_log_col_duration}}
					<td><a href="{{$page_name}}?sort_by={{$sort_field}}&amp;sort_direction={{if $smarty.session.save.$page_name.sort_by!=$sort_field}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}" class="dg_sort{{if $smarty.session.save.$page_name.sort_by==$sort_field}}_{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{/if}}">{{$sort_field_name}}</a></td>
					{{assign var=sort_field value="ip"}}{{assign var=sort_field_name value=$lang.settings.dg_activity_log_col_ip}}
					<td><a href="{{$page_name}}?sort_by={{$sort_field}}&amp;sort_direction={{if $smarty.session.save.$page_name.sort_by!=$sort_field}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}" class="dg_sort{{if $smarty.session.save.$page_name.sort_by==$sort_field}}_{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{/if}}">{{$sort_field_name}}</a></td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
					<td>
						{{if $item.user_login!=''}}
							{{if $item.is_superadmin==0 && $smarty.session.userdata.is_superadmin>0}}
								<a href="admin_users.php?action=change&amp;item_id={{$item.user_id}}">{{$item.user_login}}</a>
							{{else}}
								{{$item.user_login}}
							{{/if}}
						{{else}}
							{{$lang.settings.dg_activity_log_col_user_na}}
						{{/if}}
					</td>
					<td class="nowrap">{{$item.login_date|date_format:$smarty.session.userdata.full_date_format}}</td>
					<td class="nowrap">{{$item.last_request_date|date_format:$smarty.session.userdata.full_date_format}}</td>
					<td>{{$item.duration_str}}</td>
					<td>{{$item.ip}}</td>
				</tr>
				{{/foreach}}
		   </table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}