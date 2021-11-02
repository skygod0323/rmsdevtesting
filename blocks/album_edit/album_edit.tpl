{{if $async_submit_successful=='true'}}
	{{if $smarty.post.action=='change_complete'}}
		<div class="message">Album "{{$async_object_data.title}}" was changed.</div>
	{{else}}
		<div class="message">Album "{{$async_object_data.title}}" was created.</div>
	{{/if}}
{{else}}
	<div class="header">
		{{if $smarty.post.album_id>0}}
			Edit Album "{{$smarty.post.title}}"
		{{else}}
			New Album
		{{/if}}
	</div>

	{{if $smarty.post.album_id==0}}
		<form method="post" enctype="multipart/form-data" data-form="ajax-upload" data-continue-form="album_info_form">
			<input type="hidden" name="action" value="upload_files"/>
			<input type="hidden" name="filename" value=""/>

			<div class="generic-error hidden"></div>

			<div class="row">
				<label class="required">Upload files from your local disk</label>
				<div class="file-control" data-name="content">
					<input type="file" name="content[]" multiple/>
					<div class="field-error"></div>
				</div>
			</div>

			<div class="buttons">
				<input type="submit" value="Upload"/>
			</div>
		</form>
	{{/if}}

	<form id="album_info_form" method="post" data-form="ajax" class="{{if $smarty.post.album_id==0}}hidden{{/if}}">
		{{if $smarty.post.album_id>0}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="album_id" value="{{$smarty.post.album_id}}"/>
		{{else}}
			<input type="hidden" name="action" value="add_new_complete"/>
			<input type="hidden" name="files" value=""/>
		{{/if}}
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error {{if $change_forbidden!=1}}hidden{{/if}}">{{if $change_forbidden==1}}Album editing is disabled.{{/if}}</div>

		<div class="row">
			<label for="album_edit_title" class="required">Title</label>
			<input id="album_edit_title" type="text" name="title" value="{{$smarty.post.title}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="album_edit_description" class="{{if $optional_description!=1}}required{{/if}}">Description</label>
			<textarea id="album_edit_description" name="description" rows="3" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.description}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="album_edit_tags" class="{{if $optional_tags!=1}}required{{/if}}">Tags</label>
			<textarea id="album_edit_tags" name="tags" rows="5" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.tags}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			{{if $max_categories==1}}
				<label for="album_edit_categories" class="{{if $optional_categories!=1}}required{{/if}}">Category</label>
				<select id="album_edit_categories" data-name="category_ids" name="category_ids[]" {{if $change_forbidden==1}}disabled{{/if}}>
					<option value="">Select...</option>
					{{foreach item="item" from=$list_categories}}
						<option value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}selected{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			{{else}}
				<label class="{{if $optional_categories!=1}}required{{/if}}">Categories</label>
				<ul data-name="category_ids">
					{{foreach item="item" from=$list_categories}}
						<li>
							<input id="album_edit_categories_{{$item.category_id}}" type="checkbox" name="category_ids[]" value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
							<label for="album_edit_categories_{{$item.category_id}}">{{$item.title}}</label>
						</li>
					{{/foreach}}
				</ul>
			{{/if}}
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label>Models</label>
			<ul data-name="model_ids">
				{{foreach item="item" from=$list_models}}
					<li>
						<input id="album_edit_models_{{$item.model_id}}" type="checkbox" name="model_ids[]" value="{{$item.model_id}}" {{if is_array($smarty.post.model_ids) && in_array($item.model_id,$smarty.post.model_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
						<label for="album_edit_models_{{$item.model_id}}">{{$item.title}}</label>
					</li>
				{{/foreach}}
			</ul>
			<span class="field-error"></span>
		</div>

		{{if count($smarty.post.images)>0}}
			<div class="row">
				{{foreach item="item" from=$smarty.post.images}}
					<a href="{{$item.formats[$lang.albums.image_big_size].protected_url}}" target="_blank">
						<img alt="{{$item.title}}" src="{{$item.formats.200x150.direct_url}}?rnd={{$smarty.now}}"/>
					</a>
					<input id="delete{{$item.image_id}}" type="checkbox" name="delete_image_ids[]" value="{{$item.image_id}}" {{if $change_forbidden==1 || $change_images_forbidden==1 || $item.image_id==$smarty.post.main_photo_id}}disabled{{/if}}>
					<label for="delete{{$item.image_id}}">Delete</label>
					<input id="main_photo_id{{$item.image_id}}" type="radio" name="main_photo_id" value="{{$item.image_id}}" {{if $item.image_id==$smarty.post.main_photo_id}}checked{{/if}} {{if $change_forbidden==1 || $change_images_forbidden==1}}disabled{{/if}}>
					<label for="main_photo_id{{$item.image_id}}">Set main</label>
				{{/foreach}}
			</div>

			<div class="row">
				<label>Upload more images</label>
				<div class="file-control" data-name="content">
					<input type="file" name="content[]" multiple/>
					<div class="field-error"></div>
				</div>
			</div>
		{{/if}}

		<div class="row">
			<label for="album_edit_is_private">Access level</label>
			<select id="album_edit_is_private" name="is_private" {{if $change_forbidden==1}}disabled{{/if}}>
				<option value="0" {{if $smarty.post.is_private==0}}selected{{/if}}>Public</option>
				<option value="1" {{if $smarty.post.is_private==1}}selected{{/if}}>Private</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="album_edit_custom1">Custom 1</label>
			<input id="album_edit_custom1" type="text" name="custom1" value="{{$smarty.post.custom1}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		{{if $allow_tokens==1}}
			<div class="sub-header">Paid Album Purchase</div>

			<div class="row">
				<label for="album_edit_tokens_required">Album cost in tokens</label>
				<input id="album_edit_tokens_required" type="text" name="tokens_required" value="{{$smarty.post.tokens_required}}"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		{{if $use_captcha==1}}
			<div class="row">
				<label>Please confirm that you are a Human.</label>

				{{if $recaptcha_site_key!=''}}
					<div data-name="code">
						<div data-recaptcha-key="{{$recaptcha_site_key}}"></div>
						<div class="field-error"></div>
					</div>
				{{else}}
					<div class="captcha-control">
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=album_edit&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="album_edit_code" class="required">Security code</label>
						<input id="album_edit_code" type="text" name="code" autocomplete="off"/>
						<span class="field-error"></span>
					</div>
				{{/if}}
			</div>
		{{/if}}

		<div class="buttons">
			<input type="submit" value="Save" {{if $change_forbidden==1}}disabled{{/if}}/>
		</div>
	</form>
{{/if}}