# 勤怠管理アプリ

## 概要
本アプリケーションは、一般ユーザーと管理者の2種類の権限を持つ勤怠管理システムです。  
ユーザーは勤怠登録（出勤・退勤・休憩）や申請を行い、管理者は勤怠データや申請の承認・修正を行うことができます。  
Laravel Fortifyを用いた認証機能を実装し、Docker環境上で動作します。

---

## 使用技術

| 分類 | 内容 |
|------|------|
| 言語 | PHP 8.x |
| フレームワーク | Laravel 10.x |
| データベース | MySQL 8.x |
| 開発環境 | Docker / Docker Compose |
| 認証 | Laravel Fortify |
| メールテスト | MailHog |
| フロントエンド | HTML / CSS / Blade Template |

---

## 環境構築手順

### 1. リポジトリのクローン
`git clone https://github.com/NanaeYuge/attendance-laravel.git
cd attendance-laravel`

### 2. 環境変数ファイルの作成
`
cp .env.example .env`
.env 内の設定を確認・編集してください。
特に以下を確認します。
`ini
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=attendance
DB_USERNAME=root
DB_PASSWORD=password`

### 3. コンテナの起動
コードをコピーする
`docker compose up -d`

### 4. 依存関係のインストール

`docker compose exec php composer install`

### 5. アプリケーションキーの生成
`docker compose exec php php artisan key:generate`

### 6. マイグレーションとシーディングの実行
`docker compose exec php php artisan migrate --seed`
### 7. ブラウザでアクセス
一般ユーザー用: http://localhost/login

管理者用: http://localhost/admin/login


##ログイン情報
| ロール |	メールアドレス	| パスワード |
| 管理者	|admin@example.com	| password |
| 一般ユーザー1 |taro@example.com	| password |
|一般ユーザー2	| hanako@example.com |	password |
|一般ユーザー3	| jiro@example.com |	password |

---

一般ユーザーは初回ログイン後、メール認証が必要です。
MailHogを使用して確認できます。
URL: http://localhost:8025

##ディレクトリ構成（主要）
```bash
app/
 ├── Http/
 │    ├── Controllers/
 │    └── Requests/
 ├── Models/
database/
 ├── factories/
 ├── seeders/
 └── migrations/
resources/
 ├── views/
 │    ├── admin/
 │    └── staff/
 ├── css/
routes/
 └── web.php
```
###主な機能
####一般ユーザー側
勤怠登録（出勤・退勤・休憩開始・休憩終了）

勤怠一覧・詳細表示

申請作成・一覧表示

メール認証機能（Fortify使用）

####管理者側
スタッフ一覧表示

勤怠一覧表示

修正申請承認／却下機能

CSVエクスポート（検索結果対応）

詳細モーダル表示・削除機能

##画面一覧
画面名	対応ビュー	備考
会員登録画面	resources/views/auth/register.blade.php	一般ユーザー新規登録
ログイン画面（一般）	resources/views/auth/login.blade.php	Fortify使用
ログイン画面（管理者）	resources/views/admin/login.blade.php	管理者専用ルート
メール認証誘導画面	resources/views/auth/verify-email.blade.php	MailHogで確認
勤怠登録画面	resources/views/staff/attendance-register.blade.php	出勤・退勤・休憩操作
勤怠一覧画面	resources/views/staff/attendance-list.blade.php	ページネーション対応
勤怠詳細画面	resources/views/staff/attendance-detail.blade.php	詳細・修正申請
申請一覧画面	resources/views/staff/requests-list.blade.php	承認待ち・承認済みタブ切替
管理者勤怠一覧	resources/views/admin/attendance-list.blade.php	検索・CSV出力対応
修正申請承認画面	resources/views/admin/requests.blade.php	承認・却下ボタンあり
スタッフ別勤怠一覧	resources/views/admin/staff-attendance.blade.php	モーダル詳細表示対応

##備考
429 Too Many Requests が発生した場合
Laravel Fortify では、ログイン試行の制限がかかると「429 Too Many Requests」エラーが表示されます。
これは短時間に複数回ログインを試行したことによるレートリミットです。

###対処方法
数分待ってから再度ログインしてください。（通常は1分程度で解除されます）

即座に解除したい場合は、以下のコマンドを実行してキャッシュをクリアします。

```bash
docker compose exec php php artisan cache:clear
docker compose exec php php artisan config:clear
docker compose exec php php artisan route:clear
```

##ライセンス
このプロジェクトは学習およびポートフォリオ目的で作成されました。
著作権は開発者に帰属します。

作者
Nanae Yuge
Laravel / Docker / MySQL を用いたWebアプリケーション開発


