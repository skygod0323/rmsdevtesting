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

{{if $smarty.get.action=='add_new_spot' || $smarty.get.action=='change_spot'}}

{{if in_array('advertising|edit_all',$smarty.session.permissions) || (in_array('advertising|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
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
		{{if $smarty.get.action=='add_new_spot'}}
			<input type="hidden" name="action" value="add_new_spot_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_spot_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_advertisements_list}}</a> / {{if $smarty.get.action=='add_new_spot'}}{{$lang.website_ui.spot_add}}{{else}}{{$lang.website_ui.spot_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.spot_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		{{if $smarty.get.action=='add_new_spot'}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.spot_field_id}} (*):</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" class="dyn_full_size" value="{{$smarty.post.external_id}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.spot_field_id_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_label">{{$lang.website_ui.spot_field_id}}:</td>
				<td class="de_control">{{$smarty.post.external_id}}</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.website_ui.spot_field_insert_code}}:</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new_spot'}}
					<span class="de_autopopulate" data-autopopulate-from="external_id" data-autopopulate-pattern='{{$smarty.ldelim}}insert name="getAdv" place_id="${value}"{{$smarty.rdelim}}'></span>
				{{else}}
					{{$smarty.ldelim}}insert name="getAdv" place_id="{{$smarty.post.external_id}}"{{$smarty.rdelim}}
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.spot_field_insert_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new_spot'}}
			<tr>
				<td class="de_label">{{$lang.website_ui.spot_field_usages}}:</td>
				<td class="de_control">
					{{if count($smarty.post.usages)>0}}
						{{foreach name=data item=item from=$smarty.post.usages|smarty:nodefaults}}
							{{if $item.is_player==1}}
								<a href="{{$item.url}}">{{if $item.is_embed==1}}{{$lang.website_ui.spot_field_usages_embed}}{{else}}{{$lang.website_ui.spot_field_usages_player}}{{/if}} - {{if $item.type=='start'}}{{$lang.website_ui.spot_field_usages_type_start}}{{elseif $item.type=='pre'}}{{$lang.website_ui.spot_field_usages_type_pre}}{{elseif $item.type=='post'}}{{$lang.website_ui.spot_field_usages_type_post}}{{elseif $item.type=='pause'}}{{$lang.website_ui.spot_field_usages_type_pause}}{{/if}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
							{{elseif $item.external_id!=''}}
								<a href="project_pages.php?action=change&amp;item_id={{$item.external_id}}">{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
							{{elseif $item.block_uid!=''}}
								<a href="project_pages.php?action=change_block&amp;item_id={{$item.block_uid}}&amp;item_name={{$item.block_title}}">{{$item.title}} / {{$item.block_title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
							{{elseif $item.page_component_id!=''}}
								<a href="project_pages_components.php?action=change&amp;item_id={{$item.page_component_id}}">{{$item.page_component_id}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
							{{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.website_ui.spot_field_usages_none}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.website_ui.spot_field_template_code}}:</td>
			<td class="de_control">
				<textarea name="template" class="html_code_editor dyn_full_size" rows="5" cols="40">{{$smarty.post.template}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.spot_field_template_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					{{if $smarty.get.action=='add_new_spot'}}
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

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('advertising|edit_all',$smarty.session.permissions) || (in_array('advertising|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
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
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2">
				<div>
					<a href="{{$page_name}}">{{$lang.website_ui.submenu_option_advertisements_list}}</a>
					/
					{{if $smarty.get.action=='add_new'}}
						{{$lang.website_ui.advertisement_add}}
					{{else}}
						<a href="{{$page_name}}?action=change_spot&amp;item_id={{$smarty.post.spot_id}}">{{$smarty.post.spot_title}}</a>
						/
						{{$lang.website_ui.advertisement_edit|replace:"%1%":$smarty.post.title}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/829-how-to-maximize-your-tube-revenue-with-kvs-advertising-system">How to maximize your tube revenue with KVS advertising system</a></span><br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1300-theme-customization-how-to-show-ads-inside-video-lists">How to show ads inside video lists</a></span><br/>
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/1050-theme-customization-how-to-show-specific-hml-advertising-code-for-specific-categories">How to show specific HML / advertising code for specific categories</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.advertisement_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.advertisement_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.advertisement_field_spot}} (*):</td>
			<td class="de_control">
				<select name="spot_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data item=item from=$list_spots|smarty:nodefaults}}
						<option value="{{$item.external_id}}" {{if $item.external_id==$smarty.post.spot_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_spot_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_status}}:</td>
			<td class="de_control">
				<select name="is_active">
					<option value="1" {{if $smarty.post.is_active=='1'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_field_status_active}}</option>
					<option value="0" {{if $smarty.post.is_active=='0'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_field_status_disabled}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.advertisement_field_html_code}} (*):</td>
			<td class="de_control"><textarea name="code" class="html_code_editor dyn_full_size " cols="40" rows="15" {{if $can_edit_all==0}}disabled="disabled"{{/if}}>{{$smarty.post.code}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_url}}:</td>
			<td class="de_control">
				<input type="text" name="url" maxlength="255" class="dyn_full_size" value="{{$smarty.post.url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_url_hint|replace:"%1%":$lang.website_ui.advertisement_field_html_code}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.advertisement_divider_restrictions}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_show_date}}:</td>
			<td class="de_control">
				{{$lang.website_ui.advertisement_field_show_date_from}}:
				{{html_select_date prefix='show_from_date_' start_year='-3' end_year='+3' field_order=DMY time=$smarty.post.show_from_date}}
				<input type="text" name="show_from_date_time" maxlength="5" size="4" value="{{$smarty.post.show_from_date|date_format:"%H:%M"}}"/>
				&nbsp;&nbsp;
				{{$lang.website_ui.advertisement_field_show_date_to}}:
				{{html_select_date prefix='show_to_date_' start_year='-3' end_year='+3' field_order=DMY time=$smarty.post.show_to_date}}
				<input type="text" name="show_to_date_time" maxlength="5" size="4" value="{{$smarty.post.show_to_date|date_format:"%H:%M"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_show_date_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_show_time}}:</td>
			<td class="de_control">
				{{$lang.website_ui.advertisement_field_show_time_from}}:
				<input type="text" name="show_from_time" maxlength="5" size="4" value="{{$smarty.post.show_from_time}}"/>
				&nbsp;&nbsp;
				{{$lang.website_ui.advertisement_field_show_time_to}}:
				<input type="text" name="show_to_time" maxlength="5" size="4" value="{{$smarty.post.show_to_time}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_show_time_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.advertisement_field_devices}} (*):</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="devices[]" value="pc" {{if in_array('pc', $smarty.post.devices) || count($smarty.post.devices)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_devices_pc}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="devices[]" value="phone" {{if in_array('phone', $smarty.post.devices) || count($smarty.post.devices)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_devices_phone}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="devices[]" value="tablet" {{if in_array('tablet', $smarty.post.devices) || count($smarty.post.devices)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_devices_tablet}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_devices_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.advertisement_field_browsers}} (*):</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="chrome" {{if in_array('chrome', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_chrome}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="firefox" {{if in_array('firefox', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_firefox}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="safari" {{if in_array('safari', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_safari}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="msie" {{if in_array('msie', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_msie}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="opera" {{if in_array('opera', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_opera}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="yandex" {{if in_array('yandex', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_yandex}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="uc" {{if in_array('uc', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_uc}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="browsers[]" value="other" {{if in_array('other', $smarty.post.browsers) || count($smarty.post.browsers)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_browsers_other}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_browsers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.advertisement_field_users}} (*):</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="users[]" value="guest" {{if in_array('guest', $smarty.post.users) || count($smarty.post.users)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_guest}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="users[]" value="active" {{if in_array('active', $smarty.post.users) || count($smarty.post.users)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_active}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="users[]" value="premium" {{if in_array('premium', $smarty.post.users) || count($smarty.post.users)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_premium}}</label></div>
				<div class="de_lv_pair"><input type="checkbox" name="users[]" value="webmaster" {{if in_array('webmaster', $smarty.post.users) || count($smarty.post.users)==0}}checked="checked"{{/if}}/><label>{{$lang.website_ui.advertisement_field_users_webmaster}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.advertisement_field_users_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_categories}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=category_ids[]</span>
						<span class="js_param">empty_message={{$lang.website_ui.advertisement_field_categories_empty}}</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_category" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.website_ui.advertisement_field_categories_all}}"/>
						</div>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.website_ui.advertisement_field_categories_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_categories2}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=exclude_category_ids[]</span>
						<span class="js_param">empty_message={{$lang.website_ui.advertisement_field_categories2_empty}}</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name=data item=item from=$smarty.post.exclude_categories|smarty:nodefaults}}
						<input type="hidden" name="exclude_category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_category" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.website_ui.advertisement_field_categories2_all}}"/>
						</div>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.website_ui.advertisement_field_categories2_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.advertisement_field_countries}}:</td>
			<td class="de_control">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_countries.php</span>
						<span class="js_param">validate_input=true</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=countries[]</span>
						<span class="js_param">empty_message={{$lang.website_ui.advertisement_field_countries_empty}}</span>
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.countries|smarty:nodefaults}}
						<input type="hidden" name="countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
					{{/foreach}}
					<div class="controls">
						<input type="text" name="new_country_{{$index}}" class="fixed_300" value=""/>
						<input type="button" class="add" value="{{$lang.common.add}}"/>
						<input type="button" class="all" value="{{$lang.website_ui.advertisement_field_countries_all}}"/>
					</div>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.website_ui.advertisement_field_countries_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
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

{{if in_array('advertising|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('advertising|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
{{/if}}
{{if $can_delete==1 || $can_edit==1}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
{{if $can_delete==1 || $can_edit==1}}
	{{assign var=can_invoke_batch value=1}}
{{else}}
	{{assign var=can_invoke_batch value=0}}
{{/if}}

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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status!=''}}dgf_selected{{/if}}">{{$lang.website_ui.advertisement_filter_status}}:</td>
					<td class="dgf_control">
						<select name="se_status">
							<option value="" {{if $smarty.session.save.$page_name.se_status==''}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_filter_status_all}}</option>
							<option value="active" {{if $smarty.session.save.$page_name.se_status=='active'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_filter_status_active}}</option>
							<option value="disabled" {{if $smarty.session.save.$page_name.se_status=='disabled'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_filter_status_disabled}}</option>
							<option value="now" {{if $smarty.session.save.$page_name.se_status=='now'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_filter_status_now}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_device!=''}}dgf_selected{{/if}}">{{$lang.website_ui.advertisement_field_devices}}:</td>
					<td class="dgf_control">
						<select name="se_device">
							<option value="" {{if $smarty.session.save.$page_name.se_device==''}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_field_devices_all}}</option>
							<option value="pc" {{if $smarty.session.save.$page_name.se_device=='pc'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_field_devices_pc}}</option>
							<option value="phone" {{if $smarty.session.save.$page_name.se_device=='phone'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_field_devices_phone}}</option>
							<option value="tablet" {{if $smarty.session.save.$page_name.se_device=='tablet'}}selected="selected"{{/if}}>{{$lang.website_ui.advertisement_field_devices_tablet}}</option>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name="data_spots" item="item_spot" from=$data|smarty:nodefaults}}
					<tr class="dg_group_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_spot.external_id}}" {{if $can_invoke_additional==0 || count($item_spot.usages)>0}}disabled="disabled"{{/if}}/></td>
						{{assign var="colspan" value=0}}
						{{assign var="has_title" value=0}}
						{{foreach from=$table_fields|smarty:nodefaults item="field"}}
							{{if $field.is_enabled==1}}
								{{assign var="colspan" value=$colspan+1}}
							{{/if}}
						{{/foreach}}
						<td colspan="{{$colspan}}">
							<a href="{{$page_name}}?action=change_spot&amp;item_id={{$item_spot.external_id}}" {{if $item_spot.errors==1}}class="highlighted_text"{{elseif $item_spot.warnings==1 || $item_spot.is_debug_enabled==1}}class="warning_text"{{/if}}>{{$item_spot.title}}</a>
							{{if $item_spot.is_debug_enabled==1}}
								<span class="warning_text">({{$lang.website_ui.spot_warning_debug_enabled}})</span>
							{{/if}}
						</td>
						<td>
							<a href="{{$page_name}}?action=change_spot&amp;item_id={{$item_spot.external_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if $can_invoke_additional==1}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item_spot.external_id}}</span>
										<span class="js_param">name={{$item_spot.title}}</span>
										<span class="js_param">activate_hide=true</span>
										<span class="js_param">deactivate_hide=true</span>
										{{if count($item_spot.usages)>0}}
											<span class="js_param">delete_disable=true</span>
										{{/if}}
										{{if $item_spot.is_debug_enabled==1}}
											<span class="js_param">enable_debug_hide=true</span>
										{{else}}
											<span class="js_param">disable_debug_hide=true</span>
										{{/if}}
										{{if $item_spot.has_debug_log!=1}}
											<span class="js_param">view_debug_log_hide=true</span>
										{{/if}}
									</span>
								</a>
							{{/if}}
						</td>
					</tr>
					{{foreach name="data" item="item" from=$item_spot.ads|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.is_active==0}}disabled{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_spot.external_id}}/{{$item.advertisement_id}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td>
								<a href="{{$page_name}}?action=change&amp;item_id={{$item.advertisement_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								{{if $can_invoke_additional==1}}
									<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
										<span class="js_params">
											<span class="js_param">id={{$item_spot.external_id}}/{{$item.advertisement_id}}</span>
											<span class="js_param">name={{$item.title}}</span>
											{{if $item.is_active==1}}
												<span class="js_param">activate_hide=true</span>
											{{else}}
												<span class="js_param">deactivate_hide=true</span>
											{{/if}}
											<span class="js_param">enable_debug_hide=true</span>
											<span class="js_param">disable_debug_hide=true</span>
											<span class="js_param">view_debug_log_hide=true</span>
										</span>
									</a>
								{{/if}}
							</td>
						</tr>
					{{/foreach}}
				{{/foreach}}
			</table>
			{{if $can_invoke_additional==1}}
				<ul class="dg_additional_menu_template">
					{{if $can_delete==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">disable=${delete_disable}</span>
						</li>
					{{/if}}
					{{if $can_edit==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
							<span class="js_param">hide=${activate_hide}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
							<span class="js_param">hide=${deactivate_hide}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=enable_debug&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_enable_debug}}</span>
							<span class="js_param">hide=${enable_debug_hide}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=disable_debug&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_disable_debug}}</span>
							<span class="js_param">hide=${disable_debug_hide}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?action=view_debug_log&amp;id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_view_debug_log}}</span>
							<span class="js_param">plain_link=true</span>
							<span class="js_param">hide=${view_debug_log_hide}</span>
						</li>
					{{/if}}
				</ul>
			{{/if}}
		</div>
		<div class="dgb">
			<table>
				<tr>
					{{if $can_invoke_batch==1}}
						<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
						<td class="dgb_control">
							<select name="batch_action">
								<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
								{{if $can_delete==1}}
									<option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
								{{/if}}
								{{if $can_edit==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
								{{/if}}
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
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>

{{/if}}