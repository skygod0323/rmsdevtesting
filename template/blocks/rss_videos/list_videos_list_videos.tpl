{{foreach item="item" from=$data}}
<item>
	<title><![CDATA[
		{{$item.title}}
	]]></title>
	<link>{{$item.view_page_url}}</link>
	<description><![CDATA[
		<a href="{{$item.view_page_url}}"><img src="{{$item.screen_url}}/{{$lang.videos.thumb_size}}/{{$item.screen_main}}.jpg" border="0"><br>{{$item.description}}</a>
	]]></description>
	<pubDate>{{$item.post_date|date_format:"%a %d %b %Y %H:%M:%S +0200"}}</pubDate>
	<guid>{{$item.view_page_url}}</guid>
</item>
{{/foreach}}