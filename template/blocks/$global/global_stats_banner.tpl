{{assign var="images_total" value=$stats.albums_images_total}}
{{assign var="models_total" value=$stats.models}}
{{assign var="videos_total" value=$stats.videos_total_duration|round}}

<div class="join-spot-01">
	<div class="container">
		<div class="join-spot-01__text">
			<h2 class="join-spot-01__title">{{$lang.join_banner1.title}}</h2>
			<p class="join-spot-01__subtitle">{{$lang.join_banner1.text|smarty:nodefaults|replace:"%1%":$images_total|replace:"%2%":$models_total|replace:"%3%":$videos_total}}</p>
			<a href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade}}{{else}}{{$lang.urls.signup}}{{/if}}" data-action="popup" class="btn btn--huge btn--green">{{$lang.join_banner1.btn}}</a>
		</div>
	</div>
</div>