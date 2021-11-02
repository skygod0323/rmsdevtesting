<div class="dvd-group-view">
	<h1>{{$data.title}}</h1>

	<h2>Channel group info</h2>
	<div>
		<p>
			<label>Channel group ID</label>
			<span>{{$data.dvd_group_id}}</span>
		</p>
		<p>
			<label>Directory</label>
			<span>{{$data.dir}}</span>
		</p>
		<p>
			<label>Description</label>
			<span>{{$data.description}}</span>
		</p>
		<p>
			<label>Total channels</label>
			<span>{{$data.total_dvds}}</span>
		</p>
		{{if $data.canonical_url}}
			<p>
				<label>Page canonical</label>
				<span>{{$data.canonical_url}}</span>
			</p>
		{{/if}}
		<p>
			<label>Added</label>
			<span>{{$data.added_date|date_format:"%d %B, %Y"}}</span>
		</p>
		<p>
			<label>Cover 1</label>
			<span>{{if $data.cover1}}{{$data.base_files_url}}/{{$data.cover1}}{{/if}}</span>
		</p>
		<p>
			<label>Cover 2</label>
			<span>{{if $data.cover2}}{{$data.base_files_url}}/{{$data.cover2}}{{/if}}</span>
		</p>
		<p>
			<label>Custom 1</label>
			<span>{{$data.custom1}}</span>
		</p>
		{{if count($data.tags)>0}}
			<p>
				<label>Tags</label>
				<span>
					{{foreach name="data" item="item" from=$data.tags}}
						<a href="{{$config.project_url}}/tags/{{$item.dir}}/">{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
			<p>
				<label>Tags as string</label>
				<span>{{$data.tags_as_string}}</span>
			</p>
		{{/if}}
		{{if count($data.categories)>0}}
			<p>
				<label>Categories</label>
				<span>
					{{foreach name="data" item="item" from=$data.categories}}
						<a href="{{$config.project_url}}/categories/{{$item.dir}}/">{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
			<p>
				<label>Categories as string</label>
				<span>{{$data.categories_as_string}}</span>
			</p>
		{{/if}}
		{{if count($data.models)>0}}
			<p>
				<label>Models</label>
				<span>
					{{foreach name="data" item="item" from=$data.models}}
						<a {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}}>{{$item.title}}</a>{{if !$smarty.foreach.data.last}}, {{/if}}
					{{/foreach}}
				</span>
			</p>
			<p>
				<label>Models as string</label>
				<span>{{$data.models_as_string}}</span>
			</p>
		{{/if}}
	</div>
</div>