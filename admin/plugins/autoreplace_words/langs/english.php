<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['autoreplace_words']['title']          = "Synonymizer";
$lang['plugins']['autoreplace_words']['description']    = "Replaces words with their random synonyms in titles and descriptions.";
$lang['plugins']['autoreplace_words']['long_desc']      = "
		Use this plugin to create unique titles and descriptions of your content objects such as videos and photo
		albums. You need to define a list of known words and their synonyms. Based on this list, the plugin will
		replace the words found in object titles and / or descriptions with randomly chosen synonyms. The plugin
		handles word forms with different cases automatically, so you only need to build the synonymizer dictionary
		using words in lower case.
		[kt|br][kt|br]
		This plugin will only be used for newly added content. If you want to auto-replace text in existing content,
		you can execute this plugin via mass edit GUI. Please be aware that content directories will be modified as
		well, so content URLs may be changed if your URLs are using directories, which is true by default.
";
$lang['permissions']['plugins|autoreplace_words']   = $lang['plugins']['autoreplace_words']['title'];

$lang['plugins']['autoreplace_words']['divider_settings']               = "Settings";
$lang['plugins']['autoreplace_words']['divider_vocabulary']             = "Replacement vocabulary";
$lang['plugins']['autoreplace_words']['divider_vocabulary_hint']        = "For simplicity use plain text format with the given rule. Each record should be specified on a new line and should define comma-separated list of synonyms in lower case. Example:[kt|br][kt|b]synonym1, synonym2, synonym3[/kt|b][kt|br]When a word is found, it will be replaced with one of its synonyms.";
$lang['plugins']['autoreplace_words']['field_replace_videos']           = "Videos";
$lang['plugins']['autoreplace_words']['field_replace_albums']           = "Albums";
$lang['plugins']['autoreplace_words']['field_replace_in_title']         = "Replace in title field";
$lang['plugins']['autoreplace_words']['field_replace_in_description']   = "Replace in description field";
$lang['plugins']['autoreplace_words']['field_limit']                    = "Limit to";
$lang['plugins']['autoreplace_words']['field_limit_feeds']              = "Content created by importing feeds";
$lang['plugins']['autoreplace_words']['field_limit_grabbers']           = "Content created by grabbers";
$lang['plugins']['autoreplace_words']['field_limit_hint']               = "by default auto-replace will be applied for all content, but you can limit it to specific sub-set of content using these options";
$lang['plugins']['autoreplace_words']['field_vocabulary_example']       = "synonym1, synonym2, synonym3";
$lang['plugins']['autoreplace_words']['error_row_format']               = "[kt|b][%1%][/kt|b]: row %2% format is not valid";
$lang['plugins']['autoreplace_words']['error_word_duplicate']           = "[kt|b][%1%][/kt|b]: row %2% duplicates word \"%3%\"";
$lang['plugins']['autoreplace_words']['btn_save']                       = "Save";
