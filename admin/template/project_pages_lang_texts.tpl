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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_text_items}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.website_ui.text_item_add}}{{else}}{{$lang.website_ui.text_item_edit|replace:"%1%":$smarty.post.external_id}}{{/if}}</div></td>
		</tr>
		{{if $smarty.get.action=='add_new'}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.text_item_field_id}} (*):</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" class="dyn_full_size" value="{{$smarty.post.external_id}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.text_item_field_id_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_label">{{$lang.website_ui.text_item_field_id}}:</td>
				<td class="de_control">{{$smarty.post.external_id}}</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.website_ui.text_item_field_insert_code}}:</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new'}}
					<span class="de_autopopulate" data-autopopulate-from="external_id" data-autopopulate-pattern='{{$smarty.ldelim}}$lang.${value}{{$smarty.rdelim}}'></span>
				{{else}}
					{{$smarty.ldelim}}$lang.{{$smarty.post.external_id}}{{$smarty.rdelim}}
				{{/if}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.text_item_field_insert_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.text_item_field_text_default}} (*):</td>
			<td class="de_control">
				<input type="text" name="text_default" class="dyn_full_size" value="{{$smarty.post.text_default}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/>
					<span class="de_hint">
						{{if $smarty.post.is_url==1}}
							{{$lang.website_ui.text_item_field_text_default_hint_url}}
						{{else}}
							{{$lang.website_ui.text_item_field_text_default_hint}}
						{{/if}}
					</span>
				{{/if}}
			</td>
		</tr>
		{{foreach from=$languages|smarty:nodefaults item="language"}}
			<tr>
				<td class="de_label">{{$lang.website_ui.text_item_field_text_lang|replace:"%1%":$language.title}}:</td>
				<td class="de_control">
					{{assign var="language_code" value=$language.code}}
					{{assign var="language_key" value="text_`$language.code`"}}
					<input type="text" name="text_{{$language_code}}" class="dyn_full_size" value="{{$smarty.post.$language_key}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						<span class="de_hint">
							{{if $smarty.post.is_url==1}}
								{{$lang.website_ui.text_item_field_text_lang_hint_url|replace:"%1%":$language.title}}
							{{else}}
								{{$lang.website_ui.text_item_field_text_lang_hint|replace:"%1%":$language.title}}
							{{/if}}
						</span>
					{{/if}}
				</td>
			</tr>
		{{/foreach}}
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

{{if in_array('website_ui|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if $can_delete==1}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_prefix!=''}}dgf_selected{{/if}}">{{$lang.website_ui.text_item_filter_prefix}}:</td>
					<td class="dgf_control">
						<select name="se_prefix">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="urls" {{if $smarty.session.save.$page_name.se_prefix=='urls'}}selected="selected"{{/if}}>{{$lang.website_ui.text_item_filter_prefix_urls}}</option>
							<option value="html" {{if $smarty.session.save.$page_name.se_prefix=='html'}}selected="selected"{{/if}}>{{$lang.website_ui.text_item_filter_prefix_html}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_page!=''}}dgf_selected{{/if}}">{{$lang.website_ui.text_item_filter_page}}:</td>
					<td class="dgf_control">
						<select name="se_page">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$pages|smarty:nodefaults item="page"}}
								<option value="{{$page.external_id}}" {{if $smarty.session.save.$page_name.se_page==$page.external_id}}selected="selected"{{/if}}>{{$page.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_missing_translation!=''}}dgf_selected{{/if}}">{{$lang.website_ui.text_item_filter_missing_translation}}:</td>
					<td class="dgf_control">
						<select name="se_missing_translation">
							<option value="">{{$lang.common.select_default_option}}</option>
							{{foreach from=$languages|smarty:nodefaults item="language"}}
								<option value="{{$language.code}}" {{if $smarty.session.save.$page_name.se_missing_translation==$language.code}}selected="selected"{{/if}}>{{$language.title}}</option>
							{{/foreach}}
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
							<span class="js_param">confirm={{$lang.website_ui.text_item_action_delete_confirm|replace:"%1%":'${name}'}}</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=templates_search.php?no_filter=true&amp;se_contents=%24lang.${id}</span>
						<span class="js_param">title={{$lang.website_ui.text_item_action_find_usages}}</span>
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
					<span class="js_param">confirm={{$lang.website_ui.text_item_batch_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}