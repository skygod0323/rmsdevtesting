{{* variables supported to control this list *}}
{{assign var="top_referers_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="top_referers_title" value="Referers"}} {{* title *}}

<div id="{{$block_uid}}" class="top-referers">
	<h{{$top_referers_header_level|default:"2"}}>{{$top_referers_title|default:"Referers"}}</h{{$top_referers_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" href="{{$item.url}}">
					ID: {{$item.referer_id}}<br/>
					Title: {{$item.title}}<br/>
					Added: {{$item.added_date|date_format:"%d %B, %Y"}}<br/>
					Custom text 1: {{$item.custom1}}<br/>
					Custom file 1: {{if $item.custom_file1}}{{$item.base_files_url}}/{{$item.custom_file1}}{{/if}}<br/>
					{{if is_array($item.video)}}
						Video title: {{$item.video.title}}<br/>
						Video duration: {{$item.video.duration_array.text}}<br/>
						Video screenshot: {{$item.video.screen_url}}/320x180/{{$item.video.screen_main}}.jpg<br/>
						Video URL: {{$item.video.view_page_url}}<br/>
					{{/if}}
				</a>
			{{/foreach}}
		</div>
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>