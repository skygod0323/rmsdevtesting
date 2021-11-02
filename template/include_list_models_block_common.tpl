<div class="thumbs">
	<div class="container">
		{{if $list_models_hide_headline!='true'}}
			<div class="heading cfx">
				<h{{$list_models_header_level|default:"2"}} class="title">{{$list_models_title|mb_ucfirst}}{{if $nav.page_now>1}}{{$lang.common_list.paginated_postfix|replace:"%1%":$nav.page_now}}{{/if}}</h{{$list_models_header_level|default:"2"}}>

				{{assign var="base_url" value=$lang.urls.models}}
				{{if $list_type=='section'}}
					{{assign var="base_url" value=$lang.urls.models_by_section|replace:"%DIR%":$section}}
				{{elseif $list_type=='search'}}
					{{assign var="query_url" value=$search_keyword|replace:" ":"-"|replace:"&":"%26"|replace:"?":"%3F"|replace:"/":"%2F"|rawurlencode}}
					{{assign var="base_url" value=$lang.urls.search_query_models|replace:"%QUERY%":$query_url}}
				{{/if}}

				{{if $list_models_show_sorting=='true'}}
					<div class="buttons pull-right">
						{{if count($data)>0}}
							{{foreach from=$lang.models.sortings item="item"}}
								{{if $sort_by!=$item}}
									<a href="{{$base_url}}?by={{$item}}" class="btn">{{$lang.models.list_sorting[$item]}}</a>
								{{/if}}
							{{/foreach}}
						{{/if}}
						{{if $list_type!='search'}}
							<div class="dropdown__block align-right">
								<button class="btn" data-action="drop" data-drop-id="alphabet_drop_{{$block_uid}}">{{$lang.models.alphabet_title}}</button>
								<div class="dropdown__block__menu" id="alphabet_drop_{{$block_uid}}">
									<nav class="wide">
										<ul class="drop-inner drop-inner--az">
											{{foreach item="item" from=$lang.models.alphabet_letters}}
												<li> <a href="{{$lang.urls.models_by_section|replace:"%DIR%":$item}}?by={{$sort_by}}">{{$item}}</a> </li>
											{{/foreach}}
											<li> <a href="{{$lang.urls.models}}?by={{$sort_by}}" class="all">{{$lang.models.alphabet_all}}</a> </li>
										</ul>
									</nav>
								</div>
							</div>
						{{/if}}
					</div>
				{{/if}}
			</div>
		{{/if}}

		{{if $can_manage==1}}
			<form data-form="list" data-block-id="{{$block_uid}}" data-prev-url="{{$nav.previous}}">
				<div class="generic-error hidden"></div>
		{{/if}}
		{{if count($data)>0}}
			<div class="thumbs__list cfx">
				{{foreach item="item" from=$data name="models_list"}}
					{{if $item.subscription_id>0}}
						{{* if this list is rendered from subscriptions*}}
						{{assign var="subscription_id" value=$item.subscription_id}}
						{{assign var="item" value=$item.model}}
					{{/if}}
					<div class="item thumb thumb--models">
						<a href="{{$lang.urls.content_by_model|replace:"%DIR%":$item.dir|replace:"%ID%":$item.model_id}}" title="{{$item.title}}">
							{{if $item.screenshot1!=''}}
								<img src="{{$item.base_files_url}}/{{$item.screenshot1}}" alt="{{$item.title}}" width="{{$lang.models.thumb_size|geomsize:'width'}}" height="{{$lang.models.thumb_size|geomsize:'height'}}"/>
							{{else}}
								<img src="{{$config.statics_url}}/static/images/no-avatar-model.jpg" alt="{{$item.title}}">
								<span class="no-avatar">
									<span>{{$lang.models.list_label_no_image}}</span>
								</span>
							{{/if}}
							<div class="thumb__info">
								<div class="thumb-spot">
									{{assign var="model_rating" value="`$item.rating/5*100`"}}
									{{if $model_rating>100}}{{assign var="model_rating" value="100"}}{{/if}}
									<div class="thumb-spot__rating rotated"><span>{{$model_rating|string_format:"%d"}}%</span></div>
									<div class="thumb-spot__text">
										<h5 class="thumb-spot__title">{{$item.title}}</h5>
										<ul class="thumb-spot__data">
											<li><i class="icon-camera-shape-10"></i>{{$item.total_videos}}</li>
											<li><i class="icon-photo-shape-8"></i>{{$item.total_albums}}</li>
											{{assign var="model_views" value=$item.model_viewed|number_format:0:",":$lang.global.number_format_delimiter}}
											<li>{{$lang.models.list_label_views|replace:"%1%":$model_views}}</li>
										</ul>
									</div>
								</div>
							</div>
						</a>
						{{if $can_manage==1 && $subscription_id>0}}
							<label class="checkbox__fav-label"></label>
							<input type="checkbox" class="checkbox checkbox__fav" name="delete[]" data-action="select" value="{{$subscription_id}}">
							<div class="fav_overlay"></div>
						{{/if}}
					</div>
					{{if $smarty.foreach.models_list.index == 11  && $list_models_show_advertisement=='true'}}
						{{include file="include_join_banner_2.tpl"}}
					{{/if}}
				{{/foreach}}
			</div>
		{{else}}
			<div class="empty-content">{{$lang.common_list.no_data}}</div>
		{{/if}}
		{{if $can_manage==1}}
			<div>
				<input type="hidden" name="function" value="get_block"/>
				<input type="hidden" name="block_id" value="{{$block_uid}}"/>
				<input type="hidden" name="action" value="delete_subscriptions"/>
				{{if count($data)>0}}
					<input type="button" class="btn" value="{{$lang.models.list_action_select_all}}" data-action="select_all"/>
					<input type="button" class="btn" value="{{$lang.models.list_action_delete_selected}}" disabled data-mode="selection" data-action="delete_multi" data-confirm="{{$lang.models.list_action_delete_selected_confirm_subscriptions}}">
				{{/if}}
			</div>
			</form>
		{{/if}}
	</div>
</div>