<div class="dvd-view">
	<h1>{{$data.title}}</h1>

	{{* info section begin *}}
	<h2>Channel info</h2>
	<div>
		<p>
			<label>Channel ID</label>
			<span>{{$data.dvd_id}}</span>
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
			<label>Cover 1 front</label>
			<span>{{if $data.cover1_front}}{{$data.base_files_url}}/{{$data.cover1_front}}{{/if}}</span>
		</p>
		<p>
			<label>Cover 1 back</label>
			<span>{{if $data.cover1_back}}{{$data.base_files_url}}/{{$data.cover1_back}}{{/if}}</span>
		</p>
		<p>
			<label>Cover 2 front</label>
			<span>{{if $data.cover2_front}}{{$data.base_files_url}}/{{$data.cover2_front}}{{/if}}</span>
		</p>
		<p>
			<label>Cover 2 back</label>
			<span>{{if $data.cover2_back}}{{$data.base_files_url}}/{{$data.cover2_back}}{{/if}}</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.dvd_viewed}} views, {{$data.comments_count}} comments, {{$data.subscribers_count}} subscribers</span>
		</p>
		<p>
			<label>Content</label>
			<span>{{$data.total_videos}} total videos ({{$data.total_videos_duration/3600|ceil}} hours), {{$data.today_videos}} videos today</span>
		</p>
		<p>
			<label>Content stats</label>
			<span>average video rating: {{$data.avg_videos_rating/5*100}}%, total video views: {{$data.avg_videos_popularity*$data.total_videos|intval}}</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like {{if $is_limit_over==1}}disabled{{/if}}" data-dvd-id="{{$data.dvd_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike {{if $is_limit_over==1}}disabled{{/if}}" data-dvd-id="{{$data.dvd_id}}" data-vote="0">Dislike</a>
			</span>
		</p>
		{{if $data.user_id>0}}
			<p>
				<label>Created by</label>
				<span>
					<a href="{{$config.project_url}}/members/{{$data.user_id}}/">{{$data.user.display_name}}</a> {{if $data.user.is_subscribed==1}}(subscribed){{/if}}
					{{if $data.user.avatar_url}}<img src="{{$data.user.avatar_url}}" width="30" height="30"/>{{/if}}
				</span>
			</p>
		{{/if}}
		<p>
			<label>Can upload?</label>
			<span>{{if $data.can_upload==1}}Yes{{else}}No{{/if}}</span>
		</p>
		<p>
			<label>Custom 1</label>
			<span>{{$data.custom1}}</span>
		</p>
		<p>
			<label>Custom file 1</label>
			<span>{{if $data.custom_file1}}{{$data.base_files_url}}/{{$data.custom_file1}}{{/if}}</span>
		</p>
		{{if $data.dvd_group.dvd_group_id>0}}
			<p>
				<label>Group</label>
				<span>{{$data.dvd_group.title}}</span>
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
		{{if is_array($next_dvd)}}
			<p>
				<label>Next channel</label>
				<span>
					<a {{if $next_dvd.view_page_url}}href="{{$next_dvd.view_page_url}}"{{/if}}>{{$next_dvd.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_dvd)}}
			<p>
				<label>Previous channel</label>
				<span>
					<a {{if $previous_dvd.view_page_url}}href="{{$previous_dvd.view_page_url}}"{{/if}}>{{$previous_dvd.title}}</a>
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
				<label>Report this channel as</label>
				<span>
					<input type="radio" id="flag_other_channel" name="flag_id" value="flag_other_channel" class="radio" checked>
					<label for="flag_other_channel">Other</label>
				</span>
			</div>

			<div class="row">
				<label for="flag_message">Reason (optional)</label>
				<textarea id="flag_message" name="flag_message" rows="3" class="textarea"></textarea>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="flag"/>
				<input type="hidden" name="dvd_id" value="{{$data.dvd_id}}">
				<input type="submit" value="Send"/>
			</div>
		</form>
	</div>
	{{* flagging form section end *}}

	{{* subscribe section begin *}}
	{{if $smarty.session.user_id>0}}
		<h2>Subscription management</h2>
		{{if $data.is_subscribed==1}}
			<a href="#unsubscribe" data-id="{{$data.dvd_id}}" data-unsubscribe-to="dvd">Unsubscribe</a>
		{{else}}
			{{if $data.tokens_required>0}}
				{{if $smarty.session.tokens_available<$data.tokens_required}}
					To have access to all content of this channel you must spend <em>{{$data.tokens_required}}</em>
					tokens.<br/><br/>
					Your current tokens balance is <em>{{$smarty.session.tokens_available}}</em> tokens.<br/>
					You need <em>{{$data.tokens_required-$smarty.session.tokens_available}}</em> more tokens.
				{{else}}
					<a href="#subscribe" data-id="{{$data.dvd_id}}" data-subscribe-to="dvd">Subscribe for {{$data.tokens_required}} tokens</a>
				{{/if}}
			{{else}}
				<a href="#subscribe" data-id="{{$data.dvd_id}}" data-subscribe-to="dvd">Subscribe</a>
			{{/if}}
		{{/if}}
	{{/if}}
	{{* subscribe section end *}}
</div>