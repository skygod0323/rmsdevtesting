<div class="list_members">
	<h1 class="block_header">Members</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} members
			</div>
		{{/if}}
		<div class="block_content">
			{{if $can_manage==1}}
				{{if $smarty.get.action=='delete_done'}}
					<p class="topmost message_info">
						The selected user(s) have been removed from your friends list.
					</p>
				{{/if}}
				<form id="delete_members_form" action="" method="post">
					<input type="hidden" name="action" value="delete_from_friends"/>
			{{/if}}
			{{foreach item=item from=$data}}
				<div class="item">
					<h2><a href="{{$config.project_url}}/members/{{$item.user_id}}/" class="hl" title="{{$item.display_name}}">{{$item.display_name|truncate:12:"...":true}}</a></h2>
					<div class="image">
						<a href="{{$config.project_url}}/members/{{$item.user_id}}/" title="{{$item.display_name}}">
							{{if $item.avatar<>''}}
								<img class="thumb" src="{{$config.content_url_avatars}}/{{$item.avatar}}" alt="{{$item.display_name}}"/>
							{{else}}
								<img class="thumb" src="{{$config.project_url}}/images/no_avatar_user.jpg" alt="{{$item.display_name}}"/>
							{{/if}}
						</a>
					</div>
					{{if $mode_friends==1}}
						<div class="info">Friends: <span>{{$item.added_to_friends_date|date_format:"%text_short"}}</span></div>
					{{else}}
						<div class="info">Joined: <span>{{$item.added_date|date_format:"%text"}}</span></div>
					{{/if}}

					{{if $item.gender_id>0 || $item.age<>''}}
						<div class="info">
							<span>
								{{if $item.gender_id==1}}Male{{elseif $item.gender_id==2}}Female{{elseif $item.gender_id==3}}Couple{{elseif $item.gender_id==4}}Transsexual{{/if}}
								{{if $item.gender_id>0 && $item.age.value<>''}},{{/if}} {{if $item.age.value<>''}}{{$item.age.value}} y.o.{{/if}}
							</span>
						</div>
					{{else}}
						<div class="info">&nbsp;</div>
					{{/if}}
					{{if $can_manage==1}}
						<div class="options">
							<input id="delete_{{$item.user_id}}" type="checkbox" name="delete[]" value="{{$item.user_id}}"/> <label for="delete_{{$item.user_id}}">delete</label>
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
					params['form_id'] = 'delete_members_form';
					params['delete_confirmation_text'] = 'Are you sure to delete %1% selected user(s) from your friends list?';
					params['no_items_selected'] = 'Nothing is selected!';
					listMembersEnableDeleteForm(params);
				</script>
			{{/if}}
		</div>
	{{else}}
		<div class="text_content">
			There are no members in the list.
		</div>
	{{/if}}
</div>