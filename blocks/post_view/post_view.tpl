<div class="post-view">
	<h1>{{$data.title}}</h1>

	{{* content section begin *}}
	<div>
		{{if count($data)==0}}
			This post link is broken.
		{{else}}
			{{$data.content}}
		{{/if}}
	</div>
	{{* content section end *}}

	{{* info section begin *}}
	<h2>Post info</h2>
	<div>
		<p>
			<label>Post ID</label>
			<span>{{$data.post_id}}</span>
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
			<label>Page canonical</label>
			<span>{{$data.canonical_url}}</span>
		</p>
		<p>
			<label>Published on</label>
			<span>{{$data.post_date|date_format:"%d %B, %Y"}}</span>
		</p>
		<p>
			<label>Stats</label>
			<span>{{$data.post_viewed}} views, {{$data.comments_count}} comments</span>
		</p>
		<p>
			<label>Rating</label>
			<span class="rating-container">
				<span class="voters" data-success="Thank you!" data-error="IP already voted">{{$data.rating/5*100}}% ({{if $data.rating==0 && $data.rating_amount==1}}0{{else}}{{$data.rating_amount}}{{/if}} votes)</span>
				<a href="#like" class="rate-like" data-post-id="{{$data.post_id}}" data-vote="5">Like</a>
				<a href="#dislike" class="rate-dislike" data-post-id="{{$data.post_id}}" data-vote="0">Dislike</a>
			</span>
		</p>
		<p>
			<label>Created by</label>
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
			<label>Custom file 1</label>
			<span>{{if $data.custom_file1}}{{$data.base_files_url}}/{{$data.custom_file1}}{{/if}}</span>
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
		{{if is_array($next_post)}}
			<p>
				<label>Next post</label>
				<span>
					<a href="{{$next_post.view_page_url}}">{{$next_post.title}}</a>
				</span>
			</p>
		{{/if}}
		{{if is_array($previous_post)}}
			<p>
				<label>Previous post</label>
				<span>
					<a href="{{$previous_post.view_page_url}}">{{$previous_post.title}}</a>
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
				<label>Report this post as</label>
				<span>
					<input type="radio" id="flag_other_post" name="flag_id" value="flag_other_post" class="radio" checked>
					<label for="flag_other_post">Other</label>
				</span>
			</div>

			<div class="row">
				<label for="flag_message">Reason (optional)</label>
				<textarea id="flag_message" name="flag_message" rows="3" class="textarea"></textarea>
			</div>

			<div class="buttons">
				<input type="hidden" name="action" value="flag"/>
				<input type="hidden" name="post_id" value="{{$data.post_id}}">
				<input type="submit" value="Send"/>
			</div>
		</form>
	</div>
	{{* flagging form section end *}}
</div>