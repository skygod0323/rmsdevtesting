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
			<td class="de_header" colspan="4"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_feeds_log}}</a> / {{$lang.settings.feeds_log_view}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.feeds_log_field_message}}:</td>
			<td class="de_control">{{$smarty.post.message_text}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.feeds_log_field_details}}:</td>
			<td class="de_control"><textarea name="comment" class="dyn_full_size" rows="10" cols="40">{{$smarty.post.message_details}}</textarea></td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_show_id!=''}}dgf_selected{{/if}}">{{$lang.settings.dg_feeds_log_filter_message_type}}:</td>
					<td class="dgf_control">
						<select name="se_show_id">
							<option value="" {{if $smarty.session.save.$page_name.se_show_id==''}}selected="selected"{{/if}}>{{$lang.settings.dg_feeds_log_filter_message_type_infos_and_errors}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_show_id=='1'}}selected="selected"{{/if}}>{{$lang.settings.dg_feeds_log_filter_message_type_errors}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_show_id=='2'}}selected="selected"{{/if}}>{{$lang.settings.dg_feeds_log_filter_message_type_all}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_feed_id>0}}dgf_selected{{/if}}">{{$lang.settings.dg_feeds_log_filter_feed}}:</td>
					<td class="dgf_control">
						<select name="se_feed_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item=item from=$list_feeds|smarty:nodefaults}}
								<option value="{{$item.feed_id}}" {{if $smarty.session.save.$page_name.se_feed_id==$item.feed_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_from>0}}dgf_selected{{/if}}">{{$lang.settings.dg_feeds_log_filter_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_to>0}}dgf_selected{{/if}}">{{$lang.settings.dg_feeds_log_filter_date_to}}:</td>
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
					<col width="10%"/>
					<col/>
					<col/>
					<col/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					<td>{{$lang.settings.dg_feeds_log_col_feed}}</td>
					<td>{{$lang.settings.dg_feeds_log_col_message_type}}</td>
					<td>{{$lang.settings.dg_feeds_log_col_message}}</td>
					<td>{{$lang.settings.dg_feeds_log_col_datetime}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
					<td class="nowrap">
						{{if $item.feed_title!=''}}
							{{$item.feed_title}}
						{{else}}
							{{$lang.settings.dg_feeds_log_col_feed_deleted|replace:"%1%":$item.feed_id}}
						{{/if}}
					</td>
					<td class="nowrap">
						{{if $item.message_type==0}}
							{{$lang.settings.dg_feeds_log_col_message_type_debug}}
						{{elseif $item.message_type==1}}
							{{$lang.settings.dg_feeds_log_col_message_type_info}}
						{{elseif $item.message_type==2}}
							<span class="highlighted_text">{{$lang.settings.dg_feeds_log_col_message_type_error}}</span>
						{{/if}}
					</td>
					<td>
						{{if $item.message_details!=''}}
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}">{{$item.message_text}}</a>
						{{else}}
							{{$item.message_text}}
						{{/if}}
					</td>
					<td class="nowrap">{{$item.added_date|date_format:$smarty.session.userdata.full_date_format}}</td>
				</tr>
				{{/foreach}}
		   </table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}