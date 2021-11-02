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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.autoreplace_words.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.autoreplace_words.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.autoreplace_words.divider_settings}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.autoreplace_words.field_replace_videos}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="replace_videos_title" value="1" {{if $smarty.post.replace_videos_title==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_title}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="replace_videos_description" value="1" {{if $smarty.post.replace_videos_description==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_description}}</label></div></td>
					</tr>
				</table>
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr>
				<td class="de_label">{{$lang.plugins.autoreplace_words.field_replace_albums}}:</td>
				<td class="de_control">
					<table class="control_group">
						<tr>
							<td><div class="de_lv_pair"><input type="checkbox" name="replace_albums_title" value="1" {{if $smarty.post.replace_albums_title==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_title}}</label></div></td>
						</tr>
						<tr>
							<td><div class="de_lv_pair"><input type="checkbox" name="replace_albums_description" value="1" {{if $smarty.post.replace_albums_description==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_replace_in_description}}</label></div></td>
						</tr>
					</table>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.plugins.autoreplace_words.field_limit}}:</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="limit_feeds" value="1" {{if $smarty.post.limit_feeds==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_limit_feeds}}</label></div></td>
					</tr>
					<tr>
						<td><div class="de_lv_pair"><input type="checkbox" name="limit_grabbers" value="1" {{if $smarty.post.limit_grabbers==1}}checked="checked"{{/if}}/><label>{{$lang.plugins.autoreplace_words.field_limit_grabbers}}</label></div></td>
					</tr>
				</table>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<span class="de_hint">{{$lang.plugins.autoreplace_words.field_limit_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.plugins.autoreplace_words.divider_vocabulary}}</div></td>
		</tr>
		<tr>
			<td class="de_control" colspan="2">
				<textarea name="vocabulary" class="html_code_editor dyn_full_size" rows="30" cols="40" {{if $smarty.post.vocabulary==''}}onfocus="this.value = ''"{{/if}}>{{$smarty.post.vocabulary|default:$lang.plugins.autoreplace_words.field_vocabulary_example}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.autoreplace_words.divider_vocabulary_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.autoreplace_words.btn_save}}"/>
			</td>
		</tr>
	</table>
</form>