{{if $smarty.get.action=='confirm'}}
	<div class="header">Change Account Email Confirmation</div>
	<div class="message">
		{{if $activated==1}}
			Thank you! Your email has been changed.
		{{else}}
			Sorry, it looks like the link you are using is invalid. Please contact support.
		{{/if}}
	</div>
{{elseif $async_submit_successful=='true'}}
	<div class="message">
		{{if $smarty.post.action=='change_pass'}}
			Your password has been changed.
		{{elseif $smarty.post.action=='change_email'}}
			{{if $send_email==1}}
				A message with confirmation link was sent to your email address. Please confirm your email change.
			{{else}}
				Your email has been changed.
			{{/if}}
		{{elseif $smarty.post.action=='change_profile'}}
			Your personal info has been changed.
		{{/if}}
	</div>

{{elseif $smarty.get.action=='change_pass'}}
	{{* Password change form *}}
	<div class="header">Change Account Password</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="change_pass"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="edit_profile_old_pass" class="required">Current password</label>
			<input id="edit_profile_old_pass" type="password" name="old_pass"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_pass" class="required">New password</label>
			<input id="edit_profile_pass" type="password" name="pass"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_pass2" class="required">New password confirmation</label>
			<input id="edit_profile_pass2" type="password" name="pass2"/>
			<span class="field-error"></span>
		</div>

		<div class="buttons">
			<input type="submit" value="Change"/>
		</div>
	</form>

{{elseif $smarty.get.action=='change_email'}}
	{{* Email change form *}}
	<div class="header">Change Account Email</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="change_email"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="edit_profile_old_email" class="required">Current email</label>
			<input id="edit_profile_old_email" type="text" value="{{$old_email}}" readonly/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_email" class="required">New email</label>
			<input id="edit_profile_email" type="text" name="email"/>
			<span class="field-error"></span>
		</div>

		<div class="buttons">
			<input type="submit" value="Change"/>
		</div>
	</form>
{{else}}
	{{* Profile editing form *}}
	<div class="header">Change Personal Info</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="change_profile"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="sub-header">Profile Display</div>

		<div class="row">
			<label for="edit_profile_display_name" class="required">Display name</label>
			<input id="edit_profile_display_name" type="text" name="display_name" value="{{$smarty.post.display_name}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_avatar" class="{{if $require_avatar==1}}required{{/if}}">Avatar ({{$avatar_size}})</label>
			<input id="edit_profile_avatar" type="file" name="avatar"/>
			{{if $smarty.post.avatar}}<input id="edit_profile_avatar_delete" type="checkbox" name="avatar_delete" value="1"/> <label for="edit_profile_avatar_delete">delete</label>{{/if}}
			<span class="field-error down"></span>
		</div>

		<div class="row">
			<label for="edit_profile_cover" class="{{if $require_cover==1}}required{{/if}}">Cover ({{$cover_size}})</label>
			<input id="edit_profile_cover" type="file" name="cover"/>
			{{if $smarty.post.cover}}<input id="edit_profile_cover_delete" type="checkbox" name="cover_delete" value="1"/> <label for="edit_profile_cover_delete">delete</label>{{/if}}
			<span class="field-error down"></span>
		</div>

		{{if $allow_tokens==1}}
			<div class="sub-header">Paid Profile Subscriptions</div>

			<div class="row">
				<label for="edit_profile_tokens_required">Subscription cost in tokens</label>
				<input id="edit_profile_tokens_required" type="text" name="tokens_required" value="{{$smarty.post.tokens_required}}"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		<div class="sub-header">Additional Info</div>

		<div class="row">
			<label for="edit_profile_birth_date" class="{{if $require_birth_date==1}}required{{/if}}">Birthday</label>
			<fieldset name="birth_date">
				{{if $smarty.post.birth_date!='0000-00-00'}}
					{{assign var="birth_date_Day" value=$smarty.post.birth_date|date_format:"%e"|intval}}
					{{assign var="birth_date_Month" value=$smarty.post.birth_date|date_format:"%m"|intval}}
					{{assign var="birth_date_Year" value=$smarty.post.birth_date|date_format:"%Y"|intval}}
				{{/if}}

				<select name="birth_date_Day" id="edit_profile_birth_date">
					<option value="">Day...</option>
					{{section name="days" start="0" loop="31"}}
						<option value="{{$smarty.section.days.iteration}}" {{if $smarty.section.days.iteration==$birth_date_Day}}selected{{/if}}>{{$smarty.section.days.iteration}}</option>
					{{/section}}
				</select>
				<select name="birth_date_Month">
					<option value="">Month...</option>
					{{section name="months" start="0" loop="12"}}
						{{assign var="month_names" value=","|explode:"January,February,March,April,May,June,July,August,September,October,November,December"}}
						<option value="{{$smarty.section.months.iteration}}" {{if $smarty.section.months.iteration==$birth_date_Month}}selected{{/if}}>{{$month_names[$smarty.section.months.index]}}</option>
					{{/section}}
				</select>
				<select name="birth_date_Year">
					{{assign var="last_year" value=$smarty.now|date_format:"%Y"}}
					{{assign var="last_year" value=$last_year+$config.min_user_age+1}}
					<option value="">Year...</option>
					{{section name="years" loop=$last_year max=100 step="-1"}}
						<option value="{{$smarty.section.years.index}}" {{if $smarty.section.years.index==$birth_date_Year}}selected{{/if}}>{{$smarty.section.years.index}}</option>
					{{/section}}
				</select>
				<span class="field-error"></span>
			</fieldset>
		</div>

		<div class="row">
			<label for="edit_profile_country_id" class="{{if $require_country==1}}required{{/if}}">Country</label>
			<select id="edit_profile_country_id" name="country_id">
				<option value="">Select...</option>
				{{foreach from=$list_countries item="item" key="key"}}
					<option value="{{$key}}" {{if $smarty.post.country_id==$key}}selected{{/if}}>{{$item}}</option>
				{{/foreach}}
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_city" class="{{if $require_city==1}}required{{/if}}">City</label>
			<input id="edit_profile_city" type="text" name="city" value="{{$smarty.post.city}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_gender_id" class="{{if $require_gender==1}}required{{/if}}">Sex</label>
			<select id="edit_profile_gender_id" name="gender_id">
				<option value="">Select...</option>
				<option value="1" {{if $smarty.post.gender_id==1}}selected{{/if}}>Male</option>
				<option value="2" {{if $smarty.post.gender_id==2}}selected{{/if}}>Female</option>
				<option value="3" {{if $smarty.post.gender_id==3}}selected{{/if}}>Couple</option>
				<option value="4" {{if $smarty.post.gender_id==4}}selected{{/if}}>Transsexual</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_relationship_status_id" class="{{if $require_relationship_status==1}}required{{/if}}">Relationship status</label>
			<select id="edit_profile_relationship_status_id" name="relationship_status_id">
				<option value="">Select...</option>
				<option value="1" {{if $smarty.post.relationship_status_id==1}}selected{{/if}}>Single</option>
				<option value="2" {{if $smarty.post.relationship_status_id==2}}selected{{/if}}>Married</option>
				<option value="3" {{if $smarty.post.relationship_status_id==3}}selected{{/if}}>Open</option>
				<option value="4" {{if $smarty.post.relationship_status_id==4}}selected{{/if}}>Divorced</option>
				<option value="5" {{if $smarty.post.relationship_status_id==5}}selected{{/if}}>Widowed</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_orientation_id" class="{{if $require_orientation==1}}required{{/if}}">Sexual orientation</label>
			<select id="edit_profile_orientation_id" name="orientation_id">
				<option value="">Select...</option>
				<option value="1" {{if $smarty.post.orientation_id==1}}selected{{/if}}>I'm not sure</option>
				<option value="2" {{if $smarty.post.orientation_id==2}}selected{{/if}}>Straight</option>
				<option value="3" {{if $smarty.post.orientation_id==3}}selected{{/if}}>Gay</option>
				<option value="4" {{if $smarty.post.orientation_id==4}}selected{{/if}}>Lesbian</option>
				<option value="5" {{if $smarty.post.orientation_id==5}}selected{{/if}}>Bisexual</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_status_message">Status message</label>
			<input id="edit_profile_status_message" type="text" name="status_message" value="{{$smarty.post.status_message}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_website">Website</label>
			<input id="edit_profile_website" type="text" name="website" value="{{$smarty.post.website}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_education">Education</label>
			<input id="edit_profile_education" type="text" name="education" value="{{$smarty.post.education}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_occupation">Occupation</label>
			<input id="edit_profile_occupation" type="text" name="occupation" value="{{$smarty.post.occupation}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_interests">Interests</label>
			<input id="edit_profile_interests" type="text" name="interests" value="{{$smarty.post.interests}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_favourite_movies">Favourite movies</label>
			<input id="edit_profile_favourite_movies" type="text" name="favourite_movies" value="{{$smarty.post.favourite_movies}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_favourite_music">Favourite music</label>
			<input id="edit_profile_favourite_music" type="text" name="favourite_music" value="{{$smarty.post.favourite_music}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_favourite_books">Favourite books</label>
			<input id="edit_profile_favourite_books" type="text" name="favourite_books" value="{{$smarty.post.favourite_books}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_about_me">About me</label>
			<textarea id="edit_profile_about_me" name="about_me" rows="3">{{$smarty.post.about_me}}</textarea>
			<span class="field-error"></span>
		</div>

		<div class="sub-header">Custom Fields</div>

		<div class="row">
			<label for="edit_profile_custom1" class="{{if $require_custom1==1}}required{{/if}}">Custom 1</label>
			<input id="edit_profile_custom1" type="text" name="custom1" value="{{$smarty.post.custom1}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom2" class="{{if $require_custom2==1}}required{{/if}}">Custom 2</label>
			<input id="edit_profile_custom2" type="text" name="custom2" value="{{$smarty.post.custom2}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom3" class="{{if $require_custom3==1}}required{{/if}}">Custom 3</label>
			<input id="edit_profile_custom3" type="text" name="custom3" value="{{$smarty.post.custom3}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom4" class="{{if $require_custom4==1}}required{{/if}}">Custom 4</label>
			<input id="edit_profile_custom4" type="text" name="custom4" value="{{$smarty.post.custom4}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom5" class="{{if $require_custom5==1}}required{{/if}}">Custom 5</label>
			<input id="edit_profile_custom5" type="text" name="custom5" value="{{$smarty.post.custom5}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom6" class="{{if $require_custom6==1}}required{{/if}}">Custom 6</label>
			<input id="edit_profile_custom6" type="text" name="custom6" value="{{$smarty.post.custom6}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom7" class="{{if $require_custom7==1}}required{{/if}}">Custom 7</label>
			<input id="edit_profile_custom7" type="text" name="custom7" value="{{$smarty.post.custom7}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom8" class="{{if $require_custom8==1}}required{{/if}}">Custom 8</label>
			<input id="edit_profile_custom8" type="text" name="custom8" value="{{$smarty.post.custom8}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom9" class="{{if $require_custom9==1}}required{{/if}}">Custom 9</label>
			<input id="edit_profile_custom9" type="text" name="custom9" value="{{$smarty.post.custom9}}"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="edit_profile_custom10" class="{{if $require_custom10==1}}required{{/if}}">Custom 10</label>
			<input id="edit_profile_custom10" type="text" name="custom10" value="{{$smarty.post.custom10}}"/>
			<span class="field-error"></span>
		</div>

		<div class="buttons">
			<input type="submit" value="Change"/>
		</div>
	</form>

{{/if}}