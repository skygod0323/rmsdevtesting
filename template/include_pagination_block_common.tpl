<div class="container">
	{{if $nav.show==1}}
		<div class="pagination">
			{{if $pagination_direct_link!=''}}
				<a class="btn btn--full-width" href="{{$pagination_direct_link}}">{{$lang.common_list.load_more}}</a>
			{{else}}
				<div class="pagination-holder">
					<ul>
						{{if $nav.is_first==1}}
							<li><a class="disabled"><i class="icon-chevron-left"></i></a></li>
						{{else}}
							<li><a href="{{$pagination_url_prefix}}{{$nav.previous}}{{if $countryId}}?countryId={{$countryId}}{{/if}}"><i class="icon-chevron-left"></i></a></li>
						{{/if}}

						{{section name=index start=0 step=1 loop=$nav.page_str}}
							{{if $nav.page_str[index]!=''}}
								<li><a href="{{$pagination_url_prefix}}{{$nav.page_str[index]}}{{if $countryId}}?countryId={{$countryId}}{{/if}}">{{$nav.page_num[index]|intval}}</a></li>
							{{else}}
								<li class="active"><a class="disabled"><span>{{$nav.page_num[index]|intval}}</span></a></li>
							{{/if}}
						{{/section}}
						{{if $nav.is_last==1}}
							<li><a class="disabled"><i class="icon-chevron-right"></i></a></li>
						{{else}}
							<li><a href="{{$nav.next}}{{if $countryId}}?countryId={{$countryId}}{{/if}}"><i class="icon-chevron-right"></i></a></li>
						{{/if}}
					</ul>
				</div>
			{{/if}}
		</div>
	{{/if}}
</div>