{{if $async_submit_successful=='true'}}
	{{if $smarty.post.action=='change_complete'}}
		<div class="message">Channel "{{$async_object_data.title}}" was changed.</div>
	{{else}}
		<div class="message">Channel "{{$async_object_data.title}}" was created.</div>
	{{/if}}
{{else}}
	<div class="header">
		{{if $smarty.post.dvd_id>0}}
			Edit Channel "{{$smarty.post.title}}"
		{{else}}
			New Channel
		{{/if}}
	</div>

	<form method="post" data-form="ajax">
		{{if $smarty.post.dvd_id>0}}
			<input type="hidden" name="action" value="change_complete"/>
		{{else}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{/if}}
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error {{if $change_forbidden!=1}}hidden{{/if}}">{{if $change_forbidden==1}}Channel editing is disabled.{{/if}}</div>

		<div class="row">
			<label for="dvd_edit_title" class="required">Title</label>
			<input id="dvd_edit_title" type="text" name="title" value="{{$smarty.post.title}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_description" class="{{if $require_description==1}}required{{/if}}">Description</label>
			<textarea id="dvd_edit_description" name="description" rows="3" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.description}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_cover1_front" class="{{if $require_cover1_front==1}}required{{/if}}">Screenshot 1 image 1</label>
			<input type="file" name="cover1_front" id="dvd_edit_cover1_front" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_cover1_back" class="{{if $require_cover1_back==1}}required{{/if}}">Screenshot 1 image 2</label>
			<input type="file" name="cover1_back" id="dvd_edit_cover1_back" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_cover2_front" class="{{if $require_cover2_front==1}}required{{/if}}">Screenshot 2 image 1</label>
			<input type="file" name="cover2_front" id="dvd_edit_cover2_front" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_cover2_back" class="{{if $require_cover2_back==1}}required{{/if}}">Screenshot 2 image 2</label>
			<input type="file" name="cover2_back" id="dvd_edit_cover2_back" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_tags" class="{{if $require_tags==1}}required{{/if}}">Tags</label>
			<textarea id="dvd_edit_tags" name="tags" rows="5" {{if $change_forbidden==1}}disabled{{/if}}>{{$smarty.post.tags}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			{{if $max_categories==1}}
				<label for="dvd_edit_categories" class="{{if $require_categories==1}}required{{/if}}">Category</label>
				<select id="dvd_edit_categories" data-name="category_ids" name="category_ids[]" {{if $change_forbidden==1}}disabled{{/if}}>
					<option value="">Select...</option>
					{{foreach item="item" from=$list_categories}}
						<option value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}selected{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			{{else}}
				<label class="{{if $require_categories==1}}required{{/if}}">Categories</label>
				<ul data-name="category_ids">
					{{foreach item="item" from=$list_categories}}
						<li>
							<input id="dvd_edit_categories_{{$item.category_id}}" type="checkbox" name="category_ids[]" value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
							<label for="dvd_edit_categories_{{$item.category_id}}">{{$item.title}}</label>
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
						<input id="dvd_edit_models_{{$item.model_id}}" type="checkbox" name="model_ids[]" value="{{$item.model_id}}" {{if is_array($smarty.post.model_ids) && in_array($item.model_id,$smarty.post.model_ids)}}checked{{/if}} {{if $change_forbidden==1}}disabled{{/if}}/>
						<label for="dvd_edit_models_{{$item.model_id}}">{{$item.title}}</label>
					</li>
				{{/foreach}}
			</ul>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="dvd_edit_custom1">Custom 1</label>
			<input id="dvd_edit_custom1" type="text" name="custom1" value="{{$smarty.post.custom1}}" {{if $change_forbidden==1}}disabled{{/if}}/>
			<span class="field-error"></span>
		</div>

		{{if $allow_tokens==1}}
			<div class="sub-header">Paid Channel Subscriptions</div>

			<div class="row">
				<label for="dvd_edit_tokens_required">Subscription cost in tokens</label>
				<input id="dvd_edit_tokens_required" type="text" name="tokens_required" value="{{$smarty.post.tokens_required}}"/>
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
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=dvd_edit&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="dvd_edit_code" class="required">Security code</label>
						<input id="dvd_edit_code" type="text" name="code" autocomplete="off"/>
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