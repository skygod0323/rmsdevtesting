{{if $post_info.post_id>0}}
	<div class="comments">
		<div>
			<label>Comments</label>
			<span>
				{{if $total_count>0}}{{$total_count}}{{else}}{{$data|@count}}{{/if}}
			</span>
		</div>

		<div class="block-comments" data-block-id="{{$block_uid}}">
			{{if ($smarty.session.user_id>0 || $anonymous_user_id>0) && $can_add_comment==1}}
				<form method="post">
					<div class="success hidden">
						Thank you! Your comment has been submitted for review.
					</div>

					<div class="block-new-comment">
						<div class="generic-error hidden"></div>
						<div>
							{{if $smarty.session.user_id<1 && $anonymous_user_id>0}}
								<div class="row">
									<label for="comment_username">Your name</label>
									<input id="comment_username" type="text" name="anonymous_username" maxlength="30"/>
								</div>
							{{/if}}

							<div class="row">
								<label for="comment_message" class="required">Comment</label>
								<textarea id="comment_message" class="textarea" name="comment" rows="3"></textarea>
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
											<img src="{{$config.project_url}}/?mode=async&amp;function=show_security_code&amp;captcha_id=post_comments&amp;rand={{$smarty.now}}" alt="Captcha"/>
											<label for="comment_code" class="required">Security code</label>
											<input id="comment_code" type="text" name="code" autocomplete="off"/>
											<span class="field-error"></span>
										</div>
									{{/if}}
								</div>
							{{/if}}

							<div class="buttons">
								<input type="hidden" name="action" value="add_comment"/>
								<input type="hidden" name="post_id" value="{{$post_info.post_id}}">
								<input type="submit" value="Send"/>
							</div>
						</div>
					</div>
				</form>
			{{else}}
				<div class="notice">You can't add comments to this post</div>
			{{/if}}

			<div id="{{$block_uid}}" class="list-comments {{if count($data)==0}}hidden{{/if}}">
				<div id="{{$block_uid}}_items" class="items">
					{{foreach item="item" from=$data}}
						<div class="item" data-comment-id="{{$item.comment_id}}">
							<span class="avatar">
								{{if $item.status_id==4}}
									<span class="no-avatar">No avatar</span>
								{{else}}
									<a href="{{$config.project_url}}/members/{{$item.user_id}}/">
										<img src="{{$item.avatar_url}}" alt="{{$item.display_name}}"/>
									</a>
								{{/if}}
							</span>
							<label>
								{{if $item.status_id==4}}
									<span class="anonymous">{{$item.anonymous_username|default:$item.display_name}}</span>
								{{else}}
									<a href="{{$config.project_url}}/members/{{$item.user_id}}/">{{$item.display_name}}</a>
								{{/if}}
								{{$item.added_date|date_format:"%text"}}
							</label>
							<span class="comment-options">
								<span class="comment-rating {{if $item.rating>0}}positive{{elseif $item.rating<0}}negative{{/if}}">{{$item.rating}}</span>
								<a class="comment-like" href="#like">+1</a>
								<a class="comment-dislike" href="#dislike">-1</a>
							</span>
							<p>{{$item.comment|replace:"\n":"<br/>"}}</p>
						</div>
					{{/foreach}}
				</div>

				{{* include pagination here *}}
			</div>
		</div>
	</div>
{{/if}}