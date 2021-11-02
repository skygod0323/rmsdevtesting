<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['grabbers']['title']       = "Grabbers";
$lang['plugins']['grabbers']['description'] = "Adds ability to grab content from 3rd-party sites.";
$lang['plugins']['grabbers']['long_desc']   = "
		Use this plugin to import content from 3rd-party sites, which have grabbers provided by KVS. First you need to
		install grabbers from KVS repository and configure them. Then you can import content from URLs of these sites
		using standard KVS import functionality (by providing Video page URL field) or using the simplified import
		module inside this plugin. Most grabbers support not only importing from single content pages, but also
		importing all available content from list pages. You can enable autopilot function for each grabber to monitor
		list pages for new content, which will then be imported automatically.
		[kt|br][kt|br]
		Please note that source site may block your server IP if there is IP-based protection enabled. For each grabber
		you can specify timeout (5 seconds by default) so that your server does not produce too many requests. Also it
		is possible to configure list of proxies that will be randomly used by grabbers.
		[kt|br][kt|br]
		If you would like to propose more source sites to be added to KVS grabbers repository, please contact KVS
		support.
";
$lang['permissions']['plugins|grabbers']    = $lang['plugins']['grabbers']['title'];

$lang['plugins']['grabbers']['upload']                                  = "Upload content";
$lang['plugins']['grabbers']['divider_grabber_settings']                = "%1% grabber settings";
$lang['plugins']['grabbers']['divider_grabber_settings_default']        = "[kt|b]Attention![/kt|b] This grabber is designed to parse any pages, but its functionality is limited. It can only parse HTML title / description / keywords tags and locate source video / image files. You should always use site-specific grabbers, which will give you more data and options. If there is no site-specific grabber for the site you want to parse, please check with our support if we can add one.";
$lang['plugins']['grabbers']['divider_grabber_log']                     = "Grabber log";
$lang['plugins']['grabbers']['divider_filters']                         = "Filters";
$lang['plugins']['grabbers']['divider_autopilot']                       = "Autopilot";
$lang['plugins']['grabbers']['divider_upload']                          = "Upload content";
$lang['plugins']['grabbers']['divider_upload_options']                  = "Content options";
$lang['plugins']['grabbers']['divider_upload_confirm']                  = "Upload content confirmation";
$lang['plugins']['grabbers']['divider_install']                         = "Install grabbers";
$lang['plugins']['grabbers']['divider_grabbers']                        = "Active grabbers";
$lang['plugins']['grabbers']['divider_grabbers_videos']                 = "Videos";
$lang['plugins']['grabbers']['divider_grabbers_albums']                 = "Albums";
$lang['plugins']['grabbers']['divider_grabbers_models']                 = "Models";
$lang['plugins']['grabbers']['divider_grabbers_none']                   = "Please select which grabbers you want to install and configure them.";
$lang['plugins']['grabbers']['field_upload']                            = "Upload content using grabbers manually";
$lang['plugins']['grabbers']['field_upload_hint']                       = "this functionality allows adding initial content manually, and then you can configure autopilot function in individual grabbers to add new content automatically";
$lang['plugins']['grabbers']['field_upload_type']                       = "Upload type";
$lang['plugins']['grabbers']['field_upload_type_videos']                = "Videos";
$lang['plugins']['grabbers']['field_upload_type_albums']                = "Albums";
$lang['plugins']['grabbers']['field_upload_type_models']                = "Models";
$lang['plugins']['grabbers']['field_upload_list']                       = "URL list";
$lang['plugins']['grabbers']['field_upload_list_hint']                  = "Specify list of content URLs or content list URLs to parse by grabbers. Each URL should be specified on a new line in the following format: simply [kt|b]URL[/kt|b] or [kt|b]URL|number[/kt|b], where [kt|b]number[/kt|b] is a number of content to grab from the given list URL, scrolling list to next pages if possible. If the number is omitted or if [kt|b]0[/kt|b] is specified, grabber will only add content from the given URL and will not consider next pages. [kt|br] [kt|b]http://domain.com/top.html|1000[/kt|b] - tells grabber to add 1000 videos / albums from http://domain.com/top.html list page, using the pagination controls if needed; [kt|br] [kt|b]http://domain.com/top.html[/kt|b] - tells grabber to add N videos / albums that are displayed directly on http://domain.com/top.html list page, not using the pagination controls; [kt|br] [kt|b]http://domain.com/video-page.html[/kt|b] - tells grabber to add only 1 video displayed on http://domain.com/video-page.html (if this page is an individual video page).";
$lang['plugins']['grabbers']['field_upload_list_hint_autopilot']        = "Specify list of content list URLs to parse by this grabber. Each URL should be specified on a new line in the following format: simply [kt|b]URL[/kt|b] or [kt|b]URL|number[/kt|b], where [kt|b]number[/kt|b] is a number of content to grab from the given list URL, scrolling list to next pages if possible. If the number is omitted or if [kt|b]0[/kt|b] is specified, grabber will only add content from the given URL and will not consider next pages. [kt|br] [kt|b]http://domain.com/top.html|1000[/kt|b] - tells grabber to add 1000 videos / albums from http://domain.com/top.html list page, using the pagination controls if needed; [kt|br] [kt|b]http://domain.com/top.html[/kt|b] - tells grabber to add N videos / albums that are displayed directly on http://domain.com/top.html list page, not using the pagination controls.";
$lang['plugins']['grabbers']['field_kvs_repository']                    = "KVS repository";
$lang['plugins']['grabbers']['field_kvs_repository_empty']              = "No grabbers selected.";
$lang['plugins']['grabbers']['field_kvs_repository_all']                = "All grabbers...";
$lang['plugins']['grabbers']['field_kvs_repository_hint']               = "install grabbers from KVS repository";
$lang['plugins']['grabbers']['field_custom_grabber']                    = "Custom grabber";
$lang['plugins']['grabbers']['field_custom_grabber_hint']               = "upload custom grabber PHP file";
$lang['plugins']['grabbers']['field_delete']                            = "Delete";
$lang['plugins']['grabbers']['field_name']                              = "Grabber";
$lang['plugins']['grabbers']['field_name_missing_grabber']              = "No grabber found";
$lang['plugins']['grabbers']['field_name_error_grabber']                = "Grabber error";
$lang['plugins']['grabbers']['field_name_duplicates']                   = "Duplicates";
$lang['plugins']['grabbers']['field_version']                           = "Version";
$lang['plugins']['grabbers']['field_ydl_binary']                        = "Youtube-dl binary";
$lang['plugins']['grabbers']['field_ydl_binary_hint']                   = "Youtube-dl library is used to download videos for many grabbers, you can install it from https://github.com/rg3/youtube-dl [kt|br] In order to increase download speed you can enable multi-threaded download by installing Aria2 utility from https://aria2.github.io and by adding this option after youtube-dl binary path: [kt|br] [kt|b]/usr/local/bin/youtube-dl --external-downloader /usr/bin/aria2c[kt|b]";
$lang['plugins']['grabbers']['field_mode']                              = "Mode";
$lang['plugins']['grabbers']['field_mode_none']                         = "Not selected";
$lang['plugins']['grabbers']['field_mode_download']                     = "Download";
$lang['plugins']['grabbers']['field_mode_embed']                        = "Embed";
$lang['plugins']['grabbers']['field_mode_pseudo']                       = "Pseudo";
$lang['plugins']['grabbers']['field_mode_skip']                         = "Skip";
$lang['plugins']['grabbers']['field_mode_hint']                         = "- when using [kt|b]%1%[/kt|b] option grabber will download files to your server(s); [kt|br] - when using [kt|b]%2%[/kt|b] option grabber will embed content from target site with their player; [kt|br] - when using [kt|b]%3%[/kt|b] option grabber will put links to target site, users who will try to watch content will be redirected to target site. [kt|br]";
$lang['plugins']['grabbers']['field_url_postfix']                       = "URLs postfix";
$lang['plugins']['grabbers']['field_url_postfix_hint']                  = "if source site supports partner traffic you can specify partner ID parameter / value in this field and grabber will add it to all embed codes and pseudo URLs, for example: [kt|b]ref=kernel[/kt|b]";
$lang['plugins']['grabbers']['field_data']                              = "Data";
$lang['plugins']['grabbers']['field_data_none']                         = "Only content";
$lang['plugins']['grabbers']['field_data_title']                        = "Title";
$lang['plugins']['grabbers']['field_data_description']                  = "Description";
$lang['plugins']['grabbers']['field_data_tags']                         = "Tags";
$lang['plugins']['grabbers']['field_data_categories']                   = "Categories";
$lang['plugins']['grabbers']['field_data_models']                       = "Models";
$lang['plugins']['grabbers']['field_data_content_source']               = "Content source";
$lang['plugins']['grabbers']['field_data_channel']                      = "Channel";
$lang['plugins']['grabbers']['field_data_screenshot']                   = "Screenshot";
$lang['plugins']['grabbers']['field_data_rating']                       = "Rating";
$lang['plugins']['grabbers']['field_data_views']                        = "Views";
$lang['plugins']['grabbers']['field_data_date']                         = "Date added";
$lang['plugins']['grabbers']['field_data_custom']                       = "Custom fields";
$lang['plugins']['grabbers']['field_data_age']                          = "Age";
$lang['plugins']['grabbers']['field_data_birth_date']                   = "Birth date";
$lang['plugins']['grabbers']['field_data_gender']                       = "Gender";
$lang['plugins']['grabbers']['field_data_pseudonyms']                   = "Pseudonyms";
$lang['plugins']['grabbers']['field_data_height']                       = "Height";
$lang['plugins']['grabbers']['field_data_weight']                       = "Weight";
$lang['plugins']['grabbers']['field_data_measurements']                 = "Measurements";
$lang['plugins']['grabbers']['field_data_country']                      = "Country";
$lang['plugins']['grabbers']['field_data_city']                         = "City";
$lang['plugins']['grabbers']['field_data_state']                        = "State";
$lang['plugins']['grabbers']['field_data_eye_color']                    = "Eye color";
$lang['plugins']['grabbers']['field_data_hair_color']                   = "Hair color";
$lang['plugins']['grabbers']['field_data_hint']                         = "specify which data should be parsed by grabber";
$lang['plugins']['grabbers']['field_import_categories_as_tags']         = "Categories as tags";
$lang['plugins']['grabbers']['field_import_categories_as_tags_enabled'] = "import categories from this grabber as tags";
$lang['plugins']['grabbers']['field_import_categories_as_tags_hint']    = "By default if you choose to grab categories they will be created in your database in the same way they are defined on source site. This may result in hundreds of similar categories added with different spelling and thus your categorization structure will be poorly defined. It is recommended to keep the number of categories as low as possible, but have much more tags at the same time. If you enable this option, categories from this site will be added as tags into your database. You can also enable [kt|b]Category auto-selection[/kt|b] plugin so that content added by this grabber is auto-categorized based on your existing categories and their synonyms (you should define them manually before using grabbers).";
$lang['plugins']['grabbers']['field_content_source']                    = "Content source";
$lang['plugins']['grabbers']['field_content_source_no_group']           = "* No group *";
$lang['plugins']['grabbers']['field_content_source_hint']               = "choose content source to assign to all content from this grabber";
$lang['plugins']['grabbers']['field_quality']                           = "Quality";
$lang['plugins']['grabbers']['field_quality_none']                      = "Best possible";
$lang['plugins']['grabbers']['field_quality_multiple']                  = "Multiple";
$lang['plugins']['grabbers']['field_quality_hint']                      = "which file quality grabber should download";
$lang['plugins']['grabbers']['field_quality_missing']                   = "if missing";
$lang['plugins']['grabbers']['field_quality_missing_error']             = "Skip this content";
$lang['plugins']['grabbers']['field_quality_missing_lower']             = "Choose worse quality";
$lang['plugins']['grabbers']['field_quality_missing_higher']            = "Choose better quality";
$lang['plugins']['grabbers']['field_download_format']                   = "upload as";
$lang['plugins']['grabbers']['field_download_format_source']            = "Source file (with processing)";
$lang['plugins']['grabbers']['field_download_format_skip']              = "Skip";
$lang['plugins']['grabbers']['field_download_format_format']            = "Format \"%1%\" (without processing)";
$lang['plugins']['grabbers']['field_filters']                           = "Filters";
$lang['plugins']['grabbers']['field_quantity_filter_videos']            = "Duration filter";
$lang['plugins']['grabbers']['field_quantity_filter_albums']            = "Images count filter";
$lang['plugins']['grabbers']['field_quantity_filter_from']              = "from";
$lang['plugins']['grabbers']['field_quantity_filter_to']                = "to";
$lang['plugins']['grabbers']['field_quantity_filter_videos_hint']       = "seconds; content that do not fit into this filter will not be added";
$lang['plugins']['grabbers']['field_quantity_filter_albums_hint']       = "number of images; content that do not fit into this filter will not be added";
$lang['plugins']['grabbers']['field_rating_filter']                     = "Rating filter";
$lang['plugins']['grabbers']['field_rating_filter_from']                = "from";
$lang['plugins']['grabbers']['field_rating_filter_to']                  = "to";
$lang['plugins']['grabbers']['field_rating_filter_hint']                = "percents (0-100); content that do not fit into this filter will not be added";
$lang['plugins']['grabbers']['field_views_filter']                      = "Views filter";
$lang['plugins']['grabbers']['field_views_filter_from']                 = "from";
$lang['plugins']['grabbers']['field_views_filter_to']                   = "to";
$lang['plugins']['grabbers']['field_views_filter_hint']                 = "content that do not fit into this filter will not be added";
$lang['plugins']['grabbers']['field_date_filter']                       = "Date filter";
$lang['plugins']['grabbers']['field_date_filter_from']                  = "from";
$lang['plugins']['grabbers']['field_date_filter_to']                    = "to";
$lang['plugins']['grabbers']['field_date_filter_hint']                  = "number of days; content that do not fit into this filter will not be added";
$lang['plugins']['grabbers']['field_terminology_filter']                = "Terminology filter";
$lang['plugins']['grabbers']['field_terminology_filter_hint']           = "specify comma-separated list of words that you don't want to import; all content that have any of these words in title will be skipped";
$lang['plugins']['grabbers']['field_quality_from_filter']               = "Quality filter";
$lang['plugins']['grabbers']['field_quality_from_filter_hint']          = "choose minimum quality that you want to allow to be added";
$lang['plugins']['grabbers']['field_replacements']                      = "Text replacements";
$lang['plugins']['grabbers']['field_replacements_hint']                 = "in some cases grabbers may parse titles / descriptions that have site name or other static text you don't want to appear, you can configure replacements for such texts to either empty string or your custom text; [kt|br] specify in the following format line by line: [kt|b]original text: replacement[/kt|b]";
$lang['plugins']['grabbers']['field_autodelete']                        = "Autodelete";
$lang['plugins']['grabbers']['field_autodelete_enabled']                = "enable automatic content deletion";
$lang['plugins']['grabbers']['field_autodelete_hint']                   = "grabber will check automatically for deleted content on source site and will delete this content from your site as well";
$lang['plugins']['grabbers']['field_autopilot']                         = "Autopilot";
$lang['plugins']['grabbers']['field_autopilot_enabled']                 = "enable";
$lang['plugins']['grabbers']['field_autopilot_hint']                    = "autopilot will periodically query target site for new content using the given URLs";
$lang['plugins']['grabbers']['field_autopilot_interval']                = "Interval (hours)";
$lang['plugins']['grabbers']['field_autopilot_interval_hint']           = "how often target site should be queried for new content";
$lang['plugins']['grabbers']['field_timeout']                           = "Timeout";
$lang['plugins']['grabbers']['field_timeout_hint']                      = "it is recommended to set decent timeout (5-10 seconds) in order to prevent your server IP blocking (no warranty though); it will slightly slow down grabbing speed";
$lang['plugins']['grabbers']['field_proxies']                           = "Proxies";
$lang['plugins']['grabbers']['field_proxies_hint']                      = "configure list of proxies for this grabber if needed, random proxy will be selected; specify each proxy on the new line in the following format: [kt|b]scheme://user:password@server:port[/kt|b], where scheme and user:password are optional; [kt|br] for example [kt|b]http://user:password@123.124.125.126:3128[/kt|b] or simply [kt|b]123.124.125.126:3128[/kt|b] if no authentication is needed";
$lang['plugins']['grabbers']['field_account']                           = "Account";
$lang['plugins']['grabbers']['field_account_hint']                      = "if site needs account login to grab content, please specify it as [kt|b]username:password[/kt|b] pair";
$lang['plugins']['grabbers']['field_threads']                           = "Threads per grabber";
$lang['plugins']['grabbers']['field_threads_hint']                      = "enable multiple threads per grabber if your grabbers are configured to download files, which may take much time; please consider that multi-threaded requests may trigger your server IP to be blocked by a target site";
$lang['plugins']['grabbers']['field_limit_title']                       = "Title limit";
$lang['plugins']['grabbers']['field_limit_title_words']                 = "words";
$lang['plugins']['grabbers']['field_limit_title_characters']            = "characters";
$lang['plugins']['grabbers']['field_limit_title_hint']                  = "specify if titles should be truncated to the given amount of words or characters";
$lang['plugins']['grabbers']['field_limit_description']                 = "Description limit";
$lang['plugins']['grabbers']['field_limit_description_words']           = "words";
$lang['plugins']['grabbers']['field_limit_description_characters']      = "characters";
$lang['plugins']['grabbers']['field_limit_description_hint']            = "specify if descriptions should be truncated to the given amount of words or characters";
$lang['plugins']['grabbers']['field_status_after_import']               = "Status after import";
$lang['plugins']['grabbers']['field_status_after_import_active']        = "Active";
$lang['plugins']['grabbers']['field_status_after_import_disabled']      = "Inactive";
$lang['plugins']['grabbers']['field_options_categorization']            = "New categorization";
$lang['plugins']['grabbers']['field_options_categorization_categories'] = "Do not create new categories";
$lang['plugins']['grabbers']['field_options_categorization_models']     = "Do not create new models";
$lang['plugins']['grabbers']['field_options_categorization_cs']         = "Do not create new content sources";
$lang['plugins']['grabbers']['field_options_categorization_channels']   = "Do not create new channels";
$lang['plugins']['grabbers']['field_options_categorization_hint']       = "categorization objects detected by grabbers will be auto-created by default if they are missing; enable these options if you don't want new objects to be created, then grabbers will only use your existing categorization";
$lang['plugins']['grabbers']['field_options_other']                     = "Other options";
$lang['plugins']['grabbers']['field_options_other_duplicates']          = "Do not grab content with existing titles";
$lang['plugins']['grabbers']['field_options_other_duplicates_hint']     = "if enabled, content with the titles that already exist in your database will be skipped; this may help you to eliminate duplicate content from different sites";
$lang['plugins']['grabbers']['field_options_other_need_review']         = "Mark all content with \"Needs review\" flag";
$lang['plugins']['grabbers']['field_options_other_need_review_hint']    = "provides ability to filter all new content from grabbers in admin panel";
$lang['plugins']['grabbers']['field_options_other_randomize_time']      = "Randomize publishing time";
$lang['plugins']['grabbers']['field_options_other_randomize_time_hint'] = "if enabled, publishing time of all imported videos will be randomized between 00:00 and 23:59; otherwise all imported videos will use server time";
$lang['plugins']['grabbers']['field_videos_amount']                     = "Videos";
$lang['plugins']['grabbers']['field_albums_amount']                     = "Albums";
$lang['plugins']['grabbers']['field_models_amount']                     = "Models";
$lang['plugins']['grabbers']['field_total']                             = "Total";
$lang['plugins']['grabbers']['field_last_exec']                         = "Last executed";
$lang['plugins']['grabbers']['field_last_exec_none']                    = "none";
$lang['plugins']['grabbers']['field_last_exec_info']                    = "(%1% seconds, %2% added, %3% duplicates)";
$lang['plugins']['grabbers']['error_invalid_grabber_file']              = "[kt|b]%1%[/kt|b]: the uploaded file is not a valid PHP file or doesn't implement KVS grabber API correctly";
$lang['plugins']['grabbers']['error_same_formats_multiple_quality']     = "[kt|b]%1%[/kt|b]: please select different video format for each quality";
$lang['plugins']['grabbers']['error_autopilot_url_not_supported']       = "[kt|b]%1%[/kt|b]: one of the URLs is not supported by this grabber (%2%)";
$lang['plugins']['grabbers']['error_ydl_path_invalid']                  = "[kt|b]%1%[/kt|b]: binary path is invalid";
$lang['plugins']['grabbers']['error_no_dom_module_installed']           = "PHP DOM module is not installed.";
$lang['plugins']['grabbers']['error_no_grabbers_installed']             = "There are no grabbers installed. Please install grabbers in order to upload content from them.";
$lang['plugins']['grabbers']['error_grabber_broken']                    = "This grabber is indicated to be broken and is not working at the moment. Once fixed, it will be enabled automatically.";
$lang['plugins']['grabbers']['error_grabber_noydl']                     = "Youtube-dl library is not found (https://github.com/rg3/youtube-dl)";
$lang['plugins']['grabbers']['btn_save']                                = "Save";
$lang['plugins']['grabbers']['btn_upload']                              = "Upload";
$lang['plugins']['grabbers']['btn_back']                                = "Back";
$lang['plugins']['grabbers']['btn_confirm']                             = "Confirm";
