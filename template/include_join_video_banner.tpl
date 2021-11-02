{{if $data.can_watch==0}}
	{{if $smarty.session.user_id<1}}
		<a href="{{$lang.urls.login_required}}" class="media-container__join" data-action="popup">
	{{elseif $data.tokens_required>0 && $smarty.session.tokens_available>=$data.tokens_required}}
		<a href="#unlock" class="media-container__join" data-action="purchase" data-video-id="{{$data.video_id}}">
	{{else}}
		<a href="{{$lang.urls.upgrade_required}}" class="media-container__join" data-action="popup">
	{{/if}}
{{else}}
	<a class="media-container__join" href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade_required}}{{else}}{{$lang.urls.login_required}}{{/if}}" data-action="popup">
{{/if}}
	<img class="media-container__join__img" src="{{$storage.video_view_video_view.preview_url}}" alt="{{$data.title}}">
	<span class="media-container__join__text">
		<em class="generic-error hidden"></em>
		<strong>{{$lang.videos.video_player_banner_click}}</strong>
		<span>{{$join_message}}</span>
		<i class="icon-click"></i>
	</span>
</a>