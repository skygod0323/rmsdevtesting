<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['users_generator']['title']        = "Генератор аккаунтов для пользователей";
$lang['plugins']['users_generator']['description']  = "Создает набор платных аккаунтов или кодов доступа для пользователей исходя из выбранных опций.";
$lang['plugins']['users_generator']['long_desc']    = "
		Этот плагин предназначен для генерации аккаунтов пользователей со случайными логинами и паролями или кодов
		доступа для получения привилегий. Вы можете использовать его для продажи готовых аккаунтов / кодов доступа в
		цифровых магазинах в качестве альтернативы процессорам оплаты.
		[kt|br][kt|br]
		Коды доступа позволяют существующим пользователям использовать их для уже существующих (своих) аккаунтов, чтобы
		воспользоваться привилегией кода доступа один раз. А также они позволяют регистрироваться новым пользователям с
		указанием кода при регистрации и аналогично единоразово получить привилегию, которую дает код доступа.
		Пользователь может воспользоваться несколькимии разными кодами доступа, если они предоставляют токены - в этом
		случае полученные токены суммируются. При использовании кода доступа, который предоставляет премиум статус,
		данный пользователь не сможет воспользоваться другим кодом доступа до тех пор, пока его аккаунт имеет премиум
		статус. После обнуления статуса пользователь сможет снова использовать любые другие коды доступа.
";
$lang['permissions']['plugins|users_generator']  = $lang['plugins']['users_generator']['title'];

$lang['plugins']['users_generator']['divider_parameters']                       = "Параметры";
$lang['plugins']['users_generator']['divider_summary_accounts']                 = "Созданные аккаунты";
$lang['plugins']['users_generator']['divider_summary_access_codes']             = "Созданные коды доступа";
$lang['plugins']['users_generator']['field_generate']                           = "Создавать";
$lang['plugins']['users_generator']['field_generate_access_codes']              = "Коды доступа";
$lang['plugins']['users_generator']['field_generate_accounts']                  = "Аккаунты";
$lang['plugins']['users_generator']['field_amount']                             = "Количество";
$lang['plugins']['users_generator']['field_amount_hint']                        = "количество аккаунтов / кодов доступа, которое необходимо создать";
$lang['plugins']['users_generator']['field_access_type']                        = "Тип доступа";
$lang['plugins']['users_generator']['field_access_type_premium_unlimited']      = "Премиум доступ без ограничения";
$lang['plugins']['users_generator']['field_access_type_premium_unlimited_hint'] = "создает премиум аккаунты / коды доступа без ограничения по длительности";
$lang['plugins']['users_generator']['field_access_type_premium_duration']       = "Премиум доступ на N дней";
$lang['plugins']['users_generator']['field_access_type_premium_duration_hint']  = "создает премиум аккаунты / коды доступа, которые будут работать в течение указанного кол-ва дней после первого применения";
$lang['plugins']['users_generator']['field_access_type_tokens']                 = "Стандартный доступ с N токенами";
$lang['plugins']['users_generator']['field_access_type_tokens_hint']            = "создает стандартные аккаунты / коды доступа с указанным кол-вом токенов";
$lang['plugins']['users_generator']['field_username_length']                    = "Длина логина";
$lang['plugins']['users_generator']['field_password_length']                    = "Длина пароля";
$lang['plugins']['users_generator']['field_access_code_length']                 = "Длина кода доступа";
$lang['plugins']['users_generator']['field_access_code_referral_award']         = "Реферральская выплата";
$lang['plugins']['users_generator']['field_access_code_referral_award_hint']    = "укажите кол-во токенов для присуждения пользователям, которые привлекают других пользователей через реферральные ссылки регистрироваться у вас на сайте и покупать коды доступа данного вида; имеет смысл если ваш сайт поддерживает реферральные ссылки [kt|br] например, укажите [kt|b]100[/kt|b] чтобы пользователь заработал 100 токенов за привлечение другого пользователя, который купит код доступа";
$lang['plugins']['users_generator']['field_users']                              = "Аккаунты";
$lang['plugins']['users_generator']['field_users_hint']                         = "список созданных аккаунтов в виде пары логин:пароль";
$lang['plugins']['users_generator']['field_access_codes']                       = "Коды доступа";
$lang['plugins']['users_generator']['field_access_codes_hint']                  = "список созданных кодов доступа";
$lang['plugins']['users_generator']['btn_generate']                             = "Создать";
