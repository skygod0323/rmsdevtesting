<div class="global-stats">
	<ul>
		{{if $stats.videos_total>0}}
			<li>{{$stats.videos_total}} videos total / {{$stats.videos_total_duration|number_format:1:".":""}} hours / {{$stats.videos_total_uploaded_size}}</li>
		{{/if}}
		{{if $stats.private_videos>0}}
			<li>{{$stats.private_videos}} private videos / {{$stats.private_videos_duration|number_format:1:".":""}} hours / {{$stats.private_videos_uploaded_size}}</li>
		{{/if}}
		{{if $stats.premium_videos>0}}
			<li>{{$stats.premium_videos}} premium videos / {{$stats.premium_videos_duration|number_format:1:".":""}} hours / {{$stats.premium_videos_uploaded_size}}</li>
		{{/if}}
		{{if $stats.videos_today>0}}
			<li>{{$stats.videos_today}} videos added today</li>
		{{/if}}

		{{if $stats.albums_total>0}}
			<li>{{$stats.albums_total}} albums total / {{$stats.albums_images_total}} photos</li>
		{{/if}}
		{{if $stats.private_albums>0}}
			<li>{{$stats.private_albums}} private albums / {{$stats.albums_images_private}} photos</li>
		{{/if}}
		{{if $stats.premium_albums>0}}
			<li>{{$stats.premium_albums}} premium albums / {{$stats.albums_images_premium}} photos</li>
		{{/if}}
		{{if $stats.albums_today>0}}
			<li>{{$stats.albums_today}} albums added today</li>
		{{/if}}

		{{if $stats.members_total>0}}
			<li>{{$stats.members_total}} members total</li>
		{{/if}}
		{{if $stats.members_today>0}}
			<li>{{$stats.members_today}} members joined today</li>
		{{/if}}
		{{if $stats.members_online>0}}
			<li>{{$stats.members_online}} members online</li>
		{{/if}}
		{{if $stats.friends_total>0}}
			<li>{{$stats.friends_total}} friends</li>
		{{/if}}

		{{if $stats.comments_total>0}}
			<li>{{$stats.comments_total}} comments total</li>
		{{/if}}
		{{if $stats.comments_videos>0}}
			<li>{{$stats.comments_videos}} comments on videos</li>
		{{/if}}
		{{if $stats.comments_albums>0}}
			<li>{{$stats.comments_albums}} comments on albums</li>
		{{/if}}
		{{if $stats.comments_playlists>0}}
			<li>{{$stats.comments_playlists}} comments on playlists</li>
		{{/if}}
		{{if $stats.comments_cs>0}}
			<li>{{$stats.comments_cs}} comments on sites</li>
		{{/if}}
		{{if $stats.comments_models>0}}
			<li>{{$stats.comments_models}} comments on models</li>
		{{/if}}
		{{if $stats.comments_dvds>0}}
			<li>{{$stats.comments_dvds}} comments on channels</li>
		{{/if}}

		{{if $stats.playlists>0}}
			<li>{{$stats.playlists}} public playlists</li>
		{{/if}}
		{{if $stats.videos_bookmarks>0}}
			<li>{{$stats.videos_bookmarks}} bookmarked videos</li>
		{{/if}}
		{{if $stats.albums_bookmarks>0}}
			<li>{{$stats.albums_bookmarks}} bookmarked albums</li>
		{{/if}}

		{{if $stats.content_sources>0}}
			<li>{{$stats.content_sources}} sites</li>
		{{/if}}
		{{if $stats.content_sources_groups>0}}
			<li>{{$stats.content_sources_groups}} site networks</li>
		{{/if}}

		{{if $stats.models>0}}
			<li>{{$stats.models}} models</li>
		{{/if}}
		{{if $stats.models_groups>0}}
			<li>{{$stats.models_groups}} model groups</li>
		{{/if}}

		{{if $stats.dvds>0}}
			<li>{{$stats.dvds}} channels</li>
		{{/if}}
		{{if $stats.dvds_groups>0}}
			<li>{{$stats.dvds_groups}} channel groups</li>
		{{/if}}

		{{if $stats.traffic_yesterday>0}}
			<li>{{$stats.traffic_yesterday}} visitors today</li>
		{{/if}}
		{{if $stats.traffic_week>0}}
			<li>{{$stats.traffic_week}} visitors this week</li>
		{{/if}}
		{{if $stats.traffic_month>0}}
			<li>{{$stats.traffic_month}} visitors this month</li>
		{{/if}}
	</ul>
</div>