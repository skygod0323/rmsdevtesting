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
		<input type="hidden" name="action" value="generate"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.users_generator.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.users_generator.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.users_generator.divider_parameters}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.users_generator.field_generate}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="generate" name="generate">
						<option value="access_codes" {{if $smarty.post.generate=='access_codes'}}selected="selected"{{/if}}>{{$lang.plugins.users_generator.field_generate_access_codes}}</option>
						<option value="accounts" {{if $smarty.post.generate=='accounts'}}selected="selected"{{/if}}>{{$lang.plugins.users_generator.field_generate_accounts}}</option>
					</select>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.users_generator.field_amount}} (*):</td>
			<td class="de_control">
				<input type="text" name="amount" maxlength="10" class="dyn_full_size" value="{{$smarty.post.amount}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.users_generator.field_amount_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="option_unlimited">{{$lang.plugins.users_generator.field_access_type}}:</div>
				<div class="option_duration option_tokens de_required">{{$lang.plugins.users_generator.field_access_type}} (*):</div>
			</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<table class="control_group">
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_unlimited" type="radio" name="access_type" value="1" {{if $smarty.post.access_type==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.users_generator.field_access_type_premium_unlimited}}</label></div>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.plugins.users_generator.field_access_type_premium_unlimited_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_duration" type="radio" name="access_type" value="2" {{if $smarty.post.access_type==2}}checked="checked"{{/if}}/><label>{{$lang.plugins.users_generator.field_access_type_premium_duration}}:</label></div>
								<input type="text" name="duration" maxlength="10" class="fixed_width_150 option_duration" value="{{$smarty.post.duration}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.plugins.users_generator.field_access_type_premium_duration_hint}}</span>
								{{/if}}
							</td>
						</tr>
						<tr>
							<td>
								<div class="de_lv_pair"><input id="option_tokens" type="radio" name="access_type" value="3" {{if $smarty.post.access_type==3}}checked="checked"{{/if}}/><label>{{$lang.plugins.users_generator.field_access_type_tokens}}:</label></div>
								<input type="text" name="tokens" maxlength="10" class="fixed_width_150 option_tokens" value="{{$smarty.post.tokens}}"/>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$lang.plugins.users_generator.field_access_type_tokens_hint}}</span>
								{{/if}}
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr class="generate_accounts">
			<td class="de_label de_required">{{$lang.plugins.users_generator.field_username_length}} (*):</td>
			<td class="de_control">
				<input type="text" name="username_length" maxlength="10" class="dyn_full_size" value="{{$smarty.post.username_length}}"/>
			</td>
		</tr>
		<tr class="generate_accounts">
			<td class="de_label de_required">{{$lang.plugins.users_generator.field_password_length}} (*):</td>
			<td class="de_control">
				<input type="text" name="password_length" maxlength="10" class="dyn_full_size" value="{{$smarty.post.password_length}}"/>
			</td>
		</tr>
		<tr class="generate_access_codes">
			<td class="de_label de_required">{{$lang.plugins.users_generator.field_access_code_length}} (*):</td>
			<td class="de_control">
				<input type="text" name="access_code_length" maxlength="10" class="dyn_full_size" value="{{$smarty.post.access_code_length}}"/>
			</td>
		</tr>
		<tr class="generate_access_codes">
			<td class="de_label">{{$lang.plugins.users_generator.field_access_code_referral_award}}:</td>
			<td class="de_control">
				<input type="text" name="access_code_referral_award" maxlength="10" class="dyn_full_size" value="{{$smarty.post.access_code_referral_award}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.users_generator.field_access_code_referral_award_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.users_generator.btn_generate}}"/>
			</td>
		</tr>
		{{if $smarty.get.action=='results'}}
			{{if $smarty.post.generate=='access_codes'}}
				<tr>
					<td class="de_separator" colspan="2"><div>{{$lang.plugins.users_generator.divider_summary_access_codes}}</div></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.users_generator.field_access_codes}}:</td>
					<td class="de_control">
						<textarea rows="5" cols="4" class="dyn_full_size">{{$smarty.post.results}}</textarea>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.users_generator.field_access_codes_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_separator" colspan="2"><div>{{$lang.plugins.users_generator.divider_summary_accounts}}</div></td>
				</tr>
				<tr>
					<td class="de_label">{{$lang.plugins.users_generator.field_users}}:</td>
					<td class="de_control">
						<textarea rows="5" cols="4" class="dyn_full_size">{{$smarty.post.results}}</textarea>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.plugins.users_generator.field_users_hint}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		{{/if}}
	</table>
</form>