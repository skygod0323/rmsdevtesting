<div id="documentation">
	<h1 id="section_website_ui">Website creation tutorial</h1>
	<h2 id="section_website_ui_contents">Contents</h2>
	<div class="contents">
		<a href="#section_preface" class="l2">1. Preface</a><br/>
		<a href="#section_models_list" class="l2">2. Models list page</a><br/>
		<a href="#section_models_list1" class="l3">2.1. Creating a new page for models list</a><br/>
		<a href="#section_models_list2" class="l3">2.2. Configuring models list page</a><br/>
		<a href="#section_models_list3" class="l3">2.3. Configuring pagination for models</a><br/>
		<a href="#section_models_view" class="l2">3. Model details page</a><br/>
		<a href="#section_models_view1" class="l3">3.1. Creating a new page for model details</a><br/>
		<a href="#section_models_view2" class="l3">3.2. Adding model's videos to model details page</a><br/>
		<a href="#section_models_other" class="l2">4. Other pages</a><br/>
		<a href="#section_models_other1" class="l3">4.1. Adding video models to video details page</a><br/>
		<a href="#section_models_other2" class="l3">4.2. Adding models list to index page</a><br/>
	</div>
	<h2 id="section_preface">1. Preface</h2>
	<p>
		Using KVS website builder seems difficult from the first glance, but this task will be much
		more easier for you after a couple of tries. We would like to provide you with a small tutorial, which explains
		almost all aspects of website pages creation and their content / behavior configuration.
	</p>
	<p>
		Lets talk about displaying models on our website. We would like to see the list of models with alphabetic
		filtering, page with model details and model videos. We would also like to display the list of models for
		every video (if a video has any assigned models).
	</p>
	<p>
		So the following should be done:
	</p>
	<ul>
		<li>
			We need to create a new page for model list.
		</li>
		<li>
			We need to create a new page for model details and model videos.
		</li>
		<li>
			We need to display models for every video on video details page.
		</li>
		<li>
			And finally, we would like to display 5 models with the most videos on index page.
		</li>
	</ul>
	<p>
		Before starting this tutorial please created several models (no more than 10) and assign them to some
		videos (this can be done on video edit page in admin panel).
	</p>
	<p>
		In this tutorial we won't finalize templates with all details, such as CSS-styles, additional links and etc.
		The main purpose of tutorial is to provide you with some experience in KVS website builder,
		all other details are up to you.
	</p>
	<h2 id="section_models_list">2. Models list page</h2>
	<h3 id="section_models_list1">2.1. Creating a new page for models list</h3>
	<p>
		In <span class="term">Website UI</span> section of admin panel open <span class="term">Add page</span> link.
		You will see website page creation form.
	</p>
	<p class="important">
		<b>Important:</b> in order to create a new website page in admin panel PHP should have enough permissions to
		create files in some folders. One of these folders is domain root folder where PHP script will be created for
		every page. If there are no enough permissions, you will see validation error on save.
	</p>
	<p>
		When creating a new website page you need to fill in a couple of required fields:
	</p>
	<ul>
		<li>
			<span class="term">Display name</span> - page name for displaying in admin panel. Enter <b>Models List</b>
			in this field.
		</li>
		<li>
			<span class="term">External ID</span> - system field that uniqely identifies this page and is used for file
			names and folder names generation. Enter <b>models_list</b> in this field. The page will be accessible
			using <b>http://your_domain.com/models_list.php</b> URL then.
		</li>
		<li>
			<span class="term">Template code</span> - place where Smarty template (or static HTML code) is defined for
			rendering this page. Specify only <b>Test</b> here.
		</li>
	</ul>
	<p>
		If you don't get any validation errors, then the new page is created and accessible. Enter
		<b>http://your_domain.com/models_list.php</b> in your address bar and make sure you see <b>Test</b> you
		entered in page template.
	</p>
	<p>
		In most cases you will need your models page to be accessible from the other URL (for example,
		<b>http://your_domain.com/models/</b>), which is better for search engines. In order to do that, you need to
		add a rule in root .htaccess file to redirect such requests to <b>models_list.php</b> script:
	</p>
	<p class="code">
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		After adding this rule you will see the same page using <b>http://your_domain.com/models/</b> URL.
	</p>
	<h3 id="section_models_list2">2.2. Configuring models list page</h3>
	<p>
		Lets configure some page content for models list page. Find the created page with <b>Models List</b> name in
		<span class="term">Pages list</span> of <span class="term">Website UI</span> section and open its editor. We
		need to provide layout for this page, insert models list block (<b>list_models</b>) and also configure its
		behavior.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_01.png" alt="Models List page in Website UI pages list" width="600" height="136"/><br/>
		<span>Models List page in Website UI pages list.</span>
	</p>
	<p>
		Page layout is usually configured in header and footer components. These components have common design for
		almost all pages, so in KVS they are created as separate templates (page components). Page
		header is rendered by <b>header_general.tpl</b> component and page footer is displayed by
		<b>footer_general.tpl</b> one. Header component is configured in such a way, so that it is possible to specify
		HTML title, description and keywords before including this component into a page template. Based on this,
		models list page template may look like this:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Set values for page_title, page_description and page_keywords variables which will be
			displayed in header_general component *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="Models"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="Displays all models you can see at MySite.com"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include header_general page component, which renders page header with the values we
			specified above *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Wide column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include footer_general page component, which renders page footer *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		After saving this template (you can use <span class="term">Update content</span> button to save) open
		<b>http://your_domain.com/models/</b> page. The page contains main layout with empty central and side blocks.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_02.png" alt="Models List page on website" width="999" height="289"/><br/>
		<span>Models List page on website.</span>
	</p>
	<p>
		Please take a look into page HTML source code and make sure that header contains data we specified in page
		template:
	</p>
	<p class="code">
		&lt;title&gt;Models / Kernel Tube&lt;/title&gt;<br/>
		&lt;meta name="description" content="Displays all models you can see at MySite.com"/&gt;<br/>
		&lt;meta name="keywords" content="models, blablabla, blablabla"/&gt;
	</p>
	<p>
		Now we only need to insert models list block and the page is complete! In order to do that, add block insert
		command to page template in the place you want it to be displayed (the new line is bolded):
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Set values for page_title, page_description and page_keywords variables which will be
			displayed in header_general component *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="Models"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="Displays all models you can see at MySite.com"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include header_general page component, which renders page header with the values we
			specified above *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Insert list_models block to display the list of models *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}insert name="getBlock" block_id="list_models" block_name="List Models"{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include footer_general page component, which renders page footer *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		After saving the template you can notice that under template textarea in
		<span class="term">Page content and caching strategy</span> table some information appeared about
		<b>List Models</b> block with default configuration parameters. When a new block is inserted into page
		template, this block will always be created with default template and configuration parameters, but you can
		change both template and configuration parameters of the block at any time.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_03.png" alt="List Models block in page content table" width="829" height="136"/><br/>
		<span>List Models block in page content table.</span>
	</p>
	<p>
		Open <b>http://your_domain.com/models/</b> URL. If you had any models defined initially, the page will now
		display all of them.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_04.png" alt="Models List page on website" width="982" height="612"/><br/>
		<span>Models List page on website.</span>
	</p>
	<p>
		Now you can configure layout and styles for models list block on this page. In order to do that, you need to
		open <b>List Models</b> block editor. There are 2 ways you can access block editor in admin panel: use a link
		with this block name from <span class="term">Pages list</span> of <span class="term">Website UI</span> section,
		or click the similar link from page editor, where this block is inserted.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_05.png" alt="Block editor link on pages list" width="847" height="146"/><br/>
		<span>Block editor link on pages list.</span>
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_06.png" alt="Block editor link on page editor" width="746" height="138"/><br/>
		<span>Block editor link on page editor.</span>
	</p>
	<p>
		Once you open <b>List Models</b> block editor you will see template code that renders models list on the
		current page. On the bottom side you will find all configuration parameters supported by this block and their
		values for this block on the current page. By using all these parameters you can customize block behavior, for
		example how the models list is divided into pages, whether models without 1st and (or) 2nd screenshots will be
		displayed or not, whether models without any videos assigned will be displayed or not and etc. For every
		configuration parameter there is a short description about its purpose.
	</p>
	<p>
		As we need to filter models list by alphabet letters, lets enable <b>var_title_section</b> parameter, which
		tells block where in HTTP parameter alphabet letter value is passed (this parameter has <b>section</b> value by
		default, lets keep it).
	</p>
	<p>
		Open <b>http://your_domain.com/models_list.php?section=a</b> URL after saving block parameters. If you have
		any models starting with A letter, they will be displayed in the list (no other models will be displayed). To
		make the link se-friendly you need to add additional rule into root .htaccess file (please note that the new
		rule should be added before the existing one):
	</p>
	<p class="code">
		<b>RewriteRule ^models/([a-zA-Z])/(.*)$ /models_list.php?section=$1 [L,QSA]</b><br/>
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		Now you can use <b>http://your_domain.com/models/a/</b> link to access list of models for A letter.
	</p>
	<h3 id="section_models_list3">2.3. Configuring pagination for models</h3>
	<p>
		If you have a lot of models and there is no sense to show all of them on the same page, you would probably
		need to split the whole models list into multiple pages (enable pagination). Pagination works in the same
		way for all list blocks. Every list block supports 3 configuration parameters, which should be enabled:
	</p>
	<ul>
		<li>
			<b>items_per_page</b> - the number of elements per page (for texting purposes specify the smaller number,
			so that you have at least 3 different pages, e.g. <b>4</b>).
		</li>
		<li>
			<b>links_per_page</b> - the maximum number of page links that are displayed at a time
			(leave this by default, e.g. <b>10</b>).
		</li>
		<li>
			<b>var_from</b> - tells block where in HTTP parameter pagination start element value is passed
			(enable and leave by default, e.g. <b>from</b>).
		</li>
	</ul>
	<p>
		After the block data is saved, you will see only 4 models on models list page, but links to other pages will
		not appear. The problem is that templates of all list blocks do not display page links by default. In
		order to display them you need to include the common pagination component in <b>List Models</b> block
		template in the place where you want to display the page links (make sure that you include it in block
		template, not page template!):
	</p>
	<p class="code">
		{{$smarty.ldelim}}include file="pagination_block_common.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		As pagination works in se-friendly mode, you will also need to add additional rules into root .htaccess
		file, so that page links work both for global models list and for alphabetically filtered sub-lists
		(please consider the correct order of the rules!):
	</p>
	<p class="code">
		<b>RewriteRule ^models/([a-zA-Z])/([0-9]+)/$ /models_list.php?section=$1&amp;from=$2 [L,QSA]</b><br/>
		RewriteRule ^models/([a-zA-Z])/(.*)$ /models_list.php?section=$1 [L,QSA]<br/>
		<b>RewriteRule ^models/([0-9]+)/$ /models_list.php?from=$1 [L,QSA]</b><br/>
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		Now the pagination works fine!
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_07.png" alt="Models List page with pagination" width="766" height="243"/><br/>
		<span>Models List page with pagination.</span>
	</p>
	<h2 id="section_models_view">3. Model details page</h2>
	<h3 id="section_models_view1">3.1. Creating a new page for model details</h3>
	<p>
		Let's create a new page <b>View Model</b> with <b>view_model</b> external ID in the same way, as described in previous
		sections. Initially we will use the following template:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Set values for page_title, page_description and page_keywords variables which will be
			displayed in header_general component *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="Model Display Page"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="Displays model data at MySite.com"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include header_general page component, which renders page header with the values we
			specified above *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Insert model_view block to display information about model *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="model_view" block_name="Model View"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include footer_general page component, which renders page footer *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		After saving this page and opening it from the website (<b>http://your_domain.com/view_model.php</b>) we can
		see that <b>model_view</b> block displays the link as being broken. The issue is that <b>model_view</b>
		block needs to know, which model information should be displayed. In order to tell this, there are two block
		parameters available, which allow passing either model ID or model directory (for se-friendly URLs) to block
		logic. By default <b>var_model_dir = dir</b> parameter is enabled and it tells <b>model_view</b> block that
		model directory value will be passed in <b>dir</b> HTTP parameter. To finalize this we will need to add one
		more rule into root .htaccess file (please consider the correct rules order!):
	</p>
	<p class="code">
		RewriteRule ^models/([a-zA-Z])/([0-9]+)/$ /models_list.php?section=$1&amp;from=$2 [L,QSA]<br/>
		RewriteRule ^models/([a-zA-Z])/(.*)$ /models_list.php?section=$1 [L,QSA]<br/>
		RewriteRule ^models/([0-9]+)/$ /models_list.php?from=$1 [L,QSA]<br/>
		<b>RewriteRule ^models/(.*)/$ /view_model.php?dir=$1 [L,QSA]</b><br/>
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		After we do that, all URLs looking like <b>http://your_domain.com/models/angelina-jolie/</b> will open
		model details page. The part of URL - <b>angelina-jolie</b> - is model directory value, which is used
		for building se-friendly URLs. Directory field exists in almost every object, e.g. videos, albums,
		categories, tags, models, content sources and other. This field is generated based on object title, but
		you can also manually specify / change it. If models list block, created in previous sections builds
		ID-based URLs, you need to change its template, so that model directory (<b>dir</b>) is used instead of
		model ID (<b>model_id</b>) in HREF parameters.
	</p>
	<p>
		You may also notice that HTML title and description of model details page displays the same data for all
		models (this data is specified in <b>page_title</b> and <b>page_description</b> variables in page template).
		In order to display model name and description in HTML header, we need to insert <b>model_view</b> block
		before we include header template, because this block quieries DB and model data will be available only after
		doing that. Internal data of any block may be accessed in page template using block storage. The storage of
		a particular block will be accessible only after the place, where this block is inserted in page template.
		To access block storage you can use unique key, which is displayed in admin panel for every block used on
		the particular page:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_08.png" alt="Block storage key on the current page" width="941" height="102"/><br/>
		<span>Block storage key on the current page.</span>
	</p>
	<p>
		Let's rewrite the page template. We will insert model details block at the very beginning and assign
		page title and description with the data from this block's storage:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Insert model_view block to display information about model *{{$smarty.rdelim}}<br/>
		</span>
		<span class="comment">
			{{$smarty.ldelim}}* Pay attention to the new parameter <b>assign</b>, it should be used when you don't want this block
			to be rendered at the current place; the result of block rendering will be put into this variable instead *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}insert name="getBlock" block_id="model_view" block_name="Model View" <b>assign="model_view_result"</b>{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Set values for page_title, page_description and page_keywords variables which will be
			displayed in header_general component, take the needed values from block storage *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="<b>`$storage.model_view_model_view.title`</b>"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="<b>`$storage.model_view_model_view.description`</b>"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include header_general page component, which renders page header with the values we
			specified above *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Display the result of model_view block, which
			was put under model_view_result variable by the block insert command *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}$model_view_result|smarty:nodefaults{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include footer_general page component, which renders page footer *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		After you save this template, you can check that page layout is not changed, but HTML header displays model
		data for title and description. So, if you need to use some block data in page template, you can move its
		insertion on template top, add <b>assign</b> parameter to insert command and use storage key of this block
		to access its data. The usage of special quotes (`) when assigning page title and description variables is
		required by Smarty engine for such cases.
	</p>
	<h3 id="section_models_view2">3.2. Adding model's videos to model details page</h3>
	<p>
		Lets finalize the page and add model's videos to it. The list of videos can be displayed using
		<b>list_videos</b> block, so we need to insert this block into page template:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Insert model_view block to display information about model *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}insert name="getBlock" block_id="model_view" block_name="Model View" assign="model_view_result"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Set values for page_title, page_description and page_keywords variables which will be
			displayed in header_general component, take the needed values from block storage *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="`$storage.model_view_model_view.title`"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="`$storage.model_view_model_view.description`"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include header_general page component, which renders page header with the values we
			specified above *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Display the result of model_view block, which
			was put under model_view_result variable by the block insert command *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}$model_view_result|smarty:nodefaults{{$smarty.rdelim}}<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Insert list_videos block to display videos of this model *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Model Videos"{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include footer_general page component, which renders page footer *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		After saving page template we will see a list of videos right after the model information. By default this
		list will display all videos, which is not what we wanted, so in order to show only videos of the current model
		we need to enable model filtering in <b>list_videos</b> block. This filtering can be enabled using one of
		two <b>list_videos</b> block parameters:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_09.png" alt="Enabling model filtering in list_videos block" width="946" height="106"/><br/>
		<span>Enabling model filtering in list_videos block.</span>
	</p>
	<p>
		As model directory is already passed to this page (in <b>dir</b> HTTP parameter for <b>model_view</b> block),
		it will make sense to reuse the same parameter for <b>list_videos</b> block as well (because it contains value
		that we need for filtering videos list). Just enable <b>var_model_dir</b> block parameter and set its value to
		<b>dir</b>. After doing that, both <b>model_view</b> and <b>list_videos</b> blocks on this page will work based
		on model directory value passed in <b>dir</b> HTTP parameter. Save block configuration and refresh model
		details page on website. Now you can see that only model's videos are displayed in list.
	</p>
	<p>
		There is one more small aspect for commonly-used list type blocks (for example <b>list_videos</b>). As the
		block design is the same in most cases, we moved all rendering template code in 2 page components:
		<b>list_videos_block_common</b> for common videos lists and <b>list_videos_block_internal</b> for internal
		videos lists, such as my favourite videos and my uploaded videos. When you insert a new <b>list_videos</b>
		block somewhere on a website page, this block's template will be set as default one for <b>list_videos</b>
		block, which doesn't reuse the mentioned above page components. So in most cases, when you want your new
		<b>list_videos</b> block to be displayed in the same way other videos blocks do, you need to replace its
		template (created by default) with including either <b>list_videos_block_common</b>, or
		<b>list_videos_block_internal</b> page component:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Set list_videos_title value, which will be displayed in list_videos_block_common component *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var="list_videos_title" value="Model videos"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Include list_videos_block_common page component which renders videos list with the value we specified above *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="list_videos_block_common.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		Frankly speaking, it is not obligatory to reuse page components from block templates in such cases. Nothing
		wrong will happen if you leave default block template or change it directly with the changes you need. From
		the other hand, if you decide to change the layout or styles of all videos lists on your website, you will
		have to repeat all changes in all <b>list_videos</b> blocks where common page component is not reused. But if
		you reuse page component in all videos lists, it will be enough to make your changes in one place (e.g. in
		page component's template).
	</p>
	<p>
		Don't worry if you are confused with page components usage inside block templates. Just look into the similar
		block templates on existing pages and try doing the same way.
	</p>
	<h2 id="section_models_other">4. Other pages</h2>
	<h3 id="section_models_other1">4.1. Adding video models to video details page</h3>
	<p>
		In order to add video models to video details page we should modify <b>video_view</b> block template, which
		is located on this page and displays all information about video:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_10.png" alt="Video details block in pages list section of admin panel" width="810" height="128"/><br/>
		<span>Video details block in pages list section of admin panel.</span>
	</p>
	<p>
		The template of this block already contains rendering loops for categories and tags, lets add very similar
		loop for video models (bolded):
	</p>
	<p class="code">
		&lt;div class="info_row"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;Categories:<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}foreach name=data item=item from=$data.categories{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="/categories/{{$smarty.ldelim}}$item.dir{{$smarty.rdelim}}/"&gt;{{$smarty.ldelim}}$item.title{{$smarty.rdelim}}&lt;/a&gt;{{$smarty.ldelim}}if !$smarty.foreach.data.last{{$smarty.rdelim}}, {{$smarty.ldelim}}/if{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		&lt;/div&gt;<br/>
		<b>
		{{$smarty.ldelim}}if @count($data.models)>0{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="info_row"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Models:<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}foreach name=data item=item from=$data.models{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="/models/{{$smarty.ldelim}}$item.dir{{$smarty.rdelim}}/"&gt;{{$smarty.ldelim}}$item.title{{$smarty.rdelim}}&lt;/a&gt;{{$smarty.ldelim}}if !$smarty.foreach.data.last{{$smarty.rdelim}}, {{$smarty.ldelim}}/if{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		{{$smarty.ldelim}}/if{{$smarty.rdelim}}
		</b>
	</p>
	<p>
		In this case <b>{{$smarty.ldelim}}if @count($data.models)>0{{$smarty.rdelim}}</b> condition is used to skip
		models label display if a video doesn't have any models assigned. Now video details block looks like this:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_11.png" alt="Video details block with list of models" width="474" height="210"/><br/>
		<span>Video details block with list of models.</span>
	</p>
	<h3 id="section_models_other2">4.2. Adding models list to index page</h3>
	<p>
		In this last section we will provide an example of how a new functionality can be added to an existing page.
		On index page we will render top 5 models (sorting by assigned videos count). First we need to find the page
		and open its template for editing:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_12.png" alt="Index page in pages list section of admin panel" width="821" height="128"/><br/>
		<span>Index page in pages list section of admin panel.</span>
	</p>
	<p>
		Next we will insert <b>list_models</b> block in the place we want to see our top 5 models list:
	</p>
	<p class="code">
		{{$smarty.ldelim}}assign var=page_title value="Demo Tube Website"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Videos Watched Right Now"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Most Recent Videos"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="pagination" block_name="Most Recent Videos Pagination"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}include file="search_videos_block.tpl"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}insert name="getBlock" block_id="list_models" block_name="Top 5 Models"{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="tags_cloud" block_name="Tags"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}include file="side_advertising.tpl"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		After saving page template we will find a new block <b>Top 5 Models</b> appears in page content table. We need
		to open its editor and configure the following two block parameters:
	</p>
	<ul>
		<li>
			<b>items_per_page</b> - set it to <b>5</b>. This will configure block select only 5 records from database.
		</li>
		<li>
			<b>sort_by</b> - in the list of available sorting options we need to select <b>Total videos desc</b>.
		</li>
	</ul>
	<p>
		After saving block configuration we will see the expected result on website index page:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_13.png" alt="Top 5 models on website index page" width="500" height="325"/><br/>
		<span>Top 5 models on website index page.</span>
	</p>
</div>