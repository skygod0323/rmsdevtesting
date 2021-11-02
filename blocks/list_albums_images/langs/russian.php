<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_albums_images messages
// =====================================================================================================================

$lang['list_albums_images']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['list_albums_images']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['list_albums_images']['groups']['static_filters'] = $lang['website_ui']['block_group_default_static_filters'];

$lang['list_albums_images']['params']['items_per_page']     = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_albums_images']['params']['links_per_page']     = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_albums_images']['params']['var_from']           = $lang['website_ui']['parameter_default_var_from'];
$lang['list_albums_images']['params']['var_items_per_page'] = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_albums_images']['params']['sort_by']            = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_albums_images']['params']['var_sort_by']        = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_albums_images']['params']['is_private']         = "Статический фильтр по типу альбомов, из которых следует выводить фотографии.";
$lang['list_albums_images']['params']['format']             = "Статический фильтр по формату изображений. Поддерживаются следующие форматы изображений: [kt|b]jpg[/kt|b], [kt|b]gif[/kt|b], [kt|b]png[/kt|b].";

$lang['list_albums_images']['values']['is_private']['0']            = "Публичные и личные";
$lang['list_albums_images']['values']['is_private']['1']            = "Только публичные";
$lang['list_albums_images']['values']['is_private']['2']            = "Только личные";
$lang['list_albums_images']['values']['sort_by']['image_id']        = "ID фотографии";
$lang['list_albums_images']['values']['sort_by']['title']           = "Название";
$lang['list_albums_images']['values']['sort_by']['rating']          = "Рейтинг";
$lang['list_albums_images']['values']['sort_by']['image_viewed']    = "Популярность";
$lang['list_albums_images']['values']['sort_by']['added_date']      = "Дата создания";
$lang['list_albums_images']['values']['sort_by']['rand()']          = "Случайно";

$lang['list_albums_images']['block_short_desc'] = "Выводит список фотографий с заданными опциями";

$lang['list_albums_images']['block_desc'] = "
	Блок предназначен для отображения списка фотографий из разных альбомов с различными опциями сортировки и фильтрации.
	Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_albums_images']['block_examples'] = "
	[kt|b]Показать фотографии с сортировкой по дате добавления по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 фотографий с наибольшим рейтингом[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = rating[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 публичных GIF фотографий в случайном порядке[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rand()[kt|br]
	- is_private = 1[kt|br]
	- format = gif[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
