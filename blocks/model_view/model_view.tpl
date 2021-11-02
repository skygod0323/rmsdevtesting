<div class="model-view">
	<h1>{{$data.title}}</h1>

	{{* info section begin *}}
	<h2>Model info</h2>
	<div>
		<p>
			<label>Model ID</label>
			<span>{{$data.model_id}}</span>
		</p>
		<p>
			<label>Directory</label>
			<span>{{$data.dir}}</span>
		</p>
		<p>
			<label>Pseudonyms</label>
			<span>{{$data.alias}}</span>
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
			<label>Country</label>
			<span>{{$data.country}}</span>
		</p>
		<p>
			<label>State</label>
			<span>{{$data.state}}</span>
		</p>
		<p>
			<label>City</label>
			<span>{{$data.city}}</span>
		</p>
		<p>
			<label>Height</label>
			<span>{{$data.height}}</span>
		</p>
		<p>
			<label>Weight</label>
			<span>{{$data.weight}}</span>
		</p>
		<p>
			<label>Measurements</label>
			<span>{{$data.measurements}}</span>
		</p>
		<p>
			<label>Gender</label>
			<span>{{if $data.gender_id==0}}Female{{elseif $data.gender_id==1}}Male{{elseif $data.gender_id==2}}Other{{/if}}</span>
		</p>
		<p>
			<label>Hair</label>
			<span>{{if $data.hair_id==1}}Black{{elseif $data.hair_id==2}}Dark{{elseif $data.hair_id==3}}Red{{elseif $data.hair_id==4}}Brown{{elseif $data.hair_id==5}}Blond{{elseif $data.hair_id==6}}Grey{{elseif $data.hair_id==7}}Bald{{elseif $data.hair_id==8}}Wig{{/if}}</span>
		</p>
		<p>
			<label>Eye color</label>
			<span>{{if $data.eye_color_id==1}}Blue{{elseif $data.eye_color_id==2}}Gray{{elseif $data.eye_color_id==3}}Green{{elseif $data.eye_color_id==4}}Amber{{elseif $data.eye_color_id==5}}Brown{{elseif $data.eye_color_id==6}}Hazel{{elseif $data.eye_color_id==7}}Black{{/if}}</span>
		</p>
		<p>
			<label>Birth date</label>
			<span>{{if $data.birth_date!='0000-00-00'}}{{$data.birth_date|date_format:"%d %B, %Y"}}{{/if}}</span>
		</p>
		<p>
			<label>Death date</label>
			<span>{{if $data.death_date!='0000-00-00'}}{{$data.death_date|date_format:"%d %B, %Y"}}{{/if}}</span>
		</p>
		<p>
			<label>Age</label>
			<span>{{if $data.age>0}}{{$data.age}}{{/if}}</span>
		</p>
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
			<span>{{$data.model_viewed}} views, {{$data.comments_count}} comments, {{$data.subscribers_count}} subscribers</span>
		</p>
		<p>
			<label>Content</label>
			<span>{{$data.total_videos}} total videos, {{$data.today_videos}} videos today, {{$data.total_albums}} total albums, {{$data.today_albums}} albums today, {{$data.total_photos}} total photos, {{$data.total_posts}} total posts, {{$data.today_posts}} posts today, {{$data.total_dvds}} total channels, {{$data.total_dvd_groups}} total channel groups</span>
		</p>
		<p>
			<label>Content stats</label>
			<span>average video rating: {{$data.avg_videos_rating/5*100}}%, total video views: {{$data.avg_videos_popularity*$data.total_videos|intval}}, average album rating: {{$data.avg_albums_rating/5*100}}%, total album views: {{$data.avg_albums_popularity*$data.total_albums|intval}}, average post rating: {{$data.avg_posts_rating/5*100}}%, total post views: {{$data.avg_posts_popularity*$data.total_posts|intval}}</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like" data-model-id="{{$data.model_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike" data-model-id="{{$data.model_id}}" data-vote="0">Dislike</a>
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
		{{if $data.model_group.model_group_id>0}}
			<p>
				<label>Group</label>
				<span>{{$data.model_group.title}}</span>
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
		{{if is_array($next_model)}}
			<p>
				<label>Next model</label>
				<span>
					<a {{if $next_model.view_page_url}}href="{{$next_model.view_page_url}}"{{/if}}>{{$next_model.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_model)}}
			<p>
				<label>Previous model</label>
				<span>
					<a {{if $previous_model.view_page_url}}href="{{$previous_model.view_page_url}}"{{/if}}>{{$previous_model.title}}</a>
				</span>
			</p>
		{{/if}}
	</div>
	{{* info section end *}}

	{{* subscribe section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Subscription management</h2>
		{{if $data.is_subscribed==1}}
			<a href="#unsubscribe" data-id="{{$data.model_id}}" data-unsubscribe-to="model">Unsubscribe</a>
		{{else}}
			<a href="#subscribe" data-id="{{$data.model_id}}" data-subscribe-to="model">Subscribe</a>
		{{/if}}
	{{/if}}
	{{* subscribe section end *}}
</div>