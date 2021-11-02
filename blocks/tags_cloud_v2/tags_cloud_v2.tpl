{{* variables supported to control this list *}}
{{assign var="tags_cloud_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="tags_cloud_title" value="Tags"}} {{* title *}}

<div class="tags-cloud">
	<h{{$tags_cloud_header_level|default:"2"}}>{{$tags_cloud_title|default:"Tags"}}</h{{$tags_cloud_header_level|default:"2"}}>
	<div class="items">
		{{foreach item=item from=$data}}
			<a class="item" title="{{$item.title}}" href="{{$config.project_url}}/tags/{{$item.dir}}/" style="{{if $item.is_bold==1}}font-weight: bold; {{/if}}font-size: {{$item.size}}px;">{{$item.title}}</a>&nbsp;&nbsp;
		{{/foreach}}
	</div>
</div>