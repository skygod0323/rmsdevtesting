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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.rotator_clicks_matrix.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.rotator_clicks_matrix.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.rotator_clicks_matrix.field_page}} (*):</td>
			<td class="de_control">
				<select name="page_external_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach name=data item=item from=$smarty.post.pages|smarty:nodefaults}}
						<option value="{{$item.external_id}}" {{if $item.external_id==$smarty.request.page_external_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.rotator_clicks_matrix.field_items_in_row}} (*):</td>
			<td class="de_control">
				<input type="text" name="items_in_row" maxlength="10" class="fixed_100" value="{{$smarty.post.items_in_row}}"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.rotator_clicks_matrix.field_items_in_row_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.rotator_clicks_matrix.btn_display}}"/>
			</td>
		</tr>
	</table>
</form>
{{if is_array($smarty.post.displayed_data)}}
	<form action="{{$page_name}}" method="post">
		<div class="err_list hidden">
			<div class="err_header"></div>
			<div class="err_content"></div>
		</div>
		<div>
			<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
			<input type="hidden" name="page_external_id" value="{{$smarty.request.page_external_id}}"/>
			<input type="hidden" name="action" value="reset_complete"/>
		</div>
		<table class="de">
			<tr>
				<td class="de_separator"><div>{{$lang.plugins.rotator_clicks_matrix.divider_matrix}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.plugins.rotator_clicks_matrix.field_by_page_number}}:</td>
			</tr>
			{{foreach name=data item=item from=$smarty.post.displayed_data|smarty:nodefaults}}
				{{if is_array($item.page_matrix)}}
					<tr>
						<td class="de_table_control">
							<table border="1" cellpadding="4" cellspacing="0">
								<colgroup>
									{{section name=data start=0 step=1 loop=10}}
										<col width="100"/>
									{{/section}}
								</colgroup>
								<tr>
									<td colspan="10">
										<div class="de_lv_pair">
											<input type="checkbox" name="reset_page[]" value="{{$item.id}}"/>
											<b><label>{{$item.id}}</label></b>
										</div>
									</td>
								</tr>
								<tr>
									{{assign var=pos value=1}}
									{{section name=data start=0 step=1 loop=10}}
										<td>
											{{if $pos<10}}
												<b>{{$lang.plugins.rotator_clicks_matrix.field_by_page_number_page|replace:"%1%":$pos}}</b>
											{{else}}
												<b>{{$lang.plugins.rotator_clicks_matrix.field_by_page_number_page|replace:"%1%":$pos}}+</b>
											{{/if}}
										</td>
										{{assign var=pos value=$pos+1}}
									{{/section}}
								</tr>
								<tr>
									{{assign var=pos value=1}}
									{{section name=elements_data start=0 step=1 loop=10}}
										<td>
											{{if $item.page_matrix[$pos]!=''}}
												{{$item.page_matrix[$pos]}}
												<br/>
												({{$item.page_matrix_pc[$pos]}}%)
											{{else}}
												0
											{{/if}}
										</td>
										{{assign var=pos value=$pos+1}}
									{{/section}}
								</tr>
							</table>
						</td>
					</tr>
				{{/if}}
			{{/foreach}}
			<tr>
				<td class="de_label"><br/>{{$lang.plugins.rotator_clicks_matrix.field_by_page_position}}:</td>
			</tr>
			{{foreach name=data item=item from=$smarty.post.displayed_data|smarty:nodefaults}}
				{{if is_array($item.matrix)}}
					<tr>
						<td class="de_table_control">
							<table border="1" cellpadding="4" cellspacing="0">
								<colgroup>
									{{section name=data start=0 step=1 loop=$smarty.post.items_in_row}}
										<col width="150"/>
									{{/section}}
								</colgroup>
								{{assign var=elements_amount value=`$item.places_count/$smarty.post.items_in_row`}}
								{{assign var=pos value=1}}

								<tr>
									<td colspan="{{$smarty.post.items_in_row}}">
										<div class="de_lv_pair">
											<input type="checkbox" name="reset_place[]" value="{{$item.id}}"/>
											<b><label>{{$item.id}}</label></b>
										</div>
									</td>
								</tr>
								{{section name=elements_data start=0 step=1 loop=$elements_amount|replace:",":"."|ceil}}
									<tr>
										{{section name=data start=0 step=1 loop=$smarty.post.items_in_row}}
											<td>
												{{if $item.matrix[$pos]!=''}}
													{{$item.matrix[$pos]}} ({{$item.matrix_pc[$pos]}}%)
												{{else}}
													0
												{{/if}}
											</td>
											{{assign var=pos value=$pos+1}}
										{{/section}}
									</tr>
								{{/section}}
							</table>
						</td>
					</tr>
				{{/if}}
			{{/foreach}}
			<tr>
				<td class="de_action_group">
					<input type="submit" value="{{$lang.plugins.rotator_clicks_matrix.btn_reset}}" class="de_confirm" alt="{{$lang.plugins.rotator_clicks_matrix.btn_reset_confirm}}"/>
				</td>
			</tr>
		</table>
	</form>
{{/if}}
