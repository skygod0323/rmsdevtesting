<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['avatars_generation']['title']         = "Генерация аватаров";
$lang['plugins']['avatars_generation']['description']   = "Генерирует аватары категорий на основе видео или альбомов в этих категориях.";
$lang['plugins']['avatars_generation']['long_desc']     = "
		Плагин генерации аватаров создает аватары категорий на основе видео или альбомов в этих категориях. Для каждой
		категории выбирается только одно видео (альбом), главный скриншот которого (точнее его исходник) будет
		использоваться как аватар данной категории. В настройках плагина вы можете выбрать сортировку, которая будет
		использоваться для поиска главного видео (альбома) категории. Вы также должны указать строку опций ImageMagick,
		которая будет использоваться для ресайза исходных файлов до нужного размера аватаров.
";
$lang['permissions']['plugins|avatars_generation']      = $lang['plugins']['avatars_generation']['title'];

$lang['plugins']['avatars_generation']['field_enable']                          = "Включить генерацию";
$lang['plugins']['avatars_generation']['field_enable_disabled']                 = "Выключено";
$lang['plugins']['avatars_generation']['field_enable_videos']                   = "На основе видео";
$lang['plugins']['avatars_generation']['field_enable_albums']                   = "На основе альбомов";
$lang['plugins']['avatars_generation']['field_videos_rule']                     = "Метод выборки видео";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_all']      = "По популярности (за все время)";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_month']    = "По популярности (за этот месяц)";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_week']     = "По популярности (за эту неделю)";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_day']      = "По популярности (за сегодня)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_all']          = "По рейтингу (за все время)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_month']        = "По рейтингу (за этот месяц)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_week']         = "По рейтингу (за эту неделю)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_day']          = "По рейтингу (за сегодня)";
$lang['plugins']['avatars_generation']['field_videos_rule_most_commented']      = "По кол-ву комментариев";
$lang['plugins']['avatars_generation']['field_videos_rule_most_favourited']     = "По кол-ву добавления в закладки";
$lang['plugins']['avatars_generation']['field_videos_rule_post_date']           = "Самое свежее видео";
$lang['plugins']['avatars_generation']['field_videos_rule_ctr']                 = "По CTR (ротатор)";
$lang['plugins']['avatars_generation']['field_albums_rule']                     = "Метод выборки альбома";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_all']      = "По популярности (за все время)";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_month']    = "По популярности (за этот месяц)";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_week']     = "По популярности (за эту неделю)";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_day']      = "По популярности (за сегодня)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_all']          = "По рейтингу (за все время)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_month']        = "По рейтингу (за этот месяц)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_week']         = "По рейтингу (за эту неделю)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_day']          = "По рейтингу (за сегодня)";
$lang['plugins']['avatars_generation']['field_albums_rule_most_commented']      = "По кол-ву комментариев";
$lang['plugins']['avatars_generation']['field_albums_rule_most_favourited']     = "По кол-ву добавления в закладки";
$lang['plugins']['avatars_generation']['field_albums_rule_post_date']           = "Самый свежий альбом";
$lang['plugins']['avatars_generation']['field_im_options']                      = "Опции ImageMagick";
$lang['plugins']['avatars_generation']['field_im_options_hint']                 = "опции ImageMagick-а должны содержать токены [kt|b]%INPUT_FILE%[/kt|b], [kt|b]%OUTPUT_FILE%[/kt|b] и [kt|b]%SIZE%[/kt|b], которые будут заменены на реальные имена файлов и размер во время нарезки аватаров";
$lang['plugins']['avatars_generation']['field_crop_options']                    = "Опции кропа";
$lang['plugins']['avatars_generation']['field_crop_options_left']               = "слева";
$lang['plugins']['avatars_generation']['field_crop_options_top']                = "сверху";
$lang['plugins']['avatars_generation']['field_crop_options_right']              = "справа";
$lang['plugins']['avatars_generation']['field_crop_options_bottom']             = "снизу";
$lang['plugins']['avatars_generation']['field_schedule']                        = "Расписание";
$lang['plugins']['avatars_generation']['field_schedule_interval']               = "минимальный интервал (ч)";
$lang['plugins']['avatars_generation']['field_schedule_tod']                    = "время дня";
$lang['plugins']['avatars_generation']['field_schedule_tod_any']                = "как получится";
$lang['plugins']['avatars_generation']['field_schedule_hint']                   = "укажите минимальный интервал между повторными запусками этого плагина, а также время дня если требуется; время дня не может быть гарантировано на 100%, в зависимости от обстановки плагин может запуститься позднее в этот же день, но не ранее указанного часа";
$lang['plugins']['avatars_generation']['field_last_exec']                       = "Последний запуск";
$lang['plugins']['avatars_generation']['field_last_exec_none']                  = "нет";
$lang['plugins']['avatars_generation']['field_last_exec_seconds']               = "секунд";
$lang['plugins']['avatars_generation']['field_next_exec']                       = "Следующий запуск";
$lang['plugins']['avatars_generation']['field_next_exec_none']                  = "нет";
$lang['plugins']['avatars_generation']['btn_save']                              = "Сохранить";
$lang['plugins']['avatars_generation']['btn_regenerate']                        = "Сохранить и пересоздать сейчас";
