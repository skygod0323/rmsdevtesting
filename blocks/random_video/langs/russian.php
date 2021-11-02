<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// random_video messages
// =====================================================================================================================

$lang['random_video']['groups']['random_selection'] = "Первоначальная выборка";
$lang['random_video']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['random_video']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];

$lang['random_video']['params']['initial_set_count']        = "Указывает размер первоначальной выборки, из которой будет выбрано случайное видео.";
$lang['random_video']['params']['sort_by']                  = "Указывает сортировку элементов в первоначальной выборке.";
$lang['random_video']['params']['skip_categories']          = "Позволяет не выбирать в первоначальной выборке видео из данных категорий (список ID категорий разделенных через запятую).";
$lang['random_video']['params']['show_categories']          = "Позволяет выбирать в первоначальной выборке только видео из данных категорий (список ID категорий разделенных через запятую).";
$lang['random_video']['params']['skip_tags']                = "Позволяет не выбирать в первоначальной выборке видео с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['random_video']['params']['show_tags']                = "Позволяет выбирать в первоначальной выборке только видео с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['random_video']['params']['skip_content_sources']     = "Позволяет не выбирать в первоначальной выборке видео из данных контент провайдеров (список ID контент провайдеров разделенных через запятую).";
$lang['random_video']['params']['show_content_sources']     = "Позволяет выбирать в первоначальной выборке только видео из данных контент провайдеров (список ID контент провайдеров разделенных через запятую).";
$lang['random_video']['params']['skip_dvds']                = "Позволяет не выбирать в первоначальной выборке видео из данных DVD (список ID DVD разделенных через запятую).";
$lang['random_video']['params']['show_dvds']                = "Позволяет выбирать в первоначальной выборке только видео из данных DVD (список ID DVD разделенных через запятую).";
$lang['random_video']['params']['days_passed_from']         = "Позволяет фильтровать первоначальную выборку видео по дате публикации, например, видео добавленное сегодня, вчера, за неделю и т.д. Указывает верхнюю границу даты публикации в кол-ве дней, прошедших с текущего дня.";
$lang['random_video']['params']['days_passed_to']           = "Позволяет фильтровать первоначальную выборку видео по дате публикации, например, видео добавленное сегодня, вчера, за неделю и т.д. Указывает нижнюю границу даты публикации в кол-ве дней, прошедших с текущего дня. Значение должно быть больше, чем значение в параметре блока [kt|b]days_passed_from[/kt|b].";
$lang['random_video']['params']['is_private']               = "Позволяет выбирать в первоначальной выборке видео с различной доступностью.";
$lang['random_video']['params']['var_category_dir']         = "Параметр URL-а, в котором передается директория категории. Позволяет выбирать в первоначальной выборке только видео из категории с заданной директорией.";
$lang['random_video']['params']['var_category_id']          = "Параметр URL-а, в котором передается ID категории. Позволяет выбирать в первоначальной выборке только видео из категории с заданным ID.";
$lang['random_video']['params']['var_tag_dir']              = "Параметр URL-а, в котором передается директория тэга. Позволяет выбирать в первоначальной выборке только видео, у которых есть тэг с заданной директорией.";
$lang['random_video']['params']['var_tag_id']               = "Параметр URL-а, в котором передается ID тэга. Позволяет выбирать в первоначальной выборке только видео, у которых есть тэг с заданным ID.";
$lang['random_video']['params']['var_model_dir']            = "Параметр URL-а, в котором передается директория модели. Позволяет выбирать в первоначальной выборке только видео, у которых есть модель с заданной директорией.";
$lang['random_video']['params']['var_model_id']             = "Параметр URL-а, в котором передается ID модели. Позволяет выбирать в первоначальной выборке только видео, у которых есть модель с заданным ID.";
$lang['random_video']['params']['var_content_source_dir']   = "Параметр URL-а, в котором передается директория контент провайдера. Позволяет выбирать в первоначальной выборке видео по контент провайдеру с заданной директорией.";
$lang['random_video']['params']['var_content_source_id']    = "Параметр URL-а, в котором передается ID контент провайдера. Позволяет выбирать в первоначальной выборке видео по контент провайдеру с заданным ID.";
$lang['random_video']['params']['var_dvd_dir']              = "Параметр URL-а, в котором передается директория DVD. Позволяет выбирать в первоначальной выборке видео по DVD с заданной директорией.";
$lang['random_video']['params']['var_dvd_id']               = "Параметр URL-а, в котором передается ID DVD. Позволяет выбирать в первоначальной выборке видео по DVD с заданным ID.";

$lang['random_video']['values']['is_private']['0']                  = "Только публичные";
$lang['random_video']['values']['is_private']['1']                  = "Только личные";
$lang['random_video']['values']['is_private']['2']                  = "Только премиум";
$lang['random_video']['values']['is_private']['0|1']                = "Только публичные и личные";
$lang['random_video']['values']['is_private']['0|2']                = "Только публичные и премиум";
$lang['random_video']['values']['is_private']['1|2']                = "Только личные и премиум";
$lang['random_video']['values']['sort_by']['duration']              = "Длительность";
$lang['random_video']['values']['sort_by']['post_date']             = "Дата публикации";
$lang['random_video']['values']['sort_by']['last_time_view_date']   = "Последний просмотр";
$lang['random_video']['values']['sort_by']['rating']                = "Суммарный рейтинг";
$lang['random_video']['values']['sort_by']['rating_today']          = "Рейтинг за сегодня";
$lang['random_video']['values']['sort_by']['rating_week']           = "Рейтинг за неделю";
$lang['random_video']['values']['sort_by']['rating_month']          = "Рейтинг за месяц";
$lang['random_video']['values']['sort_by']['video_viewed']          = "Суммарная популярность";
$lang['random_video']['values']['sort_by']['video_viewed_today']    = "Популярность за сегодня";
$lang['random_video']['values']['sort_by']['video_viewed_week']     = "Популярность за неделю";
$lang['random_video']['values']['sort_by']['video_viewed_month']    = "Популярность за месяц";
$lang['random_video']['values']['sort_by']['most_favourited']       = "Добавление в закладки";
$lang['random_video']['values']['sort_by']['most_commented']        = "Кол-во комментариев";
$lang['random_video']['values']['sort_by']['pseudo_rand']           = "Псевдослучайно (работает быстрее)";
$lang['random_video']['values']['sort_by']['rand()']                = "Случайно";

$lang['random_video']['block_short_desc'] = "Выводит информацию о случайном видео";

$lang['random_video']['block_desc'] = "
	Блок отображается данные случайного видео. Он работает следующим образом: сначала делается выборка нескольких видео
	по какому-то критерию и сортировке, а затем из этих видео выбирается одно случайное.
	[kt|br][kt|br]

	Блок также предоставляет следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг видео с одного IP.[kt|br]
	- Однократное голосование каждым флагом видео с одного IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['random_video']['block_examples'] = "
	[kt|b]Показать случайное видео из 10 самых популярных видео[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- initial_set_count = 10[kt|br]
	- sort_by = video_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать случайное видео из всех видео, добавленных за сегодня[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- initial_set_count = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- days_passed_from = 0[kt|br]
	- days_passed_to = 1[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать случайное видео из 15 видео с наибольшим рейтингом за последнюю неделю в категории с директорией 'my_category'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- initial_set_count = 15[kt|br]
	- sort_by = rating_week[kt|br]
	- var_category_dir = category_dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category_dir=my_category
	[/kt|code]
";
