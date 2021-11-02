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

{{if in_array('dvds|edit_all',$smarty.session.permissions) || (in_array('dvds|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
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
		{{if $options.DVD_COVER_OPTION==0}}
			<input type="hidden" name="cover2_front" value="{{$smarty.post.cover2_front}}"/>
			<input type="hidden" name="cover2_front_hash"/>
			<input type="hidden" name="cover2_back" value="{{$smarty.post.cover2_back}}"/>
			<input type="hidden" name="cover2_back_hash"/>
		{{/if}}
		{{if $config.dvds_mode!='channels'}}
			<input type="hidden" name="user" value="{{$smarty.post.user}}"/>
			<input type="hidden" name="is_video_upload_allowed" value="{{$smarty.post.is_video_upload_allowed}}"/>
		{{/if}}
		<input type="hidden" name="custom1" value="{{$smarty.post.custom1}}"/>
		<input type="hidden" name="custom2" value="{{$smarty.post.custom2}}"/>
		<input type="hidden" name="custom3" value="{{$smarty.post.custom3}}"/>
		<input type="hidden" name="custom4" value="{{$smarty.post.custom4}}"/>
		<input type="hidden" name="custom5" value="{{$smarty.post.custom5}}"/>
		<input type="hidden" name="custom6" value="{{$smarty.post.custom6}}"/>
		<input type="hidden" name="custom7" value="{{$smarty.post.custom7}}"/>
		<input type="hidden" name="custom8" value="{{$smarty.post.custom8}}"/>
		<input type="hidden" name="custom9" value="{{$smarty.post.custom9}}"/>
		<input type="hidden" name="custom10" value="{{$smarty.post.custom10}}"/>
		<input type="hidden" name="custom_file1" value="{{$smarty.post.custom_file1}}"/>
		<input type="hidden" name="custom_file1_hash"/>
		<input type="hidden" name="custom_file2" value="{{$smarty.post.custom_file2}}"/>
		<input type="hidden" name="custom_file2_hash"/>
		<input type="hidden" name="custom_file3" value="{{$smarty.post.custom_file3}}"/>
		<input type="hidden" name="custom_file3_hash"/>
		<input type="hidden" name="custom_file4" value="{{$smarty.post.custom_file4}}"/>
		<input type="hidden" name="custom_file4_hash"/>
		<input type="hidden" name="custom_file5" value="{{$smarty.post.custom_file5}}"/>
		<input type="hidden" name="custom_file5_hash"/>
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="3">
				<div>
					<a href="{{$page_name}}">{{$lang.videos.submenu_option_dvds_list}}</a>
					/
					{{if $smarty.get.action=='add_new'}}
						{{$lang.videos.dvd_add}}
					{{else}}
						{{if $smarty.post.dvd_group_id>0}}
							{{if in_array('dvds_groups|view',$smarty.session.permissions)}}
								<a href="dvds_groups.php?action=change&amp;item_id={{$smarty.post.dvd_group_id}}">{{$smarty.post.dvd_group}}</a>
							{{else}}
								{{$smarty.post.dvd_group}}
							{{/if}}
							/
						{{/if}}
						{{$lang.videos.dvd_edit|replace:"%1%":$smarty.post.title}}
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.post.is_review_needed==1 && $can_edit_all==1}}
			<tr>
				<td class="de_separator" colspan="3"><div>{{$lang.videos.dvd_divider_review}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="3">
						<span class="de_hint">{{$lang.videos.dvd_divider_review_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_reviewed}}:</td>
				<td class="de_control" colspan="2"><div class="de_lv_pair"><input type="checkbox" name="is_reviewed" value="1"/><label>{{$lang.videos.dvd_field_reviewed_yes}}</label></div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="3"><div>{{$lang.videos.dvd_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.videos.dvd_field_title}} (*):</td>
			<td class="de_control">
				<div class="de_str_len">
					<input type="text" name="title" maxlength="255" class="dyn_full_size" value="{{$smarty.post.title}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
			{{if is_array($sidebar_fields)}}
				{{assign var="sidebar_rowspan" value="8"}}
				{{if $smarty.post.website_link!=''}}
					{{assign var="sidebar_rowspan" value=$sidebar_rowspan+1}}
				{{/if}}
				{{if $options.DVD_COVER_OPTION>0}}
					{{assign var="sidebar_rowspan" value=$sidebar_rowspan+2}}
				{{/if}}

				{{assign var="image_field" value=""}}
				{{if $smarty.post.cover1_front!=''}}
					{{assign var="image_field" value="cover1_front"}}
				{{/if}}
				{{if $options.DVD_COVER_OPTION>0}}
					{{assign var="image_size1" value="x"|explode:$options.DVD_COVER_1_SIZE}}
					{{assign var="image_size2" value="x"|explode:$options.DVD_COVER_2_SIZE}}
					{{if ($image_size1[0]>$image_size2[0] || $smarty.post.cover1_front=='') && $smarty.post.cover2_front!=''}}
						{{assign var="image_field" value="cover2_front"}}
					{{/if}}
				{{/if}}
				{{if $image_field!=''}}
					{{assign var="sidebar_image_url" value="`$config.content_url_dvds`/`$smarty.post.dvd_id`/`$smarty.post.$image_field`"}}
				{{/if}}

				{{include file="editor_sidebar_inc.tpl"}}
			{{/if}}
		</tr>
		{{if $smarty.get.action=='change'}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_directory}}:</td>
				<td class="de_control">
					<input type="text" name="dir" maxlength="255" class="dyn_full_size" value="{{$smarty.post.dir}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.dvd_field_directory_hint|replace:"%1%":$lang.videos.dvd_field_title}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.post.website_link!=''}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_website_link}}:</td>
				<td class="de_control">
					<a href="{{$smarty.post.website_link}}" rel="external">{{$smarty.post.website_link}}</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_description}}:</td>
			<td class="de_control">
				<div class="de_str_len">
					<textarea name="description" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.description}}</textarea>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_status}}:</td>
			<td class="de_control">
				<div class="de_lv_pair"><input type="checkbox" name="status_id" value="1" {{if $smarty.post.status_id=='1'}}checked="checked"{{/if}}/><label>{{$lang.videos.dvd_field_status_active}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.dvd_field_status_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_group}}:</td>
			<td class="de_control">
				<select name="dvd_group_id">
					<option value="0">{{$lang.common.select_default_option}}</option>
					{{foreach item="item" from=$list_dvds_groups|smarty:nodefaults}}
						<option value="{{$item.dvd_group_id}}" {{if $item.dvd_group_id==$smarty.post.dvd_group_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_cover1_front}}:</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.videos.dvd_field_cover1_front}}</span>
						<span class="js_param">accept={{$config.image_allowed_ext}}</span>
						{{if $smarty.get.action=='change' && $smarty.post.cover1_front!='' && in_array(end(explode(".",$smarty.post.cover1_front)),explode(",",$config.image_allowed_ext))}}
							<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover1_front}}</span>
						{{/if}}
					</div>
					<input type="text" name="cover1_front" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover1_front!=''}}value="{{$smarty.post.cover1_front}}"{{/if}} readonly="readonly"/>
					<input type="hidden" name="cover1_front_hash"/>
					{{if $can_edit_all==1}}
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.cover1_front==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					{{/if}}
					{{if $smarty.get.action=='change' && $smarty.post.cover1_front!='' && in_array(end(explode(".",$smarty.post.cover1_front)),explode(",",$config.image_allowed_ext))}}
						<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.dvd_field_cover1_front_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_1_SIZE}}</a>)</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_cover1_back}}:</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.videos.dvd_field_cover1_back}}</span>
						<span class="js_param">accept={{$config.image_allowed_ext}}</span>
						{{if $smarty.get.action=='change' && $smarty.post.cover1_back!='' && in_array(end(explode(".",$smarty.post.cover1_back)),explode(",",$config.image_allowed_ext))}}
							<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover1_back}}</span>
						{{/if}}
					</div>
					<input type="text" name="cover1_back" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover1_back!=''}}value="{{$smarty.post.cover1_back}}"{{/if}} readonly="readonly"/>
					<input type="hidden" name="cover1_back_hash"/>
					{{if $can_edit_all==1}}
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.cover1_back==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					{{/if}}
					{{if $smarty.get.action=='change' && $smarty.post.cover1_back!='' && in_array(end(explode(".",$smarty.post.cover1_back)),explode(",",$config.image_allowed_ext))}}
						<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.dvd_field_cover1_back_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_1_SIZE}}</a>)</span>
				{{/if}}
			</td>
		</tr>
		{{if $options.DVD_COVER_OPTION>0}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_cover2_front}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_cover2_front}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.cover2_front!='' && in_array(end(explode(".",$smarty.post.cover2_front)),explode(",",$config.image_allowed_ext))}}
								<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover2_front}}</span>
							{{/if}}
						</div>
						<input type="text" name="cover2_front" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover2_front!=''}}value="{{$smarty.post.cover2_front}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="cover2_front_hash"/>
						{{if $can_edit_all==1}}
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.cover2_front==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{/if}}
						{{if $smarty.get.action=='change' && $smarty.post.cover2_front!='' && in_array(end(explode(".",$smarty.post.cover2_front)),explode(",",$config.image_allowed_ext))}}
							<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						<span class="de_hint">
							{{$lang.videos.dvd_field_cover2_front_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_2_SIZE}}</a>){{if $options.DVD_COVER_OPTION==1}}; {{$lang.videos.dvd_field_cover2_front_hint2|replace:"%1%":$lang.videos.dvd_field_cover1_front}}{{/if}}
						</span>
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_cover2_back}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.videos.dvd_field_cover2_back}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.cover2_back!='' && in_array(end(explode(".",$smarty.post.cover2_back)),explode(",",$config.image_allowed_ext))}}
								<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.cover2_back}}</span>
							{{/if}}
						</div>
						<input type="text" name="cover2_back" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.cover2_back!=''}}value="{{$smarty.post.cover2_back}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="cover2_back_hash"/>
						{{if $can_edit_all==1}}
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.cover2_back==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{/if}}
						{{if $smarty.get.action=='change' && $smarty.post.cover2_back!='' && in_array(end(explode(".",$smarty.post.cover2_back)),explode(",",$config.image_allowed_ext))}}
							<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						<span class="de_hint">
							{{$lang.videos.dvd_field_cover2_back_hint}} (<a href="options.php?page=general_settings">{{$options.DVD_COVER_2_SIZE}}</a>){{if $options.DVD_COVER_OPTION==1}}; {{$lang.videos.dvd_field_cover2_back_hint2|replace:"%1%":$lang.videos.dvd_field_cover1_back}}{{/if}}
						</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.videos.dvd_field_rating}} (*):</td>
			<td class="de_control">
				<input type="text" name="avg_rating" class="fixed_100" value="{{$smarty.post.rating|replace:",":"."|round:1}}"/>
				&nbsp;{{$lang.videos.dvd_field_rating_votes}}:
				<input type="text" name="rating_amount" class="fixed_50" value="{{$smarty.post.rating_amount}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.videos.dvd_field_rating_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $config.dvds_mode=='channels'}}
			<tr>
				<td class="de_separator" colspan="3"><div>{{$lang.videos.dvd_divider_community}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_user}}:</td>
				<td class="de_control" colspan="2">
					<div class="insight">
						<div class="js_params">
							<span class="js_param">url=async/insight_users.php</span>
						</div>
						<input type="text" name="user" maxlength="255" class="fixed_200" value="{{$smarty.post.user}}"/>
						{{if $smarty.session.userdata.is_expert_mode==0}}
							<br/><span class="de_hint">{{$lang.videos.dvd_field_user_hint}}</span>
						{{/if}}
					</div>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_video_upload_allowed}}:</td>
				<td class="de_control" colspan="2">
					<select name="is_video_upload_allowed">
						<option value="0" {{if $smarty.post.is_video_upload_allowed=='0'}}selected="selected"{{/if}}>{{$lang.videos.dvd_field_video_upload_allowed_public}}</option>
						<option value="1" {{if $smarty.post.is_video_upload_allowed=='1'}}selected="selected"{{/if}}>{{$lang.videos.dvd_field_video_upload_allowed_friends}}</option>
						<option value="2" {{if $smarty.post.is_video_upload_allowed=='2'}}selected="selected"{{/if}}>{{$lang.videos.dvd_field_video_upload_allowed_owner}}</option>
					</select>
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_tokens_required}}:</td>
				<td class="de_control" colspan="3">
					<input type="text" name="tokens_required" maxlength="10" size="10" value="{{$smarty.post.tokens_required}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.videos.dvd_field_tokens_required_hint|replace:"%1%":$options.TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="3"><div>{{$lang.videos.dvd_divider_categorization}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_tags}}:</td>
			<td class="de_control" colspan="2">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_tags.php</span>
						<span class="js_param">validate_input=false</span>
						<span class="js_param">submit_mode=simple</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.videos.dvd_field_tags_empty}}</span>
					</div>
					<div class="list"></div>
					<input type="hidden" name="tags" value="{{$smarty.post.tags}}"/>
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_tag" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.dvd_field_tags_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_categories}}:</td>
			<td class="de_control" colspan="2">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_categories.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=category_ids[]</span>
						{{if in_array('categories|add',$smarty.session.permissions)}}
							<span class="js_param">allow_creation=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.videos.dvd_field_categories_empty}}</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.categories|smarty:nodefaults}}
						<input type="hidden" name="category_ids[]" value="{{$item.category_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_category" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.dvd_field_categories_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.videos.dvd_field_models}}:</td>
			<td class="de_control" colspan="2">
				<div class="de_insight_list">
					<div class="js_params">
						<span class="js_param">url=async/insight_models.php</span>
						<span class="js_param">submit_mode=compound</span>
						<span class="js_param">submit_name=model_ids[]</span>
						{{if in_array('models|add',$smarty.session.permissions)}}
							<span class="js_param">allow_creation=true</span>
						{{/if}}
						<span class="js_param">empty_message={{$lang.videos.dvd_field_models_empty}}</span>
						{{if $can_edit_all!=1}}
							<span class="js_param">forbid_delete=true</span>
						{{/if}}
					</div>
					<div class="list"></div>
					{{foreach name="data" item="item" from=$smarty.post.models|smarty:nodefaults}}
						<input type="hidden" name="model_ids[]" value="{{$item.model_id}}" alt="{{$item.title}}"/>
					{{/foreach}}
					{{if $can_edit_all==1}}
						<div class="controls">
							<input type="text" name="new_model" class="preserve_editing fixed_300" value=""/>
							<input type="button" class="add" value="{{$lang.common.add}}"/>
							<input type="button" class="all" value="{{$lang.videos.dvd_field_models_all}}"/>
						</div>
					{{/if}}
				</div>
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">
					<div>{{$lang.videos.dvd_field_flags}}:</div>
				</td>
				<td class="de_control" colspan="2">
					<div class="de_deletable_list">
						<div class="js_params">
							<span class="js_param">submit_name=delete_flags[]</span>
							<span class="js_param">empty_message={{$lang.videos.dvd_field_flags_empty}}</span>
						</div>
						<div class="list">
							{{if count($smarty.post.flags)>0}}
								{{foreach name=data item=item from=$smarty.post.flags|smarty:nodefaults}}
									{{if $can_edit_all==1}}
										<a name="{{$item.flag_id}}">{{$item.title}} ({{$item.votes}}){{if !$smarty.foreach.data.last}}, {{/if}}</a>
									{{else}}
										<span>{{$item.title}} ({{$item.votes}}){{if !$smarty.foreach.data.last}}, {{/if}}</span>
									{{/if}}
								{{/foreach}}
								<div class="clear_both"></div>
							{{else}}
								{{$lang.videos.dvd_field_flags_empty}}
							{{/if}}
						</div>
					</div>
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_DVD_FIELD_1==1 || $options.ENABLE_DVD_FIELD_2==1 || $options.ENABLE_DVD_FIELD_3==1 || $options.ENABLE_DVD_FIELD_4==1 || $options.ENABLE_DVD_FIELD_5==1 || $options.ENABLE_DVD_FIELD_6==1 || $options.ENABLE_DVD_FIELD_7==1 || $options.ENABLE_DVD_FIELD_8==1 || $options.ENABLE_DVD_FIELD_9==1 || $options.ENABLE_DVD_FIELD_10==1
			|| $options.ENABLE_DVD_FILE_FIELD_1==1 || $options.ENABLE_DVD_FILE_FIELD_2==1 || $options.ENABLE_DVD_FILE_FIELD_3==1 || $options.ENABLE_DVD_FILE_FIELD_4==1 || $options.ENABLE_DVD_FILE_FIELD_5==1}}
			<tr>
				<td class="de_separator" colspan="3"><div>{{$lang.videos.dvd_divider_customization}}</div></td>
			</tr>
			{{if $options.ENABLE_DVD_FIELD_1==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_1_NAME}}:</td>
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
			{{if $options.ENABLE_DVD_FIELD_2==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_2_NAME}}:</td>
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
			{{if $options.ENABLE_DVD_FIELD_3==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_3_NAME}}:</td>
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
			{{if $options.ENABLE_DVD_FIELD_4==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_4_NAME}}:</td>
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
			{{if $options.ENABLE_DVD_FIELD_5==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_5_NAME}}:</td>
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
			{{if $options.ENABLE_DVD_FIELD_6==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_6_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_str_len">
							<textarea name="custom6" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom6}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FIELD_7==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_7_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_str_len">
							<textarea name="custom7" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom7}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FIELD_8==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_8_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_str_len">
							<textarea name="custom8" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom8}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FIELD_9==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_9_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_str_len">
							<textarea name="custom9" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom9}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FIELD_10==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FIELD_10_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_str_len">
							<textarea name="custom10" class="dyn_full_size {{if $smarty.session.userdata.is_wysiwyg_enabled_other=='1'}}tinymce{{/if}}" cols="40" rows="4">{{$smarty.post.custom10}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint"><span class="de_str_len_value"></span>{{$lang.common.symbols}}</span>
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FILE_FIELD_1==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FILE_FIELD_1_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.DVD_FILE_FIELD_1_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file1!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file1)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file1}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file1}}</span>
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
			{{if $options.ENABLE_DVD_FILE_FIELD_2==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FILE_FIELD_2_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.DVD_FILE_FIELD_2_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file2!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file2)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file2}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file2}}</span>
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
			{{if $options.ENABLE_DVD_FILE_FIELD_3==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FILE_FIELD_3_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.DVD_FILE_FIELD_3_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file3!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file3)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file3}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file3}}</span>
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
			{{if $options.ENABLE_DVD_FILE_FIELD_4==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FILE_FIELD_4_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.DVD_FILE_FIELD_4_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file4)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file4}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file4}}</span>
									{{/if}}
								{{/if}}
							</div>
							<input type="text" name="custom_file4" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}value="{{$smarty.post.custom_file4}}"{{/if}} readonly="readonly"/>
							<input type="hidden" name="custom_file4_hash"/>
							{{if $can_edit_all==1}}
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.custom_file4==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
							{{if $smarty.get.action=='change' && $smarty.post.custom_file4!=''}}
								{{if in_array(end(explode(".",$smarty.post.custom_file4)),explode(",",$config.image_allowed_ext))}}
									<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
								{{else}}
									<input type="button" class="de_fu_download" value="{{$lang.common.attachment_btn_download}}"/>
								{{/if}}
							{{/if}}
						</div>
					</td>
				</tr>
			{{/if}}
			{{if $options.ENABLE_DVD_FILE_FIELD_5==1}}
				<tr>
					<td class="de_label">{{$options.DVD_FILE_FIELD_5_NAME}}:</td>
					<td class="de_control" colspan="2">
						<div class="de_fu">
							<div class="js_params">
								<span class="js_param">title={{$options.DVD_FILE_FIELD_5_NAME}}</span>
								{{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}
									{{if in_array(end(explode(".",$smarty.post.custom_file5)),explode(",",$config.image_allowed_ext))}}
										<span class="js_param">preview_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file5}}</span>
									{{else}}
										<span class="js_param">download_url={{$config.content_url_dvds}}/{{$smarty.post.dvd_id}}/{{$smarty.post.custom_file5}}</span>
									{{/if}}
								{{/if}}
							</div>
							<input type="text" name="custom_file5" class="fixed_500" maxlength="100" {{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}value="{{$smarty.post.custom_file5}}"{{/if}} readonly="readonly"/>
							<input type="hidden" name="custom_file5_hash"/>
							{{if $can_edit_all==1}}
								<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
								<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.custom_file5==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
							{{/if}}
							{{if $smarty.get.action=='change' && $smarty.post.custom_file5!=''}}
								{{if in_array(end(explode(".",$smarty.post.custom_file5)),explode(",",$config.image_allowed_ext))}}
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
		{{if $config.dvds_mode!='channels'}}
			<tr>
				<td class="de_separator" colspan="3"><div>{{$lang.videos.dvd_divider_videos}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.videos.dvd_field_add_videos}}:</td>
				<td class="de_control" colspan="2">
					<div class="de_insight_list">
						<div class="js_params">
							<span class="js_param">url=async/insight_videos.php</span>
							<span class="js_param">submit_mode=compound</span>
							<span class="js_param">submit_name=add_video_ids[]</span>
							<span class="js_param">empty_message={{$lang.videos.dvd_field_add_videos_empty}}</span>
						</div>
						<div class="list"></div>
						{{if $can_edit_all==1}}
							<div class="controls">
								<input type="text" name="new_video" class="fixed_300" value=""/>
								<input type="button" class="add" value="{{$lang.common.add}}"/>
							</div>
						{{/if}}
					</div>
				</td>
			</tr>
			{{if count($smarty.post.videos)>0}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<tr>
						<td class="de_simple_text" colspan="3">
							<span class="de_hint">{{$lang.videos.dvd_divider_videos_hint}}</span>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="de_table_control" colspan="3">
						<table class="de_edit_grid">
							<tr class="eg_header">
								<td class="eg_selector"><div><input type="checkbox"/> {{$lang.common.dg_actions_detach}}</div></td>
								<td>{{$lang.videos.video_field_id}}</td>
								<td>{{$lang.videos.video_field_title}}</td>
								<td>{{$lang.videos.video_field_duration}}</td>
								<td>{{$lang.videos.video_field_status}}</td>
								<td>{{$lang.videos.video_field_visits}}</td>
								<td>{{$lang.videos.video_field_rating}}</td>
								<td>{{$lang.videos.video_field_order}}</td>
							</tr>
							{{foreach item=item from=$smarty.post.videos|smarty:nodefaults}}
								<tr class="eg_data fixed_height_30 {{if $item.status_id==0}}disabled{{/if}}">
									<td class="eg_selector"><input type="checkbox" name="delete_video_ids[]" value="{{$item.video_id}}"/></td>
									<td class="nowrap">
										{{if in_array('videos|view',$smarty.session.permissions)}}
											<a href="videos.php?action=change&amp;item_id={{$item.video_id}}">{{$item.video_id}}</a>
										{{else}}
											{{$item.video_id}}
										{{/if}}
									</td>
									<td>{{$item.title}}</td>
									<td class="nowrap">{{$item.duration}}</td>
									<td class="nowrap">{{if $item.status_id==1}}{{$lang.videos.video_field_status_active}}{{elseif $item.status_id==2}}<span class="highlighted_text">{{$lang.videos.video_field_status_error}}</span>{{elseif $item.status_id==3}}{{$lang.videos.video_field_status_in_process}}{{elseif $item.status_id==4}}{{$lang.videos.video_field_status_deleting}}{{elseif $item.status_id==5}}{{$lang.videos.video_field_status_deleted}}{{else}}{{$lang.videos.video_field_status_disabled}}{{/if}}</td>
									<td class="nowrap {{if $item.video_viewed==0}}disabled{{/if}}">{{if $item.video_viewed>999}}{{$item.video_viewed/1000|number_format:1:".":""}}{{$lang.common.traffic_k}}{{else}}{{$item.video_viewed}}{{/if}}</td>
									<td class="nowrap {{if $item.rating==0}}disabled{{/if}}">{{$item.rating|number_format:1:".":" "}}</td>
									<td>
										<input type="text" name="video_sorting_{{$item.video_id}}" maxlength="32" value="{{$item.sort_id}}" size="3" autocomplete="off"/>
									</td>
								</tr>
							{{/foreach}}
						</table>
					</td>
				</tr>
			{{/if}}
		{{/if}}
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="3">
					{{if $smarty.get.action=='add_new'}}
						{{if $smarty.session.save.options.default_save_button==1}}
							<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
							<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
						{{else}}
							<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
							<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
						{{/if}}
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

{{if in_array('dvds|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('dvds|edit_all',$smarty.session.permissions)}}
	{{assign var=can_edit value=1}}
{{else}}
	{{assign var=can_edit value=0}}
{{/if}}
{{assign var=can_invoke_additional value=1}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.videos.dvd_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.videos.dvd_field_status_active}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.videos.dvd_field_status_disabled}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_dvd_group_id!=''}}dgf_selected{{/if}}">{{$lang.videos.dvd_field_group}}:</td>
					<td class="dgf_control">
						<select name="se_dvd_group_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_dvds_groups|smarty:nodefaults}}
								<option value="{{$item.dvd_group_id}}" {{if $item.dvd_group_id==$smarty.session.save.$page_name.se_dvd_group_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_tag!=''}}dgf_selected{{/if}}">{{$lang.videos.dvd_field_tag}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_tags.php</span>
							</div>
							<input type="text" name="se_tag" size="20" value="{{$smarty.session.save.$page_name.se_tag}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_category!=''}}dgf_selected{{/if}}">{{$lang.videos.dvd_field_category}}:</td>
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
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_model!=''}}dgf_selected{{/if}}">{{$lang.videos.dvd_field_model}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_models.php</span>
							</div>
							<input type="text" name="se_model" size="20" value="{{$smarty.session.save.$page_name.se_model}}"/>
						</div>
					</td>
				</tr>
			</table>
			{{if $config.dvds_mode=='channels'}}
				<table class="dgf_filter">
					<tr>
						<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.videos.dvd_field_user}}:</td>
						<td class="dgf_control">
							<div class="insight">
								<div class="js_params">
									<span class="js_param">url=async/insight_users.php</span>
								</div>
								<input type="text" name="se_user" size="20" value="{{$smarty.session.save.$page_name.se_user}}"/>
							</div>
						</td>
					</tr>
				</table>
			{{/if}}
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_field!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_field}}:</td>
					<td class="dgf_control">
						<select name="se_field">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_description}}</option>
							<option value="empty/group" {{if $smarty.session.save.$page_name.se_field=="empty/group"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_group}}</option>
							{{if $config.dvds_mode=='channels'}}
								<option value="empty/user" {{if $smarty.session.save.$page_name.se_field=="empty/user"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_user}}</option>
							{{/if}}
							<option value="empty/cover1_front" {{if $smarty.session.save.$page_name.se_field=="empty/cover1_front"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_front}}</option>
							<option value="empty/cover1_back" {{if $smarty.session.save.$page_name.se_field=="empty/cover1_back"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_back}}</option>
							<option value="empty/cover2_front" {{if $smarty.session.save.$page_name.se_field=="empty/cover2_front"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_front}}</option>
							<option value="empty/cover2_back" {{if $smarty.session.save.$page_name.se_field=="empty/cover2_back"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_back}}</option>
							<option value="empty/rating" {{if $smarty.session.save.$page_name.se_field=="empty/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_rating}}</option>
							<option value="empty/dvd_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/dvd_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_visits}}</option>
							{{if $options.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}
								<option value="empty/tokens_required" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_required"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_tokens_required}}</option>
							{{/if}}
							<option value="empty/tags" {{if $smarty.session.save.$page_name.se_field=="empty/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_tags}}</option>
							<option value="empty/categories" {{if $smarty.session.save.$page_name.se_field=="empty/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_categories}}</option>
							<option value="empty/models" {{if $smarty.session.save.$page_name.se_field=="empty/models"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.videos.dvd_field_models}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="DVD_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_DVD_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
							{{section name="data" start="1" loop="6"}}
								{{assign var="custom_field_id" value="custom_file`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="DVD_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_DVD_FILE_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="empty/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
							<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_description}}</option>
							<option value="filled/group" {{if $smarty.session.save.$page_name.se_field=="filled/group"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_group}}</option>
							{{if $config.dvds_mode=='channels'}}
								<option value="filled/user" {{if $smarty.session.save.$page_name.se_field=="filled/user"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_user}}</option>
							{{/if}}
							<option value="filled/cover1_front" {{if $smarty.session.save.$page_name.se_field=="filled/cover1_front"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_front}}</option>
							<option value="filled/cover1_back" {{if $smarty.session.save.$page_name.se_field=="filled/cover1_back"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover1_back}}</option>
							<option value="filled/cover2_front" {{if $smarty.session.save.$page_name.se_field=="filled/cover2_front"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_front}}</option>
							<option value="filled/cover2_back" {{if $smarty.session.save.$page_name.se_field=="filled/cover2_back"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_cover2_back}}</option>
							<option value="filled/rating" {{if $smarty.session.save.$page_name.se_field=="filled/rating"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_rating}}</option>
							<option value="filled/dvd_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/dvd_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_visits}}</option>
							{{if $options.ENABLE_TOKENS_SUBSCRIBE_DVDS==1}}
								<option value="filled/tokens_required" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_required"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_tokens_required}}</option>
							{{/if}}
							<option value="filled/tags" {{if $smarty.session.save.$page_name.se_field=="filled/tags"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_tags}}</option>
							<option value="filled/categories" {{if $smarty.session.save.$page_name.se_field=="filled/categories"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_categories}}</option>
							<option value="filled/models" {{if $smarty.session.save.$page_name.se_field=="filled/models"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.videos.dvd_field_models}}</option>
							{{section name="data" start="1" loop="11"}}
								{{assign var="custom_field_id" value="custom`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="DVD_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_DVD_FIELD_`$smarty.section.data.index`"}}
								{{if $options[$custom_field_enable_id]==1}}
									<option value="filled/{{$custom_field_id}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field_id`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$options[$custom_field_name_id]}}</option>
								{{/if}}
							{{/section}}
							{{section name="data" start="1" loop="6"}}
								{{assign var="custom_field_id" value="custom_file`$smarty.section.data.index`"}}
								{{assign var="custom_field_name_id" value="DVD_FILE_FIELD_`$smarty.section.data.index`_NAME"}}
								{{assign var="custom_field_enable_id" value="ENABLE_DVD_FILE_FIELD_`$smarty.section.data.index`"}}
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
							<option value="notused/videos" {{if $smarty.session.save.$page_name.se_usage=="notused/videos"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_usage_no_videos}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_flag_id!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_flag}}:</td>
					<td class="dgf_control">
						<select name="se_flag_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item=item_flag from=$list_flags_dvds|smarty:nodefaults}}
								<option value="{{$item_flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$item_flag.flag_id}}selected="selected"{{/if}}>{{$item_flag.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			{{if $config.dvds_mode=='channels'}}
				<table class="dgf_filter">
					<tr>
						<td class="dgf_label {{if $smarty.session.save.$page_name.se_review_flag!=''}}dgf_selected{{/if}}"><label for="se_review_flag">{{$lang.common.dg_filter_review_flag}}:</label></td>
						<td class="dgf_control">
							<select id="se_review_flag" name="se_review_flag">
								<option value="">{{$lang.common.dg_filter_option_all}}</option>
								<option value="1" {{if $smarty.session.save.$page_name.se_review_flag=='1'}}selected="selected"{{/if}}>{{$lang.common.dg_filter_review_flag_yes}}</option>
								<option value="2" {{if $smarty.session.save.$page_name.se_review_flag=='2'}}selected="selected"{{/if}}>{{$lang.common.dg_filter_review_flag_no}}</option>
							</select>
						</td>
					</tr>
				</table>
			{{/if}}
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
									<span class="js_param">name={{$item.title}}</span>
									{{if $item.website_link==''}}
										<span class="js_param">website_link_disable=true</span>
									{{else}}
										<span class="js_param">website_link={{$item.website_link}}</span>
									{{/if}}
									{{if $item.is_review_needed!=1}}
										<span class="js_param">mark_reviewed_hide=true</span>
									{{/if}}
									{{if $item.videos_amount==0}}
										<span class="js_param">delete_with_videos_disable=true</span>
									{{/if}}
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
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete_with_videos&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.videos.dvd_action_delete_with_videos}}</span>
							<span class="js_param">confirm={{$lang.videos.dvd_action_delete_with_videos_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">disable=${delete_with_videos_disable}</span>
							<span class="js_param">prompt_value=yes</span>
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
						<li class="js_params">
							<span class="js_param">href=?batch_action=mark_reviewed&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_mark_reviewed}}</span>
							<span class="js_param">hide=${mark_reviewed_hide}</span>
						</li>
					{{/if}}
					{{if in_array('users|manage_comments',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=comments.php?action=add_new&amp;object_type_id=5&amp;object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=${website_link}</span>
						<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
						<span class="js_param">disable=${website_link_disable}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					{{if in_array('system|administration',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=5&amp;se_object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
							<span class="js_param">plain_link=true</span>
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
									<option value="delete_with_videos">{{$lang.videos.dvd_batch_action_delete_with_videos}}</option>
								{{/if}}
								{{if $can_edit==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
									<option value="mark_reviewed">{{$lang.common.dg_batch_actions_mark_reviewed}}</option>
								{{/if}}
							</select>
						</td>
						<td class="dgb_control">
							<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
						</td>
					{{/if}}
					{{if $can_edit==1}}
						{{foreach from=$table_fields|smarty:nodefaults item="field"}}
							{{if $field.type=='sorting' && $field.is_enabled==1}}
								<td class="dgb_control">
									<input type="submit" name="reorder" value="{{$lang.common.dg_actions_reorder}}"/>
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
					<span class="js_param">value=delete_with_videos</span>
					<span class="js_param">confirm={{$lang.videos.dvd_batch_action_delete_with_videos_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">prompt_value=yes</span>
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