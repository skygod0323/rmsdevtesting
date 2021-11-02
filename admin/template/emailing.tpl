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
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="start"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.users.emailing_create}}</div></td>
		</tr>
		<tr class="option_email option_test">
			<td class="de_label de_required">{{$lang.users.emailing_field_subject}} (*):</td>
			<td class="de_control">
				<input type="text" name="subject" maxlength="255" class="dyn_full_size" value="{{$smarty.post.subject}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.emailing_field_subject_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="option_email option_test option_internal">
			<td class="de_label de_required">{{$lang.users.emailing_field_body}} (*):</td>
			<td class="de_control">
				<textarea name="body" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="15">{{$smarty.post.body}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.users.emailing_field_body_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.emailing_field_message_type}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_radio">
					<div class="de_lv_pair"><input id="option_test" type="radio" name="send_to" value="1" checked="checked"/><span>{{$lang.users.emailing_field_message_type_test}}</span></div>
					<div class="de_lv_pair"><input id="option_email" type="radio" name="send_to" value="2"/><span>{{$lang.users.emailing_field_message_type_email}}</span></div>
					<div class="de_lv_pair"><input id="option_internal" type="radio" name="send_to" value="3"/><span>{{$lang.users.emailing_field_message_type_internal}}</span></div>
					<div class="de_lv_pair"><input id="option_export" type="radio" name="send_to" value="4"/><span>{{$lang.users.emailing_field_message_type_export}}</span></div>
				</div>
			</td>
		</tr>
		<tr class="option_email option_test">
			<td class="de_label de_required">{{$lang.users.emailing_field_headers}} (*):</td>
			<td class="de_control">
				<textarea name="headers" class="dyn_full_size" cols="40" rows="5">{{$smarty.session.save.$page_name.headers|default:$config.default_email_headers}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.emailing_field_headers_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="option_email option_internal">
			<td class="de_label de_required">{{$lang.users.emailing_field_delay}} (*):</td>
			<td class="de_control">
				<input type="text" name="delay" maxlength="32" class="fixed_100" value="{{$smarty.session.save.$page_name.delay|default:"0"}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.emailing_field_delay_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="option_test">
			<td class="de_label de_required">{{$lang.users.emailing_field_test_mailbox}} (*):</td>
			<td class="de_control">
				<input type="text" name="test_email" maxlength="255" class="dyn_full_size" value="{{$smarty.session.save.$page_name.test_email}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.emailing_field_test_mailbox_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="option_internal">
			<td class="de_label de_required">{{$lang.users.emailing_field_sender}} (*):</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_users.php</span>
					</div>
					<input type="text" name="user_from" maxlength="255" class="fixed_200" value="{{$smarty.session.save.$page_name.user_from}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.emailing_field_sender_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="option_email option_internal option_export">
			<td class="de_label de_required">{{$lang.users.emailing_field_receivers}} (*):</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="user_status_ids[]" value="2" checked="checked"/><label>{{$lang.users.emailing_field_receivers_active}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="user_status_ids[]" value="3" checked="checked"/><label>{{$lang.users.emailing_field_receivers_premium}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="user_status_ids[]" value="6" checked="checked"/><label>{{$lang.users.emailing_field_receivers_webmasters}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2"><input type="submit" value="{{$lang.users.emailing_btn_send}}"/></td>
		</tr>
	</table>
</form>