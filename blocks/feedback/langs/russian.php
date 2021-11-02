<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// feedback messages
// =====================================================================================================================

$lang['feedback']['params']['require_subject']  = "Делает поле темы обязательным для заполнения.";
$lang['feedback']['params']['require_email']    = "Делает поле email обязательным для заполнения.";
$lang['feedback']['params']['use_custom1']      = "Делает доп. поле 1 обязательным для заполнения.";
$lang['feedback']['params']['use_custom2']      = "Делает доп. поле 2 обязательным для заполнения.";
$lang['feedback']['params']['use_custom3']      = "Делает доп. поле 3 обязательным для заполнения.";
$lang['feedback']['params']['use_custom4']      = "Делает доп. поле 4 обязательным для заполнения.";
$lang['feedback']['params']['use_custom5']      = "Делает доп. поле 5 обязательным для заполнения.";
$lang['feedback']['params']['use_captcha']      = "Включает использование визуальной защиты от авто-сабмита.";

$lang['feedback']['block_short_desc'] = "Предоставляет функционал для получения обратной связи от пользователей сайта";

$lang['feedback']['block_desc'] = "
	Блок предоставляет возможность отправить любые сообщения администрации сайта.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]message_required[/kt|b]: когда поле сообщения не заполнено [поле = message][kt|br]
	- [kt|b]subject_required[/kt|b]: когда поле темы не заполнено, но настроено как обязательное [поле = subject][kt|br]
	- [kt|b]email_required[/kt|b]: когда поле email не заполнено [поле = email][kt|br]
	- [kt|b]custom1_required[/kt|b]: когда дополнительное поле 1 не заполнено, но настроено как обязательное [поле = custom1][kt|br]
	- [kt|b]custom2_required[/kt|b]: когда дополнительное поле 2 не заполнено, но настроено как обязательное [поле = custom2][kt|br]
	- [kt|b]custom3_required[/kt|b]: когда дополнительное поле 3 не заполнено, но настроено как обязательное [поле = custom3][kt|br]
	- [kt|b]custom4_required[/kt|b]: когда дополнительное поле 4 не заполнено, но настроено как обязательное [поле = custom4][kt|br]
	- [kt|b]custom5_required[/kt|b]: когда дополнительное поле 5 не заполнено, но настроено как обязательное [поле = custom5][kt|br]
	- [kt|b]code_required[/kt|b]: когда включена визуальная защита и ее решение не заполнено [поле = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: когда включена визуальная защита и ее решение не корректно [поле = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";
