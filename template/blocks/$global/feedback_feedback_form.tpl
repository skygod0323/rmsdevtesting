{{if $async_submit_successful=='true'}}
	<div class="modal__window__form  modal__window__form--single cfx">
		<div class="success">
			{{$lang.feedback.success_message}}
		</div>
	</div>
{{else}}
	<div id="modal-support" class="modal popup-holder">
		<div class="modal__window">
			<h2 class="title title__modal">{{$lang.feedback.title}}</h2>

			<form action="{{$lang.urls.feedback}}" data-form="ajax" method="post">
				<div class="generic-error hidden"></div>

				<div class="modal__window__form  modal__window__form--single cfx">
					<div class="modal__window__row">
						<label for="support_message" class="modal__window__label required">{{$lang.feedback.field_message}} (*):</label>
						<div class="relative">
							<textarea name="message" id="support_message" class="input" rows="3" placeholder="{{$lang.feedback.field_message_hint}}"></textarea>
							<div class="field-error down"></div>
						</div>
					</div>

					<div class="modal__window__row">
						<label for="support_email" class="modal__window__label {{if $require_email=='1'}}required{{/if}}">{{$lang.feedback.field_email}}{{if $require_email=='1'}} (*){{/if}}:</label>
						<div class="relative">
							<input type="text" name="email" id="support_email" class="input" placeholder="{{if $require_email=='1'}}{{$lang.feedback.field_email_hint_required}}{{else}}{{$lang.feedback.field_email_hint_optional}}{{/if}}"/>
							<div class="field-error down"></div>
						</div>
					</div>

					{{if $use_custom1=='1'}}
						<div class="modal__window__row">
							<label for="support_custom1" class="modal__window__label required">{{$lang.feedback.field_custom1}} (*):</label>
							<div class="relative">
								<input type="text" name="custom1" id="support_custom1" class="input" placeholder="{{$lang.feedback.field_custom1_hint}}"/>
								<div class="field-error down"></div>
							</div>
						</div>
					{{/if}}

					{{if $use_custom2=='1'}}
						<div class="modal__window__row">
							<label for="support_custom2" class="modal__window__label required">{{$lang.feedback.field_custom2}} (*):</label>
							<div class="relative">
								<input type="text" name="custom2" id="support_custom2" class="input" placeholder="{{$lang.feedback.field_custom2_hint}}"/>
								<div class="field-error down"></div>
							</div>
						</div>
					{{/if}}

					{{if $use_custom3=='1'}}
						<div class="modal__window__row">
							<label for="support_custom3" class="modal__window__label required">{{$lang.feedback.field_custom3}} (*):</label>
							<div class="relative">
								<input type="text" name="custom3" id="support_custom3" class="input" placeholder="{{$lang.feedback.field_custom3_hint}}"/>
								<div class="field-error down"></div>
							</div>
						</div>
					{{/if}}

					{{if $use_custom4=='1'}}
						<div class="modal__window__row">
							<label for="support_custom4" class="modal__window__label required">{{$lang.feedback.field_custom4}} (*):</label>
							<div class="relative">
								<input type="text" name="custom4" id="support_custom4" class="input" placeholder="{{$lang.feedback.field_custom4_hint}}"/>
								<div class="field-error down"></div>
							</div>
						</div>
					{{/if}}

					{{if $use_custom5=='1'}}
						<div class="modal__window__row">
							<label for="support_custom5" class="modal__window__label required">{{$lang.feedback.field_custom5}} (*):</label>
							<div class="relative">
								<input type="text" name="custom5" id="support_custom5" class="input" placeholder="{{$lang.feedback.field_custom5_hint}}"/>
								<div class="field-error down"></div>
							</div>
						</div>
					{{/if}}

					{{if $use_captcha=='1'}}
						<div class="modal__window__row captcha-control">
							<h6 class="title title_tiny">{{$lang.common_forms.field_captcha_hint}}</h6>
							{{if $recaptcha_site_key!=''}}
								<div class="image relative" data-name="code">
									<div data-recaptcha-key="{{$recaptcha_site_key}}" data-recaptcha-theme="{{if $lang.theme.style=='dark'}}dark{{else}}light{{/if}}"></div>
									<div class="field-error up"></div>
								</div>
							{{else}}
								<div class="image">
									<img src="{{$lang.urls.captcha|replace:"%ID%":"feedback"}}?rand={{$smarty.now}}" alt="{{$lang.common_forms.field_captcha_image}}"/>
									<div class="relative">
										<input type="text" name="code" id="support_code" class="input" autocomplete="off" placeholder="{{$lang.common_forms.field_captcha}}"/>
										<div class="field-error up"></div>
									</div>
								</div>
							{{/if}}
						</div>
					{{/if}}

					<div class="btn__row">
						<input type="hidden" name="action" value="send"/>
						<button type="submit" class="btn btn--green btn--bigger">{{$lang.feedback.btn_send}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
{{/if}}