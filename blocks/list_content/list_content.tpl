<div class="list_content">
	<h1 class="block_header">Content List</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} items
			</div>
		{{/if}}
		<div class="block_content">
			{{if $can_manage==1}}
				{{if $smarty.get.action=='delete_done'}}
					<p class="topmost message_info">
						The selected item(s) have been removed from your favourites list.
					</p>
				{{/if}}
				<form id="delete_content_form" action="" method="post">
					<input type="hidden" name="action" value="delete_from_favourites"/>
					{{if $fav_type>0}}
						<input type="hidden" name="fav_type" value="{{$fav_type}}"/>
					{{/if}}
					{{if $playlist_id>0}}
						<input type="hidden" name="playlist_id" value="{{$playlist_id}}"/>
					{{/if}}
			{{/if}}
			{{foreach name=data item=item from=$data}}
			<div class="item">
				{{if $item.object_type==1}}
					<div class="image {{if $item.is_private==1}}private{{elseif $item.is_private==2}}premium{{/if}}" {{if $item.is_private==1 || $item.is_private==2}}style="background: url({{$item.screen_base_url}}/240x180/{{$item.main_image}}.jpg) left top no-repeat"{{/if}}>
						{{if $item.is_private==1}}
							<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img src="{{$config.project_url}}/images/bg_private_video.gif" alt="Private video"/></a>
						{{elseif $item.is_private==2}}
							<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img src="{{$config.project_url}}/images/bg_premium_video.gif" alt="Premium video"/></a>
						{{else}}
							<a href="{{$item.view_page_url}}" class="kt_imgrc" title="{{$item.title}}"><img class="thumb" src="{{$item.screen_base_url}}/240x180/{{$item.main_image}}.jpg" alt="{{$item.title}}" {{if $item.images_amount>1}}onmouseover="KT_rotationStart(this, '{{$item.screen_base_url}}/240x180/', {{$item.images_amount}})" onmouseout="KT_rotationStop(this)"{{/if}}/></a>
						{{/if}}
					</div>
					<div class="info">
						<h2>
							<a href="{{$item.view_page_url}}" title="{{$item.title}}" class="hl">{{$item.title|truncate:23:"...":true}}</a>
						</h2>
						<div class="length">{{$item.duration_array.minutes}}m:{{$item.duration_array.seconds}}s</div>
						<div class="g_clear"></div>
						{{assign var="added_date" value=$item.post_date}}
						{{if $mode_favourites==1}}
							{{assign var="added_date" value=$item.added2fav_date}}
						{{/if}}
						<div class="added">Added: <span>{{$added_date|date_format:"%text"}}</span></div>
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
								<input id="delete_video_{{$item.object_id}}" type="checkbox" name="delete_video_ids[]" value="{{$item.object_id}}"/> <label for="delete_video_{{$item.object_id}}">delete</label>
							</div>
						{{/if}}
					</div>
				{{elseif $item.object_type==2}}
					<div class="image {{if $item.is_private==1}}private{{elseif $item.is_private==2}}premium{{/if}}" {{if $item.is_private==1 || $item.is_private==2}}style="background: url({{$item.image_base_url}}/120x160/{{$item.dir_path}}/{{$item.object_id}}/preview.jpg) left top no-repeat"{{/if}}>
						{{if $item.is_private==1}}
							<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img src="{{$config.project_url}}/images/bg_private_album.gif" alt="Private album"/></a>
						{{elseif $item.is_private==2}}
							<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img src="{{$config.project_url}}/images/bg_premium_album.gif" alt="Premium album"/></a>
						{{else}}
							<a href="{{$item.view_page_url}}" title="{{$item.title}}"><img class="thumb" src="{{$item.image_base_url}}/120x160/{{$item.dir_path}}/{{$item.object_id}}/preview.jpg" alt="{{$item.title}}"/></a>
						{{/if}}
					</div>
					<div class="info">
						<h2>
							<a href="{{$item.view_page_url}}" title="{{$item.title}}" class="hl">{{$item.title|truncate:18:"...":true}}</a>
						</h2>
						{{assign var="added_date" value=$item.post_date}}
						{{if $mode_favourites==1}}
							{{assign var="added_date" value=$item.added2fav_date}}
						{{/if}}
						<div class="added">Added: <span>{{$added_date|date_format:"%text"}}</span></div>
						<div class="images">{{$item.images_amount}} pics</div>
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
								<input id="delete_album_{{$item.object_id}}" type="checkbox" name="delete_album_ids[]" value="{{$item.object_id}}"/> <label for="delete_album_{{$item.object_id}}">delete</label>
							</div>
						{{/if}}
					</div>
				{{/if}}
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
					params['form_id'] = 'delete_content_form';
					params['delete_confirmation_text'] = 'Are you sure to delete %1% selected item(s)?';
					params['no_items_selected'] = 'Nothing is selected!';
					listContentEnableDeleteForm(params);
				</script>
			{{/if}}
		</div>
	{{else}}
		<div class="text_content">
			There are no items in the list.
		</div>
	{{/if}}
</div>