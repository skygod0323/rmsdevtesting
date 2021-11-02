<!DOCTYPE html>
<html lang="en">
<head>
	<title>Feed configuration</title>
	<style>
		body {
			margin: 0;
			font-family: Verdana, sans-serif;
			font-size: 14px;
		}

		input,
		select,
		textarea {
			font-family: Verdana, sans-serif;
			font-size: 14px;
		}

		label[for] {
			cursor: pointer;
			vertical-align: middle;
		}

		input[type="checkbox"] {
			cursor: pointer;
			vertical-align: middle;
		}

		.hidden,
		.hidden2 {
			display: none;
		}

		.general-error {
			width: 400px;
			margin: 10px auto;
			padding: 10px;
			background: #f8e4e4;
			color: #fe2a07;
			border: 1px solid #fe2a07;
		}

		.controls {
			border-collapse: separate;
			border-spacing: 10px;
		}

		.label {
			padding-right: 10px;
			width: 1%;
			white-space: nowrap;
		}

		.error {
			color: #fe2a07;
		}

		.label:after {
			content: ':';
			display: inline;
		}

		.label:empty:after {
			content: '';
		}

		.dependent .label {
			padding-left: 20px;
		}

		.section {
			background: #eee;
			padding: 10px;
			font-weight: bold;
			font-size: 120%;
			cursor: pointer;
		}

		.section em {
			position: relative;
			float: right;
			margin-top: 3px;
			border: 2px solid #999;
			width: 10px;
			height: 10px;
		}

		.section em:after {
			position: absolute;
			width: 8px;
			height: 2px;
			background: #999;
			top: 4px;
			left: 1px;
			content: '';
		}

		.section.collapsed em:after {
			position: absolute;
			width: 8px;
			height: 2px;
			background: #999;
			top: 4px;
			left: 1px;
			content: '';
		}

		.section.collapsed em:before {
			position: absolute;
			height: 8px;
			width: 2px;
			background: #999;
			left: 4px;
			top: 1px;
			content: '';
		}

		.section:hover em {
			border-color: #222;
		}

		.section:hover em:after,
		.section:hover em:before {
			background: #222;
		}

		.button {
			text-align: right;
		}

		.hint {
			padding-top: 5px;
			font-size: 80%;
		}

		#login {
			display: flex;
			justify-content: center;
			margin-top: 10px;
		}

		#login .controls {
			width: 1px;
			border: 1px solid #ddd;
		}

		#feed {
			width: 800px;
			margin: 0 auto;
		}

		#feed .controls {
			width: 100%;
			border: 1px solid #ddd;
		}

		#feed #selector {
			width: 800px;
			margin: 10px auto -1px auto;
		}

		#feed #selector a {
			display: inline-block;
			background: #f8f8f8;
			border: 1px solid #ddd;
			padding: 5px 10px 5px 10px;
			cursor: pointer;
			vertical-align: bottom;
		}

		#feed #selector a.active {
			padding: 10px;
			background: #fff;
			border-bottom-color: #fff;
		}
	</style>
</head>
<body>
{{if $error_field=='password'}}
	{{if $smarty.get.password}}
		<div class="general-error">Wrong password</div>
	{{/if}}
	<form id="login" action="" method="get">
		<table class="controls">
			<tr>
				<td class="label">Password</td>
				<td class="control"><input type="password" name="password" size="20" required/></td>
			</tr>
			<tr>
				<td class="button" colspan="2"><input type="submit" value="Log in"/></td>
			</tr>
		</table>
	</form>
{{else}}
	{{if $error_field}}
		<div class="general-error">
			{{if $error_field=='feed_format'}}
				<strong>Basic feed settings - Feed format</strong>: wrong value specified in the URL
			{{elseif $error_field=='locale'}}
				<strong>Filters - Language</strong>: wrong value specified in the URL
			{{elseif $error_field=='satellite'}}
				<strong>URLs and embeds - Satellite</strong>: wrong value specified in the URL
			{{elseif $error_field=='screenshot_format'}}
				<strong>Basic feed settings - Screenshot format</strong>: wrong value specified in the URL
			{{elseif $error_field=='poster_format'}}
				<strong>Basic feed settings - Poster format</strong>: wrong value specified in the URL
			{{elseif $error_field=='video_format_1'}}
				<strong>Video files - Video format 1</strong>: wrong value specified in the URL
			{{elseif $error_field=='video_format_standard'}}
				<strong>Video files - Video format 1</strong>: we changed implementation for this parameter, please refer what's new 5.2.0
			{{elseif $error_field=='video_format_2'}}
				<strong>Video files - Video format 2</strong>: wrong value specified in the URL
			{{elseif $error_field=='video_format_premium'}}
				<strong>Video files - Video format 2</strong>: we changed implementation for this parameter, please refer what's new 5.2.0
			{{elseif $error_field=='video_quality'}}
				<strong>Video files - Video quality</strong>: wrong value specified in the URL
			{{elseif $error_field=='rotation'}}
				<strong>Filters - Rotator status</strong>: wrong value specified in the URL
			{{elseif $error_field=='sorting'}}
				<strong>Basic feed settings - Sorting</strong>: wrong value specified in the URL
			{{elseif $error_field=='player_skin'}}
				<strong>URLs and embeds - Embed player skin</strong>: wrong value specified in the URL
			{{elseif $error_field=='player_url_pattern'}}
				<strong>URLs and embeds - Change embed URLs</strong>: missing %ID% token
			{{elseif $error_field=='csv_columns'}}
				<strong>Basic feed settings - CSV columns</strong>: one of the columns has wrong value specified in the URL
			{{else}}
				<strong>Field "{{$error_field}}"</strong>: wrong value specified in the URL
			{{/if}}
		</div>
	{{/if}}
	<div id="feed">
		<div id="selector">
			<a data-form-selector="data" class="active">Data feed</a>
			<a data-form-selector="deleted">Deleted data feed</a>
		</div>
		<form id="data" name="data_{{$smarty.now}}" action="" method="get" target="_blank">
			{{if $smarty.get.password}}
				<input type="hidden" name="password" value="{{$smarty.get.password}}"/>
			{{/if}}
			<input type="hidden" name="csv_columns" value=""/>
			<table class="controls">
				<tr>
					<td data-expander="section_basic" class="section" colspan="2">Basic feed settings <em></em></td>
				</tr>
				<tr data-section="section_basic">
					<td class="label {{if $error_field=='feed_format'}}error{{/if}}">Feed format</td>
					<td class="control">
						<select name="feed_format">
							<option value="kvs" {{if $smarty.get.feed_format=='kvs'}}selected{{/if}}>KVS</option>
							<option value="csv" {{if $smarty.get.feed_format=='csv'}}selected{{/if}}>CSV</option>
							{{if $smarty.get.feed_format && $error_field=='feed_format'}}
								<option value="{{$smarty.get.feed_format}}" selected>???</option>
							{{/if}}
						</select>
						<div class="hint">KVS format is good for copying content between multiple KVS installations; use CSV in all other cases</div>
					</td>
				</tr>
				{{assign var="csv_columns_parsed" value="|"|explode:$smarty.get.csv_columns}}
				{{section start="0" loop="50" name="csv_columns"}}
					<tr data-section="section_basic" data-feed-format="csv" class="dependent hidden2">
						<td class="label {{if $csv_columns_parsed[$smarty.section.csv_columns.index] && $csv_columns_parsed[$smarty.section.csv_columns.index]|strpos:'static:'===false && !in_array($csv_columns_parsed[$smarty.section.csv_columns.index], $allowed_csv_columns) && $error_field=='csv_columns'}}error{{/if}}">CSV column {{$smarty.section.csv_columns.iteration}}</td>
						<td class="control">
							<select name="csv_column_{{$smarty.section.csv_columns.iteration}}">
								{{assign var="csv_column_selected" value="false"}}
								<option value="">Select...</option>
								<option value="static:" {{if $csv_columns_parsed[$smarty.section.csv_columns.index]|strpos:'static:'!==false}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>* Static text *</option>
								<optgroup label="General info">
									<option value="id" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='id'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video ID</option>
									<option value="title" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='title'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Title</option>
									<option value="dir" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='dir'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Directory</option>
									<option value="description" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='description'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Description</option>
									<option value="rating" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='rating'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Rating (0-5)</option>
									<option value="rating_percent" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='rating_percent'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Rating (0-100%)</option>
									<option value="votes" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='votes'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Rating (votes)</option>
									<option value="popularity" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='popularity'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Popularity (views)</option>
									<option value="post_date" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='post_date'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Publishing date / time</option>
									<option value="user" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='user'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>User</option>
									<option value="release_year" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='release_year'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Release year</option>
								</optgroup>
								<optgroup label="Content">
									<option value="link" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='link'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video page URL</option>
									<option value="duration" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='duration'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Duration (seconds)</option>
									<option value="duration_hhmmss" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='duration_hhmmss'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Duration (HH:MM:SS)</option>
									<option value="quality" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='quality'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Quality factor (SD, HD)</option>
									{{if $feed_options.video_content_type_id==2 || $feed_options.video_content_type_id==4}}
										<option value="width" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='width'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video width (pixels)</option>
										<option value="height" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='height'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video height (pixels)</option>
										<option value="size" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='size'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video size (WxH)</option>
										<option value="filesize" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='filesize'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video filesize (bytes)</option>
										<option value="url" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='url'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Video file URL</option>
										<option value="embed" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='embed'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Embed code</option>
									{{elseif $feed_options.video_content_type_id==3}}
										<option value="embed" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='embed'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Embed code</option>
									{{/if}}
								</optgroup>
								{{if $feed_options.enable_categories==1 || $feed_options.enable_tags==1 || $feed_options.enable_models==1 || $feed_options.enable_content_sources==1 || $feed_options.enable_dvds==1}}
									<optgroup label="Categorization">
										{{if $feed_options.enable_categories==1}}
											<option value="categories" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='categories'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Categories (list)</option>
										{{/if}}
										{{if $feed_options.enable_tags==1}}
											<option value="tags" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='tags'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Tags (list)</option>
										{{/if}}
										{{if $feed_options.enable_models==1}}
											<option value="models" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='models'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Models (list)</option>
										{{/if}}
										{{if $feed_options.enable_content_sources==1}}
											<option value="content_source" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='content_source'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Content source</option>
											<option value="content_source_url" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='content_source_url'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Content source URL</option>
											<option value="content_source_group" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='content_source_group'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Content source group</option>
										{{/if}}
										{{if $feed_options.enable_dvds==1}}
											<option value="dvd" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='dvd'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>DVD / Channel / TV season</option>
											<option value="dvd_group" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='dvd_group'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>DVD group / Channel group / TV series</option>
										{{/if}}
									</optgroup>
								{{/if}}
								<optgroup label="Screenshots">
									<option value="main_screenshot" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='main_screenshot'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Main screenshot</option>
									<option value="main_screenshot_number" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='main_screenshot_number'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Main screenshot number</option>
									<option value="screenshots" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='screenshots'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Screenshots (list)</option>
								</optgroup>
								<optgroup label="Posters">
									<option value="main_poster" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='main_poster'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Main poster</option>
									<option value="main_poster_number" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='main_poster_number'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Main poster number</option>
									<option value="posters" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='posters'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Posters (list)</option>
								</optgroup>
								{{if $feed_options.enable_custom_fields==1}}
									<optgroup label="Customization">
										<option value="custom1" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='custom1'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Custom 1</option>
										<option value="custom2" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='custom2'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Custom 2</option>
										<option value="custom3" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]=='custom3'}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Custom 3</option>
									</optgroup>
								{{/if}}
								{{if $feed_options.enable_localization==1 && count($languages)>0}}
									<optgroup label="Localization">
										{{foreach from=$languages|smarty:nodefaults item="item"}}
											{{assign var="csv_language_key" value="title_`$item.code`"}}
											<option value="{{$csv_language_key}}" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]==$csv_language_key}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Title ({{$item.title}})</option>
											{{assign var="csv_language_key" value="description_`$item.code`"}}
											<option value="{{$csv_language_key}}" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]==$csv_language_key}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Description ({{$item.title}})</option>
											{{assign var="csv_language_key" value="dir_`$item.code`"}}
											<option value="{{$csv_language_key}}" {{if count($csv_columns_parsed)>$smarty.section.csv_columns.index && $csv_columns_parsed[$smarty.section.csv_columns.index]==$csv_language_key}}selected{{assign var="csv_column_selected" value="true"}}{{/if}}>Directory ({{$item.title}})</option>
										{{/foreach}}
									</optgroup>
								{{/if}}
								{{if $csv_columns_parsed[$smarty.section.csv_columns.index]}}
									{{if $csv_column_selected=="false"}}
										{{if !in_array($csv_columns_parsed[$smarty.section.csv_columns.index], $allowed_csv_columns) && $error_field=='csv_columns'}}
											<option value="{{$csv_columns_parsed[$smarty.section.csv_columns.index]}}" selected>???</option>
										{{else}}
											<option value="{{$csv_columns_parsed[$smarty.section.csv_columns.index]}}" selected>{{$csv_columns_parsed[$smarty.section.csv_columns.index]}}</option>
										{{/if}}
									{{/if}}
								{{/if}}
							</select>
							{{assign var="csv_static_text_pos" value=$csv_columns_parsed[$smarty.section.csv_columns.index]|strpos:'static:'}}
							<input class="hidden" type="text" name="csv_static_text_{{$smarty.section.csv_columns.iteration}}" size="20" {{if $csv_static_text_pos!==false}}value="{{$csv_columns_parsed[$smarty.section.csv_columns.index]|substr:7}}"{{/if}}>
						</td>
					</tr>
				{{/section}}
				<tr data-section="section_basic" data-feed-format2="csv" class="dependent hidden2">
					<td class="label">CSV field separator</td>
					<td class="control">
						<input type="text" name="csv_separator" size="20" value="{{$smarty.get.csv_separator|default:"|"}}">
						<div class="hint">Which symbol should be used to separate feed columns, use <strong>\t</strong> for tabulation</div>
					</td>
				</tr>
				<tr data-section="section_basic" data-feed-format2="csv" class="dependent hidden2">
					<td class="label">CSV list separator</td>
					<td class="control">
						<input type="text" name="csv_list_separator" size="20" value="{{$smarty.get.csv_list_separator|default:","}}">
						<div class="hint">Which symbol should be used to separate lists</div>
					</td>
				</tr>
				<tr data-section="section_basic" data-feed-format2="csv" class="dependent hidden2">
					<td class="label">CSV quotation</td>
					<td class="control">
						<input type="checkbox" id="csv_quote" name="csv_quote" value="1" {{if $smarty.get.csv_quote}}checked{{/if}}/>
						<label for="csv_quote">Quote all columns</label>
						<div class="hint">Whether to enclose all data within double-quote characters</div>
					</td>
				</tr>
				<tr data-section="section_basic">
					<td class="label">Limit</td>
					<td class="control">
						<input type="number" name="limit" size="20" maxlength="10" min="1" max="{{$max_limit}}" value="{{$smarty.get.limit|default:10}}"/>
						<div class="hint">The maximum number of videos returned by feed; a number between 1 and {{$max_limit}}</div>
					</td>
				</tr>
				<tr data-section="section_basic">
					<td class="label">Start</td>
					<td class="control">
						<input type="number" name="start" size="20" maxlength="10" min="1" value="{{$smarty.get.start}}"/>
						<div class="hint">The minimum video ID to return. Can be used to skip some part of videos based on ID</div>
					</td>
				</tr>
				<tr data-section="section_basic">
					<td class="label">Skip</td>
					<td class="control">
						<input type="number" name="skip" size="20" maxlength="10" min="1" value="{{$smarty.get.skip}}"/>
						<div class="hint">The number or videos to skip from the beginning. Can be used as pagination control</div>
					</td>
				</tr>
				<tr data-section="section_basic">
					<td class="label {{if $error_field=='sorting'}}error{{/if}}">Sorting</td>
					<td class="control">
						<select name="sorting">
							<option value="post_date desc" {{if $smarty.get.sorting=='post_date desc'}}selected{{/if}}>Most recent first</option>
							<option value="post_date asc" {{if $smarty.get.sorting=='post_date asc'}}selected{{/if}}>Most recent last</option>
							<option value="rating desc" {{if $smarty.get.sorting=='rating desc'}}selected{{/if}}>Top rated first</option>
							<option value="rating asc" {{if $smarty.get.sorting=='rating asc'}}selected{{/if}}>Top rated last</option>
							<option value="popularity desc" {{if $smarty.get.sorting=='popularity desc'}}selected{{/if}}>Most popular first</option>
							<option value="popularity asc" {{if $smarty.get.sorting=='popularity asc'}}selected{{/if}}>Most popular last</option>
							<option value="duration desc" {{if $smarty.get.sorting=='duration desc'}}selected{{/if}}>Longest first</option>
							<option value="duration asc" {{if $smarty.get.sorting=='duration asc'}}selected{{/if}}>Longest last</option>
							<option value="video_id desc" {{if $smarty.get.sorting=='video_id desc'}}selected{{/if}}>Biggest ID first</option>
							<option value="video_id asc" {{if $smarty.get.sorting=='video_id asc'}}selected{{/if}}>Biggest ID last</option>
							{{if $smarty.get.sorting && $error_field=='sorting'}}
								<option value="{{$smarty.get.sorting}}" selected>???</option>
							{{/if}}
						</select>
						<div class="hint">Sorting in feed</div>
					</td>
				</tr>
				<tr data-section="section_basic">
					<td class="label {{if $error_field=='screenshot_format'}}error{{/if}}">Screenshot format</td>
					<td class="control">
						<select name="screenshot_format">
							{{foreach from=$screenshot_formats|smarty:nodefaults item="item"}}
								<option value="{{$item}}" {{if $smarty.get.screenshot_format==$item || (!$smarty.get.screenshot_format && $item=='source')}}selected{{/if}}>{{if $item=='source'}}Source{{else}}{{$item}}{{/if}}</option>
							{{/foreach}}
							{{if $smarty.get.screenshot_format && $error_field=='screenshot_format'}}
								<option value="{{$smarty.get.screenshot_format}}" selected>???</option>
							{{/if}}
						</select>
						<div class="hint">Size for feed screenshots</div>
					</td>
				</tr>
				{{if count($poster_formats)>0}}
					<tr data-section="section_basic">
						<td class="label {{if $error_field=='poster_format'}}error{{/if}}">Poster format</td>
						<td class="control">
							<select name="poster_format">
								{{foreach from=$poster_formats|smarty:nodefaults item="item"}}
									<option value="{{$item}}" {{if $smarty.get.poster_format==$item || (!$smarty.get.poster_format && $item=='source')}}selected{{/if}}>{{if $item=='source'}}Source{{else}}{{$item}}{{/if}}</option>
								{{/foreach}}
								{{if $smarty.get.poster_format && $error_field=='poster_format'}}
									<option value="{{$smarty.get.poster_format}}" selected>???</option>
								{{/if}}
							</select>
							<div class="hint">Size for feed posters</div>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td data-expander="section_filters" class="section collapsed {{if $error_field=='rotation' || $error_field=='locale'}}error{{/if}}" colspan="2">Filters <span class="changes"></span> <em></em></td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Quality</td>
					<td class="control">
						<input type="checkbox" id="hd" name="hd" value="1" {{if $smarty.get.hd}}checked{{/if}}/>
						<label for="hd">HD videos</label>
						<div class="hint">Only HD videos will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Days</td>
					<td class="control">
						<input type="number" name="days" size="20" maxlength="10" min="1" value="{{$smarty.get.days}}"/>
						<div class="hint">Only videos added within last N days will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Duration</td>
					<td class="control">
						from <input type="number" name="min_duration" size="5" maxlength="10" min="1" value="{{$smarty.get.min_duration}}"/>
						to <input type="number" name="max_duration" size="5" maxlength="10" min="1" value="{{$smarty.get.max_duration}}"/>
						in seconds
						<div class="hint">Only videos with duration within this interval will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Rating</td>
					<td class="control">
						from <input type="number" name="min_rating" size="5" maxlength="3" min="1" max="100" value="{{$smarty.get.min_rating}}"/>
						to <input type="number" name="max_rating" size="5" maxlength="3" min="1" max="100" value="{{$smarty.get.max_rating}}"/>
						in percents
						<div class="hint">Only videos with rating within this interval will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Views</td>
					<td class="control">
						from <input type="number" name="min_views" size="5" maxlength="10" min="1" value="{{$smarty.get.min_views}}"/>
						to <input type="number" name="max_views" size="5" maxlength="10" min="1" value="{{$smarty.get.max_views}}"/>
						in number of views
						<div class="hint">Only videos with number of views within this interval will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Category</td>
					<td class="control">
						{{if is_array($categories)}}
							<select name="category">
								<option value="">Select...</option>
								{{foreach from=$categories|smarty:nodefaults item="item"}}
									<option value="{{$item}}" {{if $smarty.get.category==$item}}selected{{/if}}>{{$item}}</option>
								{{/foreach}}
							</select>
						{{else}}
							<input type="text" name="category" size="20" value="{{$smarty.get.category}}"/>
						{{/if}}
						<div class="hint">Only videos with the given category will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Tag</td>
					<td class="control">
						<input type="text" name="tag" size="20" value="{{$smarty.get.tag}}"/>
						<div class="hint">Only videos with the given tag will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Model</td>
					<td class="control">
						<input type="text" name="model" size="20" value="{{$smarty.get.model}}"/>
						<div class="hint">Only videos with the given model will be returned</div>
					</td>
				</tr>
				<tr data-section="section_filters" class="hidden">
					<td class="label">Content source</td>
					<td class="control">
						<input type="text" name="sponsor" size="20" value="{{$smarty.get.sponsor}}"/>
						<div class="hint">Only videos with the given content source will be returned</div>
					</td>
				</tr>
				{{if $config.dvds_mode=='channels'}}
					<tr data-section="section_filters" class="hidden">
						<td class="label">Channel</td>
						<td class="control">
							<input type="text" name="channel" size="20" value="{{$smarty.get.channel}}"/>
							<div class="hint">Only videos with from the given channel will be returned</div>
						</td>
					</tr>
				{{/if}}
				{{if $feed_options.enable_search==1}}
					<tr data-section="section_filters" class="hidden">
						<td class="label">Text search</td>
						<td class="control">
							<input type="text" name="search" size="20" value="{{$smarty.get.search}}"/>
							<div class="hint">Only videos with the given text match will be returned</div>
						</td>
					</tr>
				{{/if}}
				{{if $feed_options.with_rotation_finished!=1 && $screenshot_rotator_enabled==1}}
					<tr data-section="section_filters" class="hidden">
						<td class="label {{if $error_field=='rotation'}}error{{/if}}">Rotator status</td>
						<td class="control">
							<select name="rotation">
								<option value="">Select...</option>
								<option value="finished" {{if $smarty.get.rotation=='finished'}}selected{{/if}}>Rotation finished</option>
								<option value="ongoing" {{if $smarty.get.rotation=='ongoing'}}selected{{/if}}>Rotation ongoing</option>
								{{if $smarty.get.rotation && $error_field=='rotation'}}
									<option value="{{$smarty.get.rotation}}" selected>???</option>
								{{/if}}
							</select>
							<div class="hint">Only videos that finished their screenshot CTR rotation will be returned</div>
						</td>
					</tr>
				{{/if}}
				{{if $feed_options.enable_localization==1 && (count($languages)>0 || $error_field=='locale')}}
					<tr data-section="section_filters" class="hidden">
						<td class="label {{if $error_field=='locale'}}error{{/if}}">Language</td>
						<td class="control">
							<select name="locale">
								<option value="">Select...</option>
								{{foreach from=$languages|smarty:nodefaults item="item"}}
									<option value="{{$item.code}}" {{if $smarty.get.locale==$item.code}}selected{{/if}}>{{$item.title}}</option>
								{{/foreach}}
								{{if $smarty.get.locale && $error_field=='locale'}}
									<option value="{{$smarty.get.locale}}" selected>???</option>
								{{/if}}
							</select>
							<div class="hint">Only videos that are translated to this language (title translation) will be returned</div>
						</td>
					</tr>
				{{/if}}
				{{if $video_content_type_id==2 || $video_content_type_id==4}}
					<tr>
						<td data-expander="section_video_files" class="section collapsed {{if $error_field=='video_quality' || $error_field=='video_format_1' || $error_field=='video_format_2' || $error_field=='video_format_standard' || $error_field=='video_format_premium'}}error{{/if}}" colspan="2">Video files <span class="changes"></span> <em></em></td>
					</tr>
					<tr data-section="section_video_files" class="hidden">
						<td class="label {{if $error_field=='video_quality'}}error{{/if}}">Video quality</td>
						<td class="control">
							<select name="video_quality">
								<option value="">Select...</option>
								<option value="best" {{if $smarty.get.video_quality=='best'}}selected{{/if}}>Best</option>
								<option value="worst" {{if $smarty.get.video_quality=='worst'}}selected{{/if}}>Worst</option>
								{{if $smarty.get.video_quality && $error_field=='video_quality'}}
									<option value="{{$smarty.get.video_quality}}" selected>???</option>
								{{/if}}
							</select>
							<div class="hint">Video file quality selector</div>
						</td>
					</tr>
					{{if count($video_formats_standard)>0}}
						<tr data-section="section_video_files" class="hidden">
							<td class="label {{if $error_field=='video_format_1' || $error_field=='video_format_standard'}}error{{/if}}">Video format 1</td>
							<td class="control">
								<select name="video_format_1">
									<option value="">Select...</option>
									{{foreach from=$video_formats_standard|smarty:nodefaults item="item"}}
										<option value="{{$item}}" {{if $smarty.get.video_format_1==$item}}selected{{/if}}>{{$item}}</option>
									{{/foreach}}
									{{if $smarty.get.video_format_1 && $error_field=='video_format_1'}}
										<option value="{{$smarty.get.video_format_1}}" selected>???</option>
									{{/if}}
								</select>
								<div class="hint">Feed will return video file of this format for each standard video <br/> If a video has no such file, feed will return empty field</div>
							</td>
						</tr>
					{{/if}}
					{{if count($video_formats_premium)>0}}
						<tr data-section="section_video_files" class="hidden">
							<td class="label {{if $error_field=='video_format_2' || $error_field=='video_format_premium'}}error{{/if}}">Video format 2</td>
							<td class="control">
								<select name="video_format_2">
									<option value="">Select...</option>
									{{foreach from=$video_formats_premium|smarty:nodefaults item="item"}}
										<option value="{{$item}}" {{if $smarty.get.video_format_2==$item}}selected{{/if}}>{{$item}}</option>
									{{/foreach}}
									{{if $smarty.get.video_format_2 && $error_field=='video_format_2'}}
										<option value="{{$smarty.get.video_format_2}}" selected>???</option>
									{{/if}}
								</select>
								<div class="hint">Feed will return video file of this format for each premium video <br/> If a video has no such file, feed will return empty field</div>
							</td>
						</tr>
					{{/if}}
					<tr data-section="section_video_files" class="hidden">
						<td class="label">Video duration</td>
						<td class="control">
							<input type="checkbox" id="show_real_duration" name="show_real_duration" value="1" {{if $smarty.get.show_real_duration}}checked{{/if}}/>
							<label for="show_real_duration">Global duration</label>
							<div class="hint">Feed will return the duration of the selected video format and it can be smaller than video duration <br/> Enable this option if you want feed to return the duration of a video instead</div>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td data-expander="section_urls_embeds" class="section collapsed {{if $error_field=='satellite' || $error_field=='player_skin' || $error_field=='player_url_pattern'}}error{{/if}}" colspan="2">URLs and embeds <span class="changes"></span> <em></em></td>
				</tr>
				{{if $feed_options.enable_satellites==1 && (count($satellites)>0 || $error_field=='satellite')}}
					<tr data-section="section_urls_embeds" class="hidden">
						<td class="label {{if $error_field=='satellite'}}error{{/if}}">Satellite</td>
						<td class="control">
							<select name="satellite">
								<option value="">Select...</option>
								{{foreach from=$satellites|smarty:nodefaults item="item"}}
									<option value="{{$item.domain}}" {{if $smarty.get.satellite==$item.domain}}selected{{/if}}>{{$item.domain}}</option>
								{{/foreach}}
								{{if $smarty.get.satellite && $error_field=='satellite'}}
									<option value="{{$smarty.get.satellite}}" selected>???</option>
								{{/if}}
							</select>
							<div class="hint">Videos will have page URLs and embed codes returned for satellite domain</div>
						</td>
					</tr>
				{{/if}}
				{{if is_array($affiliate_params)}}
					{{foreach from=$affiliate_params|smarty:nodefaults item="item"}}
						{{if $item}}
							<tr data-section="section_urls_embeds" class="hidden">
								<td class="label">{{$item}}</td>
								<td class="control">
									<input type="text" name="{{$item}}" size="20" value="{{$smarty.get.$item}}"/>
									<div class="hint">Traffic tracking parameter, will be added to page URLs and embed codes</div>
								</td>
							</tr>
						{{/if}}
					{{/foreach}}
				{{/if}}
				{{if $video_content_type_id==2 || $video_content_type_id==3 || $video_content_type_id==4}}
					<tr data-section="section_urls_embeds" class="hidden">
						<td class="label {{if $error_field=='player_skin'}}error{{/if}}">Embed player skin</td>
						<td class="control">
							<select name="player_skin">
								<option value="">Select...</option>
								<option value="black" {{if $smarty.get.player_skin=='black'}}selected{{/if}}>Black</option>
								<option value="white" {{if $smarty.get.player_skin=='white'}}selected{{/if}}>White</option>
								{{if $smarty.get.player_skin && $error_field=='player_skin'}}
									<option value="{{$smarty.get.player_skin}}" selected>???</option>
								{{/if}}
							</select>
							<div class="hint">Skin of the embed player</div>
						</td>
					</tr>
					<tr data-section="section_urls_embeds" class="hidden">
						<td class="label">Embed player width</td>
						<td class="control">
							<input type="number" name="player_width" size="20" min="1" value="{{$smarty.get.player_width}}"/>
							<div class="hint">Width of the embed player in pixels; will be using video width by default</div>
						</td>
					</tr>
					<tr data-section="section_urls_embeds" class="hidden">
						<td class="label">Embed player height</td>
						<td class="control">
							<input type="number" name="player_height" size="20" min="1" value="{{$smarty.get.player_height}}"/>
							<div class="hint">Height of the embed player in pixels; will be using video height by default</div>
						</td>
					</tr>
					<tr data-section="section_urls_embeds" class="hidden">
						<td class="label {{if $error_field=='player_url_pattern'}}error{{/if}}">Change embed URLs</td>
						<td class="control">
							<input type="text" name="player_url_pattern" size="40" value="{{$smarty.get.player_url_pattern}}"/>
							<div class="hint">Customize embed player URL pattern if it should be different from the default one: <br> {{$config.project_url}}/embed/%ID%</div>
						</td>
					</tr>
				{{/if}}
				<tr>
					<td class="button" colspan="2"><input type="submit" value="Get videos"/></td>
				</tr>
			</table>
		</form>
		<form id="deleted" name="deleted_{{$smarty.now}}" action="" method="get" target="_blank" class="hidden">
			{{if $smarty.get.password}}
				<input type="hidden" name="password" value="{{$smarty.get.password}}"/>
			{{/if}}
			<table class="controls">
				<tr>
					<td class="label">Data format</td>
					<td class="control">
						<select name="action">
							<option value="get_deleted_ids">IDs</option>
							<option value="get_deleted_urls">URLs</option>
						</select>
						<div class="hint">Format of the deleted data</div>
					</td>
				</tr>
				<tr>
					<td class="label">Days</td>
					<td class="control">
						<input type="number" name="days" size="20" maxlength="10" min="1"/>
						<div class="hint">Only videos deleted within last N days will be returned</div>
					</td>
				</tr>
				{{if $feed_options.enable_satellites==1 && (count($satellites)>0 || $error_field=='satellite')}}
					<tr data-deleted-format="urls">
						<td class="label">Satellite</td>
						<td class="control">
							<select name="satellite">
								<option value="">Select...</option>
								{{foreach from=$satellites|smarty:nodefaults item="item"}}
									<option value="{{$item.domain}}">{{$item.domain}}</option>
								{{/foreach}}
							</select>
							<div class="hint">Deleted URLs will be formatted for this satellite</div>
						</td>
					</tr>
				{{/if}}
				{{if is_array($affiliate_params)}}
					{{foreach from=$affiliate_params|smarty:nodefaults item="item"}}
						{{if $item}}
							<tr data-deleted-format="urls">
								<td class="label">{{$item}}</td>
								<td class="control">
									<input type="text" name="{{$item}}" size="20" value="{{$smarty.get.$item}}"/>
									<div class="hint">Traffic tracking parameter, will be added to page URLs to form exact match</div>
								</td>
							</tr>
						{{/if}}
					{{/foreach}}
				{{/if}}
				<tr>
					<td class="button" colspan="2"><input type="submit" value="Get videos"/></td>
				</tr>
			</table>
		</form>
	</div>
{{/if}}
<script>
	function $list(selector) {
		if (selector) {
			return document.querySelectorAll(selector);
		}
		return [];
	}

	function $first(selector) {
		var list = $list(selector);
		if (list && list.length > 0) {
			return list[0];
		}
		return null;
	}

	function $add(element, style) {
		if (element && style) {
			if (!$has(element, style)) {
				if (element.className.length == 0) {
					element.className = style;
				} else {
					element.className += ' ' + style;
				}
			}
		}
		return element;
	}

	function $has(element, style) {
		if (element && element.className && style) {
			var classes = element.className.split(' '), i;
			for (i = 0; i < classes.length; i++) {
				if (classes[i].trim() == style) {
					return true;
				}
			}
		}
		return false;
	}

	function $delete(element, style) {
		if (element && style) {
			var classes = element.className.split(' '), newClass = '', i;
			for (i = 0; i < classes.length; i++) {
				if (classes[i] != style) {
					newClass += ' ' + classes[i];
				}
			}
			element.className = newClass.trim();
		}
		return element;
	}

	function $toggle(element, style, condition) {
		if (element && style) {
			if (condition) {
				$add(element, style);
			} else {
				$delete(element, style);
			}
		}
		return element;
	}

	function $disable(element, condition) {
		if (element) {
			element.disabled = !!condition;
		}
		return element;
	}

	function $event(object, event, listener) {
		if (object && event && listener) {
			var list = [object], i;
			if (typeof object == 'string') {
				list = $list(object);
			}
			for (i = 0; i < list.length; i++) {
				if (list[i].addEventListener) {
					list[i].addEventListener(event, listener, false);
				} else {
					list[i]['on' + event] = listener;
				}
			}
		}
	}

	function $value(element, value) {
		if (element) {
			if (typeof value == 'undefined') {
				if (element.tagName.toLowerCase() == 'select') {
					if (element.options && element.selectedIndex >= 0 && element.selectedIndex < element.options.length) {
						return element.options[element.selectedIndex].value;
					}
				} else if (element.type == 'checkbox' && !element.checked) {
					return '';
				}
				return element.value;
			} else {
				if (element.type == 'checkbox')
				{
					element.checked = (!!value);
				} else if (element.tagName.toLowerCase() == 'select')
				{
					for (var i = 0; i < element.options.length; i++) {
						if (element.options[i].value == value) {
							element.selectedIndex = i;
							break;
						}
					}
				} else {
					element.value = value;
				}
				return $value(element);
			}
		}
		return '';
	}

	function $attr(element, name, value) {
		if (element && name) {
			if (typeof value == 'undefined') {
				if (typeof element.getAttribute == 'function') {
					return element.getAttribute(name) || '';
				} else {
					return '';
				}
			} else {
				element.setAttribute(name, value);
				return '' + value;
			}
		}
		return '';
	}

	$event('[data-expander]', 'click', function() {
		var state = false, dependents, i, sectionId = $attr(this, 'data-expander');

		if (sectionId) {
			$toggle(this, 'collapsed', !$has(this, 'collapsed'));
			if (!$has(this, 'collapsed')) {
				state = true;
			}

			dependents = $list('[data-section="' + sectionId + '"]');
			for (i = 0; i < dependents.length; i++) {
				$toggle(dependents[i], 'hidden', !state);
			}
		}
	});

	$event('[data-form-selector]', 'click', function() {
		var formId = $attr(this, 'data-form-selector'), list, i;
		if (formId) {
			list = $list('form');
			for (i = 0; i < list.length; i++) {
				$toggle(list[i], 'hidden', list[i].id != formId);
			}
			list = $list('[data-form-selector]');
			for (i = 0; i < list.length; i++) {
				$toggle(list[i], 'active', $attr(list[i], 'data-form-selector') == formId);
			}
		}
	});

	$event('form', 'submit', function(e) {
		if (e) {
			e.preventDefault();

			var element, i, disabled = [], csv_columns = [], feed_format;
			for (i = 0; i < this.elements.length; i++) {
				element = this.elements[i];
				if (element.name == 'feed_format') {
					feed_format = $value(element);
				}
				if (!$value(element) || element.name.indexOf('csv_static_text_') == 0 || element.name == 'csv_separator' || element.name == 'csv_list_separator' || element.name == 'csv_quote') {
					$disable(element, true);
					disabled.push(element);
				} else if (element.name.indexOf('csv_column_') == 0) {
					if ($value(element) == 'static:') {
						csv_columns.push($value(element) + $value($first('[name="csv_static_text_' + parseInt(element.name.substring('csv_column_'.length)) + '"]')));
					} else {
						csv_columns.push($value(element));
					}
					$disable(element, true);
					disabled.push(element);
				}
			}
			if (feed_format == 'csv') {
				if (csv_columns.length > 0) {
					$value($first('[name="csv_columns"]'), csv_columns.join('|'));
					$disable($first('[name="csv_columns"]'), false);
				}
				$disable($first('[name="csv_separator"]'), false);
				$disable($first('[name="csv_list_separator"]'), false);
				$disable($first('[name="csv_quote"]'), false);
			}
			this.submit();
			$value($first('[name="csv_columns"]'), '');
			for (i = 0; i < disabled.length; i++) {
				$disable(disabled[i], false);
			}
		}
	});

	$event(document, 'change', function(e) {
		if (e && e.target && e.target.name) {
			var parent = e.target.parentNode, section = $attr(parent, 'data-section'), changes, i, j;
			while (parent && !section) {
				parent = parent.parentNode;
				section = $attr(parent, 'data-section')
			}
			if (section && section != 'section_basic') {
				parent = $list('[data-expander="' + section + '"] .changes');
				if (parent.length > 0) {
					changes = $attr(parent[0], 'data-cnt') || '';
					changes = changes ? changes.split(',') : [];
					i = changes.indexOf(e.target.name);
					if ($value(e.target)) {
						if (i == -1) {
							changes.push(e.target.name);
						}
					} else {
						if (i >= 0) {
							changes.splice(i, 1);
						}
					}
					$attr(parent[0], 'data-cnt', changes.join(','));
					parent[0].innerHTML = changes.length == 0 ? '' : '(' + changes.length + ' selected)';
				}
			}
			if (e.target.name == 'feed_format' || e.target.name.indexOf('csv_column_') == 0) {
				j = $value($first('[name="feed_format"]')) != 'csv';
				changes = $list('[data-feed-format="csv"] select');
				section = 0;
				for (i = 0; i < changes.length; i++) {
					if (changes[i].name && changes[i].name.indexOf('csv_column_') == 0 && $value(changes[i])) {
						section = i + 1;
					}
				}
				for (i = 0; i < changes.length; i++) {
					parent = changes[i].parentNode;
					while (parent && $attr(parent, 'data-feed-format') != 'csv') {
						parent = parent.parentNode;
					}
					$toggle(parent, 'hidden2', j || i > section);
				}
				changes = $list('[data-feed-format2="csv"]');
				for (i = 0; i < changes.length; i++) {
					$toggle(changes[i], 'hidden2', j);
				}
			}
			if (e.target.name.indexOf('csv_column_') == 0) {
				$toggle($first('[name="csv_static_text_' + parseInt(e.target.name.substring('csv_column_'.length)) + '"]'), 'hidden', $value(e.target) != 'static:');
			}
			if (e.target.name == 'action') {
				changes = $list('[data-deleted-format="urls"]');
				for (i = 0; i < changes.length; i++) {
					$toggle(changes[i], 'hidden2', $value(e.target) != 'get_deleted_urls');
				}
			}
		}
	});

	var forms = $list('form'), i, j, e;
	for (j = 0; j < forms.length; j++) {
		for (i = 0; i < forms[j].elements.length; i++) {
			e = document.createEvent('HTMLEvents');
			e.initEvent('change', true, true);
			forms[j].elements[i].dispatchEvent(e);
		}
	}
</script>
</body>
</html>