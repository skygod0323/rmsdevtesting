<div id="documentation">
	<h1 id="section_website_ui">Туториал по управлению сайтом</h1>
	<h2 id="section_website_ui_contents">Содержание</h2>
	<div class="contents">
		<a href="#section_preface" class="l2">1. Введение</a><br/>
		<a href="#section_models_list" class="l2">2. Создание страницы для отображения списка моделей</a><br/>
		<a href="#section_models_list1" class="l3">2.1. Создание страницы списка моделей</a><br/>
		<a href="#section_models_list2" class="l3">2.2. Настройка страницы списка моделей</a><br/>
		<a href="#section_models_list3" class="l3">2.3. Настройка пагинации (разбиения по страницам)</a><br/>
		<a href="#section_models_view" class="l2">3. Страница для отображения информации о модели</a><br/>
		<a href="#section_models_view1" class="l3">3.1. Создание страницы с блоком информации о модели</a><br/>
		<a href="#section_models_view2" class="l3">3.2. Добавление списка видео на страницу с блоком информации о модели</a><br/>
		<a href="#section_models_other" class="l2">4. Другие страницы</a><br/>
		<a href="#section_models_other1" class="l3">4.1. Добавление списка моделей на страницу просмотра видео</a><br/>
		<a href="#section_models_other2" class="l3">4.2. Добавление списка моделей на главную страницу сайта</a><br/>
	</div>
	<h2 id="section_preface">1. Введение</h2>
	<p>
		Создание сайта на движке KVS кажется сложным на первый взгляд, но на самом деле эта задача
		достаточно проста после нескольких попыток. Мы предлагаем пройти небольшой туториал, который тем не менее
		покрывает практически все аспекты создания страниц и настройки контента на них.
	</p>
	<p>
		Рассмотрим задачу отображения моделей на сайте. Мы бы хотели видеть список моделей с возможностью алфавитной
		фильтрации, страницу информации о модели и списком видео с участием этой модели, а также на странице
		просмотра видео отобразить список моделей, которые снимаются в данном видео.
	</p>
	<p>
		Итак, что нужно сделать:
	</p>
	<ul>
		<li>
			Создать новую страницу для отображения списка моделей.
		</li>
		<li>
			Создать новую страницу для отображения информации о модели и списком видео с участием этой модели.
		</li>
		<li>
			Добавить вывод списка моделей текущего видео на странице просмотра видео.
		</li>
		<li>
			И, дополнительно, вывести на главной странице сайта 5 моделей с наибольшим количеством видео.
		</li>
	</ul>
	<p>
		Перед выполнением туториала создайте несколько моделей и привяжите их к нескольким видео (привязка видео
		к моделям делается на странице редактирования видео в панели администрирования).
	</p>
	<p>
		Мы не будем финально настраивать страницы со всеми деталями, такими как CSS-стили, вспомогательные ссылки
		и прочее. Основная цель данного туториала - познакомить с основами создания сайта на движке Kernel Video
		Sharing, доработка мелочей и деталей остается на ваше усмотрение.
	</p>
	<h2 id="section_models_list">2. Страница для отображения списка моделей</h2>
	<h3 id="section_models_list1">2.1. Создание страницы списка моделей</h3>
	<p>
		В разделе <span class="term">UI сайта (Website UI)</span> панели администрирования зайдите на подраздел
		<span class="term">Добавить страницу (Add page)</span>. Вы увидите форму для создания новой страницы сайта.
	</p>
	<p class="important">
		<b>Важно:</b> для создания страницы в панели администрирования PHP должен иметь права на создание файлов в
		некоторых папках. В частности, это корневая папка домена, в которой появится PHP файл страницы. Если прав
		не хватает, то при сохранении страницы вы увидите ошибку с информацией на какую папку необходимо
		установить права.
	</p>
	<p>
		При создании новой страницы вам необходимо заполнить несколько обязательных полей:
	</p>
	<ul>
		<li>
			<span class="term">Название (Display name)</span> - название страницы для вашего удобства, которое
			отображается только в панели администрирования. Введите <b>Models List</b>.
		</li>
		<li>
			<span class="term">Идентификатор (External ID)</span> - системное поле, которое уникально
			идентифицирует страницу и на основе которого генерируются имена файлов и папок, относящихся к данной
			странице. Введите <b>models_list</b>. Тогда страница будет доступна по ссылке
			<b>http://your_domain.com/models_list.php</b>.
		</li>
		<li>
			<span class="term">Код шаблона (Template code)</span> - здесь в виде шаблона Smarty (или просто HTML
			кода) содержится отображение данных страницы. Заполните данное поле одним словом <b>Test</b>.
		</li>
	</ul>
	<p>
		Если при сохранении вы не получили никаких ошибок, то страница создалась и стала доступна на сайте. В
		адресной строке браузера введите <b>http://your_domain.com/models_list.php</b> и убедитесь, что видите
		слово <b>Test</b>, которое вы ввели в шаблоне страницы.
	</p>
	<p>
		В большинстве случаев вы захотите, чтобы страница была доступна по другой ссылке (например,
		<b>http://your_domain.com/models/</b>), что более удобно для поисковых систем. Для того, чтобы это сделать,
		необходимо создать правило в корневом .htaccess файле, которое будет перенаправлять запросы на скрипт
		<b>models_list.php</b>:
	</p>
	<p class="code">
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		После этого по ссылке <b>http://your_domain.com/models/</b> вы будете видеть эту же страницу.
	</p>
	<h3 id="section_models_list2">2.2. Настройка страницы списка моделей</h3>
	<p>
		Теперь настроим содержимое страницы списка моделей. Для этого на
		<span class="term">Списке страниц (Pages list)</span> в разделе
		<span class="term">UI сайта (Website UI)</span> найдем созданную страницу с названием <b>Models List</b> и
		откроем ее на редактирование. Нам необходимо задать компоновку страницы, а также вставить блок вывода
		списка моделей (<b>list_models</b>) и настроить его поведение.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_01.png" alt="Cтраница Models List на списке страниц" width="600" height="136"/><br/>
		<span>Cтраница Models List на списке страниц.</span>
	</p>
	<p>
		За компоновку страницы обычно отвечают такие компоненты как шапка и нижняя часть. Эти компоненты имеют
		общий вид на всех страницах сайта и, поэтому, вынесены в отдельные шаблоны (компоненты страниц). Вывод
		шапки осуществляется компонентом <b>header_general.tpl</b>, за вывод нижней части отвечает компонент
		<b>footer_general.tpl</b>. Компонент шапки поддерживает возможность указать HTML название страницы, ее
		описание и ключевые слова перед включением этого компонента в шаблон страницы. С учетом этого, код шаблона
		страницы списка моделей может выглядеть таким образом:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Устанавливаем значение переменных page_title, page_description и page_keywords которые используются
			в компоненте страницы header_general *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="Models"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="Displays all models you can see at MySite.com"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы header_general, который отображает шапку страницы с учетом значений,
			указанных выше *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Wide column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы footer_general, который отображает нижнюю часть страницы *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		После сохранения данного шаблона (для сохранения можно использовать кнопку
		<span class="term">Обновить содержимое (Update content)</span>) откройте страницу
		<b>http://your_domain.com/models/</b>. На странице появилась основная верстка сайта с пустыми центральной
		и боковой областями.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_02.png" alt="Cтраница Models List на сайте" width="999" height="289"/><br/>
		<span>Cтраница Models List на сайте.</span>
	</p>
	<p>
		Обратите внимание на то, что в HTML коде шапки вывелась информация, которую мы ввели
		в шаблоне страницы:
	</p>
	<p class="code">
		&lt;title&gt;Models / Kernel Tube&lt;/title&gt;<br/>
		&lt;meta name="description" content="Displays all models you can see at MySite.com"/&gt;<br/>
		&lt;meta name="keywords" content="models, blablabla, blablabla"/&gt;
	</p>
	<p>
		Теперь осталось подключить только блок списка моделей и страница готова! Для этого в нужном месте шаблона,
		где вы хотите вывести список моделей, необходимо всего лишь вставить одну строчку (выделена жирным):
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Устанавливаем значение переменных page_title, page_description и page_keywords которые используются
			в компоненте страницы header_general *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="Models"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="Displays all models you can see at MySite.com"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы header_general, который отображает шапку страницы с учетом значений,
			указанных выше *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Включаем блок list_models для отображения списка моделей *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}insert name="getBlock" block_id="list_models" block_name="List Models"{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы footer_general, который отображает нижнюю часть страницы *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		После сохранения шаблона обратите внимание на то, что под полем редактирования шаблона в разделе
		<span class="term">Содержимое страницы и стратегия кэширования (Page content and caching strategy)</span>
		появилась информация о блоке <b>List Models</b> с некоторыми параметрами конфигурации, заданными по
		умолчанию. При добавлении нового блока на страницу, блок всегда создается с шаблоном и параметрами
		конфигурации по умолчанию, но вы в любой момент можете перенастроить и шаблон, и параметры конфигурации.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_03.png" alt="List Models блок в содержимом страницы" width="829" height="136"/><br/>
		<span>List Models блок в содержимом страницы.</span>
	</p>
	<p>
		Откройте страницу <b>http://your_domain.com/models/</b>. Если у вас были созданы какие-либо модели, то их
		полный список должен появиться на данной странице.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_04.png" alt="Cтраница Models List на сайте" width="982" height="612"/><br/>
		<span>Cтраница Models List на сайте.</span>
	</p>
	<p>
		Теперь можно настроить верстку и стили отображения самого списка моделей на данной странице. Для того,
		чтобы поменять шаблон и параметры блока <b>List Models</b> на созданной странице (как уже было описано
		выше, при создании блока все настраивается по умолчанию), вам необходимо открыть страницу редактирования
		данного блока. Попасть на страницу редактирования блока можно из 2 мест в панели администрирования:
		кликнуть на ссылку с названием блока на <span class="term">Списке страниц (Pages list)</span> в разделе
		<span class="term">UI сайта (Website UI)</span>, либо использовать аналогичную ссылку на странице
		редактирования нужной страницы сайта.
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_05.png" alt="Сслыка на страницу редактирования блока на общем списке страниц" width="847" height="146"/><br/>
		<span>Сслыка на страницу редактирования блока на общем списке страниц.</span>
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_06.png" alt="Сслыка на страницу редактирования блока на странице редактирования нужной страницы" width="746" height="138"/><br/>
		<span>Сслыка на страницу редактирования блока на странице редактирования нужной страницы.</span>
	</p>
	<p>
		Попав на страницу редактирования блока вы сразу увидите шаблон блока, который отображает список моделей на
		текущей странице. Внизу вы найдете список всех параметров конфигурации, которые поддерживаются блоком и
		влияют на его работу на текущей странице. Используя данные параметры, вы можете настроить разбиение списка
		моделей на страницы с указанием кол-ва моделей, отображаемое на каждой странице, вы можете убрать из списка
		моделей, которые не имеют каких-либо скриншотов, либо не снимаются ни в каких видео. Для каждого параметра
		блока присутствует короткое описание, для чего он предназначен.
	</p>
	<p>
		Поскольку нам необходимо организовать возможность выводить моделей по букве алфавита, включим параметр
		блока <b>var_title_section</b>, который указывает блоку, в каком HTTP параметре придет значение буквы
		алфавита для вывода (по умолчанию это <b>section</b>, его и оставим).
	</p>
	<p>
		После сохранения сохранения данных блока откройте страницу
		<b>http://your_domain.com/models_list.php?section=a</b>. Если у вас есть модели на букву A, то на сайте
		отобразятся только они. Добавим правило редиректа в корневой .htaccess файл для создания более приятных
		ссылок (обратите внимание, что новое правило должно идти перед предыдущим правилом для всего списка):
	</p>
	<p class="code">
		<b>RewriteRule ^models/([a-zA-Z])/(.*)$ /models_list.php?section=$1 [L,QSA]</b><br/>
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		Теперь список моделей по букве A будет доступен и по ссылке
		<b>http://your_domain.com/models/a/</b>.
	</p>
	<h3 id="section_models_list3">2.3. Настройка пагинации (разбиения по страницам)</h3>
	<p>
		Если количество моделей у вас на сайте достаточно большое и нет смысла показывать всех моделей на одной
		странице, вам необходимо настроить пагинацию для блока <b>List Models</b>. Пагинация работает по
		одинаковому принципу для всех блоков списков. Каждый блок списка поддерживает 3 параметра конфигурации,
		которые нужно включить:
	</p>
	<ul>
		<li>
			<b>items_per_page</b> - количество элементов на каждой странице (поставьте для примера такое значение,
			чтобы у вас было хотя бы 3 страницы с моделями, например <b>4</b>).
		</li>
		<li>
			<b>links_per_page</b> - максимальное количество ссылок на страницы, которое отображается одновременно
			(включите и оставьте по умолчанию, т.е. <b>10</b>).
		</li>
		<li>
			<b>var_from</b> - указывает блоку, в каком HTTP параметре придет номер элемента, с которого нужно
			показывать список (включите и оставьте по умолчанию, т.е. <b>from</b>).
		</li>
	</ul>
	<p>
		После того, как вы сохраните данные блока, на странице списка моделей останется только 4 модели, но ссылки
		на остальные страницы не появятся. Дело в том, что блоки списков не содержат отображение ссылок пагинации
		по умолчанию. Для того, чтобы они появились, вам нужно добавить в шаблон блока <b>List Models</b>
		включение общего шаблона для ссылок пагинации в нужном месте (в шаблон блока, а не страницы!):
	</p>
	<p class="code">
		{{$smarty.ldelim}}include file="pagination_block_common.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		Поскольку пагинация работает в se-friendly режиме, вам также необходимо добавить дополнительные правила
		в корневой .htaccess файл, чтобы ссылки на страницы работали корректно как для общего списка моделей, так
		и для списков моделей по буквам алфавита (обратите внимание на порядок правил!):
	</p>
	<p class="code">
		<b>RewriteRule ^models/([a-zA-Z])/([0-9]+)/$ /models_list.php?section=$1&amp;from=$2 [L,QSA]</b><br/>
		RewriteRule ^models/([a-zA-Z])/(.*)$ /models_list.php?section=$1 [L,QSA]<br/>
		<b>RewriteRule ^models/([0-9]+)/$ /models_list.php?from=$1 [L,QSA]</b><br/>
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		Теперь пагинация полностью настроена и работает!
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_07.png" alt="Cтраница Models List на сайте с пагинацией" width="766" height="243"/><br/>
		<span>Cтраница Models List на сайте с пагинацией.</span>
	</p>
	<h2 id="section_models_view">3. Страница для отображения информации о модели</h2>
	<h3 id="section_models_view1">3.1. Создание страницы с блоком информации о модели</h3>
	<p>
		Действуя по аналогии с предыдущей главой, создадим новую страницу с названием <b>View Model</b> и
		идентификатором <b>view_model</b>. Для начала установим такой код шаблона:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Устанавливаем значение переменных page_title, page_description и page_keywords которые используются
			в компоненте страницы header_general *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="Model Display Page"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="Displays model data at MySite.com"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы header_general, который отображает шапку страницы с учетом значений,
			указанных выше *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Включаем блок model_view для отображения данных о модели *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="model_view" block_name="Model View"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы footer_general, который отображает нижнюю часть страницы *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		После сохранения страницы попробуем открыть ее на сайте (<b>http://your_domain.com/view_model.php</b>). Мы
		видим информацию о битой ссылке, которую выводит блок <b>model_view</b>. Проблема заключается в том, что
		блоку <b>model_view</b> необходимо знать, данные какой модели нужно отображать. Для этого в блоке
		существует 2 var-параметра, которые позволяют передать блоку либо ID модели, либо директорию (для
		se-friendly ссылок). По умолчанию в блоке включается параметр <b>var_model_dir = dir</b>, который ожидает,
		что в HTTP параметре <b>dir</b> будет передана директория модели. Для того, чтобы это организовать,
		добавим еще одно правило в корневой .htaccess файл (обратите внимание на порядок правил!):
	</p>
	<p class="code">
		RewriteRule ^models/([a-zA-Z])/([0-9]+)/$ /models_list.php?section=$1&amp;from=$2 [L,QSA]<br/>
		RewriteRule ^models/([a-zA-Z])/(.*)$ /models_list.php?section=$1 [L,QSA]<br/>
		RewriteRule ^models/([0-9]+)/$ /models_list.php?from=$1 [L,QSA]<br/>
		<b>RewriteRule ^models/(.*)/$ /view_model.php?dir=$1 [L,QSA]</b><br/>
		RewriteRule ^models/(.*)$ /models_list.php [L,QSA]
	</p>
	<p>
		После этого по ссылкам вида <b>http://your_domain.com/models/angelina-jolie/</b> будет открываться страница
		с информацией о модели. Часть ссылки <b>angelina-jolie</b> - это директория модели, которая предназначена
		для se-friendly ссылок. Директория генерируется на основе названия и присутствует практически у всех
		объектов: видео, фотоальбомы, категории, тэги, модели, контент провайдеры и др. Если блок списка моделей,
		который был создан в предыдущей главе, генерирует ссылки с идентификаторами моделей вместо директорий, то
		вам необходимо изменить шаблон, чтобы в ссылку вместо ID модели (<b>model_id</b>) подставлялась директория
		(<b>dir</b>).
	</p>
	<p>
		Обратите внимание на то, что в HTML названии и описании страницы выводятся одинаковые данные для всех
		моделей (а именно те, которые указаны в шаблоне для переменных <b>page_title</b> и
		<b>page_description</b>). Для того, чтобы вывести в шапке сайта имя модели и ее описание, нам необходимо
		вставить блок информации о модели до включения компонента шапки, поскольку блок информации о модели
		(<b>model_view</b>) делает выборку из базы данных, и только после этого на странице будут доступны данные
		о модели. Данные любого блока в шаблоне страницы могут быть получены из хранилища блока, которое
		называется <b>storage</b> и то только после того места, как блок вставлен в страницу. Для доступа к
		хранилищу блока используется уникальный ключ, который выводится в панели администрирования в списке блоков,
		используемых на странице:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_08.png" alt="Ключ к storage блока на странице" width="941" height="102"/><br/>
		<span>Ключ к storage блока на странице.</span>
	</p>
	<p>
		Перепишем шаблон страницы. Вынесем вставку блока информации о модели в начало шаблона и присвоим HTML
		названию и описанию данные из хранилища этого блока:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Включаем блок model_view для отображения данных о модели *{{$smarty.rdelim}}<br/>
		</span>
		<span class="comment">
			{{$smarty.ldelim}}* Обратите внимание на новый параметр <b>assign</b>, при его использовании результат работы блока
			помещается в переменную, а не выводится в этом месте страницы *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}insert name="getBlock" block_id="model_view" block_name="Model View" <b>assign="model_view_result"</b>{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Устанавливаем значение переменных page_title, page_description и page_keywords которые используются
			в компоненте страницы header_general, берем нужные данные из хранилища блока *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="<b>`$storage.model_view_model_view.title`</b>"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="<b>`$storage.model_view_model_view.description`</b>"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы header_general, который отображает шапку страницы с учетом значений,
			указанных выше *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Выводим результат работы блока model_view, который был помещен в переменную model_view_result *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}$model_view_result|smarty:nodefaults{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы footer_general, который отображает нижнюю часть страницы *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		После сохранения можно обновить страницу на сайте и увидеть, что дизайн страницы не изменился, все осталось
		на своих местах, однако в HTML названии и описании сайта теперь выводятся данные модели. Таким образом,
		вынос блока в начала шаблона с параметром <b>assign</b> может использоваться в тех случаях, если вам нужно
		далее на странице использовать данные блока через его хранилище. Использование косых ковычек (`) при
		присвоении HTML названия и описания в данном случае требуется движком Smarty.
	</p>
	<h3 id="section_models_view2">3.2. Добавление списка видео на страницу с блоком информации о модели</h3>
	<p>
		Добавим теперь на эту же страницу список видео, в котором участвует модель. Список видео выводится блоком
		<b>list_videos</b>, его и добавим в шаблон страницы сразу после вывода результата блока информации о
		модели:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Включаем блок model_view для отображения данных о модели *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}insert name="getBlock" block_id="model_view" block_name="Model View" assign="model_view_result"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Устанавливаем значение переменных page_title, page_description и page_keywords которые используются
			в компоненте страницы header_general, берем нужные данные из хранилища блока *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var=page_title value="`$storage.model_view_model_view.title`"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_description value="`$storage.model_view_model_view.description`"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}assign var=page_keywords value="models, blablabla, blablabla"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы header_general, который отображает шапку страницы с учетом значений,
			указанных выше *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Выводим результат работы блока model_view, который был помещен в переменную model_view_result *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}$model_view_result|smarty:nodefaults{{$smarty.rdelim}}<br/>
		<span class="comment">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}* Подключаем блок list_videos для отображения списка видео по модели *{{$smarty.rdelim}}<br/>
		</span>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Model Videos"{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Side column data<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент страницы footer_general, который отображает нижнюю часть страницы *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		После сохранения на сайте появится список видео под блоком данных о модели. По умолчанию в нем будут
		отображаться все видео, поэтому для того, чтобы блок списка показывал только видео по нужной модели,
		в блоке необходимо включить фильтрацию по модели. Сделать это можно при помощи одного из двух параметров
		блока <b>list_videos</b>:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_09.png" alt="Включение фильтрации по модели в блоке list_videos" width="946" height="106"/><br/>
		<span>Включение фильтрации по модели в блоке list_videos.</span>
	</p>
	<p>
		Поскольку на страницу уже передается директория модели (в HTTP параметре <b>dir</b> для блока
		<b>model_view</b>), было бы разумным использовать значение этой директории и в блоке <b>list_videos</b>.
		Для этого просто включим параметр блока <b>var_model_dir</b> и установим его значение в <b>dir</b>. Таким
		образом, оба блока на данной странице будут работать базируясь на значении директории модели, переданной
		в HTTP параметре <b>dir</b>. Обновив страницу на сайте можно увидеть, что список видео показывает только
		видео, в котором участвует данная модель.
	</p>
	<p>
		Есть еще один небольшой нюанс, который касается блоков списка, часто используемых на сайте (например,
		<b>list_videos</b>). Поскольку дизайн блока как правило одинаковый для всех страниц сайта, вся верстка
		блока была вынесена в 2 компонента страниц: <b>list_videos_block_common</b> для обычных списков видео и
		<b>list_videos_block_internal</b> для списков видео внутри мемберки сайта (мои любимые и мои загруженные
		видео). Во время вставки нового блока <b>list_videos</b> где-нибудь на сайте, в качестве его шаблона
		берется шаблон по умолчанию, который не использует указанные выше компоненты страниц. Поэтому в большинстве
		случаев, если вам нужно чтобы новый блок отображался точно также, как и остальные блоки <b>list_videos</b>
		на сайте, необходимо вместо его шаблона по умолчанию вставить включение компонента
		<b>list_videos_block_common</b> или <b>list_videos_block_internal</b>:
	</p>
	<p class="code">
		<span class="comment">
			{{$smarty.ldelim}}* Устанавливаем значение переменной list_videos_title, которая выводится компонентом list_videos_block_common *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}assign var="list_videos_title" value="Model videos"{{$smarty.rdelim}}<br/>
		<br/>
		<span class="comment">
			{{$smarty.ldelim}}* Включаем компонент list_videos_block_common, который отображает список видео *{{$smarty.rdelim}}<br/>
		</span>
		{{$smarty.ldelim}}include file="list_videos_block_common.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		Вообще говоря, вы не обязаны использовать компоненты страниц в подобных случаях, т.е. ничего страшного не
		случится, если вы отредактируете дизайн и верстку блока в самом шаблоне блока. С другой стороны, если вы
		захотите в какой-то момент глобально изменить верстку всех списков видео на сайте, вам придется менять это
		во всех блоках <b>list_videos</b> на сайте, где не используется общий компонент страниц. При использовании
		общего компонента вам достаточно будет изменить лишь шаблон этого компонента, и изменения отразятся на всех
		блоках, которые используют его.
	</p>
	<p>
		Если это вас совершенно запутало, смотрите каким образом задаются шаблоны блоков в уже существующих
		страницах сайта и старайтесь сделать по аналогии.
	</p>
	<h2 id="section_models_other">4. Другие страницы</h2>
	<h3 id="section_models_other1">4.1. Добавление списка моделей на страницу просмотра видео</h3>
	<p>
		Для добавления списка моделей на страницу просмотра видео нам необходимо отредактировать шаблон блока
		<b>video_view</b>, который находится на этой странице и отображает всю информацию о видео:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_10.png" alt="Блок информации о видео на списке страниц в панели администрирования" width="810" height="128"/><br/>
		<span>Блок информации о видео на списке страниц в панели администрирования.</span>
	</p>
	<p>
		Шаблон блока уже содержит циклы вывода категорий и тэгов видео, добавим аналогичный цикл для моделей (выделен
		жирным шрифтом):
	</p>
	<p class="code">
		&lt;div class="info_row"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;Categories:<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}foreach name=data item=item from=$data.categories{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="/categories/{{$smarty.ldelim}}$item.dir{{$smarty.rdelim}}/"&gt;{{$smarty.ldelim}}$item.title{{$smarty.rdelim}}&lt;/a&gt;{{$smarty.ldelim}}if !$smarty.foreach.data.last{{$smarty.rdelim}}, {{$smarty.ldelim}}/if{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		&lt;/div&gt;<br/>
		<b>
		{{$smarty.ldelim}}if @count($data.models)>0{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="info_row"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Models:<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}foreach name=data item=item from=$data.models{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;a href="/models/{{$smarty.ldelim}}$item.dir{{$smarty.rdelim}}/"&gt;{{$smarty.ldelim}}$item.title{{$smarty.rdelim}}&lt;/a&gt;{{$smarty.ldelim}}if !$smarty.foreach.data.last{{$smarty.rdelim}}, {{$smarty.ldelim}}/if{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}/foreach{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		{{$smarty.ldelim}}/if{{$smarty.rdelim}}
		</b>
	</p>
	<p>
		В данном случае условие <b>{{$smarty.ldelim}}if @count($data.models)>0{{$smarty.rdelim}}</b> используется для того, чтобы не выводить
		пустую надпись Models, если у видео нет привязанных моделей. Теперь блок просмотра видео выглядит таким
		образом:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_11.png" alt="Блок информации о видео со списком моделей" width="474" height="210"/><br/>
		<span>Блок информации о видео со списком моделей.</span>
	</p>
	<h3 id="section_models_other2">4.2. Добавление списка моделей на главную страницу сайта</h3>
	<p>
		В последнем разделе рассмотрим случай, когда вам нужно добавить вывод новых данных на уже существующую
		страницу сайта. Для примера, добавим вывод на главную страницу сайта 5-ти моделей, которые участвуют в
		наибольшем количестве видео. Для этого на списке страниц найдем главную страницу сайта и откроем
		редактирование ее шаблона:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_12.png" alt="Главная страница сайта на списке страниц в панели администрирования" width="821" height="128"/><br/>
		<span>Главная страница сайта на списке страниц в панели администрирования.</span>
	</p>
	<p>
		Добавим вставку блока <b>list_models</b> в том месте, где мы хотим его видеть на странице:
	</p>
	<p class="code">
		{{$smarty.ldelim}}assign var=page_title value="Demo Tube Website"{{$smarty.rdelim}}<br/>
		{{$smarty.ldelim}}include file="header_general.tpl"{{$smarty.rdelim}}<br/>
		<br/>
		&lt;div id="data"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="wide_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Videos Watched Right Now"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="list_videos" block_name="Most Recent Videos"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="pagination" block_name="Most Recent Videos Pagination"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div id="side_col"&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}include file="search_videos_block.tpl"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>{{$smarty.ldelim}}insert name="getBlock" block_id="list_models" block_name="Top 5 Models"{{$smarty.rdelim}}</b><br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}insert name="getBlock" block_id="tags_cloud" block_name="Tags"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$smarty.ldelim}}include file="side_advertising.tpl"{{$smarty.rdelim}}<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;/div&gt;<br/>
		&nbsp;&nbsp;&nbsp;&nbsp;&lt;div class="g_clear"&gt;&lt;/div&gt;<br/>
		&lt;/div&gt;<br/>
		<br/>
		{{$smarty.ldelim}}include file="footer_general.tpl"{{$smarty.rdelim}}
	</p>
	<p>
		Сохраним изменения шаблона страницы. После этого в списке блоков страницы появится новый блок
		<b>Top 5 Models</b>. Откроем его и настроим 2 параметра:
	</p>
	<ul>
		<li>
			<b>items_per_page</b> - установим значение <b>5</b>. Это заставит блок выбрать только 5 элементов из
			базы данных.
		</li>
		<li>
			<b>sort_by</b> - в списке возможных сортировок выберем значение <b>Кол-во видео desc</b>.
		</li>
	</ul>
	<p>
		После сохранения изменений блока посмотрим на главную страницу сайта. Там появилось то, что мы и хотели
		получить:
	</p>
	<p class="screenshot">
		<img src="docs/screenshots/website_ui_tutorial_13.png" alt="Список из 5 моделей на главной странице сайта" width="500" height="325"/><br/>
		<span>Список из 5 моделей на главной странице сайта.</span>
	</p>
</div>