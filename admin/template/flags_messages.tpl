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

{{if $smarty.get.action=='change'}}

<form action="{{$page_name}}" method="post">
	<div class="err_list hidden">
		<div class="err_header"></div>
		<div class="err_content"></div>
	</div>
	<table class="de">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div><a href="{{$page_name}}">{{$lang.users.submenu_option_flags_messages}}</a> / {{$lang.users.flag_message_edit|replace:"%1%":$smarty.post.flag_message_id}}</div></td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_flag}}:</td>
			<td class="de_control">
				{{$smarty.post.flag}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_object}}:</td>
			<td class="de_control">
				{{if $smarty.post.video_id>0}}
					{{if in_array('videos|view',$smarty.session.permissions)}}
						<a href="videos.php?action=change&amp;item_id={{$smarty.post.video_id}}">{{$lang.common.object_type_video}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_video}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.album_id>0}}
					{{if in_array('albums|view',$smarty.session.permissions)}}
						<a href="albums.php?action=change&amp;item_id={{$smarty.post.album_id}}">{{$lang.common.object_type_album}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_album}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.dvd_id>0}}
					{{if in_array('dvds|view',$smarty.session.permissions)}}
						<a href="dvds.php?action=change&amp;item_id={{$smarty.post.dvd_id}}">{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_dvd}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.post_id>0}}
					{{if in_array('posts|view',$smarty.session.permissions)}}
						<a href="posts.php?action=change&amp;item_id={{$smarty.post.post_id}}">{{$lang.common.object_type_post}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_post}} "{{$smarty.post.object}}"
					{{/if}}
				{{elseif $smarty.post.playlist_id>0}}
					{{if in_array('playlists|view',$smarty.session.permissions)}}
						<a href="playlists.php?action=change&amp;item_id={{$smarty.post.playlist_id}}">{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"</a>
					{{else}}
						{{$lang.common.object_type_playlist}} "{{$smarty.post.object}}"
					{{/if}}
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_ip}}:</td>
			<td class="de_control">
				{{$smarty.post.ip}}{{if $smarty.post.country!=''}} ({{$smarty.post.country}}){{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_user_agent}}:</td>
			<td class="de_control">
				{{$smarty.post.user_agent}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_referer}}:</td>
			<td class="de_control">
				{{if $smarty.post.referer!=''}}
					<a href="{{$smarty.post.referer}}" rel="external">{{$smarty.post.referer}}</a>
				{{/if}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_added_date}}:</td>
			<td class="de_control">
				{{$smarty.post.added_date|date_format:$smarty.session.userdata.full_date_format}}
			</td>
		</tr>
		<tr>
			<td class="de_label">{{$lang.users.flag_message_field_message}}:</td>
			<td class="de_control">
				{{$smarty.post.message}}
			</td>
		</tr>
	</table>
</form>

{{else}}

{{if in_array('feedbacks|delete',$smarty.session.permissions)}}
	{{assign var=can_delete value=1}}
{{else}}
	{{assign var=can_delete value=0}}
{{/if}}
{{if $can_delete==1}}
	{{assign var=can_invoke_additional value=1}}
{{else}}
	{{assign var=can_invoke_additional value=0}}
{{/if}}
{{if $can_delete==1}}
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
					<td class="dgf_label {{if $smarty.session.save.$page_name.se_flag_id!=''}}dgf_selected{{/if}}">{{$lang.users.flag_message_field_flag}}:</td>
					<td class="dgf_control">
						<select name="se_flag_id">
							<option value="">{{$lang.common.dg_filter_option_all}}</option>
							{{foreach from=$list_flags_grouped|smarty:nodefaults item="list_flags" key="group_id"}}
								{{if $group_id==1}}
									{{assign var="group_title" value=$lang.common.object_type_videos}}
								{{elseif $group_id==2}}
									{{assign var="group_title" value=$lang.common.object_type_albums}}
								{{elseif $group_id==3}}
									{{assign var="group_title" value=$lang.common.object_type_dvds}}
								{{elseif $group_id==4}}
									{{assign var="group_title" value=$lang.common.object_type_posts}}
								{{elseif $group_id==5}}
									{{assign var="group_title" value=$lang.common.object_type_playlists}}
								{{/if}}
								<optgroup label="{{$group_title}}">
									{{foreach from=$list_flags|smarty:nodefaults item="flag"}}
										<option value="{{$flag.flag_id}}" {{if $smarty.session.save.$page_name.se_flag_id==$flag.flag_id}}selected="selected"{{/if}}>{{$flag.title}}</option>
									{{/foreach}}
								</optgroup>
							{{/foreach}}
						</select>
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
							<option value="5" {{if $smarty.session.save.$page_name.se_object_type_id==5}}selected="selected"{{/if}}>{{$lang.common.object_type_dvds}}</option>
							<option value="12" {{if $smarty.session.save.$page_name.se_object_type_id==12}}selected="selected"{{/if}}>{{$lang.common.object_type_posts}}</option>
							<option value="13" {{if $smarty.session.save.$page_name.se_object_type_id==13}}selected="selected"{{/if}}>{{$lang.common.object_type_playlists}}</option>
						</select>
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
				<tr class="dg_data{{if $smarty.foreach.data.iteration % 2==0}} dg_even{{/if}}">
					<td class="dg_selector"><input type="checkbox" name="row_select[]" value="{{$item.$table_key_name}}" {{if $can_invoke_additional==0}}disabled="disabled"{{/if}}/></td>
					{{assign var="table_columns_display_mode" value="data"}}
					{{include file="table_columns_inc.tpl"}}
					<td>
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}" class="edit" title="{{$lang.common.dg_actions_edit}}"></a>
						{{if $can_invoke_additional==1}}
							<a href="javascript:stub()" class="additional" title="{{$lang.common.dg_actions_additional}}">
								<span class="js_params">
									<span class="js_param">id={{$item.$table_key_name}}</span>
									<span class="js_param">name={{$item.$table_key_name}}</span>
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
			</ul>
		</div>
	</form>
</div>
{{include file="navigation.tpl"}}

{{/if}}