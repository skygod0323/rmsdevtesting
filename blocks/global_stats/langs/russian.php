<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// global_stats messages
// =====================================================================================================================

$lang['global_stats']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['global_stats']['groups']['performance']      = $lang['website_ui']['block_group_default_performance'];

$lang['global_stats']['params']['var_category_dir']     = "Параметр URL-а, в котором передается директория категории. Позволяет учитывать в статистике только объекты из категории с заданной директорией.";
$lang['global_stats']['params']['var_category_id']      = "Параметр URL-а, в котором передается ID категории. Позволяет учитывать в статистике только объекты из категории с заданным ID.";
$lang['global_stats']['params']['skip_videos']          = "Выключает отображение статистики по видео.";
$lang['global_stats']['params']['skip_albums']          = "Выключает отображение статистики по альбомам.";
$lang['global_stats']['params']['skip_members']         = "Выключает отображение статистики по сообществу.";
$lang['global_stats']['params']['skip_content_sources'] = "Выключает отображение статистики по контент провайдерам.";
$lang['global_stats']['params']['skip_models']          = "Выключает отображение статистики по моделям.";
$lang['global_stats']['params']['skip_dvds']            = "Выключает отображение статистики по DVD / каналам / TV сериалам.";
$lang['global_stats']['params']['skip_traffic']         = "Выключает отображение статистики по трафику";

$lang['global_stats']['block_short_desc'] = "Выводит информацию о глобальной статистике сайта";

$lang['global_stats']['block_desc'] = "
	Блок отображает агрегированную статистику по различным показателям сайта: кол-во видео, альбомов, и др.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";
