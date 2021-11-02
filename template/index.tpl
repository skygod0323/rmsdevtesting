{{assign var="page_title" value=$lang.html.index_title}}
{{assign var="page_description" value=$lang.html.index_description}}
{{assign var="page_keywords" value=$lang.html.index_keywords}}
{{assign var="page_rss" value=$lang.urls.rss_videos}}
{{assign var="page_canonical" value=$config.project_url}}

{{include file="include_header_general.tpl"}}

{{if $lang.index.enable_video_gallery=='true' && $smarty.session.status_id!=3}}
	{{insert name="getBlock" block_id="custom_list_videos" block_name="Gallery"}}
	{{insert name="getGlobal" global_id="global_stats_stats"}}
{{/if}}

{{insert name="getBlock" block_id="custom_list_videos" block_name="Videos Index"}}
{{insert name="getBlock" block_id="custom_list_videos" block_name="Top Rated"}}

{{if $smarty.session.status_id!=3}}
	{{insert name="getGlobal" global_id="global_stats_banner"}}
{{/if}}

{{if $lang.enable_albums=='true'}}
	{{insert name="getBlock" block_id="list_albums" block_name="Albums Index"}}
	{{include file="include_join_banner_2.tpl"}}
{{/if}}

{{if $lang.enable_models=='true'}}
	{{insert name="getBlock" block_id="list_models" block_name="Models Index"}}
{{/if}}

{{if $lang.index.enable_footer_text=='true'}}
	<div class="text-block">
		<div class="container">
			<h2 class="title">{{$lang.index.bottom_text.title}}</h2>
			<p>{{$lang.index.bottom_text.description}}</p>
		</div>
	</div>
{{/if}}

{{include file="include_footer_general.tpl"}}