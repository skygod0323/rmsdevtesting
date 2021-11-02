{{* variables supported to control this list *}}
{{assign var="list_content_sources_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_content_sources_title" value="Content Sources"}} {{* title *}}

<div id="{{$block_uid}}" class="list-content-sources">
	<h{{$list_content_sources_header_level|default:"2"}}>{{$list_content_sources_title|default:"Content Sources"}}</h{{$list_content_sources_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}}>
					ID: {{$item.content_source_id}}<br/>
					Title: {{$item.title}}<br/>
					Description: {{$item.description}}<br/>
					Directory: {{$item.dir}}<br/>
					URL: {{$item.url}}<br/>
					Rating: {{$item.rating/5*100}}%<br/>
					Votes: {{if $item.rating==0}}0{{else}}{{$item.rating_amount}}{{/if}}<br/>
					Views: {{$item.cs_viewed}}<br/>
					Comments: {{$item.comments_count}}<br/>
					Subscribers: {{$item.subscribers_count}}<br/>
					Total videos: {{$item.total_videos}}<br/>
					Today videos: {{$item.today_videos}}<br/>
					Total albums: {{$item.total_albums}}<br/>
					Today albums: {{$item.today_albums}}<br/>
					Total photos: {{$item.total_photos}}<br/>
					Added: {{$item.added_date|date_format:"%d %B, %Y"}}<br/>
					Last updated: {{if $item.last_content_date!='0000-00-00'}}{{$item.last_content_date|date_format:"%d %B, %Y"}}{{/if}}<br/>
					Rank: {{$item.rank}}<br/>
					Prev rank: {{$item.last_rank}}<br/>
					Screenshot 1: {{if $item.screenshot1}}{{$item.base_files_url}}/{{$item.screenshot1}}{{/if}}<br/>
					Screenshot 2: {{if $item.screenshot2}}{{$item.base_files_url}}/{{$item.screenshot2}}{{/if}}<br/>
					Custom text 1: {{$item.custom1}}<br/>
					Custom file 1: {{if $item.custom_file1}}{{$item.base_files_url}}/{{$item.custom_file1}}{{/if}}<br/>
				</a>
			{{/foreach}}
		</div>

		{{* include pagination here *}}
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>