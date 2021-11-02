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
			{{if $smarty.get.item_id=='source'}}
				<input type="hidden" name="action" value="change_source_complete"/>
			{{else}}
				<input type="hidden" name="action" value="change_complete"/>
				<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
			{{/if}}
		{{/if}}
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.settings.submenu_option_formats_albums_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.settings.format_album_add}}{{else}}{{$lang.settings.format_album_edit|replace:"%1%":$smarty.post.title}}{{/if}}</div></td>
		</tr>
		{{if $smarty.get.item_id=='source'}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.format_album_divider_source}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_create_zip}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="is_create_zip" value="1" {{if $smarty.post.is_create_zip==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_album_field_create_zip_yes}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_create_zip_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_access_level}}:</td>
				<td class="de_control">
					<select name="access_level_id">
						<option value="0" {{if $smarty.post.access_level_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_any}}</option>
						<option value="1" {{if $smarty.post.access_level_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_member}}</option>
						<option value="2" {{if $smarty.post.access_level_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_premium}}</option>
						<option value="3" {{if $smarty.post.access_level_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_none}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_access_level_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_access_level_image}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.format_album_field_access_level_image}}</span>
							<span class="js_param">accept=jpg</span>
							{{if $smarty.post.access_level_image!=''}}
								<span class="js_param">preview_url={{$config.content_url_other}}/{{$smarty.post.access_level_image}}</span>
							{{/if}}
						</div>
						<input type="text" name="access_level_image" maxlength="100" class="fixed_400" {{if $smarty.post.access_level_image!=''}}value="{{$smarty.post.access_level_image}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="access_level_image_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $smarty.post.access_level_image==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{if $smarty.post.access_level_image!=''}}
							<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_access_level_image_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{else}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.format_album_divider_general}}</div></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_album_field_title}} (*):</td>
				<td class="de_control"><input type="text" name="title" maxlength="100" class="dyn_full_size" value="{{$smarty.post.title}}"/></td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_album_field_group}} (*):</td>
				<td class="de_control">
					<div class="de_vis_sw_select">
						<select id="group_id" name="group_id" {{if $smarty.get.action!='add_new'}}disabled="disabled"{{/if}}>
							<option value="1" {{if $smarty.post.group_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_group_main}}</option>
							<option value="2" {{if $smarty.post.group_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_group_preview}}</option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_album_field_size}} (*):</td>
				<td class="de_control">
					<input type="text" name="size" maxlength="9" class="dyn_full_size" value="{{$smarty.post.size}}" {{if $smarty.get.action!='add_new'}}disabled="disabled"{{/if}}/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.common.size_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label de_required">{{$lang.settings.format_album_field_im_options}} (*):</td>
				<td class="de_control">
					<textarea name="im_options" class="dyn_full_size" cols="30" rows="3">{{$smarty.post.im_options}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_im_options_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_skip_crop}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="is_skip_crop" value="1" {{if $smarty.post.is_skip_crop==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_album_field_skip_crop_yes}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_skip_crop_hint|replace:"%1%":$crop_options.left|replace:"%2%":$crop_options.top|replace:"%3%":$crop_options.right|replace:"%4%":$crop_options.bottom}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_aspect_ratio}}:</td>
				<td class="de_control">
					<table class="control_group">
						<colgroup>
							<col width="5%"/>
							<col width="95%"/>
						</colgroup>
						<tr class="group_data">
							<td class="nowrap">{{$lang.settings.format_album_field_aspect_ratio_horizontal}}:</td>
							<td>
								<div class="de_vis_sw_select">
									<select id="aspect_ratio_id" name="aspect_ratio_id">
										<option value="1" {{if $smarty.post.aspect_ratio_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_preserve_source}}</option>
										<option value="2" {{if $smarty.post.aspect_ratio_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_convert_to_target}}</option>
										<option value="3" {{if $smarty.post.aspect_ratio_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_dynamic_size}}</option>
										<option value="4" {{if $smarty.post.aspect_ratio_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_dynamic_width}}</option>
										<option value="5" {{if $smarty.post.aspect_ratio_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_dynamic_height}}</option>
									</select>
									<span class="aspect_ratio_id_2">
										&nbsp;&nbsp;
										{{$lang.settings.format_album_field_aspect_ratio_gravity}}:
										<select name="aspect_ratio_gravity">
											<option value="" {{if $smarty.post.aspect_ratio_gravity==''}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_center}}</option>
											<option value="North" {{if $smarty.post.aspect_ratio_gravity=='North'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_north}}</option>
											<option value="West" {{if $smarty.post.aspect_ratio_gravity=='West'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_west}}</option>
											<option value="East" {{if $smarty.post.aspect_ratio_gravity=='East'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_east}}</option>
											<option value="South" {{if $smarty.post.aspect_ratio_gravity=='South'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_south}}</option>
										</select>
									</span>
								</div>
							</td>
						</tr>
						<tr class="group_data">
							<td class="nowrap">{{$lang.settings.format_album_field_aspect_ratio_vertical}}:</td>
							<td>
								<div class="de_vis_sw_select">
									<select id="vertical_aspect_ratio_id" name="vertical_aspect_ratio_id">
										<option value="1" {{if $smarty.post.vertical_aspect_ratio_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_preserve_source}}</option>
										<option value="2" {{if $smarty.post.vertical_aspect_ratio_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_convert_to_target}}</option>
										<option value="3" {{if $smarty.post.vertical_aspect_ratio_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_dynamic_size}}</option>
										<option value="4" {{if $smarty.post.vertical_aspect_ratio_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_dynamic_width}}</option>
										<option value="5" {{if $smarty.post.vertical_aspect_ratio_id==5}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_dynamic_height}}</option>
									</select>
									<span class="vertical_aspect_ratio_id_2">
										&nbsp;&nbsp;
										{{$lang.settings.format_album_field_aspect_ratio_gravity}}:
										<select name="vertical_aspect_ratio_gravity">
											<option value="" {{if $smarty.post.vertical_aspect_ratio_gravity==''}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_center}}</option>
											<option value="North" {{if $smarty.post.vertical_aspect_ratio_gravity=='North'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_north}}</option>
											<option value="West" {{if $smarty.post.vertical_aspect_ratio_gravity=='West'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_west}}</option>
											<option value="East" {{if $smarty.post.vertical_aspect_ratio_gravity=='East'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_east}}</option>
											<option value="South" {{if $smarty.post.vertical_aspect_ratio_gravity=='South'}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_aspect_ratio_gravity_south}}</option>
										</select>
									</span>
								</div>
							</td>
						</tr>
					</table>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint">{{$lang.settings.format_album_field_aspect_ratio_hint|replace:"%1%":$lang.settings.format_album_field_aspect_ratio_preserve_source|replace:"%2%":$lang.settings.format_album_field_aspect_ratio_convert_to_target|replace:"%3%":$lang.settings.format_album_field_aspect_ratio_dynamic_size|replace:"%4%":$lang.settings.format_album_field_aspect_ratio_dynamic_width|replace:"%5%":$lang.settings.format_album_field_aspect_ratio_dynamic_height}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_interlace}}:</td>
				<td class="de_control">
					<select name="interlace_id">
						<option value="0" {{if $smarty.post.interlace_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_interlace_none}}</option>
						<option value="1" {{if $smarty.post.interlace_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_interlace_line}}</option>
						<option value="2" {{if $smarty.post.interlace_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_interlace_plane}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_interlace_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_comment}}:</td>
				<td class="de_control">
					<input type="text" name="comment" maxlength="255" class="dyn_full_size" value="{{$smarty.post.comment}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_comment_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_create_zip}}:</td>
				<td class="de_control">
					<div class="de_lv_pair"><input type="checkbox" name="is_create_zip" value="1" class="group_id_1" {{if $smarty.post.is_create_zip==1}}checked="checked"{{/if}}/><label>{{$lang.settings.format_album_field_create_zip_yes}}</label></div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_create_zip_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.format_album_divider_watermark}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_watermark_image}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.format_album_field_watermark_image}}</span>
							<span class="js_param">accept=png</span>
							{{if $smarty.post.watermark_image_url!=''}}
								<span class="js_param">preview_url={{$smarty.post.watermark_image_url}}</span>
							{{/if}}
						</div>
						<input type="text" name="watermark_image" maxlength="100" class="fixed_400" {{if $smarty.post.watermark_image!=''}}value="{{$smarty.post.watermark_image}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="watermark_image_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $smarty.post.watermark_image==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{if $smarty.post.watermark_image_url!=''}}
							<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_watermark_image_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_watermark_position}}:</td>
				<td class="de_control">
					<select name="watermark_position_id">
						<option value="0" {{if $smarty.post.watermark_position_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_watermark_position_random}}</option>
						<option value="1" {{if $smarty.post.watermark_position_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_watermark_position_top_left}}</option>
						<option value="2" {{if $smarty.post.watermark_position_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_watermark_position_top_right}}</option>
						<option value="3" {{if $smarty.post.watermark_position_id==3}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_watermark_position_bottom_right}}</option>
						<option value="4" {{if $smarty.post.watermark_position_id==4}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_watermark_position_bottom_left}}</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_watermark_max_width}}:</td>
				<td class="de_control">
					{{$lang.settings.format_album_field_watermark_max_width_horizontal}}: <input type="text" name="watermark_max_width" maxlength="10" class="fixed_100" value="{{$smarty.post.watermark_max_width}}"/> %
					&nbsp;&nbsp;&nbsp;&nbsp;
					{{$lang.settings.format_album_field_watermark_max_width_vertical}}: <input type="text" name="watermark_max_width_vertical" maxlength="10" class="fixed_100" value="{{$smarty.post.watermark_max_width_vertical}}"/> %
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_watermark_max_width_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.format_album_divider_access}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_access_level}}:</td>
				<td class="de_control">
					<select name="access_level_id">
						<option value="0" {{if $smarty.post.access_level_id==0}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_any}}</option>
						<option value="1" {{if $smarty.post.access_level_id==1}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_member}}</option>
						<option value="2" {{if $smarty.post.access_level_id==2}}selected="selected"{{/if}}>{{$lang.settings.format_album_field_access_level_premium}}</option>
					</select>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_access_level_hint}}</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.format_album_field_access_level_image}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.settings.format_album_field_access_level_image}}</span>
							<span class="js_param">accept=jpg</span>
							{{if $smarty.post.access_level_image!=''}}
								<span class="js_param">preview_url={{$config.content_url_other}}/{{$smarty.post.access_level_image}}</span>
							{{/if}}
						</div>
						<input type="text" name="access_level_image" maxlength="100" class="fixed_400" {{if $smarty.post.access_level_image!=''}}value="{{$smarty.post.access_level_image}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="access_level_image_hash"/>
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove {{if $smarty.post.access_level_image==''}}hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{if $smarty.post.access_level_image!=''}}
							<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.settings.format_album_field_access_level_image_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{assign var=group_id value=0}}
				{{section name=groups start=0 step=1 loop=2}}
					{{assign var="group_colspan" value=1}}
					{{foreach from=$table_fields|smarty:nodefaults item="field"}}
						{{if $field.is_enabled==1}}
							{{assign var="group_colspan" value=$group_colspan+1}}
						{{/if}}
					{{/foreach}}
					<tr class="dg_group_header">
						<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
						<td colspan="{{$group_colspan}}">
							{{if $group_id==0}}
								{{$lang.settings.format_album_field_group_main}}
							{{elseif $group_id==1}}
								{{$lang.settings.format_album_field_group_preview}}
							{{/if}}
						</td>
					</tr>
					{{if count($data[$group_id])>0}}
						{{foreach name=data item=item from=$data[$group_id]|smarty:nodefaults}}
							<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0 || $item.status_id==3}}disabled{{/if}}">
								<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
								{{assign var="table_columns_display_mode" value="data"}}
								{{include file="table_columns_inc.tpl"}}
								<td>
									{{if $item.is_editing_forbidden!=1}}
										<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
									{{/if}}
									{{if ($item.status_id!=0 && $item.status_id!=3)}}
										<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
											<span class="js_params">
												<span class="js_param">id={{$item.$table_key_name}}</span>
												<span class="js_param">name={{$item.title}}</span>
												{{if $item.$table_key_name=='source'}}
													<span class="js_param">delete_hide=true</span>
													<span class="js_param">recreate_hide=true</span>
												{{/if}}
												{{if $item.status_id==2}}
													<span class="js_param">recreate_hide=true</span>
												{{elseif $item.status_id==4}}
													<span class="js_param">delete_hide=true</span>
													<span class="js_param">recreate_hide=true</span>
												{{else}}
													<span class="js_param">restart_hide=true</span>
												{{/if}}
												{{if $item.zip_albums_count==0}}
													<span class="js_param">delete_zip_hide=true</span>
												{{/if}}
												{{if $item.is_create_zip==1}}
													<span class="js_param">delete_zip_disable=true</span>
												{{/if}}
											</span>
										</a>
									{{/if}}
								</td>
							</tr>
						{{/foreach}}
					{{else}}
						<tr class="dg_data">
							<td class="dg_selector"><input type="checkbox" disabled="disabled"/></td>
							<td colspan="{{$group_colspan}}">{{$lang.settings.format_album_field_group_no_formats}}</td>
						</tr>
					{{/if}}
					{{assign var=group_id value=$group_id+1}}
				{{/section}}
			</table>
			<ul class="dg_additional_menu_template">
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
					<span class="js_param">confirm={{$lang.settings.format_album_action_delete_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${delete_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=recreate&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.format_album_action_recreate}}</span>
					<span class="js_param">confirm={{$lang.settings.format_album_action_recreate_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${recreate_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=restart&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.format_album_action_restart}}</span>
					<span class="js_param">confirm={{$lang.settings.format_album_action_restart_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${restart_hide}</span>
				</li>
				<li class="js_params">
					<span class="js_param">href=?batch_action=delete_zip&amp;row_select[]=${id}</span>
					<span class="js_param">title={{$lang.settings.format_album_action_delete_zip}}</span>
					<span class="js_param">confirm={{$lang.settings.format_album_action_delete_zip_confirm|replace:"%1%":'${name}'}}</span>
					<span class="js_param">hide=${delete_zip_hide}</span>
					<span class="js_param">disable=${delete_zip_disable}</span>
				</li>
			</ul>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>

{{/if}}