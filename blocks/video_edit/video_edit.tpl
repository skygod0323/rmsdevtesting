{{if $async_submit_successful=='true'}}
	{{if $smarty.post.action=='change_complete'}}
		<div class="message">Video "{{$async_object_data.title}}" was changed.</div>
	{{else}}
		<div class="message">Video "{{$async_object_data.title}}" was created.</div>
	{{/if}}
{{else}}
	<div class="header">
		{{if $smarty.post.video_id>0}}
			Edit Video "{{$smarty.post.title}}"
		{{else}}
			New Video
		{{/if}}
	</div>

	{{if $smarty.post.video_id==0}}
		<form method="post" enctype="multipart/form-data" data-form="ajax-upload" data-continue-form="video_info_form">
			<input type="hidden" name="action" value="upload_file"/>
			<input type="hidden" name="filename" value=""/>

			<div class="generic-error hidden"></div>

			{{if $allow_url==1}}
				<div class="row">
					<input type="radio" name="upload_option" value="file" id="edit_video_upload_option_file" checked/>
					<label for="edit_video_upload_option_file">Upload file from your local disk</label>
					<div class="field-error"></div>
				</div>
			{{else}}
				<input type="hidden" name="upload_option" value="file"/>
			{{/if}}

			<div class="row">
				<div class="file-control">
					<input type="file" name="content"/>
					<div class="field-error"></div>
				</div>
			</div>

			{{if $allow_url==1}}
				<div class="row">
					<input type="radio" name="upload_option" value="url" id="edit_video_upload_option_url"/>
					<label for="edit_video_upload_option_url">Upload file from Internet URL</label>
					<div class="field-error"></div>
				</div>

				<div class="row">
					<input type="text" name="url" disabled/>
					<div class="field-error"></div>
				</div>
			{{/if}}

			{{if $allow_embed==1}}
				<div class="row">
					<input type="radio" name="upload_option" value="embed" id="edit_video_upload_option_embed"/>
					<label for="edit_video_upload_option_embed">Embed video from other site</label>
					<div class="field-error"></div>
				</div>

				<div class="row">
					<label for="edit_video_embed">Embed code</label>
					<textarea id="edit_video_embed" name="embed" rows="3" disabled></textarea>
					<div class="field-error"></div>
				</div>

				<div class="row">
					<label for="edit_video_duration">Duration</label>
					<input id="edit_video_duration" type="text" name="duration" disabled/>
					<div class="field-error"></div>
				</div>

				<div class="row">
					<div class="file-control">
						<label for="edit_video_screenshot">Screenshot</label>
						<input id="edit_video_screenshot" type="file" name="screenshot" disabled/>
						<div class="field-error"></div>
					</div>
				</div>
			{{/if}}

			<div class="buttons">
				<input type="submit" value="Upload"/>
			</div>
		</form>
	{{/if}}

	<form id="video_info_form" method="post" data-form="ajax" class="{{if $smarty.post.video_id==0}}hidden{{/if}}">
		{{if $smarty.post.video_id>0}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="video_id" value="{{$smarty.post.video_id}}"/>
		{{else}}
			<input type="hidden" name="action" value="add_new_complete"/>
			<input type="hidden" name="file" value=""/>
			<input type="hidden" name="file_hash" value=""/>
		{{/if}}
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error {{if $change_forbidden!=1}}hidden{{/if}}">{{if $change_forbidden==1}}Video editing is disabled.{{/if}}</div>

		<div class="row">
			<label for="video_edit_title" class="required">Title</label>
			<input id="video_edit_title" type="text" name="title" value="{{$smarty.post.title}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="video_edit_description" class="{{if $optional_description!=1}}required{{/if}}">Description</label>
			<textarea id="video_edit_description" name="description" rows="3" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.description}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="video_edit_tags" class="{{if $optional_tags!=1}}required{{/if}}">Tags</label>
			<textarea id="video_edit_tags" name="tags" rows="5" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.tags}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			{{if $max_categories==1}}
				<label for="video_edit_categories" class="{{if $optional_categories!=1}}required{{/if}}">Category</label>
				<select id="video_edit_categories" data-name="category_ids" name="category_ids[]" {{if $change_forbidden==1}}disabled{{/if}}>
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
							<input id="video_edit_categories_{{$item.category_id}}" type="checkbox" name="category_ids[]" value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
							<label for="video_edit_categories_{{$item.category_id}}">{{$item.title}}</label>
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
						<input id="video_edit_models_{{$item.model_id}}" type="checkbox" name="model_ids[]" value="{{$item.model_id}}" {{if is_array($smarty.post.model_ids) && in_array($item.model_id,$smarty.post.model_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
						<label for="video_edit_models_{{$item.model_id}}">{{$item.title}}</label>
					</li>
				{{/foreach}}
			</ul>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="video_edit_screenshot">Main Screenshot</label>
			<input type="file" name="screenshot" id="video_edit_screenshot" {{if $change_forbidden==1 || $change_screenshots_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		{{if $smarty.post.screen_amount>0}}
			{{section name="data" start="0" loop=$smarty.post.screen_amount}}
				<a href="{{$smarty.post.screenshot_sources[$smarty.section.data.index]}}" target="_blank">
					<img alt="Screenshot #{{$smarty.section.data.index+1}}" src="{{$smarty.post.screen_url}}/320x180/{{$smarty.section.data.index+1}}.jpg?rnd={{$smarty.now}}"/>
				</a>
				<input id="main_screenshot{{$smarty.section.data.index+1}}" type="radio" name="main_screenshot" value="{{$smarty.section.data.index+1}}" {{if $smarty.section.data.index+1==$smarty.post.screen_main}}checked{{/if}} {{if $change_forbidden==1 || $change_screenshots_forbidden==1}}disabled{{/if}}>
				<label for="main_screenshot{{$smarty.section.data.index+1}}">Set main</label>
			{{/section}}
		{{/if}}

		<div class="row">
			<label for="video_edit_is_private">Access level</label>
			<select id="video_edit_is_private" name="is_private" {{if $change_forbidden==1}}disabled{{/if}}>
				<option value="0" {{if $smarty.post.is_private==0}}selected{{/if}}>Public</option>
				<option value="1" {{if $smarty.post.is_private==1}}selected{{/if}}>Private</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="video_edit_custom1">Custom 1</label>
			<input id="video_edit_custom1" type="text" name="custom1" value="{{$smarty.post.custom1}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		{{if $allow_tokens==1}}
			<div class="sub-header">Paid Video Purchase</div>

			<div class="row">
				<label for="video_edit_tokens_required">Video cost in tokens</label>
				<input id="video_edit_tokens_required" type="text" name="tokens_required" value="{{$smarty.post.tokens_required}}"/>
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
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=video_edit&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="video_edit_code" class="required">Security code</label>
						<input id="video_edit_code" type="text" name="code" autocomplete="off"/>
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