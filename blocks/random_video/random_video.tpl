<div class="random-video">
	<h1>{{$data.title}}</h1>

	{{* player section begin *}}
	<div>
		{{if $data.load_type_id==3}}
			<div class="embed-wrap">
				{{$data.embed|smarty:nodefaults}}
			</div>
		{{elseif $data.load_type_id==5}}
			<div class="no-player">
				<img src="{{$data.preview_url}}" alt="{{$data.title}}"/>
				<br/>
				<a class="btn-play" href="{{$data.pseudo_url}}" target="_blank">Play</a>
			</div>
		{{elseif $data.load_type_id==2}}
			<video src="{{$data.file_url}}" poster="{{$data.preview_url}}" width="{{$data.file_dimensions[0]}}" height="{{$data.file_dimensions[1]}}"></video>
		{{else}}
			{{assign var="display_postfix" value=".mp4"}}
			{{if $data.formats[$display_postfix].postfix}}
				<video controls src="{{$config.project_url}}/get_file/{{$data.server_group_id}}/{{$data.formats[$display_postfix].file_path}}/" poster="{{$data.formats[$display_postfix].preview_url}}" width="{{$data.formats[$display_postfix].dimensions[0]}}" height="{{$data.formats[$display_postfix].dimensions[1]}}"></video>
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
				<a href="#like" class="rate-like" data-video-id="{{$data.video_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike" data-video-id="{{$data.video_id}}" data-vote="0">Dislike</a>
			</span>
		</p>
		<p>
			<label>Source size</label>
			<span>{{$data.file_dimensions.0}}x{{$data.file_dimensions.1}}</span>
		</p>
		<p>
			<label>Uploaded by</label>
			<span>
				<a href="{{$config.project_url}}/members/{{$data.user_id}}/">{{$data.user.display_name}}</a>
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
		<p>
			<label>Screenshots</label>
			<span>
				{{assign var="thumb_format_size" value="320x180"}}
				{{section name="data" start="0" loop=$data.screen_amount}}
					<img src="{{$data.screen_url}}/{{$thumb_format_size}}/{{$smarty.section.data.index+1}}.jpg"/>
				{{/section}}
			</span>
		</p>
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
</div>