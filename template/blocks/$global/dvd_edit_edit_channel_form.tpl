
{{if $async_submit_successful=='true'}}
	<h2 class="title title__modal">
		{{if $smarty.post.dvd_id>0}}
			{{$lang.edit_channel.title_edit_channel}}
		{{else}}
			{{$lang.edit_channel.title_create_channel}}
		{{/if}}
	</h2>

	{{if $smarty.post.action=='change_complete'}}
		<div class="success" data-fancybox="refresh">
			{{$lang.edit_channel.success_message|replace:"%1%":$async_object_data.title}}
		</div>
	{{else}}
		<div data-dvd-id="{{$async_object_data.dvd_id}}" data-dvd-title="{{$async_object_data.title}}">
			{{$lang.edit_channel.success_message_create|replace:"%1%":$async_object_data.title}}
		</div>
	{{/if}}
{{else}}
<div class="modal popup-holder" id="modal-logon">
	{{assign var="is_locked" value="false"}}
	{{if $change_forbidden==1}}
		{{assign var="is_locked" value="true"}}
	{{/if}}
	{{if $smarty.post.is_locked==1}}
		{{assign var="is_locked" value="true"}}
	{{/if}}
	<div class="modal__window">
		<form {{if $smarty.post.dvd_id>0}}action="{{$lang.urls.memberzone_edit_channel|replace:"%ID%":$smarty.post.dvd_id}}"{{else}}action="{{$lang.urls.memberzone_create_channel}}"{{/if}} data-form="ajax" method="post">
			<h2 class="title title__modal">
				{{if $smarty.post.dvd_id>0}}
					{{$lang.edit_channel.title_edit_channel}}
				{{else}}
					{{$lang.edit_channel.title_create_channel}}
				{{/if}}
			</h2>
			<div class="generic-error {{if $is_locked!='true'}}hidden{{/if}}">
				{{if $is_locked=='true'}}
					{{$lang.validation.common.dvd_locked}}
				{{/if}}
			</div>
			<div class="modal__window__form  modal__window__form--single cfx">
				<div class="modal__window__row">
					<label for="edit_channel_title" class="field-label modal__window__label required">{{$lang.edit_channel.field_title}}</label>
					<div class="relative">
						<input type="text" name="title" id="edit_channel_title" class="input" value="{{$smarty.post.title}}" placeholder="{{$lang.edit_channel.field_title_hint}}" {{if $lang.channels.truncate_title_to>0}}maxlength="{{$lang.channels.truncate_title_to}}"{{/if}} {{if $is_locked=='true'}}readonly{{/if}}/>
					</div>
					<div class="field-error down"></div>
				</div>

				{{*<div class="row">
					<label for="edit_channel_description" class="field-label {{if $require_description==1}}required{{/if}}">{{$lang.edit_channel.field_description}}</label>
					<textarea name="description" id="edit_channel_description" class="textarea" rows="3" placeholder="{{$lang.edit_channel.field_description_hint}}" {{if $is_locked=='true'}}readonly{{/if}}>{{$smarty.post.description}}</textarea>
					<div class="field-error down"></div>
				</div>

				<div class="row">
					<label for="edit_channel_cover1_front" class="field-label {{if $require_cover1_front==1}}required{{/if}}">{{$lang.edit_channel.field_cover1_front|replace:"%1%":$cover1_size}}</label>
					<div class="file-control">
						<input type="text" class="textfield" placeholder="{{if $smarty.post.cover1_front==''}}{{$lang.common_forms.file_upload_btn_browse}}{{else}}{{$lang.common_forms.file_upload_btn_change}}{{/if}} {{$lang.edit_channel.field_cover1_front_hint|replace:"%1%":$cover1_size}}" {{if $smarty.post.cover1_front!=''}}value="{{$lang.common_forms.file_upload_btn_change}} {{$smarty.post.cover1_front}} ({{$cover1_size}})"{{/if}} readonly/>
						<div class="button">
							{{if $smarty.post.cover1_front==''}}
								{{$lang.common_forms.file_upload_btn_browse}}
							{{else}}
								{{$lang.common_forms.file_upload_btn_change}}
							{{/if}}
						</div>
						<input type="file" name="cover1_front" id="edit_channel_cover1_front" class="file" {{if $is_locked=='true'}}disabled{{/if}}/>
						<div class="field-error down"></div>
					</div>
				</div>

				<div class="row">
					<label for="edit_channel_cover2_front" class="field-label {{if $require_cover2_front==1}}required{{/if}}">{{$lang.edit_channel.field_cover2_front|replace:"%1%":$cover2_size}}</label>
					<div class="file-control">
						<input type="text" class="textfield" placeholder="{{if $smarty.post.cover2_front==''}}{{$lang.common_forms.file_upload_btn_browse}}{{else}}{{$lang.common_forms.file_upload_btn_change}}{{/if}} {{$lang.edit_channel.field_cover2_front_hint|replace:"%1%":$cover2_size}}" {{if $smarty.post.cover2_front!=''}}value="{{$lang.common_forms.file_upload_btn_change}} {{$smarty.post.cover2_front}} ({{$cover2_size}})"{{/if}} readonly/>
						<div class="button">
							{{if $smarty.post.cover2_front==''}}
								{{$lang.common_forms.file_upload_btn_browse}}
							{{else}}
								{{$lang.common_forms.file_upload_btn_change}}
							{{/if}}
						</div>
						<input type="file" name="cover2_front" id="edit_channel_cover2_front" class="file" {{if $is_locked=='true'}}disabled{{/if}}/>
						<div class="field-error down"></div>
					</div>
				</div>

				{{if $max_categories==1}}
					<div class="row" data-name="category_ids">
						<label for="edit_channel_categories" class="field-label {{if $require_categories==1}}required{{/if}}">{{$lang.edit_channel.field_category}}</label>
						<select id="edit_channel_categories" name="category_ids[]" class="selectbox" {{if $is_locked=='true'}}disabled{{/if}}>
							<option value="">{{$lang.common_forms.field_select_default}}</option>
							{{foreach item="item" from=$list_categories}}
								<option value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}selected{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
						<div class="field-error down"></div>
					</div>
				{{else}}
					{{assign var="category_ids" value=""}}
					{{assign var="category_text" value=""}}
					{{foreach item="category_id" from=$smarty.post.category_ids}}
						{{foreach item="item" from=$list_categories}}
							{{if $category_id==$item.category_id}}
								{{if $category_ids!=''}}
									{{assign var="category_ids" value="`$category_ids`, "}}
									{{assign var="category_text" value="`$category_text`, "}}
								{{/if}}
								{{assign var="category_ids" value="`$category_ids``$item.category_id`"}}
								{{assign var="category_text" value="`$category_text``$item.title`"}}
							{{/if}}
						{{/foreach}}
					{{/foreach}}
					<div class="row list-selector" {{if $is_locked!='true'}}data-name="category_ids" data-selector="{{$lang.urls.categories_selector}}" data-selected="{{$category_ids}}"{{/if}}>
						<label for="edit_channel_categories" class="field-label {{if $require_categories==1}}required{{/if}}">{{$lang.edit_channel.field_categories}}</label>
						<input type="text" id="edit_channel_categories" class="textfield" value="{{$category_text}}" placeholder="{{$lang.edit_channel.field_categories_hint|replace:"%1%":$max_categories}}" readonly/>
						<div class="field-error down"></div>
						{{foreach item="item" from=$smarty.post.category_ids}}
							<input type="hidden" name="category_ids[]" value="{{$item}}"/>
						{{/foreach}}
					</div>
				{{/if}}

				{{assign var="website_field" value=$lang.channels.website_field}}
				{{if $website_field!=''}}
					<div class="row">
						<label for="edit_channel_website" class="field-label">{{$lang.edit_channel.field_website}}</label>
						<input type="text" name="{{$website_field}}" id="edit_channel_website" class="textfield" value="{{$smarty.post.$website_field}}" placeholder="{{$lang.edit_channel.field_website_hint}}" {{if $is_locked=='true'}}readonly{{/if}}/>
						<div class="field-error down"></div>
					</div>
				{{/if}}

				{{if $lang.enable_upload_videos=='true'}}
					<div class="row">
						<div class="button-group">
							<label for="edit_channel_is_video_upload_allowed" class="field-label">{{$lang.edit_channel.field_is_video_upload_allowed}}</label>
							<div class="row">
								<input type="radio" id="edit_channel_is_video_upload_allowed_0" name="is_video_upload_allowed" value="0" class="radio" {{if $smarty.post.is_video_upload_allowed==0}}checked{{/if}} {{if $is_locked=='true'}}disabled{{/if}}>
								<label for="edit_channel_is_video_upload_allowed_0">{{$lang.edit_channel.field_is_video_upload_allowed_values.0}}</label>
								{{if $lang.enable_community=='true'}}
									<input type="radio" id="edit_channel_is_video_upload_allowed_1" name="is_video_upload_allowed" value="1" class="radio" {{if $smarty.post.is_video_upload_allowed==1}}checked{{/if}} {{if $is_locked=='true'}}disabled{{/if}}>
									<label for="edit_channel_is_video_upload_allowed_1">{{$lang.edit_channel.field_is_video_upload_allowed_values.1}}</label>
								{{/if}}
								<input type="radio" id="edit_channel_is_video_upload_allowed_2" name="is_video_upload_allowed" value="2" class="radio" {{if $smarty.post.is_video_upload_allowed==2}}checked{{/if}} {{if $is_locked=='true'}}disabled{{/if}}>
								<label for="edit_channel_is_video_upload_allowed_2">{{$lang.edit_channel.field_is_video_upload_allowed_values.2}}</label>
							</div>
						</div>
						<div class="field-error down"></div>
					</div>
				{{/if}}
				*}}
				<div class="btn__row btn__row--align-right">
					{{if $smarty.post.dvd_id>0}}
						<input type="hidden" name="action" value="change_complete"/>
						<input type="submit" class="btn btn--green btn--middle submit" value="{{$lang.edit_channel.btn_edit_channel}}" {{if $is_locked=='true'}}disabled{{/if}}/>
					{{else}}
						<input type="hidden" name="action" value="add_new_complete"/>
						<input type="submit" class="btn btn--green btn--middle submit" value="{{$lang.edit_channel.btn_create_channel}}"/>
					{{/if}}
				</div>
			</div>
		</form>
	</div>
</div>

{{/if}}