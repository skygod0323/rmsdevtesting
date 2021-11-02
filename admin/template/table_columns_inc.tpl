{{assign var="max_thumb_size" value=$smarty.session.save.options.maximum_thumb_size|default:"150x150"}}
{{assign var="max_thumb_size" value="x"|explode:$max_thumb_size}}

{{foreach from=$table_fields|smarty:nodefaults item="field"}}
	{{if $table_columns_display_mode=='header'}}
		{{if $field.is_enabled==1}}
			<td>
				{{assign var=sort_field value=$field.id}}
				{{assign var=sort_field_name value=$field.title}}
				{{if $field.is_sortable==1}}
					<a href="{{$page_name}}?sort_by={{$sort_field}}&amp;sort_direction={{if $smarty.session.save.$page_name.sort_by!=$sort_field}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{else}}{{if $smarty.session.save.$page_name.sort_direction=='desc'}}asc{{else}}desc{{/if}}{{/if}}" class="dg_sort{{if $smarty.session.save.$page_name.sort_by==$sort_field}}_{{if $smarty.session.save.$page_name.sort_direction=='desc'}}desc{{else}}asc{{/if}}{{/if}}">{{$sort_field_name|smarty:nodefaults}}</a>
				{{else}}
					{{$sort_field_name|smarty:nodefaults}}
				{{/if}}
			</td>
		{{/if}}
	{{elseif $table_columns_display_mode=='sizes'}}
		{{if $field.is_enabled==1}}
			<col {{if $field.width!=''}}width="{{$field.width}}"{{elseif $field.type=='id' || $field.type=='sorting' || $field.type=='thumb'}}width="5%"{{/if}}/>
		{{/if}}
	{{elseif $table_columns_display_mode=='selector'}}
		{{if $field.type!='id'}}
			<div class="dg_lv_pair"><em class="dg_move_handle"></em><input type="checkbox" name="grid_columns[]" value="{{$field.id}}" {{if $field.is_enabled==1}}checked="checked"{{/if}}/><label>{{$field.title|smarty:nodefaults|replace:"[kt|br]":""}}</label></div>
		{{/if}}
	{{elseif $table_columns_display_mode=='data' || $table_columns_display_mode=='summary'}}
		{{if $field.is_enabled==1}}
			<td {{if $field.type=='id' || $field.type=='user' || $field.type=='admin' || $field.type=='number' || $field.type=='bool' || $field.type=='float' || $field.type=='double' || $field.type=='currency' || $field.type=='datetime' || $field.type=='date' || $field.type=='time' || $field.type=='ip' || $field.type=='traffic' || $field.type=='duration' || $field.is_nowrap==1}}class="nowrap"{{/if}}>
				{{if $table_columns_summary_field_name!='' && $table_columns_summary_field_name==$field.id}}
					{{$item[$field.id]}}
				{{elseif $field.type=='id'}}
					{{if $item.is_editing_forbidden==1}}
						{{$item.$table_key_name}}
					{{else}}
						<a href="{{$page_name}}?action=change&amp;item_id={{$item.$table_key_name}}">{{$item.$table_key_name}}</a>
					{{/if}}
				{{elseif $field.type=='sorting'}}
					<input type="text" name="sorting_{{$item.$table_key_name}}" maxlength="32" value="{{$item.sort_id}}" size="3" {{if $can_edit==0}}disabled="disabled"{{/if}} autocomplete="off"/>
				{{elseif $field.type=='rename'}}
					<input type="text" name="rename_{{$item.$table_key_name}}" maxlength="150" size="15" {{if $can_edit==0}}disabled="disabled"{{/if}} autocomplete="off"/>
				{{else}}
					{{assign var="value" value=$item[$field.id]|smarty:nodefaults}}
					{{assign var="value_is_empty" value="false"}}
					{{assign var="value_is_disabled" value="false"}}
					{{assign var="value_is_highlighted" value="false"}}
					{{assign var="value_is_warning" value="false"}}
					{{assign var="value_postfix" value=""}}

					{{assign var="field_link_href" value=""}}
					{{assign var="field_link_class" value=""}}
					{{assign var="field_link_is_external" value="false"}}

					{{if $field.type=='bool'}}
						{{assign var="bool_append" value=""}}
						{{if is_array($field.append) && $field.append[$value]!=''}}
							{{assign var="bool_append" value=$field.append[$value]}}
							{{assign var="bool_append" value=$item[$bool_append]}}
						{{/if}}
						{{if $value==1}}
							{{assign var="value" value=$lang.common.yes}}
						{{else}}
							{{assign var="value" value=$lang.common.no}}
						{{/if}}
						{{if $bool_append!=''}}
							{{assign var="value" value="`$value` (`$bool_append`)"}}
						{{/if}}
					{{elseif $field.type=='number'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{assign var="value" value=$value|number_format:0:".":""}}
						{{if $value!=0 && $field.format=='percent'}}
							{{assign var="value" value="`$value`%"}}
						{{/if}}
					{{elseif $field.type=='float'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{assign var="value" value=$value|number_format:1:".":""}}
						{{if $value!=0 && $field.format=='percent'}}
							{{assign var="value" value="`$value`%"}}
						{{/if}}
					{{elseif $field.type=='double'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{assign var="value" value=$value|number_format:2:".":""}}
						{{if $value!=0 && $field.format=='percent'}}
							{{assign var="value" value="`$value`%"}}
						{{/if}}
					{{elseif $field.type=='currency'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{assign var="value_is_negative" value="false"}}
						{{if $value<0}}
							{{assign var="value_is_highlighted" value="true"}}
							{{assign var="value_is_negative" value="true"}}
						{{/if}}
						{{if $value_is_empty=='true'}}
							{{assign var="value" value=""}}
						{{else}}
							{{assign var="value" value=$value|abs|number_format:2:".":""}}
							{{assign var="currency_field_id" value="`$field.id`_currency"}}
							{{if $item[$currency_field_id]=='USD'}}
								{{assign var="value" value="\$`$value`"}}
							{{elseif $item[$currency_field_id]=='EUR'}}
								{{assign var="value" value="€`$value`"}}
							{{elseif $item[$currency_field_id]=='GBP'}}
								{{assign var="value" value="£`$value`"}}
							{{/if}}
							{{if $value_is_negative=='true'}}
								{{assign var="value" value="-`$value`"}}
							{{/if}}
						{{/if}}
					{{elseif $field.type=='ip'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{assign var="value" value=$value|int2ip}}
					{{elseif $field.type=='duration'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{assign var="value" value=$value|durationToHumanString}}
					{{elseif $field.type=='traffic'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
						{{/if}}
						{{if $value>999}}
							{{assign var="value" value=$value/1000|number_format:1:".":""}}
							{{assign var="value" value="`$value``$lang.common.traffic_k`"}}
						{{else}}
							{{assign var="value" value=$value|number_format:0:".":""}}
						{{/if}}
					{{elseif $field.type=='time'}}
						{{if $value==0}}
							{{assign var="value_is_empty" value="true"}}
							{{assign var="value" value=""}}
						{{else}}
							{{if $value<60}}
								{{assign var="value" value="`$value``$lang.common.second_truncated`"}}
							{{elseif $value<3600}}
								{{assign var="value_minutes" value=$value/60|intval}}
								{{assign var="value_seconds" value=$value-$value_minutes*60}}
								{{if $value_seconds==0}}
									{{assign var="value" value="`$value_minutes``$lang.common.minute_truncated`"}}
								{{else}}
									{{assign var="value" value="`$value_minutes``$lang.common.minute_truncated` `$value_seconds``$lang.common.second_truncated`"}}
								{{/if}}
							{{else}}
								{{assign var="value_hours" value=$value/3600|intval}}
								{{assign var="value_minutes" value=$value-$value_hours*3600}}
								{{assign var="value_minutes" value=$value_minutes/60|intval}}
								{{assign var="value_seconds" value=$value-$value_hours*3600-$value_minutes*60}}
								{{if $value_seconds==0 && $value_minutes==0}}
									{{assign var="value" value="`$value_hours``$lang.common.hour_truncated`"}}
								{{elseif $value_seconds==0}}
									{{assign var="value" value="`$value_hours``$lang.common.hour_truncated` `$value_minutes``$lang.common.minute_truncated`"}}
								{{else}}
									{{assign var="value" value="`$value_hours``$lang.common.hour_truncated` `$value_minutes``$lang.common.minute_truncated` `$value_seconds``$lang.common.second_truncated`"}}
								{{/if}}
							{{/if}}
						{{/if}}
					{{elseif $field.type=='datetime'}}
						{{if $value=='0000-00-00 00:00:00'}}
							{{assign var="value_is_empty" value="true"}}
							{{if $field.min_date_label!=''}}
								{{assign var="value" value=$field.min_date_label}}
							{{else}}
								{{assign var="value" value=""}}
							{{/if}}
						{{elseif $value=='2070-01-01 00:00:00'}}
							{{if $field.max_date_label!=''}}
								{{assign var="value" value=$field.max_date_label}}
							{{else}}
								{{assign var="value" value="2070-01-01 00:00:00"}}
							{{/if}}
						{{else}}
							{{assign var="relative_date_key" value="relative_`$field.id`"}}
							{{assign var="relative_date" value=$item[$relative_date_key]|smarty:nodefaults}}
							{{if $config.relative_post_dates=='true' && $relative_date!=0}}
								{{assign var="value" value=$lang.common.relative_date_value|replace:"%1%":$relative_date}}
							{{else}}
								{{assign var="value" value=$value|date_format:$smarty.session.userdata.full_date_format}}
							{{/if}}
						{{/if}}
					{{elseif $field.type=='date'}}
						{{if $value=='0000-00-00'}}
							{{assign var="value_is_empty" value="true"}}
							{{if $field.min_date_label!=''}}
								{{assign var="value" value=$field.min_date_label}}
							{{else}}
								{{assign var="value" value=""}}
							{{/if}}
						{{elseif $value=='2070-01-01'}}
							{{if $field.max_date_label!=''}}
								{{assign var="value" value=$field.max_date_label}}
							{{else}}
								{{assign var="value" value="2070-01-01"}}
							{{/if}}
						{{else}}
							{{assign var="value" value=$value|date_format:$smarty.session.userdata.short_date_format}}
						{{/if}}
					{{elseif $field.type=='date_range'}}
						{{assign var="date_range_from" value="`$field.id`_from"}}
						{{assign var="date_range_from" value=$item[$date_range_from]}}
						{{assign var="date_range_to" value="`$field.id`_to"}}
						{{assign var="date_range_to" value=$item[$date_range_to]}}
						{{if $date_range_from!='0000-00-00' || $date_range_to!='0000-00-00'}}
							{{if $date_range_from=='0000-00-00'}}
								{{if $field.min_date_label!=''}}
									{{assign var="date_range_from" value=$field.min_date_label}}
								{{else}}
									{{assign var="date_range_from" value=""}}
								{{/if}}
							{{else}}
								{{assign var="date_range_from" value=$date_range_from|date_format:$smarty.session.userdata.short_date_format}}
							{{/if}}
							{{if $date_range_to=='0000-00-00'}}
								{{if $field.min_date_label!=''}}
									{{assign var="date_range_to" value=$field.min_date_label}}
								{{else}}
									{{assign var="date_range_to" value=""}}
								{{/if}}
							{{else}}
								{{assign var="date_range_to" value=$date_range_to|date_format:$smarty.session.userdata.short_date_format}}
							{{/if}}
							{{assign var="value" value="`$date_range_from` - `$date_range_to`"}}
						{{else}}
							{{assign var="value" value=""}}
						{{/if}}
					{{elseif $field.type=='time_range'}}
						{{assign var="time_range_from" value="`$field.id`_from"}}
						{{assign var="time_range_from" value=$item[$time_range_from]}}
						{{assign var="time_range_to" value="`$field.id`_to"}}
						{{assign var="time_range_to" value=$item[$time_range_to]}}
						{{if $time_range_from!='00:00' || $time_range_to!='00:00'}}
							{{assign var="value" value="`$time_range_from` - `$time_range_to`"}}
						{{else}}
							{{assign var="value" value=""}}
						{{/if}}
					{{elseif $field.type=='choice'}}
						{{assign var="choice_append" value=""}}
						{{if is_array($field.append) && $field.append[$value]!=''}}
							{{assign var="choice_append" value=$field.append[$value]}}
							{{assign var="choice_append" value=$item[$choice_append]}}
						{{/if}}
						{{assign var="value" value=$field.values[$value]|default:$value}}
						{{if $choice_append!=''}}
							{{assign var="value" value="`$value` (`$choice_append`)"}}
						{{/if}}
					{{elseif $field.type=='multi_choice'}}
						{{if $field.value_all!='' && (count($field.values)==count($value) || count($value)==0)}}
							{{assign var="value" value=$field.value_all}}
						{{else}}
							{{assign var="multi_choice_value" value=''}}
							{{if count($value) < count($field.values) / 2}}
								{{foreach from=$value|smarty:nodefaults name="data_choice" item="choice_value"}}
									{{assign var="choice_value" value=$field.values[$choice_value]|default:$choice_value}}
									{{assign var="multi_choice_value" value="`$multi_choice_value`+`$choice_value`"}}
									{{if !$smarty.foreach.data_choice.last}}
										{{assign var="multi_choice_value" value="`$multi_choice_value`, "}}
									{{/if}}
								{{/foreach}}
							{{else}}
								{{foreach from=$field.values|smarty:nodefaults key="key_choice" name="data_choice" item="choice_value"}}
									{{if !in_array($key_choice, $value)}}
										{{assign var="multi_choice_value" value="`$multi_choice_value`-`$choice_value`"}}
										{{if !$smarty.foreach.data_choice.last}}
											{{assign var="multi_choice_value" value="`$multi_choice_value`, "}}
										{{/if}}
									{{/if}}
								{{/foreach}}
								{{assign var="multi_choice_value" value=$multi_choice_value|trim:", "}}
							{{/if}}
							{{assign var="value" value=$multi_choice_value}}
						{{/if}}
					{{elseif $field.type=='longtext'}}
						{{if $field.no_truncate!=1}}
							{{assign var="value" value=$value|smarty:nodefaults|string_truncate:200:" ... ":true:true}}
						{{/if}}
					{{elseif $field.type=='object'}}
						{{assign var="object_field_id" value="`$field.id`_id"}}
						{{assign var="object_field_type" value="`$field.id`_type_id"}}
						{{assign var="link" value=""}}
						{{assign var="permission" value=""}}
						{{if $item[$field.id]}}
							{{assign var="value" value=$item[$field.id]|smarty:nodefaults}}
						{{elseif $item[$object_field_id]>0}}
							{{assign var="value" value=$item[$object_field_id]}}
						{{else}}
							{{assign var="value" value=""}}
						{{/if}}
						{{if $item[$object_field_type]==1}}
							{{assign var="value" value="`$lang.common.object_type_video` \"`$value`\""}}
							{{assign var="link" value="videos.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="videos|view"}}
						{{elseif $item[$object_field_type]==101}}
							{{assign var="value" value="`$lang.common.object_type_video_file` \"`$value`\""}}
							{{assign var="link" value="videos.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="videos|view"}}
						{{elseif $item[$object_field_type]==2}}
							{{assign var="value" value="`$lang.common.object_type_album` \"`$value`\""}}
							{{assign var="link" value="albums.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="albums|view"}}
						{{elseif $item[$object_field_type]==102}}
							{{assign var="value" value="`$lang.common.object_type_album_file` \"`$value`\""}}
							{{assign var="link" value="albums.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="albums|view"}}
						{{elseif $item[$object_field_type]==3}}
							{{assign var="value" value="`$lang.common.object_type_content_source` \"`$value`\""}}
							{{assign var="link" value="content_sources.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="content_sources|view"}}
						{{elseif $item[$object_field_type]==4}}
							{{assign var="value" value="`$lang.common.object_type_model` \"`$value`\""}}
							{{assign var="link" value="models.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="models|view"}}
						{{elseif $item[$object_field_type]==5}}
							{{assign var="value" value="`$lang.common.object_type_dvd` \"`$value`\""}}
							{{assign var="link" value="dvds.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="dvds|view"}}
						{{elseif $item[$object_field_type]==6}}
							{{assign var="value" value="`$lang.common.object_type_category` \"`$value`\""}}
							{{assign var="link" value="categories.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="categories|view"}}
						{{elseif $item[$object_field_type]==7}}
							{{assign var="value" value="`$lang.common.object_type_category_group` \"`$value`\""}}
							{{assign var="link" value="categories_groups.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="category_groups|view"}}
						{{elseif $item[$object_field_type]==8}}
							{{assign var="value" value="`$lang.common.object_type_content_source_group` \"`$value`\""}}
							{{assign var="link" value="content_sources_groups.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="content_sources_groups|view"}}
						{{elseif $item[$object_field_type]==9}}
							{{assign var="value" value="`$lang.common.object_type_tag` \"`$value`\""}}
							{{assign var="link" value="tags.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="tags|view"}}
						{{elseif $item[$object_field_type]==10}}
							{{assign var="value" value="`$lang.common.object_type_dvd_group` \"`$value`\""}}
							{{assign var="link" value="dvds_groups.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="dvds_groups|view"}}
						{{elseif $item[$object_field_type]==11}}
							{{assign var="value" value="`$lang.common.object_type_post_type` \"`$value`\""}}
							{{assign var="link" value="posts_types.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="posts_types|view"}}
						{{elseif $item[$object_field_type]==12}}
							{{assign var="value" value="`$lang.common.object_type_post` \"`$value`\""}}
							{{assign var="link" value="posts.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="posts|view"}}
						{{elseif $item[$object_field_type]==13}}
							{{assign var="value" value="`$lang.common.object_type_playlist` \"`$value`\""}}
							{{assign var="link" value="playlists.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="playlists|view"}}
						{{elseif $item[$object_field_type]==14}}
							{{assign var="value" value="`$lang.common.object_type_model_group` \"`$value`\""}}
							{{assign var="link" value="models_groups.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="models_groups|view"}}
						{{elseif $item[$object_field_type]==15}}
							{{assign var="value" value="`$lang.common.object_type_comment` \"`$value`\""}}
							{{assign var="link" value="comments.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="users|manage_comments"}}
						{{elseif $item[$object_field_type]==20}}
							{{assign var="value" value="`$lang.common.object_type_profile` \"`$value`\""}}
							{{assign var="link" value="users.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="users|view"}}
						{{elseif $item[$object_field_type]==21}}
							{{assign var="value" value="`$lang.common.object_type_message` \"`$value`\""}}
							{{assign var="link" value="messages.php?action=change&item_id=%id%"}}
							{{assign var="permission" value="messages|view"}}
						{{/if}}
						{{if $item[$object_field_id]>0 && !$item[$field.id]}}
							{{assign var="value" value="`$value` `$lang.common.object_flag_deleted`"}}
							{{assign var="value_is_empty" value="true"}}
							{{assign var="value_is_disabled" value="true"}}
						{{elseif $link && ($permission=='' || in_array($permission,$smarty.session.permissions)) && $item[$field.id]}}
							{{assign var="link_value" value=$item[$object_field_id]|urlencode|smarty:nodefaults}}
							{{assign var="field_link_href" value=$link|replace:"%id%":$link_value|smarty:nodefaults}}
						{{/if}}
					{{elseif $field.type=='user'}}
						{{assign var="user_field_id" value="`$field.id`_id"}}
						{{assign var="user_status_field_id" value="`$field.id`_status_id"}}
						{{if $value=='' && $item[$user_field_id]>0}}
							{{assign var="value" value=$lang.common.user_deleted|replace:"%1%":$item[$user_field_id]}}
							{{assign var="value_is_empty" value="true"}}
							{{assign var="value_is_disabled" value="true"}}
						{{elseif in_array('users|view',$smarty.session.permissions) && $item[$user_field_id]>0}}
							{{assign var="link_value" value=$item[$user_field_id]|urlencode|smarty:nodefaults}}
							{{assign var="field_link_href" value="users.php?action=change&item_id=%id%"|replace:"%id%":$link_value|smarty:nodefaults}}
						{{/if}}
						{{if $item[$user_status_field_id]==3}}
							{{assign var="value_postfix" value="[P]"}}
						{{elseif $item[$user_status_field_id]==4}}
							{{assign var="value_postfix" value="[A]"}}
						{{elseif $item[$user_status_field_id]==6}}
							{{assign var="value_postfix" value="[W]"}}
						{{/if}}
					{{elseif $field.type=='admin'}}
						{{assign var="admin_field_id" value="`$field.id`_id"}}
						{{assign var="admin_field_is_superadmin" value="`$field.id`_is_superadmin"}}
						{{if $value=='' && $item[$admin_field_id]>0}}
							{{assign var="value" value=$lang.common.admin_deleted|replace:"%1%":$item[$admin_field_id]}}
							{{assign var="value_is_empty" value="true"}}
							{{assign var="value_is_disabled" value="true"}}
						{{elseif $smarty.session.userdata.is_superadmin==1 && $item[$admin_field_id]>0 && !$item[$admin_field_is_superadmin]}}
							{{assign var="link_value" value=$item[$admin_field_id]|urlencode|smarty:nodefaults}}
							{{assign var="field_link_href" value="admin_users.php?action=change&item_id=%id%"|replace:"%id%":$link_value|smarty:nodefaults}}
						{{/if}}
					{{elseif $field.type=='file' || $field.type=='image'}}
						{{assign var="file_link_key" value="`$field.id`_url"}}
						{{if $value && $item[$file_link_key]}}
							{{assign var="file_ext" value="."|explode:$value}}
							{{assign var="file_ext_length" value=$file_ext|@count}}
							{{if $file_ext_length>0}}
								{{assign var="file_ext_length" value=$file_ext_length-1}}
								{{assign var="file_ext" value=$file_ext[$file_ext_length]}}
							{{/if}}
							{{assign var="image_ext" value=","|explode:$config.image_allowed_ext}}

							{{assign var="field_link_href" value=$item[$file_link_key]|smarty:nodefaults}}
							{{if $field.type=='image' || in_array($file_ext, $image_ext)}}
								{{assign var="field_link_class" value="dg_preview"}}
							{{/if}}
						{{/if}}
					{{elseif $field.type=='url'}}
						{{if $value}}
							{{assign var="field_link_href" value=$value|smarty:nodefaults}}
							{{assign var="field_link_is_external" value="true"}}
							{{assign var="value" value=$value|smarty:nodefaults|string_truncate:150:" ... ":true}}
						{{/if}}
					{{/if}}

					{{if is_array($field.placeholders)}}
						{{foreach from=$field.placeholders|smarty:nodefaults key="placeholder" item="placeholder_value"}}
							{{assign var="value" value=$value|replace:"%`$placeholder`%":$item[$placeholder_value]|smarty:nodefaults}}
						{{/foreach}}
					{{/if}}

					{{if ($value=='' || $value_is_empty=='true') && $field.zero_label!=''}}
						{{assign var="value" value=$field.zero_label}}
					{{/if}}

					{{if $field.ifhighlight && $item[$field.ifhighlight]==1}}
						{{assign var="value_is_highlighted" value="true"}}
					{{/if}}
					{{if $field.ifwarn && $item[$field.ifwarn]==1}}
						{{assign var="value_is_warning" value="true"}}
					{{/if}}
					{{if ($field.ifdisable!='' && $item[$field.ifdisable]==1) || ($field.ifdisable_zero==1 && $value_is_empty=='true')}}
						{{assign var="value_is_disabled" value="true"}}
					{{/if}}

					{{assign var="averaged_field_types" value=","|explode:"number,float,double,currency,duration,traffic,time"}}
					{{if $table_columns_display_mode=='data' && is_array($table_columns_average) && in_array($field.type, $averaged_field_types) && !$item.is_today}}
						{{assign var="diff" value=$item[$field.id]-$table_columns_average[$field.id]}}
						{{if $table_columns_average[$field.id]>0}}
							{{assign var="diff" value=$diff/$table_columns_average[$field.id]*100}}
							{{assign var="diff" value=$diff|number_format:0:".":""}}
							{{if $diff>0}}
								{{assign var="diff" value="+`$diff`"}}
							{{/if}}
						{{else}}
							{{assign var="diff" value="0"}}
						{{/if}}
						{{assign var="value_postfix" value="(`$diff`%)"}}
						{{if $diff<0}}
							{{assign var="value_postfix" value="<span class=\"dg_discrepancy negative\">`$value_postfix`</span>"}}
						{{elseif $diff>0}}
							{{assign var="value_postfix" value="<span class=\"dg_discrepancy positive\">`$value_postfix`</span>"}}
						{{else}}
							{{assign var="value_postfix" value="<span class=\"dg_discrepancy\">`$value_postfix`</span>"}}
						{{/if}}
					{{/if}}

					{{if $value_postfix=='' && $field.value_postfix!='' && $item[$field.value_postfix]!=''}}
						{{assign var="value_postfix" value=$item[$field.value_postfix]|smarty:nodefaults}}
					{{/if}}

					{{if $value!='' && $value_is_empty!='true'}}
						{{if !$field_link_href && $field.link!=''}}
							{{assign var="link" value=$field.link|smarty:nodefaults}}
							{{if $link=='custom'}}
								{{assign var="custom_link_key" value="`$field.id`_link"}}
								{{assign var="link" value=$item[$custom_link_key]|smarty:nodefaults}}
							{{/if}}
							{{assign var="permission" value=$field.permission|smarty:nodefaults}}
							{{if $permission=='custom'}}
								{{assign var="custom_permission_key" value="`$field.id`_permission"}}
								{{assign var="permission" value=$item[$custom_permission_key]|smarty:nodefaults}}
							{{/if}}

							{{if $link!='' && ($permission=='' || in_array($permission,$smarty.session.permissions))}}
								{{if !$field.link_id || $item[$field.link_id]}}
									{{if !$field.link_id}}
										{{assign var="field_link_href" value=$link|smarty:nodefaults}}
									{{else}}
										{{assign var="link_value" value=$item[$field.link_id]|urlencode|smarty:nodefaults}}
										{{assign var="field_link_href" value=$link|replace:"%id%":$link_value|smarty:nodefaults}}
									{{/if}}
								{{/if}}
							{{/if}}
						{{/if}}
					{{/if}}

					{{if $field_link_href && $field.link_is_editor==1 && $item.is_editing_forbidden==1}}
						{{assign var="field_link_href" value=""}}
					{{/if}}

					{{if $field_link_href}}
						{{if is_array($value)}}
							{{foreach from=$value name="list_field" item="list_value"}}
								{{assign var="link_value" value=$list_value.id|urlencode|smarty:nodefaults}}
								<a href="{{$field_link_href|replace:"%id%":$link_value}}">{{$list_value.title}}</a>{{if !$smarty.foreach.list_field.last}},{{/if}}
							{{/foreach}}
						{{else}}
							{{assign var="thumb_size_field_id" value="`$field.id`_size"}}
							{{assign var="thumb_size" value=""}}
							{{if $item.$thumb_size_field_id}}
								{{assign var="thumb_size" value="x"|explode:$item.$thumb_size_field_id}}
							{{/if}}
							<a href="{{$field_link_href}}" class="{{if $field.type!='refid' && $field.type!='user' && $field.type!='admin' && $field.type!='object' && $field.link_is_editor!=1}}no_popup{{/if}} {{if $value_is_highlighted=='true'}}highlighted_text{{elseif $value_is_warning=='true'}}warning_text{{/if}} {{if $value_is_disabled=='true'}}disabled{{/if}} {{$field_link_class}}" {{if $field_link_is_external=='true'}}rel="external"{{/if}}>
								{{if $field.type=='thumb'}}<div class="dg_image">{{if $value}}<img alt="" style="{{if $max_thumb_size.0>0}}max-width: {{$max_thumb_size.0}}px{{/if}}; {{if $max_thumb_size.1>0}}max-height: {{$max_thumb_size.1}}px{{/if}}" src="{{$value}}{{if $value|strpos:"?"}}&{{else}}?{{/if}}rnd={{$smarty.now}}" {{if $thumb_size.0>0}}width="{{$thumb_size.0}}"{{/if}} {{if $thumb_size.1>0}}height="{{$thumb_size.1}}"{{/if}}/>{{else}}<em>{{$lang.common.undefined}}</em>{{/if}}</div>{{else}}{{$value}}{{/if}}</a>
						{{/if}}
						{{$value_postfix|smarty:nodefaults}}
					{{else}}
						{{if is_array($value)}}
							{{foreach from=$value name="list_field" item="list_value"}}
								{{$list_value.title}}{{if !$smarty.foreach.list_field.last}},{{/if}}
							{{/foreach}}
						{{elseif $field.type=='thumb'}}
							<div class="dg_image">
								{{if $value}}
									{{assign var="thumb_size_field_id" value="`$field.id`_size"}}
									{{assign var="thumb_size" value=""}}
									{{if $item.$thumb_size_field_id}}
										{{assign var="thumb_size" value="x"|explode:$item.$thumb_size_field_id}}
									{{/if}}
									<img alt="" style="{{if $max_thumb_size.0>0}}max-width: {{$max_thumb_size.0}}px{{/if}}; {{if $max_thumb_size.1>0}}max-height: {{$max_thumb_size.1}}px{{/if}}" src="{{$value}}{{if $value|strpos:"?"}}&{{else}}?{{/if}}rnd={{$smarty.now}}" {{if $thumb_size.0>0}}width="{{$thumb_size.0}}"{{/if}} {{if $thumb_size.1>0}}height="{{$thumb_size.1}}"{{/if}}/>
								{{else}}
									<em>{{$lang.common.undefined}}</em>
								{{/if}}
							</div>
						{{else}}
							{{if $field.ucfirst==1}}
								{{assign var="value" value=$value|ucfirst}}
							{{/if}}
							{{if $value_is_highlighted=='true'}}
								<span class="highlighted_text">
									{{$value}}
									{{$value_postfix|smarty:nodefaults}}
								</span>
							{{elseif $value_is_warning=='true'}}
								<span class="warning_text">
									{{$value}}
									{{$value_postfix|smarty:nodefaults}}
								</span>
							{{elseif $value_is_disabled=='true'}}
								<span class="disabled">
									{{$value}}
									{{$value_postfix|smarty:nodefaults}}
								</span>
							{{else}}
								{{$value}}
								{{$value_postfix|smarty:nodefaults}}
							{{/if}}
						{{/if}}
					{{/if}}
				{{/if}}
			</td>
		{{/if}}
	{{/if}}
{{/foreach}}