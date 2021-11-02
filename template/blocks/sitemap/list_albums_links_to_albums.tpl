<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	{{foreach name=data item=item from=$data}}
	<url>
		<loc>{{$item.view_page_url}}</loc>
		<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
	</url>
	{{/foreach}}
</urlset>