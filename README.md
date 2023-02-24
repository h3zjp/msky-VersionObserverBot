# Misskey VersionObserverBot
Misskey で動いている bot です。  
A bot running on Misskey.  

各 Misskey 鯖を監視し、 #Misskey_Upgrade_Battle のハッシュタグを用いて、更新されたことをお知らせします。  
(監視するインスタンスは手動で更新しています)  
このアカウント宛へ返信しても、一切応答を返しません。  

We monitor each Misskey server and use the #Misskey_Upgrade_Battle hashtag to inform you that it has been updated.  
(The instance to monitor is being updated manually)  
Replies to this account will not receive any response.  

## Installation
PHP で動いています。  
config.php を書き換えて、お好きな場所へアップロードすれば、とりあえず動くかと思います。  
あとは、cron で一定間隔ごとに動かすように設定すればOKです。

## Lisence
MIT Lisence