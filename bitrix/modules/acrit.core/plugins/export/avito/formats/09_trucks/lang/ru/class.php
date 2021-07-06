<?
$strMessPrefix = 'ACRIT_EXP_AVITO_TRUCKS_';

// General
$MESS[$strMessPrefix.'NAME'] = 'Авито (Грузовики и спецтехника)';

// Fields
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = 'Категория товара';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = 'Категория товара — строка «Грузовики и спецтехника»';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DEFAULT'] = 'Грузовики и спецтехника';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = 'Состояние';
	$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = 'Состояние вещи — одно из значений списка:<br/>
<ul>
	<li>Новое</li>
	<li>Б/у</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_NAME'] = 'Пробег';
	$MESS[$strMessPrefix.'FIELD_KILOMETRAGE_DESC'] = 'Пробег транспортного средства в км - целое число, в диапазоне от 1 до 1000000';
$MESS[$strMessPrefix.'FIELD_TECHNICAL_PASSPORT_NAME'] = 'Наличие ПТС';
	$MESS[$strMessPrefix.'FIELD_TECHNICAL_PASSPORT_DESC'] = 'Наличие паспорта транспортного средства (ПТС) - одно из значений списка:
<ul>
	<li>Нет</li>
	<li>В наличии</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ENGINE_HOURS_NAME'] = 'Моточасы';
	$MESS[$strMessPrefix.'FIELD_ENGINE_HOURS_DESC'] = 'Моточасы транспортного средства - целое число, в диапазоне от 1 до 1000000';
$MESS[$strMessPrefix.'FIELD_VIN_NAME'] = 'VIN-номер';
	$MESS[$strMessPrefix.'FIELD_VIN_DESC'] = 'VIN-номер (vehicle identification number) — строка из 17 символов.';
$MESS[$strMessPrefix.'FIELD_MAKE_NAME'] = 'Марка';
	$MESS[$strMessPrefix.'FIELD_MAKE_DESC'] = 'Марка - марка транспортного средства, текстовое значение из <a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Справочника</a> (Brand).';
$MESS[$strMessPrefix.'FIELD_MODEL_NAME'] = 'Модель';
	$MESS[$strMessPrefix.'FIELD_MODEL_DESC'] = 'Модель - модель транспортного средства, текстовое значение из <a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Справочника</a> (Model).';
$MESS[$strMessPrefix.'FIELD_YEAR_NAME'] = 'Год выпуска';
	$MESS[$strMessPrefix.'FIELD_YEAR_DESC'] = 'Год выпуска от 1905 до '.date('Y');
$MESS[$strMessPrefix.'FIELD_BODY_TYPE_NAME'] = 'Тип кузова';
	$MESS[$strMessPrefix.'FIELD_BODY_TYPE_DESC'] = 'Тип кузова - тип кузова транспортного средства, текстовое значение из <a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Справочника</a> (Modification).';
$MESS[$strMessPrefix.'FIELD_TYPE_OF_VEHICLE_NAME'] = 'Тип техники';
	$MESS[$strMessPrefix.'FIELD_TYPE_OF_VEHICLE_DESC'] = 'Тип техники.
<ul>
	<li><a href="https://autoload.avito.ru/format/trailer_catalog.xml" target="_blank">Каталог прицепов</a></li>
	<li><a href="https://autoload.avito.ru/format/agricultural_machinery.xml" target="_blank">Каталог сельхозтехники</a></li>
	<li><a href="https://autoload.avito.ru/format/autocrane.xml" target="_blank">Каталог автокранов</a></li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TYPE_OF_VEHICLE_NAME'] = 'Тип техники';
	$MESS[$strMessPrefix.'FIELD_TYPE_OF_VEHICLE_DESC'] = '
	<ul>
		<li>Используется для прицепов</li>
		<li>Используется для сельхозтехники</li>
		<li>Используется для автокранов</li>
		<li>Используется для строительной техники</li>
		<li>Используется для погрузчиков</li>
		<li>Используется для экскаваторов</li>
		<li>Используется для бульдозеров</li>
		<li>Используется для автобусов</li>
		<li>Используется для техники для лесозаготовки</li>
		<li>Используется для автодомов</li>
		<li>Используется для коммунальной техники</li>
		<li>Используется для навесного оборудования</li>
		<li>Используется для других видов техники</li>
	</ul>
	<ul>
		<li>Обязателен для автобусов</li>
		<li>Обязателен для прицепов</li>
		<li>Обязателен для строительной техники</li>
		<li>Обязателен для экскаваторов</li>
		<li>Обязателен для бульдозеров</li>
		<li>Обязателен для погрузчиков</li>
		<li>Обязателен для других видов техники</li>
		<li>Обязателен для навесного оборудования</li>
		<li>Обязателен для автокранов</li>
		<li>Обязателен для сельхозтехники</li>
		<li>Обязателен для техники для лесозаготовки</li>
	</ul>
	<ul>
		<li><a href="https://autoload.avito.ru/format/truck_catalog.xml" target="_blank">Каталог грузовиков</a></li>
		<li><a href="https://autoload.avito.ru/format/cab_catalog.xml" target="_blank">Каталог тягачей</a></li>
		<li><a href="https://autoload.avito.ru/format/trailer_catalog.xml" target="_blank">Каталог прицепов</a></li>
		<li><a href="https://autoload.avito.ru/format/agricultural_machinery.xml" target="_blank">Каталог сельхозтехники</a></li>
		<li><a href="https://autoload.avito.ru/format/autocrane.xml" target="_blank">Каталог автокранов</a></li>
		<li><a href="https://autoload.avito.ru/format/construction_machinery.xml" target="_blank">Каталог строительной техники</a></li>
		<li><a href="https://autoload.avito.ru/format/loader.xml" target="_blank">Каталог погрузчиков</a></li>
		<li><a href="https://autoload.avito.ru/format/excavator.xml" target="_blank">Каталог экскаваторов</a></li>
		<li><a href="https://autoload.avito.ru/format/bulldozer.xml" target="_blank">Каталог бульдозеров</a></li>
		<li><a href="https://autoload.avito.ru/format/bus.xml" target="_blank">Каталог автобусов</a></li>
		<li><a href="https://autoload.avito.ru/format/logging_machinery.xml" target="_blank">Каталог техники для лесозаготовки</a></li>
		<li><a href="https://autoload.avito.ru/format/motorhome.xml" target="_blank">Каталог автодомов</a></li>
		<li><a href="https://autoload.avito.ru/format/municipal_machinery.xml" target="_blank">Каталог коммунальной техники</a></li>
		<li><a href="https://autoload.avito.ru/format/machinery_attachments.xml" target="_blank">Каталог навесного оборудования</a></li>
	</ul>
	
	';
$MESS[$strMessPrefix.'FIELD_TYPE_OF_TRAILER_NAME'] = 'Тип прицепа';
	$MESS[$strMessPrefix.'FIELD_TYPE_OF_TRAILER_DESC'] = 'Тип прицепа, текстовое значение из <a href="https://autoload.avito.ru/format/trailer_catalog.xml" target="_blank">Справочника</a> (TypeOfTrailer).';
$MESS[$strMessPrefix.'FIELD_TRAILER_VIN_NAME'] = 'VIN-номер';
	$MESS[$strMessPrefix.'FIELD_TRAILER_VIN_DESC'] = 'VIN-номер (vehicle identification number) — строка из 17 символов.';
$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_NAME'] = 'Вид техники';
	$MESS[$strMessPrefix.'FIELD_GOODS_TYPE_DESC'] = 'Вид техники — одно из значений списка:<br/>
<ul>
	<li>Автобусы<li>
	<li>Автодома<li>
	<li>Автокраны<li>
	<li>Бульдозеры<li>
	<li>Грузовики<li>
	<li>Коммунальная техника<li>
	<li>Лёгкий транспорт<li>
	<li>Погрузчики<li>
	<li>Прицепы<li>
	<li>Сельхозтехника<li>
	<li>Строительная техника<li>
	<li>Техника для лесозаготовки<li>
	<li>Тягачи<li>
	<li>Экскаваторы<li>
</ul>';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = 'Название объявления';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = 'Название объявления — строка до 50 символов.<br/>
Примечание: не пишите в название цену и контактную информацию — для этого есть отдельные поля — и не используйте слово «продам».';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = 'Цена в рублях';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = 'Цена в рублях — целое число.';
$MESS[$strMessPrefix.'FIELD_DISPLAY_AREAS_NAME'] = 'Зоны показа объявления';
	$MESS[$strMessPrefix.'FIELD_DISPLAY_AREAS_DESC'] = 'Зоны показа объявления - значения из <a href="https://autoload.avito.ru/format/DisplayAreas.xml" target="_blank">справочника</a>.<br/><br/>
	Поле должно выгружаться как множественное!<br/><br/>
	<b>Внимание, данная функциональность Avito находится на стадии тестирования.</b>';
$MESS[$strMessPrefix.'FIELD_SUB_TYPE_OF_VEHICLE_NAME'] = 'Подтип техники';
	$MESS[$strMessPrefix.'FIELD_SUB_TYPE_OF_VEHICLE_DESC'] = 'Используется для других видов техники. <a href="https://autoload.avito.ru/format/other_transport.xml" target="_blank">Каталог других видов техники</a>';
$MESS[$strMessPrefix.'FIELD_MAKE_KMU_NAME'] = 'Марка крана-манипулятора';
	$MESS[$strMessPrefix.'FIELD_MAKE_KMU_DESC'] = 'Марка крана-манипулятора из <a href="http://autoload.avito.ru/format/crane_arm.xml" target="_blank">Справочника</a> (Make).';
$MESS[$strMessPrefix.'FIELD_MODEL_KMU_NAME'] = 'Модель крана-манипулятора';
	$MESS[$strMessPrefix.'FIELD_MODEL_KMU_DESC'] = 'Модель крана-манипулятора из <a href="http://autoload.avito.ru/format/crane_arm.xml" target="_blank">Справочника</a> (Model).';
$MESS[$strMessPrefix.'FIELD_BRAND_NAME'] = 'Марка лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_BRAND_DESC'] = 'Марка лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (Brand).';
$MESS[$strMessPrefix.'FIELD_BODY_NAME'] = 'Тип кузова лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_BODY_DESC'] = 'Тип кузова лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (Body).';
$MESS[$strMessPrefix.'FIELD_DOORS_COUNT_NAME'] = 'Количество дверей лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_DOORS_COUNT_DESC'] = 'Количество дверей лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (DoorsCount).';
$MESS[$strMessPrefix.'FIELD_GENERATION_NAME'] = 'Поколение лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_GENERATION_DESC'] = 'Поколение лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (Generation).';
$MESS[$strMessPrefix.'FIELD_ENGINE_TYPE_NAME'] = 'Тип двигателя лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_ENGINE_TYPE_DESC'] = 'Тип двигателя лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (EngineType).';
$MESS[$strMessPrefix.'FIELD_DRIVE_TYPE_NAME'] = 'Тип привода лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_DRIVE_TYPE_DESC'] = 'Тип привода лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (DriveType).';
$MESS[$strMessPrefix.'FIELD_TRANSMISSION_NAME'] = 'Тип трансмиссии лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_TRANSMISSION_DESC'] = 'Тип трансмиссии лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (Transmission).';
$MESS[$strMessPrefix.'FIELD_MODIFICATION_NAME'] = 'Модификация лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_MODIFICATION_DESC'] = 'Модификация лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (Modification).';
$MESS[$strMessPrefix.'FIELD_TRIM_NAME'] = 'Комплектация лёгкого коммерческого транспорта';
	$MESS[$strMessPrefix.'FIELD_TRIM_DESC'] = 'Комплектация лёгкого коммерческого транспорта из <a href="http://autoload.avito.ru/format/lcv.xml" target="_blank">Справочника</a> (Trim).';
$MESS[$strMessPrefix.'FIELD_WHEEL_TYPE_NAME'] = 'Руль';
	$MESS[$strMessPrefix.'FIELD_WHEEL_TYPE_DESC'] = 'Руль — одно из значений списка:
	<ul>
		<li>Левый</li>
		<li>Правый</li>
	</ul>';
$MESS[$strMessPrefix.'FIELD_OWNERS_BY_DOCUMENTS_NAME'] = 'Количество владельцев по ПТС';
	$MESS[$strMessPrefix.'FIELD_OWNERS_BY_DOCUMENTS_DESC'] = 'Количество владельцев по ПТС — одно из значений списка:
	<ul>
		<li>1</li>
		<li>2</li>
		<li>3</li>
		<li>4+</li>
	</ul>';
$MESS[$strMessPrefix.'FIELD_COLOR_NAME'] = 'Цвет кузова';
	$MESS[$strMessPrefix.'FIELD_COLOR_DESC'] = 'Цвет кузова — одно из значений списка:
	<ul>
		<li>Белый</li>
		<li>Чёрный</li>
		<li>Серебряный</li>
		<li>Коричневый</li>
		<li>Золотой</li>
		<li>Бежевый</li>
		<li>Красный</li>
		<li>Оранжевый</li>
		<li>Жёлтый</li>
		<li>Зелёный</li>
		<li>Голубой</li>
		<li>Синий</li>
		<li>Фиолетовый</li>
		<li>Пурпурный</li>
		<li>Розовый</li>
	</ul>';
$MESS[$strMessPrefix.'FIELD_ACCIDENT_NAME'] = 'Состояние б/у';
	$MESS[$strMessPrefix.'FIELD_ACCIDENT_DESC'] = 'Состояние б/у — одно из значений списка:
	<ul>
		<li>Битый</li>
		<li>Не битый</li>
	</ul>';
	
?>