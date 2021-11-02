<div id="documentation">
		<h1 id="section_website_ui">Site Management</h1>
	<h2 id="section_website_ui_contents">Contents</h2>
	<div class="contents">
		<a href="#section_website_ui_basic" class="l2">Basic Concepts</a><br/>
		<a href="#section_website_ui_page_components" class="l2">Page Components</a><br/>
		<a href="#section_website_ui_page_components_usage" class="l3">Using Page Components on Your Site</a><br/>
		<a href="#section_website_ui_page_components_admin_panel" class="l3">Page Components in Administration Panel</a><br/>
		<a href="#section_website_ui_page_components_default" class="l3">Page Components in Default Design</a><br/>
		<a href="#section_website_ui_blocks" class="l2">Blocks</a><br/>
		<a href="#section_website_ui_blocks_reference" class="l3">Overview</a><br/>
		<a href="#section_website_ui_blocks_usage" class="l3">Using Blocks in Pages</a><br/>
		<a href="#section_website_ui_blocks_params" class="l3">Configuration Parameters and Block Templates</a><br/>
		<a href="#section_website_ui_blocks_pagination" class="l3">List Blocks and Pagination</a><br/>
		<a href="#section_website_ui_blocks_global" class="l3">Global Blocks</a><br/>
		<a href="#section_website_ui_advertising" class="l2">Advertisements and Ad Spots</a><br/>
		<a href="#section_website_ui_advertising_usage" class="l3">Using Ad Spots</a><br/>
		<a href="#section_website_ui_advertising_admin_panel" class="l3">Configuring Ads in Administration Panel</a><br/>
		<a href="#section_website_ui_pages" class="l2">Site Pages</a><br/>
		<a href="#section_website_ui_pages_reference" class="l3">Overview</a><br/>
		<a href="#section_website_ui_pages_admin_panel" class="l3">Configuring Pages in Administration Panel</a><br/>
		<a href="#section_website_ui_pages_existing" class="l3">Brief Overview of Existing Pages</a><br/>
		<a href="#section_website_ui_caching" class="l2">Caching</a><br/>
		<a href="#section_website_ui_caching_blocks" class="l3">First-tier Caching</a><br/>
		<a href="#section_website_ui_caching_pages" class="l3">Second-tier Caching</a><br/>
		<a href="#section_website_ui_caching_important" class="l3">Important Aspects of Caching</a><br/>
		<a href="#section_website_ui_caching_performance" class="l3">Performance Statistics</a><br/>
		<a href="#section_website_ui_other" class="l2">Other Site-Related Issues</a><br/>
		<a href="#section_website_ui_other_js" class="l3">JavaScript Files</a><br/>
		<a href="#section_website_ui_other_emails" class="l3">System Emails</a><br/>
		<a href="#section_website_ui_other_session" class="l3">User Session Details</a><br/>
		<a href="#section_website_ui_other_engine_customization" class="l3">Customizing Site Engine</a><br/>
		<a href="#section_website_ui_other_engine_custom_blocks" class="l3">Creating Custom Site Blocks</a><br/>
		<a href="#section_website_ui_other_debugger" class="l3">Debugging Site Pages</a><br/>
	</div>
	<h2 id="section_website_ui_basic">Basic Concepts</h2>
	<!-- ch_website_ui_basic(start) -->
	<div>
		<p>
			KVS lets you use a wide range of features to build and customize the pages of your site in
			any way you want. See the list of major KVS engine advantages below:
		</p>
		<ul>
			<li>
				Unmatched speed made possible by two-tier page content caching. You can adjust your caching strategy at
				any time, which lets you balance the server load when your traffic grows. See the section on caching to
				find out more about the several primary caching aspects you need to understand to make your site
				perform at its best.
			</li>
			<li>
				Site pages are built with blocks, which lets you configure the way each and every page of your site
				works, fast and easy. All you need is basic understanding of KVS engine and HTML layouts.
			</li>
			<li>
				You can develop your own blocks if you need custom functionality, and easily implement them into your
				site. No need to go through thousands lines of code. You just need to understand a few basic things
				about the way blocks work.
			</li>
			<li>
				Any new blocks developed after your site was launched can be integrated into any place on your site
				without any damage done to your existing site features.
			</li>
			<li>
				Built-in site performance analysis system lets you spot performance bottlenecks and optimize your
				blocks on the fly.
			</li>
		</ul>
		<p>
			KVS engine uses 4 basic entities to build site pages:
		</p>
		<ul>
			<li>
				<span class="term">Page components</span>: these are instances of the HTML code that can be used in
				multiple locations. Page components merely display HTML code according to the logic configured in the
				template of the component. These are used for common site elements, e.g. headers, footers, search forms
				etc. Components can be used in templates of other entities, e.g. in pages and blocks. So, if you need
				to make changes to your site header (usually containing site name and header HTML code), you can adjust
				the corresponding page component. The changes will take effect for all pages where this component is
				part of the template.
			</li>
			<li>
				<span class="term">Blocks</span>: are primary logical modules used to build pages. A block is a
				logically complete site feature that can be used on any page. The primary difference between blocks and
				components is that blocks are used for site functionality (database operations, preparing results for
				display, processing user-submitted forms etc.) and displaying the results of such functionality
				(templates with HTML code). Components, on the other hand, are used to display certain data and design
				elements. Most blocks support configuration parameters that you can use to adjust the way the block
				behaves. E.g. you can limit the number of elements in a block, or enable protection when users logs
				into their accounts. In addition to parameters, each block supports certain level of caching and can be
				cached on its own.
			</li>
			<li>
				<span class="term">Ad spots</span>: are slots displaying advertisements. You can set up as many ad
				spots for your site as you want, and use different ads in each of them.
			</li>
			<li>
				<span class="term">Pages</span>: are primary components of your site structure. Each page can contain
				any number of components, blocks, and/or ad spots. Alternatively, a page can only contain static HTML
				code. Pages support second-tier caching, which, when enabled, moves the entire page display results
				into RAM, making users see the cached static HTML code for a certain period of time that you can
				customize.
			</li>
			<li>
				<span class="term">Groups of pages</span>: for your convenience, you can unite pages into groups in the
				administration panel.
			</li>
		</ul>
		<div class="screenshot">
			<img src="docs/screenshots/website_ui_page_structure.png" alt="Site page structure" width="981" height="714"/><br/>
			<span>Site page structure.</span>
		</div>
		<p>
			In order for you to visualize all the primary entities you can use to build your site, let's take a look at
			the screenshot above. Everything displayed there is an individual page that has an address of its own. In
			page top, we see the header. Header is a page component, as it does not have any features. Header only
			displays the top part of the HTML code of the page. In the header, there is also an ad spot that shows one
			banner. If we assign multiple banners to this spot, they will be rotated. Below, the video list block
			(<b>list_videos</b>) is displayed, showing 9 latest viewed videos. The difference between a block and a
			component is obvious here. A block queries the site database to display certain data, while a component
			merely shows a piece of HTML code. To the right from the list of videos we see a component showing the
			search field. Right under it, the tag cloud (<b>tags_cloud</b>) is displayed.
		</p>
		<p>
			As another example, let us take a look at the Community page where we want to show this content:
		</p>
		<ul>
			<li>3 recommended premium videos</li>
			<li>20 newest registered users</li>
			<li>3 most active users</li>
			<li>10 latest added photo albums</li>
			<li>10 most popular photo albums</li>
		</ul>
		<p>
			Here is an example of how the code of such page template could look like:
		</p>
		<p class="code">
			<span class="comment">
				{{$smarty.ldelim}}* Setting the value of the page_title variable used in the header_general component *{{$smarty.rdelim}}<br/>
			</span>
			{{$smarty.ldelim}}assign var=page_title value="Community"{{$smarty.rdelim}}<br/>
			<br/>
			<span class="comment">
				{{$smarty.ldelim}}* Including the header_general component that displays page header *{{$smarty.rdelim}}<br/>
			</span>
			{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
			<br/>
			&lt;div id="data"&gt;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
			<span class="comment">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Including the list_videos block to show 3 premium videos *{{$smarty.rdelim}}<br/>
			</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Recommended premium videos"{{$smarty.rdelim}}<br/>
			<br/>
			<span class="comment">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Including the list_members to show 20 newest registered users *{{$smarty.rdelim}}<br/>
			</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_members" block_name="Latest members"{{$smarty.rdelim}}<br/>
			<br/>
			<span class="comment">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Including the list_albums block to display 10 latest photo albums *{{$smarty.rdelim}}<br/>
			</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_albums" block_name="Latest albums"{{$smarty.rdelim}}<br/>
			<br/>
			<span class="comment">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Including the list_albums block to display 10 most popular photo albums *{{$smarty.rdelim}}<br/>
			</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_albums" block_name="Most popular albums"{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
			<span class="comment">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Including the list_members block to show 3 most active users *{{$smarty.rdelim}}<br/>
			</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_members" block_name="Active members"{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
			&lt;/div&gt;<br/>
			<br/>
			<span class="comment">
				{{$smarty.ldelim}}* Including the footer_general component that displays the footer *{{$smarty.rdelim}}<br/>
			</span>
			{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
		</p>
		<p>
			In addition to direct inclusion of blocks in page template, you also need to configure their parameters for
			this particular page. For example, you need to tell the <b>list_videos</b> block to show only 3 videos and
			these videos need to be premium etc. Also, you may need to configure block templates for this particular
			page (their data display logic). We will address this in greater detail later on.
		</p>
	</div>
	<!-- ch_website_ui_basic(end) -->
	<h2 id="section_website_ui_page_components">Page Components</h2>
	<!-- ch_website_ui_page_components_usage(start) -->
	<div>
		<h3 id="section_website_ui_page_components_usage">Using Page Components on Your Site</h3>
		<p>
			Page components are page elements and components of the HTML layout used in multiple locations on your
			site. As your site may have a large number of pages that will most likely have common elements (e.g.
			header, search field etc.), we recommend creating page components out of these common elements and using
			these components where necessary.
		</p>
		<p>
			You can insert page components in any location where a Smarty template is expected, i.e. in page templates,
			block templates, and even in component templates. Use a standard Smarty directive to insert a component:
		</p>
		<p class="code">
			{{$smarty.ldelim}}include file="%PAGE_COMPONENT_EXTERNAL_ID%.tpl"{{$smarty.rdelim}}
		</p>
		<p>
			In this example, you need to replace the <b>%PAGE_COMPONENT_EXTERNAL_ID%</b> token with the
			<span class="term">External ID</span> of the component you need to use, e.g. <b>header_general</b>:
		</p>
		<p class="code">
			{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}
		</p>
		<p>
			When the engine processes this particular page, the Smarty engine replaces this directive with the contents
			of the component template you used. This lets you use any variables in page component templates and set the
			values of these variables in each particular instance where such components are used. Let us take a look at
			an example where site header has a page title. A header cannot have the same title for all site pages as
			they obviously have different titles. So, instead of using a fixed title, use a variable, e.g.
			<b>page_title</b>, the value of which will be set in the template of each individual page right before the
			component is included into the page. This is how the header template code could look like:
		</p>
		<p class="code">
			&lt;html xmlns="http://www.w3.org/1999/xhtml"&gt;<br/>
			&lt;head&gt;<br/>
			<span class="comment">
				&nbsp;&nbsp;&nbsp;&nbsp;&lt;!-- Displaying the value of page_title in page title --&gt;<br/>
			</span>
			&nbsp;&nbsp;&nbsp;&nbsp;&lt;title&gt;{{$smarty.ldelim}}$page_title{{$smarty.rdelim}} - My Tube Site&lt;/title&gt;<br/>
			&lt;/head&gt;<br/>
			&lt;body&gt;<br/>
		</p>
		<p>
			In order to replace the <b>page_title</b> variable with its value when displaying a page, you need to
			assign this value before the component is included. You can do it this way (page with the list of premium
			videos):
		</p>
		<p class="code">
			<span class="comment">
				{{$smarty.ldelim}}* Setting the value of the page_title variable *{{$smarty.rdelim}}<br/>
			</span>
			{{$smarty.ldelim}}assign var=page_title value="Premium Videos"{{$smarty.rdelim}}<br/>
			<br/>
			<span class="comment">
				{{$smarty.ldelim}}* Including the header_general component that displays the header *{{$smarty.rdelim}}<br/>
			</span>
			{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
			<br/>
			....
		</p>
	</div>
	<!-- ch_website_ui_page_components_usage(end) -->
	<!-- ch_website_ui_page_components_admin_panel(start) -->
	<div>
		<h3 id="section_website_ui_page_components_admin_panel">Page Components in Administration Panel</h3>
		<p>
			Files of page components are stored in <b>/template</b> in the root directory of your site. Apache needs to
			have file creating permissions in this directory so that it can create new components. Correspondingly,
			editing permissions are needed to edit existing components.
		</p>
		<p>
			Use any of these 3 methods to create a page component:
		</p>
		<ul>
			<li>
				Create a component manually in the administration panel, specifying its ID and template code.
			</li>
			<li>
				Duplicate an existing component by using the <b>Duplicate</b> feature in the context menu for any
				existing component. You will only need to specify the ID of the new component.
			</li>
			<li>
				Copy a <b>.tpl</b> file into <b>/template</b> of your root directory and set the permissions to 666.
			</li>
		</ul>
		<p>
			Data fields supported by components:
		</p>
		<ul>
			<li>
				<span class="term">External ID</span>: is a unique component ID that can contain a limited set of
				characters (a-z, A-Z, 0-9, _). This ID is used to store the component template code on your server as a
				<b>.tpl</b> file. You cannot change the ID after you are done creating a component. If you need to
				rename a component, make a copy of it, switch to the new component with the new ID in all locations
				where the old one was used, and delete the old component.
			</li>
			<li>
				<span class="term">Template code</span>: the Smarty template code (or HTML code) of this component. You
				cannot include blocks in component templates. Blocks can be used in page templates only.
			</li>
		</ul>
		<p>
			When you work with page and block content, you can include even those page components that do not exist
			using the <b>{{$smarty.ldelim}}include ...{{$smarty.rdelim}}</b> Smarty directive. In this case, the component will be created
			automatically and it will have a blank template. All you need to do is fill its template with content
			later.
		</p>
		<p>
			You can delete page components only when they are not linked to from other entities of your site, i.e.
			pages, blocks, or other components.
		</p>
	</div>
	<!-- ch_website_ui_page_components_admin_panel(end) -->
	<!-- ch_website_ui_page_components_default(start) -->
	<div>
		<h3 id="section_website_ui_page_components_default">Page Components in Default Design</h3>
		<p>
			This table briefly describes page components used in the default design and explains their features. Basic
			packages of KVS may not offer certain components described below.
		</p>
		<p>
			Many components support something we call outer variables. Let's have a look at what it means. If a
			component has multiple variations different in small fragments (e.g. different names of video lists but the
			lists themselves are displayed in the same way), you do not need to create a new component for each
			individual list. You only need to create one component, displaying a variable value instead of the list
			name. The value will be set before the component is included. For example, here's how the header component
			supports an outer variable for the HTML title of the page:
		</p>
		<p class="code">
			&lt;title&gt;{{$smarty.ldelim}}$page_title{{$smarty.rdelim}} / Kernel Tube&lt;/title&gt;
		</p>
		<p>
			Before you include this header component, you need to set the value of the variable that is about to be
			displayed within the component:
		</p>
		<p class="code">
			{{$smarty.ldelim}}assign var="page_title" value="Community"{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}
		</p>
		<div class="table">
			<table>
				<colgroup>
					<col width="10%"/>
					<col/>
				</colgroup>
				<tr class="header">
					<td>File</td>
					<td>Description</td>
				</tr>
				<tr>
					<td>header_general.tpl</td>
					<td>
						Site header; supports these outer variables:<br/>
						- <b>page_title</b> - page title.<br/>
						- <b>page_description</b> - page description. If left blank, default description is shown
						(modify it).<br/>
						- <b>page_keywords</b> - page tags. If left blank, default tags are shown (modify them).<br/>
						- <b>page_rss</b> - RSS feed for current page. If left blank, default RSS feed will be
						used.<br/>
						- <b>page_canonical</b> - canonical URL for the page. Used for SE optimization when same pages
						are shown under different URLs. In this case they need to have matching canonical URLs.
					</td>
				</tr>
				<tr>
					<td>footer_general.tpl</td>
					<td>Site footer.</td>
				</tr>
				<tr>
					<td>list_albums_block_common.tpl</td>
					<td>
						Displaying photo album list on main site pages. Used in templates of the <b>list_albums</b>
						block. Supports these outer variables:<br/>
						- <b>list_albums_title</b> - list title.<br/>
						- <b>list_albums_show_all_link</b> - link to the page with all albums in case current list
						displays only part of the album list without pagination.<br/>
						- <b>list_albums_show_rating</b> - can be 1,2,3,4; shows current sorting by rating (1=today,
						2=week, 3=month, 4=all time).<br/>
						- <b>list_albums_show_popularity</b> - can be 1,2,3,4; shows current sorting by popularity
						(1=today, 2=week, 3=month, 4=all time).
					</td>
				</tr>
				<tr>
					<td>list_albums_block_internal.tpl</td>
					<td>
						Displaying photo album list on personal site pages, delete and editing is possible. Used in
						templates of the <b>list_albums</b> block. Supports outer variable <b>list_albums_title</b> -
						list title.
					</td>
				</tr>
				<tr>
					<td>list_members_block_common.tpl</td>
					<td>
						Displaying user list on main site pages. Used in templates of the <b>list_members</b> block.
						Supports these outer variables:<br/>
						- <b>list_members_title</b> - list title.<br/>
						- <b>list_members_show_all_link</b> - link to the page with all users in case current list
						displays only part of the user list without pagination.
					</td>
				</tr>
				<tr>
					<td>list_members_events_block_common.tpl</td>
					<td>
						Displaying user list on main site pages. Used in templates of the <b>list_members</b> block.
						Supports these outer variables:<br/>
						- <b>list_members_events_title</b> - list title.<br/>
						- <b>list_members_events_show_all_link</b> - link to the page with all users in case current
						list displays only part of the user list without pagination.
					</td>
				</tr>
				<tr>
					<td>list_members_blog_block_common.tpl</td>
					<td>
						Displaying list of user blog posts on main site pages. Used in templates of the
						<b>list_members_blog</b> block. Supports these outer variables:<br/>
						- <b>list_members_blog_title</b> - list title.<br/>
						- <b>list_members_blog_show_all_link</b> - link to the page with all blog posts in case current
						list displays only part of the list without pagination.<br/>
						- <b>list_members_blog_disable_edit</b> - set to 1 if the template should not be editable.
					</td>
				</tr>
				<tr>
					<td>list_videos_block_common.tpl</td>
					<td>
						Displaying list of videos on main site pages. Used in templates of the <b>list_videos</b>
						block. Supports these outer variables:<br/>
						- <b>list_videos_title</b> - list title.<br/>
						- <b>list_videos_show_all_link</b> - link to the page with all videos in case current list
						displays only part of the list without pagination.<br/>
						- <b>list_videos_show_rating</b> - can be 1,2,3,4; shows current sorting by rating (1=today,
						2=week, 3=month, 4=all time).<br/>
						- <b>list_videos_show_popularity</b> - can be 1,2,3,4; shows current sorting by popularity
						(1=today, 2=week, 3=month, 4=all time).<br/>
						- <b>list_videos_show_sorting</b> - can be 1,2,3,4; shows current sorting by different criteria
						(1=publishing date, 2=rating, 3=popularity, 4=duration).<br/>
						- <b>list_videos_show_sorting_link</b> - used together with
						<b>list_videos_show_sorting</b> to specify beginning part of sorting URLs.
					</td>
				</tr>
				<tr>
					<td>list_videos_block_internal.tpl</td>
					<td>
						Displaying list of videos on personal site pages, deleting and editing is possible. Used in
						templates of the <b>list_videos</b> block. Supports outer variable <b>list_videos_title</b> -
						list title.
					</td>
				</tr>
				<tr>
					<td>member_menu.tpl</td>
					<td>
						Menu with links to various internal site pages for registered users.
					</td>
				</tr>
				<tr>
					<td>pagination_block_common.tpl</td>
					<td>
						Displaying pagination for any listing block. Used in templates of the <b>pagination</b> block
						as well as templates of any listing blocks.
					</td>
				</tr>
				<tr>
					<td>pagination_block_ajax.tpl</td>
					<td>
						Displaying AJAX pagination for any listing block. Used in templates of the <b>pagination</b>
						block as well as templates of any listing blocks.
					</td>
				</tr>
				<tr>
					<td>search_albums_block.tpl</td>
					<td>Photo album search form.</td>
				</tr>
				<tr>
					<td>search_members_block.tpl</td>
					<td>User search form.</td>
				</tr>
				<tr>
					<td>search_videos_block.tpl</td>
					<td>Video search form.</td>
				</tr>
				<tr>
					<td>side_advertising.tpl</td>
					<td>Sidebar ad block.</td>
				</tr>
				<tr>
					<td>tags_cloud_block_albums.tpl</td>
					<td>Displaying photo album tag cloud. Used in templates of the <b>tags_cloud</b> block.</td>
				</tr>
				<tr>
					<td>tags_cloud_block_common.tpl</td>
					<td>Displaying video tag cloud. Used in templates of the <b>tags_cloud</b> block.</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- ch_website_ui_page_components_default(end) -->
	<h2 id="section_website_ui_blocks">Blocks</h2>
	<!-- ch_website_ui_blocks_reference(start) -->
	<div>
		<h3 id="section_website_ui_blocks_reference">Overview</h3>
		<p>
			Blocks are primary logical modules used to build pages of your site. Unlike components, blocks offer data
			processing and sorting as well as lots of ways to configure the logic according to which data is processed
			and displayed. The engine lets you instantly adjust the way your site works by configuring block parameters
			and adding new blocks. You can also create custom blocks for your individual needs.
		</p>
		<p>
			Check the <span class="term">Page blocks overview</span> in <span class="term">Website UI</span> in the
			administration panel to see the list of all blocks and details about each of them with usage examples. When
			you open block details, you see all usage information, the list and description of all parameters, as well
			as configuration examples.
		</p>
		<p>
			There are several ways to classify all the blocks in KVS. For instance, depending on the type of content
			displayed, you can divide all blocks into:
		</p>
		<ul>
			<li>
				<span class="term">Listing blocks</span>: blocks that display lists of certain items. For example,
				<b>list_categories</b> lists the categories, <b>video_comments</b> shows comments to a video, and so
				on. These blocks all support pagination (going from one page of the listing to another). You can also
				connect standalone pagination block to these (this was left mainly to preserve compatibility with older
				script versions).
			</li>
			<li>
				<span class="term">Data display blocks</span>: these blocks display details of certain items. For
				example, <b>member_profile_view</b> shows user profile details, while <b>album_view</b> shows photo
				album details, and so on.
			</li>
			<li>
				<span class="term">Data / form editing blocks</span>: these are blocks that display forms to be filled
				on. For example, <b>logon</b> shows the login form that takes users to their accounts, while
				<b>video_edit</b> shows the form that is used to create and edit videos, and so on. These blocks do not
				support caching.
			</li>
		</ul>
		<p>
			Depending on access level and privileges, blocks can be classified in this way:
		</p>
		<ul>
			<li>
				<span class="term">Public blocks</span>: blocks always available to all site visitors, e.g.
				<b>list_categories</b> showing all the categories, or <b>invite_friend</b> showing a form letting users
				share pages with their friends, and so on.
			</li>
			<li>
				<span class="term">Private blocks</span>: these always require the user to be logged in, e.g.
				<b>member_profile_edit</b> showing forms letting user edit their profiles, or <b>list_messages</b>
				displaying a list of internal messages the user has in their inbox, and so on. If the user requests a
				page with a block of this type without being logged in, the user will be redirected to the login page
				(configured in the block parameters). After the login is complete, the user will be taken back to the
				original page with the private block.
			</li>
			<li>
				<span class="term">Combined access blocks</span>: these are blocks that can be either public or private
				depending on their configuration. For example, <b>list_videos</b> can display user video bookmarks as a
				private block, or display the list of premium videos as a public block.
			</li>
		</ul>
		<p>
			Variables with block data are available only within the block template and cannot be accessed from outside.
			If you need to display certain block data in page template, you will need to use a global storage space we
			call <b>$storage</b>. Almost all blocks use the global storage space on the page to store certain output
			data that can be used on the page (i.e. outside the block). In the template of each page, there is a global
			variable called <b>$storage</b> that you can use to access the block output data. For example, the
			<b>video_view</b> block uses the storage to save certain information related to the video currently being
			played, e.g. title, description, tags and more.
		</p>
		<p class="important">
			<b>Important!</b> The <b>$storage</b> variable of a block becomes available only after you insert this
			block into the page.
		</p>
		<p>
			If you want to find out which types of block-related data is available to be used in the template of the
			block or on the page via <b>$storage</b>, use the page debugger. You will find more information on it later
			in this manual.
		</p>
	</div>
	<!-- ch_website_ui_blocks_reference(end) -->
	<!-- ch_website_ui_blocks_usage(start) -->
	<div>
		<h3 id="section_website_ui_blocks_usage">Using Blocks in Pages</h3>
		<p>
			Use the following syntax in the code of page template to insert a block into this page:
		</p>
		<p class="code">
			{{$smarty.ldelim}}insert name="getBlock" block_id="%BLOCK_ID%" block_name="%UNIQUE_BLOCK_NAME%"{{$smarty.rdelim}}
		</p>
		<p>
			Here, the <b>%BLOCK_ID%</b> token needs to be replaced with the <span class="term">block ID</span> of the
			block you are inserting, e.g. <b>list_videos</b>. Also, the <b>%UNIQUE_BLOCK_NAME%</b> token needs to be
			replaced with any unique name for the block on this particular page:
		</p>
		<p class="code">
			{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Most Popular Videos"{{$smarty.rdelim}}
		</p>
		<p class="important">
			<b>Important!</b> When you change a block's name, the block with the old name will be deleted while a block
			with the new name will be added. It means the old settings of the block on the current page will not be
			saved. We recommend giving your blocks meaningful up to date names right away.
		</p>
		<p>
			You can insert any number of blocks of any type within a single page. The only condition here is that the
			block names within this page need to be unique.
		</p>
		<p class="code">
			<span class="comment">{{$smarty.ldelim}}* First list_videos block *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Most Popular Videos"{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* One more instance of list_videos *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Top Rated Videos"{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* The third instance of list_videos *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Premium Videos"{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* List of traders *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}insert name="getBlock" block_id="top_referers" block_name="Traders Text Links"{{$smarty.rdelim}}<br/><br/>
			...
		</p>
		<p>
			Normally, when you insert a block into a page, it will be called and display output data directly in the
			page location where you inserted it. Sometimes you may want a block to run in the beginning of the page,
			e.g. to show its data from <b>$storage</b> in page header (title, description, keywords). To do this, use
			<b>assign</b>, an additional parameter of the <b>{{$smarty.ldelim}}insert ...{{$smarty.rdelim}}</b> directive. When you use this parameter,
			the block's output data will not be displayed in the location where the block was inserted. Instead, the
			data will be sent to the variable set in the value of the <b>assign</b> parameter. Further on, you can
			display this output data in any place on your page using standard Smarty output:
		</p>
		<p class="code">
			<span class="comment">{{$smarty.ldelim}}* Calling the video viewing block in the beginning of the page and assigning its output result to the video_view_result variable *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}insert name="getBlock" block_id="video_view" block_name="View Video" <b>assign="video_view_result"</b>{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Some content follows, e.g. using the $storage of video_view *{{$smarty.rdelim}}</span><br/>
			...<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Now we need to display the results generated by the video viewing block *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}<b>$video_view_result</b>|smarty:nodefaults{{$smarty.rdelim}}<br/><br/>

			...
		</p>
		<p>
			This is enough to show video name in page title, as after the block is processed, the data it sends to the
			<b>storage</b> space can easily be used later on:
		</p>
		<p class="code">
			<span class="comment">{{$smarty.ldelim}}* Calling the video viewing block in the beginning of the page and assigning the result to the video_view_result variable *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}insert name="getBlock" block_id="video_view" block_name="View Video" <b>assign="video_view_result"</b>{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Assigning the page_title variable with the video name stored in global storage *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}assign var="page_title" value=<b>$storage.video_view_video_view.title</b>{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Inserting the page component that will display the value of the page_title variable *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Now we need to display the output results of the video viewing block *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}<b>$video_view_result</b>|smarty:nodefaults{{$smarty.rdelim}}<br/><br/>

			...
		</p>
		<p>
			As you can see from the example, each block uses the global data storage space under its unique key
			(<b>$storage.video_view_video_view</b> in the example). You don't need to try and understand how these keys
			are created. The administration panel displays these keys for each block in the block table of each
			individual site page. When you need to use the global storage space, just copy the key from the table. You
			can also use the page debugger to see what data the block sent to <b>$storage</b>. See Debugging Site Pages
			for more information.
		</p>
		<p>
			To delete a block from a page, you only need to remove the directive that inserts the block from the code
			of the template. Files of the block will not be deleted. We did this to help you restore blocks faster if
			you remove any by mistake. If you insert the block directive with the same parameters again (the
			<b>block_id</b> and <b>block_name</b> directive parameters), the block will be restored with its original
			configuration settings. If you still want to delete the block forever i.e. delete the block files (we
			recommend doing this only after you made sure the block was deleted from the page accurately), go to
			<span class="term">Pages list</span> and you'll see the sidebar menu will have a new item called
			<span class="term">Cleanup files</span>. Right next to it, the number of files to clean up will be shown.
			Use this feature to delete unused block files.
		</p>
		<p class="important">
			<b>Important!</b> After you delete the block files (template file and configuration file) on the
			<span class="term">Сleanup files</span> page, you will not be able to restore the data of the block you
			deleted. If you need to restore the data, you will need to create and configure the blog once again.
		</p>
	</div>
	<!-- ch_website_ui_blocks_usage(end) -->
	<!-- ch_website_ui_blocks_params(start) -->
	<div>
		<h3 id="section_website_ui_blocks_params">Configuration Parameters and Block Templates</h3>
		<p>
			The block configuration parameters define the way a block behaves on the page. Almost all blocks let you
			use a large number of configuration parameters, with the exception of the most basic blocks. Block
			templates are used to display the data processed by the block. Alternatively, it can be the other way
			around: templates prepare data used later on by the block. This is mostly related to blocks that display
			various forms to be filled in. It is important to understand that different blocks on the page (even if
			they are the same blocks) can have completely different parameters and templates. Thus, a block's
			parameters and template define how a particular instance of this block works and displays on this
			particular page.
		</p>
		<p>
			When you insert a block into a page, it is initialized with its default parameters and template. It
			contains only the text with the block name. To modify the block parameters and template, go to block
			editing on your current page. Do this either from the <span class="term">Pages list</span> that shows
			blocks with editing links for each page, or from the editor of each individual page that shows the list of
			inserted blocks.
		</p>
		<p>
			Block editing pages feature the following fields:
		</p>
		<ul>
			<li>
				<span class="term">Block name</span>: block name on this particular page, not editable. The block name
				is taken from the block insertion directive that you placed in the page template. If you modify the
				block name in the directive, it will be a different block with other parameters and a different
				template.
			</li>
			<li>
				<span class="term">Block unique ID</span>: internal block ID on the page.
			</li>
			<li>
				<span class="term">Block type</span>: block type ID, not editable. You can find links to display block
				description and its default template next to the block type ID. The default template describes basic
				aspects of the way the block is displayed. You can use it as an example of how the block layout works.
			</li>
			<li>
				<span class="term">Uses components</span>: lists all page components inserted in this block's template.
			</li>
			<li>
				<span class="term">Template code</span>: specifies the Smarty template code for this block on this
				page. In the block template, you can insert page component and ad spots. However, you cannot insert
				other blocks. If you are not sure which variables you can use in block templates to display certain
				types of content, open the debugger for this page. It will show you all available variables with their
				current values (see Debugging Site Pages for more information).
			</li>
			<li>
				<span class="term">Cache lifetime</span>: lifetime in seconds specifying the time during which this
				block will use cache on this particular page. When set to <b>0</b>, the block will not be cached.
				Blocks that do not support caching do not let you edit this field.
			</li>
			<li>
				<span class="term">disable caching for registered users</span>: this lets you disable caching of the
				block for registered site users. When this is enabled, you can modify the template of this block in
				such a way so that it shows different content to different users (e.g. displaying a greeting like 'Hi,
				&lt;user&gt;'). We do not recommend using this for blocks with lists, as this will generate excessive
				database load.
			</li>
			<li>
				<span class="term">Configuration parameters</span>: a table with supported parameters and their values
				for this block (if the block has configuration parameters). If you want to enable a parameter, tick the
				checkbox and enter the required value. Some parameters can be just on or off, no value. Enabled
				parameters are highlighted in bold.
			</li>
		</ul>
		<p>
			Among all the various parameters, let us take a closer look at the so-called <b>var</b> parameters. These
			are used in many blocks, e.g. <b>var_from</b> for pagination, <b>var_video_dir</b> for displaying videos,
			and more. These parameters are called <b>var</b> parameters because they are related to the name of
			corresponding HTTP parameters you want a particular block to use on any given page. Let us have a look at
			an example with the <b>list_videos</b> block. The block needs to know the ID of a category to display a
			list of videos of this category. A category can be identified in 2 ways, with an ID and with a directory.
			Let us imagine we want to display videos that belong to a particular category by directory (the way you
			would usually do when you want the URLs of your site to look nice). You need the block to know which HTTP
			parameter sends the directory name of the category. The <b>var_category_dir</b> contains this value,
			letting the block know which HTTP parameter to check for the category directory name. It means that if this
			parameter is set to <b>dir</b>, the link to the page with the current block needs to contain the directory
			name in its <b>dir</b> HTTP parameter. An example of such a link would be
			<b>/page_id.php?dir=category_directory_value</b>. Other <b>var</b> parameters work in a similar way.
		</p>
		<p>
			One may think it would have been an easier way to go if we just introduced fixed names for all HTTP
			parameters that blocks can understand. Here, you need to take into account two important factors. First,
			enabling the parameter in block setting may influence the way the block works, even if the value for this
			parameter is not sent in the HTTP request. Second, this method of specifying parameters that contain values
			you want your block to process lets you display several video lists from several categories. For example,
			you can build a page that will show 3 lists of videos from 3 most popular categories on your site. To do
			this, you will need to send directories (or IDs) of 3 categories to this page. Naturally, these would be 3
			different HTTP parameters. You may never actually need to use this feature, but KVS lets you build pages
			like this nonetheless.
		</p>
		<p>
			Luckily, in most cases you can use default values of corresponding <b>var</b> parameters. You only need to
			realize that for a block to function correctly, the link to this page needs to contain the <b>var</b>
			parameters this block needs. If you use mod_rewrite for links (which you most likely do), your mod_rewrite
			rules also need to take into account that certain blocks need certain <b>var</b> parameters.
		</p>
		<p>
			<b>Var</b> parameters are among the key elements of the KVS engine. They provide blocks with the data they
			need to function correctly. They are also used to cache blocks and pages. The default names and values of
			<b>var</b> parameters were given in such a way that matching blocks could function trouble free on the same
			page. For example, the <b>video_view</b>, <b>video_comments</b>, <b>list_videos</b> and
			<b>top_referrers</b> blocks have parameters with the same name and value. These are <b>var_video_dir</b> 
			and <b>var_video_id</b>. When you insert these blocks into one and the same page and enable any of these
			parameters in all the blocks, the blocks will show video details, video comments, related vides, and
			referrer list that match the video category.
		</p>
		<p>
			In most cases, same blocks on different pages would look similar, with the exception of certain elements.
			Names of video lists may be different on each page, but the lists will be essentially the same. In such
			cases, you may want to move the way the block is displayed (its template) into a page component and include
			this component in the block. Different elements, like the list name, should be displayed using Smarty
			variables, values of which are assigned right before the component is included into the page. The design is
			built this way by default in cases where there are several page components showing blocks used most often
			(e.g. <b>list_videos_block_common.tpl</b> and <b>list_videos_block_internal.tpl</b> components are used by
			the <b>list_videos</b> block on many pages).
		</p>
	</div>
	<!-- ch_website_ui_blocks_params(end) -->
	<!-- ch_website_ui_blocks_pagination(start) -->
	<div>
		<h3 id="section_website_ui_blocks_pagination">List Blocks and Pagination</h3>
		<p>
			Almost half of the blocks featured in KVS are list blocks. List blocks are special in the way that they can
			display only a part of the list and let the user navigate through list pages (pagination). Alternatively,
			these blocks can show lists without pagination with all the items displayed together.
		</p>
		<p>
			All list blocks support pagination inside the block as well as AJAX-based pagination that does not require
			the user to reload the page (this is not used in default templates). If you want to enable pagination in a
			list block, you need to make sure the <b>var_from</b> parameter is enabled, telling the block which HTTP
			parameter contains current page number (<b>var_from=from</b> by default). You can also use other pagination
			parameters supported by all list blocks:
		</p>
		<ul>
			<li>
				<b>items_per_page</b>: number of list items on one page. The number of pages will be calculated
				according to the total number of items.
			</li>
			<li>
				<b>links_per_page</b>: this is how many numbers of other pages a page shows.
			</li>
		</ul>
		<p>
			After you configure the block parameters, you will also need to add the pagination links to the block
			template. By default, block templates do not contain these. To do so, you can simply insert the
			<b>pagination_block_common</b> page component where you want your links to be placed. This component
			contains the configuration of how page lists are displayed:
		</p>
		<p class="code">
			...<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Inserting the pagination on top of the list *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}include file="pagination_block_common.tpl"{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Here comes the list itself *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}foreach name=data item=item from=$data{{$smarty.rdelim}}<br/>
			...<br/>
			{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/><br/>

			<span class="comment">{{$smarty.ldelim}}* Inserting second pagination in the bottom of the list. This example shows you can insert pagination twice in a block *{{$smarty.rdelim}}</span><br/>
			{{$smarty.ldelim}}include file="pagination_block_common.tpl"{{$smarty.rdelim}}<br/><br/>

			...
		</p>
		<p>
			All pagination links displayed within a block are SE-friendly:
		</p>
		<p class="code">
			http://your_domain.com/popular_videos/<br/>
			http://your_domain.com/popular_videos/1/<br/>
			http://your_domain.com/popular_videos/2/<br/>
			... etc.
		</p>
		<p>
			Alternatively, you can configure your links in a different way. See the FAQ for more information.
		</p>
		<p>
			For these links to work correctly, you need to configure your mod_rewrite rules for all existing lists
			accordingly. If you are adding new lists or changing the page URLs for existing ones, you need to make sure
			your mod_rewrite rules are configured for correct pagination.
		</p>
		<p>
			Even though all list blocks support pagination, it can also be displayed with a separate block called
			<b>pagination</b>. First, this is required for backward compatibility with older KVS versions. Second, this
			separate <b>pagination</b> block offers advanced pagination features for certain situations (see the
			<b>pagination</b> block manual for more information).
		</p>
		<p class="important">
			<b>Important!</b> Don't use same <b>var_from</b> values for blocks on the same page. As different lists may
			have different number of items, for each block, the page number needs to be sent in a different parameter.
			Otherwise, in some blocks pages with certain numbers may not exist, and the page will return 404 error.
			Once again, this concerns only pages that contain several list blocks.
		</p>
		<p>
			Rounding up the pagination part, let us take a look at how AJAX-based pagination works. To use it, you need
			to make 2 minor changes to the pagination concept described above:
		</p>
		<ul>
			<li>
				First, the entire template (its HTML code) of the list block needs to be wrapped into a div with a
				unique block ID:
				<span class="code">
					&lt;div id="{{$smarty.ldelim}}$block_uid{{$smarty.rdelim}}"&gt;<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;...<br/>
					&lt;/div&gt;
				</span>
			</li>
			<li>
				Second, instead of including the <b>pagination_block_common</b> component, you need to include the
				<b>pagination_block_ajax</b> component:
				<span class="code">
					{{$smarty.ldelim}}include file="pagination_block_ajax.tpl"{{$smarty.rdelim}}
				</span>
			</li>
		</ul>
	</div>
	<!-- ch_website_ui_blocks_pagination(end) -->
	<!-- ch_website_ui_blocks_global(start) -->
	<div>
		<h3 id="section_website_ui_blocks_global">Global Blocks</h3>
		<p>
			When you include a block on a page of your site, you need to specify its template and parameters to be used
			by the block on this very page. Often, when building a site, you need to use blocks with the same
			configuration on multiple pages. For instance, you may want to show your tag cloud on different pages, or
			top 10 site search queries for the past 7 days. To avoid duplicating the settings and templates of these
			blocks, KVS lets you define global site blocks and include them wherever you need them.
		</p>
		<p>
			Global blocks are created manually in the list of <span class="term">Global blocks</span> in
			<span class="term">Website UI</span>. You will need to select block type and give the block a name. Then,
			you can go to the block editing page and configure the template and the parameters to be used throughout
			the entire site.
		</p>
		<p>
			This is the syntax you need to use to include a global block into any page of your site:
		</p>
		<p class="code">
			{{$smarty.ldelim}}insert name="getGlobal" global_id="%UNIQUE_BLOCK_ID%"{{$smarty.rdelim}}
		</p>
		<p>
			The global block inclusion directive is shown right in the list of global blocks. All you need to do is
			copy it and paste into your page template where needed. Whenever you make changes to the template and/or
			the parameters of a global block, the changes will affect all pages where this block is used.
		</p>
		<p class="important">
			<b>Important!</b> You can only insert global blocks (like regular blocks) in page templates.
		</p>
	</div>
	<!-- ch_website_ui_blocks_global(end) -->
	<h2 id="section_website_ui_advertising">Advertisements and Ad Spots</h2>
	<!-- ch_website_ui_advertising_usage(start) -->
	<div>
		<h3 id="section_website_ui_advertising_usage">Using Ad Spots</h3>
		<p>
			KVS features a basic advertising management module that is powerful enough for the needs
			of most site owners.
		</p>
		<p>
			Essentially, ad spots are containers (i.e. slots) where ads are placed. They can be used in templates of
			pages, components, and blocks. You can assign any number of ads to a spot, making the ads rotate randomly.
		</p>
		<p>
			This is the directive you need to use when you want to include an ad spot into a template of a page,
			component, or block:
		</p>
		<p class="code">
			{{$smarty.ldelim}}insert name="getAdv" place_id="%ADVERTISING_SPOT_EXTERNAL_ID%"{{$smarty.rdelim}}
		</p>
		<p>
			In this example, you need to replace the <b>%ADVERTISING_SPOT_EXTERNAL_ID%</b> token with the
			<span class="term">External ID</span> of the ad you want to use, e.g. <b>top_banner</b>:
		</p>
		<p class="code">
			{{$smarty.ldelim}}insert name="getAdv" place_id="top_banner"{{$smarty.rdelim}}
		</p>
	</div>
	<!-- ch_website_ui_advertising_usage(end) -->
	<!-- ch_website_ui_advertising_admin_panel(start) -->
	<div>
		<h3 id="section_website_ui_advertising_admin_panel">Configuring Ads in Administration Panel</h3>
		<p>
			Ad spots support the following data fields:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: ad spot name for the administration panel.
			</li>
			<li>
				<span class="term">External ID</span>: unique ID to be used to include this ad spot on the site. Can
				contain a limited set of characters (a-z, A-Z, 0-9, _).
			</li>
		</ul>
		<p>
			Ads support the following data fields:
		</p>
		<ul>
			<li>
				<span class="term">Title</span>: ad name to be used in the administration panel.
			</li>
			<li>
				<span class="term">Spot</span>: lets you select the ad spot this ad will be assigned to. The ad will be
				shown in the selected spot only.
			</li>
			<li>
				<span class="term">Status</span>: enabling or disabling the ad. Your site shows only ads with active
				status.
			</li>
			<li>
				<span class="term">Show date</span>: lets you set a date interval within which this ad will be shown.
				If no date interval is set, the ad will be shown continuously. You can also set either just the start
				or the end date for showing the ad.
			</li>
			<li>
				<span class="term">HTML code</span>: ad content (static HTML only).
			</li>
			<li>
				<span class="term">Outgoing URL</span>: lets you track clicks on the ad in the statistics. When this
				field is used, the ad HTML code needs to contain the <b>%URL%</b> token in each location where you use
				an outgoing URL. In this case, the token will be replaced with the internal redirect script. The script
				will track the click in the statistics and redirect the user to the ad URL.
			</li>
		</ul>
	</div>
	<!-- ch_website_ui_advertising_admin_panel(end) -->
	<h2 id="section_website_ui_pages">Site Pages</h2>
	<!-- ch_website_ui_pages_reference(start) -->
	<div>
		<h3 id="section_website_ui_pages_reference">Overview</h3>
		<p>
			Pages are entry points into the KVS site environment and top level elements of site structure. Pages can
			contain all elements described above, i.e. components, blocks, and ad spots.
		</p>
		<p>
			Each page has its unique ID that is used to create the page's PHP file and display template. A PHP file of
			a page is used to create links to it.
		</p>
		<p>
			You can create a page at any time; it will not be used until you place a link to it somewhere on your site.
			Similarly, you can delete a page at any time, and if there are links pointing to it from other pages, these
			links will no longer work.
		</p>
		<p>
			Links to a page need to contain all parameters required for this page to function. These parameters
			actually are all enabled <b>var</b> parameters of all the blocks of this particular page, as described
			above. In most cases, the default templates use abstract links processed by mod_rewrite. This is why many
			parameters a page needs to function are substituted based on URL parts. For example, for the link to a
			video viewing page <b>/videos/123/my-uploaded-video/</b> this redirect rule will be used:
		</p>
		<p class="code">
			RewriteRule ^videos/([0-9]+)/([^/]+)/$ /view_video.php?id=$1&amp;dir=$2 [L,QSA]
		</p>
		<p>
			This rule means that the <b>/videos/123/my-uploaded-video/</b> link will be redirected to the page with
			<b>view_video</b> ID (PHP file of this page is <b>view_video.php</b>), and the number coming after
			<b>videos/</b> will be used as the <b>id</b> parameter while the last part of the URL will be used as the
			<b>dir</b> parameter. So, our example generates this request:
		</p>
		<p class="code">
			/view_video.php?id=123&amp;dir=my-uploaded-video
		</p>
		<p>
			Then, the engine will process this request. It will load all blocks on this page giving them <b>id=123</b>
			and <b>dir=my-uploaded-video</b> as parameters. The blocks will display their content based on the values of
			these parameters. In the example given, the <b>video_view</b> block on this page will display video with ID
			<b>123</b>, if the directory of this video matches the directory sent with the <b>dir</b> parameter. If the
			directory does not match, the block will make a 301 redirect to the same video but with the correct
			directory value.
		</p>
		<p>
			The engine processes all pages of the site created via the administration panel in a similar way.
		</p>
		<p>
			In addition to the mod_rewrite rules, there are also global settings in which you need to configure
			patterns for links to pages displaying various types of site content (videos, photo albums, models, etc.)
			These patterns are used to create links to video viewing pages (and pages for viewing other content) on the
			site and in the administration panel. Go to <span class="term">Settings</span> and then to
			<span class="term">Website settings</span> in the administration panel to find these settings. The patters
			defined there need to have matching mod_rewrite rules so that the links do not generate 404 errors.
		</p>
	</div>
	<!-- ch_website_ui_pages_reference(end) -->
	<!-- ch_website_ui_pages_admin_panel(start) -->
	<div>
		<h3 id="section_website_ui_pages_admin_panel">Configuring Pages in Administration Panel</h3>
		<p>
			Page files are stored in the root directory of your domain on your server (PHP files), and in the
			<b>/template</b> directory (template files).
		</p>
		<p>
			If Apache has no permissions to create files in the root directory of your site, before creating a page,
			you will need to copy its PHP file template manually from <b>/admin/tools/page_template.php</b> to the
			root directory of the site, and change its name to the name of the page file. The renamed file needs to
			have a name similar to <b>%external_id%.php</b>, where instead of the <b>%external_id%</b> token you need
			to specify the external ID you will be using while creating a page. For instance, if you want to create a
			page with <b>my_new_page</b> external ID, before you create it, you will need to copy the PHP file template
			to the file <b>/my_new_page.php</b> in the root directory of your site. If you did not copy the PHP page
			file where necessary and KVS lacks permissions to do it automatically, you will see an error while trying
			to create a page.
		</p>
		<p>
			Similarly, before you delete a page, you will need to delete its PHP file from the root directory of your
			site. Only then you are safe to delete the page completely.
		</p>
		<p>
			<span class="term">Pages list</span> is a primary element in the <span class="term">Website UI</span>
			section of the administration panel. The list shows a hierarchy of pages with blocks on them (lines
			denoting pages are highlighted in bold). It also lets you modify basic settings of page and block caching.
			The list contains the following colums:
		</p>
		<ul>
			<li>
				<span class="term">Page / block name</span>: name of the page or the block, depending on the line.
			</li>
			<li>
				<span class="term">Block type</span>: block type (for lines that contain block data).
			</li>
			<li>
				<span class="term">Cache</span>: cache lifetime for the page or the block, set in seconds. As some
				blocks do not support caching, this field will not be editable for such blocks, as well as for pages
				with such blocks.
			</li>
			<li>
				<span class="term">Compress</span>: specifies whether cache compression is used (for page-related lines
				only).
			</li>
			<li>
				<span class="term">Loads</span>: shows the number of requests to each site page from the time the
				performance statistics was reset last. This value can be seen as relative popularity of this or that
				page.
			</li>
			<li>
				<span class="term">Performance</span>: shows the performance statistics of each site page since its
				last reset.
			</li>
		</ul>
		<p>
			You can create a page using 2 methods:
		</p>
		<ul>
			<li>
				Create a page manually in the administration panel speficying its external ID and template code. After
				you have created the page, you need to configure block parameters and templates for the blocks used on
				your page.
			</li>
			<li>
				Duplicate an existing page. To do this, you can use the <span class="term">Duplicate</span> option of
				the context menu for the source page in the list. You just need to enter the ID of your new page.
				Copying existing pages is easier because it copies all the settings of sourse pages, creating a fully
				identical copy.
			</li>
		</ul>
		<p>
			When you add or edit a page, you can use the following fields:
		</p>
		<ul>
			<li>
				<span class="term">Display name</span>: page name to be used in the administration panel.
			</li>
			<li>
				<span class="term">External ID</span>: unique page ID that can contain a limited set of characters
				(a-z, A-Z, 0-9, _). This ID is used in PHP file and in page template filenames. ID cannot be changed
				after you create a page.
			</li>
			<li>
				<span class="term">Status</span>: lets you disable the page. If users request a disabled page, they
				will see a 404 error as if the page does not exist.
			</li>
			<li>
				<span class="term">Cache lifetime</span>: period of time for which the page will be cached in RAM. When
				set to <b>0</b>, this will disable caching. For pages containing blocks that cannot be cached, this
				field is not editable.
			</li>
			<li>
				<span class="term">Enable MemCache compression</span>: specifies whether cache compression should be
				used for this page. When enabled, it lets you save RAM while slightly affecting page performance.
			</li>
			<li>
				<span class="term">XML content type</span>: specifies that the page will deliver XML data. Used to
				build XML feeds.
			</li>
			<li>
				<span class="term">Disallow access for</span>: lets you disallow access to the page for certain types
				of site users.
			</li>
			<li>
				<span class="term">Redirect disallowed users to</span>: if you disallowed page access for some users,
				you can set a URL to which such users will be redirected when they try to access the page. When this is
				left blank, the users will see a 403 error.
			</li>
			<li>
				<span class="term">Rules from .htaccess</span>: the field is available only for existing pages. It
				shows rules from the root .htaccess file associated with the page.
			</li>
			<li>
				<span class="term">Uses components</span>: lists all page components included in the page template.
			</li>
			<li>
				<span class="term">Template code</span>: Smarty template code for the page. In page templates, you can
				include blocks, page components and ad spots.
			</li>
			<li>
				<span class="term">Page content and caching strategy</span>: table listing the blocks included into the
				page, with parameters enabled for each block, their values, and parameter descriptions. The table lets
				you easily modify the value of any enabled parameter for any block on the page, as well as customize
				cache lifetime for all blocks. If you want to enable or disable a block parameter, go to the block
				editing page using the link in the block name. To make things easier for you, the table also shows the
				value of the <b>$storage</b> variable for each block. These values can be used in page templates to
				access block data from inside the page.
			</li>
		</ul>
		<p>
			See below for more details on caching.
		</p>
	</div>
	<!-- ch_website_ui_pages_admin_panel(end) -->
	<!-- ch_website_ui_pages_existing(start) -->
	<div>
		<h3 id="section_website_ui_pages_existing">Brief Overview of Existing Pages</h3>
		<p>
			All the pages in the default site design can be classified into 3 logical groups:
		</p>
		<ul>
			<li>
				Pages used in member areas and are not available for the outside, e.g. user profile editing page, video
				upload page, etc. These pages have the <b>[Memberzone]</b> prefix in their names, as well as the
				<b>member_my_</b> prefix in page external ID (for instance, <b>[Memberzone] My Friends Events</b> and 
				<b>member_my_friends_events</b>).
			</li>
			<li>
				Pages displahing data related to a particular user, available to all users, e.g. page with the list of
				videos uploaded by a user, a page with friends of a user, etc. These pages have the <b>Member's</b> 
				prefix in their names, as well as the <b>member_</b> prefix in page external ID (for instance,
				<b>Member's Events</b> and <b>member_events</b>).
			</li>
			<li>
				Other non user related public pages, e.g. list of categories, login page, etc.
			</li>
		</ul>
		<p>
			We recommend following these naming conventions when you create new pages.
		</p>
	</div>
	<!-- ch_website_ui_pages_existing(end) -->
	<h2 id="section_website_ui_caching">Caching</h2>
	<p>
		Caching lets you noticeably decrease server load as your site traffic grows. The KVS engine supports 2 levels,
		or tiers of caching: standard caching on the block template level, and superfast caching of entire pages in
		server RAM.
	</p>
	<p>
		The <span class="term">Cache lifetime</span> field is the primary parameter used to configure the way caching
		works. You can adjust it for blocks as well as for pages. The longer the lifetime, the lower the server load.
		However, it may mean your page content will be less up to date. Pages and/or blocks that are not updated for
		longer periods of time can be cached with longer lifetimes, while pages and/or blocks that are to be up to date
		at all times (like the index page) should have shorter cache lifetimes. Set the cache lifetime to <b>0</b> if
		you want to disable caching for a particular page and/or block.
	</p>
	<!-- ch_website_ui_caching_blocks(start) -->
	<div>
		<h3 id="section_website_ui_caching_blocks">First-tier Caching (Block Caching)</h3>
		<p>
			First-tier caching (block caching) is relatively slow. You can use this type of caching virtually anywhere
			as long as the blocks you use support it. Just like we have said above, certain blocks do not support
			caching. Mainly these are blocks containing forms to be filled in, e.g. the login block, the video upload
			block etc. Some blocks do not support caching in certain configurations, e.g. the video list block does not
			support caching when displaying search results and in some other situations. Anyway, even when caching is
			enabled, the engine will decide which page content to cache.
		</p>
		<p>
			When you enable first-tier caching, you can set cache lifetime separately for each block. You can do this
			anywhere in the administration panel where block editing is available. We recommend using the
			<span class="term">Pages list</span> section where you can also set global caching parameters for your
			entire site.
		</p>
	</div>
	<!-- ch_website_ui_caching_blocks(end) -->
	<!-- ch_website_ui_caching_pages(start) -->
	<div>
		<h3 id="section_website_ui_caching_pages">Second-Tier Caching (Page Caching)</h3>
		<p>
			Second-tier caching (caching of entire pages) is a very fast type of caching. Cached pages are placed in
			server RAM and are immediately sent to the user upon request. Page caching largely depends on caching of
			blocks used on a particular page. In situations when one block does not support caching, it means the
			entire page with this block cannot be cached. At the same time, caching-enabled blocks on that very page
			can be cached using first-tier caching.
		</p>
		<p>
			With second-tier caching, you can use caching compression, an additional feature that lets you save RAM
			while slightly affecting overall cache speed. Use this feature on pages instances of which are stored in
			RAM in large quantities, e.g. video view pages (the cache stores a large number of instances of this page
			for all videos).
		</p>
		<p>
			When you enable second-tier caching, you can set cache lifetime separately for each page. We recommend
			using the <span class="term">Pages list</span> section where you can also set global caching parameters for
			your entire site.
		</p>
	</div>
	<!-- ch_website_ui_caching_pages(end) -->
	<!-- ch_website_ui_caching_important(start) -->
	<div>
		<h3 id="section_website_ui_caching_important">Important Aspects of Caching</h3>
		<p>
			There are a few caching aspects you need to understand before you start configuring your site in KVS.
		</p>
		<p>
			Caching is disabled for site administrator (if they are logged into the administration panel). This was
			done to avoid any lags during initial debugging stage. Final site testing should be done with caching taken
			into account, i.e. you should test your site in a different browser (not in the same browser you use to
			work with the administration panel).
		</p>
		<p>
			Caching limits your possibilities of customizing your site to a certain extent (especially second-tier
			caching). For instance, displaying certain content cannot depend on parameters sent to the page. Let us
			have a look at a basic example. You need to send the affiliate ID that will later be inserted into a link
			to a sponsor site (processing affiliate traffic). If this ID is sent in the <b>wm_id</b> parameter, the
			easiest method you can think of is displaying the link to the sponsor site while specifying the value from
			the request parameter in it:
		</p>
		<p class="code">
			&lt;a href="http://sponsor.com?wm_id={{$smarty.ldelim}}$smarty.request.wm_id{{$smarty.rdelim}}"&gt;
		</p>
		<p>
			This will not work properly when caching is enabled, because for the entire period of cache lifetime the
			link will contain the same value of <b>wm_id</b>. To make these parameters work properly, you need to use
			<span class="term">Dynamic HTTP parameters</span>. You can find these by going to
			<span class="term">Settings</span> and then to <span class="term">Website settings</span> in the
			administration panel. In these fields, you can list up to 5 HTTP parameters that will not depend on
			caching. Moreover, these HTTP parameters will also be supported in the links to content sources and in
			outgoing ad links.
		</p>
		<p>
			To use these parameters, insert a <b>%param_name%</b> type token into the template (content sources URL or
			ad URL). It means that for the example above the link to the sponsor site needs to look like this:
		</p>
		<p class="code">
			&lt;a href="http://sponsor.com?wm_id=%wm_id%"&gt;
		</p>
		<p>
			Here, it means that you have already set the <b>wm_id</b> parameter in one of the fields of
			<span class="term">Dynamic HTTP parameters</span>.
		</p>
		<p>
			In situations when a block does not support caching, using request parameters remains a valid method. As an
			example, let's have a look at the user profile editing block. It displays 3 different forms depending on
			the value of the <b>action</b> HTTP parameter sent to the page. As this block does not support caching,
			which also means the page where this block is inserted is also not cached, you are free to use request
			parameters in this context.
		</p>
		<p>
			Thus, in most cases you will not be able to freely use variables like <b>$smarty.request</b>,
			<b>$smarty.get</b>, <b>$smarty.post</b> in site templates. You will need to use dynamic HTTP parameters
			instead.
		</p>
		<p>
			Second-tier caching is automatically disabled for users who are logged in. It lets you not just display
			greetings like 'Hello, username' in site header, but also use virtually any user session data in page
			templates (but not in block templates).
		</p>
		<p>
			In most cases, you will not be able to use values from user session data (<b>$smarty.session</b>) in block
			templates when you want to display different content to different site users. The reason for this is that
			for all users the same block cache is used. Currently, it works like this everywhere with the exception of
			these blocks: <b>video_view</b>, <b>video_comments</b>, <b>album_view</b>, <b>album_comments</b>, 
			<b>album_images</b>, <b>dvd_view</b> and <b>dvd_comments</b>. These blocks save different versions of cache
			for different users. Also, you can use any user session data in all blocks that are not cached.
		</p>
		<p>
			Check whether your caching settings are correct by launching a page, block and template check using the
			audit plugin. If no errors are found, most likely the caching settings are optimal. If you see errors, we
			strongly recommend fixing them to avoid site malfunctions.
		</p>
		<p>
			In case you need to reset the cache, whether first- or second-tier, you can use the
			<span class="term">Reset file cache</span> and the <span class="term">Reset MemCache</span> features
			accordingly. You can find these features in the side panel of the <span class="term">Website UI</span>
			section in your administration panel. Resetting the cache will result in a server load surge if your
			traffic is high. This is why we recommend using these features in case of emergency only (e.g. when you
			need to update all site pages immediately). First, reset the file cache (block cache), and after a while,
			you can reset the MemCache (page cache).
		</p>
		<p>
			Caching is one of the most advanced and complicated aspects of KVS. You can avoid using
			any caching at all or use only first-tier block caching. In this case, all limitations outlined above apply
			only to block templates. If you need to customize your site in ways that may potentially affect caching,
			we recommend contacting us for more in-depth information.
		</p>
	</div>
	<!-- ch_website_ui_caching_important(end) -->
	<!-- ch_website_ui_caching_performance(start) -->
	<div>
		<h3 id="section_website_ui_caching_performance">Performance Statistics</h3>
		<p>
			Performance statistics lets you evaluate the performance of any page or block. This lets you find out
			whether your chosen caching strategy is right for your site.
		</p>
		<p>
			For each page / block, the list of pages shows 4 numbers:
		</p>
		<ul>
			<li>
				Total page loads since monitoring started.
			</li>
			<li>
				Average load time with caching.
			</li>
			<li>
				Average load time without caching.
			</li>
			<li>
				Relative caching percent. This parameter is the most important as it lets you see how often the cache
				of this particular page / block is used. The higher this value, the more often cache is triggered, i.e.
				your page or block performs better.
			</li>
		</ul>
		<p>
			Performance statistics can be reset in any moment. Use <span class="term">Reset performance stats</span> to
			do so. You will find it in the side panel of the <span class="term">Website UI</span> section of the
			administration panel.
		</p>
	</div>
	<!-- ch_website_ui_caching_performance(end) -->
	<h2 id="section_website_ui_other">Other Site-Related Issues</h2>
	<!-- ch_website_ui_other_js(start) -->
	<div>
		<h3 id="section_website_ui_other_js">JavaScript Files</h3>
		<p>
			KVS uses a number of JavaScript files to power your site. These scripts are located in
			<b>/js</b> in the root directory of your site. Some blocks use the functions stored in these JavaScript
			files. This is why these blocks require enabling such files on corresponding pages. JavaScript files
			required by blocks on a page will be included into this page automatically. This line in
			<b>header_general.tpl</b> includes the files:
		</p>
		<p class="code">
			{{$smarty.ldelim}}$js_includes|smarty:nodefaults{{$smarty.rdelim}}
		</p>
		<p>
			All JavaScript files are included via links that contain current software version. These links also use the
			<b>jsx</b> extension instead of <b>js</b>. This is required to prevent user browsers from caching
			JavaScript files after you upgrade to a newer KVS version (all links will become different). Here is a
			sample link:
		</p>
		<p class="code">
			http://your_domain.com/js/KernelTeamVideoSharingSystem_2.0.0.jsx
		</p>
		<p>
			Among all the JavaScript files, some are system files. These are added independently from blocks: 
			<b>KernelTeamVideoSharingSystem.js</b> (always added) and <b>KernelTeamVideoSharingMembers.js</b> (added
			only for logged in users).
		</p>
		<p>
			By default, all video lists are configured in such a way that they rotate 240x180 video screenshots. The
			JavaScript code with the rotator is included in site header and is available on all pages of your site. If
			you use only 1 screenshot for your videos or you do not intend to use any screenshot rotation at all, we
			recommend removing the inclusion of the rotating script from the header template. This is how the rotator
			is included:
		</p>
		<p class="code">
			&lt;script type="text/javascript" src="{{$smarty.ldelim}}$config.project_url{{$smarty.rdelim}}/js/KernelTeamImageRotator_{{$smarty.ldelim}}$config.project_version{{$smarty.rdelim}}.jsx"&gt;&lt;/script&gt;<br/>
			&lt;script type="text/javascript"&gt;<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;KT_rotationEngineStartup(0.2, 0.5);<br/>
			&lt;/script&gt;
		</p>
		<p>
			When calling the <b>KT_rotationEngineStartup</b> function, the second parameter sets the period for which a
			single screenshot is shown (in seconds) before the next one appears. The first parameter is ignored.
		</p>
		<p>
			The rotation itself is launched when the mouse is moved over the screenshot (and stops when the mouse is
			moved outside the screenshot):
		</p>
		<p class="code">
			<span class="comment">
				// second parameter sets the path to screenshot directory<br/>
				// third parameter sets the number of screenshots<br/>
			</span>
			onmouseover="KT_rotationStart(this, '{{$smarty.ldelim}}$config.content_url_videos_screenshots{{$smarty.rdelim}}/{{$smarty.ldelim}}$item.screen_url{{$smarty.rdelim}}/240x180/', {{$smarty.ldelim}}$item.screen_amount{{$smarty.rdelim}})"<br/>
			<br/>
			onmouseout="KT_rotationStop(this)"
		</p>
		<p>
			If you need to rotate screenshots of other formats, you will need to replace 240x180 with your desired size
			in the mouseover handler.
		</p>
		<p>
			The table below lists all default JavaScript files. All of these are stored in <b>/js</b> in the root
			directory of your site. Some of these files are available only in the full KVS package.
		</p>
		<div class="table">
			<table>
				<colgroup>
					<col width="10%"/>
					<col/>
				</colgroup>
				<tr class="header">
					<td>File</td>
					<td>Description</td>
				</tr>
				<tr>
					<td>KernelTeamImageRotator.js</td>
					<td>
						Screenshot rotator implemented in the site header template. Can be used on any page.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingAlbumEdit.js</td>
					<td>
						Script that validates and processes the photo album creation/editing form.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingAlbumView.js</td>
					<td>
						Script that processes and validates various forms used in photo album blocks: rating, adding to
						favorites, submitting comments, etc.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingCSView.js</td>
					<td>
						Script that processes and validates various forms used in content source blocks: rating,
						submitting comments, etc.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingDVDView.js</td>
					<td>
						Script that processes and validates various forms used in DVD / channel blocks: rating,
						submitting comments, etc.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingForms.js</td>
					<td>
						Script that processes and validates various forms used in external site blocks: login and
						registration forms, inviting friends, etc.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingMembers.js</td>
					<td>
						Script that processes and validates various forms used by site users: editing user profile,
						managing bookmarks, internal messaging, managing subscriptions, etc.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingModelView.js</td>
					<td>
						Script that processes and validates various forms used in model blocks: rating, submitting
						comments, etc.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingSystem.js</td>
					<td>
						System script used on all pages.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingVideoEdit.js</td>
					<td>
						Script that processes and validates the video uploading/editing form.
					</td>
				</tr>
				<tr>
					<td>KernelTeamVideoSharingVideoView.js</td>
					<td>
						Script that processes and validates various forms used in video blocks: rating, adding to
						favorites, submitting comments, etc.
					</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- ch_website_ui_other_js(end) -->
	<!-- ch_website_ui_other_emails(start) -->
	<div>
		<h3 id="section_website_ui_other_emails">System Emails</h3>
		<p>
			Certain site blocks can send email messages with various content (e.g. sending a link to a page to a
			friend, etc.) You can find texts and headers of these messages in the <b>emails</b> directory of each block
			that can send email notifications. If you need, you can modify these files manually:
		</p>
		<p class="important">
			<b>Important!</b> All email files are saved in UTF8.
		</p>
		<div class="table">
			<table>
				<colgroup>
					<col width="15%"/>
					<col width="25%"/>
					<col/>
				</colgroup>
				<tr class="header">
					<td>Block</td>
					<td>Path to email files</td>
					<td>Description</td>
				</tr>
				<tr>
					<td>album_view</td>
					<td>/blocks/album_view/emails</td>
					<td>
						The block supports emailing the link to the current photo album page to a friend. These tokens
						are supported:<br/>
						- <b>{{$smarty.ldelim}}$message{{$smarty.rdelim}}</b> - message entered by the user when sending the link.<br/>
						- <b>{{$smarty.ldelim}}$link{{$smarty.rdelim}}</b> - link to the current page with the photo album.<br/>
						- <b>{{$smarty.ldelim}}$project_name{{$smarty.rdelim}}</b> - site name specified during installation in <b>setup.php</b>.<br/>
						- <b>{{$smarty.ldelim}}$support_email{{$smarty.rdelim}}</b> - email specified during installation in <b>setup.php</b> that will
						be used as the From address.<br/>
						- <b>{{$smarty.ldelim}}$project_licence_domain{{$smarty.rdelim}}</b> - site domain specified during installation in
						<b>setup.php</b>.<br/>
					</td>
				</tr>
				<tr>
					<td>invite_friend</td>
					<td>/blocks/invite_friend/emails</td>
					<td>
						This block supports emailing the link to your site to a friend. These tokens are supported:<br/>
						- <b>{{$smarty.ldelim}}$message{{$smarty.rdelim}}</b> - message entered by the user when sending the link.<br/>
						- <b>{{$smarty.ldelim}}$project_name{{$smarty.rdelim}}</b> - site name specified during installation in <b>setup.php</b>.<br/>
						- <b>{{$smarty.ldelim}}$support_email{{$smarty.rdelim}}</b> - email specified during installation in <b>setup.php</b> that will
						be used as the From address.<br/>
						- <b>{{$smarty.ldelim}}$project_licence_domain{{$smarty.rdelim}}</b> - site domain specified during installation in
						<b>setup.php</b>.<br/>
					</td>
				</tr>
				<tr>
					<td>logon</td>
					<td>/blocks/logon/emails</td>
					<td>
						This block supports sending messages in case when users are temporarily blocked. These tokens
						are supported:<br/>
						- <b>{{$smarty.ldelim}}$link{{$smarty.rdelim}}</b> - unblocking link.<br/>
						- <b>{{$smarty.ldelim}}$email{{$smarty.rdelim}}</b> - user email.<br/>
						- <b>{{$smarty.ldelim}}$username{{$smarty.rdelim}}</b> - user login.<br/>
						- <b>{{$smarty.ldelim}}$pass{{$smarty.rdelim}}</b> - old user password that was blocked.<br/>
						- <b>{{$smarty.ldelim}}$new_pass{{$smarty.rdelim}}</b> - newly generated user password.<br/>
						- <b>{{$smarty.ldelim}}$project_name{{$smarty.rdelim}}</b> - site name specified during installation in <b>setup.php</b>.<br/>
						- <b>{{$smarty.ldelim}}$support_email{{$smarty.rdelim}}</b> - email specified during installation in <b>setup.php</b> that will
						be used as the From address.<br/>
						- <b>{{$smarty.ldelim}}$project_licence_domain{{$smarty.rdelim}}</b> - site domain specified during installation in
						<b>setup.php</b>.<br/>
					</td>
				</tr>
				<tr>
					<td>member_profile_edit</td>
					<td>/blocks/member_profile_edit/emails</td>
					<td>
						The block supports emailing requests to users to confirm email change. These tokens are
						supported:<br/>
						- <b>{{$smarty.ldelim}}$link{{$smarty.rdelim}}</b> - email change confirmation link.<br/>
						- <b>{{$smarty.ldelim}}$email{{$smarty.rdelim}}</b> - new user email.<br/>
						- <b>{{$smarty.ldelim}}$username{{$smarty.rdelim}}</b> - user login.<br/>
						- <b>{{$smarty.ldelim}}$project_name{{$smarty.rdelim}}</b> - site name specified during installation in <b>setup.php</b>.<br/>
						- <b>{{$smarty.ldelim}}$support_email{{$smarty.rdelim}}</b> - email specified during installation in <b>setup.php</b> that will
						be used as the From address.<br/>
						- <b>{{$smarty.ldelim}}$project_licence_domain{{$smarty.rdelim}}</b> - site domain specified during installation in
						<b>setup.php</b>.<br/>
					</td>
				</tr>
				<tr>
					<td>signup</td>
					<td>/blocks/signup/emails</td>
					<td>
						The block supports emailing registration confirmation requests as well as password change
						notifications (after the password is changed). These tokens are supported:<br/>
						- <b>{{$smarty.ldelim}}$link{{$smarty.rdelim}}</b> - registration confirmation / password reset link.<br/>
						- <b>{{$smarty.ldelim}}$email{{$smarty.rdelim}}</b> - user email.<br/>
						- <b>{{$smarty.ldelim}}$username{{$smarty.rdelim}}</b> - user login.<br/>
						- <b>{{$smarty.ldelim}}$pass{{$smarty.rdelim}}</b> - user password set during registration, or the newly generated password (in
						case the password is reset).<br/>
						- <b>{{$smarty.ldelim}}$project_name{{$smarty.rdelim}}</b> - site name specified during installation in <b>setup.php</b>.<br/>
						- <b>{{$smarty.ldelim}}$support_email{{$smarty.rdelim}}</b> - email specified during installation in <b>setup.php</b> that will
						be used as the From address.<br/>
						- <b>{{$smarty.ldelim}}$project_licence_domain{{$smarty.rdelim}}</b> - site domain specified during installation in
						<b>setup.php</b>.<br/>
					</td>
				</tr>
				<tr>
					<td>video_view</td>
					<td>/blocks/video_view/emails</td>
					<td>
						The block supports emailing the link to the page with the current video to a friend. These
						tokens are supported:<br/>
						- <b>{{$smarty.ldelim}}$message{{$smarty.rdelim}}</b> - message entered by the user when sending the link.<br/>
						- <b>{{$smarty.ldelim}}$link{{$smarty.rdelim}}</b> - link to current video page.<br/>
						- <b>{{$smarty.ldelim}}$project_name{{$smarty.rdelim}}</b> - site name specified during installation in <b>setup.php</b>.<br/>
						- <b>{{$smarty.ldelim}}$support_email{{$smarty.rdelim}}</b> - email specified during installation in <b>setup.php</b> that will
						be used as the From address.<br/>
						- <b>{{$smarty.ldelim}}$project_licence_domain{{$smarty.rdelim}}</b> - site domain specified during installation in
						<b>setup.php</b>.<br/>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- ch_website_ui_other_emails(end) -->
	<!-- ch_website_ui_other_session(start) -->
	<div>
		<h3 id="section_website_ui_other_session">User Session Details</h3>
		<p>
			Below you can find the list of fields used in sessions of logged in users that are available in site
			templates. Use the page debugger to see the actual values of all session variables. Session details will
			only be seen if you are logged in.
		</p>
		<p class="important">
			<b>Important!</b> User session variables can be used only in page templates and in templates of certain
			blocks (<b>video_view</b>, <b>video_comments</b>, <b>album_view</b>, <b>album_comments</b>,
			<b>album_images</b>, <b>dvd_view</b> and <b>dvd_comments</b>).
		</p>
		<div class="table">
			<table>
				<colgroup>
					<col width="10%"/>
					<col/>
				</colgroup>
				<tr class="header">
					<td>Variable</td>
					<td>Description</td>
				</tr>
				<tr>
					<td>$smarty.session.user_id</td>
					<td>
						User ID. If this is set, it means the user is logged in. If this is not set, all session
						parameters will be empty. Usage example:<br/>
						{{$smarty.ldelim}}if $smarty.session.user_id>0{{$smarty.rdelim}}<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;user is logged in<br/>
						{{$smarty.ldelim}}/if{{$smarty.rdelim}}
					</td>
				</tr>
				<tr>
					<td>$smarty.session.display_name</td>
					<td>
						User nickname.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.username</td>
					<td>
						User login.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.last_login_date</td>
					<td>
						Date of user's last login into their member area.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.added_date</td>
					<td>
						User registration date.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.avatar</td>
					<td>
						User avatar, path relative to the <b>$config.content_url_avatars</b> directory:<br/>
						{{$smarty.ldelim}}$config.content_url_avatars{{$smarty.rdelim}}/{{$smarty.ldelim}}$smarty.session.avatar{{$smarty.rdelim}}
					</td>
				</tr>
				<tr>
					<td>$smarty.session.content_source_group_id</td>
					<td>
						ID of content source group, if assigned to this user.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.is_trusted</td>
					<td>
						If this is a trusted user (flagged accordingly in user settings), this variable has the value
						of <b>1</b>.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.unread_messages</td>
					<td>
						Number of unread internal messages. By default this variable is not updated when the user is on
						the site, as recalculating it increases database load. You can enable real time updating of
						this variable by enabling <span class="term">Synchronize user unread messages</span> in
						<span class="term">Website settings</span>.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.unread_invites</td>
					<td>
						Number of unread friend requests. By default this variable is not updated when the user is on
						the site (see details for <b>$smarty.session.unread_messages</b>).
					</td>
				</tr>
				<tr>
					<td>$smarty.session.unread_non_invites</td>
					<td>
						Number of unread messages not counting friend requests (the value of 
						<b>$smarty.session.unread_messages</b> minus the value of 
						<b>$smarty.session.unread_invites</b>). By default this variable is not updated when the user
						is on the site (see details for <b>$smarty.session.unread_messages</b>).
					</td>
				</tr>
				<tr>
					<td>$smarty.session.status_id</td>
					<td>
						User status (2 = active, 3 = premium, 6 = webmaster). If you use paid subscriptions, you will
						need to enable synchronization of user statuses in <span class="term">Website settings</span>.
						This is required so that users lose access (i.e. their status is changed) when subscriptions
						expire.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.tokens_available</td>
					<td>
						Number of tokens available to user. If you use token-based access, you need to enable user
						status synchronization in <span class="term">Website settings</span>.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.paid_access_hours_left</td>
					<td>
						This variable can only be used when user status synchronization is enabled in
						<span class="term">Website settings</span>. It is used for premium users only, showing how many
						full hours is left before premium subscription expires.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.paid_access_is_unlimited</td>
					<td>
						This variable can only be used when user status synchronization is enabled in
						<span class="term">Website settings</span>. It is used for premium users only, showing whether
						their premium subscription will expire (<b>=0</b>) or will not expire (lifetime, <b>=1</b>).
					</td>
				</tr>
				<tr>
					<td>$smarty.session.external_package_id</td>
					<td>
						This variable can only be used when user status synchronization is enabled in
						<span class="term">Website settings</span>. It is used for premium users only, containing
						external ID of the chosen subscription type. You can use it in the templates in case you need
						to display different content to users with different subscription packages.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.playlists</td>
					<td>
						Array with user playlist. Check page debugger for array structure.
					</td>
				</tr>
				<tr>
					<td>$smarty.session.user_info</td>
					<td>
						Full user profile data. Check page debugger for data structure.
					</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- ch_website_ui_other_session(end) -->
	<!-- ch_website_ui_other_engine_customization(start) -->
	<div>
		<h3 id="section_website_ui_other_engine_customization">Customizing Site Engine</h3>
		<p>
			If you need to add custom features to the site processing engine, you can edit certain the PHP files called
			when each site page is processed. These scripts are not updatable, therefore your changes will remain
			intact when system updates will take place.
		</p>
		<div class="table">
			<table>
				<colgroup>
					<col width="30%"/>
					<col/>
					<col/>
					<col/>
				</colgroup>
				<tr class="header">
					<td>Path</td>
					<td>Description</td>
					<td>Request Types</td>
					<td>XML pages</td>
				</tr>
				<tr>
					<td>/admin/include/pre_initialize_page_code.php</td>
					<td>
						Called right before processing a page. Can be used to replace HTTP request parameters, which
						may also affect caching.
					</td>
					<td>POST and GET</td>
					<td>Yes</td>
				</tr>
				<tr>
					<td>/admin/include/pre_process_page_code.php</td>
					<td>
						Called when page processing starts. Called regardless of whether the page is cached or not. You
						cannot replace HTTP parameters in this script. This script is also called for POST requests,
						therefore, you cannot add logic that sends content to the output stream.
					</td>
					<td>POST and GET</td>
					<td>No</td>
				</tr>
				<tr>
					<td>/admin/include/pre_display_page_code.php</td>
					<td>
						Called before page content is displayed. Called regardless of whether the page is cached or
						not. This script is called only for GET requests. Here, you can add external scripts that add
						content to the page output stream (e.g. incoming traffic trade script).
					</td>
					<td>GET</td>
					<td>No</td>
				</tr>
				<tr>
					<td>/admin/include/post_process_page_code.php</td>
					<td>
						Called directly after the page was processed and the content was sent to the user. Called
						regardless of whether the page is cached or not. This script is called for POST requests also.
					</td>
					<td>POST and GET</td>
					<td>No</td>
				</tr>
			</table>
		</div>
	</div>
	<!-- ch_website_ui_other_engine_customization(end) -->
	<!-- ch_website_ui_other_engine_custom_blocks(start) -->
	<div>
		<h3 id="section_website_ui_other_engine_custom_blocks">Creating Custom Site Blocks</h3>
		<p>
			KVS lets you take advantage of its module-based engine that lets you add custom blocks. Each block needs to
			have a unique ID (which is more like a block type ID), and all files of this block need to be located in
			<b>/blocks/%block_id%</b>. Here, <b>%block_id%</b> is the actual block ID.
		</p>
		<p>
			A block needs to contain 3 primary files as well as localization files:
		</p>
		<ul>
			<li>
				<span class="term">/blocks/%block_id%/%block_id%.php</span>: PHP file of the block with all the block
				logic (the way block works).
			</li>
			<li>
				<span class="term">/blocks/%block_id%/%block_id%.tpl</span>: default block template. This template is
				used as an example only, so you can create a blank template.
			</li>
			<li>
				<span class="term">/blocks/%block_id%/%block_id%.dat</span>: - block metadata in XML format. You can
				use this template for this file:
						<span class="code">
							&lt;block&gt;<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&lt;block_name&gt;Block name&lt;/block_name&gt;<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&lt;author&gt;Block developer&lt;/author&gt;<br/>
							&nbsp;&nbsp;&nbsp;&nbsp;&lt;version&gt;Block version (in any format)&lt;/version&gt;<br/>
							&lt;/block&gt;
						</span>
			</li>
			<li>
				<span class="term">/blocks/%block_id%/langs/english.php</span>: default localization file for block
				text.
			</li>
			<li>
				<span class="term">/blocks/%block_id%/langs/russian.php</span>: localization file for Russian block
				text (file is optional).
			</li>
		</ul>
		<p>
			As you can understand, the primary block file is the PHP file. This file needs to contain a set of
			functions with special names. These will be called by the engine when needed. In addition to these, the
			PHP block file also needs to contain this line so that it can respond to the block testing mechanism:
		</p>
		<p class="code">
			if ($_SERVER['argv'][1]=='test' &amp;&amp; $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
		</p>
		<p>
			Each block function (feature) needs to have a name starting with the block ID, i.e.
			<b>%block_id%FunctionName</b>. For example, the <b>Show</b> function for the <b>list_videos</b> block needs
			to have this name: <b>list_videosShow</b>.
		</p>
		<p>
			These are the functions (features) blocks support:
		</p>
		<ul>
			<li>
				<span class="term">Show function (%block_id%Show, required)</span>: called by the engine in case GET /
				POST requests are sent to a page where this block is inserted. Depending on request type, the function
				can either select certain data and prepare it for display, or process the form submitted by the user
				(with POST requests). The Show function is called only when a block does not support caching or block
				cache has expired and full block display cycle needs to be run. The function receives 2 parameters,
				<b>$block_config</b>, an associative array with all enabled block configuration parameters on the page
				and their values (key are parameter name), and <b>$object_id</b>, unique block ID on the page. The
				function places the data to be displayed into the <b>$smarty</b> global object that will display this
				block's template. The data you place into the <b>$smarty</b> object will be available within the block
				template only. If you want certain block data to be available from page template, you need to place
				such data into the <b>$storage</b> global object. Place them into the array under the
				<b>$object_id</b>. Here is a basic example of how this function works:
				<span class="code">
					function my_blockShow($block_config,$object_id)<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;global $smarty,$storage;<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$show_numbers_to=intval($block_config['show_numbers_to']); <span class="comment">// here, we obtain the N number from the block configuration where it is set up</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$data=array();<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;for ($i=1;$i<=$show_numbers_to;$i++)<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$data[]=$i; <span class="comment">// creating an array with all numbers from 1 to N</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$smarty->assign("data",$data); <span class="comment">// placing the array into the $smarty global variable to display in block template</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$storage[$object_id]['total_count']=$show_numbers_to; <span class="comment">// placing total number of elements into the block's $storage to display in page template</span><br/>
					}<br/>
				</span>
				If you want your block to process POST requests, this is how it can be done:
				<span class="code">
					function my_blockShow($block_config,$object_id)<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;global $smarty,$storage;<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;if ($_POST['action']=='my_post_action')<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;form processing logic executed here<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;header("Location: ?action=post_processed_successfully");die; <span class="comment">// completing page processing, redirecting to the same page</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;if ($_GET['action']=='post_processed_successfully')<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$smarty->assign("message","Your request has been processed"); <span class="comment">// placing success message to be displayed</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
					}<br/>
				</span>
				With the Show function, you can only redirect the user and finish page processing when dealing with
				POST requests because no block caching happens in this case. If you need to make a redirect when
				processing a GET request, you need your Show function to return a special string to be processed by the
				engine, with all the specifics of caching taken into account:
				<span class="code">
					function my_blockShow($block_config,$object_id)<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;global $smarty,$storage;<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;here the processing logic is executed<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp; <span class="comment">// telling the engine that 301 redirect should be made</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;return "status_301:http://url_to_redirect.com";<br/>
					<br/>
					&nbsp;&nbsp;&nbsp;&nbsp; <span class="comment">// instead, here we tell the engine that 302 redirect should be made</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;return "status_302:http://url_to_redirect.com";<br/>
					}<br/>
				</span>
				Additionally, you can return a "status_404" string if you want the engine to return the 404 error when
				a page with this block is requested.
			</li>
			<li>
				<span class="term">GetHash function (%block_id%GetHash, required)</span>: used for block caching. The
				function needs to return equal string values for requests for which matching block HTML code is
				displayed while different values will be returned for requests for which the block HTML code will be
				different. This function accepts 1 parameter called <b>$block_config</b>. This is an associative array
				with all enabled block configuration parameters on this page, along with their values (keys are
				parameter names). As a rule, the returned string needs to contain the list of all values sent to the
				block via request parameters, separated with a certain separator. The most basic example here will be
				list block with pagination, returning different hash values for different pages of the list. Here is
				how this is done:
				<span class="code">
					function my_blockGetHash($block_config)<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$from=intval($_REQUEST[$block_config['var_from']]); <span class="comment">// here, we receive the page number value sent with the request, can be empty</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;$category=trim($_REQUEST[$block_config['var_category_dir']]); <span class="comment">// here, we receive the category directory sent with the request, can be empty</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;return "$from|$category"; <span class="comment">// here, we return both values with a separator so that different input parameters result in different output strings</span><br/>
					}<br/>
				</span>
				If you don't need the block to be cached, you can return the system value <b>"nocache"</b>, which will
				mean block caching is disallowed. It also means the page with this block will not be cached.
			</li>
			<li>
				<span class="term">CacheControl function (%block_id%CacheControl, optional)</span>: used to set block
				caching type, which is used in the administration panel to spot possible caching problems. The function
				needs to return one of these strings: <b>"nocache"</b>, if the block is no cached,
				<b>"user_specific"</b>, if the block is cached separately for different users,
				<b>"status_specific"</b>, if the block is cached separately for users with different statuses, and
				<b>"default"</b>, if the block uses standard caching. The function receives 1 parameter called
				<b>$block_config</b>, an associative array with all enabled block configuration parameters on this
				page, and their values (keys are parameter names). Here is an example of this function:
				<span class="code">
					function my_blockCacheControl($block_config)<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;return "user_specific"; <span class="comment">// here, we specify that the block has different cache versions for different users</span><br/>
					}<br/>
				</span>
			</li>
			<li>
				<span class="term">MetaData function (%block_id%MetaData, required)</span>: this function is used to
				specify configuration parameters supported by the block. It is these parameters and their values, when
				enabled in block settings on the page, that are sent to other block functions as an associative array
				(parameter_name => its value) in the <b>$block_config</b> parameter. Here is an example of how this
				function works with all parameter types:
				<span class="code">
					function my_blockMetaData()<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;return array(<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array("name"=>"integer_parameter", &nbsp;&nbsp;&nbsp;&nbsp;      "type"=>"INT", &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                "is_required"=>1, "default_value"=>"10"),<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array("name"=>"string_parameter", &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "type"=>"STRING", &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                               "is_required"=>0, "default_value"=>""),<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array("name"=>"checkbox_parameter", &nbsp;&nbsp;&nbsp;           "type"=>"", &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; "is_required"=>0),<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array("name"=>"integer_list_parameter",                          "type"=>"INT_LIST", &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                         "is_required"=>0, "default_value"=>""),<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array("name"=>"combobox_parameter",&nbsp;&nbsp;&nbsp;&nbsp;      "type"=>"CHOICE[1,2,3]", &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;                                                                  "is_required"=>0, "default_value"=>"1"),<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;array("name"=>"sorting_parameter", &nbsp;&nbsp;&nbsp;&nbsp;      "type"=>"SORTING[field1,field2]",                                                                                                          "is_required"=>1, "default_value"=>"field1"),<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;);<br/>
					}<br/>
				</span>
			</li>
			<li>
				<span class="term">Javascript function (%block_id%Javascript, optional)</span>: lets you specify which
				JavaScript file is needed to power this or that block. The function receives 1 parameter called
				<b>$block_config</b>, an associative array with all enabled block configuration parameters on this page
				and their values (keys are parameter names). The function returns the name of the JavaScript file
				relative to the /js/ directory in your site's root folder, i.e. just the filename without the path. If
				the function is not used, the engine supposes the block does not need any JavaScript. Here is an
				example of how this function works:
				<span class="code">
					function my_blockJavascript()<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;return "MyBlockJavaScript.js";<br/>
					}<br/>
				</span>
			</li>
			<li>
				<span class="term">Async function (%block_id%Async, optional)</span>: used to process asynchronous page
				requests (requests made with the ?mode=async parameter). The function receives 1 parameter called
				<b>$block_config</b>, an associative array with all enabled block configuration parameters on this page
				and their values (keys are parameter names). When receiving an asynchronous request, the engine does
				not call the Show function of page blocks, but calls Async function for blocks where such are defined
				instead. Any block can process a request the Async function receives. After that, it outputs data and
				completes the task:
				<span class="code">
					function my_blockAsync($block_config)<br/>
					{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;if ($_REQUEST['action']=='my_async_action')<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;{<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;here, the processing logic is executed<br/>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;echo "processed";die; <span class="comment">// completing page processing, sending output result into stream</span><br/>
					&nbsp;&nbsp;&nbsp;&nbsp;}<br/>
					}<br/>
				</span>
			</li>
			<li>
				<span class="term">PreProcess function (%block_id%PreProcess, optional)</span>: used in cases when a
				certain code in the block needs to be executed regardless of whether the block is cached or not. Unlike
				the Show function that is not called during any request of the page with this block (the frequency of
				Show function requests depends on the block caching strategy), the PreProcess function is always
				called. Hence, the PreProcess function needs to contain only code that is executed fast enough and does
				not require any database connections. The main usage of this function is tracking of the statistics.
				For instance, the PreProcess function of the <b>video_view</b> block logs each its request in the
				stats; later, this data is accumulated as video viewing statistics. The function receives 2 parameters:
				<b>$block_config</b>, an associative array with all the enabled block configuration parameters on this
				page and their values (keys are parameter names), and <b>$object_id</b>, unique block ID on the page.
			</li>
		</ul>
		<p>
			Block localization file needs to contain basic descriptions as well as descriptions of all the
			configuration parameters along with value names for parameters like CHOICE and SORTING. These are set by
			the MetaData function. See an example of a typical localization file below:
		</p>
		<p class="code">
			$lang['%block_id%']['block_short_desc'] = "Brief block description";<br/>
			$lang['%block_id%']['block_desc'] = "Full block description";<br/>
			$lang['%block_id%']['params']['integer_parameter'] = "Description of integer_parameter";<br/>
			$lang['%block_id%']['params']['string_parameter'] = "Description of string_parameter";<br/>
			$lang['%block_id%']['params']['checkbox_parameter'] = "Description of checkbox_parameter";<br/>
			$lang['%block_id%']['params']['integer_list_parameter'] = "Description of integer_list_parameter";<br/>
			$lang['%block_id%']['params']['combobox_parameter'] = "Description of combobox_parameter";<br/>
			$lang['%block_id%']['params']['sorting_parameter'] = "Description of sorting_parameter";<br/>
			$lang['%block_id%']['values']['combobox_parameter']['1'] = "Name of option 1 of combobox_parameter";<br/>
			$lang['%block_id%']['values']['combobox_parameter']['2'] = "Name of option 2 of combobox_parameter";<br/>
			$lang['%block_id%']['values']['combobox_parameter']['3'] = "Name of option 3 of combobox_parameter";<br/>
			$lang['%block_id%']['values']['sorting_parameter']['field1'] = "Name of option field1 of sorting_parameter";<br/>
			$lang['%block_id%']['values']['sorting_parameter']['field2'] = "Name of option field2 of sorting_parameter";<br/>
			$lang['%block_id%']['values']['sorting_parameter']['rand()'] = "Name of option rand() of sorting_parameter";<br/>
		</p>
	</div>
	<!-- ch_website_ui_other_engine_custom_blocks(end) -->
	<!-- ch_website_ui_other_debugger(start) -->
	<div>
		<h3 id="section_website_ui_other_debugger">Debugging Site Pages</h3>
		<p>
			The KVS page debugging tool makes building and customizing your site much easier. It lets you make minor
			fixes to existing templates as well as create new pages without studying the manuals in too much detail.
			You can launch the debugger for any site page. The debugger shows details and data related to the page you
			see on your screen at any given moment.
		</p>
		<p class="important">
			<b>Important!</b> You can only access the page debugger when logged into the administration panel.
		</p>
		<p>
			To launch the debugger for the page you are on now, add the <b>debug=true</b> HTTP parameter to the page
			URL. After that, KVS will display all debugging information related to the current page:
		</p>
		<p class="code">
			http://domain.com/videos/123/video/?debug=true
		</p>
		<p>
			The debugger displays the following types of page data:
		</p>
		<ul>
			<li>
				<span class="term">Page ID</span>: ID of current page.
			</li>
			<li>
				<span class="term">Page name</span>: current page name and link to its editing page.
			</li>
			<li>
				<span class="term">Is XML</span>: whether current page sends XML data.
			</li>
			<li>
				<span class="term">Locale</span>: if the current page is shown in non-standard locale, the locale is
				shown here.
			</li>
			<li>
				<span class="term">Page components used in page template</span>: list of page components used in
				current page template, and links to pages where these components are edited.
			</li>
			<li>
				<span class="term">Request URI</span>: part of request URL after the domain name (the part used in
				mod_rewrite rules).
			</li>
			<li>
				<span class="term">HTTP parameters</span>: list of HTTP parameters sent to the page (they can be sent
				either through the request URI or substituted from mod_rewrite rules).
			</li>
			<li>
				<span class="term">Session values</span>: user session variables, if you are logged in the site member
				area.
			</li>
			<li>
				<span class="term">Dynamic HTTP parameters</span>: dynamic parameter values.
			</li>
		</ul>
		<p>
			For each block included in the page, the debugger shows the following details:
		</p>
		<ul>
			<li>
				<span class="term">Block name</span>: block name on current page and link to its editing page.
			</li>
			<li>
				<span class="term">Block type</span>: block type and link to detailed block type description.
			</li>
			<li>
				<span class="term">Storage key</span>: key of <b>$storage</b> global variable for the specified block
				on current page.
			</li>
			<li>
				<span class="term">Block configuration parameters</span>: here, all enabled parameters of block
				configuration are listed, together with their values. For <b>var</b> parameters (parameters linked to
				HTTP parameters of the request), you can see the corresponding values of HTTP request parameters in the
				brackets (shown in case match is found).
			</li>
			<li>
				<span class="term">Page components used in block template</span>: list of page components used in the
				template of the specified block on current page.
			</li>
			<li>
				<span class="term">Block data in storage </span>: all data stored in the <b>$storage</b> of the block,
				available to be used in the template of current site page. Data from <b>$storage</b> can be used only
				after the location where the block was inserted into the template.
			</li>
			<li>
				<span class="term">Block template variables</span>: all variables that can be used in the template of
				the specified block on current page, together with their current values.
			</li>
		</ul>
		<p>
			Example of using a variable:
		</p>
		<p class="code">
			<span class="comment">// displaying video ID from the storage of the video_view block in site page template</span><br/>
			{{$smarty.ldelim}}$storage.video_view_video_view.video_id{{$smarty.rdelim}}<br/><br/>

			<span class="comment">// displaying video ID in video_view block template</span><br/>
			{{$smarty.ldelim}}$data.video_id{{$smarty.rdelim}}
		</p>
		<p>
			To iterate through an array valuable, use the {{$smarty.ldelim}}foreach{{$smarty.rdelim}} pattern:
		</p>
		<p class="code">
			{{$smarty.ldelim}}foreach name=data item=<b>item</b> from=$storage.video_view_video_view.tags{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">{{$smarty.ldelim}}* Requesting an array element is carried out using the $item variable *{{$smarty.rdelim}}</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}<b>$item</b>.tag_id{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}<b>$item</b>.tag{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		</p>
		<p>
			Pay attention: if you already have a {{$smarty.ldelim}}foreach{{$smarty.rdelim}} cycle, e.g. through a list of videos, and you want to add
			another cycle within the first one (e.g. display categories for each video in the list), you will need to
			use different names for the item and name variables:
		</p>
		<p class="code">
			{{$smarty.ldelim}}foreach name=<b>data</b> item=<b>item</b> from=<b>$data</b>{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">{{$smarty.ldelim}}* Main array element is requested using the $item variable *{{$smarty.rdelim}}</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;Title: {{$smarty.ldelim}}<b>$item</b>.title{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;Categories:<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">{{$smarty.ldelim}}* Internal cycle through categories; categories are listed in the $item.categories variable *{{$smarty.rdelim}}</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}foreach name=<b>data_inner</b> item=<b>item_inner</b> from=<b>$item.categories</b>{{$smarty.rdelim}}<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">{{$smarty.ldelim}}* Internal array element is requested using the $item_inner element *{{$smarty.rdelim}}</span><br/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}<b>$item_inner.title</b>{{$smarty.rdelim}},<br/>
			&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
			{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		</p>
	</div>
	<!-- ch_website_ui_other_debugger(end) -->
</div>