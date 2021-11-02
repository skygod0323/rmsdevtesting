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
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		<input type="hidden" name="action" value="cleanup_complete"/>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.stats.cleanup_header}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.stats.cleanup_field_to_date}} (*):</td>
			<td class="de_control">
				{{html_select_date prefix='to_date_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$smarty.now}}
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.stats.cleanup_field_to_date_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.stats.cleanup_field_stats_to_cleanup}} (*):</td>
			<td class="de_control">
				<table class="control_group">
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="traffic" value="1" {{if $smarty.session.save.stats_cleanup.traffic==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_traffic}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="embed" value="1" {{if $smarty.session.save.stats_cleanup.embed==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_embed}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="videos" value="1" {{if $smarty.session.save.stats_cleanup.videos==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_videos}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="albums" value="1" {{if $smarty.session.save.stats_cleanup.albums==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_albums}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="player" value="1" {{if $smarty.session.save.stats_cleanup.player==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_player}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="search" value="1" {{if $smarty.session.save.stats_cleanup.search==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_search}}</label></div>
						</td>
					</tr>
					<tr>
						<td>
							<div class="de_lv_pair"><input type="checkbox" name="overload" value="1" {{if $smarty.session.save.stats_cleanup.overload==1}}checked="checked"{{/if}}/><label>{{$lang.stats.cleanup_field_stats_to_cleanup_overload}}</label></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_action_group" colspan="2">
				<input type="submit" value="{{$lang.stats.cleanup_btn_cleanup}}"/>
			</td>
		</tr>
	</table>
</form>