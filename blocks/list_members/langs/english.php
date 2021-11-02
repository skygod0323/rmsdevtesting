<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_members messages
// =====================================================================================================================

$lang['list_members']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_members']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_members']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_members']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_members']['groups']['display_modes']    = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_members']['groups']['search']           = "Search users by text";

$lang['list_members']['params']['items_per_page']                   = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_members']['params']['links_per_page']                   = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_members']['params']['var_from']                         = $lang['website_ui']['parameter_default_var_from'];
$lang['list_members']['params']['var_items_per_page']               = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_members']['params']['sort_by']                          = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_members']['params']['var_sort_by']                      = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_members']['params']['show_only_with_avatar']            = "If enabled, only members with avatar available are displayed in result.";
$lang['list_members']['params']['show_only_online']                 = "If enabled, only members who are currently online are displayed in result.";
$lang['list_members']['params']['show_only_trusted']                = "If enabled, only trusted members are displayed in result.";
$lang['list_members']['params']['show_only_current_countries']      = "If enabled, only members from country of the current user are displayed in result.";
$lang['list_members']['params']['show_gender']                      = "If enabled, only members with the configured gender are displayed in result.";
$lang['list_members']['params']['show_status']                      = "If enabled, only members with the configured status are displayed in result.";
$lang['list_members']['params']['match_locale']                     = "If this parameter is enabled, block will show only members registered with the current KVS locale.";
$lang['list_members']['params']['var_country_id']                   = "HTTP parameter, which provides country ID. If specified, only members from country with this ID will be displayed.";
$lang['list_members']['params']['var_city']                         = "HTTP parameter, which provides city part. If specified, only members from matching cities will be displayed.";
$lang['list_members']['params']['var_gender_id']                    = "HTTP parameter, which provides gender ID. If specified, only members of this gender will be displayed.";
$lang['list_members']['params']['var_relationship_status_id']       = "HTTP parameter, which provides relationship status ID. If specified, only members with this relationship status will be displayed.";
$lang['list_members']['params']['var_orientation_id']               = "HTTP parameter, which provides sexual orientation ID. If specified, only members with this sexual orientation will be displayed.";
$lang['list_members']['params']['var_age_from']                     = "HTTP parameter, which provides lower age threshold. If specified, only members with age greater than given threshold will be displayed.";
$lang['list_members']['params']['var_age_to']                       = "HTTP parameter, which provides upper age threshold. If specified, only members with age less than given threshold will be displayed.";
$lang['list_members']['params']['var_show_only_with_avatar']        = "HTTP parameter, which must provide [kt|b]1[/kt|b] to make this filter working. If specified, only members with avatar available will be displayed.";
$lang['list_members']['params']['var_show_only_online']             = "HTTP parameter, which must provide [kt|b]1[/kt|b] to make this filter working. If specified, only members who are currently online will be displayed.";
$lang['list_members']['params']['var_show_only_current_countries']  = "HTTP parameter, which must provide [kt|b]1[/kt|b] to make this filter working. If specified, only members from country of the current user will be displayed.";
$lang['list_members']['params']['var_custom1']                      = "HTTP parameter, which provides value for custom field 1 dynamic filtering.";
$lang['list_members']['params']['var_custom2']                      = "HTTP parameter, which provides value for custom field 2 dynamic filtering.";
$lang['list_members']['params']['var_custom3']                      = "HTTP parameter, which provides value for custom field 3 dynamic filtering.";
$lang['list_members']['params']['var_custom4']                      = "HTTP parameter, which provides value for custom field 4 dynamic filtering.";
$lang['list_members']['params']['var_custom5']                      = "HTTP parameter, which provides value for custom field 5 dynamic filtering.";
$lang['list_members']['params']['var_custom6']                      = "HTTP parameter, which provides value for custom field 6 dynamic filtering.";
$lang['list_members']['params']['var_custom7']                      = "HTTP parameter, which provides value for custom field 7 dynamic filtering.";
$lang['list_members']['params']['var_custom8']                      = "HTTP parameter, which provides value for custom field 8 dynamic filtering.";
$lang['list_members']['params']['var_custom9']                      = "HTTP parameter, which provides value for custom field 9 dynamic filtering.";
$lang['list_members']['params']['var_custom10']                     = "HTTP parameter, which provides value for custom field 10 dynamic filtering.";
$lang['list_members']['params']['var_search']                       = "HTTP parameter, which provides search string. If specified, only members, which match this string will be displayed.";
$lang['list_members']['params']['search_method']                    = "Specifies search method.";
$lang['list_members']['params']['mode_conversations']               = "Enables conversations display mode for the current user.";
$lang['list_members']['params']['mode_invites']                     = "Enables invites display mode for the current user.";
$lang['list_members']['params']['mode_friends']                     = "Enables friends display mode.";
$lang['list_members']['params']['mode_subscribers']                 = "Enables subscribers display mode.";
$lang['list_members']['params']['mode_subscribed']                  = "Enables subscribed users display mode.";
$lang['list_members']['params']['var_user_id']                      = "URL parameter, which provides user ID for the selected display mode.";
$lang['list_members']['params']['redirect_unknown_user_to']         = "Specifies redirect URL for the visitors that are not logged in and are attempting to access display mode available for members only.";

$lang['list_members']['values']['search_method']['1']                   = "Whole expression match";
$lang['list_members']['values']['search_method']['2']                   = "Any expression part match";
$lang['list_members']['values']['show_gender']['1']                     = "Men";
$lang['list_members']['values']['show_gender']['2']                     = "Women";
$lang['list_members']['values']['show_status']['2']                     = "Active";
$lang['list_members']['values']['show_status']['3']                     = "Premium";
$lang['list_members']['values']['show_status']['6']                     = "Webmasters";
$lang['list_members']['values']['sort_by']['user_id']                   = "User ID";
$lang['list_members']['values']['sort_by']['display_name']              = "Display name";
$lang['list_members']['values']['sort_by']['birth_date']                = "Birth date";
$lang['list_members']['values']['sort_by']['video_viewed']              = "His videos viewed times";
$lang['list_members']['values']['sort_by']['album_viewed']              = "His albums viewed times";
$lang['list_members']['values']['sort_by']['profile_viewed']            = "His profile viewed times";
$lang['list_members']['values']['sort_by']['video_watched']             = "Videos watched by user";
$lang['list_members']['values']['sort_by']['album_watched']             = "Albums watched by user";
$lang['list_members']['values']['sort_by']['comments_videos_count']     = "Video comments posted";
$lang['list_members']['values']['sort_by']['comments_albums_count']     = "Album comments posted";
$lang['list_members']['values']['sort_by']['comments_cs_count']         = "CS comments posted";
$lang['list_members']['values']['sort_by']['comments_models_count']     = "Model comments posted";
$lang['list_members']['values']['sort_by']['comments_dvds_count']       = "DVD / channel comments posted";
$lang['list_members']['values']['sort_by']['comments_posts_count']      = "Post comments posted";
$lang['list_members']['values']['sort_by']['comments_playlists_count']  = "Playlist comments posted";
$lang['list_members']['values']['sort_by']['comments_total_count']      = "Total comments posted";
$lang['list_members']['values']['sort_by']['logins_count']              = "Times logged in";
$lang['list_members']['values']['sort_by']['public_videos_count']       = "Public videos uploaded";
$lang['list_members']['values']['sort_by']['private_videos_count']      = "Private videos uploaded";
$lang['list_members']['values']['sort_by']['premium_videos_count']      = "Premium videos uploaded";
$lang['list_members']['values']['sort_by']['total_videos_count']        = "Total videos uploaded";
$lang['list_members']['values']['sort_by']['favourite_videos_count']    = "Videos added to favourites";
$lang['list_members']['values']['sort_by']['public_albums_count']       = "Public albums created";
$lang['list_members']['values']['sort_by']['private_albums_count']      = "Private albums created";
$lang['list_members']['values']['sort_by']['premium_albums_count']      = "Premium albums created";
$lang['list_members']['values']['sort_by']['total_albums_count']        = "Total albums created";
$lang['list_members']['values']['sort_by']['favourite_albums_count']    = "Albums added to favourites";
$lang['list_members']['values']['sort_by']['added_date']                = "Registration date";
$lang['list_members']['values']['sort_by']['last_login_date']           = "Last visit date";
$lang['list_members']['values']['sort_by']['last_online_date']          = "Last online date";
$lang['list_members']['values']['sort_by']['activity']                  = "Activity on website";
$lang['list_members']['values']['sort_by']['tokens_available']          = "Tokens available";
$lang['list_members']['values']['sort_by']['rand()']                    = "Random";

$lang['list_members']['block_short_desc'] = "Displays list of members with the given options";

$lang['list_members']['block_desc'] = "
	Block displays list of members with different sorting and filtering options. This block is a
	regular list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 6 different display modes for this block:[kt|br]
	1) Current user's conversation list. In order to use this mode, you should enable [kt|b]mode_conversations[/kt|b]
	   block parameter. If this mode is enabled, block will try to display conversations list of the current user
	   ('my conversations'), and if the current user is not logged in - user will be redirected to the URL specified in
	   the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.[kt|br]
	2) Current user's invites list. In order to use this mode, you should enable [kt|b]mode_invites[/kt|b]
	   block parameter. If this mode is enabled, block will try to display invites list for the current user
	   ('my invites'), and if the current user is not logged in - user will be redirected to the URL specified in
	   the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.[kt|br]
	3) User's friends list. In order to use this mode, you should enable [kt|b]mode_friends[/kt|b] block parameter.
	   If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display friends list of the user, whose
	   ID is passed in the related HTTP parameter. Otherwise block will first try to display friends list of the
	   current user ('my friends'), and if the current user is not logged in - user will be redirected to the URL
	   specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.[kt|br]
	4) User's subscribers list. In order to use this mode, you should enable [kt|b]mode_subscribers[/kt|b] block
	   parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display subscribers list of
	   the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display
	   subscribers list of the current user ('my subscribers'), and if the current user is not logged in - user will be
	   redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.[kt|br]
	5) User's subscriptions list. In order to use this mode, you should enable [kt|b]mode_subscribed[/kt|b] block
	   parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display subscriptions list of
	   the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display
	   subscriptions list of the current user ('my subscriptions'), and if the current user is not logged in - user
	   will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.[kt|br]
	6) Regular members list. You can use all filters and sorting for this display mode.
	[kt|br][kt|br]

	Text search on members can be implemented by using [kt|b]var_search[/kt|b] block parameter. It should
	point to HTTP parameter, which contains query string. One of several different search methods can be
	selected in [kt|b]search_method[/kt|b] block parameter.
	[kt|br][kt|br]

	You can also use different [kt|b]var_xx[/kt|b] block parameters in order to implement limited members
	search (search by such parameters as age, sex, orientation and etc).
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block
	will not be cached when displaying friends list of the current user ('my friends'), and also when
	searching members by search string.
";

$lang['list_members']['block_examples'] = "
	[kt|b]Display members 20 per page sorted by registration date[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 most active members[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = activity[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 15 most visited men profiles with avatar[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = profile_viewed desc[kt|br]
	- var_gender_id = gender_id[kt|br]
	- var_show_only_with_avatar = show_only_with_avatar[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?gender_id=1&show_only_with_avatar=1
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display friends list of member with ID '287', 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_friends[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my own friends list 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_friends[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";

?>