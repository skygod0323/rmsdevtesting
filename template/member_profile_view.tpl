{{assign var="member_page_type" value="profile"}}
{{if $smarty.request.type=='fav_videos' || $smarty.request.type=='fav_albums' || $smarty.request.type=='fav_models' || $smarty.request.type=='playlists' || $smarty.request.type=='videos' || $smarty.request.type=='upload_video'}}
	{{assign var="member_page_type" value=$smarty.request.type}}
{{/if}}
 
{{if $member_page_type=='fav_videos'}}
	{{insert name="getBlock" block_id="custom_list_videos" block_name="My Favourite Videos" assign="block_result"}}

	{{if $storage.custom_list_videos_my_favourite_videos.playlist_id>0}}
		{{assign var="page_title" value=$lang.html.memberzone_my_fav_videos_playlist_title|replace_tokens:$storage.custom_list_videos_my_favourite_videos.playlist_info}}
		{{assign var="page_description" value=$lang.html.memberzone_my_fav_videos_playlist_description|replace_tokens:$storage.custom_list_videos_my_favourite_videos.playlist_info}}
		{{assign var="page_keywords" value=$lang.html.memberzone_my_fav_videos_playlist_keywords|replace_tokens:$storage.custom_list_videos_my_favourite_videos.playlist_info}}
		{{assign var="page_canonical" value=$lang.urls.memberzone_my_playlist|replace:"%ID%":$storage.custom_list_videos_my_favourite_videos.playlist_id}}
	{{else}}
		{{assign var="page_title" value=$lang.html.memberzone_my_fav_videos_title}}
		{{assign var="page_description" value=$lang.html.memberzone_my_fav_videos_description}}
		{{assign var="page_keywords" value=$lang.html.memberzone_my_fav_videos_keywords}}
		{{assign var="page_canonical" value=$lang.urls.memberzone_my_fav_videos}}
	{{/if}}

{{elseif $member_page_type=='fav_albums'}}
	{{insert name="getBlock" block_id="list_albums" block_name="My Favourite Albums" assign="block_result"}}

	{{assign var="page_title" value=$lang.html.memberzone_my_fav_albums_title}}
	{{assign var="page_description" value=$lang.html.memberzone_my_fav_albums_description}}
	{{assign var="page_keywords" value=$lang.html.memberzone_my_fav_albums_keywords}}
	{{assign var="page_canonical" value=$lang.urls.memberzone_my_fav_albums}}

{{elseif $member_page_type=='fav_models'}}
	{{insert name="getBlock" block_id="list_members_subscriptions" block_name="My Favourite Models" assign="block_result"}}

	{{assign var="page_title" value=$lang.html.memberzone_my_fav_models_title}}
	{{assign var="page_description" value=$lang.html.memberzone_my_fav_models_description}}
	{{assign var="page_keywords" value=$lang.html.memberzone_my_fav_models_keywords}}
	{{assign var="page_canonical" value=$lang.urls.memberzone_my_fav_models}}

{{elseif $member_page_type=='playlists'}}
	{{insert name="getBlock" block_id="list_playlists" block_name="My Created Playlists" assign="block_result"}}

	{{assign var="page_title" value=$lang.html.memberzone_my_playlists_title}}
	{{assign var="page_description" value=$lang.html.memberzone_my_playlists_description}}
	{{assign var="page_keywords" value=$lang.html.memberzone_my_playlists_keywords}}
	{{assign var="page_canonical" value=$lang.urls.memberzone_my_playlists}}

{{elseif $member_page_type=='upload_video'}}
	{{insert name="getBlock" block_id="video_edit" block_name="Video Edit" assign="block_result"}}

	{{assign var="page_title" value=$lang.html.memberzone_my_video_upload_title}}
	{{assign var="page_description" value=$lang.html.memberzone_my_video_upload_description}}
	{{assign var="page_keywords" value=$lang.html.memberzone_my_video_upload_keywords}}
	{{assign var="page_canonical" value=$lang.urls.memberzone_my_playlists}}

{{elseif $member_page_type=='videos'}}
	{{insert name="getBlock" block_id="list_videos" block_name="My Videos" assign="block_result"}}

	{{assign var="page_title" value=$lang.html.memberzone_my_videos_title}} 
	{{assign var="page_description" value=$lang.html.memberzone_my_videos_description}}
	{{assign var="page_keywords" value=$lang.html.memberzone_my_videos_keywords}}
	{{assign var="page_canonical" value=$lang.urls.memberzone_my_videos}}

{{else}}
	{{assign var="page_title" value=$lang.html.memberzone_my_profile_title}}
	{{assign var="page_description" value=$lang.html.memberzone_my_profile_description}}
	{{assign var="page_keywords" value=$lang.html.memberzone_my_profile_keywords}}
	{{assign var="page_canonical" value=$lang.urls.memberzone_my_profile}}

{{/if}}

{{include file="include_header_general.tpl"}}

	{{if $block_result!=''}}
		{{$block_result|smarty:nodefaults}}
	{{else}}
		<div class="container">
			<h1 class="title">{{$lang.edit_profile.title}}</h1>
			<div class="full-width-box cols cfx">
				<div class="col form_area">
					{{insert name="getBlock" block_id="member_profile_edit" block_name="Edit Profile"}}
				</div>
				<div class="col form_area">
					{{insert name="getBlock" block_id="member_profile_edit" block_name="Edit Password"}}
				</div>
				<div class="col form_area">
					{{insert name="getBlock" block_id="member_profile_edit" block_name="Edit Email"}}
				</div>
			</div>
		</div>
	{{/if}}

{{include file="include_footer_general.tpl"}}