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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.kvs_news.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.kvs_news.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.kvs_news.field_disable}}:</td>
			<td class="de_control"><div class="de_lv_pair de_vis_sw_checkbox"><input type="checkbox" id="is_disabled" name="is_disabled" value="1" {{if $smarty.post.is_disabled==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.kvs_news.field_disable_disabled}}</label></div></td>
		</tr>
		<tr class="is_disabled_off">
			<td class="de_label">{{$lang.plugins.kvs_news.field_last_exec}}:</td>
			<td class="de_control">
				{{if $smarty.post.last_exec_date=='0000-00-00 00:00:00'}}
					{{$lang.plugins.kvs_news.field_last_exec_none}}
				{{else}}
					{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
					({{$smarty.post.duration|default:0}} {{$lang.plugins.kvs_news.field_last_exec_seconds}})
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.kvs_news.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>