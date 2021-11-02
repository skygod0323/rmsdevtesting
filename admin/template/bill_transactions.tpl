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

{{if in_array('billing|edit_all',$smarty.session.permissions)}}
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
					<a href="{{$page_name}}">{{$lang.users.submenu_option_billing_transactions}}</a>
					/
					{{if $smarty.get.action=='add_new'}}
						{{$lang.users.bill_transaction_add}}
					{{else}}
						{{$lang.users.bill_transaction_edit|replace:"%1%":$smarty.post.transaction_id}}
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.bill_transaction_field_bill_type}}:</td>
			<td class="de_control" colspan="3">
				{{if $smarty.get.action=='add_new'}}
					{{$lang.users.bill_transaction_field_bill_type_manual}}
				{{else}}
					{{if $smarty.post.bill_type_id==5}}
						{{$lang.users.bill_transaction_field_bill_type_htpasswd}}
					{{elseif $smarty.post.bill_type_id==4}}
						{{$lang.users.bill_transaction_field_bill_type_api}}
					{{elseif $smarty.post.bill_type_id==3}}
						{{$lang.users.bill_transaction_field_bill_type_sms}} {{if $smarty.post.internal_provider!=''}}({{$smarty.post.internal_provider}}){{/if}}
					{{elseif $smarty.post.bill_type_id==2}}
						{{$lang.users.bill_transaction_field_bill_type_card}} {{if $smarty.post.internal_provider!=''}}({{$smarty.post.internal_provider}}){{/if}}
					{{elseif $smarty.post.bill_type_id==1}}
						{{$lang.users.bill_transaction_field_bill_type_manual}}
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action=='add_new'}}
			<tr>
				<td class="de_label de_required">{{$lang.users.bill_transaction_field_user}} (*):</td>
				<td class="de_control">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight_users.php</span>
						</div>
						<input type="text" name="user" maxlength="255" class="fixed_200" value="{{$smarty.request.user}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.users.bill_transaction_field_user_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="option_unlimited">{{$lang.users.bill_transaction_field_access_type}}:</div>
					<div class="option_duration option_tokens de_required">{{$lang.users.bill_transaction_field_access_type}} (*):</div>
				</td>
				<td class="de_control">
					<div class="de_vis_sw_radio">
						<table class="control_group">
							<tr>
								<td>
									<div class="de_lv_pair"><input id="option_unlimited" type="radio" name="access_type" value="1" {{if $smarty.post.duration==0 && $smarty.post.tokens==0}}checked="checked"{{/if}}/><label>{{$lang.users.bill_transaction_field_access_type_unlimited}}</label></div>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.users.bill_transaction_field_access_type_unlimited_hint}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input id="option_duration" type="radio" name="access_type" value="2" {{if $smarty.post.duration!=0 && $smarty.post.tokens==0}}checked="checked"{{/if}}/><label>{{$lang.users.bill_transaction_field_access_type_duration}}:</label></div>
									<input type="text" name="duration" maxlength="10" class="fixed_width_150 option_duration" value="{{$smarty.post.duration}}"/>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.users.bill_transaction_field_access_type_duration_hint}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input id="option_tokens" type="radio" name="access_type" value="3" {{if $smarty.post.tokens>0}}checked="checked"{{/if}}/><label>{{$lang.users.bill_transaction_field_access_type_tokens}}:</label></div>
									<input type="text" name="tokens" maxlength="10" class="fixed_width_150 option_tokens" value="{{$smarty.post.tokens}}"/>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.users.bill_transaction_field_access_type_tokens_hint}}</span>
									{{/if}}
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_description}}:</td>
				<td class="de_control">
					<textarea name="transaction_log" class="dyn_full_size" rows="4" cols="40"></textarea>
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_type}}:</td>
				<td class="de_control">
					{{if $smarty.post.type_id==10}}
						{{$lang.users.bill_transaction_field_type_tokens}} ({{$smarty.post.tokens_granted}})
					{{elseif $smarty.post.type_id==6}}
						{{$lang.users.bill_transaction_field_type_void}}
					{{elseif $smarty.post.type_id==5}}
						{{$lang.users.bill_transaction_field_type_refund}}
					{{elseif $smarty.post.type_id==4}}
						{{$lang.users.bill_transaction_field_type_chargeback}}
					{{elseif $smarty.post.type_id==3}}
						{{$lang.users.bill_transaction_field_type_rebill}}
					{{elseif $smarty.post.type_id==2}}
						{{$lang.users.bill_transaction_field_type_conversion}}
					{{elseif $smarty.post.type_id==1}}
						{{$lang.users.bill_transaction_field_type_initial}} {{if $smarty.post.is_trial==1}}({{$lang.users.bill_transaction_field_type_initial_trial}}){{/if}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_status}}:</td>
				<td class="de_control">
					{{if $smarty.post.status_id=='1'}}
						<select name="status_id">
							<option value="1" {{if $smarty.post.status_id==1}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_open}}</option>
							<option value="3" {{if $smarty.post.status_id==3}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_cancelled}}</option>
						</select>
					{{elseif $smarty.post.status_id=='0'}}
						{{$lang.users.bill_transaction_field_status_approval}}
					{{elseif $smarty.post.status_id=='2'}}
						{{if $smarty.post.tokens_granted>0}}
							<select name="status_id">
								<option value="2" {{if $smarty.post.status_id==2}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_closed}}</option>
								<option value="3" {{if $smarty.post.status_id==3}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_cancelled}}</option>
							</select>
						{{else}}
							{{$lang.users.bill_transaction_field_status_closed}}
						{{/if}}
					{{elseif $smarty.post.status_id=='4'}}
						{{$lang.users.bill_transaction_field_status_pending}}
					{{else}}
						{{$lang.users.bill_transaction_field_status_cancelled}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_user}}:</td>
				<td class="de_control">
					{{if $smarty.post.user_id==0}}
						{{$lang.users.bill_transaction_field_user_waiting}}
					{{else}}
						{{if $smarty.post.user!=''}}
							{{if in_array('users|view',$smarty.session.permissions)}}
								<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
							{{else}}
								{{$smarty.post.user}}
							{{/if}}
						{{else}}
							{{$lang.common.user_deleted|replace:"%1%":$smarty.post.user_id}}
						{{/if}}
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.access_code!=''}}
				<tr>
					<td class="de_label">{{$lang.users.bill_transaction_field_access_code}}:</td>
					<td class="de_control">{{$smarty.post.access_code}}</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_start_date}}:</td>
				<td class="de_control">
					{{if $smarty.post.access_start_date=='0000-00-00 00:00:00'}}
						{{$lang.common.undefined}}
					{{else}}
						{{$smarty.post.access_start_date|date_format:$smarty.session.userdata.full_date_format}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_end_date}}:</td>
				<td class="de_control">
					{{if $smarty.post.is_unlimited_access==1}}
						{{$lang.users.bill_transaction_field_end_date_unlimited}}
					{{elseif $smarty.post.access_end_date=='0000-00-00 00:00:00'}}
						{{$lang.common.undefined}}
					{{else}}
						{{$smarty.post.access_end_date|date_format:$smarty.session.userdata.full_date_format}} {{if $smarty.post.duration_rebill>0}}({{$lang.users.bill_transaction_field_end_date_rebillable|replace:"%1%":$smarty.post.duration_rebill}}){{/if}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.bill_transaction_field_log}}:</td>
				<td class="de_control">
					<textarea name="transaction_log" class="dyn_full_size" rows="10" cols="40" readonly="readonly">{{$smarty.post.transaction_log}}</textarea>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action=='add_new' || (($smarty.post.status_id==1 || ($smarty.post.status_id==2 && $smarty.post.tokens_granted>0)) && $can_edit_all==1)}}
			<tr>
				<td class="de_action_group" colspan="4">
					{{if $smarty.get.action=='add_new'}}
						{{if $smarty.session.save.options.default_save_button==1}}
							<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
							<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
						{{else}}
							<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
							<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
						{{/if}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control dgf_search">
						<input type="text" name="se_text" size="20" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}"/>
						{{if count($search_fields)>0}}
							<div class="dgf_search_layer hidden">
								<span>{{$lang.common.dg_filter_search_in}}:</span>
								<ul>
									{{assign var="search_everywhere" value="true"}}
									{{foreach from=$search_fields|smarty:nodefaults item="field"}}
										<li>
											{{assign var="option_id" value="se_text_`$field.id`"}}
											<input type="hidden" name="{{$option_id}}" value="0"/>
											<div class="dg_lv_pair"><input type="checkbox" name="{{$option_id}}" value="1" {{if $smarty.session.save.$page_name[$option_id]==1}}checked="checked"{{/if}}/><label>{{$field.title}}</label></div>
											{{if $smarty.session.save.$page_name[$option_id]!=1}}
												{{assign var="search_everywhere" value="false"}}
											{{/if}}
										</li>
									{{/foreach}}
									<li class="dgf_everywhere">
										<div class="dg_lv_pair"><input type="checkbox" name="se_text_all" value="1" {{if $search_everywhere=='true'}}checked="checked"{{/if}} class="dgf_everywhere"/><label>{{$lang.common.dg_filter_search_in_everywhere}}</label></div>
									</li>
								</ul>
							</div>
						{{/if}}
					</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_approval}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_open}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_closed}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_cancelled}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_status_id=='4'}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_status_pending}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_field_user}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_bill_type_id}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_field_bill_type}}:</td>
					<td class="dgf_control">
						<select name="se_bill_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_bill_type_id==1}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_bill_type_manual}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_bill_type_id==2}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_bill_type_card}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_bill_type_id==3}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_bill_type_sms}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_bill_type_id==4}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_bill_type_api}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_bill_type_id==5}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_bill_type_htpasswd}}</option>
							{{foreach item=item from=$list_providers|smarty:nodefaults}}
								<option value="{{$item.internal_id}}" {{if $smarty.session.save.$page_name.se_bill_type_id==$item.internal_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_type_id>0}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_field_type}}:</td>
					<td class="dgf_control">
						<select name="se_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_type_id==1}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_initial}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_type_id==2}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_conversion}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_type_id==3}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_rebill}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_type_id==4}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_chargeback}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_type_id==5}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_refund}}</option>
							<option value="6" {{if $smarty.session.save.$page_name.se_type_id==6}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_void}}</option>
							<option value="10" {{if $smarty.session.save.$page_name.se_type_id==10}}selected="selected"{{/if}}>{{$lang.users.bill_transaction_field_type_tokens}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_start_date_from>0}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_filter_start_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_start_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_start_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_start_date_from_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_start_date_to>0}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_filter_start_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_start_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_start_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_start_date_to_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_end_date_from>0}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_filter_end_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_end_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_end_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_end_date_from_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_end_date_to>0}}dgf_selected{{/if}}">{{$lang.users.bill_transaction_filter_end_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_end_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_end_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_end_date_to_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
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
							{{if $can_edit_all==1 && ($item.status_id==1 || ($item.status_id==2 && $item.tokens_granted>0))}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">user={{$item.user|default:$item.user_id}}</span>
									</span>
								</a>
							{{/if}}
						</td>
					</tr>
				{{/foreach}}
			</table>
			{{if $can_edit_all==1}}
				<ul class="dg_additional_menu_template">
					<li class="js_params">
						<span class="js_param">href=?batch_action=cancel&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.users.bill_transaction_action_cancel}}</span>
						<span class="js_param">confirm={{$lang.users.bill_transaction_action_cancel_confirm|replace:"%1%":'${id}'|replace:"%2%":'${user}'}}</span>
					</li>
				</ul>
			{{/if}}
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>

{{include file="navigation.tpl"}}
{{/if}}