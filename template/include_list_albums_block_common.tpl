<div class="thumbs">
	<div class="container">
		{{if $list_albums_hide_headline!='true'}}
			<div class="heading cfx">
				<h{{$list_albums_header_level|default:"2"}} class="title">{{$list_albums_title|mb_ucfirst}}{{if $nav.page_now>1}}{{$lang.common_list.paginated_postfix|replace:"%1%":$nav.page_now}}{{/if}}</h{{$list_albums_header_level|default:"2"}}>

				{{assign var="base_url" value=$lang.urls.albums}}
				{{if $list_type=='tags'}}
					{{assign var="base_url" value=$lang.urls.albums_by_tag|replace:"%DIR%":$tag_info.tag_dir|replace:"%ID%":$tag_info.tag_id}}
				{{elseif $list_type=='categories'}}
					{{assign var="base_url" value=$lang.urls.albums_by_category|replace:"%DIR%":$category_info.dir|replace:"%ID%":$category_info.category_id}}
				{{elseif $list_type=='models'}}
					{{assign var="base_url" value=$lang.urls.albums_by_model|replace:"%DIR%":$model_info.dir|replace:"%ID%":$model_info.model_id}}
				{{elseif $list_type=='search'}}
					{{assign var="query_url" value=$search_keyword|replace:" ":"-"|replace:"&":"%26"|replace:"?":"%3F"|replace:"/":"%2F"|rawurlencode}}
					{{assign var="base_url" value=$lang.urls.search_query_albums|replace:"%QUERY%":$query_url}}
				{{/if}}

				{{if $list_albums_show_sorting=='true'}}
					{{if count($data)>0}}
						<div class="buttons pull-right">
							{{if $list_type=='search' && $sort_by!='relevance' && $is_search_supports_relevance==1}}
								<a href="{{$base_url}}" class="btn">{{$lang.albums.list_sorting.relevance}}</a>
							{{/if}}
							{{foreach from=$lang.albums.sortings item="item"}}
								{{if $sort_by!=$item || $sort_by=='rating' || $sort_by=='album_viewed'}}
									{{if $item=='rating' && count($lang.albums.sortings_top_rated)>0}}
										<div class="dropdown__block">
											<button class="btn" data-action="drop" data-drop-id="rating_sort_drop_{{$block_uid}}">{{$lang.albums.list_sorting[$item]}}</button>
											<div class="dropdown__block__menu" id="rating_sort_drop_{{$block_uid}}">
												<nav>
													<ul class="drop-inner">
														{{foreach from=$lang.albums.sortings_top_rated item="item2"}}
															{{if $sort_by!=$item2}}
																<li>
																	<a href="{{$base_url}}?by={{$item2}}">{{$lang.albums.list_sorting_period[$item2]}}</a>
																</li>
															{{/if}}
														{{/foreach}}
													</ul>
												</nav>
											</div>
										</div>
									{{elseif $item=='album_viewed' && count($lang.albums.sortings_most_popular)>0}}
										<div class="dropdown__block">
											<button class="btn" data-action="drop" data-drop-id="views_sort_drop_{{$block_uid}}">{{$lang.albums.list_sorting[$item]}}</button>
											<div class="dropdown__block__menu" id="views_sort_drop_{{$block_uid}}">
												<nav>
													<ul class="drop-inner">
														{{foreach from=$lang.albums.sortings_most_popular item="item2"}}
															{{if $sort_by!=$item2}}
																<li>
																	<a href="{{$base_url}}?by={{$item2}}">{{$lang.albums.list_sorting_period[$item2]}}</a>
																</li>
															{{/if}}
														{{/foreach}}
													</ul>
												</nav>
											</div>
										</div>
									{{else}}
										<a href="{{$base_url}}?by={{$item}}" class="btn">{{$lang.albums.list_sorting[$item]}}</a>
									{{/if}}
								{{/if}}
							{{/foreach}}
						</div>
					{{/if}}
				{{elseif $mode_favourites=='1' && count($lang.albums.predefined_favourites)>0}}
					<div class="buttons pull-right">
						{{foreach item="item" from=$lang.albums.predefined_favourites}}
							{{if $fav_type!=$item}}
								<a href="{{$lang.urls.memberzone_my_fav_albums}}?fav_type={{$item}}" class="btn">{{$lang.albums.list_switch_favourites[$item]}} ({{$favourites_summary[$item].amount|default:"0"}})</a>
							{{/if}}
						{{/foreach}}
					</div>
				{{/if}}
			</div>
		{{/if}}
		{{if $can_manage==1 && $mode_favourites==1}}
			<form data-form="list" data-block-id="{{$block_uid}}" data-prev-url="{{$nav.previous}}">
				<div class="generic-error hidden"></div>
		{{/if}}
		{{if count($data)>0}}
			<div class="thumbs__list cfx">
				{{foreach item="item" from=$data name="albums_list"}}
					<div class="item thumb thumb--albums">
						<a {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}} title="{{$item.title}}">
							<img src="{{$item.preview_url}}/{{$lang.albums.thumb_size}}/{{$item.dir_path}}/{{$item.album_id}}/preview.jpg" alt="{{$item.title}}" width="{{$lang.albums.thumb_size|geomsize:'width'}}" height="{{$lang.albums.thumb_size|geomsize:'height'}}"/>
							<div class="thumb__info">
								<div class="thumb-spot">
									{{assign var="album_rating" value="`$item.rating/5*100`"}}
									{{if $album_rating>100}}{{assign var="album_rating" value="100"}}{{/if}}
									<div class="thumb-spot__rating rotated"><span>{{$album_rating|string_format:"%d"}}%</span></div>
									<div class="thumb-spot__text">
										<h5 class="thumb-spot__title">
											{{if $lang.albums.truncate_title_to>0}}
												{{$item.title|truncate:$lang.albums.truncate_title_to:"...":true}}
											{{else}}
												{{$item.title}}
											{{/if}}
										</h5>
										<ul class="thumb-spot__data">
											<li><i class="icon-photo-shape-8"></i>{{$lang.albums.list_label_photos|count_format:"%1%":$item.photos_amount}}</li>
											<li>{{$item.post_date|date_format:$lang.global.date_format}}</li>
											{{assign var="album_views" value=$item.album_viewed|number_format:0:",":$lang.global.number_format_delimiter}}
											<li>{{$lang.albums.list_label_views|replace:"%1%":$album_views}}</li>
										</ul>
									</div>
								</div>
							</div>
						</a>
						{{if $can_manage==1 && $mode_favourites==1}}
							<label class="checkbox__fav-label"></label>
							<input type="checkbox" data-action="select" class="checkbox checkbox__fav" name="delete[]" value="{{$item.album_id}}">
							<div class="fav_overlay"></div>
						{{/if}}
					</div>

					{{if $smarty.foreach.albums_list.index == 11 && $list_albums_show_advertisement=='true'}}
						{{include file="include_join_banner_2.tpl"}}
					{{/if}}
				{{/foreach}}
			</div>
		{{else}}
			<div class="empty-content">{{$list_albums_empty_message|default:$lang.common_list.no_data}}</div>
		{{/if}}
		{{if $can_manage==1 && $mode_favourites==1}}
			<div>
				{{if $mode_favourites==1}}
					<input type="hidden" name="function" value="get_block"/>
					<input type="hidden" name="block_id" value="{{$block_uid}}"/>
					<input type="hidden" name="action" value="delete_from_favourites"/>
					<input type="hidden" name="fav_type" value="{{$fav_type}}"/>
				{{/if}}
				{{if count($data)>0}}
					<input type="button" class="btn" value="{{$lang.albums.list_action_select_all}}" data-action="select_all"/>
					<input type="button" class="btn" value="{{$lang.albums.list_action_delete_selected}}" disabled data-mode="selection" data-action="delete_multi" data-confirm="{{$lang.albums.list_action_delete_selected_confirm_favourites}}">
				{{/if}}
			</div>
			</form>
		{{/if}}
	</div>
</div>