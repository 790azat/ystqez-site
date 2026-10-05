# Yst Qez — сайт канала

Сайт YouTube/Instagram-проекта **Yst Qez** — друзей, которые уже 7–8 лет путешествуют вместе и ведут интеллектуальные разговоры.

**Стек:** Laravel 12 · Livewire 3 · Tailwind CSS 4 (Vite) · PostgreSQL (Neon) / SQLite локально · деплой на Vercel (`vercel-php`).

## Возможности

| Раздел | Адрес | Что внутри |
|---|---|---|
| Главная | `/` | Hero с аватаром/баннером канала (без баннера — градиент), статистика из настроек (подписчики YouTube/Instagram), свежие выпуски, популярные темы форума, сетка Instagram, анонс чата, CTA сотрудничества |
| Выпуски | `/videos` | Livewire-каталог: поиск, фильтр по типу (видео / Shorts / эфиры), сортировка, теги, пагинация. Если на странице только Shorts — вертикальная сетка 9:16 |
| Выпуск | `/videos/{slug}` | Плеер `youtube-nocookie.com`. Для Shorts — вертикальный плеер по центру, листание стрелками ↑/↓ (или `j`/`k`), свайпом влево/вправо и кнопками. Кликабельные таймкоды (YouTube IFrame API), лайки сайта, комментарии (гости — с именем, honeypot, rate limit), похожие выпуски, кнопки «поделиться» |
| Чат | `/chat` | Живой чат на `wire:poll` (Vercel не поддерживает websockets), ник для гостей, лимит частоты, удаление админом. Плавающий мини-чат на всех страницах |
| Сотрудничество | `/collab` | Форма заявки: имя, email/Telegram, компания, формат (реклама / интеграция / гость / другое), бюджет, сообщение |
| Форум | `/forum` | Разделы, темы, ответы, закреп/закрытие, регистрация и вход (`/register`, `/login`) |
| Instagram | `/instagram` | Сетка импортированных публикаций со ссылками на instagram.com/p/{shortcode} |
| Админка | `/admin` | Дашборд, видео (CRUD, «избранное», добавление по ссылке через oEmbed, кнопка «Синхронизировать с YouTube» через RSS), заявки со статусами, модерация форума/чата/комментариев, настройки сайта, пользователи и права админа |

## Локальный запуск

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed      # создаст админа из ADMIN_EMAIL / ADMIN_PASSWORD
npm install && npm run dev      # или npm run build
php artisan serve
```

Тесты: `php artisan test`.

## Контент (импорт из JSON)

Сидер (и маршрут установки) импортирует данные из `database/data/`, если файлы есть. Все поля необязательные, импорт идемпотентный (upsert по YouTube id / shortcode):

- `videos.json` — массив `{id, title, description, upload_date (YYYYMMDD), duration (сек), view_count, like_count, thumbnail, type: video|short|live, tags[]}`
- `channel.json` — `{title, description, avatar, banner, subscriber_count}`
- `instagram.json` — `{profile: {followers, biography, profile_pic_url|local_avatar}, posts: [{shortcode, caption, date, type, media_urls[], likes, local_image}]}`

Повторный импорт — кнопка «Импорт из JSON» в `/admin/settings` или `php artisan db:seed`.

## Медиафайлы (картинки) — через CDN, не через Vercel

Локальные изображения (`public/media/...`, например фото Instagram из `local_image`) **не выкладываются на Vercel** (`public/media` в `.vercelignore`). Они отдаются с CDN — по умолчанию jsDelivr-зеркало GitHub-репозитория:

```
MEDIA_CDN_URL=https://cdn.jsdelivr.net/gh/790azat/ystqez-site@main/public
```

Хелпер `media_url($path)` строит ссылку: `media/instagram/X.jpg` → `{MEDIA_CDN_URL}/media/instagram/X.jpg`; абсолютные URL возвращаются как есть; при пустом `MEDIA_CDN_URL` — локальный `asset()`.
Важно: jsDelivr отдаёт файлы только из **публичного** репозитория, и лимит на файл — 20 МБ (на весь репозиторий для `gh` — 50 МБ на пакет; при большом объёме фото лучше вынести их в отдельный репозиторий/Cloudflare R2 и поменять `MEDIA_CDN_URL`).
Загрузки файлов в админке нет (ФС Vercel только для чтения) — картинки указываются внешними ссылками. Превью YouTube берутся с `i.ytimg.com`.

## Деплой на Vercel + Neon

1. **База.** Создайте проект в [Neon](https://neon.tech) (или подключите Neon через Vercel Storage) и скопируйте строку подключения вида
   `postgresql://user:pass@ep-xxx.eu-central-1.aws.neon.tech/neondb?sslmode=require`.
2. **Проект в Vercel.** Импортируйте репозиторий. Framework Preset — *Other*; build/install-команды и `outputDirectory: public` уже заданы в `vercel.json` (сборка ассетов не нужна — `public/build` закоммичен).
3. **Переменные окружения** (Settings → Environment Variables):

   | Переменная | Значение |
   |---|---|
   | `APP_KEY` | `php artisan key:generate --show` (вида `base64:...`) |
   | `APP_URL` | `https://ваш-домен` (желательно) |
   | `DATABASE_URL` | строка подключения Neon (`POSTGRES_URL` тоже подхватывается) |
   | `SETUP_TOKEN` | длинная случайная строка, например `openssl rand -hex 24` |
   | `ADMIN_EMAIL` | email администратора |
   | `ADMIN_PASSWORD` | пароль администратора |
   | `MEDIA_CDN_URL` | (необязательно) базовый URL CDN для `public/media` |

   Остальное (`APP_ENV=production`, `DB_CONNECTION=pgsql`, `SESSION_DRIVER=cookie`, `CACHE_STORE=database`, `LOG_CHANNEL=stderr`, пути кэша в `/tmp` и т.д.) уже прописано в `vercel.json`.
4. **Deploy.**
5. **Установка БД:** откройте `https://ваш-домен/_setup/<SETUP_TOKEN>` — выполнятся `migrate --force` и сидер (админ, разделы форума, настройки, импорт JSON). Маршрут идемпотентный, его можно открывать повторно после обновлений. Без `SETUP_TOKEN` он отдаёт 404; после установки токен можно удалить из env.
6. Войдите на `/login` под админом → `/admin`.

### Как устроен деплой

- `api/index.php` подключает `public/index.php`; все динамические запросы маршрутизируются в него (`vercel.json → routes`).
- Статика: `/build/*` (Vite), `/vendor/livewire/*` (опубликованные ассеты Livewire), `favicon`, `robots.txt` — отдаются Vercel напрямую из `public/`.
- Рантайм `vercel-php@0.7.4` = PHP 8.3 (совпадает с `composer.lock`). Последняя версия — `vercel-php@0.9.0` (PHP 8.4/8.5); для перехода обновите `vercel.json` и проверьте зависимости под новую версию PHP.
- ФС только для чтения: скомпилированные шаблоны и кэши Laravel пишутся в `/tmp` (`bootstrap/app.php` создаёт каталоги и переносит `storage` в `/tmp/storage` при `VERCEL=1`).
- Websockets недоступны — чат обновляется опросом (`wire:poll` 3–4 с).
- **После обновления Livewire** (`composer update`) ассеты публикуются автоматически (`post-update-cmd`); закоммитьте `public/vendor/livewire`.
- **После изменений фронтенда** выполните `npm run build` и закоммитьте `public/build`.

## Синхронизация с YouTube

Кнопка в `/admin/videos` загружает RSS `https://www.youtube.com/feeds/videos.xml?channel_id=UC9aPP_5pJfGz1OjXppiOCcw` (последние ~15 видео) и обновляет/добавляет записи (название, дата, описание, просмотры). Добавление по ссылке подтягивает название и обложку через oEmbed. Обе операции не падают, если YouTube недоступен, — показывается сообщение.

Канал: YouTube [@YstQez](https://www.youtube.com/@YstQez) · Instagram [@yst.qez](https://www.instagram.com/yst.qez/)
