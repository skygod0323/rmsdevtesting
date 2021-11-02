<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_playlists messages
// =====================================================================================================================

$lang['list_playlists']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_playlists']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_playlists']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_playlists']['groups']['dynamic_filters']    = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_playlists']['groups']['display_modes']      = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_playlists']['groups']['related']            = "Плэйлисты, в которых содержится указанное видео";
$lang['list_playlists']['groups']['subselects']         = "Выборка дополнительных данных для каждого плэйлиста";
$lang['list_playlists']['groups']['pull_videos']        = "Выборка видео для каждого плэйлиста";

$lang['list_playlists']['params']['items_per_page']             = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_playlists']['params']['links_per_page']             = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_playlists']['params']['var_from']                   = $lang['website_ui']['parameter_default_var_from'];
$lang['list_playlists']['params']['var_items_per_page']         = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_playlists']['params']['sort_by']                    = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_playlists']['params']['var_sort_by']                = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_playlists']['params']['is_private']                 = "Позволяет выводить плэйлисты с различной доступностью.";
$lang['list_playlists']['params']['show_only_with_description'] = "Позволяет выводить только плэйлисты, у которых задано не пустое описание.";
$lang['list_playlists']['params']['show_only_with_videos']      = "Используется для показа только тех плэйлистов, в которых есть заданное кол-во видео.";
$lang['list_playlists']['params']['var_title_section']          = "HTTP параметр, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_playlists']['params']['var_category_dir']           = "HTTP параметр, в котором передается директория категории. Позволяет выводить только плэйлисты из категории с заданной директорией.";
$lang['list_playlists']['params']['var_category_id']            = "HTTP параметр, в котором передается ID категории. Позволяет выводить только плэйлисты из категории с заданным ID.";
$lang['list_playlists']['params']['var_category_ids']           = "HTTP параметр, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только плэйлисты из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те плэйлисты, которые одновременно принадлежат всем перечисленным категориям.";
$lang['list_playlists']['params']['var_category_group_dir']     = "HTTP параметр, в котором передается директория группы категорий. Позволяет выводить только плэйлисты из группы категорий с заданной директорией.";
$lang['list_playlists']['params']['var_category_group_id']      = "HTTP параметр, в котором передается ID группы категорий. Позволяет выводить только плэйлисты из группы категорий с заданным ID.";
$lang['list_playlists']['params']['var_category_group_ids']     = "HTTP параметр, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только плэйлисты из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те плэйлисты, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['list_playlists']['params']['var_tag_dir']                = "HTTP параметр, в котором передается директория тэга. Позволяет выводить только плэйлисты, у которых есть тэг с заданной директорией.";
$lang['list_playlists']['params']['var_tag_id']                 = "HTTP параметр, в котором передается ID тэга. Позволяет выводить только плэйлисты, у которых есть тэг с заданным ID.";
$lang['list_playlists']['params']['var_tag_ids']                = "HTTP параметр, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только плэйлисты у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те плэйлисты, у которых одновременно есть все перечисленные тэги.";
$lang['list_playlists']['params']['var_is_private']             = "Позволяет выводить плэйлисты с различной доступностью базируясь на значении в данном HTTP параметре (1 - личные плэйлисты и 0 - публичные плэйлисты). Перекрывает параметр is_private.";
$lang['list_playlists']['params']['mode_global']                = "Включает режим отображения глобальных плэйлистов.";
$lang['list_playlists']['params']['mode_related_video']         = "Включает режим отображения связанных с выбранным видео плэйлистов.";
$lang['list_playlists']['params']['var_related_video_dir']      = "HTTP параметр, в котором передается директория видео для отображения связанных с ним плэйлистов.";
$lang['list_playlists']['params']['var_related_video_id']       = "HTTP параметр, в котором передается ID видео для отображения связанных с ним плэйлистов.";
$lang['list_playlists']['params']['var_user_id']                = "HTTP параметр, в котором передается ID пользователя, чьи плэйлисты должны быть выведены. Если не задан, то выводятся плэйлисты текущего пользователя.";
$lang['list_playlists']['params']['redirect_unknown_user_to']   = "Указывает путь, на который будет перенаправлен незалогиненный пользователь при попытке доступа к своим личным плэйлистам (в большинстве случаев это путь на страницу с формой логина).";
$lang['list_playlists']['params']['show_categories_info']       = "Включает выборку данных о категориях для каждого плэйлиста (работает медленнее).";
$lang['list_playlists']['params']['show_tags_info']             = "Включает выборку данных о тэгах для каждого плэйлиста (работает медленнее).";
$lang['list_playlists']['params']['show_user_info']             = "Включает выборку данных о пользователе для каждого плэйлиста (работает медленнее).";
$lang['list_playlists']['params']['show_flags_info']            = "Включает выборку данных о флагах для каждого плэйлиста (работает медленнее).";
$lang['list_playlists']['params']['show_comments']              = "Включает возможность выборки списка комментариев для каждого плэйлиста. Количество комментариев настраивается отдельным параметром блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_playlists']['params']['show_comments_count']        = "Может использоваться при включенном параметре блока [kt|b]show_comments[/kt|b]. Указывает кол-во комментариев, которое выбирается для каждого плэйлиста.";
$lang['list_playlists']['params']['pull_videos']                = "Включает возможность выборки списка видео для каждого плэйлиста. Количество и сортировка списка видео настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_playlists']['params']['pull_videos_count']          = "Указывает кол-во видео, которое выбирается для каждого плэйлиста.";
$lang['list_playlists']['params']['pull_videos_sort_by']        = "Указывает сортировку для видео, которые выбираются для каждого плэйлиста.";

$lang['list_playlists']['values']['is_private']['0']                        = "Только публичные";
$lang['list_playlists']['values']['is_private']['1']                        = "Только личные";
$lang['list_playlists']['values']['pull_videos_sort_by']['rating']          = "Рейтинг";
$lang['list_playlists']['values']['pull_videos_sort_by']['video_viewed']    = "Популярность";
$lang['list_playlists']['values']['pull_videos_sort_by']['most_favourited'] = "Добавление в закладки";
$lang['list_playlists']['values']['pull_videos_sort_by']['most_commented']  = "Кол-во комментариев";
$lang['list_playlists']['values']['pull_videos_sort_by']['added2fav_date']  = "Дата добавления в закладки";
$lang['list_playlists']['values']['pull_videos_sort_by']['rand()']          = "Случайно";
$lang['list_playlists']['values']['sort_by']['playlist_id']                 = "ID плэйлиста";
$lang['list_playlists']['values']['sort_by']['title']                       = "Название";
$lang['list_playlists']['values']['sort_by']['total_videos']                = "Кол-во видео";
$lang['list_playlists']['values']['sort_by']['rating']                      = "Рейтинг";
$lang['list_playlists']['values']['sort_by']['playlist_viewed']             = "Популярность";
$lang['list_playlists']['values']['sort_by']['most_commented']              = "Кол-во комментариев";
$lang['list_playlists']['values']['sort_by']['subscribers_count']           = "Кол-во подписок";
$lang['list_playlists']['values']['sort_by']['last_content_date']           = "Дата последнего добавления контента";
$lang['list_playlists']['values']['sort_by']['added_date']                  = "Дата создания";
$lang['list_playlists']['values']['sort_by']['rand()']                      = "Случайно";

$lang['list_playlists']['block_short_desc'] = "Выводит список плэйлистов с заданными опциями";

$lang['list_playlists']['block_desc'] = "
	Блок предназначен для отображения списка плэйлистов с различными опциями сортировки и фильтрации. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Существует 3 различных типа листинга плэйлистов:[kt|br]
	1) Список глобальных плэйлистов. Для получения данного списка должен быть включен параметр блока
	   [kt|b]mode_global[/kt|b].[kt|br]
	2) Список плэйлистов, связанных с выбранным видео. Для получения данного списка должен быть включен параметр блока
	   [kt|b]mode_related_video[/kt|b], а также один из параметров [kt|b]var_related_video_id[/kt|b] или
	   [kt|b]var_related_video_dir[/kt|b].[kt|br]
	3) Список плэйлистов пользователя. Если включен параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит список
	   плэйлистов пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
	   [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список плэйлистов текущего пользователя
	   ('мои плэйлисты'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
	   [kt|b]redirect_unknown_user_to[/kt|b]. В данном режиме списка существует также возможность удаления своих
	   плэйлистов.
	[kt|br][kt|br]

	Если вы хотите показать только плэйлисты с непустым описанием, то включите параметр блока
	[kt|b]show_only_with_description[/kt|b].
	[kt|br][kt|br]

	Для того, чтобы вывести плэйлисты по какой-либо категории (тэгу), следует использовать один из параметров блока
	[kt|b]var_xxx_dir[/kt|b] или [kt|b]var_xxx_id[/kt|b].
	[kt|br][kt|br]

	Вы можете настроить блок таким образом, чтобы он выводил только плэйлисты, название которых начинается с указанной
	подстроки (или буквы). Для этого параметр блока [kt|b]var_title_section[/kt|b] должен ссылаться на HTTP параметр,
	который содержит комбинацию первых символов. Регистр букв в комбинации не имеет значения. Так вы можете показывать
	отдельный список плэйлистов для каждой буквы алфавита.
	[kt|br][kt|br]

	При необходимости блок позволяет включить выборку видео для плэйлистов по определенному критерию (сортировке),
	чтобы показать несколько видео для каждого плэйлиста. Для этого вам необходимо включить параметр блока
	[kt|b]pull_videos[/kt|b], а также указать кол-во видео и сортировку в параметрах блока
	[kt|b]pull_videos_count[/kt|b] и [kt|b]pull_videos_sort_by[/kt|b]. Обратите внимание на то, что дополнительная
	выборка видео ухудшит производительность блока, и вам, возможно, придется увеличить время кэширования.
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок может быть закэширован на длительный промежуток времени. Для всех пользователей будет использоваться одна и та
	же версия кэша. Блок не кэшируется, когда отображает список своих плэйлистов для текущего пользователя.
";

$lang['list_playlists']['block_examples'] = "
	[kt|b]Показать все плэйлисты пользователя с ID '287'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать свои плэйлисты по 10 на странице с 4 наиболее рейтинговыми видео для каждого плэйлиста[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	- pull_videos[kt|br]
	- pull_videos_count = 4[kt|br]
	- pull_videos_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать топ 10 плэйлистов, в которых есть видео с ID '234'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = rating desc[kt|br]
	- mode_related_video = true[kt|br]
	- var_related_video_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=234
	[/kt|code]
";

?>