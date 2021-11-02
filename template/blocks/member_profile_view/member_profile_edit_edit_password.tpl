{{if $async_submit_successful=='true'}}
	<div class="success">
		{{$lang.edit_profile.success_message_changed_password}}
	</div>
{{else}}
	<h2 class="title title_small">{{$lang.edit_profile.title_edit_password}}</h2>
	<form action="{{$lang.urls.memberzone_my_profile}}" data-form="ajax" method="post">
		<div class="generic-error hidden"></div>

		<div class="form__area__row">
			<label for="edit_profile_old_pass" class="form__area__label">{{$lang.edit_profile.field_old_password}} (*):</label>
			<input id="edit_profile_old_pass" type="password" name="old_pass" class="input"/>
			<div class="field-error down"></div>
		</div>
		<div class="form__area__row">
			<label for="edit_profile_pass" class="form__area__label">{{$lang.edit_profile.field_password}} (*):</label>
			<input id="edit_profile_pass" type="password" name="pass" class="input" placeholder="{{$lang.edit_profile.field_password_hint}}"/>
			<div class="field-error down"></div>
		</div>
		<div class="form__area__row">
			<label for="edit_profile_pass2" class="form__area__label">{{$lang.edit_profile.field_password2}} (*):</label>
			<input id="edit_profile_pass2" type="password" name="pass2" class="input" placeholder="{{$lang.edit_profile.field_password2_hint}}"/>
			<div class="field-error down"></div>
		</div>
		<input type="hidden" name="action" value="change_pass"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		<input type="submit" class="btn btn--green btn--middle" value="{{$lang.edit_profile.btn_change}}"/>
	</form>
{{/if}}