<div class="list_members_blog">
	<h1 class="block_header">Blog Entries</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} entries
			</div>
		{{/if}}
	{{/if}}
	<div class="block_content">
		{{if $smarty.get.action=='send_done'}}
			<p class="topmost message_info">Your message has been sent for review.</p>
		{{/if}}
		{{if count($data)>0}}
			<form id="delete_entries_form" action="" method="post">
				<input type="hidden" name="action" value="delete"/>
				{{assign var="can_delete" value=0}}
				{{foreach name=data item=item from=$data}}
					<div class="entry">
						{{if $user_id==$smarty.session.user_id}}
							<div class="delete">
								<input type="checkbox" name="delete[]" value="{{$item.entry_id}}"/>
								{{assign var="can_delete" value=1}}
							</div>
						{{/if}}
						<div class="avatar">
							<a href="{{$config.project_url}}/members/{{$item.user_from_id}}/">
								<img src="{{if $item.user_from_avatar<>''}}{{$config.content_url_avatars}}/{{$item.user_from_avatar}}{{else}}{{$config.project_url}}/images/no_avatar_user.jpg{{/if}}" alt="{{$item.user_from_name}}"/>
							</a>
						</div>
						<div class="text">
							<h2>
								{{$item.added_date|date_format:"%text"}} by <a href="{{$config.project_url}}/members/{{$item.user_from_id}}/">{{$item.user_from_name}}</a>
							</h2>
							<div class="content">
								{{$item.entry|replace:"\n":"<br/>"}}
							</div>
						</div>
						<div class="g_clear"></div>
					</div>
				{{/foreach}}
				{{if $can_delete==1}}
					<div class="actions">
						<input type="image" src="{{$config.project_url}}/images/btn_delete_selected.gif"/>
					</div>
					<script type="text/javascript">
						var params = {}
						params['form_id'] = 'delete_entries_form';
						params['delete_confirmation_text'] = 'Are you sure to delete %1% selected record(s)?';
						params['no_items_selected'] = 'Nothing is selected!';
						listMembersBlogEnableDeleteForm(params);
					</script>
				{{/if}}
			</form>
		{{else}}
			<div class="text_content">There are no entries in the list.</div>
		{{/if}}
		{{if $smarty.session.user_id>0}}
			<div class="add_entry">
				<form id="ae_form" action="" method="post">
					<input type="hidden" name="action" value="add"/>
					<div class="label">Add new entry (*):</div>
					<div class="control">
						<textarea name="entry" cols="40" rows="10"></textarea>
						<div id="entry_error_1" class="field_error {{if $errors.entry<>1}}g_hidden{{/if}}">The field is required</div>
					</div>
					<div class="button"><input type="image" src="{{$config.project_url}}/images/btn_send.gif"/></div>
				</form>
				<script type="text/javascript">
					listMembersBlogEnableAddEntry({form_id: 'ae_form'});
				</script>
			</div>
		{{/if}}
	</div>
</div>