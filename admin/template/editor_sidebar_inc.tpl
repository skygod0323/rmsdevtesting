{{assign var="max_thumb_size" value=$smarty.session.save.options.maximum_thumb_size|default:"150x150"}}
{{assign var="max_thumb_size" value="x"|explode:$max_thumb_size}}

<td class="de_sidebar" rowspan="{{$sidebar_rowspan}}">
	<div class="de_image">
		{{if $sidebar_image_url!=''}}
			<img src="{{$sidebar_image_url}}?rnd={{$smarty.now}}" alt="{{$smarty.post.title}}"/>
		{{else}}
			<span class="no-image">{{$lang.common.no_image}}</span>
		{{/if}}
	</div>
	{{foreach from=$sidebar_fields|smarty:nodefaults item="field"}}
		<div class="de_info_row">
			<label>{{$field.title}}:</label>
			<em>
				{{assign var="field_id" value=$field.id}}

				{{assign var="value" value=$smarty.post.$field_id|smarty:nodefaults}}
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
					{{assign var="value" value=$value|number_format:0:".":" "}}
					{{if $value!=0 && $field.format=='percent'}}
						{{assign var="value" value="`$value`%"}}
					{{/if}}
				{{elseif $field.type=='float'}}
					{{if $value==0}}
						{{assign var="value_is_empty" value="true"}}
					{{/if}}
					{{assign var="value" value=$value|number_format:1:".":" "}}
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
							{{if $smarty.post.$currency_field_id=='USD'}}
								{{assign var="value" value="\$`$value`"}}
							{{elseif $smarty.post.$currency_field_id=='EUR'}}
								{{assign var="value" value="€`$value`"}}
							{{elseif $smarty.post.$currency_field_id=='GBP'}}
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
						{{assign var="relative_date_key" value="relative_`$field_id`"}}
						{{assign var="relative_date" value=$smarty.post.$relative_date_key|smarty:nodefaults}}
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
					{{assign var="date_range_from" value="`$field_id`_from"}}
					{{assign var="date_range_from" value=$smarty.post.$date_range_from}}
					{{assign var="date_range_to" value="`$field_id`_to"}}
					{{assign var="date_range_to" value=$smarty.post.$date_range_to}}
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
					{{assign var="time_range_from" value="`$field_id`_from"}}
					{{assign var="time_range_from" value=$smarty.post.$time_range_from}}
					{{assign var="time_range_to" value="`$field_id`_to"}}
					{{assign var="time_range_to" value=$smarty.post.$time_range_to}}
					{{if $time_range_from!='00:00' && $time_range_to!='00:00'}}
						{{assign var="value" value="`$time_range_from` - `$time_range_to`"}}
					{{else}}
						{{assign var="value" value=""}}
					{{/if}}
				{{elseif $field.type=='choice'}}
					{{assign var="choice_append" value=""}}
					{{if is_array($field.append) && $field.append[$value]!=''}}
						{{assign var="choice_append" value=$field.append[$value]}}
						{{assign var="choice_append" value=$smarty.post.$choice_append}}
					{{/if}}
					{{assign var="value" value=$field.values[$value]|default:''}}
					{{if $choice_append!=''}}
						{{assign var="value" value="`$value` (`$choice_append`)"}}
					{{/if}}
				{{elseif $field.type=='multi_choice'}}
					{{if $field.value_all!='' && (count($field.values)==count($value) || count($value)==0)}}
						{{assign var="value" value=$field.value_all}}
					{{else}}
						{{assign var="multi_choice_value" value=''}}
						{{foreach from=$value|smarty:nodefaults name="data_choice" item="choice_value"}}
							{{assign var="choice_value" value=$field.values[$choice_value]|default:$choice_value}}
							{{assign var="multi_choice_value" value="`$multi_choice_value``$choice_value`"}}
							{{if !$smarty.foreach.data_choice.last}}
								{{assign var="multi_choice_value" value="`$multi_choice_value`, "}}
							{{/if}}
						{{/foreach}}
						{{assign var="value" value=$multi_choice_value}}
					{{/if}}
				{{elseif $field.type=='longtext'}}
					{{assign var="value" value=$value|smarty:nodefaults|string_truncate:100:"...":true}}
				{{elseif $field.type=='user'}}
					{{assign var="user_field_id" value="`$field_id`_id"}}
					{{assign var="user_status_field_id" value="`$field_id`_status_id"}}
					{{if $value=='' && $smarty.post.$user_field_id>0}}
						{{assign var="value" value=$lang.common.user_deleted|replace:"%1%":$smarty.post.user_field_id}}
						{{assign var="value_is_empty" value="true"}}
						{{assign var="value_is_disabled" value="true"}}
					{{elseif in_array('users|view',$smarty.session.permissions) && $smarty.post.$user_field_id>0}}
						{{assign var="link_value" value=$smarty.post.$user_field_id|urlencode|smarty:nodefaults}}
						{{assign var="field_link_href" value="users.php?action=change&item_id=%id%"|replace:"%id%":$link_value|smarty:nodefaults}}
					{{/if}}
					{{if $smarty.post.$user_status_field_id==3}}
						{{assign var="value_postfix" value="[P]"}}
					{{elseif $smarty.post.$user_status_field_id==4}}
						{{assign var="value_postfix" value="[A]"}}
					{{elseif $smarty.post.$user_status_field_id==6}}
						{{assign var="value_postfix" value="[W]"}}
					{{/if}}
				{{elseif $field.type=='admin'}}
					{{assign var="admin_field_id" value="`$field_id`_id"}}
					{{assign var="admin_field_is_superadmin" value="`$field_id`_is_superadmin"}}
					{{if $value=='' && $smarty.post.$admin_field_id>0}}
						{{assign var="value" value=$lang.common.admin_deleted|replace:"%1%":$smarty.post.$admin_field_id}}
						{{assign var="value_is_empty" value="true"}}
						{{assign var="value_is_disabled" value="true"}}
					{{elseif $smarty.session.userdata.is_superadmin==1 && $smarty.post.$admin_field_id>0 && !$smarty.post.$admin_field_is_superadmin}}
						{{assign var="link_value" value=$smarty.post.$admin_field_id|urlencode|smarty:nodefaults}}
						{{assign var="field_link_href" value="admin_users.php?action=change&item_id=%id%"|replace:"%id%":$link_value|smarty:nodefaults}}
					{{/if}}
				{{elseif $field.type=='file' || $field.type=='image'}}
					{{assign var="file_link_key" value="`$field_id`_url"}}
					{{if $value && $smarty.post.$file_link_key}}
						{{assign var="field_link_href" value=$smarty.post.$file_link_key|smarty:nodefaults}}
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
						{{assign var="value" value=$value|replace:"%`$placeholder`%":$smarty.post.$placeholder_value|smarty:nodefaults}}
					{{/foreach}}
				{{/if}}

				{{if ($value=='' || $value_is_empty=='true') && $field.zero_label!=''}}
					{{assign var="value" value=$field.zero_label}}
				{{/if}}

				{{assign var="field_ifhighlight" value=$field.ifhighlight}}
				{{if $field_ifhighlight && $smarty.post.$field_ifhighlight==1}}
					{{assign var="value_is_highlighted" value="true"}}
				{{/if}}

				{{assign var="field_ifwarn" value=$field.ifwarn}}
				{{if $field_ifwarn && $smarty.post.$field_ifwarn==1}}
					{{assign var="value_is_warning" value="true"}}
				{{/if}}

				{{assign var="field_ifdisable" value=$field.ifdisable}}
				{{if ($field_ifdisable!='' && $smarty.post.$field_ifdisable==1) || ($field.ifdisable_zero==1 && $value_is_empty=='true')}}
					{{assign var="value_is_disabled" value="true"}}
				{{/if}}

				{{if $value!='' && $value_is_empty!='true'}}
					{{if !$field_link_href && $field.link!=''}}
						{{assign var="link" value=$field.link|smarty:nodefaults}}
						{{if $link=='custom'}}
							{{assign var="custom_link_key" value="`$field.id`_link"}}
							{{assign var="link" value=$smarty.post.$custom_link_key|smarty:nodefaults}}
						{{/if}}
						{{assign var="permission" value=$field.permission|smarty:nodefaults}}
						{{if $permission=='custom'}}
							{{assign var="custom_permission_key" value="`$field.id`_permission"}}
							{{assign var="permission" value=$smarty.post.$custom_permission_key|smarty:nodefaults}}
						{{/if}}

						{{if $link!='' && ($permission=='' || in_array($permission,$smarty.session.permissions))}}
							{{assign var="field_link_id" value=$field.link_id}}
							{{if !$field_link_id || $smarty.post.$field_link_id}}
								{{if !$field_link_id}}
									{{assign var="field_link_href" value=$link|smarty:nodefaults}}
								{{else}}
									{{assign var="link_value" value=$smarty.post.$field_link_id|urlencode|smarty:nodefaults}}
									{{assign var="field_link_href" value=$link|replace:"%id%":$link_value|smarty:nodefaults}}
								{{/if}}
							{{/if}}
						{{/if}}
					{{/if}}
				{{/if}}

				{{if $field_link_href}}
					{{if is_array($value)}}
						{{foreach from=$value name="list_field" item="list_value"}}
							{{assign var="link_value" value=$list_value.id|urlencode|smarty:nodefaults}}
							<a tabindex="-1" href="{{$field_link_href|replace:"%id%":$link_value}}">{{$list_value.title}}</a>{{if !$smarty.foreach.list_field.last}},{{/if}}
						{{/foreach}}
					{{else}}
						<a tabindex="-1" href="{{$field_link_href}}" class="{{if $field.type!='refid' && $field.type!='user' && $field.type!='admin' && $field.type!='object' && $field.link_is_editor!=1}}no_popup{{/if}} {{if $value_is_highlighted=='true'}}highlighted_text{{/if}} {{if $value_is_disabled=='true'}}disabled{{/if}} {{$field_link_class}}" {{if $field_link_is_external=='true'}}rel="external"{{/if}}>
							{{if $field.type=='thumb'}}<div class="dg_image">{{if $value}}<img alt="" style="{{if $max_thumb_size.0>0}}max-width: {{$max_thumb_size.0}}px{{/if}}; {{if $max_thumb_size.1>0}}max-height: {{$max_thumb_size.1}}px{{/if}}" src="{{$value}}{{if $value|strpos:"?"}}&{{else}}?{{/if}}rnd={{$smarty.now}}"/>{{else}}<em>{{$lang.common.undefined}}</em>{{/if}}</div>{{else}}{{$value}}{{/if}}</a>
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
								<img alt="" style="{{if $max_thumb_size.0>0}}max-width: {{$max_thumb_size.0}}px{{/if}}; {{if $max_thumb_size.1>0}}max-height: {{$max_thumb_size.1}}px{{/if}}" src="{{$value}}{{if $value|strpos:"?"}}&{{else}}?{{/if}}rnd={{$smarty.now}}"/>
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
			</em>
		</div>
	{{/foreach}}
</td>