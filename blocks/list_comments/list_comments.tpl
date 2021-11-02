<div class="list_comments">
	<h1 class="block_header">Comments List</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} comments
			</div>
		{{/if}}
		<div class="block_content">
			{{foreach name=data item=item from=$data}}
				<div class="comment_row">
					<div class="avatar">
						{{if $item.is_anonymous}}
							<img src="{{$config.project_url}}/images/no_avatar_user.jpg" alt="{{$item.display_name}}"/>
						{{else}}
							<a href="{{$config.project_url}}/members/{{$item.user_id}}/">
								<img src="{{if $item.avatar<>''}}{{$item.avatar}}{{else}}{{$config.project_url}}/images/no_avatar_user.jpg{{/if}}" alt="{{$item.display_name}}"/>
							</a>
						{{/if}}
					</div>
					<h2>
						{{$item.added_date|date_format:"%d %B, %Y"}} by
						{{if $item.is_anonymous}}
							<span class="anonymous_user">{{$item.display_name}}</span>
						{{else}}
							<a href="{{$config.project_url}}/members/{{$item.user_id}}/">{{$item.display_name}}</a>
						{{/if}}
						{{if $item.content_type==1}}
							on video <a href="{{$item.content_view_page_url}}" title="{{$item.content_desc}}">{{$item.content_title}}</a>:
						{{elseif $item.content_type==2}}
							on album <a href="{{$item.content_view_page_url}}" title="{{$item.content_desc}}">{{$item.content_title}}</a>:
						{{elseif $item.content_type==3}}
							on sponsor
							{{if $item.content_view_page_url<>''}}
								<a href="{{$item.content_view_page_url}}" title="{{$item.content_desc}}">{{$item.content_title}}</a>:
							{{else}}
								{{$item.content_title}}:
							{{/if}}
						{{elseif $item.content_type==4}}
							on performer
							{{if $item.content_view_page_url<>''}}
								<a href="{{$item.content_view_page_url}}" title="{{$item.content_desc}}">{{$item.content_title}}</a>:
							{{else}}
								{{$item.content_title}}:
							{{/if}}
						{{elseif $item.content_type==5}}
							on channel
							{{if $item.content_view_page_url<>''}}
								<a href="{{$item.content_view_page_url}}" title="{{$item.content_desc}}">{{$item.content_title}}</a>:
							{{else}}
								{{$item.content_title}}:
							{{/if}}
						{{elseif $item.content_type==12}}
							on post
							{{if $item.content_view_page_url<>''}}
								<a href="{{$item.content_view_page_url}}" title="{{$item.content_desc}}">{{$item.content_title}}</a>:
							{{else}}
								{{$item.content_title}}:
							{{/if}}
						{{elseif $item.content_type==13}}
							on playlist {{$item.content_title}}:
						{{/if}}
					</h2>
					<p>{{$item.comment|replace:"\n":"<br/>"}}</p>
					<div class="g_clear"></div>
				</div>
			{{/foreach}}
		</div>
	{{else}}
		<div class="text_content">
			There are no comments in the list.
		</div>
	{{/if}}
</div>