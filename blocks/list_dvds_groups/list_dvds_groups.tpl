{{* variables supported to control this list *}}
{{assign var="list_dvds_groups_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_dvds_groups_title" value="Channel Groups"}} {{* title *}}

<div id="{{$block_uid}}" class="list-dvds-groups">
	<h{{$list_dvds_groups_header_level|default:"2"}}>{{$list_dvds_groups_title|default:"Channel Groups"}}</h{{$list_dvds_groups_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}}>
					ID: {{$item.dvd_group_id}}<br/>
					Title: {{$item.title}}<br/>
					Description: {{$item.description}}<br/>
					Directory: {{$item.dir}}<br/>
					Total channels: {{$item.total_dvds}}<br/>
					Added: {{$item.added_date|date_format:"%d %B, %Y"}}<br/>
					Cover 1: {{if $item.cover1}}{{$item.base_files_url}}/{{$item.cover1}}{{/if}}<br/>
					Cover 2: {{if $item.cover2}}{{$item.base_files_url}}/{{$item.cover2}}{{/if}}<br/>
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