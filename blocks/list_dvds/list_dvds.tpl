<div class="list_dvds">
	<h1 class="block_header">Channels</h1>
	{{if count($data)>0}}
		{{if $total_count>0}}
			<div class="block_sub_header">
				{{assign var="last_item" value=$showing_from+$items_per_page}}
				{{if $last_item>$total_count}}
					{{assign var="last_item" value=$total_count}}
				{{/if}}
				Showing {{$showing_from+1}} - {{$last_item}} of {{$total_count}} channels
			</div>
		{{/if}}
		<div class="block_content">
			{{if $can_manage==1}}
				{{if $smarty.get.action=='delete_done'}}
					<p class="topmost message_info">
						 The selected channel(s) have been removed.
					</p>
				{{elseif $smarty.get.action=='delete_forbidden'}}
					<p class="topmost message_info">
						Sorry, you should contact website support if you want your channels to be removed.
					</p>
				{{/if}}
				<form id="delete_dvds_form" action="" method="post">
					<input type="hidden" name="action" value="delete_dvds"/>
			{{/if}}
			{{foreach item=item from=$data}}
				<div class="item">
					<h2>
						{{if $item.view_page_url<>''}}
							<a href="{{$item.view_page_url}}" class="hl" title="{{$item.title}}">{{$item.title}}</a>
						{{else}}
							{{$item.title}}
						{{/if}}
					</h2>
					<div class="info">
						{{$item.total_videos}} videos
					</div>
					{{if $can_manage==1}}
						<div class="options">
							<input id="delete_{{$item.dvd_id}}" type="checkbox" name="delete[]" value="{{$item.dvd_id}}"/> <label for="delete_{{$item.dvd_id}}">delete this channel</label>
						</div>
					{{/if}}
				</div>
			{{/foreach}}
			{{if $can_manage==1}}
				<div class="actions">
					<input type="image" src="{{$config.project_url}}/images/btn_delete_selected.gif"/>
				</div>
				</form>
				<script type="text/javascript">
					var params = {};
					params['form_id'] = 'delete_dvds_form';
					params['delete_confirmation_text'] = 'Are you sure to delete %1% selected channel(s)?';
					params['no_items_selected'] = 'Nothing is selected!';
					listDVDsEnableDeleteForm(params);
				</script>
			{{/if}}
		</div>
	{{else}}
		<div class="text_content">
			There are no channels in the list.
		</div>
	{{/if}}
</div>