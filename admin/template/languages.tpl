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
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_languages_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.language_add}}{{else}}{{$lang.settings.language_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.language_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.language_field_title}} (*):</td>
			<td class="de_control">
				<div class="de_str_len">
					<input type="text" name="title" maxlength="100" class="dyn_full_size" value="{{$smarty.post.title}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.settings.language_field_code}} (*):</td>
			<td class="de_control">
				<input type="text" name="code" maxlength="2" class="dyn_full_size" {{if $smarty.get.action!='add_new'}}disabled="disabled"{{/if}} value="{{$smarty.post.code}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.language_field_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.language_divider_scope}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_videos}}:</td>
			<td class="de_control">
				<select name="translation_scope_videos">
					<option value="0" {{if $smarty.post.translation_scope_videos==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_videos==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_albums}}:</td>
			<td class="de_control">
				<select name="translation_scope_albums">
					<option value="0" {{if $smarty.post.translation_scope_albums==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_albums==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_content_sources}}:</td>
			<td class="de_control">
				<select name="translation_scope_content_sources">
					<option value="0" {{if $smarty.post.translation_scope_content_sources==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_content_sources==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_content_source_groups}}:</td>
			<td class="de_control">
				<select name="translation_scope_content_sources_groups">
					<option value="0" {{if $smarty.post.translation_scope_content_sources_groups==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_content_sources_groups==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_models}}:</td>
			<td class="de_control">
				<select name="translation_scope_models">
					<option value="0" {{if $smarty.post.translation_scope_models==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_models==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_model_groups}}:</td>
			<td class="de_control">
				<select name="translation_scope_models_groups">
					<option value="0" {{if $smarty.post.translation_scope_models_groups==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_models_groups==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_dvds}}:</td>
			<td class="de_control">
				<select name="translation_scope_dvds">
					<option value="0" {{if $smarty.post.translation_scope_dvds==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_dvds==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_dvd_groups}}:</td>
			<td class="de_control">
				<select name="translation_scope_dvds_groups">
					<option value="0" {{if $smarty.post.translation_scope_dvds_groups==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_dvds_groups==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_categories}}:</td>
			<td class="de_control">
				<select name="translation_scope_categories">
					<option value="0" {{if $smarty.post.translation_scope_categories==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_categories==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.common.object_type_category_groups}}:</td>
			<td class="de_control">
				<select name="translation_scope_categories_groups">
					<option value="0" {{if $smarty.post.translation_scope_categories_groups==0}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_all}}</option>
					<option value="1" {{if $smarty.post.translation_scope_categories_groups==1}}selected="selected"{{/if}}>{{$lang.settings.language_field_scope_title_only}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.language_divider_directories}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.language_field_directories_localize}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_directories_localize" name="is_directories_localize" value="1" {{if $smarty.post.is_directories_localize==1}}checked="checked"{{/if}}/><label>{{$lang.settings.language_field_directories_localize_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.language_field_directories_localize_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.language_field_directories_translit}}:</td>
			<td class="de_control">
				<div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_directories_translit" name="is_directories_translit" value="1" class="is_directories_localize_on" {{if $smarty.post.is_directories_translit==1}}checked="checked"{{/if}}/><label>{{$lang.settings.language_field_directories_translit_enabled}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.language_field_directories_translit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.language_field_directories_translit_rules}}:</td>
			<td class="de_control">
				<textarea name="directories_translit_rules" rows="3" class="dyn_full_size is_directories_localize_on is_directories_translit_on">{{$smarty.post.directories_translit_rules}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.settings.language_field_directories_translit_rules_hint}}</span>
				{{/if}}
			</td>
		</tr>
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
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0 || $item.$table_key_name==''}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						{{if $item.$table_key_name!=''}}
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if $can_invoke_additional==1}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.title}}</span>
									</span>
								</a>
							{{/if}}
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
{{include file="navigation.tpl"}}

{{/if}}