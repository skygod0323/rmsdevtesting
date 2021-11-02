<div id="documentation">
	<h1 id="section_quick_start">KVS: the Basics</h1>
	<h2 id="section_quick_start_contents">Table of Contents</h2>
	<div class="contents">
		<a href="#section_benefits" class="l2">Advantages</a><br/>
		<a href="#section_content" class="l2">Storing and Processing Content</a><br/>
		<a href="#section_content_storage" class="l3">Multi-Server Content Storage</a><br/>
		<a href="#section_content_storage_protection_video" class="l3">Protecting Content from Hotlinking and Unauthorized Access</a><br/>
		<a href="#section_content_storage_protection_album" class="l3">Protecting Photos from Unauthorized Access</a><br/>
		<a href="#section_content_conversion_tasks" class="l3">Background Tasks and Their Types</a><br/>
		<a href="#section_content_conversion_pause" class="l3">Pause Mode</a><br/>
		<a href="#section_content_conversion_servers" class="l3">Conversion Servers</a><br/>
		<a href="#section_content_hdd_space" class="l3">Calculating Free Disk Space</a><br/>
		<a href="#section_video_content" class="l2">Video Content</a><br/>
		<a href="#section_video_content_formats" class="l3">Multi-format Videos</a><br/>
		<a href="#section_video_content_formats_comparison" class="l3">Difference Between MP4 and FLV</a><br/>
		<a href="#section_video_content_ffmpeg" class="l3">FFMPEG Video Conversion Options</a><br/>
		<a href="#section_video_content_screenshots" class="l3">Video Screenshots</a><br/>
		<a href="#section_video_content_load_type" class="l3">Video Content Types</a><br/>
		<a href="#section_video_content_source_files" class="l3">Source Files</a><br/>
		<a href="#section_video_content_statuses" class="l3">Video Statuses</a><br/>
		<a href="#section_video_content_manual_adding" class="l3">Adding and Editing Videos Manually</a><br/>
		<a href="#section_video_content_import" class="l3">Mass Video Importing</a><br/>
		<a href="#section_video_content_feeds_import" class="l3">Importing Videos via Feeds</a><br/>
		<a href="#section_video_content_mass_edit" class="l3">Mass Video Editing</a><br/>
		<a href="#section_video_content_manual_screenshots" class="l3">Video Screenshots</a><br/>
		<a href="#section_video_content_export" class="l3">Exporting Video Data</a><br/>
		<a href="#section_video_content_feeds_export" class="l3">Exporting Videos via Feeds</a><br/>
		<a href="#section_album_content" class="l2">Photo Albums</a><br/>
		<a href="#section_album_content_formats" class="l3">Multi-format Photo Albums</a><br/>
		<a href="#section_album_content_source_files" class="l3">Photo Source Files</a><br/>
		<a href="#section_album_content_statuses" class="l3">Photo Album Statuses</a><br/>
		<a href="#section_album_content_adding_and_work" class="l3">Adding and Editing Photo Albums Manually and More</a><br/>
		<a href="#section_album_content_import" class="l3">Mass Photo Album Importing</a><br/>
		<a href="#section_album_content_mass_edit" class="l3">Mass Photo Album Editing</a><br/>
		<a href="#section_album_content_export" class="l3">Exporting Photo Album Data</a><br/>
		<a href="#section_categorization" class="l2">Content Categorization</a><br/>
		<a href="#section_categorization_basic" class="l3">Basic Categorization: Categories and Tags</a><br/>
		<a href="#section_categorization_advanced" class="l3">Content Sources, Models, and DVDs / Channels</a><br/>
		<a href="#section_categorization_flags" class="l3">Flags</a><br/>
		<a href="#section_memberzone" class="l2">Member Area</a><br/>
		<a href="#section_memberzone_intro" class="l3">General Member Area Features</a><br/>
		<a href="#section_memberzone_configuration" class="l3">Setting Up Paid Access</a><br/>
		<a href="#section_memberzone_video" class="l3">Setting Up Video Access</a><br/>
		<a href="#section_memberzone_album" class="l3">Setting Up Photo Album Access</a><br/>
		<a href="#section_memberzone_tokens" class="l3">Using Tokens</a><br/>
		<a href="#section_memberzone_protection" class="l3">Protecting Member Area from Shared Access</a><br/>
		<a href="#section_community" class="l2">Community Features</a><br/>
		<a href="#section_community_intro" class="l3">General Community Features</a><br/>
		<a href="#section_community_members" class="l3">Users and Profiles</a><br/>
		<a href="#section_community_bookmarks" class="l3">User Favorites</a><br/>
		<a href="#section_community_video_upload" class="l3">User Video Uploads</a><br/>
		<a href="#section_community_albums_creation" class="l3">User Photo Albums</a><br/>
		<a href="#section_community_other" class="l3">Miscellaneous</a><br/>
		<a href="#section_rotator" class="l2">Rotator</a><br/>
		<a href="#section_rotator_basic" class="l3">General Rotator Overview</a><br/>
		<a href="#section_rotator_videos" class="l3">Rotating Videos</a><br/>
		<a href="#section_rotator_screenshots" class="l3">Rotating Video Screenshots</a><br/>
		<a href="#section_advanced" class="l2">Advanced Features</a><br/>
		<a href="#section_advanced_clones" class="l3">Building Satellite Sites</a><br/>
		<a href="#section_advanced_embed" class="l3">Using Embed Codes for Your Videos</a><br/>
		<a href="#section_advanced_runtime_params" class="l3">Dynamic HTTP Parameters and Receiving Webmaster Traffic</a><br/>
		<a href="#section_advanced_admin_users" class="l3">Managing Employees / Site Admins</a><br/>
		<a href="#section_advanced_localization" class="l3">Localizing Sites and Content</a><br/>
		<a href="#section_stats" class="l2">Site Statistics</a><br/>
		<a href="#section_stats_traffic" class="l3">Traffic Statistics</a><br/>
		<a href="#section_stats_search" class="l3">Site Search Statistics</a><br/>
		<a href="#section_stats_embed" class="l3">Embed Code Usage Statistics</a><br/>
		<a href="#section_stats_referers" class="l3">Referers or Traffic Sources</a><br/>
		<a href="#section_stats_members" class="l3">Member Area and Community Statistics</a><br/>
		<a href="#section_website_ui" class="l2">Building and Managing Your Site</a><br/>
		<a href="#section_website_ui_concepts" class="l3">General Concepts</a><br/>
		<a href="#section_website_ui_debug" class="l3">Debugging Site Pages</a><br/>
		<a href="#section_performance" class="l2">Performance and Load</a><br/>
		<a href="#section_performance_general" class="l3">General Information</a><br/>
		<a href="#section_performance_overload" class="l3">Preventing Overload</a><br/>
		<a href="#section_performance_monitoring" class="l3">Performance Statistics</a><br/>
		<a href="#section_plugins" class="l2">Plugins</a><br/>
		<a href="#section_first_steps" class="l2">Getting Started</a><br/>
		<a href="#section_troubleshooting" class="l2">What Do I Do If..?</a><br/>
	</div>
	<h2 id="section_benefits">Advantages</h2>
	<p>
		KVS is one of the leading tube site backend scripts and video content managers. We
		honestly think it’s better, and here’s why:
	</p>
	<ul>
		<li>
			KVS was initially built with handling large amounts of visitors in mind. Built-in site performance
			monitoring lets us tackle all arising load issues in a full and timely manner. When needed, we optimize the
			performance on the fly.
		</li>
		<li>
			Unlike other products, KVS is not really limited to just a few site types. You will enjoy using KVS to
			build all sorts of sites, CJ tubes, paysite review sites, tube sites, premium access sites, social sites,
			DVD stores, and more.
		</li>
		<li>
			KVS offers wider customization options than other products. It’s not just about setting up the way your
			site looks, which is standard now. You can easily build a unique site, which nobody has ever built before
			and which will stand out from all competition.
		</li>
		<li>
			Not a single other script offers more content conversion and storage features.
		</li>
		<li>
			KVS lets you localize the entire site and all your content. With this, you can build a fully multi-language
			site where all content can be translated to multiple languages, including database items.
		</li>
		<li>
			KVS is built with open source PHP code. You can be sure you have the independence you need to run your
			business and add custom features.
		</li>
		<li>
			We never stop improving our product in all the ways we can. Your feedback is extremely important, and we
			have already implemented plenty of new features suggested by our clients. You may think the product has too
			many features at first. But the truth is, each and every feature is useful. We hope you will use as many of
			KVS advanced features as possible.
		</li>
	</ul>
	<p>
		This manual describes the basic features of KVS in a simple, easy to grasp way. It also suggests scenarios for
		using the features described.
	</p>
	<h2 id="section_content">Storing and Processing Content</h2>
	<!-- ch_concepts_content_storage(start) -->
	<div>
		<h3 id="section_content_storage">Multi-Server Content Storage</h3>
		<p>
			In most cases, all content is stored on the same server where the script and your site are installed. This
			is the most basic option. It is not perfect though, as content servers need faster hard drives and better
			connectivity. Your primary server, however, is used to serve the pages of your site and possibly convert
			the content; thus, CPU and RAM are more important.
		</p>
		<p>
			If you plan to launch multiple sites on different servers, we recommend storing these sites’ content on the
			same server, which has better connectivity and is set up to serve static content faster. The sites,
			however, will work better on a second server with more processing power and experienced administrators.
		</p>
		<p>
			KVS lets you configure and manage your storage servers easily. This is how the multi-server storage
			architecture works.
		</p>
		<p>
			Every video which requires storing processed video files on disk (see video content types) is assigned to
			the storage server group and stored on each server in this group. In most cases, this group will contain
			only one server, which stores the processed video files. Photo albums are assigned to their storage server
			groups in a similar way.
		</p>
		<p>
			KVS has been developed in such a way that separate groups of servers need to be set up for videos and
			picture albums. Here, you can use the same physical server for both videos and photo albums. However, in
			the KVS admin panel, you will need to create separate storage servers and set up different file system
			locations (paths) on the same physical server.
		</p>
		<p class="important">
			<b>Important!</b> Creating storage servers for videos and photo albums pointing to the same directory in
			the server’s file system is not allowed. It will most likely lead to data loss in the future.
		</p>
		<p>
			You can set up groups with multiple storage servers and balance the load between them. In many cases, you
			will be better off if you balance the load between several servers as opposed to serving all content from
			just one server. You can balance the load between servers in a group either with coefficients or by
			countries. You can set up a CDN system, which serves videos from a US-based server to US-based surfers,
			while other surfers get content from other server or servers. Also, balancing the load by countries can be
			used to serve content from a premium server to surfers from solvent countries and use a regular server for
			other surfers.
		</p>
		<p>
			When you find yourself running out of your primary storage server’s disk space, you can add more servers
			and store new content on these servers. As the content is bound to a group of servers and not to individual
			servers, in this case you will need to set up a new group and add your server(s) to it. When your content
			will be created, the script will automatically select a matching server group with most free disk space.
			This behavior can be customized.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_storage_servers.png" alt="An example of video storage server configuration" width="886" height="195"/><br/>
			<span>An example of video storage server configuration.</span>
		</div>
		<p>
			In this configuration example, the first server group has 1,520 videos and has almost no free space (0.04%
			free space calculated as the least amount among all the servers of the group). In this group, we have 2
			servers and the videos are duplicated on both of the servers. To add the remaining 916 videos, we created a
			new server group and added a server to it.
		</p>
		<p>
			If you want your content to be always added to the same server group (no free space analysis), you can set
			up content options for videos and photo albums which let you configure default server groups. Also, in
			content settings, you will find an option, which sets the minimal threshold for a server group to be
			eligible for storing your content. This option gives you an early warning if you’re running out of disk
			space. In case your free disk space becomes equal to your minimal threshold, your background processing
			tasks will be paused until you add more free space.
		</p>
		<p>
			Apart from the built-in multi-server content storage capabilities, KVS supports storing content on
			virtually any CDN. Public CDN are different in the way that KVS has fewer tasks to complete. In this case,
			the engine will store all files to just one location as opposed to distributing files between servers.
			Here, the CDN software handles everything else.
		</p>
		<p>
			To use CDN features, you will need to copy the CDN management script to <b>/admin/cdn</b> and set up its
			name in the storage server settings, for example, it’s <b>ucnd.php</b>. The CDN management script is
			different from one provider to another. We can supply you with scripts for certain providers. In other
			cases, you will need to either contact the provider’s support department for a script, which would have the
			features you need, or write such a script yourself. Check <b>/admin/tools/cdnapi.php</b> for a sample CDN
			management script. The script needs to have a few features redirecting requests to video and photo album
			files as well as invalidation of the files. Invalidation is usually required, as when files are changed on
			the KVS side, invalidation is needed for the changes to be reflected in the CDN.
		</p>
	</div>
	<!-- ch_concepts_content_storage(end) -->
	<!-- ch_concepts_content_storage_protection_video(start) -->
	<div>
		<h3 id="section_content_storage_protection_video">Protecting Content from Hotlinking and Unauthorized Access</h3>
		<p>
			Protecting your video files from hotlinking is based on two levels of protection both of which are required
			for full-featured protection:
		</p>
		<ul>
			<li>
				The first, or bottom, level is protecting video files from direct downloads. Setting up such protection
				depends on the type of streaming used on your storage server. For storage servers with plain Apache,
				content protection is impossible, and this is one of the reasons we don’t recommend this streaming type
				(there are other reasons as well). When you use CDN, it handles content protection on its own. When you
				use Nginx, the protection uses the X-Accel-Redirect technology. See the KVS settings manual (storage
				servers) for more details on setting up your server level protection.
			</li>
			<li>
				The second, or top, protection level is protecting the video serving script.
			</li>
		</ul>
		<p>
			Thus, for comprehensive video protection, you need to make sure your video files are not available via
			direct links, and the video serving script is protected as well.
		</p>
		<p>
			If you use multiple storage servers, direct link video file protection needs to be set up on each of them.
			Also, each server needs to be checked individually. For full storage server check, you can use the
			<span class="term">Test content serving</span> feature available in the server’s context menu. To have this
			option enabled, you need to have at least one video on this server (the videos are assigned to the server
			group this server belongs to). Ideally, you need to have videos with all the video formats you intend to
			use. When running this check, KVS requests each video file in several modes:
		</p>
		<ul>
			<li>Direct link to file should not work.</li>
			<li>Link to file through the processing script should work.</li>
			<li>For FLV and MP4, streaming should work.</li>
		</ul>
		<p>
			After a check is complete, you will see the results in the <span class="term">Status</span> column. Also,
			when you need, you will be able to see the headers the server returned for each request (the
			<span class="term">Details</span> column). The video formats available only to premium users cannot be
			automatically checked via the serving script. In this case, you will need to manually check the link to the
			serving script and the streaming.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_storage_servers_test_video.png" alt="Checking the operability of a storage server" width="980" height="249"/><br/>
			<span>Checking the operability of a storage server.</span>
		</div>
		<p>
			Protection of the video serving script is enabled in the content settings in the administration panel. It
			takes effect for all the storage servers. Apart from hotlink protection, this feature lets you protect your
			video files from third party grabbing. You can set up maximum serving script request threshold allowed for
			a certain time period, e.g. 100 requests or less for each 5 minutes. You will need to choose this limit in
			such a way that it doesn’t affect regular site users who may sometimes skip through videos making new
			requests to the servers. Also, we do not recommend using time periods longer than 10 minutes.
		</p>
		<p>
			If a video file request attempt triggers hotlink protection or IP restriction, the serving script will
			redirect the request to the video file set in the
			<span class="term">Render file from this URL instead of video</span> field.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_video_protection.png" alt="Protecting the video serving script in content settings" width="631" height="117"/><br/>
			<span>Protecting the video serving script in content settings.</span>
		</div>
		<p>
			In addition to global hotlink protection used for video files for all formats, KVS lets you set up
			protection for individual video formats. Let’s have a closer look at these features. They are very
			important if you are building a site with multi-format videos. You can find these features on the page for
			adding / editing a video format:
		</p>
		<ul>
			<li>
				<span class="term">Access level</span>: lets you choose user types for which the video files of a
				format will be physically available via a link to the video serving script. This option is the main
				option for setting up member areas for premium users. It lets you create video formats, which will be
				available only to such premium users.
			</li>
			<li>
				<span class="term">Enable ability to download</span>: lets you enable downloading of videos of the
				current format outside the Flash player, as well as playback on devices with no Flash support. If you
				enable this option for a video format available to unregistered users, it will make the overall hotlink
				protection for video files of such format weaker.
			</li>
			<li>
				<span class="term">Disable hotlink protection</span>: in some cases, you may need to allow hotlinking
				for files of a certain format, e.g. when you are setting up hosted videos for your affiliates. This
				will turn the 2nd level protection off completely for all video files of the current format. If 1st
				level protection is enabled, direct links to video files will still not be working.
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_video_format_protection.png" alt="Protection options for a video format" width="705" height="213"/><br/>
			<span>Protection options for a video format.</span>
		</div>
		<p>
			Apart from protecting video files on your storage servers, you will need to disable public access to the
			video source files directory. It is always stored on your primary server. In it, apart from source
			screenshots, source video files may also be stored. The directory must be configured as internal directory
			in your Nginx configuration:
		</p>
		<p class="code">
			location ^~ /contents/videos_sources/ {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;root /path/to/domain/root; <span class="comment"># full path to root directory for contents/videos_sources</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disallowing access to the directory via direct links</span><br/>
			}
		</p>
		<p>
			If Nginx is not installed on your primary server, you won’t be able to protect your source file directory.
			To prevent it from unauthorized access, you can rename it, give it a random name, and set up the new path
			in <b>/admin/include/setup.php</b>:
		</p>
		<p class="code">
			$config['content_path_videos_sources']="/path/to/domain/root/contents/videos_sources";
		</p>
		<p>
			To run a final protection check, use the <span class="term">Check content protection</span> feature of the
			Audit plugin; it will check all levels of protection.
		</p>
	</div>
	<!-- ch_concepts_content_storage_protection_video(end) -->
	<!-- ch_concepts_content_storage_protection_album(start) -->
	<div>
		<h3 id="section_content_storage_protection_album">Protecting Photos from Unauthorized Access</h3>
		<p>
			Links to photos in albums can be either direct or protected. Direct links are links to image files
			directly. Direct links should be enabled only for formats available to all users. Using direct links for
			such formats lets you decrease Apache load.
		</p>
		<p>
			Protected links are intercepted by the serving script (similar to video files) that either allows or
			disallows displaying the photos depending on the format settings and user status. Use protected links for
			formats available to premium users only. In default templates, protected links are used only for larger
			images; for previews, direct links are used to decrease server load.
		</p>
		<p>
			Check out an example of both direct and protected links:
		</p>
		<p class="code">
			<span class="comment"># direct link leads to the file itself</span><br/>
			http://domain.com/contents/albums/main/700x525/0/37/225.jpg
			<br/><br/>
			<span class="comment"># protected link is intercepted and processed by the serving script</span><br/>
			http://domain.com/get_image/398b4bd3ff0df7e881db01797b456872/main/700x525/0/37/225.jpg/
		</p>
		<p>
			All photo album files, including formats and source files, are stored on storage servers, so you will need
			to disable direct access to them for each server individually. Your exact protection configuration depends
			on the streaming type used on your storage server. For storage servers powered by pure Apache no protection
			is possible. When you use CDN, it handles content protection on its own. When you use Nginx, the protection
			uses the X-Accel-Redirect technology.
		</p>
		<p>
			Unlike video files stored in the same directory, photos in albums are stored in different directories so
			that you can enable direct access to some formats and disable direct access to others.
		</p>
		<p class="important">
			<b>Important!</b> Direct access must be restricted to the formats you intend to make available to
			registered users only. For these formats, you need to use protected links (protected_link) in the
			templates, instead of direct links (direct_link).
		</p>
		<p>
			Use the settings of a particular file format to set up access levels to files of such format. When a user
			tries to access a file of a format for which privileges are needed through the serving script, they will
			either see the photo, or there will be an error, if they don’t have sufficient privileges. For each
			restricted format, you can upload an image to be used instead of the error message. For example, you may
			want to display an image saying the user needs to register to see the picture they want.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_album_formats_access.png" alt="Setting up access to photo album formats" width="754" height="161"/><br/>
			<span>Setting up access to photo album formats.</span>
		</div>
		<p>
			You can display not just format files but photo source files on your site; you can do that either via
			direct or via protected links, as if these are files of a certain format. By default, all users can access
			the photo source files by direct and by protected links as well. If you don’t want to offer public access
			to such files, you will need to disable direct access to source files on each storage server, and set up
			access levels for source files in the content settings:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_album_sources_access.png" alt="Setting up access to source files of photo albums" width="588" height="87"/><br/>
			<span>Setting up access to source files of photo albums.</span>
		</div>
		<p>
			In Nginx configuration, disabling direct access to a directory with photo source files will look like this:
		</p>
		<p class="code">
			location ^~ /contents/albums/sources/ {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;root /path/to/domain/root; <span class="comment"># full path to the root directory for contents/albums/sources</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disabling directory access by direct links</span><br/>
			}
		</p>
		<p>
			When you need to disable direct access to a certain photo format, you can use a rule like this (800x600 is
			the format’s size):
		</p>
		<p class="code">
			location ^~ /contents/albums/main/800x600/ {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;root /path/to/domain/root; <span class="comment"># full path to the root directory for contents/albums/main/800x600</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disabling directory access by direct links</span><br/>
			}
		</p>
		<p>
			In a similar way as for the videos, you can check whether your photos are protected on your storage server.
			Use the <span class="term">Test content serving</span> feature from the server’s context menu. To have this
			option enabled, you need to have at least one photo album on this server. When running this check, KVS
			sends requests to a photo in multiple modes:
		</p>
		<ul>
			<li>
				Direct link to the photo should not work if the format is disabled for unregistered users. If the
				format is public, the direct link should work.
			</li>
			<li>
				Direct link to ZIP archive should not work if the format is disabled for unregistered users. If the
				format is public, the link should work.
			</li>
			<li>The link to the photo via the serving script should work.</li>
			<li>The link to the ZIP archive through the serving script should work.</li>
		</ul>
		<p>
			After a check is complete, you will see the results in the <span class="term">Status</span> column. Also,
			when you need, you will be able to see the headers the server returned for each request (the
			<span class="term">Details</span> column). The photo album formats available only to registered users
			cannot be automatically checked via the serving script. In this case, you will need to manually check the
			link to the serving script and see that it’s working.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_storage_servers_test_album.png" alt="Checking the operability of a storage server" width="978" height="245"/><br/>
			<span>Checking the operability of a storage server.</span>
		</div>
		<p>
			To run a final photo protection check, use the <span class="term">Check content protection</span> feature
			of the Audit plugin; it will check all levels of protection.
		</p>
	</div>
	<!-- ch_concepts_content_storage_protection_album(end) -->
	<!-- ch_concepts_content_conversion_tasks(start) -->
	<div>
		<h3 id="section_content_conversion_tasks" class="l3">Background Tasks and Their Types</h3>
		<p>
			All content processing is done in the background. When you add new videos or photo albums or do a lot of
			other things, the script creates tasks that are queued. On your primary server, the script uses a schedule
			to manage queued tasks. The primary server handles some tasks right away while other tasks are assigned to
			conversion servers for content processing.
		</p>
		<p>
			For resource-heavy background tasks on the primary server, you can customize the priority of any task by
			using the <span class="term">Background tasks priority</span> option that you can find in the content
			settings. You can also customize the priority for tasks on any individual conversion server in a similar
			way.
		</p>
		<p>
			In KVS, tasks can have various types. Conversion servers handle some tasks, mostly processing of video and
			photo files, as well. For most tasks, however, conversion servers are not used as the primary server
			handles these tasks. There are various task types that can take a while to be completed, up to several
			days. During this time, other background processes are frozen. For example, this happens when new formats
			of screenshots and photo albums are created; a task goes through all the videos and photo albums and
			creates (or rewrites) image files. It’s not hard to imagine that if you have lots of videos and photo
			albums, this task may take a long time. For your convenience, KVS shows the progress of such tasks in
			percent right in the task list that you can find in the admin panel.
		</p>
		<p>
			When a task is successfully completed, it is removed from the task list and appears in task log. If there
			was an error, the task stays there with a special ‘error’ status. All tasks have a log that you can check
			by using the task list’s context menu. Most errors are related to video conversions (ffmpeg errors, error
			code 07). When such errors occur, you need to check the task’s log file for the ffmpeg library errors. As a
			rule, these errors occur because of a faulty ffmpeg build and/or incorrect ffmpeg options in the video
			format settings. Another class of errors that occur frequently is connection errors occurring when
			connecting to remote conversion or storage servers (error codes 02 and 04). Most such errors are temporary
			and should not reoccur when you relaunch the task. If an error seems to be permanent, it means the
			connection between servers is faulty. In this case, KVS will most likely display an error message on the
			starting page.
		</p>
		<p>
			You can relaunch any task that wasn’t completed because of an error. Do this only after you fix what caused
			the error, for example, faulty FTP connection, incorrect ffmpeg settings for a particular format etc.
			Otherwise the error will most likely reoccur.
		</p>
		<p>
			If a task in the list is highlighted in orange, it means the task cannot be completed now but can be
			completed in the future once a temporary error is fixed. Some of these errors may be random and may go away
			by themselves. You can get details about a particular error by moving your mouse over the task’s name in the
			table. The most frequent error here is inability to connect to storage servers to distribute content. In
			this case, the conversion engine won’t be able to complete the task until the connection to the storage
			servers is restored.
		</p>
	</div>
	<!-- ch_concepts_content_conversion_tasks(end) -->
	<!-- ch_concepts_content_conversion_pause(start) -->
	<div>
		<h3 id="section_content_conversion_pause">Pause Mode</h3>
		<p>
			Tasks are started within a minute from the moment they were added to the queue. Sometimes you may need to
			pause the conversion engine. For example, you want to adjust some video format settings, or settings for
			screenshots or photo albums. The engine does not allow these operations to run when there are background
			tasks to handle. It means that you may need to wait until all current background tasks are complete.
			Sometimes it can take a long time, and this is why you may want to use the pause mode.
		</p>
		<p>
			You can pause the conversion engine by going to the content settings. The option is called
			<span class="term">Enable pause for background tasks</span>. As soon as you enable this option, the engine
			will start going into pause mode. It won’t happen right away; the engine will need to complete the running
			tasks first. It’s only after that it will be able to go into pause mode, which means no new tasks will be
			launched. In the administration panel’s header and on the start page you will see prompts about paused
			tasks. Starting from this moment, you will be able to make changes to video formats, screenshots, and photo
			albums. After you’re done making changes, you will need to disable the pause mode so that the tasks from
			the queue resume.
		</p>
	</div>
	<!-- ch_concepts_content_conversion_pause(end) -->
	<!-- ch_concepts_content_conversion_servers(start) -->
	<div>
		<h3 id="section_content_conversion_servers">Conversion Servers</h3>
		<p class="important">
			<b>Important!</b> Remote conversion servers are not available in the basic and second KVS packages. Only
			current server can be used as a conversion server in these packages.
		</p>
		<p>
			KVS lets you set up any number of conversion servers to be used simultaneously to tackle the most
			resource-heavy content processing tasks. Go to administration panel settings to manage your conversion
			servers.
		</p>
		<p>
			To set up a server as a conversion server, you need to copy the processing script on it first
			(<b>/admin/tools/remote_cron.php</b>). You also need to set this script as a cron task. When script runs
			for the first time, it will attempt to detect paths to all required libraries automatically. A file called
			<b>config.properties</b> will be created where all paths will be listed. If certain libraries are not
			installed or you want to use libraries with different paths, you will need to edit this file.
		</p>
		<p class="important">
			<b>Important!</b> In server settings, you can specify the maximum possible number of tasks to be assigned
			to a conversion server. All tasks within a conversion server are tackled one after another. If you want one
			physical server to run multiple conversion processes automatically, you will need to create a second
			conversion server in the KVS administration panel, and point it to a different directory with another
			<b>remote_cron.php</b> script called as a cron task.
		</p>
	</div>
	<!-- ch_concepts_content_conversion_servers(end) -->
	<!-- ch_concepts_content_hdd_space(start) -->
	<div>
		<h3 id="section_content_hdd_space">Calculating Free Disk Space</h3>
		<p>
			You need free disk space to store content on storage servers as well as for processing content on the
			primary server. To avoid any potential problems related to insufficient free space, KVS has a range of
			settings that let you address this issue proactively and pause content processing when necessary. Check
			content settings for these:
		</p>
		<ul>
			<li>
				<span class="term">Minimum free disc space for primary server</span>: this sets minimal free space
				threshold for the primary server. When this threshold is reached, KVS pauses mass content import tasks
				as well as processing import feeds as these tasks can store large amount of data (source files) on the
				primary server before processing and distribution to storage servers. We recommend using a value that
				is 10-20 times greater than your average source file which you mass import. Also, remember that the
				site and KVS itself need free space to run without errors. Check the header of your administration page
				for your current free space left, shown in real time.
			</li>
			<li>
				<span class="term">Minimum free disc space for storage server group</span>: this sets minimal free
				space threshold for storage servers. When this threshold is reached, KVS pauses background tasks that
				store files on this storage server. We recommend using a value that is 10-20 times greater than all
				files of an average video or a photo album.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_content_hdd_space(end) -->
	<h2 id="section_video_content">Video Content</h2>
	<!-- ch_concepts_video_content_formats(start) -->
	<div>
		<h3 id="section_video_content_formats">Multi-format Videos</h3>
		<p class="important">
			<b>Important!</b> Multi-format videos are not available in the basic package. Instead of a video format
			list, you will see a settings page for the single format used.
		</p>
		<p>
			KVS lets you set up a list of various video formats with different settings. You may need multi-format
			videos for such tasks as:
		</p>
		<ul>
			<li>
				Showing lower resolution videos to mobile users.
			</li>
			<li>
				Showing videos in several quality modes.
			</li>
			<li>
				Letting your users download video files of several formats, MP4, MPG, MOV etc.
			</li>
			<li>
				Showing shorter and/or lower quality videos to unregistered users and requiring registration for full
				videos in good quality.
			</li>
			<li>
				Limit download speed for free videos.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> Multi-format videos are only possible for the types of video content that feature storage
			on your servers (see Video Content Types, <span class="term">File Upload</span> and
			<span class="term">Video Gallery</span>).
		</p>
		<p>
			You can manage video formats in the settings. There are 2 groups of video formats; each of them lets you
			manage videos of certain type. There are several video types in KVS: standard videos (public and private),
			and premium videos. There is no difference between standard and premium video formats. All these videos are
			stored and shown in the same way. When you set up various aspects of your member area, you can make certain
			formats of premium videos available only to privileged site users. You can also set up standard formats in
			the same way. So, you should treat these format groups in the same way. It is just a way to split videos
			into 2 groups with different sets of files.
		</p>
		<p>
			You can create formats which will be required for each videos (files of these formats will be created by
			the conversion engine unless you upload such files manually), as well as optional formats which you will
			upload manually only for some videos. This behavior is set up by adjusting a format’s status:
		</p>
		<ul>
			<li>
				<span class="term">Disabled</span>: a file of this format won’t be created when a new video us uploaded,
				and a file of this format cannot be uploaded manually. You may need to use this status if you don’t
				intend to use an obsolete format any more but at the same time you don’t want to delete files of this
				format for videos that already have it. Set this status for a format to keep files of this format for
				existing videos that already had it. It won’t be used for new videos from now on.
			</li>
			<li>
				<span class="term">Active required</span>: files of this format will be created for all uploaded
				videos. If you are creating a new format with such status and you already have videos in the system,
				for videos that don’t have files of such format files will be automatically created. Thus, after this
				task is completed, you can be sure you have files of this format for all your videos.
			</li>
			<li>
				<span class="term">Active optional</span>: files of this format won’t be created automatically and no
				files of such format will be created for existing videos. This status lets you upload files of a format
				manually for any videos, when needed. Thus, for some videos, you may not have files of such format.
			</li>
			<li>
				<span class="term">Removing files</span>: files of such format are now being deleted for all videos.
				If you want to remove a format, files won’t be deleted right away. The format status will be set to
				<span class="term">Removing files</span> and the format will be removed only after all files of such
				format will be deleted from all storage servers.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> When we say all videos, we mean all videos of a certain type, standard or premium. As a
			format is only applicable to one video type, it means that adding a new required format for premium videos
			will lead to automatic creation of files of such format only for premium videos, provided the system has
			such videos.
		</p>
		<p>
			When you set up a format list for a certain video type, you always need to create at least one
			<span class="term">Active required</span> format. It means that any video has at least 1 video file. When
			you need to add more files of new formats, either source file (if it was saved) or the file of a format
			flagged as source or a file of a format with the largest file size will be used as source. See a special
			section for more information on video source files.
		</p>
		<p class="important">
			<b>Important!</b> When you add a new <span class="term">Active required</span> format, you need to
			understand that conversion tasks will be added for all existing videos (of a certain type). This operation
			can take days and even weeks, depending on how many videos you have and what are the settings and the
			performance of your conversion servers. We recommend creating new formats as
			<span class="term">Active optional</span> in order not to waste any time. Then, you can use mass video
			editing to create files of this format for a few test videos. If the files were created successfully and
			the resulting files look good, you can set the format status to <span class="term">Active required</span>
			and wait until all new files are created.
		</p>
		<p>
			There is something important you need to know about <span class="term">Active optional</span> formats. You
			can set up a format as conditionally optional. In this case, files of this format will be created
			automatically, but only when source video duration and file size allow for this. For instance, you want
			videos in 3 sizes, SD, HD, and Full HD. Let’s imagine only some of your source files allow for creating of
			full HD videos. You need to set this format’s status to conditionally optional and then files of this
			format will be created only when it’s possible.
		</p>
		<p>
			Conditionally optional formats will be created only when you add new videos. If you already have uploaded
			videos and want files of your optional format to be added for the videos that allow for it, use mass
			editing to manually launch creating of files of such format.
		</p>
		<p>
			There are certain video format settings that can be overlapped by other settings for various videos. These
			are watermarks and duration limits in seconds. KVS lets you assign these aspects to custom fields of a
			video content source (sponsor). This was done so that video files from different content sources can
			have different duration limits or different watermarks, which you specify in custom fields in content
			source settings (enable custom fields in customization settings). If a video is not assigned to any
			content sources, default format settings will be used.
		</p>
		<p>
			Timeline screenshots are also assigned to video formats. These may be enabled for each format individually.
			See further for more details on video screenshots.
		</p>
		<p>
			Let’s have a look at a basic sample video format configuration that we’ll also use later on. Our imaginary
			site will contain standard videos in low and good quality as well as premium videos in HD quality (MP4 and
			WMV). It will also have short 1-minute MP4 trailers. WMV will be optional and we’ll upload files of this
			format manually. For now, we won’t address access restrictions. Let’s suppose all videos and formats are
			available to all users.
		</p>
		<p>
			Here is a structure of our formats:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_video_formats.png" alt="Structure of video formats" width="776" height="209"/><br/>
			<span>Structure of video formats.</span>
		</div>
	</div>
	<!-- ch_concepts_video_content_formats(end) -->
	<!-- ch_concepts_video_content_formats_comparison(start) -->
	<div>
		<h3 id="section_video_content_formats_comparison">Difference Between MP4 and FLV</h3>
		<p>
			Even though MP4 and FLV formats are created using the same h264 codec, there are substantial differences in
			the way these formats are displayed during client playback.
		</p>
		<ul>
			<li>
				MP4 files are playable by all modern mobile devices while FLV files play only via Flash player. Some
				older devices, like iPhone 3 or Android v1, cannot play MP4 files which were encoded without a
				baseline profile (ffmpeg –profile baseline option). This option makes a video require less processing
				power to play.
			</li>
			<li>
				FLV files start playing faster; the longer the video, the more obvious the difference. This also goes
				for skipping through a video.
			</li>
		</ul>
		<p>
			To choose a format correctly, you need to know the approximate predicted duration of your videos. If you
			will mostly have clips 5, 10, or 20 minutes long, you don’t even need to consider using FLV. If you will
			mostly feature full length videos, 60 minutes and longer, you may want to use 2 formats, FLV for player and
			MP4 for playback on mobile devices. KVS settings let you specify a format to be played on devices without
			Flash support. Here, you’ll have yet another advantage of multi-format videos. You can encode MP4 files
			with a baseline profile and with lower resolution. It will make these videos play on some older devices. In
			any case, before you make your final choice, you can create several formats and upload test videos to see
			how they play on different devices.
		</p>
	</div>
	<!-- ch_concepts_video_content_ffmpeg(start) -->
	<div>
		<h3 id="section_video_content_ffmpeg">FFMPEG Video Conversion Options</h3>
		<p>
			The KVS engine uses FFmpeg, an open source library, as a foundation for its conversion engine. Today, this
			library offers the best stability, features, and compatibility. FFmpeg options are set up separately for
			each video format; these are fully customizable. The default video option string looks like this:
		</p>
		<p class="code">
			-vcodec libx264 -movflags +faststart -threads 0 -r 25 -g 50 -crf 25 -me_method hex -trellis 0 -bf 8 -acodec aac -strict -2 -ar 44100 -ab 128k -f mp4
		</p>
		<p>
			Let’s have a look at the most important options:
		</p>
		<ul>
			<li>
				The <b>-vcodec libx264</b> parameter specifies the codec used to encode the output file. Currently h264
				is the most widely used codec which is the default one.
			</li>
			<li>
				The <b>-r 25</b> parameter sets the frame rate to 25 frames per second.
			</li>
			<li>
				The <b>-crf 25</b> parameter sets the video quality factor (the less this value, the higher the
				quality). When you use this parameter, the videos will be encoded with permanent quality and dynamic
				bitrate. It means that video fragments where quality loss is not critical (motion pieces etc) will be
				encoded with lower quality. At the same time, fragments where quality loss would be noticeable will
				have higher quality and higher bitrate. A possible disadvantage here is that you can’t predict file
				size and requires a few test iterations before you establish values that work best for you. We
				recommend setting this parameter to a value between 20 and 35.
			</li>
			<li>
				The <b>-acodec libfaac</b> parameter sets the audio codec used to encode the output file. The codecs
				most popular now are libfaac (AAC) and libmp3lame (LAME). We recommend that you go with AAC.
			</li>
			<li>
				The <b>-ar 44100</b> parameter sets sampling rate for the audio stream (44,100 Hz, Audio CD quality).
			</li>
			<li>
				The <b>-ab 128k</b> parameter sets the audio stream bitrate (128 kbps).
			</li>
			<li>
				The <b>-f mp4</b> parameter sets which container format should be used for the output file. If you need
				to create FLV files, set this to -f flv.
			</li>
		</ul>
		<p>
			You can get more information on the FFmpeg options on the
			<a href="http://www.ffmpeg.org/ffmpeg-doc.html">library’s official website</a>. Check
			<a href="http://mewiki.project357.com/wiki/X264_Settings">here</a> for h264 options and quality presents.
		</p>
	</div>
	<!-- ch_concepts_video_content_ffmpeg(end) -->
	<!-- ch_concepts_video_content_screenshots(start) -->
	<div>
		<h3 id="section_video_content_screenshots">Video Screenshots</h3>
		<p>
			In KVS, video screenshots are subdivided into 2 major groups: primary, or overview screenshots, and
			timeline screenshots. Primary screenshots are created based on source videos and are meant primarily for
			display in various areas of your site. These can also be used when reimporting videos to other systems
			where images of different sizes may be needed. Timeline screenshots are created for each video format
			separately (provided they’re enabled for this format). These are meant mostly for in-player display on the
			site. They are shown to a user as they move the mouse across the playback progress bar. You can also use
			them in other contexts as needed.
		</p>
		<p>
			Similar to video files, video screenshots also support multiple formats. Multi-format support is offered
			for primary as well as for timeline screenshots. For each format, you can customize the size, an
			ImageMagick string with processing options, and set a watermark and its position. In most cases, you will
			be using formats to create screenshots of different sizes so that they work in different parts of your
			site’s layout. When you need to change the creation options for a format (let’s say you want to apply a
			watermark), you can launch a re-creation process for all screenshots of this format for all the videos by
			using a corresponding option in the screenshot format’s context menu.
		</p>
		<p>
			KVS doesn’t have a fixed number of screenshots, primary as well as timeline, which means different videos
			can have different number of screenshots. Within a single video, there will be the same number of
			screenshots of different formats. Think of formats as of different ways to showcase the same screenshots.
		</p>
		<p>
			Starting number of overview screenshots is set up in the content settings and has a few variations:
		</p>
		<ul>
			<li>
				Fixed number of screenshots for all videos. For example, if you set this to <b>5</b>, 5 screenshots
				will be created for each video. Also, interval between screenshots will be calculated based on video
				duration.
			</li>
			<li>
				Screenshot capture interval, in seconds. For example, if you set this to <b>10</b>, for each video, a
				screenshot will be created every 10 seconds. Thus, logically, for videos of different duration,
				different number of screenshots will be created.
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_screenshots_count.png" alt="Setting screenshot number in content settings" width="798" height="118"/><br/>
			<span>Setting screenshot number in content settings.</span>
		</div>
		<p>
			Let us repeat this: despite the fact that you can set the original number of screenshots as fixed, you can
			delete any number of screenshots from any video at any time. For example, if you want each video to always
			have 5 good screenshots, we recommend setting a value slightly larger, like 10. Often screenshots are taken
			at moments that are not the best for this purpose. If you do so, you will be able to delete the 5
			screenshots you don’t like from the 10 that were created.
		</p>
		<p>
			In order to set up timeline screenshots and their number, you need to enable timeline screenshots for video
			formats you want to have them. When enabling them, you will be asked to set the path where the timeline
			screenshots for this format will be saved. Also, you will need to specify the screenshot taking interval.
			Thus, chosen video formats will have timeline screenshots taken at the interval you set up, and these will
			be saved to the directory you specified.
		</p>
		<p class="important">
			<b>Important!</b> For timeline screenshots, you will need to set directory name, not path to it. Choose
			directory name based on the video format. For instance, for the <b>Premium MP4 HQ</b> format, you may want
			to set <b>premium_mp4_hq</b>, and so on.
		</p>
		<p>
			Let us go back to our sample video format structure we talked about in the previous chapter. Here, we will
			have a look at how timeline screenshots work here. Let’s suppose we want to enable timeline screenshots
			only for premium videos only for those formats that are shown on the site in the player:
		</p>
		<div class="table">
			<table>
				<tr class="header">
					<td>Name</td>
					<td>Postfix</td>
					<td>Timeline screenshots enabled?</td>
					<td>Directory</td>
					<td>Interval</td>
				</tr>
				<tr class="header">
					<td colspan="5">Standard videos</td>
				</tr>
				<tr>
					<td>MP4 LQ</td>
					<td>.mp4</td>
					<td>no</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>MP4 HQ</td>
					<td>_hq.mp4</td>
					<td>no</td>
					<td></td>
					<td></td>
				</tr>
				<tr class="header">
					<td colspan="5">Premium videos</td>
				</tr>
				<tr>
					<td>Premium MP4</td>
					<td>_premium.mp4</td>
					<td>yes</td>
					<td>premium_mp4</td>
					<td>10 seconds</td>
				</tr>
				<tr>
					<td>Premium WMV</td>
					<td>_premium.wmv</td>
					<td>no</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>Premium Trailer MP4</td>
					<td>_premium_trailer.mp4</td>
					<td>yes</td>
					<td>premium_trailer_mp4</td>
					<td>10 seconds</td>
				</tr>
			</table>
		</div>
		<p>
			After you enable timeline screenshots for the formats you need, you can add timeline screenshot formats.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_screenshots_formats.png" alt="Screenshot formats" width="886" height="180"/><br/>
			<span>Screenshot formats.</span>
		</div>
		<p>
			Each screenshot format has a special setting which lets you set up the correlation between the aspect ratio
			of a video to the aspect ratio of screenshots of this formats. There are 2 ways you can use this:
		</p>
		<ul>
			<li>
				<span class="term">Preserve source video aspect ratio</span>: in this case, the image captured from a
				video file will be reduced to its biggest size possible, and centered on a black background. It’s
				obvious that if the aspect ratio of the video is different from that of the screenshot, the screenshot
				will have black bars on the edges.
			</li>
			<li>
				<span class="term">Adjust to the required aspect ratio for screenshots of this format</span>: in this
				case the image captured from a video file will be cropped from the sides required to obtain the
				necessary aspect ratio. No black bars here.
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_screenshots_aspect_ratio.png" alt="A comparison of different aspect ratio adaptation options" width="670" height="260"/><br/>
			<span>A comparison of different aspect ratio adaptation options.</span>
		</div>
		<p class="important">
			<b>Important!</b> Processing screenshots which have been uploaded manually is different from processing
			screenshots created automatically. For screenshots uploaded manually, there’s no cropping and a separate
			ImageMagick option line is used. All other operations, such as aspect ratio adaptation and watermarking are
			similar. So, when you upload screenshots manually, you need to do the cropping yourself. You may need to
			change ImageMagick options used for manually uploaded screenshots. No improvements are made by default,
			only resizing.
		</p>
	</div>
	<!-- ch_concepts_video_content_screenshots(end) -->
	<!-- ch_concepts_video_content_load_type(start) -->
	<div>
		<h3 id="section_video_content_load_type">Video Content Types</h3>
		<p>
			As of today, KVS supports 5 video content types which have different ways of processing and storage:
		</p>
		<ul>
			<li>
				<span class="term">File upload</span> (multi-format supported). You upload the source video file (when
				desired, and/or files of different formats) from your local drive or from anywhere on a server (you
				need to specify URLs to files). When needed, the script creates all active required and conditionally
				optional video formats from the source files. Also, screenshots are created. After that, files of all
				formats are saved on all the servers of the group to which this video was assigned when being created.
				Let us repeat this, when you use <span class="term">File upload</span>, the video is stored on your
				server.
			</li>
			<li>
				<span class="term">Hosted file</span> (multi-format not supported). You specify the link to a file
				stored somewhere else, not on your server. KVS can temporarily download the video to check its
				duration or make screenshots. Thus, when you hotlink, you don’t store videos on your server. The player
				requests the link specified and plays back the video from this link.
			</li>
			<li>
				<span class="term">Embed code</span> (multi-format not supported). You specify the embed code for the
				player with the video. If you have the link to the video file played with this embed code, you can
				specify the link as well. KVS can temporarily download the video to check its duration or make
				screenshots. If there’s no direct link to the video, you will need to specify the duration manually,
				and upload the screenshots as well. Otherwise you won’t be able to create a video of this type. In most
				cases you will have the direct link to the video if you have the embed code. Similar to
				<span class="term">Hosted file</span>, videos are not stored on your servers. On your site, instead of
				the KVS player, the embed code specified is shown.
			</li>
			<li>
				<span class="term">Gallery URL</span> (multi-format supported). You specify the link to a gallery with
				a video. Here, a gallery is a page that features a player with a single video, or multiple player
				instances with parts of a video. The gallery is parsed, and all video files are detected. These are
				downloaded and merged into a single video. This single video is then converted to all active required
				and conditionally optional formats, and screenshots are created. This is similar to uploading a file
				manually. Such files are stored like <span class="term">File uploads</span>, i.e. on your servers.
			</li>
			<li>
				<span class="term">Pseudo video</span> (multi-format not supported). You specify an outgoing link where
				the user will be sent after they click the video on your site. If you have a link to the video file
				corresponding to this outgoing link, you can specify it as well. In this case, KVS can temporarily
				download the video to check its duration or make screenshots. If there’s no direct link to the video,
				you will need to specify the duration manually, and upload the screenshots as well. Otherwise you won’t
				be able to create a video of this type.
			</li>
		</ul>
		<p>
			So, multiple formats are supported for those types of video content that are stored on your server. These
			are <span class="term">File uploads</span> and <span class="term">Gallery URLs</span>. To use these content
			types on your site, you will need to have a group of servers set up with at least 1 server in it.
		</p>
		<p>
			When you add videos manually, first, you select the type of content, and then you fill in the fields
			depending on the type chosen. When you mass import video content, the type is selected automatically based
			on the options specified (more on this in the Import chapter).
		</p>
		<p class="important">
			<b>Important!</b> <span class="term">Gallery URLs</span> are supported only for mass adding (importing).
		</p>
	</div>
	<!-- ch_concepts_video_content_load_type(end) -->
	<!-- ch_concepts_video_content_source_files(start) -->
	<div>
		<h3 id="section_video_content_source_files">Source Files</h3>
		<p>
			For different types of video content, there will be different source files:
		</p>
		<ul>
			<li>
				<span class="term">File upload</span>: one of the files uploaded. The source will be used to detect
				video duration (unless you have previously set up detecting duration by a certain format in the
				settings) as well as create all required video formats and overview screenshots.
			</li>
			<li>
				<span class="term">Hosted file</span>: the file downloaded from the hotlink URL. The source will be
				used to create overview screenshots.
			</li>
			<li>
				<span class="term">Embed code</span>: the file downloaded from the URL, if such was specified when
				creating a video.
			</li>
			<li>
				<span class="term">Gallery URL</span>: the file combined from the video parts found on the gallery
				page. The source will be used to detect duration (unless you have previously set up detecting duration
				by a certain format in the settings) as well as create all required video formats and overview
				screenshots.
			</li>
			<li>
				<span class="term">Pseudo video</span>: the file downloaded from the URL, if such was specified when
				creating a video. The source will be used to create overview screenshots.
			</li>
		</ul>
		<p>
			Before we address strategies for storing source files, let us separate content types into 3 groups. First
			group will be content types with no multi-format support (<span class="term">Hosted file</span>,
			<span class="term">Embed code</span>, and <span class="term">Pseudo video</span>). Second group will be
			<span class="term">Gallery URL</span>, and <span class="term">File upload</span> will be third.
		</p>
		<p>
			The types of content with no multi-format support (<span class="term">Hosted file</span>,
			<span class="term">Embed code</span>, and <span class="term">Pseudo video</span>) let you store your source
			files on the primary server only. Saving source files for videos of these types may be required only in one
			case: to have an opportunity to create new overview screenshots manually. When you create a video, overview
			screenshots are generated automatically using the settings you specified. As this is automated, there’s no
			guarantee these screenshots will look good. In some cases you may want to create new screenshots to find
			better frames for some of the screenshots. To do that, you can have KVS re-download the files using the
			URLs, or use the existing source file, if such file was saved for the video. Source files saved in advance
			let you avoid waiting for KVS to download the video, which may take a while. Also, in some cases, the link
			to the site may be dead already. In this case, you will need to manually update the link to make sure it
			works, or not create new screenshots for this video at all.
		</p>
		<p>
			Concluding all that has been said, saving source files for these video content types simplifies creating
			new screenshots a lot. On the other hand, files saved on your primary server will take up disk space.
			However, this won’t be a major issue for you as KVS lets you delete source files either for individual
			videos or through mass deletion at any time.
		</p>
		<p class="important">
			<b>Important!</b> By default, source files are not saved. You can enable saving source files in the content
			settings.
		</p>
		<p>
			Just like previous types of content, <span class="term">Gallery URL</span> let you store your source files
			on your primary server only. The primary difference here is that when there is no source file available,
			you won’t be to download it on the fly and create new screenshots. In addition to creating new screenshots,
			source files can also be used to create new video formats. If there are no source files available, an
			existing format will be used to create new formats. This will lead to duplicate encoding; first, the source
			file will have been encoded into one of the formats and then this encoded format will be used to create
			another encoded format. It means that if the source file originally had better quality, bigger duration, or
			larger image size, these aspects will be lost.
		</p>
		<p>
			For videos that have the <span class="term">File upload</span> type, there are several different strategies
			for saving source files. Each of these has its advantages and disadvantages:
		</p>
		<ul>
			<li>
				Storing source files in the primary server. This is the same strategy we addressed when having a look
				at other content types. To store your source file on the primary server when creating a video, you need
				to upload the source file into a special source file field, not into a field of a format. The
				advantages here are faster video processing as well as increased speed for any other type of processing
				which require source files (manually creating new overview screenshots, creating any new video
				formats). Source files are stored on the primary server, so you don’t need to copy them between the
				primary and the storage servers. Hence faster processing. A possible disadvantage here can be the
				inability to offer source files to users and using up more disk space on the primary server.
			</li>
			<li>
				If your source files are also one of the video formats which you intend to feature on your site (not
				just let them play through the player but also offer them as downloads, for example), you can store
				such source files on storage servers as files of this format. To do that, you will need to create the
				format in the settings and upload the source files via the field of this format, as opposed to the
				source file field. In this case, if needed, all other video formats will be created based on the file
				uploaded. If you upload multiple files at the same time (for example, when converting videos on a local
				machine), you need to tick the format you want to use as source files in the video format settings.
					<span class="important">
						<b>Important!</b> When you upload your source file as one of the formats, the file will be saved as
						uploaded without any conversion. It means that no video format settings will be applied to it, e.g.
						watermarks, duration limit, size settings.
					</span>
				The advantages of this strategy are that you can show source files on the site as files of a format,
				you use less primary server disk space, and you do not encode the videos. The main disadvantage,
				however, would be uploading the file ‘as is.’ If you need files of this format to be resized or have a
				watermark, this is not your preferred strategy.
			</li>
			<li>
				If you want to store source files on storage servers but at the same time you don’t want to load them
				as either format, you need to create a ‘fake’ format and upload them as files of this format. This
				‘fake’ format has to be optional, source file option needs to be enabled for it, the format can have any
				postfix, e.g. <b>.dat</b>. You can use any string for ffmpeg options because no files of this format
				will ever be created automatically. Optional status will also let you delete files of this format for
				any video, if needed. When creating new videos you need to upload source files to this format’s field.
				This strategy has certain disadvantages: additional source file copying is required between storage
				servers and your primary server, and source files cannot be displayed on the site. However, the main
				advantage is that you save a lot of primary server disk space.
			</li>
		</ul>
		<p>
			So, source files of all video content type can be stored on your primary server. To do this, you need to
			enable the corresponding option in content settings. Also, when you are creating
			<span class="term">File upload</span> type videos, you will need to upload the source file into the special
			source file field. Additionally, for videos of the <span class="term">File upload</span> type, there are a
			few other strategies which let you store source files on storage servers thus saving up extra primary
			server disk space.
		</p>
		<p>
			In some cases, you may need to have the video duration (displayed on the site) taken not from the source
			file but from one of the video formats. For instance, you have source files that are 1 hour long, but you
			want to use them to create and show 3-minute trailers. If you took your video duration from your source
			files, it would be 1 hour for all videos, which is incorrect in our case. For such situations, you have the
			<span class="term">Take video duration from</span> option in content settings. It lets you choose which
			video format the video duration will be taken from.
		</p>
	</div>
	<!-- ch_concepts_video_content_source_files(end) -->
	<!-- ch_concepts_video_content_statuses(start) -->
	<div>
		<h3 id="section_video_content_statuses">Video Statuses</h3>
		<p>
			A video can have one of these 5 statuses:
		</p>
		<ul>
			<li>
				<span class="term">In process</span>: the video is queued for processing or being processed right now.
				A video has this status right after it’s added to the database.
			</li>
			<li>
				<span class="term">Error</span>: there was an error during video processing. Check the processing log
				for this video to see the error details (see context menu for a video to see the list).
			</li>
			<li>
				<span class="term">Active</span>: the video is available on the site. You need to take into account
				that an active video will show on the site starting from the time set in the video publishing date.
				Publishing date is the date when an active video starts showing on the site.
			</li>
			<li>
				<span class="term">Disabled</span>: the video is not available on the site and is not shown in any
				lists. Direct link to video page works.
			</li>
			<li>
				<span class="term">Deleting</span>: the video is queued for deletion. Deleting videos is a background
				task. After all video data will be deleted (video files, screenshots, and database records), the video
				will be removed forever.
			</li>
		</ul>
		<p>
			You can only edit videos that are active or disabled.
		</p>
	</div>
	<!-- ch_concepts_video_content_statuses(end) -->
	<!-- ch_concepts_video_content_manual_adding(start) -->
	<div>
		<h3 id="section_video_content_manual_adding">Adding and Editing Videos Manually</h3>
		<p>
			You can manually add videos from the <span class="term">Video</span> section of the admin panel (see the
			sidebar menu for the link). When you add videos manually, you can add video content of all types except
			<span class="term">Gallery URLs</span> that are meant for mass adding only.
		</p>
		<p>
			If you don’t want to fill in the video title field (for example, this is the job of your content writers,
			together with video description), you need to set the status to <span class="term">Disabled</span>. Then,
			the content writer will be able to set the filter to disabled videos, go through the list and fill in the
			required fields, setting the status of each processed video to active at the same time. Remember, your site
			shows only videos with their status set to <span class="term">Active</span>.
		</p>
		<p>
			The <span class="term">Directory</span> field is used to create SE friendly links to video pages. This is a
			service field and virtually all site objects have it. Usually it is left blank and its value is
			automatically generated as soon as the object gets a title (the title is used to generate this field’s
			value). If you are launching a Russian language site, for example, you will need transliteration of your
			directory names (as the names will be in Cyrillic). To enable transliteration, make sure that the
			transliteration flag was enabled in the script’s configuration file <b>/admin/include/setup.php</b>:
		</p>
		<p class="code">
			$config['is_translit_directories']="true";
		</p>
		<p>
			The transliteration logic is handled by the <b>/admin/include/translit.php</b> file and is adapted for the
			Russian language. If you need transliteration from a different language, you are welcome to edit this file.
			Use an editor that understands the UTF-8 encoding otherwise the file may stop functioning after you save
			your changes. If you don’t know which editor to choose, you can download and use Notepad2 for Windows for
			free.
		</p>
		<p>
			You can set up the way the <span class="term">Directory</span> field works in content settings. By default
			this field is editable, but the directory name is not changed even if you change the video name. If you
			want a new directory to be generated from a new video name, you need to clear the
			<span class="term">Directory</span> field and save the video. If you want the
			<span class="term">Directory</span> to change whenever the name is changed, you need to enable the
			<span class="term">Re-generate directories automatically</span> option in content settings. After your
			enable it, the <span class="term">Directory</span> will change automatically whenever you change the video
			name.
		</p>
		<p class="important">
			<b>Important!</b> The video directory is used when building links to the video viewing page. When you
			change the directory, the links will also change. However, old links will stay active and have 301
			redirect to new links, if they use numeric video IDs. You can adjust the way video viewing page links look
			in website settings. By default these contain numeric IDs. If you for some reason don’t want to use numeric
			IDs in links, we do not recommend changing directories for active videos.
		</p>
		<p>
			KVS supports delayed video publishing on the site. The date and time of publishing are set in the
			<span class="term">Published on</span> field which may either be set to current date, or to any date in
			future or past. Use this field to plan ahead and build a schedule of video publishing by day, e.g.
			publishing 10 videos every day. You don’t need to add 10 videos every day manually. All you need to do is
			add all the videos together and set the <span class="term">Published on</span> in the way you need.
			Publishing time (time of day on the chosen date) is also important. For example, if
			<span class="term">Published on</span> is set to January 1st, 2010, and the time is set to 18:50, the video
			will be published right after the server clock (shown in administration panel) hits 18:50 on January 1st,
			2010. See content settings for the option which lets you set the default publishing time for added videos
			(random time, current server time, or 00:00).
		</p>
		<p class="important">
			<b>Important!</b> One of the most common questions users have is why they don’t see videos which are active
			and the publishing date is today. They forget about the time. The videos will be shown right after the time
			set in the <span class="term">Published on</span> field.
		</p>
		<p>
			Video <span class="term">type</span> (public, private, and premium) let you use different access levels for
			your videos. Let us remind you that each video type has its own set of video formats (public and private
			are standard types with one and the same set of formats available for them). If for a video type no formats
			are defined, you won’t be able to add videos of this type. Classifying videos according to access level
			lets you organize a premium access zone on your website. You can set different access levels for your
			videos and let users with different account types access different formats. So, if a user doesn’t have
			access to any of the formats of a video type, they won’t see any videos. Using different video types makes
			little sense for those who use the basic packages of KVS to power their sites; these don’t support user
			registration and logging on. Private videos are used in communities: by default, private videos are only
			shown to friends. You can adjust this behavior through a few simple manipulations with the
			<b>video_view</b> block’s template.
		</p>
		<p>
			The <span class="term">Content source</span> field lets you assign a video to any site being promoted, like
			an affiliate program’s paysite, or to any other ad-like content or entity. Content sources are created in
			the administration panel’s categorization area. When you create a content source, you will need to enter
			its name and your referral link. If you want, you can also use a content source image to be shown in
			default templates above the video player as a banner, enter a description, and upload up to 10 ad files.
			You can use all these to display ads inside the player (set this up in player settings in administration
			panel) as well as modify the <b>video_view</b> block to display any additional ads in any way you may want
			to.
		</p>
		<p class="important">
			<b>Important!</b> We strongly recommend using content sources to assign videos to ads. In addition to
			displaying ads on the video viewing page, you will be able to offer paysite reviews with ratings, build
			link catalogs and more. KVS lets you do all that; see further for more details.
		</p>
		<p>
			Let us note here that you have access to statistics covering all clicks on links and/or ads leading to your
			content sources. This includes in-player clicks and page clicks as well. See the Statistics section on
			more details of how to analyze users going to content source sites.
		</p>
		<p>
			The <span class="term">DVD / Channel</span> field is used to assign a video to a particular DVD / channel
			as one of the scenes of this DVD / channel. When you assign a video in this way, you can show all scenes
			of a DVD / channel on the page for this DVD / channel. DVDs / Channels are available in the full package of
			KVS only.
		</p>
		<p>
			The <span class="term">User</span> field lets you set which site user this video will be posted from. When
			KVS is installed, a user called Admin is created, which will be the default user for adding videos. You can
			change the default user in the KVS content settings.
		</p>
		<p>
			Depending on the content type you selected (<span class="term">Content type</span> field), you need
			to fill in various content-related fields:
		</p>
		<ul>
			<li>
				<span class="term">File upload</span>. For this content type, you need to upload either the
				<span class="term">Source file</span>, or one of the formats that will be used as source. Files of all
				<span class="term">Active required</span> formats will be created by the engine from the uploaded file.
				Additionally, you can upload files of any formats. If you do so, the files of these formats won’t be
				created (except for files of FLV and MP4, the streaming formats, these will be uploaded to conversion
				servers to prepare them for streaming). If you process your videos locally, you can manually upload all
				required format files and do not use the conversion engine at all (except for streaming preparation).
				If you need, you can also upload primary screenshot or all screenshots as ZIP.
				<span class="important">
					<b>Important!</b> When you upload files of formats manually, the script cannot check whether the
					file being uploaded is a valid file of this format, e.g. if the file is a valid MPG file. Please
					always make sure you upload valid and correct files into the required fields.
				</span>
				When editing a video of the <span class="term">File upload</span> type, you can re-upload its source
				file to have all video formats or preview screenshots re-created.
			</li>
			<li>
				<span class="term">Hosted file</span>. With hotlinking, you will need to enter the URL to the file
				which you will hotlink. On the site, this URL will be sent to the player.
			</li>
			<li>
				<span class="term">Embed code</span>. You need to enter the embed code which will be displayed on the
				video viewing page. Then, you can use either strategy from these two. Strategy one: you can enter the
				direct link to the video file so that video duration is calculated and screenshots are created based on
				this file. Strategy two: instead of entering the link, you can enter the duration and upload the
				screenshots manually.
			</li>
			<li>
				<span class="term">Pseudo video</span>. You need to specify the URL your visitors will be sent to. As
				with <span class="term">Embed codes</span>, you can either enter the link to the video file, or enter
				the duration and upload the screenshot(s) manually.
			</li>
		</ul>
		<p>
			Uploading screenshots manually when creating a video is something we need to address separately. You can
			upload either one JPG image, or a group of JPG images in a ZIP archive. Then, the processing of uploaded
			data will be defined by your screenshot number settings:
		</p>
		<ul>
			<li>
				If your options are set in such a way that only 1 screenshot is created for each video, and you upload
				1 file, no more screenshots will be created. The file you uploaded will become this one and only
				screenshot.
			</li>
			<li>
				If your options are set in such a way that multiple screenshots are created for each video, and you
				upload 1 file, remaining screenshots will be created automatically.
			</li>
			<li>
				If you upload a ZIP file with any number of images in it, all screenshots in the archive will be used
				as video screenshot, i.e. no screenshots will be created automatically.
			</li>
		</ul>
		<p>
			Several categorization fields let you select categories, tags and models to be assigned to the video you
			are uploading. Start typing and KVS will prompt you to choose between available variants. You can also copy
			and paste lists separated by commas.
		</p>
		<p>
			In addition to main fields, videos also support 3 custom text fields that you can enable in the
			customization in the settings. You can use these for your own purposes, not just within the administration
			panel, but on the site as well.
		</p>
	</div>
	<!-- ch_concepts_video_content_manual_adding(end) -->
	<!-- ch_concepts_video_content_import(start) -->
	<div>
	<h3 id="section_video_content_import">Mass Video Importing</h3>
	<p>
		Unlike manual video adding, mass import lets you create hundreds of videos in shortest possible time. In
		addition to saving time, mass import also gives you several extra features:
	</p>
	<ul>
		<li>
			You can specify a list of users the videos will be posted from. For each video, a user will be randomly
			selected from the list.
		</li>
		<li>
			You can specify a date interval within which the videos will appear on the site
			(<span class="term">Published on</span>). For each video, a random date from within the range will be
			selected.
		</li>
		<li>
			Instead of a date interval, you can also set a number of dates in the future which will define the date
			interval for random date assignment (1 day – just today, 2 days – today and tomorrow, etc). The
			advantage here is that you don’t need to enter new dates with each new import.
		</li>
		<li>
			Video galleries are supported only for importing.
		</li>
	</ul>
	<p>
		Mass video import lets you use files with data of any format. You can specify what line and field
		separators will be used by the importing script to process data. Here, data are lines (each line has to
		contain data related to a single video) and columns (each column has to contain data of one of the video’
		s fields). Depending on the columns that your video data has, you need to specify the list of fields for
		importing in exactly the same order. Even though the importing page shows only 5 slots for selecting the
		fields in the beginning, new slots will be added as you will be selecting them.
	</p>
	<p>
		This table contains details and examples of all import fields:
	</p>
	<div class="table">
	<table>
	<colgroup>
		<col width="20%"/>
		<col/>
		<col width="350"/>
	</colgroup>
	<tr class="header">
		<td>Field</td>
		<td>Description</td>
		<td>Example</td>
	</tr>
	<tr>
		<td>Video ID</td>
		<td>
			Unique video ID; in most cases you don’t need to use this field.
		</td>
		<td>18725</td>
	</tr>
	<tr>
		<td>Title</td>
		<td>
			Video title. If a video with this title already exists in the database, there will be a warning related
			to this video when you run a check. In import settings, you can enable
			<span class="term">Skip videos with duplicate titles</span> that will make videos with existing names
			generate errors instead of warnings. Thus, they will be skipped during import.
		</td>
		<td>This is a demo video</td>
	</tr>
	<tr>
		<td>Directory</td>
		<td>
			Video directory is a field based on which SE-friendly links to video pages are generated. In most
			cases, you don’t need to fill it in as by default the directory is generated based on the title.
		</td>
		<td>this-is-a-demo-video</td>
	</tr>
	<tr>
		<td>Description</td>
		<td>
			Video description.
		</td>
		<td>Video description</td>
	</tr>
	<tr>
		<td>Categories</td>
		<td>
			Categories separated by commas. When analyzing categories, KVS also analyzes synonyms defined for
			existing categories. If no category with a particular name and no matching synonym in details of other
			categories were found, this category will be created automatically. When running a check, you will get
			a warning about this. When KVS searches existing categories, the search is not case sensitive.
		</td>
		<td>Auto, Fun, Ads</td>
	</tr>
	<tr>
		<td>Models</td>
		<td>
			Names of models in the video separated by commas. When analyzing models, KVS also analyzes pseudonyms
			defined for existing models. If no model with a particular name and no matching pseudonym in details of
			other models were found, this model will be created automatically. When running a check, you will get a
			warning about this. When KVS searches existing models, the search is not case sensitive.
		</td>
		<td>Brad Pitt, Kis Cole</td>
	</tr>
	<tr>
		<td>Tags</td>
		<td>
			Video tags separated by commas. Tags are always created as needed.
		</td>
		<td>auto, cars, advertising, fun, brad pitt, kis cole, cool ads</td>
	</tr>
	<tr>
		<td>Content source</td>
		<td>
			Content source (sponsor) name. If no content source with such name was found, it will be created
			automatically and you will see a warning about this when running a check. When KVS searches
			existing content sources, the search is not case sensitive.
		</td>
		<td>YouTube</td>
	</tr>
	<tr>
		<td>DVD / Channel</td>
		<td>
			DVD / Channel name. If no DVD / channel with such name was found, it will be created automatically and
			you will see a warning about this when running a check. When KVS searches existing DVDs /
			channels, the search is not case sensitive.
		</td>
		<td>Funny Videos Channel</td>
	</tr>
	<tr>
		<td>Source video file</td>
		<td>
			Link to the video file that should be downloaded and used as the source file to create all video
			formats and overview screenshots, if needed. If this link is specified, the video will have the
			<span class="term">File upload</span> type.
		</td>
		<td>http://domain.com/videos_for_import/1643.wmv</td>
	</tr>
	<tr>
		<td>Video file "XXX"</td>
		<td>
			Link to the video file that should be downloaded and used as a file of XXX format. Similar fields are
			available for all video formats. You can use them as alternative to the source video file or in
			addition to it (see the section about source files). If this link is specified, the video will have the
			<span class="term">File upload</span> type.
		</td>
		<td>http://domain.com/videos_for_import/1643.flv</td>
	</tr>
	<tr>
		<td>Hosted file URL</td>
		<td>
			Link to the video file that will be hotlinked. If the link is specified, the video will have the
			<span class="term">Hosted file</span> type.
		</td>
		<td>http://domain.com/public_videos/1876.flv</td>
	</tr>
	<tr>
		<td>Embed code</td>
		<td>
			Embed code of a third party player with the video. If this field is filled in, the video will have the
			<span class="term">Embed code</span> type. When choosing this field you will also need to either
			specify a direct link to the video file in the <span class="term">Hosted file URL</span> field, or set
			the video duration and add screenshot(s) in any supported way.
		</td>
		<td>&lt;embed&gt;...&lt;/embed&gt;</td>
	</tr>
	<tr>
		<td>Gallery URL</td>
		<td>
			Link to the video page that contains either a player with the video, or links to multiple video files.
			If this field is filled in, the video will have the <span class="term">Gallery URL</span> type.
		</td>
		<td>http://domain.com/videos/video-page.html</td>
	</tr>
	<tr>
		<td>Outgoing URL</td>
		<td>
			Link to the video page which will be used to redirect visitors. If this field is filled in, the video
			will have the <span class="term">Pseudo video</span> type. When selecting this field, you will also
			need to either specify a direct link to the video file in the <span class="term">Hosted file URL</span>
			field, or set the video duration and add screenshot(s) in any supported way.
		</td>
		<td>http://domain.com/videos/video-page.html</td>
	</tr>
	<tr>
		<td>Duration</td>
		<td>
			Video duration in the HH:MM:SS format, or MM:SS, or in seconds.
		</td>
		<td>1:20:43</td>
	</tr>
	<tr>
		<td>Published on</td>
		<td>
			Date / time when the video is published on the site. The date is in the YYYY-MM-DD HH:MM or YYYY-MM-DD
			formats.
		</td>
		<td>2012-12-24 14:52</td>
	</tr>
	<tr>
		<td>Relative publishing date</td>
		<td>
			This field is only available when relative publishing dates are enabled in the settings (there is more
			info on these in the respective section). This is the number of days before (for negative values) or
			after registration (positive values) when the video will appear on the site for current user.
		</td>
		<td>-30 or 30</td>
	</tr>
	<tr>
		<td>Rating</td>
		<td>
			Video rating, an integer number from 1 to 10.
		</td>
		<td>4</td>
	</tr>
	<tr>
		<td>User</td>
		<td>
			Name (login) of the user who is adding the video. If there is no user with such name, they will be
			created automatically.
		</td>
		<td>admin</td>
	</tr>
	<tr>
		<td>Status</td>
		<td>
			Video status, one of the following: active or disabled.
		</td>
		<td>active</td>
	</tr>
	<tr>
		<td>Type</td>
		<td>
			Video type, one of the following: public, private or premium.
		</td>
		<td>public</td>
	</tr>
	<tr>
		<td>Price in tokens</td>
		<td>
			This field is available starting from the advanced KVS package. Number of tokens a user needs to spend
			to get premium access to this video.
		</td>
		<td>100</td>
	</tr>
	<tr>
		<td>Administrator flag</td>
		<td>
			Name of a video flag to be set for the video as the administrator flag. Flags to be used in this field
			are configured in categorization settings.
		</td>
		<td>Admin Flag</td>
	</tr>
	<tr>
		<td>Main screenshot number</td>
		<td>
			Number of the main video screenshot among all screenshots for this video.
		</td>
		<td>3</td>
	</tr>
	<tr>
		<td>Main screenshot</td>
		<td>
			Link to the source file of the main video screenshot.
		</td>
		<td>http://domain.com/videos_for_import/1643.jpg</td>
	</tr>
	<tr>
		<td>Overview screenshots</td>
		<td>
			Link to the ZIP archive with source files of overview screenshots, or direct links to source files
			separated by commas.
		</td>
		<td>http://domain.com/videos_for_import/1643.zip</td>
	</tr>
	</table>
	</div>
	<p class="important">
		<b>Important!</b> If you upload files for separate video formats using the
		<span class="term">Video file XXX</span> field, you need to take into account that the importing engine
		will process only the fields of those formats which correspond to the video type (standard or premium).
		Video type is specified in the <span class="term">Type</span> field. For standard videos (public or
		private), the importing engine will process only the <span class="term">Video file XXX</span> fields for
		standard formats. Premium videos are treated in a similar way.
	</p>
	<p>
		The easiest way to compile import data is fill in all the fields in a table by using a spreadsheet
		application like MS Excel. One line is one video, one column is one field in the video’s properties. After
		you fill in the data, you need to select all the cells of the table and copy them into the text field on
		the import page. Use the line break symbol (\r\n for Windows and \n for Unix) as a
		<span class="term">Line separator</span> and the tab symbol (\t) as a
		<span class="term">Field separator</span>. After this, the only thing you need to do is select the
		<span class="term">Fields for import</span> in the right order and specify all the additional options you
		may need.
	</p>
	<div class="screenshot">
		<img src="docs/screenshots/quick_start_import_data.png" alt="Example of import data" width="891" height="123"/><br/>
		<span>Example of import data. Column names are given as example and you don’t need to copy them.</span>
	</div>
	<div class="screenshot">
		<img src="docs/screenshots/quick_start_import_fields.png" alt="Selected data fields" width="891" height="184"/><br/>
		<span>Selected data fields.</span>
	</div>
	<p>
		KVS lets you save import patterns to be used after on. To do that, you need to enter the pattern’s name in
		the <span class="term">create new pattern</span> field and specify if it’s the default pattern when needed.
		After the import starts, the fields and options you have selected will be saved as pattern with the
		specified name. Later on, you can select a pattern saved before and don't go through all the fields again.
		After the import starts, all changes in the settings will be saved in the pattern you have selected.
	</p>
	<p>
		If a pattern is marked as default, this pattern will be selected automatically each time you go to the
		import page.
	</p>
	<p class="important">
		<b>Important!</b> When you select an existing pattern, remember to adjust the publishing date interval that will
		always contain the values you entered when you last used this pattern. If you don’t do that, all content
		will be imported to the same dates as the last time. If you used days in the future instead of a fixed date
		interval, you don’t need to do this as dates are relative to the current date in this case.
	</p>
	<p>
		As many affiliate programs let you export their data in different formats, we recommend creating import
		patterns for each affiliate program that offers this feature. This is much easier than adjusting data
		exported from different affiliate programs to your default KVS import pattern.
	</p>
	<p>
		After you set all the options and import settings, they are pre-processed and checked. This is a background
		task that may take a while to complete. Most time-consuming tasks are checking URLs and galleries. You will
		be able to see the progress of this check in %. After the check is complete, you will be taken to a page
		with results that will list all errors, warnings and notifications. If you are not happy with the results
		of the check, you can return to the import page and adjust your settings and data.
	</p>
	<p>
		This preliminary check can generate error messages of various types:
	</p>
	<ul>
		<li>
			<span class="term">Error</span>: a line with video data contains a critical error and cannot be
			processed. You can go back to fix the line, or you can continue importing, and this line will be
			skipped.
		</li>
		<li>
			<span class="term">Warning</span>: a line with video data possibly contains a potential error. Some
			examples of warnings are ‘video with this name already exists’, ‘category does not exist and will be
			created’, etc. Warnings are not a reason to skip the line during importing.
		</li>
		<li>
			<span class="term">Notification</span>: miscellaneous information regarding a line with video data.
		</li>
	</ul>
	<p>
		After you see the preliminary check results and confirm proceeding with importing, the importing will be
		launched. You will be able to see the import progress. Import can take a considerable amount of time, as
		during import the engine downloads all source video files for videos added with
		<span class="term">File upload</span> or <span class="term">Gallery URL</span> as their content type. You
		don’t need to wait till the import is complete. When import is in progress, KVS creates a special type of
		background task. You can see the progress of this task in % if you go to the list of background tasks. This
		task has special priority and is run as a process separate from the conversion engine. This was done to let
		the conversion engine process videos that have been already downloaded as importing continues. If you would
		like to suspend importing for any reason, just delete the background task. To see which videos have been
		added already, you can check the importing task log in the background tasks log list of the administration
		section.
	</p>
	<p>
		Data of each import are available in import log list of the administration section.
	</p>
	<p>
		As we have described before, when importing, the engine will download all video source files to your
		primary server and create individual background tasks in the task queue to convert each video. If you are
		importing a large batch of content at once, you may face a shortage of free disk space on your server as a
		lot of content will be waiting to be converted. To deal with this, see the
		<span class="term">Minimum free disc space for primary server</span> option in the content settings. When
		the threshold specified here is reached, the importing will be paused until there is free space available,
		after which it will resume its operation.
	</p>
	</div>
	<!-- ch_concepts_video_content_import(end) -->
	<!-- ch_concepts_video_content_feeds_import(start) -->
	<div>
		<h3 id="section_video_content_feeds_import">Importing Videos via Feeds</h3>
		<p>
			Import feeds let you fully automate adding content to your site. Unlike mass video importing which is
			essentially half-manual, feeds don’t need your attention to work. You can use import feeds in the following
			ways:
		</p>
		<ul>
			<li>
				One-time importing of a video database via public feeds supplied by third parties, usually affiliate
				programs. Essentially, this is building your initial content archive.
			</li>
			<li>
				Regular importing of new videos published via public feeds, i.e. updating your content archive.
			</li>
			<li>
				Integration with third party grabber scripts. These grabber scripts are supposed to supply the engine
				with data in one of the supported formats.
			</li>
			<li>
				Integrating your sites into a network where adding content to the primary site leads to publishing this
				content on all other network sites which are powered by the feeds of the primary site.
			</li>
		</ul>
		<p>
			KVS supports the following feed formats:
		</p>
		<ul>
			<li>
				XML-based KVS format.
			</li>
			<li>
				XML-based SmartScripts format.
			</li>
			<li>
				RSS (video galleries).
			</li>
			<li>
				Virtually any CSV format. When choosing this format you will also need to specify the list of columns
				which needs to correspond to the fixed way data is structured in the feed. Also, you will need to
				select the key column based on which the feed processor will filter the videos from this feed that have
				been added before.
			</li>
		</ul>
		<p>
			To create an import feed, you need to specify the feed’s URL with all the options, as well as the interval
			between two consecutive feed imports. Optionally, you can limit the number of videos added per single
			import. If you want your feed to be imported only once, you need to enable the corresponding option. After
			this feed is imported, its status will be set to inactive.
		</p>
		<p>
			The <span class="term">Duplicate prefix</span> field lets you specify a prefix to detect video duplicates.
			Each video sent through the feed has its unique ID (for CSV format, you select the field used as an ID
			yourself). As in different feeds IDs may overlap, KVS uses the duplicate prefix + video ID combo to
			globally identify videos so that videos with the same ID but from different feeds are not treated as
			duplicates. As a rule, different feeds are supposed to have different prefixes. However, in situations when
			several feeds can contain the same videos, you need to set the same duplicate prefixes for them for these
			videos not to be added several times and not to be duplicated in your content archive.
		</p>
		<p class="important">
			<b>Important!</b> When you change duplicate prefix for a feed which was already used to add videos, the
			videos will be re-added if the feed returns them.
		</p>
		<p class="important">
			<b>Important!</b> After you delete videos added via a feed, records of deleted videos will stay in feed
			history and you will never be able to re-add these videos. If you set up the feed incorrectly and had to
			delete the videos added through it at some point, you can change the duplicate prefix for this feed, and
			the feed will re-add all videos.
		</p>
		<p>
			In addition to basic options, import feeds let you set up video adding behavior in great detail, with all
			the multitude of options offered by KVS. For example, you can have the same content source assigned to all
			the videos imported via a feed. You can choose whether your custom screenshots will be created, or feed
			screenshots will be used instead. You can also set up the behavior with which video publishing dates will
			be set. Finally, you can set the video content type (which influences the way videos are stored and
			displayed) for all the videos from a feed. Let’s have a closer look at some of the options.
		</p>
		<p>
			<span class="term">Video adding mode</span> sets the type of video content and storage method for each
			videos added via a feed. These options are supported:
		</p>
		<ul>
			<li>
				<span class="term">Add as embed code</span>: all videos from this feed will be added to your site as
				embed codes. For this option to work, you need the feed to have embed codes for each video. Most feeds
				today don’t offer that, but embed codes may be offered by third party grabbers.
			</li>
			<li>
				<span class="term">Add as pseudo video</span>: all videos from this feed will be added to your site as
				pseudo videos. For this option to work, you need the feed to have links to pages with videos.
			</li>
			<li>
				<span class="term">Hotlink videos (parts are hotlinked as separate videos)</span>: all videos from this
				feed will be added to your site with hotlinks to the video file specified in the feed. If the feed
				contains videos split into several parts, these parts will be added as separate videos. In addition to
				this option you also need to specify the name postfix to be added to names of each of the video parts.
				For instance, if you specify Part %N%, a video from 3 parts will be added as 3 separate videos called
				Video Title Part 1, Video Title Part 2, and Video Title Part 3. Here, Video Title is the video name
				from the feed.
			</li>
			<li>
				<span class="term">Download videos (parts are downloaded as separate videos)</span>: all videos from
				this feed will be added to your site saving the video file from the feed to your server. If the feed
				contains videos split into several parts, all these parts will be added as separate videos. Just like
				with the previous option, you need to specify the name postfix. Also, you need to choose a format for
				saving files to your server. Here, the same rules as with manual video adding apply. If you save a file
				as your source file, the conversion engine will automatically create files of all required formats. If
				you choose to save as one of the video formats, the video file will be saved as the file for this
				format and files of other formats will be created as needed.
			</li>
			<li>
				<span class="term">Download videos (parts are merged in one video)</span>: all videos from this feed
				will be added to your site saving the video file from the feed to your server. If the feed contains
				videos split into several parts, these parts will be merged into one video (similar to processing video
				galleries). Here, you only need to choose the format to save the video files to your server in.
			</li>
			<li>
				<span class="term">Add as video galleries</span>: all web pages from this feed will be processed and
				added as video galleries. If a gallery has multiple video files, these will be merged into one. This
				option is only available for RSS feeds and is the only option available for this format.
			</li>
		</ul>
		<p>
			<span class="term">Video publishing dates</span> lets you adjust the behavior of video publishing in terms
			of dates. As you know, videos are only published when their status is set to active, and they are only
			published when the time and date specified in the <span class="term">Published on</span> field come. The
			<span class="term">Video publishing dates</span> feed option lets you set the publishing date (the time is
			set according to the <span class="term">Publishing time part</span> setting, which can be either randomly
			chosen, taken from current server time, or set to 00:00). This feed setting offers the following options:
		</p>
		<ul>
			<li>
				<span class="term">Use server date</span>: the date of publishing for each video from the feed will be
				set according to the current date, which means the videos will be published within a day (depending on
				the publishing time setting).
			</li>
			<li>
				<span class="term">Use publishing date from feed</span>: the date of publishing of each video from the
				feed will be set according to feed data. For this option to work, you need the feed to contain dates for
				each video.
			</li>
			<li>
				<span class="term">Distribute evenly</span>: lets you publish videos from the feed evenly for a certain
				number of days in the future. When choosing this you will need to specify the number of days during
				which the videos will be added, and maximum videos a day as well (if there will be more videos for some
				of the days, the feed will try to add them the next day and so on until the defined number of days
				ends). Using this option makes more sense for feeds that add new videos to your site regularly, making
				it look as if your site is manually updated on a regular basis.
			</li>
			<li>
				<span class="term">Distribute randomly</span>: lets you distribute videos from the feed across the date
				interval you set (which can be in the past as well as in the future). When choosing this you will need
				to set the date interval. Also, you can optionally set maximum allowed number of videos to be added per
				day. Using this option makes no sense for feeds that update your site periodically; however, it makes
				much more sense for one-time feeds used to build your initial video archive.
			</li>
		</ul>
		<p>
			Let us have a look at a small example of how dates can be set up and feed limits can be adjusted. Let’s
			imagine we have 10 feeds which are launched daily and let you add 5 to 10 new videos each. Let’s also
			imagine we don’t need this many videos and 30 new videos every day is enough. We also want feeds to add
			videos for 10 coming days.
		</p>
		<p>
			As feeds work once a day, it means all feeds can add 50 to 100 videos every day. Initially, we need the
			feeds to add enough videos for 10 coming days, 30 videos a day. To do this, we need to use
			<span class="term">Distribute evenly</span> as a setting for <span class="term">Video publishing dates</span>
			in the settings of each feed. We also set the interval in the future to 10 days, and then we set maximum
			videos per day to 30. Thus, within the following 4-8 days the feeds will add all the videos they have to
			fill in the 10-day interval (we need 300 total videos for coming days).
		</p>
		<p>
			After this point is reached, each day the feeds will add only 30 new videos, as we need to add videos only
			for the most remote day from the 10-day interval. Here we may have a problem: videos will be added from
			some feeds only, e.g. from 3-6 feeds which will be launched first and other feeds won’t be able to add
			anything because of the daily 30-video limit. To take videos from feeds evenly, you can set maximum number
			of videos to be taken from feed per single launch (this field is called
			<span class="term">Videos limit per execution</span>). Thus, when we set 3 as maximum number of videos a
			feed can add, we’ll have 30 new videos taken every day from all 10 feeds.
		</p>
	</div>
	<!-- ch_concepts_video_content_feeds_import(end) -->
	<!-- ch_concepts_video_content_mass_edit(start) -->
	<div>
		<h3 id="section_video_content_mass_edit">Mass Video Editing</h3>
		<p>
			Mass video editing lets you make a wide range of changes to many videos at the same time. Check the list of
			changes you can make below:
		</p>
		<ul>
			<li>
				Change video status, user, price in tokens, content source, or DVD / channel for all selected videos.
			</li>
			<li>
				Make video public and vice versa.
			</li>
			<li>
				Change the publishing date for all selected videos to a random date within a specified range. This
				feature lets you take e.g. 100 oldest videos from your site and have them published again in the
				nearest future. Together with video publishing dates, comment dates and dates of all other related
				events will be also changed.
			</li>
			<li>
				Change rating of all selected videos to a random value within a specified range.
			</li>
			<li>
				Add or delete specified tags, categories, or models for all selected videos.
			</li>
			<li>
				Set the value of a flag to zero for all selected videos.
			</li>
			<li>
				Re-create overview screenshots for selected videos that have source files.
			</li>
			<li>
				Delete source files of all selected videos. Here, we’re talking about source files stored on the
				primary server.
			</li>
			<li>
				Create (re-create) video files of a selected format (works only for types of content which support
				multi-format videos). If you are not storing source files anywhere, the selected format will be created
				based on one of the existing formats, the one marked as <span class="term">Source files</span>, or from
				the format with the biggest file size. If the format being created requires a watermark and files of
				the source format already have a watermark, you may need to enable the
				<span class="term">don't apply watermark</span> to avoid repeated watermarking.
			</li>
			<li>
				Delete video files of a selected format (only for content types which support multi-format videos).
			</li>
			<li>
				Migrate all video files of selected videos from one server group to another. Here, deleting files on
				the servers of the old group will be a queued task and will occur no sooner than in a day. This is
				required for all the pages of the site to have updated video file links, as after physical video file
				deletion all old links will stop working on the servers from the old group.
			</li>
			<li>
				Reset rotator statistics for all selected videos.
			</li>
			<li>
				Process selected videos using post-processing plugins.
			</li>
		</ul>
		<p>
			Mass video editing is launched from group operations in the video list shown in the administration panel.
			You can either tick the videos you need to edit and use <span class="term">Mass edit selected</span>, or
			use <span class="term">Mass edit all videos</span> option.
			<span class="term">Mass edit filtered videos</span> lets you run mass operations only for the videos that
			are currently shown by the list filters.
		</p>
		<p class="important">
			<b>Important!</b> Mass editing is possible only for videos with
			<span class="term">Active</span> and <span class="term">Disabled</span> statuses.
		</p>
	</div>
	<!-- ch_concepts_video_content_mass_edit(end) -->
	<!-- ch_concepts_video_content_manual_screenshots(start) -->
	<div>
		<h3 id="section_video_content_manual_screenshots">Video Screenshots</h3>
		<p>
			After initial processing of the videos, the engine will create all required formats for preview and
			timeline screenshots. Source files of all screenshots will also be saved to be used later to create new
			formats. This is why adding new screenshot formats requires little time and resources.
		</p>
		<p>
			Manual screenshot operations include the following:
		</p>
		<ul>
			<li>
				You can download the ZIP archive with source files of preview or timeline screenshots.
			</li>
			<li>
				You can upload a ZIP archive with screenshots that will fully replace the overview screenshots (same
				number of screenshots not a requirement).
			</li>
			<li>
				You can replace some of the overview screenshots.
			</li>
			<li>
				You can delete any overview screenshots leaving only one screenshot.
			</li>
			<li>
				You can reassign the main overview screenshot status (by default, the first screenshot will always have
				this status unless you selected a different screenshot importing).
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> Manual screenshot-related operations essentially affect source files directly, not
			formats. For example, if you replace the 2nd screenshot, what you actually do is replacing the source file
			of the 2nd screenshot; then, files of all formats for the 2nd screenshot are created. All operations will
			symmetrically affect all formats.
		</p>
		<p>
			If you are not happy with the overview screenshots created after initial video processing, you can use
			manual grabbing. First, you need to select the grabbing source. Here, you have a number of options:
		</p>
		<ul>
			<li>
				Using the existing timeline screenshots. If for a particular video format timeline screenshots have
				already been created, you can select some of these screenshots (in fact, some of their source files) to
				create overview screenshots. This operation does not include any video file prossing and therefore is
				very fast.
			</li>
			<li>
				Using the video source file or a file of a format to create overview screenshots for it. In this case,
				you will need to set the interval with which screenshots will be created. Optionally, you can adjust
				crop settings for this operation.
				<span class="important">
					<b>Important!</b> Interval with which screenshots are taken lets you create several ‘candidates’
					from which you can choose as many overview screenshots as you need later on.
				</span>
			</li>
		</ul>
		<p>
			As source files displayed for you to choose from may actually be large files, KVS lets you set the
			thumbnail size to which all sources will be reduced before display.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_manual_grabbing.png" alt="Manual grabbing settings" width="824" height="169"/><br/>
			<span>Manual grabbing settings. The output will be 3 screenshots from which you can choose any number.</span>
		</div>
	</div>
	<!-- ch_concepts_video_content_manual_screenshots(end) -->
	<!-- ch_concepts_video_content_export(start) -->
	<div>
		<h3 id="section_video_content_export">Exporting Video Data</h3>
		<p>
			Video data export lets you create a text file in the format you need containing any video data. Lots of
			filters are supported to let you export exactly the data you need. In most cases, the resulting text file
			is used for further importing the data to other scripts.
		</p>
	</div>
	<!-- ch_concepts_video_content_export(end) -->
	<!-- ch_concepts_video_content_feeds_export(start) -->
	<div>
		<h3 id="section_video_content_feeds_export">Exporting Videos via Feeds</h3>
		<p>
			Export feeds (available in all KVS packages except basic and 2nd) let you offer public or partially limited
			access to your video database to other webmasters.
		</p>
		<p>
			You can create any number of export feeds with different options. Go to <span class="term">Video</span>
			section of admin panel to do that. After the export feed is activated (the status is
			<span class="term">Active</span>), it will become available via the link that you will be able to see on
			the feed editing page. In extra actions in the export feed list, you will also see options that let you
			open the feed’s main page with feed details and run a test feed query.
		</p>
		<p>
			To offer feed information to your partners, all you need to do is give them the link to the feed (and
			password, when applicable). The password may be needed in case you want to make the feed available to a
			limited list of partners only. You can set the password in feed settings.
		</p>
		<p>
			When sending an empty request to the feed, the user can see the feed documentation page that shows all
			options supported by the feed. Also, custom requests can be sent. The options that can be used are listed
			below:
		</p>
		<ul>
			<li>
				Choosing feed format, KVS or CSV. When CSV is selected, the user can specify a list of custom fields in
				a custom format, along with field separator.
			</li>
			<li>
				Choosing the screenshot format best suited for your users from the overview screenshot formats you
				have.
			</li>
			<li>
				Choosing the standard / premium video formats best suited for your users if the feed gives links for
				hotlinking or temporary links.
			</li>
			<li>
				Making a selection by individual categories, tags, sponsors, and models.
			</li>
			<li>
				Sorting videos in the selection.
			</li>
			<li>
				Specifying limits and starting point for a selection.
			</li>
			<li>
				Selecting videos for past N days.
			</li>
			<li>
				Specifying affiliate ID and getting data with affiliate links.
			</li>
			<li>
				Specifying player skin and width when receiving embed codes.
			</li>
		</ul>
		<p>
			You can set video and content types to be sent by the feed directly in feed settings. If things are clear
			with video types (public, private, premium, and possible combinations), content types need further
			explanation:
		</p>
		<ul>
			<li>
				<span class="term">Website link</span>: choose this content type to have your feed offer videos as
				links to your site. Your partners won’t be able to get embed codes and direct links for hotlinking from
				your feed. They will be able to add the videos to their sites as pseudo videos, and they will also be
				able to use referral links, if the feed supports this.
			</li>
			<li>
				<span class="term">Hotlink</span>: choose this content type to have the feed offer not just links to
				your site, but links to your video files as well, with hotlink support. For this to work for videos
				stored on your server, you need to disable hotlink protection for the formats you want to be
				hotlinkable (see Protecting video files). For example, you have 3 formats for standard videos,
				<b>High Quality MP4</b>, <b>Low Quality MP4</b> and <b>Low Quality FLV</b>. <b>High Quality MP4</b> is
				used only for watching videos on your site while <b>Low Quality MP4</b> and <b>Low Quality FLV</b> have
				hotlink enabled for your partners. If you disable hotlink protection for <b>Low Quality</b> formats,
				the feed will offer hotlink-enabled links. As there are two formats with hotlink enabled, your partners
				will be able to choose the format best for their sites. By default the feed will offer links to the
				first format from the ones available. If your videos added are links to third party video files, these
				links will be offered by the feed as is.
			</li>
			<li>
				<span class="term">Embed</span>: choose this content type to have the feed offer not just links to your
				site, but embed codes for your videos as well. Here, for video files stored on your servers, hotlinking
				will not be possible. See more about the different ways of using embed codes for your videos in the
				chapter covering embed codes.
			</li>
			<li>
				<span class="term">Temporary link</span>: this content type lets you offer temporary links to your
				video files. You may need this if you want to import your content into another system and you need
				working links to download the video files.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> In cases when users of your feed may need to download your video files, you need to set
			up content grabbing protection in a different way. Otherwise the users may face IP limits and inability to
			import some of the content. Here you can choose between 2 options: whitelist the IPs of the servers of your
			partners, or disable IP protection by setting a request limit with a large reserve, e.g. 100,000 requests
			every 5 minutes. See content settings for grabbing protection options.
		</p>
	</div>
	<!-- ch_concepts_video_content_feeds_export(end) -->
	<h2 id="section_album_content">Photo Albums</h2>
	<p>
		Photo albums are supported only by the ultimate package of KVS.
	</p>
	<!-- ch_concepts_album_content_formats(start) -->
	<div>
		<h3 id="section_album_content_formats">Multi-format Photo Albums</h3>
		<p>
			Multi-format support for photo albums is similar to multi-format support of video screenshots. Photo albums
			support 2 groups of formats:
		</p>
		<ul>
			<li>
				<span class="term">Main images group</span>: formats, files of which will be created for all pictures
				of your albums are to be added to this group. Thus, in this group you can create several formats with
				different sizes, e.g. to display reduced thumbnails for every picture, and display full pictures when a
				thumbnail is clicked.
			</li>
			<li>
				<span class="term">Preview group</span>: this group should contain formats, files of which will be
				created only for one main photo. These files are to be displayed in the lists of photo albums. You can
				create several formats for the main photo, for example, to show it in different ways in different
				places of your site.
			</li>
		</ul>
		<p>
			Setting up formats for photo albums is different from setting up formats of video screenshots in several
			ways. First, photo album formats have different options related to image aspect ratios:
		</p>
		<ul>
			<li>
				<span class="term">Preserve source image aspect ratio (fixed size)</span>: this option is similar to
				the option of screenshot formats, when the source image and format image aspect ratios are different
				from each other. In this case, black bars are added to the resulting image. Here, regardless of the
				source photo size, all images of this format will have exactly the size specified.
			</li>
			<li>
				<span class="term">Adjust to the required aspect ratio for images of this format (fixed size)</span>:
				this option is also similar to the option for screenshot formats. When the aspect ratios are different,
				source image will be cropped to fit the aspect ratio required, and no black bars will be added. Thus,
				all images of this format will have exactly the size specified.
			</li>
			<li>
				<span class="term">Preserve source image aspect ratio (dynamic size)</span>: this option lets you set
				maximum image sizes for this format. When creating files of this format, the engine will analyze the
				proportions of the source photograph and reduce it to the width or height required, preserving the
				original aspect ratio. This option lets you get format files with the original proportions intact,
				without black bars on the sides.
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_albums_aspect_ratio.png" alt="A comparison of different ways to adjust the aspect ratio for photo albums" width="900" height="234"/><br/>
			<span>A comparison of different ways to adjust the aspect ratio for photo albums.</span>
		</div>
		<p>
			Second, photo album formats let you set up different access levels. Here, you can allow viewing large
			images only for registered or premium users, while smaller images (thumbnails) will be seen by everyone.
			You can also make only your main photos of the albums (formats from the preview group) available to
			everyone, while main images of all sizes will be available only to privileged users.
		</p>
		<p class="important">
			<b>Important!</b> If you set any non-public access level for any of your formats, you will need to set up
			your server protection in such a way that images of this format will not be available via direct links.
			Such protection is to be set up and checked for each of your storage servers separately. See the section on
			storage servers in the KVS settings manual for more information on server-side protection.
		</p>
	</div>
	<!-- ch_concepts_album_content_formats(end) -->
	<!-- ch_concepts_album_content_source_files(start) -->
	<div>
		<h3 id="section_album_content_source_files">Photo Source Files</h3>
		<p>
			Photo source files, unlike video source files, are always saved. Photo source files are stored on the
			storage servers together with other files of photo albums – however, they are stored in a directory of
			their own. On your site, you can use source files of your photos as if they were one of the main image
			formats. By default, any user has access to photo source files via direct and protected links alike. If you
			don’t want your photo source files to be publicly available, you need to disable direct access to source
			files for each of your storage servers, and set up access level to source files in your content settings:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_album_sources_access.png" alt="Setting up access to photo album source files" width="588" height="87"/><br/>
			<span>Setting up access to photo album source files.</span>
		</div>
	</div>
	<!-- ch_concepts_album_content_source_files(end) -->
	<!-- ch_concepts_album_content_statuses(start) -->
	<div>
		<h3 id="section_album_content_statuses">Photo Album Statuses</h3>
		<p>
			Photo albums can have statuses similar to videos:
		</p>
		<ul>
			<li>
				<span class="term">In process</span>: the photo album is queued for processing or is being processed
				right now. A photo album has this status right after it is added to the database.
			</li>
			<li>
				<span class="term">Error</span>: there was an error while processing the photo album. Check the
				processing log of this photo album for error details (in the list in additional operations for a photo
				album).
			</li>
			<li>
				<span class="term">Active</span>: the photo album is available on the site. You need to take into
				account that an active photo album will show on the site starting from the time set in the photo album
				publishing date. Publishing date is the date when an active photo album starts showing on the site.
			</li>
			<li>
				<span class="term">Disabled</span>: the photo album is not available on the site and not shown in any
				lists. Direct link to the photo album viewing page works.
			</li>
			<li>
				<span class="term">Deleting</span>: the photo album is pending deletion. Deleting photo albums is a
				background task. After all data of a photo album is deleted (images and database records), the photo
				album will be deleted from the database forever.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_album_content_statuses(end) -->
	<!-- ch_concepts_album_content_adding_and_work(start) -->
	<div>
		<h3 id="section_album_content_adding_and_work">Adding and Editing Photo Albums Manually and More</h3>
		<p>
			Go to <span class="term">Albums</span> in the administration panel (link in navigation sidebar) to manually
			add photo albums.
		</p>
		<p>
			If you don’t want to enter a name for the photo album (for example, you want your content writers to enter
			it together with the photo album description), you need to set the status of the album to
			<span class="term">Disabled</span>. Then your content writers will be able to display only disabled albums
			in the list, go through them and fill in the required fields, setting the status of each album to
			<span class="term">Active</span>. Remember, photo albums are shown on the site only when their status is
			<span class="term">Active</span>.
		</p>
		<p>
			The <span class="term">Directory</span> field is used to create SE friendly links to photo albums pages.
			This is a service field and virtually all site objects have it. Usually it is left blank and its value is
			automatically generated as soon as the object gets a title (the title is used to generate this field’s
			value). If you are launching a Russian language site, for example, you will need transliteration of your
			directory names (as the names will be in Cyrillic). To enable transliteration, make sure that the
			transliteration flag was enabled in the script’s configuration file <b>/admin/include/setup.php</b>:
		</p>
		<p class="code">
			$config['is_translit_directories']="true";
		</p>
		<p>
			The transliteration logic is handled by the <b>/admin/include/translit.php</b> file and is adapted for the
			Russian language. If you need transliteration from a different language, you are welcome to edit this file.
			Use an editor that understands the UTF-8 encoding otherwise the file may stop functioning after you save
			your changes. If you don’t know which editor to choose, you can download and use Notepad2 for Windows for
			free.
		</p>
		<p>
			You can set up the way the <span class="term">Directory</span> field works in content settings. By default
			this field is editable, but the directory name is not changed even if you change the name of the photo
			album. If you want a new directory to be generated from a new photo album name, you need to clear the
			<span class="term">Directory</span> field and save the album. If you want the
			<span class="term">Directory</span> to change whenever the name is changed, you need to enable the
			<span class="term">Re-generate directories automatically</span> option in content settings. After your
			enable it, the <span class="term">Directory</span> will change automatically whenever you change the photo
			album name.
		</p>
		<p class="important">
			<b>Important!</b> The photo album directory is used when building links to the photo album viewing page.
			When you change the directory, the links will also change. However, old links will stay active and have
			301 redirect to new links, if they use numeric photo album IDs. You can adjust the way photo album viewing
			page links look in website settings. By default these contain numeric IDs. If you for some reason don’t want
			to use numeric IDs in links, we do not recommend changing directories for active photo albums.
		</p>
		<p>
			KVS supports delayed publishing of photo albums on the site. The date and time of publishing are set in the
			<span class="term">Published on</span> field which may either be set to current date, or to any date in
			future or past. Use this field to plan ahead and build a schedule of photo album publishing by day, e.g.
			publishing 10 photo albums every day. You don’t need to add 10 photo albums every day manually. All you
			need to do is add all the photo albums together and set the <span class="term">Published on</span> in the
			way you need. Publishing time (time of day on the chosen date) is also important. For example, if
			<span class="term">Published on</span> is set to January 1st, 2010, and the time is set to 18:50, the photo
			album will be published right after the server clock (shown in administration panel) hits 18:50 on January
			1st, 2010. See content settings for the option that lets you set the default publishing time for the photo
			albums you add (random time, current server time, or 00:00).
		</p>
		<p class="important">
			<b>Important!</b> One of the most common questions users have is why they don’t see photo albums that are
			active and the publishing date is today. They forget about the time. The photo albums will be shown right
			after the time set in the <span class="term">Published on</span> field.
		</p>
		<p>
			Photo album <span class="term">Type</span> (public, private, and premium) lets you use different access
			levels for your photo albums. Unlike videos, which have several format sets for each video type, photo
			albums of all types have the same set of formats. However, photo album types let you adjust the behavior of
			photo album pages on your site. For instance, you can make premium photo albums available to premium users
			only. Private photo albums are used in communities. By default private photo albums are available to
			friends of a user only. You can adjust this behavior at any time with a few simple tweaks in the photo
			album viewing block template.
		</p>
		<p>
			The <span class="term">Content source</span> field lets you assign a photo album to any site being
			promoted, like an affiliate program’s paysite, or to any other ad-like content or entity. Content sources
			are created in the administration panel’s categorization settings. When you create a content source, you
			will need to enter its name and your referral link. If you want, you can also use a content source image to
			be shown in default templates above the large photo album image as a banner, enter a description, and
			upload up to 10 ad files. You can use all these to display ads in the photo album viewing block to display
			any additional ads in any way you may want to.
		</p>
		<p class="important">
			<b>Important!</b> We strongly recommend using content sources to assign photo albums to ads. In addition to
			displaying ads on the photo album viewing page, you will be able to offer paysite reviews with ratings,
			build link catalogs and more. KVS lets you do all that; see further for more details.
		</p>
		<p>
			Let us note here that you have access to statistics covering all clicks on links and/or ads leading to your
			content sources. See the Statistics section on more details of how to analyze users going to content source
			sites.
		</p>
		<p>
			The <span class="term">User</span> field lets you set which site user this photo album will be posted from.
			When KVS is installed, a user called Admin is created, which will be the default user for adding photo
			albums. You can change the default user in the KVS content settings.
		</p>
		<p>
			Several categorization fields let you choose categories, tags and models assigned to a particular photo
			album. Start typing and KVS will prompt you to choose between available variants. You can also copy and
			paste lists separated by commas.
		</p>
		<p>
			Uploading content is possible via uploading a ZIP file with source images, and by uploading a list of
			images as well. Photo processing is a background task handled by conversion servers.
		</p>
		<p>
			After you have created a photo album, you can manually replace any of the photos in it, remove some photos,
			and upload new ones. Here, processing newly uploaded photos will also be a background task. Photos in KVS
			are independent entities and support different descriptions, different ratings and sets of comments. When a
			photo album is displayed, either on the site or in the administration panel, its rating and comments are
			ratings and comments of individual photos combined.
		</p>
		<p>
			In addition to primary fields, photo albums also feature 3 additional text fields that you can enable in
			customization settings. Go to Settings for these; you can use this for your own purposes, not just in the
			administration panel, but on the site as well.
		</p>
	</div>
	<!-- ch_concepts_album_content_adding_and_work(end) -->
	<!-- ch_concepts_album_content_import(start) -->
	<div>
	<h3 id="section_album_content_import">Mass Photo Album Importing</h3>
	<p>
		Unlike manual adding of photo albums, mass import lets you create hundreds of photo albums in shortest
		possible time. In addition to saving time, mass import also gives you several extra features:
	</p>
	<ul>
		<li>
			You can specify a list of users the photo albums will be posted from. For each photo album, a user will
			be randomly selected from the list.
		</li>
		<li>
			You can specify a date interval within which the photo albums will appear on the site
			(<span class="term">Published on</span>). For each photo album, a random date from within the range will
			be selected.
		</li>
		<li>
			Instead of a date interval, you can also set a number of dates in the future which will define the date
			interval for random date assignment (1 day – just today, 2 days – today and tomorrow, etc). The
			advantage here is that you don’t need to enter new dates with each new import.
		</li>
		<li>
			Photo galleries are supported only for importing.
		</li>
	</ul>
	<p>
		Mass photo album import lets you use files with data of any format. You can specify what line and field
		separators will be used by the importing script to process data. Here, data are lines (each line has to
		contain data related to a single photo album) and columns (each column has to contain data of one of the
		photo album’s fields). Depending on the columns that your photo album data has, you need to specify the
		list of fields for importing in exactly the same order. Even though the importing page shows only 5 slots
		for selecting the fields in the beginning, new slots will be added as you will be selecting them.
	</p>
	<p>
		This table contains details and examples of all import fields:
	</p>
	<div class="table">
		<table>
			<colgroup>
				<col width="20%"/>
				<col/>
				<col width="350"/>
			</colgroup>
			<tr class="header">
				<td>Field</td>
				<td>Description</td>
				<td>Example</td>
			</tr>
			<tr>
				<td>Album ID</td>
				<td>
					Unique photo album ID; in most cases you don’t need to use this field.
				</td>
				<td>18725</td>
			</tr>
			<tr>
				<td>Title</td>
				<td>
					Photo album title. If a photo album with this title already exists in the database, there will be a
					warning related to this photo album when you run a check. In import settings, you can enable
					<span class="term">Skip albums with duplicate titles</span> that will make photo albums with existing
					names generate errors instead of warnings. Thus, they will be skipped during import.
				</td>
				<td>This is a demo album</td>
			</tr>
			<tr>
				<td>Directory</td>
				<td>
					Photo album directory is a field based on which SE-friendly links to photo album pages are generated.
					In most cases, you don’t need to fill it in as by default the directory is generated based on the
					title.
				</td>
				<td>this-is-a-demo-album</td>
			</tr>
			<tr>
				<td>Description</td>
				<td>
					Photo album description.
				</td>
				<td>Album description</td>
			</tr>
			<tr>
				<td>Categories</td>
				<td>
					Categories separated by commas. When analyzing categories, KVS also analyzes synonyms defined for
					existing categories. If no category with a particular name and no matching synonym in details of other
					categories were found, this category will be created automatically. When running a check, you will get
					a warning about this. When KVS searches existing categories, the search is not case sensitive.
				</td>
				<td>Auto, Fun, Ads</td>
			</tr>
			<tr>
				<td>Models</td>
				<td>
					Names of models in the photo album separated by commas. When analyzing models, KVS also analyzes
					pseudonyms defined for existing models. If no model with a particular name and no matching pseudonym in
					details of other models were found, this model will be created automatically. When running a check, you
					will get a warning about this. When KVS searches existing models, the search is not case sensitive.
				</td>
				<td>Brad Pitt, Kis Cole</td>
			</tr>
			<tr>
				<td>Tags</td>
				<td>
					Photo album tags separated by commas. Tags are always created as needed.
				</td>
				<td>auto, cars, advertising, fun, brad pitt, kis cole, cool ads</td>
			</tr>
			<tr>
				<td>Content source</td>
				<td>
					Content source (sponsor) name. If no content source with such name was found, it will be created
					automatically and you will see a warning about this when running a check. When KVS searches
					existing content sources, the search is not case sensitive.
				</td>
				<td>YouTube</td>
			</tr>
			<tr>
				<td>Published on</td>
				<td>
					Date / time when the photo album is published on the site. The date is in the YYYY-MM-DD HH:MM or
					YYYY-MM-DD formats.
				</td>
				<td>2012-12-24 14:52</td>
			</tr>
			<tr>
				<td>Relative publishing date</td>
				<td>
					This field is only available when relative publishing dates are enabled in the settings (there is
					more info on these in the respective section). This is the number of days before (for negative
					values) or after registration (positive values) when the photo album will appear on the site for
					current user.
				</td>
				<td>-30 or 30</td>
			</tr>
			<tr>
				<td>Rating</td>
				<td>
					Photo album rating, an integer number from 1 to 10.
				</td>
				<td>4</td>
			</tr>
			<tr>
				<td>User</td>
				<td>
					Name (login) of the user who is adding the photo album. If there is no user with such name, they will
					be created automatically.
				</td>
				<td>admin</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>
					Photo album status, one of the following: active or disabled.
				</td>
				<td>active</td>
			</tr>
			<tr>
				<td>Type</td>
				<td>
					Photo album type, one of the following: public, private, or premium.
				</td>
				<td>public</td>
			</tr>
			<tr>
				<td>Price in tokens</td>
				<td>
					This field is available starting from the advanced KVS package. Number of tokens a user needs to spend
					to get premium access to this photo album.
				</td>
				<td>100</td>
			</tr>
			<tr>
				<td>Images</td>
				<td>
					Link to the ZIP archive with photo source files, or direct links to source files separated by commas.
				</td>
				<td>http://domain.com/albums_for_import/1643.zip</td>
			</tr>
			<tr>
				<td>Main image number</td>
				<td>
					Number of the main photo among all source photos.
				</td>
				<td>12</td>
			</tr>
			<tr>
				<td>Preview image</td>
				<td>
					Link to the source file of the main photo. Can be uploaded separately and is not related to the main
					set of photos in the album.
				</td>
				<td>http://domain.com/albums_for_import/1643.jpg</td>
			</tr>
			<tr>
				<td>Gallery URL</td>
				<td>
					Link to the photo album page that contains a set of thumbnails with links to images. If this field is
					filled in, the photo album will be created using the downloaded images.
				</td>
				<td>http://domain.com/albums/album-page.html</td>
			</tr>
		</table>
	</div>
	<p>
		The easiest way to compile import data is fill in all the fields in a table by using a spreadsheet
		application like MS Excel. One line is one photo album, one column is one field in the photo album’s
		properties. After you fill in the data, you need to select all the cells of the table and copy them into
		the text field on the import page. Use the line break symbol (\r\n for Windows and \n for Unix) as a
		<span class="term">Line separator</span> and the tab symbol (\t) as a
		<span class="term">Field separator</span>. After this, the only thing you need to do is select the
		<span class="term">Fields for import</span> in the right order and specify all the additional options you
		may need.
	</p>
	<div class="screenshot">
		<img src="docs/screenshots/quick_start_import_data_album.png" alt="Example of import data" width="793" height="94"/><br/>
		<span>Example of import data. Column names are given as example and you don’t need to copy them.</span>
	</div>
	<div class="screenshot">
		<img src="docs/screenshots/quick_start_import_fields_album.png" alt="Selected data fields" width="793" height="145"/><br/>
		<span>Selected data fields.</span>
	</div>
	<p>
		KVS lets you save import patterns to be used after on. To do that, you need to enter the pattern’s name in
		the <span class="term">create new pattern</span> field and specify if it’s the default pattern when needed.
		After the import starts, the fields and options you have selected will be saved as pattern with the
		specified name. Later on, you can select a pattern saved before and don't go through all the fields again.
		After the import starts, all changes in the settings will be saved in the pattern you have selected.
	</p>
	<p>
		If a pattern is marked as default, this pattern will be selected automatically each time you go to the
		import page.
	</p>
	<p class="important">
		<b>Important!</b> When you select an existing pattern, remember to adjust the publishing date interval that will
		always contain the values you entered when you last used this pattern. If you don’t do that, all content
		will be imported to the same dates as the last time. If you used days in the future instead of a fixed date
		interval, you don’t need to do this as dates are relative to the current date in this case.
	</p>
	<p>
		As many affiliate programs let you export their data in different formats, we recommend creating import
		patterns for each affiliate program that offers this feature. This is much easier than adjusting data
		exported from different affiliate programs to your default KVS import pattern.
	</p>
	<p>
		After you set all the options and import settings, they are pre-processed and checked. This is a background
		task that may take a while to complete. Most time-consuming tasks are checking URLs and galleries. You will
		be able to see the progress of this check in %. After the check is complete, you will be taken to a page
		with results that will list all errors, warnings and notifications. If you are not happy with the results
		of the check, you can return to the import page and adjust your settings and data.
	</p>
	<p>
		This preliminary check can generate error messages of various types:
	</p>
	<ul>
		<li>
			<span class="term">Error</span>: a line with photo album data contains a critical error and cannot be
			processed. You can go back to fix the line, or you can continue importing, and this line will be
			skipped.
		</li>
		<li>
			<span class="term">Warning</span>: a line with photo album data possibly contains a potential error.
			Some examples of warnings are ‘album with this name already exists’, ‘category does not exist and will
			be created’, etc. Warnings are not a reason to skip the line during importing.
		</li>
		<li>
			<span class="term">Notification</span>: miscellaneous information regarding a line with photo album
			data.
		</li>
	</ul>
	<p>
		After you see the preliminary check results and confirm proceeding with importing, the importing will be
		launched. You will be able to see the import progress. Import can take a considerable amount of time, as
		during import the engine downloads all source photo album files. You don’t need to wait till the import is
		complete. When import is in progress, KVS creates a special type of background task. You can see the
		progress of this task in % if you go to the list of background tasks. This task has special priority and is
		run as a process separate from the conversion engine. This was done to let the conversion engine process
		photo albums that have been already downloaded as importing continues. If you would like to suspend importing
		for any reason, just delete the background task. To see which photo albums have been added already, you can
		check the importing task log in the background tasks log list of the administration section.
	</p>
	<p>
		Data of each import are available in import log list of the administration section.
	</p>
	<p>
		As we have described before, when importing, the engine will download all photo album source files to your
		primary server and create individual background tasks in the task queue to convert each photo album. If you
		are importing a large batch of content at once, you may face a shortage of free disk space on your server
		as a lot of content will be waiting to be converted. To deal with this, see the
		<span class="term">Minimum free disc space for primary server</span> option in the content settings. When
		the threshold specified here is reached, the importing will be paused until there is free space available,
		after which it will resume its operation.
	</p>
	</div>
	<!-- ch_concepts_album_content_import(end) -->
	<!-- ch_concepts_album_content_mass_edit(start) -->
	<div>
		<h3 id="section_album_content_mass_edit">Mass Photo Album Editing</h3>
		<p>
			Mass photo album editing lets you make a wide range of changes to many photo albums at the same time. Check
			the list of changes you can make below:
		</p>
		<ul>
			<li>
				Change status, type, price in tokens, user, and content source for all selected photo albums.
			</li>
			<li>
				Change the publishing date for all selected photo albums to a random date within a specified range.
				This feature lets you take e.g. 100 oldest photo albums from your site and have them published again in
				the nearest future. Together with photo album publishing dates, comment dates and dates of all other
				related events will be also changed.
			</li>
			<li>
				Change rating of all selected photo albums to a random value within a specified range.
			</li>
			<li>
				Add or delete specified tags, categories, or models for all selected photo albums.
			</li>
			<li>
				Set the value of a flag to zero for all selected photo albums.
			</li>
			<li>
				Migrate all files of selected photo albums from one server group to another. Here, deleting files on
				the servers of the old group will be a queued task and will occur no sooner than in a day. This is
				required for all the pages of the site to have updated links to photos, as after physical photo album
				file deletion all old links will stop working on the servers from the old group.
			</li>
			<li>
				Process selected photo albums using post-processing plugins.
			</li>
		</ul>
		<p>
			Mass photo album editing is launched from group operations in the photo album list shown in the
			administration panel. You can either tick the photo albums you need to edit and use
			<span class="term">Mass edit selected</span>, or use <span class="term">Mass edit all albums</span> option.
			<span class="term">Mass edit filtered albums</span> lets you run mass operations only for the photo albums
			that are currently shown by the list filters.
		</p>
		<p class="important">
			<b>Important!</b> Mass editing is possible only for photo albums with
			<span class="term">Active</span> or <span class="term">Disabled</span> statuses.
		</p>
	</div>
	<!-- ch_concepts_album_content_mass_edit(end) -->
	<!-- ch_concepts_album_content_export(start) -->
	<div>
		<h3 id="section_album_content_export">Exporting Photo Album Data</h3>
		<p>
			Photo album data export lets you create a text file in the format you need containing any photo album data.
			Lots of filters are supported to let you export exactly the data you need. In most cases, the resulting
			text file is used for further importing the data to other scripts.
		</p>
	</div>
	<!-- ch_concepts_album_content_export(end) -->
	<h2 id="section_categorization">Content Categorization</h2>
	<!-- ch_concepts_categorization_basic(start) -->
	<div>
		<h3 id="section_categorization_basic">Basic Categorization: Categories and Tags</h3>
		<p>
			Tags are the simplest form of categorizing your video and photo content in KVS. You don’t need to create
			your tags beforehand. Just type in the list of tags separated by commas, and they will be created as
			needed. If you want tags to be selected based on content names and descriptions as you add it, you will
			need to create them beforehand.
		</p>
		<p>
			Tags support mass operations. Go to the list of tags, and you'll be able to edit (rename) any of them. This
			will affect all items that use this tag. When a tag is renamed, merging is processed separately if the tag
			with the new name already exists. You can use this to get rid of typos and/or merge multiple tags into one.
			You can also delete any tag, and it will be removed from all objects that use it.
		</p>
		<p>
			Categories as well as tags can be dynamically created when adding new content. Unlike tags, categories
			support more fields and can be split into groups. Categories and groups of categories can have avatars
			(with image size defined in content settings) descriptions and additional fields that you can use for your
			own needs.
		</p>
		<p>
			In the site's templates, 2 extra category fields are used by default. One is called <b>HTML title</b> and
			is used to modify the <b>&lt;title&gt;</b> tag on the video list page listing videos with the same
			category. The other one is called HTML description and works in a similar way. Thus, you can use these
			fields to specify values you want to send to the meta fields of the pages which list videos of each
			category. When the meta fields of a page are empty, category name and description will be taken from basic
			fields to fill these in.
		</p>
		<p>
			Apart from being used in videos and photo albums, categories can be applied to content sources and
			referers (traders).
		</p>
		<p>
			When you add new content, you don’t have to set categories and tags manually. Instead, you can use the
			auto-selection plugins. For them to work, you should enable them and create lists of tags and categories
			which will be used for matching. These plugins will be triggered when you add new content. They analyze
			names and descriptions of content items and add matching tags and categories to videos and albums.
		</p>
	</div>
	<!-- ch_concepts_categorization_basic(end) -->
	<!-- ch_concepts_categorization_advanced(start) -->
	<div>
		<h3 id="section_categorization_advanced">Content Sources, Models, and DVDs / Channels</h3>
		<p>
			Content sources are the foundation of advertising features offed by KVS. Basically, a content source is an
			entity uniting ad-related information on a sponsor site which you intent to promote. For each content
			source, you can define plenty of ad-related details, e.g. your referral link, images and banners of various
			formats, and more. Content sources support many custom fields.
		</p>
		<p>
			In video format settings, you can define watermark images and duration limits as additional fields of a
			content source to get different watermarks and durations for videos from different content sources.
		</p>
		<p>
			Content sources are assigned to videos and photo albums. At the same time, they are available as separate
			entities on your site. This lets you create sections dedicated to content sources only, like a review
			section. Also, content sources (and models, and DVDs / channels as well) support user comments and ratings.
		</p>
		<p>
			From the video point of view, you can use ad information of a content source to display in-player ads. The
			easiest example here would be as follows: let’s say you have trailers, full videos for which are in the
			paid premium zone of your sponsor’s site. Virtually any in-player ads can be used to encourage users to go
			to a sponsor’s site and make purchases there. When you use content source related ads, you can promote
			different sponsors via different videos.
		</p>
		<p>
			Models are categorization elements for videos and photo albums, similar to categories and tags. In addition
			to basic fields, models can have 2 screenshots of fixed sizes assigned to them, as well as parameters such
			as age, height, weight, hair color etc. As we have said before, models can have comments and ratings from
			users. The basic package of KVS does not feature model support.
		</p>
		<p>
			DVDs / channels are not categorization elements, but are used in a similar way. DVDs and channels let you
			group videos together, like episodes of a series, videos covering related topics etc. To do this, you need
			to create a channel or a DVD in the administration panel first. Then, when you add or mass edit videos,
			assign the channel or DVD to them. Channels and DVDs are supported by the full KVS package only.
		</p>
		<p>
			For historical reasons, the functionality of channels and DVDs is implemented through the same object type.
			By default, the administration panel uses channels as the most used feature. If you would like to use DVDs
			instead, you can set the flag in the script configuration file <b>/admin/include/setup.php</b> to
			<b>dvds</b>:
		</p>
		<p class="code">
			$config['dvds_mode']="dvds";
		</p>
		<!--TODO: block from russian doc-->
	</div>
	<!-- ch_concepts_categorization_advanced(end) -->
	<!-- ch_concepts_categorization_flags(start) -->
	<div>
		<h3 id="section_categorization_flags" class="l3">Flags</h3>
		<p>
			One could say flags are elements of categorization as well. However, unlike other methods, flags are meant
			to be used by the users of your site. The simplest way to use them would be the Like / Dislike flags. In
			addition to this, KVS lets you create any other flags and use them as needed. Users can not only vote for a
			flag but also leave a text message, which lets you build great feedback with your users.
		</p>
	</div>
	<!-- ch_concepts_categorization_flags(end) -->
	<h2 id="section_memberzone">Member Area</h2>
	<!-- ch_concepts_memberzone_intro(start) -->
	<div>
		<h3 id="section_memberzone_intro">General Member Area Features</h3>
		<p>
			Member area functionality is featured in all the packages of KVS except the basic one. KVS offers many
			settings and limits that you can use to build a site with maximum monetization potential. The foundation of
			these settings is support of several types, or statuses, of users:
		</p>
		<ul>
			<li>
				<span class="term">Unregistered</span>: site visitors who are not registered or haven’t logged into the
				member area, i.e. guests.
			</li>
			<li>
				<span class="term">Standard</span> users (Active users): users that have registered for free and logged
				into the member area with their credentials.
			</li>
			<li>
				<span class="term">Premium</span> users: users who have paid for their subscription and logged into the
				member area with their credentials. This user type is the foundation of paid access.
			</li>
			<li>
				<span class="term">Webmasters</span>: system users whom you can create or assign from the
				administration panel.
			</li>
		</ul>
		<p>
			Standard users can purchase tokens that they can later spend on access to premium content. In addition to
			selling tokens for money, you can also award users with tokens for specific member area activity, like
			uploading videos, commenting etc. The nature of the tokens is such that the users will still have standard
			status, but when they will access purchased content, their status will be seen as premium, which means they
			will see everything other premium users see. For better experience of such users, KVS lets them check the
			list of content they have purchased access to.
		</p>
		<p>
			So, the member area of KVS lets you use free registration along with paid subscriptions of
			<span class="term">Premium</span> users, and also use tokens to assign <span class="term">Premium</span>
			status selectively.
		</p>
	</div>
	<!-- ch_concepts_memberzone_intro(end) -->
	<!-- ch_concepts_memberzone_configuration(start) -->
	<div>
		<h3 id="section_memberzone_configuration">Setting Up Paid Access</h3>
		<p>
			Paid memberships are configured in the administration panel in Users. KVS supports a choice of SMS and
			credit card payment processors. If you need to use other processors, please contact the support department
			to see if the features you need can be implemented for free, or for assistance.
		</p>
		<p>
			You can combine using card and SMS payment processors. Also, you can allow or disallow free registration.
			KVS lets users upgrade their <span class="term">Standard</span> membership to
			<span class="term">Premium</span>. Depending on whether you want to allow non-premium access to the content
			or not, go to member area settings to choose the status the users will receive after their premium
			membership expires. This can be <span class="term">Active</span>, if you allow non-premium access, or
			<span class="term">Disabled</span> (after premium memberships expire, users won’t be able to log into the
			member area).
		</p>
		<p>
			Go to the administration panel and specify at least one membership package for your chosen payment
			processor. Membership packages created in KVS need to correspond to the packages you create on the payment
			processor’s side. Different processors have different ways of setting up packages. After all packages are
			set up, you need to activate at least one processor and select the default one. Other active processors
			will be used as alternative (you can send the ID of an active processor to the registration / membership
			upgrade page using the <b>service_id</b> parameter which will tell the registration / upgrade form to use
			this processor).
		</p>
		<p>
			When creating membership packages, you need to specify the URL to this package on the processor’s website.
			Usually it’s a https:// URL which sends the package ID to the processor along with any processor options
			which let you set up the way the subscription form looks. Also, you need to specify the package ID on the
			processor’s side in a separate field. This ID is always contained in the URL leading to the payment page.
		</p>
		<p>
			After you have created and set up the packages for the payment processor you have selected, you need to set
			several options in the <b>signup</b> block on the registration page:
		</p>
		<ul>
			<li>
				Whether credit card payments should be enabled.
			</li>
			<li>
				Whether SMS payments should be enabled.
			</li>
			<li>
				Whether free registration is allowed.
			</li>
			<li>
				What is the default payment option.
			</li>
		</ul>
		<p>
			After you add the membership packages and enable corresponding options in the <b>signup</b> block, you will
			be able to see membership package selection on the registration page (free, credit card payment, SMS
			payment). When a membership type is selected, the page will refresh and show details on available
			membership packages (countries and operators for SMS payments). Then, depending on the membership type
			selected, the registration process can go in different ways:
		</p>
		<ul>
			<li>
				When free access is selected (provided the settings of the <b>signup</b> block allow it), the user
				needs to fill in the form and, if such are the settings of the <b>signup</b> block, confirm the
				registration by email. After the registration is complete, the user status will be set to
				<span class="term">Active</span>, i.e. <span class="term">Standard</span> access. This access type can
				upgrade to <span class="term">Premium</span> later on, or earn / buy tokens for premium access to
				selected content.
			</li>
			<li>
				When a credit card payment is selected (provided the settings of the <b>signup</b> block allow it and a
				credit card payment processor is activated), the user needs to fill in the form. Then, they will be
				redirected to the processor’s website where they complete the payment. After the payment is confirmed,
				the payment processor will send the transaction details to KVS in the background, and KVS will add user
				information into the database, giving <span class="term">Premium</span> membership to the user.
			</li>
			<li>
				When SMS payment is selected (provided the settings of the <b>signup</b> block allow it and a SMS
				payment processor is activated), the user needs to send an SMS message with the text specified to a
				phone number the user chooses depending on their location and their mobile network. This information is
				set when creating SMS payment processor packages on the KVS side and is shown on the registration page.
				After the processor receives the SMS, it will send the transaction details to KVS in the background and
				send an SMS response to the user with the access code. The user needs to enter this code into the
				corresponding registration form field. If the code is valid, the user becomes registered and gets
				<span class="term">Premium</span> membership.
			</li>
		</ul>
		<p>
			If you plan to only offer credit card payments for premium memberships, you can simplify the registration
			form so that it does not require entering login and email. It will only prompt to select the membership
			package. To do this, hide all the unnecessary fields using CSS in the signup block (NEVER delete them from
			the template), and use pre-generated random strings as values:
		</p>
		<p class="code">
			<span class="comment">&lt;!-- username input --&gt;</span><br/>
			&lt;input type="text" name="username" maxlength="100" value="{{$smarty.ldelim}}$generated_username{{$smarty.rdelim}}"/&gt;<br/>
			<br/>
			<span class="comment">&lt;!-- password input --&gt;</span><br/>
			&lt;input type="password" name="pass" value="{{$smarty.ldelim}}$generated_password{{$smarty.rdelim}}"/&gt;<br/>
			<br/>
			<span class="comment">&lt;!-- password confirmation input --&gt;</span><br/>
			&lt;input type="password" name="pass2" value="{{$smarty.ldelim}}$generated_password{{$smarty.rdelim}}"/&gt;<br/>
			<br/>
			<span class="comment">&lt;!-- email input --&gt;</span><br/>
			&lt;input type="text" name="email" maxlength="100" value="{{$smarty.ldelim}}$generated_email{{$smarty.rdelim}}"/&gt;
		</p>
		<p>
			This is how you let the user skip entering all these details. Generated values will be sent to the payment
			processor. There, the user will only need to enter their email and financial details.
		</p>
		<p class="important">
			<b>Important!</b> Registration confirmation by email (if such is set in the <b>signup</b> block parameters)
			will be required only for users who registered for free.
		</p>
		<p>
			In most cases, <span class="term">Premium</span> memberships expire after a certain period of time
			(subscription duration). After the subscription expires, the user status is changed from
			<span class="term">Premium</span> to the status you set in the member area settings. The user is not
			deleted from the database and full user history stays available. Inactive users cannot log into the member
			area.
		</p>
	</div>
	<!-- ch_concepts_memberzone_configuration(end) -->
	<!-- ch_concepts_memberzone_video(start) -->
	<div>
		<h3 id="section_memberzone_video">Setting Up Video Access</h3>
		<p>
			Offering paid access to a video-based site is usually based on one of the following concepts:
		</p>
		<ul>
			<li>
				Unregistered users have access to all videos but they can play only videos with limitations (e.g. full
				length videos in lower quality, or shorter videos, or any combination of quality and duration limits).
				After the payment is made, users can watch videos in full length and full quality.
			</li>
			<li>
				Unregistered users have access only to the free segment of the video archive (lower resolution, lower
				quality, shorter duration, i.e. free promo videos). At the same time, user see there are other videos
				available, longer and of better quality. After the payment, users can play both video types, free and
				full.
			</li>
			<li>
				Unregistered users see lots of ads in the player that stop showing after the payment is made.
			</li>
			<!--
				<li>
					Незарегистрированные посетители имеют большое ограничение на скорость загрузки видео, которого после
					оплаты нет.
				</li>
				<li>
					Незарегистрированные посетители могут посмотреть не более N видео за M часов (например, не более 3
					видео в сутки). После оплаты пользователи могут смотреть видео сколько угодно.
				</li>
				-->
		</ul>
		<p>
			In regards to paid access duration, there are also several different concepts:
		</p>
		<ul>
			<li>
				Access without duration limits.
			</li>
			<li>
				Expiring access, e.g. after 30 days.
			</li>
		</ul>
		<p>
			KVS lets you manage the way your users access your videos using all the concepts defined above, as well as
			combining them in any desired way.
		</p>
		<p>
			Let’s go back to our sample video format configuration and have a look at them with user access levels in
			mind. Let’s imagine we want standard videos of good quality to be available only to registered users. We
			also want trailers to premium videos to be available to all users without any limits, while full premium
			MP4 and WMV videos are available for downloads only to premium users.
		</p>
		<p>
			To configure access in this way, let’s set the <span class="term">Access level</span> option in the way we
			want for each of the video formats. We enable downloading for 2 full premium formats. In the video format
			list, we switch to the <span class="term">Access and protection</span> display mode and see what we have so
			far:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_video_formats_access.png" alt="Displaying access levels and protection for video formats" width="903" height="201"/><br/>
			<span>Displaying access levels and protection for video formats.</span>
		</div>
		<p>
			We see that unregistered users can only get access to files of 2 formats, <b>MP4 LQ</b> and
			<b>Premium Trailer MP4</b>.
		</p>
		<p>
			Now, when our formats are set up in the way we want, let’s go to setting up the way they are displayed in
			the player on the site. The KVS player supports options that set up the way the video formats are shown in
			the player. For each video type, standard and premium, there are 7 slots for which you choose whether they
			will show existing video formats, or they will redirect the users instead. See the player’s manual for more
			details on the settings available.
		</p>
		<p>
			In the player, the slots are shown as a drop-down list with slot names and current slot being highlighted.
			When other slots are selected, the player can either show the file from this slot, or redirect the user to
			the URL specified in the settings. This can happen in 2 cases: a) the user doesn’t have the access level
			required to watch the video file in this slot, or b) the slot contains a static redirect instead of a video
			file. You can also use the JS API of the player to configure any other action, e.g. show a popup window
			with payment options.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_player_slots_demo.png" alt="Video slots as shown by the player" width="547" height="100"/><br/>
			<span>Video slots as shown by the player.</span>
		</div>
		<p>
			Let’s have a look at the way slots are configured for standard videos:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_player_slots_setting_std.png" alt="Configuring player slots for standard videos" width="745" height="141"/><br/>
			<span>Configuring player slots for standard videos.</span>
		</div>
		<p>
			We used 3 slots and chose 2 existing video formats for them, along with a static redirect in the last slot.
			Near each slot you can see a prompt that tells you what happens to users of different access levels when
			this slot is selected. For instance, when the <b>High quality</b> slot is selected, the video file will be
			shown to registered users (active and premium users) while unregistered users will be redirected to the URL
			specified. This URL is set up in player settings in the <span class="term">Redirect URL</span> field. For
			our example, let’s use a redirect to the registration page (<b>signup.php</b>). So, this is what users of
			each type will see when they attempt to switch between slots in the player:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_player_slots_std.png" alt="Player slot behavior for users of each type" width="567" height="340"/><br/>
			<span>Player slot behavior for users of each type.</span>
		</div>
		<p>
			Let’s pay attention to 2 important things shown on the screenshot above:
		</p>
		<ul>
			<li>
				The slot order shown to unregistered users is different from the order set in player settings. The
				difference arises because unregistered users don’t have access to the 1st slot (<b>MP4 HQ</b> format).
				This is why the <b>MP4 LQ</b> format slot which such users have access to becomes the first one (and
				the default one as well). Users of other types see the original slot order as both active and premium
				users are allowed to access the <b>MP4 HQ</b> format slot.
			</li>
			<li>
				For premium users, showing the <b>HD quality</b> slot is not needed as this slot contains the redirect
				to the registration page. Obviously, premium users have already registered.
			</li>
		</ul>
		<p>
			Let’s have a look at the second part in more detail. KVS lets you have different player settings for users
			of all types. This lets you set up your ads in different ways for different types of users. It also gives
			you wide opportunities of setting up your video slots creatively. To enable certain player settings for
			some user types, you need to switch the access level display in the top of the player settings page and
			enable overriding settings:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_player_settings_override.png" alt="Overriding player settings for premium users" width="694" height="147"/><br/>
			<span>Overriding player settings for premium users.</span>
		</div>
		<p class="important">
			<b>Important!</b> When overriding player settings for a user type, you override not just slot settings, but
			all possible settings (ads, logo etc) as well.
		</p>
		<p>
			After the player settings are overridden for premium users, let’s have a look at our finalized video slot
			configuration:
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_player_slots_setting_all.png" alt="Player slot settings for all users" width="711" height="542"/><br/>
			<span>Player slot settings for all users.</span>
		</div>
		<p>
			For premium users, we left only 2 slots in standard videos. Premium users have access to video formats in
			both slots, and it means that no redirects will be made (which is also shown in the prompts). In premium
			video slots, we set up just 1 slot with the full MP4 format. In these cases, player won’t show any slots at
			all to premium users, so you don’t need to have a name for the slot.
		</p>
		<p>
			For all other users (active and not registered), we left standard video slot options as they were. In
			premium video slots, we selected 2 video formats, only to 1 of which these users have access to (the
			trailers). Thus, when Full video HD slot is selected in the player on the site, the users will be taken to
			the registration page. We have to note here, however, that you need to override player configuration for
			active users as well, as you need to redirect them to <b>upgrade.php</b>, the membership upgrade page, not
			<b>signup.php</b>, the registration page.
		</p>
		<p>
			Let us finalize the list of all the details you may need to consider when setting up slots for your site:
		</p>
		<ul>
			<li>
				Levels of access to video formats are configured in the settings for these formats. Slots with video
				formats are configured in player settings.
			</li>
			<li>
				In the first slot, you need to select one of the video formats (no static redirect here).
			</li>
			<li>
				If a user doesn’t have access to any of the formats in the slots, this user cannot play any videos. Site
				templates feature a text shown in this case which you can customize.
			</li>
			<li>
				If there is only one first slot configured in player settings, the player on the site will not be
				showing the drop-down menu and you can leave the slot name field blank.
			</li>
			<li>
				The video file from the first slot available to the user will be selected as the video file shown by
				the player during initialization. This slot will be the first in the list while other slots will be
				shown in the order set up in the settings.
			</li>
			<li>
				If a user doesn’t have access to the video file selected in a slot, when choosing this slot, the user
				will be redirected to the URL configured in player settings.
			</li>
			<li>
				In order to make embed codes for third party sites available, you need to configure the embed player in
				such a way so that it has at least the 1st slot. For the embed player, you can choose only the video
				formats available to all site users.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_memberzone_video(end) -->
	<!-- ch_concepts_memberzone_album(start) -->
	<div>
		<h3 id="section_memberzone_album">Setting Up Photo Album Access</h3>
		<p>
			In terms of limiting access to photo albums, KVS lets you set up access levels to different formats of
			photo albums (via settings for these formats) as well as to source files of photos (in content settings).
		</p>
		<p>
			The way the photo album page will be displayed on the site for users with different statuses is a front
			end code aspect. In most cases, you can limit this to showing reduced photos to non-premium users, while
			premium users see large photos or source files and can download ZIP archives. To implement this, you can
			set up a status check in the <b>album_view</b> and <b>album_images</b> block templates:
		</p>
		<p class="code">
			{{$smarty.ldelim}}if $smarty.session.status_id==3{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">this is the premium user</span><br/>
			{{$smarty.ldelim}}elseif $smarty.session.user_id>0{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">this is the non-premium user who has not logged in</span><br/>
			{{$smarty.ldelim}}else{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">this is the user who hasn’t logged in</span><br/>
			{{$smarty.ldelim}}/if{{$smarty.rdelim}}
		</p>
	</div>
	<!-- ch_concepts_memberzone_album(end) -->
	<!-- ch_concepts_memberzone_tokens(start) -->
	<div>
		<h3 id="section_memberzone_tokens">Using Tokens</h3>
		<p>
			Tokens can be used along with regular premium memberships. There are 3 ways tokens can be obtained:
		</p>
		<ul>
			<li>
				Purchasing tokens during registration. To enable this, you need to enable paid access in the
				<b>signup</b> block settings. You also need to have active payment processors with packages for
				purchasing tokens.
			</li>
			<li>
				Free registration and purchasing tokens using the membership upgrade form later on. To have this
				enabled, you need to enable free access in the <b>signup</b> block settings. You also need to have
				paid access enabled in the <b>upgrade</b> block settings as well as have active payment processors with
				packages for purchasing tokens.
			</li>
			<li>
				Free registration and obtaining tokens for member area activity. To have this enabled, you need to have
				awards for activity enabled in the member area settings.
			</li>
		</ul>
		<p>
			To enable buying access to content in exchange for tokens, you need to enable purchasing access to certain
			content for tokens in member area settings. There, you also set up global content pricing and set up
			duration for each content purchase. Each video and photo album can have individual price settings that will
			override global settings.
		</p>
		<p>
			In the site’s default templates, there are no forms for purchasing access to content for tokens. Check the
			FAQ for samples of code here. Additionally, you will need to build a new site’s page where logged in users
			will be able to see the photo albums and videos they purchased access to. Use the <b>list_videos</b> and
			<b>list_albums</b> blocks to do that, with <b>mode_purchased</b> enabled. Also, in your site’s header, you
			may want to display the user’s current token balance (this information is available in the session), as
			well as a link to access upgrade page that lets users buy more tokens.
		</p>
	</div>
	<!-- ch_concepts_memberzone_tokens(end) -->
	<!-- ch_concepts_memberzone_protection(start) -->
	<div>
		<h3 id="section_memberzone_protection">Protecting Member Area from Shared Access</h3>
		<p>
			If you are building a premium member area, you will face the necessity of protecting the member area from
			many different people who use the same credentials to log into the site. It happens a lot as some users
			share their passwords to paysites on forums, social networks etc. KVS features built-in multi-access
			protection features which you can use.
		</p>
		<p>
			When users log into the site each time, details such as IP address, country (if you have GEOIP installed),
			and browser are logged. After you accumulate a certain amount of this data covering a certain amount of
			time, you can detect shared account with high degree of probability.
		</p>
		<p>
			KVS lets you define conditions under which an account is seen as shared, as well as set up a policy of
			blocking such accounts:
		</p>
		<ul>
			<li>
				Instant blocking forever. If you choose this policy, when one of the blocking conditions is met, the
				account is blocked and if the user attempts to log in, they see a message about blocking. Normal
				account features can only be resumed by contacting the support department, i.e. only administrators
				cancel the blocking manually in the administration panel.
			</li>
			<li>
				Temporary blocking. When one of the blocking conditions is met, the account is temporarily blocked and
				the user password is reset to a randomly generated one. The user gets an email saying their account is
				blocked and their password was changed. To unblock the account, the user needs to visit a special link.
				In addition to this policy, you can also set up the maximum amount of temporary blocks after which the
				account is blocked forever (the account can still be unblocked by the administrator when needed). After
				a temporary block, the old password cannot be used anymore. The system will make sure the password
				cannot be set back to the old one which has been potentially shared.
			</li>
		</ul>
		<p>
			To analyze login attempts of individual users, you can use the login statistics in the statistics section
			of the administration panel. Also, for blocked users, you can see the analyzed data for a certain analyzed
			period on this user’s information page. The analyzed period either starts from the registration day or from
			the last blocking of this account.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_users_login_stats.png" alt="Analyzing user login statistics" width="916" height="131"/><br/>
			<span>Analyzing user login statistics.</span>
		</div>
		<p>
			Protecting your member area from multi-access is configured in the site’s UI section in the <b>logon</b>
			block on the login page. You can use the following protection triggers each of which is sufficient for
			blocking:
		</p>
		<ul>
			<li>
				Number of unique IPs used within a certain time interval. Not a very reliable criterion as often users
				have dynamic IPs.
			</li>
			<li>
				Number of unique IP masks within a certain time interval. Here, mask is first 3 digits of an IP
				address, like 192.168.12.xxx.
			</li>
			<li>
				Number of unique countries within a certain time interval (only works with GEOIP installed on your
				server).
			</li>
			<li>
				Number of unique browsers (user agents) within a certain time interval.
			</li>
		</ul>
		<p>
			Before you enable and set up the protection, we recommend you to analyze the data you have to choose the
			limitations that suit your situation best. To do this, determine the most active users by video views in
			your user activity statistics. For each of these users, check their login statistics. Usually you’ll be
			able to determine if the user’s credentials are shared or not. Then, based on the data obtained, you will
			be able to set the criteria for blocking in a suitable way.
		</p>
		<p>
			The KVS start page contains information on blocked accounts. Click the links and you’ll be able to see
			temporarily and permanently blocked accounts. To unblock an account, use the user’s information page, or
			the extra actions option in the user list, or group operations.
		</p>
	</div>
	<!-- ch_concepts_memberzone_protection(end) -->
	<h2 id="section_community">Community Features</h2>
	<!-- ch_concepts_section_community_intro(start) -->
	<div>
		<h3 id="section_community_intro">General Community Features</h3>
		<p>
			Community features let you enhance your member area and make your site more popular. Building a community
			is a challenging task. Not every site needs a community; this is why we introduced multiple KVS packages
			with different community features. So, only the full KVS package offers the full set of community features.
		</p>
		<p>
			Communities are based on registered site users. They can fill in their profiles, build bookmark
			collections, interact with other users, add friends, upload videos, and create photo albums.
		</p>
		<p>
			You can introduce limits making any site functionality available only to community members. For instance,
			you can determine whether anonymous users can leave comments or not. When needed, you can also disable
			video ratings for anonymous users. Such settings are configured in site settings, either in the templates,
			or in the parameters of corresponding site blocks. Usually, in the templates, you only set up how data is
			displayed and how the interface looks. In block parameters, you customize the behavior of these blocks.
		</p>
	</div>
	<!-- ch_concepts_section_community_intro(end) -->
	<!-- ch_concepts_section_community_members(start) -->
	<div>
		<h3 id="section_community_members">Users and Profiles</h3>
		<p>
			After the registration process is complete, users become full-featured community members. Registration and
			changing email address are operations that by default require email confirmation. Before the registration
			is confirmed, a user’s status is <span class="term">Not confirmed</span>, and such users cannot log into
			their account. If you don’t want your registration to feature email confirmation, you can disable it in the
			<b>signup</b> block parameters in site settings. In this case you will most likely need to disable this
			functionality for changing the email address in the <b>member_profile_edit</b> block as well.
		</p>
		<p>
			Users appear in lists on the site only after they become active (confirmed their registration by email, if
			this is enabled), or paid for their premium memberships (for premium users). All user lists are displayed
			by the <b>list_members</b> block. This block supports a large amount of filters (such as users with avatars
			only, filtering by countries, and more), as well as text searching through user data.
		</p>
		<p>
			Changing a user’s profile details is carried out via the <b>member_profile_edit</b> block which processes 3
			forms: profile editing form (main details and avatar), email editing form (confirmation of email change
			configured by the block parameters), and personal area password editing form.
		</p>
		<p>
			For your users to be able to delete their profiles, you need to create a page with the
			<b>member_profile_delete</b> block (not used in default templates). In this case, users will be able to
			submit profile deletion requests to site admin who will see such requests on the start page and decide
			whether to delete them or not.
		</p>
		<p>
			KVS supports trusted users who don’t need their various activity to be approved by the administrator of the
			site. To give a user trusted status, you need to flag their profile accordingly.
		</p>
	</div>
	<!-- ch_concepts_section_community_members(end) -->
	<!-- ch_concepts_section_community_bookmarks(start) -->
	<div>
		<h3 id="section_community_bookmarks">User Favorites</h3>
		<p>
			KVS supports up to 10 built-in lists of video and photo album favorites that you can use at your own
			discretion. In default templates, just one list is used, a generic list of video or photo favorites. At the
			same time you can use more favorites-related features on your site. For example, you can let your users add
			content not just to favorites, but to ‘watch later’ lists. This list is essentially favorites as well, but
			it exists as a separate entity. Similarly, you can create other lists of favorites that your users will use
			(not more than 10, main list included).
		</p>
		<p>
			In addition to system lists you as the site administrator create, you can let your users create playlists.
			Thus users can categorize videos they like as they want. Playlists are not supported by photo albums.
			Playlists are created by the <b>playlist_edit</b> block while their list is displayed by the
			<b>list_playlists</b> block. Use the <b>list_videos</b> block to show videos from the playlist, sending the
			playlist ID to it.
		</p>
	</div>
	<!-- ch_concepts_section_community_bookmarks(end) -->
	<!-- ch_concepts_section_community_video_upload(start) -->
	<div>
		<h3 id="section_community_video_upload">User Video Uploads</h3>
		<p>
			By default, only community members can upload videos. If you want to let anonymous site users upload
			videos, you need to enable the corresponding parameter in the <b>video_edit</b> block so that the page with
			this block is available to all users. Otherwise when unregistered users try to load this page, they will be
			redirected to the login page (you can customize their destination in the settings of this block too).
		</p>
		<p>
			Regardless of whether the video is uploaded by a community member or an anonymous user, the IP address of
			the uploader will be logged in the database. In the administration area, you will always be able to see
			uploader IPs for every video ever uploaded to your site.
		</p>
		<p>
			The upload and video edit block (<b>video_edit</b>) lets you customize several aspects of video validation:
		</p>
		<ul>
			<li>
				Whether video description is a required field or not (it is required by default).
			</li>
			<li>
				Whether tags are required or not (they are required by default).
			</li>
			<li>
				Whether categories are required or not (they are required by default).
			</li>
			<li>
				How many categories can be selected for one video (3 by default).
			</li>
			<li>
				What is the minimal video duration in seconds (5 seconds by default).
			</li>
			<li>
				What is the maximum video duration in seconds.
			</li>
		</ul>
		<p>
			After a video is uploaded, it is queued for conversion. By default, in the settings of the
			<b>video_edit</b> block, there is a parameter that makes the engine set video status to inactive so that it
			does not appear on the site right after it’s processed. Also, all videos uploaded to the site (or edited)
			are flagged as ‘pending approval’ so that the site administrator can filter all newly uploaded videos and
			check them. So, the administrator needs to check videos, remove the ‘pending approval’ flag, and set the
			video status to active if the video is approved.
		</p>
		<p>
			If you want, you can disable the parameter that sets the status of all videos uploaded from the site to
			inactive (see the settings of the <b>video_edit</b> block). If this parameter is disabled, the video will
			appear on the site after it’s uploaded. It will also appear in the list of videos with pending approval so
			that the site administrator can check the video and delete it if needed.
		</p>
		<p>
			The parameter that sets status of new videos to inactive is also used when users edit their uploaded videos
			(they can edit virtually all the fields except the video file itself). If the parameter is enabled, the
			video status is set to inactive and video is no longer shown on the site. In either case, the video is
			listed in the administrator’s list of videos requiring approval.
		</p>
		<p>
			The video upload block lets you prevent video editing, either entirely or main screenshots only. There are
			several parameters in the block settings that are used to manage this. If you want to prevent only certain
			videos from editing (and from being deleted as well, however, video deletion is managed by the video list,
			a different block), you can flag the videos as locked in the administration panel. After that, these videos
			will not be available for deletion or editing.
		</p>
	</div>
	<!-- ch_concepts_section_community_video_upload(end) -->
	<!-- ch_concepts_section_community_albums_creation(start) -->
	<div>
		<h3 id="section_community_albums_creation">User Photo Albums</h3>
		<p>
			By default, only community members can create photo albums. If you want to let anonymous site users create
			photo albums, you need to enable the corresponding parameter in the <b>album_edit</b> block so that the
			page with this block is available to all users. Otherwise when unregistered users try to load this page,
			they will be redirected to the login page (you can customize their destination in the settings of this
			block too).
		</p>
		<p>
			Regardless of whether the photo album is created by a community member or an anonymous user, the IP address
			of the uploader will be logged in the database. In the administration area, you will always be able to see
			uploader IPs for every photo album ever uploaded to your site.
		</p>
		<p>
			The photo album creation and edit block (<b>album_edit</b>) lets you customize several aspects of photo
			album validation:
		</p>
		<ul>
			<li>
				Whether photo album description is a required field or not (it is required by default).
			</li>
			<li>
				Whether tags are required or not (they are required by default).
			</li>
			<li>
				Whether categories are required or not (they are required by default).
			</li>
			<li>
				How many categories can be selected for one photo album (3 by default).
			</li>
			<li>
				What is the minimal image size (800x600 pixels by default).
			</li>
		</ul>
		<p>
			After a photo album is created, it is queued as a background process and becomes active only when the
			background process finishes. All photo albums that were created or edited on the site are flagged as
			‘pending approval’ so that the site administrator can filter all newly uploaded and edited photo albums and
			check them. The administrator needs to check the photo albums, remove the ‘pending approval’ flag, and set
			the photo album status to active if needed.
		</p>
		<p>
			Despite the fact that by default all photo albums become active after they are created or edited, you can
			adjust the settings of the <b>album_edit</b> block so that the album status is set to inactive until they
			are checked and activated by the site administrator.
		</p>
		<p>
			Also, you can prevent users from editing the photo albums they created, or from editing the photos in their
			albums. Editing can be disabled either globally in the settings of the <b>album_edit</b> parameter, or for
			each photo album separately (tick the ‘locked’ option in the photo album details in the administration
			panel).
		</p>
	</div>
	<!-- ch_concepts_section_community_albums_creation(end) -->
	<!-- ch_concepts_section_community_other(start) -->
	<div>
		<h3 id="section_community_other">Miscellaneous</h3>
		<p>
			In addition to profiles, uploading videos and creating photos, KVS also offers a range of basic features
			that let your site’s community members communicate between each other.
		</p>
		<p>
			First, KVS has internal messaging features that let your users exchange messages. Users can also add
			friends, which is also based on the messaging system. Users can become friends and let their friends see
			their private videos and photo albums.
		</p>
		<p>
			Second, KVS has community events that include primary actions of the users, like uploading videos, creating
			photo albums, leaving a comment etc. The site can show a global event feed as well as event feeds for
			individual users, and event feeds for friends of a user.
		</p>
		<p>
			Third, KVS lets users leave posts on ‘walls’ of other users. Essentially, these are comments posted on a
			user’s profile.
		</p>
	</div>
	<!-- ch_concepts_section_community_other(end) -->
	<h2 id="section_rotator">Rotator</h2>
	<!-- ch_concepts_rotator_basic(start) -->
	<div>
		<h3 id="section_rotator_basic">General Rotator Overview</h3>
		<p>
			KVS features a two-tier video rotation module:
		</p>
		<ul>
			<li>
				The first tier, or level of video rotation is rotation of videos to determine the ones with the highest
				CTR. Such rotation results in all videos arranged according to their CTR, the higher the CTR, the
				higher the probability of a video being clicked.
			</li>
			<li>
				The second tier, or level of video rotation is rotating screenshots within each video to determine the
				ones with the highest CTR. Such rotation results in choosing the screenshot with the highest CTR as a
				video’s primary screenshot. Also, optionally, several low CTR screenshots can be deleted.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> Using the rotator module increases server load (HDD and CPU).
		</p>
		<!--TODO: block from russian doc-->
	</div>
	<!-- ch_concepts_rotator_basic(end) -->
	<!-- ch_concepts_rotator_videos(start) -->
	<div>
		<h3 id="section_rotator_videos">Rotating Videos</h3>
		<p>
			To enable this rotation tier you need to enable the corresponding option in KVS content settings.
		</p>
		<p>
			Video rotation means that statistics of video impressions in various lists on the site and clicks on the
			videos is constantly collected and analyzed. All video lists participate in the rotation at the same time
			(<b>list_videos</b> blocks), with an exception of video lists assigned to users (user favorites and user
			uploads). Also, for any <b>list_video</b> block, you can disable the rotator using the
			<b>disable_rotator</b> parameter.
		</p>
		<p>
			Here, it’s important to understand that enabling first tier rotation doesn’t change anything on your site.
			First tier rotation is essentially collecting the statistics and analyzing the CTR of each video. Later on,
			you can use this data to sort the videos in lists of your choice. For example, you can build a separate
			page to get traffic to, with 50 top-performing videos by CTR. To do this, you only need to insert the
			<b>list_videos</b> block and enable sorting by CTR.
		</p>
		<p>
			Unlike typical rotating scripts available today, video rotation in KVS can require more traffic
			circulation. This is caused by several factors:
		</p>
		<ul>
			<li>
				In KVS, videos are displayed via many lists with different sorting options and filters. Hence the
				rotator cannot freely choose videos to be displayed in a list as the videos are usually listed with
				fixed sorting options. Different lists get different amounts of user visits (for example, videos of
				cat1 category get 10x more visits than the videos of cat2 category). This causes an imbalance in
				impressions that leads to uneven collection of statistics.
			</li>
			<li>
				Caching is a crucial aspect of how KVS powers your site. This also influences imbalances in video
				impressions as due to caching one and the same video can be shown in the same site location for up to a
				day. In reality, maximum duration of showing a video depends on the page / block caching time, which is
				a customizable parameter.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_rotator_videos(end) -->
	<!-- ch_concepts_rotator_screenshots(start) -->
	<div>
		<h3 id="section_rotator_screenshots">Rotating Video Screenshots</h3>
		<p>
			Rotation of video screenshots can be enabled only when first tier rotation is enabled, as the rotator uses
			the statistics gathered during first tier rotation. Thus, for screenshot rotation to function properly, you
			need to give video rotation some time to run. This lets the rotator to gather enough statistical data.
			There is a special KVS plugin that lets you check and reset the statistics.
		</p>
		<p>
			Unlike first tier video rotation, screenshot rotation is a process that has beginning and end for each
			video. After rotation is complete for a video, KVS does the following:
		</p>
		<ul>
			<li>
				If in screenshot rotation settings deleting low CTR screenshots is enabled, these screenshots will be
				deleted.
			</li>
			<li>
				A new main screenshot with best CTR will be selected.
			</li>
			<li>
				Screenshot rotation for this video will stop.
			</li>
		</ul>
		<p>
			With this approach, it’s obvious that a criterion should be established based on which screenshot rotation
			for a video will be considered as complete. We have chosen 2 values as criteria: minimum video impressions
			in site lists and minimum clicks on the video. Thus, rotation of screenshots for any video ends as soon as
			a criterion of completion is met.
		</p>
		<p>
			To choose a completion criterion in the best possible way, KVS shows a simple chart of screenshot rotation
			completeness. In it, distribution by discreet completeness intervals is shown. If you are not happy with
			the rotation speed (for instance, it’s been a day after the rotation was enabled and only 10% videos have
			rotation progress between 0% and 20%), you can decrease the criteria values and see how the chart behaves.
			In a few such iterations, you will be able to set criteria values optimally.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_screenshots_rotator.png" alt="Criterion of screenshot rotation completeness and distribution of videos by completeness intervals" width="709" height="220"/><br/>
			<span>Criterion of screenshot rotation completeness and distribution of videos by completeness intervals.</span>
		</div>
		<p>
			Soon screenshot rotation for most videos will be complete and these videos will no longer be rotated. In
			the chart, these videos will be in the 100% interval.
		</p>
		<p class="important">
			<b>Important!</b> Before you enable screenshot rotations, make sure video rotation has accumulated enough
			statistical data. To do this, open the rotator statistics plugin and check that there are actual values
			there, not zeros.
		</p>
		<p>
			When screenshot rotation is enabled, each video will change its main screenshot on the site randomly to
			cover the impressions in an even way. Correspondingly, if a video has 10 screenshots, all of these will be
			shown randomly on all site pages where this video is shown (except user favorites and user uploads). You
			may not want this to happen on certain pages where it would be better to show only best screenshots
			selected so far to boost productivity. This could be the Index page, or any other page you get traffic to.
			To do this, you need to enable the parameter <b>show_best_screenshots</b> in the instances of the
			<b>list_videos</b> block where you only want to show screenshots with the best CTR (i.e. no screenshot
			rotation will take place).
		</p>
		<p>
			KVS also lets you have separate pages that will only show videos where rotation has not been completed. You
			can use these pages to receive low quality traffic to, which will help complete the rotation faster. To do
			this, enable the <b>under_rotation</b> parameter in the <b>list_videos</b> block on this page.
		</p>
		<p>
			You can create pages with videos only with screenshots for which rotation has been finished in a similar
			way. Just enable the <b>finished_rotation</b> parameter in the <b>list_videos</b> block.
		</p>
	</div>
	<!-- ch_concepts_rotator_screenshots(end) -->
	<h2 id="section_advanced">Advanced Features</h2>
	<!-- ch_concepts_advanced_clones(start) -->
	<div>
		<h3 id="section_advanced_clones">Building Satellite Sites</h3>
		<p>
			KVS lets you use a number of approaches when it comes to building satellite sites.
		</p>
		<p>
			First approach: you can use several copies of KVS running on the same database. Your sites will have some
			tables with data in common (e.g. content and user tables), while other tables will be different (e.g.
			statistics tables). One of the copies will be primary and will have full functionality; other copies will
			be secondary and most data in them will be in viewing mode.
		</p>
		<p>
			So, all copies of KVS will show the same videos, comments, and users. They can have completely unique
			designs and layouts, and individual statistics. One copy can be a full-featured tube site with social
			features and premium zone (full package), while other sites are pseudo-tubes (basic packages) used to
			generate traffic for the primary site. Also, please remember that KVS lets you customize any video list on
			the site in a very flexible way. You can use it to show only videos from certain categories on your
			satellite site, while your primary site will show videos of all categories.
		</p>
		<p>
			Second approach: your site will have different databases but the video archives will be the same. If you
			have a KVS-powered site and want to build satellites, you can hotlink some of your videos from satellites
			in such a way that hotlinking will only work from your satellites and nowhere else. If you need to disable
			hotlink protection for your satellites, just contact our support department and we will help you set this
			up.
		</p>
		<p>
			With this approach, you will need to synchronize video databases on your satellites, but this is easy to
			achieve when you use export and import feeds.
		</p>
	</div>
	<!-- ch_concepts_advanced_clones(end) -->
	<!-- ch_concepts_advanced_embed(start) -->
	<div>
		<h3 id="section_advanced_embed">Using Embed Codes for Your Videos</h3>
		<p>
			KVS offers rich functionality which lets you manage the embed codes to your videos. Promoting your site via
			embed codes has a number of advantages over, let’s say, offering hosted videos:
		</p>
		<ul>
			<li>
				Your partners don’t need to pay for bandwidth, similar to using hosted videos.
			</li>
			<li>
				With hosted videos, you cannot be sure your ads will be shown. With embed codes from KVS, your ads
				cannot be overridden or removed.
			</li>
			<li>
				Ad-related features of the KVS embed player do not depend on the embed code itself. The embed code
				contains only the video ID and some playback parameters, like preview image, skin, and sizes. Ad
				settings are defined globally in embed player settings in the administration panel. These can be
				changed at any time, and the changes will reflect in each instance of your embed codes.
			</li>
			<li>
				If you want, you can let your partners send their affiliate ID via the embed code and get a % of your
				earnings. Export feeds which send your videos as embed codes are a convenient way of doing this. Your
				partners can send their IDs and get embed codes with their IDs already in the codes.
			</li>
			<li>
				Unlike hosted videos, embed codes can be independently distributed by users.
			</li>
			<li>
				KVS offers embed code impression statistics.
			</li>
		</ul>
		<p>
			Let us mention here that KVS can offer hosted videos as well, as long as it makes sense in your business
			model (in a similar way as embed codes via export feeds).
		</p>
		<p>
			For your player to feature embed codes to videos being played, you need to make sure the video format shown
			via the embed code is selected in the embed player settings. Thus, if you want, you can create separate
			formats for standard and premium videos to display them via embed codes (e.g. shorter and/or lower quality
			videos).
		</p>
		<p class="important">
			<b>Important!</b> In the embed player, you can only select the video formats which unregistered users can
			play.
		</p>
		<p>
			If you don’t want your embed codes to be publicly distributed, you can disable copying of your embed codes
			in the player settings (see the NON-embed player settings for this). It won’t guarantee that somebody will
			use embed codes to your videos. They can, if they build embed valid embed codes themselves. Still, it will
			prevent your embed codes from being distributed publicly and uncontrollably. To disable embed codes
			completely, delete a video format in the embed player settings. After that, all embed codes will stop
			working. Statistics may still be collected, but this does not mean videos can be played via the embed codes.
		</p>
		<p>
			Embed player ads are managed in the embed player settings.
		</p>
		<p>
			KVS features 2 embed code types:
		</p>
		<ul>
			<li>
				<span class="term">Standard &lt;embed&gt; code</span>: a standard embed code for Flash apps. The main
				disadvantage here is inability to show alternative players for devices that do not support Flash.
			</li>
			<li>
				<span class="term">Iframe code</span>: an iframe in which a special page of your site with the player
				is displayed. This option is the more advanced and modern way as it lets you not just show an
				alternative HTML5 player for devices which do not support Flash, but also use JavaScript ads and
				customize the way the embed code looks by editing the HTML. The main disadvantage here is that
				webmasters may be suspicious about installing iframes on their sites.
			</li>
		</ul>
		<p>
			Go to embed player settings to switch between the two embed code types. This option influences all aspects
			of your player, including the way embed codes look on the site, exporting in the administration panel, and
			embed codes in the export feeds.
		</p>
	</div>
	<!-- ch_concepts_advanced_embed(end) -->
	<!-- ch_concepts_advanced_runtime_params(start) -->
	<div>
		<h3 id="section_advanced_runtime_params">Dynamic HTTP Parameters and Receiving Webmaster Traffic</h3>
		<p>
			KVS was built with large amounts of traffic in mind. Two-tier caching is used to optimize the performance
			and decrease the database load. However, caching introduces certain limits related to site templates. One
			of these limits is inability to display (or use otherwise) HTTP parameters sent via URLs to the pages of
			your site.
		</p>
		<p>
			Let us have a look at a simple example. You need to allow your partners send their ID so that this ID is
			included in the payment processor URL later on. Usually a link to your site will look like this:
		</p>
		<p class="code">
			http://domain.com/path/to/page/?<b>refid</b>=123456
		</p>
		<p>
			In the template of the page where you need to include the ID sent, you can just display it via Smarty:
		</p>
		<p class="code">
			&lt;a href="https://secure.payment-processor.com/?reseller_code={{$smarty.ldelim}}$smarty.request.<b>refid</b>{{$smarty.rdelim}}"&gt;Get premium access!&lt;/a&gt;
		</p>
		<p>
			When caching is enabled, this technique will not work. Each time the cached page expires (for each page,
			cache lifetime is different), a full cycle of page content generation is run. When you use the technique
			described above, when the full cycle is run, a link with partner ID sent at that moment will be generated.
			This link will ‘stay’ on the page until the next full page generation cycle. Here, even if a new ID is sent
			via HTTP parameters at some point, the link will stay the same.
		</p>
		<p>
			To even out this disadvantage of caching, KVS lets you configure up to 5 dynamic HTTP parameters that you
			can include in your pages regardless of what the caching settings are. Go to KVS site settings to configure
			these parameters. You can give them names as well as default values that will be used if no values were
			sent to the engine via links to the site.
		</p>
		<p>
			Dynamic HTTP parameters are sent to the site engine in the way similar to regular HTTP parameters, i.e. the
			link to the site will look the same (here, we suppose that you configured <b>refid</b> as a dynamic HTTP
			parameter):
		</p>
		<p class="code">
			http://domain.com/path/to/page/?<b>refid</b>=123456
		</p>
		<p>
			When processing request of this kind, the KVS engine sees that a value for <b>refid</b>, a dynamic HTTP
			parameter, was sent. This value will be saved in the user session and in their cookies (for 360 days).
			Thus, this value will be saved when further requests and visits of this user occur until this user comes to
			the site via a link with new value for this parameter, or until the cookie expires.
		</p>
		<p>
			To use the value of this dynamic HTTP parameter on the site or elsewhere (see table below), use the
			<b>%refid%</b> token which in our example will be replaced with <b>123456</b>. See below for list of places
			where you can use tokens of dynamic HTTP parameters:
		</p>
		<div class="table">
			<table>
				<colgroup>
					<col width="30%"/>
					<col width="70%"/>
				</colgroup>
				<tr class="header">
					<td>Place</td>
					<td>Example</td>
				</tr>
				<tr>
					<td>
						Site templates
					</td>
					<td>
						&lt;a href="https://secure.payment-processor.com/?reseller_code=<b>%refid%</b>"&gt;Get premium access!&lt;/a&gt;
					</td>
				</tr>
				<tr>
					<td>
						URL field for content sources
					</td>
					<td>
						http://my-sponsor.com/?reseller_code=<b>%refid%</b>
					</td>
				</tr>
				<tr>
					<td>
						URL field for ads
					</td>
					<td>
						http://my-sponsor.com/?reseller_code=<b>%refid%</b>
					</td>
				</tr>
				<tr>
					<td>
						URLs in player and embed player settings
					</td>
					<td>
						http://my-sponsor.com/?reseller_code=<b>%refid%</b>
					</td>
				</tr>
				<tr>
					<td>
						URL field in the payment page in the settings of a membership package for a credit card payment
						processor
					</td>
					<td>
						https://secure.payment-processor.com/?eticket_id=123&amp;reseller_code=<b>%refid%</b>
					</td>
				</tr>
			</table>
		</div>
		<p class="important">
			<b>Important!</b> Use only the dynamic HTTP parameters you actually use as they tend to slow the engine
			down a bit.
		</p>
		<p>
			Thus, in order to accept traffic from your partners and send partner IDs to payment processors or sponsor
			sites, you need to specify dynamic HTTP parameters (with any name) and use the token in the places where
			you need to include the ID sent by your partners.
		</p>
		<p>
			In addition to links with partner IDs, you can let your partners send their referral ID via embed code to
			your videos. When such embed codes are used, the player will attempt to replace the token of the partner
			ID with its value in all in-player ad links. For this to work, choose which of the dynamic HTTP parameters
			is used to send referral IDs in embed player settings.
		</p>
		<p>
			There are two ways for your partners to obtain embed codes with their referral IDs included:
		</p>
		<ul>
			<li>
				Visit the site with their referral ID and manually copy the embed codes from the player for all the
				videos.
			</li>
			<li>
				If you created an export feed that sends embed codes to your videos, partners can use this to mass
				obtain all embed codes (and further updates of them). To do this, you need to configure the HTTP
				parameter used on your site in which the referral ID will be sent through the feed.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_advanced_runtime_params(end) -->
	<!-- ch_concepts_advanced_admin_users(start) -->
	<div>
		<h3 id="section_advanced_admin_users">Managing Employees / Site Admins</h3>
		<p>
			In order to manage and run major sites, you may need your employees to work with the administration panel
			of KVS.
		</p>
		<p class="important">
			<b>Important!</b> Never let your employees access the administration panel as superadministrators. Instead,
			create administrators with limited rights and try to assign only those rights that are needed for their
			everyway work (for example, an employee working on content does not need to have rights to edit site pages
			and templates).
		</p>
		<p>
			When you use administrators with limited rights, KVS lets you control their work through the
			<span class="term">Activity log</span> and <span class="term">Audit log</span> data. You can find these
			logs in the administration section. <span class="term">Activity log</span> lets you see all logins into the
			administration panel, and session durations as well. <span class="term">Audit log</span> collects detailed
			statistics covering operations with videos, photo albums, and categorization objects. You can see who
			created, changed, or deleted these entities. You can use the <span class="term">Audit log analysis</span>
			plugin to see summaries for any period of time on all administrators or site users.
		</p>
		<p>
			When several administrators are working at the same time (one administrator writes descriptions, another
			processes screenshots etc.), it is desirable to prevent their job areas from overlapping. You can use video
			flagging to achieve this. When saving a video, each administrator can mark a video choosing any flag. You
			can filter the videos in the administration panel by flags. Administrators can switch video flags in the
			desired order to coordinate their work.
		</p>
		<p>
			By default, when administrators have rights to work with videos or photo albums, these rights cover ‘all’
			the videos and photo albums. If you want your administrators to be able to access only the videos and photo
			albums they own, you can use the
			<span class="term">forbid access to the videos and albums owned by other</span> option. The administrator
			who uploaded content is always the owner of this content. You can set and change owners for any content via
			mass editing.
		</p>
		<p>
			You can use the content owners functionality in a number of scenarios:
		</p>
		<ul>
			<li>
				Let trusted partners access the administration panel with limitations so that they can upload and
				manage their content on your site. If you enable the
				<span class="term">forbid access to the videos and albums owned by other</span> option for such
				administrators, they will only be able to see and edit the content they have uploaded.
			</li>
			<li>
				Distribute work among content managers so that each of them works with their own set of content. To do
				that, use mass editing to assign owners to groups of content.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_advanced_admin_users(end) -->
	<!-- ch_concepts_advanced_localization(start) -->
	<div>
		<h3 id="section_advanced_localization" class="l3">Localizing Sites and Content</h3>
		<p>
			KVS supports full site localization in its Ultimate version. There are several different approaches to
			localization that we will address below.
		</p>
		<p>
			First, you need to create the list of languages your site supports in site settings. Creating languages
			does not affect the site in any way, it merely adds new fields to the data storage system and new features
			to the administration panel.
		</p>
		<p>
			After that, you can go to <span class="term">Administration -> Data localization</span> in the
			administration panel and work on translating content and categorization objects. This section offers a
			unified interface to translate all object types that support localization in KVS. If you find it easier to
			translate videos and photo albums in primary editors of these objects, you can enable display of language
			fields on video and photo album editing pages in personal settings. All translation activity is logged in
			the audit log and can be processed by the <span class="term">Audit log analysis</span> plugin. This is how
			you can easily control the work of your translators and calculate their earnings.
		</p>
		<p>
			When content is displayed on the site, it will be displayed according to the site’s current locale. You
			don’t need to modify the templates in any way to achieve this. If for some objects there is no translation
			yet, for example, only half of the videos have been translated, original, non-translated content will be
			shown instead. Your site can function even if the content has been only partially localized.
		</p>
		<p>
			To achieve full localization, you can set up comments and member area activity blocks so that they display
			only comments and activity created in the site’s current locale. This is set up by using the
			<b>match_locale</b> parameter.
		</p>
		<p>
			There are several approaches of implementing localized versions on your site:
		</p>
		<ul>
			<li>
				<span class="term">All in one</span>. When you use this approach, all language versions are handled by
				the same KVS installation. The <b>kt_lang=%code%</b> parameter can be sent to any page, and this will
				switch this page to the language with <b>%code%</b> code. Also, the engine will suggest a language
				version according to the user’s language settings in the browser. If the language code is sent via the
				<b>kt_lang</b> parameter, it is stored in the cookies and will override the language settings in the
				browser.
				<span class="whitespace"></span>
				This approach has 2 main disadvantages: a) all templates need to be moved to language keys so that site
				text always corresponds to the current locale, and b) same URLs will be used to display content in
				different languages, which is a potential SE bottleneck.
				<span class="whitespace"></span>
				To enable this approach, add all the required languages in the admin panel and add this string to
				<b>/admin/include/setup.php</b>:
					<span class="code">
						$config['locales']=array('de','fr','es','it'); <span class="comment">// list all the supported language codes in any order</span>
					</span>
				After this, your site will automatically display its content in the current user’s locale, as long as it
				is in the list of supported locales.
			</li>
			<li>
				<span class="term">Language satellites</span>. This approach means that you need to install separate
				KVS copies to language subdirectories or subdomains and connect these copes as satellites to the
				primary database. As satellites feature different sets of pages and templates, it lets you add new
				languages without moving main site templates to language keys (i.e. copying main templates and
				replacing original text with translated), which may be simpler if you are adding one language. If you
				want to add multiple languages, it is easier to move the site’s templates to language keys as after that
				you will only need to translate the language file.
				<span class="whitespace"></span>
				The main advantage here is SE friendliness. Language satellites have separate URLs and pages with
				different content that do not overlap with your primary site. Another advantage here may be separate
				statistics for each language satellite. A possible disadvantage is the necessity to support additional
				KVS installations, including updating each of them separately, as well as the necessity to modify site
				templates separately. The latter can be handled by using language keys on the primary site. In this
				case, you can set up automated server replication of all template modifications and page configurations
				from your primary site to all satellite sites. Thus you will only need to manually update the language
				files.
				<span class="whitespace"></span>
				To switch a satellite site to a supported locale you need to add this string to
				<b>/admin/include/setup.php</b>:
					<span class="code">
						$config['locale']='de'; <span class="comment">// specify a supported language code that you want to assign to this satellite</span>
					</span>
				After this, the satellite will be displayed in the locale you selected.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_advanced_localization(end) -->
	<h2 id="section_stats">Site Statistics</h2>
	<p>
		KVS lets you see and analyze full-featured statistics covering all aspects of your site, member area, and
		community.
	</p>
	<!-- ch_concepts_stats_traffic(start) -->
	<div>
		<h3 id="section_stats_traffic">Traffic Statistics</h3>
		<p>
			Traffic statistics is collected based on the JavaScript + cookie combo to differentiate between main and
			nocookie traffic. Search engine bots, hitbots, and sometimes actual users (if they have JavaScript or
			cookie limitations) may be detected as nocookie traffic.
		</p>
		<p>
			Request and outgoing traffic statistics is calculated once every 5 minutes as a Cron job, and is stored
			with a 1-day discreteness.
		</p>
		<p>
			Here is the list of main parameters of site requests:
		</p>
		<ul>
			<li>
				<span class="term">Uniques</span>: unique visitors for the specified period. Unique visitors are
				visitors with no cookie installed for the past 24 hours.
			</li>
			<li>
				<span class="term">Total</span>: total site requests made by unique visitors for the specified period.
			</li>
			<li>
				<span class="term">Videos</span>: requests made to a video page during the specified period (or, more
				specifically, to the video viewing block on it). If the video viewing block is used on other pages,
				requests to it will be added to this value.
			</li>
			<li>
				<span class="term">Albums</span>: requests made to a photo album viewing page during the specified
				period (or, more specifically, to the album viewing block on it). If the album viewing block is used on
				other pages, requests to it will be added to this value.
			</li>
			<li>
				<span class="term">Embeds</span>: how many times your embed code was loaded on third party sites.
			</li>
			<li>
				<span class="term">Nocookie</span>: nocookie traffic statistics. This value includes all users that did
				not pass the cookie check.
			</li>
			<li>
				<span class="term">CS outs</span>: visitors that clicked the outgoing links to video content sources.
				Each location where you used the special built-in redirect script instead of direct links to content
				sources will be included here. Clicks on any in-player ads are also included (pre-roll, post-roll,
				pause etc.) as long as they are assigned to content source URLs. Also, the ratio between unique site
				visitors and outgoing traffic to content sources is shown.
			</li>
			<li>
				<span class="term">Ads outs</span>: visitors that clicked the ads. Only ad blocks inserted via ad
				settings in the admin panel will be included, as long as the outgoing URL is specified in the ad
				settings. Also, the ratio between unique site visitors and outgoing traffic to ads is shown.
			</li>
		</ul>
		<p>
			Incoming request statistics includes country statistics. Thus you can see the distribution of incoming
			traffic by countries. For traffic outgoing to content sources and ads, countries are not included.
		</p>
		<p>
			To include statistics of traffic outgoing to content sources, you need to use links in a special format.
			Clicking these links, the visitors will be redirected to content source sites with the URL set in the
			content source settings. You can use links of 2 formats:
		</p>
		<p class="code">
			link to PHP script: <b>http://your_domain.com/redirect_cs.php?dir=%content_source_directory%</b><br/>
			or<br/>
			mod_rewrite link: <b>http://your_domain.com/cs/%content_source_directory%/</b>
		</p>
	</div>
	<!-- ch_concepts_stats_traffic(end) -->
	<!-- ch_concepts_stats_search(start) -->
	<div>
		<h3 id="section_stats_search">Site Search Statistics</h3>
		<p>
			KVS collects on-site statistics including video and photo album searches. Search query statistics is
			collected only when the search request originates on your site (you may want to use external links to
			search results for certain key phrases). This was made to prevent the search statistics from potential
			overloads. Phrases and words entered on your site are most relevant.
		</p>
		<p class="important">
			<b>Important!</b> Only requests entered 2 or more times are shown in the administration panel to optimize
			the way statistics is displayed.
		</p>
	</div>
	<!-- ch_concepts_stats_search(end) -->
	<!-- ch_concepts_stats_embed(start) -->
	<div>
		<h3 id="section_stats_embed">Embed Code Usage Statistics</h3>
		<p>
			Each time your embed codes are displayed on third party sites, KVS records the domain name where your embed
			code is shown. This way you can always see which sites are best at promoting your site via embed codes.
		</p>
	</div>
	<!-- ch_concepts_stats_embed(end) -->
	<!-- ch_concepts_stats_referers(start) -->
	<div>
		<h3 id="section_stats_referers">Referers or Traffic Sources</h3>
		<p>
			To obtain traffic statistics for individual traffic sources (sites from which traffic comes to your site),
			you need to add required traffic sources to the list of referers that you can find in the statistics
			section. By default, the list contains only 1 system referer –
			<span class="term">Empty referer (bookmarks)</span>. This referer includes visitors that come to your site
			without any information on any referring site, e.g. when they type in your URL into their browser or use
			their bookmarks).
		</p>
		<p>
			KVS does not collect information on all traffic sources for your site. Use your server-side statistics
			instead. However, KVS lets you set the traffic sources you want to monitor the statistics for. When you add
			a traffic source (referer), you need to set the string that will be used to detect visitors coming from
			this traffic source. As you may have guessed, the detection algorithm is comparing this string with the
			HTTP Referer header.
		</p>
		<p>
			Thus, after you create all the referers you need, you will see full statistics on traffic coming from these
			referers. You can use this to analyze the quality of traffic from various sources, e.g. which referer sends
			traffic that makes more on-site clicks, or content source or ad clicks, and more.
		</p>
		<p>
			If you know the sites where embed codes with your videos are used, you can add these sites as referers and
			see statistics showing how many times your videos were played on third party sites.
		</p>
		<p>
			In addition to analyzing the statistics, you can use referers to rank traders by various criteria and
			display the top chart on your site. The trader top is displayed by the <b>top_referers</b> block. It lets
			you display the list of referers with customizable sorting (10+ sorting options) and also supports a
			variety of auxiliary settings:
		</p>
		<ul>
			<li>
				Each referer can be assigned to a category (in referer settings). Use <b>top_referers</b> block on the
				category information page to display referers only from this category. Use <b>top_referers</b> block on
				the video viewing page to display the list of referers by open video categories.
			</li>
			<li>
				You can set up the <b>top_referers</b> block in such a way that it functions as a ‘video list’. In this
				case, for each referer, a video of matching category will be selected. You can use this feature to
				display lists like ‘top videos on friendly sites’ etc.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_stats_referers(end) -->
	<!-- ch_concepts_stats_members(start) -->
	<div>
		<h3 id="section_stats_members">Member Area and Community Statistics</h3>
		<p>
			The member area and community statistics includes the following:
		</p>
		<ul>
			<li>
				<span class="term">Transactions summary</span>: full statistics on all member area transactions on your
				site.
			</li>
			<li>
				<span class="term">User activity</span>: statistics on activity of all users. Contains a lot of data,
				so column names are abbreviated. Move your mouse over a column and wait for a short while to see the
				column description.
			</li>
			<li>
				<span class="term">User logins</span>: statistics on user logins into the member area. Comes in 2
				formats: flat log with all logins and grouping by users with totals for each user shown.
			</li>
			<li>
				<span class="term">Content visits</span>: statistics on content page visits for all registered users.
			</li>
			<li>
				<span class="term">Content purchases</span>: premium membership purchase statistics (for tokens). Comes
				in 2 formats: flat log with all purchases and grouping by users with totals for each user shown.
			</li>
			<li>
				<span class="term">Transaction IPs</span>: lets you analyze IPs from which transactions are made to see
				if there have been mass transactions from any IPs.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_stats_members(end) -->
	<h2 id="section_website_ui">Building and Managing Your Site</h2>
	<p>
		With KVS, you take advantage of its unique module-based engine that lets you build and customize your site in a
		multitude of ways. In short, the engine lets you build any pages with any content. You can add as many logical
		blocks to any page as you like, choosing from over 50 blocks (full version), e.g. lists of videos, lists of
		categories, and configure their options and templates.
	</p>
	<p>
		In addition to building site pages, the engine can be used to create XML feeds of any kinds. Here, XML feed is
		a site page that is displayed as XML. As you can fully customize the content of any site page, you can create
		absolutely any feed you may need.
	</p>
	<p>
		Using the site engine is covered in separate documents:
		<a href="documentation.php?doc_id=website_ui">manual</a> and
		<a href="documentation.php?doc_id=website_ui_tutorial">tutorial</a>. If you don’t want to go into too much
		engine-related detail, you can contract third parties (designers and front end coders) who are experienced in
		customizing KVS sites and can help you with your tasks. Below, you can find a general overview of KVS engine.
	</p>
	<!-- ch_concepts_website_ui_concepts(start) -->
	<div>
		<h3 id="section_website_ui_concepts">General Concepts</h3>
		<p>
			Sites are built based on a set of pages set up in the KVS engine. For each page, a PHP file in your site’s
			root directory is created. This file redirects requests to this page to the KVS engine. Also, for each
			page, a template in the <b>/template</b> directory is created, defining the content the page displays. It
			can be virtually any content at all, static HTML, database output, or XML code.
		</p>
		<p>
			So, at any given moment, you can create any site pages with the content you want. Go to
			<span class="term">Website UI</span> section of the administration panel to do that.
		</p>
		<p>
			If you need to create a page similar to a page you already have, the easiest way here is copying the
			existing page and modifying the copy as needed. You can copy any page in the page list using the
			<span class="term">Duplicate</span> command of a page’s context menu. Similarly, you can duplicate any page
			component. Copying a page will give you a new page available by a different URL (when duplicating, you
			specify the new page’s ID), but with the same look and functionality as the original page. Here are a few
			situations when you may need to duplicate a page:
		</p>
		<ul>
			<li>
				Debugging design modifications to implement them on the original page once they are tested.
			</li>
			<li>
				Cloning a page with minor modifications to send some traffic to it that meets certain criteria. Here,
				use the .htaccess file in your root directory to define these criteria.
			</li>
			<li>
				Creating a new page based on the page you already have.
			</li>
		</ul>
		<p>
			If you want to create a page that displays certain KVS data (video list, user profile, or a combination),
			you need to add the blocks that display the content you need to this page. Go to
			<span class="term">Website UI</span> to see the full list of blocks and decide which ones you need for a
			particular page. Then, you can add a block to a KVS page by using a special directive in page template. The
			engine will replace this directive with block output when processing a request to this page.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_page_structure.png" alt="Block structure for the index page – 4 blocks on page" width="696" height="128"/><br/>
			<span>Block structure for the index page – 4 blocks on page.</span>
		</div>
		<p>
			Essentially, when you insert a block-connecting directive into a template of a KVS page, what you do is
			insert the HTML code that will be generated by the block on this page. When needed, this directive lets you
			use the generated HTML code as a value to be used elsewhere instead of displaying the code on the page.
		</p>
		<p>
			Different blocks generate different HTML codes as their output (for example, the <b>list_videos</b> block
			generates the HTML code for a video list, while the <b>member_profile_view</b> generates the HTML code for
			a user profile). When you insert a block into a page, you have 2 configuration options:
		</p>
		<ul>
			<li>
				Block configuration parameters – these let you customize the behavior of this block on the page. The
				parameters are responsible for the data shown by the block, or for what checks will be run when the
				user submits the block form. In other words, these parameters define the logic of how the block works.
				For example, the <b>list_videos</b> block can display 50 latest videos on the index page and 100 top
				videos with best CTR on the In Traffic page. In both cases, the block type stays the same, but because
				the parameters differ, the block behaves differently on different pages. Similarly, blocks of the same
				type can behave differently on one and the same page.
			</li>
			<li>
				Block template – lets you customize the way block content is displayed. The template translates the
				block data into HTML code which is later inserted into a KVS page. For example, the <b>list_videos</b>
				block displays 5 videos per line on the index page while on the In Traffic page the same block displays
				10 videos per line. This is fully defined by the block template.
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_block_parameters.png" alt="Configuration parameters define the way different aspects of the way the block functions on the login page" width="829" height="249"/><br/>
			<span>Configuration parameters define the way different aspects of the way the block functions on the login page.</span>
		</div>
		<p>
			Adding blocks to the pages of your site you build an informal logical structure of these pages, e.g. this
			part is the header, then come 2 video lists one above the other, in this corner we have a drop down list
			with models sorted by popularity, etc. Each logical area is either generated by a block or is a piece of
			static HTML (e.g. site header and footer).
		</p>
	</div>
	<!-- ch_concepts_website_ui_concepts(end) -->
	<!-- ch_concepts_website_ui_debug(start) -->
	<div>
		<h3 id="section_website_ui_debug">Debugging Site Pages</h3>
		<p>
			To make building and customizing your site easier, KVS has a ‘page debugger’ feature that can help you make
			minor modifications in existing templates or build new pages from scratch for your custom needs. All of
			this becomes possible without studying the documentation in great detail. You can launch the debugger for
			any page of the site. It shows details and data related exactly to the page you see on your screen at the
			current moment.
		</p>
		<p class="important">
			<b>Important!</b> Only users logged into the administration area have access to the page debugger.
		</p>
		<p>
			To open the page debugger for the page on which you currently are, you need to add the <b>debug=true</b>
			HTTP parameter to the page URL. After you do this, KVS will display all debug information related to this
			page:
		</p>
		<p class="code">
			http://domain.com/videos/path_to_video/?debug=true
		</p>
		<p>
			The debugger displays the following types of data:
		</p>
		<ul>
			<li>
				On which KVS page you are right now.
			</li>
			<li>
				List of all page components (generic templates with static HTML) used in this page’s template. Each
				component can be opened for editing.
			</li>
			<li>
				List of all HTTP parameters sent to this page.
			</li>
			<li>
				List of all session variables for current user.
			</li>
			<li>
				List of all blocks used on this page. Each block can be opened for editing. For each block, you see its
				enabled configuration parameters, values of all $storage variables (variables which KVS page templates
				can use) values of all local variables of the block (variables which can be used in the block
				template).
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_page_debug.png" alt="Debugging information for a video viewing page. Details on the video_view block on this page." width="833" height="450"/><br/>
			<span>Debugging information for a video viewing page. Details on the video_view block on this page.</span>
		</div>
		<p>
			As you see in the screenshot above, the debugger displays actual values of all existing block variables on
			the current page. The name of each value is displayed in the way this variable should be inserted into a
			template (either of a page or of a block); for example, for video titles:
		</p>
		<p class="code">
			{{$smarty.ldelim}}$storage.video_view_video_view.title{{$smarty.rdelim}}
		</p>
		<p>
			To iterate by an array variable, use {{$smarty.ldelim}}foreach{{$smarty.rdelim}}:
		</p>
		<p class="code">
			{{$smarty.ldelim}}foreach name=data item=<b>item</b> from=$storage.video_view_video_view.tags{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">{{$smarty.ldelim}}* Addressing an array element via the item variable *{{$smarty.rdelim}}</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}<b>$item</b>.tag_id{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}<b>$item</b>.tag{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		</p>
	</div>
	<!-- ch_concepts_website_ui_debug(end) -->
	<h2 id="section_performance">Performance and Load</h2>
	<!-- ch_concepts_performance_general(start) -->
	<div>
		<h3 id="section_performance_general">General Information</h3>
		<p>
			When you use KVS, you can be sure it is perfectly able to process large amounts of traffic on your site.
			Our research shows that a copy of KVS with standard site configuration works smoothly with 1,000,000 daily
			hits on an average server setup. Some KVS-powered sites get 10,000,000 daily hits during peak times, which
			shows there are huge performance reserves to use.
		</p>
		<p>
			LA (load average) is the primary load indicator. This value is shown in the header of the administration
			panel. When the server functions normally, LA should not exceed 3-5 (except when the same server handles
			video conversion, during which LA may be higher).
		</p>
		<p>
			When LA is consistently high, it means your site has troubles processing all the incoming traffic. There
			may be different reasons for this:
		</p>
		<ul>
			<li>
				Excessive MySQL load. This problem usually arises when the caching strategy for some of the pages
				and/or blocks is far from optimal. To solve this problem, check the performance statistics which you
				can find on the list of pages in <span class="term">Website UI</span>. Based on performance stats, you
				can come up with ideas for improving your caching strategies. If your caching is set up in the optimal
				way but MySQL load still remains high, it is best to contact the support department for deeper
				analysis. If the block that causes MySQL load can be optimized, we will do it. If there is no way this
				can be done, it makes sense to consider customizing the block to remove the functionality you don’t
				need. Also, the block can be rewritten with the specifics of your site taken into account, which will
				make this block generate less MySQL load.
			</li>
			<li>
				Hard disk(s) load. This issue is not easy to diagnose, but usually it is the reason for high LA if
				MySQL load is within reasonable limits. If you store your content on your primary server, the obvious
				solution here would be moving the content to another server, or duplicate the videos and balance video
				serving.
			</li>
			<li>
				Conversion load. If your primary server handles video conversion tasks regularly, it will definitely
				cause higher server LA. To get rid of this excessive load, you may want to move your conversion
				operations to a different server.
			</li>
		</ul>
		<p>
			In addition to the load caused by the KVS engine, there may be other issues on your server not directly
			related to KVS. In such situations, only your server administrators will be able to help. Here are a few of
			such issues:
		</p>
		<ul>
			<li>
				Site is really slow. It was later found out that the disk system of the server was faulty.
			</li>
			<li>
				Heavy Apache load. Incorrect Nginx configuration that made Apache serve all video content.
			</li>
			<li>
				Most of the time, the site works with minimal load while several times a day the Apache load peaks
				heavily. It was found out that this is caused by an issue with the operating system libraries that
				caused Apache child processes to freeze and use lots of CPU resources.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_performance_general(end) -->
	<!-- ch_concepts_performance_overload(start) -->
	<div>
		<h3 id="section_performance_overload">Preventing Overload</h3>
		<p>
			The KVS engine features a built-in anti-overload protection system. Why would the engine need this if it is
			supposed to work smoothly? A legitimate question, and the answer here is simple: to help your server stay
			up and running in critical situations.
		</p>
		<p>
			Our monitoring of high-load sites shows that when the LA value reaches 250, the server experiences
			‘clinical death’ and putting it out of this condition is very problematic (in most cases, the server will
			need to reboot). To avoid this, KVS attempts to keep LA under this level by all means, disabling various
			engine modules as LA grows.
		</p>
		<p>
			Emergency situations when LA peaks unexpectedly can arise because of a variety of reasons, including these:
		</p>
		<ul>
			<li>
				Site cache reset with large amounts of incoming traffic. In this case, MySQL load increases suddenly,
				bringing about an avalanche-like growth of LA.
			</li>
			<li>
				DoS attack on your server.
			</li>
			<li>
				Operating system library faults or other server software errors.
			</li>
		</ul>
		<p>
			When KVS detects gradual LA growth, it does the following:
		</p>
		<ul>
			<li>
				LA > 10: main cron jobs are suspended. All statistics is no longer refreshed, background conversion
				processes and other scheduled tasks are suspended.
			</li>
			<li>
				LA > 30: the engine serves only the pages that have a current cached version in MemCache. It means that
				displaying site pages is a task that MySQL no longer handles, and load is thus decreased. When pages
				that do not have a current cached version are requested, the engine redirects these requests to the
				/overload.html file which is supposed to be found in the site’s root directory (provided this file
				exists). You can customize the contents of this file.
			</li>
			<li>
				LA > 50: the engine redirects all requests to /overload.html.
			</li>
		</ul>
		<p>
			LA thresholds listed above can be modified in the configuration file: /admin/include/setup.php.
		</p>
	</div>
	<!-- ch_concepts_performance_overload(end) -->
	<!-- ch_concepts_performance_monitoring(start) -->
	<div>
		<h3 id="section_performance_monitoring">Performance Statistics</h3>
		<p>
			As KVS works, performance of all site pages is constantly monitored. Performance statistics is meant to
			give you information about how fast this or that page serves content so that you can adjust the caching
			settings accordingly. Then, for high-load sites, performance statistics lets you see which areas of your
			site (e.g. blocks) are potential performance bottlenecks and could be optimized in the future.
		</p>
		<div class="screenshot">
			<img src="docs/screenshots/quick_start_performance.png" alt="Analyzing the performance statistics" width="885" height="152"/><br/>
			<span>Analyzing the performance statistics.</span>
		</div>
		<p>
			Here, there are 3 primary values:
		</p>
		<ul>
			<li>
				Time which an element (page or block) takes to finish working when caching is enabled.
			</li>
			<li>
				Time which an element (page or block) takes to finish working when caching is disabled.
			</li>
			<li>
				Cache use percentage. This shows how often caching is used for this element (page or block).
			</li>
		</ul>
	</div>
	<!-- ch_concepts_performance_monitoring(end) -->
	<!-- ch_concepts_plugins(start) -->
	<div>
		<h2 id="section_plugins">Plugins</h2>
		<p>
			Even though KVS is a software product with many features and settings, there is always room for perfection.
			We do our best to add new features to our product constantly, including features requested by our valued
			customers. Sometimes adding features to existing code is unreasonable as it complicates development and
			testing, and makes the product more difficult to use on a daily basis. This is why we introduced plugin
			support. These are minor standalone modules featured in the administration panel, used to handle various
			tasks.
		</p>
		<p>
			Here is a brief overview of all the KVS plugins:
		</p>
		<ul>
			<li>
				<span class="term">Category auto-selection</span>. Use this to add categories to videos and photo
				albums automatically based on the title, description and tags fields. For this plugin to start working,
				you need to create a database of categories, as the plugin does not create new categories choosing from
				existing ones instead. Category search is done not just by category names but by synonyms as well.
			</li>
			<li>
				<span class="term">Model auto-selection</span>. Use this to add models to videos and photo albums
				automatically based on the title and description fields. For this plugin to start working, you need to
				create a database of models, as the plugin does not create new models choosing from existing ones
				instead. Model search is done not just by model names but by all their pseudonyms as well.
			</li>
			<li>
				<span class="term">Tag auto-selection</span>. Use this to add tags to videos and photo albums
				automatically based on the title and description fields. For this plugin to start working, you need to
				create a database of tags, as the plugin does not create new tags choosing from existing ones instead.
				Model search is done not just by tag names but by synonyms as well.
			</li>
			<li>
				<span class="term">Audit log analysis</span>. This plugin is mainly used to evaluate the amount of work
				your content managers, writers, and translators have completed for any given period of time. The
				calculations are based on the audit log that logs all content creation and editing activity.
			</li>
			<li>
				<span class="term">System audit</span>. The audit plugin runs a full check on many critical aspects of
				KVS, from checking file and directory privileges to full content availability check. If you launched
				the audit plugin with all options enabled and no errors were detected, most likely, your site is
				working without any issues (this is not a 100% guarantee though). If you have a lot of videos and photo
				albums, it is likely that full content scan will take several hours to complete. You don’t need to wait
				for it to finish, as this is a background task. You can close the page and return to it in a while. The
				results of the check will be shown in the list of recent checks.
			</li>
			<li>
				<span class="term">Movie from image</span>. Use this plugin to create videos with custom duration and
				quality from an image you upload. The plugin will create an MP4 video file that will show the uploaded
				image for the time you specify. You can use such videos in hotlink protection settings showing it
				whenever the protection was triggered.
			</li>
			<li>
				<span class="term">External search</span>. This plugin lets you use third party API to add a search
				feature to your site. You can set up the conditions that trigger the replacement of inner search
				results with third party search; third party results can also be added to on-site search results. Use
				this plugin to monetize your site better by selling search result clicks. Contact the support
				department on more information about the configuration of this plugin.
			</li>
			<li>
				<span class="term">Avatar generation</span>. The avatar-generating plugin creates avatars for
				categories based on videos in these categories. For each category, only one video is selected, and the
				main screenshot of this video (more specifically, its source file) will be used as the avatar for this
				category. In plugin settings, you can choose the sorting method used to choose a category’s main video.
				You also need to specify the ImageMagick option string that will be used to resize source files of
				video screenshots to the avatar size.
			</li>
			<li>
				<span class="term">FTP content uploader</span>. This plugin can make uploading video and photo content
				to your servers much easier. All you need to do is upload the files to the server with the required
				file structure and run the plugin, modifying its settings so that it processes the right directories.
				The plugin will analyze the directories and give you an overview of the content found there. During the
				final launch the selected content will be added to the site. The plugin supports 3 directories for
				uploading standard videos, premium videos, and photo albums. We recommend using the same directories
				each time as content duplicates are detected based on directories (i.e. when content that has already
				been added and wasn’t deleted is uploaded to the same directory, it is considered duplicate). For
				security reasons, the directories with uploaded files need to be child directories of the root
				directory of your site. Both video directories let you upload single video files to directory root and
				multiple files into subdirectories as well. Subdirectories can contain video files and screenshots in
				ZIP archives or as JPG files. You can upload not just video source files but files of individual
				formats as well. When needed, you can upload only source files, or only format files, or any
				combination of these. Similarly to videos, you can upload photo album files either file by file to
				directory root, or upload subdirectories with multiple files in them. The photos can be uploaded as
				ZIP archives, or as sets of JPG images. After the plugin completes the upload, the uploaded files will
				not be deleted. You can either keep them or delete them manually. When you run the plugin again, files
				or subdirectories that have already been uploaded will be considered duplicate.
			</li>
			<li>
				<span class="term">Database repair</span>. Use this plugin in situations when the tables of your
				database contain errors that you need to fix.
			</li>
			<li>
				<span class="term">KVS news</span>. Once a day, this plugin pings the KVS website for
				news and new versions.
			</li>
			<li>
				<span class="term">KVS update</span>. This plugin lets you partially automate the update process. You
				need to upload the archived update that you received and specify the MD5 hash of the archive which is
				shown on the KVS website in the protected customer area. The plugin will check whether this archive is
				suitable for updating your script and it will then give you step-by-step instructions. It will also
				check the completion of each step. If at some point the plugin displays an error notification saying
				the step was not completed, you will need to follow the instructions again.
			</li>
			<li>
				<span class="term">Template Cache Cleanup</span>. The template cache clearing plugin clears file cache
				either manually or using a schedule. This plugin can also be used to get information about your file
				cache size and the number of files in it. File cash is used in various parts of KVS, but it cannot
				clear itself. As your site keeps working, the file cache will keep growing. We recommend clearing it
				manually from time to time. If your site’s member area is used by a large number of users, we recommend
				scheduling cache-clearing tasks. Please pay attention that when you launch the clearing manually, it
				will actually be launched within 5 minutes after you submit the form.
			</li>
			<li>
				<span class="term">Rotator weighting matrixes</span>. Use this plugin to analyze the way clicks are
				distributed across your pages. If the rotator is supported by a list block (so far only list_videos
				supports it), this block will collect statistics on all the clicks made on it. Based on this
				statistics, click distribution matrixes are created, used by the rotator to determine the weight of
				each individual click. This information may be useful to you when finding the most clickable areas on
				your site etc.
			</li>
			<li>
				<span class="term">Backup</span>. Use this plugin to make backup copies of the database,
				KVS system files, and site template and design as well. You need to specify the directory where the
				backup copies will be stored. This directory needs to have sufficient privileges so that PHP / Apache
				can write to it.
			</li>
			<li>
				<span class="term">Rotator stats reset</span>. Use this plugin to reset all rotator data except for
				weighting matrixes of click distribution. You need to select the types of data to be reset. This is a
				background operation that may take some time to complete.
			</li>
			<li>
				<span class="term">Synonymizer</span>. Use this plugin to create unique titles and descriptions of your
				content objects such as videos and photo albums. You need to define a list of known words and their
				synonyms. Based on this list, the plugin will replace the words found in object titles and / or
				descriptions with randomly chosen synonyms. The plugin handles word forms with different cases
				automatically, so you only need to build the synonymizer dictionary using words in lower case.
			</li>
			<li>
				<span class="term">Content stats</span>. This plugin displays summary stats for all videos and photo
				albums grouped by their formats. Thus you can see how much space will be released if you drop off this
				or that format.
			</li>
		</ul>
	</div>
	<!-- ch_concepts_plugins(end) -->
	<div>
		<h2 id="section_first_steps">Getting Started</h2>
		<p>
			We hope this manual helped you understand the main features of KVS and realize which of them best suit your
			goals. Concluding the manual, we would like to share our vision on what is a good way of getting started
			with your copy of KVS after it is installed and checked.
		</p>
		<ul>
			<li>
				In content settings, set the sizes of all images KVS will handle (avatars etc.). It is important to do
				this right from the start, as later on changing the size of an image is problematic (images are not
				resized automatically).
			</li>
			<li>
				Decide whether you need to store source video files or not. You may need them if you plan to add new
				video formats later on. If you don’t have the source files, the new formats will be created based on the
				ones you already have. In most cases, you don’t need to store source files, so this option is disabled
				by default. If you do need to store source files, pay attention to different storage strategies and
				choose the one which best suits you (source files can be stored on the primary server or on the storage
				servers).
			</li>
			<li>
				Decide on the initial screenshot number and set the corresponding option in content settings
				accordingly. Number of screenshots your videos have is not a critical backend parameter and is only
				used in the site’s design to scroll through. If you want each video to have at least 5 good screenshots,
				it is a good idea to set the initial number to 8-10. Then, for each video, you can delete worse-looking
				screenshots leaving 5 better ones. If you don’t plan to go through the screenshots of each video
				manually, you don’t need to initially create more than 5 of them, so you can set this value to 5 right
				from the start.
			</li>
			<li>
				Decide which video formats you need and configure them in the settings. You don’t have to define all
				formats right from the start if you feel you may add more later on. In the future, you will always be
				able to add new formats, which will be automatically created based on your source files, or, if you
				don’t have the source files, based on the formats you have. Remember that video formats make sense only
				for the video content types that store files on your server. If you plan to launch a site based on
				hotlinked videos, embed codes and / or pseudo-videos, you don’t need to configure video formats. If you
				are not sure which format you need to use, FLV or MP4, use MP4. Check the section of this manual that
				covers video formats for more details on the differences between various formats.
			</li>
			<li>
				Decide whether you need timeline screenshots. If you do, configure the creation of timeline screenshots
				for the video formats you need (usually these are the formats shown in the player). You don’t need to
				enable timeline screenshots in the beginning. They will be created for all the videos needed as soon as
				you enable them.
			</li>
			<li>
				Decide which screenshot formats you need and configure them in settings (screenshot formats). New
				screenshot formats can be added at any time and they will be created automatically. If you want to show
				screenshots of a size not defined in your templates, you will need to modify the CSS styles and replace
				the size values in the templates. To quickly change the existing size in the templates, e.g. 240x180,
				you can use the template search function in the Website UI section of the administration panel. Enter
				‘240x180’ without any quotation marks into the field, and you will get a list of templates where this
				size was found. Then, go through each template and replace the size with the one you need.
			</li>
			<li>
				By default, KVS creates a local conversion server and local storage servers for videos and photo albums
				when installed. If you plan to store your content using CDN or separate servers, it’s a good idea to
				configure this right from the start to avoid migrating the content later on. You can add new remote
				servers easily at any time.
			</li>
			<li>
				If you want to add categories, tags, or models to your newly uploaded content, create these manually in
				the administration panel and enable corresponding plugins.
			</li>
			<li>
				Upload a few videos and see that they are processed by the conversion engine correctly. Check all the
				created video and screenshot formats.
			</li>
			<li>
				Configure the player and embed player settings you need. If you want to show sponsor-assigned ads in
				the player, use the additional content source fields to upload your ad files, and assign the player
				ads to these fields. Also, make sure you give the fields valid and understandable names in
				customization in the settings so that there is no confusion. Thus you’ll be able to get statistics on
				all the outgoing clicks (traffic going to your content sources).
			</li>
			<li>
				Before you launch your website, make sure you check your content protection settings. Use the audit
				plugin to do so.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> Before you contact the support department, make sure support department access is enabled
			in the administration panel. You can check this on the start page and quickly enable or disable this
			feature there when needed.
		</p>
	</div>
	<div>
		<h2 id="section_troubleshooting">What Do I Do If..?</h2>
		<p>
			In this section, we address the most typical issues that you may come across while using KVS:
		</p>
		<ul>
			<li>
				You forgot the password to the administration panel. Check the FAQ on our website for the link to a
				downloadable password reset script.
			</li>
			<li>
				The start page shows an error message saying some background tasks were finished with errors. Click the
				link in the notification to go to the list of background tasks. In extra actions for the task that was
				finished with errors, go to the task log. In the end of the log you will see the error. If the error
				was caused by FFmpeg or ImageMagick, contact the administrator with this information. If you are not
				sure what the reason for the error was, create a ticket and copy your entire log file there.
			</li>
			<li>
				A video has <span class="term">Error</span> status. Open the video log using the respective extra
				action for this video in the list. In the end of the log you will see the error. If the error was
				caused by FFmpeg or ImageMagick, contact the administrator with this information. If you are not sure
				what the reason for the error was, create a ticket and copy your entire log file there.
			</li>
			<li>
				The start page shows a warning saying that last Cron launch was over 15 minutes ago. Contact the
				administrator immediately as Cron stop is a serious issue. Your site will not collect any statistics,
				process payments, launch plugins, have anti-overload protection and run other background tasks.
			</li>
			<li>
				The start page shows a warning saying that Cron is launched from an incorrect directory. Contact your
				server administrators asking them to check the Cron launch command. The command needs to enter the
				directory with the Cron script first (cd /path/to/admin/include), and then call the <b>cron.php</b>
				script from this directory.
			</li>
			<li>
				The start page shows a warning saying that faults were found in database tables. Click the link in the
				warning to go to the database repair plugin. The plugin will show you the status of all database
				tables, and some of them will have faults. Use the plugins to repair the tables.
			</li>
			<li>
				The start page shows a warning that there were errors while checking storage servers. Click the link in
				the warning to go to the list of storage servers. There, you will see the error details. There are
				several types of possible errors here:
				<span class="whitespace"></span>
				<b>(a)</b> Writing to a storage directory is not possible. Most likely, file system permissions for the
				storage directory specified in the settings were changed. KVS cannot use this storage server and will
				return errors when attempts to load content or carry out other operations related to storage servers
				will be made.
				<span class="whitespace"></span>
				<b>(b)</b> The control script is not responding. Only remote storage servers can have this error. It
				usually happens when the <b>remote_control.php</b> script copied to the root directory of a remote
				server is not available or returns errors. As a result, content will not be served from this server.
				Make sure the control script returns the <b>connected</b> response when launched.
				<span class="whitespace"></span>
				<b>(c)</b> Time is not synchronized. This error happens when time on the remote storage server is
				different from the time on the primary server. Set the correct time offset for this server in storage
				server settings.
				<span class="whitespace"></span>
				<b>(d)</b> Server is unavailable or is not configured correctly. This error is usually the result of
				regular background content serving checks. Run the content serving check manually for the server where
				the error arises and check the log for details.
				<span class="whitespace"></span>
				<b>(e)</b> CDN control script does not exist. This error means that for some reason the CDN control
				script for this server was deleted. It is supposed to be located in the <b>/admin/cdn</b> directory and
				is copied there when a storage server is set up. You need to contact your CDN solution provider and ask
				for the script that integrates with KVS. Copy the script to this directory.
				<span class="whitespace"></span>
				<b>(f)</b> No more free space. The storage server does not have enough free space to add new content.
			</li>
			<li>
				The start page shows a warning saying there were errors while checking the conversion servers. Click
				the link in the warning to go to the list of conversion servers. There, you will see the error details.
				There are several types of possible errors here:
				<span class="whitespace"></span>
				<b>(a)</b> Writing to a conversion directory is not possible. Most likely, file system permissions for
				the conversion directory specified in the settings were changed. KVS cannot use this conversion server
				and will return errors when attempts to upload videos or carry out other operations related to
				conversions servers will be made.
				<span class="whitespace"></span>
				<b>(b)</b> The conversion script is not working. This error means that the <b>remote_cron.php</b>
				script cannot be launched. KVS will load tasks to this server but they will not be completed until the
				issue is fixed. Contact the administrator asking to have your Cron conversion script scheduled to run
				once a minute.
				<span class="whitespace"></span>
				<b>(c)</b> The conversion script was last launched over 15 minutes ago. For some reason Cron jobs
				stopped on the conversion server. KVS will load tasks to this server but they will not be completed
				until the issue is fixed. Contact the administrator asking to have your Cron conversion script
				scheduled to run once a minute.
				<span class="whitespace"></span>
				<b>(d)</b> Paths to certain libraries are incorrect on the server. This error means that the
				<b>config.properties</b> file on the conversion server has incorrect links to one of the server
				libraries, or this library stopped working. Ask your server administrators to fix the paths or
				reinstall the faulty libraries.
			</li>
			<li>
				Content is not shown on the site. For your content to show on the site, it needs to have active status
				and its publishing date needs to be less than the current server date. Check whether the content is
				scheduled to appear on the site later. Also, if you are not currently logged into the member area or
				are using a different browser to visit the site, you may be seeing a cached version. In some time
				(according to your caching settings) the cache will be updated and the content will appear on the site.
			</li>
		</ul>
	</div>
</div>