<div class="modal popup-holder {{if $smarty.get.error=='only_for_members'}}disallow{{/if}}" id="modal-logon">
	<form action="{{$lang.urls.login}}" data-form="ajax" method="post">
		{{if $smarty.get.error=='only_for_members'}}
			<a href="{{$lang.urls.signup}}" data-action="popup" class="btn btn--unlock btn--unlock--danger">
				<span class="lock"><i class="icon-lock-shape-20"></i></span>
				<strong class="error-message">
					{{$lang.login.error_message_only_for_members}}
					<span>
						{{$lang.login.error_message_only_for_members_join}}
					</span>
				</strong>
			</a>
		{{/if}}
		<div class="modal__window">
			<h2 class="title title__modal">{{$lang.login.title}}</h2>
			<div class="generic-error hidden"></div>

			<div class="modal__window__form  modal__window__form--single cfx">
				<div class="modal__window__row">
					<label for="logon_username" class="modal__window__label required">{{$lang.login.field_username}} (*):</label>
					<div class="relative">
						<input id="logon_username" type="text" name="username" class="input" placeholder="{{$lang.login.field_username_hint}}">
						<div class="field-error down"></div>
					</div>
				</div>

				<div class="modal__window__row">
					<label for="logon_password" class="modal__window__label required">{{$lang.login.field_password}} (*):</label>
					<div class="relative">
						<input id="logon_password" type="password" name="pass" class="input">
						<div class="field-error down"></div>
					</div>
				</div>

				{{if $use_captcha==1}}
					<div class="modal__window__row captcha-control">
						<h6 class="title title_tiny">{{$lang.common_forms.field_captcha_hint}}</h6>
						{{if $recaptcha_site_key!=''}}
							<div class="image relative" data-name="code">
								<div data-recaptcha-key="{{$recaptcha_site_key}}" data-recaptcha-theme="{{if $lang.theme.style=='dark'}}dark{{else}}light{{/if}}"></div>
								<div class="field-error up"></div>
							</div>
						{{else}}
							<div class="image">
								<img src="{{$lang.urls.captcha|replace:"%ID%":"logon"}}?rand={{$smarty.now}}" alt="{{$lang.common_forms.field_captcha_image}}"/>
								<div class="relative">
									<input type="text" name="code" id="logon_code" class="input" autocomplete="off" placeholder="{{$lang.common_forms.field_captcha}}"/>
									<div class="field-error up"></div>
								</div>
							</div>
						{{/if}}
					</div>
				{{/if}}

				<div class="btn__row btn__row--align-right">
					<input type="hidden" name="action" value="login"/>
					<input type="hidden" name="email_link" value="{{$lang.urls.email_action}}"/>
					<button type="submit" class="btn btn--green btn--middle submit">{{$lang.login.btn_log_in}}</button>
					<div class="link__holder">
						<a href="{{$lang.urls.reset_password}}" data-action="popup" class="link mark-color">{{$lang.login.link_reset_password}}</a><br>
						<a href="{{$lang.urls.feedback}}" data-action="popup" class="link mark-color2">{{$lang.login.link_help}}</a>
					</div>
				</div>
			</div>

			<div class="modal__join">
				<h2 class="modal__join__title">{{$lang.login.not_member}}QQ</h2>
				<div class="btn__row">
					<a href="{{$lang.urls.signup}}" data-action="popup" class="btn btn--duble">
						<strong>{{$lang.login.link_join_now}}</strong>
						<span>{{$lang.login.link_join_now_hint}}</span>
					</a>
				</div>
			</div>
		</div>
	</form>
</div>