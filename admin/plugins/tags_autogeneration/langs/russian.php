<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['tags_autogeneration']['title']        = "Автоподбор тэгов";
$lang['plugins']['tags_autogeneration']['description']  = "Используется для подбора тэгов у видео и альбомов на основе названия и описания.";
$lang['plugins']['tags_autogeneration']['long_desc']    = "
		Используется для подбора тэгов у видео и альбомов на основе полей названия и описания. Для того, чтобы этот
		плагин начал работу, вам необходимо создать базу тэгов, т.к. плагин не создает новые тэги - он лишь выбирает из
		существующих тэгов. Поиск тэгов осуществляется не только по названиям, но и по всем синонимам тэгов.
";
$lang['permissions']['plugins|tags_autogeneration']     = $lang['plugins']['tags_autogeneration']['title'];

$lang['plugins']['tags_autogeneration']['field_enable_for_videos']          = "Видео";
$lang['plugins']['tags_autogeneration']['field_enable_for_videos_disabled'] = "Не обрабатывать";
$lang['plugins']['tags_autogeneration']['field_enable_for_videos_always']   = "Обрабатывать каждое видео";
$lang['plugins']['tags_autogeneration']['field_enable_for_videos_empty']    = "Обрабатывать только видео, у которых нет явно указанных тэгов";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums']          = "Альбомы";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums_disabled'] = "Не обрабатывать";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums_always']   = "Обрабатывать каждый альбом";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums_empty']    = "Обрабатывать только альбомы, у которых нет явно указанных тэгов";
$lang['plugins']['tags_autogeneration']['field_lenient']                    = "Мягкое соответствие";
$lang['plugins']['tags_autogeneration']['field_lenient_off']                = "Выключено";
$lang['plugins']['tags_autogeneration']['field_lenient_all']                = "Включено для всех сложных тэгов и синонимов";
$lang['plugins']['tags_autogeneration']['field_lenient_specific']           = "Включено для указанных тэгов и синонимов";
$lang['plugins']['tags_autogeneration']['field_lenient_hint1']              = "при включении этой опции сложные тэги из нескольких слов не будут требовать полного соответствия, вместо этого отдельные слова таких тэгов будут проверяться отдельно и тэг подойдет, если каждое его слово подходит, однако для русского языка ввиду разнообразия окончаний и форм необходимо использование синонимов; например тэг [kt|b]смешная кошка[/kt|b] с синонимом [kt|b]смешн кошк[/kt|b] будет подобран для видео с названием [kt|b]Смешное видео с кошкой и собакой[/kt|b] [kt|br] [kt|b]ВНИМАНИЕ![/kt|b] Используйте с осторожностью. Использование этой опции может выбирать тэги некорректно, т.к. мягкое соответствие не дает 100% аккуратности.";
$lang['plugins']['tags_autogeneration']['field_lenient_hint2']              = "укажите список тэгов или синонимов через запятую, для которых применять мягкое соответствие; для всех остальных сложных тэгов оно не будет использоваться";
$lang['plugins']['tags_autogeneration']['btn_save']                         = "Сохранить";
