<div class="list_members_events">
	<h1 class="block_header">Members Events</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} events
			</div>
		{{/if}}
		<div class="block_content">
			{{foreach name=data item=item from=$data}}
				<div class="event">
					{{$item.added_date|date_format:"%text"}}:
					<a href="{{$config.project_url}}/members/{{$item.user_id}}/">{{$item.display_name}}</a>
					{{if $item.event_type_id==1}}
						added a new video <a href="{{$item.content_view_page_url}}">{{$item.video_title}}</a>.
					{{elseif $item.event_type_id==2}}
						added a new album <a href="{{$item.content_view_page_url}}">{{$item.album_title}}</a>.
					{{elseif $item.event_type_id==4}}
						commented on video <a href="{{$item.content_view_page_url}}">{{$item.video_title}}</a>.
					{{elseif $item.event_type_id==5}}
						commented on album <a href="{{$item.content_view_page_url}}">{{$item.album_title}}</a>.
					{{elseif $item.event_type_id==6}}
						changed video <a href="{{$item.content_view_page_url}}">{{$item.video_title}}</a> visibility to private.
					{{elseif $item.event_type_id==7}}
						changed video <a href="{{$item.content_view_page_url}}">{{$item.video_title}}</a> visibility to public.
					{{elseif $item.event_type_id==8}}
						changed album <a href="{{$item.content_view_page_url}}">{{$item.album_title}}</a> visibility to private.
					{{elseif $item.event_type_id==9}}
						changed album <a href="{{$item.content_view_page_url}}">{{$item.album_title}}</a> visibility to public.
					{{elseif $item.event_type_id==10}}
						and <a href="{{$config.project_url}}/members/{{$item.user_target_id}}/">{{$item.user_target_name}}</a> are friends now.
					{{elseif $item.event_type_id==11}}
						and <a href="{{$config.project_url}}/members/{{$item.user_target_id}}/">{{$item.user_target_name}}</a> are no more friends.
					{{elseif $item.event_type_id==12}}
						added message to {{if $item.gender_id==1}}his{{elseif $item.gender_id==2}}her{{else}}their{{/if}} <a href="{{$config.project_url}}/members/{{$item.user_id}}/wall/">own wall</a>.
					{{elseif $item.event_type_id==13}}
						added message to <a href="{{$config.project_url}}/members/{{$item.user_target_id}}/wall/">{{$item.user_target_name}}'s wall</a>.
					{{elseif $item.event_type_id==14}}
						commented on performer
						{{if $item.content_view_page_url<>''}}
							<a href="{{$item.content_view_page_url}}">{{$item.model_title}}</a>.
						{{else}}
							{{$item.model_title}}.
						{{/if}}
					{{elseif $item.event_type_id==15}}
						commented on sponsor
						{{if $item.content_view_page_url<>''}}
							<a href="{{$item.content_view_page_url}}">{{$item.cs_title}}</a>.
						{{else}}
							{{$item.cs_title}}.
						{{/if}}
					{{elseif $item.event_type_id==16}}
						commented on channel
						{{if $item.content_view_page_url<>''}}
							<a href="{{$item.content_view_page_url}}">{{$item.dvd_title}}</a>.
						{{else}}
							{{$item.dvd_title}}.
						{{/if}}
					{{elseif $item.event_type_id==17}}
						changed {{if $item.gender_id==1}}his{{elseif $item.gender_id==2}}her{{else}}their{{/if}} avatar.
					{{elseif $item.event_type_id==18}}
						changed {{if $item.gender_id==1}}his{{elseif $item.gender_id==2}}her{{else}}their{{/if}} status to <a href="{{$config.project_url}}/members/{{$item.user_id}}/">{{$item.status_message}}</a>.
					{{elseif $item.event_type_id==20}}
						commented on playlist {{$item.playlist_title}}.
					{{elseif $item.event_type_id==21}}
						commented on post
						{{if $item.content_view_page_url<>''}}
							<a href="{{$item.content_view_page_url}}">{{$item.post_title}}</a>.
						{{else}}
							{{$item.post_title}}.
						{{/if}}
					{{/if}}
				</div>
			{{/foreach}}
		</div>
	{{else}}
		<div class="text_content">
			There are no events in the list.
		</div>
	{{/if}}
</div>