# track sharing SNS（完全なSPA）

SSRされたHTMLを使用していないためdebugbar使えなかった😭（debugbarはlaravelのサーバーのポートに接続してる時生成されるhtmlに追加されるためvue側のポートでは使えない。もし使いたいならbladeファイルにvueを組み込んでマウントしてbladeファイル一つだけSSRすれば使える。現状はdebugbarなくても困らないからそのまま）

そこまで重いアプリでもないし、ユーザーが増えることも想定していないので現状フルSPA（SEO問題も気にする必要はないだろう）

## 技術スタック

vue3, laravel11
