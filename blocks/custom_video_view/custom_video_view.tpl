<div class="video-view">
	<h1>{{$data.title}}</h1>

	{{* player section begin *}}
	<div>
		{{if count($data)==0}}
			This video link is broken.
		{{elseif $data.status_id==5 || $data.status_id==2 || $data.status_id==3}}
			<div class="no-player">
				<span class="message">
					{{$data.delete_reason|default:"You are not allowed to watch this video."}}
				</span>
			</div>
		{{elseif $is_limit_over==1}}
			<div class="no-player">
				<img src="{{$flashvars.preview_url}}" width="{{$player_size[0]}}" height="{{$player_size[1]}}" alt="{{$data.title}}"/>
				<br/>
				<span class="message">
					{{if $smarty.session.user_id>0}}
						Your have exceeded your video watching limit.
					{{else}}
						Your have exceeded your video watching limit. Become a member to see more videos.
					{{/if}}
				</span>
			</div>
		{{elseif $data.can_watch==0}}
			<div class="no-player">
				<img src="{{$flashvars.preview_url}}" width="{{$player_size[0]}}" height="{{$player_size[1]}}" alt="{{$data.title}}"/>
				<br/>
				<span class="message">
					{{if $smarty.session.user_id<1}}
						Only members can access this video. Please log in or register.
					{{elseif $data.can_watch_option=='premium'}}
						{{if $data.tokens_required>0}}
							{{if $smarty.session.tokens_available<$data.tokens_required}}
								To have access to this video you must spend <em>{{$data.tokens_required}}</em> tokens.<br/><br/>
								Your current tokens balance is <em>{{$smarty.session.tokens_available}}</em> tokens.<br/>
								You need <em>{{$data.tokens_required-$smarty.session.tokens_available}}</em> more tokens.
							{{else}}
								To have access to this video you must spend <em>{{$data.tokens_required}}</em> tokens.<br/><br/>
								Your current tokens balance is <em>{{$smarty.session.tokens_available}}</em> tokens.<br/>
								Please confirm spending <em>{{$data.tokens_required}}</em> tokens on this video.<br/>
								<form action="{{$data.canonical_url}}" method="post" data-form="ajax">
									<span class="generic-error hidden"></span>
									<input type="hidden" name="action" value="purchase_video"/>
									<input type="hidden" name="video_id" value="{{$data.video_id}}">
									<input type="submit" class="submit" value="Spend {{$data.tokens_required}} tokens on this video">
								</form>
							{{/if}}
						{{else}}
							Only premium members can access this video. Please upgrade your profile to premium.
						{{/if}}
					{{elseif $data.can_watch_option=='friends'}}
						Only {{$data.user.username}}'s friends can access this video.
					{{else}}
						You are not allowed to watch this video.
					{{/if}}
				</span>
			</div>
		{{else}}
			{{if $data.load_type_id==3}}
				<div class="embed-wrap">
					{{$data.embed|smarty:nodefaults}}
				</div>
			{{elseif $flashvars.video_url==''}}
				<div class="no-player">
					<img src="{{$flashvars.preview_url}}" width="{{$player_size[0]}}" height="{{$player_size[1]}}" alt="{{$data.title}}"/>
					<br/>
					{{if $data.load_type_id==5}}
						<a class="btn-play" href="{{$data.pseudo_url}}" target="_blank">Play</a>
					{{else}}
						<span class="message">
							You are not allowed to watch this video.
						</span>
					{{/if}}
				</div>
			{{else}}
				<div class="player" style="width: {{$player_size[0]}}px; height: {{$player_size[1]}}px">
					<div id="kt_player"></div>
				</div>
				<script type="text/javascript" src="{{$config.project_url}}/player/kt_player.js?v={{$config.project_version}}"></script>
				<script type="text/javascript">
					/* <![CDATA[ */
					{{if $data.is_private!=1 && $data.is_private!=2}}
					function getEmbed(width, height) {
						if (width && height) {
							return '<iframe width="' + width + '" height="' + height + '" src="{{$config.project_url}}/embed/{{$data.video_id}}" frameborder="0" allowfullscreen></iframe>';
						}
						return '<iframe width="{{$player_size_embed[0]}}" height="{{$player_size_embed[1]}}" src="{{$config.project_url}}/embed/{{$data.video_id}}" frameborder="0" allowfullscreen></iframe>';
					}
					{{/if}}

					var flashvars = {
					{{foreach name="data" key="key" item="item" from=$flashvars}}
						{{$key}}: '{{$item|replace:"'":"\'"}}'{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
					};
					{{if $data.is_private==1 || $data.is_private==2}}
						flashvars['embed'] = '0';
					{{/if}}
					kt_player('kt_player', '{{$config.project_url}}/player/kt_player.swf?v={{$config.project_version}}', '100%', '100%', flashvars);
					/* ]]> */
				</script>
			{{/if}}
		{{/if}}
	</div>
	{{* player section end *}}

	{{* info section begin *}}
	<h2>Video info</h2>
	<div>
		<p>
			<label>Video ID</label>
			<span>{{$data.video_id}}</span>
		</p>
		<p>
			<label>Directory</label>
			<span>{{$data.dir}}</span>
		</p>
		<p>
			<label>Description</label>
			<span>{{$data.description}}</span>
		</p>
		<p>
			<label>Duration</label>
			<span>{{$data.duration_array.text}}</span>
		</p>
		<p>
			<label>Page canonical</label>
			<span>{{$data.canonical_url}}</span>
		</p>
		<p>
			<label>Published on</label>
			<span>{{$data.post_date|date_format:"%d %B, %Y"}}</span>
		</p>
		<p>
			<label>Release year</label>
			<span>{{$data.release_year}}</span>
		</p>
		<p>
			<label>Status</label>
			<span>{{if $data.status_id==5}}Deleted{{elseif $data.status_id==3}}In process{{elseif $data.status_id==2}}Error{{elseif $data.status_id==1}}Active{{elseif $data.status_id==0}}Disabled{{/if}}</span>
		</p>
		<p>
			<label>Type</label>
			<span>{{if $data.is_private==0}}Public{{elseif $data.is_private==1}}Private{{elseif $data.is_private==2}}Premium{{/if}}</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.video_viewed}} views, {{$data.video_viewed_unique}} unique views, {{$data.comments_count}} comments, in {{$data.favourites_count}} favourites, purchased {{$data.purchases_count}} times</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like {{if $is_limit_over==1 || $data.can_watch==0}}disabled{{/if}}" data-video-id="{{$data.video_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike {{if $is_limit_over==1 || $data.can_watch==0}}disabled{{/if}}" data-video-id="{{$data.video_id}}" data-vote="0">Dislike</a>
			</span>
		</p>
		<p>
			<label>Source size</label>
			<span>{{$data.file_dimensions.0}}x{{$data.file_dimensions.1}}</span>
		</p>
		<p>
			<label>Uploaded by</label>
			<span>
				<a href="{{$config.project_url}}/members/{{$data.user_id}}/">{{$data.user.display_name}}</a> {{if $data.user.is_subscribed==1}}(subscribed){{/if}}
				{{if $data.user.avatar_url}}<img src="{{$data.user.avatar_url}}" width="30" height="30"/>{{/if}}
			</span>
		</p>
		<p>
			<label>Custom 1</label>
			<span>{{$data.custom1}}</span>
		</p>
		<p>
			<label>Is HD?</label>
			<span>{{if $data.is_hd==1}}Yes{{else}}No{{/if}}</span>
		</p>
		{{if $data.tokens_required>0}}
			<p>
				<label>Tokens price</label>
				<span>{{$data.tokens_required}} {{if $data.tokens_required_period>0}}(purchase expires in {{$data.tokens_required_period}} days){{/if}}</span>
			</p>
			<p>
				<label>Is purchased?</label>
				<span>{{if $data.is_purchased_video==1}}Yes{{else}}No{{/if}}</span>
			</p>
		{{/if}}
		{{if count($data.flags)>0}}
			<p>
				<label>Flags</label>
				<span>
					{{foreach name="data" item="item" key="key" from=$data.flags}}
						{{$key}}: {{$item}}{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
		{{if count($data.tags)>0}}
			<p>
				<label>Tags</label>
				<span>
					{{foreach name="data" item="item" from=$data.tags}}
						<a href="{{$config.project_url}}/tags/{{$item.dir}}/">{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
			<p>
				<label>Tags as string</label>
				<span>{{$data.tags_as_string}}</span>
			</p>
		{{/if}}
		{{if count($data.categories)>0}}
			<p>
				<label>Categories</label>
				<span>
					{{foreach name="data" item="item" from=$data.categories}}
						<a href="{{$config.project_url}}/categories/{{$item.dir}}/">{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
			<p>
				<label>Categories as string</label>
				<span>{{$data.categories_as_string}}</span>
			</p>
		{{/if}}
		{{if count($data.models)>0}}
			<p>
				<label>Models</label>
				<span>
					{{foreach name="data" item="item" from=$data.models}}
						<a {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}}>{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
			<p>
				<label>Models as string</label>
				<span>{{$data.models_as_string}}</span>
			</p>
		{{/if}}
		{{if $data.content_source.content_source_id>0}}
			<p>
				<label>Sponsor</label>
				<span>
					<a {{if $data.content_source.view_page_url!=''}}href="{{$data.content_source.view_page_url}}"{{/if}}>{{$data.content_source.title}}</a> {{if $data.content_source.is_subscribed==1}}(subscribed){{/if}}
					{{if $data.content_source.screenshot1}}<img src="{{$data.content_source.base_files_url}}/{{$data.content_source.screenshot1}}" width="30" height="30"/>{{/if}}
				</span>
			</p>
		{{/if}}
		{{if $data.dvd.dvd_id>0}}
			<p>
				<label>Channel</label>
				<span>
					<a {{if $data.dvd.view_page_url!=''}}href="{{$data.dvd.view_page_url}}"{{/if}}>{{$data.dvd.title}}</a> {{if $data.dvd.is_subscribed==1}}(subscribed){{/if}}
					{{if $data.dvd.cover1_front}}<img src="{{$data.dvd.base_files_url}}/{{$data.dvd.cover1_front}}" width="30" height="30"/>{{/if}}
				</span>
			</p>
		{{/if}}
		{{if $is_limit_over==0 && count($data.download_formats)>0}}
			<p>
				<label>Download files</label>
				<span>
					{{foreach name="data" item="item" from=$data.download_formats}}
						<a href="{{$item.file_url}}" data-attach-session="{{$session_name}}">{{$item.title}} ({{$item.file_size_string}})</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
		<p>
			<label>Screenshots</label>
			<span>
				{{assign var="thumb_format_size" value="320x180"}}
				{{section name="data" start="0" loop=$data.screen_amount}}
					<img src="{{$data.screen_url}}/{{$thumb_format_size}}/{{$smarty.section.data.index+1}}.jpg"/>
				{{/section}}
			</span>
		</p>
		{{if is_array($next_video)}}
			<p>
				<label>Next video</label>
				<span>
					<a href="{{$next_video.view_page_url}}">{{$next_video.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_video)}}
			<p>
				<label>Previous video</label>
				<span>
					<a href="{{$previous_video.view_page_url}}">{{$previous_video.title}}</a>
				</span>
			</p>
		{{/if}}
	</div>
	{{* info section end *}}

	{{* flagging form section begin *}}
	<h2>Flagging form</h2>
	<div class="block-flagging">
		<form method="post">
			<div class="generic-error hidden"></div>
			<div class="success hidden">Thank you! We appreciate your help.</div>

			<div class="row">
				<label>Report this video as</label>
				<span>
					<input type="radio" id="flag_inappropriate_video" name="flag_id" value="flag_inappropriate_video" class="radio">
					<label for="flag_inappropriate_video">Inappropriate</label>
				</span>
				<span>
					<input type="radio" id="flag_error_video" name="flag_id" value="flag_error_video" class="radio">
					<label for="flag_error_video">Error (no video, no sound)</label>
				</span>
				<span>
					<input type="radio" id="flag_copyrighted_video" name="flag_id" value="flag_copyrighted_video" class="radio">
					<label for="flag_copyrighted_video">Copyrighted material</label>
				</span>
				<span>
					<input type="radio" id="flag_other_video" name="flag_id" value="flag_other_video" class="radio" checked>
					<label for="flag_other_video">Other</label>
				</span>
			</div>

			<div class="row">
				<label for="flag_message">Reason (optional)</label>
				<textarea id="flag_message" name="flag_message" rows="3" class="textarea"></textarea>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="flag"/>
				<input type="hidden" name="video_id" value="{{$data.video_id}}">
				<input type="submit" value="Send"/>
			</div>
		</form>
	</div>
	{{* flagging form section end *}}

	{{* add to favourites section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Favourites management</h2>
		<ul class="btn-favourites">
			{{if in_array(0, $data.favourite_types)}}
				<li><a href="#delete_from_fav" data-video-id="{{$data.video_id}}" data-fav-type="0" class="delete">Delete from favourites</a></li>
			{{else}}
				<li><a href="#add_to_fav" data-video-id="{{$data.video_id}}" data-fav-type="0">Add to favourites</a></li>
			{{/if}}
			{{if in_array(1, $data.favourite_types)}}
				<li><a href="#delete_from_fav" data-video-id="{{$data.video_id}}" data-fav-type="1" class="delete">Delete from watching list</a></li>
			{{else}}
				<li><a href="#add_to_fav" data-video-id="{{$data.video_id}}" data-fav-type="1">Watch later</a></li>
			{{/if}}
			{{foreach item="item" from=$smarty.session.playlists}}
				{{if in_array($item.playlist_id, $data.favourite_playlists)}}
					<li><a href="#delete_from_playlist" data-video-id="{{$data.video_id}}" data-fav-type="10" data-playlist-id="{{$item.playlist_id}}" class="delete">Delete from <em>{{$item.title}}</em></a></li>
				{{else}}
					<li><a href="#add_to_playlist" data-video-id="{{$data.video_id}}" data-fav-type="10" data-playlist-id="{{$item.playlist_id}}">Add to <em>{{$item.title}}</em></a></li>
				{{/if}}
			{{/foreach}}
		</ul>
	{{/if}}
	{{* add to favourites section end *}}
</div>