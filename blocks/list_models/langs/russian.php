<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_models messages
// =====================================================================================================================

$lang['list_models']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_models']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_models']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_models']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_models']['groups']['search']            = "Текстовый поиск моделей";
$lang['list_models']['groups']['related']           = "Похожие модели";
$lang['list_models']['groups']['subselects']        = "Выборка дополнительных данных для каждой модели";
$lang['list_models']['groups']['pull_videos']       = "Выборка видео для каждой модели";
$lang['list_models']['groups']['pull_albums']       = "Выборка альбомов для каждой модели";

$lang['list_models']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_models']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_models']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['list_models']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_models']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_models']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_models']['params']['show_only_with_screenshot1']        = "Используется для показа только тех моделей, у которых задан скриншот #1.";
$lang['list_models']['params']['show_only_with_screenshot2']        = "Используется для показа только тех моделей, у которых задан скриншот #2.";
$lang['list_models']['params']['show_only_with_description']        = "Используется для показа только тех моделей, у которых задано не пустое описание.";
$lang['list_models']['params']['show_only_with_videos']             = "Используется для показа только тех моделей, для которых есть заданное кол-во видео.";
$lang['list_models']['params']['show_only_with_albums']             = "Используется для показа только тех моделей, для которых есть заданное кол-во фотоальбомов.";
$lang['list_models']['params']['show_only_with_posts']              = "Используется для показа только тех моделей, для которых есть заданное кол-во записей.";
$lang['list_models']['params']['show_only_with_albums_or_videos']   = "Используется для показа только тех моделей, для которых есть заданное кол-во видео или фотоальбомов.";
$lang['list_models']['params']['show_gender']                       = "Используется для показа только моделей указанного пола.";
$lang['list_models']['params']['skip_model_groups']                 = "Позволяет не выводить модели из данных групп моделей (список ID групп моделей разделенных через запятую).";
$lang['list_models']['params']['show_model_groups']                 = "Позволяет выводить только модели из данных групп моделей (список ID групп моделей разделенных через запятую).";
$lang['list_models']['params']['skip_categories']                   = "Позволяет не выводить моделей из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_models']['params']['show_categories']                   = "Позволяет выводить только моделей из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_models']['params']['skip_tags']                         = "Позволяет не выводить моделей с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_models']['params']['show_tags']                         = "Позволяет выводить только моделей с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_models']['params']['var_title_section']                 = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_models']['params']['var_model_group_dir']               = "Параметр URL-а, в котором передается директория группы моделей. Позволяет выводить только моделей из группы с заданной директорией.";
$lang['list_models']['params']['var_model_group_id']                = "Параметр URL-а, в котором передается ID группы моделей. Позволяет выводить только моделей из группы с заданным ID.";
$lang['list_models']['params']['var_model_group_ids']               = "Параметр URL-а, в котором передается список ID групп моделей, разделенных через запятую. Позволяет выводить только моделей из групп с заданными ID.";
$lang['list_models']['params']['var_category_dir']                  = "Параметр URL-а, в котором передается директория категории. Позволяет выводить только моделей из категории с заданной директорией.";
$lang['list_models']['params']['var_category_id']                   = "Параметр URL-а, в котором передается ID категории. Позволяет выводить только моделей из категории с заданным ID.";
$lang['list_models']['params']['var_category_ids']                  = "Параметр URL-а, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только моделей из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те модели, которые одновременно принадлежат всем перечисленным категориям.";
$lang['list_models']['params']['var_category_group_dir']            = "Параметр URL-а, в котором передается директория группы категорий. Позволяет выводить только моделей из группы категорий с заданной директорией.";
$lang['list_models']['params']['var_category_group_id']             = "Параметр URL-а, в котором передается ID группы категорий. Позволяет выводить только моделей из группы категорий с заданным ID.";
$lang['list_models']['params']['var_category_group_ids']            = "Параметр URL-а, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только моделей из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те модели, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['list_models']['params']['var_tag_dir']                       = "Параметр URL-а, в котором передается директория тэга. Позволяет выводить только моделей, у которых есть тэг с заданной директорией.";
$lang['list_models']['params']['var_tag_id']                        = "Параметр URL-а, в котором передается ID тэга. Позволяет выводить только моделей, у которых есть тэг с заданным ID.";
$lang['list_models']['params']['var_tag_ids']                       = "Параметр URL-а, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только моделей, у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те модели, у которых одновременно есть все перечисленные тэги.";
$lang['list_models']['params']['var_country_id']                    = "Параметр URL-а, в котором передается идентификатор страны модели для фильтрации списка по стране.";
$lang['list_models']['params']['var_state']                         = "Параметр URL-а, в котором передается название штата для фильтрации списка по штату.";
$lang['list_models']['params']['var_city']                          = "Параметр URL-а, в котором передается название города или часть названия города для фильтрации списка по городу.";
$lang['list_models']['params']['var_hair_id']                       = "Параметр URL-а, в котором передается идентификатор цвета волос модели для фильтрации списка по цвету волос ([kt|b]1[/kt|b] - черные, [kt|b]2[/kt|b] - темные, [kt|b]3[/kt|b] - рыжие, [kt|b]4[/kt|b] - коричневые, [kt|b]5[/kt|b] - светлые, [kt|b]6[/kt|b] - серые / седые, [kt|b]7[/kt|b] - нет волос, [kt|b]8[/kt|b] - парик).";
$lang['list_models']['params']['var_eye_color_id']                  = "Параметр URL-а, в котором передается идентификатор цвета глаз модели для фильтрации списка по цвету глаз ([kt|b]1[/kt|b] - голубые, [kt|b]2[/kt|b] - серые, [kt|b]3[/kt|b] - зеленые, [kt|b]4[/kt|b] - янтарные, [kt|b]5[/kt|b] - карие, [kt|b]6[/kt|b] - болотные, [kt|b]7[/kt|b] - черные).";
$lang['list_models']['params']['var_gender_id']                     = "Параметр URL-а, в котором передается идентификатор пола модели для фильтрации списка по полу ([kt|b]0[/kt|b] - женский, [kt|b]1[/kt|b] - мужской, [kt|b]2[/kt|b] - другой).";
$lang['list_models']['params']['var_age_from']                      = "Параметр URL-а, который позволяет вывести моделей с возрастом большим или равным чем цифра, которая передается в данном параметре (кол-во лет).";
$lang['list_models']['params']['var_age_to']                        = "Параметр URL-а, который позволяет вывести моделей с возрастом меньшим чем цифра, которая передается в данном параметре (кол-во лет).";
$lang['list_models']['params']['var_custom1']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 1.";
$lang['list_models']['params']['var_custom2']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 2.";
$lang['list_models']['params']['var_custom3']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 3.";
$lang['list_models']['params']['var_custom4']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 4.";
$lang['list_models']['params']['var_custom5']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 5.";
$lang['list_models']['params']['var_custom6']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 6.";
$lang['list_models']['params']['var_custom7']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 7.";
$lang['list_models']['params']['var_custom8']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 8.";
$lang['list_models']['params']['var_custom9']                       = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 9.";
$lang['list_models']['params']['var_custom10']                      = "HTTP параметр, в котором передается значение для динамического фильтра по доп. полю 10.";
$lang['list_models']['params']['var_search']                        = "Параметр URL-а, в котором передается поисковая строка. Позволяет выводить только модели, которые соответствуют поисковой строке.";
$lang['list_models']['params']['search_method']                     = "Устанавливает метод поиска.";
$lang['list_models']['params']['search_scope']                      = "Указывает, по каким полям должен идти поиск.";
$lang['list_models']['params']['search_redirect_enabled']           = "Включает редирект на страницу модели, если результаты поиска содержат только 1 модель.";
$lang['list_models']['params']['search_redirect_pattern']           = "Паттерн страницы модели, на которую нужно перенаправлять пользователя, если результаты поиска содержат только 1 модель (в этом случае пользователь будет мгновенно перенаправлен на страницу этой модели). Паттерн должен содержать как минимум один из токенов: [kt|b]%ID%[/kt|b] и / или [kt|b]%DIR%[/kt|b].";
$lang['list_models']['params']['mode_related']                      = "Включает режим отображения похожих моделей.";
$lang['list_models']['params']['var_model_dir']                     = "Параметр URL-а, в котором передается директория модели для отображения похожих на нее моделей.";
$lang['list_models']['params']['var_model_id']                      = "Параметр URL-а, в котором передается ID модели для отображения похожих на нее моделей.";
$lang['list_models']['params']['var_mode_related']                  = "Позволяет динамически переключать режим отображения похожих моделей, передавая одно из значений в URL-е: [kt|b]1[/kt|b] - по тэгам, [kt|b]2[/kt|b] - по категориям, [kt|b]3[/kt|b] - по стране, [kt|b]4[/kt|b] - по городу, [kt|b]5[/kt|b] - по полу, [kt|b]6[/kt|b] - по возрасту, [kt|b]7[/kt|b] - по росту, [kt|b]8[/kt|b] - по весу, [kt|b]9[/kt|b] - по цвету волос, [kt|b]10[/kt|b] - по видео, [kt|b]11[/kt|b] - по альбомам, [kt|b]12[/kt|b] - по группе, [kt|b]13[/kt|b] - по штату.";
$lang['list_models']['params']['mode_related_category_group_id']    = "Используется при режиме похожих моделей по категориям. Укажите ID или внешний ID группы категорий, чтобы ограничить похожие модели только этой группой категорий.";
$lang['list_models']['params']['show_categories_info']              = "Включает выборку данных о категориях для каждой модели. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_models']['params']['show_tags_info']                    = "Включает выборку данных о тэгах для каждой модели. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_models']['params']['pull_videos']                       = "Включает возможность выборки списка видео для каждой модели. Количество и сортировка видео настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_models']['params']['pull_videos_count']                 = "Указывает кол-во видео, которое выбирается для каждой модели.";
$lang['list_models']['params']['pull_videos_sort_by']               = "Указывает сортировку для видео, которые выбираются для каждой модели.";
$lang['list_models']['params']['pull_videos_duplicates']            = "Включите, если хотите разрешить выбор повторяющихся видео для разных моделей.";
$lang['list_models']['params']['pull_albums']                       = "Включает возможность выборки списка фотоальбомов для каждой модели. Количество и сортировка фотоальбомов настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_models']['params']['pull_albums_count']                 = "Указывает кол-во фотоальбомов, которое выбирается для каждой модели.";
$lang['list_models']['params']['pull_albums_sort_by']               = "Указывает сортировку для фотоальбомов, которые выбираются для каждой модели.";
$lang['list_models']['params']['pull_albums_duplicates']            = "Включите, если хотите разрешить выбор повторяющихся альбомов для разных моделей.";

$lang['list_models']['values']['show_gender']['0']                              = "Женский";
$lang['list_models']['values']['show_gender']['1']                              = "Мужской";
$lang['list_models']['values']['show_gender']['2']                              = "Другой";
$lang['list_models']['values']['search_method']['1']                            = "Полное совпадение с запросом";
$lang['list_models']['values']['search_method']['2']                            = "Совпадение с элементами запроса";
$lang['list_models']['values']['search_scope']['0']                             = "Название / псевдоним и описание";
$lang['list_models']['values']['search_scope']['1']                             = "Только название / псевдоним";
$lang['list_models']['values']['mode_related']['1']                             = "Похожие по тэгам";
$lang['list_models']['values']['mode_related']['2']                             = "Похожие по категориям";
$lang['list_models']['values']['mode_related']['3']                             = "Похожие по стране";
$lang['list_models']['values']['mode_related']['4']                             = "Похожие по городу";
$lang['list_models']['values']['mode_related']['5']                             = "Похожие по полу";
$lang['list_models']['values']['mode_related']['6']                             = "Похожие по возрасту";
$lang['list_models']['values']['mode_related']['7']                             = "Похожие по росту";
$lang['list_models']['values']['mode_related']['8']                             = "Похожие по весу";
$lang['list_models']['values']['mode_related']['9']                             = "Похожие по цвету волос";
$lang['list_models']['values']['mode_related']['10']                            = "Похожие по видео";
$lang['list_models']['values']['mode_related']['11']                            = "Похожие по альбомам";
$lang['list_models']['values']['mode_related']['12']                            = "Похожие по группе";
$lang['list_models']['values']['mode_related']['13']                            = "Похожие по штату";
$lang['list_models']['values']['pull_videos_sort_by']['duration']               = "Длительность";
$lang['list_models']['values']['pull_videos_sort_by']['post_date']              = "Дата публикации";
$lang['list_models']['values']['pull_videos_sort_by']['last_time_view_date']    = "Последний просмотр";
$lang['list_models']['values']['pull_videos_sort_by']['rating']                 = "Суммарный рейтинг";
$lang['list_models']['values']['pull_videos_sort_by']['rating_today']           = "Рейтинг за сегодня";
$lang['list_models']['values']['pull_videos_sort_by']['rating_week']            = "Рейтинг за неделю";
$lang['list_models']['values']['pull_videos_sort_by']['rating_month']           = "Рейтинг за месяц";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed']           = "Суммарная популярность";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed_today']     = "Популярность за сегодня";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed_week']      = "Популярность за неделю";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed_month']     = "Популярность за месяц";
$lang['list_models']['values']['pull_videos_sort_by']['most_favourited']        = "Добавление в закладки";
$lang['list_models']['values']['pull_videos_sort_by']['most_commented']         = "Кол-во комментариев";
$lang['list_models']['values']['pull_videos_sort_by']['ctr']                    = "CTR (ротатор)";
$lang['list_models']['values']['pull_videos_sort_by']['rand()']                 = "Случайно";
$lang['list_models']['values']['pull_albums_sort_by']['photos_amount']          = "Кол-во фотографий";
$lang['list_models']['values']['pull_albums_sort_by']['post_date']              = "Дата публикации";
$lang['list_models']['values']['pull_albums_sort_by']['last_time_view_date']    = "Последний просмотр";
$lang['list_models']['values']['pull_albums_sort_by']['rating']                 = "Суммарный рейтинг";
$lang['list_models']['values']['pull_albums_sort_by']['rating_today']           = "Рейтинг за сегодня";
$lang['list_models']['values']['pull_albums_sort_by']['rating_week']            = "Рейтинг за неделю";
$lang['list_models']['values']['pull_albums_sort_by']['rating_month']           = "Рейтинг за месяц";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed']           = "Суммарная популярность";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed_today']     = "Популярность за сегодня";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed_week']      = "Популярность за неделю";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed_month']     = "Популярность за месяц";
$lang['list_models']['values']['pull_albums_sort_by']['most_favourited']        = "Добавление в закладки";
$lang['list_models']['values']['pull_albums_sort_by']['most_commented']         = "Кол-во комментариев";
$lang['list_models']['values']['pull_albums_sort_by']['rand()']                 = "Случайно";
$lang['list_models']['values']['sort_by']['model_id']                           = "ID модели";
$lang['list_models']['values']['sort_by']['sort_id']                            = "ID сортировки";
$lang['list_models']['values']['sort_by']['title']                              = "Название";
$lang['list_models']['values']['sort_by']['birth_date']                         = "Дата рождения";
$lang['list_models']['values']['sort_by']['age']                                = "Возраст";
$lang['list_models']['values']['sort_by']['rating']                             = "Рейтинг";
$lang['list_models']['values']['sort_by']['model_viewed']                       = "Популярность";
$lang['list_models']['values']['sort_by']['screenshot1']                        = "Скриншот 1";
$lang['list_models']['values']['sort_by']['screenshot2']                        = "Скриншот 2";
$lang['list_models']['values']['sort_by']['today_videos']                       = "Кол-во видео сегодня";
$lang['list_models']['values']['sort_by']['total_videos']                       = "Кол-во видео";
$lang['list_models']['values']['sort_by']['today_albums']                       = "Кол-во фотоальбомов сегодня";
$lang['list_models']['values']['sort_by']['total_albums']                       = "Кол-во фотоальбомов";
$lang['list_models']['values']['sort_by']['today_posts']                        = "Кол-во записей сегодня";
$lang['list_models']['values']['sort_by']['total_posts']                        = "Кол-во записей всего";
$lang['list_models']['values']['sort_by']['avg_videos_rating']                  = "Средний рейтинг видео";
$lang['list_models']['values']['sort_by']['avg_videos_popularity']              = "Средняя популярность видео";
$lang['list_models']['values']['sort_by']['avg_albums_rating']                  = "Средний рейтинг фотоальбомов";
$lang['list_models']['values']['sort_by']['avg_albums_popularity']              = "Средняя популярность фотоальбомов";
$lang['list_models']['values']['sort_by']['avg_posts_rating']                   = "Средний рейтинг записей";
$lang['list_models']['values']['sort_by']['avg_posts_popularity']               = "Средняя популярность записей";
$lang['list_models']['values']['sort_by']['comments_count']                     = "Кол-во комментариев";
$lang['list_models']['values']['sort_by']['subscribers_count']                  = "Кол-во подписок";
$lang['list_models']['values']['sort_by']['rank']                               = "Ранг";
$lang['list_models']['values']['sort_by']['last_content_date']                  = "Дата последнего добавления контента";
$lang['list_models']['values']['sort_by']['added_date']                         = "Дата создания";
$lang['list_models']['values']['sort_by']['rand()']                             = "Случайно";

$lang['list_models']['block_short_desc'] = "Выводит список моделей с заданными опциями";

$lang['list_models']['block_desc'] = "
	Блок предназначен для отображения списка моделей с различными опциями сортировки и фильтрации. Является стандартным
	блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	[kt|b]Похожие модели[/kt|b]
	[kt|br][kt|br]

	Вы можете настроить, чтобы блок выводил список моделей, похожих на заданную по большому набору критериев
	похожести. Для включения этого режима активируйте параметр блока [kt|b]mode_related[/kt|b] и дополнительно один из
	параметров [kt|b]var_model_dir[/kt|b] или [kt|b]var_model_id[/kt|b]. Для корректной работы этого режима в URL-е
	страницы должны передаваться либо директория, либо ID модели, для которой блок будет искать похожих:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=model-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_models']['block_examples'] = "
	[kt|b]Показать всех моделей по алфавиту[/kt|b]
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

	[kt|b]Показать только моделей с видео, по 10 на страницу и сортировкой по кол-ву видео для них[/kt|b]
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

	[kt|b]Показать всех моделей с названием, которое начинается на букву 'a'[/kt|b]
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

	[kt|b]Показать 15 моделей в категории с директорией 'my_category' и сортировкой по алфавиту[/kt|b]
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

	[kt|b]Показать всех моделей-мужчин[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_gender_id = gender_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?gender_id=1
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать по 10 моделей на страницу с 5-ю топовыми видео для каждой модели по рейтингу[/kt|b]
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

	[kt|b]Показать все модели с 10-ю топовыми фотоальбомами в каждой модели по популярности[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- pull_albums[kt|br]
	- pull_albums_count = 10[kt|br]
	- pull_albums_sort_by = album_viewed desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
