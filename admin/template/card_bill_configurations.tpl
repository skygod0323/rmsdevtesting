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
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_card_billing}}</a> / {{$lang.users.card_bill_config_edit|replace:"%1%":$smarty.post.title}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/991-how-to-add-custom-payment-processor-in-kvs-5-0">How to add custom payment processor</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_config_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.card_bill_config_field_processor_type}}:</td>
			<td class="de_control"><a href="{{$smarty.post.url}}" rel="external">{{$smarty.post.title}}</a></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.card_bill_config_field_features}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" disabled="disabled" {{if $smarty.post.cf_pkg_rebills==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_config_field_features_rebills}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" disabled="disabled" {{if $smarty.post.cf_pkg_trials==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_config_field_features_trials}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" disabled="disabled" {{if $smarty.post.cf_pkg_tokens==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_config_field_features_tokens}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" disabled="disabled" {{if $smarty.post.cf_pkg_setprice==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_config_field_features_dynamic_pricing}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" disabled="disabled" {{if $smarty.post.cf_pkg_oneclick==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_config_field_features_one_click}}</label></div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.card_bill_config_field_status}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="status_id" name="status_id">
						<option value="0" {{if $smarty.post.status_id=='0'}}selected="selected"{{/if}}>{{$lang.users.card_bill_config_field_status_disabled}}</option>
						<option value="1" {{if $smarty.post.status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.card_bill_config_field_status_active}}</option>
					</select>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.users.card_bill_config_field_status_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.card_bill_config_field_default}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_default" value="1" class="status_id_1" {{if $smarty.post.is_default==1}}checked="checked"{{/if}}/><label class="status_id_1">{{$lang.users.card_bill_config_field_default}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.card_bill_config_field_default_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.cf_pkg_trials==1}}
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_trials}}:</td>
				<td class="de_control">
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_trials_as_active" name="options[is_trials_as_active]" value="1" {{if $smarty.post.options.is_trials_as_active==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_config_field_trials_as_active}}</label></div>
					<input type="text" name="options[trial_tokens]" class="is_trials_as_active_on fixed_100" value="{{$smarty.post.options.trial_tokens|default:"0"}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.card_bill_config_field_trials_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.internal_id!='tokens'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_config_divider_postback}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_postback_url}}:</td>
				<td class="de_control">{{$config.project_url}}/admin/billings/{{$smarty.post.internal_id}}/{{$config.billing_scripts_name}}.php</td>
			</tr>
			{{if $smarty.post.internal_id=='segpay'}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_username}}:</td>
					<td class="de_control">
						<input type="text" name="postback_username" class="dyn_full_size" maxlength="255" value="{{$smarty.post.postback_username}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.users.card_bill_config_field_postback_username_hint}}</span>
						{{/if}}
					</td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_password}}:</td>
					<td class="de_control">
						<input type="text" name="postback_password" class="dyn_full_size" maxlength="255" value="{{$smarty.post.postback_password}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.users.card_bill_config_field_postback_password_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{elseif $smarty.post.internal_id=='epoch'}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_ip_prefix}}:</td>
					<td class="de_control">
						<input type="text" name="postback_ip_protection" class="dyn_full_size" maxlength="255" value="{{$smarty.post.postback_ip_protection}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.users.card_bill_config_field_postback_ip_prefix_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{elseif $smarty.post.internal_id=='zombaio'}}
				<tr>
					<td class="de_label">{{$lang.users.card_bill_config_field_postback_password}}:</td>
					<td class="de_control">
						<input type="text" name="postback_password" class="dyn_full_size" maxlength="255" value="{{$smarty.post.postback_password}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.users.card_bill_config_field_postback_password_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_postback_reseller_param}}:</td>
				<td class="de_control">
					<input type="text" name="postback_reseller_param" class="dyn_full_size" maxlength="255" value="{{$smarty.post.postback_reseller_param}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.card_bill_config_field_postback_reseller_param_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_postback_repost_url}}:</td>
				<td class="de_control">
					<input type="text" name="postback_repost_url" class="dyn_full_size" maxlength="255" value="{{$smarty.post.postback_repost_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.card_bill_config_field_postback_repost_url_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $smarty.post.cf_pkg_setprice==1}}
				<tr>
					<td class="de_label">
						<div class="de_required status_id_1">{{$lang.users.card_bill_config_field_signature}} (*):</div>
						<div class="status_id_0">{{$lang.users.card_bill_config_field_signature}}:</div>
					</td>
					<td class="de_control">
						<input type="text" name="signature" class="dyn_full_size" maxlength="255" value="{{$smarty.post.signature}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<span class="de_hint">{{$lang.users.card_bill_config_field_signature_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		{{/if}}
		{{if $smarty.post.internal_id=='ccbill'|| $smarty.post.internal_id=='ccbilldyn'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_config_divider_datalink}}</div></td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required status_id_1">{{$lang.users.card_bill_config_field_datalink_account}} (*):</div>
					<div class="status_id_0">{{$lang.users.card_bill_config_field_datalink_account}}:</div>
				</td>
				<td class="de_control">
					<input type="text" name="account_id" class="fixed_200" maxlength="50" value="{{$smarty.post.account_id}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required status_id_1">{{$lang.users.card_bill_config_field_datalink_subaccount}} (*):</div>
					<div class="status_id_0">{{$lang.users.card_bill_config_field_datalink_subaccount}}:</div>
				</td>
				<td class="de_control">
					<input type="text" name="sub_account_id" class="fixed_200" maxlength="50" value="{{$smarty.post.sub_account_id}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required status_id_1">{{$lang.users.card_bill_config_field_datalink_username}} (*):</div>
					<div class="status_id_0">{{$lang.users.card_bill_config_field_datalink_username}}:</div>
				</td>
				<td class="de_control">
					<input type="text" name="datalink_username" class="fixed_200" maxlength="255" value="{{$smarty.post.datalink_username}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required status_id_1">{{$lang.users.card_bill_config_field_datalink_password}} (*):</div>
					<div class="status_id_0">{{$lang.users.card_bill_config_field_datalink_password}}:</div>
				</td>
				<td class="de_control">
					<input type="text" name="datalink_password" class="fixed_200" maxlength="255" value="{{$smarty.post.datalink_password}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_datalink_use_ip}}:</td>
				<td class="de_control">
					<input type="text" name="datalink_use_ip" class="fixed_200" maxlength="25" value="{{$smarty.post.datalink_use_ip}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.card_bill_config_field_datalink_use_ip_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{elseif $smarty.post.internal_id=='nats' || $smarty.post.internal_id=='natsum'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_config_divider_datalink}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_datalink_url}}:</td>
				<td class="de_control">
					<input type="text" name="datalink_url" class="fixed_200" maxlength="255" value="{{$smarty.post.datalink_url}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_datalink_username}}:</td>
				<td class="de_control">
					<input type="text" name="datalink_username" class="fixed_200" maxlength="255" value="{{$smarty.post.datalink_username}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_datalink_password}}:</td>
				<td class="de_control">
					<input type="text" name="datalink_password" class="fixed_200" maxlength="255" value="{{$smarty.post.datalink_password}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.card_bill_config_field_datalink_use_ip}}:</td>
				<td class="de_control">
					<input type="text" name="datalink_use_ip" class="fixed_200" maxlength="25" value="{{$smarty.post.datalink_use_ip}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.card_bill_config_field_datalink_use_ip_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_config_divider_packages}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table id="table_packages" class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
						<col/>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header">
						<td>{{$lang.users.card_bill_package_field_id}}</td>
						<td>{{$lang.users.card_bill_package_field_title}}</td>
						<td>{{$lang.users.card_bill_package_field_scope}}</td>
						<td>{{$lang.users.card_bill_package_field_order}}</td>
						<td>{{$lang.users.card_bill_package_field_status_active}}</td>
						<td>{{$lang.users.card_bill_package_field_default}}</td>
						<td>{{$lang.users.card_bill_package_action_delete}}</td>
					</tr>
					<tr id="add_package_row_template" class="eg_data fixed_height_30 hidden">
						<td><input type="hidden"/></td>
						<td><input type="text" size="45" maxlength="255"/></td>
						<td>
							<select>
								<option value="0">{{$lang.users.card_bill_package_field_scope_all}}</option>
								<option value="1">{{$lang.users.card_bill_package_field_scope_signup}}</option>
								<option value="2">{{$lang.users.card_bill_package_field_scope_upgrade}}</option>
							</select>
						</td>
						<td><input type="text" size="3" maxlength="10"/></td>
						<td><input type="checkbox" value="1" disabled="disabled"/></td>
						<td><input type="radio" name="default_package_id" disabled="disabled"/></td>
						<td><input type="checkbox" value="1"/></td>
					</tr>
					{{if count($smarty.post.packages)>0}}
						{{foreach item=item from=$smarty.post.packages|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td><a href="{{$page_name}}?action=change_package&amp;item_id={{$item.package_id}}">{{$item.package_id}}</a></td>
								<td><input type="text" name="title_{{$item.package_id}}" value="{{$item.title}}" size="45" maxlength="255"/></td>
								<td>
									<select name="scope_{{$item.package_id}}">
										<option value="0" {{if $item.scope_id==0}}selected="selected"{{/if}}>{{$lang.users.card_bill_package_field_scope_all}}</option>
										<option value="1" {{if $item.scope_id==1}}selected="selected"{{/if}}>{{$lang.users.card_bill_package_field_scope_signup}}</option>
										<option value="2" {{if $item.scope_id==2}}selected="selected"{{/if}}>{{$lang.users.card_bill_package_field_scope_upgrade}}</option>
									</select>
								</td>
								<td><input type="text" name="order_{{$item.package_id}}" value="{{$item.sort_id}}" size="3" maxlength="10"/></td>
								<td><input type="checkbox" name="is_active_{{$item.package_id}}" value="1" {{if $item.status_id==1}}checked="checked"{{/if}} {{if $item.payment_page_url==''}}disabled="disabled"{{/if}}/></td>
								<td><input type="radio" name="default_package_id" value="{{$item.package_id}}" {{if $item.is_default==1}}checked="checked"{{/if}}/></td>
								<td><input type="checkbox" name="delete_{{$item.package_id}}" value="1"/></td>
							</tr>
						{{/foreach}}
					{{else}}
						<tr id="add_package_info_message" class="eg_data fixed_height_30">
							<td colspan="6">{{$lang.users.card_bill_config_divider_packages_hint}}</td>
						</tr>
					{{/if}}
				</table>
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					<input id="btn_add_package" type="button" value="{{$lang.users.card_bill_package_action_add}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>
{{if $can_edit_all==1}}
	<div id="custom_js" class="js_params">
		<span class="js_param">buildCardConfigLogic=call</span>
	</div>
{{/if}}

{{elseif $smarty.get.action=='change_package'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_package_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_card_billing}}</a> / <a href="{{$page_name}}?action=change&amp;item_id={{$smarty.post.provider_id}}">{{$smarty.post.provider.title}}</a> / {{$lang.users.card_bill_package_edit|replace:"%1%":$smarty.post.title}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_package_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.card_bill_package_field_title}} (*):</td>
			<td class="de_control">
				<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.card_bill_package_field_status}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox"name="status_id" value="1" {{if $smarty.post.status_id==1}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_package_field_status_active}}</label></div>
				<select name="scope_id">
					<option value="0" {{if $smarty.post.scope_id==0}}selected="selected"{{/if}}>{{$lang.users.card_bill_package_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.scope_id==1}}selected="selected"{{/if}}>{{$lang.users.card_bill_package_field_scope_signup}}</option>
					<option value="2" {{if $smarty.post.scope_id==2}}selected="selected"{{/if}}>{{$lang.users.card_bill_package_field_scope_upgrade}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.card_bill_package_field_scope_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.card_bill_package_field_external_id}} (*):</td>
			<td class="de_control">
				<input type="text" name="external_id" maxlength="100" class="dyn_full_size {{if $smarty.post.provider.cf_pkg_setprice!=0}}readonly_field{{/if}}" value="{{$smarty.post.external_id}}" {{if $smarty.post.provider.cf_pkg_setprice!=0}}readonly="readonly"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.card_bill_package_field_external_id_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="option_unlimited">{{$lang.users.card_bill_package_field_access_type}}:</div>
				<div class="option_duration option_tokens de_required">{{$lang.users.card_bill_package_field_access_type}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_unlimited" type="radio" name="access_type" value="1" {{if $smarty.post.duration_initial==0 && $smarty.post.tokens==0}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_package_field_access_type_unlimited}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.users.card_bill_package_field_access_type_unlimited_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_duration" type="radio" name="access_type" value="2" {{if $smarty.post.duration_initial!=0 && $smarty.post.tokens==0}}checked="checked"{{/if}}/><label>{{$lang.users.card_bill_package_field_access_type_duration}}:</label></div>
								<input type="text" name="duration_initial" maxlength="10" class="fixed_100 option_duration" value="{{$smarty.post.duration_initial}}"/>
								{{if $smarty.post.provider.cf_pkg_rebills==1}}
									&nbsp;&nbsp;{{$lang.users.card_bill_package_field_access_type_duration_recurring}}:
									<input type="text" name="duration_rebill" maxlength="10" class="fixed_100 option_duration" value="{{if $smarty.post.duration_rebill!='0'}}{{$smarty.post.duration_rebill}}{{/if}}"/>
								{{/if}}
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.users.card_bill_package_field_access_type_duration_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_tokens" type="radio" name="access_type" value="3" {{if $smarty.post.tokens>0}}checked="checked"{{/if}} {{if $smarty.post.provider.cf_pkg_tokens==0}}disabled="disabled"{{/if}}/><label>{{$lang.users.card_bill_package_field_access_type_tokens}}:</label></div>
								<input type="text" name="tokens" maxlength="10" class="fixed_100 option_tokens" value="{{$smarty.post.tokens}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									{{if $smarty.post.provider.cf_pkg_tokens==1}}
										<br/><span class="de_hint">{{$lang.users.card_bill_package_field_access_type_tokens_hint}}</span>
									{{else}}
										<br/><span class="de_hint">{{$lang.users.card_bill_package_field_access_type_tokens_hint2}}</span>
									{{/if}}
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		{{if $smarty.post.provider.cf_pkg_setprice!=0}}
			<tr>
				<td class="de_label de_required">{{$lang.users.card_bill_package_field_price}} (*):</td>
				<td class="de_control">
					<input type="text" name="price_initial" maxlength="20" class="fixed_100" value="{{$smarty.post.price_initial}}"/>
					<select name="price_initial_currency">
						{{if $smarty.post.provider.internal_id=='tokens'}}
							<option value="TOK" {{if $smarty.post.price_initial_currency=='TOK'}}selected="selected"{{/if}}>Tokens</option>
						{{else}}
							<option value="USD" {{if $smarty.post.price_initial_currency=='USD'}}selected="selected"{{/if}}>USD</option>
							<option value="EUR" {{if $smarty.post.price_initial_currency=='EUR'}}selected="selected"{{/if}}>EUR</option>
							<option value="GBP" {{if $smarty.post.price_initial_currency=='GBP'}}selected="selected"{{/if}}>GBP</option>
							<option value="AUD" {{if $smarty.post.price_initial_currency=='AUD'}}selected="selected"{{/if}}>AUD</option>
							<option value="CAD" {{if $smarty.post.price_initial_currency=='CAD'}}selected="selected"{{/if}}>CAD</option>
							<option value="CHF" {{if $smarty.post.price_initial_currency=='CHF'}}selected="selected"{{/if}}>CHF</option>
							<option value="DKK" {{if $smarty.post.price_initial_currency=='DKK'}}selected="selected"{{/if}}>DKK</option>
							<option value="NOK" {{if $smarty.post.price_initial_currency=='NOK'}}selected="selected"{{/if}}>NOK</option>
							<option value="SEK" {{if $smarty.post.price_initial_currency=='SEK'}}selected="selected"{{/if}}>SEK</option>
							<option value="RUB" {{if $smarty.post.price_initial_currency=='RUB'}}selected="selected"{{/if}}>RUB</option>
						{{/if}}
					</select>
					{{if $smarty.post.provider.cf_pkg_rebills==1}}
						&nbsp;&nbsp;{{$lang.users.card_bill_package_field_price_recurring}}:
						<input type="text" name="price_rebill" maxlength="20" class="fixed_100 option_duration" value="{{$smarty.post.price_rebill}}"/>
						<select name="price_rebill_currency" class="option_duration">
							{{if $smarty.post.provider.internal_id=='tokens'}}
								<option value="TOK" {{if $smarty.post.price_rebill_currency=='TOK'}}selected="selected"{{/if}}>Tokens</option>
							{{else}}
								<option value="USD" {{if $smarty.post.price_rebill_currency=='USD'}}selected="selected"{{/if}}>USD</option>
								<option value="EUR" {{if $smarty.post.price_rebill_currency=='EUR'}}selected="selected"{{/if}}>EUR</option>
								<option value="GBP" {{if $smarty.post.price_rebill_currency=='GBP'}}selected="selected"{{/if}}>GBP</option>
								<option value="AUD" {{if $smarty.post.price_rebill_currency=='AUD'}}selected="selected"{{/if}}>AUD</option>
								<option value="CAD" {{if $smarty.post.price_rebill_currency=='CAD'}}selected="selected"{{/if}}>CAD</option>
								<option value="CHF" {{if $smarty.post.price_rebill_currency=='CHF'}}selected="selected"{{/if}}>CHF</option>
								<option value="DKK" {{if $smarty.post.price_rebill_currency=='DKK'}}selected="selected"{{/if}}>DKK</option>
								<option value="NOK" {{if $smarty.post.price_rebill_currency=='NOK'}}selected="selected"{{/if}}>NOK</option>
								<option value="SEK" {{if $smarty.post.price_rebill_currency=='SEK'}}selected="selected"{{/if}}>SEK</option>
								<option value="RUB" {{if $smarty.post.price_rebill_currency=='RUB'}}selected="selected"{{/if}}>RUB</option>
							{{/if}}
						</select>
					{{/if}}
					<br/><span class="de_hint">{{$lang.users.card_bill_package_field_price_hint}}</span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.users.card_bill_package_field_payment_page_url}} (*):</td>
			<td class="de_control">
				<input type="text" name="payment_page_url" maxlength="400" class="dyn_full_size" value="{{$smarty.post.payment_page_url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/>
					<span class="de_hint">
						{{$lang.users.card_bill_package_field_payment_page_url_hint}}
						{{if $smarty.post.provider.example_payment_url!=''}}
							<br/>{{$lang.users.card_bill_package_field_urls_example|replace:"%1%":$smarty.post.provider.example_payment_url}}
						{{/if}}
					</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.provider.cf_pkg_oneclick!=0}}
			<tr>
				<td class="de_label">{{$lang.users.card_bill_package_field_oneclick_page_url}}:</td>
				<td class="de_control">
					<input type="text" name="oneclick_page_url" maxlength="400" class="dyn_full_size" value="{{$smarty.post.oneclick_page_url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						<span class="de_hint">
							{{$lang.users.card_bill_package_field_oneclick_page_url_hint}}
							{{if $smarty.post.provider.example_oneclick_url!=''}}
								<br/>{{$lang.users.card_bill_package_field_urls_example|replace:"%1%":$smarty.post.provider.example_oneclick_url}}
							{{/if}}
						</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.card_bill_package_divider_countries}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<td class="de_simple_text" colspan="2">
				<span class="de_hint">{{$lang.users.card_bill_package_divider_countries_hint}}</span>
			</td>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.card_bill_package_field_include_countries}}:</td>
			<td class="de_control">
				<input type="text" name="include_countries" maxlength="1000" class="dyn_full_size" value="{{$smarty.post.include_countries}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.card_bill_package_field_include_countries_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.card_bill_package_field_exclude_countries}}:</td>
			<td class="de_control">
				<input type="text" name="exclude_countries" maxlength="1000" class="dyn_full_size" value="{{$smarty.post.exclude_countries}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.card_bill_package_field_exclude_countries_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control"><input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/></td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
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
					<col/>
					<col/>
					<col/>
					<col/>
					<col/>
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
					<td>{{$lang.users.card_bill_package_field_id}}</td>
					<td>{{$lang.users.card_bill_package_field_title}}</td>
					<td>{{$lang.users.card_bill_package_field_status}}</td>
					<td>{{$lang.users.card_bill_package_field_scope}}</td>
					<td>{{$lang.users.card_bill_package_field_access_type}}</td>
					<td>{{$lang.users.card_bill_package_field_countries}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_group_header {{if $item.status_id==0}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
						<td colspan="6">
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.provider_id}}">{{$item.title}}</a>
							{{if $item.status_id==1}}({{$lang.users.card_bill_config_field_status_active}}{{if $item.is_default==1}}, {{$lang.users.card_bill_config_field_default}}{{/if}}){{/if}}
						</td>
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.provider_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if in_array('system|administration',$smarty.session.permissions)}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">delete_hide=true</span>
										<span class="js_param">internal_id={{$item.internal_id}}</span>
									</span>
								</a>
							{{/if}}
						</td>
					</tr>
					{{foreach name=data2 item=item2 from=$item.packages|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data2.iteration % 2==0}} dg_even{{/if}} {{if $item2.status_id==0}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item2.package_id}}" {{if $can_edit_all!=1}}disabled="disabled"{{/if}}/></td>
						<td>
							<a href="{{$page_name}}?action=change_package&amp;item_id={{$item2.package_id}}">{{$item2.package_id}}</a>
						</td>
						<td>
							{{$item2.title}} {{if $item2.is_default==1}}({{$lang.users.card_bill_package_field_default}}){{/if}}
						</td>
						<td>{{if $item2.status_id==0}}{{$lang.users.card_bill_package_field_status_disabled}}{{elseif $item2.status_id==1}}{{$lang.users.card_bill_package_field_status_active}}{{/if}}</td>
						<td>{{if $item2.scope_id==0}}{{$lang.users.card_bill_package_field_scope_all}}{{elseif $item2.scope_id==1}}{{$lang.users.card_bill_package_field_scope_signup}}{{elseif $item2.scope_id==2}}{{$lang.users.card_bill_package_field_scope_upgrade}}{{/if}}</td>
						<td>
							{{if $item2.tokens>0}}
								{{if $item.cf_pkg_setprice==1}}
									{{$lang.users.card_bill_package_field_access_type_tokens_short2|replace:"%1%":$item2.tokens|replace:"%2%":$item2.price_initial|replace:"%3%":$item2.price_initial_currency}}
								{{else}}
									{{$lang.users.card_bill_package_field_access_type_tokens_short|replace:"%1%":$item2.tokens}}
								{{/if}}
							{{elseif $item2.duration_initial==0}}
								{{if $item.cf_pkg_setprice==1}}
									{{$lang.users.card_bill_package_field_access_type_unlimited2|replace:"%2%":$item2.price_initial|replace:"%3%":$item2.price_initial_currency}}
								{{else}}
									{{$lang.users.card_bill_package_field_access_type_unlimited}}
								{{/if}}
							{{elseif $item2.duration_rebill}}
								{{if $item.cf_pkg_setprice==1}}
									{{$lang.users.card_bill_package_field_access_type_duration_recurring_short2|replace:"%1%":$item2.duration_initial|replace:"%2%":$item2.price_initial|replace:"%3%":$item2.price_initial_currency|replace:"%4%":$item2.duration_rebill|replace:"%5%":$item2.price_rebill|replace:"%6%":$item2.price_rebill_currency}}
								{{else}}
									{{$lang.users.card_bill_package_field_access_type_duration_recurring_short|replace:"%1%":$item2.duration_initial|replace:"%2%":$item2.duration_rebill}}
								{{/if}}
							{{else}}
								{{if $item.cf_pkg_setprice==1}}
									{{$lang.users.card_bill_package_field_access_type_duration_short2|replace:"%1%":$item2.duration_initial|replace:"%2%":$item2.price_initial|replace:"%3%":$item2.price_initial_currency}}
								{{else}}
									{{$lang.users.card_bill_package_field_access_type_duration_short|replace:"%1%":$item2.duration_initial}}
								{{/if}}
							{{/if}}
						</td>
						<td>
							{{if $item2.include_countries=='' && $item2.exclude_countries==''}}
								{{$lang.users.card_bill_package_field_countries_all}}
							{{else}}
								{{assign var="countries_separator" value=""}}
								{{if $item2.include_countries!=''}}
									{{assign var="include_countries_list" value=","|explode:$item2.include_countries}}
									<span title="{{$item2.include_countries}}">+{{$include_countries_list|@count}}<span>
									{{assign var="countries_separator" value=", "}}
								{{/if}}
								{{if $item2.exclude_countries!=''}}
									{{assign var="exclude_countries_list" value=","|explode:$item2.exclude_countries}}
									{{$countries_separator}}<span title="{{$item2.exclude_countries}}">-{{$exclude_countries_list|@count}}<span>
								{{/if}}
							{{/if}}
						</td>
						<td>
							<a href="{{$page_name}}?action=change_package&amp;item_id={{$item2.package_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if $can_edit_all==1}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item2.package_id}}</span>
										<span class="js_param">name={{$item2.title}}</span>
										<span class="js_param">bill_log_hide=true</span>
									</span>
								</a>
							{{/if}}
						</td>
					</tr>
					{{/foreach}}
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_edit_all==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
						<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						<span class="js_param">hide=${delete_hide}</span>
					</li>
				{{/if}}
				<li class="js_params">
					<span class="js_param">href=log_bill.php?no_filter=true&amp;se_internal_provider_id=${internal_id}</span>
					<span class="js_param">title={{$lang.users.card_bill_config_action_view_log}}</span>
					<span class="js_param">hide=${bill_log_hide}</span>
					<span class="js_param">plain_link=true</span>
				</li>
			</ul>
		</div>
		<div class="dgb">
			<table>
				<tr>
					{{if $can_edit_all==1}}
						<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
						<td class="dgb_control">
							<select name="batch_action">
								<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
								<option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
							</select>
						</td>
						<td class="dgb_control">
							<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
						</td>
					{{/if}}
					<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
				</tr>
			</table>
			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}