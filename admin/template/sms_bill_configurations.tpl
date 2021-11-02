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

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_sms_billing}}</a> /{{$lang.users.sms_bill_config_edit|replace:"%1%":$smarty.post.title}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.sms_bill_config_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.sms_bill_config_field_processor_type}}:</td>
			<td class="de_control"><a href="{{$smarty.post.url}}" rel="external">{{$smarty.post.title}}</a></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.sms_bill_config_field_status}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="status_id" name="status_id">
						<option value="0" {{if $smarty.post.status_id=='0'}}selected="selected"{{/if}}>{{$lang.users.sms_bill_config_field_status_disabled}}</option>
						<option value="1" {{if $smarty.post.status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.sms_bill_config_field_status_active}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.sms_bill_config_field_status_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="status_id_0">{{$lang.users.sms_bill_config_field_secret_key}}:</div>
				<div class="de_required status_id_1">{{$lang.users.sms_bill_config_field_secret_key}} (*):</div>
			</td>
			<td class="de_control">
				<input type="text" name="secret_key" value="{{$smarty.post.secret_key}}" maxlength="100"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.sms_bill_config_field_secret_key_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.sms_bill_config_field_postback_url}}:</td>
			<td class="de_control">{{$config.project_url}}/admin/billings/{{$smarty.post.internal_id}}/{{$config.billing_scripts_name}}.php</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.sms_bill_config_divider_packages}}</div></td>
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
						<td>{{$lang.users.sms_bill_config_packages_col_id}}</td>
						<td>{{$lang.users.sms_bill_config_packages_col_title}}</td>
						<td>{{$lang.users.sms_bill_config_packages_col_countries}}</td>
						<td>{{$lang.users.sms_bill_config_packages_col_order}}</td>
						<td>{{$lang.users.sms_bill_config_packages_col_active}}</td>
						<td>{{$lang.users.sms_bill_config_packages_col_default}}</td>
						<td>{{$lang.users.sms_bill_config_packages_col_delete}}</td>
					</tr>
					<tr id="add_package_row_template" class="eg_data fixed_height_30 hidden">
						<td><input type="hidden"/></td>
						<td><input type="text" size="45" maxlength="255"/></td>
						<td></td>
						<td><input type="text" size="3" maxlength="10"/></td>
						<td><input type="checkbox" disabled="disabled"/></td>
						<td><input type="radio" disabled="disabled"/></td>
						<td><input type="checkbox" value="1"/></td>
					</tr>
					{{if count($smarty.post.packages)>0}}
						{{foreach item=item from=$smarty.post.packages|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td><a href="{{$page_name}}?action=change_package&amp;item_id={{$item.package_id}}">{{$item.package_id}}</a></td>
								<td><input type="text" name="title_{{$item.package_id}}" value="{{$item.title}}" size="45" maxlength="255"/></td>
								<td>{{$item.countries_amount}}</td>
								<td><input type="text" name="order_{{$item.package_id}}" value="{{$item.sort_id}}" size="3" maxlength="10"/></td>
								<td><input type="checkbox" name="is_active_{{$item.package_id}}" value="1" {{if $item.status_id==1}}checked="checked"{{/if}} {{if $item.countries_amount==0}}disabled="disabled"{{/if}}/></td>
								<td><input type="radio" name="default_package_id" value="{{$item.package_id}}" {{if $item.is_default==1}}checked="checked"{{/if}}/></td>
								<td><input type="checkbox" name="delete_{{$item.package_id}}" value="1"/></td>
							</tr>
						{{/foreach}}
					{{else}}
						<tr id="add_package_info_message" class="eg_data fixed_height_30">
							<td colspan="7">{{$lang.users.sms_bill_config_divider_packages_hint}}</td>
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
					<input id="btn_add_package" type="button" value="{{$lang.users.sms_bill_config_btn_add_package}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>
{{if $can_edit_all==1}}
	<div id="custom_js" class="js_params">
		<span class="js_param">buildSmsConfigLogic=call</span>
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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_sms_billing}}</a> / <a href="{{$page_name}}?action=change&amp;item_id={{$smarty.post.provider_id}}">{{$smarty.post.provider.title}}</a> / {{$lang.users.sms_bill_package_edit|replace:"%1%":$smarty.post.title}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.sms_bill_package_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.sms_bill_package_field_title}} (*):</td>
			<td class="de_control">
				<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.sms_bill_package_field_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id==1}}checked="checked"{{/if}}/><span {{if $smarty.post.status_id==1}}class="selected"{{/if}}>{{$lang.users.sms_bill_package_field_enable_enabled}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.sms_bill_package_field_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.sms_bill_package_field_external_id}}:</td>
			<td class="de_control">
				<input type="text" name="external_id" maxlength="100" class="dyn_full_size" value="{{$smarty.post.external_id}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.sms_bill_package_field_external_id_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="option_unlimited">{{$lang.users.sms_bill_package_field_access_type}}:</div>
				<div class="option_duration option_tokens de_required">{{$lang.users.sms_bill_package_field_access_type}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_unlimited" type="radio" name="access_type" value="1" {{if $smarty.post.duration==0 && $smarty.post.tokens==0}}checked="checked"{{/if}}/><label>{{$lang.users.sms_bill_package_field_access_type_unlimited}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.users.sms_bill_package_field_access_type_unlimited_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_duration" type="radio" name="access_type" value="2" {{if $smarty.post.duration!=0 && $smarty.post.tokens==0}}checked="checked"{{/if}}/><label>{{$lang.users.sms_bill_package_field_access_type_duration}}:</label></div>
								<input type="text" name="duration" maxlength="10" class="fixed_100 option_duration" value="{{$smarty.post.duration}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.users.sms_bill_package_field_access_type_duration_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_tokens" type="radio" name="access_type" value="3" {{if $smarty.post.tokens>0}}checked="checked"{{/if}}/><label>{{$lang.users.sms_bill_package_field_access_type_tokens}}:</label></div>
								<input type="text" name="tokens" maxlength="10" class="fixed_100 option_tokens" value="{{$smarty.post.tokens}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.users.sms_bill_package_field_access_type_tokens_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.users.sms_bill_package_divider_countries}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table id="table_countries" class="de_edit_grid">
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
						<td>{{$lang.users.sms_bill_package_countries_col_title}}</td>
						<td>{{$lang.users.sms_bill_package_countries_col_number}}</td>
						<td>{{$lang.users.sms_bill_package_countries_col_prefix}}</td>
						<td>{{$lang.users.sms_bill_package_countries_col_subscriber_cost}}</td>
						<td>{{$lang.users.sms_bill_package_countries_col_order}}</td>
						<td>{{$lang.users.sms_bill_package_countries_col_active}}</td>
						<td>{{$lang.users.sms_bill_package_countries_col_delete}}</td>
					</tr>
					{{if count($smarty.post.countries)>0}}
						{{foreach item=item_country from=$smarty.post.countries|smarty:nodefaults}}
						<tr class="eg_group_header fixed_height_30">
							<td><input type="text" name="country_title_{{$item_country.country_id}}" class="dyn_full_size" value="{{$item_country.title}}" maxlength="255"/></td>
							<td>
								<select name="country_code_{{$item_country.country_id}}" class="fixed_200">
									<option value="">{{$lang.users.sms_bill_package_countries_code_select}}</option>
									{{foreach from=$list_countries|smarty:nodefaults item=item}}
										<option value="{{$item.country_code}}" {{if $item_country.country_code==$item.country_code}}selected="selected"{{/if}}>
											{{$item.title}}
										</option>
									{{/foreach}}
								</select>
							</td>
							<td colspan="2">
								{{if $can_edit_all==1}}
									<a id="country_link_{{$item_country.country_id}}" href="javascript:stub()" class="add">{{$lang.users.sms_bill_package_countries_action_add_operator}}</a>
								{{/if}}
							</td>
							<td><input type="text" name="country_order_{{$item_country.country_id}}" class="fixed_50" value="{{$item_country.sort_id}}" maxlength="10"/></td>
							<td><input type="checkbox" name="country_is_active_{{$item_country.country_id}}" value="1" {{if $item_country.status_id==1}}checked="checked"{{/if}}/></td>
							<td><input type="checkbox" name="country_delete_{{$item_country.country_id}}" value="1"/></td>
						</tr>
							{{foreach item=item from=$item_country.operators|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td>
									<input type="hidden" name="ref_country_{{$item.operator_id}}" value="{{$item_country.country_id}}"/>
									<input type="text" name="title_{{$item.operator_id}}" class="dyn_full_size" value="{{$item.title}}" maxlength="255"/>
								</td>
								<td><input type="text" name="phone_{{$item.operator_id}}" class="dyn_full_size" value="{{$item.phone}}" maxlength="100"/></td>
								<td><input type="text" name="prefix_{{$item.operator_id}}" class="fixed_100" value="{{$item.prefix}}" maxlength="100"/></td>
								<td><input type="text" name="cost_{{$item.operator_id}}" class="fixed_150" value="{{$item.cost}}" maxlength="100"/></td>
								<td><input type="text" name="order_{{$item.operator_id}}" class="fixed_50" value="{{$item.sort_id}}" maxlength="10"/></td>
								<td><input type="checkbox" name="is_active_{{$item.operator_id}}" value="1"{{if $item.status_id==1}} checked="checked"{{/if}}/></td>
								<td><input type="checkbox" name="delete_{{$item.operator_id}}" value="1"/></td>
							</tr>
							{{/foreach}}
						{{/foreach}}
					{{else}}
						<tr id="add_country_info_message" class="eg_data fixed_height_30">
							<td colspan="7">{{$lang.users.sms_bill_package_divider_countries_hint}}</td>
						</tr>
					{{/if}}
					<tr id="add_country_row_template" class="eg_group_header fixed_height_30 hidden">
						<td>
							<input type="hidden"/>
							<input type="text" class="dyn_full_size" maxlength="255"/>
						</td>
						<td>
							<select class="fixed_200">
								<option value="">{{$lang.users.sms_bill_package_countries_code_select}}</option>
								{{foreach from=$list_countries|smarty:nodefaults item=item}}
									<option value="{{$item.country_code}}">
										{{$item.title}}
									</option>
								{{/foreach}}
							</select>
						</td>
						<td colspan="2"><a id="add_country_row_template_link" href="javascript:stub()" class="add">{{$lang.users.sms_bill_package_countries_action_add_operator}}</a></td>
						<td><input type="text" class="fixed_50" maxlength="10"/></td>
						<td><input type="checkbox" value="1" checked="checked"/></td>
						<td><input type="checkbox" value="1"/></td>
					</tr>
					<tr id="add_operator_row_template" class="eg_data fixed_height_30 hidden">
						<td>
							<input type="hidden"/>
							<input type="text" class="dyn_full_size" maxlength="255"/>
						</td>
						<td><input type="text" class="dyn_full_size" maxlength="100"/></td>
						<td><input type="text" class="fixed_100" maxlength="100"/></td>
						<td><input type="text" class="fixed_150" maxlength="100"/></td>
						<td><input type="text" class="fixed_50" maxlength="10"/></td>
						<td><input type="checkbox" value="1" checked="checked"/></td>
						<td><input type="checkbox" value="1"/></td>
					</tr>
				</table>
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					<input id="btn_add_country" type="button" value="{{$lang.users.sms_bill_package_btn_add_country}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>
{{if $can_edit_all==1}}
	<div id="custom_js" class="js_params">
		<span class="js_param">buildSmsAccessPackageLogic=call</span>
	</div>
{{/if}}

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
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
					<td>{{$lang.users.dg_sms_bill_configs_col_title}}</td>
					<td>{{$lang.users.dg_sms_bill_configs_col_status}}</td>
					<td>{{$lang.users.dg_sms_bill_configs_col_countries}}</td>
					<td>{{$lang.users.dg_sms_bill_configs_col_duration}}</td>
					<td>{{$lang.users.dg_sms_bill_configs_col_tokens}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_group_header {{if $item.status_id==0}}disabled{{/if}}">
						<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
						<td colspan="5">
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.provider_id}}">{{$item.title}}</a>
							{{if $item.status_id==1}}({{$lang.users.dg_sms_bill_configs_col_title_active}}){{/if}}
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
							<a href="{{$page_name}}?action=change_package&amp;item_id={{$item2.package_id}}">{{$item2.title}}</a>
							{{if $item2.is_default==1}}({{$lang.users.dg_sms_bill_configs_col_title_default}}){{/if}}
						</td>
						<td>{{if $item2.status_id==0}}{{$lang.users.dg_sms_bill_configs_col_status_disabled}}{{elseif $item2.status_id==1}}{{$lang.users.dg_sms_bill_configs_col_status_active}}{{/if}}</td>
						<td>{{$item2.countries_amount}}</td>
						<td>
							{{if $item2.duration==0}}
								{{$lang.users.dg_sms_bill_configs_col_duration_unlimited}}
							{{else}}
								{{$item2.duration}}
							{{/if}}
						</td>
						<td>
							{{if $item2.tokens==0}}
								{{$lang.users.dg_sms_bill_configs_col_tokens_none}}
							{{else}}
								{{$item2.tokens}}
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
					<span class="js_param">title={{$lang.users.dg_sms_bill_configs_action_view_log}}</span>
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