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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.push_notifications.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.push_notifications.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.push_notifications.field_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_enabled" name="is_enabled" value="1" {{if $smarty.post.is_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.push_notifications.field_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.push_notifications.field_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.is_library_missing==1}}
			<tr>
				<td class="de_label de_dependent">
					<div class="is_enabled_on de_required">{{$lang.plugins.push_notifications.field_js_library}} (*):</div>
					<div class="is_enabled_off">{{$lang.plugins.push_notifications.field_js_library}}:</div>
				</td>
				<td class="de_control">
					<a href="?plugin_id=push_notifications&amp;action=library">{{$lang.plugins.push_notifications.field_js_library_download}}</a>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.plugins.push_notifications.field_js_library_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_dependent">
				<div class="is_enabled_on de_required">{{$lang.plugins.push_notifications.field_refid}} (*):</div>
				<div class="is_enabled_off">{{$lang.plugins.push_notifications.field_refid}}:</div>
			</td>
			<td class="de_control">
				<input type="text" name="refid" class="is_enabled_on fixed_200" size="20" value="{{$smarty.post.refid}}"/>
				&nbsp;
				<a href="https://publisher.ad-maven.com/#/register?source_id=kvs" rel="external">{{$lang.plugins.push_notifications.field_refid_sign_up}}</a>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/>
					<span class="de_hint">
						{{if $smarty.post.is_https==1}}
							{{$lang.plugins.push_notifications.field_refid_hint_https}}
						{{else}}
							{{$lang.plugins.push_notifications.field_refid_hint_http}}
						{{/if}}
					</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_repeat}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="repeat" name="repeat" class="is_enabled_on">
						<option value="always" {{if $smarty.post.repeat=='always'}}selected="selected"{{/if}}>{{$lang.plugins.push_notifications.field_repeat_always}}</option>
						<option value="interval" {{if $smarty.post.repeat=='interval'}}selected="selected"{{/if}}>{{$lang.plugins.push_notifications.field_repeat_interval}}</option>
						<option value="once" {{if $smarty.post.repeat=='once'}}selected="selected"{{/if}}>{{$lang.plugins.push_notifications.field_repeat_once}}</option>
					</select>
					<span class="repeat_interval">
						&nbsp;
						<input type="text" name="repeat_interval" size="5" maxlength="10" value="{{$smarty.post.repeat_interval}}"/>
						&nbsp;
						{{$lang.plugins.push_notifications.field_repeat_interval_minutes}}
					</span>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.plugins.push_notifications.field_repeat_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_first_click}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="skip_first_click" value="1" {{if $smarty.post.skip_first_click==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.push_notifications.field_first_click_skip}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.push_notifications.field_first_click_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_exclude_referers}}:</td>
			<td class="de_control">
				<textarea name="exclude_referers" class="is_enabled_on dyn_full_size" rows="4" cols="20">{{$smarty.post.exclude_referers}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.push_notifications.field_exclude_referers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_include_referers}}:</td>
			<td class="de_control">
				<textarea name="include_referers" class="is_enabled_on dyn_full_size" rows="4" cols="20">{{$smarty.post.include_referers}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.push_notifications.field_include_referers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">{{$lang.plugins.push_notifications.field_exclude_members}}:</td>
			<td class="de_control">
				<select name="exclude_members" class="is_enabled_on">
					<option value="" {{if $smarty.post.exclude_members==''}}selected="selected"{{/if}}>{{$lang.plugins.push_notifications.field_exclude_members_none}}</option>
					<option value="all" {{if $smarty.post.exclude_members=='all'}}selected="selected"{{/if}}>{{$lang.plugins.push_notifications.field_exclude_members_all}}</option>
					<option value="premium" {{if $smarty.post.exclude_members=='premium'}}selected="selected"{{/if}}>{{$lang.plugins.push_notifications.field_exclude_members_premium}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.push_notifications.field_exclude_members_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.push_notifications.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>