{{if $smarty.session.user_info.is_removal_requested==1}}
	<div class="header">Request Profile Removal</div>

	<div class="message">
		Profile removal request has been submitted. Please note that your profile will be manually removed by administrator.
	</div>

{{elseif $async_submit_successful=='true'}}
	<div class="message">
		Profile removal request has been submitted. Please note that your profile will be manually removed by administrator.
	</div>

{{else}}
	<div class="header">Request Profile Removal</div>

	<form method="post" data-form="ajax">
		<input type="hidden" name="action" value="delete_profile"/>
		<input type="hidden" name="function" value="get_block"/>
		<input type="hidden" name="block_id" value="{{$block_uid}}"/>
		{{if $is_global==1}}
			<input type="hidden" name="global" value="true"/>
		{{/if}}

		<div class="generic-error hidden"></div>

		<div class="row">
			<label for="delete_profile_reason" class="{{if $require_reason==1}}required{{/if}}">Reason</label>
			<textarea id="delete_profile_reason" name="reason" rows="3"></textarea>
			<span class="field-error"></span>
		</div>

		<div class="row">
			<input id="delete_profile_confirm_delete" type="checkbox" name="confirm_delete" value="1"/>
			<label for="delete_profile_confirm_delete">I confirm that I want my profile and all connected data to be removed</label>
			<span class="field-error"></span>
		</div>

		<div class="buttons">
			<input type="submit" value="Submit Request"/>
		</div>
	</form>
{{/if}}