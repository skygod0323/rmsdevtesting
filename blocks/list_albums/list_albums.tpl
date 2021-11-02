<div class="list_albums">
	<h1 class="block_header">Albums List</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} albums
			</div>
		{{/if}}
		<div class="block_content">
			{{if $can_manage==1}}
				{{if $smarty.get.action=='delete_done'}}
					<p class="topmost message_info">
						{{if $mode_favourites==1}}
							The selected album(s) have been removed from your favourites list.
						{{elseif $mode_uploaded==1}}
							The selected album(s) have been removed from our website.
						{{/if}}
					</p>
				{{elseif $smarty.get.action=='delete_forbidden'}}
					<p class="topmost message_info">
						Sorry, you should contact website support if you want your albums to be removed.
					</p>
				{{/if}}
				<form id="delete_albums_form" action="" method="post">
				{{if $mode_favourites==1}}
					<input type="hidden" name="action" value="delete_from_favourites"/>
					{{if $fav_type>0}}
						<input type="hidden" name="fav_type" value="{{$fav_type}}"/>
					{{/if}}
				{{elseif $mode_uploaded==1}}
					<input type="hidden" name="action" value="delete_from_uploaded"/>
				{{/if}}
			{{/if}}
			{{foreach name=data item=item from=$data}}
			<div class="item">
				<div class="image {{if $item.is_private==1}}private{{elseif $item.is_private==2}}premium{{/if}}" {{if $item.is_private==1 || $item.is_private==2}}style="background: url({{$item.preview_url}}/120x160/{{$item.dir_path}}/{{$item.album_id}}/preview.jpg) left top no-repeat"{{/if}}>
					{{if $mode_uploaded==1}}
						{{if $item.status_id<>1 && $item.status_id<>0}}
							<img src="{{$config.project_url}}/images/bg_disabled_album.gif" alt="Disabled album"/>
						{{else}}
							<a href="{{$config.project_url}}/my_album_edit/{{$item.album_id}}/" title="{{$item.title}}"><img class="thumb" src="{{$item.preview_url}}/120x160/{{$item.dir_path}}/{{$item.album_id}}/preview.jpg" alt="{{$item.title}}"/></a>
						{{/if}}
					{{elseif $item.is_private==1}}
						<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img src="{{$config.project_url}}/images/bg_private_album.gif" alt="Private album"/></a>
					{{elseif $item.is_private==2}}
						<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img src="{{$config.project_url}}/images/bg_premium_album.gif" alt="Premium album"/></a>
					{{else}}
						<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img class="thumb" src="{{$item.preview_url}}/120x160/{{$item.dir_path}}/{{$item.album_id}}/preview.jpg" alt="{{$item.title}}"/></a>
					{{/if}}
				</div>
				<div class="info">
					<h2>
						{{if $mode_uploaded==1}}
							{{if $item.status_id<>1 && $item.status_id<>0}}
								{{$item.title|truncate:18:"...":true}}
							{{else}}
								<a href="{{$config.project_url}}/my_album_edit/{{$item.album_id}}/" title="{{$item.title}}">{{$item.title|truncate:18:"...":true}}</a>
							{{/if}}
						{{else}}
							<a href="{{$item.view_page_url}}" title="{{$item.title}}" class="hl">{{$item.title|truncate:18:"...":true}}</a>
						{{/if}}
					</h2>
					{{assign var="added_date" value=$item.post_date}}
					{{if $mode_favourites==1}}
						{{assign var="added_date" value=$item.added2fav_date}}
					{{/if}}
					<div class="added">Added: <span>{{$added_date|date_format:"%text"}}</span></div>
					<div class="images">{{$item.photos_amount}} pics</div>
					<div class="rating">
						{{strip}}
							{{if $item.rating>0}}{{if $item.rating<=0.5}}<img src="{{$config.project_url}}/images/star_small_half.gif" alt="1"/>{{else}}<img src="{{$config.project_url}}/images/star_small_full.gif" alt="1"/>{{/if}}{{else}}<img src="{{$config.project_url}}/images/star_small_empty.gif" alt="1"/>{{/if}}
							{{if $item.rating>1}}{{if $item.rating<=1.5}}<img src="{{$config.project_url}}/images/star_small_half.gif" alt="2"/>{{else}}<img src="{{$config.project_url}}/images/star_small_full.gif" alt="2"/>{{/if}}{{else}}<img src="{{$config.project_url}}/images/star_small_empty.gif" alt="2"/>{{/if}}
							{{if $item.rating>2}}{{if $item.rating<=2.5}}<img src="{{$config.project_url}}/images/star_small_half.gif" alt="3"/>{{else}}<img src="{{$config.project_url}}/images/star_small_full.gif" alt="3"/>{{/if}}{{else}}<img src="{{$config.project_url}}/images/star_small_empty.gif" alt="3"/>{{/if}}
							{{if $item.rating>3}}{{if $item.rating<=3.5}}<img src="{{$config.project_url}}/images/star_small_half.gif" alt="4"/>{{else}}<img src="{{$config.project_url}}/images/star_small_full.gif" alt="4"/>{{/if}}{{else}}<img src="{{$config.project_url}}/images/star_small_empty.gif" alt="4"/>{{/if}}
							{{if $item.rating>4}}{{if $item.rating<=4.5}}<img src="{{$config.project_url}}/images/star_small_half.gif" alt="5"/>{{else}}<img src="{{$config.project_url}}/images/star_small_full.gif" alt="5"/>{{/if}}{{else}}<img src="{{$config.project_url}}/images/star_small_empty.gif" alt="5"/>{{/if}}
						{{/strip}}
					</div>
					<div class="g_clear"></div>
					{{if $can_manage==1}}
						<div class="options">
							{{if $mode_uploaded==1}}
								<input id="delete_{{$item.album_id}}" type="checkbox" name="delete[]" value="{{$item.album_id}}" {{if $item.is_locked==1}}disabled="disabled"{{/if}}/> <label for="delete_{{$item.album_id}}">delete</label>
							{{else}}
								<input id="delete_{{$item.album_id}}" type="checkbox" name="delete[]" value="{{$item.album_id}}"/> <label for="delete_{{$item.album_id}}">delete</label>
							{{/if}}
						</div>
					{{/if}}
				</div>
			</div>
			{{/foreach}}
			<div class="g_clear"></div>
			{{if $can_manage==1}}
				<div class="actions">
					<input type="image" src="{{$config.project_url}}/images/btn_delete_selected.gif"/>
				</div>
				</form>
				<script type="text/javascript">
					var params = {};
					params['form_id'] = 'delete_albums_form';
					params['delete_confirmation_text'] = 'Are you sure to delete %1% selected album(s)?';
					params['no_items_selected'] = 'Nothing is selected!';
					listAlbumsEnableDeleteForm(params);
				</script>
			{{/if}}
		</div>
	{{else}}
		<div class="text_content">
			There are no albums in the list.
		</div>
	{{/if}}
</div>