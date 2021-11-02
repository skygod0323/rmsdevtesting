{{if count($data)>0}}
	<div class="media-carousel">
		{{assign var="active_slide_index" value="1"}}
		{{foreach item="item" name="indexer" from=$data}}
			{{if $item.video_id==$smarty.get.id}}
				{{assign var="active_slide_index" value=$smarty.foreach.indexer.iteration}}
			{{/if}}
		{{/foreach}}
		<div class="flexslider auto" data-playlist="true" data-playlist-thumb-width="185" data-playlist-thumb-margin="10" data-playlist-id="{{$playlist_id}}" data-playlist-active-slide="{{$active_slide_index}}" data-playlist-active-video-id="{{$smarty.get.id}}">
			<ul class="slides">
				{{foreach item="item" from=$data}}
					<li class="media-carousel__item item--bordered">
						{{assign var="tag_name" value="a"}}
						{{if $item.video_id==$smarty.get.id}}
							{{assign var="tag_name" value="span"}}
						{{/if}}
						<{{$tag_name}} {{if $item.video_id!=$smarty.get.id}}href="{{$lang.urls.memberzone_my_playlist_view|replace:"%ID%":$playlist_id|replace:"%VIDEO%":$item.video_id}}"{{/if}} class="item" data-playlist-video-id="{{$item.video_id}}">
							<img src="{{$item.screen_url}}/{{$lang.videos.thumb_size}}/{{$item.screen_main}}.jpg" alt="{{$item.title}}" {{if $lang.enable_thumb_scrolling=='true'}}data-cnt="{{$item.screen_amount}}"{{/if}} width="{{$lang.videos.thumb_size|geomsize:'width'}}" height="{{$lang.videos.thumb_size|geomsize:'height'}}"/>
							<span class="thumb__rhomb thumb--left-bottom rotated">
								<span>{{$item.duration_array.text}}</span>
							</span>
							{{if $item.video_id==$smarty.get.id}}
								<span class="playing">
									<span class="playing__holder">
										<span>{{$lang.videos.list_label_playing}}</span>
										<i class="playing__progress"></i>
										<span class="playing__holder-btn" data-playlist-action="delete" data-confirm="{{$lang.videos.list_action_delete_confirm_favourites|replace:"%1%":$item.title}}">{{$lang.videos.list_action_delete}}</span>
									</span>
								</span>
							{{/if}}
						</{{$tag_name}}>
					</li>
				{{/foreach}}
			</ul>
		</div>
	</div>
{{/if}}