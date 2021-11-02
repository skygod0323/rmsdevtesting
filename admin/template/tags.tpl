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

{{if in_array('tags|edit_all',$smarty.session.permissions) || (in_array('tags|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
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
		<input type="hidden" name="custom4" value="{{$smarty.post.custom4}}"/>
		<input type="hidden" name="custom5" value="{{$smarty.post.custom5}}"/>
	</div>
	<table class="de {{if $can_edit==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="3"><div><a href="{{$page_name}}">{{$lang.categorization.submenu_option_tags_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.categorization.tag_add}}{{else}}{{$lang.categorization.tag_edit|replace:"%1%":$smarty.post.tag}}{{/if}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0 && $smarty.session.userdata.is_hide_forum_hints==0}}
			<tr>
				<td class="de_simple_text" colspan="2">
					<span class="de_hint">Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/543-categorization-best-practices">Categorization best practices</a></span>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action!='add_new' && ($options.ENABLE_TAG_FIELD_1==1 || $options.ENABLE_TAG_FIELD_2==1 || $options.ENABLE_TAG_FIELD_3==1 || $options.ENABLE_TAG_FIELD_4==1 || $options.ENABLE_TAG_FIELD_5==1)}}
			<tr>
				<td class="de_separator" colspan="3"><div>{{$lang.categorization.tag_divider_general}}</div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">
				{{if $smarty.get.action=='add_new'}}
					{{$lang.categorization.tag_field_tags}} (*):
				{{else}}
					{{$lang.categorization.tag_field_tag}} (*):
				{{/if}}
			</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new'}}
					<textarea name="tag" class="dyn_full_size" cols="40" rows="8"></textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.categorization.tag_field_tags_hint}}</span>
					{{/if}}
				{{else}}
					<input type="text" name="tag" maxlength="150" class="dyn_full_size" value="{{$smarty.post.tag}}"/>
				{{/if}}
			</td>
			{{if is_array($sidebar_fields)}}
				{{assign var="sidebar_rowspan" value="4"}}
				{{include file="editor_sidebar_inc.tpl"}}
			{{/if}}
		</tr>
		{{if $smarty.get.action=='change'}}
			<tr>
				<td class="de_label">{{$lang.categorization.tag_field_directory}}:</td>
				<td class="de_control">
					<input type="text" name="tag_dir" maxlength="255" class="dyn_full_size" value="{{$smarty.post.tag_dir}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.categorization.tag_field_directory_hint|replace:"%1%":$lang.categorization.tag_field_tag}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.categorization.tag_field_synonyms}}:</td>
				<td class="de_control">
					<textarea name="synonyms" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.synonyms}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.categorization.tag_field_synonyms_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.categorization.tag_field_status}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked="checked"{{/if}}/><label>{{$lang.categorization.tag_field_status_active}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.categorization.tag_field_status_hint}}</span>
					{{/if}}
				</td>
			</tr>
			{{if $options.ENABLE_TAG_FIELD_1==1 || $options.ENABLE_TAG_FIELD_2==1 || $options.ENABLE_TAG_FIELD_3==1 || $options.ENABLE_TAG_FIELD_4==1 || $options.ENABLE_TAG_FIELD_5==1}}
				<tr>
					<td class="de_separator" colspan="3"><div>{{$lang.categorization.tag_divider_customization}}</div></td>
				</tr>
				{{if $options.ENABLE_TAG_FIELD_1==1}}
					<tr>
						<td class="de_label">{{$options.TAG_FIELD_1_NAME}}:</td>
						<td class="de_control" colspan="2">
							<div class="de_str_len">
								<textarea name="custom1" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom1}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_TAG_FIELD_2==1}}
					<tr>
						<td class="de_label">{{$options.TAG_FIELD_2_NAME}}:</td>
						<td class="de_control" colspan="2">
							<div class="de_str_len">
								<textarea name="custom2" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom2}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_TAG_FIELD_3==1}}
					<tr>
						<td class="de_label">{{$options.TAG_FIELD_3_NAME}}:</td>
						<td class="de_control" colspan="2">
							<div class="de_str_len">
								<textarea name="custom3" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom3}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_TAG_FIELD_4==1}}
					<tr>
						<td class="de_label">{{$options.TAG_FIELD_4_NAME}}:</td>
						<td class="de_control" colspan="2">
							<div class="de_str_len">
								<textarea name="custom4" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom4}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				{{/if}}
				{{if $options.ENABLE_TAG_FIELD_5==1}}
					<tr>
						<td class="de_label">{{$options.TAG_FIELD_5_NAME}}:</td>
						<td class="de_control" colspan="2">
							<div class="de_str_len">
								<textarea name="custom5" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom5}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				{{/if}}
			{{/if}}
		{{/if}}
		{{if $can_edit==1}}
			<tr>
				<td class="de_action_group" colspan="3">
					{{if $smarty.get.action=='add_new'}}
						<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
					{{else}}
						{{if $smarty.session.save.options.default_save_button==1}}
							<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
							<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
							<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
						{{else}}
							<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
							<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
							<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
						{{/if}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
	</table>
</form>

{{else}}

{{if in_array('tags|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('tags|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
{{/if}}
{{if $can_delete==1 || $can_edit==1}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
{{if $can_delete==1 || $can_edit==1}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.categorization.tag_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.categorization.tag_field_status_active}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.categorization.tag_field_status_disabled}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_field!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_field}}:</td>
					<td class="dgf_control">
						<select name="se_field">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="empty/synonyms" {{if $smarty.session.save.$page_name.se_field=="empty/synonyms"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.categorization.tag_field_synonyms}}</option>
							{{section name="data" start="1" loop="6"}}
								{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="TAG_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_TAG_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
							<option value="filled/synonyms" {{if $smarty.session.save.$page_name.se_field=="filled/synonyms"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.categorization.tag_field_synonyms}}</option>
							{{section name="data" start="1" loop="6"}}
								{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="TAG_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_TAG_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_usage!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_usage}}:</td>
					<td class="dgf_control">
						<select name="se_usage">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="used/videos" {{if $smarty.session.save.$page_name.se_usage=="used/videos"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_videos}}</option>
							<option value="used/albums" {{if $smarty.session.save.$page_name.se_usage=="used/albums"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_albums}}</option>
							<option value="used/posts" {{if $smarty.session.save.$page_name.se_usage=="used/posts"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_posts}}</option>
							<option value="used/other" {{if $smarty.session.save.$page_name.se_usage=="used/other"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_other}}</option>
							<option value="used/all" {{if $smarty.session.save.$page_name.se_usage=="used/all"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_any}}</option>
							<option value="notused/videos" {{if $smarty.session.save.$page_name.se_usage=="notused/videos"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_no_videos}}</option>
							<option value="notused/albums" {{if $smarty.session.save.$page_name.se_usage=="notused/albums"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_no_albums}}</option>
							<option value="notused/posts" {{if $smarty.session.save.$page_name.se_usage=="notused/posts"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_no_posts}}</option>
							<option value="notused/other" {{if $smarty.session.save.$page_name.se_usage=="notused/other"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_no_other}}</option>
							<option value="notused/all" {{if $smarty.session.save.$page_name.se_usage=="notused/all"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_no_any}}</option>
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
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.tag}}</span>
									{{if $item.status_id==0}}
										<span class="js_param">deactivate_hide=true</span>
									{{else}}
										<span class="js_param">activate_hide=true</span>
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
					{{if $can_edit==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
							<span class="js_param">hide=${activate_hide}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">hide=${deactivate_hide}</span>
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
								{{if $can_edit==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
								{{/if}}
							</select>
						</td>
						<td class="dgb_control">
							<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
						</td>
					{{/if}}
					{{if $can_edit==1}}
						{{foreach from=$table_fields|smarty:nodefaults item="field"}}
							{{if $field.type=='rename' && $field.is_enabled==1}}
								<td class="dgb_control">
									<input type="submit" value="{{$lang.categorization.tag_btn_batch_rename}}" name="save_rename"/>
								</td>
							{{/if}}
						{{/foreach}}
					{{/if}}
					<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
				</tr>
			</table>
			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}