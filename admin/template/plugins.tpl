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

{{if is_array($smarty.post.errors)}}
	<div class="err_list">
		<div class="err_header">{{$lang.validation.common_header}}</div>
		<div class="err_content">
			<ul>
				{{foreach name=data_err item=item_err from=$smarty.post.errors|smarty:nodefaults}}
					<li>{{$item_err}}</li>
				{{/foreach}}
			</ul>
		</div>
	</div>
{{else}}
	<div class="dg_wrapper">
		<form action="{{$page_name}}" method="get" class="form_dgf">
			<div class="dg">
				<table>
					<colgroup>
						<col width="1%"/>
						<col width="20%"/>
						<col/>
						<col width="15%"/>
						<col width="5%"/>
						<col width="5%"/>
					</colgroup>
					<tr class="dg_header">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
						<td>{{$lang.plugins.dg_plugins_col_title}}</td>
						<td>{{$lang.plugins.dg_plugins_col_description}}</td>
						<td>{{$lang.plugins.dg_plugins_col_type}}</td>
						<td>{{$lang.plugins.dg_plugins_col_version}}</td>
						<td>{{$lang.plugins.dg_plugins_col_kvs_version}}</td>
					</tr>
					{{foreach name=data item=item from=$plugins|smarty:nodefaults}}
						<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
							<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$id}}" disabled="disabled" {{if $item.is_enabled==1}}checked="checked"{{/if}}/></td>
							<td>
								<a href="{{$page_name}}?plugin_id={{$item.id}}" class="no_popup {{if $item.is_invalid==1}}highlighted_text{{/if}}">{{if $item.is_enabled==1}}<b>{{/if}}{{$item.title}}{{if $item.is_enabled==1}}</b>{{/if}}</a>
							</td>
							<td>{{$item.description}}</td>
							<td>
								{{foreach name=data_type item=item_type from=$item.plugin_types|smarty:nodefaults}}
									{{if $item_type=='manual'}}
										{{$lang.plugins.dg_plugins_col_type_manual}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{elseif $item_type=='cron'}}
										{{$lang.plugins.dg_plugins_col_type_cron}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{elseif $item_type=='api'}}
										{{$lang.plugins.dg_plugins_col_type_api}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{elseif $item_type=='process_object'}}
										{{$lang.plugins.dg_plugins_col_type_object_callback}}{{if !$smarty.foreach.data_type.last}},{{/if}}
									{{/if}}
								{{/foreach}}
							</td>
							<td>{{$item.version}}</td>
							<td>{{$item.kvs_version}}</td>
						</tr>
					{{/foreach}}
				</table>
			</div>
			<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
		</form>
	</div>
{{/if}}