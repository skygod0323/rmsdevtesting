<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['categories_autogeneration']['title']          = "Автоподбор категорий";
$lang['plugins']['categories_autogeneration']['description']    = "Используется для подбора категорий у видео и альбомов на основе названия, описания и тэгов.";
$lang['plugins']['categories_autogeneration']['long_desc']      = "
		Используется для подбора категорий у видео и альбомов на основе полей названия, описания и тэгов. Для того,
		чтобы этот плагин начал работу, вам необходимо создать базу категорий, т.к. плагин не создает новые категории -
		он лишь выбирает из существующих категорий. Поиск категорий осуществляется не только по названиям, но и по всем
		синонимам категорий.
";
$lang['permissions']['plugins|categories_autogeneration']   = $lang['plugins']['categories_autogeneration']['title'];

$lang['plugins']['categories_autogeneration']['field_enable_for_videos']            = "Видео";
$lang['plugins']['categories_autogeneration']['field_enable_for_videos_disabled']   = "Не обрабатывать";
$lang['plugins']['categories_autogeneration']['field_enable_for_videos_always']     = "Обрабатывать каждое видео";
$lang['plugins']['categories_autogeneration']['field_enable_for_videos_empty']      = "Обрабатывать только видео, у которых нет явно указанных категорий";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums']            = "Альбомы";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums_disabled']   = "Не обрабатывать";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums_always']     = "Обрабатывать каждый альбом";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums_empty']      = "Обрабатывать только альбомы, у которых нет явно указанных категорий";
$lang['plugins']['categories_autogeneration']['field_lenient']                      = "Мягкое соответствие";
$lang['plugins']['categories_autogeneration']['field_lenient_off']                  = "Выключено";
$lang['plugins']['categories_autogeneration']['field_lenient_all']                  = "Включено для всех сложных категорий и синонимов";
$lang['plugins']['categories_autogeneration']['field_lenient_specific']             = "Включено для указанных категорий и синонимов";
$lang['plugins']['categories_autogeneration']['field_lenient_hint1']                = "при включении этой опции сложные категории из нескольких слов не будут требовать полного соответствия, вместо этого отдельные слова таких категорий будут проверяться отдельно и категория подойдет, если каждое ее слово подходит, однако для русского языка ввиду разнообразия окончаний и форм необходимо использование синонимов; например категория [kt|b]Смешная кошка[/kt|b] с синонимом [kt|b]смешн кошк[/kt|b] будет подобрана для видео с названием [kt|b]Смешное видео с кошкой и собакой[/kt|b] [kt|br] [kt|b]ВНИМАНИЕ![/kt|b] Используйте с осторожностью. Использование этой опции может выбирать категории некорректно, т.к. мягкое соответствие не дает 100% аккуратности.";
$lang['plugins']['categories_autogeneration']['field_lenient_hint2']                = "укажите список категорий или синонимов через запятую, для которых применять мягкое соответствие; для всех остальных сложных категорий оно не будет использоваться";
$lang['plugins']['categories_autogeneration']['btn_save']                           = "Сохранить";
