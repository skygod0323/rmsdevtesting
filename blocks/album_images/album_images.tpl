<div class="album-images">
	{{assign var="thumb_format_size" value="200x150"}}
	{{assign var="big_format_size" value="source"}}
	<div>
		<div class="images">
			{{if $can_watch_album==0}}
				{{foreach item="item" from=$data}}
					<img alt="{{$item.title|default:$data.title}}" src="{{$item.formats[$thumb_format_size].direct_url}}" width="{{$item.formats[$thumb_format_size].dimensions.0}}" height="{{$item.formats[$thumb_format_size].dimensions.1}}">
				{{/foreach}}
			{{else}}
				{{foreach item="item" from=$data}}
					<a href="{{$item.formats[$big_format_size].protected_url}}">
						<img alt="{{$item.title|default:$data.title}}" src="{{$item.formats[$thumb_format_size].direct_url}}" width="{{$item.formats[$thumb_format_size].dimensions.0}}" height="{{$item.formats[$thumb_format_size].dimensions.1}}">
					</a>
				{{/foreach}}
			{{/if}}
		</div>
	</div>
</div>