<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['custom_post_processing']['title']          = "Кастомная пост-обработка";
$lang['plugins']['custom_post_processing']['description']    = "Вы можете добавить свою собственную логику пост-обработки видео и альбомов в код этого плагина.";
$lang['plugins']['custom_post_processing']['long_desc']      = "
		Этот плагин позволяет добавить кастомную логику пост-обработки видео и альбомов, которая вызовется после
		завершения обработки каждого нового видео или альбома. Для программирования логики вам необходимо поместить свой
		код в файл [kt|b]/admin/plugins/custom_post_processing/custom_post_processing.php[/kt|b] в обозначенные места
		по видео или альбомам, а также изменить функцию [kt|b]custom_post_processingIsEnabled[/kt|b] в этом же файле,
		чтобы она возвращала true.
		[kt|br][kt|br]
		Если вам необходимо, чтобы эта логика выполнилась для уже существующих видео или альбомов, воспользуйтесь
		массовым редактированием - оно позволит запустить этот плагин вручную для выбранного набора контента.
";
$lang['permissions']['plugins|custom_post_processing']   = $lang['plugins']['custom_post_processing']['title'];
