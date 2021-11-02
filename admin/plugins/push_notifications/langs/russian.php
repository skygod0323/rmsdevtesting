<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['push_notifications']['title']         = "$$$ Push сообщения";
$lang['plugins']['push_notifications']['description']   = "Революция в монетизации тюб сайтов.";
$lang['plugins']['push_notifications']['long_desc']     = "
Плагин push сообщений открывает новую эпоху в монетизации тюб сайтов. Теперь вы можете использовать альтернативный и очень весомый канал монетизации вашего сайта через функциональность push сообщений в браузере. Как это работает? 
[kt|br][kt|br]
При открытии вашего сайта браузер будет предлагать пользователю подписаться на получение сообщений. Если пользователь согласится, он будет получать рекламные материалы прямо в браузер без привязки к вашему сайту, даже если больше ни разу не зайдет на него снова. В отличии от обычной рекламы данный вид рекламы не оказывает влияния на содержимое вашего сайта и его восприятие пользователем, т.к. запрос разрешения на показ сообщений отображается браузером, а не сайтом. Кроме того, Ad Maven, рекламодатель интегрированный в KVS, гарантирует 100% соблюдение требований Google к рекламным материалам. Вам не следует опасаться последствий для SEO при использовании плагина push сообщений на вашем сайте.
[kt|br][kt|br]
В плане монетизации в отличие от стандартных типов рекламы, push сообщения будут приносить вам постоянный арендный доход достаточно долгое время, т.к. подписавшись на сообщения, пользователь будет получать их постоянно, и каждый показ рекламных материалов этому пользователю будет приносить вам доход. Тем самым со временем вы начнете получать пассивный доход, даже если на вашем сайте не будет трафика.
[kt|br][kt|br]
Для начала работы с push сообщениями вам необходимо зарегистрироваться на сайте Ad Maven. Возможность создать рекламный тэг для push сообщений доступна только при регистрации через KVS, т.к. рекламодатель требует соблюдение целого ряда условий, которые уже реализованы в KVS. [kt|b]ВАЖНО![/kt|b] В зависимости от того, работает ли ваш сайт через HTTPS или через HTTP вам необходимо создавать разные рекламные тэги на сайте Ad Maven. При работе через HTTPS вам необходимо создать тэг (Create tag) с типом [kt|b]Native Push[/kt|b] - это нативные push сообщения, которые работают только на HTTPS сайтах. При работе через HTTP вам необходимо создать тэг (Create tag) с типом [kt|b]Push In Page[/kt|b] - это эмуляция push сообщений, которая будет работать через вспомогательную страницу.
[kt|br][kt|br]
После активации плагина вы должны увидеть запрос на разрешение отправки сообщений на любой странице вашего сайта в течение 20 минут. Вы можете использовать опции плагина, чтобы ограничить частоту показа запроса, а также исключить его отображение для некоторых рефереров. Например, некоторые системы продажи трафика могут запрещать push сообщения при переходах на ваш сайт.
[kt|br][kt|br]
Если вы уже используете push рекламу от Ad Maven, вы можете переключиться на данный плагин чтобы воспользоваться его расширенными опциями. При этом Ad Maven гарантирует одинаковые рэйты независимо от того, используете ли вы плагин KVS или нет.
";
$lang['permissions']['plugins|push_notifications']      = $lang['plugins']['push_notifications']['title'];

$lang['plugins']['push_notifications']['field_enable']                  = "Включить push сообщения";
$lang['plugins']['push_notifications']['field_enable_enabled']          = "включено";
$lang['plugins']['push_notifications']['field_enable_hint']             = "активирует сервис push сообщений на сайте, система push сообщений должна заработать в течение 20 минут";
$lang['plugins']['push_notifications']['field_refid']                   = "ID тэга";
$lang['plugins']['push_notifications']['field_refid_sign_up']           = "Зарегистрируйтесь на Ad Maven";
$lang['plugins']['push_notifications']['field_refid_hint_http']         = "создайте рекламный тэг для вашего сайта с типом [kt|b]Push In Page[/kt|b]; скопируйте в это поле ID тэга, который вы можете найти в embed коде тэга вида: [kt|br] <script data-cfasync=\"false\" src=\"//d2d8qsxiai9qwj.cloudfront.net/?xsqdd=[kt|b]ID_тэга_цифры[/kt|b]\"></script>";
$lang['plugins']['push_notifications']['field_refid_hint_https']        = "создайте рекламный тэг для вашего сайта с типом [kt|b]Native Push[/kt|b]; скопируйте в это поле ID тэга, который вы можете найти в embed коде тэга вида: [kt|br] <script data-cfasync=\"false\" src=\"//d2d8qsxiai9qwj.cloudfront.net/?xsqdd=[kt|b]ID_тэга_цифры[/kt|b]\"></script>";
$lang['plugins']['push_notifications']['field_repeat']                  = "Повторять";
$lang['plugins']['push_notifications']['field_repeat_always']           = "Повторять на каждой странице";
$lang['plugins']['push_notifications']['field_repeat_interval']         = "Повторять через каждые ...";
$lang['plugins']['push_notifications']['field_repeat_interval_minutes'] = "минут";
$lang['plugins']['push_notifications']['field_repeat_once']             = "Показывать только один раз";
$lang['plugins']['push_notifications']['field_repeat_hint']             = "выберите как часто одному и тому же пользователю должно показываться предложение согласиться на получение сообщений";
$lang['plugins']['push_notifications']['field_first_click']             = "Первое посещение";
$lang['plugins']['push_notifications']['field_first_click_skip']        = "не показывать при первом посещении";
$lang['plugins']['push_notifications']['field_first_click_hint']        = "позволяет не предлагать посетителю подписаться на push сообщения при первом визите на ваш сайт; в этом случае предложение будет показываться после того как пользователь откроет любую другую страницу на вашем сайте";
$lang['plugins']['push_notifications']['field_exclude_referers']        = "Исключить рефереры";
$lang['plugins']['push_notifications']['field_exclude_referers_hint']   = "укажите список рефереров, трафик которых должен быть исключен из сервиса push сообщений; каждый реферер должен быть указан на отдельной строке на основе следующих правил: [kt|br] - начните реферер с [kt|b]http://[/kt|b] если вы хотите исключить трафик с отдельных доменов (не указывайте www-вариант, он будет учитываться автоматически); например, реферер [kt|b]http://google.[/kt|b] исключит трафик с доменов [kt|b]google.com[/kt|b] и [kt|b]google.ru[/kt|b] и т.д.; [kt|br] - укажите реферер без [kt|b]http://[/kt|b], если вы хотите исключить трафик на определенных URL-ах вашего сайта; например, реферер [kt|b]utm_source=adwords[/kt|b] исключит весь трафик, который приходит на ваш сайт по URL-ам содержащим параметр [kt|b]utm_source=adwords[/kt|b]";
$lang['plugins']['push_notifications']['field_include_referers']        = "Только рефереры";
$lang['plugins']['push_notifications']['field_include_referers_hint']   = "укажите список рефереров, трафик которых должен быть включен в сервис push сообщений (весь остальной трафик не будет включаться); каждый реферер должен быть указан на отдельной строке на основе следующих правил: [kt|br] - начните реферер с [kt|b]http://[/kt|b] если вы хотите включить трафик с отдельных доменов (не указывайте www-вариант, он будет учитываться автоматически); например, реферер [kt|b]http://google.[/kt|b] включит трафик с доменов [kt|b]google.com[/kt|b] и [kt|b]google.ru[/kt|b] и т.д.; [kt|br] - укажите реферер без [kt|b]http://[/kt|b], если вы хотите включить трафик на определенных URL-ах вашего сайта; например, реферер [kt|b]utm_source=adwords[/kt|b] включит весь трафик, который приходит на ваш сайт по URL-ам содержащим параметр [kt|b]utm_source=adwords[/kt|b]";
$lang['plugins']['push_notifications']['field_exclude_members']         = "Исключить пользователей";
$lang['plugins']['push_notifications']['field_exclude_members_none']    = "Не исключать";
$lang['plugins']['push_notifications']['field_exclude_members_all']     = "Зарегистрированных пользователей";
$lang['plugins']['push_notifications']['field_exclude_members_premium'] = "Премиум пользователей";
$lang['plugins']['push_notifications']['field_exclude_members_hint']    = "позволяет исключить зарегистрированных или премиум пользователей из сервиса push сообщений";
$lang['plugins']['push_notifications']['field_js_library']              = "JS библиотека";
$lang['plugins']['push_notifications']['field_js_library_download']     = "Скачать файл";
$lang['plugins']['push_notifications']['field_js_library_hint']         = "скачайте файл JS библиотеки (sw.js) и скопируйте его в корень вашего проекта на FTP";
$lang['plugins']['push_notifications']['error_missing_library']         = "[kt|b][%1%][/kt|b]: файл JS библиотеки sw.js не найден в корне проекта";
$lang['plugins']['push_notifications']['btn_save']                      = "Созранить";
