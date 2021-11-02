{{if $async_submit_successful=='true'}}
	<div class="modal__window__form  modal__window__form--single cfx">
		<div class="success">
			{{$lang.reset_password.success_message}}
		</div>
	</div>
{{else}}
	<div id="modal-forgot" class="modal popup-holder">
		<div class="modal__window">
			<h2 class="title title__modal">{{$lang.reset_password.title}}</h2>

			<form action="{{$lang.urls.reset_password}}" data-form="ajax" method="post">
				<div class="generic-error hidden"></div>

				<div class="modal__window__form  modal__window__form--single cfx">
					<div class="modal__window__row">
						<label for="reset_password_email" class="modal__window__label required">{{$lang.reset_password.field_email}} (*):</label>
						<div class="relative">
							<input id="reset_password_email" type="text" name="email" class="input" placeholder="{{$lang.reset_password.field_email_hint}}"/>
							<div class="field-error down"></div>
						</div>
					</div>

					{{if $disable_captcha!=1}}
						<div class="modal__window__row captcha-control">
							<h6 class="title title_tiny">{{$lang.common_forms.field_captcha_hint}}</h6>
							{{if $recaptcha_site_key!=''}}
								<div class="image relative" data-name="code">
									<div data-recaptcha-key="{{$recaptcha_site_key}}" data-recaptcha-theme="{{if $lang.theme.style=='dark'}}dark{{else}}light{{/if}}"></div>
									<div class="field-error up"></div>
								</div>
							{{else}}
								<div class="image">
									<img src="{{$lang.urls.captcha|replace:"%ID%":"signup"}}?rand={{$smarty.now}}" alt="{{$lang.common_forms.field_captcha_image}}"/>
									<div class="relative">
										<input type="text" name="code" id="signup_code" class="input" autocomplete="off" placeholder="{{$lang.common_forms.field_captcha}}"/>
										<div class="field-error up"></div>
									</div>
								</div>
							{{/if}}
						</div>
					{{/if}}

					<div class="btn__row">
						<input type="hidden" name="action" value="restore_password"/>
						<input type="hidden" name="email_link" value="{{$lang.urls.email_action}}"/>
						<button type="submit" class="btn btn--green btn--bigger">{{$lang.reset_password.btn_reset_password}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
{{/if}}