<div class="list_messages">
	<h1 class="block_header">
		{{if $conversation_user_id>0}}
			{{$conversation_display_name}} /
		{{/if}}
		{{if $folder=='inbox'}}My Inbox{{else}}<a href="{{$config.project_url}}/my_messages/inbox/">My Inbox</a>{{/if}}
		/
		{{if $folder=='outbox'}}My Sent Messages{{else}}<a href="{{$config.project_url}}/my_messages/outbox/">My Sent Messages</a>{{/if}}
		/
		{{if $folder=='unread'}}My Unread Messages{{else}}<a href="{{$config.project_url}}/my_messages/unread/">My Unread Messages</a>{{/if}}
	</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} messages
			</div>
		{{/if}}
		<div class="block_content">
			<form id="delete_messages_form" action="" method="post">
				<input type="hidden" name="action" value="delete"/>
				{{foreach name=data item=item from=$data}}
					{{assign var="message_user_id" value=$item.user_from_id}}
					{{assign var="message_user_name" value=$item.user_from_name}}
					{{assign var="message_avatar" value=$item.user_from_avatar}}
					{{if $folder=='outbox'}}
						{{assign var="message_user_id" value=$item.user_id}}
						{{assign var="message_user_name" value=$item.user_to_name}}
						{{assign var="message_avatar" value=$item.user_to_avatar}}
					{{/if}}
					<div class="message">
						<div class="delete"><input type="checkbox" name="delete[]" value="{{$item.message_id}}" {{if $item.type_id==1}}disabled="disabled"{{/if}}/></div>
						<div class="avatar">
							<a href="{{$config.project_url}}/members/{{$message_user_id}}/">
								<img src="{{if $message_avatar<>''}}{{$config.content_url_avatars}}/{{$message_avatar}}{{else}}{{$config.project_url}}/images/no_avatar_user.jpg{{/if}}" alt="{{$message_user_name}}"/>
							</a>
						</div>
						<div class="text">
							<h2>
								{{$item.added_date|date_format:"%text"}} {{if $folder<>'outbox'}}from{{else}}to{{/if}} <a href="{{$config.project_url}}/members/{{$message_user_id}}/">{{$message_user_name}}</a>
								{{if $folder<>'conversation' && $message_user_id<>$smarty.session.user_id}}
									/ <a href="{{$config.project_url}}/my_messages/{{$message_user_id}}/conversation/">View conversation</a>
								{{/if}}
								{{if $folder<>'outbox' && $message_user_id<>$smarty.session.user_id}}
									/ <a href="{{$config.project_url}}/my_messages/message/{{$item.message_id}}/">Reply</a>
								{{/if}}
							</h2>
							<div class="content">
								{{if $item.type_id==1}}
									{{$message_user_name}} has invited you to be his (her) friend. Do you want {{$message_user_name}} to be in your friends list?
									{{if $item.message<>''}}
										(S)he wrote the following message:<br/><br/>{{$item.message|truncate:1500:"...":true|replace:"\n":"<br/>"}}<br/><br/>
									{{/if}}
									<div class="button">
										<input id="btn_confirm{{$message_user_id}}" type="image" src="{{$config.project_url}}/images/btn_confirm.gif"/>
										<input id="btn_reject{{$message_user_id}}" type="image" src="{{$config.project_url}}/images/btn_reject.gif"/>
									</div>
									<script type="text/javascript">
										var params = {};
										params['confirm_button_id'] = 'btn_confirm{{$message_user_id}}';
										params['reject_button_id'] = 'btn_reject{{$message_user_id}}';
										params['message_from_user_id'] = {{$message_user_id}};
										listMessagesEnableFriends(params);
									</script>
								{{elseif $item.type_id==2}}
									{{$message_user_name}} has declined your invitation.
								{{elseif $item.type_id==3}}
									{{$message_user_name}} has removed you from his (her) friends.
								{{elseif $item.type_id==4}}
									{{$message_user_name}} has confirmed your invitation.
								{{else}}
									{{$item.message|truncate:1500:"...":true|replace:"\n":"<br/>"}}
									{{if $item.message|@strlen>1500}}
										<a href="{{$config.project_url}}/my_messages/message/{{$item.message_id}}/">Read full text</a>
									{{/if}}
								{{/if}}
							</div>
						</div>
						<div class="g_clear"></div>
					</div>
					{{if !$smarty.foreach.data.last}}
						<div class="message_separator"></div>
					{{/if}}
				{{/foreach}}
				<div class="actions">
					<input type="image" src="{{$config.project_url}}/images/btn_delete_selected.gif"/>
				</div>
			</form>
			<script type="text/javascript">
				var params = {}
				params['form_id'] = 'delete_messages_form';
				params['delete_confirmation_text'] = 'Are you sure to delete %1% selected message(s)?';
				params['no_items_selected'] = 'Nothing is selected!';
				listMessagesEnableDeleteForm(params);
			</script>
		</div>
	{{else}}
		<div class="text_content">
			There are no messages in the list.
		</div>
	{{/if}}
</div>