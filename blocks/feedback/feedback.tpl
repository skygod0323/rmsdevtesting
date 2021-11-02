{{if $async_submit_successful=='true'}}
	<div class="message">Thank you! We appreciate your feedback and will respond soon.</div>
{{else}}
	<div class="header">Feedback Form</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="send"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="feedback_subject" class="{{if $require_subject==1}}required{{/if}}">Subject</label>
			<input id="feedback_subject" type="text" name="subject"/>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<label for="feedback_email" class="{{if $require_email==1}}required{{/if}}">E-mail</label>
			<input id="feedback_email" type="text" name="email"/>
			<span class="field-error"></span>
		</div>

		{{if $use_custom1==1}}
			<div class="row">
				<label for="feedback_custom1" class="required">Custom1</label>
				<input id="feedback_custom1" type="text" name="custom1"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		{{if $use_custom2==1}}
			<div class="row">
				<label for="feedback_custom2" class="required">Custom2</label>
				<input id="feedback_custom2" type="text" name="custom2"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		{{if $use_custom3==1}}
			<div class="row">
				<label for="feedback_custom3" class="required">Custom3</label>
				<input id="feedback_custom3" type="text" name="custom3"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		{{if $use_custom4==1}}
			<div class="row">
				<label for="feedback_custom4" class="required">Custom4</label>
				<input id="feedback_custom4" type="text" name="custom4"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		{{if $use_custom5==1}}
			<div class="row">
				<label for="feedback_custom5" class="required">Custom5</label>
				<input id="feedback_custom5" type="text" name="custom5"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		<div class="row">
			<label for="feedback_message" class="required">Message</label>
			<textarea id="feedback_message" name="message" rows="3"></textarea>
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
						<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=feedback&amp;rand={{$smarty.now}}" alt="Captcha"/>
						<label for="feedback_code" class="required">Security code</label>
						<input id="feedback_code" type="text" name="code" autocomplete="off"/>
						<span class="field-error"></span>
					</div>
				{{/if}}
			</div>
		{{/if}}

		<div class="buttons">
			<input type="submit" value="Send"/>
		</div>
	</form>
{{/if}}