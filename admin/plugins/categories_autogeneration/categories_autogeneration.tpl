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
		<input type="hidden" name="action" value="save"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.categories_autogeneration.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.categories_autogeneration.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.categories_autogeneration.field_enable_for_videos}}:</td>
			<td class="de_control">
				<select name="enable_for_videos">
					<option value="0" {{if $smarty.post.enable_for_videos==0}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_enable_for_videos_disabled}}</option>
					<option value="1" {{if $smarty.post.enable_for_videos==1}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_enable_for_videos_always}}</option>
					<option value="2" {{if $smarty.post.enable_for_videos==2}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_enable_for_videos_empty}}</option>
				</select>
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_label">{{$lang.plugins.categories_autogeneration.field_enable_for_albums}}:</td>
				<td class="de_control">
					<select name="enable_for_albums">
						<option value="0" {{if $smarty.post.enable_for_albums==0}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_enable_for_albums_disabled}}</option>
						<option value="1" {{if $smarty.post.enable_for_albums==1}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_enable_for_albums_always}}</option>
						<option value="2" {{if $smarty.post.enable_for_albums==2}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_enable_for_albums_empty}}</option>
					</select>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">
				<div class="lenient_0 lenient_1">{{$lang.plugins.categories_autogeneration.field_lenient}}:</div>
				<div class="de_required lenient_2">{{$lang.plugins.categories_autogeneration.field_lenient}} (*):</div>
			</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_vis_sw_select">
								<select id="lenient" name="lenient">
									<option value="0">{{$lang.plugins.categories_autogeneration.field_lenient_off}}</option>
									<option value="1" {{if $smarty.post.lenient==1}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_lenient_all}}</option>
									<option value="2" {{if $smarty.post.lenient==2}}selected="selected"{{/if}}>{{$lang.plugins.categories_autogeneration.field_lenient_specific}}</option>
								</select>
							</div>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<span class="de_hint">{{$lang.plugins.categories_autogeneration.field_lenient_hint1}}</span>
							{{/if}}
						</td>
					</tr>
					<tr class="lenient_2">
						<td>
							<textarea name="lenient_list" rows="3" cols="40" class="dyn_full_size">{{$smarty.post.lenient_list}}</textarea>
							{{if $smarty.session.userdata.is_expert_mode==0}}
								<br/><span class="de_hint">{{$lang.plugins.categories_autogeneration.field_lenient_hint2}}</span>
							{{/if}}
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.categories_autogeneration.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>