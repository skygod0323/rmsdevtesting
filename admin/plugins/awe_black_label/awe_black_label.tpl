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
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.awe_black_label.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.awe_black_label.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.awe_black_label.divider_configuration}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_white_label_url}} (*):</td>
			<td class="de_control">
				<input type="text" name="white_label_url" maxlength="400" class="dyn_full_size" value="{{$smarty.post.white_label_url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_white_label_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_app_secret}} (*):</td>
			<td class="de_control">
				<input type="text" name="app_secret" maxlength="400" class="dyn_full_size" value="{{$smarty.post.app_secret}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_app_secret_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if count($smarty.post.languages)>0}}
			<tr>
				<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_language}} (*):</td>
				<td class="de_control">
					<select name="language_code">
						<option value="auto" {{if $smarty.post.language_code=='auto'}}selected="selected"{{/if}}>{{$lang.plugins.awe_black_label.field_language_auto}}</option>
						{{foreach from=$smarty.post.languages|smarty:nodefaults item="language"}}
							<option value="{{$language.code}}" {{if $smarty.post.language_code==$language.code}}selected="selected"{{/if}}>{{$language.enName}}</option>
						{{/foreach}}
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_language_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.plugins.awe_black_label.field_members_status_update}} (*):</td>
			<td class="de_control">
				<input type="text" name="member_status_refresh_interval" maxlength="10" size="10" value="{{$smarty.post.member_status_refresh_interval|default:"60"}}"/> {{$lang.plugins.awe_black_label.field_members_status_update_min}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_members_status_update_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.awe_black_label.field_enable_debug}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_debug_enabled" name="is_debug_enabled" value="1" {{if $smarty.post.is_debug_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.awe_black_label.field_enable_debug_enabled}}</label></div>
				{{if $smarty.post.is_debug_enabled==1}}
					(<a href="{{$page_name}}?plugin_id=awe_black_label&amp;action=get_log" rel="external">{{$lang.plugins.awe_black_label.field_enable_debug_log}}</a>)
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_enable_debug_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="is_debug_enabled_on">
			<td class="de_label de_dependent">{{$lang.plugins.awe_black_label.field_debug_ips}}:</td>
			<td class="de_control">
				<input type="text" name="debug_ips" size="10" class="dyn_full_size" value="{{$smarty.post.debug_ips}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_debug_ips_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.awe_black_label.divider_display}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.awe_black_label.field_niche}}:</td>
			<td class="de_control">
				<select name="niche">
					<option value="">{{$lang.plugins.awe_black_label.field_niche_all}}</option>
					<option value="girls" {{if $smarty.post.niche=='girls'}}selected="selected"{{/if}}>{{$lang.plugins.awe_black_label.field_niche_girls}}</option>
					<option value="boys" {{if $smarty.post.niche=='boys'}}selected="selected"{{/if}}>{{$lang.plugins.awe_black_label.field_niche_boys}}</option>
					<option value="tranny" {{if $smarty.post.niche=='tranny'}}selected="selected"{{/if}}>{{$lang.plugins.awe_black_label.field_niche_tranny}}</option>
					<option value="celebrity" {{if $smarty.post.niche=='celebrity'}}selected="selected"{{/if}}>{{$lang.plugins.awe_black_label.field_niche_celebrity}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_niche_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.awe_black_label.field_primary_button_bg}}:</td>
			<td class="de_control">
				<input type="text" name="primary_button_bg" size="10" class="dyn_full_size" value="{{$smarty.post.primary_button_bg}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_primary_button_bg_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.awe_black_label.field_primary_button_color}}:</td>
			<td class="de_control">
				<input type="text" name="primary_button_color" size="10" class="dyn_full_size" value="{{$smarty.post.primary_button_color}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_primary_button_color_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.awe_black_label.field_terms_link_color}}:</td>
			<td class="de_control">
				<input type="text" name="terms_link_color" size="10" class="dyn_full_size" value="{{$smarty.post.terms_link_color}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_terms_link_color_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.awe_black_label.field_terms_toggle_color}}:</td>
			<td class="de_control">
				<input type="text" name="terms_toggle_color" size="10" class="dyn_full_size" value="{{$smarty.post.terms_toggle_color}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.awe_black_label.field_terms_toggle_color_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.awe_black_label.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>