{{assign var='images_in_row' value=5}}
{{if count($data)>0}}
	<div class="list_albums_images">
		<div class="block_content">
			{{assign var='image_number' value=1}}
			{{foreach name=data item=item from=$data}}
				<div class="item">
					<div class="image">
						<a href="{{$item.view_page_url}}" title="{{$item.title|default:$item.album_title}}"><img class="thumb" src="{{$item.formats.120x160.direct_url}}" alt="{{$item.title|default:$item.album_title}}"/></a>
					</div>
				</div>
				{{assign var='image_number' value=$image_number+1}}
				{{if $image_number>$images_in_row}}
					<div class="g_clear"></div>
					{{assign var='image_number' value=1}}
				{{/if}}
			{{/foreach}}
			<div class="g_clear"></div>
		</div>
	</div>
{{/if}}