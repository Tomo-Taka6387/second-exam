# 勤怠管理アプリ

## アプリケーション概要

勤怠の打刻・申請・承認を行う Web アプリケーションです。

---

## 環境構築

### 1. リポジトリをクローン

git clone git@github.com:Tomo-Taka6387/second-exam.git

### 2. DockerDesktop アプリを起動

docker-compose up -d --build

### 3. コンテナ起動確認

docker ps

---

## Laravel 環境構築

### 1. PHP コンテナに入る

docker-compose exec php bash

### 2. Composer 依存関係インストール

composer install

### 3. 環境ファイルを設定

`.env.example` をコピーして `.env` に変更、または新規作成。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=example@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. アプリケーションキー生成

php artisan key:generate

### 5. マイグレーション・シーディング

php artisan migrate --seed

## 🧰 使用技術

項目 バージョン
PHP 8.1.33
 Laravel 8.83.8
 MySQL 8.0.42

## URL

- アプリ環境: [http://localhost/]
- phpMyAdmin: [http://localhost:8080/]



##  ログイン用テストアカウント

**管理者ユーザー**


name: 管理者ユーザー
email: admin@test.com
password: password


**一般ユーザー**


name: 山田 太郎
email: taro.y@coachtech.co
password: password


---

##  ER 図

![ER図](./ER図.png)



##  メール認証設定（Mailtrap）

1. [Mailtrap](https://mailtrap.io/) に会員登録
2. Mailbox の「Integrations」から **Laravel 7.x and 8.x** を選択
3. `.env` のメール関連設定をコピー＆ペースト
4. `MAIL_FROM_ADDRESS` は任意のメールアドレスを指定



## PHPUnit テスト

### 1. テスト用 DB 作成


docker-compose exec mysql bash
mysql -u root -p
# パスワード: root
create database test_database;


### 2. テスト用マイグレーション実行


docker-compose exec php bash
php artisan migrate:fresh --env=testing


### 3. テスト実行


./vendor/bin/phpunit



##  注意事項

1. **データがない場合の表示**
申請待ちor申請済みデータがない場合,”データがありません”の文言を表示する指定をしています。
   - 申請一覧（一般ユーザー）: `user/application/index.blade.php`
   - 申請一覧（管理者）: `admin/request/index.blade.php`

2. **当日の勤怠データがない場合**
当日の勤怠データがない場合,”データがありません”の文言を表示する指定をしています。

   - 勤怠一覧（管理者）: `user/attendance/index.blade.php`

3. **承認済みデータは修正不可**
承認済みデータには"承認済みのため修正できません"の文言を表示する指定をしています。

   - 申請一覧（一般ユーザー）: `user/application/index.blade.php`

4. **不正ログインの制御**

   - 一般ユーザーが管理者ページにアクセスした場合
   - 管理者が一般ユーザーページにアクセスした場合
     → 「このアカウントではログインできません」と表示
   - 対象ファイル：
     - `auth/login.blade.php`
     - `admin/auth/login.blade.php`

5. **勤怠詳細データがない場合**
   - 「この日の勤怠は登録されていません。」と表示
   - 対象ファイル：
     - `user/attendance/show.blade.php`
     - `admin/attendance/show.blade.php`

---


