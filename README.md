# sample-laravel-vue3-inertia

## 初回起動

### ■ envファイルの設置
`$ cp webapp/.env.docker webapp/.env`

### ■ dockerの初回起動
`$ docker compose up -d --build`

### ■ WEBコンテナへのアクセス
`$ docker compose exec web bash`

### ■ アプリケーションの初期設定
```
$ ./refresh.sh
$ php artisan key:generate
$ npm install
$ npm run dev
```

### ブラウザでのアクセス
http://localhost/

## その他
### ■ docker操作
#### ■ dockerの通常起動
`$ docker compose up -d`

#### ■ dockerの起動停止
`$ docker compose down`


#### ■ WEBコンテナへのアクセス
`$ docker compose exec web bash`

#### ■ DBへの接続方
`$ docker compose exec db mysql -udocker -pdocker sample_db`

### VueJSの実行

#### ■ debug
`$ npm run dev`

#### ■ JSファイルの出力
webapp/public/build へJSファイルが出力される  
`$ npm run build`
