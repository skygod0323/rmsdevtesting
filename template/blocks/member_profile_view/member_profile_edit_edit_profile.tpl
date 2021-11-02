{{if $async_submit_successful=='true'}}
	<div class="success">
		{{$lang.edit_profile.success_message_changed_profile}}
	</div>
{{else}}
	<h2 class="title title_small">{{$lang.edit_profile.title_edit_profile}}</h2>
	<form action="{{$lang.urls.memberzone_my_profile}}" data-form="ajax" method="post">
		<div class="generic-error hidden"></div>

		<div class="form__area__row">
			<label for="edit_profile_display_name" class="form__area__label">{{$lang.edit_profile.field_display_name}} (*):</label>
			<input id="edit_profile_display_name" type="text" name="display_name" class="input" value="{{$smarty.post.display_name}}" {{if $lang.memberzone.truncate_username_to>0}}maxlength="{{$lang.memberzone.truncate_username_to}}"{{/if}} placeholder="{{$lang.edit_profile.field_display_name_hint}}"/>
			<div class="field-error down"></div>
		</div>

		<div class="form__area__row">
			<label for="edit_profile_country_id" class="form__area__label">{{$lang.edit_profile.field_country}} (*):</label>
			<select id="edit_profile_country_id" class="input" name="country_id">
				<option value="">Select...</option>
				{{foreach from=$list_countries item="item" key="key"}}
					<option value="{{$key}}" {{if $smarty.post.country_id==$key}}selected{{/if}}>{{$item}}</option>
				{{/foreach}}
			</select>
			<div class="field-error down"></div>
		</div>

		<div class="form__area__row">
			<label for="edit_profile_charity" class="form__area__label">{{$lang.edit_profile.field_charity}} (*):</label>
			<select id="edit_profile_charity" class="input" name="custom1">
				<option value="">Select...</option>
				{{foreach from=$smarty.const.list_available_charities item="item" key="key"}}
					<option value="{{$key}}" {{if $smarty.post.custom1==$key}}selected{{/if}}>{{$item}}</option>
				{{/foreach}}
			</select>
			<div class="field-error down"></div>
		</div>

		<div class="form__area__row">
			<label for="edit_profile_avatar" class="form__area__label">{{$lang.edit_profile.field_avatar}}: {{$lang.edit_profile.field_avatar_hint|replace:"%1%":$avatar_size}}</label>
			<div class="file_area">
				<input type="text" class="input" placeholder="{{if $smarty.post.avatar==''}}{{$lang.common_forms.file_upload_btn_browse}}{{else}}{{$lang.common_forms.file_upload_btn_change}}{{/if}}" readonly/>
				<input type="file" name="avatar" id="edit_profile_avatar" class="input"/>
				<div class="field-error down"></div>
			</div>
		</div>
		<input type="hidden" name="action" value="change_profile"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		<input type="submit" class="btn btn--green btn--middle" value="{{$lang.edit_profile.btn_change}}"/>
	</form>
{{/if}}