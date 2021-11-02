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

{{if $smarty.get.action=='restore_pages'}}

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

<div class="dg_wrapper">
	<div class="dgf">
		<table>
			<tr>
				<td>{{$lang.website_ui.dg_restore_pages_hint}}</td>
			</tr>
		</table>
	</div>
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="err_list hidden">
			<div class="err_header"></div>
			<div class="err_content"></div>
		</div>
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col width="10%"/>
					<col/>
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value=""/></td>
					<td>{{$lang.website_ui.dg_restore_pages_col_page_id}}</td>
					<td>{{$lang.website_ui.dg_restore_pages_col_page_display_name}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item key=key from=$deleted_pages|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}"/></td>
						<td>{{$item.external_id}}</td>
						<td>{{$item.title}}</td>
						<td>
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.external_id}}</span>
									<span class="js_param">name={{$item.title}}</span>
								</span>
							</a>
						</td>
					</tr>
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_add==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=restore_page&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.dg_restore_pages_action_restore}}</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_pages_action_restore_confirm|replace:"%1%":'${name}'}}</span>
					</li>
				{{/if}}
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=wipeout_page&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.dg_restore_pages_action_wipeout}}</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_pages_action_wipeout_confirm|replace:"%1%":'${name}'}}</span>
					</li>
				{{/if}}
			</ul>
		</div>
		{{if count($deleted_pages)!=0}}
			<div class="dgb">
				<table>
					<tr>
						<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
						<td class="dgb_control">
							<select name="batch_action">
								<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
								{{if $can_add==1}}
									<option value="restore_page">{{$lang.website_ui.dg_restore_pages_batch_actions_restore}}</option>
								{{/if}}
								{{if $can_delete==1}}
									<option value="wipeout_page">{{$lang.website_ui.dg_restore_pages_batch_actions_wipeout}}</option>
								{{/if}}
							</select>
						</td>
						<td class="dgb_control">
							<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
						</td>
						<td class="dgb_list_stats"></td>
					</tr>
				</table>
				<ul class="dgb_actions_configuration">
					<li class="js_params">
						<span class="js_param">value=wipeout_page</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_pages_batch_actions_wipeout_confirm|replace:"%1%":'${count}'}}</span>
					</li>
					<li class="js_params">
						<span class="js_param">value=restore_page</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_pages_batch_actions_restore_confirm|replace:"%1%":'${count}'}}</span>
					</li>
				</ul>
			</div>
		{{/if}}
	</form>
</div>

{{elseif $smarty.get.action=='restore_blocks'}}

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
	<div class="dgf">
		<table>
			<tr>
				<td>{{$lang.website_ui.dg_restore_blocks_hint}}</td>
			</tr>
		</table>
	</div>
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="err_list hidden">
			<div class="err_header"></div>
			<div class="err_content"></div>
		</div>
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col/>
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value=""/></td>
					<td>{{$lang.website_ui.dg_restore_blocks_col_block_type}}</td>
					<td>{{$lang.website_ui.dg_restore_blocks_col_block_name}}</td>
					<td>{{$lang.website_ui.dg_restore_blocks_col_block_directive}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$deleted_blocks|smarty:nodefaults}}
					<tr class="dg_group_header">
						<td><input type="checkbox" name="row_select[]" disabled="disabled"/></td>
						<td colspan="4"><a href="{{$page_name}}?action=change&amp;item_id={{$item.external_id}}">{{$lang.website_ui.dg_restore_blocks_col_page|replace:"%1%":$item.title}}</a></td>
					</tr>
					{{foreach name=data_blocks item=item_block from=$item.blocks|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data_blocks.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}||{{$item_block.block_id}}_{{$item_block.block_name_mod}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
							<td>{{$item_block.block_id}}</td>
							<td>{{$item_block.block_name}}</td>
							<td>{{$smarty.ldelim}}insert name="getBlock" block_id="{{$item_block.block_id}}" block_name="{{$item_block.block_name}}"{{$smarty.rdelim}}</td>
							<td>
								{{if $can_invoke_additional==1}}
									<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
										<span class="js_params">
											<span class="js_param">id={{$item.external_id}}||{{$item_block.block_id}}_{{$item_block.block_name_mod}}</span>
											<span class="js_param">name={{$item_block.block_name}}</span>
										</span>
									</a>
								{{/if}}
							</td>
						</tr>
					{{/foreach}}
				{{/foreach}}
			</table>
			{{if $can_invoke_additional==1}}
				<ul class="dg_additional_menu_template">
					{{if $can_delete==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=wipeout_block&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.website_ui.dg_restore_blocks_action_wipeout}}</span>
							<span class="js_param">confirm={{$lang.website_ui.dg_restore_blocks_action_wipeout_confirm|replace:"%1%":'${name}'}}</span>
						</li>
					{{/if}}
				</ul>
			{{/if}}
		</div>
		{{if count($deleted_blocks)!=0}}
			<div class="dgb">
				<table>
					<tr>
						{{if $can_invoke_batch==1}}
							<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
							<td class="dgb_control">
								<select name="batch_action">
									<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
									{{if $can_delete==1}}
										<option value="wipeout_block">{{$lang.website_ui.dg_restore_blocks_batch_actions_wipeout}}</option>
									{{/if}}
								</select>
							</td>
							<td class="dgb_control">
								<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
							</td>
						{{/if}}
						<td class="dgb_list_stats"></td>
					</tr>
				</table>
				<ul class="dgb_actions_configuration">
					<li class="js_params">
						<span class="js_param">value=wipeout_block</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_blocks_batch_actions_wipeout_confirm|replace:"%1%":'${count}'}}</span>
					</li>
				</ul>
			</div>
		{{/if}}
	</form>
</div>

{{elseif $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

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
	{{assign var="is_page_without_caching" value=0}}
	{{foreach item=item from=$smarty.post.blocks|smarty:nodefaults}}
		{{if $item.no_cache==1}}
			{{assign var="is_page_without_caching" value=1}}
		{{/if}}
	{{/foreach}}
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.website_ui.submenu_option_pages_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.website_ui.page_add}}{{else}}{{$lang.website_ui.page_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.page_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.page_field_display_name}} (*):</td>
			<td class="de_control">
				<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
			</td>
		</tr>
		{{if $smarty.get.action=='add_new'}}
			<tr>
				<td class="de_label de_required">{{$lang.website_ui.page_field_page_id}} (*):</td>
				<td class="de_control">
					<input type="text" name="external_id" maxlength="100" class="dyn_full_size" value="{{$smarty.post.external_id}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.page_field_page_id_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_page_id}}:</td>
				<td class="de_control">{{$smarty.post.external_id}} [<a href="{{$config.project_url}}/{{$smarty.post.external_id}}.php" rel="external">{{$config.project_url}}/{{$smarty.post.external_id}}.php</a>]</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.website_ui.page_field_cache_time}}:</td>
			<td class="de_control">
				<input type="text" name="cache_time" maxlength="32" class="fixed_100" value="{{$smarty.post.cache_time}}" {{if $is_page_without_caching==1}}disabled="disabled"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_field_cache_time_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_htaccess_rules}}:</td>
				<td class="de_control">
					<textarea class="html_code_editor dyn_full_size readonly_field" rows="4" cols="40" readonly="readonly">{{$smarty.post.htaccess_rules}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.page_field_htaccess_rules_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.website_ui.page_field_page_seo}}:</td>
				<td class="de_control">
					<a href="project_pages_lang_texts.php?no_filter=true&amp;se_prefix=html&amp;se_page={{$smarty.post.external_id}}">{{$lang.website_ui.page_field_page_seo_show}}</a>
				</td>
			</tr>
			{{if count($smarty.post.template_includes)>0}}
				<tr>
					<td class="de_label">{{$lang.website_ui.page_field_page_includes}}:</td>
					<td class="de_control">
						{{foreach name=data from=$smarty.post.template_includes item=item}}
							<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.filename}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
						{{/foreach}}
					</td>
				</tr>
			{{/if}}
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.page_field_template_code}} (*):</td>
			<td class="de_control">
				<textarea name="template" class="html_code_editor dyn_full_size" rows="30" cols="40">{{$smarty.post.template}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_field_template_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_field_template_doc}}:</td>
			<td class="de_control">
				<a href="http://www.smarty.net/docsv2/en/" rel="external">http://www.smarty.net/docsv2/en/</a>
			</td>
		</tr>
		{{if $smarty.get.action=='change' && $can_edit_all==1}}
			<tr>
				<td class="de_label"></td>
				<td class="de_action_group">
					<input type="submit" name="update_content" value="{{$lang.website_ui.page_btn_update_content}}"/>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.page_divider_advanced}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_field_status}}:</td>
			<td class="de_control">
				<select name="is_disabled">
					<option value="0" {{if $smarty.post.is_disabled==0}}selected="selected"{{/if}}>{{$lang.website_ui.page_field_status_active}}</option>
					<option value="1" {{if $smarty.post.is_disabled==1}}selected="selected"{{/if}}>{{$lang.website_ui.page_field_status_disabled}}</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_field_status_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_field_dynamic_http_params}}:</td>
			<td class="de_control">
				<input type="text" name="dynamic_http_params" maxlength="100" class="dyn_full_size" value="{{$smarty.post.dynamic_http_params}}" {{if $is_page_without_caching==1}}disabled="disabled"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_field_dynamic_http_params_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_field_is_xml}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="is_xml" value="1" {{if $smarty.post.is_xml==1}}checked="checked"{{/if}}/><span class="{{if $smarty.post.is_xml==1}}selected{{/if}} {{if $can_edit_all==0}}de_grayed{{/if}}">{{$lang.website_ui.page_field_is_xml_enabled}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_field_is_xml_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.page_field_block_access}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="access_type_id" name="access_type_id">
						<option value="0">{{$lang.common.select_default_option}}</option>
						<option value="1" {{if $smarty.post.access_type_id==1}}selected="selected"{{/if}}>{{$lang.website_ui.page_field_block_access_anonymous}}</option>
						<option value="2" {{if $smarty.post.access_type_id==2}}selected="selected"{{/if}}>{{$lang.website_ui.page_field_block_access_except_premium}}</option>
						<option value="3" {{if $smarty.post.access_type_id==3}}selected="selected"{{/if}}>{{$lang.website_ui.page_field_block_access_except_webmaster}}</option>
						<option value="4" {{if $smarty.post.access_type_id==4}}selected="selected"{{/if}}>{{$lang.website_ui.page_field_block_access_except_trusted}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.page_field_block_access_hint}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr class="access_type_id_1 access_type_id_2 access_type_id_3 access_type_id_4">
			<td class="de_label">{{$lang.website_ui.page_field_block_access_url}}:</td>
			<td class="de_control">
				<input type="text" name="access_type_redirect_url" maxlength="255" class="dyn_full_size" value="{{$smarty.post.access_type_redirect_url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.page_field_block_access_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action=='change'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.website_ui.page_divider_content}}</div></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						{{foreach item=item from=$smarty.post.blocks|smarty:nodefaults}}
							<tr class="eg_header">
								<td>
									{{if $item.is_global==1}}
										<a href="{{$page_name}}?action=change_block&amp;item_id=$global||{{$item.block_id}}||{{$item.block_name_dir}}&amp;item_name={{$item.block_name|escape}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.block_name}}</a>
									{{else}}
										<a href="{{$page_name}}?action=change_block&amp;item_id={{$smarty.post.external_id}}||{{$item.block_id}}||{{$item.block_name_dir}}&amp;item_name={{$item.block_name|escape}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.block_name}}</a>
									{{/if}}
								</td>
								<td class="nowrap {{if $item.errors==1}}highlighted_text{{/if}}">{{$item.block_id}}{{if $item.is_global==1}} [G]{{/if}}</td>
								<td {{if $item.critical_errors==1}}class="highlighted_text"{{/if}}>{{if $item.critical_errors==0}}<input type="text" name="{{if $item.is_global==1}}global_{{/if}}cache_time_{{$item.block_name_dir}}" maxlength="32" size="7" value="{{$item.cache_time}}" {{if $item.no_cache==1 || $item.is_global==1}}disabled="disabled"{{/if}}/>{{else}}{{$item.cache_time}}{{/if}}{{$lang.common.second_truncated}}</td>
								<td {{if $item.critical_errors==1}}class="highlighted_text"{{/if}}>{{if $item.is_global!=1}}$storage.{{$item.block_id}}_{{$item.block_name_dir}}{{else}}$global_storage.{{$item.block_id}}_{{$item.block_name_dir}}{{/if}}</td>
							</tr>
							{{if $item.critical_errors==0}}
								{{foreach item=param from=$item.params|smarty:nodefaults}}
									<tr class="eg_data fixed_height_30">
										<td class="nowrap">
											<span {{if $param.is_deprecated==1}}class="deprecated_text"{{/if}}>{{$param.name}} ({{$param.type_name}})</span>
											<input type="hidden" name="{{if $item.is_global==1}}global_{{/if}}is_{{$item.block_name_dir}}_{{$param.name}}" value="1"/>
										</td>
										<td colspan="2">
											{{if $param.type=='STRING' || $param.type=='INT' || $param.type=='INT_LIST'}}
												<input class="fixed_300" type="text" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}" value="{{$param.value}}" {{if $item.is_global==1}}disabled="disabled"{{/if}}/>
											{{elseif $param.type=='INT_PAIR'}}
												<input class="fixed_100" type="text" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}1" value="{{$param.value.0}}" {{if $item.is_global==1}}disabled="disabled"{{/if}}/>
												/
												<input class="fixed_100" type="text" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}2" value="{{$param.value.1}}" {{if $item.is_global==1}}disabled="disabled"{{/if}}/>
											{{elseif $param.type=='CHOICE' || $param.type=='LIST_BLOCK'}}
												<select class="fixed_300" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}" {{if $item.is_global==1}}disabled="disabled"{{/if}}>
													{{html_options options=$param.values selected=$param.value}}
												</select>
											{{elseif $param.type=='SORTING'}}
												<select class="fixed_200" name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}1" {{if $item.is_global==1}}disabled="disabled"{{/if}}>
													{{html_options options=$param.values selected=$param.value}}
												</select>
												<select name="{{if $item.is_global==1}}global_{{/if}}{{$item.block_name_dir}}_{{$param.name}}2" {{if $item.is_global==1}}disabled="disabled"{{/if}}>
													<option value="desc" {{if $param.value_modifier=='desc'}}selected="selected"{{/if}}>{{$lang.common.order_desc}}</option>
													<option value="asc" {{if $param.value_modifier=='asc'}}selected="selected"{{/if}}>{{$lang.common.order_asc}}</option>
												</select>
											{{else}}
												{{$lang.website_ui.page_blocks_parameter_enabled}}
											{{/if}}
										</td>
										<td><span class="de_hint">{{$param.desc}}</span></td>
									</tr>
								{{/foreach}}
							{{/if}}
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{/if}}
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

{{elseif $smarty.get.action=='change_block'}}

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
		<input type="hidden" name="action" value="change_block_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		<input type="hidden" name="item_name" value="{{$smarty.get.item_name}}"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>
				{{if $smarty.post.page_info.is_global=='true'}}
					<a href="project_pages_global.php">{{$lang.website_ui.submenu_option_global_blocks}}</a> / {{$lang.website_ui.block_global_edit|replace:"%1%":$smarty.post.block_name}}
				{{else}}
					<a href="{{$page_name}}">{{$lang.website_ui.submenu_option_pages_list}}</a> / <a href="{{$page_name}}?action=change&amp;item_id={{$smarty.post.page_info.external_id}}">{{$smarty.post.page_info.title}}</a> / {{$lang.website_ui.block_edit|replace:"%1%":$smarty.post.block_name}}
				{{/if}}
			</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.block_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.block_field_name}}:</td>
			<td class="de_control">{{$smarty.post.block_name}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.block_field_uid}}:</td>
			<td class="de_control">{{$smarty.post.block_uid}}</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.block_field_type}}:</td>
			<td class="de_control">
				<a href="project_blocks.php?action=show_long_desc&amp;block_id={{$smarty.post.block_id}}">{{$smarty.post.block_id}}</a>
				&nbsp;/&nbsp;
				<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.website_ui.block_field_type_expander_doc}}</a>
				&nbsp;/&nbsp;
				<a id="deftempl_expander" class="de_expand" href="javascript:stub()">{{$lang.website_ui.block_field_type_expander_template}}</a>
			</td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_label">{{$lang.website_ui.block_field_description}}:</td>
			<td class="de_control">
				<div class="dyn_full_size fixed_height_350 scrollable_message">
					{{$smarty.post.description}}
				</div>
			</td>
		</tr>
		<tr class="deftempl_expander hidden">
			<td class="de_label">{{$lang.website_ui.block_field_default_template_code}}:</td>
			<td class="de_control">
				<textarea name="default_template" class="html_code_editor dyn_full_size" rows="30" cols="40" readonly="readonly">{{$smarty.post.default_template}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.block_field_default_template_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if count($smarty.post.template_includes)>0}}
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_block_includes}}:</td>
				<td class="de_control">
					{{foreach name=data from=$smarty.post.template_includes item=item}}
						<a href="project_pages_components.php?action=change&amp;item_id={{$item.filename}}" {{if $item.errors==1}}class="highlighted_text"{{/if}}>{{$item.filename}}</a>{{if !$smarty.foreach.data.last}},{{/if}}
					{{/foreach}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.page_info.is_global=='true'}}
			<tr>
				<td class="de_label">{{$lang.website_ui.block_field_insert_code}}:</td>
				<td class="de_control">
					{{$smarty.ldelim}}insert name="getGlobal" global_id="{{$smarty.post.block_uid}}"{{$smarty.rdelim}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.website_ui.block_field_insert_code_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.website_ui.block_field_template_code}} (*):</td>
			<td class="de_control">
				<textarea name="template" class="html_code_editor dyn_full_size" rows="30" cols="40">{{$smarty.post.template}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.block_field_template_code_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.block_field_template_doc}}:</td>
			<td class="de_control">
				<a href="http://www.smarty.net/docsv2/en/" rel="external">http://www.smarty.net/docsv2/en/</a>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.block_field_cache_time}}:</td>
			<td class="de_control">
				<input type="text" name="cache_time" maxlength="32" class="fixed_100" value="{{$smarty.post.cache_time}}" {{if $smarty.post.no_cache==1}}disabled="disabled"{{/if}}/>
				&nbsp;
				<div class="de_lv_pair"><input type="checkbox" name="is_not_cached_for_members" value="1" {{if $smarty.post.no_cache==1}}disabled="disabled"{{/if}} {{if $smarty.post.is_not_cached_for_members==1}}checked="checked"{{/if}}/><label>{{$lang.website_ui.block_field_cache_time_members}}</label></div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.block_field_dynamic_http_params}}:</td>
			<td class="de_control">
				<input type="text" name="dynamic_http_params" maxlength="100" class="dyn_full_size" value="{{$smarty.post.dynamic_http_params}}" {{if $smarty.post.no_cache==1}}disabled="disabled"{{/if}}/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.block_field_dynamic_http_params_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.block_divider_params}}</div></td>
		</tr>
		{{if count($smarty.post.params)>0}}
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col width="20%"/>
							<col width="13%"/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.website_ui.block_params_col_name}}</td>
							<td>{{$lang.website_ui.block_params_col_value}}</td>
							<td>{{$lang.website_ui.block_params_col_description}}</td>
						</tr>
						{{assign var="last_group" value=""}}
						{{foreach item=item from=$smarty.post.params|smarty:nodefaults}}
							{{if ($last_group=='' || $last_group!=$item.group) && $item.group!=''}}
								{{assign var="last_group" value=$item.group}}
								<tr class="eg_group_header">
									<td colspan="3">{{$item.group_desc}}</td>
								</tr>
							{{/if}}
							<tr class="eg_data fixed_height_30">
								<td {{if $item.is_required==1}}class="de_required"{{/if}}>
									<div class="de_lv_pair de_vis_sw_checkbox">
										<input type="checkbox" id="param_{{$item.name}}" name="is_{{$item.name}}" value="1" {{if $item.is_required==1 || $item.is_enabled==1}}checked="checked"{{/if}} {{if $item.is_required==1}}disabled="disabled"{{/if}}/>
										<label class="{{if $item.is_enabled==1}}selected{{/if}} {{if $item.is_deprecated==1}}deprecated_text{{/if}}">{{$item.name}} ({{$item.type_name}})</label>
									</div>
								</td>
								<td>
									{{if $item.type=='STRING' || $item.type=='INT' || $item.type=='INT_LIST'}}
										<input class="param_{{$item.name}}_on fixed_300" type="text" name="{{$item.name}}" value="{{$item.value}}" {{if $item.is_enabled!=1}}disabled="disabled"{{/if}}/>
									{{elseif $item.type=='INT_PAIR'}}
										<input class="param_{{$item.name}}_on fixed_100" type="text" name="{{$item.name}}1" value="{{$item.value.0}}" {{if $item.is_enabled!=1}}disabled="disabled"{{/if}}/>
										/
										<input class="param_{{$item.name}}_on fixed_100" type="text" name="{{$item.name}}2" value="{{$item.value.1}}" {{if $item.is_enabled!=1}}disabled="disabled"{{/if}}/>
									{{elseif $item.type=='CHOICE' || $item.type=='LIST_BLOCK'}}
										<select class="param_{{$item.name}}_on fixed_300" name="{{$item.name}}" {{if $item.is_enabled!=1}}disabled="disabled"{{/if}}>
											{{html_options options=$item.values selected=$item.value}}
										</select>
									{{elseif $item.type=='SORTING'}}
										<select class="param_{{$item.name}}_on fixed_200" name="{{$item.name}}1" {{if $item.is_enabled!=1}}disabled="disabled"{{/if}}>
											{{html_options options=$item.values selected=$item.value}}
										</select>
										<select class="param_{{$item.name}}_on" name="{{$item.name}}2" {{if $item.is_enabled!=1}}disabled="disabled"{{/if}}>
											<option value="desc" {{if $item.value_modifier=='desc'}}selected="selected"{{/if}}>{{$lang.common.order_desc}}</option>
											<option value="asc" {{if $item.value_modifier=='asc'}}selected="selected"{{/if}}>{{$lang.common.order_asc}}</option>
										</select>
									{{/if}}
								</td>
								<td><span class="de_hint">{{$item.desc}}</span></td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_simple_text" colspan="2">{{$lang.website_ui.block_divider_params_nothing}}</td>
			</tr>
		{{/if}}
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
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
{{if in_array('website_ui|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_show_id!=''}}dgf_selected{{/if}}">{{$lang.website_ui.dg_pages_filter_show}}:</td>
					<td class="dgf_control">
						<select name="se_show_id" class="dgf_switcher">
							<option value="" {{if $smarty.session.save.$page_name.se_show_id==''}}selected="selected"{{/if}}>{{$lang.website_ui.dg_pages_filter_show_all}}</option>
							<option value="active" {{if $smarty.session.save.$page_name.se_show_id=='active'}}selected="selected"{{/if}}>{{$lang.website_ui.dg_pages_filter_show_active}}</option>
							<option value="disabled" {{if $smarty.session.save.$page_name.se_show_id=='disabled'}}selected="selected"{{/if}}>{{$lang.website_ui.dg_pages_filter_show_disabled}}</option>
							<option value="slow" {{if $smarty.session.save.$page_name.se_show_id=='slow'}}selected="selected"{{/if}}>{{$lang.website_ui.dg_pages_filter_show_slow}}</option>
							<option value="popular" {{if $smarty.session.save.$page_name.se_show_id=='popular'}}selected="selected"{{/if}}>{{$lang.website_ui.dg_pages_filter_show_popular}}</option>
						</select>
					</td>
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
					<col/>
					<col width="15%"/>
					<col width="10%"/>
					{{if $config.disable_performance_stats!='true'}}
						<col width="5%"/>
						<col width="15%"/>
					{{/if}}
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					<td>{{$lang.website_ui.dg_pages_col_page_block_name}}</td>
					<td>{{$lang.website_ui.dg_pages_col_block_id}}</td>
					<td>{{$lang.website_ui.dg_pages_col_cache}}</td>
					{{if $config.disable_performance_stats!='true'}}
						<td>{{$lang.website_ui.dg_pages_col_loads}}</td>
						<td>{{$lang.website_ui.dg_pages_col_performance}}</td>
					{{/if}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}

				{{assign var="is_page_without_caching" value=0}}
				{{foreach name=data_blocks item=item_blocks from=$item.blocks|smarty:nodefaults}}
					{{if $item_blocks.no_cache==1}}
						{{assign var="is_page_without_caching" value=1}}
					{{/if}}
				{{/foreach}}

				<tr class="dg_group_header {{if $item.is_disabled=='1'}}disabled{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.external_id}}" {{if $item.is_system==1 || $can_invoke_batch==0}}disabled="disabled"{{/if}}/></td>
					<td colspan="2"><a href="{{$page_name}}?action=change&amp;item_id={{$item.external_id}}" {{if $item.errors=='1'}}class="highlighted_text"{{elseif $item.warnings=='1'}}class="warning_text"{{/if}}>{{$item.title}} {{if $item.is_xml=='1'}}(XML){{/if}}</a></td>
					<td>{{if $item.errors!='1'}}<input type="text" name="cache_time_{{$item.external_id}}" maxlength="32" size="6" value="{{$item.cache_time}}" {{if $can_edit==0 || $is_page_without_caching}}disabled="disabled"{{/if}}/>{{else}}{{$item.cache_time}}{{/if}} {{$lang.common.second_truncated}}</td>
					{{if $config.disable_performance_stats!='true'}}
						<td class="nowrap">{{$item.total_requests|strrev|wordwrap:3:".":true|strrev|default:"0"}}{{if $item.total_requests_needs_k}}{{$lang.common.traffic_k}}{{/if}}</td>
						<td class="nowrap">{{$item.cached_avg_time_s|default:"0"|number_format:2}} / {{$item.uncached_avg_time_s|default:"0"|number_format:2}} / {{$item.cache_pc|default:"0"|intval}}% / {{$item.max_memory|default:"0"|sizeToHumanString}}</td>
					{{/if}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.external_id}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.external_id}}</span>
									<span class="js_param">name={{$item.title}}</span>
									<span class="js_param">existing_id={{$item.external_id}}</span>
									{{if $item.is_system==1}}
										<span class="js_param">delete_hide=true</span>
									{{/if}}
								</span>
							</a>
						{{/if}}
					</td>
				</tr>
					{{assign var="blocks_from_includes" value="0"}}
					{{assign var="blocks_from_includes_slow" value="0"}}
					{{assign var="blocks_from_includes_cached_time" value="0"}}
					{{assign var="blocks_from_includes_uncached_time" value="0"}}
					{{assign var="blocks_from_includes_cache_pc" value="0"}}
					{{assign var="blocks_from_includes_max_memory" value="0"}}
					{{foreach name=data_blocks item=item_blocks from=$item.blocks|smarty:nodefaults}}
						{{if $item_blocks.is_from_include==1}}
							{{assign var="blocks_from_includes" value=$blocks_from_includes+1}}
							{{assign var="blocks_from_includes_cached_time" value=$blocks_from_includes_cached_time+$item_blocks.cached_avg_time_s}}
							{{assign var="blocks_from_includes_uncached_time" value=$blocks_from_includes_uncached_time+$item_blocks.uncached_avg_time_s}}
							{{assign var="blocks_from_includes_cache_pc" value=$blocks_from_includes_cache_pc+$item_blocks.cache_pc}}
							{{assign var="blocks_from_includes_max_memory" value=$blocks_from_includes_max_memory+$item_blocks.max_memory}}
							{{if $item_blocks.is_slow==1}}
								{{assign var="blocks_from_includes_slow" value="1"}}
							{{/if}}
						{{/if}}
					{{/foreach}}
					{{if $blocks_from_includes>0}}
						{{assign var="blocks_from_includes_cache_pc" value=$blocks_from_includes_cache_pc/$blocks_from_includes}}
					{{/if}}


					{{assign var="blocks_iteration" value="0"}}
					{{foreach name=data_blocks item=item_blocks from=$item.blocks|smarty:nodefaults}}
						{{if $item_blocks.is_from_include==1}}
							{{if $blocks_from_includes>1}}
								<tr class="dg_data{{if $blocks_iteration % 2==0}} dg_even{{/if}}">
									<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_blocks.block_id}}" disabled="disabled"/></td>
									<td>
										<span id="{{$item.external_id}}_gb" class="dg_expand">{{$lang.website_ui.dg_pages_group_global_blocks|replace:"%1%":$blocks_from_includes}}</span>
									</td>
									<td class="nowrap"></td>
									<td></td>
									{{if $config.disable_performance_stats!='true'}}
										<td></td>
										<td class="nowrap {{if $blocks_from_includes_slow==1}}warning_text{{/if}}">
											{{$blocks_from_includes_cached_time|default:"0"|number_format:2}} / {{$blocks_from_includes_uncached_time|default:"0"|number_format:2}} / {{$blocks_from_includes_cache_pc|default:"0"|intval}}% / {{$blocks_from_includes_max_memory|default:"0"|sizeToHumanString}}
										</td>
									{{/if}}
									<td></td>
								</tr>
								{{assign var="blocks_iteration" value=$blocks_iteration+1}}
								{{assign var="blocks_from_includes" value=0}}
							{{/if}}
						{{/if}}
						<tr class="dg_data{{if $blocks_iteration % 2==0}} dg_even{{/if}} {{if $item.is_disabled=='1'}}disabled{{/if}} {{if $item_blocks.is_from_include==1}}{{$item.external_id}}_gb hidden{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item_blocks.block_id}}" disabled="disabled"/></td>
							<td>
								{{if $item_blocks.is_global==1}}
									<a href="{{$page_name}}?action=change_block&amp;item_id=$global||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" {{if $item_blocks.errors=='1'}}class="highlighted_text"{{elseif $item_blocks.warnings=='1'}}class="warning_text"{{/if}}>{{$item_blocks.block_name}}</a>
								{{else}}
									<a href="{{$page_name}}?action=change_block&amp;item_id={{$item.external_id}}||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" {{if $item_blocks.errors=='1'}}class="highlighted_text"{{elseif $item_blocks.warnings=='1'}}class="warning_text"{{/if}}>{{$item_blocks.block_name}}</a>
								{{/if}}
							</td>
							<td class="nowrap">{{$item_blocks.block_id}}{{if $item_blocks.is_global==1}} [G]{{/if}}</td>
							<td>{{if $item_blocks.errors!='1' && $item_blocks.is_global!=1}}<input type="text" name="cache_time_{{$item.external_id}}_{{$item_blocks.block_name_dir}}" maxlength="32" size="6" value="{{$item_blocks.cache_time}}" {{if $can_edit==0 || $item_blocks.no_cache==1}}disabled="disabled"{{/if}}/>{{else}}{{$item_blocks.cache_time}}{{/if}} {{$lang.common.second_truncated}}</td>
							{{if $config.disable_performance_stats!='true'}}
								<td></td>
								<td class="nowrap {{if $item_blocks.is_slow==1}}warning_text{{/if}}">{{$item_blocks.cached_avg_time_s|default:"0"|number_format:2}} / {{$item_blocks.uncached_avg_time_s|default:"0"|number_format:2}} / {{$item_blocks.cache_pc|default:"0"|intval}}% / {{$item_blocks.max_memory|default:"0"|sizeToHumanString}}</td>
							{{/if}}
							<td>
								{{if $item_blocks.is_global==1}}
									<a href="{{$page_name}}?action=change_block&amp;item_id=$global||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								{{else}}
									<a href="{{$page_name}}?action=change_block&amp;item_id={{$item.external_id}}||{{$item_blocks.block_id}}||{{$item_blocks.block_name_dir}}&amp;item_name={{$item_blocks.block_name|escape}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
								{{/if}}
							</td>
						</tr>
					{{assign var="blocks_iteration" value=$blocks_iteration+1}}
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
							<span class="js_param">hide=${delete_hide}</span>
						</li>
					{{/if}}
					{{if $can_add==1}}
						<li class="js_params">
							<span class="js_param">href=?action=duplicate&amp;item_id=${id}&amp;external_id=${external_id}</span>
							<span class="js_param">title={{$lang.website_ui.dg_pages_action_duplicate}}</span>
							<span class="js_param">confirm={{$lang.website_ui.dg_pages_field_new_id|replace:"%1%":'${existing_id}'}}:</span>
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
					{{if $can_edit==1}}
						<td class="dgb_control">
							<input type="submit" value="{{$lang.website_ui.dg_pages_btn_save_caching}}" name="save_caching"/>
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