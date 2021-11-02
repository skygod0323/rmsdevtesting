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
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.plugins.submenu_plugins_home}}</a> / {{$lang.plugins.avatars_generation.title}} &nbsp;[<a id="doc_expander" class="de_expand" href="javascript:stub()">{{$lang.plugins.plugin_divider_description}}</a>]</div></td>
		</tr>
		<tr class="doc_expander hidden">
			<td class="de_control" colspan="2">
				{{$lang.plugins.avatars_generation.long_desc}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.avatars_generation.field_enable}}:</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="is_enabled" name="is_enabled">
						<option value="0" {{if $smarty.post.is_enabled==0}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_enable_disabled}}</option>
						<option value="1" {{if $smarty.post.is_enabled==1}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_enable_videos}}</option>
						{{if $config.installation_type==4}}
							<option value="2" {{if $smarty.post.is_enabled==2}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_enable_albums}}</option>
						{{/if}}
					</select>
				</div>
			</td>
		</tr>
		<tr class="is_enabled_1">
			<td class="de_label">{{$lang.plugins.avatars_generation.field_videos_rule}}:</td>
			<td class="de_control">
				<select name="sort_by">
					<option value="popularity_all" {{if $smarty.post.sort_by=='popularity_all'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_popularity_all}}</option>
					<option value="popularity_month" {{if $smarty.post.sort_by=='popularity_month'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_popularity_month}}</option>
					<option value="popularity_week" {{if $smarty.post.sort_by=='popularity_week'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_popularity_week}}</option>
					<option value="popularity_day" {{if $smarty.post.sort_by=='popularity_day'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_popularity_day}}</option>
					<option value="rating_all" {{if $smarty.post.sort_by=='rating_all'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_rating_all}}</option>
					<option value="rating_month" {{if $smarty.post.sort_by=='rating_month'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_rating_month}}</option>
					<option value="rating_week" {{if $smarty.post.sort_by=='rating_week'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_rating_week}}</option>
					<option value="rating_day" {{if $smarty.post.sort_by=='rating_day'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_rating_day}}</option>
					<option value="most_commented" {{if $smarty.post.sort_by=='most_commented'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_most_commented}}</option>
					<option value="most_favourited" {{if $smarty.post.sort_by=='most_favourited'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_most_favourited}}</option>
					<option value="post_date" {{if $smarty.post.sort_by=='post_date'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_post_date}}</option>
					<option value="ctr" {{if $smarty.post.sort_by=='ctr'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_videos_rule_ctr}}</option>
				</select>
			</td>
		</tr>
		{{if $config.installation_type==4}}
			<tr class="is_enabled_2">
				<td class="de_label">{{$lang.plugins.avatars_generation.field_albums_rule}}:</td>
				<td class="de_control">
					<select name="sort_by_albums">
						<option value="popularity_all" {{if $smarty.post.sort_by_albums=='popularity_all'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_popularity_all}}</option>
						<option value="popularity_month" {{if $smarty.post.sort_by_albums=='popularity_month'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_popularity_month}}</option>
						<option value="popularity_week" {{if $smarty.post.sort_by_albums=='popularity_week'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_popularity_week}}</option>
						<option value="popularity_day" {{if $smarty.post.sort_by_albums=='popularity_day'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_popularity_day}}</option>
						<option value="rating_all" {{if $smarty.post.sort_by_albums=='rating_all'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_rating_all}}</option>
						<option value="rating_month" {{if $smarty.post.sort_by_albums=='rating_month'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_rating_month}}</option>
						<option value="rating_week" {{if $smarty.post.sort_by_albums=='rating_week'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_rating_week}}</option>
						<option value="rating_day" {{if $smarty.post.sort_by_albums=='rating_day'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_rating_day}}</option>
						<option value="most_commented" {{if $smarty.post.sort_by_albums=='most_commented'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_most_commented}}</option>
						<option value="most_favourited" {{if $smarty.post.sort_by_albums=='most_favourited'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_most_favourited}}</option>
						<option value="post_date" {{if $smarty.post.sort_by_albums=='post_date'}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_albums_rule_post_date}}</option>
					</select>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label de_required">{{$lang.plugins.avatars_generation.field_im_options}} (*):</td>
			<td class="de_control">
				<textarea name="im_options" class="dyn_full_size" cols="30" rows="3">{{$smarty.post.im_options}}</textarea>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.avatars_generation.field_im_options_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.plugins.avatars_generation.field_crop_options}}:</td>
			<td class="de_control">
				{{$lang.plugins.avatars_generation.field_crop_options_left}}:
				<input type="text" name="crop_options_left" maxlength="1000" class="fixed_50" value="{{$smarty.post.crop_options_left}}"/>
				<select name="crop_options_left_unit">
					<option value="1" {{if $smarty.post.crop_options_left_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
					<option value="2" {{if $smarty.post.crop_options_left_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
				</select>
				&nbsp;
				{{$lang.plugins.avatars_generation.field_crop_options_top}}:
				<input type="text" name="crop_options_top" maxlength="1000" class="fixed_50" value="{{$smarty.post.crop_options_top}}"/>
				<select name="crop_options_top_unit">
					<option value="1" {{if $smarty.post.crop_options_top_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
					<option value="2" {{if $smarty.post.crop_options_top_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
				</select>
				&nbsp;
				{{$lang.plugins.avatars_generation.field_crop_options_right}}:
				<input type="text" name="crop_options_right" maxlength="1000" class="fixed_50" value="{{$smarty.post.crop_options_right}}"/>
				<select name="crop_options_right_unit">
					<option value="1" {{if $smarty.post.crop_options_right_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
					<option value="2" {{if $smarty.post.crop_options_right_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
				</select>
				&nbsp;
				{{$lang.plugins.avatars_generation.field_crop_options_bottom}}:
				<input type="text" name="crop_options_bottom" maxlength="1000" class="fixed_50" value="{{$smarty.post.crop_options_bottom}}"/>
				<select name="crop_options_bottom_unit">
					<option value="1" {{if $smarty.post.crop_options_bottom_unit==1}}selected="selected"{{/if}}>px&nbsp;</option>
					<option value="2" {{if $smarty.post.crop_options_bottom_unit==2}}selected="selected"{{/if}}>%&nbsp;</option>
				</select>
			</td>
		</tr>
		<tr class="is_enabled_1 is_enabled_2">
			<td class="de_label de_required">{{$lang.plugins.avatars_generation.field_schedule}} (*):</td>
			<td class="de_control">
				{{$lang.plugins.avatars_generation.field_schedule_interval}}:
				<input type="text" name="interval" maxlength="10" class="fixed_50" value="{{if $smarty.post.interval>0}}{{$smarty.post.interval}}{{/if}}"/>
				&nbsp;&nbsp;
				{{$lang.plugins.avatars_generation.field_schedule_tod}}:
				<select name="tod">
					<option value="0" {{if $smarty.post.tod==0}}selected="selected"{{/if}}>{{$lang.plugins.avatars_generation.field_schedule_tod_any}}</option>
					<option value="1" {{if $smarty.post.tod==1}}selected="selected"{{/if}}>00:00-01:00</option>
					<option value="2" {{if $smarty.post.tod==2}}selected="selected"{{/if}}>01:00-02:00</option>
					<option value="3" {{if $smarty.post.tod==3}}selected="selected"{{/if}}>02:00-03:00</option>
					<option value="4" {{if $smarty.post.tod==4}}selected="selected"{{/if}}>03:00-04:00</option>
					<option value="5" {{if $smarty.post.tod==5}}selected="selected"{{/if}}>04:00-05:00</option>
					<option value="6" {{if $smarty.post.tod==6}}selected="selected"{{/if}}>05:00-06:00</option>
					<option value="7" {{if $smarty.post.tod==7}}selected="selected"{{/if}}>06:00-07:00</option>
					<option value="8" {{if $smarty.post.tod==8}}selected="selected"{{/if}}>07:00-08:00</option>
					<option value="9" {{if $smarty.post.tod==9}}selected="selected"{{/if}}>08:00-09:00</option>
					<option value="10" {{if $smarty.post.tod==10}}selected="selected"{{/if}}>09:00-10:00</option>
					<option value="11" {{if $smarty.post.tod==11}}selected="selected"{{/if}}>10:00-11:00</option>
					<option value="12" {{if $smarty.post.tod==12}}selected="selected"{{/if}}>11:00-12:00</option>
					<option value="13" {{if $smarty.post.tod==13}}selected="selected"{{/if}}>12:00-13:00</option>
					<option value="14" {{if $smarty.post.tod==14}}selected="selected"{{/if}}>13:00-14:00</option>
					<option value="15" {{if $smarty.post.tod==15}}selected="selected"{{/if}}>14:00-15:00</option>
					<option value="16" {{if $smarty.post.tod==16}}selected="selected"{{/if}}>15:00-16:00</option>
					<option value="17" {{if $smarty.post.tod==17}}selected="selected"{{/if}}>16:00-17:00</option>
					<option value="18" {{if $smarty.post.tod==18}}selected="selected"{{/if}}>17:00-18:00</option>
					<option value="19" {{if $smarty.post.tod==19}}selected="selected"{{/if}}>18:00-19:00</option>
					<option value="20" {{if $smarty.post.tod==20}}selected="selected"{{/if}}>19:00-20:00</option>
					<option value="21" {{if $smarty.post.tod==21}}selected="selected"{{/if}}>20:00-21:00</option>
					<option value="22" {{if $smarty.post.tod==22}}selected="selected"{{/if}}>21:00-22:00</option>
					<option value="23" {{if $smarty.post.tod==23}}selected="selected"{{/if}}>22:00-23:00</option>
					<option value="24" {{if $smarty.post.tod==24}}selected="selected"{{/if}}>23:00-00:00</option>
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.plugins.avatars_generation.field_schedule_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr class="is_enabled_1 is_enabled_2">
			<td class="de_label">{{$lang.plugins.avatars_generation.field_last_exec}}:</td>
			<td class="de_control">
				{{if !$smarty.post.last_exec_date || $smarty.post.last_exec_date=='0000-00-00 00:00:00'}}
					{{$lang.plugins.avatars_generation.field_last_exec_none}}
				{{else}}
					{{$smarty.post.last_exec_date|date_format:$smarty.session.userdata.full_date_format}}
					(<a href="{{$page_name}}?plugin_id=avatars_generation&amp;action=get_log" rel="external">{{$smarty.post.duration|default:0}} {{$lang.plugins.avatars_generation.field_last_exec_seconds}}</a>)
				{{/if}}
			</td>
		</tr>
		<tr class="is_enabled_1 is_enabled_2">
			<td class="de_label">{{$lang.plugins.avatars_generation.field_next_exec}}:</td>
			<td class="de_control">
				{{if !$smarty.post.next_exec_date || $smarty.post.next_exec_date=='0000-00-00 00:00:00'}}
					{{$lang.plugins.avatars_generation.field_next_exec_none}}
				{{else}}
					{{$smarty.post.next_exec_date|date_format:$smarty.session.userdata.full_date_format}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" name="save_default" value="{{$lang.plugins.avatars_generation.btn_save}}"/>
				<input type="submit" name="save_regenerate" value="{{$lang.plugins.avatars_generation.btn_regenerate}}"/>
			</td>
		</tr>
	</table>
</form>