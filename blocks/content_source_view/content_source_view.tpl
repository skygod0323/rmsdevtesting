<div class="content-source-view">
	<h1>{{$data.title}}</h1>

	{{* info section begin *}}
	<h2>Content source info</h2>
	<div>
		<p>
			<label>Content source ID</label>
			<span>{{$data.content_source_id}}</span>
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
			<label>URL</label>
			<span>{{$data.url}}</span>
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
			<label>Screenshot 1</label>
			<span>{{if $data.screenshot1}}{{$data.base_files_url}}/{{$data.screenshot1}}{{/if}}</span>
		</p>
		<p>
			<label>Screenshot 2</label>
			<span>{{if $data.screenshot2}}{{$data.base_files_url}}/{{$data.screenshot2}}{{/if}}</span>
		</p>
		<p>
			<label>Rank</label>
			<span>{{$data.rank}} (was {{$data.last_rank}})</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.cs_viewed}} views, {{$data.comments_count}} comments, {{$data.subscribers_count}} subscribers</span>
		</p>
		<p>
			<label>Content</label>
			<span>{{$data.total_videos}} total videos, {{$data.today_videos}} videos today, {{$data.total_albums}} total albums, {{$data.today_albums}} albums today, {{$data.total_photos}} total photos</span>
		</p>
		<p>
			<label>Content stats</label>
			<span>average video rating: {{$data.avg_videos_rating/5*100}}%, total video views: {{$data.avg_videos_popularity*$data.total_videos|intval}}, average album rating: {{$data.avg_albums_rating/5*100}}%, total album views: {{$data.avg_albums_popularity*$data.total_albums|intval}}</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like" data-cs-id="{{$data.content_source_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike" data-cs-id="{{$data.content_source_id}}" data-vote="0">Dislike</a>
			</span>
		</p>
		<p>
			<label>Custom 1</label>
			<span>{{$data.custom1}}</span>
		</p>
		<p>
			<label>Custom file 1</label>
			<span>{{if $data.custom_file1}}{{$data.base_files_url}}/{{$data.custom_file1}}{{/if}}</span>
		</p>
		{{if $data.content_source_group.content_source_group_id>0}}
			<p>
				<label>Group</label>
				<span>{{$data.content_source_group.title}}</span>
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
		{{if is_array($next_content_source)}}
			<p>
				<label>Next content source</label>
				<span>
					<a {{if $next_content_source.view_page_url}}href="{{$next_content_source.view_page_url}}"{{/if}}>{{$next_content_source.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_content_source)}}
			<p>
				<label>Previous content source</label>
				<span>
					<a {{if $previous_content_source.view_page_url}}href="{{$previous_content_source.view_page_url}}"{{/if}}>{{$previous_content_source.title}}</a>
				</span>
			</p>
		{{/if}}
	</div>
	{{* info section end *}}

	{{* subscribe section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Subscription management</h2>
		{{if $data.is_subscribed==1}}
			<a href="#unsubscribe" data-id="{{$data.content_source_id}}" data-unsubscribe-to="content_source">Unsubscribe</a>
		{{else}}
			<a href="#subscribe" data-id="{{$data.content_source_id}}" data-subscribe-to="content_source">Subscribe</a>
		{{/if}}
	{{/if}}
	{{* subscribe section end *}}
</div>