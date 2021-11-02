<div id="{{$block_uid}}">
	<div class="thumbs">
		<div class="container">
			<div class="heading cfx">
				<h1 class="title">{{$lang.categories.list_title_by_sorting|replace:"%1%":$lang.categories.list_sorting[$sort_by]}}{{if $nav.page_now>1}}{{$lang.common_list.paginated_postfix|replace:"%1%":$nav.page_now}}{{/if}}</h1>

				<div class="buttons pull-right">
					{{foreach from=$lang.categories.sortings item="item"}}
						{{if $sort_by!=$item}}
							<a href="{{$lang.urls.categories}}?by={{$item}}" class="btn">{{$lang.categories.list_sorting[$item]}}</a>
						{{/if}}
					{{/foreach}}
				</div>
			</div>
			{{if count($data)>0}}
				<div class="thumbs__list cfx">
					{{foreach item="item" from=$data}}
						<div class="item thumb thumb--categories">
							<a href="{{$lang.urls.content_by_category|replace:"%DIR%":$item.dir|replace:"%ID%":$item.category_id}}" title="{{$item.title}}">
								{{if $item.screenshot1!=''}}
									<img src="{{$item.base_files_url}}/{{$item.screenshot1}}" alt="{{$item.title}}" width="{{$lang.videos.thumb_size|geomsize:'width'}}" height="{{$lang.videos.thumb_size|geomsize:'height'}}"/>
								{{elseif count($item.videos)>0}}
									<img src="{{$item.videos[0].screen_url}}/{{$lang.videos.thumb_size}}/{{$item.videos[0].screen_main}}.jpg" alt="{{$item.title}}" width="{{$lang.videos.thumb_size|geomsize:'width'}}" height="{{$lang.videos.thumb_size|geomsize:'height'}}"/>
								{{else}}
									<img src="{{$config.statics_url}}/static/images/no-avatar-categorie.jpg" alt="{{$item.title}}"/>
									<span class="no-avatar">
										<span>{{$lang.categories.list_label_no_image}}</span>
									</span>
								{{/if}}
								<div class="thumb__info">
									<div class="thumb-spot">
										{{assign var="category_rating" value="`$item.avg_videos_rating/5*100`"}}
										{{if $category_rating>100}}{{assign var="category_rating" value="100"}}{{/if}}
										<div class="thumb-spot__rating rotated"><span>{{$category_rating|string_format:"%d"}}%</span></div>
										<div class="thumb-spot__text">
											<h5 class="thumb-spot__title">{{$item.title}}</h5>
											<ul class="thumb-spot__data">
												<li><i class="icon-camera-shape-10"></i>{{$item.total_videos}}</li>
												<li><i class="icon-photo-shape-8"></i>{{$item.total_albums}}</li>
												{{assign var="category_views" value=$item.avg_videos_popularity|round|number_format:0:",":$lang.global.number_format_delimiter}}
												<li>{{$lang.categories.list_label_views|replace:"%1%":$category_views}}</li>
											</ul>
										</div>
									</div>
								</div>
							</a>
						</div>
					{{/foreach}}
				</div>
			{{else}}
				<div class="empty-content">{{$lang.common_list.no_data}}</div>
			{{/if}}
		</div>

		{{include file="include_pagination_block_common.tpl"}}
	</div>
</div>