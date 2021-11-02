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
		<input type="hidden" name="action" value="create"/>
		<input type="hidden" name="plugin_id" value="{{$smarty.request.plugin_id}}"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.movie_from_image.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.movie_from_image.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.movie_from_image.field_image}} (*):</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.plugins.movie_from_image.field_image}}</span>
						<span class="js_param">accept=jpg</span>
					</div>
					<input type="text" name="source_image" class="fixed_500" maxlength="100" readonly="readonly"/>
					<input type="hidden" name="source_image_hash"/>
					<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
					<input type="button" class="de_fu_remove hidden" value="{{$lang.common.attachment_btn_remove}}"/>
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.movie_from_image.field_image_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.movie_from_image.field_duration}} (*):</td>
			<td class="de_control">
				<input type="text" name="duration" class="dyn_full_size" maxlength="10" value="10"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.movie_from_image.field_duration_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.plugins.movie_from_image.field_quality}} (*):</td>
			<td class="de_control">
				<input type="text" name="quality" class="dyn_full_size" maxlength="1000" value="-vcodec libx264 -threads 0 -crf 28 -vf &quot;fps=25,format=yuv420p&quot;"/>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.movie_from_image.field_quality_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.movie_from_image.btn_create}}"/>
			</td>
		</tr>
	</table>
</form>