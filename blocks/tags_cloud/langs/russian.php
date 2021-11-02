<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// tags_cloud messages
// =====================================================================================================================

$lang['tags_cloud']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['tags_cloud']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['tags_cloud']['groups']['sizes']          = "Размер шрифта";
$lang['tags_cloud']['groups']['display_modes']  = $lang['website_ui']['block_group_default_display_modes'];
$lang['tags_cloud']['groups']['categories']     = "Облако категорий";

$lang['tags_cloud']['params']['items_per_page']             = "Ограничивает кол-во тэгов для отображения. Установите значение [kt|b]0[/kt|b] для отображения всех возможных тэгов.";
$lang['tags_cloud']['params']['sort_by']                    = "Указывает сортировку тэгов в облаке.";
$lang['tags_cloud']['params']['size_from']                  = "Размер шрифта в пикселах для самого редкого (минимального) тэга.";
$lang['tags_cloud']['params']['size_to']                    = "Размер шрифта в пикселах для самого используемого (максимального) тэга.";
$lang['tags_cloud']['params']['bold_from']                  = "Указывает, начиная с какого размера шрифта тэг должен выделяться жирным шрифтом.";
$lang['tags_cloud']['params']['mode_albums']                = "Включает отображение тэгов по фотоальбомам вместо тэгов по видео.";
$lang['tags_cloud']['params']['mode_posts']                 = "Включает отображение тэгов по постам вместо тэгов по видео.";
$lang['tags_cloud']['params']['mode_playlists']             = "Включает отображение тэгов по плэйлистам вместо тэгов по видео.";
$lang['tags_cloud']['params']['mode_dvds']                  = "Включает отображение тэгов по DVD / каналам вместо тэгов по видео.";
$lang['tags_cloud']['params']['mode_cs']                    = "Включает отображение тэгов по контент провайдерам вместо тэгов по видео.";
$lang['tags_cloud']['params']['mode_models']                = "Включает отображение тэгов по моделям вместо тэгов по видео.";
$lang['tags_cloud']['params']['mode_categories']            = "Включает отображение облака категорий вместо облака тэгов.";
$lang['tags_cloud']['params']['show_only_with_avatar']      = "Используется для показа только тех категорий, у которых задан аватар.";
$lang['tags_cloud']['params']['show_only_without_avatar']   = "Используется для показа только тех категорий, у которых не задан аватар.";
$lang['tags_cloud']['params']['show_only_with_items']       = "Используется для показа только тех категорий, в которых есть контент.";

$lang['tags_cloud']['values']['sort_by']['title']   = "Название";
$lang['tags_cloud']['values']['sort_by']['amount']  = "Кол-во контента";
$lang['tags_cloud']['values']['sort_by']['rand()']  = "Случайно";

$lang['tags_cloud']['block_short_desc'] = "Выводит облако тэгов или категорий вашего сайта";

$lang['tags_cloud']['block_desc'] = "
	Блок предназначен для отображения облака тэгов или категорий вашего сайта.
	[kt|br][kt|br]

	[kt|b]Размер шрифта[/kt|b]
	[kt|br][kt|br]

	Облако тэгов отображается как список тэгов, в котором все тэги имеют разные размеры в зависимости от частоты их
	использования так, что наиболее популярные тэги имеют большие размеры. Параметры этого раздела позволяют задать
	абсолютные значения шрифтов для отображения.
	[kt|br][kt|br]

	[kt|b]Режимы отображения[/kt|b]
	[kt|br][kt|br]

	По умолчанию популярность тэгов считается по видео, но это может быть переключено на другие объекты с помощью
	режимов отображения.
	[kt|br][kt|br]

	[kt|b]Облако категорий[/kt|b]
	[kt|br][kt|br]

	Вы также можете вывести облако из категорий в этом блоке.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['tags_cloud']['block_examples'] = "
	[kt|b]Показать полное облако тэгов в случайной сортировке[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = rand()[kt|br]
	- size_from = 12[kt|br]
	- size_to = 19[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
