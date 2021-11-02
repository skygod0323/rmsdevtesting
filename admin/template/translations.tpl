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


{{assign var="can_edit" value="0"}}
{{foreach item=item from=$list_languages|smarty:nodefaults}}
	{{assign var="permission_id" value="localization|`$item.code`"}}
	{{if in_array($permission_id,$smarty.session.permissions)}}
		{{assign var="can_edit" value="1"}}
	{{/if}}
{{/foreach}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="change_complete"/>
		<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		<input type="hidden" name="item_type" value="{{$smarty.get.item_type}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>
				<a href="{{$page_name}}">{{$lang.settings.submenu_option_translations_list}}</a> /
				{{if $item_type==1}}
					{{if in_array('videos|view',$smarty.session.permissions)}}
						<a href="videos.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==2}}
					{{if in_array('albums|view',$smarty.session.permissions)}}
						<a href="albums.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==3}}
					{{if in_array('content_sources|view',$smarty.session.permissions)}}
						<a href="content_sources.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==4}}
					{{if in_array('models|view',$smarty.session.permissions)}}
						<a href="models.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==5}}
					{{if in_array('dvds|view',$smarty.session.permissions)}}
						<a href="dvds.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==6}}
					{{if in_array('categories|view',$smarty.session.permissions)}}
						<a href="categories.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==7}}
					{{if in_array('category_groups|view',$smarty.session.permissions)}}
						<a href="categories_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==8}}
					{{if in_array('content_sources_groups|view',$smarty.session.permissions)}}
						<a href="content_sources_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==9}}
					{{if in_array('tags|view',$smarty.session.permissions)}}
						<a href="tags.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==10}}
					{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
						<a href="dvds_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{elseif $item_type==14}}
					{{if in_array('models_groups|view',$smarty.session.permissions)}}
						<a href="models_groups.php?action=change&amp;item_id={{$smarty.post.$table_key_name}}">{{$smarty.post.$title_selector}}</a>
					{{else}}
						{{$smarty.post.$title_selector}}
					{{/if}}
				{{/if}}
				/
				{{$lang.settings.translation_edit}}
			</div></td>
		</tr>
		{{if $item_type==1}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_video_info}}</div></td>
			</tr>
			{{if $smarty.post.screen_url!=''}}
				{{if $smarty.post.screen_amount>0}}
					<tr>
						<td class="de_control" colspan="2">
							<div class="de_img_list">
								{{assign var="pos" value=1}}
								{{section name="screenshots" start="0" step="1" loop=$smarty.post.screen_amount}}
									<div class="de_img_list_item">
										<div class="de_img_list_thumb">
											<img src="{{$smarty.post.screen_url}}/{{$pos}}.jpg?rnd={{$smarty.now}}" alt=""/>
										</div>
									</div>
									{{assign var="pos" value=$pos+1}}
								{{/section}}
							</div>
						</td>
					</tr>
				{{/if}}
			{{else}}
				<tr>
					<td class="de_control" colspan="2">
						<span class="de_hint">{{$lang.settings.translation_edit_object_type_video_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_tags}}:</td>
				<td class="de_control">
					{{if count($smarty.post.tags)>0}}
						{{foreach name=data item=item from=$smarty.post.tags}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_categories}}:</td>
				<td class="de_control">
					{{if count($smarty.post.categories)>0}}
						{{foreach name=data item=item from=$smarty.post.categories}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_models}}:</td>
				<td class="de_control">
					{{if count($smarty.post.models)>0}}
						{{foreach name=data item=item from=$smarty.post.models}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_content_source}}:</td>
				<td class="de_control">
					{{if $smarty.post.content_source!=''}}
						{{$smarty.post.content_source}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{elseif $item_type==2}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_album_info}}</div></td>
			</tr>
			{{if $smarty.post.photos_amount>0}}
				<tr>
					<td class="de_control" colspan="4">
						<div class="de_img_list">
							{{assign var="pos" value=1}}
							{{section name="images" start="0" step="1" loop=$smarty.post.photos_amount}}
								<div class="de_img_list_item">
									<div class="de_img_list_thumb">
										{{assign var="pos" value=$pos-1}}
										<img src="{{$config.project_url}}/get_image/{{$smarty.post.server_group_id}}/{{$smarty.post.list_images[$pos].file_path}}/?rnd={{$smarty.now}}" alt="{{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}" title="{{$lang.albums.images_mgmt_file_title_image|replace:"%1%":$smarty.post.list_images[$pos].image_id}}"/>
										{{assign var="pos" value=$pos+1}}
									</div>
								</div>
								{{assign var=pos value=$pos+1}}
							{{/section}}
						</div>
					</td>
				</tr>
			{{else}}
				<tr>
					<td class="de_control" colspan="2">
						<span class="de_hint">{{$lang.settings.translation_edit_object_type_album_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_tags}}:</td>
				<td class="de_control">
					{{if count($smarty.post.tags)>0}}
						{{foreach name=data item=item from=$smarty.post.tags}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_categories}}:</td>
				<td class="de_control">
					{{if count($smarty.post.categories)>0}}
						{{foreach name=data item=item from=$smarty.post.categories}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_models}}:</td>
				<td class="de_control">
					{{if count($smarty.post.models)>0}}
						{{foreach name=data item=item from=$smarty.post.models}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_content_source}}:</td>
				<td class="de_control">
					{{if $smarty.post.content_source!=''}}
						{{$smarty.post.content_source}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{elseif $item_type==3}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_content_source_info}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_group}}:</td>
				<td class="de_control">
					{{if $smarty.post.group!=''}}
						{{$smarty.post.group}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_tags}}:</td>
				<td class="de_control">
					{{if count($smarty.post.tags)>0}}
						{{foreach name=data item=item from=$smarty.post.tags}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_categories}}:</td>
				<td class="de_control">
					{{if count($smarty.post.categories)>0}}
						{{foreach name=data item=item from=$smarty.post.categories}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{elseif $item_type==4}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_model_info}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_group}}:</td>
				<td class="de_control">
					{{if $smarty.post.group!=''}}
						{{$smarty.post.group}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_tags}}:</td>
				<td class="de_control">
					{{if count($smarty.post.tags)>0}}
						{{foreach name=data item=item from=$smarty.post.tags}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_categories}}:</td>
				<td class="de_control">
					{{if count($smarty.post.categories)>0}}
						{{foreach name=data item=item from=$smarty.post.categories}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{elseif $item_type==5}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_dvd_info}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_group}}:</td>
				<td class="de_control">
					{{if $smarty.post.group!=''}}
						{{$smarty.post.group}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_tags}}:</td>
				<td class="de_control">
					{{if count($smarty.post.tags)>0}}
						{{foreach name=data item=item from=$smarty.post.tags}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_categories}}:</td>
				<td class="de_control">
					{{if count($smarty.post.categories)>0}}
						{{foreach name=data item=item from=$smarty.post.categories}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_models}}:</td>
				<td class="de_control">
					{{if count($smarty.post.models)>0}}
						{{foreach name=data item=item from=$smarty.post.models}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{elseif $item_type==6}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_category_info}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_group}}:</td>
				<td class="de_control">
					{{if $smarty.post.group!=''}}
						{{$smarty.post.group}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{elseif $item_type==10}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_dvd_group_info}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_tags}}:</td>
				<td class="de_control">
					{{if count($smarty.post.tags)>0}}
						{{foreach name=data item=item from=$smarty.post.tags}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_categories}}:</td>
				<td class="de_control">
					{{if count($smarty.post.categories)>0}}
						{{foreach name=data item=item from=$smarty.post.categories}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_object_models}}:</td>
				<td class="de_control">
					{{if count($smarty.post.models)>0}}
						{{foreach name=data item=item from=$smarty.post.models}}
							{{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
						{{/foreach}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_title_translation}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.translation_field_original_title}}:</td>
			<td class="de_control">
				<div class="de_str_len">
					<input type="text" name="{{$title_selector}}" maxlength="255" class="dyn_full_size readonly_field" value="{{$smarty.post.$title_selector}}" readonly="readonly"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.settings.translation_field_original_directory}}:</td>
			<td class="de_control">
				<div class="de_str_len">
					<input type="text" name="{{$dir_selector}}" maxlength="255" class="dyn_full_size readonly_field" value="{{$smarty.post.$dir_selector}}" readonly="readonly"/>
				</div>
			</td>
		</tr>
		{{foreach item=item from=$list_languages|smarty:nodefaults}}
			{{assign var="permission_id" value="localization|`$item.code`"}}
			{{assign var="can_edit_language" value="0"}}
			{{if in_array($permission_id,$smarty.session.permissions)}}
				{{assign var="can_edit_language" value="1"}}
			{{/if}}
			{{assign var="language_selector" value="`$title_selector`_`$item.code`"}}
			{{assign var="column_ok_id" value="ok_`$item.code`"}}
			{{assign var="column_title_id" value="title_`$item.code`"}}
			<tr {{if is_array($smarty.session.save.$page_name.grid_columns) && $smarty.session.save.$page_name.grid_columns.$column_ok_id!=1 && $smarty.session.save.$page_name.grid_columns.$column_title_id!=1}}class="hidden"{{/if}}>
				<td class="de_label">{{$lang.settings.translation_field_language_title|replace:"%1%":$item.title}}:</td>
				<td class="de_control">
					<div class="de_str_len">
						<input type="text" name="{{$language_selector}}" maxlength="255" class="dyn_full_size {{if $can_edit_language==0}}readonly_field{{/if}}" value="{{$smarty.post.$language_selector}}" {{if $can_edit_language==0}}readonly="readonly"{{/if}}/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if $item.is_directories_localize==1}}
				{{assign var="language_selector" value="`$dir_selector`_`$item.code`"}}
				<tr {{if is_array($smarty.session.save.$page_name.grid_columns) && $smarty.session.save.$page_name.grid_columns.$column_ok_id!=1 && $smarty.session.save.$page_name.grid_columns.$column_title_id!=1}}class="hidden"{{/if}}>
					<td class="de_label">{{$lang.settings.translation_field_language_directory|replace:"%1%":$item.title}}:</td>
					<td class="de_control">
						<input type="text" name="{{$language_selector}}" maxlength="255" class="dyn_full_size {{if $can_edit_language==0}}readonly_field{{/if}}" value="{{$smarty.post.$language_selector}}" {{if $can_edit_language==0}}readonly="readonly"{{/if}}/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.settings.translation_field_language_directory_hint|replace:"%1%":$item.title|replace:"%2%":$lang.settings.translation_field_language_title|replace:"%1%":$item.title}}</span>
						{{/if}}
					</td>
				</tr>
			{{/if}}
		{{/foreach}}
		{{if $desc_selector!=''}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.translation_divider_description_translation}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.translation_field_original_description}}:</td>
				<td class="de_control">
					<div class="de_str_len">
						<textarea name="{{$desc_selector}}" class="dyn_full_size readonly_field {{if $smarty.session.userdata[$tiny_mce_key]=='1'}}tinymce{{/if}}" cols="40" rows="3" readonly="readonly">{{$smarty.post.$desc_selector}}</textarea>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			{{foreach item=item from=$list_languages|smarty:nodefaults}}
				{{assign var="permission_id" value="localization|`$item.code`"}}
				{{assign var="can_edit_language" value="0"}}
				{{if in_array($permission_id,$smarty.session.permissions)}}
					{{assign var="can_edit_language" value="1"}}
				{{/if}}
				{{assign var="language_selector" value="`$desc_selector`_`$item.code`"}}
				{{assign var="column_ok_id" value="ok_`$item.code`"}}
				{{assign var="column_desc_id" value="description_`$item.code`"}}
				{{if $item.translation_scope==0}}
					<tr {{if is_array($smarty.session.save.$page_name.grid_columns) && $smarty.session.save.$page_name.grid_columns.$column_ok_id!=1 && $smarty.session.save.$page_name.grid_columns.$column_desc_id!=1}}class="hidden"{{/if}}>
						<td class="de_label">{{$lang.settings.translation_field_language_description|replace:"%1%":$item.title}}:</td>
						<td class="de_control">
							<div class="de_str_len">
								<textarea name="{{$language_selector}}" class="dyn_full_size {{if $smarty.session.userdata[$tiny_mce_key]=='1'}}tinymce{{/if}} {{if $can_edit_language==0}}readonly_field{{/if}}" cols="40" rows="3" {{if $can_edit_language==0}}readonly="readonly"{{/if}}>{{$smarty.post.$language_selector}}</textarea>
								{{if $smarty.session.userdata.is_expert_mode==0}}
									<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
								{{/if}}
							</div>
						</td>
					</tr>
				{{/if}}
			{{/foreach}}
		{{/if}}
		{{if $can_edit==1}}
			<tr>
				<td class="de_action_group" colspan="2">
					{{if $smarty.session.save.options.default_save_button==1}}
						<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					{{else}}
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_edit" value="{{$lang.common.btn_save_and_edit_next}}"/>
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
					<td class="dgf_label">{{$lang.settings.translation_field_object_type}}:</td>
					<td class="dgf_control">
						<select name="se_object_type" class="dgf_switcher">
							<option value="">{{$lang.common.select_default_option}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_object_type=='1'}}selected="selected"{{/if}}>{{$lang.common.object_type_videos}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_object_type=='2'}}selected="selected"{{/if}}>{{$lang.common.object_type_albums}}</option>
							<option value="6" {{if $smarty.session.save.$page_name.se_object_type=='6'}}selected="selected"{{/if}}>{{$lang.common.object_type_categories}}</option>
							<option value="7" {{if $smarty.session.save.$page_name.se_object_type=='7'}}selected="selected"{{/if}}>{{$lang.common.object_type_category_groups}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_object_type=='4'}}selected="selected"{{/if}}>{{$lang.common.object_type_models}}</option>
							<option value="14" {{if $smarty.session.save.$page_name.se_object_type=='14'}}selected="selected"{{/if}}>{{$lang.common.object_type_model_groups}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_object_type=='3'}}selected="selected"{{/if}}>{{$lang.common.object_type_content_sources}}</option>
							<option value="8" {{if $smarty.session.save.$page_name.se_object_type=='8'}}selected="selected"{{/if}}>{{$lang.common.object_type_content_source_groups}}</option>
							<option value="9" {{if $smarty.session.save.$page_name.se_object_type=='9'}}selected="selected"{{/if}}>{{$lang.common.object_type_tags}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_object_type=='5'}}selected="selected"{{/if}}>{{$lang.common.object_type_dvds}}</option>
							<option value="10" {{if $smarty.session.save.$page_name.se_object_type=='10'}}selected="selected"{{/if}}>{{$lang.common.object_type_dvd_groups}}</option>
						</select>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_translation_missing_for!=''}}dgf_selected{{/if}}">{{$lang.settings.translation_filter_translation_required}}:</td>
					<td class="dgf_control">
						<select name="se_translation_missing_for" class="fixed_100">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item=item from=$list_languages|smarty:nodefaults}}
								<option value="{{$item.code}}" {{if $smarty.session.save.$page_name.se_translation_missing_for==$item.code}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_translation_having_for!=''}}dgf_selected{{/if}}">{{$lang.settings.translation_filter_translation_done}}:</td>
					<td class="dgf_control">
						<select name="se_translation_having_for" class="fixed_100">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item=item from=$list_languages|smarty:nodefaults}}
								<option value="{{$item.code}}" {{if $smarty.session.save.$page_name.se_translation_having_for==$item.code}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_translated_date_from>0}}dgf_selected{{/if}}">{{$lang.settings.translation_filter_translated_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_translated_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_translated_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_translated_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_translated_date_to>0}}dgf_selected{{/if}}">{{$lang.settings.translation_filter_translated_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_translated_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_translated_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_translated_date_to_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				 </tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_columns hidden">
			{{assign var="table_columns_display_mode" value="selector"}}
			{{include file="table_columns_inc.tpl"}}
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg">
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
				{{if $smarty.session.save.$page_name.se_object_type>0}}
					{{foreach name=data key=key item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
						<td>
							<a href="{{$page_name}}?action=change&amp;item_id={{$item.object_id}}&amp;item_type={{$smarty.session.save.$page_name.se_object_type}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						</td>
					</tr>
					{{/foreach}}
				{{else}}
					{{assign var="table_fields_visible" value="0"}}
					{{foreach from=$table_fields|smarty:nodefaults item="field"}}
						{{if $field.is_enabled==1}}
							{{assign var="table_fields_visible" value=$table_fields_visible+1}}
						{{/if}}
					{{/foreach}}
					<tr class="dg_data">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="" disabled="disabled"/></td>
						<td colspan="{{$table_fields_visible+1}}">{{$lang.settings.translation_field_object_type_hint_list}}</td>
					</tr>
				{{/if}}
		   </table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}