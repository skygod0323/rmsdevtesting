{{* variables supported to control this list *}}
{{assign var="awebl_list_categories_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="awebl_list_categories_title" value="Categories"}} {{* title *}}

<div id="{{$block_uid}}" class="awebl-list-categories">
	<h{{$awebl_list_categories_header_level|default:"2"}}>{{$awebl_list_categories_title|default:"Categories"}}</h{{$awebl_list_categories_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="group" from=$data}}
				{{if count($group.categories)>0}}
					<div class="group">{{$group.title}}</div>
					{{foreach item="item" from=$group.categories}}
						<div>{{$item}}</div>
					{{/foreach}}
				{{/if}}
			{{/foreach}}
		</div>
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>