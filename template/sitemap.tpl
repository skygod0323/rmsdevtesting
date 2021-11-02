<?xml version="1.0" encoding="UTF-8"?>

{{if $smarty.request.type==''}}

<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap>
		<loc>{{$lang.urls.sitemap}}?type=other</loc>
		<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
	</sitemap>

	{{if $lang.enable_categories=='true'}}
		<sitemap>
			<loc>{{$lang.urls.sitemap}}?type=categories</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		</sitemap>
	{{/if}}

	{{if $lang.enable_models=='true'}}
		<sitemap>
			<loc>{{$lang.urls.sitemap}}?type=models</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		</sitemap>
	{{/if}}

	{{if $lang.enable_tags=='true'}}
		<sitemap>
			<loc>{{$lang.urls.sitemap}}?type=tags</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		</sitemap>
	{{/if}}

	{{insert name="getBlock" block_id="list_videos" block_name="Sitemaps Videos"}}
	{{if $lang.enable_albums=='true'}}
		{{insert name="getBlock" block_id="list_albums" block_name="Sitemaps Albums"}}
	{{/if}}
</sitemapindex>

{{elseif $smarty.request.type=='categories'}}
	{{insert name="getBlock" block_id="list_categories" block_name="Links To Categories"}}

{{elseif $smarty.request.type=='models'}}
	{{insert name="getBlock" block_id="list_models" block_name="Links To Models"}}

{{elseif $smarty.request.type=='tags'}}
	{{insert name="getBlock" block_id="list_tags" block_name="Links To Tags"}}

{{elseif $smarty.request.type=='videos'}}
	{{insert name="getBlock" block_id="list_videos" block_name="Links To Videos"}}

{{elseif $smarty.request.type=='albums'}}
	{{insert name="getBlock" block_id="list_albums" block_name="Links To Albums"}}

{{elseif $smarty.request.type=='other'}}

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>{{$lang.urls.home}}</loc>
		<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		<changefreq>hourly</changefreq>
		<priority>1.0</priority>
	</url>
	<url>
		<loc>{{$lang.urls.videos}}</loc>
		<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		<changefreq>hourly</changefreq>
		<priority>1.0</priority>
	</url>
	{{if $lang.enable_albums=='true'}}
		<url>
			<loc>{{$lang.urls.albums}}</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
			<changefreq>hourly</changefreq>
			<priority>1.0</priority>
		</url>
	{{/if}}
	{{if $lang.enable_categories=='true'}}
		<url>
			<loc>{{$lang.urls.categories}}</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
			<changefreq>hourly</changefreq>
			<priority>1.0</priority>
		</url>
	{{/if}}
	{{if $lang.enable_models=='true'}}
		<url>
			<loc>{{$lang.urls.models}}</loc>
			<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
			<changefreq>hourly</changefreq>
			<priority>1.0</priority>
		</url>
	{{/if}}
</urlset>

{{/if}}