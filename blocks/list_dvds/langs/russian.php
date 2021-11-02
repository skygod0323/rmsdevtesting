<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_dvds messages
// =====================================================================================================================

$lang['list_dvds']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['list_dvds']['groups']['sorting']         = $lang['website_ui']['block_group_default_sorting'];
$lang['list_dvds']['groups']['static_filters']  = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_dvds']['groups']['dynamic_filters'] = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_dvds']['groups']['display_modes']   = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_dvds']['groups']['search']          = "Текстовый поиск DVD";
$lang['list_dvds']['groups']['subselects']      = "Выборка дополнительных данных для каждого DVD";
$lang['list_dvds']['groups']['pull_videos']     = "Выборка видео для каждого DVD";

$lang['list_dvds']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_dvds']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_dvds']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['list_dvds']['params']['var_items_per_page']          = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_dvds']['params']['sort_by']                     = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_dvds']['params']['var_sort_by']                 = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_dvds']['params']['show_only_with_cover1']       = "Используется для показа только тех DVD, у которых задана обложка #1 (лицевая и обратная стороны).";
$lang['list_dvds']['params']['show_only_with_cover2']       = "Используется для показа только тех DVD, у которых задана обложка #2 (лицевая и обратная стороны).";
$lang['list_dvds']['params']['show_only_with_description']  = "Используется для показа только тех DVD, у которых задано не пустое описание.";
$lang['list_dvds']['params']['show_only_with_videos']       = "Используется для показа только тех DVD, у которых есть заданное кол-во видео.";
$lang['list_dvds']['params']['skip_categories']             = "Позволяет не выводить DVD из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_dvds']['params']['show_categories']             = "Позволяет выводить только DVD из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_dvds']['params']['skip_tags']                   = "Позволяет не выводить DVD с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_dvds']['params']['show_tags']                   = "Позволяет выводить только DVD с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_dvds']['params']['skip_models']                 = "Позволяет не выводить DVD с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_dvds']['params']['show_models']                 = "Позволяет выводить только DVD с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_dvds']['params']['skip_dvd_groups']             = "Позволяет не выводить DVD из данных групп (список ID групп разделенных через запятую).";
$lang['list_dvds']['params']['show_dvd_groups']             = "Позволяет выводить только DVD из данных групп (список ID групп разделенных через запятую).";
$lang['list_dvds']['params']['var_title_section']           = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_dvds']['params']['var_category_dir']            = "Параметр URL-а, в котором передается директория категории. Позволяет выводить только DVD из категории с заданной директорией.";
$lang['list_dvds']['params']['var_category_id']             = "Параметр URL-а, в котором передается ID категории. Позволяет выводить только DVD из категории с заданным ID.";
$lang['list_dvds']['params']['var_category_ids']            = "Параметр URL-а, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только DVD из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те DVD, которые одновременно принадлежат всем перечисленным категориям.";
$lang['list_dvds']['params']['var_category_group_dir']      = "Параметр URL-а, в котором передается директория группы категорий. Позволяет выводить только DVD из группы категорий с заданной директорией.";
$lang['list_dvds']['params']['var_category_group_id']       = "Параметр URL-а, в котором передается ID группы категорий. Позволяет выводить только DVD из группы категорий с заданным ID.";
$lang['list_dvds']['params']['var_category_group_ids']      = "Параметр URL-а, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только DVD из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те DVD, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['list_dvds']['params']['var_tag_dir']                 = "Параметр URL-а, в котором передается директория тэга. Позволяет выводить только DVD, у которых есть тэг с заданной директорией.";
$lang['list_dvds']['params']['var_tag_id']                  = "Параметр URL-а, в котором передается ID тэга. Позволяет выводить только DVD, у которых есть тэг с заданным ID.";
$lang['list_dvds']['params']['var_tag_ids']                 = "Параметр URL-а, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только DVD, у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те DVD, у которых одновременно есть все перечисленные тэги.";
$lang['list_dvds']['params']['var_model_dir']               = "Параметр URL-а, в котором передается директория модели. Позволяет выводить только DVD, у которых есть модель с заданной директорией.";
$lang['list_dvds']['params']['var_model_id']                = "Параметр URL-а, в котором передается ID модели. Позволяет выводить только DVD, у которых есть модель с заданным ID.";
$lang['list_dvds']['params']['var_model_ids']               = "Параметр URL-а, в котором передается список ID моделей, разделенных через запятую. Позволяет выводить только DVD, у которых есть модели с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те DVD, у которых одновременно есть все перечисленные модели.";
$lang['list_dvds']['params']['var_model_group_dir']         = "Параметр URL-а, в котором передается директория группы моделей. Позволяет выводить только DVD из группы моделей с заданной директорией.";
$lang['list_dvds']['params']['var_model_group_id']          = "Параметр URL-а, в котором передается ID группы моделей. Позволяет выводить только DVD из группы моделей с заданным ID.";
$lang['list_dvds']['params']['var_model_group_ids']         = "Параметр URL-а, в котором передается список ID групп моделей, разделенных через запятую. Позволяет выводить только DVD из групп моделей с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те DVD, которые одновременно принадлежат всем перечисленным группам моделей.";
$lang['list_dvds']['params']['var_dvd_group_dir']           = "Параметр URL-а, в котором передается директория группы DVD. Позволяет выводить только DVD из группы с заданной директорией.";
$lang['list_dvds']['params']['var_dvd_group_id']            = "Параметр URL-а, в котором передается ID группы DVD. Позволяет выводить только DVD из группы с заданным ID.";
$lang['list_dvds']['params']['var_dvd_group_ids']           = "Параметр URL-а, в котором передается список ID групп DVD, разделенных через запятую. Позволяет выводить только DVD из групп с заданными ID.";
$lang['list_dvds']['params']['var_search']                  = "Параметр URL-а, в котором передается поисковая строка. Позволяет выводить только DVD, которые соответствуют поисковой строке.";
$lang['list_dvds']['params']['search_method']               = "Устанавливает метод поиска.";
$lang['list_dvds']['params']['search_scope']                = "Указывает, по каким полям должен идти поиск.";
$lang['list_dvds']['params']['search_redirect_enabled']     = "Включает редирект на страницу DVD, если результаты поиска содержат только 1 DVD.";
$lang['list_dvds']['params']['search_redirect_pattern']     = "Паттерн страницы DVD, на которую нужно перенаправлять пользователя, если результаты поиска содержат только 1 DVD (в этом случае пользователь будет мгновенно перенаправлен на страницу этого DVD). Паттерн должен содержать как минимум один из токенов: [kt|b]%ID%[/kt|b] и / или [kt|b]%DIR%[/kt|b].";
$lang['list_dvds']['params']['mode_created']                = "Включает режим отображения DVD, созданных пользователем. Вы можете также включить параметр [kt|b]var_user_id[/kt|b], чтобы указать пользователя, для которого выводить список; в противном случае список созданных DVD будет выводиться для текущего залогиненного пользователя.";
$lang['list_dvds']['params']['mode_uploadable']             = "Включает режим отображения DVD, в которые пользователь может загружать видео. Вы можете также включить параметр [kt|b]var_user_id[/kt|b], чтобы указать пользователя, для которого выводить список; в противном случае список DVD, доступных для загрузки, будет выводиться для текущего залогиненного пользователя.";
$lang['list_dvds']['params']['var_user_id']                 = "Параметр URL-а, в котором передается ID пользователя для выбранного режима отображения.";
$lang['list_dvds']['params']['redirect_unknown_user_to']    = "Указывает URL, на который будет перенаправлен незалогиненный пользователь при попытке доступа к режиму отображения, доступному только для залогиненных пользователей.";
$lang['list_dvds']['params']['allow_delete_created_dvds']   = "Разрешает пользователям удалять свои созданные DVD в режиме отображения [kt|b]mode_created[/kt|b].";
$lang['list_dvds']['params']['show_categories_info']        = "Включает выборку данных о категориях для каждого DVD. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds']['params']['show_tags_info']              = "Включает выборку данных о тэгах для каждого DVD. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds']['params']['show_models_info']            = "Включает выборку данных о моделях для каждого DVD. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds']['params']['show_group_info']             = "Включает выборку данных о группе для каждого DVD. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds']['params']['show_user_info']              = "Включает выборку данных о пользователе для каждого DVD. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds']['params']['pull_videos']                 = "Включает возможность выборки списка видео для каждого DVD. Количество и сортировка списка видео настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_dvds']['params']['pull_videos_count']           = "Указывает кол-во видео, которое выбирается для каждого DVD.";
$lang['list_dvds']['params']['pull_videos_sort_by']         = "Указывает сортировку для видео, которые выбираются для каждого DVD.";

$lang['list_dvds']['values']['search_method']['1']                          = "Полное совпадение с запросом";
$lang['list_dvds']['values']['search_method']['2']                          = "Совпадение с элементами запроса";
$lang['list_dvds']['values']['search_scope']['0']                           = "Название и описание";
$lang['list_dvds']['values']['search_scope']['1']                           = "Только название";
$lang['list_dvds']['values']['allow_delete_created_dvds']['0']              = "Удаление запрещено";
$lang['list_dvds']['values']['allow_delete_created_dvds']['1']              = "Удаление разрешено без видео";
$lang['list_dvds']['values']['allow_delete_created_dvds']['2']              = "Удаление разрешено вместе со всеми видео";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_id']             = "ID видео";
$lang['list_dvds']['values']['pull_videos_sort_by']['dvd_sort_id']          = "ID сортировки в DVD";
$lang['list_dvds']['values']['pull_videos_sort_by']['title']                = "Название";
$lang['list_dvds']['values']['pull_videos_sort_by']['duration']             = "Длительность";
$lang['list_dvds']['values']['pull_videos_sort_by']['post_date']            = "Дата публикации";
$lang['list_dvds']['values']['pull_videos_sort_by']['last_time_view_date']  = "Последний просмотр";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating']               = "Суммарный рейтинг";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating_today']         = "Рейтинг за сегодня";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating_week']          = "Рейтинг за неделю";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating_month']         = "Рейтинг за месяц";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed']         = "Суммарная популярность";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed_today']   = "Популярность за сегодня";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed_week']    = "Популярность за неделю";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed_month']   = "Популярность за месяц";
$lang['list_dvds']['values']['pull_videos_sort_by']['most_favourited']      = "Добавление в закладки";
$lang['list_dvds']['values']['pull_videos_sort_by']['most_commented']       = "Кол-во комментариев";
$lang['list_dvds']['values']['pull_videos_sort_by']['ctr']                  = "CTR (ротатор)";
$lang['list_dvds']['values']['pull_videos_sort_by']['rand()']               = "Случайно";
$lang['list_dvds']['values']['sort_by']['dvd_id']                           = "ID DVD";
$lang['list_dvds']['values']['sort_by']['sort_id']                          = "ID сортировки";
$lang['list_dvds']['values']['sort_by']['title']                            = "Название";
$lang['list_dvds']['values']['sort_by']['rating']                           = "Рейтинг";
$lang['list_dvds']['values']['sort_by']['dvd_viewed']                       = "Популярность";
$lang['list_dvds']['values']['sort_by']['today_videos']                     = "Кол-во видео сегодня";
$lang['list_dvds']['values']['sort_by']['total_videos']                     = "Кол-во видео";
$lang['list_dvds']['values']['sort_by']['total_videos_duration']            = "Длительность видео";
$lang['list_dvds']['values']['sort_by']['avg_videos_rating']                = "Средний рейтинг видео";
$lang['list_dvds']['values']['sort_by']['avg_videos_popularity']            = "Средняя популярность видео";
$lang['list_dvds']['values']['sort_by']['comments_count']                   = "Кол-во комментариев";
$lang['list_dvds']['values']['sort_by']['subscribers_count']                = "Кол-во подписок";
$lang['list_dvds']['values']['sort_by']['last_content_date']                = "Дата последнего добавления контента";
$lang['list_dvds']['values']['sort_by']['added_date']                       = "Дата создания";
$lang['list_dvds']['values']['sort_by']['rand()']                           = "Случайно";

$lang['list_dvds']['block_short_desc'] = "Выводит список DVD / каналов / ТВ сезонов с заданными опциями";

$lang['list_dvds']['block_desc'] = "
	Блок предназначен для отображения списка DVD / каналов / ТВ сезонов с различными опциями сортировки и фильтрации.
	Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	[kt|b]Режимы отображения[/kt|b]
	[kt|br][kt|br]

	1) [kt|b]По умолчанию[/kt|b]. Этот режим позволяет вывести просто список DVD объектов. Он работает по умолчанию.
	[kt|br][kt|br]

	2) [kt|b]Созданные DVD[/kt|b]. Этот режим позволяет вывести DVD, которые были созданы пользователем. Если
	отображаются созданные DVD текущего залогиненного пользователя, данный режим также позволяет удалить любые
	созданные DVD, если эта операция разрешена параметром [kt|b]allow_delete_created_dvds[/kt|b].
	[kt|br][kt|br]

	3) [kt|b]DVD доступные для загрузки[/kt|b]. Если вы используете DVD в качестве каналов, пользователи могут грузить
	видео в свои каналы, каналы своих друзей или публичные каналы. Используйте этот режим, чтобы вывести для
	пользователя список DVD, доступные ему для загрузки видео.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_list_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_mode_specific']}
";

$lang['list_dvds']['block_examples'] = "
	[kt|b]Показать все DVD по алфавиту[/kt|b]
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

	[kt|b]Показать все DVD из группы с директорией 'my-group'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_dvd_group_dir = group[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?group=my-group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать только DVD с видео, по 10 на страницу и сортировкой по кол-ву видео в них[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = total_videos desc[kt|br]
	- show_only_with_videos[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все DVD с названием, которое начинается на букву 'a'[/kt|b]
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

	[kt|b]Показать 15 DVD в категории с директорией 'my_category' и сортировкой по алфавиту[/kt|b]
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

	[kt|b]Показать по 10 DVD на страницу с 5-ю топовыми видео для каждого DVD по рейтингу[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- pull_videos[kt|br]
	- pull_videos_count = 5[kt|br]
	- pull_videos_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать свои созданные DVD по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_created[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
