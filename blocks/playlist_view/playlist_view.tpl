<div class="playlist-view">
	<h1>{{$data.title}}</h1>

	{{* videos section begin *}}
	{{assign var="thumb_format_size" value="320x180"}}
	<div>
		{{foreach item="item" from=$data.videos}}
			<a href="{{$item.view_page_url}}">
				<img alt="{{$item.title}}" src="{{$item.screen_url}}/{{$thumb_format_size}}/{{$item.screen_main}}.jpg">
			</a>
		{{/foreach}}

		{{* include pagination here *}}
	</div>
	{{* videos section end *}}

	{{* info section begin *}}
	<h2>Playlist info</h2>
	<div>
		<p>
			<label>Playlist ID</label>
			<span>{{$data.playlist_id}}</span>
		</p>
		<p>
			<label>Directory</label>
			<span>{{$data.dir}}</span>
		</p>
		<p>
			<label>Description</label>
			<span>{{$data.description}}</span>
		</p>
		{{if $data.canonical_url}}
			<p>
				<label>Page canonical</label>
				<span>{{$data.canonical_url}}</span>
			</p>
		{{/if}}
		<p>
			<label>Added</label>
			<span>{{$data.added_date|date_format:"%d %B, %Y"}}</span>
		</p>
		<p>
			<label>Last updated</label>
			<span>{{if $data.last_content_date!='0000-00-00'}}{{$data.last_content_date|date_format:"%d %B, %Y"}}{{/if}}</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.playlist_viewed}} views, {{$data.comments_count}} comments, {{$data.subscribers_count}} subscribers</span>
		</p>
		<p>
			<label>Content</label>
			<span>{{$data.total_videos}} total videos</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like {{if $is_limit_over==1}}disabled{{/if}}" data-playlist-id="{{$data.playlist_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike {{if $is_limit_over==1}}disabled{{/if}}" data-playlist-id="{{$data.playlist_id}}" data-vote="0">Dislike</a>
			</span>
		</p>
		<p>
			<label>Created by</label>
			<span>
				<a href="{{$config.project_url}}/members/{{$data.user_id}}/">{{$data.user.display_name}}</a> {{if $data.user.is_subscribed==1}}(subscribed){{/if}}
				{{if $data.user.avatar_url}}<img src="{{$data.user.avatar_url}}" width="30" height="30"/>{{/if}}
			</span>
		</p>
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
		{{if is_array($next_playlist)}}
			<p>
				<label>Next playlist</label>
				<span>
					<a {{if $next_playlist.view_page_url}}href="{{$next_playlist.view_page_url}}"{{/if}}>{{$next_playlist.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_playlist)}}
			<p>
				<label>Previous playlist</label>
				<span>
					<a {{if $previous_playlist.view_page_url}}href="{{$previous_playlist.view_page_url}}"{{/if}}>{{$previous_playlist.title}}</a>
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
				<label>Report this playlist as</label>
				<span>
					<input type="radio" id="flag_other_playlist" name="flag_id" value="flag_other_playlist" class="radio" checked>
					<label for="flag_other_playlist">Other</label>
				</span>
			</div>

			<div class="row">
				<label for="flag_message">Reason (optional)</label>
				<textarea id="flag_message" name="flag_message" rows="3" class="textarea"></textarea>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="flag"/>
				<input type="hidden" name="playlist_id" value="{{$data.playlist_id}}">
				<input type="submit" value="Send"/>
			</div>
		</form>
	</div>
	{{* flagging form section end *}}

	{{* subscribe section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Subscription management</h2>
		{{if $data.is_subscribed==1}}
			<a href="#unsubscribe" data-id="{{$data.playlist_id}}" data-unsubscribe-to="playlist">Unsubscribe</a>
		{{else}}
			<a href="#subscribe" data-id="{{$data.playlist_id}}" data-subscribe-to="playlist">Subscribe</a>
		{{/if}}
	{{/if}}
	{{* subscribe section end *}}
</div>