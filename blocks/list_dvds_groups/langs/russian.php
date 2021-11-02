<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_dvds_groups messages
// =====================================================================================================================

$lang['list_dvds_groups']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_dvds_groups']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_dvds_groups']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_dvds_groups']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_dvds_groups']['groups']['display_modes']    = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_dvds_groups']['groups']['search']           = "Текстовый поиск групп DVD";
$lang['list_dvds_groups']['groups']['subselects']       = "Выборка дополнительных данных для каждой группы DVD";
$lang['list_dvds_groups']['groups']['pull_dvds']        = "Выборка DVD для каждой группы";

$lang['list_dvds_groups']['params']['items_per_page']               = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_dvds_groups']['params']['links_per_page']               = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_dvds_groups']['params']['var_from']                     = $lang['website_ui']['parameter_default_var_from'];
$lang['list_dvds_groups']['params']['var_items_per_page']           = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_dvds_groups']['params']['sort_by']                      = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_dvds_groups']['params']['var_sort_by']                  = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_dvds_groups']['params']['show_only_with_dvds']          = "Используется для показа только тех групп, в которых есть DVD.";
$lang['list_dvds_groups']['params']['show_only_with_screenshot1']   = "Используется для показа только тех групп, у которых задан скриншот #1.";
$lang['list_dvds_groups']['params']['show_only_with_screenshot2']   = "Используется для показа только тех групп, у которых задан скриншот #2.";
$lang['list_dvds_groups']['params']['show_only_with_description']   = "Используется для показа только тех групп, у которых задано не пустое описание.";
$lang['list_dvds_groups']['params']['skip_categories']              = "Позволяет не выводить группы из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_dvds_groups']['params']['show_categories']              = "Позволяет выводить только группы из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_dvds_groups']['params']['skip_tags']                    = "Позволяет не выводить группы с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_dvds_groups']['params']['show_tags']                    = "Позволяет выводить только группы с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_dvds_groups']['params']['skip_models']                  = "Позволяет не выводить группы с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_dvds_groups']['params']['show_models']                  = "Позволяет выводить только группы с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_dvds_groups']['params']['var_title_section']            = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_dvds_groups']['params']['var_category_dir']             = "Параметр URL-а, в котором передается директория категории. Позволяет выводить только группы из категории с заданной директорией.";
$lang['list_dvds_groups']['params']['var_category_id']              = "Параметр URL-а, в котором передается ID категории. Позволяет выводить только группы из категории с заданным ID.";
$lang['list_dvds_groups']['params']['var_category_ids']             = "Параметр URL-а, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только группы из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те группы, которые одновременно принадлежат всем перечисленным категориям.";
$lang['list_dvds_groups']['params']['var_category_group_dir']       = "Параметр URL-а, в котором передается директория группы категорий. Позволяет выводить только группы из группы категорий с заданной директорией.";
$lang['list_dvds_groups']['params']['var_category_group_id']        = "Параметр URL-а, в котором передается ID группы категорий. Позволяет выводить только группы из группы категорий с заданным ID.";
$lang['list_dvds_groups']['params']['var_category_group_ids']       = "Параметр URL-а, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только группы из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те группы, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['list_dvds_groups']['params']['var_tag_dir']                  = "Параметр URL-а, в котором передается директория тэга. Позволяет выводить только группы, у которых есть тэг с заданной директорией.";
$lang['list_dvds_groups']['params']['var_tag_id']                   = "Параметр URL-а, в котором передается ID тэга. Позволяет выводить только группы, у которых есть тэг с заданным ID.";
$lang['list_dvds_groups']['params']['var_tag_ids']                  = "Параметр URL-а, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только группы, у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те группы, у которых одновременно есть все перечисленные тэги.";
$lang['list_dvds_groups']['params']['var_model_dir']                = "Параметр URL-а, в котором передается директория модели. Позволяет выводить только группы, у которых есть модель с заданной директорией.";
$lang['list_dvds_groups']['params']['var_model_id']                 = "Параметр URL-а, в котором передается ID модели. Позволяет выводить только группы, у которых есть модель с заданным ID.";
$lang['list_dvds_groups']['params']['var_model_ids']                = "Параметр URL-а, в котором передается список ID моделей, разделенных через запятую. Позволяет выводить только группы, у которых есть модели с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те группы, у которых одновременно есть все перечисленные модели.";
$lang['list_dvds_groups']['params']['var_model_group_dir']          = "Параметр URL-а, в котором передается директория группы моделей. Позволяет выводить только группы из группы моделей с заданной директорией.";
$lang['list_dvds_groups']['params']['var_model_group_id']           = "Параметр URL-а, в котором передается ID группы моделей. Позволяет выводить только группы из группы моделей с заданным ID.";
$lang['list_dvds_groups']['params']['var_model_group_ids']          = "Параметр URL-а, в котором передается список ID групп моделей, разделенных через запятую. Позволяет выводить только группы из групп моделей с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те группы, которые одновременно принадлежат всем перечисленным группам моделей.";
$lang['list_dvds_groups']['params']['var_search']                   = "Параметр URL-а, в котором передается поисковая строка. Позволяет выводить только группы, которые соответствуют поисковой строке.";
$lang['list_dvds_groups']['params']['search_method']                = "Устанавливает метод поиска.";
$lang['list_dvds_groups']['params']['search_scope']                 = "Указывает, по каким полям должен идти поиск.";
$lang['list_dvds_groups']['params']['search_redirect_enabled']      = "Включает редирект на страницу группы, если результаты поиска содержат только 1 группу.";
$lang['list_dvds_groups']['params']['search_redirect_pattern']      = "Паттерн страницы группы, на которую нужно перенаправлять пользователя, если результаты поиска содержат только 1 группу (в этом случае пользователь будет мгновенно перенаправлен на страницу этой группы). Паттерн должен содержать как минимум один из токенов: [kt|b]%ID%[/kt|b] и / или [kt|b]%DIR%[/kt|b].";
$lang['list_dvds_groups']['params']['show_categories_info']         = "Включает выборку данных о категориях для каждой группы. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds_groups']['params']['show_tags_info']               = "Включает выборку данных о тэгах для каждой группы. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds_groups']['params']['show_models_info']             = "Включает выборку данных о моделях для каждой группы. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds_groups']['params']['pull_dvds']                    = "Включает возможность выборки списка DVD для каждой группы. Количество и сортировка списка DVD настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds_groups']['params']['pull_dvds_count']              = "Указывает кол-во DVD, которое выбирается для каждой группы.";
$lang['list_dvds_groups']['params']['pull_dvds_sort_by']            = "Указывает сортировку DVD, которые выбираются для каждой группы.";

$lang['list_dvds_groups']['values']['search_method']['1']                           = "Полное совпадение с запросом";
$lang['list_dvds_groups']['values']['search_method']['2']                           = "Совпадение с элементами запроса";
$lang['list_dvds_groups']['values']['search_scope']['0']                            = "Название и описание";
$lang['list_dvds_groups']['values']['search_scope']['1']                            = "Только название";
$lang['list_dvds_groups']['values']['sort_by']['dvd_group_id']                      = "ID группы";
$lang['list_dvds_groups']['values']['sort_by']['sort_id']                           = "ID сортировки";
$lang['list_dvds_groups']['values']['sort_by']['title']                             = "Название";
$lang['list_dvds_groups']['values']['sort_by']['total_dvds']                        = "Кол-во DVD";
$lang['list_dvds_groups']['values']['sort_by']['rand()']                            = "Случайно";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['dvd_id']                  = "ID DVD";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['sort_id']                 = "ID сортировки";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['title']                   = "Название";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['rating']                  = "Рейтинг";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['dvd_viewed']              = "Популярность";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['today_videos']            = "Кол-во видео сегодня";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['total_videos']            = "Кол-во видео";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['total_videos_duration']   = "Длительность видео";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['avg_videos_rating']       = "Средний рейтинг видео";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['avg_videos_popularity']   = "Средняя популярность видео";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['comments_count']          = "Кол-во комментариев";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['subscribers_count']       = "Кол-во подписок";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['last_content_date']       = "Дата последнего добавления контента";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['added_date']              = "Дата создания";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['rand()']                  = "Случайно";

$lang['list_dvds_groups']['block_short_desc'] = "Выводит список групп DVD / каналов / ТВ сериалов с заданными опциями";

$lang['list_dvds_groups']['block_desc'] = "
	Блок предназначен для отображения списка групп DVD / каналов / ТВ сериалов с различными опциями сортировки и
	фильтрации. Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_dvds_groups']['block_examples'] = "
	[kt|b]Показать все группы DVD по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать только группы с DVD, по 10 на страницу и сортировкой по кол-ву DVD в них[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = total_dvds desc[kt|br]
	- show_only_with_dvds[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все группы с названием, которое начинается на букву 'a'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_title_section = section[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?section=a
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 групп в категории с директорией 'my_category' и сортировкой по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = title asc[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать по 10 групп на страницу с 5-ю топовыми DVD для каждой группы по рейтингу[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- pull_dvds[kt|br]
	- pull_dvds_count = 5[kt|br]
	- pull_dvds_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
