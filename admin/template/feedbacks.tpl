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

{{if $smarty.get.action=='change'}}

{{if in_array('feedbacks|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{if $smarty.post.email!=''}}
			<input type="hidden" name="response_email" value="{{$smarty.post.email}}"/>
		{{/if}}
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_feedbacks}}</a> / {{$lang.users.feedback_edit|replace:"%1%":$smarty.post.feedback_id}}</div></td>
		</tr>
		{{if $smarty.post.status_id==1 && $smarty.post.email!=''}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.users.feedback_divider_user_entry}}</div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_status}}:</td>
			<td class="de_control">
				{{if $smarty.post.status_id==1}}
					<select name="status_id">
						<option value="1">{{$lang.users.feedback_field_status_new}}</option>
						<option value="2">{{$lang.users.feedback_field_status_closed}}</option>
					</select>
				{{elseif $smarty.post.status_id==2}}
					{{if $smarty.post.response!=''}}
						{{$lang.users.feedback_field_status_replied}}
					{{else}}
						{{$lang.users.feedback_field_status_closed}}
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.user_id>0}}
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_user}}:</td>
				<td class="de_control">
					{{if $smarty.post.user!=''}}
						{{if in_array('users|view',$smarty.session.permissions)}}
							<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
						{{else}}
							{{$smarty.post.user}}
						{{/if}}
					{{else}}
						{{$lang.common.user_deleted|replace:"%1%":$smarty.post.user_id}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_ip}}:</td>
			<td class="de_control">
				{{$smarty.post.ip}}{{if $smarty.post.country!=''}} ({{$smarty.post.country}}){{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_user_agent}}:</td>
			<td class="de_control">
				{{$smarty.post.user_agent}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_referer}}:</td>
			<td class="de_control">
				{{if $smarty.post.referer!=''}}
					<a href="{{$smarty.post.referer}}" rel="external">{{$smarty.post.referer}}</a>
				{{/if}}
			</td>
		</tr>
		{{if $options.ENABLE_FEEDBACK_FIELD_1==1}}
			<tr>
				<td class="de_label">{{$options.FEEDBACK_FIELD_1_NAME}}:</td>
				<td class="de_control">
					{{$smarty.post.custom1}}
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_FEEDBACK_FIELD_2==1}}
			<tr>
				<td class="de_label">{{$options.FEEDBACK_FIELD_2_NAME}}:</td>
				<td class="de_control">
					{{$smarty.post.custom2}}
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_FEEDBACK_FIELD_3==1}}
			<tr>
				<td class="de_label">{{$options.FEEDBACK_FIELD_3_NAME}}:</td>
				<td class="de_control">
					{{$smarty.post.custom3}}
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_FEEDBACK_FIELD_4==1}}
			<tr>
				<td class="de_label">{{$options.FEEDBACK_FIELD_4_NAME}}:</td>
				<td class="de_control">
					{{$smarty.post.custom4}}
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_FEEDBACK_FIELD_5==1}}
			<tr>
				<td class="de_label">{{$options.FEEDBACK_FIELD_5_NAME}}:</td>
				<td class="de_control">
					{{$smarty.post.custom5}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.email!=''}}
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_email}}:</td>
				<td class="de_control">
					<a href="mailto:{{$smarty.post.email}}">{{$smarty.post.email}}</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_added_date}}:</td>
			<td class="de_control">{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_closed_date}}:</td>
			<td class="de_control">{{$smarty.post.closed_date|date_format:$smarty.session.userdata.full_date_format}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_subject}}:</td>
			<td class="de_control"><input type="text" class="dyn_full_size readonly_field" value="{{$smarty.post.subject}}" readonly="readonly"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.feedback_field_message}}:</td>
			<td class="de_control"><textarea class="dyn_full_size readonly_field" rows="5" cols="40" readonly="readonly">{{$smarty.post.message}}</textarea></td>
		</tr>
		{{if $smarty.post.status_id==1 && $smarty.post.email!=''}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.users.feedback_divider_response}}</div></td>
			</tr>
			<tr>
				<td class="de_label"></td>
				<td class="de_control"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="do_reply" name="do_reply" value="1"/><span>{{$lang.users.feedback_field_response_do_reply}}</span></div></td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required do_reply_on">{{$lang.users.feedback_field_response_subject}} (*):</div>
					<div class="do_reply_off">{{$lang.users.feedback_field_response_subject}}:</div>
				</td>
				<td class="de_control"><input type="text" name="response_subject" class="dyn_full_size do_reply_on" value="{{if $smarty.post.subject!=''}}RE: {{$smarty.post.subject}}{{else}}{{$smarty.session.save.$page_name.subject}}{{/if}}"/></td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required do_reply_on">{{$lang.users.feedback_field_response_headers}} (*):</div>
					<div class="do_reply_off">{{$lang.users.feedback_field_response_headers}}:</div>
				</td>
				<td class="de_control"><textarea name="response_headers" class="dyn_full_size do_reply_on" rows="5" cols="40">{{$smarty.session.save.$page_name.headers|default:$config.default_email_headers}}</textarea></td>
			</tr>
			<tr>
				<td class="de_label">
					<div class="de_required do_reply_on">{{$lang.users.feedback_field_response_body}} (*):</div>
					<div class="do_reply_off">{{$lang.users.feedback_field_response_body}}:</div>
				</td>
				<td class="de_control"><textarea name="response_body" class="dyn_full_size do_reply_on" rows="10" cols="40">{{$smarty.post.response}}</textarea></td>
			</tr>
		{{elseif $smarty.post.response!=''}}
			<tr>
				<td class="de_label">{{$lang.users.feedback_field_response_body}}:</td>
				<td class="de_control"><textarea class="dyn_full_size readonly_field" rows="10" cols="40" readonly="readonly">{{$smarty.post.response}}</textarea></td>
			</tr>
		{{/if}}
		{{if $can_edit_all==1}}
			{{if $smarty.post.status_id==1}}
				<tr>
					<td class="de_action_group" colspan="2">
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					</td>
				</tr>
			{{/if}}
		{{/if}}
	</table>
</form>

{{else}}

{{if in_array('feedbacks|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('feedbacks|edit_all',$smarty.session.permissions)}}
	{{assign var=can_close value=1}}
{{else}}
	{{assign var=can_close value=0}}
{{/if}}
{{if $can_delete==1 || $can_close}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
{{if $can_delete==1 || $can_close}}
	{{assign var=can_invoke_batch value=1}}
{{else}}
	{{assign var=can_invoke_batch value=0}}
{{/if}}

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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id>0}}dgf_selected{{/if}}">{{$lang.users.feedback_filter_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id==0}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id==1}}selected="selected"{{/if}}>{{$lang.users.feedback_filter_status_new}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id==2}}selected="selected"{{/if}}>{{$lang.users.feedback_filter_status_closed}}</option>
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
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.$table_key_name}}</span>
									{{if $item.status_id==2 || $item.status_id==21}}
										<span class="js_param">close_hide=true</span>
									{{/if}}
								</span>
							</a>
						{{/if}}
					</td>
				</tr>
				{{/foreach}}
			</table>
			{{if $can_invoke_additional==1}}
				<ul class="dg_additional_menu_template">
					{{if $can_delete==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						</li>
					{{/if}}
					{{if $can_close==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=close&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.users.feedback_action_mark_closed}}</span>
							<span class="js_param">hide=${close_hide}</span>
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
								{{if $can_close==1}}
									<option value="close">{{$lang.users.feedback_batch_action_mark_closed}}</option>
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
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}