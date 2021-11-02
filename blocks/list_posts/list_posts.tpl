{{* variables supported to control this list *}}
{{assign var="list_posts_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_posts_title" value="Posts"}} {{* title *}}

{{if $can_manage==1}}
{{* render editing form for 'my posts' functionality *}}
<form method="post" data-form="ajax">
<input type="hidden" name="action" value="delete_posts"/>

<div class="generic-error {{if $can_delete==0}}hidden{{/if}}">{{if $can_delete==0}}Post deleting is disabled.{{/if}}</div>
{{/if}}

<div id="{{$block_uid}}" class="list-posts">
	<h{{$list_posts_header_level|default:"2"}}>{{$list_posts_title|default:"Posts"}}</h{{$list_posts_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<div class="item">
					<a title="{{$item.title}}" href="{{$item.view_page_url}}">
						ID: {{$item.post_id}}<br/>
						Title: {{$item.title}}<br/>
						Description: {{$item.description}}<br/>
						Directory: {{$item.dir}}<br/>
						Rating: {{$item.rating/5*100}}%<br/>
						Votes: {{if $item.rating==0}}0{{else}}{{$item.rating_amount}}{{/if}}<br/>
						Views: {{$item.post_viewed}}<br/>
						Comments: {{$item.comments_count}}<br/>
						Added: {{$item.post_date|date_format:"%d %B, %Y"}}<br/>
						Custom text 1: {{$item.custom1}}<br/>
						Custom file 1: {{if $item.custom_file1}}{{$item.base_files_url}}/{{$item.custom_file1}}{{/if}}<br/>
					</a>
					<div>
						{{if $can_manage==1}}
							<input id="delete_{{$item.post_id}}" type="checkbox" name="delete[]" value="{{$item.post_id}}" {{if $can_delete==0 || $item.is_locked==1}}disabled{{/if}}/>
							<label for="delete_{{$item.post_id}}">delete</label>
						{{/if}}
					</div>
				</div>
			{{/foreach}}
		</div>

		{{* include pagination here *}}
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>
{{if $can_manage==1}}
<div class="buttons">
	<input type="submit" value="Delete" {{if $can_delete==0}}disabled{{/if}}/>
</div>
</form>
{{/if}}