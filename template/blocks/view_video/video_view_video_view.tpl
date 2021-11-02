<div class="container">
	<h1 class="title">{{$data.title}}</h1>
	<div class="media-container">
		<div class="player">
			<div class="player-holder"> 
				{{if $is_limit_over==1}} 
					{{assign var="join_message" value=$lang.videos.video_player_banner_message_unlimited}}
					{{include file="include_join_video_banner.tpl"}}
				{{elseif $data.can_watch==1}}
					{{if $data.load_type_id==3}}
						<div class="embed-wrap" style="width: 100%; height: 0; padding-bottom: {{$player_size[1]/$player_size[0]*100|replace:",":"."}}%">{{$data.embed|smarty:nodefaults}}</div>
					{{elseif $flashvars.video_url==''}}
						{{assign var="join_message" value=$lang.videos.video_player_banner_message_unlock}}
						{{include file="include_join_video_banner.tpl"}}
					{{else}}
						<div style="width: 100%; height: 0; padding-bottom: {{$player_size[1]/$player_size[0]*100|replace:",":"."}}%; position: relative;">
							<div id="kt_player"></div>
						</div>
						<script type="text/javascript" src="{{$config.project_url}}/player/kt_player.js?v={{$config.project_version}}"></script>
						<script type="text/javascript">
							/* <![CDATA[ */
							var flashvars = {
								{{foreach name="data" key="key" item="item" from=$flashvars}}
									{{if $data.is_purchased_video != '1'}}
										{{if $key != 'video_alt_url' && $key != 'video_alt_url2'}}
											{{$key}}: '{{$item|replace:"'":"\'"}}'{{if !$smarty.foreach.data.last}}, {{/if}}
										{{/if}}
									{{else}}
										{{if $key != 'video_url'}}
											{{$key}}: '{{$item|replace:"'":"\'"}}'{{if !$smarty.foreach.data.last}}, {{/if}}
										{{/if}}
									{{/if}}
								{{/foreach}}
							};
							{{if $smarty.get.playlist>0}}
								flashvars['autoplay'] = 'true';
							{{/if}}
							var player_obj = kt_player('kt_player', '{{$config.project_url}}/player/kt_player.swf?v={{$config.project_version}}', '100%', '100%', flashvars);
							/* ]]> */
						</script>
						{{if $smarty.session.user_id>0}}
							{{if $data.is_purchased_video != '1'}}
								{{if $data.tokens_required>0 && $lang.enable_tokens=="true"}}
									{{if $smarty.session.tokens_available<$data.tokens_required}}
										{{assign var="tokens_left" value=$data.tokens_required-$smarty.session.tokens_available}}
										{{$lang.videos.video_player_tokens_not_enough[$video_type]|replace:"%MEMBER%":$data.user.display_name|replace:"%TOKENS_COST%":$data.tokens_required|replace:"%TOKENS_AVAILABLE%":$smarty.session.tokens_available|replace:"%TOKENS_LEFT%":$tokens_left|replace:"%UPGRADE%":$lang.urls.upgrade|smarty:nodefaults}}
										<form class="comments" id="tokens_form">
											<input type="button" class="submit btn btn__submit btn--color btn--middle" data-action="popup" data-href="{{$lang.urls.upgrade}}" value="{{$lang.videos.video_player_tokens_btn_buy|replace:"%TOKENS_COST%":$data.tokens_required|replace:"%TOKENS_AVAILABLE%":$smarty.session.tokens_available|replace:"%TOKENS_LEFT%":$tokens_left}}">
										</form>
									{{else}}
										{{assign var="tokens_left" value=$smarty.session.tokens_available-$data.tokens_required}}
										{{$lang.videos.video_player_tokens_purchase[$video_type]|replace:"%MEMBER%":$data.user.display_name|replace:"%TOKENS_COST%":$data.tokens_required|replace:"%TOKENS_AVAILABLE%":$smarty.session.tokens_available|replace:"%TOKENS_LEFT%":$tokens_left|replace:"%UPGRADE%":$lang.urls.upgrade|smarty:nodefaults}}
										<form class="comments" id="tokens_form" action="{{$data.canonical_url}}" method="post" data-form="ajax">
											<div class="generic-error hidden"></div>
											<input type="hidden" name="action" value="purchase_video"/>
											<input type="hidden" name="video_id" value="{{$data.video_id}}">
											<input type="submit" class="submit btn btn__submit btn--color btn--middle" value="{{$lang.videos.video_player_tokens_btn_spend|replace:"%TOKENS_COST%":$data.tokens_required|replace:"%TOKENS_AVAILABLE%":$smarty.session.tokens_available|replace:"%TOKENS_LEFT%":$tokens_left}}">
										</form>
									{{/if}} 
								{{else}}
									{{$lang.videos.video_player_premium_required[$video_type]|replace:"%MEMBER%":$data.user.display_name|replace:"%UPGRADE%":$lang.urls.upgrade|smarty:nodefaults}}
									<form class="comments" id="tokens_form">
										<input type="button" class="submit btn btn__submit btn--color btn--middle" data-action="popup" data-href="{{$lang.urls.upgrade}}" value="{{$lang.videos.video_player_premium_btn_buy}}">
									</form>
								{{/if}}
								<script type="text/javascript">
									player_obj.listen('ktVideoFinished', function() {
										document.getElementById('tokens_form').classList.add("flex");
									});
								</script>
							{{/if}}
						{{else}}
							<script type="text/javascript">
								/* <![CDATA[ */
								player_obj.listen('ktVideoFinished', function() {
									//document.getElementById('signup_guests').click();
								});
								/* ]]> */
							</script>
						{{/if}}
					{{/if}}
				{{else}}
					{{assign var="join_message" value=$lang.videos.video_player_banner_message_unlock}}
					{{include file="include_join_video_banner.tpl"}}
				{{/if}}
			</div> 
		</div>
	</div>
	<a href="{{$lang.urls.signup_guests}}" class="hidden" id="signup_guests" data-action="popup">guests</a>

	{{if $var_playlist!=''}}
		{{$var_playlist|smarty:nodefaults}}
	{{/if}}
	<div class="info-bar cfx">
		<div class="vote-block info-bar__cell" data-action="rating">

			<input type="range">

			{{assign var="video_rating" value="`$data.rating/5*100`"}}
			{{if $video_rating>100}}{{assign var="video_rating" value="100"}}{{/if}}

			{{assign var="can_rate" value="0"}}
			{{if $lang.features_access.rate=='all' || ($lang.features_access.rate=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
				{{assign var="can_rate" value="1"}}
			{{/if}}

			{{if $is_limit_over==1 || $data.can_watch==0}}
				<a href="#like" class="btn__vote btn__like disabled" title="{{$lang.videos.video_details_rate_not_allowed}}">
					<i class="icon-like-shape-15"></i>
				</a>
			{{else}}
				<a class="btn__vote btn__like" title="{{$lang.videos.video_details_rate_like}}" {{if $can_rate==1}}href="#like" data-video-id="{{$data.video_id}}" data-vote="5"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
					<i class="icon-like-shape-15"></i>
				</a>
			{{/if}}

			<div id="voters" class="rotated thumb-spot__rating">
				<span data-rating="percent">{{$video_rating|string_format:"%d"}}%</span>
			</div>

			{{if $is_limit_over==1 || $data.can_watch==0}}
				<a href="#dislike" class="btn__vote btn__dislike disabled" title="{{$lang.videos.video_details_rate_not_allowed}}">
					<i class="icon-dislike-shape-15"></i>
				</a>
			{{else}}
				<a class="btn__vote btn__dislike" title="{{$lang.videos.video_details_rate_dislike}}" {{if $can_rate==1}}href="#like" data-video-id="{{$data.video_id}}" data-vote="0"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
					<i class="icon-dislike-shape-15"></i>
				</a>
			{{/if}}

			<span class="tooltip hidden" data-show="success">{{$lang.videos.video_details_rating_message_success}}</span>
			<span class="tooltip hidden" data-show="error">{{$lang.videos.video_details_rating_message_error}}</span>
		</div>
		<div class="media-data info-bar__cell">
			<ul class="media-data__list">
				<li>
					<span class="media-data__list-title">{{$lang.videos.video_details_label_duration}}:</span>
					<strong class="media-data__list-value">{{$data.duration_array.text}}</strong>
				</li>
				<li>
					<span class="media-data__list-title">{{$lang.videos.video_details_label_views}}:</span>
					<strong class="media-data__list-value">{{$data.video_viewed|number_format:0:",":$lang.global.number_format_delimiter}}</strong>
				</li>
				<li>
					<span class="media-data__list-title">{{$lang.videos.video_details_label_added_date}}:</span>
					<strong class="media-data__list-value">{{$data.post_date|date_format:$lang.global.date_format}}</strong>
				</li>
                                {{if $data.custom1!='' }}
                                        <li>
                                            <span class="media-data__list-title">{{$lang.videos.video_details_label_country}}:</span>
                                            <strong class="media-data__list-value">{{$data.custom1|country}}</strong>
                                        </li>
                                {{/if}}
			</ul>
		</div>
		<div class="info-bar__buttons">
			<div class="info-bar__cell">
				<div class="dropdown__block align-center">
					{{assign var="can_favourite" value="0"}}
					{{if ($lang.features_access.favourite=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
						{{assign var="can_favourite" value="1"}}
					{{/if}}
					{{if $can_favourite==1}}
						<a title="{{$lang.videos.video_details_action_add_to_favourites[0]}}" href="#add2fav" class="info-bar__button {{if $data.is_favourited==1}}subscribed{{/if}}" data-action="drop" data-drop-id="fav_list">
							<i class="icon-heart-shape-16"><span data-favourites="count">{{$data.favourites_count}}</span></i>
						</a>
						<div id="fav_list" class="dropdown__block__menu dropdown__block__menu--alt dropdown__block__menu--fav">
							<ul class="drop-media">
								{{foreach item="item" from=$lang.videos.predefined_favourites}}
									<li data-fav-list-id="delete_fav_{{$item}}" {{if !in_array($item, $data.favourite_types)}}class="hidden"{{/if}}>
										<span>
											<a href="{{$lang.urls.memberzone_my_fav_videos}}?fav_type={{$item}}">{{$lang.videos.video_details_action_add_to_favourites[$item]}}</a>
											<a href="#delete" class="delete" data-action="delete" data-video-id="{{$data.video_id}}" data-fav-type="{{$item}}">{{$lang.videos.video_details_action_delete_from_favourites}}</a>
										</span>
									</li>
									<li data-fav-list-id="add_fav_{{$item}}" {{if in_array($item, $data.favourite_types)}}class="hidden"{{/if}}><a href="#add_to_fav" data-action="add" data-video-id="{{$data.video_id}}" data-fav-type="{{$item}}">{{$lang.videos.video_details_action_add_to_favourites[$item]}}</a></li>
								{{/foreach}}
								{{foreach item="item" from=$smarty.session.playlists}}
									<li data-fav-list-id="delete_playlist_{{$item.playlist_id}}" {{if !in_array($item.playlist_id, $data.favourite_playlists)}}class="hidden"{{/if}}>
										<span>
											<a href="{{$lang.urls.memberzone_my_playlist|replace:"%ID%":$item.playlist_id}}">{{$lang.videos.video_details_action_add_to_playlist|replace:"%1%":$item.title|smarty:nodefaults}}</a>
											<a href="#delete" class="delete" data-action="delete" data-video-id="{{$data.video_id}}" data-fav-type="10" data-playlist-id="{{$item.playlist_id}}">{{$lang.videos.video_details_action_delete_from_favourites}}</a>
										</span>
									</li>
									<li data-fav-list-id="add_playlist_{{$item.playlist_id}}" {{if in_array($item.playlist_id, $data.favourite_playlists)}}class="hidden"{{/if}}><a href="#add_to_playlist" data-action="add" data-video-id="{{$data.video_id}}" data-fav-type="10" data-playlist-id="{{$item.playlist_id}}">{{$lang.videos.video_details_action_add_to_playlist|replace:"%1%":$item.title|smarty:nodefaults}}</a></li>
								{{/foreach}}
								<li data-fav-list-id="add_playlist_new"><a href="#add_to_new_playlist" data-action="add" data-video-id="{{$data.video_id}}" data-fav-type="10" data-fav-url="{{$lang.urls.memberzone_my_playlist}}" data-create-playlist-url="{{$lang.urls.memberzone_create_playlist}}">{{$lang.videos.video_details_action_add_to_new_playlist}}</a></li>
								<li data-fav-list-id="delete_playlist_" class="hidden">
									<span>
										<a href="{{$lang.urls.memberzone_my_playlist}}">{{$lang.videos.video_details_action_add_to_playlist|smarty:nodefaults}}</a>
										<a href="#delete" class="delete" data-action="delete" data-video-id="{{$data.video_id}}" data-fav-type="10" data-playlist-id="%ID%">{{$lang.videos.video_details_action_delete_from_favourites}}</a>
									</span>
								</li>
								<li data-fav-list-id="add_playlist_" class="hidden"><a href="#add_to_playlist" data-action="add" data-video-id="{{$data.video_id}}" data-fav-type="10" data-playlist-id="%ID%">{{$lang.videos.video_details_action_add_to_playlist|smarty:nodefaults}}</a></li>
							</ul>
						</div>
					{{else}}
						<a title="{{$lang.videos.video_details_action_add_to_favourites[0]}}" href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup" class="info-bar__button">
							<i class="icon-heart-shape-16"><span>{{$data.favourites_count}}</span></i>
						</a>
					{{/if}}
				</div>
			</div>
			{{assign var="can_download" value="0"}}
			{{if $lang.features_access.download=='all' || ($lang.features_access.download=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
				{{assign var="can_download" value="1"}}
			{{/if}}
			{{if count($data.download_formats)>0}}
				<div class="info-bar__cell">
					<a class="info-bar__button" title="{{$lang.videos.video_details_label_download}}" {{if $can_download==1 && $data.can_watch==1}}href="#download" data-action="toggle" data-toggle-id="download_list" data-toggle-save="true"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
						<i class="icon-download-shape-17"><span>{{$data.download_formats|@count}}</span></i>
					</a>
				</div>
			{{/if}}
			{{if $lang.videos.show_screenshots=='true'}}
				<div class="info-bar__cell">
					<a class="info-bar__button" title="{{$lang.videos.video_details_label_screenshots}}" href="#screenshots" data-action="toggle" data-toggle-id="screenshots_list" data-toggle-save="true">
						<i class="icon-playlist-shape-21"><span>{{$data.screen_amount}}</span></i>
					</a>
				</div>
			{{/if}}
			<div class="info-bar__cell">
				{{assign var="can_add_comment" value="0"}}
				{{if $lang.features_access.comment=='all' || ($lang.features_access.comment=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
					{{assign var="can_add_comment" value="1"}}
				{{/if}}
				<a class="info-bar__button" title="{{$lang.comments.btn_add_comment}}" {{if $can_add_comment==1}}href="#add_comment" data-action="toggle" data-toggle-id="new_comment"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
					<i class="icon-dialog-clouds-shape-19"><span>{{$data.comments_count}}</span></i>
					<i class="icon-icons8-Plus"></i>
				</a>
			</div>
		</div>
	</div>
	<div class="info-bar cfx">
	        <a href="https://www.rmsdevtesting.com/signup-info/">Placeholder</a>
	</div>
	<div class="box media-info">
		{{if $can_download==1 && count($data.download_formats)>0 && $data.can_watch==1}}
			<div id="download_list" class="media-info__row hidden">
				<div class="media-info__lists-row">
					<span class="media-info__label">{{$lang.videos.video_details_label_download}}:</span>
					<div class="media-info__buttons">
						{{foreach item="item" from=$data.download_formats}}
							{{assign var="format_lang_key" value=$item.postfix|replace:".":"_"}}
							<a class="btn" href="{{$item.file_url}}" data-attach-session="{{$session_name}}">{{$lang.videos.video_details_label_download_format[$format_lang_key]|default:"%1%, %2%"|replace:"%1%":"`$item.title`"|replace:"%2%":$item.file_size_string}}</a>
						{{/foreach}}
					</div>
				</div>
			</div>
		{{/if}}
		{{if $lang.videos.show_screenshots=='true'}}
			<div id="screenshots_list" class="media-info__row hidden">
				<ul class="previw__list">
					{{section name="data" start="0" loop=$data.screen_amount}}
						<li class="previw__list__item item--bordered">
							<a href="{{$data.screenshot_sources[$smarty.section.data.index]}}" rel="screenshots" data-fancybox-type="image">
								<img src="{{$data.screen_url}}/{{$lang.videos.thumb_size}}/{{$smarty.section.data.index+1}}.jpg" width="{{$lang.videos.thumb_size|geomsize:'width'}}" height="{{$lang.videos.thumb_size|geomsize:'height'}}" alt="{{$data.title}}">
							</a>
						</li>
					{{/section}}
				</ul>
			</div>
		{{/if}}
		{{if $data.description!=''}}
			<div class="media-info__row media-desc">
				<span class="media-info__label">{{$lang.videos.video_details_label_description}}:</span>
				<span class="media-info__desc">{{$data.description}}</span>
			</div>
		{{/if}}
		{{if count($data.categories)>0 || count($data.tags)>0 || count($data.models)>0}}
			<div class="media-info__row media-models">
				{{if count($data.models)>0}}
					{{foreach item="item" from=$data.models name="models"}}
						{{if $smarty.foreach.models.iteration<=2}}
							<div class="media-box media-model pull-left">
								<div class="media-box__img">
									<a {{if $lang.enable_models=='true'}}href="{{$lang.urls.content_by_model|replace:"%DIR%":$item.dir|replace:"%ID%":$item.model_id}}"{{/if}}>
										{{if $item.screenshot1!=''}}
											<img src="{{$item.base_files_url}}/{{$item.screenshot1}}" alt="{{$item.title}}"/>
										{{else}}
											<img src="{{$config.statics_url}}/static/images/no-avatar-model.jpg" alt="{{$item.title}}">
											<span class="no-avatar">
												<span>{{$lang.models.list_label_no_image}}</span>
											</span>
										{{/if}}
									</a>
								</div>
								<div class="media-box__content">
									<h3 class="media-box__title">{{$item.title}}</h3>
									<div class="media-box__list">
										<div class="media-box__row">
											<div class="media-box__left">
												{{$lang.models.info_age}}: <span>{{if $item.age>0}}{{$item.age}}{{else}}{{$lang.models.info_na}}{{/if}}</span>
											</div>
											<div class="media-box__right">
												{{$lang.models.info_measurements}}: <span>{{$item.measurements|default:$lang.models.info_na}}</span>
											</div>
										</div>
										<div class="media-box__row">
											<div class="media-box__left">
												{{$lang.models.info_height}}: <span>{{$item.height|default:$lang.models.info_na}}</span>
											</div>
											<div class="media-box__right">
												{{$lang.models.info_hair}}: <span>{{$lang.models.info_hair_values[$item.hair_id]|default:$lang.models.info_na}}</span>
											</div>
										</div>
										<div class="media-box__row">
											<div class="media-box__left">
												{{$lang.models.info_weight}}: <span>{{$item.weight|default:$lang.models.info_na}}</span>
											</div>
											<div class="media-box__right">
												{{$lang.models.info_eyes}}: <span>{{$lang.models.info_eyes_values[$item.eye_color_id]|default:$lang.models.info_na}}</span>
											</div>
										</div>
									</div>
									{{if $lang.enable_models=='true'}}
										<a href="{{$lang.urls.content_by_model|replace:"%DIR%":$item.dir|replace:"%ID%":$item.model_id}}" class="btn">{{$lang.models.btn_profile}}</a>
									{{/if}}
								</div>
							</div>
						{{/if}}
					{{/foreach}}
				{{/if}}
				{{if count($data.categories)>0 || count($data.tags)>0}}
					<div class="media-info__lists">
						{{if count($data.categories)>0}}
							<div class="media-info__lists-row">
								<span class="media-info__label">{{$lang.videos.video_details_label_categories}}:</span>
								<div class="media-info__buttons">
									{{foreach item="item" from=$data.categories}}
										<a class="btn" {{if $lang.enable_categories=='true'}}href="{{$lang.urls.content_by_category|replace:"%DIR%":$item.dir|replace:"%ID%":$item.category_id}}"{{/if}}>{{$item.title}}</a>
									{{/foreach}}
								</div>
							</div>
						{{/if}}
						{{if count($data.tags)>0}}
							<div class="media-info__lists-row">
								<span class="media-info__label">{{$lang.videos.video_details_label_tags}}:</span>
								<div class="media-info__buttons">
									{{foreach item="item" from=$data.tags}}
										<a class="btn" {{if $lang.enable_tags=='true'}}href="{{$lang.urls.content_by_tag|replace:"%DIR%":$item.tag_dir|replace:"%ID%":$item.tag_id}}"{{/if}}>{{$item.tag}}</a>
									{{/foreach}}
								</div>
							</div>
						{{/if}}
					</div>
				{{/if}}
			</div>
		{{/if}}
	</div>
</div>
