{{if $async_submit_successful=='true'}}
	<form>
		<div class="success" data-fancybox="message" data-fancybox-redirect-to="{{$lang.urls.memberzone_my_videos}}">
			{{if $smarty.post.action=='change_complete'}}
				{{$lang.edit_video.success_message_edit}}
			{{else}}
				{{$lang.edit_video.success_message_upload}}
			{{/if}}
		</div>
	</form>
{{else}}
	<div class="container">
		<div class="heading cfx">
			<h2 class="title">
				{{if $smarty.post.video_id>0}}
					{{$lang.edit_video.title_edit|replace:"%1%":$smarty.post.title|replace:"%VIDEOS%":$lang.urls.memberzone_my_videos|replace:"%VIDEO%":$smarty.post.view_page_url|smarty:nodefaults}}
				{{else}}
					{{if $dvd.dvd_id>0}}
						{{assign var="channel_url" value=$lang.urls.videos_by_channel|replace:"%DIR%":$dvd.dir|replace:"%ID%":$dvd.dvd_id}}
						{{$lang.edit_video.title_upload_channel_step1|replace:"%CHANNEL%":$channel_url|replace:"%1%":$dvd.title|smarty:nodefaults}}
					{{else}}
						{{$lang.edit_video.title_upload_step1|replace:"%VIDEOS%":$lang.urls.memberzone_my_videos|smarty:nodefaults}}
					{{/if}}
				{{/if}}
			</h2>
		</div>

		{{assign var="is_locked" value="false"}}
		{{assign var="is_screenshots_locked" value="false"}}
		{{if $smarty.post.video_id>0}}
			{{if $change_forbidden==1}}
				{{assign var="is_locked" value="true"}}
			{{/if}}
			{{if $change_screenshots_forbidden==1}}
				{{assign var="is_screenshots_locked" value="true"}}
			{{/if}}
		{{/if}}
		{{if $smarty.post.is_locked==1}}
			{{assign var="is_locked" value="true"}}
		{{/if}}
		{{if $is_locked=='true'}}
			{{assign var="is_screenshots_locked" value="true"}}
		{{/if}}

		{{assign var="allow_url" value="false"}}

		<div class="box1">
			{{if $smarty.post.video_id==0}}
				<form class="form-upload" method="post" enctype="multipart/form-data" data-form="ajax-upload" data-continue-form="video_info_form" data-progress-url="{{$lang.urls.url_upload_progress}}">
					<div class="generic-error {{if $dvd_forbidden==0}}hidden{{/if}}">
						{{if $dvd_forbidden==1}}
							{{$lang.validation.video_edit.dvd_id_forbidden}}
						{{/if}}
					</div>

					<div class="two-sections">
						<div class="section-one">
							<strong class="section-title">{{$lang.edit_video.sub_title_upload}}</strong>

							{{if $allow_url==1}}
								<div class="row">
									<input type="radio" name="upload_option" value="file" id="edit_video_upload_option_file" class="radio" checked/>
									<label for="edit_video_upload_option_file" class="field-label">{{$lang.edit_video.field_upload_option_file}}</label>
									<div class="field-error down"></div>
								</div>
							{{else}}
								<input type="hidden" name="upload_option" value="file"/>
							{{/if}}

							<div class="row">
								<div class="file-control">
									<input type="text" class="textfield" placeholder="{{$lang.common_forms.file_upload_btn_browse}} {{$lang.edit_video.field_upload_option_file_hint}}" readonly/>
									<div class="button">{{$lang.common_forms.file_upload_btn_browse}}</div>
									<input type="file" name="content" class="file"/>
									<div class="field-error down"></div>
								</div>
							</div>

							{{if $allow_url==1}}
								<div class="row">
									<input type="radio" name="upload_option" value="url" id="edit_video_upload_option_url" class="radio"/>
									<label for="edit_video_upload_option_url" class="field-label">{{$lang.edit_video.field_upload_option_url}}</label>
									<div class="field-error down"></div>
								</div>

								<div class="row">
									<input type="text" name="url" class="textfield" placeholder="{{$lang.edit_video.field_upload_option_url_hint}}" disabled/>
									<div class="field-error down"></div>
								</div>
							{{/if}}

							{{if $allow_embed==1}}
								<div class="row">
									<input type="radio" name="upload_option" value="embed" id="edit_video_upload_option_embed" class="radio"/>
									<label for="edit_video_upload_option_embed" class="field-label">{{$lang.edit_video.field_upload_option_embed}}</label>
									<div class="field-error down"></div>
								</div>

								<div class="row">
									<label for="edit_video_embed" class="field-label">{{$lang.edit_video.field_embed_code}}</label>
									<textarea name="embed" id="edit_video_embed" class="textarea" rows="3" placeholder="{{$lang.edit_video.field_embed_code_hint}}" disabled></textarea>
									<div class="field-error down"></div>
								</div>

								<div class="row">
									<label for="edit_video_duration" class="field-label">{{$lang.edit_video.field_embed_duration}}</label>
									<input type="text" name="duration" id="edit_video_duration" class="textfield" placeholder="{{$lang.edit_video.field_embed_duration_hint}}" disabled/>
									<div class="field-error down"></div>
								</div>

								<div class="row">
									<label for="edit_video_screenshot" class="field-label">{{$lang.edit_video.field_embed_screenshot}}</label>
									<div class="file-control">
										<input type="text" id="edit_video_screenshot" class="textfield" placeholder="{{$lang.common_forms.file_upload_btn_browse}} {{$lang.edit_video.field_embed_screenshot_hint|replace:"%1%":$screenshot_size}}" readonly/>
										<div class="button">{{$lang.common_forms.file_upload_btn_browse}}</div>
										<input type="file" name="screenshot" class="file" disabled/>
										<div class="field-error down"></div>
									</div>
								</div>
							{{/if}}
						</div>

						<div class="section-two">
							<strong class="section-title">{{$lang.edit_video.sub_title_rules}}</strong>

							{{foreach item="item" from=$lang.edit_video.field.upload_rules}}
								<p>{{$item}}</p>
							{{/foreach}}
						</div>
					</div>

					<div class="bottom">
						<input type="hidden" name="action" value="upload_file"/>
						<input type="hidden" name="filename" value=""/>
						<input type="submit" class="submit" value="{{$lang.edit_video.btn_continue}}" {{if $dvd_forbidden==1}}disabled{{/if}}/>
					</div>
					<br/><br/>
				</form>
			{{/if}}

			<form id="video_info_form" class="form-upload {{if $smarty.post.video_id==0}}hidden{{/if}}" data-form="ajax" method="post">
				<div class="generic-error {{if $is_locked!='true' && $is_screenshots_locked!='true'}}hidden{{/if}}">
					{{if $is_locked=='true'}}
						{{$lang.validation.common.video_locked}}
					{{elseif $is_screenshots_locked=='true'}}
						{{$lang.validation.common.video_screenshots_locked}}
					{{/if}}
				</div>

				<div class="two-sections">
					<div class="section-one">
						<strong class="section-title">{{$lang.edit_video.sub_title_info}}</strong>

						<div class="row">
							<label for="edit_video_title" class="field-label required">{{$lang.edit_video.field_title}}</label>
							<input type="text" name="title" id="edit_video_title" class="textfield" value="{{$smarty.post.title}}" placeholder="{{$lang.edit_video.field_title_hint}}" {{if $lang.videos.truncate_title_to>0}}maxlength="{{$lang.videos.truncate_title_to}}"{{/if}} {{if $is_locked=='true'}}readonly{{/if}}/>
							<div class="field-error down"></div>
						</div>

						<div class="row">
							<label for="edit_video_description" class="field-label {{if $optional_description!=1}}required{{/if}}">{{$lang.edit_video.field_description}}</label>
							<textarea name="description" id="edit_video_description" class="textarea" rows="3" placeholder="{{$lang.edit_video.field_description_hint}}" {{if $is_locked=='true'}}readonly{{/if}}>{{$smarty.post.description}}</textarea>
							<div class="field-error down"></div>
						</div>

						<div class="holder row" data-name="category_ids">
							<label for="edit_video_categories" class="field-label {{if $optional_categories!=1}}required{{/if}}">{{$lang.edit_video.field_categories}}<span class="grey">({{$lang.edit_video.field_categories_hint|replace:"%1%":$max_categories}})</span></label>
							<div class="field-wrap">
								<select class="selectbox select-friend" style="width: 100%;" multiple="multiple" data-select="category_ids">
									{{foreach item="item" from=$list_categories}}
										<option value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}selected{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								</select>
								<div class="field-error down"></div>
								<div class="category_ids">
									{{foreach item="item" from=$smarty.post.category_ids}}
										<input type="hidden" name="category_ids[]" value="{{$item}}"/>
									{{/foreach}}
								</div>
							</div>
						</div>

						{{if 2<1}}
							{{if $max_categories==1}}
								<div class="row" data-name="category_ids">
									<label for="edit_video_categories" class="field-label {{if $optional_categories!=1}}required{{/if}}">{{$lang.edit_video.field_category}}</label>
									<select id="edit_video_categories" name="category_ids[]" class="selectbox" {{if $is_locked=='true'}}disabled{{/if}}>
										<option value="">{{$lang.common_forms.field_select_default}}</option>
										{{foreach item="item" from=$list_categories}}
											<option value="{{$item.category_id}}" {{if is_array($smarty.post.category_ids) && in_array($item.category_id,$smarty.post.category_ids)}}selected{{/if}}>{{$item.title}}</option>
										{{/foreach}}
									</select>
									<div class="field-error down"></div>
								</div>
							{{else}}
								{{assign var="category_ids" value=""}}
								{{assign var="category_text" value=""}}
								{{foreach item="category_id" from=$smarty.post.category_ids}}
									{{foreach item="item" from=$list_categories}}
										{{if $category_id==$item.category_id}}
											{{if $category_ids!=''}}
												{{assign var="category_ids" value="`$category_ids`, "}}
												{{assign var="category_text" value="`$category_text`, "}}
											{{/if}}
											{{assign var="category_ids" value="`$category_ids``$item.category_id`"}}
											{{assign var="category_text" value="`$category_text``$item.title`"}}
										{{/if}}
									{{/foreach}}
								{{/foreach}}
								<div class="row list-selector" {{if $is_locked!='true'}}data-name="category_ids" data-selector="{{$lang.urls.categories_selector}}" data-selected="{{$category_ids}}"{{/if}}>
									<label for="edit_video_categories" class="field-label {{if $optional_categories!=1}}required{{/if}}">{{$lang.edit_video.field_categories}}</label>
									<input type="text" id="edit_video_categories" class="textfield" value="{{$category_text}}" placeholder="{{$lang.edit_video.field_categories_hint|replace:"%1%":$max_categories}}" readonly/>
									<div class="field-error down"></div>
									{{foreach item="item" from=$smarty.post.category_ids}}
										<input type="hidden" name="category_ids[]" value="{{$item}}"/>
									{{/foreach}}
								</div>
							{{/if}}
						{{/if}}

						<div class="row">
							<label for="edit_video_tags" class="field-label {{if $optional_tags!=1}}required{{/if}}">{{$lang.edit_video.field_tags}}</label>
							<input type="text" name="tags" id="edit_video_tags" class="textfield" value="{{$smarty.post.tags}}" placeholder="{{$lang.edit_video.field_tags_hint}}" {{if $is_locked=='true'}}readonly{{/if}}/>
							<div class="field-error down"></div>
						</div>

						{{if $lang.enable_models=='true'}}
							{{if count($list_models)>0}}
								{{assign var="model_ids" value=""}}
								{{assign var="model_text" value=""}}
								{{foreach item="model_id" from=$smarty.post.model_ids}}
									{{foreach item="item" from=$list_models}}
										{{if $model_id==$item.model_id}}
											{{if $model_ids!=''}}
												{{assign var="model_ids" value="`$model_ids`, "}}
												{{assign var="model_text" value="`$model_text`, "}}
											{{/if}}
											{{assign var="model_ids" value="`$model_ids``$item.model_id`"}}
											{{assign var="model_text" value="`$model_text``$item.title`"}}
										{{/if}}
									{{/foreach}}
								{{/foreach}}
								<div class="row list-selector" {{if $is_locked!='true'}}data-name="model_ids" data-selector="{{$lang.urls.models_selector}}" data-selected="{{$model_ids}}"{{/if}}>
									<label for="edit_video_models" class="field-label">{{$lang.edit_video.field_models}}</label>
									<input type="text" id="edit_video_models" class="textfield" value="{{$model_text}}" placeholder="{{$lang.edit_video.field_models_hint}}" readonly/>
									<div class="field-error down"></div>
									{{foreach item="item" from=$smarty.post.model_ids}}
										<input type="hidden" name="model_ids[]" value="{{$item}}"/>
									{{/foreach}}
								</div>
							{{/if}}
						{{else}}
							{{foreach item="item" from=$smarty.post.model_ids}}
								<input type="hidden" name="model_ids[]" value="{{$item}}"/>
							{{/foreach}}
						{{/if}}

						{{if $smarty.session.content_source_group_id>0 && count($list_content_sources)>0}}
							<div class="row">
								<label for="edit_video_sponsor" class="field-label">{{$lang.edit_video.field_sponsor}}</label>
								<select name="content_source_id" id="edit_video_sponsor" class="selectbox" {{if $is_locked=='true'}}disabled{{/if}}>
									<option value="0">{{$lang.common_forms.field_select_default}}</option>
									{{foreach item=item from=$list_content_sources}}
										<option value="{{$item.content_source_id}}" {{if $item.content_source_id==$smarty.post.content_source_id}}selected="selected"{{/if}}>{{$item.title}}</option>
									{{/foreach}}
								</select>
								<div class="field-error down"></div>
							</div>
						{{/if}}

						{{if $dvd.dvd_id>0}}
							<div class="row">
								<label for="edit_video_channel" class="field-label">{{$lang.edit_video.field_channel}}</label>
								<input type="text" id="edit_video_channel" class="textfield" value="{{$dvd.title}}" readonly/>
								{{if $smarty.post.video_id==0}}
									<input type="hidden" name="dvd_id" value="{{$dvd.dvd_id}}"/>
								{{/if}}
								<div class="field-error down"></div>
							</div>
						{{/if}}

						<div class="row">
							<label for="edit_video_screenshot" class="field-label">{{$lang.edit_video.field_screenshot|replace:"%1%":$screenshot_size}}</label>
							<div class="file-control">
								<input type="text" class="textfield" placeholder="{{$lang.common_forms.file_upload_btn_browse}} {{$lang.edit_video.field_screenshot_hint|replace:"%1%":$screenshot_size}}" readonly/>
								<div class="button {{if $is_locked=='true'}}disabled{{/if}}">{{$lang.common_forms.file_upload_btn_browse}}</div>
								<input type="file" name="screenshot" id="edit_video_screenshot" class="file" {{if $is_locked=='true'}}disabled{{/if}}/>
								<div class="field-error down"></div>
							</div>
						</div>

						<div class="row">
							<div class="button-group">
								<label for="edit_video_is_private" class="field-label">{{$lang.edit_video.field_is_private}}</label>
								<div class="row">
									<input type="radio" id="edit_video_is_private_0" name="is_private" value="0" class="radio" {{if $smarty.post.is_private==0}}checked{{/if}} {{if $is_locked=='true'}}disabled{{/if}}>
									<label for="edit_video_is_private_0">{{$lang.edit_video.field_is_private_values.0}}</label>
									<input type="radio" id="edit_video_is_private_1" name="is_private" value="1" class="radio" {{if $smarty.post.is_private==1}}checked{{/if}} {{if $is_locked=='true'}}disabled{{/if}}>
									<label for="edit_video_is_private_1">{{$lang.edit_video.field_is_private_values.1}}</label>
								</div>
							</div>
							<div class="field-error down"></div>
						</div>

						{{if $smarty.post.screen_amount>0}}
							<strong class="section-title expand" data-expand-id="tab_screenshots">{{$lang.edit_video.sub_title_screenshots}}</strong>

							<div id="tab_screenshots" class="list-videos-screenshots hidden">
								<div class="margin-fix">
									{{section name="data" start="0" loop=$smarty.post.screen_amount}}
										<div class="item">
											<div class="img">
												<a href="{{$smarty.post.screenshot_sources[$smarty.section.data.index]}}" rel="images" data-fancybox-type="image">
													<img alt="" class="thumb {{if $lang.enable_thumb_lazyload=='true'}}lazy-load{{/if}}" {{if $lang.enable_thumb_lazyload=='true'}}src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" data-original{{else}}src{{/if}}="{{$smarty.post.screen_url}}/{{$lang.videos.thumb_size}}/{{$smarty.section.data.index+1}}.jpg?rnd={{$smarty.now}}"/>
												</a>
											</div>
											<div class="item-control">
												<div class="item-control-holder">
													<div class="toggle-button {{if $is_screenshots_locked=='true'}}disabled{{elseif $smarty.section.data.index+1==$smarty.post.screen_main}}active{{/if}}" data-action="choose">
														<input type="radio" class="radio" name="main_screenshot" value="{{$smarty.section.data.index+1}}" {{if $smarty.section.data.index+1==$smarty.post.screen_main}}checked{{/if}} {{if $is_screenshots_locked=='true'}}disabled{{/if}}>
														<span>{{$lang.edit_video.label_main}}</span>
													</div>
												</div>
											</div>
										</div>
									{{/section}}
								</div>
							</div>
						{{/if}}
					</div>

					<div class="section-two">
						<strong class="section-title">{{$lang.edit_video.sub_title_preview}}</strong>

						<p class="preview {{if $smarty.post.video_id>0 && $smarty.post.status_id==0}}disabled{{/if}}">
							{{if $smarty.post.video_id>0}}
								<img alt="" src="{{$smarty.post.preview_url}}"/>
								<em class="left">{{$lang.edit_video.field_file_info|replace:"%1%":"`$smarty.post.file_dimensions.0`x`$smarty.post.file_dimensions.1`"|replace:"%2%":$smarty.post.file_duration_string|replace:"%3%":$smarty.post.file_size_string}}</em>
								{{if $smarty.post.status_id==0}}
									<em class="right">{{$lang.edit_video.label_disabled}}</em>
								{{else}}
									{{assign var="video_rating" value="`$smarty.post.rating/5*100`"}}
									{{if $video_rating>100}}{{assign var="video_rating" value="100"}}{{/if}}
									<em class="right {{if $video_rating<50 && $smarty.post.rating_amount>1}}negative{{else}}positive{{/if}}">{{$video_rating|string_format:"%d"}}%</em>
								{{/if}}
							{{else}}
								<img alt="" src="{{$config.statics_url}}/static/images/waiting_upload.gif" data-preview-src="{{$lang.urls.memberzone_upload_video_preview}}"/>
								<em class="left" data-info-src="{{$lang.edit_video.field_file_info}}"></em>
							{{/if}}
						</p>
					</div>
				</div>

				<div class="bottom">
					<input type="hidden" name="function" value="get_block"/>
					<input type="hidden" name="block_id" value="{{$block_uid}}"/>
					{{if $smarty.post.video_id>0}}
						<input type="hidden" name="action" value="change_complete"/>
						<input type="submit" class="submit" value="{{$lang.edit_video.btn_edit}}" {{if $is_locked=='true'}}disabled{{/if}}/>
					{{else}}
						{{if $use_captcha==1}}
							<label>{{$lang.common_forms.field_captcha_hint}}</label>
							<div class="captcha-control">
								{{if $recaptcha_site_key!=''}}
									<div data-name="code">
										<div data-recaptcha-key="{{$recaptcha_site_key}}" data-recaptcha-theme="{{if $lang.theme.style=='metal'}}dark{{else}}light{{/if}}"></div>
										<div class="field-error down"></div>
									</div>
								{{else}}
									<div class="image">
										<img src="{{$lang.urls.captcha|replace:"%ID%":"video_edit"}}?rand={{$smarty.now}}" alt="{{$lang.common_forms.field_captcha_image}}"/>
										<label for="edit_video_code" class="field-label required">{{$lang.common_forms.field_captcha}}</label>
										<input type="text" name="code" id="edit_video_code" class="textfield" autocomplete="off"/>
										<div class="field-error up"></div>
									</div>
								{{/if}}
								<input type="submit" class="submit" value="{{$lang.edit_video.btn_upload}}" disabled/>
							</div>
						{{else}}
							<input type="submit" class="submit" value="{{$lang.edit_video.btn_upload}}" disabled/>
						{{/if}}
						<input type="hidden" name="action" value="add_new_complete"/>
						<input type="hidden" name="file" value=""/>
						<input type="hidden" name="file_hash" value=""/>
					{{/if}}
				</div>
			</form>
		</div>
	</div>
{{/if}}