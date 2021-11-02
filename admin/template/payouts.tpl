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

{{if in_array('payouts|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="4">
				<div>
					<a href="{{$page_name}}">{{$lang.users.submenu_option_payouts_list}}</a>
					/
					{{if $smarty.get.action=='add_new'}}
						{{$lang.users.payout_add}}
					{{else}}
						{{$lang.users.payout_edit|replace:"%1%":$smarty.post.payout_id}}
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.payout_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.payout_field_status}}:</td>
			<td class="de_control">
				<select name="status_id" {{if $smarty.post.status_id!=1}}disabled="disabled"{{/if}}>
					<option value="1" {{if $smarty.post.status_id==1}}selected="selected"{{/if}}>{{$lang.users.payout_field_status_in_progress}}</option>
					<option value="2" {{if $smarty.post.status_id==2}}selected="selected"{{/if}}>{{$lang.users.payout_field_status_closed}}</option>
					<option value="3" {{if $smarty.post.status_id==3}}selected="selected"{{/if}}>{{$lang.users.payout_field_status_cancelled}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.payout_field_status_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.payout_field_awards}}:</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new'}}
					{{foreach key="key" item="item" from=$list_all_award_types|smarty:nodefaults}}
						<div class="de_lv_pair"><input type="checkbox" name="award_types[]" value="{{$key}}" {{if in_array($key, $smarty.post.award_types)}}checked="checked"{{/if}}/><label>{{$item}}</label></div>
					{{/foreach}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.payout_field_awards_hint}}</span>
					{{/if}}
				{{else}}
					{{foreach item="item" name="data" from=$smarty.post.award_types|smarty:nodefaults}}
						{{foreach key="key" item="item_type" from=$list_all_award_types|smarty:nodefaults}}
							{{if $item==$key}}
								{{$item_type}}{{if !$smarty.foreach.data.last}}, {{/if}}
							{{/if}}
						{{/foreach}}
					{{/foreach}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.payout_field_awards_hint}}</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.payout_field_description}}:</td>
			<td class="de_control">
				<textarea name="description" class="dyn_full_size" rows="3">{{$smarty.post.description}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.payout_field_description_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.payout_field_exclude_users}}:</td>
			<td class="de_control">
				{{if $smarty.get.action!='add_new'}}
					{{$smarty.post.excluded_users|default:$lang.common.users_empty}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.payout_field_exclude_users_hint}}</span>
					{{/if}}
				{{else}}
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_users.php</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=excluded_users[]</span>
							<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name=data item=item from=$smarty.post.excluded_users|smarty:nodefaults}}
							<input type="hidden" name="excluded_users[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_user" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.common.users_all}}"/>
						</div>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.users.payout_field_exclude_users_hint}}</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.payout_field_include_users}}:</td>
			<td class="de_control">
				{{if $smarty.get.action!='add_new'}}
					{{$smarty.post.included_users|default:$lang.common.users_empty}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.payout_field_include_users_hint}}</span>
					{{/if}}
				{{else}}
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_users.php</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=included_users[]</span>
							<span class="js_param">empty_message={{$lang.common.users_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name=data item=item from=$smarty.post.included_users|smarty:nodefaults}}
							<input type="hidden" name="included_users[]" value="{{$item.user_id}}" alt="{{$item.username}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_user" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.common.users_all}}"/>
						</div>
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.users.payout_field_include_users_hint}}</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.users.payout_field_conversion}}{{if $smarty.get.action=='add_new'}} (*){{/if}}:</td>
			<td class="de_control">
				<input type="text" name="conversion" maxlength="20" class="fixed_100" value="{{$smarty.post.conversion|replace:",":"."}}" {{if $smarty.get.action!='add_new'}}readonly="readonly"{{/if}}/>
				{{if $smarty.get.action!='add_new'}}
					{{$smarty.post.conversion_currency}}
				{{else}}
					<select name="conversion_currency">
						<option value="USD" {{if $smarty.post.conversion_currency=='USD'}}selected="selected"{{/if}}>USD</option>
						<option value="EUR" {{if $smarty.post.conversion_currency=='EUR'}}selected="selected"{{/if}}>EUR</option>
						<option value="GBP" {{if $smarty.post.conversion_currency=='GBP'}}selected="selected"{{/if}}>GBP</option>
						<option value="AUD" {{if $smarty.post.conversion_currency=='AUD'}}selected="selected"{{/if}}>AUD</option>
						<option value="CAD" {{if $smarty.post.conversion_currency=='CAD'}}selected="selected"{{/if}}>CAD</option>
						<option value="CHF" {{if $smarty.post.conversion_currency=='CHF'}}selected="selected"{{/if}}>CHF</option>
						<option value="DKK" {{if $smarty.post.conversion_currency=='DKK'}}selected="selected"{{/if}}>DKK</option>
						<option value="NOK" {{if $smarty.post.conversion_currency=='NOK'}}selected="selected"{{/if}}>NOK</option>
						<option value="SEK" {{if $smarty.post.conversion_currency=='SEK'}}selected="selected"{{/if}}>SEK</option>
						<option value="RUB" {{if $smarty.post.conversion_currency=='RUB'}}selected="selected"{{/if}}>RUB</option>
					</select>
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.payout_field_conversion_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label {{if $smarty.get.action=='add_new'}}de_required{{/if}}">{{$lang.users.payout_field_min_tokens_limit}}{{if $smarty.get.action=='add_new'}} (*){{/if}}:</td>
			<td class="de_control">
				<input type="text" name="min_tokens_limit" maxlength="20" class="fixed_100" value="{{$smarty.post.min_tokens_limit}}" {{if $smarty.get.action!='add_new'}}readonly="readonly"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.payout_field_min_tokens_limit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.payout_field_gateway}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="gateway" name="gateway" {{if $smarty.get.action!='add_new'}}disabled="disabled"{{/if}}>
						<option value="manual" {{if $smarty.post.gateway=='manual'}}selected="selected"{{/if}}>{{$lang.users.payout_field_gateway_manual}}</option>
						<option value="paypal" {{if $smarty.post.gateway=='paypal'}}selected="selected"{{/if}}>{{$lang.users.payout_field_gateway_paypal}}</option>
					</select>
					{{if $smarty.get.action!='add_new' && $smarty.post.gateway!='manual'}}
						&nbsp;
						<a href="?action=instructions&amp;item_id={{$smarty.post.payout_id}}">{{$lang.users.payout_field_gateway_download}}</a>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint gateway_manual">{{$lang.users.payout_field_gateway_manual_hint}}</span>
					<span class="de_hint gateway_paypal">{{$lang.users.payout_field_gateway_paypal_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.users.payout_field_tokens}}:</td>
				<td class="de_control">
					<input type="text" maxlength="20" class="fixed_100" value="{{$smarty.post.tokens}}" readonly="readonly"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.payout_field_tokens_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.payout_field_amount}}:</td>
				<td class="de_control">
					<input type="text" maxlength="20" class="fixed_100" value="{{$smarty.post.amount|replace:",":"."}}" readonly="readonly"/>
					{{$smarty.post.conversion_currency}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.payout_field_amount_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.payout_field_comment}}:</td>
			<td class="de_control">
				<input type="text" name="comment" class="dyn_full_size" maxlength="255" value="{{$smarty.post.comment}}" {{if $smarty.get.action!='add_new' && $smarty.post.status_id!=1}}readonly="readonly"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.payout_field_comment_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.last_comment!=''}}
			<tr>
				<td class="de_label">{{$lang.users.payout_field_last_comment}}:</td>
				<td class="de_control">
					{{$smarty.post.last_comment|replace:"\n":"<br/>"}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.payout_divider_users}}</div></td>
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
					</colgroup>
					<tr class="eg_header">
						<td class="eg_selector"><div><input type="checkbox" {{if $smarty.post.status_id!=1}}disabled="disabled"{{/if}}/> {{$lang.users.payout_field_delete}}</div></td>
						<td>{{$lang.users.payout_field_user}}</td>
						<td>
							{{if $smarty.post.gateway=='paypal'}}
								{{$lang.users.payout_field_gateway_paypal}}
							{{/if}}
						</td>
						<td>{{$lang.users.payout_field_tokens}}</td>
						<td>{{$lang.users.payout_field_amount}}</td>
					</tr>
					{{if count($smarty.post.user_payments)>0}}
						{{foreach item=item from=$smarty.post.user_payments|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td class="eg_selector"><input type="checkbox" name="delete_user[]" value="{{$item.user_id}}" {{if $smarty.post.status_id!=1}}disabled="disabled"{{/if}}/></td>
								<td class="nowrap">
									{{if $item.username==''}}
										{{$lang.users.payout_field_user_deleted|replace:"%1%":$item.user_id}}
									{{else}}
										{{if in_array('users|view',$smarty.session.permissions)}}
											<a href="users.php?action=change&amp;item_id={{$item.user_id}}">{{$item.username}}</a>
										{{else}}
											{{$item.username}}
										{{/if}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{$item.account}}
								</td>
								<td class="nowrap">
									{{$item.tokens}}
								</td>
								<td class="nowrap">
									{{$item.amount}} {{$item.amount_currency}}
								</td>
							</tr>
						{{/foreach}}
					{{elseif $smarty.get.action=='add_new'}}
						<tr class="eg_data fixed_height_30">
							<td colspan="5">{{$lang.users.payout_divider_users_hint}}</td>
						</tr>
					{{/if}}
				</table>
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="4">
					{{if $smarty.get.action=='add_new'}}
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
					{{else}}
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					{{/if}}
				</td>
			</tr>
		{{/if}}
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
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.users.payout_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.payout_field_status_in_progress}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected="selected"{{/if}}>{{$lang.users.payout_field_status_closed}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected="selected"{{/if}}>{{$lang.users.payout_field_status_cancelled}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.users.payout_field_user}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_users.php</span>
							</div>
							<input type="text" name="se_user" size="20" value="{{$smarty.session.save.$page_name.se_user}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_from>0}}dgf_selected{{/if}}">{{$lang.users.payout_filter_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_to>0}}dgf_selected{{/if}}">{{$lang.users.payout_filter_date_to}}:</td>
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
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==3}}disabled{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
					</td>
				</tr>
				{{/foreach}}
			</table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>

{{include file="navigation.tpl"}}
{{/if}}