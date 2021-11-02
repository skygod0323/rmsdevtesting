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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.recaptcha.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.recaptcha.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.recaptcha.field_enable}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_enabled" name="is_enabled" value="1" {{if $smarty.post.is_enabled==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.recaptcha.field_enable_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.recaptcha.field_enable_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">
				<div class="is_enabled_off">{{$lang.plugins.recaptcha.field_site_key}}:</div>
				<div class="is_enabled_on de_required">{{$lang.plugins.recaptcha.field_site_key}} (*):</div>
			</td>
			<td class="de_control">
				<input type="text" name="site_key" maxlength="400" class="dyn_full_size is_enabled_on" value="{{$smarty.post.site_key}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.recaptcha.field_site_key_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">
				<div class="is_enabled_off">{{$lang.plugins.recaptcha.field_secret_key}}:</div>
				<div class="is_enabled_on de_required">{{$lang.plugins.recaptcha.field_secret_key}} (*):</div>
			</td>
			<td class="de_control">
				<input type="text" name="secret_key" maxlength="400" class="dyn_full_size is_enabled_on" value="{{$smarty.post.secret_key}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.recaptcha.field_secret_key_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_dependent">{{$lang.plugins.recaptcha.field_aliases}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_aliases" name="is_aliases" class="is_enabled_on" value="1" {{if $smarty.post.is_aliases==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.recaptcha.field_aliases_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.recaptcha.field_aliases_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="is_enabled_on is_aliases_on">
			<td></td>
			<td class="de_control">
				<table>
					<tr>
						<td class="nowrap">{{$lang.plugins.recaptcha.field_alias_domain}}:&nbsp;</td>
						<td>
							{{section name="aliases" loop=5}}
								<input type="text" name="aliases[{{$smarty.section.aliases.index}}][domain]" maxlength="400" class="fixed_150" value="{{$smarty.post.aliases[$smarty.section.aliases.index].domain}}"/>
							{{/section}}
						</td>
					</tr>
					<tr>
						<td class="nowrap">{{$lang.plugins.recaptcha.field_site_key}}:&nbsp;</td>
						<td>
							{{section name="aliases" loop=5}}
								<input type="text" name="aliases[{{$smarty.section.aliases.index}}][site_key]" maxlength="400" class="fixed_150" value="{{$smarty.post.aliases[$smarty.section.aliases.index].site_key}}"/>
							{{/section}}
						</td>
					</tr>
					<tr>
						<td class="nowrap">{{$lang.plugins.recaptcha.field_secret_key}}:&nbsp;</td>
						<td>
							{{section name="aliases" loop=5}}
								<input type="text" name="aliases[{{$smarty.section.aliases.index}}][secret_key]" maxlength="400" class="fixed_150" value="{{$smarty.post.aliases[$smarty.section.aliases.index].secret_key}}"/>
							{{/section}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.recaptcha.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>