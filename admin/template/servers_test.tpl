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

<table class="de">
	<tr>
		<td class="de_header"><div><a href="servers.php">{{$lang.settings.submenu_option_storage_servers_list}}</a> / <a href="servers.php?action=change&amp;item_id={{$server.server_id}}">{{$server.title}}</a> / {{$lang.settings.server_test|replace:"%1%":$server.title}}</div></td>
	</tr>
	<tr>
		<td class="de_table_control">
			<table class="de_edit_grid">
				<colgroup>
					<col/>
				</colgroup>
				<tr class="eg_header">
					<td>{{$lang.settings.server_dg_result_col_check}}</td>
					<td>{{$lang.settings.server_dg_result_col_status}}</td>
					<td>{{$lang.settings.server_dg_result_col_url}}</td>
					<td>{{$lang.settings.server_dg_result_col_details}}</td>
				</tr>
				{{foreach item=item from=$data|smarty:nodefaults}}
					<tr class="eg_group_header">
						<td colspan="4">
							{{if $item.is_sources==1}}
								{{$lang.settings.server_dg_result_format_sources}}
							{{else}}
								{{$lang.settings.server_dg_result_format|replace:"%1%":$item.format}}
							{{/if}}
						</td>
					</tr>
					{{if count($item.checks)==0}}
						<tr class="eg_data_text">
							<td colspan="4">{{$lang.settings.server_dg_result_no_content|replace:"%1%":$server.title}}</td>
						</tr>
					{{/if}}
					{{foreach item=item_check from=$item.checks|smarty:nodefaults}}
						<tr class="eg_data_text">
							<td class="nowrap {{if $item_check.disabled>0}}disabled{{elseif $item_check.not_accessible==1}}warning_text{{elseif $item_check.is_error==1}}highlighted_text{{/if}}">
								{{if $item_check.type=='direct_link'}}
									{{$lang.settings.server_dg_result_col_check_direct_link}}
								{{elseif $item_check.type=='direct_link2'}}
									{{$lang.settings.server_dg_result_col_check_direct_link2}}
								{{elseif $item_check.type=='protected_link'}}
									{{$lang.settings.server_dg_result_col_check_protected_link}}
								{{elseif $item_check.type=='streaming'}}
									{{$lang.settings.server_dg_result_col_check_streaming}}
								{{/if}}
							</td>
							<td>
								{{if $item_check.not_accessible==1}}
									<span class="warning_text">{{$lang.settings.server_dg_result_col_status_na}}</span>
								{{elseif $item_check.is_error==1}}
									<span class="highlighted_text">{{$lang.settings.server_dg_result_col_status_failure}}</span>
								{{elseif $item_check.disabled>0}}
									<span class="disabled">{{$lang.settings.server_dg_result_col_status_na}}</span>
								{{else}}
									{{$lang.settings.server_dg_result_col_status_ok}}
								{{/if}}
							</td>
							<td>
								<a href="{{$item_check.url}}" rel="external">{{$item_check.url}}</a>
							</td>
							<td>
								{{if $item_check.details!=''}}
									<div class="details_link">
										<a href="javascript:stub()"></a>
										<span class="js_params">
											<span class="js_param">text={{$item_check.details}}</span>
										</span>
									</div>
								{{/if}}
							</td>
						</tr>
					{{/foreach}}
				{{/foreach}}
			</table>
		</td>
	</tr>
</table>