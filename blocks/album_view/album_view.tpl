<div class="album-view">
	<h1>{{$data.title}}</h1>

	{{* images section begin *}}
	{{assign var="thumb_format_size" value="200x150"}}
	{{assign var="big_format_size" value="source"}}
	<div>
		<div class="images">
			{{if count($data)==0}}
				This album link is broken.
			{{elseif $data.status_id==5 || $data.status_id==2 || $data.status_id==3}}
				<span class="message">
					{{$data.delete_reason|default:"You are not allowed to watch this album."}}
				</span>
			{{elseif $is_limit_over==1}}
				{{foreach item="item" from=$data.images}}
					<img alt="{{$item.title|default:$data.title}}" src="{{$item.formats[$thumb_format_size].direct_url}}" width="{{$item.formats[$thumb_format_size].dimensions.0}}" height="{{$item.formats[$thumb_format_size].dimensions.1}}">
				{{/foreach}}
				<br/>
				<span class="message">
					{{if $smarty.session.user_id>0}}
						Your have exceeded your album watching limit.
					{{else}}
						Your have exceeded your album watching limit. Become a member to see more albums.
					{{/if}}
				</span>
			{{elseif $data.can_watch==0}}
				{{foreach item="item" from=$data.images}}
					<img alt="{{$item.title|default:$data.title}}" src="{{$item.formats[$thumb_format_size].direct_url}}" width="{{$item.formats[$thumb_format_size].dimensions.0}}" height="{{$item.formats[$thumb_format_size].dimensions.1}}">
				{{/foreach}}
				<br/>
				<span class="message">
					{{if $smarty.session.user_id<1}}
						Only members can access this album. Please log in or register.
					{{elseif $data.can_watch_option=='premium'}}
						{{if $data.tokens_required>0}}
							{{if $smarty.session.tokens_available<$data.tokens_required}}
								To have access to this album you must spend <em>{{$data.tokens_required}}</em> tokens.<br/><br/>
								Your current tokens balance is <em>{{$smarty.session.tokens_available}}</em> tokens.<br/>
								You need <em>{{$data.tokens_required-$smarty.session.tokens_available}}</em> more tokens.
							{{else}}
								To have access to this album you must spend <em>{{$data.tokens_required}}</em> tokens.<br/><br/>
								Your current tokens balance is <em>{{$smarty.session.tokens_available}}</em> tokens.<br/>
								Please confirm spending <em>{{$data.tokens_required}}</em> tokens on this album.<br/>
								<form action="{{$data.canonical_url}}" method="post" data-form="ajax">
									<span class="generic-error hidden"></span>
									<input type="hidden" name="action" value="purchase_album"/>
									<input type="hidden" name="album_id" value="{{$data.album_id}}">
									<input type="submit" class="submit" value="Spend {{$data.tokens_required}} tokens on this album">
								</form>
							{{/if}}
						{{else}}
							Only premium members can access this album. Please upgrade your profile to premium.
						{{/if}}
					{{elseif $data.can_watch_option=='friends'}}
						Only {{$data.user.username}}'s friends can access this album.
					{{else}}
						You are not allowed to watch this album.
					{{/if}}
				</span>
			{{else}}
				{{foreach item="item" from=$data.images}}
					<a href="{{$item.formats[$big_format_size].protected_url}}">
						<img alt="{{$item.title|default:$data.title}}" src="{{$item.formats[$thumb_format_size].direct_url}}" width="{{$item.formats[$thumb_format_size].dimensions.0}}" height="{{$item.formats[$thumb_format_size].dimensions.1}}">
					</a>
				{{/foreach}}
			{{/if}}
		</div>
	</div>
	{{* images section end *}}

	{{* info section begin *}}
	<h2>Album info</h2>
	<div>
		<p>
			<label>Album ID</label>
			<span>{{$data.album_id}}</span>
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
			<label>Images count</label>
			<span>{{$data.photos_amount}}</span>
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
			<label>Status</label>
			<span>{{if $data.status_id==5}}Deleted{{elseif $data.status_id==3}}In process{{elseif $data.status_id==2}}Error{{elseif $data.status_id==1}}Active{{elseif $data.status_id==0}}Disabled{{/if}}</span>
		</p>
		<p>
			<label>Type</label>
			<span>{{if $data.is_private==0}}Public{{elseif $data.is_private==1}}Private{{elseif $data.is_private==2}}Premium{{/if}}</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.album_viewed}} views, {{$data.album_viewed_unique}} unique views, {{$data.comments_count}} comments, in {{$data.favourites_count}} favourites, purchased {{$data.purchases_count}} times</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like {{if $is_limit_over==1 || $data.can_watch==0}}disabled{{/if}}" data-album-id="{{$data.album_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike {{if $is_limit_over==1 || $data.can_watch==0}}disabled{{/if}}" data-album-id="{{$data.album_id}}" data-vote="0">Dislike</a>
			</span>
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
		{{if $data.tokens_required>0}}
			<p>
				<label>Tokens price</label>
				<span>{{$data.tokens_required}} {{if $data.tokens_required_period>0}}(purchase expires in {{$data.tokens_required_period}} days){{/if}}</span>
			</p>
			<p>
				<label>Is purchased?</label>
				<span>{{if $data.is_purchased_album==1}}Yes{{else}}No{{/if}}</span>
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
		{{if count($data.zip_files)>0}}
			<p>
				<label>Download ZIP files</label>
				<span>
					{{foreach name="data" item="item" from=$data.zip_files}}
						<a href="{{$item.file_url}}" data-attach-session="{{$session_name}}">{{$item.size}} ({{$item.file_size_string}})</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
		{{/if}}
		{{if is_array($next_album)}}
			<p>
				<label>Next album</label>
				<span>
					<a href="{{$next_album.view_page_url}}">{{$next_album.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_album)}}
			<p>
				<label>Previous album</label>
				<span>
					<a href="{{$previous_album.view_page_url}}">{{$previous_album.title}}</a>
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
				<label>Report this album as</label>
					<span>
						<input type="radio" id="flag_inappropriate_album" name="flag_id" value="flag_inappropriate_album" class="radio">
						<label for="flag_inappropriate_album">Inappropriate</label>
					</span>
					<span>
						<input type="radio" id="flag_error_album" name="flag_id" value="flag_error_album" class="radio">
						<label for="flag_error_album">Error (no images)</label>
					</span>
					<span>
						<input type="radio" id="flag_copyrighted_album" name="flag_id" value="flag_copyrighted_album" class="radio">
						<label for="flag_copyrighted_album">Copyrighted material</label>
					</span>
					<span>
						<input type="radio" id="flag_other_album" name="flag_id" value="flag_other_album" class="radio" checked>
						<label for="flag_other_album">Other</label>
					</span>
			</div>

			<div class="row">
				<label for="flag_message">Reason (optional)</label>
				<textarea id="flag_message" name="flag_message" rows="3" class="textarea"></textarea>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="flag"/>
				<input type="hidden" name="album_id" value="{{$data.album_id}}">
				<input type="submit" value="Send"/>
			</div>
		</form>
	</div>
	{{* flagging form section end *}}

	{{* add to favourites section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Favourites management</h2>
		<ul class="btn-favourites">
			{{if is_array($data.favourite_types) && in_array(0, $data.favourite_types)}}
				<li><a href="#delete_from_fav" data-album-id="{{$data.album_id}}" data-fav-type="0" class="delete">Delete from favourites</a></li>
			{{else}}
				<li><a href="#add_to_fav" data-album-id="{{$data.album_id}}" data-fav-type="0">Add to favourites</a></li>
			{{/if}}
			{{if is_array($data.favourite_types) && in_array(1, $data.favourite_types)}}
				<li><a href="#delete_from_fav" data-album-id="{{$data.album_id}}" data-fav-type="1" class="delete">Delete from watching list</a></li>
			{{else}}
				<li><a href="#add_to_fav" data-album-id="{{$data.album_id}}" data-fav-type="1">Watch later</a></li>
			{{/if}}
		</ul>
	{{/if}}
	{{* add to favourites section end *}}
</div>