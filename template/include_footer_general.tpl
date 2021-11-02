</div>{{* main *}}
{{if $smarty.session.status_id!=3}}
	{{insert name="getGlobal" global_id="global_stats_stats"}}
{{/if}}
<footer class="footer">
	<div class="container">
		<h1 class="logo pull-left">
			<a href="{{$lang.urls.home}}"><span class="mark">{{$lang.logo_mark}}</span> {{$lang.logo_text}}</a>
		</h1>
		<div class="footer_options">
			<nav class="footer-menu">
				<ul class="footer-menu__list">
					<li class="footer-menu__item {{if $page_id=='index'}}active{{/if}}">
						<a href="{{$lang.urls.home}}">{{$lang.footer.primary_menu_updates}}</a>
					</li>
					<li class="footer-menu__item {{if $page_id=='videos_list' || ($page_id=='search' && $search_type=='videos')}}active{{/if}}">
						<a href="{{$lang.urls.videos}}">{{$lang.footer.primary_menu_videos}}</a>
					</li>
					{{if $lang.enable_albums=='true'}}
						<li class="footer-menu__item {{if $page_id=='albums_list' || ($page_id=='search' && $search_type=='albums')}}active{{/if}}">
							<a href="{{$lang.urls.albums}}">{{$lang.footer.primary_menu_albums}}</a>
						</li>
					{{/if}}
					{{if $lang.enable_models=='true'}}
						<li class="footer-menu__item {{if $page_id=='models_list' || ($page_id=='search' && $search_type=='models')}}active{{/if}}">
							<a href="{{$lang.urls.models}}">{{$lang.footer.primary_menu_models}}</a>
						</li>
					{{/if}}
					{{if $lang.enable_categories=='true'}}
						<li class="footer-menu__item {{if $page_id=='categories_list'}}active{{/if}}">
							<a href="{{$lang.urls.categories}}">{{$lang.footer.primary_menu_categories}}</a>
						</li>
					{{/if}}

					{{if $smarty.session.status_id==''}}
						<li class="footer-menu__item">
							<a class="premium-link" href="{{$lang.urls.signup}}" data-action="popup">{{$lang.footer.primary_menu_get_premium}}</a>
						</li>
					{{elseif $smarty.session.status_id==2}}
						<li class="footer-menu__item">
							<a class="premium-link" href="{{$lang.urls.upgrade}}" data-action="popup">{{$lang.footer.primary_menu_get_premium}}</a>
						</li>
					{{/if}}

					{{if $smarty.session.user_id>0}}
						<li class="footer-menu__item">
							<a href="{{$lang.urls.logout}}">{{$lang.footer.primary_menu_logout}}</a>
						</li>
					{{else}}
						<li class="footer-menu__item">
							<a href="{{$lang.urls.login}}" data-action="popup">{{$lang.footer.primary_menu_login}}</a>
						</li>
					{{/if}}

					{{if $lang.enable_static_terms=='true'}}
						<li class="footer-menu__item {{if $page_id=='terms'}}active{{/if}}">
							<a href="{{$lang.urls.terms}}">{{$lang.footer.primary_menu_terms}}</a>
						</li>
					{{/if}}
					{{if $lang.enable_static_dmca=='true'}}
						<li class="footer-menu__item {{if $page_id=='dmca'}}active{{/if}}">
							<a href="{{$lang.urls.dmca}}">{{$lang.footer.primary_menu_dmca}}</a>
						</li>
					{{/if}}
					{{if $lang.enable_static_2257=='true'}}
						<li class="footer-menu__item {{if $page_id=='2257'}}active{{/if}}">
							<a href="{{$lang.urls.2257}}">{{$lang.footer.primary_menu_2257}}</a>
						</li>
					{{/if}}
					{{if $lang.enable_static_privacy=='true'}}
						<li class="footer-menu__item {{if $page_id=='privacy'}}active{{/if}}">
							<a href="{{$lang.urls.privacy}}">{{$lang.footer.primary_menu_privacy}}</a>
						</li>
					{{/if}}
				</ul>
			</nav>
			<div class="footer__copy">&copy;  {{$lang.footer.copyright_year}} {{$lang.project_name}}. {{$lang.footer.copyright_text}} {{$lang.footer.site_text}}</div>
		</div>
	</div>
</footer>

{{if false}}
	<div class="first-visit %first%">
		<div class="modal popup-holder" id="modal-logon">
			<form action="#">
				<div class="modal__window">
					<div class="modal__join">
						<h2 class="title title__modal">Continue as Guest OR Become a Premium member</h2>
						<div class="btn__row">
							<a class="btn btn--green btn--middle submit js-guest">Guest</a>
							<a href="{{$lang.urls.signup}}" data-action="popup" class="btn btn--green btn--middle submit js-premium">Premium member</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
{{/if}}

</div> <!-- wrapper end -->
<script src="{{$config.statics_url}}/static/js/vendors.min.js?v=1.5"></script>
<script src="{{$config.statics_url}}/static/js/theme.min.js?v=1.5"></script>
{{if $smarty.get.type == 'upload_video'}}
	<script src="{{$config.statics_url}}/static/js/main.min.js"></script>
	<link href="{{$config.statics_url}}/static/styles/select2.min.css?v={{$smarty.now}}" rel="stylesheet" type="text/css"/>

{{/if}}
<script src="{{$config.statics_url}}/static/js/ion.rangeSlider.js"></script>
<script src="{{$config.statics_url}}/static/js/custom.js"></script>
{{if $recaptcha_site_key!=''}}
	<script>
		function recaptchaOnLoad() {
			$(document).trigger('recaptchaloaded');
		}
	</script>
	<script src="https://www.google.com/recaptcha/api.js?onload=recaptchaOnLoad&render=explicit"></script>
{{/if}}
</body>
</html>