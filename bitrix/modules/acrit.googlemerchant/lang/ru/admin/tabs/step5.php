<?
$MESS["ACRIT_GOOGLEMERCHANT_NE_VYBRANO"] = "-- не выбрано --";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_FIELD"] = "Свойство или поле";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONST"] = "Постоянное значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_COMPLEX"] = "Комплексное значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE"] = "Композитное значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_TRUE"] = "Условие выполнено";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_FALSE"] = "Условие не выполнено";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_ADD"] = "Добавить";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ADD_PART_TO_COMPOSITE_FIELD"] = "Добавить поле в композитное значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_REQUIRED"] = "Обязательное";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION"] = "Условие";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HEADER"] = "Настройка полей экспорта";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DESCRIPTION"] = "
    <b>\"ТЕГИ ВЫГРУЗКИ\"</b> заменяются значениями. Если значение не задано или неопределено,<br/>
    то тег в выгрузке предложения будет пропущен. Если установлена галочка <b>\"обязательное\"</b> поле и тег<br/>
    не определен или пропущен, то товарное предложение будет исключено из выгрузки.<br/>
    Если установлена галочка <b>\"условие\"</b>, то для каждого этого значения товарного предложения оно будет проверяться<br/>.
    Если галочка <b>\"условие\"</b> установлена и блока условий нет, снимите галочку и установите снова.<br/>
";

$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER"] = "Разделитель";

$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_PROCESS_LOGIC"] = "Обрабатывать полученное значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY"] = "Удалять тег, если значение не установлено";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_FORCE"] = "Удалять тег целиком (вне зависимости от атрибутов), если значение не установлено";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ROWFORCE"] = "Удалять тег целиком на уровне структуры выгрузки (FORCE), если значение не установлено";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES"] = "Удалять пустые атрибуты в теге";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_URL_ENCODE"] = "URL-кодирование строки";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_CASE"] = "Изменить регистр";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE"] = "Экранировать спецсимволы";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_CUT"] = "Вырезать спецсимволы";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_TO_TXT"] = "Перевести HTML в TXT";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_SKIP_UNTERM_ELEMENT"] = "Пропустить некорректное предложение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_EXPORT_ROWCATEGORY_PARENT_LIST"] = "Выгружать цепочку категорий";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_TO_STRING"] = "Преобразовать значения множественного тега в строку";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_DIVIDER"] = "Разделитель преобразования множественного тега в строку";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_LIMIT"] = "Количество выбираемых значений из множественного тега";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_FORMAT"] = "Схема обработки множественного тега";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_TEXT_LIMIT"] = "Количество символов в обрезанной строке";

$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_CONDITION_ADD"] = "Добавить";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_DATA"] = "Конвертация данных";

$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION"] = "Количество десятичных знаков (округление)";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_UP"] = "Округлять в большую сторону";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_DOWN"] = "Округлять в меньшую сторону";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_EVEN"] = "Округлять в сторону ближайшего четного знака";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_ODD"] = "Округлять в сторону ближайшего нечетного знака";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE"] = "Использовать округлений цен из настроек Интернет-магазина";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE_CODE"] = "";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE"] = "Получить минимальную цену торговых предложений";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_CODE"] = "Название тега выгрузки минимальной цены";

$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_REQUIRED_HELP"] = "Установка параметра означает добавление проверки на обязательность заполнения данного тега и в случае, если в элементе инфоблока будут отсутствовать значения, все элементы у которых будет отсутствовать значение, попадут в лог ошибок";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_HELP"] = "Включение условий формирования тега. Позоляет формировать нужные значения в файле экспорта в зависимости от исходных данных. Например, если у вас в свойстве элемента инфоблока присутствует значение \"студия\", а Яндекс.Недвижимость принимает только значения в цифрах, то вы, используя условия, можете переустановить значения в конечном файле, добавив условие, что если в тег попадает значение \"студия\", его надо устанавливать равным \"1\". И так же можно создавать группы условий для более сложных вариантов настройки";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_PROCESS_LOGIC_HELP"] = "Установка этой опции позволяет получить конечные значения выбранного набора данных. Например, в случае необходимости выбора из свойства инфоблока типа \"Привязка к элементам\" значений привязанных элементов опцию необходимо установить. В случае же необходимости получения только ID самих привязанных элементов - снять";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_HELP"] = "Установка этой опции позволит полностью удалить даже сам тег из файла в случае, если ему устанавливается пустое значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_FORCE_HELP"] = "Установка этой опции позволит полноcтью удалить тег ВМЕСТЕ СО ВСЕМИ АТРИБУТАМИ из файла в случае, если ему устанавливается пустое значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ROWFORCE_HELP"] = "Установка этой опции позволит полноcтью удалить физический тег (FORCE) на уровне структуры выгрузки из файла в случае, если данному тегу выгрузки устанавливается пустое значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ATTRIBUTES_HELP"] = "Установка этой опции позволит полностью удалить атрибуты тега из файла в случае, если им устанавливается пустое значение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_URL_ENCODE_HELP"] = "Установка этой опции включает механизм кодирования при передаче значений свойств элементов инфоблока для замены символов, которые используются как служебные.<br/><br/> Тут полный список кодирования:<br/> <a href=\"http://web-developer.name/urlcode/\" target=\"_blank\">http://web-developer.name/urlcode/</a>";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_CASE_HELP"] = "Установка данного флага позволяет привести все значения к нижнему регистру";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_HELP"] = "Установка данного флага позволяет экранировать все спецсимволы, передающиеся в значениях свойств элементов инфоблоков";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_CUT_HELP"] = "Установка данного флага позволяет вырезать все спецсимволы, передающиеся в значениях свойств элементов инфоблоков";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_TO_TXT_HELP"] = "Установка данного флага позволяет перевести все значениях свойств элементов инфоблоков из вида HTML к текстововму, вырезав все HTML теги из исходного кода";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_SKIP_UNTERM_ELEMENT_HELP"] = "Пропустить некорректное предложение";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_EXPORT_ROWCATEGORY_PARENT_LIST_HELP"] = "Выгружать в цепочку полное дерево (вверх от выбранной в теге категории) категорий";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_TO_STRING_HELP"] = "Установка данного флага позволяет трасформировать значения множественного тега в строку через разделитель. По умолчанию: пробел";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_DIVIDER_HELP"] = "Разделитель, размещаемый между значениями множественного тега, при преобразовании в строку";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_LIMIT_HELP"] = "Количество выбираемых значений из множественного тега";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_FORMAT_HELP"] = "Указывается схема обработки множественного тега. Например, следующая запись: 1, 3-5, 12 - выберет 1, 3, 4, 5 и 12 значение из множественного тега.";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_TEXT_LIMIT_HELP"] = "Установка количетсва символов, до которого будет обрезана строка в файле выгрузки";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_DATA_HELP"] = "Установка соответствия конвертируемых значений в поле";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION_HELP"] = "Количество десятичных знаков дробной части";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE_HELP"] = "Функция получения настроек округления из настроек Интернет-магазина";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE_CODE_HELP"] ="";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_HELP"] = "Функция получения минимальной цены <br/>выбранного типа среди торговых предложений товара";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_CODE_HELP"] ="Название <b>#ТЕГИ ВЫГРУЗКИ#</b><br/>которому будет присвоена<br/>минимальная цена торговых предложений";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION_HELP"] = "Количество десятичных знаков дробной части";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER_HELP"] = "Укажите разделитель между полями композитного значения";
?>