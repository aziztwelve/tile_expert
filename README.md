# Tile Price Scraper API (Symfony 7.4)

REST/SOAP API для получения цен на плитку и управления заказами с поддержкой полнотекстового поиска через Manticore Search.

## Технологический стек

- **Framework**: Symfony 7.4
- **PHP**: 8.3
- **Database**: PostgreSQL 15
- **Search**: Manticore Search
- **Containerization**: Docker & Docker Compose
- **Testing**: PHPUnit

## Быстрый старт

### Требования

- Docker 20.10+
- Docker Compose 2.0+
- Make (опционально)

### Запуск через Make

```bash
# Сборка и запуск
make up

# Просмотр логов
make logs

# Остановка
make down

# Запуск тестов
make test

# Пересборка
make rebuild

# Миграции БД
make migrate
```

### Запуск через Docker Compose

```bash
# Сборка и запуск
docker-compose up --build -d

# Просмотр логов
docker-compose logs -f app

# Остановка
docker-compose down

# Запуск тестов
docker-compose exec app php bin/phpunit

# Миграции
docker-compose exec app php bin/console doctrine:migrations:migrate -n
```

## Конфигурация

Настройки в `.env`:

```env
# APP
APP_ENV=prod
APP_SECRET=your-secret-key-here
APP_PORT=8000

# Database
DATABASE_URL="postgresql://postgres:postgres@db:5432/tiledb?serverVersion=15&charset=utf8"
POSTGRES_HOST=db
POSTGRES_PORT=5432
POSTGRES_DB=tiledb
POSTGRES_USER=postgres
POSTGRES_PASSWORD=postgres

# Manticore Search
MANTICORE_HOST=manticore
MANTICORE_PORT=9308
```

## API Endpoints

После запуска доступна документация:
- **Swagger UI**: http://localhost:8000/api/doc
- **OpenAPI JSON**: http://localhost:8000/api/doc.json

### 1. Получение цены плитки

**Endpoint:** `GET /api/v1/price`

**Параметры:**
- `factory` (string, required) - Производитель
- `collection` (string, required) - Коллекция
- `article` (string, required) - Артикул

**Пример:**
```bash
curl "http://localhost:8000/api/v1/price?factory=cobsa&collection=manual&article=manu7530bcbm-manualbaltic7-5x30"
```

**Ответ:**
```json
{
  "price": 38.99,
  "factory": "cobsa",
  "collection": "manual",
  "article": "manu7530bcbm-manualbaltic7-5x30"
}
```

### 2. Статистика заказов с группировкой

**Endpoint:** `GET /api/v1/orders/stats`

**Параметры:**
- `group_by` (string, required) - Группировка: `day`, `month`, `year`
- `page` (int, optional, default: 1) - Номер страницы
- `page_size` (int, optional, default: 10) - Размер страницы

**Пример:**
```bash
curl "http://localhost:8000/api/v1/orders/stats?group_by=month&page=1&page_size=10"
```

**Ответ:**
```json
{
  "page": 1,
  "page_size": 10,
  "total_pages": 2,
  "total_items": 12,
  "data": [
    {
      "period": "2024-01",
      "count": 45
    },
    {
      "period": "2024-02",
      "count": 52
    }
  ]
}
```

### 3. Создание заказа (SOAP)

**Endpoint:** `POST /api/v1/soap/orders`

**Headers:**
- `Content-Type: text/xml`

**Пример запроса:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CreateOrder>
      <customer_name>John Doe</customer_name>
      <customer_email>john@example.com</customer_email>
      <product_name>Tile Baltic</product_name>
      <quantity>10</quantity>
      <price>38.99</price>
    </CreateOrder>
  </soap:Body>
</soap:Envelope>
```

**Пример с curl:**
```bash
curl -X POST http://localhost:8000/api/v1/soap/orders \
  -H "Content-Type: text/xml" \
  -d '<?xml version="1.0"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CreateOrder>
      <customer_name>John Doe</customer_name>
      <customer_email>john@example.com</customer_email>
      <product_name>Tile Baltic</product_name>
      <quantity>10</quantity>
      <price>38.99</price>
    </CreateOrder>
  </soap:Body>
</soap:Envelope>'
```

**Ответ:**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <CreateOrderResponse>
      <order_id>123</order_id>
      <status>created</status>
      <message>Order created successfully</message>
    </CreateOrderResponse>
  </soap:Body>
</soap:Envelope>
```

### 4. Получение одного заказа

**Endpoint:** `GET /api/v1/orders/{id}`

**Пример:**
```bash
curl "http://localhost:8000/api/v1/orders/123"
```

**Ответ:**
```json
{
  "id": 123,
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "product_name": "Tile Baltic",
  "quantity": 10,
  "price": 38.99,
  "total": 389.90,
  "status": "pending",
  "created_at": "2024-01-15T10:30:00+00:00",
  "updated_at": "2024-01-15T10:30:00+00:00"
}
```

### 5. Поиск через Manticore Search

**Endpoint:** `GET /api/v1/orders/search`

**Параметры:**
- `q` (string, required) - Поисковый запрос
- `page` (int, optional, default: 1) - Номер страницы
- `page_size` (int, optional, default: 10) - Размер страницы

**Пример:**
```bash
curl "http://localhost:8000/api/v1/orders/search?q=Baltic&page=1&page_size=10"
```

**Ответ:**
```json
{
  "page": 1,
  "page_size": 10,
  "total_items": 5,
  "total_pages": 1,
  "results": [
    {
      "id": 123,
      "customer_name": "John Doe",
      "customer_email": "john@example.com",
      "product_name": "Tile Baltic",
      "quantity": 10,
      "price": 38.99,
      "relevance": 2.456
    }
  ]
}
```

## Тестирование

```bash
# Запуск всех тестов
make test

# Или через docker-compose
docker-compose exec app php bin/phpunit

# С покрытием кода
docker-compose exec app php bin/phpunit --coverage-html coverage

# Конкретный тест
docker-compose exec app php bin/phpunit tests/Controller/PriceControllerTest.php
```

## Анализ и улучшение базы данных

### Проблемы исходной схемы

#### 1. **Отсутствие индексов**
```sql
-- Медленные запросы при поиске и группировке
SELECT * FROM orders WHERE customer_email = 'test@example.com'; -- Full table scan
```

#### 2. **Нет внешних ключей**
```sql
-- Можно вставить несуществующий customer_id
INSERT INTO orders (customer_id, ...) VALUES (99999, ...);
-- Нарушение целостности данных
```

#### 3. **Денормализация**
```sql
-- Дублирование данных клиента в каждом заказе
orders: customer_name, customer_email (повторяется для каждого заказа)
-- Проблема при изменении email клиента - нужно обновлять все заказы
```

#### 4. **Отсутствие ENUM типов**
```sql
-- status VARCHAR без ограничений
INSERT INTO orders (status) VALUES ('invalid_status'); -- Допустимо!
```

#### 5. **Нет временных меток**
```sql
-- Отсутствуют created_at, updated_at
-- Невозможно отследить когда создан/изменен заказ
```

#### 6. **VARCHAR без ограничений длины**
```sql
customer_name VARCHAR -- Может быть любой длины, проблемы с производительностью
```

#### 7. **Отсутствие constraints**
```sql
price DECIMAL -- Может быть отрицательным
quantity INT -- Может быть отрицательным или нулевым
```

#### 8. **Денормализация цены**
```sql
-- total вычисляемое поле хранится в БД
-- При изменении price или quantity нужно пересчитывать total
```

### Улучшенная схема

См. файл `migrations/Version20240115000002.php` и `database/improved_schema.sql`

**Ключевые улучшения:**

1. ✅ **Нормализация**: Выделены таблицы `customers` и `products`
2. ✅ **Индексы**: На часто используемые поля (email, created_at, status)
3. ✅ **Foreign Keys**: С каскадными действиями
4. ✅ **ENUM типы**: Для статусов заказов
5. ✅ **Временные метки**: created_at, updated_at с автообновлением
6. ✅ **Constraints**: Проверки на положительные значения
7. ✅ **Партиционирование**: По датам для больших объемов (опционально)
8. ✅ **Вычисляемые поля**: total как generated column

## Структура проекта

```
.
├── config/
│   ├── packages/
│   ├── routes/
│   └── services.yaml
├── migrations/
│   ├── Version20240115000001.php  # Initial schema
│   └── Version20240115000002.php  # Improved schema
├── docker/
│   ├── local/
│   │   ├── nginx/
│   │   ├── php-fpm/
├── src/
│   ├── Controller/
│   │   ├── PriceController.php
│   │   ├── OrderController.php
│   │   └── SoapController.php
│   ├── Entity/
│   │   ├── Order.php
│   │   ├── Customer.php
│   │   └── Product.php
│   ├── Repository/
│   │   ├── OrderRepository.php
│   │   ├── CustomerRepository.php
│   │   └── ProductRepository.php
│   ├── Service/
│   │   ├── PriceScraperService.php
│   │   ├── ManticoreSearchService.php
│   │   └── OrderService.php
│   └── Kernel.php
├── tests/
│   ├── Controller/
│   │   ├── PriceControllerTest.php
│   │   ├── OrderControllerTest.php
│   │   └── SoapControllerTest.php
│   └── Service/
│       ├── PriceScraperServiceTest.php
│       └── ManticoreSearchServiceTest.php
├── database/
│   ├── init.sql
│   └── improved_schema.sql
├── docker-compose.yml
├── Makefile
├── composer.json
└── README.md
```
