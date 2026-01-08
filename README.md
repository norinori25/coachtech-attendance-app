# coachtech勤怠管理アプリ

## 環境構築

### Dockerビルド

1. git clone <git@github.com>:norinori25/coachtech-attendance-app.git
2. Docker Desktop を起動
3. docker-compose up -d --build

---

## Laravel環境構築

1. コンテナに入る

```
docker compose exec php bash
```

2. 依存関係インストール

```
composer install

```

3. 環境ファイル作成

```
cp .env.example .env

```

4. .env（例）

```
APP_NAME=AttendanceApp
APP_ENV=local
APP_KEY=（php artisan key:generate で自動生成）
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS="<noreply@example.com>"
MAIL_FROM_NAME="Attendance App"

```

5. アプリケーションキー作成

```
php artisan key:generate

```

6. マイグレーション＆シーディング

```
php artisan migrate:fresh --seed

```

---

## テスト環境構築

プロジェクト直下に .env.testing を作成し、以下を記述

```
APP_ENV=testing
APP_KEY=（.env の APP_KEY をコピー）

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
## テスト実行

```
docker compose exec php php artisan test

```

---

## 使用技術(実行環境)

・言語 PHP 8.2
・フレームワーク Laravel 10
・データベース MySQL 8.0.26
・Webサーバー Nginx 1.21.1
・パッケージ管理 Composer
・メール確認ツール MailHog
・コンテナ管理 Docker / docker-compose

---

## ER図

![ER図](./docs/er.svg)


## URL

・開発環境： http://localhost/
・phpMyAdmin: http://localhost:8080/
・MailHog: http://localhost:8025/


---

## テスト用アカウント

### 管理者ユーザー

メールアドレス: <admin@example.com>
パスワード: admin123
権限: 管理者（is_admin = true）

### 一般ユーザー（自動生成）

メールアドレス: ランダム生成
パスワード: password（UserFactory のデフォルト）
権限: 一般ユーザー

※ 一般ユーザーのメールアドレスは DB から確認できます。
