{{section start=1 loop=$nav.page_total+1 name=pages}}
<sitemap>
	<loc>{{$lang.urls.sitemap}}?type=videos&amp;from_links_videos={{$smarty.section.pages.index}}</loc>
	<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
</sitemap>
{{/section}}