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
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
		<input type="hidden" name="action" value="change_complete"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.external_search.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.external_search.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.external_search.field_enable_external_search}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="enable_external_search" name="enable_external_search">
						<option value="0" {{if $smarty.post.enable_external_search==0}}selected="selected"{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_never}}</option>
						<option value="1" {{if $smarty.post.enable_external_search==1}}selected="selected"{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_always}}</option>
						<option value="2" {{if $smarty.post.enable_external_search==2}}selected="selected"{{/if}}>{{$lang.plugins.external_search.field_enable_external_search_condition}}</option>
					</select>
					&nbsp;
					<input type="text" name="enable_external_search_condition" class="fixed_50 enable_external_search_2" maxlength="10" value="{{$smarty.post.enable_external_search_condition}}"/>
				</div>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.external_search.field_display_results}}:</td>
			<td class="de_control">
				<select name="display_results">
					<option value="0" {{if $smarty.post.display_results==0}}selected="selected"{{/if}}>{{$lang.plugins.external_search.field_display_results_replace}}</option>
					<option value="1" {{if $smarty.post.display_results==1}}selected="selected"{{/if}}>{{$lang.plugins.external_search.field_display_results_beginning}}</option>
					<option value="2" {{if $smarty.post.display_results==2}}selected="selected"{{/if}}>{{$lang.plugins.external_search.field_display_results_end}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="enable_external_search_0">{{$lang.plugins.external_search.field_api_call}}:</div>
				<div class="de_required enable_external_search_1 enable_external_search_2">{{$lang.plugins.external_search.field_api_call}} (*):</div>
			</td>
			<td class="de_control">
				<input type="text" name="api_call" class="dyn_full_size" value="{{$smarty.post.api_call}}">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.external_search.field_api_call_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">
				<div class="enable_external_search_0">{{$lang.plugins.external_search.field_outgoing_url}}:</div>
				<div class="de_required enable_external_search_1 enable_external_search_2">{{$lang.plugins.external_search.field_outgoing_url}} (*):</div>
			</td>
			<td class="de_control">
				<input type="text" name="outgoing_url" class="dyn_full_size" value="{{$smarty.post.outgoing_url}}">
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.external_search.field_outgoing_url_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.external_search.field_avg_query_time}}:</td>
			<td class="de_control">
				{{if $smarty.post.performance.query_time>0}}
					{{$smarty.post.performance.query_time}} {{$lang.common.second_truncated}}
				{{else}}
					{{$lang.common.undefined}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.external_search.field_avg_parse_time}}:</td>
			<td class="de_control">
				{{if $smarty.post.performance.parse_time>0}}
					{{$smarty.post.performance.parse_time}} {{$lang.common.second_truncated}}
				{{else}}
					{{$lang.common.undefined}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.external_search.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>