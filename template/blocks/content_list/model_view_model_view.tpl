<div class="container">
	<h1 class="title">{{$lang.models.info_title|replace:"%1%":$data.title}} {{if $data.alias!=''}}({{$data.alias}}){{/if}}</h1>
	<div class="model-view">
		<div class="img-holder pull-left">
			{{if $data.screenshot1!=''}}
				<img src="{{$data.base_files_url}}/{{$data.screenshot1}}" alt="{{$data.title}}" width="{{$lang.models.thumb_size|geomsize:'width'}}" height="{{$lang.models.thumb_size|geomsize:'height'}}">
			{{else}}
				<img src="{{$config.statics_url}}/static/images/no-avatar-model.jpg" alt="{{$item.title}}">
				<span class="no-avatar">
					<span>{{$lang.models.info_no_image}}</span>
				</span>
			{{/if}}
		</div>
		<div class="media-info__wrap">
			<div class="box media-info">
				<div class="media-info__row">
					<div class="media-box media-box__large full-width">
						<div class="media-box__list">
							<div class="media-box__row">
								<div class="media-box__left">
									{{if $data.birth_date=='0000-00-00' && $data.age>0}}
										{{$lang.models.info_age}}: <span>{{$data.age}}</span>
									{{else}}
										{{$lang.models.info_birthday}}: <span>{{if $data.birth_date!='0000-00-00'}}{{$data.birth_date|date_format:"%d %B, %Y"}} ({{$lang.models.info_birthday_age|replace:"%1%":$data.age}}){{else}}{{$lang.models.info_na}}{{/if}}</span>
									{{/if}}
								</div>
								<div class="media-box__right">
									{{$lang.models.info_hair}}: <span>{{$lang.models.info_hair_values[$data.hair_id]|default:$lang.models.info_na}}</span>
								</div>
							</div>
							<div class="media-box__row">
								<div class="media-box__left">
									{{$lang.models.info_country}}: <span>{{$data.country|default:$lang.models.info_na}}</span>
								</div>
								<div class="media-box__right">
									{{$lang.models.info_eyes}}: <span>{{$lang.models.info_eyes_values[$data.eye_color_id]|default:$lang.models.info_na}}</span>
								</div>
							</div>
							<div class="media-box__row">
								<div class="media-box__left">
									{{$lang.models.info_city}}: <span>{{$data.city|default:$lang.models.info_na}}</span>
								</div>
								<div class="media-box__right">
									{{$lang.models.info_height}}: <span>{{$data.height|default:$lang.models.info_na}}</span>
								</div>
							</div>
							<div class="media-box__row">
								<div class="media-box__left">
									{{$lang.models.info_ethnicity}}: <span>{{$data.custom2|default:$lang.models.info_na}}</span>
								</div>
								<div class="media-box__right">
									{{$lang.models.info_weight}}: <span>{{$data.weight|default:$lang.models.info_na}}</span>
								</div>
							</div>
							<div class="media-box__row">
								<div class="media-box__left">
									{{$lang.models.info_profession}}: <span>{{$data.custom3|default:$lang.models.info_na}}</span>
								</div>
								<div class="media-box__right">
									{{$lang.models.info_measurements}}: <span>{{$data.measurements|default:$lang.models.info_na}}</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				{{if count($data.tags)>0}}
					<div class="media-info__row">
						<span class="media-info__label">{{$lang.models.info_tags}}:</span>
						<div class="media-info__buttons">
							{{foreach item="item" from=$data.tags}}
								<a class="btn" {{if $lang.enable_tags=='true'}}href="{{$lang.urls.content_by_tag|replace:"%DIR%":$item.tag_dir|replace:"%ID%":$item.tag_id}}"{{/if}}>{{$item.tag}}</a>
							{{/foreach}}
						</div>
					</div>
				{{/if}}
				{{if count($data.categories)>0}}
					<div class="media-info__row">
						<span class="media-info__label">{{$lang.models.info_categories}}:</span>
						<div class="media-info__buttons">
							{{foreach item="item" from=$data.categories}}
								<a class="btn" {{if $lang.enable_categories=='true'}}href="{{$lang.urls.content_by_category|replace:"%DIR%":$item.dir|replace:"%ID%":$item.category_id}}"{{/if}}>{{$item.title}}</a>
							{{/foreach}}
						</div>
					</div>
				{{/if}}
				<div class="media-info__row media-info__row--unstyled media-info__row--fix-height">
					<span class="media-info__label">{{$lang.models.info_description}}:</span>
					<span class="media-info__desc">{{$data.description|default:$lang.models.info_no_description}}</span>
				</div>
			</div>
			<div class="info-bar cfx">
				<div class="vote-block info-bar__cell" data-action="rating">
					{{assign var="model_rating" value="`$data.rating/5*100`"}}
					{{if $model_rating>100}}{{assign var="model_rating" value="100"}}{{/if}}

					{{assign var="can_rate" value="0"}}
					{{if $lang.features_access.rate=='all' || ($lang.features_access.rate=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
						{{assign var="can_rate" value="1"}}
					{{/if}}

					<a class="btn__vote btn__like" title="{{$lang.models.info_action_rate_like}}" {{if $can_rate==1}}href="#like" data-model-id="{{$data.model_id}}" data-vote="5"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
						<i class="icon-like-shape-15"></i>
					</a>

					<div id="voters" class="rotated thumb-spot__rating">
						<span data-rating="percent">{{$model_rating|string_format:"%d"}}%</span>
					</div>

					<a class="btn__vote btn__dislike" title="{{$lang.models.info_action_rate_dislike}}" {{if $can_rate==1}}href="#dislike" data-model-id="{{$data.model_id}}" data-vote="0"{{else}}href="{{if $smarty.session.status_id>1}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup"{{/if}}>
						<i class="icon-dislike-shape-15"></i>
					</a>

					<span class="tooltip hidden" data-show="success">{{$lang.models.info_rating_message_success}}</span>
					<span class="tooltip hidden" data-show="error">{{$lang.models.info_rating_message_error}}</span>
				</div>
				<div class="media-data info-bar__cell">
					<ul class="media-data__list">
						{{assign var="avg_videos_rating" value="`$data.avg_videos_rating/5*100`"}}
						{{if $avg_videos_rating>100}}{{assign var="avg_videos_rating" value="100"}}{{/if}}
						<li>
							<span class="media-data__list-title">{{$lang.models.info_videos}}:</span>
							<strong  class="media-data__list-value">{{if $data.total_videos>0}}<a href="{{$lang.urls.videos_by_model|replace:"%DIR%":$data.dir|replace:"%ID%":$data.model_id}}">{{$data.total_videos}}</a>{{else}}{{$data.total_videos}}{{/if}} <span class="media-data__small-info">({{$avg_videos_rating|string_format:"%d"}}% <i class="icon-like-shape-15"></i>)</span></strong>
						</li>
						{{if $lang.enable_albums=='true'}}
							{{assign var="avg_albums_rating" value="`$data.avg_albums_rating/5*100`"}}
							{{if $avg_albums_rating>100}}{{assign var="avg_albums_rating" value="100"}}{{/if}}
							<li>
								<span class="media-data__list-title">{{$lang.models.info_albums}}:</span>
								<strong  class="media-data__list-value">{{if $data.total_albums>0}}<a href="{{$lang.urls.albums_by_model|replace:"%DIR%":$data.dir|replace:"%ID%":$data.model_id}}">{{$data.total_albums}}</a>{{else}}{{$data.total_albums}}{{/if}} <span class="media-data__small-info">({{$avg_albums_rating|string_format:"%d"}}% <i class="icon-like-shape-15"></i>)</span></strong>
							</li>
						{{/if}}
					</ul>
				</div>
				<div class="info-bar__buttons">
					<div class="info-bar__cell">
						{{assign var="can_favourite" value="0"}}
						{{if ($lang.features_access.favourite=='members' && $smarty.session.status_id>1) || $smarty.session.status_id==3}}
							{{assign var="can_favourite" value="1"}}
						{{/if}}
						{{if $can_favourite==1}}
							{{if $data.is_subscribed==1}}
								<a title="{{$lang.models.info_action_unsubscribe|replace:"%1%":$data.title}}" href="#unsubscribe" data-unsubscribe-to="model" data-id="{{$data.model_id}}" class="info-bar__button subscribed">
									<i class="icon-heart-shape-16"><span data-subscribers="count">{{$data.subscribers_count}}</span></i>
								</a>
							{{else}}
								<a title="{{$lang.models.info_action_subscribe|replace:"%1%":$data.title}}" href="#subscribe" data-subscribe-to="model" data-id="{{$data.model_id}}" class="info-bar__button">
									<i class="icon-heart-shape-16"><span data-subscribers="count">{{$data.subscribers_count}}</span></i>
								</a>
							{{/if}}
						{{else}}
							<a title="{{$lang.models.info_action_subscribe|replace:"%1%":$data.title}}" href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup" class="info-bar__button">
								<i class="icon-heart-shape-16"><span>{{$data.subscribers_count}}</span></i>
							</a>
						{{/if}}
					</div>
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
		</div>
	</div>
</div>