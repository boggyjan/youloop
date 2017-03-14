<?php include 'dictionary.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">

  <title>YouLoop</title>
  <meta name="description" content="<?= __('貼上YouTube, Dailymotion或Xuite影片網址，就為您重播到死！') ?>">
  <meta name="keywords" content="youtube,dailymotion,video,repeat,loop,影片,重複">
  <meta name="author" content="Yu-Chun Chang">

  <link rel="shortcut icon" href="assets/images/favicon.ico">
  <link rel="apple-touch-icon" href="assets/images/apple-touch-icon.png">
  <meta name="apple-mobile-web-app-title" content="YouLoop">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="format-detection" content="telephone=no">

  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="YouLoop">
  <meta name="twitter:description" content="<?= __('貼上YouTube, Dailymotion或Xuite影片網址，就為您重播到死！') ?>">
  <meta name="twitter:image" content="http://app.boggy.tw/youloop/assets/images/sns_share_img.jpg">
  
  <meta property="og:type" content="website">
  <meta property="og:title" content="YouLoop">
  <meta property="og:description" content="<?= __('貼上YouTube, Dailymotion或Xuite影片網址，就為您重播到死！') ?>">
  <meta property="og:url" content="http://app.boggy.tw/youloop">
  <meta property="og:site_name" content="YouLoop">
  <meta property="og:image" content="http://app.boggy.tw/youloop/assets/images/sns_share_img.jpg">

  <link rel="stylesheet" href="http://static.boggy.tw/vendor/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="http://static.boggy.tw/vendor/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/css/app.css?v=<?= filemtime('assets/css/app.css'); ?>">

</head>
<body>
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/<?= $fbsdkLang ?>/sdk.js#xfbml=1&version=v2.8&appId=261381780593436";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>

  <div class="container youloop">
    <div class="page-header">
      <a href="mailto:boggyjan+youloopreport@gmail.com" class="pull-right btn btn-danger"><i class="fa fa-info-circle"></i> <?= __('問題回報') ?></a>
      <h1>
        <span class="logo">You<span class="red">Loop</span></span>
        <small><?= __('為您重播到死') ?></small>
      </h1>
    </div>

    <div class="row inputs" :class="{playing: playing}">
      <div class="col-sm-10 col-xs-7">
        <input v-model="videoURL" @keyup.enter="submitURL" class="form-control" placeholder="<?= __('請貼上YouTube, Dailymotion或Xuite影片網址') ?>" required>
      </div>
      <div class="col-sm-2 col-xs-5">
        <a class="btn btn-default play-btn" @click="submitURL"><i class="fa fa-play-circle"></i> <?= __('無限重播') ?></a>
      </div>
    </div>
    
    <div class="players">
      <div v-show="isPlayingYoutube">
        <div id="youtube-player"></div>
      </div>

      <div v-show="isPlayingDailymotion">
        <div id="dailymotion-player"></div>
      </div>

      <div v-show="isPlayingXuite">
        <div id="xuite-player"></div>
      </div>

      <div class="hidden-sm hidden-xs qrcode-container" v-show="playing">
        <?= __('用手機狂播') ?>
        <div id="qrcode" class="qrcode"></div>
      </div>
    </div>

    <hr>
    <div class="panel panel-default history">
      <div class="panel-heading">
        <h3 class="panel-title">
          <?= __('播放歷史') ?>
          <span class="badge">{{history.length}}</span>

          <div class="btn-group history-actions" role="group" aria-label="...">
            <a class="btn btn-sm btn-default hidden-xs clear-btn" @click="exportHistory"><?= __('匯出') ?></a>
            <a class="btn btn-sm btn-default hidden-xs clear-btn" @click="importHistory"><?= __('匯入') ?></a>
            <a class="btn btn-sm btn-default clear-btn" @click="clearHistory"><?= __('清除歷史') ?></a>
          </div>

          <div class="import-file-container">
            <input type="file" class="import-file" @change="importHistoryDataSelected">
          </div>
        </h3>
      </div>
      <div class="panel-body">
        <ul class="list-group history-list">
          <li class="list-group-item item" v-for="video in history">
            <img :src="video.thumb" class="video-thumb">
            <a @click="play(video)">
              {{video.title}}
              <span class="video-type">{{video.type}}</span>
            </a>
            <a @click="removeHistoryItem(video)" class="remove-item-btn"><i class="fa fa-times-circle"></i></a>
          </li>
        </ul>
      </div>
    </div>
    
    <div class="hidden-xs ad">
      <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
      <!-- YouLoop -->
      <ins class="adsbygoogle"
           style="display:block"
           data-ad-client="ca-pub-8209950884395919"
           data-ad-slot="2219692017"
           data-ad-format="auto"></ins>
      <script>
      (adsbygoogle = window.adsbygoogle || []).push({});
      </script>
    </div>

    <div class="panel panel-default comments">
      <div class="panel-body">
        <div class="fb-comments" data-href="http://app.boggy.tw/youloop/" data-width="100%" data-numposts="5"></div>
      </div>
    </div>

    <div class="footer">
      Copyright © <?= date("Y"); ?> Boggy Jang. All rights reserved.
    </div>
  </div>
  

  <!--<script src="https://www.youtube.com/iframe_api"></script>-->
  <script src="https://s.ytimg.com/yts/jsbin/www-widgetapi-vflWbfRpo/www-widgetapi.js"></script>
  <script src="https://api.dmcdn.net/all.js"></script>
  <script src="http://static.boggy.tw/vendor/jquery/jquery-3.1.1.min.js"></script>
  <script src="http://static.boggy.tw/vendor/vue.js/2.1.10/vue.min.js"></script>
  <script src="http://static.boggy.tw/vendor/qrcodesvg/raphael-2.1.0-min.js"></script>
  <script src="http://static.boggy.tw/vendor/qrcodesvg/qrcodesvg.js"></script>
  <script src="http://static.boggy.tw/vendor/FileSaver/1.3.2/FileSaver.min.js"></script>
  <script src="assets/js/app.php?v=<?= filemtime('assets/js/app.php'); ?>"></script>
  <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-91744129-1', 'auto');
    ga('send', 'pageview');
  </script>
</body>
</html>
