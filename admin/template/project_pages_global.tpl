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

{{if $smarty.get.action=='restore_blocks'}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
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
				<td>{{$lang.website_ui.dg_restore_global_blocks_hint}}</td>
			</tr>
		</table>
	</div>
	<form action="{{$page_name}}" method="post" class="form_dg">
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
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					<col width="10%"/>
					<col width="10%"/>
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value=""/></td>
					<td>{{$lang.website_ui.dg_restore_blocks_col_block_type}}</td>
					<td>{{$lang.website_ui.dg_restore_blocks_col_block_name}}</td>
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$deleted_global_blocks|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.block_id}}||{{$item.block_name_mod}}"/></td>
						<td>{{$item.block_id}}</td>
						<td>{{$item.block_name}}</td>
						<td>
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.block_id}}||{{$item.block_name_mod}}</span>
									<span class="js_param">name={{$item.block_name}}</span>
								</span>
							</a>
						</td>
					</tr>
				{{/foreach}}
			</table>
			<ul class="dg_additional_menu_template">
				{{if $can_edit==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=restore_block&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.dg_restore_blocks_action_restore}}</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_blocks_action_restore_confirm|replace:"%1%":'${name}'}}</span>
					</li>
				{{/if}}
				{{if $can_delete==1}}
					<li class="js_params">
						<span class="js_param">href=?batch_action=wipeout_block&amp;row_select[]=${id}</span>
						<span class="js_param">title={{$lang.website_ui.dg_restore_blocks_action_wipeout}}</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_blocks_action_wipeout_confirm|replace:"%1%":'${name}'}}</span>
					</li>
				{{/if}}
			</ul>
		</div>
		{{if count($deleted_global_blocks)!=0}}
			<div class="dgb">
				<table>
					<tr>
						<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
						<td class="dgb_control">
							<select name="batch_action">
								<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
								{{if $can_edit==1}}
									<option value="restore_block">{{$lang.website_ui.dg_restore_blocks_batch_actions_restore}}</option>
								{{/if}}
								{{if $can_delete==1}}
									<option value="wipeout_block">{{$lang.website_ui.dg_restore_blocks_batch_actions_wipeout}}</option>
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
						<span class="js_param">value=restore_block</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_blocks_batch_actions_restore_confirm|replace:"%1%":'${count}'}}</span>
					</li>
					<li class="js_params">
						<span class="js_param">value=wipeout_block</span>
						<span class="js_param">confirm={{$lang.website_ui.dg_restore_blocks_batch_actions_wipeout_confirm|replace:"%1%":'${count}'}}</span>
					</li>
				</ul>
			</div>
		{{/if}}
	</form>
</div>

{{else}}

{{if in_array('website_ui|edit_all',$smarty.session.permissions)}}
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
		<input type="hidden" name="action" value="change_complete"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.website_ui.submenu_option_global_blocks}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col width="15%"/>
						<col width="15%"/>
						<col width="5%"/>
						<col/>
						<col width="1%"/>
					</colgroup>
					<tr class="eg_header">
						<td class="nowrap">{{$lang.website_ui.page_global_dg_blocks_col_name}}</td>
						<td class="nowrap">{{$lang.website_ui.page_global_dg_blocks_col_id}}</td>
						<td class="nowrap">{{$lang.website_ui.page_global_dg_blocks_col_cache_time}}</td>
						<td class="nowrap">{{$lang.website_ui.page_global_dg_blocks_insert_code}}</td>
						<td class="nowrap">{{$lang.website_ui.page_global_dg_blocks_col_delete}}</td>
					</tr>
					{{section name=data start=0 step=1 loop=70}}
						<tr id="row_{{$smarty.section.data.iteration}}" class="eg_data fixed_height_30 {{if $smarty.post.blocks[$smarty.section.data.iteration]==''}}hidden{{/if}}">
							{{if $smarty.post.blocks[$smarty.section.data.iteration]!=''}}
								<td>
									<input type="hidden" name="id_{{$smarty.section.data.iteration}}" value="{{$smarty.post.blocks[$smarty.section.data.iteration].id}}"/>
									<input type="hidden" name="name_{{$smarty.section.data.iteration}}" value="{{$smarty.post.blocks[$smarty.section.data.iteration].display_name}}"/>
									<a href="project_pages.php?action=change_block&amp;item_id=$global||{{$smarty.post.blocks[$smarty.section.data.iteration].id}}||{{$smarty.post.blocks[$smarty.section.data.iteration].name}}&amp;item_name={{$smarty.post.blocks[$smarty.section.data.iteration].display_name}}" class="popup {{if $smarty.post.blocks[$smarty.section.data.iteration].errors=='1'}}highlighted_text{{elseif $smarty.post.blocks[$smarty.section.data.iteration].warnings=='1'}}warning_text{{/if}}">{{$smarty.post.blocks[$smarty.section.data.iteration].display_name}}</a>
								</td>
								<td>{{$smarty.post.blocks[$smarty.section.data.iteration].id}}</td>
								<td>{{if $item.errors!='1'}}<input type="text" size="7" maxlength="32" name="cache_{{$smarty.section.data.iteration}}" value="{{$smarty.post.blocks[$smarty.section.data.iteration].cache}}"/>{{else}}{{$smarty.post.blocks[$smarty.section.data.iteration].cache}}{{/if}}</td>
								<td>{{$smarty.ldelim}}insert name="getGlobal" global_id="{{$smarty.post.blocks[$smarty.section.data.iteration].id}}_{{$smarty.post.blocks[$smarty.section.data.iteration].name}}"{{$smarty.rdelim}}</td>
							{{else}}
								<td><input type="text" size="30" name="name_{{$smarty.section.data.iteration}}"/></td>
								<td>
									<select name="id_{{$smarty.section.data.iteration}}">
										<option value="">{{$lang.common.select_default_option}}</option>
										{{foreach item=item from=$blocks_list|smarty:nodefaults}}
											<option value="{{$item}}">{{$item}}</option>
										{{/foreach}}
									</select>
								</td>
								<td><input type="text" size="7" maxlength="32" name="cache_{{$smarty.section.data.iteration}}" value="86400"/></td>
								<td></td>
							{{/if}}
							<td><input type="checkbox" name="delete_{{$smarty.section.data.iteration}}" value="1" {{if $smarty.post.blocks[$smarty.section.data.iteration].is_used==1}}disabled="disabled"{{/if}}/></td>
						</tr>
					{{/section}}
				</table>
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
					<input type="button" id="add_global_block" value="{{$lang.website_ui.page_global_btn_add_block}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>
<div id="custom_js" class="js_params">
	<span class="js_param">buildGlobalBlocksLogic=call</span>
</div>

{{/if}}