<!doctype html>
<html lang="{{$config.locale|default:$lang.header.default_lang}}">
<head>
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<title>{{$page_title|mb_ucfirst|default:$lang.html.default_title}}</title>
	<meta name="description" content="{{$page_description|mb_ucfirst|default:$lang.html.default_description}}">
	<meta name="keywords" content="{{$page_keywords|trim:" ,"|replace:", ,":","|replace:",,":","|default:$lang.html.default_keywords}}">
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
	<meta name="HandheldFriendly" content="true">

	<link rel="icon" href="{{$config.project_url}}/favicon.ico" type="image/x-icon">
	<link rel="shortcut icon" href="{{$config.project_url}}/favicon.ico" type="image/x-icon">

	<link href='https://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>
	{{if $lang.theme.style=='white'}}
		<link href="{{$config.statics_url}}/static/styles/jquery.fancybox.css?v=1.5" rel="stylesheet" type="text/css"/>
		<link href="{{$config.statics_url}}/static/styles/all.css?v=1.5" rel="stylesheet" type="text/css"/>
	{{else}}
		<link href="{{$config.statics_url}}/static/styles/jquery.fancybox-dark.css?v=1.5" rel="stylesheet" type="text/css"/>
		<link href="{{$config.statics_url}}/static/styles/all-dark.css?v={{$smarty.now}}" rel="stylesheet" type="text/css"/>
	{{/if}}
        <link href="{{$config.statics_url}}/static/styles/ion.rangeSlider.css" rel="stylesheet" type="text/css"/>
        <link href="{{$config.statics_url}}/static/styles/custom.css?v={{$smarty.now}}" rel="stylesheet" type="text/css"/>
	<script>
		var pageContext = {
			{{if $config.disable_stats=='true'}}disableStats: true,{{/if}}
			{{if $smarty.session.user_id>0}}userId: '{{$smarty.session.user_id}}', {{/if}}
			{{if $storage.video_view_video_view.video_id>0}}videoId: '{{$storage.video_view_video_view.video_id}}', {{/if}}
			{{if $storage.album_view_album_view.album_id>0}}albumId: '{{$storage.album_view_album_view.album_id}}', {{/if}}
			loginUrl: '{{$lang.urls.login_required}}'
		};
	</script>
	{{if $page_rss!=''}}
		<link href="{{$page_rss}}" rel="alternate" type="application/rss+xml"/>
	{{/if}}
	{{if $page_canonical!=''}}
		<link href="{{$page_canonical}}" rel="canonical"/>
		{{foreach from=$lang.header.language item="item"}}
			{{if $item.url!=''}}
				{{assign var="page_canonical_truncated" value=$page_canonical|replace:$config.project_url:""}}
				<link href="{{$item.url}}{{$page_canonical_truncated}}" rel="alternate" hreflang="{{$item.code}}"/>
				{{if $item.code==$lang.header.lang_default}}
					<link href="{{$item.url}}{{$page_canonical_truncated}}" rel="alternate" hreflang="x-default"/>
				{{/if}}
			{{else}}
				{{if $item.code==$lang.header.lang_default}}
					<link href="{{$page_canonical}}" rel="alternate" hreflang="{{$item.code}}"/>
					<link href="{{$page_canonical}}" rel="alternate" hreflang="x-default"/>
				{{else}}
					<link href="{{$page_canonical}}?kt_lang={{$item.code}}" rel="alternate" hreflang="{{$item.code}}"/>
				{{/if}}
			{{/if}}
		{{/foreach}}
	{{/if}}
	{{if $page_og_title!=''}}
		<meta property="og:title" content="{{$page_og_title|mb_ucfirst}}"/>
	{{/if}}
	{{if $page_og_image!=''}}
		<meta property="og:image" content="{{$page_og_image}}"/>
	{{/if}}
	{{if $page_og_description!=''}}
		<meta property="og:description" content="{{$page_og_description|mb_ucfirst}}"/>
	{{/if}}
</head>

<body>
<div class="wrapper">
	<header class="header cfx">
		<div class="container">
			<h1 class="logo">
				<a href="{{$lang.urls.home}}"><span class="mark">{{$lang.logo_mark}}</span> {{$lang.logo_text}}</a>
			</h1>
			<div class="header__options">
				<button class="mobile-menu-opener" data-action="mobile"><i class="icon-three-bars"></i></button>
				<nav class="main-menu" data-navigation="true">
					<ul class="main-menu__list">
						<li class="main-menu__item {{if $page_id=='videos_list' || ($page_id=='search' && $search_type=='videos')}}active{{/if}}">
							<a href="{{$lang.urls.videos}}">{{$lang.header.primary_menu_videos}}</a>
						</li>
						{{if $lang.enable_albums=='true'}}
							<li class="main-menu__item {{if $page_id=='albums_list' || ($page_id=='search' && $search_type=='albums')}}active{{/if}}">
								<a href="{{$lang.urls.albums}}">{{$lang.header.primary_menu_albums}}</a>
							</li>
						{{/if}}
						{{if $lang.enable_categories=='true'}}
							<li class="main-menu__item {{if $page_id=='categories_list'}}active{{/if}}">
								<a href="{{$lang.urls.categories}}">{{$lang.header.primary_menu_categories}}</a>
							</li>
						{{/if}}

						{{if $smarty.session.user_id>0}}
							{{if $lang.features_access.favourite=='members'}}
								<li class="main-menu__item {{if $page_id=='list_videos_my_favourite_videos'}}active{{/if}}">
									<a href="{{$lang.urls.memberzone_my_fav_videos}}">{{$lang.header.member_menu_fav_videos}}</a>
								</li>
							{{/if}}

							<li class="main-menu__item ">
								<a href="#">{{$lang.header.primary_menu_channels}}</a>
							</li>
							<li class="main-menu__item ">
								<a href="{{$lang.urls.memberzone_upload_video}}">{{$lang.header.primary_menu_upload}}</a>
							</li>
						{{/if}}
						{{if $lang.enable_header_network=='true'}}
							<li class="main-menu__item">
								<a>{{$lang.header.primary_menu_network}}</a>
								<ul  class="main-menu__drop">
									{{if is_array($lang.header.network_sites)}}
										{{foreach item="item" from=$lang.header.network_sites}}
											<li class="main-menu__drop__item"><a href="{{$item.link}}" class="hl">{{$item.name}}</a></li>
										{{/foreach}}
									{{/if}}
								</ul>
							</li>
						{{/if}}
						{{if $smarty.session.status_id==''}}
							<li class="main-menu__item">
								<a class="premium-link" href="{{$lang.urls.signup}}" data-action="popup">{{$lang.header.primary_menu_get_premium}}</a>
							</li>
						{{elseif $smarty.session.status_id==2}}
							<li class="main-menu__item">
								<a class="premium-link" href="{{$lang.urls.upgrade}}" data-action="popup">{{$lang.header.primary_menu_get_premium}}</a>
							</li>
						{{/if}}
						{{if $smarty.session.user_id>0}}
							<li class="main-menu__item main-menu__user-menu">
								<a href="#">{{$smarty.session.display_name|truncate:15:"...":true}} <i class="icon-chevron-down"></i></a>
								<ul  class="main-menu__drop">
									<li class="main-menu__drop__item main-menu__drop__item--alt">
										{{if $member_page_type!='profile'}}
											<a href="{{$lang.urls.memberzone_my_profile}}">
												<span class="main-menu__user-image rotated">
													{{if $smarty.session.avatar_url!=''}}
														<img src="{{$smarty.session.avatar_url}}" width="45" height="45" alt="{{$smarty.session.display_name}}"/>
													{{/if}}
												</span>
												{{$lang.header.member_menu_profile}}
											</a>
										{{else}}
											<span>
												<span class="main-menu__user-image rotated">
													{{if $smarty.session.avatar_url!=''}}
														<img src="{{$smarty.session.avatar_url}}" width="45" height="45" alt="{{$smarty.session.display_name}}"/>
													{{/if}}
												</span>
												{{$lang.header.member_menu_profile}}
											</span>
										{{/if}}
									</li>
									<li class="main-menu__drop__item">
										{{if $member_page_type!='playlists'}}
											<a {{if $lang.features_access.favourite=='members' || $smarty.session.status_id==3}}href="{{$lang.urls.memberzone_my_playlists}}"{{else}}href="{{$lang.urls.upgrade_required}}" data-action="popup"{{/if}}>
												{{$lang.header.member_menu_playlists}} ({{$smarty.session.playlists_amount}})
											</a>
										{{else}}
											<span>
												{{$lang.header.member_menu_playlists}} ({{$smarty.session.playlists_amount}})
											</span>
										{{/if}}
									</li>
									<li class="main-menu__drop__item">
										{{assign var="fav_videos_count" value=0}}
										{{foreach item="item" from=$lang.videos.predefined_favourites}}
											{{if $smarty.session.favourite_videos_summary[$item].amount>0}}
												{{assign var="fav_videos_count" value=$fav_videos_count+$smarty.session.favourite_videos_summary[$item].amount}}
											{{/if}}
										{{/foreach}}
										{{if $member_page_type!='fav_videos'}}
											<a {{if $lang.features_access.favourite=='members' || $smarty.session.status_id==3}}href="{{$lang.urls.memberzone_my_fav_videos}}"{{else}}href="{{$lang.urls.upgrade_required}}" data-action="popup"{{/if}}>
												{{$lang.header.member_menu_fav_videos}} ({{$fav_videos_count}})
											</a>
										{{else}}
											<span>
												{{$lang.header.member_menu_fav_videos}} ({{$fav_videos_count}})
											</span>
										{{/if}}
									</li>
									{{if $lang.enable_albums=='true'}}
										<li class="main-menu__drop__item">
											{{assign var="fav_albums_count" value=0}}
											{{foreach item="item" from=$lang.albums.predefined_favourites}}
												{{if $smarty.session.favourite_albums_summary[$item].amount>0}}
													{{assign var="fav_albums_count" value=$fav_albums_count+$smarty.session.favourite_albums_summary[$item].amount}}
												{{/if}}
											{{/foreach}}
											{{if $member_page_type!='fav_albums'}}
												<a {{if $lang.features_access.favourite=='members' || $smarty.session.status_id==3}}href="{{$lang.urls.memberzone_my_fav_albums}}"{{else}}href="{{$lang.urls.upgrade_required}}" data-action="popup"{{/if}}>
													{{$lang.header.member_menu_fav_albums}} ({{$fav_albums_count}})
												</a>
											{{else}}
												<span>
													{{$lang.header.member_menu_fav_albums}} ({{$fav_albums_count}})
												</span>
											{{/if}}
										</li>
									{{/if}}
									{{if $lang.enable_models=='true'}}
										<li class="main-menu__drop__item">
											{{if $member_page_type!='fav_models'}}
												<a {{if $lang.features_access.favourite=='members' || $smarty.session.status_id==3}}href="{{$lang.urls.memberzone_my_fav_models}}"{{else}}href="{{$lang.urls.upgrade_required}}" data-action="popup"{{/if}}>
													{{$lang.header.member_menu_fav_models}} ({{$smarty.session.subscriptions_amount}})
												</a>
											{{else}}
												<span>
													{{$lang.header.member_menu_fav_models}} ({{$smarty.session.subscriptions_amount}})
												</span>
											{{/if}}
										</li>
									{{/if}} 
									{{if $config.dvds_count>0}}
										<li class="main-menu__drop__item">
											<a href="{{$lang.urls.videos_by_channel|replace:"%DIR%":$config.dvds.dir}}">My Channel</a> 
										</li>
									{{else}}
										{{* <li class="main-menu__drop__item">
											<a href="{{$lang.urls.memberzone_create_channel}}" data-action="popup">{{$lang.channels.list_action_create_channel}}</a> 
										</li> *}}
									{{/if}}

									<li class="main-menu__drop__item"><a href="{{$lang.urls.logout}}">{{$lang.header.primary_menu_logout}}</a></li>
								</ul>
							</li>
						{{else}}
							<li class="main-menu__item main-menu__item--login">
								<a id="login_link" href="{{$lang.urls.login}}" data-action="popup">{{$lang.header.primary_menu_login}}</a>
							</li>
						{{/if}}
					</ul>
				</nav>
				<div class="search-bar">
					<a href="#" class="search-bar__button" data-action="drop" data-drop-id="search_drop"><i class="icon-search"></i></a>
					<div id="search_drop" class="search-bar__form">
						<form id="search_form" action="{{$lang.urls.search}}" method="get" data-url="{{if $search_type=='videos' || $search_type==''}}{{$lang.urls.search_query_videos}}{{elseif $search_type=='albums'}}{{$lang.urls.search_query_albums}}{{elseif $search_type=='models'}}{{$lang.urls.search_query_models}}{{/if}}">
							<input class="search-bar__input pull-left" name="q" type="text" value="{{$search_query}}" placeholder="{{$lang.search_box.search_hint}}">
							<div class="search-bar__filter pull-left">
								<a href="#" class="search-bar__filter-button" data-action="drop" data-drop-id="search_options_drop">
									{{if $search_type=='videos' || $search_type==''}}
										<i class="icon-camera-shape-10" data-search-type-icon></i>
									{{elseif $search_type=='albums'}}
										<i class="icon-photo-shape-8" data-search-type-icon></i>
									{{elseif $search_type=='models'}}
										<i class="icon-user-shape-14" data-search-type-icon></i>
									{{/if}}
									<i class="icon-chevron-down"></i>
								</a>
								<ul class="search-bar__drop" id="search_options_drop">
									<li>
										<input type="radio" name="for" value="videos" id="search_type_videos" data-url="{{$lang.urls.search_query_videos}}" {{if $search_type=='videos' || $search_type==''}}checked{{/if}}>
										<label for="search_type_videos">
											<span>{{$lang.header.primary_menu_videos}}</span>
											<i class="icon-camera-shape-10" data-search-type-icon></i>
										</label>
									</li>
									{{if $lang.enable_albums=='true'}}
										<li>
											<input type="radio" name="for" value="albums" id="search_type_albums" data-url="{{$lang.urls.search_query_albums}}" {{if $search_type=='albums'}}checked{{/if}}>
											<label for="search_type_albums">
												<span>{{$lang.header.primary_menu_albums}}</span>
												<i class="icon-photo-shape-8" data-search-type-icon></i>
											</label>
										</li>
									{{/if}}
									{{if $lang.enable_models=='true'}}
										<li>
											<input type="radio" name="for" value="models" id="search_type_models" data-url="{{$lang.urls.search_query_models}}" {{if $search_type=='models'}}checked{{/if}}>
											<label for="search_type_models">
												<span>{{$lang.header.primary_menu_models}}</span>
												<i class="icon-user-shape-14" data-search-type-icon></i>
											</label>
										</li>
									{{/if}}
								</ul>
							</div>
							<button class="search-bar__submit pull-left" type="submit"><i class="icon-search"></i></button>
						</form>
					</div>
				</div>
				{{if is_array($lang.header.language)}}
					<div class="lang-select">
						<div class="dropdown__block align-right cfg-select">
							<div data-action="drop" data-drop-id="languages_drop">
								{{foreach item="item" from=$lang.header.language}}
									{{if $lang.header.default_lang==$item.code}}
										<img src="{{$config.statics_url}}/static/images/flags/{{$item.flag}}.png" alt="{{$item.name}}" title="{{$item.name}}">
									{{/if}}
								{{/foreach}}
							</div>
							<div class="dropdown__block__menu" id="languages_drop">
								<nav>
									<em></em>
									<ul class="drop-inner">
										{{foreach item="item" from=$lang.header.language}}
											{{if $lang.header.default_lang!=$item.code}}
												<li>
													{{if $item.url!=''}}
														{{assign var="page_canonical_truncated" value=$page_canonical|replace:$config.project_url:""}}
														<a href="{{$item.url}}{{$page_canonical_truncated}}">
															<img src="{{$config.statics_url}}/static/images/flags/{{$item.flag}}.png" class="cfg-icon" alt="{{$item.flag}}"/>{{$item.name}}
														</a>
													{{else}}
														<a href="{{$page_canonical}}?kt_lang={{$item.code}}">
															<img src="{{$config.statics_url}}/static/images/flags/{{$item.flag}}.png" class="cfg-icon" alt="{{$item.flag}}"/>{{$item.name}}
														</a>
													{{/if}}
												</li>
											{{/if}}
										{{/foreach}}
									</ul>
								</nav>
							</div>
						</div>
					</div>
				{{/if}}
			</div>
		</div>
	</header>
	<div class="main">