# CRM + склад для цветочного магазина

## Быстрые ответы на вопросы
- Рекомендуемый вариант мобайла: **PWA** (один код React/TS/Tailwind, офлайн-кэш, установка на iOS/Android без стора).
- Учет склада: **по штукам/метрам** для всех номенклатур (цветы, упаковка, расходники) + автосписание по рецептам букетов. Букеты хранятся как продукт-«виртуал» без собственного остатка, себестоимость формируется из партий ингредиентов.

## UX каркас
### Мобайл (продавец/флорист, PWA)
Нижняя навигация: **Заказы**, **Касса**, **Склад**, **Клиенты**, **Еще**.
- Заказы: список с поиском и фильтром статуса; кнопка «Создать»; внутри — быстрый выбор типа (доставка/самовывоз/зал), список позиций с крупными карточками, сканер штрихкода (опция).
- Касса: прием оплаты (нал/карта/перевод/смешанная), скидка, закрытие смены, печать/отправка чека.
- Склад: остатки (сортировка по сроку), приход/списание/инвентаризация, отметка брака, FIFO подсказка.
- Клиенты: поиск по телефону/имени, быстрые заметки, напоминания о ДР.
- Еще: отчеты день/неделя, настройки, переключение магазина.

### ПК (владелец/управляющий)
Левое меню, основные таблицы:
- Заказы (фильтры: дата, статус, доставка, оплата, источник)
- Склад: вкладки «Остатки» и «Движения»
- Номенклатура (товары/категории/единицы)
- Сборки (рецепты букетов)
- Клиенты
- Отчеты (день/неделя/месяц, маржа, списания)
- Настройки (магазины, пользователи, роли, интеграции)

## MVP функционал
- **Заказы**: создание ≤60 сек; статусы Новый → В работе → Готов → Выдан/Доставлен → Завершен/Отменен; позиции с количеством и скидкой; оплата наличные/карта/перевод/смешанная; чек/квитанция.
- **Склад**: приход/списание/инвентаризация/корректировка; партии с датой и закупкой; FIFO при списании; опционально срок свежести; автосписание при завершении заказа (бумажные позиции и рецепты букетов).
- **Отчеты**: выручка дня, себестоимость (по закупке), прибыль/маржа, топ списаний/брака.

## Роли и права
- Владелец — полный доступ.
- Админ/управляющий — отчеты, склад, цены, сотрудники.
- Продавец — заказы, касса, клиенты.
- Флорист — статусы заказов, сборки, ограниченные списания.

## Каркас данных (Laravel migrations)
Таблицы и ключевые поля:
- **users**, **roles**, **role_user** (многие-ко-многим). Роли: owner, manager, seller, florist.
- **customers**: id, name, phone (unique), notes, birthday, created_by.
- **products**: id, type(`flower|material|bouquet`), name, unit(`pcs|meter`), sku, active, price_retail, price_min, tax_rate.
- **product_batches**: id, product_id, supplier_id, buy_price, qty_in, qty_left, arrived_at, expires_at(nullable), store_id.
- **inventory_movements**: id, product_id, batch_id(nullable), type(`in|out|adjust|waste`), qty, reason, order_id(nullable), user_id, created_at, note.
- **bouquet_recipes**: id, bouquet_product_id (FK products), name.
- **bouquet_recipe_items**: id, recipe_id, product_id, qty.
- **orders**: id, customer_id, status, delivery_type(`delivery|pickup|instore`), delivery_address, delivery_time, total, discount, paid_total, payment_status, store_id.
- **order_items**: id, order_id, product_id, qty, price, discount.
- **payments**: id, order_id, method(`cash|card|transfer|mixed`), amount, paid_at, shift_id(nullable).
- **cash_shifts**: id, user_id, store_id, opened_at, closed_at, cash_start, cash_end, cash_diff.
- **stores** (если сеть): id, name, address, timezone.
- **suppliers**: id, name, phone, contact.

### Правила списания
- FIFO по `product_batches` (минимальный `arrived_at` у партии с `qty_left>0`).
- Если позиция — продукт-«букет», разложить на ингредиенты из `bouquet_recipe_items` и списать их FIFO.
- Себестоимость заказа = сумма (`qty списано * buy_price партии`).

## API-роуты (Laravel, `api.php`)
- **Auth/профиль**: `POST /auth/login`, `POST /auth/logout`, `GET /me`.
- **Номенклатура**: `GET/POST/PUT/DELETE /products`, `GET /products/{id}`, `GET /products?type=bouquet`.
- **Партии/склад**: `GET /inventory/batches`, `POST /inventory/in`, `POST /inventory/out`, `POST /inventory/adjust`, `POST /inventory/inventory-check`, `GET /inventory/movements`.
- **Рецепты**: `GET/POST/PUT/DELETE /bouquet-recipes`, `GET /bouquet-recipes/{id}/items`.
- **Заказы**: `GET /orders`, `POST /orders`, `GET /orders/{id}`, `PUT /orders/{id}`, `POST /orders/{id}/status`, `POST /orders/{id}/complete` (триггер автосписания), `POST /orders/{id}/cancel`.
- **Оплаты/касса**: `POST /orders/{id}/payments`, `GET /cash-shifts`, `POST /cash-shifts/open`, `POST /cash-shifts/{id}/close`.
- **Клиенты**: `GET/POST/PUT /customers`, `GET /customers/{id}`.
- **Отчеты**: `GET /reports/sales?range=today`, `GET /reports/margin`, `GET /reports/writeoffs`.

## UI-структура (React TS + Tailwind)
- `layout/MobileShell` (нижняя навигация) и `layout/DesktopShell` (левое меню + таблицы).
- Страницы мобайл: `OrdersList`, `OrderCreate`, `CashDesk`, `Inventory`, `Customers`, `More`.
- Страницы ПК: `OrdersTable`, `InventoryStock`, `InventoryMovements`, `Catalog`, `BouquetRecipes`, `CustomersTable`, `Reports`, `Settings`.
- Компоненты: `ProductPicker` (крупные карточки + поиск/штрихкод), `CustomerQuickForm`, `PaymentWidget`, `BatchExpiryBadge`, `StatusStepper`.

### Флоу «Заказ за 30 секунд» (мобайл)
1. Кнопка **«Создать заказ»** → выбрать тип (доставка/самовывоз/зал).
2. Ввод/скан телефона → автоподстановка клиента или быстрый ввод имени.
3. Добавить позиции: крупные карточки популярных букетов + поиск, кнопки +/−, поле скидки.
4. Итог: подсказка себестоимости/маржи, выбор оплаты, «Завершить заказ» → автосписание.
5. Печать/отправка чека (если подключено).

## Этапы внедрения
1. **Проект и авторизация**: роли/права, магазины, сотрудники, смены.
2. **Номенклатура**: товары, единицы, цены, SKU.
3. **Склад**: партии, приход/списание/инвентаризация, FIFO-логика.
4. **Сборки букетов**: рецепты, превью себестоимости.
5. **Заказы**: создание, статусы, оплаты.
6. **Автосписание**: обработчик `order.complete` → FIFO списания/маржа.
7. **Отчеты и закрытие смены**: выручка, маржа, списания, сверка кассы.
8. **Интеграции**: мессенджеры, доставки, печать чеков, импорт.

## Дополнительно
- Реалтайм обновление статусов: Laravel Echo/Pusher (опционально).
- Печать чеков: web-print view + настройки принтера в ПК-версии.
- PWA: манифест, сервис-воркер, офлайн-кэш последнего ассортимента для ускорения продаж.

## Что уже реализовано в Laravel-коде
- **Миграции**: клиенты, поставщики, партии, движения склада, рецепты букетов, позиции заказов, оплаты и смены кассы; расширены товары (тип/единица/sku/минимальная цена/ставка налога) и заказы (клиент, доставка, суммы, статусы оплат).
- **Модели/сервисы**: модели для новых таблиц + сервис `InventoryService` с FIFO-списанием по партиям и обработкой рецептов букетов.
- **API (`routes/api.php` префикс `/crm`)**: CRUD клиентов и рецептов букетов, приход/списание/корректировка склада, создание/просмотр заказов с позициями, завершение заказа с автосписанием, приём оплат, открытие/закрытие кассовой смены.
