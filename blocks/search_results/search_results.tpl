{{* variables supported to control this list *}}
{{assign var="search_results_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="search_results_title" value="Searches"}} {{* title *}}

<div id="{{$block_uid}}" class="search-results">
	<h{{$search_results_header_level|default:"2"}}>{{$search_results_title|default:"Searches"}}</h{{$search_results_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.query}}" {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}} {{if $item.size}}style="{{if $item.is_bold==1}}font-weight: bold; {{/if}}font-size: {{$item.size}}px;"{{/if}}>
					Query: {{$item.query}}<br/>
					Submitted: {{$item.amount}}<br/>
					Found videos: {{$item.query_results_videos}}<br/>
					Found albums: {{$item.query_results_albums}}<br/>
					Found total: {{$item.query_results_total}}<br/>
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