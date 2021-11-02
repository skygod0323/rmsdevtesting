{{if $async_submit_successful=='true'}}
	<div class="success">
		{{if $send_email==1}}
			{{$lang.edit_profile.success_message_changed_email_confirm}}
		{{else}}
			{{$lang.edit_profile.success_message_changed_email}}
		{{/if}}
	</div>
{{else}}
	<h2 class="title title_small">{{$lang.edit_profile.title_edit_email}}</h2>
	<form action="{{$lang.urls.memberzone_my_profile}}" data-form="ajax" method="post">
		<div class="generic-error hidden"></div>

		<div class="form__area__row">
			<label for="edit_profile_old_email" class="form__area__label">{{$lang.edit_profile.field_old_email}} (*):</label>
			<input id="edit_profile_old_email" type="text" class="input" value="{{$smarty.post.email}}" readonly/>
			<div class="field-error down"></div>
		</div>
		<div class="form__area__row">
			<label for="edit_profile_email" class="form__area__label">{{$lang.edit_profile.field_email}} (*):</label>
			<input id="edit_profile_email" type="text" name="email" class="input"/>
			<div class="field-error down"></div>
		</div>
		<input type="hidden" name="action" value="change_email"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		<input type="hidden" name="email_link" value="{{$lang.urls.email_action}}"/>
		<input type="submit" class="btn btn--green btn--middle" value="{{$lang.edit_profile.btn_change}}"/>
	</form>
{{/if}}