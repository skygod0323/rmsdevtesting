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
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.post.$table_key_name}}"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_vast_profiles_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.vast_profile_add}}{{else}}{{$lang.settings.vast_profile_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/829-how-to-maximize-your-tube-revenue-with-kvs-advertising-system">How to maximize your tube revenue with KVS advertising system</a></span>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.settings.vast_profile_field_title}} (*):</td>
			<td class="de_control">
				<div class="de_str_len">
					<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.vast_profile_field_enable_debug}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.settings.vast_profile_field_enable_debug_enabled}}</label></div>
				{{if $smarty.post.is_debug_enabled==1}}
					(<a href="{{$page_name}}?action=view_debug_log&amp;id={{$smarty.post.$table_key_name}}" rel="external">{{$lang.settings.vast_profile_field_enable_debug_log}}</a>)
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.settings.vast_profile_field_usages}}:</td>
				<td class="de_control">
					{{if count($smarty.post.usages)>0}}
						{{foreach name=data item=item from=$smarty.post.usages|smarty:nodefaults}}
							<a href="{{$item.url}}">{{if $item.is_embed==1}}{{$lang.settings.vast_profile_field_usages_embed}}{{else}}{{$lang.settings.vast_profile_field_usages_player}}{{/if}} - {{if $item.type=='pre'}}{{$lang.settings.vast_profile_field_usages_type_pre}}{{elseif $item.type=='post'}}{{$lang.settings.vast_profile_field_usages_type_post}}{{/if}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.settings.vast_profile_field_usages_none}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{section name=data start=0 step=1 loop=$limit_providers}}
			{{assign var="index" value=$smarty.section.data.iteration-1}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.vast_profile_divider_advertiser|replace:"%1%":$smarty.section.data.iteration}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.vast_profile_field_enable}}:</td>
				<td class="de_control">
					<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="provider_{{$index}}" name="is_provider_{{$index}}" value="1" {{if $smarty.post.providers[$index].is_enabled==1 || ($smarty.get.action=='add_new' && $index==0)}}checked="checked"{{/if}}/><label>{{$lang.settings.vast_profile_field_enable_enabled}}</label></div>
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label de_required">{{$lang.settings.vast_profile_field_vast_url}} (*):</td>
				<td class="de_control">
					<input type="text" name="provider_{{$index}}_url" class="dyn_full_size" value="{{$smarty.post.providers[$index].url}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.vast_profile_field_vast_url_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label">{{$lang.settings.vast_profile_field_vast_alt_url}}:</td>
				<td class="de_control">
					<textarea name="provider_{{$index}}_alt_url" class="dyn_full_size" rows="3" cols="40">{{$smarty.post.providers[$index].alt_url}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.vast_profile_field_vast_alt_url_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label">{{$lang.settings.vast_profile_field_include_countries}}:</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_countries.php</span>
							<span class="js_param">validate_input=true</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=provider_{{$index}}_countries[]</span>
							<span class="js_param">empty_message={{$lang.settings.vast_profile_field_include_countries_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.providers[$index].countries|smarty:nodefaults}}
							<input type="hidden" name="provider_{{$index}}_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_country_{{$index}}" class="fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.settings.vast_profile_field_include_countries_all}}"/>
						</div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<span class="de_hint">{{$lang.settings.vast_profile_field_include_countries_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label">{{$lang.settings.vast_profile_field_exclude_countries}}:</td>
				<td class="de_control">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_countries.php</span>
							<span class="js_param">validate_input=true</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=provider_{{$index}}_exclude_countries[]</span>
							<span class="js_param">empty_message={{$lang.settings.vast_profile_field_exclude_countries_empty}}</span>
						</div>
						<div class="list"></div>
						{{foreach name="data" item="item" from=$smarty.post.providers[$index].exclude_countries|smarty:nodefaults}}
							<input type="hidden" name="provider_{{$index}}_exclude_countries[]" value="{{$item}}" alt="{{$list_countries[$item]}}"/>
						{{/foreach}}
						<div class="controls">
							<input type="text" name="new_country_{{$index}}" class="fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.settings.vast_profile_field_exclude_countries_all}}"/>
						</div>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<span class="de_hint">{{$lang.settings.vast_profile_field_exclude_countries_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label">{{$lang.settings.vast_profile_field_include_referers}}:</td>
				<td class="de_control">
					<textarea name="provider_{{$index}}_referers" class="dyn_full_size" rows="3" cols="40">{{$smarty.post.providers[$index].referers}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.vast_profile_field_include_referers_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label">{{$lang.settings.vast_profile_field_exclude_referers}}:</td>
				<td class="de_control">
					<textarea name="provider_{{$index}}_exclude_referers" class="dyn_full_size" rows="3" cols="40">{{$smarty.post.providers[$index].exclude_referers}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.vast_profile_field_exclude_referers_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr class="provider_{{$index}}_on">
				<td class="de_label">{{$lang.settings.vast_profile_field_weight}}:</td>
				<td class="de_control">
					<input type="text" name="provider_{{$index}}_weight" maxlength="10" class="fixed_100" value="{{$smarty.post.providers[$index].weight|default:"0"}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.vast_profile_field_weight_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/section}}
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
	</table>
</form>

{{else}}

{{assign var=can_delete value=1}}
{{assign var=can_invoke_additional value=1}}
{{if $can_delete==1}}
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
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text==''}}disabled="disabled"{{/if}}/>
					</td>
					<td class="dgf_advanced_link">
						<a href="javascript:stub()" class="dgf_columns">{{$lang.common.dg_filter_columns}}</a>
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
				{{foreach name="data_profiles" item="item_profile" from=$data|smarty:nodefaults}}
					{{assign var="group_colspan" value=0}}
					{{foreach from=$table_fields|smarty:nodefaults item="field"}}
						{{if $field.is_enabled==1}}
							{{assign var="group_colspan" value=$group_colspan+1}}
						{{/if}}
					{{/foreach}}
					<tr class="dg_group_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_profile.$table_key_name}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
						<td colspan="{{$group_colspan}}">
							<a href="{{$page_name}}?action=change&amp;item_id={{$item_profile.$table_key_name}}" {{if $item_profile.has_errors==1}}class="highlighted_text"{{elseif $item_profile.has_warnings==1 || $item_profile.is_debug_enabled==1}}class="warning_text"{{/if}}>{{$item_profile.title}}</a>
							{{if $item_profile.is_debug_enabled==1}}
									<span class="warning_text">({{$lang.settings.vast_profile_warning_debug_enabled}})</span>
							{{/if}}
						</td>
						<td>
							{{if $item_profile.$table_key_name!=''}}
								<a href="{{$page_name}}?action=change&amp;item_id={{$item_profile.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								{{if $can_invoke_additional==1}}
									<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
										<span class="js_params">
											<span class="js_param">id={{$item_profile.$table_key_name}}</span>
											<span class="js_param">name={{$item_profile.title}}</span>
											{{if $item_profile.is_debug_enabled==1}}
												<span class="js_param">enable_debug_hide=true</span>
											{{else}}
												<span class="js_param">disable_debug_hide=true</span>
											{{/if}}
											{{if count($item_profile.usages)>0}}
												<span class="js_param">delete_disable=true</span>
											{{/if}}
										</span>
									</a>
								{{/if}}
							{{/if}}
						</td>
					</tr>
					{{foreach name="data" item="item" from=$item_profile.providers|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
							{{assign var="table_columns_display_mode" value="data"}}
							{{include file="table_columns_inc.tpl"}}
							<td></td>
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
					</li>
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
								<option value="enable_debug">{{$lang.common.dg_batch_actions_enable_debug}}</option>
								<option value="disable_debug">{{$lang.common.dg_batch_actions_disable_debug}}</option>
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
{{include file="navigation.tpl"}}

{{/if}}