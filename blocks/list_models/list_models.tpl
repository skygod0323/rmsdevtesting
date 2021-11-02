{{* variables supported to control this list *}}
{{assign var="list_models_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="list_models_title" value="Models"}} {{* title *}}

<div id="{{$block_uid}}" class="list-models">
	<h{{$list_models_header_level|default:"2"}}>{{$list_models_title|default:"Models"}}</h{{$list_models_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<a class="item" title="{{$item.title}}" {{if $item.view_page_url!=''}}href="{{$item.view_page_url}}"{{/if}}>
					ID: {{$item.model_id}}<br/>
					Title: {{$item.title}}<br/>
					Description: {{$item.description}}<br/>
					Directory: {{$item.dir}}<br/>
					Pseudonyms: {{$item.alias}}<br/>
					Country: {{$item.country}}<br/>
					State: {{$item.state}}<br/>
					City: {{$item.city}}<br/>
					Height: {{$item.height}}<br/>
					Weight: {{$item.weight}}<br/>
					Measurements: {{$item.measurements}}<br/>
					Gender: {{if $item.gender_id==0}}Female{{elseif $item.gender_id==1}}Male{{elseif $item.gender_id==2}}Other{{/if}}<br/>
					Hair: {{if $item.hair_id==1}}Black{{elseif $item.hair_id==2}}Dark{{elseif $item.hair_id==3}}Red{{elseif $item.hair_id==4}}Brown{{elseif $item.hair_id==5}}Blond{{elseif $item.hair_id==6}}Grey{{elseif $item.hair_id==7}}Bald{{elseif $item.hair_id==8}}Wig{{/if}}<br/>
					Eye color: {{if $item.eye_color_id==1}}Blue{{elseif $item.eye_color_id==2}}Gray{{elseif $item.eye_color_id==3}}Green{{elseif $item.eye_color_id==4}}Amber{{elseif $item.eye_color_id==5}}Brown{{elseif $item.eye_color_id==6}}Hazel{{elseif $item.eye_color_id==7}}Black{{/if}}<br/>
					Birth date: {{if $item.birth_date!='0000-00-00'}}{{$item.birth_date|date_format:"%d %B, %Y"}}{{/if}}<br/>
					Death date: {{if $item.death_date!='0000-00-00'}}{{$item.death_date|date_format:"%d %B, %Y"}}{{/if}}<br/>
					Age: {{if $item.age>0}}{{$item.age}}{{/if}}<br/>
					Rating: {{$item.rating/5*100}}%<br/>
					Votes: {{if $item.rating==0}}0{{else}}{{$item.rating_amount}}{{/if}}<br/>
					Views: {{$item.model_viewed}}<br/>
					Comments: {{$item.comments_count}}<br/>
					Subscribers: {{$item.subscribers_count}}<br/>
					Total videos: {{$item.total_videos}}<br/>
					Today videos: {{$item.today_videos}}<br/>
					Total albums: {{$item.total_albums}}<br/>
					Today albums: {{$item.today_albums}}<br/>
					Total photos: {{$item.total_photos}}<br/>
					Total posts: {{$item.total_posts}}<br/>
					Today posts: {{$item.today_posts}}<br/>
					Total channels: {{$item.total_dvds}}<br/>
					Total channel groups: {{$item.total_dvd_groups}}<br/>
					Added: {{$item.added_date|date_format:"%d %B, %Y"}}<br/>
					Last updated: {{if $item.last_content_date!='0000-00-00'}}{{$item.last_content_date|date_format:"%d %B, %Y"}}{{/if}}<br/>
					Rank: {{$item.rank}}<br/>
					Prev rank: {{$item.last_rank}}<br/>
					Screenshot 1: {{if $item.screenshot1}}{{$item.base_files_url}}/{{$item.screenshot1}}{{/if}}<br/>
					Screenshot 2: {{if $item.screenshot2}}{{$item.base_files_url}}/{{$item.screenshot2}}{{/if}}<br/>
					Custom text 1: {{$item.custom1}}<br/>
					Custom file 1: {{if $item.custom_file1}}{{$item.base_files_url}}/{{$item.custom_file1}}{{/if}}<br/>
				</a>
			{{/foreach}}
		</div>

		{{* include pagination here *}}
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>