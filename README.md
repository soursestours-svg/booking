## Установка

### 1. Клонирование репозитория
```bash
git clone https://github.com/soursestours-svg/booking.git
cd booking
```

Далее вы можете выбрать один из двух способов установки: **локально** или с помощью **Docker**.

---

### Вариант А: Локальная установка

1.  **Установите зависимости Composer:**
    ```bash
    composer install
    ```

2.  **Создайте и настройте файл окружения:**
    Скопируйте `.env.example` в `.env`:
    ```bash
    cp .env.example .env
    ```
    Затем откройте файл `.env` и укажите свои учетные данные для доступа к базе данных (DB_DATABASE, DB_USERNAME, DB_PASSWORD).

3.  **Сгенерируйте ключ приложения:**
    ```bash
    php artisan key:generate
    ```

4.  **Выполните миграции и сидинг базы данных:**
    ```bash
    php artisan migrate --seed
    ```

5.  **Установите зависимости NPM и соберите ассеты:**
    ```bash
    npm install
    npm run dev
    ```

6.  **Запустите сервер для разработки:**
    ```bash
    php artisan serve
    ```
    Приложение будет доступно по адресу `http://127.0.0.1:80`.

---

### Вариант Б: Установка с помощью Docker

1.  **Создайте и настройте файл окружения:**
    ```bash
    cp .env.example .env
    ```
    Откройте файл `.env` и настройте переменные окружения. Для Docker, хостом базы данных (`DB_HOST`) должно быть имя сервиса из `docker-compose.yml` (например, `mysql` или `db`).

2.  **Соберите и запустите контейнеры:**
    ```bash
    docker-compose up --build -d
    ```
    Эта команда соберет образы и запустит контейнеры в фоновом режиме.

3.  **Выполните команды установки внутри контейнера:**
    Все последующие команды выполняются внутри запущенного контейнера `app`.

    - **Установка зависимостей Composer:**
      ```bash
      docker-compose exec app composer install
      ```
    - **Генерация ключа приложения:**
      ```bash
      docker-compose exec app php artisan key:generate
      ```
    - **Выполнение миграций и сидинга:**
      ```bash
      docker-compose exec app php artisan migrate --seed
      ```
    - **Установка зависимостей NPM и сборка ассетов:**
      ```bash
      docker-compose exec app npm install
      docker-compose exec app npm run dev
      ```

4.  **Готово!** Приложение будет доступно по адресу `http://localhost:8089`.
