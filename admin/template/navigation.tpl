{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if $nav.show==1}}
<div class="paging">
	{{if ($nav.page_str_left_jump<>'')}}
		<a href="{{$nav.first}}" title="{{$lang.common.navigation_page_number|replace:"%1%":"01"}}">01</a>
		<a href="{{$nav.page_str_left_jump}}" title="{{$lang.common.navigation_prev_10_pages}}">...</a>
	{{/if}}

	{{section name=nav start=0 step=1 loop=$nav.page_str}}
		{{if $nav.page_str[nav]<>''}}
			<a href="{{$nav.page_str[nav]}}" title="{{$lang.common.navigation_page_number|replace:"%1%":$nav.page_num[nav]}}">{{$nav.page_num[nav]}}</a>
		{{else}}
			<span>{{$nav.page_num[nav]}}</span>
		{{/if}}
	{{/section}}

	{{if ($nav.page_str_right_jump<>'')}}
		<a href="{{$nav.page_str_right_jump}}" title="{{$lang.common.navigation_next_10_pages}}">...</a>
		<a href="{{$nav.last}}" title="{{$lang.common.navigation_page_number|replace:"%1%":$nav.last_from}}">{{$nav.last_from}}</a>
	{{/if}}
</div>
{{/if}}