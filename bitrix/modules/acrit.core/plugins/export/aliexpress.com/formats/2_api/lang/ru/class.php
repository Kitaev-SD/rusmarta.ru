<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';

// General
$MESS[$strLang.'NAME'] = 'Aliexpress.com (обмен через API)';

// Settings
$MESS[$strSName.'TOKEN'] = 'Токен для подключения';
$MESS[$strSHint.'TOKEN'] = 'Укажите здесь токен для обмена данными модуля с вашим кабинетом продавца';
$MESS[$strLang.'TOKEN_CHECK'] = 'Проверить доступ';
$MESS[$strLang.'TOKEN_HELP'] = 'Для получения токена авторизации вам необходимо, будучи авторизованным на AliExpress под аккаунтом продавца, перейти по нижеследующей ссылке и выдать приложению "Интеграция с Битрикс24" требуемые разрешения. После этого вы будете перенаправлены на страницу с токеном. Скопируйте его и вставьте в поле "Токен авторизации".';
$MESS[$strLang.'GET_TOKEN'] = 'Получить токен авторизации';
$MESS[$strLang.'CHECK_ERROR'] = 'Ошибка: ';
$MESS[$strLang.'CHECK_SUCCESS'] = 'Успешно';
$MESS[$strSName.'SECTIONS'] = 'Раздел каталога';
$MESS[$strSHint.'SECTIONS'] = 'Выберите раздел товароного каталога AliExpress, в который будут выгружаться товары данного профиля. От этого выбора будет зависеть набор полей, доступных для выгрузки.';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Общая информация';

$MESS[$strName.'sku_code'] = 'Артикул';
$MESS[$strHint.'sku_code'] = 'Артикул или штрихкод товара';
$MESS[$strName.'title'] = 'Название товара';
$MESS[$strHint.'title'] = 'Название - это категория, бренд, модель и дополнительные атрибуты. Например, «Кроссовки летние AliExpress женские для города и прогулок TMALL W TM55555».';
$MESS[$strName.'locale'] = 'Локализация';
$MESS[$strHint.'locale'] = 'Языковой код. Варианты: #VARIANTS#';
$MESS[$strName.'description'] = 'Описание товара';
$MESS[$strHint.'description'] = 'Текст описания товара может содержать HTML-теги.';
$MESS[$strName.'brand_name'] = 'Бренд';
$MESS[$strHint.'brand_name'] = 'Название бренда';
$MESS[$strName.'images'] = ' Изображения';
$MESS[$strHint.'images'] = 'Будет загружено до шести изображений.';
$MESS[$strName.'product_units_type'] = 'Единица измерения';
$MESS[$strHint.'product_units_type'] = 'Числовой код единицы измерения. Варианты: #VARIANTS#';
$MESS[$strName.'inventory_deduction_strategy'] = 'Вычет запасов';
$MESS[$strHint.'inventory_deduction_strategy'] = 'После заказа товара - place_order_withhold, после оплаты - payment_success_deduct';
$MESS[$strName.'inventory'] = 'Остаток';
$MESS[$strHint.'inventory'] = 'Остаток товара на складе';
$MESS[$strName.'price'] = 'Цена';
$MESS[$strHint.'price'] = 'Розничная цена товара';
$MESS[$strName.'discount_price'] = 'Цена со скидкой';
$MESS[$strHint.'discount_price'] = 'Цена с учетом скидки';
$MESS[$strName.'package_weight'] = 'Вес в упаковке';
$MESS[$strHint.'package_weight'] = 'Используется для расчета стоимости и возможности доставки';
$MESS[$strName.'package_length'] = 'Размер в упаковке (Д), см';
$MESS[$strHint.'package_length'] = 'Используется для расчета стоимости и определения возможности доставки.';
$MESS[$strName.'package_height'] = 'Размер в упаковке (В), см';
$MESS[$strHint.'package_height'] = 'Используется для расчета стоимости и определения возможности доставки.';
$MESS[$strName.'package_width'] = 'Размер в упаковке (Ш), см';
$MESS[$strHint.'package_width'] = 'Используется для расчета стоимости и определения возможности доставки.';
$MESS[$strName.'shipping_preparation_time'] = 'Время подготовки заказа, в днях';
$MESS[$strHint.'shipping_preparation_time'] = 'Время, за которое данный товар должен быть отправлен покупателю (отправленным заказ считается после введения трек-номера). Отсчет начинается с момента оплаты.';
$MESS[$strName.'shipping_template_id'] = 'Шаблон доставки';
$MESS[$strHint.'shipping_template_id'] = 'Шаблон доставки – это сценарий доставки товара, включающий в себя все ваши курьерские службы. Вариант по-умолчанию: Почта России. Другие варианты: #VARIANTS#';
$MESS[$strName.'service_template_id'] = 'Шаблон услуг';
$MESS[$strHint.'service_template_id'] = 'Варианты: #VARIANTS#';
?>