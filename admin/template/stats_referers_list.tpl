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

{{if $smarty.post.referer=='&lt;bookmarks&gt;'}}
	{{assign var=can_edit_all value=0}}
{{else}}
	{{assign var=can_edit_all value=1}}
{{/if}}

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
		<input type="hidden" name="custom1" value="{{$smarty.post.custom1}}"/>
		<input type="hidden" name="custom2" value="{{$smarty.post.custom2}}"/>
		<input type="hidden" name="custom3" value="{{$smarty.post.custom3}}"/>
		<input type="hidden" name="custom_file1" value="{{$smarty.post.custom_file1}}"/>
		<input type="hidden" name="custom_file1_hash"/>
		<input type="hidden" name="custom_file2" value="{{$smarty.post.custom_file2}}"/>
		<input type="hidden" name="custom_file2_hash"/>
		<input type="hidden" name="custom_file3" value="{{$smarty.post.custom_file3}}"/>
		<input type="hidden" name="custom_file3_hash"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.stats.submenu_option_referers_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.stats.referer_add}}{{else}}{{$lang.stats.referer_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $options.ENABLE_REFERER_FIELD_1==1 || $options.ENABLE_REFERER_FIELD_2==1 || $options.ENABLE_REFERER_FIELD_3==1 || $options.ENABLE_REFERER_FILE_FIELD_1==1 || $options.ENABLE_REFERER_FILE_FIELD_2==1 || $options.ENABLE_REFERER_FILE_FIELD_3==1}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.stats.referer_divider_general}}</div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.stats.referer_field_title}} (*):</td>
			<td class="de_control"><input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.stats.referer_field_description}}:</td>
			<td class="de_control"><textarea name="description" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="3">{{$smarty.post.description}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.stats.referer_field_url}}:</td>
			<td class="de_control">
				<input type="text" name="url" maxlength="255" class="dyn_full_size" value="{{$smarty.post.url}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.stats.referer_field_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.stats.referer_field_category}}:</td>
			<td class="de_control">
				<div class="insight">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
					</div>
					<input type="text" name="category" maxlength="255" class="fixed_300" value="{{$smarty.post.category.title}}"/>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.stats.referer_field_referer}} (*):</td>
			<td class="de_control">
				<input type="text" name="referer" maxlength="255" class="dyn_full_size" value="{{$smarty.post.referer}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.stats.referer_field_referer_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $options.ENABLE_REFERER_FIELD_1==1 || $options.ENABLE_REFERER_FIELD_2==1 || $options.ENABLE_REFERER_FIELD_3==1 || $options.ENABLE_REFERER_FILE_FIELD_1==1 || $options.ENABLE_REFERER_FILE_FIELD_2==1 || $options.ENABLE_REFERER_FILE_FIELD_3==1}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.stats.referer_divider_customization}}</div></td>
			</tr>
			{{if $options.ENABLE_REFERER_FIELD_1==1}}
				<tr>
					<td class="de_label">{{$options.REFERER_FIELD_1_NAME}}:</td>
					<td class="de_control"><textarea name="custom1" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea></td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_REFERER_FIELD_2==1}}
				<tr>
					<td class="de_label">{{$options.REFERER_FIELD_2_NAME}}:</td>
					<td class="de_control"><textarea name="custom2" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea></td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_REFERER_FIELD_3==1}}
				<tr>
					<td class="de_label">{{$options.REFERER_FIELD_3_NAME}}:</td>
					<td class="de_control"><textarea name="custom3" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea></td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_REFERER_FILE_FIELD_1==1}}
				<tr>
					<td class="de_label">{{$options.REFERER_FILE_FIELD_1_NAME}}:</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.REFERER_FILE_FIELD_1_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file1)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_referers}}/{{$smarty.post.referer_id}}/{{$smarty.post.custom_file1}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_referers}}/{{$smarty.post.referer_id}}/{{$smarty.post.custom_file1}}</span>
									{{/if}}
								{{/if}}
							</div>
							<input type="text" name="custom_file1" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}value="{{$smarty.post.custom_file1}}"{{/if}} readonly="readonly"/>
							<input type="hidden" name="custom_file1_hash"/>
							{{if $can_edit_all==1}}
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.custom_file1==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
							{{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}
								{{if in_array(end(explode(".",$smarty.post.custom_file1)),explode(",",$config.image_allowed_ext))}}
									<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
								{{else}}
									<input type="button" class="de_fu_download" value="{{$lang.common.attachment_btn_download}}"/>
								{{/if}}
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_REFERER_FILE_FIELD_2==1}}
				<tr>
					<td class="de_label">{{$options.REFERER_FILE_FIELD_2_NAME}}:</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.REFERER_FILE_FIELD_2_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file2)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_referers}}/{{$smarty.post.referer_id}}/{{$smarty.post.custom_file2}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_referers}}/{{$smarty.post.referer_id}}/{{$smarty.post.custom_file2}}</span>
									{{/if}}
								{{/if}}
							</div>
							<input type="text" name="custom_file2" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}value="{{$smarty.post.custom_file2}}"{{/if}} readonly="readonly"/>
							<input type="hidden" name="custom_file2_hash"/>
							{{if $can_edit_all==1}}
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.custom_file2==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
							{{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}
								{{if in_array(end(explode(".",$smarty.post.custom_file2)),explode(",",$config.image_allowed_ext))}}
									<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
								{{else}}
									<input type="button" class="de_fu_download" value="{{$lang.common.attachment_btn_download}}"/>
								{{/if}}
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_REFERER_FILE_FIELD_3==1}}
				<tr>
					<td class="de_label">{{$options.REFERER_FILE_FIELD_3_NAME}}:</td>
					<td class="de_control">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.REFERER_FILE_FIELD_3_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file3)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_referers}}/{{$smarty.post.referer_id}}/{{$smarty.post.custom_file3}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_referers}}/{{$smarty.post.referer_id}}/{{$smarty.post.custom_file3}}</span>
									{{/if}}
								{{/if}}
							</div>
							<input type="text" name="custom_file3" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}value="{{$smarty.post.custom_file3}}"{{/if}} readonly="readonly"/>
							<input type="hidden" name="custom_file3_hash"/>
							{{if $can_edit_all==1}}
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.custom_file3==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
							{{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}
								{{if in_array(end(explode(".",$smarty.post.custom_file3)),explode(",",$config.image_allowed_ext))}}
									<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
								{{else}}
									<input type="button" class="de_fu_download" value="{{$lang.common.attachment_btn_download}}"/>
								{{/if}}
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
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

{{else}}

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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_category!=''}}dgf_selected{{/if}}">{{$lang.stats.referer_field_category}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_categories.php</span>
							</div>
							<input type="text" name="se_category" size="20" value="{{$smarty.session.save.$page_name.se_category}}"/>
						</div>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0"/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $item.referer=='&lt;bookmarks&gt;'}}disabled="disabled"{{/if}}/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
							{{if $item.referer!='&lt;bookmarks&gt;'}}
								<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
									<span class="js_params">
										<span class="js_param">id={{$item.$table_key_name}}</span>
										<span class="js_param">name={{$item.referer}}</span>
									</span>
								</a>
							{{/if}}
						</td>
					</tr>
				{{/foreach}}
		   </table>
		   <ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
				</li>
		   </ul>
	   </div>
	   <div class="dgb">
		   <table>
			   <tr>
				   <td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
				   <td class="dgb_control">
					   <select name="batch_action">
						   <option value="0">{{$lang.common.dg_batch_actions_select}}</option>
						   <option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
					   </select>
				   </td>
				   <td class="dgb_control">
					   <input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
				   </td>
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