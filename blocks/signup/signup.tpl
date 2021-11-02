{{if $smarty.get.action=='confirm'}}
	<div class="header">Account Confirmation</div>
	<div class="message">
		{{if $activated==1}}
			Thank you! Your account is confirmed. You are now an active member.
		{{else}}
			Sorry, it looks like the link you are using is invalid. Please contact support.
		{{/if}}
	</div>

{{elseif $smarty.get.action=='confirm_restore_pass'}}
	<div class="header">Password Reset Confirmation</div>
	<div class="message">
		{{if $activated==1}}
			Thank you! Your password change was confirmed. You can use your new password.
		{{else}}
			Sorry, it looks like the link you are using is invalid. Please contact support.
		{{/if}}
	</div>

{{elseif $smarty.get.action=='payment_done'}}
	<div class="header">Payment Successful</div>
	<div class="message">
		Thank you! Your payment has been processed successfully.
	</div>

{{elseif $smarty.get.action=='payment_failed'}}
	<div class="header">Payment Failed</div>
	<div class="message">
		Unfortunately our payment processor was unable to accept your payment. Please contact support.
	</div>

{{elseif $async_submit_successful=='true'}}
	<div class="message">
		{{if $smarty.post.action=='restore_password'}}
			Thank you! A new generated password has been sent to your email address.
		{{elseif $smarty.post.action=='resend_confirmation'}}
			Thank you! A message with confirmation link was sent to your email address. Please confirm your registration to activate your account.
		{{elseif $smarty.post.action=='signup'}}
			{{if $smarty.session.user_id<1}}
				Thank you! You are about one step to become an active member.
				A message with confirmation link was sent to your email address. Please confirm your registration to activate your account.
			{{else}}
				{{if $smarty.session.status_id==3}}
					Thank you! You are now a premium member.
				{{else}}
					Thank you! You are now an active member.
				{{/if}}
			{{/if}}
		{{/if}}
	</div>

{{elseif $smarty.get.action=='restore_password'}}
	{{* Password reminder form *}}
	<div class="header">Reset Password</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="restore_password"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="signup_email" class="required">Email</label>
			<input id="signup_email" type="text" name="email"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label>Please confirm that you are a Human.</label>

			{{if $recaptcha_site_key!=''}}
				<div data-name="code">
					<div data-recaptcha-key="{{$recaptcha_site_key}}"></div>
					<div class="field-error"></div>
				</div>
			{{else}}
				<div class="captcha-control">
					<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=signup&amp;rand={{$smarty.now}}" alt="Captcha"/>
					<label for="signup_code" class="required">Security code</label>
					<input id="signup_code" type="text" name="code" autocomplete="off"/>
					<span class="field-error"></span>
				</div>
			{{/if}}
		</div>

		<div class="buttons">
			<input type="submit" value="Reset"/>
		</div>
	</form>

{{elseif $smarty.get.action=='resend_confirmation'}}
	{{* Confirmation re-send form *}}
	<div class="header">Re-send Confirmation Email</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="resend_confirmation"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="signup_email" class="required">Email</label>
			<input id="signup_email" type="text" name="email"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label>Please confirm that you are a Human.</label>

			{{if $recaptcha_site_key!=''}}
				<div data-name="code">
					<div data-recaptcha-key="{{$recaptcha_site_key}}"></div>
					<div class="field-error"></div>
				</div>
			{{else}}
				<div class="captcha-control">
					<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=signup&amp;rand={{$smarty.now}}" alt="Captcha"/>
					<label for="signup_code" class="required">Security code</label>
					<input id="signup_code" type="text" name="code" autocomplete="off"/>
					<span class="field-error"></span>
				</div>
			{{/if}}
		</div>

		<div class="buttons">
			<input type="submit" value="Re-send"/>
		</div>
	</form>

{{else}}
	{{* Signup form *}}
	<div class="header">New User Registration</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="signup"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="sub-header">Account Info</div>

		<div class="row">
			<label for="signup_username" class="required">Username</label>
			<input id="signup_username" type="text" name="username"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_pass" class="required">Password</label>
			<input id="signup_pass" type="password" name="pass"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_pass2" class="required">Password confirmation</label>
			<input id="signup_pass2" type="password" name="pass2"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_email" class="required">Email</label>
			<input id="signup_email" type="text" name="email"/>
			<span class="field-error"></span>
		</div>

		<div class="sub-header">Profile Display</div>

		<div class="row">
			<label for="signup_display_name" class="{{if $require_display_name==1}}required{{/if}}">Display name</label>
			<input id="signup_display_name" type="text" name="display_name"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_avatar" class="{{if $require_avatar==1}}required{{/if}}">Avatar ({{$avatar_size}})</label>
			<input id="signup_avatar" type="file" name="avatar"/>
			<span class="field-error down"></span>
		</div>

		<div class="row">
			<label for="signup_cover" class="{{if $require_cover==1}}required{{/if}}">Cover ({{$cover_size}})</label>
			<input id="signup_cover" type="file" name="cover"/>
			<span class="field-error down"></span>
		</div>

		<div class="sub-header">Additional Info</div>

		<div class="row">
			<label for="signup_birth_date" class="{{if $require_birth_date==1}}required{{/if}}">Birthday</label>
			<fieldset name="birth_date">
				<select name="birth_date_Day" id="signup_birth_date">
					<option value="">Day...</option>
					{{section name="days" start="0" loop="31"}}
						<option value="{{$smarty.section.days.iteration}}">{{$smarty.section.days.iteration}}</option>
					{{/section}}
				</select>
				<select name="birth_date_Month">
					<option value="">Month...</option>
					{{section name="months" start="0" loop="12"}}
						{{assign var="month_names" value=","|explode:"January,February,March,April,May,June,July,August,September,October,November,December"}}
						<option value="{{$smarty.section.months.iteration}}">{{$month_names[$smarty.section.months.index]}}</option>
					{{/section}}
				</select>
				<select name="birth_date_Year">
					{{assign var="last_year" value=$smarty.now|date_format:"%Y"}}
					{{assign var="last_year" value=$last_year+$config.min_user_age+1}}
					<option value="">Year...</option>
					{{section name="years" loop=$last_year max=100 step="-1"}}
						<option value="{{$smarty.section.years.index}}">{{$smarty.section.years.index}}</option>
					{{/section}}
				</select>
				<span class="field-error"></span>
			</fieldset>
		</div>

		<div class="row">
			<label for="signup_country_id" class="{{if $require_country==1}}required{{/if}}">Country</label>
			<select id="signup_country_id" name="country_id">
				<option value="">Select...</option>
				{{foreach from=$list_countries item="item" key="key"}}
					<option value="{{$key}}" {{if $smarty.post.country_id==$key}}selected{{/if}}>{{$item}}</option>
				{{/foreach}}
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_city" class="{{if $require_city==1}}required{{/if}}">City</label>
			<input id="signup_city" type="text" name="city"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_gender_id" class="{{if $require_gender==1}}required{{/if}}">Sex</label>
			<select id="signup_gender_id" name="gender_id">
				<option value="">Select...</option>
				<option value="1">Male</option>
				<option value="2">Female</option>
				<option value="3">Couple</option>
				<option value="4">Transsexual</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_relationship_status_id" class="{{if $require_relationship_status==1}}required{{/if}}">Relationship status</label>
			<select id="signup_relationship_status_id" name="relationship_status_id">
				<option value="">Select...</option>
				<option value="1">Single</option>
				<option value="2">Married</option>
				<option value="3">Open</option>
				<option value="4">Divorced</option>
				<option value="5">Widowed</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_orientation_id" class="{{if $require_orientation==1}}required{{/if}}">Sexual orientation</label>
			<select id="signup_orientation_id" name="orientation_id">
				<option value="">Select...</option>
				<option value="1">I'm not sure</option>
				<option value="2">Straight</option>
				<option value="3">Gay</option>
				<option value="4">Lesbian</option>
				<option value="5">Bisexual</option>
			</select>
			<span class="field-error"></span>
		</div>

		<div class="sub-header">Custom Fields</div>

		<div class="row">
			<label for="signup_custom1" class="{{if $require_custom1==1}}required{{/if}}">Custom 1</label>
			<input id="signup_custom1" type="text" name="custom1"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom2" class="{{if $require_custom2==1}}required{{/if}}">Custom 2</label>
			<input id="signup_custom2" type="text" name="custom2"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom3" class="{{if $require_custom3==1}}required{{/if}}">Custom 3</label>
			<input id="signup_custom3" type="text" name="custom3"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom4" class="{{if $require_custom4==1}}required{{/if}}">Custom 4</label>
			<input id="signup_custom4" type="text" name="custom4"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom5" class="{{if $require_custom5==1}}required{{/if}}">Custom 5</label>
			<input id="signup_custom5" type="text" name="custom5"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom6" class="{{if $require_custom6==1}}required{{/if}}">Custom 6</label>
			<input id="signup_custom6" type="text" name="custom6"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom7" class="{{if $require_custom7==1}}required{{/if}}">Custom 7</label>
			<input id="signup_custom7" type="text" name="custom7"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom8" class="{{if $require_custom8==1}}required{{/if}}">Custom 8</label>
			<input id="signup_custom8" type="text" name="custom8"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom9" class="{{if $require_custom9==1}}required{{/if}}">Custom 9</label>
			<input id="signup_custom9" type="text" name="custom9"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="signup_custom10" class="{{if $require_custom10==1}}required{{/if}}">Custom 10</label>
			<input id="signup_custom10" type="text" name="custom10"/>
			<span class="field-error"></span>
		</div>

		{{if count($card_packages)>0 || count($access_codes)>0 || $require_access_code==1}}
			<div class="sub-header">Paid Access</div>

			{{if count($card_packages)>0}}
				<div class="row">
					<input type="hidden" name="payment_option" value="2"/>
					{{if $disable_free_access!=1}}
						<div data-action="choose">
							<input id="signup_payment_option_1" type="radio" name="payment_option" value="1" {{if $smarty.post.payment_option==1}}checked{{/if}}/>
							<label for="signup_payment_option_1">Free access</label>
						</div>
					{{/if}}
					{{foreach item="item" from=$card_packages}}
						<div data-action="choose">
							<input id="signup_card_package_id_{{$item.package_id}}" type="radio" name="card_package_id" value="{{$item.package_id}}" {{if $smarty.post.payment_option==2 && $item.is_default==1}}checked{{/if}}/>
							<label for="signup_card_package_id_{{$item.package_id}}">{{$item.title}}</label>
						</div>
					{{/foreach}}
					<span data-name="card_package_id" class="field-error"></span>
				</div>
			{{/if}}

			{{if count($access_codes)>0 || $require_access_code==1}}
				<div class="row">
					<label for="signup_access_code" class="{{if $require_access_code==1}}required{{/if}}">Access code</label>
					<input id="signup_access_code" type="text" name="access_code"/>
					<span class="field-error"></span>
				</div>
			{{/if}}
		{{/if}}

		{{if $disable_captcha!=1}}
			<div class="row">
				<label>Please confirm that you are a Human.</label>

				{{if $recaptcha_site_key!=''}}
					<div data-name="code">
						<div data-recaptcha-key="{{$recaptcha_site_key}}"></div>
						<div class="field-error"></div>
					</div>
				{{else}}
					<div class="captcha-control">
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=signup&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="signup_code" class="required">Security code</label>
						<input id="signup_code" type="text" name="code" autocomplete="off"/>
						<span class="field-error"></span>
					</div>
				{{/if}}
			</div>
		{{/if}}

		<div class="buttons">
			<input type="submit" value="Register"/>
		</div>
	</form>

{{/if}}