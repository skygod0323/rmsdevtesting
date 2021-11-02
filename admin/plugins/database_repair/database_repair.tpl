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
	<div class="err_list {{if !is_array($smarty.post.errors)}}hidden{{/if}}">
		<div class="err_header">{{if is_array($smarty.post.errors)}}{{$lang.validation.common_header}}{{/if}}</div>
		<div class="err_content">
			{{if is_array($smarty.post.errors)}}
				<ul>
				{{foreach name=data_err item=item_err from=$smarty.post.errors|smarty:nodefaults}}
					<li>{{$item_err}}</li>
				{{/foreach}}
				</ul>
			{{/if}}
		</div>
	</div>
	<div>
		<input type="hidden" name="action" value="repair"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.database_repair.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.database_repair.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.database_repair.field_database_version}}:</td>
			<td class="de_control">
				{{$smarty.post.database_version|default:$lang.common.undefined}}
			</td>
		</tr>
		{{if count($smarty.post.queries)>0}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.plugins.database_repair.divider_queries}}</div></td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="2">
					<table class="de_edit_grid">
						<colgroup>
							<col width="1%"/>
							<col/>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header fixed_height_30">
							<td class="eg_selector"></td>
							<td>{{$lang.plugins.database_repair.dg_queries_col_id}}</td>
							<td>{{$lang.plugins.database_repair.dg_queries_col_command}}</td>
							<td>{{$lang.plugins.database_repair.dg_queries_col_time}}</td>
							<td>{{$lang.plugins.database_repair.dg_queries_col_state}}</td>
							<td>{{$lang.plugins.database_repair.dg_queries_col_info}}</td>
						</tr>
						{{foreach item=item from=$smarty.post.queries|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td class="eg_selector"><input type="checkbox" name="kill_queries[]" value="{{$item.Id}}"/></td>
								<td class="nowrap">{{$item.Id}}</td>
								<td class="nowrap">{{$item.Command}}</td>
								<td class="nowrap">{{$item.Time}}</td>
								<td class="nowrap">{{$item.State}}</td>
								<td>{{$item.Info}}</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="save_default" value="{{$lang.plugins.database_repair.btn_kill}}"/>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.database_repair.divider_tables}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header fixed_height_30">
						<td>{{$lang.plugins.database_repair.dg_data_col_table_name}}</td>
						<td>{{$lang.plugins.database_repair.dg_data_col_engine}}</td>
						<td>{{$lang.plugins.database_repair.dg_data_col_rows}}</td>
						<td>{{$lang.plugins.database_repair.dg_data_col_size}}</td>
						<td>{{$lang.plugins.database_repair.dg_data_col_status}}</td>
						<td>{{$lang.plugins.database_repair.dg_data_col_message}}</td>
					</tr>
					{{foreach item=item from=$smarty.post.data|smarty:nodefaults}}
						{{foreach item=item2 from=$item.status|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td class="nowrap">{{$item.table}}</td>
								<td class="nowrap">{{$item.engine}}</td>
								<td class="nowrap">{{$item.rows|number_format:0:".":" "}}</td>
								<td class="nowrap">{{$item.size}}</td>
								<td class="nowrap">
									{{if $item2.Msg_type=='status'}}
										{{$item2.Msg_text}}
									{{elseif $item2.Msg_type|strtolower=='error'}}
										<span class="highlighted_text">{{$item2.Msg_type}}</span>
									{{elseif $item2.Msg_type|strtolower=='warning'}}
										<span class="warning_text">{{$item2.Msg_type}}</span>
									{{else}}
										<span>{{$item2.Msg_type}}</span>
									{{/if}}
								</td>
								<td>{{if $item2.Msg_type!='status'}}{{$item2.Msg_text}}{{/if}}</td>
							</tr>
						{{/foreach}}
					{{/foreach}}
				</table>
			</td>
		</tr>
			<tr>
				<td class="de_action_group" colspan="2">
					<input type="submit" name="full_check" value="{{$lang.plugins.database_repair.btn_check_tables}}"/>
					{{if $smarty.post.has_errors==1}}
						<input type="submit" name="repair" value="{{$lang.plugins.database_repair.btn_repair}}"/>
					{{/if}}
				</td>
			</tr>
	</table>
</form>