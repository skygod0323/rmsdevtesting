{{if $async_submit_successful=='true'}}
	<div class="modal__window__form  modal__window__form--single cfx">
		<div class="success" data-action="refresh">
			{{if $smarty.session.status_id==3}}
				{{$lang.upgrade.success_message_premium_member|replace:"%1%":$lang.project_name}}
			{{else}}
				{{$lang.upgrade.success_message_active_member|replace:"%1%":$lang.project_name}}
			{{/if}}
		</div>
	</div>
{{else}}
	<div id="modal-signup" class="modal popup-holder">
		{{if $smarty.get.error=='only_for_members'}}
			<div class="btn btn--unlock btn--unlock--danger">
				<span class="lock"><i class="icon-lock-shape-20"></i></span>
				<strong class="error-message">
					{{$lang.login.error_message_only_for_members}}
					<span>
							{{$lang.login.error_message_only_for_members_join}}
						</span>
				</strong>
			</div>
		{{/if}}
		<div class="modal__window">
			<h2 class="title title__modal">{{$lang.upgrade.title|replace:"%1%":$lang.project_name}}</h2>

			<form action="{{$lang.urls.upgrade}}" data-form="ajax" method="post">
				<div class="generic-error hidden"></div>

				<div class="cols">
					<div class="twocolumn cfx">
						<div class="left">
							<ul class="price-list">
								{{foreach item="item" from=$card_packages name='packages'}}
									<li class="price-list__item {{if $item.is_default==1}}active{{/if}}">
										<input type="radio" id="r-{{$smarty.foreach.packages.index}}" name="card_package_id" value="{{$item.package_id}}" {{if $item.is_default==1}}checked{{/if}}/>
										<label for="r-{{$smarty.foreach.packages.index}}" class="price-list__item__body cfx">
											<span class="price-list__button"></span>
											{{assign var=labelTitle value=$lang.memberzone.access_packages.title[$item.package_id]|default:$item.title}}
											{{assign var=labelTitle value="|"|explode:$labelTitle}}
											<span class="price-list__text">
												<strong>{{$labelTitle[0]}}</strong>
												<span>{{$labelTitle[1]}}</span>
											</span>
											<span class="price-list__price">{{$labelTitle[2]}}</span>
										</label>
									</li>
								{{/foreach}}
							</ul>
							<input type="hidden" name="payment_option" value="2"/>
							<input type="hidden" name="action" value="upgrade"/>
							<input type="hidden" name="back_link" value="{{$lang.urls.payment_action}}"/>
						</div>
						<div class="right">
							<ul class="profits__list">
								{{foreach from=$lang.memberzone.access_packages.profits item="profit"}}
									<li class="profits__list__item"><i class="ico ico-check"></i><div>{{$profit}}</div></li>
								{{/foreach}}
							</ul>
						</div>
					</div>
					<div class="btn__row">
						<button type="submit" class="btn btn--green btn--bigger">
							{{$lang.upgrade.btn_continue}}
							<span class="btn__small-bottom">{{$lang.upgrade.btn_continue_hint}}</span>
						</button>
					</div>
				</div>
			</div>
		</form>
	</div>
{{/if}}