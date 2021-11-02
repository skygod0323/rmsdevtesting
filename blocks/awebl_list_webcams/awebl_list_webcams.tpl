{{* variables supported to control this list *}}
{{assign var="awebl_list_webcams_header_level" value="1"}} {{* whether to render H1 or H2 *}}
{{assign var="awebl_list_webcams_title" value="Webcams"}} {{* title *}}

<div id="{{$block_uid}}" class="awebl-list-webcams">
	<h{{$awebl_list_webcams_header_level|default:"2"}}>{{$awebl_list_webcams_title|default:"Webcams"}}</h{{$awebl_list_webcams_header_level|default:"2"}}>
	{{if count($data)>0}}
		<div class="items">
			{{foreach item="item" from=$data}}
				<div>
					ID: {{$item.id}}<br/>
					Title: {{$item.nick}}<br/>
					Status: {{if $item.status==0}}Offline{{elseif $item.status==1}}Online{{elseif $item.status==2}}Private{{/if}}
					Price: {{$item.price}}<br/>
					Age: {{$item.personAge}}<br/>
					Region: {{$item.region}}<br/>
					Rating: {{$item.averageRating}}<br/>
					Ethnicity: {{$item.ethnicity}}<br/>
					Profile picture 285x160: {{$item.profilePictures.285x160}}<br/>
					Profile picture 358x201: {{$item.profilePictures.358x201}}<br/>
					Profile picture 445x250: {{$item.profilePictures.445x250}}<br/>
					Profile picture 224x168: {{$item.profilePictures.224x168}}<br/>
					Profile picture 460x345: {{$item.profilePictures.460x345}}<br/>
					Profile picture 1024x768: {{$item.profilePictures.1024x768}}<br/>
					Has story: {{if $item.hasStory==1}}Yes{{else}}No{{/if}}<br/>
					Has VIP show: {{if $item.hasVipShow==1}}Yes{{else}}No{{/if}}<br/>
					Video calls allowed: {{if $item.isVideoCallEnabled==1}}Yes{{else}}No{{/if}}<br/>
					Vibrator active: {{if $item.isVibratorActive==1}}Yes{{else}}No{{/if}}<br/>
					Languages: {{foreach from=$item.language item="item_language" name="data_language"}}{{$item_language}}{{if !$smarty.foreach.data_language.last}}, {{/if}}{{/foreach}}<br/>
					Willingness: {{foreach from=$item.willingness item="item_willingness" name="data_willingness"}}{{$item_willingness}}{{if !$smarty.foreach.data_willingness.last}}, {{/if}}{{/foreach}}<br/>
				</div>
			{{/foreach}}
		</div>
	{{else}}
		<div class="text">
			There is no data in this list.
		</div>
	{{/if}}
</div>