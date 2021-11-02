{{if $smarty.get.action=='payment_done'}}
	<div class="header">Payment Successful</div>
	<div class="message">
		Thank you! Your payment has been processed successfully.
	</div>

{{elseif $smarty.get.action=='payment_failed'}}
	<div class="header">Payment Failed</div>
	<div class="message">
		Unfortunately our payment processor was unable to accept your payment. Please contact support.
	</div>

{{elseif $async_submit_successful=='true'}}
	<div class="message">
		{{if $smarty.session.status_id==3}}
			Thank you! Your transaction has been processed successfully. You are now a premium member.
		{{else}}
			Thank you! Your transaction has been processed successfully.
		{{/if}}
	</div>

{{else}}
	<div class="header">Access Level Upgrade</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="upgrade"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		{{if count($card_packages)>0}}
			<div class="row">
				<input type="hidden" name="payment_option" value="2"/>
				{{foreach item="item" from=$card_packages}}
					<div data-action="choose" class="{{if $smarty.post.payment_option==2 && $item.is_default==1}}active{{/if}}">
						<input id="upgrade_card_package_id_{{$item.package_id}}" type="radio" name="card_package_id" value="{{$item.package_id}}" {{if $smarty.post.payment_option==2 && $item.is_default==1}}checked{{/if}}/>
						<label for="upgrade_card_package_id_{{$item.package_id}}">{{$item.title}}</label>
					</div>
				{{/foreach}}
				<span data-name="card_package_id" class="field-error"></span>
			</div>
		{{/if}}

		{{if count($access_codes)>0}}
			<div class="row">
				<label for="upgrade_access_code">Access code</label>
				<input id="upgrade_access_code" type="text" name="access_code"/>
				<span class="field-error"></span>
			</div>
		{{/if}}

		<div class="buttons">
			<input type="submit" value="Upgrade"/>
		</div>
	</form>

{{/if}}