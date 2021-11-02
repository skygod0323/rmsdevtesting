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
			<td class="de_header" colspan="2"><div>{{$lang.website_ui.theme_settings_title}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_control de_hint" colspan="2">
					{{$lang.website_ui.theme_settings_title_hint}}
					<br/><br/>
					Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/search?searchJSON=%7B%22tag%22%3A%5B%22theme%22%5D%7D">Theme customization</a><br/>
					Forum: <a rel="external" href="https://forum.kernel-video-sharing.com/forum/educational-support/educational-series/643-working-with-seo-texts-and-urls-in-kvs-themes">Working with SEO, texts and URLs in KVS themes</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.theme_settings_divider_info}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.theme_settings_field_name}}:</td>
			<td class="de_control">
				{{$theme.name}} {{if $theme.version!=''}}({{$theme.version}}){{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.theme_settings_field_developer}}:</td>
			<td class="de_control">
				{{if $theme.developer_url!=''}}
					<a href="{{$theme.developer_url}}" rel="external">{{$theme.developer|default:$theme.developer_url}}</a>
				{{else}}
					{{$theme.developer|default:$lang.common.undefined}}
				{{/if}}
			</td>
		</tr>
		{{if $theme.forum!=''}}
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_forum}}:</td>
				<td class="de_control">
					<a href="https://forum.kernel-video-sharing.com/forum/themes-templates/{{$theme.forum}}" rel="external">{{$lang.website_ui.theme_settings_field_forum_open}}</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.website_ui.theme_settings_divider_global}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.theme_settings_field_texts}}:</td>
			<td class="de_control">
				<a href="project_pages_lang_texts.php?no_filter=true" rel="external">{{$lang.website_ui.theme_settings_field_texts_value}}</a>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.theme_settings_field_urls}}:</td>
			<td class="de_control">
				{{if in_array('system|website_settings',$smarty.session.permissions)}}
					<a href="options.php?page=website_settings" rel="external">{{$lang.website_ui.theme_settings_field_urls_value_objects}}</a>
				{{else}}
					{{$lang.website_ui.theme_settings_field_urls_value_objects}}
				{{/if}}
				&nbsp;/&nbsp;
				<a href="project_pages_lang_texts.php?no_filter=true&amp;se_prefix=urls" rel="external">{{$lang.website_ui.theme_settings_field_urls_value_design}}</a>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.website_ui.theme_settings_field_urls_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.website_ui.theme_settings_field_seo}}:</td>
			<td class="de_control">
				<a href="project_pages_lang_texts.php?no_filter=true&amp;se_prefix=html" rel="external">{{$lang.website_ui.theme_settings_field_seo_value}}</a>
			</td>
		</tr>
		{{if $theme.header!=''}}
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_header}}:</td>
				<td class="de_control">
					<a href="project_pages_components.php?action=change&amp;item_id={{$theme.header}}" rel="external">{{$lang.website_ui.theme_settings_field_header_value}}</a>
				</td>
			</tr>
		{{/if}}
		{{if $theme.footer!=''}}
			<tr>
				<td class="de_label">{{$lang.website_ui.theme_settings_field_footer}}:</td>
				<td class="de_control">
					<a href="project_pages_components.php?action=change&amp;item_id={{$theme.footer}}" rel="external">{{$lang.website_ui.theme_settings_field_footer_value}}</a>
				</td>
			</tr>
		{{/if}}
		{{foreach from=$theme.sections|smarty:nodefaults item="section"}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$section.title}}</div></td>
			</tr>
			{{foreach from=$section.fields|smarty:nodefaults item="field"}}
				{{if $field.hidden!=1 && $field.unsupported!=1}}
					<tr>
						<td class="de_label {{if $field.required==1}}de_required{{/if}} {{if $field.unsupported==1}}disabled{{/if}}">{{$field.title}}{{if $field.required==1}} (*){{/if}}:</td>
						<td class="de_control">
							{{if $field.type=='checkbox'}}
								<div class="de_lv_pair"><input type="checkbox" name="{{$field.id}}" value="true" {{if $field.value=='true'}}checked="checked"{{/if}} {{if $field.unsupported==1}}disabled="disabled"{{/if}}/><label>{{$field.label|default:$lang.website_ui.theme_settings_field_label_enable}}</label></div>
							{{elseif $field.type=='text'}}
								<input type="text" name="{{$field.id}}" class="dyn_full_size" value="{{$field.value}}" {{if $field.unsupported==1}}disabled="disabled"{{/if}}/>
							{{elseif $field.type=='select'}}
								<select name="{{$field.id}}" {{if $field.unsupported==1}}disabled="disabled"{{/if}}>
									{{assign var="select_has_valid_option" value="false"}}
									{{foreach from=$field.options|smarty:nodefaults item="option"}}
										<option value="{{$option.id}}" {{if $option.id==$field.value}}selected="selected"{{/if}}>{{$option.title}}</option>
										{{if $option.id==$field.value}}
											{{assign var="select_has_valid_option" value="true"}}
										{{/if}}
									{{/foreach}}
									{{if $select_has_valid_option=='false'}}
										<option value="__INVALID__" selected="selected">{{$lang.website_ui.theme_settings_field_label_missing}}</option>
									{{/if}}
								</select>
							{{elseif $field.type=='multiselect'}}
								{{foreach from=$field.options|smarty:nodefaults item="option"}}
									<div class="de_lv_pair"><input type="checkbox" name="{{$field.id}}[]" value="{{$option.id}}" {{if in_array($option.id, $field.value)}}checked="checked"{{/if}} {{if $field.unsupported==1}}disabled="disabled"{{/if}}/><label>{{$option.title}}</label></div>
								{{/foreach}}
							{{elseif $field.type=='group'}}
								<table class="control_group">
									{{foreach from=$field.group|smarty:nodefaults item="field_inner"}}
										{{if $field.hidden!=1}}
											<tr><td>
												{{if $field_inner.type=='checkbox'}}
													<div class="de_lv_pair"><input type="checkbox" name="{{$field_inner.id}}" value="true" {{if $field_inner.value=='true'}}checked="checked"{{/if}} {{if $field_inner.unsupported==1}}disabled="disabled"{{/if}}/><label>{{$field_inner.title}}</label></div>
												{{/if}}
											</td></tr>
										{{/if}}
									{{/foreach}}
								</table>
							{{elseif $field.type=='block'}}
								{{foreach from=$field.blocks|smarty:nodefaults item="block" name="blocks"}}
									{{if $field.unsupported==1 || $block.unsupported==1 || $block.unused==1}}
										<span class="disabled">{{$block.title}}</span>
									{{else}}
										<a href="{{$block.link|smarty:nodefaults}}" rel="external">{{$block.title}}</a>
									{{/if}}
									{{if !$smarty.foreach.blocks.last}}&nbsp;/&nbsp;{{/if}}
								{{/foreach}}
							{{/if}}
							{{if $field.hint!=''}}
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<br/><span class="de_hint">{{$field.hint}}</span>
								{{/if}}
							{{/if}}
						</td>
					</tr>
				{{/if}}
			{{/foreach}}
		{{/foreach}}
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>