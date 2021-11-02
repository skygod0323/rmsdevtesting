{{if $data.show==1}}
<div class="pagination">
	<div class="block_content">
		{{if ($data.page_str_left_jump<>'')}}
			<a href="{{$data.first}}" title="Page 01">01</a>
			<a href="{{$data.page_str_left_jump}}" title="Previous 10 pages">...</a>
		{{/if}}

		{{section name=nav start=0 step=1 loop=$data.page_str}}
			{{if $data.page_str[nav]<>''}}
				<a href="{{$data.page_str[nav]}}" title="Page {{$data.page_num[nav]}}">{{$data.page_num[nav]}}</a>
			{{else}}
				<span>{{$data.page_num[nav]}}</span>
			{{/if}}
		{{/section}}

		{{if ($data.page_str_right_jump<>'')}}
			<a href="{{$data.page_str_right_jump}}" title="Next 10 pages">...</a>
			<a href="{{$data.last}}" title="Page {{$data.last_from}}">{{$data.last_from}}</a>
		{{/if}}
	</div>
</div>
{{/if}}