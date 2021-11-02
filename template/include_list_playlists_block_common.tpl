<div class="thumbs">
	<div class="container">
		{{if $list_playlists_hide_headline!='true'}}
			<div class="heading cfx">
				<h{{$list_playlists_header_level|default:"2"}} class="title">{{$list_playlists_title|mb_ucfirst}}{{if $nav.page_now>1}}{{$lang.common_list.paginated_postfix|replace:"%1%":$nav.page_now}}{{/if}}</h{{$list_playlists_header_level|default:"2"}}>
			</div>
		{{/if}}
		{{if $can_manage==1}}
		<form data-form="list" data-block-id="{{$block_uid}}" data-prev-url="{{$nav.previous}}">
			<div class="generic-error hidden"></div>
		{{/if}}
		{{if count($data)>0}}
			<div class="thumbs__list cfx">
				{{foreach item="item" from=$data}}
					<div class="thumb">
						<a {{if count($item.videos)>0}}href="{{$lang.urls.memberzone_my_playlist_view|replace:"%ID%":$item.playlist_id|replace:"%VIDEO%":$item.videos[0].video_id}}"{{/if}} title="{{$item.title}}">
							{{if count($item.videos)>0}}
								{{assign var="video_number" value=1}}
								{{foreach item="item_videos" from=$item.videos}}
									<img class="video{{$video_number}}" src="{{$item_videos.screen_url}}/{{$lang.videos.thumb_size}}/{{$item_videos.screen_main}}.jpg" alt="{{$item_videos.title}}" title="{{$item_videos.title}}" width="{{$lang.videos.thumb_size|geomsize:'width'}}" height="{{$lang.videos.thumb_size|geomsize:'height'}}"/>
									{{assign var="video_number" value=$video_number+1}}
								{{/foreach}}
							{{else}}
								<img src="{{$config.statics_url}}/static/images/no-avatar-playlist.jpg" alt="{{$item.title}}">
								<span class="no-avatar">
									<span>{{$lang.playlists.list_label_no_videos}}</span>
								</span>
							{{/if}}
							<div class="thumb__info">
								<div class="thumb-spot">
									<div class="thumb-spot__text">
										<h5 class="thumb-spot__title">
											{{if $lang.playlists.truncate_title_to>0}}
												{{$item.title|truncate:$lang.playlists.truncate_title_to:"...":true}}
											{{else}}
												{{$item.title}}
											{{/if}}
										</h5>
										<ul class="thumb-spot__data">
											<li>{{$item.last_content_date|date_format:$lang.global.date_format}}</li>
											<li><i class="icon-camera-shape-10"></i>{{$item.total_videos}}</li>
											{{assign var="playlist_views" value=$item.playlist_viewed|number_format:0:",":$lang.global.number_format_delimiter}}
											<li>{{$lang.playlists.list_label_views|replace:"%1%":$playlist_views}}</li>
										</ul>
									</div>
								</div>
							</div>
						</a>
						{{if $can_manage==1}}
							<label class="checkbox__fav-label"></label>
							<input type="checkbox" class="checkbox checkbox__fav" name="delete[]" data-action="select" value="{{$item.playlist_id}}" {{if $item.is_locked==1}}disabled{{/if}}>
							<div class="fav_overlay"></div>
						{{/if}}
					</div>
				{{/foreach}}
			</div>
		{{else}}
			<div class="empty-content">{{$list_playlists_empty_message|default:$lang.common_list.no_data}}</div>
		{{/if}}
		{{if $can_manage==1}}
			<div>
				<input type="hidden" name="function" value="get_block"/>
				<input type="hidden" name="block_id" value="{{$block_uid}}"/>
				<input type="hidden" name="action" value="delete_playlists"/>

				<input type="button" class="btn" value="{{$lang.playlists.list_action_select_all}}" data-action="select_all"/>
				<input type="button" class="btn" value="{{$lang.playlists.list_action_delete_selected}}" disabled data-mode="selection" data-action="delete_multi" data-confirm="{{$lang.playlists.list_action_delete_selected_confirm}}">
				<input type="button" class="btn" value="{{$lang.playlists.list_action_create_playlist}}" data-action="popup" data-href="{{$lang.urls.memberzone_create_playlist}}">
			</div>
		</form>
		{{/if}}
	</div>
</div>