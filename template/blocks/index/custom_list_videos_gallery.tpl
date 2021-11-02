<div class="main-gallery-holder">
	<div class="main-gallery-inner">
		<div class="main-gallery">
			<div class="container" data-slider-container="index">
				<div class="flexslider auto" data-slider="index">
					<ul class="slides">
						{{foreach name="data" item="item" from=$data}}
							{{if $item.is_private==2}}
								{{assign var="format_postfix" value=$lang.index.gallery_format_premium}}
							{{else}}
								{{assign var="format_postfix" value=$lang.index.gallery_format_standard}}
							{{/if}}

							{{if $smarty.session.user_id==0}}
								{{assign var="format_postfix" value="_trailer.mp4"}}
							{{/if}}

							<li {{if $format_postfix!='' && $item.formats[$format_postfix].file_path}}data-video-url="{{$config.project_url}}/get_file/{{$item.server_group_id}}/{{$item.formats[$format_postfix].file_path}}/"{{/if}}>
								<div class="gallery-player">
									<img {{if $smarty.foreach.data.first}}src{{else}}data-src{{/if}}="{{$item.screen_url}}/preview.jpg" alt="{{$item.title}}"/>
									<div class="thumb-spot">
										{{assign var="video_rating" value="`$item.rating/5*100`"}}
										{{if $video_rating>100}}{{assign var="video_rating" value="100"}}{{/if}}
										<div class="thumb-spot__rating rotated"><span>{{$video_rating|string_format:"%d"}}%</span></div>
										<div class="thumb-spot__text">
											<h5 class="thumb-spot__title">
												{{if $lang.videos.truncate_title_to>0}}
													{{$item.title|truncate:$lang.videos.truncate_title_to:"...":true}}
												{{else}}
													{{$item.title}}
												{{/if}}
											</h5>
											<ul class="thumb-spot__data">
												<li>
													{{if $smarty.session.user_id==0}}
														{{$item.formats[$format_postfix].duration_string}}
													{{else}}
														{{$item.duration_array.text}}
													{{/if}}
												</li>
												<li>{{$item.post_date|date_format:$lang.global.date_format}}</li>
												{{assign var="video_views" value=$item.video_viewed|number_format:0:",":$lang.global.number_format_delimiter}}
												<li>{{$lang.videos.list_label_views|replace:"%1%":$video_views}}</li>
											</ul>
										</div>
									</div>
									<a class="btn-play rotated" href="{{$item.view_page_url}}" data-action="play"><i class="icon-play-shape-1"></i></a>
								</div>
							</li>
						{{/foreach}}
					</ul>
				</div>
				<video preload="none" class="slider__video" id="js-slide-video"></video>
			</div>
		</div>
	</div>
	<img class="main-gallery-loader" src="static/images/spin.gif" alt="">
</div>