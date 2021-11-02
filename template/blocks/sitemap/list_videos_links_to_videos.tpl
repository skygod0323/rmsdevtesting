<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
	{{foreach name=data item=item from=$data}}
	<url>
		<loc>{{$item.view_page_url}}</loc>
		<lastmod>{{$smarty.now|date_format:"%Y-%m-%d"}}</lastmod>
		<changefreq>daily</changefreq>
		<priority>0.8</priority>
		<image:image>
			<image:loc>{{$item.screen_url}}/preview.jpg</image:loc>
			<image:caption><![CDATA[{{$item.title}}]]></image:caption>
		</image:image>
		<video:video>
			<video:thumbnail_loc>{{$item.screen_url}}/preview.jpg</video:thumbnail_loc>
			<video:title><![CDATA[{{$item.title}}]]></video:title>
			<video:description><![CDATA[{{$item.description}}]]></video:description>
			<video:duration>{{$item.duration}}</video:duration>

			{{if $item.server_group_id>0}}
				{{assign var="postfix" value=$lang.videos.sitemap_format_standard}}
				{{if $item.is_private==2}}
					{{assign var="postfix" value=$lang.videos.sitemap_format_premium}}
				{{/if}}
				{{if $item.formats[$postfix].file_path!=''}}
					<video:content_loc>{{$config.project_url}}/get_file/{{$item.server_group_id}}/{{$item.formats[$postfix].file_path}}/</video:content_loc>
				{{/if}}
			{{/if}}

			<video:rating>{{if $item.rating|default:0>5}}5{{else}}{{$item.rating|default:0|number_format:1}}{{/if}}</video:rating>
			<video:view_count>{{$item.video_viewed}}</video:view_count>
			<video:publication_date>{{$item.post_date|date_format:"%Y-%m-%d"}}</video:publication_date>
			{{if count($item.categories)>0}}
				<video:category><![CDATA[{{$item.categories.0.title}}]]></video:category>
			{{/if}}
			{{if count($item.tags)>0}}
				{{foreach name=data_tags item=item_tags from=$item.tags}}
					<video:tag><![CDATA[{{$item_tags.tag}}]]></video:tag>
				{{/foreach}}
			{{/if}}
		</video:video>
	</url>
	{{/foreach}}
</urlset>