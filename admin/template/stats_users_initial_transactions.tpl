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
					<td class="dgf_label">{{$lang.stats.users_initial_transactions_filter_group_by}}:</td>
					<td class="dgf_control">
						<select name="se_group_by" class="dgf_switcher">
							<option value="ip" {{if $smarty.session.save.$page_name.se_group_by=='ip'}}selected="selected"{{/if}}>{{$lang.stats.users_initial_transactions_filter_group_by_ip}}</option>
							<option value="log" {{if $smarty.session.save.$page_name.se_group_by=='log'}}selected="selected"{{/if}}>{{$lang.stats.users_initial_transactions_filter_group_by_full}}</option>
						</select>
					</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_ip!=''}}dgf_selected{{/if}}">{{$lang.stats.users_initial_transactions_field_ip}}:</td>
					<td class="dgf_control"><input type="text" name="se_ip" size="20" value="{{$smarty.session.save.$page_name.se_ip}}"/></td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_country!=''}}dgf_selected{{/if}}">{{$lang.stats.users_initial_transactions_field_country}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_countries.php</span>
							</div>
							<input type="text" name="se_country" size="20" value="{{$smarty.session.save.$page_name.se_country}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_reseller_code!=''}}dgf_selected{{/if}}">{{$lang.stats.users_initial_transactions_field_reseller_code}}:</td>
					<td class="dgf_control"><input type="text" name="se_reseller_code" size="20" value="{{$smarty.session.save.$page_name.se_reseller_code}}"/></td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_period_id>0}}dgf_selected{{/if}}">{{$lang.stats.common_period}}:</td>
					<td class="dgf_control">
						<div class="de_vis_sw_select">
							<select id="se_period_id" name="se_period_id" class="dgf_date_period">
								<option value="">{{$lang.common.dg_filter_option_all}}</option>
								<option value="1" {{if $smarty.session.save.$page_name.se_period_id==1}}selected="selected"{{/if}}>{{$lang.stats.common_period_days7}}</option>
								<option value="2" {{if $smarty.session.save.$page_name.se_period_id==2}}selected="selected"{{/if}}>{{$lang.stats.common_period_days30}}</option>
								<option value="3" {{if $smarty.session.save.$page_name.se_period_id==3}}selected="selected"{{/if}}>{{$lang.stats.common_period_current_month}}</option>
								<option value="4" {{if $smarty.session.save.$page_name.se_period_id==4}}selected="selected"{{/if}}>{{$lang.stats.common_period_prev_month}}</option>
								<option value="5" {{if $smarty.session.save.$page_name.se_period_id==5}}selected="selected"{{/if}}>{{$lang.stats.common_period_custom}}</option>
							</select>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter se_period_id_5">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_from>0}}dgf_selected{{/if}}">{{$lang.stats.common_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter se_period_id_5">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_to>0}}dgf_selected{{/if}}">{{$lang.stats.common_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_to_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				 </tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_columns hidden">
			{{assign var="table_columns_display_mode" value="selector"}}
			{{include file="table_columns_inc.tpl"}}
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					{{assign var="table_columns_display_mode" value="sizes"}}
					{{include file="table_columns_inc.tpl"}}
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
				</tr>
				{{assign var="table_columns_average" value=$average}}
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
					</tr>
				{{/foreach}}
				{{foreach name=data item=item from=$total|smarty:nodefaults}}
					<tr class="dg_total">
						<td class="dg_selector"></td>
						{{assign var="table_columns_display_mode" value="summary"}}
						{{assign var="table_columns_summary_field_name" value=$table_summary_field_name}}
						{{include file="table_columns_inc.tpl"}}
					</tr>
				{{/foreach}}
		   </table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}