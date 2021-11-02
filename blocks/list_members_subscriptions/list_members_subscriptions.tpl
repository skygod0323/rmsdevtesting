<div class="list_members_subscriptions">
	<h1 class="block_header">Subscriptions</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} subscriptions
			</div>
		{{/if}}
		<div class="block_content">
			{{if $can_manage==1}}
				{{if $smarty.get.action=='delete_done'}}
					<p class="topmost message_info">
						The selected subscription(s) have been removed.
					</p>
				{{/if}}
				<form id="delete_subscriptions_form" action="" method="post">
					<input type="hidden" name="action" value="delete_subscriptions"/>
			{{/if}}
			{{foreach item=item from=$data}}
				<div class="item">
					<h2>{{$item.title}}</h2>
					{{if $can_manage==1}}
						<div class="options">
							<input id="delete_{{$item.subscription_id}}" type="checkbox" name="delete[]" value="{{$item.subscription_id}}"/> <label for="delete_{{$item.subscription_id}}">delete</label>
						</div>
					{{/if}}
				</div>
			{{/foreach}}
			<div class="g_clear"></div>
			{{if $can_manage==1}}
				<div class="actions">
					<input type="image" src="{{$config.project_url}}/images/btn_delete_selected.gif"/>
				</div>
				</form>
				<script type="text/javascript">
					var params = {};
					params['form_id'] = 'delete_subscriptions_form';
					params['delete_confirmation_text'] = 'Are you sure to delete %1% selected subscription(s)?';
					params['no_items_selected'] = 'Nothing is selected!';
					listMembersSubscriptionsEnableDeleteForm(params);
				</script>
			{{/if}}
		</div>
	{{else}}
		<div class="text_content">
			There are no subscriptions in the list.
		</div>
	{{/if}}
</div>