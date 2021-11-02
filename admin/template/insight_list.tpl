{{*
	Developed by Kernel Team
	http://kernel-team.com
	Version: 1.0
*}}
<div class="insight_list_table">
	<table class="de">
		<tr>
			<td>
				<table class="control_group">
					<colgroup>
						<col width="25%"/>
						<col width="25%"/>
						<col width="25%"/>
						<col width="25%"/>
					</colgroup>
					{{if $is_grouped==1}}
						{{foreach name=data_groups item=item_group from=$data|smarty:nodefaults}}
						<tr class="group_title">
							<td colspan="4"><div>{{$item_group[0].group_title|default:$lang.common.no_group}}</div></td>
						</tr>
						<tr class="group_data">
							{{foreach name=data key=key item=item from=$item_group|smarty:nodefaults}}
								<td><div class="de_lv_pair wrap"><input type="checkbox" name="data[]" value="{{$item.id}}" alt="{{$item.title}}"/><label {{if $item.status_id=='0'}}class="disabled"{{/if}}>{{$item.title}}</label></div></td>
								{{if $smarty.foreach.data.iteration%4==0 && !$smarty.foreach.data.last}}</tr><tr class="group_data">{{/if}}
							{{/foreach}}
						</tr>
						{{/foreach}}
					{{else}}
						<tr class="group_data">
							{{foreach name=data key=key item=item from=$data|smarty:nodefaults}}
								<td><div class="de_lv_pair wrap"><input type="checkbox" name="data[]" value="{{$item.id}}" alt="{{$item.title}}"/><label {{if $item.status_id=='0'}}class="disabled"{{/if}}>{{$item.title}}</label></div></td>
								{{if $smarty.foreach.data.iteration%4==0 && !$smarty.foreach.data.last}}</tr><tr class="group_data">{{/if}}
							{{/foreach}}
						</tr>
					{{/if}}
				</table>
			</td>
		</tr>
	</table>
</div>
<div class="insight_list_buttons">
	<input type="button" value="{{$lang.common.insight_add_selected_items}}"/>
	{{if count($sortings)>0}}
		<select name="sort_by">
			{{foreach item="item" from=$sortings|smarty:nodefaults}}
				<option value="{{$item.id}}" {{if $item.id==$selected_sorting}}selected="selected"{{/if}}>{{$item.title}}</option>
			{{/foreach}}
		</select>
	{{/if}}
	{{if $is_grouping_supported==1}}
		<select name="group_by">
			<option value="group" {{if $is_grouped==1}}selected="selected"{{/if}}>{{$lang.common.insight_group_by_group}}</option>
			<option value="none" {{if $is_grouped==0}}selected="selected"{{/if}}>{{$lang.common.insight_group_by_none}}</option>
		</select>
	{{/if}}
	{{if count($statuses)>0}}
		<select name="status">
			{{foreach item="item" from=$statuses|smarty:nodefaults}}
				<option value="{{$item.id}}" {{if $item.id==$selected_status}}selected="selected"{{/if}}>{{$item.title}}</option>
			{{/foreach}}
		</select>
	{{/if}}
</div>