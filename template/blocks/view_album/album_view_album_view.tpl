<div class="container">
	<h1 class="title">{{$data.title}}</h1>
	<div class="album-view">
		{{if $is_limit_over==1 || $data.can_watch==0}}
			{{if $is_limit_over==1}}
				{{assign var="join_message" value=$lang.albums.album_images_banner_message_unlimited}}
			{{else}}
				{{assign var="join_message" value=$lang.albums.album_images_banner_message_unlock}}
			{{/if}}
			{{if $smarty.session.user_id<1}}
				<a href="{{$lang.urls.login_required}}" data-action="popup" class="btn btn--unlock">
			{{elseif $data.tokens_required>0 && $smarty.session.tokens_available>=$data.tokens_required}}
				<a href="#unlock" data-action="purchase" data-album-id="{{$data.album_id}}" class="btn btn--unlock">
			{{else}}
				<a href="{{$lang.urls.upgrade_required}}" data-action="popup" class="btn btn--unlock">
			{{/if}}
				<span class="lock"><i class="icon-lock-shape-20"></i></span>
				<strong>{{$lang.albums.album_images_banner_click}}</strong> <span>{{$join_message}}</span>
			</a>
		{{else}}
			<div class="album-gallery-holder">
				<div class="album-gallery flexslider auto" data-slider="album">
					<ul class="slides">
						{{foreach item="item" name="images" from=$data.images}}
							<li>
								<a href="{{$item.formats[$lang.albums.image_big_size].protected_url}}" class="item" rel="images" target="_blank" data-attach-session="{{$session_name}}">
									<img alt="{{$item.title|default:$data.title}}" {{if $smarty.foreach.images.first}}src{{else}}data-src{{/if}}="{{$item.formats[$lang.albums.image_big_size].protected_url}}" data-attach-session="{{$session_name}}">
								</a>
							</li>
						{{/foreach}}
					</ul>
				</div>
			</div>
		{{/if}}
		<div class="album-images box flexslider" data-slider="images">
			<ul class="album-images__list  slides">
				{{foreach item="item" from=$data.images}}
					<li class="album-images__item item--bordered {{if $data.can_watch==0}}disabled{{/if}}">
						<img alt="{{$item.title|default:$data.title}}" data-src="{{$item.formats[$lang.albums.image_small_size].direct_url}}" width="{{$item.formats[$lang.albums.image_small_size].dimensions.0}}" height="{{$item.formats[$lang.albums.image_small_size].dimensions.1}}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">

						{{if $smarty.session.status_id==''}}
							<a href="{{$lang.urls.signup}}" data-action="popup" class="btn btn--color">{{$lang.common_list.get_access}}</a>
						{{elseif $smarty.session.status_id==2}}
							<a href="{{$lang.urls.upgrade}}" data-action="popup" class="btn btn--color">{{$lang.common_list.get_access}}</a>
						{{/if}}
					</li>
				{{/foreach}}
			</ul>
		</div>
	</div>
	<div class="info-bar cfx">
		<div class="vote-block info-bar__cell" data-action="rating">
			{{assign var="album_rating" value="`$data.rating/5*100`"}}
			{{if $album_rating>100}}{{assign var="album_rating" value="100"}}{{/if}}

			{{assign var="can_rate" value="0"}}
			{{if $lang.features_access.rate=='all' || ($lang.features_access.rate=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
				{{assign var="can_rate" value="1"}}
			{{/if}}

			{{if $is_limit_over==1 || $data.can_watch==0}}
				<a href="#like" class="btn__vote btn__like disabled" title="{{$lang.albums.album_details_rate_not_allowed}}">
					<i class="icon-like-shape-15"></i>
				</a>
			{{else}}
				<a class="btn__vote btn__like" title="{{$lang.albums.album_details_rate_like}}" {{if $can_rate==1}}href="#like" data-album-id="{{$data.album_id}}" data-vote="5"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
					<i class="icon-like-shape-15"></i>
				</a>
			{{/if}}

			<div id="voters" class="rotated thumb-spot__rating">
				<span data-rating="percent">{{$album_rating|string_format:"%d"}}%</span>
			</div>

			{{if $is_limit_over==1 || $data.can_watch==0}}
				<a href="#dislike" class="btn__vote btn__dislike disabled" title="{{$lang.albums.album_details_rate_not_allowed}}">
					<i class="icon-dislike-shape-15"></i>
				</a>
			{{else}}
				<a class="btn__vote btn__dislike" title="{{$lang.albums.album_details_rate_dislike}}" {{if $can_rate==1}}href="#like" data-album-id="{{$data.album_id}}" data-vote="0"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
					<i class="icon-dislike-shape-15"></i>
				</a>
			{{/if}}

			<span class="tooltip hidden" data-show="success">{{$lang.albums.album_details_rating_message_success}}</span>
			<span class="tooltip hidden" data-show="error">{{$lang.albums.album_details_rating_message_error}}</span>
		</div>
		<div class="media-data info-bar__cell">
			<ul class="media-data__list">
				<li>
					<span class="media-data__list-title">{{$lang.albums.album_details_label_images}}:</span>
					<strong class="media-data__list-value">{{$data.photos_amount}}</strong>
				</li>
				<li>
					<span class="media-data__list-title">{{$lang.albums.album_details_label_views}}:</span>
					<strong class="media-data__list-value">{{$data.album_viewed|number_format:0:",":$lang.global.number_format_delimiter}}</strong>
				</li>
				<li>
					<span class="media-data__list-title">{{$lang.albums.album_details_label_added_date}}:</span>
					<strong class="media-data__list-value">{{$data.post_date|date_format:$lang.global.date_format}}</strong>
				</li>
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
						<a title="{{$lang.albums.album_details_action_add_to_favourites[0]}}" href="#add2fav" class="info-bar__button {{if $data.is_favourited==1}}subscribed{{/if}}" data-action="drop" data-drop-id="fav_list">
							<i class="icon-heart-shape-16"><span data-favourites="count">{{$data.favourites_count}}</span></i>
						</a>
						<div id="fav_list" class="dropdown__block__menu dropdown__block__menu--alt dropdown__block__menu--fav">
							<ul class="drop-media">
								{{foreach item="item" from=$lang.albums.predefined_favourites}}
									<li data-fav-list-id="delete_fav_{{$item}}" {{if !in_array($item, $data.favourite_types)}}class="hidden"{{/if}}>
										<span>
											<a href="{{$lang.urls.memberzone_my_fav_albums}}?fav_type={{$item}}">{{$lang.albums.album_details_action_add_to_favourites[$item]}}</a>
											<a href="#delete" class="delete" data-action="delete" data-album-id="{{$data.album_id}}" data-fav-type="{{$item}}">{{$lang.albums.album_details_action_delete_from_favourites}}</a>
										</span>
									</li>
									<li data-fav-list-id="add_fav_{{$item}}" {{if in_array($item, $data.favourite_types)}}class="hidden"{{/if}}><a href="#add_to_fav" data-action="add" data-album-id="{{$data.album_id}}" data-fav-type="{{$item}}">{{$lang.albums.album_details_action_add_to_favourites[$item]}}</a></li>
								{{/foreach}}
							</ul>
						</div>
					{{else}}
						<a title="{{$lang.albums.album_details_action_add_to_favourites[0]}}" href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup" class="info-bar__button">
							<i class="icon-heart-shape-16"><span>{{$data.favourites_count}}</span></i>
						</a>
					{{/if}}
				</div>
			</div>
			{{assign var="can_download" value="0"}}
			{{if $lang.features_access.download=='all' || ($lang.features_access.download=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
				{{assign var="can_download" value="1"}}
			{{/if}}
			{{if count($data.zip_files)>0}}
				<div class="info-bar__cell">
					<a class="info-bar__button" title="{{$lang.albums.album_details_label_download}}" {{if $can_download==1 && $data.can_watch==1}}href="#download" data-action="toggle" data-toggle-id="download_list" data-toggle-save="true"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
						<i class="icon-download-shape-17"><span>{{$data.zip_files|@count}}</span></i>
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
	<div class="box media-info">
		{{if $can_download==1 && count($data.zip_files)>0 && $data.can_watch==1}}
			<div id="download_list" class="media-info__row hidden">
				<div class="media-info__lists-row">
					<span class="media-info__label">{{$lang.albums.album_details_label_download}}:</span>
					<div class="media-info__buttons">
						{{foreach item="item" from=$data.zip_files}}
							{{assign var="format_lang_key" value=$item.size|replace:".":"_"}}
							<a class="btn" href="{{$item.file_url}}" data-attach-session="{{$session_name}}">{{$lang.albums.album_details_label_download_format[$format_lang_key]|default:"%1%, %2%"|replace:"%1%":"`$item.size`"|replace:"%2%":$item.file_size_string}}</a>
						{{/foreach}}
					</div>
				</div>
			</div>
		{{/if}}
		{{if $data.description!=''}}
			<div class="media-info__row media-desc">
				<span class="media-info__label">{{$lang.albums.album_details_label_description}}:</span>
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
								<span class="media-info__label">{{$lang.albums.album_details_label_categories}}:</span>
								<div class="media-info__buttons">
									{{foreach item="item" from=$data.categories}}
										<a class="btn" {{if $lang.enable_categories=='true'}}href="{{$lang.urls.content_by_category|replace:"%DIR%":$item.dir|replace:"%ID%":$item.category_id}}"{{/if}}>{{$item.title}}</a>
									{{/foreach}}
								</div>
							</div>
						{{/if}}
						{{if count($data.tags)>0}}
							<div class="media-info__lists-row">
								<span class="media-info__label">{{$lang.albums.album_details_label_tags}}:</span>
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