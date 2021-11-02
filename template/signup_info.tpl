{{assign var="page_title" value=$lang.html.static_html_title.signup_info}}
{{assign var="page_description" value=$lang.html.static_html_description.signup_info}}
{{assign var="page_keywords" value=$lang.html.static_html_keywords.signup_info}}
{{assign var="page_canonical" value=$lang.urls.signup_info}}

{{include file="include_header_general.tpl"}}

<div class="signup-info">
  <div class="signup-info-wrap">
    <div class="signup-info__head">
      <div class="signup-info__head-container">
        <h1 class="signup-info__title">Welcome to RMS</h1>
        <p class="signup-info__text">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
          ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation
          ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
          reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur
          sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id
          est laborum.
        </p>
      </div>
    </div>
    <div class="signup-info__body">
      <div class="signup-info__body-container">
        <div class="signup-info-card-wrap">
          <div class="signup-info-card">
            <h2 class="signup-info-card__title">Guest</h2>
            <p class="signup-info-card__text">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
              incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
              exercitation ullamco laboris nisi ut aliquip
            </p>
            <ul class="signup-info-features">
              <li class="signup-info-features__item">Watch 40 second teaser videos in 720P</li>
              <li class="signup-info-features__item">Watch Full Length Videos in 1080P/4K</li>
              <li class="signup-info-features__item">Rate or Comment on any videos</li>
              <li class="signup-info-features__item">Save videos to your favorites section</li>
              <li class="signup-info-features__item">
                Create & Upload your own videos and earn cash!
              </li>
              <li class="signup-info-features__item">
                Have % of proceeds from your videos go to charity
              </li>
              <li class="signup-info-features__item">Disable advertisements site-wide</li>
              <li class="signup-info-features__item">Subscribe to user channels</li>
              <li class="signup-info-features__item">
                Get notified when new content is added from subscribed channels
              </li>
              <li class="signup-info-features__item">full personal library of favorited videos</li>
            </ul>
            <a href="/" class="signup-info-card__button signup-info-card__button--guest">
              <span> CONTINUE AS GUEST </span>
              <span> ENJOY FREE PREVIEWS </span>
            </a>
          </div>
          <div class="signup-info-card">
            <h2 class="signup-info-card__title">Premium</h2>
            <p class="signup-info-card__text">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
              incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
              exercitation ullamco laboris nisi ut aliquip
            </p>
            <ul class="signup-info-features">
              <li class="signup-info-features__item">Watch 40 second teaser videos in 720P</li>
              <li class="signup-info-features__item">Watch Full Length Videos in 1080P/4K</li>
              <li class="signup-info-features__item">Rate or Comment on any videos</li>
              <li class="signup-info-features__item">Save videos to your favorites section</li>
              <li class="signup-info-features__item">
                Create & Upload your own videos and earn cash!
              </li>
              <li class="signup-info-features__item">
                Have % of proceeds from your videos go to charity
              </li>
              <li class="signup-info-features__item">Disable advertisements site-wide</li>
              <li class="signup-info-features__item">Subscribe to user channels</li>
              <li class="signup-info-features__item">
                Get notified when new content is added from subscribed channels
              </li>
              <li class="signup-info-features__item">full personal library of favorited videos</li>
            </ul>
            <a href="{{if $smarty.session.user_id>0}}{{$lang.urls.upgrade}}{{else}}{{$lang.urls.signup}}{{/if}}" data-action="popup" class="signup-info-card__button signup-info-card__button--premium">
              <span> ACCESS PREMIUM </span>
              <span> GET FULL ACCESS NOW! </span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{include file="include_footer_general.tpl"}}