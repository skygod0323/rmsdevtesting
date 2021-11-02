<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// search_results messages
// =====================================================================================================================

$lang['search_results']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['search_results']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['search_results']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['search_results']['groups']['search']             = "Похожие запросы";
$lang['search_results']['groups']['sizes']              = "Режим облака тэгов";

$lang['search_results']['params']['items_per_page']         = $lang['website_ui']['parameter_default_items_per_page'];
$lang['search_results']['params']['links_per_page']         = $lang['website_ui']['parameter_default_links_per_page'];
$lang['search_results']['params']['var_from']               = $lang['website_ui']['parameter_default_var_from'];
$lang['search_results']['params']['var_items_per_page']     = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['search_results']['params']['sort_by']                = $lang['website_ui']['parameter_default_sort_by'];
$lang['search_results']['params']['var_sort_by']            = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['search_results']['params']['days']                   = "Позволяет выбирать только поисковые запросы, сделанные за указанное количество дней.";
$lang['search_results']['params']['query_length_min']       = "Ограничивает минимальную длину поисковых запросов, которые отображаются блоком (включительно).";
$lang['search_results']['params']['query_length_max']       = "Ограничивает максимальную длину поисковых запросов, которые отображаются блоком (включительно).";
$lang['search_results']['params']['query_results_min']      = "Отображает только те поисковые запросы, которые нашли как минимум заданное число результатов (включительно).";
$lang['search_results']['params']['query_results_min_type'] = "Ограничивает область применения параметра [kt|b]query_results_min[/kt|b] только видео, альбомами или всем вместе.";
$lang['search_results']['params']['query_amount_min']       = "Позволяет выбирать только поисковые запросы, частота которых достигла и превысила указанное значение (включительно).";
$lang['search_results']['params']['var_query']              = "Параметр URL-а, в котором передается запрос. Позволяет выбрать похожие запросы к указанному запросу.";
$lang['search_results']['params']['var_category_id']        = "Параметр URL-а, в котором передается ID категории. Позволяет выбрать похожие запросы к категории с указанным ID.";
$lang['search_results']['params']['var_category_dir']       = "Параметр URL-а, в котором передается директория категории. Позволяет выбрать похожие запросы к категории с указанной директорией.";
$lang['search_results']['params']['var_tag_id']             = "Параметр URL-а, в котором передается ID тэга. Позволяет выбрать похожие запросы к тэгу с указанным ID.";
$lang['search_results']['params']['var_tag_dir']            = "Параметр URL-а, в котором передается директория тэга. Позволяет выбрать похожие запросы к тэгу с указанной директорией.";
$lang['search_results']['params']['search_method']          = "Устанавливает метод поиска похожих запросов.";
$lang['search_results']['params']['sort_by_relevance']      = "Форсирует сортировку запросов по степени похожести, чтобы вывести наиболее похожие запросы в начале списка. Работает только в случае использования полнотекстового индекса в параметре [kt|b]search_method[/kt|b].";
$lang['search_results']['params']['size_from']              = "Размер шрифта в пикселах для запроса с наименьшим кол-вом найденных результатов.";
$lang['search_results']['params']['size_to']                = "Размер шрифта в пикселах для запроса с наибольшим кол-вом найденных результатов.";
$lang['search_results']['params']['bold_from']              = "Указывает, начиная с какого размера шрифта запрос должен выделяться жирным шрифтом.";

$lang['search_results']['values']['query_results_min_type']['0']        = "Видео и альбомы";
$lang['search_results']['values']['query_results_min_type']['1']        = "Только видео";
$lang['search_results']['values']['query_results_min_type']['2']        = "Только альбомы";
$lang['search_results']['values']['search_method']['1']                 = "Полное совпадение с запросом";
$lang['search_results']['values']['search_method']['2']                 = "Совпадение с элементами запроса";
$lang['search_results']['values']['search_method']['3']                 = "Полнотекстовый индекс (натуральный режим)";
$lang['search_results']['values']['search_method']['4']                 = "Полнотекстовый индекс (булевый режим)";
$lang['search_results']['values']['search_method']['5']                 = "Полнотекстовый индекс (с расширенным подзапросом)";
$lang['search_results']['values']['sort_by']['query']                   = "Текст запроса";
$lang['search_results']['values']['sort_by']['amount']                  = "Кол-во запросов";
$lang['search_results']['values']['sort_by']['query_results_total']     = "Кол-во найденных результатов";
$lang['search_results']['values']['sort_by']['query_results_videos']    = "Кол-во найденных видео";
$lang['search_results']['values']['sort_by']['query_results_albums']    = "Кол-во найденных альбомов";
$lang['search_results']['values']['sort_by']['pseudo_rand']             = "Псевдослучайно (быстро)";
$lang['search_results']['values']['sort_by']['rand()']                  = "Случайно (медленно)";

$lang['search_results']['block_short_desc'] = "Выводит список поисковых запросов, выполненных пользователями сайта";

$lang['search_results']['block_desc'] = "
	Блок предназначен для отображения списка поисковых запросов, которые были выполнены пользователями сайта. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	[kt|b]Похожие запросы[/kt|b]
	[kt|br][kt|br]

	Этот блок предоставляет мощный функционал для SEO оптимизации путем вывода на странице похожих поисковых запросов
	к любой строке или категории / тэгу.
	[kt|br][kt|br]

	[kt|b]Режим облака тэгов[/kt|b]
	[kt|br][kt|br]

	Облако тэгов отображается как список запросов, в котором все запросы имеют разные размеры в зависимости от кол-ва
	найденных ими результатов так, что запросы с наибольшим числом объектов имеют большие размеры. Параметры этого
	раздела позволяют задать абсолютные значения шрифтов для отображения.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['search_results']['block_examples'] = "
	[kt|b]Показать 10 наиболее популярных поисковых запросов[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 запросов, похожие на запрос 'cars'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_search = q[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?q=cars
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 запросов, похожие на категорию с директорией 'auto'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=auto
	[/kt|code]
";
