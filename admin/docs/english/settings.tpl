<div id="documentation">
	<h1 id="section_settings">Settings</h1>
	<h2 id="section_settings_contents">Contents</h2>
	<div class="contents">
		<a href="#section_settings_intro" class="l2">Introduction</a><br/>
		<a href="#section_personal_settings" class="l2">Personal Settings</a><br/>
		<a href="#section_content_settings" class="l2">Content Settings</a><br/>
		<a href="#section_content_settings_images" class="l3">Image Settings</a><br/>
		<a href="#section_content_settings_conversion_engine" class="l3">Conversion Engine Settings</a><br/>
		<a href="#section_content_settings_video" class="l3">Video Settings</a><br/>
		<a href="#section_content_settings_video_protection" class="l3">Video Download Script Protection Settings</a><br/>
		<a href="#section_content_settings_rotator" class="l3">Rotator Settings</a><br/>
		<a href="#section_content_settings_album" class="l3">Album Settings</a><br/>
		<a href="#section_content_settings_add_video" class="l3">Video Adding / Editing Settings in Admin Panel</a><br/>
		<a href="#section_content_settings_add_album" class="l3">Album Adding / Editing Settings in Admin Panel</a><br/>
		<a href="#section_content_settings_api" class="l3">API Settings</a><br/>
		<a href="#section_website_settings" class="l2">Website Settings</a><br/>
		<a href="#section_memberzone_settings" class="l2">Member Area Settings</a><br/>
		<a href="#section_customization" class="l2">Customization</a><br/>
		<a href="#section_formats" class="l2">Formats</a><br/>
		<a href="#section_formats_videos" class="l3">Video Formats</a><br/>
		<a href="#section_formats_screenshots" class="l3">Screenshot Formats</a><br/>
		<a href="#section_formats_albums" class="l3">Album Formats</a><br/>
		<a href="#section_multiserver" class="l2">Multi-Server Support</a><br/>
		<a href="#section_multiserver_storage" class="l3">Storage Servers</a><br/>
		<a href="#section_multiserver_storage_group" class="l3">Storage Server Groups</a><br/>
		<a href="#section_multiserver_storage_test" class="l3">Checking Content Serving</a><br/>
		<a href="#section_multiserver_conversion" class="l3">Conversion Servers</a><br/>
		<a href="#section_languages" class="l2">Localization</a><br/>
	</div>
	<h2 id="section_settings_intro">Introduction</h2>
	<p>
		Use the <span class="term">Settings</span> section to configure all the system settings KVS has, with the 
		exception of settings related to the way content is displayed on your site. For these settings, go to the 
		<span class="term">Website UI</span> section. They are covered in a 
		<a href="KVS_UG_website_ui.html">separate manual</a>.
	</p>
	<p class="important">
		<b>Important!</b> Changing some settings may lead to a situation when some features of your website and / or
		the admin area stop working.
	</p>
	<h2 id="section_personal_settings">Personal Settings</h2>
	<!-- ch_settings_personal_settings(start) -->
	<div>
		<p>
			Use personal settings to configure the way you interact with the KVS administration panel. Administrators
			can have their own settings. Most personal settings do not require any further explanation, so let us
			address only a few of them:
		</p>
		<ul>
			<li>
				<span class="term">Disable IP protection</span>: this disables IP-based administrator session
				protection. Enable this parameter only if your IP changes as you use the KVS administration panel.
				Whenever the IP changes, KVS ends the current session; unless this parameter is turned on, you will
				need to re-login into the administration panel.
			</li>
			<li>
				<span class="term">Video edit page display mode</span>: this lets you choose one of the pre-defined
				display modes for the video edit page. When working with site text or video categorization, we
				recommend using the <b>Content writer</b> mode where all unnecessary fields are hidden.
			</li>
			<li>
				<span class="term">Screenshot display</span>: this lets you set up the way screenshots are displayed on
				the video edit page. By default, they are not displayed. You can enable them and choose the screenshot
				format which suits your needs best.
			</li>
			<li>
				<span class="term">Video list columns</span>: this lets you set up the way video list looks in the
				administration panel. If you enable thumbnail display, you will also need to choose the screenshot
				format and set the size thumbnails will be scaled down to when displayed.
			</li>
			<li>
				<span class="term">Album edit page display mode</span>: this lets you choose one of the pre-defined
				display modes for the album edit page. When working with site text or photo album categorization, we
				recommend using the <b>Content writer</b> mode where all unnecessary fields are hidden.
			</li>
			<li>
				<span class="term">Image display</span>: this lets you set up the way photos are displayed on the photo
				album edit page. By default, they are not displayed. You can enable them and choose the album format
				which suits your needs best.
			</li>
			<li>
				<span class="term">Album list columns</span>: this lets you set up the way photo album list looks in
				the administration panel. If you enable thumbnail display, you will also need to choose the photo
				album format and set the size thumbnails will be scaled down to when displayed.
			</li>
			<li>
				<span class="term">User list columns</span>: this lets you set up the way user list looks in the
				administration panel.
			</li>
		</ul>
	</div>
	<!-- ch_settings_personal_settings(end) -->
	<h2 id="section_content_settings">Content Settings</h2>
	<!-- ch_settings_content_settings(start) -->
	<div>
		<h3 id="section_content_settings_images">Image Settings</h3>
		<p>
			In this section of the settings, you configure sizes of images the KVS engine should use (with the
			exception of video screenshots and photos). All image sizes are set as NxM, where N is image width and M is
			image height.
		</p>
		<p class="important">
			<b>Important!</b> When you make any changes in this section, no images on the server will be converted
			automatically. Thus, for instance, when you change user avatar size, all avatars uploaded after you made
			the change will have the new size. No changes will be made to existing avatars.
		</p>
		<p>
			Please find a detailed explanation of the options below (some of the options are available only in some
			feature packages):
		</p>
		<ul>
			<li>
				<span class="term">User avatar size</span>: fixed size for user avatars. When uploaded in user or
				administration areas, the images will be scaled down to the size set here. Uploading an image smaller
				than the size specified will result in an error.
			</li>
			<li>
				<span class="term">Category avatar size</span>: fixed size for category avatars. When uploaded in the
				administration panel, the images will be scaled down to the size set here. The same size is used for
				avatars of category groups. Uploading an image smaller than the size specified will result in an error.
			</li>
			<li>
				<span class="term">Model screenshot size</span>: fixed size for model screenshots. Models support 2
				screenshot sizes, so you can select the processing option for the second screenshot. If you choose
				<span class="term">Autocreate based on size #1</span>, when you upload a source image into the
				screenshot 1 field, screenshot 2 will be created from that file. This lets you have two similar images
				of different sizes. If you choose <span class="term">Upload manually</span>, screenshot 2 will be
				created only if you upload the image manually. This lets you have two different images. Uploading an
				image smaller than the size specified will result in an error.
			</li>
			<li>
				<span class="term">Channel screenshot size (or DVD cover size)</span>: fixed size for channel
				screenshots / DVD covers. Channels / DVDs support 2 screenshot sizes, so you can select the processing
				option for the second screenshot. If you choose <span class="term">Autocreate based on size #1</span>,
				when you upload a source image into the screenshot 1 field, screenshot 2 will be created from that
				file. This lets you have two similar images of different sizes. If you choose
				<span class="term">Upload manually</span>, screenshot 2 will be created only if you upload the image
				manually. This lets you have two different images. Uploading an image smaller than the size specified
				will result in an error.
			</li>
			<li>
				<span class="term">Channel group screenshot size (or DVD group cover size)</span>: fixed size for
				channel group screenshots / DVD group covers. Channel groups / DVD groups support 2 screenshot sizes,
				so you can select the processing option for the second screenshot. If you choose
				<span class="term">Autocreate based on size #1</span>, when you upload a source image into the
				screenshot 1 field, screenshot 2 will be created from that file. This lets you have two similar images
				of different sizes. If you choose <span class="term">Upload manually</span>, screenshot 2 will be
				created only if you upload the image manually. This lets you have two different images. Uploading an
				image smaller than the size specified will result in an error.
			</li>
		</ul>
		<h3 id="section_content_settings_conversion_engine">Conversion Engine Settings</h3>
		<p>
			Please find a detailed explanation of the options below:
		</p>
		<ul>
			<li>
				<span class="term">Enable pause mode for background tasks</span>: when you enable this, the
				conversion mode will start switching to pause mode. The time required for this depends on the number
				and type of background tasks that have been partially launched. After all launched tasks are completed,
				the engine will switch to pause mode displaying a warning on the start page and a prompt in
				administration panel header.
			</li>
			<li>
				<span class="term">Background tasks priority</span>: this lets you choose a priority setting for
				resource-consuming tasks that are handled by the primary server (<b>realtime</b>, <b>high</b>,
				<b>medium</b>, <b>low</b>, <b>very low</b>). You can configure each of the conversion servers
				separately in the same way.
			</li>
			<li>
				<span class="term">Minimum free disc space for primary server</span>: this sets the limit for free
				disk space on the primary server. When this limit is reached, all content-importing tasks will be
				suspended to prevent server overload resulting from lack of free disk space.
			</li>
			<li>
				<span class="term">Minimum free disc space for storage server group</span>: this sets the limit for
				free disk space on the storage servers. When this limit is reached, you will no longer be able to add
				content to your server belonging to a certain server group.
			</li>
		</ul>
		<h3 id="section_content_settings_video">Video Settings</h3>
		<p>
			Please find a detailed explanation of the options below:
		</p>
		<ul>
			<li>
				<span class="term">Initial video rating</span>: rating assigned to a video when it is created.
			</li>
			<li>
				<span class="term">Initial video server group</span>: this lets you configure the way a group of
				servers will be selected for storing videos. Choose between automatic selection of a group with most
				free space, random selection, or a specific server group.
			</li>
			<li>
				<span class="term">Save source files</span>: enable this, and source video files will be saved on the
				primary server. Source video files may be used later when you want to add new video formats or grab
				screenshots manually. Storing source files is not required to create new video formats, as files of
				existing video formats can be used instead. However, if they are, the quality of the files of a new
				format will very much depend on the quality of your existing video formats, not on the quality of
				original videos.
			</li>
			<li>
				<span class="term">Detect video duration from</span>: this lets you select the video file type to be
				used when detecting duration of standard and premium videos. You can choose between source files and
				files of certain video formats.
			</li>
			<li>
				<span class="term">Screenshot count</span>: initial number of overview screenshots to be created
				automatically when all videos undergo initial processing. You can set a fixed number, and in this case
				each video will have the same number of overview screenshots, or you can use an interval in seconds.
				This will be the interval between points in a video when screenshots are taken. Thus, number of
				screenshots per video will vary depending on video duration.
			</li>
			<li>
				<span class="term">Screenshot cropping</span>: this lets you configure cropping when creating video
				screenshots. This is set for each size of an image, either in pixels, or in % from original size.
				Cropping occurs before screenshots are scaled down to the size set in the settings.
			</li>
			<li>
				<span class="term">First screenshot offset</span>: offset in seconds from the video start before the
				first screenshot is taken. Use this if you want to avoid grabbing screenshots from credits or other
				static content displayed in the beginning of your videos.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> Cropping is used only for the screenshots the engine creates automatically. When you
			upload manually created screenshots, you will need to crop these yourself.
		</p>
		<h3 id="section_content_settings_video_protection">Video Download Script Protection Settings</h3>
		<p>
			Video file protection settings are used together with server-sized protection (X-Accel-Redirect for Nginx)
			to protect your videos from hotlinking and unauthorized access.
		</p>
		<p>
			To configure full-featured protection, the directory where your video files are stored needs to be declared
			as internal in the Nginx configuration (use the internal directive to achieve this). If you use multiple
			video storage servers, you need to configure video storage directories in the same way on all of them. For
			internal Nginx directories, direct access to files in this directory is forbidden. This makes files
			available only to the <b>get_file.php</b> serving script, where all security checks are built in. Do the
			following to declare a directory as internal in your Nginx configuration:
		</p>
		<p class="code">
			location ^~ /contents/videos/ {<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;mp4; <span class="comment"># enabling MP4 streaming</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;root /usr/home/clients/ftp0/domains/kernel-tube.com/html; <span class="comment"># specifying full path to /contents/videos/</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disallowing access via direct links</span><br/>
			}
		</p>
		<p>
			KVS features the following video file protection options and settings:
		</p>
		<ul>
			<li>
				<span class="term">Enable protection for video download script</span>: this enables protection of the
				<b>get_file.php</b> serving script from hotlinking and content parsing. Use this, and links to the
				<b>get_file.php</b> script in your site will become temporary and will not work on third party sites
				that attempt hotlinking. When this protection is triggered, the site will return the file located at
				the URL configured below.
			</li>
			<li>
				<span class="term">IP limit</span>: this works when <b>get_file.php</b> anti-hotlinking protection is
				enabled. With this setting, you can configure the number of videos to be available from the same IP
				address within a period of time you specify. All requests to the download script are included here,
				including skipping through video. When this protection is triggered, the site will return the file
				located at the URL configured below.
			</li>
			<li>
				<span class="term">Render file from this URL instead of video</span>: the file located at this URL will
				be returned instead of a video file when <b>get_file.php</b> protection is triggered. If this is left
				blank, the script will return a 403 (Forbidden) error. Here, you need to specify a link to a video file
				so that it is displayed correctly in the player. If you want to display a static text, you can use the
				<b>Movie from image</b> plugin that will create a video file of a specified duration from an image you
				upload.
			</li>
			<li>
				<span class="term">Project IP</span>: this is the IP address used by your site for test video requests.
				This IP is never blocked by the protection system.
			</li>
			<li>
				<span class="term">IP white list</span>: here, you can configure IP address masks for IPs that are not
				to be blocked by the protection system regardless of how many requests come from these IPs.
			</li>
			<li>
				<span class="term">Blocked IPs</span>: this is the list of IPs that are currently blocked. The list can
				change every 5 minutes, as every 5 minutes the engine rebuilds the IP blacklist.
			</li>
		</ul>
		<p>
			You will need to check your protection settings for each of the storage servers you have. For each server
			in the storage server list shown in the administration panel, there is a feature that you can yse to check
			content serving from this server. Among other things, this feature also tests whether content protection
			for the server is set up correctly.
		</p>
		<p>
			Protection can be disabled for each individual video format. Use the settings of this format to do so.
		</p>
		<p class="important">
			You need to enable video downloading in format settings to let users watch your videos from mobile devices.
			This decreases overall protection level for your video content.
		</p>
		<h3 id="section_content_settings_rotator">Rotator Settings</h3>
		<p>
			Rotator settings let you enable the video and screenshot rotator, and configure the way it works.
			Screenshot rotator can be enabled only together with video rotation.
		</p>
		<p class="important">
			<b>Important!</b> Using the rotator increases your server load (HDD and CPU are used more intensively).
		</p>
		<p>
			Here is a detailed explanation of the options:
		</p>
		<ul>
			<li>
				<span class="term">Enable video rotator</span>: enables video rotation on your site.
			</li>
			<li>
				<span class="term">Enable screenshot rotator</span>: enables video screenshot rotation on your site.
				Use options below to configure the screenshot rotator.
			</li>
			<li>
				<span class="term">Min video views</span>: the first from the two rotation completion criteria. A video
				needs to have this many views for the rotator to finish rotating the screenshots for this video.
			</li>
			<li>
				<span class="term">Min video clicks</span>: second rotation completion criterion. For a video with this
				many clicks, screenshot rotations will be set to completed.
			</li>
			<li>
				<span class="term">Delete screenshots</span>: this lets you have less clickable screenshots of a video
				deleted after screenshot rotation for this video is completed. If you want to delete some screenshots,
				you need to set the number of screenshots to be left after screenshot rotation.
			</li>
			<li>
				<span class="term">Completion</span>: shows the way your videos are distributed according to rotation
				completion degree. 5 increment intervals are used (0%-20%, 21%-40%, 41%-60%, 61%-80%, 81%-100%), and 1
				final completion degree (100%), which, when clicked, takes you to the list of videos for which
				screenshot rotation has been completed. When you change any of the two rotation completion criteria,
				this graph may look different.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> When the rotator is enabled, it starts working in a gradual way. This is a process that
			depends on your site’s caching settings. The rotator will be in full operation only when all cache
			refreshes. You can speed this up by resetting site cache (Smarty and MemCache) manually in
			<span class="term">Website UI</span>. Remember that resetting the cache will lead to a rapid site load
			increase.
		</p>
		<p>
			After you enable the rotator, you may notice that links to viewing pages now have the <b>?pqr</b> parameter
			which contains data needed for the rotator to process clicks. If you want to remove this parameter from
			your links, you can configure your site in such a way that click parameters will be sent via JavaScript.
			Check the technical FAQ to find out more about the procedure here.
		</p>
		<h3 id="section_content_settings_album">Album Settings</h3>
		<p>
			This group of settings is used to set global options for albums. This is available only in the Ultimate
			package. Please find the explanation of the options below:
		</p>
		<ul>
			<li>
				<span class="term">Initial album rating</span>: rating assigned to an album when it is created.
			</li>
			<li>
				<span class="term">Initial album server group</span>: this lets you configure the way a group of
				servers will be selected for storing photo albums. Choose between automatic selection of a group with
				most free space, random selection, or a specific server group.
			</li>
			<li>
				<span class="term">Image cropping</span>: these cropping settings are used when album formats are
				created from the original images. For each image size, this is set either in pixels or in % from the
				original size. Cropping occurs before the images are scaled down to a required size.
			</li>
			<li>
				<span class="term">Create ZIPs with source images</span>: this lets you create ZIP archives with source
				images of your albums. When enabled, this will create a background task that will create archives for
				all existing photo albums.
			</li>
			<li>
				<span class="term">Access to source images for users</span>: this lets you specify users of which types
				can physically access source files of album images via the serving script. By default, site users do
				not have access to source files. If you want to allow source file access for all users, you need to
				also make sure your source images are available via direct links on all your storage servers. Use the
				content serving test feature that checks this and shows an error if direct links do not work. Direct
				links work when the rule in the Nginx configuration is set accordingly.
			</li>
		</ul>
		<h3 id="section_content_settings_add_video">Video Adding / Editing Settings in Admin Panel</h3>
		<p>
			This group of settings lets you set up certain default values used when adding or editing videos in the
			administration panel.
		</p>
		<p>
			The explanation of the options follows below:
		</p>
		<ul>
			<li>
				<span class="term">Default user for adding video</span>: this sets the user by whom the videos in the
				administration panel will be added by default. While adding a video, you will be able to select any
				other user. If you need to upload a video from multiple users, use importing where lists of users can
				be used.
			</li>
			<li>
				<span class="term">Default status for adding video</span>: this sets the default video status. While
				adding a video, you will be able to select any other status when needed.
			</li>
			<li>
				<span class="term">Publishing time</span>: this configures the time in the video publishing date. When
				adding a video, you will be able to set any time for any particular video to be published on the site.
			</li>
			<li>
				<span class="term">Re-generate directories automatically</span>: this disables manual editing of video
				directory and forces the directory to be re-generated each time the video title is changed. When this
				is disabled, the video directory will not change even when video title changes. We do not recommend
				enabling this option, unless you use numeric IDs in links to video pages (they are used by default).
				When the directory changes, old links to the video page will no longer work. If the links contain
				numeric IDs, changing the directory will not make the links invalid.
			</li>
		</ul>
		<h3 id="section_content_settings_add_album">Album Adding / Editing Settings in Admin Panel</h3>
		<p>
			This group of settings lets you set up certain default values used when adding or editing photo albums in
			the administration panel. This is available only in the Ultimate package.
		</p>
		<p>
			The explanation of the options follows below:
		</p>
		<ul>
			<li>
				<span class="term">Default user for adding album</span>: this sets the user by whom the photo albums in
				the administration panel will be added by default. While adding an album, you will be able to select
				any other user. If you need to upload photo albums from multiple users, use importing where lists of
				users can be used.
			</li>
			<li>
				<span class="term">Default status for adding album</span>: this sets the default photo album status.
				While adding an album, you will be able to select any other status when needed.
			</li>
			<li>
				<span class="term">Publishing time</span>: this configures the time in the photo album publishing date.
				When adding photo albums, you will be able to set any time for any particular photo album to be
				published on the site.
			</li>
			<li>
				<span class="term">Re-generate directories automatically</span>: this disables manual editing of photo
				album directory and forces the directory to be re-generated each time the photo album title is changed.
				When this is disabled, the photo album directory will not change even when photo album title changes.
				We do not recommend enabling this option, unless you use numeric IDs in links to photo album pages
				(they are used by default). When the directory changes, old links to the photo album page will no
				longer work. If the links contain numeric IDs, changing the directory will not make the links invalid.
			</li>
		</ul>
		<h3 id="section_content_settings_api">API Settings</h3>
		<p>
			KVS offers basic API functionality that lets you use outer scripts that create site users and assign
			premium status to them (and remove such status as well). You can use the API to integrate KVS with your
			other sites when needed. See the <b>/admin/api/kvs_api.php</b> script to find out more about the parameters
			the API receives.
		</p>
		<p>
			The explanation of the options follows:
		</p>
		<ul>
			<li>
				<span class="term">Enable API</span>: use this to enable API for your site.
			</li>
			<li>
				<span class="term">API password</span>: this creates a non-empty key that will be required for your API
				to accept outside requests.
			</li>
			<li>
				<span class="term">API access URL</span>: the URL used to access your API. This differs for different
				domains.
			</li>
		</ul>
	</div>
	<!-- ch_settings_content_settings(end) -->
	<h2 id="section_website_settings">Website Settings</h2>
	<!-- ch_settings_website_settings(start) -->
	<div>
		<p>
			Site settings let you configure global aspects of your site. Some options are available only in certain
			feature packages. See explanation of the options below:
		</p>
		<ul>
			<li>
				<span class="term">Disable website</span>: this lets you fully disable your site for outside users. KVS
				administrators will still be able to see the site without any limitations. Outside users will be
				redirected to <b>/website_unavailable.html</b>, a static page the contents of which you can customize.
			</li>
			<li>
				<span class="term">Dynamic HTTP parameters</span>: this table lets you specify up to 5 dynamic HTTP
				parameters along with their default values. These parameters will be used by the site’s engine on all
				pages as well as in links to content sources and payment pages, even when caching is used. Dynamic
				HTTP parameters are used in situations when you need to accept a HTTP parameter from the outside (and
				the parameter is not supported by the engine) and display its value on your site or insert it into your
				links to content sources and / or payment processors. For example, let’s address a situation when you
				need to accept webmaster traffic and insert their ID to your links to ads and / or payment pages. When
				caching is enabled, you cannot do this without using dynamic HTTP parameters. To insert the value of a
				<b>param1</b> parameter (for example) use the <b>%param1%</b> token in the page template or URL. Then,
				for a user who visits a link given below, the <b>%param1%</b> token will be replaced with
				<b>123456</b>:
				<span class="code">
					http://your_domain.com/page/?param1=123456
				</span>
				<span class="important">
					<b>Important!</b> Using dynamic HTTP parameters may slightly decrease the performance of your site.
					Make sure you configure only the parameters you actually use.
				</span>
			</li>
			<li>
				<span class="term">XXX page URL pattern</span>: this sets up the pattern to be used for links to
				viewing pages for objects of various types (videos, photo albums, models, content sources etc.). This
				pattern is used in the administration panel to quickly go to the viewing page. It is also used by the
				site engine to create links to viewing pages. The pattern needs to have the <b>%DIR%</b> token which
				will be replaced with the object’s directory value, and / or the <b>%ID%</b> token to be replaced with
				the object ID. For photos, you can also use the <b>%IMG%</b> token which is replaced with the photo’s
				ID.
				<span class="important">
					<b>Important!</b> When you change these patterns, you need to add new rules to your root htaccess
					file. New links will not work unless the rule is added. Also, make sure you keep the old rule as
					there can be incoming links to your site which use older values.
				</span>
				Patters of secondary objects are not required, as such objects do not always need viewing pages. When
				you specify a pattern, you can use the <b>{{$smarty.ldelim}}$item.view_page_url{{$smarty.rdelim}}</b> variable in the template of this
				object’s list block. This variable contains the link with tokens replaced with their actual values.
			</li>
			<li>
				<span class="term">Synchronize user status</span>: this enables synchronization of user statuses with
				the database, and sets the interval for it. When your site has a member area, premium users can have
				expired memberships while they are still on the site, or their access to certain content purchased for
				tokens can expire. This situation does not occur very often, therefore, you don’t need to query the
				database with each request to check whether the access has expired or not. This option lets you set an
				interval in minutes. This will be the interval between updates of user status from the database. If
				your site does not have a member area or token-based access, we recommend disabling this setting.
			</li>
			<li>
				<span class="term">Synchronize user online status</span>: this enables synchronization of ‘user online’
				statuses with the database, and sets the interval for it. After a user enters the member area, once
				every 60 seconds a small script is run that pings the server. On the server, such ping can be recorded
				in the database, which would mean the user is currently online on the site. If you need to use this
				information anywhere on your site, or you would like to obtain site usage statistics that would include
				user activity duration, enable this option. The interval defines the period of time between instances
				when ‘user is online’ statuses will be saved to the database.
			</li>
			<li>
				<span class="term">Synchronize user unread messages</span>: this enables synchronization of internal
				user messages with the database, and sets the interval for it. As users navigate the site, it makes
				little sense to constantly check whether they have new messages. You can enable this and set the
				interval between checks for new messages, e.g. in order to show a notification in site header.
			</li>
			<li>
				<span class="term">Stop words</span>: the list of words not allowed on your site, separated by commas.
				When users create any content on the site (comments, wall posts, videos, or photo albums), stop words
				will be replaced with replacement word (e.g. [censored]). Use this to avoid search engine bots
				detecting certain words on your site and banning it from the SERP. In internal search, stop words will
				be removed from all search queries.
			</li>
			<li>
				<span class="term">Replacement</span>: the word all stop words will be replaced with.
			</li>
		</ul>
	</div>
	<!-- ch_settings_website_settings(end) -->
	<h2 id="section_memberzone_settings">Member Area Settings</h2>
	<!-- ch_settings_memberzone_settings(start) -->
	<div>
		<p>
			Member area settings are used to configure global aspects of the way your site’s member area functions.
			This is available starting from the Advanced package. Please find the explanation of the options below:
		</p>
		<ul>
			<li>
				<span class="term">User status after premium</span>: this defines what status is assigned to users when
				their premium memberships expire or transactions are declined. Users with status set to
				<span class="term">Active</span> can log into the member area (unless this is disallowed in the
				settings of the login block), so in this case your site will need to display content correctly to
				non-premium users. If you choose to set the <span class="term">Disabled</span> status, users with
				expired premium memberships will not be able to log into the member area.
			</li>
			<li>
				<span class="term">Purchasing standard videos for tokens</span>: this lets your users use tokens to
				purchase access to standard videos, and sets the default price for such videos in tokens. If you need,
				you can set different prices for any specific videos.
			</li>
			<li>
				<span class="term">Purchasing premium videos for tokens</span>: this lets your users use tokens to
				purchase access to premium videos, and sets the default price for such videos in tokens. If you need,
				you can set different prices for any specific videos.
			</li>
			<li>
				<span class="term">Purchasing standard albums for tokens</span>: this lets your users use tokens to
				purchase access to standard photo albums, and sets the default price for such photo albums in tokens.
				If you need, you can set different prices for any specific photo albums.
			</li>
			<li>
				<span class="term">Purchasing premium albums for tokens</span>: this lets your users use tokens to
				purchase access to premium photo albums, and sets the default price for such photo albums in tokens. If
				you need, you can set different prices for any specific photo albums.
			</li>
			<li>
				<span class="term">Purchase nulled after N days</span>: this sets maximum duration of access to
				purchased content. When a non-zero value is used here, user purchases will be nulled after this many
				days. If you change this value, the change will affect only new purchases made from then on.
			</li>
			<li>
				<span class="term">Activity index formula</span>: configures math formula used to calculate activity
				index for each user. Activity index is an abtract number, which ranks users according to their activity
				on your site: the more activity index is, the better rank will be assigned to a user. For example the
				user with rank 1 is the most active and etc. You can use the following tokens in this formula as
				operands:
				<span class="code">
					<b>%videos_visited%</b> - the number of watched videos<br/>
					<b>%albums_visited%</b> - the number of viewed albums<br/>
					<b>%videos_comments%</b> - the number of comments posted for videos<br/>
					<b>%albums_comments%</b> - the number of comments posted for albums<br/>
					<b>%cs_comments%</b> - the number of comments posted for content sources<br/>
					<b>%models_comments%</b> - the number of comments posted for models<br/>
					<b>%dvds_comments%</b> - the number of comments posted for DVDs / channels<br/>
					<b>%posts_comments%</b> - the number of comments posted for posts<br/>
					<b>%playlists_comments%</b> - the number of comments posted for playlists<br/>
					<b>%total_comments%</b> - the number of total comments posted<br/>
					<b>%logins%</b> - the number of times user logged into memberzone<br/>
					<b>%public_videos%</b> - the number of uploaded public videos<br/>
					<b>%private_videos%</b> - the number of uploaded private videos<br/>
					<b>%premium_videos%</b> - the number of uploaded premium videos<br/>
					<b>%total_videos%</b> - the number of total videos uploaded<br/>
					<b>%favourite_videos%</b> - the number of favourite videos<br/>
					<b>%public_albums%</b> - the number of uploaded public albums<br/>
					<b>%private_albums%</b> - the number of uploaded private albums<br/>
					<b>%premium_albums%</b> - the number of uploaded premium albums<br/>
					<b>%total_albums%</b> - the number of total albums created<br/>
					<b>%favourite_albums%</b> - the number of favourite albums<br/>
				</span>
				When needed you can use any math operations with these operands. Usually in formula you need to assign
				weights to diffirent operands and finally summarize them:
				<span class="code">
					%total_videos%*20 + %total_albums%*20 + %total_comments%*10 + %logins%
				</span>
			</li>
			<li>
				<span class="term">Exclude users from activity index</span>: this lets you exclude some specific
				users from activity ranking. As all content created in administration panel is actually created on
				behalf of certain site users, these users can take the topmost ranks in activity ranking. But they
				are not 'real site users'. Normally such users should be excluded so that real users are motivated to
				win the topmost ranks for their activities.
			</li>
			<li>
				<span class="term">Activity awards</span>: here, you can set up a system of token awards your users get
				for various activities on the site. Later, they can use tokens to purchase access to premium content.
				In this table, token amounts are defined for awards users get for different types of activity. For some
				activity types, you can set minimum activity completion criteria, e.g. minimum uploaded video duration,
				minimum comment length etc.
			</li>
		</ul>
	</div>
	<!-- ch_settings_memberzone_settings(end) -->
	<h2 id="section_customization">Customization</h2>
	<!-- ch_settings_customization(start) -->
	<div>
		<p>
			In Customization, you can enable additional fields for certain objects. These fields can be used in the
			administration panel and on the site as well. If you enable certain fields for some of the objects, they
			will not show on the site automatically. To use them in this way, you will need to modify the corresponding
			templates manually.
		</p>
		<p>
			When you enable additional fields, you can also set their names to be displayed in the administration panel
			(and nowhere else!).
		</p>
		<p>
			Here are some examples of how you can use additional fields:
		</p>
		<ul>
			<li>
				For categories, 2 additional fields are enabled by default. These let you set the title and description
				to be used in the metadata of the HTML page (SE-related fields). These values will be used (unless the
				fields are empty) for each category on the video list page of this category.
			</li>
			<li>
				For content sources, you can use a big variety of fields to offer full-featured reviews of your
				sponsors and virtually any other promos.
			</li>
			<li>
				Additional fields for feedback can be enabled and set to required in the <b>feedback</b> block. In this
				case, users will be required to fill these fields in when submitting the feedback form.
			</li>
		</ul>
	</div>
	<!-- ch_settings_customization(end) -->
	<h2 id="section_formats">Formats</h2>
	<!-- ch_settings_formats_videos(start) -->
	<div>
		<h3 id="section_formats_videos">Video Formats</h3>
		<p>
			Video formats are split into 2 groups, which lets you set up different sets of video files for standard and
			premium videos on your site. Standard videos can also be private or public. The Basic package does not
			feature multi-format support, letting you configure only 1 video format.
		</p>
		<p>
			Please find the description of video format fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: format name to be used in the administration panel, or on the site, if
				you decide to use it there.
			</li>
			<li>
				<span class="term">Postfix</span>: unique format ID that has to be the last part of file name with
				extension. The postfix is used in video file names, with video numeric IDs coming before them. Postfix
				examples: <b>.flv</b>, <b>.mp4</b>, <b>_hq.mp4</b>, <b>premium.wmv</b> etc. Correspondingly, video file
				names for the postfixes listed here will be <b>123.flv</b>, <b>123.mp4</b>, <b>123_hq.mp4</b>,
				<b>123premium.wmv</b> etc. You cannot modify a postfix after you have created the video format.
			</li>
			<li>
				<span class="term">Video type</span>: video format group selection: standard videos or premium videos.
				After you have created the video format, you cannot select a different group here.
			</li>
			<li>
				<span class="term">Status</span>: video format status that defines the life cycle of the files of this
				format. <span class="term">Active required</span> formats will always be created automatically for all
				videos, unless files of this format are uploaded manually. <span class="term">Active optional</span>
				formats can be uploaded manually, but will not be created automatically.
				<span class="term">Disabled</span> formats will not be used when creating videos. You may need this
				status if you want to remove one of the older formats but do not want to lose existing files of this
				format. The conditional optional checkbox lets you set up optional formats that will be created
				provided the duration and the sizes of video source files allow for the creation of such format.
			</li>
			<li>
				<span class="term">Source files</span>: lets you mark a format so that files of this format are used as
				source files later on. If original source files are missing, the engine will use the files of the
				format marked with this checkbox as source files. If no such format is defined, the engine will use the
				format with the largest files.
			</li>
			<li>
				<span class="term">Video size</span>: lets you either specify a fixed video size to be used while
				creating files of this format, or make the engine keep the size of source files. If you set a fixed
				size, you can also either preserve the original video aspect ratio (then either width or height will be
				dynamic depending on the option selected), or force a fixed size to be used (then cropping will occur
				on video edges to force the format aspect ratio).
			</li>
			<li>
				<span class="term">FFmpeg options</span>: FFmpeg options which define the quality of video and audio
				streams, set codecs, container formats, and more. You can use these basic options to start with:
				<span class="code">
					-vcodec libx264 -movflags +faststart -threads 0 -r 25 -g 50 -crf 25 -me_method hex -trellis 0 -bf 8 -acodec aac -strict -2 -ar 44100 -ab 128k -f mp4
				</span>
			</li>
			<li>
				<span class="term">Watermark image</span>: a PNG image to be put over videos.
			</li>
			<li>
				<span class="term">Watermark position</span>: lets you position the watermark on videos.
			</li>
			<li>
				<span class="term">Customize watermark for content sources</span>: this lets you customize watermarks
				for different content sources. To do so, choose the additional file field for content sources where
				watermarks will be stored. When processing videos, KVS will take the file associated with each video’s
				content source, as long as this field is filled in. Enable the corresponding field in customization
				settings to be able to upload custom watermarks for different content sources.
			</li>
			<li>
				<span class="term">Access level</span>: this lets you choose categories of users that will be able to
				access the video files of this format. Correspondingly, the files will not be available for other
				users. This is used to set up a member area on your site.
			</li>
			<li>
				<span class="term">Enable ability to download</span>: this enables downloading video files of this
				format, or playing then in the HTML5 mode (only for MP4 files). If this is disabled, the files will be
				available only via the Flash player. When this option is enabled for video formats available to all
				users, anti-hotlink and video parsing protection will become weaker.
			</li>
			<li>
				<span class="term">Disable hotlink protection</span>: allows hotlinking for the video files of this
				format in any possible way.
			</li>
			<li>
				<span class="term">Limit duration to</span>: this limits the duration of the video files of this
				format, based on seconds or % specified. With limits in %, you can also set minimum and maximum values
				in seconds.
			</li>
			<li>
				<span class="term">Number of parts</span>: this is used together with duration limit, letting you
				create trailers of specified duration from several parts of a video. The more video parts there are,
				the greater the conversion server load.
			</li>
			<li>
				<span class="term">Offset from beginning</span>: this lets you set an offset from the start of the
				video in seconds or %.
			</li>
			<li>
				<span class="term">Offset from end</span>: this lets you set an offset from the end of the video in
				seconds or %.
			</li>
			<li>
				<span class="term">Last part</span>: this lets the engine know that the last part of the trailer needs
				to be created from fragment end, not start. When you set the number of parts to >1, the engine splits
				the video into several fragments of equal duration, picking a piece of certain duration from the
				beginning of each fragment, creating a merged trailer of required duration. Enable this to have the
				last part of the trailer created from fragment end, not start.
			</li>
			<li>
				<span class="term">Customize duration for content sources</span>: this lets you enable custom format
				durations for different content sources. To do this, you need to choose the additional text field of
				content sources where the duration (in seconds) will be stored. When processing videos, KVS will use
				the duration limit in seconds from content source data, as long as this field is filled in and is a
				valid number. Enable the corresponding field in customization settings to be able to set custom
				duration for different content sources.
			</li>
			<li>
				<span class="term">Limit speed to</span>: this lets you limit file delivery speed for a particular
				format. This option works only when videos are served via Nginx.
			</li>
			<li>
				<span class="term">Create timeline screenshots</span>: this enables creation of timeline screenshots
				for video files of this format. After this option is enabled, timeline screenshot creation will be
				launched in the background for all video files of this format. When this is disabled, no existing
				timeline screenshots will be deleted. Use the context menu in format list to delete timeline
				screenshots that have already been created.
			</li>
			<li>
				<span class="term">Screenshots interval</span>: interval in seconds between instances of grabbing
				timeline screenshots.
			</li>
			<li>
				<span class="term">Directory name</span>: name of the directory where timeline screenshots for each
				video file of this format will be stored. You need to specify the name of the directory, not the path
				to it. The name needs to be unique among all video formats. It will be used to build the path to the
				directory where timeline screenshots are stored. Examples of names: <b>flv</b>, <b>mp4</b>,
				<b>mp4_premium_timelines</b> etc. After you set the directory name, you will not be able to change it.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> When you create an active required video format, or when you change a format’s status to
			active required, for each video that doesn’t have a video file of this format file creation tasks will be
			launched. With large number of videos, background tasks like this can run for days or even weeks, so please
			be careful when creating active required formats. Before you set the status of a format to active required,
			test it on several videos using mass editing.
		</p>
		<p class="important">
			<b>Important!</b> When you enable timeline screenshots for existing video formats, for each video that does
			not have timeline screenshots background tasks will be launched to create such screenshots. When
			screenshots cannot be grabbed fast enough, creation of timeline screenshots can take a long time to
			complete.
		</p>
		<p>
			When you delete a video format, all video files of this format will be deleted from all storage servers.
			Deletion will be a background task. Until it is complete, the format status will be set to
			<span class="term">Removing files</span>.
		</p>
	</div>
	<!-- ch_settings_formats_videos(end) -->
	<!-- ch_settings_formats_screenshots(start) -->
	<div>
		<h3 id="section_formats_screenshots">Screenshot Formats</h3>
		<p>
			Screenshot formats are split into 2 groups: overview and timeline screenshots. Overview screenshots are
			created for every video using the source video file. Number of overview screenshots is set globally in
			content settings. Timeline screenshots are assigned to video formats, this is why their number is
			configured in settings for each format. You need to specify the interval between the instances when
			screenshots are grabbed. Thus, each video is supposed to have at least 1 overview screenshot. Sets of
			timeline screenshots can be different for different video formats.
		</p>
		<p>
			Please find the explanation of format fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: format name to be displayed in the administration panel.
			</li>
			<li>
				<span class="term">Group</span>: format group, can be either overview or timeline. After you create a
				format, you will not be able to select a different group for it.
			</li>
			<li>
				<span class="term">Size</span>: fixed screenshot size for this format. If you want the screenshots for
				this format to have the same size as the original files, you need to set the size to <b>source</b>
				keyword. In this case, screenshot sizes can differ depending on original video size. Size is a unique
				group ID, i.e. you cannot create 2 overview screenshot formats with the same size. You cannot modify
				the size after you have created the format.
			</li>
			<li>
				<span class="term">ImageMagick options for screenshots created automatically</span>: ImageMagick
				options for processing the screenshots of this format that have been automatically grabbed from videos.
				The options need to contain tokens <b>%INPUT_FILE%</b>, <b>%OUTPUT_FILE%</b> and <b>%SIZE%</b>; these
				will be replaced with relevant paths and size when the operation is running. These ImageMagick options
				will only be used for automated video screenshot creation.
			</li>
			<li>
				<span class="term">ImageMagick options for screenshots uploaded manually</span>: ImageMagick options
				for processing the screenshots of this format that have been manually uploaded, either during video
				creation, or during setting up screenshots. The options need to contain tokens <b>%INPUT_FILE%</b>,
				<b>%OUTPUT_FILE%</b> and <b>%SIZE%</b>; these will be replaced with relevant paths and size when the
				operation is running. These ImageMagick options will only be used for manually uploaded video
				screenshots.
			</li>
			<li>
				<span class="term">Screenshot aspect ratio</span>: this is an important option that lets you configure
				screenshot scaling. You can have the screenshots grabbed from your videos to be scaled to the size
				required by a particular format. If the aspect ratio of the original video is preserved, screenshots
				may have black bars on the edges. In case the screenshot aspect ratio is adjusted to the format
				requirements, there will be no black bars. However, source screenshots will be cropped on the edges if
				needed.
			</li>
			<li>
				<span class="term">Create ZIPs</span>: this specifies whether ZIP archives with screenshots of this
				format are to be created for each video. If it is enabled for an existing format, a background ZIP
				screenshot archiving task is launched for all videos. If it is disabled for an existing format, a
				similar background ZIP archive deletion task is launched for all videos.
			</li>
			<li>
				<span class="term">Watermark image</span>: a PNG image to be put over each screenshot.
			</li>
			<li>
				<span class="term">Watermark position</span>: lets you position the watermark on the screenshot.
			</li>
		</ul>
		<p class="important">
			<b>Important!</b> When you create a new screenshot format, a background screenshot creation task will be
			launched for all videos, creating screenshots of this format. This task will be processed by the primary
			server and may take a long time to complete, depending on available server resources and total number of
			screenshots.
		</p>
		<p>
			When you delete a screenshot format, all screenshots of this format will be deleted for all the videos.
			Deletion will be launched as a background task. While it is running, the format status will be set to
			<span class="term">Removing files</span>. Before you delete a format, you will need to make sure your site
			templates do not have any more links to the screenshots of the format being deleted. To do this, use
			template search in the administration panel and search for format size in your templates (e.g.
			<b>240x180</b>).
		</p>
		<p>
			If you need to change the screenshot size you use on your site, you will need to create a new screenshot
			format with the required size, wait for the background creation task to be complete, and then switch the
			site templates to the new format. After that, it is safe to delete the old format.
		</p>
	</div>
	<!-- ch_settings_formats_screenshots(end) -->
	<!-- ch_settings_formats_albums(start) -->
	<div>
		<h3 id="section_formats_albums">Album Formats</h3>
		<p>
			Photo album formats are split into 2 groups: main image group and preview group. Files of main image group
			formats are created for each photo in the album. At the same time, files of preview group are created for
			the main photo of each album, one file per album. Album formats are supported in the Ultimate package only.
		</p>
		<p>
			Please find the description of album format fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: format name to be displayed in the administration panel.
			</li>
			<li>
				<span class="term">Group</span>: format group, can be either main or overview. After you create a
				format, you will not be able to select a different group for it.
			</li>
			<li>
				<span class="term">Size</span>: fixed image size for this format. Is not a fixed size, as unlike video
				screenshots, album photographs support dynamic sizes with various limitations. Size is a unique ID for
				a format in the group. You cannot modify the size after you have created the format.
			</li>
			<li>
				<span class="term">ImageMagick options</span>: ImageMagick options for processing the photos of this
				format. The options need to contain tokens <b>%INPUT_FILE%</b>, <b>%OUTPUT_FILE%</b> and <b>%SIZE%</b>;
				these will be replaced with relevant paths and size when the operation is running. These ImageMagick
				options will be used for any photo-related tasks.
			</li>
			<li>
				<span class="term">Image aspect ratio</span>: this is an important option that lets you configure the
				scaling of source photographs to the size set for this format. If the aspect ratio of the original
				photo is preserved, photos may either have black bars on the edges (fixed size), or the photo size will
				be smaller than the size set in the format (dynamic size). In case the photo aspect ratio is adjusted
				to the format requirements, there will be no black bars. However, source photos will be cropped on the
				edges if needed.
			</li>
			<li>
				<span class="term">Create ZIPs</span>: this specifies whether ZIP archives with images of this format
				are to be created for each photo album. If it is enabled for an existing format, a background ZIP
				archiving task is launched for all photo albums. If it is disabled for an existing format, existing ZIP
				archives will not be deleted. Here lies the difference between photo album ZIPs and video screenshot
				ZIPs. Use the context menu in the format list to delete existing ZIP archives.
			</li>
			<li>
				<span class="term">Watermark image</span>: a PNG image to be put over each photo.
			</li>
			<li>
				<span class="term">Watermark position</span>: lets you position the watermark on the photo.
			</li>
			<li>
				<span class="term">Access level</span>: this lets you choose categories of users that will be able to
				access the photos of this format. Correspondingly, the photos will not be available for other users.
				This access restriction will only work when you use protected links to photographs (via the serving
				script).
			</li>
			<li>
				<span class="term">Show this image when no access</span>: this lets you use a pre-defined image to be
				shown to users who attempt to access a photo via the serving script and do not have enough user
				privileges to do so. If this field is left blank, the serving script will return the 403 (Forbidden)
				error.
			</li>
		</ul>
		<p>
			KVS lets you use source photos on the site in the same way as photos of any format. Go to content settings
			to configure access level required to see the source photos.
		</p>
		<p class="important">
			<b>Important!</b> When you create a new photo album format, a background task creating photos of this
			format for all photo albums will be launched. This task will be processed by the primary server and may
			take a long time to complete, depending on available server resources and total number of photos.
		</p>
		<p>
			When you delete a photo album format, all photos of this format will be deleted from all the photo albums.
			Deletion will be launched as a background task. While it is running, the format status will be set to
			<span class="term">Removing files</span>. Before you delete a format, you will need to make sure your site
			templates do not have any more links to the photos of the format being deleted. To do this, use template
			search in the administration panel and search for format size in your templates (e.g. <b>120x160</b>).
		</p>
		<p>
			If you need to change the size of photos that you use on your site, you will need to create a new photo
			album format with the required size, wait for the background creation task to be complete, and then switch
			the site templates to the new format. After that, it is safe to delete the old format.
		</p>
	</div>
	<!-- ch_settings_formats_albums(end) -->
	<h2 id="section_multiserver">Multi-Server Support</h2>
	<p>
		Multi-server support lets you address seemingly unresolvable issues with insufficient disk space and server
		connection speed any rapidly growing sites face at this or that stage. In addition to issues related to content
		storage and serving, multi-server support lets you use multiple conversion servers to speed up background tasks
		and decrease primary server load.
	</p>
	<!-- ch_settings_multiserver_storage(start) -->
	<div>
		<h3 id="section_multiserver_storage">Storage Servers</h3>
		<p>
			Storage servers are used to store and serve video and photo content. In most cases, the content is stored
			on the same server as the site that uses it. Sometimes it makes sense to migrate the content to a separater
			server with a special configuration optimized for faster content serving. This can decrease primary server
			load. Also, in certain cases it is a good idea to serve the same content from several different servers to
			balance the load between them. This is exactly what KVS multi-server architecture was implemented for.
		</p>
		<p>
			KVS features support of virtually any servers whicih store content. The main requirement for remote servers
			is FTP access with writing permissions. In case you need to use content protection and disallow direct
			content access, other requirements may apply.
		</p>
		<p>
			The base of content storage is a specified directory in the server’s file system. KVS stores content in a
			tree data structure, which means certain subdirectories will be created in the root of the storage
			directory. In these subdirectories, content files will be stored.
		</p>
		<p>
			Please find an explanation of content storage fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: server name to be displayed in the administration panel.
			</li>
			<li>
				<span class="term">Server group</span>: server group that this particular server belongs to. Content
				storage is assigned to groups of servers, not to individual servers. Any server that belongs to a group
				will store and serve all content assigned to this group. This structure lets you balance the serving
				load if there are multiple servers in one group.
			</li>
			<li>
				<span class="term">URL</span>: HTTP link to the content storage directory. In most cases, this
				directory needs to be protected from direct access so that files in this directory cannot be downloaded
				bypassing the content protection system.
			</li>
			<li>
				<span class="term">Streaming type</span>: this lets you choose the streaming type supported by this
				particular storage server. For example, if you serve content via Apache, you need to select the
				<b>HTTP 302 redirect</b> streaming type; if you serve content via Nginx, you need to select the
				<b>Nginx (x-accel-redirect)</b> streaming type. CDN, the last option in the list, lets you connect any
				servers with non-standard protection system. When you choose CDN, you will need to create a PHP control
				script for this CDN system, or request such script from your CDN solution provider.
			</li>
			<li>
				<span class="term">CDN control script</span>: full name of the PHP script to manage this particular CDN
				server, found in the <b>/admin/cdn</b> directory. The <b>/admin/cdn</b> directory does not exist by
				default, so you need to create it and copy the script there. A template of the script is located here:
				<b>/admin/tools/cdnapi.php</b>. The template contains a set of functions with special names, which will
				be called by the KVS engine when files are requested or content is invalidated. Invalidating is an
				integral part of CDN that cache your content. If no invalidation takes place, CDN servers will keep
				serving old content even if the content has been replaced. Function names in the script depend on the
				name of the script to prevent overlapping between scripts for different CDN systems. You will need to
				rename all functions in the template in accordance with the script name. All functions are documented
				and have sample values which are sent from the KVS engine as parameters.
			</li>
			<li>
				<span class="term">Streaming key</span>: CDN provider key used for content protection. The key is sent
				to the CDN control script where it can be used in the protection mechanism.
			</li>
			<li>
				<span class="term">Use custom streaming HTTP param</span>: this lets you specify a HTTP streaming
				parameter name that is used instead of the standard <b>start</b> name to skip through videos. Use this
				field if your server does not support <b>start</b> as a standard streaming parameter.
			</li>
			<li>
				<span class="term">Connection type</span>: this defines how data copying between servers is carried
				out.
				<span class="important">
					<b>Important!</b> Regardless of the connection type, one and the same directory in a server’s file
					system can be used only for one storage server in KVS.
				</span>
			</li>
			<li>
				<span class="term">Path</span>: full path to the content storage directory. The directory specified
				here needs to have writing permissions (777) and correspond to the HTTP link to it defined in the URL
				field. When Nginx is used, the storage directory needs to be declared internal in the Nginx
				configuration to prevent direct access to it.
			</li>
			<li>
				<span class="term">FTP host</span>: hostname used when connecting via FTP.
			</li>
			<li>
				<span class="term">FTP user</span>: username used when connecting via FTP.
			</li>
			<li>
				<span class="term">FTP password</span>: password used when connecting via FTP.
			</li>
			<li>
				<span class="term">FTP folder</span>: directory in which the content should be stored, relative to FTP
				root. If the FTP access leads directly to the storage directory, this field should be left blank.
				<span class="important">
					<b>Important!</b> Point your FTP user to the www root in FTP server settings and specify path to
					storage directory relative to www in KVS settings to avoid potential problems later on. For
					example, if content should be stored on the server in the <b>http://ip.ad.re.ss/contents/videos</b>
					directory, then your FTP connection should be set up in such a way that the home directory is
					<b>http://ip.ad.re.ss</b> and the <span class="term">FTP folder</span> is <b>contents/videos</b>.
				</span>
			</li>
			<li>
				<span class="term">Control script URL</span>: link to the control script <b>remote_control.php</b>,
				used to manage content serving from a remote server, as well as for monitoring server status.
				Control scripts are not required for CDN servers. This field is filled in automatically. All you need
				to do is copy the file <b>/admin/tools/remote_control.php</b> to your storage server and make sure it
				is available at the URL from this field (the script needs to be in the server’s www root). If the
				script is available, <b>connected</b> word will be displayed. Otherwise you will need to find out why
				the script is not working (invalid Apache configuration, invalid Nginx configuration, invalid PHP
				configuration, etc.).
			</li>
			<li>
				<span class="term">Time offset</span>: this can be used in remote storage server settings to set its
				time difference with the primary server. For remote storage servers, KVS requires time synchronization
				with the primary server with minute precision. If you do not want to synchronize the time zone between
				your servers, you can set the time offset in storage server settings.
			</li>
		</ul>
		<p>
			When you create a second and further servers in a server group to which content has already been assigned,
			you need to fully copy the contents of the storage directory from any existing server in the group to the
			new server. Here, directory contents include the entire subdirectory structure and files (as you know, KVS
			stores content in a tree data structure). If you don’t do this or copied only some of the files, or copied
			the files into a different directory, KVS will return an error when you try adding this server.
		</p>
		<p class="important">
			<b>Important!</b> After you add a new server to a server group to which content has already been assigned,
			this server’s status will be set to disabled, which means it will not be used to serve content. It lets you
			check everything before final server activation, most importantly, check content serving. You can activate
			servers using the context menu in the server list, and also in server group settings.
		</p>
		<p>
			Let us have a look at most frequent errors you may encounter when trying to add a new storage server:
		</p>
		<ul>
			<li>
				<span class="term">No PHP extension for FTP was found</span>: your PHP package does not contain
				functions required to work with FTP. Re-compile your PHP so that it features the FTP extension.
			</li>
			<li>
				<span class="term">Unable to connect to host:21</span>: FTP connection declined by server. Firewall
				protection on storage server could have been triggered.
			</li>
			<li>
				<span class="term">Unable to login with credentials provided</span>: invalid FTP username and / or
				password.
			</li>
			<li>
				<span class="term">Put / get / chmod / delete operations failed, insufficient permissions possible</span>:
				this error means that KVS was unable to complete the basic operations test (copying file to server,
				setting access permissions, copying file from server, deleting file). Try completing these operations
				from your FTP client.
			</li>
			<li>
				<span class="term">Automatic check cannot find some files selected for checking. Please make sure you copied all files to this server.</span>:
				this error means that most likely you did not copy the files of existing content to this server, or the
				directory where the files are in FTP settings is incorrect. When you add a storage server to a server
				group that already has content, KVS randomly selects up to 10 items of content and checks whether they
				exist on the storage server that you are adding.
			</li>
			<li>
				<span class="term">[Control script URL]: script failure</span>: when the control script was requested,
				it did not return the <b>connected</b> word as it should. There may be various reasons for this: you
				did not copy the script to required location, the server’s Apache, Nginx, or PHP configuration is
				invalid.
			</li>
			<li>
				<span class="term">[Time offset]: remote server time is not synchronized with primary server time</span>:
				the time on a storage server needs to be the same as the time on the primary server with minute
				precision. If the servers use different time zones, you will need to configure the offset in
				<span class="term">Time offset</span>.
			</li>
			<li>
				<span class="term">[CDN control script]: file /admin/cdn/mycdn.php does not exist</span>: you are
				trying to add a CDN server, but you did not copy the control script to <b>/admin/cdn</b>.
			</li>
			<li>
				<span class="term">[CDN control script]: file /admin/cdn/mycdn.php does not contain required functions</span>:
				you are trying to add a CDN server, but your control script does not contain functions with required
				names. Most likely, you did not take into account that names of all the script functions need to have
				the PHP filename as prefix. E.g. for the <b>mycdn.php</b> script, function names need to look like
				this: mycdn_test, <b>mycdn_get_video</b>, etc.
			</li>
			<li>
				<span class="term">[CDN control script]: CDN check error: &lt;error details&gt;</span>: the _test
				function of the control script returned an error.
			</li>
		</ul>
		<p>
			When you want to protect your content from hotlinking and / or unauthorized access, you need to set up this
			protection on your storage servers:
		</p>
		<ul>
			<li>
				Nginx-powered servers let you disallow accessing content via direct links. Instead of direct links, a
				serving script is used, which tells Nginx which file should be served to the user. For a local storage
				server, this script is the main <b>get_file.php</b> or <b>get_image.php</b> script. For remote servers,
				the <b>remote_control.php</b> script offers such functionality. You need to copy this script to your
				storage server. Regardless whether your storage server is local or remote, accessing files via direct
				links needs to be disallowed. Modify your Nginx configuration in the following way to do this:
				<span class="code">
					location ^~ /contents/videos/ {<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;mp4; <span class="comment"># enabling MP4 streaming for videos</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;root /usr/home/ftp0/domains/domain.com/html; <span class="comment"># specifying full server path relative to /contents/videos/</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disallowing access to this directory via direct links</span><br/>
					}
				</span>
				After you declare this rule, direct links looking like
				<b>http://domain.com/contents/videos/0/1/1.mp4</b> should stop working and will return 404 error from
				then on. Accessing content will be possible only via the serving script which features different levels
				of protection. Protecting photo albums is carried out in a similar way:
				<span class="code">
					location ^~ /contents/albums/sources/ { <span class="comment"># disallowing direct access to source images</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;root /usr/home/ftp0/domains/domain.com/html; <span class="comment"># specifying full server path relative to /contents/albums/sources/</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disallowing access to this directory via direct links</span><br/>
					}<br/>
					location ^~ /contents/albums/main/1024x1024/ { <span class="comment"># disallowing direct access to images with size 1024x1024</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;root /usr/home/ftp0/domains/domain.com/html; <span class="comment"># specifying full server path relative to /contents/albums/main/1024x1024/</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;internal; <span class="comment"># disallowing access to this directory via direct links</span><br/>
					}
				</span>
			</li>
			<li>
				CDN servers implement protection through generating a temporary hash in the CDN management script.
				However, the specifics of using CDN require you to have the origin server that the CDN queries to cache
				your content. In KVS settings, you set up the origin server as your storage directory, and the CDN
				itself as URL. With a bit of simplification, the whole arrangement looks like this: KVS copies files to
				the origin server > users request files from CDN > CDN requests files from the origin server and caches
				them, after which no requests to the origin server are made. As a result, if your server is the origin
				server, all content needs to be available via direct links so that CDN can cache it. In order to
				prevent unauthorized access to content on your origin server, use a CDN provider’s server which
				supports FTP connection as your origin server.
			</li>
			<li>
				When you use standard 302 redirect, the content is still available via direct links. As a rule, in this
				situation referrer protection can be used for anti-hotlinking; however, this will not protect you from
				third parties grabbing your database.
			</li>
		</ul>
	</div>
	<!-- ch_settings_multiserver_storage(end) -->
	<!-- ch_settings_multiserver_storage_group(start) -->
	<div>
		<h3 id="section_multiserver_storage_group">Storage Server Groups</h3>
		<p>
			Storage server groups let you set up balancing the content serving between servers in the same group. You
			can assign multiple storage servers to each group. The content is assigned to a server group, not to a
			specific server. If there are several servers in the group, the content is duplicated between the servers.
			Thus, the same content can be served from multiple servers at the same time.
		</p>
		<p>
			When facing an insufficient disk space situation, you need to create a new server group and add one or more
			physical servers to it. After that, you will be able to add content to the server group you have just
			created.
		</p>
		<p>
			Please find the description of storage server group fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: group name to be displayed in the administration panel.
			</li>
			<li>
				<span class="term">Content type</span>: this lets you create a group for storing videos or photo
				albums. Historically, KVS cannot store videos and photo albums on the same server group. So, even if
				the content will be physically stored on the same server, you still need to create 2 groups of servers,
				one for videos, the other for photo albums.
			</li>
		</ul>
		<p>
			Please find the description of balancing table fields below:
		</p>
		<ul>
			<li>
				<span class="term">Server</span>: name of storage server which is configured in this line of the table.
			</li>
			<li>
				<span class="term">Status</span>: storage server status. Contnet is served from active servers only.
			</li>
			<li>
				<span class="term">Load balancing weight</span>: sets the weight of this server for balancing. The
				higher the weight of this server relative to other servers, the more likely content is to be served
				from this server.
			</li>
			<li>
				<span class="term">Assign countries</span>: this lets you assign certain servers to certain countries
				and serve content from these servers only to users from these countries. For one server, this field
				needs to be blank, which means this server is used for all countries not defined in other fields.
			</li>
		</ul>
	</div>
	<!-- ch_settings_multiserver_storage_group(end) -->
	<!-- ch_settings_multiserver_storage_test(start) -->
	<div>
		<h3 id="section_multiserver_storage_test">Checking Content Serving</h3>
		<p>
			KVS features an automated tool for checking how content is served from your storage servers. This tool can
			be launched manually for each individual server to get details on each aspect of content serving. Also,
			this tool regularly checks all storage server and displays error messages on the start page if problems are
			detected.
		</p>
		<p>
			In the administration panel, content serving check can be run from the context menu for any of the servers.
			For video servers, serving videos of each format is checked. For photo servers, serving photo albums of
			each format is checked, as well as serving ZIP files of the formats for which they are enabled. Here are
			the check details:
		</p>
		<ul>
			<li>
				Direct links to video files should not work. If for a particular video format its files are available
				via direct links, you will see an error message. This error, however, is not critical. It does not
				affect the way your site works and is not shown on the start page.
			</li>
			<li>
				Links to video files via the serving script should work. If for a particular video format its file(s)
				are not available via the serving script, you will see an error message. The exception here are formats
				of videos access to which is not allowed for unregistered users. In this case, the check will display a
				warning, as only registered users or administrators can check links to files of this format. In order
				to make sure the file is actually served, you can click the link in the notification and see if it is
				working.
			</li>
			<li>
				FLV or MP4 file streaming should work. If for a particular video format streaming is not working, you
				will see an error message.
			</li>
			<li>
				Direct links to photos should work for the formats with unrestricted access (i.e. photos available to
				all users). If photo(s) of a publicly available format are not available via direct links, you will see
				an error message.
			</li>
			<li>
				Direct links to photos should not work for the formats that are not available to unregistered users. If
				photo(s) of a format with restricted access are available via direct links, you will see an error
				message. This error, however, is not critical. It does not affect the way your site works and is not
				shown on the start page.
			</li>
			<li>
				Links to photos via the serving script should work. If for a particular photo album format its file(s)
				are not available via the serving script, you will see an error message. The exception here are formats
				of photo albums access to which is not allowed for unregistered users. In this case, the check will
				display a warning, as only registered users or administrators can check links to photos of this format.
				In order to make sure the file is actually served, you can click the link in the notification and see
				if it is working.
			</li>
			<li>
				Links to ZIP files of photo albums are checked using the same logic as links to photos.
			</li>
		</ul>
		<p>
			If a check returns an error, you can see the HTTP headers of requests and responses in the details. As a
			rule, errors occur when storage servers are not configured properly. Let us have a look at most common
			situations:
		</p>
		<ul>
			<li>
				<span class="term">Direct link not working?</span> check: here, an error means that files are available
				via direct links. If the storage server uses <span class="term">HTTP 302 redirect</span> as its
				streaming type, this error is natural and there is no way it can be avoided. Let us remind you that
				this streaming type means that all files are available via direct links. If the storage server uses
				Nginx, most likely, in your Nginx configuration, the <b>internal</b> directive for the storage
				directory is not set (see storage server settings for more information).
			</li>
			<li>
				<span class="term">Direct link working?</span> check: here, an error means that files are not available
				via direct links. If your storage server uses Nginx, most likely, the <b>internal</b> directive is set
				in the configuration for the storage directory. Remove this directive. If your storage server uses CDN,
				you need to disable protection on this server.
			</li>
			<li>
				<span class="term">Protected link working?</span> check: here, an error means that files are not
				available via the serving script. Most likely reasons are: content files do not physically exist in
				storage directory, or MultiViews is enabled in Apache, which interferes with content serving scripts.
			</li>
			<li>
				<span class="term">Streaming working?</span> check: here, an error means that video file streaming is
				not working. If the storage server uses Nginx, most likely, the FLV / MP4 streaming module is not
				enabled for the storage directory. If this is a CDN server, your CDN may need non-standard streaming
				parameter. Contact your CDN provider for more information and specify the parameter in storage server
				settings in KVS.
			</li>
		</ul>
	</div>
	<!-- ch_settings_multiserver_storage_test(end) -->
	<!-- ch_settings_multiserver_conversion(start) -->
	<div>
		<h3 id="section_multiserver_conversion">Conversion Servers</h3>
		<p>
			Conversion servers are used to distribute load the system experiences during content-related operations.
			The Basic and Advances packages only support local conversion servers.
		</p>
		<p>
			Please find a description of conversion server fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: server name to be displayed in the administration panel.
			</li>
			<li>
				<span class="term">Status</span>: server status. Only active conversion servers are used.
			</li>
			<li>
				<span class="term">Maximum tasks</span>: this sets maximum number of tasks to be sent to this
				particular conversion server at the same time. However, this does not mean the conversion server will
				process these tasks simultaneously. All loaded tasks will be processed consecutively, one after
				another.
			</li>
			<li>
				<span class="term">CPU-hungry operations priority</span>: this lets you choose a priority setting for
				resource-consuming tasks that are handled by this particular server (<b>realtime</b>, <b>high</b>,
				<b>medium</b>, <b>low</b>, <b>very low</b>).
			</li>
			<li>
				<span class="term">Optimize content copying</span>: enable this to make the conversion server copy
				processed content directly to storage servers. This saves primary server bandwidth and speeds up
				content processing. If you enable this and the version of <b>remote_cron.php</b> on your conversion
				server is old, or the FTP extension for PHP is not installed, an error may occur.
			</li>
			<li>
				<span class="term">Connection type</span>: this defines how data is copied between servers.
				<span class="important">
					<b>Important!</b> Regardless of the connection type, one and the same directory in a server’s file
					system can be used only for one conversion server in KVS.
				</span>
			</li>
			<li>
				<span class="term">Path</span>: full path to the server’s working directory. The directory specified
				here needs to have writing permissions (777).
			</li>
			<li>
				<span class="term">FTP host</span>: hostname used when connecting via FTP.
			</li>
			<li>
				<span class="term">FTP user</span>: username used when connecting via FTP.
			</li>
			<li>
				<span class="term">FTP password</span>: password used when connecting via FTP.
			</li>
			<li>
				<span class="term">FTP folder</span>: the server’s working directory relative to FTP root. If FTP
				access leads directly to the directory required, leave this field blank.
			</li>
		</ul>
		<p>
			Before you create a remote conversion server, you need to copy the <b>/admin/tools/remote_cron.php</b>
			script to its working directory, and set it up as a Cron job to be launched once every minute. When it is
			run for the first time, the script will try to detect paths to all required libraries automatically. It
			will also create a file called <b>config.properties</b> in the same directory, where all paths will be
			listed. Check whether the libraries were detected correctly and make modifications to the file if
			necessary:
		</p>
		<p class="code">
			<span class="comment"># ffmpeg is required</span><br/>
			ffmpeg = /usr/local/bin/ffmpeg<br/><br/>

			<span class="comment"># imagemagick (convert binary) is required</span><br/>
			imagemagick = /usr/local/bin/convert<br/><br/>

			<span class="comment"># yamdi is required for FLV videos support</span><br/>
			yamdi = /usr/local/bin/yamdi<br/><br/>

			<span class="comment"># qt-faststart is required for MP4 videos support</span><br/>
			qt-faststart = /usr/local/bin/qt-faststart<br/><br/>

			<span class="comment"># time offset in comparison to main server (in hours)</span><br/>
			timeoffset = 0
		</p>
		<ul>
			<li>
				<b>ffmpeg</b>: path to FFmpeg, a required setting. The FFmpeg library is used for primary video
				conversion tasks.
			</li>
			<li>
				<b>imagemagick</b>: path to the imagemagick convert library, required.
			</li>
			<li>
				<b>yamdi</b>: path to the Yamdi library, required only if you intend to store FLV videos. The Yamdi
				library is used to add metadata to FLV files so that the player can skip through them.
			</li>
			<li>
				<b>qt-faststart</b>: path to the qt-faststart library, required only if you intend to store MP4 videos.
				The qt-faststart library is used for post-processing of MP4 files so that the player can skip through
				them.
			</li>
			<li>
				<b>timeoffset</b>: positive or negative time offset between the primary server and the conversion
				server.
			</li>
		</ul>
		<p>
			Let us have a look at common errors which you may come across when adding a conversion server:
		</p>
		<ul>
			<li>
				<span class="term">No PHP extension for FTP was found</span>: your PHP package does not contain
				functions required to work with FTP. Re-compile your PHP so that it features the FTP extension. If
				<span class="term">Optimize content copying</span> option is enabled, the FTP extension for PHP needs
				to be installed on the conversion server as well.
			</li>
			<li>
				<span class="term">Unable to connect to host:21</span>: FTP connection declined by server. Firewall
				protection on storage server could have been triggered.
			</li>
			<li>
				<span class="term">Unable to login with credentials provided</span>: invalid FTP username and / or
				password.
			</li>
			<li>
				<span class="term">Put / get / chmod / delete operations failed, insufficient permissions possible</span>:
				this error means that KVS was unable to complete the basic operations test (copying file to server,
				setting access permissions, copying file from server, deleting file). Try completing these operations
				from your FTP client.
			</li>
			<li>
				<span class="term">Conversion script not configured / not working on this server</span>: in the
				server’s working directory, there is no data generated by the conversion script. Most likely, the
				script was not configured as a Cron job.
			</li>
		</ul>
	</div>
	<!-- ch_settings_multiserver_conversion(end) -->
	<h2 id="section_languages">Localization</h2>
	<!-- ch_settings_languages(start) -->
	<div>
		<p>
			Content localization lets you offer your site’s content in a choice of languages. When you create new
			languages, the engine ‘extends’ the database so that it can duplicate existing data in new languages. Also,
			in the administration panel, you see features that let you translate your objects into new languages. Only
			the Ultimate package features localization support.
		</p>
		<p>
			Please find the description of language fields below:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: language name to be displayed in the administration panel.
			</li>
			<li>
				<span class="term">ISO code</span>: 2-character ISO code of the language (Latin alphabet only). This
				code will be used to switch the site to another language mode.
			</li>
			<li>
				<span class="term">Applicable to</span>: choosing the localization application area. This option
				defines how translations work in the administration panel. If you want to translate only object titles,
				set this value accordingly.
			</li>
		</ul>
		<p>
			The main documentation covers a variety of methods you can use to make your site multi-lingual. When you
			add multiple languages within the same domain name, you need to list the ISO codes of these languages in
			the <b>/admin/include/setup.php</b> file:
		</p>
		<p class="code">
			$config['locales']=array('de','fr','es','it'); <span class="comment">// list codes of all supported languages in any order</span>
		</p>
		<p>
			In this case, in order to switch the site to a localized version, you need to send the
			<b>kt_lang=%code%</b> parameter to any page. This parameter will be stored in user’s cookies, and later on,
			for this user, the site will switch to the chosen localized version automatically.
		</p>
		<p>
			When you use language satellites, you need to specify the ISO code of the current locale in the
			<b>/admin/include/setup.php</b> file so that this particular satellite always works in this locale:
		</p>
		<p class="code">
			$config['locale']='de'; <span class="comment">// specify one of the supported language codes to be used for this satellite</span>
		</p>
		<p>
			If you want to use language keys in your site’s templates, you need to create the <b>/langs/default.php</b>
			file where all keys and their values for the default locale will be defined. Then, copy the contents of
			this file to language files called <b>/langs/de.php</b>, <b>/langs/fr.php</b> etc., for all the locales
			your site is supposed to support. Then, you can give these files to your content writers and translators so
			that they enter the copy. The files need to have this format:
		</p>
		<p class="code">
			&lt;?php<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;$lang['key1']="Text 1";<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;$lang['section1']['key1']="Section 1 Text 1";<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;$lang['section1']['key2']="Section 1 Text 2";<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;$lang['section2']['subsection1']['key1']="Section 2 Subsection 1 Text 1";<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;...<br/>
			?&gt;
		</p>
		<p>
			There can be any number of sections and they can be nested in any way you want. We recommend declaring
			values common for the entire site without any sections. We also recommend declaring notions that are
			logically grouped in the same section, e.g.:
		</p>
		<p class="code">
			$lang['site_title']="Site name - best videos and community site";<br/>
			$lang['posted_by']="Posted by";<br/>
			$lang['duration']="Duration";<br/>
			...<br/>
			<br/>
			$lang['main_menu']['home']="Home";<br/>
			$lang['main_menu']['videos']="Videos";<br/>
			...<br/>
		</p>
		<p>
			Then, in your site’s templates, you can use these keys to display certain types of copy:
		</p>
		<p class="code">
			{{$smarty.ldelim}}$lang.site_title{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}$lang.posted_by{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}$lang.duration{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}$lang.main_menu.home{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}$lang.main_menu.videos{{$smarty.rdelim}}
		</p>
		<p>
			In case you need to insert certain dynamic values into a block of text, NEVER split this text in several
			parts and display on your site like this. Instead, insert placeholders into it. These will be replaced with
			actual values when displayed on the page. The reason here is that in different languages word order can
			differ and your translators may need to rewrite the block completely. For instance:
		</p>
		<p class="code">
			$lang['titles']['videos_by_category']="Videos for category <b>%1%</b>";<br/>
			$lang['confirmations']['delete_messages']="Are you sure to delete <b>%1%</b> messages from <b>%2%</b> user?";
		</p>
		<p class="code">
			{{$smarty.ldelim}}$lang.titles.videos_by_category|replace:"<b>%1%</b>":$category_info.title{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}$lang.confirmations.delete_messages|replace:"<b>%1%</b>":$selected_count|replace:"<b>%2%</b>":$username{{$smarty.rdelim}}
		</p>
		<p>
			When your site works in a particular locale, the engine will first implement the default language file, and
			then the language file for this locale, as long as it exists. Thus, when certain keys are missing in the
			locale language file, copy from the default language file will be displayed on the site.
		</p>
	</div>
	<!-- ch_settings_languages(end) -->
</div>