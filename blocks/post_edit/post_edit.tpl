{{if $async_submit_successful=='true'}}
	{{if $smarty.post.action=='change_complete'}}
		<div class="message">Post "{{$async_object_data.title}}" was changed.</div>
	{{else}}
		<div class="message">Post "{{$async_object_data.title}}" was created.</div>
	{{/if}}
{{else}}
	<div class="header">
		{{if $smarty.post.post_id>0}}
			Edit Post "{{$smarty.post.title}}"
		{{else}}
			New Post
		{{/if}}
	</div>

	<form method="post" data-form="ajax">
		{{if $smarty.post.post_id>0}}
			<input type="hidden" name="action" value="change_complete"/>
		{{else}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{/if}}
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error {{if $change_forbidden!=1}}hidden{{/if}}">{{if $change_forbidden==1}}Post editing is disabled.{{/if}}</div>

		<div class="row">
			<label for="post_edit_title" class="required">Title</label>
			<input id="post_edit_title" type="text" name="title" value="{{$smarty.post.title}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="post_edit_description" class="{{if $optional_description!=1}}required{{/if}}">Description</label>
			<textarea id="post_edit_description" name="description" rows="3" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.description}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="post_edit_content" class="required">Content</label>
			<textarea id="post_edit_content" name="content" rows="5" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.content}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="post_edit_tags" class="{{if $optional_tags!=1}}required{{/if}}">Tags</label>
			<textarea id="post_edit_tags" name="tags" rows="5" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.tags}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			{{if $max_categories==1}}
				<label for="post_edit_categories" class="{{if $optional_categories!=1}}required{{/if}}">Category</label>
				<select id="post_edit_categories" data-name="category_ids" name="category_ids[]" {{if $change_forbidden==1}}disabled{{/if}}>
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
							<input id="post_edit_categories_{{$item.category_id}}" type="checkbox" name="category_ids[]" value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
							<label for="post_edit_categories_{{$item.category_id}}">{{$item.title}}</label>
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
						<input id="post_edit_models_{{$item.model_id}}" type="checkbox" name="model_ids[]" value="{{$item.model_id}}" {{if is_array($smarty.post.model_ids) && in_array($item.model_id,$smarty.post.model_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
						<label for="post_edit_models_{{$item.model_id}}">{{$item.title}}</label>
					</li>
				{{/foreach}}
			</ul>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="post_edit_custom1">Custom 1</label>
			<input id="post_edit_custom1" type="text" name="custom1" value="{{$smarty.post.custom1}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

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
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=post_edit&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="post_edit_code" class="required">Security code</label>
						<input id="post_edit_code" type="text" name="code" autocomplete="off"/>
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