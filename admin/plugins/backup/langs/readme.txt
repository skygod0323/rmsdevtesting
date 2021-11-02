This archive is structured in a special way to allow you restore different aspects of your KVS project easily. It may
store the following data:

- Database backup: stored inside "mysql" subdirectory. If you want to restore KVS database from this backup, you
  should create a new database in MySQL and import "backup.sql" file there, then change KVS database connection to a
  new database in "/admin/include/setup_db.php". If you later want to remove old database to save disk space, this is
  up to you, but first you should make sure that your site works correctly with the restored database.

- KVS system files: stored inside "kvs" subdirectory. Typically you may need to replace a specific file or files if
  they were broken or deleted by accident. Use Installation check in System Audit plugin to find which system files are
  considered 'changed' or 'deleted'. If you want to completely restore KVS system files (for example if KVS update went
  wrong and you want to rollback to previous KVS version), just copy the contents of "kvs" subdirectory on top of
  your project's document root directory on server.

- KVS site theme: stored inside "website" subdirectory. It contains all KVS theme settings and templates, theme
  advertising, and many static theme files, such as JS, CSS, images and fonts. Copy the contents of "website"
  subdirectory on top of your project's document root directory on server if you want to completely restore theme to
  the version kept in this backup archive. Or take the needed files if you want to restore them individually.

- KVS player settings: stored inside "player" subdirectory. This subdirectory contains player and embed player
  settings as well as all player advertising. Copy the contents of "player" subdirectory on top of your project's
  document root directory on server to restore all player settings to this particular version.

- Content files: stored inside "content" subdirectory and contain member profile and categorization uploaded data,
  such as avatars, screenshots, custom files. Typically you won't need this, but just in case.

------------------------------------------------------------------------------------------------------------------------

Данный архив специальным образом разбит на несколько структур, чтобы позволить легко восстановить в целостности
какой-либо из аспектов проекта KVS. Он может содержать такие данные:

- Резервная копия базы данных - содержится в подкаталоге "mysql". Для восстановления базы данных вам необходимо
  сначала создать новую базу данных MySQL и испортировать в нее файл "backup.sql" из архива. Затем переключите KVS на
  подключение к этой новой базе данных в файле "/admin/include/setup_db.php". Вы можете удалить старую базу данных
  для экономии дискового пространства после того, как вы проверите работоспособность вашего проекта на восстановленной
  базе.

- Системные файлы KVS - хранятся в подкаталоге "kvs". Обычно вам может понадобиться восстановить какие-то системные
  файлы, которые вы модифицировали или случайно удалили. Для поиска таких файлов воспользуйтесь проверкой инсталляции в
  плагине Аудита системы. Если же вы хотите полностью восстановить все системные файлы KVS, например если вдруг
  обновление на новую версию пошло не так, просто скопируйте все содержимое подкаталога "kvs" поверх корня вашего
  проекта на сервере.

- Файлы темы сайта - содержатся в подкаталоге "website". Там находятся все файлы всех страници компонентов, их
  настройки, все настройки рекламы (кроме рекламы плеера), а также статические файлы темы, такие как JS, CSS, картинки
  или шрифты. Для полного восстановление темы сайта к этой версии скопируйте все содержимое подкаталога "website"
  поверх корня вашего проекта на сервере.

- Настройки плеера - хранятся в подкаталоге "player". Здесь хранятся все настройки плеера, embed плеера, а также вся
  реклама в плеере. Для полного восстановление настроек плеера к этой версии скопируйте все содержимое подкаталога
  "player" поверх корня вашего проекта на сервере.

- Файлы контента - находятся внутри подкаталога "content" и содержат только ограниченный набор данных: файлы
  пользователей и категоризации, такие как аватары и скриншоты, доп. файлы. Восстановление этих файлов может
  производиться в редких случаях, обычно они не существенны для проектов.