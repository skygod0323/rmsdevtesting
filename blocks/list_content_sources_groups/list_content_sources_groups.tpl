{{* variables supported to control this list *}}
{{assign var="list_content_sources_groups_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_content_sources_groups_title" value="Content Source Groups"}} {{* title *}}

<div id="{{$block_uid}}" class="list-content-sources-groups">
	<h{{$list_content_sources_groups_header_level|default:"2"}}>{{$list_content_sources_groups_title|default:"Content Source Groups"}}</h{{$list_content_sources_groups_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" href="{{$config.project_url}}/cs-groups/{{$item.dir}}/">
					ID: {{$item.content_source_group_id}}<br/>
					Title: {{$item.title}}<br/>
					Description: {{$item.description}}<br/>
					Directory: {{$item.dir}}<br/>
					Total content sources: {{$item.total_content_sources}}<br/>
					Total videos: {{$item.total_videos}}<br/>
					Total albums: {{$item.total_albums}}<br/>
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