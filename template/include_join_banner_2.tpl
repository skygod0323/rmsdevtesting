{{if $smarty.session.status_id!=3}}
	<div class="join-spot-02">
		<div class="container">
			<div class="join-spot-02__text">
				<h2 class="join-spot-02__title">{{$lang.join_banner2.title}}</h2>
				<p class="join-spot-02__subtitle">{{$lang.join_banner2.subtitle}}</p>
				<a href="{{if $smarty.session.status_id>0}}{{$lang.urls.upgrade}}{{else}}{{$lang.urls.signup}}{{/if}}" data-action="popup" class="btn btn--duble">
					<strong>{{$lang.join_banner2.btn1}}</strong>
					<span>{{$lang.join_banner2.btn2}}</span>
				</a>
			</div>
		</div>
	</div>
{{/if}}