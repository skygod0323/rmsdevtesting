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

{{if $smarty.get.action=='add_new' || $smarty.get.action=='change'}}

{{if in_array('users|edit_all',$smarty.session.permissions) || (in_array('users|add',$smarty.session.permissions) && $smarty.get.action=='add_new')}}
	{{assign var=can_edit_all value=1}}
{{else}}
	{{assign var=can_edit_all value=0}}
{{/if}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<div>
		{{if $smarty.get.action=='add_new'}}
			<input type="hidden" name="action" value="add_new_complete"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
		{{if $options.USER_COVER_OPTION==0}}
			<input type="hidden" name="cover" value="{{$smarty.post.cover}}"/>
			<input type="hidden" name="cover_hash"/>
		{{/if}}
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col/>
			<col width="5%"/>
			<col/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="4"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_users_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.user_add}}{{else}}{{$lang.users.user_edit|replace:"%1%":$smarty.post.username}}{{/if}}</div></td>
		</tr>
		{{if $smarty.post.login_protection_is_banned==1}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_memberzone_protection}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.users.user_divider_memberzone_protection_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.user_field_analysis_period}}:</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.login_protection_date_from!='0000-00-00 00:00:00'}}
						{{$smarty.post.login_protection_date_from|date_format:$smarty.session.userdata.full_date_format}}
					{{else}}
						{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}
					{{/if}}
					&nbsp;-&nbsp;
					{{$smarty.now|date_format:$smarty.session.userdata.full_date_format}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_analysis_data}}:</td>
				<td class="de_control" colspan="3">
					{{$lang.users.user_field_analysis_data_ips|replace:"%1%":$smarty.post.unique_ips}}
					&nbsp;&nbsp;
					{{$lang.users.user_field_analysis_data_ipmasks|replace:"%1%":$smarty.post.unique_ipmasks}}
					&nbsp;&nbsp;
					{{$lang.users.user_field_analysis_data_countries|replace:"%1%":$smarty.post.unique_countries}}
					&nbsp;&nbsp;
					{{$lang.users.user_field_analysis_data_browsers|replace:"%1%":$smarty.post.unique_browsers}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_ban_type}}:</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.login_protection_restore_code>0}}
						{{$lang.users.user_field_ban_type_temporary}}
					{{else}}
						{{$lang.users.user_field_ban_type_forever}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_make_unbanned}}:</td>
				<td class="de_control" colspan="3"><div class="de_lv_pair"><input type="checkbox" name="is_unbanned" value="1"/><span>{{$lang.users.user_field_make_unbanned_yes}}</span></div></td>
			</tr>
		{{/if}}
		{{if $smarty.post.is_removal_requested==1 && $can_edit_all==1}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_removal_requested}}</div></td>
			</tr>
			{{if $smarty.session.userdata.is_expert_mode==0}}
				<tr>
					<td class="de_simple_text" colspan="4">
						<span class="de_hint">{{$lang.users.user_divider_removal_requested_hint}}</span>
					</td>
				</tr>
			{{/if}}
			<tr>
				<td class="de_label">{{$lang.users.user_field_removal_reason}}:</td>
				<td class="de_control" colspan="3"><textarea class="dyn_full_size" readonly="readonly" cols="40" rows="4">{{$smarty.post.removal_reason|default:$lang.users.user_field_removal_reason_na}}</textarea></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_decline_removal}}:</td>
				<td class="de_control" colspan="3"><div class="de_lv_pair"><input type="checkbox" name="decline_removal" value="1"/><label>{{$lang.users.user_field_decline_removal_yes}}</label></div></td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_general}}</div></td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.user_field_username}} (*):</td>
			<td class="de_control"><input type="text" name="username" maxlength="100" size="30" value="{{$smarty.post.username}}"/></td>
			{{if $smarty.get.action=='add_new'}}
				<td class="de_label de_required">{{$lang.users.user_field_password}} (*):</td>
				<td class="de_control">
					<input type="password" name="pass1" maxlength="32" size="30"/>
				</td>
			{{else}}
				<td class="de_label">{{$lang.users.user_field_password}}:</td>
				<td class="de_control">
					<input type="password" name="pass1" maxlength="32" size="30"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.user_field_password_hint}}</span>
					{{/if}}
				</td>
			{{/if}}
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.user_field_display_name}} (*):</td>
			<td class="de_control"><input type="text" name="display_name" maxlength="100" size="30" value="{{$smarty.post.display_name}}"/></td>
			{{if $smarty.get.action=='add_new'}}
				<td class="de_label de_required">{{$lang.users.user_field_password_confirm}} (*):</td>
				<td class="de_control">
					<input type="password" name="pass2" maxlength="32" size="30"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.user_field_password_confirm_hint}}</span>
					{{/if}}
				</td>
			{{else}}
				<td class="de_label">{{$lang.users.user_field_password_confirm}}:</td>
				<td class="de_control">
					<input type="password" name="pass2" maxlength="32" size="30"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.user_field_password_confirm_hint}}</span>
					{{/if}}
				</td>
			{{/if}}
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.user_field_email}} (*):</td>
			<td class="de_control"><input type="text" name="email" maxlength="100" size="30" value="{{$smarty.post.email}}"/></td>
			<td class="de_label">{{$lang.users.user_field_birth_date}}:</td>
			<td class="de_control">{{html_select_date prefix='birth_date_' start_year='-120' end_year=$config.min_user_age field_order=DMY time=$smarty.post.birth_date reverse_years=1}}</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.user_field_status}} (*):</td>
			<td class="de_control">
				<div class="de_vis_sw_select">
					<select id="status_id" name="status_id" {{if $smarty.post.status_id=='4'}}disabled="disabled"{{/if}}>
						{{if $smarty.post.status_id=='4'}}
							<option value="4" selected="selected">{{$lang.users.user_field_status_anonymous}}</option>
						{{else}}
							<option value="6" {{if $smarty.post.status_id=='6'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_webmaster}}</option>
							{{if $smarty.post.status_id=='3' || $smarty.post.last_open_transaction.transaction_id>0}}
								<option value="3" {{if $smarty.post.status_id=='3'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_premium}}</option>
							{{/if}}
							<option value="2" {{if $smarty.post.status_id=='2'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_active}}</option>
							{{if $smarty.post.status_id=='1'}}
								<option value="1" selected="selected">{{$lang.users.user_field_status_not_confirmed}}</option>
							{{/if}}
							<option value="0" {{if $smarty.post.status_id=='0'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_disabled}}</option>
						{{/if}}
					</select>
					{{if $smarty.get.action!='add_new'}}
						{{if $smarty.post.last_open_transaction.time_left>0 || $smarty.post.last_open_transaction.is_unlimited_access==1}}
							&nbsp;
							<a class="status_id_3" href="bill_transactions.php?action=change&amp;item_id={{$smarty.post.last_open_transaction.transaction_id}}">
								{{if $smarty.post.last_open_transaction.is_unlimited_access==1}}{{$lang.users.user_field_status_premium_unlimited}}{{else}}{{$lang.users.user_field_status_premium_left|replace:"%1%":$smarty.post.last_open_transaction.time_left}}{{/if}}{{if $smarty.post.is_trial==1}}, {{$lang.users.user_field_status_premium_trial}}{{/if}}
							</a>
						{{elseif $smarty.post.status_id!=1 && $smarty.post.status_id!=3}}
							&nbsp;
							<a href="bill_transactions.php?action=add_new&amp;user={{$smarty.post.username}}">{{$lang.users.user_field_status_premium_award}}</a>
						{{/if}}
					{{/if}}
				</div>
			</td>
			<td class="de_label">{{$lang.users.user_field_gender}}:</td>
			<td class="de_control">
				<select name="gender_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					<option value="1" {{if $smarty.post.gender_id=='1'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_male}}</option>
					<option value="2" {{if $smarty.post.gender_id=='2'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_female}}</option>
					<option value="3" {{if $smarty.post.gender_id=='3'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_couple}}</option>
					<option value="4" {{if $smarty.post.gender_id=='4'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_transsexual}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_country}}:</td>
			<td class="de_control">
				<select name="country_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach item="item" from=$list_countries|smarty:nodefaults}}
						<option value="{{$item.country_id}}" {{if $smarty.post.country_id==$item.country_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
			<td class="de_label">{{$lang.users.user_field_relationship_status}}:</td>
			<td class="de_control">
				<select name="relationship_status_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					<option value="1" {{if $smarty.post.relationship_status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.user_field_relationship_status_single}}</option>
					<option value="2" {{if $smarty.post.relationship_status_id=='2'}}selected="selected"{{/if}}>{{$lang.users.user_field_relationship_status_married}}</option>
					<option value="3" {{if $smarty.post.relationship_status_id=='3'}}selected="selected"{{/if}}>{{$lang.users.user_field_relationship_status_open}}</option>
					<option value="4" {{if $smarty.post.relationship_status_id=='4'}}selected="selected"{{/if}}>{{$lang.users.user_field_relationship_status_divorced}}</option>
					<option value="5" {{if $smarty.post.relationship_status_id=='5'}}selected="selected"{{/if}}>{{$lang.users.user_field_relationship_status_widowed}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_city}}:</td>
			<td class="de_control"><input type="text" name="city" maxlength="100" size="30" value="{{$smarty.post.city}}"/></td>
			<td class="de_label">{{$lang.users.user_field_orientation}}:</td>
			<td class="de_control">
				<select name="orientation_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					<option value="1" {{if $smarty.post.orientation_id=='1'}}selected="selected"{{/if}}>{{$lang.users.user_field_orientation_unknown}}</option>
					<option value="2" {{if $smarty.post.orientation_id=='2'}}selected="selected"{{/if}}>{{$lang.users.user_field_orientation_straight}}</option>
					<option value="3" {{if $smarty.post.orientation_id=='3'}}selected="selected"{{/if}}>{{$lang.users.user_field_orientation_gay}}</option>
					<option value="4" {{if $smarty.post.orientation_id=='4'}}selected="selected"{{/if}}>{{$lang.users.user_field_orientation_lesbian}}</option>
					<option value="5" {{if $smarty.post.orientation_id=='5'}}selected="selected"{{/if}}>{{$lang.users.user_field_orientation_bisexual}}</option>
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_avatar}}:</td>
			<td class="de_control">
				<div class="de_fu">
					<div class="js_params">
						<span class="js_param">title={{$lang.users.user_field_avatar}}</span>
						<span class="js_param">accept={{$config.image_allowed_ext}}</span>
						{{if $smarty.get.action=='change' && $smarty.post.avatar!=''}}
							<span class="js_param">preview_url={{$config.content_url_avatars}}/{{$smarty.post.avatar}}</span>
						{{/if}}
					</div>
					<input type="text" name="avatar" maxlength="100" class="fixed_200" {{if $smarty.get.action=='change' && $smarty.post.avatar!=''}}value="{{$smarty.post.avatar}}"{{/if}} readonly="readonly"/>
					<input type="hidden" name="avatar_hash"/>
					{{if $can_edit_all==1}}
						<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
						<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.avatar==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
					{{/if}}
					{{if $smarty.get.action=='change' && $smarty.post.avatar!=''}}
						<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
					{{/if}}
				</div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.user_field_avatar_hint}} (<a href="options.php?page=general_settings">{{$options.USER_AVATAR_SIZE}}</a>)</span>
				{{/if}}
			</td>
			<td class="de_label" {{if $options.USER_COVER_OPTION>0}}rowspan="2"{{/if}}>{{$lang.users.user_field_description}}:</td>
			<td class="de_control" {{if $options.USER_COVER_OPTION>0}}rowspan="2"{{/if}}>
				<textarea name="description" rows="{{if $options.USER_COVER_OPTION>0}}4{{else}}2{{/if}}" cols="30">{{$smarty.post.description}}</textarea>
			</td>
		</tr>
		{{if $options.USER_COVER_OPTION>0}}
			<tr>
				<td class="de_label">{{$lang.users.user_field_cover}}:</td>
				<td class="de_control">
					<div class="de_fu">
						<div class="js_params">
							<span class="js_param">title={{$lang.users.user_field_cover}}</span>
							<span class="js_param">accept={{$config.image_allowed_ext}}</span>
							{{if $smarty.get.action=='change' && $smarty.post.cover!=''}}
								<span class="js_param">preview_url={{$config.content_url_avatars}}/{{$smarty.post.cover}}</span>
							{{/if}}
						</div>
						<input type="text" name="cover" maxlength="100" class="fixed_200" {{if $smarty.get.action=='change' && $smarty.post.cover!=''}}value="{{$smarty.post.cover}}"{{/if}} readonly="readonly"/>
						<input type="hidden" name="cover_hash"/>
						{{if $can_edit_all==1}}
							<input type="button" class="de_fu_upload" value="{{$lang.common.attachment_btn_upload}}"/>
							<input type="button" class="de_fu_remove{{if $smarty.get.action=='add_new' || $smarty.post.cover==''}} hidden{{/if}}" value="{{$lang.common.attachment_btn_remove}}"/>
						{{/if}}
						{{if $smarty.get.action=='change' && $smarty.post.cover!=''}}
							<input type="button" class="de_fu_preview" value="{{$lang.common.attachment_btn_preview}}"/>
						{{/if}}
					</div>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.user_field_cover_hint}} (<a href="options.php?page=general_settings">{{$options.USER_COVER_SIZE}}</a>){{if $options.USER_COVER_OPTION==1}}; {{$lang.users.user_field_cover_hint2|replace:"%1%":$lang.users.user_field_avatar}}{{/if}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">{{$lang.users.user_field_memberzone_protection}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_lv_pair"><input type="checkbox" name="login_protection_is_skipped" value="1" {{if $smarty.post.login_protection_is_skipped==1}}checked="checked"{{/if}}/><span {{if $smarty.post.login_protection_is_skipped==1}}class="selected"{{/if}}>{{$lang.users.user_field_memberzone_protection_skip}}</span></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.user_field_memberzone_protection_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_trusted}}:</td>
			<td class="de_control" colspan="3">
				<div class="de_lv_pair"><input type="checkbox" name="is_trusted" value="1" {{if $smarty.post.is_trusted==1}}checked="checked"{{/if}}/><label>{{$lang.users.user_field_trusted_yes}}</label></div>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.user_field_trusted_hint}}</span>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_webmaster_cs_group}}:</td>
			<td class="de_control" colspan="3">
				<select name="content_source_group_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach item="item" from=$list_cs_groups|smarty:nodefaults}}
						<option value="{{$item.content_source_group_id}}" {{if $item.content_source_group_id==$smarty.post.content_source_group_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
				{{if $smarty.session.userdata.is_expert_mode==0}}
					<br/><span class="de_hint">{{$lang.users.user_field_webmaster_cs_group_hint}}</span>
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.users.user_field_tokens_available}}:</td>
				<td class="de_control" colspan="3">
					{{$smarty.post.tokens_available}}&nbsp;
					{{if in_array('billing|edit_all',$smarty.session.permissions)}}
						<a href="bill_transactions.php?action=add_new&amp;user={{$smarty.post.username}}">{{$lang.users.user_field_tokens_available_award}}</a>
					{{/if}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.user_field_tokens_available_hint}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $options.ENABLE_TOKENS_SUBSCRIBE_MEMBERS==1}}
			<tr>
				<td class="de_label">{{$lang.users.user_field_tokens_required}}:</td>
				<td class="de_control" colspan="3">
					<input type="text" name="tokens_required" maxlength="10" size="10" value="{{$smarty.post.tokens_required}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.user_field_tokens_required_hint|replace:"%1%":$options.TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE}}</span>
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_stats}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_logins}}:</td>
				<td class="de_control">
					{{if in_array('stats|view_user_stats',$smarty.session.permissions)}}
						<a href="stats_users_logins.php?no_filter=true&amp;se_group_by=log&amp;se_user={{$smarty.post.username}}">{{$smarty.post.logins_count}}</a>
					{{else}}
						{{$smarty.post.logins_count}}
					{{/if}}
					{{if $smarty.post.last_login_days==0}}
						({{$lang.users.user_field_logins_today}})
					{{elseif $smarty.post.last_login_days>0}}
						({{$lang.users.user_field_logins_days|replace:"%1%":$smarty.post.last_login_days}})
					{{/if}}
				</td>
				<td class="de_label">{{$lang.users.user_field_average_sess_duration}}:</td>
				<td class="de_control">{{$smarty.post.avg_sess_duration}}</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_video_watched}}:</td>
				<td class="de_control">{{$smarty.post.video_watched}} {{if $smarty.post.video_watched_unique>0}}({{$lang.users.user_field_video_watched_unique|replace:"%1%":$smarty.post.video_watched_unique}}){{/if}}</td>
				<td class="de_label">{{$lang.users.user_field_album_watched}}:</td>
				<td class="de_control">{{$smarty.post.album_watched}} {{if $smarty.post.album_watched_unique>0}}({{$lang.users.user_field_album_watched_unique|replace:"%1%":$smarty.post.album_watched_unique}}){{/if}}</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_video_rated}}:</td>
				<td class="de_control">{{$smarty.post.ratings_videos_count}}</td>
				<td class="de_label">{{$lang.users.user_field_album_rated}}:</td>
				<td class="de_control">{{$smarty.post.ratings_albums_count}}</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_video_viewed}}:</td>
				<td class="de_control">{{$smarty.post.video_viewed}}</td>
				<td class="de_label">{{$lang.users.user_field_album_viewed}}:</td>
				<td class="de_control">{{$smarty.post.album_viewed}}</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_visits}}:</td>
				<td class="de_control">{{$smarty.post.profile_viewed}}</td>
				<td class="de_label">{{$lang.users.user_field_activity_rank}}:</td>
				<td class="de_control">#{{$smarty.post.activity_rank}} ({{$lang.users.user_field_activity_rank_points|replace:"%1%":$smarty.post.activity}})</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_videos}}:</td>
				<td class="de_control">
					{{if in_array('videos|view',$smarty.session.permissions)}}
						<a href="videos.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.videos_count}}</a>
					{{else}}
						{{$smarty.post.videos_count}}
					{{/if}}
				</td>
				<td class="de_label">{{$lang.users.user_field_albums}}:</td>
				<td class="de_control">
					{{if in_array('albums|view',$smarty.session.permissions)}}
						<a href="albums.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.albums_count}}</a>
					{{else}}
						{{$smarty.post.albums_count}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_dvds}}:</td>
				<td class="de_control">
					{{if in_array('dvds|view',$smarty.session.permissions)}}
						<a href="dvds.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.dvds_count}}</a>
					{{else}}
						{{$smarty.post.dvds_count}}
					{{/if}}
				</td>
				<td class="de_label">{{$lang.users.user_field_playlists}}:</td>
				<td class="de_control">
					{{if in_array('playlists|view',$smarty.session.permissions)}}
						<a href="playlists.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.playlists_count}}</a>
					{{else}}
						{{$smarty.post.playlists_count}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_posts}}:</td>
				<td class="de_control">
					{{if in_array('posts|view',$smarty.session.permissions)}}
						<a href="posts.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.posts_count}}</a>
					{{else}}
						{{$smarty.post.posts_count}}
					{{/if}}
				</td>
				<td class="de_label">{{$lang.users.user_field_comments}}:</td>
				<td class="de_control">
					{{if in_array('users|manage_comments',$smarty.session.permissions)}}
						<a href="comments.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.comments_count}}</a>
					{{else}}
						{{$smarty.post.comments_count}}
					{{/if}}
				</td>
			</tr>
		{{/if}}
		{{if count($smarty.post.transactions)>0}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_transactions}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_reseller_code}}:</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.reseller_code!=''}}
						{{$smarty.post.reseller_code}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_total_payments}}:</td>
				<td class="de_control" colspan="3">
					{{if $smarty.post.total_payments!=''}}
						{{$smarty.post.total_payments}}
					{{else}}
						{{$lang.common.undefined}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="4">
					<table class="de_edit_grid">
						<colgroup>
							<col width="8%"/>
							<col/>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.users.bill_transaction_field_id}}</td>
							<td>{{$lang.users.bill_transaction_field_bill_type}}</td>
							<td>{{$lang.users.bill_transaction_field_type}}</td>
							<td>{{$lang.users.bill_transaction_field_status}}</td>
							<td>{{$lang.users.bill_transaction_field_start_date}}</td>
							<td>{{$lang.users.bill_transaction_field_end_date}}</td>
						</tr>
						{{foreach item=item from=$smarty.post.transactions|smarty:nodefaults}}
							<tr class="eg_data fixed_height_30">
								<td>
									{{if in_array('billing|view',$smarty.session.permissions)}}
										<a href="bill_transactions.php?action=change&amp;item_id={{$item.transaction_id}}">{{$item.transaction_id}}</a>
									{{else}}
										{{$item.transaction_id}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.bill_type_id==5}}
										{{$lang.users.bill_transaction_field_bill_type_htpasswd}}
									{{elseif $item.bill_type_id==4}}
										{{$lang.users.bill_transaction_field_bill_type_api}}
									{{elseif $item.bill_type_id==3}}
										{{$lang.users.bill_transaction_field_bill_type_sms}} ({{$item.internal_provider_id}})
									{{elseif $item.bill_type_id==2}}
										{{$lang.users.bill_transaction_field_bill_type_card}} ({{$item.internal_provider_id}})
									{{elseif $item.bill_type_id==1}}
										{{$lang.users.bill_transaction_field_bill_type_manual}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.type_id==10}}
										{{$lang.users.bill_transaction_field_type_tokens}} ({{$item.tokens_granted}})
									{{elseif $item.type_id==6}}
										{{$lang.users.bill_transaction_field_type_void}}
									{{elseif $item.type_id==5}}
										{{$lang.users.bill_transaction_field_type_refund}}
									{{elseif $item.type_id==4}}
										{{$lang.users.bill_transaction_field_type_chargeback}}
									{{elseif $item.type_id==3}}
										{{$lang.users.bill_transaction_field_type_rebill}}
									{{elseif $item.type_id==2}}
										{{$lang.users.bill_transaction_field_type_conversion}}
									{{elseif $item.type_id==1}}
										{{$lang.users.bill_transaction_field_type_initial}} {{if $item.is_trial==1}}({{$lang.users.bill_transaction_field_type_initial_trial}}){{/if}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.status_id==4}}
										{{$lang.users.bill_transaction_field_status_pending}}
									{{elseif $item.status_id==3}}
										{{$lang.users.bill_transaction_field_status_cancelled}}
									{{elseif $item.status_id==2}}
										{{$lang.users.bill_transaction_field_status_closed}}
									{{elseif $item.status_id==1}}
										{{$lang.users.bill_transaction_field_status_open}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.access_start_date=='0000-00-00 00:00:00'}}
										{{$lang.common.undefined}}
									{{else}}
										{{$item.access_start_date|date_format:$smarty.session.userdata.full_date_format}}
									{{/if}}
								</td>
								<td class="nowrap">
									{{if $item.is_unlimited_access==1}}
										{{$lang.users.bill_transaction_field_end_date_unlimited}}
									{{elseif $item.access_end_date=='0000-00-00 00:00:00'}}
										{{$lang.common.undefined}}
									{{else}}
										{{$item.access_end_date|date_format:$smarty.session.userdata.full_date_format}}
									{{/if}}
								</td>
							</tr>
						{{/foreach}}
					</table>
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_payouts}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_paypal_account}}:</td>
				<td class="de_control" colspan="3">
					<input type="text" name="account_paypal" maxlength="255" size="30" value="{{$smarty.post.account_paypal}}"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_tokens_earned}}:</td>
				<td class="de_control" colspan="3">
					{{if in_array('stats|view_user_stats',$smarty.session.permissions)}}
						<a href="stats_users_awards.php?no_filter=true&amp;se_user={{$smarty.post.username}}&amp;se_group_by=log">{{$smarty.post.tokens_earned}}</a>
					{{else}}
						{{$smarty.post.tokens_earned}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.users.user_field_tokens_paid}}:</td>
				<td class="de_control" colspan="3">
					{{if in_array('payouts|view',$smarty.session.permissions)}}
						<a href="payouts.php?no_filter=true&amp;se_user={{$smarty.post.username}}">{{$smarty.post.tokens_paid}}</a>
					{{else}}
						{{$smarty.post.tokens_paid}}
					{{/if}}
				</td>
			</tr>
			<tr>
				<td class="de_table_control" colspan="4">
					<table class="de_edit_grid">
						<colgroup>
							<col width="8%"/>
							<col/>
							<col/>
							<col/>
							<col/>
							<col/>
						</colgroup>
						<tr class="eg_header">
							<td>{{$lang.users.payout_field_id}}</td>
							<td>{{$lang.users.payout_field_status}}</td>
							<td>{{$lang.users.payout_field_tokens}}</td>
							<td>{{$lang.users.payout_field_amount}}</td>
							<td>{{$lang.users.payout_field_currency}}</td>
							<td>{{$lang.users.payout_field_added_date}}</td>
						</tr>
						{{if count($smarty.post.payouts)>0}}
							{{foreach item=item from=$smarty.post.payouts|smarty:nodefaults}}
								<tr class="eg_data fixed_height_30">
									<td>
										{{if in_array('payouts|view',$smarty.session.permissions)}}
											<a href="payouts.php?action=change&amp;item_id={{$item.payout_id}}">{{$item.payout_id}}</a>
										{{else}}
											{{$item.transaction_id}}
										{{/if}}
									</td>
									<td class="nowrap">
										{{if $item.status_id==1}}
											{{$lang.users.payout_field_status_in_progress}}
										{{elseif $item.status_id==2}}
											{{$lang.users.payout_field_status_closed}}
										{{/if}}
									</td>
									<td class="nowrap">
										{{$item.tokens}}
									</td>
									<td class="nowrap">
										{{$item.amount}}
									</td>
									<td class="nowrap">
										{{$item.amount_currency}}
									</td>
									<td class="nowrap">
										{{$item.added_date|date_format:$smarty.session.userdata.full_date_format}}
									</td>
								</tr>
							{{/foreach}}
						{{else}}
							<tr class="eg_data fixed_height_30">
								<td colspan="6">{{$lang.users.user_divider_payouts_none}}</td>
							</tr>
						{{/if}}
					</table>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_additional}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_fav_category}}:</td>
			<td class="de_control" colspan="3">
				<select name="favourite_category_id">
					<option value="">{{$lang.common.select_default_option}}</option>
					{{foreach item="item" from=$list_categories|smarty:nodefaults}}
						<option value="{{$item.category_id}}" {{if $item.category_id==$smarty.post.favourite_category_id}}selected="selected"{{/if}}>{{$item.title}}</option>
					{{/foreach}}
				</select>
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_website}}:</td>
			<td class="de_control" colspan="3"><input type="text" name="website" maxlength="255" class="dyn_full_size" value="{{$smarty.post.website}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_education}}:</td>
			<td class="de_control" colspan="3"><input type="text" name="education" maxlength="255" class="dyn_full_size" value="{{$smarty.post.education}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_occupation}}:</td>
			<td class="de_control" colspan="3"><input type="text" name="occupation" maxlength="255" class="dyn_full_size" value="{{$smarty.post.occupation}}"/></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_about_me}}:</td>
			<td class="de_control" colspan="3"><textarea name="about_me" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.about_me}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_interests}}:</td>
			<td class="de_control" colspan="3"><textarea name="interests" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.interests}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_fav_movies}}:</td>
			<td class="de_control" colspan="3"><textarea name="favourite_movies" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.favourite_movies}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_fav_music}}:</td>
			<td class="de_control" colspan="3"><textarea name="favourite_music" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.favourite_music}}</textarea></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.user_field_fav_books}}:</td>
			<td class="de_control" colspan="3"><textarea name="favourite_books" class="dyn_full_size" cols="40" rows="4">{{$smarty.post.favourite_books}}</textarea></td>
		</tr>
		{{if count($custom_text_fields)>0 || count($custom_file_fields)>0}}
			<tr>
				<td class="de_separator" colspan="4"><div>{{$lang.users.user_divider_customization}}</div></td>
			</tr>
			{{assign var="custom_colspan" value="3"}}
			{{include file="custom_fields_inc.tpl"}}
		{{/if}}
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="4">
					{{if $smarty.get.action=='add_new'}}
						{{if $smarty.session.save.options.default_save_button==1}}
							<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
							<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
						{{else}}
							<input type="submit" name="save_default" value="{{$lang.common.btn_save}}"/>
							<input type="submit" name="save_and_add" value="{{$lang.common.btn_save_and_add}}"/>
						{{/if}}
					{{else}}
						<input type="submit" name="save_and_stay" value="{{$lang.common.btn_save}}"/>
						<input type="submit" name="save_and_close" value="{{$lang.common.btn_save_and_close}}"/>
					{{/if}}
				</td>
			</tr>
		{{/if}}
	</table>
</form>

{{else}}

{{if in_array('users|edit_all',$smarty.session.permissions)}}
	{{assign var=can_unban value=1}}
{{else}}
	{{assign var=can_unban value=0}}
{{/if}}
{{if in_array('users|edit_all',$smarty.session.permissions)}}
	{{assign var=can_confirm value=1}}
{{else}}
	{{assign var=can_confirm value=0}}
{{/if}}
{{if in_array('users|edit_all',$smarty.session.permissions)}}
	{{assign var=can_activate value=1}}
{{else}}
	{{assign var=can_activate value=0}}
{{/if}}
{{if in_array('users|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('messages|add',$smarty.session.permissions)}}
	{{assign var=can_send_message value=1}}
{{else}}
	{{assign var=can_send_message value=0}}
{{/if}}
{{if in_array('stats|view_user_stats',$smarty.session.permissions) && $config.installation_type>=2}}
	{{assign var=can_view_stats value=1}}
{{else}}
	{{assign var=can_view_stats value=0}}
{{/if}}
{{if in_array('system|administration',$smarty.session.permissions)}}
	{{assign var=can_view_logs value=1}}
{{else}}
	{{assign var=can_view_logs value=0}}
{{/if}}
{{if in_array('billing|edit_all',$smarty.session.permissions)}}
	{{assign var=can_add_transaction value=1}}
{{else}}
	{{assign var=can_add_transaction value=0}}
{{/if}}
{{if $can_delete==1 || $can_send_message==1 || $can_add_transaction==1 || $can_view_stats==1 || $can_view_logs==1}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
{{if $can_delete==1 || $can_unban==1 || $can_confirm==1}}
	{{assign var=can_invoke_batch value=1}}
{{else}}
	{{assign var=can_invoke_batch value=0}}
{{/if}}

<div class="dg_wrapper">
	<form action="{{$page_name}}" method="get" class="form_dgf">
		<div class="dgf">
			<table>
				<tr>
					<td class="dgf_label">{{$lang.common.dg_filter_show_on_page}}:</td>
					<td class="dgf_control"><input type="text" name="num_on_page" size="3" value="{{$smarty.session.save.$page_name.num_on_page}}"/></td>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_text!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_search}}:</td>
					<td class="dgf_control dgf_search">
						<input type="text" name="se_text" size="20" autocomplete="off" value="{{$smarty.session.save.$page_name.se_text}}"/>
						{{if count($search_fields)>0}}
							<div class="dgf_search_layer hidden">
								<span>{{$lang.common.dg_filter_search_in}}:</span>
								<ul>
									{{assign var="search_everywhere" value="true"}}
									{{foreach from=$search_fields|smarty:nodefaults item="field"}}
										<li>
											{{assign var="option_id" value="se_text_`$field.id`"}}
											<input type="hidden" name="{{$option_id}}" value="0"/>
											<div class="dg_lv_pair"><input type="checkbox" name="{{$option_id}}" value="1" {{if $smarty.session.save.$page_name[$option_id]==1}}checked="checked"{{/if}}/><label>{{$field.title}}</label></div>
											{{if $smarty.session.save.$page_name[$option_id]!=1}}
												{{assign var="search_everywhere" value="false"}}
											{{/if}}
										</li>
									{{/foreach}}
									<li class="dgf_everywhere">
										<div class="dg_lv_pair"><input type="checkbox" name="se_text_all" value="1" {{if $search_everywhere=='true'}}checked="checked"{{/if}} class="dgf_everywhere"/><label>{{$lang.common.dg_filter_search_in_everywhere}}</label></div>
									</li>
								</ul>
							</div>
						{{/if}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id!=''}}dgf_selected{{/if}}">{{$lang.users.user_field_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="6" {{if $smarty.session.save.$page_name.se_status_id=='6'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_webmaster}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_status_id=='4'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_anonymous}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_status_id=='3'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_premium}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id=='2'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_active}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id=='1'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_not_confirmed}}</option>
							<option value="0" {{if $smarty.session.save.$page_name.se_status_id=='0'}}selected="selected"{{/if}}>{{$lang.users.user_field_status_disabled}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_country_id>0}}dgf_selected{{/if}}">{{$lang.users.user_field_country}}:</td>
					<td class="dgf_control">
						<select name="se_country_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach item="item" from=$list_countries|smarty:nodefaults}}
								<option value="{{$item.country_id}}" {{if $smarty.session.save.$page_name.se_country_id==$item.country_id}}selected="selected"{{/if}}>{{$item.title}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_gender_id!=''}}dgf_selected{{/if}}">{{$lang.users.user_field_gender}}:</td>
					<td class="dgf_control">
						<select name="se_gender_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_gender_id=='1'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_male}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_gender_id=='2'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_female}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_gender_id=='3'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_couple}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_gender_id=='4'}}selected="selected"{{/if}}>{{$lang.users.user_field_gender_transsexual}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_field!=''}}dgf_selected{{/if}}">{{$lang.common.dg_filter_field}}:</td>
					<td class="dgf_control">
						<select name="se_field">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="empty/description" {{if $smarty.session.save.$page_name.se_field=="empty/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_description}}</option>
							<option value="empty/avatar" {{if $smarty.session.save.$page_name.se_field=="empty/avatar"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_avatar}}</option>
							<option value="empty/cover" {{if $smarty.session.save.$page_name.se_field=="empty/cover"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_cover}}</option>
							<option value="empty/profile_viewed" {{if $smarty.session.save.$page_name.se_field=="empty/profile_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_visits}}</option>
							<option value="empty/birth_date" {{if $smarty.session.save.$page_name.se_field=="empty/birth_date"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_birth_date}}</option>
							<option value="empty/country_id" {{if $smarty.session.save.$page_name.se_field=="empty/country_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_country}}</option>
							<option value="empty/city" {{if $smarty.session.save.$page_name.se_field=="empty/city"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_city}}</option>
							<option value="empty/gender_id" {{if $smarty.session.save.$page_name.se_field=="empty/gender_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_gender}}</option>
							<option value="empty/relationship_status_id" {{if $smarty.session.save.$page_name.se_field=="empty/relationship_status_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_relationship_status}}</option>
							<option value="empty/orientation_id" {{if $smarty.session.save.$page_name.se_field=="empty/orientation_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_orientation}}</option>
							<option value="empty/website" {{if $smarty.session.save.$page_name.se_field=="empty/website"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_website}}</option>
							<option value="empty/education" {{if $smarty.session.save.$page_name.se_field=="empty/education"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_education}}</option>
							<option value="empty/occupation" {{if $smarty.session.save.$page_name.se_field=="empty/occupation"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_occupation}}</option>
							<option value="empty/about_me" {{if $smarty.session.save.$page_name.se_field=="empty/about_me"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_about_me}}</option>
							<option value="empty/interests" {{if $smarty.session.save.$page_name.se_field=="empty/interests"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_interests}}</option>
							<option value="empty/favourite_category_id" {{if $smarty.session.save.$page_name.se_field=="empty/favourite_category_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_fav_category}}</option>
							<option value="empty/favourite_movies" {{if $smarty.session.save.$page_name.se_field=="empty/favourite_movies"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_fav_movies}}</option>
							<option value="empty/favourite_music" {{if $smarty.session.save.$page_name.se_field=="empty/favourite_music"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_fav_music}}</option>
							<option value="empty/favourite_books" {{if $smarty.session.save.$page_name.se_field=="empty/favourite_books"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_fav_books}}</option>
							{{if $options.ENABLE_TOKENS_SUBSCRIBE_MEMBERS==1}}
								<option value="empty/tokens_required" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_required"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_tokens_required}}</option>
							{{/if}}
							<option value="empty/tokens_available" {{if $smarty.session.save.$page_name.se_field=="empty/tokens_available"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$lang.users.user_field_tokens_available}}</option>
							{{foreach from=$custom_text_fields|smarty:nodefaults item="custom_field"}}
								<option value="empty/{{$custom_field.field_name}}" {{if $smarty.session.save.$page_name.se_field=="empty/`$custom_field.field_name`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_empty_field|replace:"%1%":$custom_field.name}}</option>
							{{/foreach}}
							<option value="filled/description" {{if $smarty.session.save.$page_name.se_field=="filled/description"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_description}}</option>
							<option value="filled/avatar" {{if $smarty.session.save.$page_name.se_field=="filled/avatar"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_avatar}}</option>
							<option value="filled/cover" {{if $smarty.session.save.$page_name.se_field=="filled/cover"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_cover}}</option>
							<option value="filled/profile_viewed" {{if $smarty.session.save.$page_name.se_field=="filled/profile_viewed"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_visits}}</option>
							<option value="filled/birth_date" {{if $smarty.session.save.$page_name.se_field=="filled/birth_date"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_birth_date}}</option>
							<option value="filled/country_id" {{if $smarty.session.save.$page_name.se_field=="filled/country_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_country}}</option>
							<option value="filled/city" {{if $smarty.session.save.$page_name.se_field=="filled/city"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_city}}</option>
							<option value="filled/gender_id" {{if $smarty.session.save.$page_name.se_field=="filled/gender_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_gender}}</option>
							<option value="filled/relationship_status_id" {{if $smarty.session.save.$page_name.se_field=="filled/relationship_status_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_relationship_status}}</option>
							<option value="filled/orientation_id" {{if $smarty.session.save.$page_name.se_field=="filled/orientation_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_orientation}}</option>
							<option value="filled/website" {{if $smarty.session.save.$page_name.se_field=="filled/website"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_website}}</option>
							<option value="filled/education" {{if $smarty.session.save.$page_name.se_field=="filled/education"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_education}}</option>
							<option value="filled/occupation" {{if $smarty.session.save.$page_name.se_field=="filled/occupation"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_occupation}}</option>
							<option value="filled/about_me" {{if $smarty.session.save.$page_name.se_field=="filled/about_me"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_about_me}}</option>
							<option value="filled/interests" {{if $smarty.session.save.$page_name.se_field=="filled/interests"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_interests}}</option>
							<option value="filled/favourite_category_id" {{if $smarty.session.save.$page_name.se_field=="filled/favourite_category_id"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_fav_category}}</option>
							<option value="filled/favourite_movies" {{if $smarty.session.save.$page_name.se_field=="filled/favourite_movies"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_fav_movies}}</option>
							<option value="filled/favourite_music" {{if $smarty.session.save.$page_name.se_field=="filled/favourite_music"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_fav_music}}</option>
							<option value="filled/favourite_books" {{if $smarty.session.save.$page_name.se_field=="filled/favourite_books"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_fav_books}}</option>
							{{if $options.ENABLE_TOKENS_SUBSCRIBE_MEMBERS==1}}
								<option value="filled/tokens_required" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_required"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_tokens_required}}</option>
							{{/if}}
							<option value="filled/tokens_available" {{if $smarty.session.save.$page_name.se_field=="filled/tokens_available"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$lang.users.user_field_tokens_available}}</option>
							{{foreach from=$custom_text_fields|smarty:nodefaults item="custom_field"}}
								<option value="filled/{{$custom_field.field_name}}" {{if $smarty.session.save.$page_name.se_field=="filled/`$custom_field.field_name`"}}selected="selected"{{/if}}>{{$lang.common.dg_filter_option_non_empty_field|replace:"%1%":$custom_field.name}}</option>
							{{/foreach}}
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_activity!=''}}dgf_selected{{/if}}">{{$lang.users.user_filter_activity}}:</td>
					<td class="dgf_control">
						<select name="se_activity">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="have/logins" {{if $smarty.session.save.$page_name.se_activity=='have/logins'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_logins}}</option>
							<option value="have/logins_week" {{if $smarty.session.save.$page_name.se_activity=='have/logins_week'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_logins_week}}</option>
							<option value="have/logins_month" {{if $smarty.session.save.$page_name.se_activity=='have/logins_month'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_logins_month}}</option>
							<option value="have/logins_year" {{if $smarty.session.save.$page_name.se_activity=='have/logins_year'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_logins_year}}</option>
							<option value="have/videos" {{if $smarty.session.save.$page_name.se_activity=='have/videos'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_videos}}</option>
							<option value="have/albums" {{if $smarty.session.save.$page_name.se_activity=='have/albums'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_albums}}</option>
							{{if $config.dvds_mode=='channels'}}
								<option value="have/dvds" {{if $smarty.session.save.$page_name.se_activity=='have/dvds'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_dvds}}</option>
							{{/if}}
							<option value="have/playlists" {{if $smarty.session.save.$page_name.se_activity=='have/playlists'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_playlists}}</option>
							<option value="have/comments" {{if $smarty.session.save.$page_name.se_activity=='have/comments'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_comments}}</option>
							<option value="have/friends" {{if $smarty.session.save.$page_name.se_activity=='have/friends'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_friends}}</option>
							<option value="no/logins" {{if $smarty.session.save.$page_name.se_activity=='no/logins'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_logins}}</option>
							<option value="no/logins_week" {{if $smarty.session.save.$page_name.se_activity=='no/logins_week'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_logins_week}}</option>
							<option value="no/logins_month" {{if $smarty.session.save.$page_name.se_activity=='no/logins_month'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_logins_month}}</option>
							<option value="no/logins_year" {{if $smarty.session.save.$page_name.se_activity=='no/logins_year'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_logins_year}}</option>
							<option value="no/videos" {{if $smarty.session.save.$page_name.se_activity=='no/videos'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_videos}}</option>
							<option value="no/albums" {{if $smarty.session.save.$page_name.se_activity=='no/albums'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_albums}}</option>
							{{if $config.dvds_mode=='channels'}}
								<option value="no/dvds" {{if $smarty.session.save.$page_name.se_activity=='no/dvds'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_dvds}}</option>
							{{/if}}
							<option value="no/playlists" {{if $smarty.session.save.$page_name.se_activity=='no/playlists'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_playlists}}</option>
							<option value="no/comments" {{if $smarty.session.save.$page_name.se_activity=='no/comments'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_comments}}</option>
							<option value="no/friends" {{if $smarty.session.save.$page_name.se_activity=='no/friends'}}selected="selected"{{/if}}>{{$lang.users.user_filter_activity_no_friends}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_banned_status>0}}dgf_selected{{/if}}">{{$lang.users.user_filter_banned_status}}:</td>
					<td class="dgf_control">
						<select name="se_banned_status">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_banned_status=='1'}}selected="selected"{{/if}}>{{$lang.users.user_filter_banned_status_temporary}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_banned_status=='2'}}selected="selected"{{/if}}>{{$lang.users.user_filter_banned_status_forever}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_control">
						<input type="hidden" name="se_is_removal_requested" value="0">
						<div class="dg_lv_pair">
							<input type="checkbox" name="se_is_removal_requested" value="1" {{if $smarty.session.save.$page_name.se_is_removal_requested=='1'}}checked="checked"{{/if}}/>
							<label>{{$lang.users.user_filter_removal_requested}}</label>
						</div>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_control">
						<input type="hidden" name="se_is_trusted" value="0">
						<div class="dg_lv_pair">
							<input type="checkbox" name="se_is_trusted" value="1" {{if $smarty.session.save.$page_name.se_is_trusted=='1'}}checked="checked"{{/if}}/>
							<label>{{$lang.users.user_filter_trusted}}</label>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="dgf dgf_advanced_columns hidden">
			{{assign var="table_columns_display_mode" value="selector"}}
			{{include file="table_columns_inc.tpl"}}
		</div>
	</form>
	<form action="{{$page_name}}" method="post" class="form_dg">
		<div class="err_list hidden">
			<div class="err_header"></div>
			<div class="err_content"></div>
		</div>
		<div class="dg">
			<table>
				<colgroup>
					<col width="1%"/>
					{{assign var="table_columns_display_mode" value="sizes"}}
					{{include file="table_columns_inc.tpl"}}
					<col width="1%"/>
				</colgroup>
				<tr class="dg_header">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.status_id==0}}disabled{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0 || $item.status_id==4 || $item.username==$options.DEFAULT_USER_IN_ADMIN_ADD_VIDEO || $item.username==$options.DEFAULT_USER_IN_ADMIN_ADD_ALBUM || $item.username==$options.DEFAULT_USER_IN_ADMIN_ADD_POST}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if ($can_invoke_additional==1 || ($can_unban==1 && $item.login_protection_is_banned==1) || ($can_confirm==1 && $item.status_id==1) || !in_array($item.status_id,array(0,1))) && $item.status_id!=4}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.username}}</span>
									{{if $can_unban==0 || $item.login_protection_is_banned==0}}
										<span class="js_param">unban_hide=true</span>
									{{/if}}
									{{if $can_confirm==0 || $item.status_id!=1}}
										<span class="js_param">confirm_hide=true</span>
									{{/if}}
									{{if $item.username==$options.DEFAULT_USER_IN_ADMIN_ADD_VIDEO || $item.username==$options.DEFAULT_USER_IN_ADMIN_ADD_ALBUM || $item.username==$options.DEFAULT_USER_IN_ADMIN_ADD_POST}}
										<span class="js_param">delete_disable=true</span>
										<span class="js_param">delete_with_content_disable=true</span>
										<span class="js_param">deactivate_disable=true</span>
									{{/if}}
									{{if $item.status_id==0 || $item.status_id==1}}
										<span class="js_param">deactivate_hide=true</span>
										<span class="js_param">login_hide=true</span>
									{{/if}}
									{{if $item.status_id==1 || $item.status_id==2 || $item.status_id==3 || $item.status_id==6}}
										<span class="js_param">activate_hide=true</span>
									{{/if}}
									{{if $item.status_id==3}}
										<span class="js_param">add_transaction_hide=true</span>
									{{/if}}
								</span>
							</a>
						{{/if}}
					</td>
				</tr>
				{{/foreach}}
			</table>
			{{if $can_invoke_additional==1 || $can_unban==1 || $can_confirm==1 || !in_array($item.status_id,array(0,1))}}
				<ul class="dg_additional_menu_template">
					{{if $can_confirm==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=confirm&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.users.user_action_confirm}}</span>
							<span class="js_param">confirm={{$lang.users.user_action_confirm_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">hide=${confirm_hide}</span>
						</li>
					{{/if}}
					{{if $can_activate==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=activate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_activate}}</span>
							<span class="js_param">hide=${activate_hide}</span>
							<span class="js_param">disable=${activate_disable}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=deactivate&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_deactivate}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_deactivate_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">hide=${deactivate_hide}</span>
							<span class="js_param">disable=${deactivate_disable}</span>
						</li>
					{{/if}}
					{{if $can_delete==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">disable=${delete_disable}</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete_with_content&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.users.user_action_delete_with_content}}</span>
							<span class="js_param">confirm={{$lang.users.user_action_delete_with_content_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">disable=${delete_with_content_disable}</span>
							<span class="js_param">prompt_value=yes</span>
						</li>
					{{/if}}
					{{if $can_send_message==1}}
						<li class="js_params">
							<span class="js_param">href=?action=send_message&amp;item_id=${id}</span>
							<span class="js_param">title={{$lang.users.user_action_send_message}}</span>
						</li>
					{{/if}}
					{{if $can_add_transaction==1}}
						<li class="js_params">
							<span class="js_param">href=?action=add_transaction&amp;item_id=${id}</span>
							<span class="js_param">title={{$lang.users.user_action_add_transaction}}</span>
							<span class="js_param">hide=${add_transaction_hide}</span>
						</li>
					{{/if}}
					{{if $can_view_stats==1}}
						<li class="js_params">
							<span class="js_param">href=stats_users_logins.php?no_filter=true&amp;se_user=${name}&amp;se_group_by=log</span>
							<span class="js_param">title={{$lang.users.user_action_view_logins}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=stats_users_content.php?no_filter=true&amp;se_user=${name}&amp;se_group_by=log</span>
							<span class="js_param">title={{$lang.users.user_action_view_content_visits}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
						<li class="js_params">
							<span class="js_param">href=stats_users_awards.php?no_filter=true&amp;se_user=${name}&amp;se_group_by=log</span>
							<span class="js_param">title={{$lang.users.user_action_view_awards}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					{{if $can_view_logs==1}}
						<li class="js_params">
							<span class="js_param">href=log_audit.php?no_filter=true&amp;se_user=${name}</span>
							<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
					{{if $can_unban==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=unban&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.users.user_action_remove_ban}}</span>
							<span class="js_param">confirm={{$lang.users.user_action_remove_ban_confirm|replace:"%1%":'${name}'}}</span>
							<span class="js_param">hide=${unban_hide}</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=?action=login&amp;user_id=${id}</span>
						<span class="js_param">title={{$lang.users.user_action_login_as_user}}</span>
						<span class="js_param">plain_link=true</span>
						<span class="js_param">hide=${login_hide}</span>
					</li>
				</ul>
			{{/if}}
		</div>
		<div class="dgb">
			<table>
				<tr>
					{{if $can_invoke_batch==1}}
						<td class="dgb_label">{{$lang.common.dg_batch_actions}}:</td>
						<td class="dgb_control">
							<select name="batch_action">
								<option value="0">{{$lang.common.dg_batch_actions_select}}</option>
								{{if $can_delete==1}}
									<option value="delete">{{$lang.common.dg_batch_actions_delete}}</option>
									<option value="delete_with_content">{{$lang.users.user_batch_delete_with_content}}</option>
								{{/if}}
								{{if $can_confirm==1}}
									<option value="confirm">{{$lang.users.user_batch_confirm}}</option>
								{{/if}}
								{{if $can_activate==1}}
									<option value="activate">{{$lang.common.dg_batch_actions_activate}}</option>
									<option value="deactivate">{{$lang.common.dg_batch_actions_deactivate}}</option>
								{{/if}}
								{{if $can_unban==1}}
									<option value="unban">{{$lang.users.user_batch_remove_ban}}</option>
								{{/if}}
							</select>
						</td>
						<td class="dgb_control">
							<input type="submit" value="{{$lang.common.dg_batch_actions_btn_execute}}" disabled="disabled"/>
						</td>
					{{/if}}
					<td class="dgb_list_stats">{{$lang.common.dg_list_stats|replace:"%1%":$total_num}}</td>
				</tr>
			</table>
			<ul class="dgb_actions_configuration">
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_with_content</span>
					<span class="js_param">confirm={{$lang.users.user_batch_delete_with_content_confirm|replace:"%1%":'${count}'}}</span>
					<span class="js_param">prompt_value=yes</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=deactivate</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_deactivate_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=unban</span>
					<span class="js_param">confirm={{$lang.users.user_batch_remove_ban_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=confirm</span>
					<span class="js_param">confirm={{$lang.users.user_batch_confirm_confirm|replace:"%1%":'${count}'}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}