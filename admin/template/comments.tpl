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

{{if in_array('users|manage_comments',$smarty.session.permissions)}}
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
			<input type="hidden" name="object_id" value="{{$smarty.get.object_id}}"/>
			<input type="hidden" name="object_type_id" value="{{$smarty.get.object_type_id}}"/>
		{{else}}
			<input type="hidden" name="action" value="change_complete"/>
			<input type="hidden" name="item_id" value="{{$smarty.get.item_id}}"/>
		{{/if}}
	</div>
	<table class="de {{if $can_edit_all==0}}de_readonly{{/if}}">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_comments_list}}</a> / {{if $smarty.get.action=='add_new'}}{{$lang.users.comment_add}}{{else}}{{$lang.users.comment_edit|replace:"%1%":$smarty.post.comment_id}}{{/if}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.comment_field_object}}:</td>
			<td class="de_control">
				{{if $smarty.post.object_type_id==1}}
					{{if in_array('videos|view',$smarty.session.permissions)}}
						<a href="videos.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_video}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_video}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.object_type_id==2}}
					{{if in_array('albums|view',$smarty.session.permissions)}}
						<a href="albums.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_album}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_album}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.object_type_id==3}}
					{{if in_array('content_sources|view',$smarty.session.permissions)}}
						<a href="content_sources.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_content_source}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_content_source}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.object_type_id==4}}
					{{if in_array('models|view',$smarty.session.permissions)}}
						<a href="models.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_model}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_model}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.object_type_id==5}}
					{{if in_array('dvds|view',$smarty.session.permissions)}}
						<a href="dvds.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.object_type_id==12}}
					{{if in_array('posts|view',$smarty.session.permissions)}}
						<a href="posts.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_post}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_post}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.object_type_id==13}}
					{{if in_array('playlists|view',$smarty.session.permissions)}}
						<a href="playlists.php?action=change&amp;item_id={{$smarty.post.object_id}}">{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{if $smarty.post.website_link!=''}}
			<tr>
				<td class="de_label">{{$lang.users.comment_field_website_link}}:</td>
				<td class="de_control">
					<a href="{{$smarty.post.website_link}}" rel="external">{{$smarty.post.website_link}}</a>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">
				{{if $smarty.get.action=='add_new'}}
					<div class="de_required">{{$lang.users.comment_field_user}} (*):</div>
				{{else}}
					{{$lang.users.comment_field_user}}:
				{{/if}}
			</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new'}}
					<div class="de_vis_sw_radio">
						<table class="control_group">
							<tr>
								<td>
									<div class="de_lv_pair"><input id="user_type_registered" type="radio" name="user_type" value="1" checked="checked"/><label>{{$lang.users.comment_field_user_registered}}:</label></div>
									<div class="insight">
										<div class="js_params">
											<span class="js_param">url=async/insight_users.php</span>
										</div>
										<input type="text" name="user" maxlength="255" class="fixed_200 user_type_registered"/>
									</div>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.users.comment_field_user_hint1}}</span>
									{{/if}}
								</td>
							</tr>
							<tr>
								<td>
									<div class="de_lv_pair"><input id="user_type_anonymous" type="radio" name="user_type" value="2"/><label>{{$lang.users.comment_field_user_anonymous}}:</label></div>
									<input type="text" name="anonymous_username" maxlength="255" class="fixed_200 user_type_anonymous"/>
									{{if $smarty.session.userdata.is_expert_mode==0}}
										<br/><span class="de_hint">{{$lang.users.comment_field_user_hint2}}</span>
									{{/if}}
								</td>
							</tr>
						</table>
					</div>
				{{else}}
					{{if $smarty.post.user_status_id==4}}
						{{$smarty.post.user}}
					{{else}}
						{{if in_array('users|view',$smarty.session.permissions)}}
							<a href="users.php?action=change&amp;item_id={{$smarty.post.user_id}}">{{$smarty.post.user}}</a>
						{{else}}
							{{$smarty.post.user}}
						{{/if}}
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{if $smarty.get.action!='add_new' && $config.safe_mode!='true'}}
			<tr>
				<td class="de_label">{{$lang.users.comment_field_ip}}:</td>
				<td class="de_control">
					{{$smarty.post.ip}} {{if $smarty.post.country!=''}}({{$smarty.post.country}}){{/if}}
				</td>
			</tr>
		{{/if}}
		{{if $smarty.get.action!='add_new'}}
			<tr>
				<td class="de_label">{{$lang.users.comment_field_rating}}:</td>
				<td class="de_control">
					{{$lang.users.comment_field_rating_value|replace:"%1%":$smarty.post.rating|replace:"%2%":$smarty.post.likes|replace:"%3%":$smarty.post.dislikes}}
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_label">
				{{if $smarty.get.action=='add_new'}}
					<div class="de_required">{{$lang.users.comment_field_added_date}} (*):</div>
				{{else}}
					{{$lang.users.comment_field_added_date}}:
				{{/if}}
			</td>
			<td class="de_control">
				{{if $smarty.get.action=='add_new'}}
					{{html_select_date prefix='added_date_' start_year='+2' end_year='2006' reverse_years="1" field_order=DMY time=$smarty.post.added_date}}
					<input type="text" name="added_time" maxlength="5" size="4" value="{{$smarty.post.added_date|date_format:"%H:%M"}}"/>
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/>
						<span class="de_hint">
							{{if $smarty.post.object_post_date==''}}
								{{$lang.users.comment_field_added_date_hint1}}
							{{else}}
								{{assign var="object_post_date" value=$smarty.post.object_post_date|date_format:$smarty.session.userdata.full_date_format}}
								{{$lang.users.comment_field_added_date_hint2|replace:"%1%":$object_post_date}}
							{{/if}}
						</span>
					{{/if}}
				{{else}}
					{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label de_required">{{$lang.users.comment_field_comment}} (*):</td>
			<td class="de_control">
				<textarea name="comment" class="dyn_full_size" rows="10" cols="40">{{$smarty.post.comment}}</textarea>
				{{if $smarty.post.comment_id>0 && $smarty.post.comment==''}}
					{{if $smarty.session.userdata.is_expert_mode==0}}
						<br/><span class="de_hint">{{$lang.users.comment_field_comment_deleted}}</span>
					{{/if}}
				{{/if}}
			</td>
		</tr>
		{{if $can_edit_all==1}}
			<tr>
				<td class="de_action_group" colspan="2">
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

{{if in_array('users|manage_comments',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if in_array('users|manage_comments',$smarty.session.permissions)}}
	{{assign var=can_approve value=1}}
{{else}}
	{{assign var=can_approve value=0}}
{{/if}}
{{assign var=can_invoke_additional value=1}}
{{if $can_delete==1 || $can_approve==1}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_status_id>0}}dgf_selected{{/if}}">{{$lang.users.comment_filter_status}}:</td>
					<td class="dgf_control">
						<select name="se_status_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_status_id==1}}selected="selected"{{/if}}>{{$lang.users.comment_filter_status_new}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_status_id==2}}selected="selected"{{/if}}>{{$lang.users.comment_filter_status_approved}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_status_id==3}}selected="selected"{{/if}}>{{$lang.users.comment_filter_status_not_approved}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_user!=''}}dgf_selected{{/if}}">{{$lang.users.comment_field_user}}:</td>
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_object_type_id!=''}}dgf_selected{{/if}}">{{$lang.common.object_type}}:</td>
					<td class="dgf_control">
						<select name="se_object_type_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							<option value="1" {{if $smarty.session.save.$page_name.se_object_type_id==1}}selected="selected"{{/if}}>{{$lang.common.object_type_videos}}</option>
							<option value="2" {{if $smarty.session.save.$page_name.se_object_type_id==2}}selected="selected"{{/if}}>{{$lang.common.object_type_albums}}</option>
							<option value="3" {{if $smarty.session.save.$page_name.se_object_type_id==3}}selected="selected"{{/if}}>{{$lang.common.object_type_content_sources}}</option>
							<option value="4" {{if $smarty.session.save.$page_name.se_object_type_id==4}}selected="selected"{{/if}}>{{$lang.common.object_type_models}}</option>
							<option value="5" {{if $smarty.session.save.$page_name.se_object_type_id==5}}selected="selected"{{/if}}>{{$lang.common.object_type_dvds}}</option>
							<option value="12" {{if $smarty.session.save.$page_name.se_object_type_id==12}}selected="selected"{{/if}}>{{$lang.common.object_type_posts}}</option>
							<option value="13" {{if $smarty.session.save.$page_name.se_object_type_id==13}}selected="selected"{{/if}}>{{$lang.common.object_type_playlists}}</option>
						</select>
					</td>
				</tr>
			</table>
			<table class="dgf_filter">
				<tr>
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_object_id!=''}}dgf_selected{{/if}}">{{$lang.users.comment_filter_object_id}}:</td>
					<td class="dgf_control">
						<input type="text" name="se_object_id" size="10" value="{{$smarty.session.save.$page_name.se_object_id}}"/>
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
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="0" {{if $can_invoke_batch==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="header"}}
					{{include file="table_columns_inc.tpl"}}
					<td>{{$lang.common.dg_actions}}</td>
				</tr>
				{{foreach name=data item=item from=$data|smarty:nodefaults}}
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}} {{if $item.is_approved==0}}disabled{{/if}}">
					<td class="dg_selector">
						<input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_batch==0}}disabled="disabled"{{/if}}/>
						<input type="hidden" name="row_all[]" value="{{$item.$table_key_name}}"/>
					</td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.$table_key_name}}</span>
									<span class="js_param">object_id={{$item.object_id}}</span>
									<span class="js_param">object_type_id={{$item.object_type_id}}</span>
									{{if $item.is_review_needed==0}}
										<span class="js_param">approve_hide=true</span>
									{{/if}}
									{{if $item.website_link==''}}
										<span class="js_param">website_link_disable=true</span>
									{{else}}
										<span class="js_param">website_link={{$item.website_link}}</span>
									{{/if}}
								</span>
							</a>
						{{/if}}
					</td>
				</tr>
				{{/foreach}}
			</table>
			{{if $can_invoke_additional==1}}
				<ul class="dg_additional_menu_template">
					{{if $can_delete==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=delete&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_delete}}</span>
							<span class="js_param">confirm={{$lang.common.dg_actions_delete_confirm|replace:"%1%":'${name}'}}</span>
						</li>
					{{/if}}
					{{if $can_approve==1}}
						<li class="js_params">
							<span class="js_param">href=?batch_action=approve&amp;row_select[]=${id}</span>
							<span class="js_param">title={{$lang.users.comment_action_approve}}</span>
							<span class="js_param">hide=${approve_hide}</span>
						</li>
					{{/if}}
					<li class="js_params">
						<span class="js_param">href=?action=add_new&amp;object_type_id=${object_type_id}&amp;object_id=${object_id}</span>
						<span class="js_param">title={{$lang.common.dg_actions_add_comment}}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					<li class="js_params">
						<span class="js_param">href=${website_link}</span>
						<span class="js_param">title={{$lang.common.dg_actions_website_link}}</span>
						<span class="js_param">disable=${website_link_disable}</span>
						<span class="js_param">plain_link=true</span>
					</li>
					{{if in_array('system|administration',$smarty.session.permissions)}}
						<li class="js_params">
							<span class="js_param">href=log_audit.php?no_filter=true&amp;se_object_type_id=15&amp;se_object_id=${id}</span>
							<span class="js_param">title={{$lang.common.dg_actions_additional_view_audit_log}}</span>
							<span class="js_param">plain_link=true</span>
						</li>
					{{/if}}
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
								{{/if}}
								{{if $can_approve==1}}
									<option value="approve">{{$lang.users.comment_batch_action_approve}}</option>
								{{/if}}
								{{if $can_delete==1 && $can_approve==1}}
									<option value="approve_and_delete">{{$lang.users.comment_batch_action_approve_and_delete}}</option>
									<option value="delete_and_approve">{{$lang.users.comment_batch_action_delete_and_approve}}</option>
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
				{{assign var="displayed_count" value=$data|@count}}
				<li class="js_params">
					<span class="js_param">value=delete</span>
					<span class="js_param">confirm={{$lang.common.dg_batch_actions_delete_confirm|replace:"%1%":'${count}'}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=approve_and_delete</span>
					<span class="js_param">confirm={{$lang.users.comment_batch_action_approve_and_delete_confirm|replace:"%1%":'${count}'|replace:"%2%":$displayed_count}}</span>
				</li>
				<li class="js_params">
					<span class="js_param">value=delete_and_approve</span>
					<span class="js_param">confirm={{$lang.users.comment_batch_action_delete_and_approve_confirm|replace:"%1%":'${count}'|replace:"%2%":$displayed_count}}</span>
				</li>
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}