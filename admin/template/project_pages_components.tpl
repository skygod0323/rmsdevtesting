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

{{if in_array('website_ui|edit_all',$smarty.session.permissions) || (in_array('website_ui|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_page_components}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.website_ui.page_component_add}}{{else}}{{$lang.website_ui.page_component_edit|replace:"%1%":$smarty.post.external_id}}{{/if}}</div></td>
		</tr>
		{{if $smarty.get.action=='add_new'}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.page_component_field_id}} (*):</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" class="dyn_full_size" value="{{$smarty.post.external_id}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.page_component_field_id_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_label">{{$lang.website_ui.page_component_field_id}}:</td>
				<td class="de_control">{{$smarty.post.external_id}}</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.website_ui.page_component_field_insert_code}}:</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new'}}
					<span class="de_autopopulate" data-autopopulate-from="external_id" data-autopopulate-pattern='{{$smarty.ldelim}}include file="${value}.tpl"{{$smarty.rdelim}}'></span>
				{{else}}
					{{$smarty.ldelim}}include file="{{$smarty.post.external_id}}.tpl"{{$smarty.rdelim}}
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_component_field_insert_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if count($smarty.post.template_includes)>0}}
			<tr>
				<td class="de_label">{{$lang.website_ui.page_component_field_component_includes}}:</td>
				<td class="de_control">
					{{foreach name=data from=$smarty.post.template_includes item=item}}
						<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.filename}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
					{{/foreach}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.page_component_field_template_code}} (*):</td>
			<td class="de_control">
				<textarea name="template" class="html_code_editor dyn_full_size" rows="30" cols="40">{{$smarty.post.template}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_component_field_template_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_component_field_template_doc}}:</td>
			<td class="de_control">
				<a href="http://www.smarty.net/docsv2/en/" rel="external">http://www.smarty.net/docsv2/en/</a>
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

{{if in_array('website_ui|add',$smarty.session.permissions)}}
	{{assign var=can_add value=1}}
{{else}}
	{{assign var=can_add value=0}}
{{/if}}
{{if in_array('website_ui|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if $can_delete==1 || $can_add==1}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
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
					<col width="30%"/>
					<col/>
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					<td>{{$lang.website_ui.dg_page_components_col_id}}</td>
					<td>{{$lang.website_ui.dg_page_components_col_included_in}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data key=key item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$key}}" {{if count($item)>0 || $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					<td>
						{{if is_array($smarty.post.invalid_templates) && in_array($key,$smarty.post.invalid_templates)}}
							<a href="{{$page_name}}?action=change&amp;item_id={{$key}}" class="highlighted_text">{{$key}}</a>
						{{elseif is_array($smarty.post.warning_templates) && in_array($key,$smarty.post.warning_templates)}}
							<a href="{{$page_name}}?action=change&amp;item_id={{$key}}" class="warning_text">{{$key}}</a>
						{{else}}
							<a href="{{$page_name}}?action=change&amp;item_id={{$key}}">{{$key}}</a>
						{{/if}}
					</td>
					<td>
						{{foreach name=data2 item=item2 from=$item|smarty:nodefaults}}
							{{if $item2.external_id!=''}}
								<a href="project_pages.php?action=change&amp;item_id={{$item2.external_id}}">{{$item2.title}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
							{{elseif $item2.block_uid!=''}}
								<a href="project_pages.php?action=change_block&amp;item_id={{$item2.block_uid}}&amp;item_name={{$item2.block_title}}">{{$item2.title}} / {{$item2.block_title}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
							{{elseif $item2.page_component_id!=''}}
								<a href="project_pages_components.php?action=change&amp;item_id={{$item2.page_component_id}}">{{$item2.page_component_id}}</a>{{if !$smarty.foreach.data2.last}}, {{/if}}
							{{/if}}
						{{/foreach}}
					</td>
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$key}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$key}}</span>
									<span class="js_param">name={{$key}}</span>
									<span class="js_param">existing_id={{$key|replace:".tpl":""}}</span>
									{{if count($item)!=0}}
										<span class="js_param">delete_disable=true</span>
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
							<span class="js_param">disable=${delete_disable}</span>
						</li>
					{{/if}}
					{{if $can_add==1}}
						<li class="js_params">
							<span class="js_param">href=?action=duplicate&amp;item_id=${id}&amp;external_id=${external_id}</span>
							<span class="js_param">title={{$lang.website_ui.dg_page_components_action_duplicate}}</span>
							<span class="js_param">confirm={{$lang.website_ui.dg_page_components_field_new_id|replace:"%1%":'${existing_id}'}}:</span>
							<span class="js_param">prompt_variable=external_id</span>
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