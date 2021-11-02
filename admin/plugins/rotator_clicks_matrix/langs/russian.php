<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['rotator_clicks_matrix']['title']          = "Просмотр весовых матриц ротатора";
$lang['plugins']['rotator_clicks_matrix']['description']    = "Позволяет анализировать собранную статистику по кликам для всех блоков типа list_xxx, которые поддерживают ротатор.";
$lang['plugins']['rotator_clicks_matrix']['long_desc']      = "
		Вы можете использовать этот плагин для анализа распределения кликов по вашим страницам. Если ротатор
		поддерживается каким-либо блоком списка (пока это только list_videos), этот блок будет собирать статистику по
		всем кликам, сделанным на нем. На основе статистики создаются матрицы распределения кликов, которые
		используются ротатором для определения веса любого конкретного клика. Эта информация может быть полезна вам
		в качестве определения наиболее кликабельных мест на сайте и т.д.
";
$lang['permissions']['plugins|rotator_clicks_matrix']       = $lang['plugins']['rotator_clicks_matrix']['title'];

$lang['plugins']['rotator_clicks_matrix']['field_page']                 = "Страница";
$lang['plugins']['rotator_clicks_matrix']['field_page_global']          = "* Глобальные блоки *";
$lang['plugins']['rotator_clicks_matrix']['field_items_in_row']         = "Элементов в строке";
$lang['plugins']['rotator_clicks_matrix']['field_items_in_row_hint']    = "матрица ротатора является плоским массивом; укажите кол-во элементов, которое отображается в одной строке на странице сайта, чтобы предоставить информацию в удобном виде";
$lang['plugins']['rotator_clicks_matrix']['divider_matrix']             = "Статистика кликов ротатора";
$lang['plugins']['rotator_clicks_matrix']['field_by_page_number']       = "По номерам страниц";
$lang['plugins']['rotator_clicks_matrix']['field_by_page_number_page']  = "Стр. %1%";
$lang['plugins']['rotator_clicks_matrix']['field_by_page_position']     = "По положению на странице";
$lang['plugins']['rotator_clicks_matrix']['btn_display']                = "Отобразить";
$lang['plugins']['rotator_clicks_matrix']['btn_reset']                  = "Сбросить выбранные";
$lang['plugins']['rotator_clicks_matrix']['btn_reset_confirm']          = "Вы уверены, что хотите сбросить выбранные весовые матрицы?";
