<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	{{foreach item=item from=$data}}
		<url>
			<loc>{{$lang.urls.content_by_tag|replace:"%DIR%":$item.tag_dir|replace:"%ID%":$item.tag_id}}</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
			<changefreq>hourly</changefreq>
			<priority>0.6</priority>
		</url>
	{{/foreach}}
</urlset>