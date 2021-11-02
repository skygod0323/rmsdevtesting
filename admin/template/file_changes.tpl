{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}

{{if is_array($list_messages)}}
	<div class="message">
	{{foreach item=item from=$list_messages|smarty:nodefaults}}
		<p>{{$item}}</p>
	{{/foreach}}
	</div>
{{/if}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="approve"/>
	</div>
	<table class="de">
		<tr>
			<td class="de_header"><div>{{$lang.settings.file_changes_header}}</div></td>
		</tr>
		{{if $smarty.session.userdata.is_expert_mode==0}}
			<tr>
				<td class="de_control"><span class="de_hint">{{$lang.settings.file_changes_header_hint}}</span></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_table_control">
				<table class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header">
						<td>{{$lang.settings.file_changes_col_content_old}}</td>
						<td>{{$lang.settings.file_changes_col_content_new}}</td>
						<td>{{$lang.settings.file_changes_col_modified_date}}</td>
					</tr>
					{{if count($data)>0}}
						{{foreach item=item from=$data|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td><a rel="external" href="?action=get_old_content&amp;hash={{$item.hash}}">{{$item.path}}</a></td>
								<td><a rel="external" href="?action=get_new_content&amp;hash={{$item.hash}}">{{$item.path}}</a></td>
								<td>{{$item.modified_date|date_format:$smarty.session.userdata.full_date_format}}</td>
							</tr>
						{{/foreach}}
					{{else}}
						<tr class="eg_data fixed_height_30">
							<td colspan="3">{{$lang.settings.file_changes_no_changes}}</td>
						</tr>
					{{/if}}
				</table>
			</td>
		</tr>
		{{if count($data)>0}}
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" value="{{$lang.settings.file_changes_btn_approve}}"/>
				</td>
			</tr>
		{{/if}}
	</table>
</form>