{{* variables supported to control this list *}}
{{assign var="list_models_groups_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_models_groups_title" value="Model Groups"}} {{* title *}}

<div id="{{$block_uid}}" class="list-models-groups">
	<h{{$list_models_groups_header_level|default:"2"}}>{{$list_models_groups_title|default:"Model Groups"}}</h{{$list_models_groups_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" href="{{$config.project_url}}/model-groups/{{$item.dir}}/">
					ID: {{$item.model_group_id}}<br/>
					Title: {{$item.title}}<br/>
					Description: {{$item.description}}<br/>
					Directory: {{$item.dir}}<br/>
					Total models: {{$item.total_models}}<br/>
					Total videos: {{$item.total_videos}}<br/>
					Total albums: {{$item.total_albums}}<br/>
					Total posts: {{$item.total_posts}}<br/>
					Added: {{$item.added_date|date_format:"%d %B, %Y"}}<br/>
					Screenshot 1: {{if $item.screenshot1}}{{$item.base_files_url}}/{{$item.screenshot1}}{{/if}}<br/>
					Screenshot 2: {{if $item.screenshot2}}{{$item.base_files_url}}/{{$item.screenshot2}}{{/if}}<br/>
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