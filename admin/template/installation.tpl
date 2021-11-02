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
	<table class="de de_readonly">
		<colgroup>
			<col width="5%"/>
			<col width="95%"/>
		</colgroup>
		<tr>
			<td class="de_header" colspan="2"><div>{{$lang.settings.installation_header}}</div></td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_system}}</div></td>
		</tr>
		<tr>
			<td class="de_label">PHP WWW:</td>
			<td class="de_control">
				<a href="installation.php?action=get_info" rel="external">PHP {{$phpversion}}</a>
			</td>
		</tr>
		{{foreach item=item from=$system|smarty:nodefaults}}
			<tr>
				<td class="de_label">{{$item.name}}:</td>
				<td class="de_control">
					{{if $item.type=='multiline'}}
						<textarea class="dyn_full_size" cols="20" rows="3">{{$item.value}}</textarea>
					{{else}}
						<input type="text" class="dyn_full_size" value="{{$item.value}}"/>
					{{/if}}
				</td>
			</tr>
		{{/foreach}}
		{{if count($memcache_stats)}}
			<tr>
				<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_memcache}}</div></td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.installation_memcache_utilization}}:</td>
				<td class="de_control">
					<input type="text" class="dyn_full_size" value="{{$memcache_stats.memcache_used_memory}} / {{$memcache_stats.memcache_total_memory}} ({{$memcache_stats.memcache_usage_percent}}%)"/>
				</td>
			</tr>
			<tr>
				<td class="de_label">{{$lang.settings.installation_memcache_success}}:</td>
				<td class="de_control">
					<input type="text" class="dyn_full_size" value="{{$memcache_stats.memcache_success_hits}} / {{$memcache_stats.memcache_total_hits}} ({{$memcache_stats.memcache_success_percent}}%)"/>
				</td>
			</tr>
		{{/if}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_configuration}}</div></td>
		</tr>
		{{foreach item=item from=$data|smarty:nodefaults}}
			<tr>
				<td class="de_label">$config.{{$item.key}}:</td>
				<td class="de_control">
					{{if is_array($item.value)}}
						{{assign var="value" value="Array["}}
						{{foreach item=item2 name=data from=$item.value|smarty:nodefaults}}
							{{if $smarty.foreach.data.last}}
								{{assign var="value" value="`$value``$item2`"}}
							{{else}}
								{{assign var="value" value="`$value``$item2`, "}}
							{{/if}}
						{{/foreach}}
						{{assign var="value" value="`$value`]"}}
						<input type="text" class="dyn_full_size" value="{{$value}}"/>
					{{else}}
						<input type="text" class="dyn_full_size" value="{{$item.value}}"/>
					{{/if}}
				</td>
			</tr>
		{{/foreach}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_php_options}}</div></td>
		</tr>
		{{foreach item=item key=key from=$ini_vars|smarty:nodefaults}}
			<tr>
				<td class="de_label">{{$key}}:</td>
				<td class="de_control">
					{{if is_array($item)}}
						{{assign var="global_value" value="`$item.global_value`"}}
						{{if $global_value=='0'}}
							{{assign var="global_value" value="Off"}}
						{{elseif $global_value=='1'}}
							{{assign var="global_value" value="On"}}
						{{elseif $global_value==''}}
							{{assign var="global_value" value="N/A"}}
						{{/if}}
						{{assign var="local_value" value="`$item.local_value`"}}
						{{if $local_value=='0'}}
							{{assign var="local_value" value="Off"}}
						{{elseif $local_value=='1'}}
							{{assign var="local_value" value="On"}}
						{{elseif $local_value==''}}
							{{assign var="local_value" value="N/A"}}
						{{/if}}
						<input type="text" class="fixed_200" value="{{$global_value}}"/>
						/
						<input type="text" class="fixed_200" value="{{$local_value}}"/>
					{{/if}}
				</td>
			</tr>
		{{/foreach}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_environment}}</div></td>
		</tr>
		{{foreach key=key item=item from=$smarty.server|smarty:nodefaults}}
			<tr>
				<td class="de_label">{{$key}}:</td>
				<td class="de_control">
					<input type="text" class="dyn_full_size" value="{{$item}}"/>
				</td>
			</tr>
		{{/foreach}}
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_logs}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header fixed_height_30">
						<td>{{$lang.settings.installation_files_log_file}}</td>
						<td>{{$lang.settings.installation_files_filesize}}</td>
						<td>{{$lang.settings.installation_files_modified}}</td>
					</tr>
					{{foreach item=item from=$logs|smarty:nodefaults}}
						<tr class="eg_data fixed_height_30">
							<td><a href="?action=get_log&amp;log_index={{$item.file_index}}" rel="external">{{$item.file_name}}</a></td>
							<td>{{$item.file_size}}</td>
							<td>{{$item.file_time|date_format:$smarty.session.userdata.full_date_format}}</td>
						</tr>
					{{/foreach}}
				</table>
			</td>
		</tr>
		<tr>
			<td class="de_separator" colspan="2"><div>{{$lang.settings.installation_divider_engine_customizations}}</div></td>
		</tr>
		<tr>
			<td class="de_table_control" colspan="2">
				<table class="de_edit_grid">
					<colgroup>
						<col/>
						<col/>
						<col/>
					</colgroup>
					<tr class="eg_header fixed_height_30">
						<td>{{$lang.settings.installation_files_engine_file}}</td>
						<td>{{$lang.settings.installation_files_filesize}}</td>
						<td>{{$lang.settings.installation_files_modified}}</td>
					</tr>
					{{foreach item=item from=$engine_customizations|smarty:nodefaults}}
						<tr class="eg_data fixed_height_30">
							<td><a href="?action=get_customization_file&amp;file_index={{$item.file_index}}" rel="external">{{$item.file_name}}</a></td>
							<td>{{$item.file_size}}</td>
							<td>{{$item.file_time|date_format:$smarty.session.userdata.full_date_format}}</td>
						</tr>
					{{/foreach}}
				</table>
			</td>
		</tr>
	</table>
</form>