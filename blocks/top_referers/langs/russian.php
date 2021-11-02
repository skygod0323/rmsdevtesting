<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// top_referers messages
// =====================================================================================================================

$lang['top_referers']['groups']['top']              = "Настройки топлиста";
$lang['top_referers']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['top_referers']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['top_referers']['groups']['modes']            = "Режимы";

$lang['top_referers']['params']['items_per_page']   = "Ограничивает кол-во рефереров в топлисте.";
$lang['top_referers']['params']['sort_by']          = "Указывает сортировку рефереров.";
$lang['top_referers']['params']['stats_period']     = "Указывает какой период статистики будет использоваться для расчета позиции каждого реферера (в днях).";
$lang['top_referers']['params']['show_referers']    = "Позволяет выводить только рефереров с данными ID (список ID рефереров разделенных через запятую).";
$lang['top_referers']['params']['skip_referers']    = "Позволяет не выводить рефереров с данными ID (список ID рефереров разделенных через запятую).";
$lang['top_referers']['params']['show_categories']  = "Позволяет выводить только рефереров из данных категорий (список ID категорий разделенных через запятую).";
$lang['top_referers']['params']['skip_categories']  = "Позволяет не выводить рефереров из данных категорий (список ID категорий разделенных через запятую).";
$lang['top_referers']['params']['var_video_dir']    = "Параметр URL-а, в котором передается директория видео. Позволяет выводить только рефереров с такой же категорией, как у видео с заданной директорией.";
$lang['top_referers']['params']['var_video_id']     = "Параметр URL-а, в котором передается ID видео. Позволяет выводить только рефереров с такой же категорией, как у видео с заданным ID.";
$lang['top_referers']['params']['var_category_dir'] = "Параметр URL-а, в котором передается директория категории. Позволяет выводить только рефереров из категории с заданной директорией.";
$lang['top_referers']['params']['var_category_id']  = "Параметр URL-а, в котором передается ID категории. Позволяет выводить только рефереров из категории с заданным ID.";
$lang['top_referers']['params']['mode_videos']      = "Включает выборку данных о видео для каждого реферера и устанавливает метод выборки. Позволяет имитировать список видео со ссылками на рефереров.";

$lang['top_referers']['values']['mode_videos']['duration']                                  = "Наибольшая длительность";
$lang['top_referers']['values']['mode_videos']['post_date']                                 = "Добавленные недавно";
$lang['top_referers']['values']['mode_videos']['last_time_view_date']                       = "Просматриваются прямо сейчас";
$lang['top_referers']['values']['mode_videos']['rating']                                    = "С наиболее высоким рейтингом";
$lang['top_referers']['values']['mode_videos']['video_viewed']                              = "Самые популярные";
$lang['top_referers']['values']['mode_videos']['rand()']                                    = "Случайно";
$lang['top_referers']['values']['sort_by']['referer_id']                                    = "ID реферера";
$lang['top_referers']['values']['sort_by']['title']                                         = "Название";
$lang['top_referers']['values']['sort_by']['uniq_amount']                                   = "Уник. запросы";
$lang['top_referers']['values']['sort_by']['total_amount']                                  = "Все запросы";
$lang['top_referers']['values']['sort_by']['view_video_amount']                             = "Просмотры видео";
$lang['top_referers']['values']['sort_by']['view_embed_amount']                             = "Загрузки embed";
$lang['top_referers']['values']['sort_by']['cs_out_amount+adv_out_amount']                  = "Выходы";
$lang['top_referers']['values']['sort_by']['total_amount/uniq_amount']                      = "Всего / уник. (ратио)";
$lang['top_referers']['values']['sort_by']['view_video_amount/uniq_amount']                 = "Просмотры / уник. (ратио)";
$lang['top_referers']['values']['sort_by']['(cs_out_amount+adv_out_amount)/uniq_amount']    = "Выходы / уник. (ратио)";
$lang['top_referers']['values']['sort_by']['view_video_amount/total_amount']                = "Просмотры / всего (ратио)";
$lang['top_referers']['values']['sort_by']['(cs_out_amount+adv_out_amount)/total_amount']   = "Выходы / всего (ратио)";
$lang['top_referers']['values']['sort_by']['rand()']                                        = "Случайно";

$lang['top_referers']['block_short_desc'] = "Выводит топлист предустановленных рефереров с заданными опциями";

$lang['top_referers']['block_desc'] = "
	Блок предназначен для отображения списка рефереров с различными опциями сортировки и фильтрации. Для того, чтобы
	рефереры попали в список отображения, их необходимо предварительно добавить в разделе статистики панели
	администрирования.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	[kt|b]{$lang['top_referers']['groups']['modes']}[/kt|b]
	[kt|br][kt|br]

	Блок позволяет имитировать список видео со ссылками на рефереров. Для этого вам необходимо включить режим выборки
	видео для каждого реферера. Если у реферера в настройках указана категория, то выборка видео будет производиться
	исходя из заданной категории. Дубликаты видео выбираться не будут.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['top_referers']['block_examples'] = "
	[kt|b]Показать 20 случайных рефереров[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- sort_by = rand()[kt|br]
	- stats_period = 30[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 рефереров, подходящих к категории с директорией 'my_category'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = rand()[kt|br]
	- stats_period = 30[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 рефереров, подходящих к видео с ID '287' и отображением наиболее популярного видео для каждого[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rand()[kt|br]
	- stats_period = 30[kt|br]
	- var_video_id = id[kt|br]
	- mode_videos = video_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=287
	[/kt|code]
";
