<?php

$lang['awebl_list_webcams']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['awebl_list_webcams']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['awebl_list_webcams']['groups']['dynamic_filters']    = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['awebl_list_webcams']['groups']['related']            = "Похожие вебкамы";

$lang['awebl_list_webcams']['params']['items_per_page']     = $lang['website_ui']['parameter_default_items_per_page'];
$lang['awebl_list_webcams']['params']['var_list_page_id']   = $lang['website_ui']['parameter_default_var_from'];
$lang['awebl_list_webcams']['params']['var_items_per_page'] = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['awebl_list_webcams']['params']['niche']              = "Используется для показа вебкамов из выбранной ниши.";
$lang['awebl_list_webcams']['params']['var_niche']          = "Параметр URL-а, в котором передается ниша вебкамов. Позволяет выводить только вебкамы из заданной ниши. Поддерживает такие ниши: [kt|b]girls[/kt|b], [kt|b]boys[/kt|b], [kt|b]tranny[/kt|b] и [kt|b]celebrity[/kt|b].";
$lang['awebl_list_webcams']['params']['var_search']         = "Параметр URL-а, в котором передается поисковая строка. Позволяет выводить только вебкамы, которые соответствуют поисковой строке.";
$lang['awebl_list_webcams']['params']['mode_related']       = "Включает режим отображения похожих вебкамов.";
$lang['awebl_list_webcams']['params']['var_webcam_dir']     = "Параметр URL-а, в котором передается директория вебкама для отображения похожих вебкамов.";

$lang['awebl_list_webcams']['values']['niche']['girls']     = "Девушки";
$lang['awebl_list_webcams']['values']['niche']['boys']      = "Мальчики";
$lang['awebl_list_webcams']['values']['niche']['tranny']    = "Трансы";
$lang['awebl_list_webcams']['values']['niche']['celebrity'] = "Знаменитости";

$lang['awebl_list_webcams']['block_short_desc'] = "Выводит список вебкамов с заданными опциями";

$lang['awebl_list_webcams']['block_desc'] = "
	Блок предназначен для отображения списка вебкамов с различными опциями фильтрации. Этот блок поддерживает пагинацию
	только в режиме 'показать еще'.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['awebl_list_webcams']['block_examples'] = "
";