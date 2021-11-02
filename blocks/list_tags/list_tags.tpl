{{* variables supported to control this list *}}
{{assign var="list_tags_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_tags_title" value="Tags"}} {{* title *}}

<div id="{{$block_uid}}" class="list-tags">
	<h{{$list_tags_header_level|default:"2"}}>{{$list_tags_title|default:"Tags"}}</h{{$list_tags_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" href="{{$config.project_url}}/tags/{{$item.dir}}/">
					ID: {{$item.tag_id}}<br/>
					Title: {{$item.title}}<br/>
					Directory: {{$item.dir}}<br/>
					Synonyms: {{$item.synonyms}}<br/>
					Total videos: {{$item.total_videos}}<br/>
					Today videos: {{$item.today_videos}}<br/>
					Total albums: {{$item.total_albums}}<br/>
					Today albums: {{$item.today_albums}}<br/>
					Total photos: {{$item.total_photos}}<br/>
					Total posts: {{$item.total_posts}}<br/>
					Today posts: {{$item.today_posts}}<br/>
					Total playlists: {{$item.total_playlists}}<br/>
					Total models: {{$item.total_models}}<br/>
					Total channels: {{$item.total_dvds}}<br/>
					Total channel groups: {{$item.total_dvd_groups}}<br/>
					Total content sources: {{$item.total_cs}}<br/>
					Added: {{$item.added_date|date_format:"%d %B, %Y"}}<br/>
					Custom text 1: {{$item.custom1}}<br/>
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