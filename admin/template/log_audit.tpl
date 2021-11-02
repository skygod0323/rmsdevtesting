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

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf">
		<div class="dgf">
			<table>
				<tr>
					<td class="dgf_label">{{$lang.common.dg_filter_show_on_page}}:</td>
					<td class="dgf_control"><input type="text" name="num_on_page" size="3" value="{{$smarty.session.save.$page_name.num_on_page}}"/></td>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control">
						<input type="text" name="se_text" size="20" value="{{$smarty.session.save.$page_name.se_text}}"/>
					</td>
					<td class="dgf_control">
						<input type="submit" value="{{$lang.common.dg_filter_btn_submit}}"/>
						<input type="submit" name="reset_filter" value="{{$lang.common.dg_filter_btn_reset}}" {{if $smarty.session.save.$page_name.se_text=='' && $table_filtered==0}}disabled="disabled"{{/if}}/>
					</td>
					<td class="dgf_advanced_link">
						<a href="javascript:stub()" class="dgf_filters {{if $table_filtered==1}}dgf_selected{{/if}}">{{$lang.common.dg_filter_filters}}</a>
						<a href="javascript:stub()" class="dgf_columns">{{$lang.common.dg_filter_columns}}</a>
					</td>
				</tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_filters {{if $table_filtered==0}}hidden{{/if}}">
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_object_type_id>0}}dgf_selected{{/if}}">{{$lang.common.object_type}}:</td>
					<td class="dgf_control">
						<select name="se_object_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected="selected"{{/if}}>{{$lang.common.object_type_videos}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected="selected"{{/if}}>{{$lang.common.object_type_albums}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_object_type_id==3}}selected="selected"{{/if}}>{{$lang.common.object_type_content_sources}}</option>
							<option value="8" {{if $smarty.session.save.$page_name.se_object_type_id==8}}selected="selected"{{/if}}>{{$lang.common.object_type_content_source_groups}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_object_type_id==4}}selected="selected"{{/if}}>{{$lang.common.object_type_models}}</option>
							<option value="14" {{if $smarty.session.save.$page_name.se_object_type_id==14}}selected="selected"{{/if}}>{{$lang.common.object_type_model_groups}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_object_type_id==5}}selected="selected"{{/if}}>{{$lang.common.object_type_dvds}}</option>
							<option value="10" {{if $smarty.session.save.$page_name.se_object_type_id==10}}selected="selected"{{/if}}>{{$lang.common.object_type_dvd_groups}}</option>
							<option value="6" {{if $smarty.session.save.$page_name.se_object_type_id==6}}selected="selected"{{/if}}>{{$lang.common.object_type_categories}}</option>
							<option value="7" {{if $smarty.session.save.$page_name.se_object_type_id==7}}selected="selected"{{/if}}>{{$lang.common.object_type_category_groups}}</option>
							<option value="9" {{if $smarty.session.save.$page_name.se_object_type_id==9}}selected="selected"{{/if}}>{{$lang.common.object_type_tags}}</option>
							<option value="11" {{if $smarty.session.save.$page_name.se_object_type_id==11}}selected="selected"{{/if}}>{{$lang.common.object_type_post_types}}</option>
							<option value="12" {{if $smarty.session.save.$page_name.se_object_type_id==12}}selected="selected"{{/if}}>{{$lang.common.object_type_posts}}</option>
							<option value="13" {{if $smarty.session.save.$page_name.se_object_type_id==13}}selected="selected"{{/if}}>{{$lang.common.object_type_playlists}}</option>
							<option value="15" {{if $smarty.session.save.$page_name.se_object_type_id==15}}selected="selected"{{/if}}>{{$lang.common.object_type_comments}}</option>
							<option value="30" {{if $smarty.session.save.$page_name.se_object_type_id==30}}selected="selected"{{/if}}>{{$lang.common.object_type_settings}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_object_id>0}}dgf_selected{{/if}}">{{$lang.settings.audit_log_filter_object_id}}:</td>
					<td class="dgf_control"><input type="text" name="se_object_id" size="10" value="{{if $smarty.session.save.$page_name.se_object_id>0}}{{$smarty.session.save.$page_name.se_object_id}}{{/if}}"/></td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_action_type_id>0}}dgf_selected{{/if}}">{{$lang.settings.audit_log_field_action}}:</td>
					<td class="dgf_control">
						<select name="se_action_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_action_type_id==1}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_added_object_manually}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_action_type_id==2}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_added_object_import}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_action_type_id==3}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_added_object_feed}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_action_type_id==4}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_added_object_plugin}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_action_type_id==5}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_object}}</option>
							<option value="6" {{if $smarty.session.save.$page_name.se_action_type_id==6}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_video_screenshots}}</option>
							<option value="7" {{if $smarty.session.save.$page_name.se_action_type_id==7}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_album_images}}</option>
							<option value="8" {{if $smarty.session.save.$page_name.se_action_type_id==8}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_object_massedit}}</option>
							<option value="9" {{if $smarty.session.save.$page_name.se_action_type_id==9}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_deleted_object}}</option>
							<option value="10" {{if $smarty.session.save.$page_name.se_action_type_id==10}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_translated_object}}</option>
							<option value="220" {{if $smarty.session.save.$page_name.se_action_type_id==220}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_content_settings}}</option>
							<option value="221" {{if $smarty.session.save.$page_name.se_action_type_id==221}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_website_settings}}</option>
							<option value="222" {{if $smarty.session.save.$page_name.se_action_type_id==222}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_memberzone_settings}}</option>
							<option value="223" {{if $smarty.session.save.$page_name.se_action_type_id==223}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_stats_settings}}</option>
							<option value="224" {{if $smarty.session.save.$page_name.se_action_type_id==224}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_customization_settings}}</option>
							<option value="225" {{if $smarty.session.save.$page_name.se_action_type_id==225}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_player_settings}}</option>
							<option value="226" {{if $smarty.session.save.$page_name.se_action_type_id==226}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_embed_settings}}</option>
							<option value="227" {{if $smarty.session.save.$page_name.se_action_type_id==227}}selected="selected"{{/if}}>{{$lang.settings.audit_log_field_action_modified_antispam_settings}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_admin_id>0}}dgf_selected{{/if}}">{{$lang.settings.audit_log_filter_admin}}:</td>
					<td class="dgf_control">
						<select name="se_admin_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_admins|smarty:nodefaults}}
								<option value="{{$item.user_id}}" {{if $smarty.session.save.$page_name.se_admin_id==$item.user_id}}selected="selected"{{/if}}>{{$item.login}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.settings.audit_log_filter_user}}:</td>
					<td class="dgf_control">
						<div class="insight">
							<div class="js_params">
								<span class="js_param">url=async/insight_users.php</span>
							</div>
							<input type="text" name="se_user" size="20" value="{{$smarty.session.save.$page_name.se_user}}"/>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_from>0}}dgf_selected{{/if}}">{{$lang.settings.audit_log_filter_date_from}}:</td>
					{{if $smarty.session.save.$page_name.se_date_from!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_from}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_from_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_date_to>0}}dgf_selected{{/if}}">{{$lang.settings.audit_log_filter_date_to}}:</td>
					{{if $smarty.session.save.$page_name.se_date_to!=''}}{{assign var="temp" value=$smarty.session.save.$page_name.se_date_to}}{{else}}{{assign var="temp" value='00-00-000'}}{{/if}}
					<td class="dgf_control">{{html_select_date prefix='se_date_to_' start_year='+0' end_year='2006' reverse_years="1" field_order=DMY time=$temp}}</td>
				</tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_columns hidden">
			{{assign var="table_columns_display_mode" value="selector"}}
			{{include file="table_columns_inc.tpl"}}
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					{{assign var="table_columns_display_mode" value="sizes"}}
					{{include file="table_columns_inc.tpl"}}
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" disabled="disabled"/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
				</tr>
					{{foreach name=data item=item from=$data|smarty:nodefaults}}
					<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
						<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" disabled="disabled"/></td>
						{{assign var="table_columns_display_mode" value="data"}}
						{{include file="table_columns_inc.tpl"}}
					</tr>
					{{/foreach}}
		   </table>
		</div>
		<div class="dgb"><table><tr><td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td></tr></table></div>
	</form>
</div>
{{include file="navigation.tpl"}}