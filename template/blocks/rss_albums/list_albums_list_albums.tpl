{{foreach item="item" from=$data}}
	<item>
		<title><![CDATA[
			{{$item.title}}
			]]></title>
		<link>{{$item.view_page_url}}</link>
		<description><![CDATA[
			<a href="{{$item.view_page_url}}"><img src="{{$item.preview_url}}/{{$lang.albums.thumb_size}}/{{$item.dir_path}}/{{$item.album_id}}/preview.jpg" border="0"><br>{{$item.description}}</a>
			]]></description>
		<pubDate>{{$item.post_date|date_format:"%a %d %b %Y %H:%M:%S +0200"}}</pubDate>
		<guid>{{$item.view_page_url}}</guid>
	</item>
{{/foreach}}