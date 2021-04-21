<?
$MESS["ACRIT_GOOGLEMERCHANT_CONDITIONS"] = "Условия экспорта (фильтр)";

$MESS["ACRIT_GOOGLEMERCHANT_CURRENCU"] = "Настройки валют";

$MESS["ACRIT_GOOGLEMERCHANT_CURRENCU_RUB"] = "Российский рубль";
$MESS["ACRIT_GOOGLEMERCHANT_CURRENCU_HEAD_CURRENCY"] = "Валюта";
$MESS["ACRIT_GOOGLEMERCHANT_CURRENCU_HEAD_RATE"] = "Курс";
$MESS["ACRIT_GOOGLEMERCHANT_CURRENCU_HEAD_CORRECT"] = "Коррекция курса";
$MESS["ACRIT_GOOGLEMERCHANT_CURRENCY_CONVERT_CURRENCY"] = "Конвертировать курс валют";
$MESS["ACRIT_GOOGLEMERCHANT_CURRENCU_HEAD_CONVERTTO"] = "Конвертировать в";
$MESS["ACRIT_GOOGLEMERCHANT_MARKET_CURRENCY_DESCRIPTION"] = "
    Если галочка <b>\"Конвертировать курс валют\" не установлена</b>, то будет произведена коррекция выбранных<br/>
    валют, на выбранное значение в процентах.
    Корректировать можно в обе стороны. Если в большую сторону, то указывается число(дробное или целое),<br/>
    если коррекция в обратную сторону, то перед значением ставится минус.<br/>
    Если галочка <b>\"Конвертировать курс валют\" установлена</b>, то выбранные валюты, с заданными символьными<br/>
    значениями(должны совпадать со значениями в настройках валют), будут сконвертированы в выбранные валюты(<b>\"Конвертировать в\"</b>)<br/>
    в соответствии с выбранным курсом.
";

$MESS["ACRIT_GOOGLEMERCHANT_CURRENCY_CONVERT_CURRENCY_HELP"] = "Используется для пересчета курсов валют к одной";

$MESS["ACRIT_GOOGLEMERCHANT_CONVERT_FIELDSET_CONDITION_ADD"] = "Добавить";
$MESS["ACRIT_GOOGLEMERCHANT_CONVERT_FIELDSET_HEADER"] = "Конвертация данных";
$MESS["ACRIT_GOOGLEMERCHANT_CONVERT_FIELDSET_DESCRIPTION"] = "
    <b>\"КОНВЕРТАЦИЯ ЗНАЧЕНИЙ\"</b> позволяет заменить набор значений в формируемом файле выгрузки.<br/><br/>
    В левой колонке формируется набор данных для замены. В правой - набор, на который будет произведена замена.<br/><br/>
    Например, в случае указания в левой колонке значения \"футбол\", а в правой значения \"хоккей\" при генерации файла выгрузки во всех данных, полученных из полей экспорта, будет произведена прямая замена слова \"футбол\" на слово \"хоккей\"
";

$MESS["ACRIT_GOOGLEMERCHANT_SETUP_VALIDATE_CONDITIONS"] = "Применять только для существующих свойств инфоблока";
$MESS["ACRIT_GOOGLEMERCHANT_SETUP_VALIDATE_CONDITIONS_HELP"] = "В случае указания данной опции выборка будет применяться только к существующим свойствам инфоблока";

$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ADDITIONAL_SETTINGS_HEADER"] = "Дополнительные настройки";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ADDITIONAL_SETTINGS_SKU_USE_CANONICAL"] = "Использовать канонический URL в торговых предложениях";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ADDITIONAL_SETTINGS_OFFER_WITH_SKU_USE_QUANTITY"] = "Учитывать остаток товаров при работе опции «Товары с элементами торговых предложений»";
$MESS["ACRIT_GOOGLEMERCHANT_FIELDSET_ADDITIONAL_SETTINGS_OFFER_WITH_SKU_USE_QUANTITY_HINT"] = "При использовании опции выгрузки «Товары с элементами торговых предложений» для выгрузки товара выбирается первое ТП для этого товара (с сортировкой по цене и по ID) для использование его данных в товаре.<br/><br/>Данная опция позволяет при поиске учитывать остаток ТП - т.е. в поиске будут учитываться только ТП в наличии.";
?>