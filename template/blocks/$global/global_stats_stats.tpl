{{assign var="images_total" value=$stats.albums_images_total}}
{{assign var="models_total" value=$stats.models}}
{{assign var="videos_total" value=$stats.videos_total_duration|round}}

<div class="join-block-thin">
	<div class="container">
		<span class="join-block-thin__text">{{$lang.stats_banner.text|smarty:nodefaults|replace:"%1%":$images_total|replace:"%2%":$models_total|replace:"%3%":$videos_total}}</span>
		<a href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade}}{{else}}{{$lang.urls.signup}}{{/if}}" data-action="popup" class="btn btn--big btn--color pull-right">{{$lang.stats_banner.btn}}</a>
	</div>
</div>