{{if $lang.features_access.comment=='all' || ($lang.features_access.comment=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
	{{assign var="can_add_comment" value="1"}}
{{else}}
	{{assign var="can_add_comment" value="0"}}
{{/if}}

<div class="container">
	<div class="box comments comments_models">
		<span class="media-info__label">
			{{$lang.comments.field_comments}}:
			<a {{if $can_add_comment==1}}href="#add_comment" data-action="toggle" data-toggle-id="new_comment"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
				{{if count($data)==0}}{{$lang.comments.field_comments_empty}}{{else}}{{$lang.comments.btn_add_comment}}{{/if}}
			</a>
		</span>

		{{if $can_add_comment==1}}
			<div id="new_comment_success" class="success hidden">
				{{$lang.comments.success_message}}
			</div>
			<div id="new_comment" class="hidden">
				<form method="post" data-form="comments" data-block-id="{{$block_uid}}" data-success-hide-id="new_comment" data-success-show-id="new_comment_success">
					<div class="generic-error hidden"></div>
					{{if $smarty.session.user_id<1}}
						<div class="comments__row">
							<input name="anonymous_username" maxlength="30" type="text" class="input comments__it" placeholder="{{$lang.comments.field_anonymous_name}}">
						</div>
					{{/if}}
					<div class="comments__row">
						<div class="relative">
							<textarea class="input comments__textarea" name="comment" rows="3" placeholder="{{$lang.comments.field_comment}}"></textarea>
							<div class="field-error down"></div>
							{{if $use_captcha!=1}}
								<button type="submit" class="btn btn__submit btn--color btn--middle">{{$lang.comments.btn_send}}</button>
								<input type="hidden" name="action" value="add_comment"/>
								<input type="hidden" name="model_id" value="{{$model_info.model_id}}">
							{{/if}}
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
									<img src="{{$lang.urls.captcha|replace:"%ID%":"comments"}}?rand={{$smarty.now}}" alt="{{$lang.common_forms.field_captcha_image}}"/>
									<div class="relative">
										<input type="text" name="code" class="input" autocomplete="off" placeholder="{{$lang.common_forms.field_captcha}}"/>
										<div class="field-error up"></div>
									</div>
								</div>
							{{/if}}
							<input type="hidden" name="action" value="add_comment"/>
							<input type="hidden" name="model_id" value="{{$model_info.model_id}}">
							<input type="submit" class="btn btn__submit btn--color btn--middle" value="{{$lang.comments.btn_send}}">
						</div>
					{{/if}}
				</form>
			</div>
		{{/if}}

		{{include file="include_content_comments.tpl"}}
	</div>
</div>