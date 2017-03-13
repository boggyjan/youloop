<?php include '../../dictionary.php'; ?>

(function (window, document, $, undefined) {

  "use strict";

  var STORAGE_KEY = 'youloop-1.1';
  var PLAYER_WIDTH = '640';
  var PLAYER_HEIGHT = '390';

  // 要新增一個site的時候要先確認他的縮圖api, url regexp, info(oembed) 是否有能力取得
  var siteRules = {
    youtube: {
      regexp: new RegExp(/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=|\/sandalsResorts#\w\/\w\/.*\/))([^\/&]{10,12})/)
    },
    dailymotion: {
      regexp: new RegExp(/(?:dailymotion\.com(?:\/video|\/hub)|dai\.ly)\/([0-9a-z]+)(?:[\-_0-9a-zA-Z]+#video=([a-z0-9]+))?/)
    },
    xuite: {
      regexp: new RegExp(/(?:(?:vlog\.xuite\.net\/(?:play|embed)\/)|(?:m\.xuite\.net\/vlog\/.+\/))([0-9a-zA-Z]+=?=?)(\/[\-_0-9a-zA-Z]+)?/)
      // 不支援playsinline
    }
  };
  
  var historyStorage = {
    fetch: function () {
      var history = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
      return history;
    },
    save: function (history) {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(history))
    }
    /*
      history object mock-up
      [{
        title: '',
        video_id: '',
        type: ''.
        thumb: ''
      },...]
    */
  };

  function containsObject(list, obj) {
    for (var i = 0; i < list.length; i++) {
      if (list[i].video_id === obj.video_id) return true;
    }
    return false;
  }

  var app = new Vue({
    data: {
      videoURL: '', // url
      players: {}, // players
      playing: false,
      playingType: null,
      readyToGetInfo: true,
      history: historyStorage.fetch()
    },

    methods: {
      submitURL: function() {

        if (this.videoURL) {

          for (var i in siteRules) {
            var regexp = siteRules[i].regexp;

            if (this.videoURL.match(regexp)) {
              var videoID = this.videoURL.match(regexp)[1];
              
              if (videoID) {
                this.play({video_id: videoID, type: i});
                return;
              }
            }
          }
          alert('<?= __('這個不是YouTube, Dailymotion或Xuite影片網址喔！') ?>');
        }
        else {
          alert('<?= __('這個不是YouTube, Dailymotion或Xuite影片網址喔！') ?>');
        }
      },

      play: function(video) {
        this.playingType = video.type;
        this.pauseAllPlayer(video.type);

        switch (video.type) {
          case 'xuite':
            this.createPlayer(video); // xuite沒有api 只有把iframe放進去或拿掉兩個選項
            break;

          case 'dailymotion':
            
            if (!this.players.dailymotion) {
              this.createPlayer(video);
            }
            else {
              this.players.dailymotion.load(video.video_id);
              this.getDailymotionVideoInfo(video); // video_start event 撈不到id
            }
            break;

          case 'youtube':
            
            if (!this.players.youtube) {
              this.createPlayer(video);
            }
            else {
              this.players.youtube.loadVideoById(video.video_id)
            }
            break;
        }

        // change url hash
        window.location.hash = video.type + '_' + video.video_id;
        
        // qrcode
        $('#qrcode').empty();
        var qrcodesvg = new Qrcodesvg(window.location.toString(), 'qrcode', 80);
        qrcodesvg.draw();
      },

      pauseAllPlayer: function(type) {

        if (this.players.xuite) {
          this.players.xuite.innerHTML = '';
        }

        if (this.players.dailymotion) {
          this.players.dailymotion.pause();
          // 已知問題：dailymotion player在loading時不會管pause()所以趁他loading切到youtube就會無法停下來
        }

        if (this.players.youtube) {
          this.players.youtube.stopVideo();
        }
      },

      createPlayer: function(video) {
        var _app = this;

        switch (video.type) {
          case 'xuite':
            this.getXuiteVideoInfo(video);
            _app.players.xuite = document.getElementById('xuite-player');
            var iframe = document.createElement('iframe');
            iframe.src = `http://vlog.xuite.net/embed/${video.video_id}?v=2.0&ar=1&as=1`
            _app.players.xuite.append(iframe);

            break;

          case 'dailymotion':
            this.getDailymotionVideoInfo(video);

            _app.players.dailymotion = DM.player(document.getElementById('dailymotion-player'), {
                width: PLAYER_WIDTH,
                height: PLAYER_HEIGHT,
                video: video.video_id,
                params: {
                  autoplay: true
                }
            });

            _app.players.dailymotion.addEventListener('playing', function(data){
              _app.playing = true;
            });

            _app.players.dailymotion.addEventListener('video_end', function(){
              _app.players.dailymotion.play();
            });

            _app.players.dailymotion.addEventListener('pause', function(){
              _app.playing = false;
            });
            break;

          case 'youtube':
            _app.players.youtube = new YT.Player('youtube-player', {
              width: PLAYER_WIDTH,
              height: PLAYER_HEIGHT,
              videoId: video.video_id,
              playerVars: {
                playsinline: 1,
                autoplay: 1
              },
              events: {
                onReady: function(event) {
                  event.target.playVideo();
                },
                onStateChange: function(event) {
                  switch (event.data) {
                    case -1:
                      // 每換一次影片時，才會觸發一次，但第一次進來時不會觸發，所以上面data那邊宣告readyToGetInfo時才會預設為true
                      _app.readyToGetInfo = true;
                      break;

                    case YT.PlayerState.PLAYING:
                      _app.playing = true;

                      if (_app.readyToGetInfo) {

                        // youtube 取資料
                        _app.readyToGetInfo = false;

                        var videoData = event.target.getVideoData();
                        var videoObj = {
                          title: videoData.title,
                          type: 'youtube',
                          video_id: videoData.video_id,
                          thumb: `https://i.ytimg.com/vi/${videoData.video_id}/hqdefault.jpg`
                        }
                        
                        if (!containsObject(_app.history, videoObj)) {
                          _app.saveHistory(videoObj);
                        }
                        ga('send', 'event', '播放影片', videoObj.title);
                      }
                      break;

                    case YT.PlayerState.PAUSED:
                      _app.playing = false;
                      break;

                    case YT.PlayerState.ENDED:
                      _app.players.youtube.playVideo();
                      break;

                    default:
                  } 
                }
              }
            });
            break;
        }
      },

      getDailymotionVideoInfo: function(video) {
        var _app = this;
        var oembedURL = 'http://www.dailymotion.com/services/oembed?url=http://www.dailymotion.com/video/' + video.video_id;
        
        // dailymotion 取資料
        $.ajax({
          url: oembedURL,
          dataType: 'jsonp'
        }).done(function(videoData) {
          var videoObj = {
            title: videoData.title,
            type: 'dailymotion',
            video_id: video.video_id,
            thumb: 'http://www.dailymotion.com/thumbnail/video/' + video.video_id
          }
          if (!containsObject(_app.history, videoObj)) {
            _app.saveHistory(videoObj);
          }
          ga('send', 'event', '播放影片', videoObj.title);
        });
      },

      getXuiteVideoInfo: function(video) {
        var _app = this;  
        var oembedURL = 'xuite_oembed.php?v=' + video.video_id;
        
        // xuite 取資料
        $.getJSON(oembedURL).done(function(data) {
          var title = data.title.replace(' - 隨意窩 Xuite影音', '');
          var videoObj = {
            title: title,
            type: 'xuite',
            video_id: video.video_id,
            thumb: data.thumbnail_url
          }

          if (!containsObject(_app.history, videoObj)) {
            _app.saveHistory(videoObj);
          }
          ga('send', 'event', '播放影片', videoObj.title);
        });
      },

      saveHistory: function(video) {
        this.history.push(video);
        historyStorage.save(this.history);
      },

      removeHistoryItem: function(item) {
        this.history = this.history.filter(function(el) {
          return el.video_id !== item.video_id;
        });
        historyStorage.save(this.history);
        ga('send', 'event', '清除單筆歷史紀錄', item.title);
      },

      clearHistory: function() {
        if (confirm('<?= __('確定清除所有歷史紀錄？') ?>')) {
          this.history = [];
          historyStorage.save(this.history);
          ga('send', 'event', '清除歷史紀錄');
        }
      }
    },

    computed: {
      isPlayingYoutube: function() {
        return this.playingType == 'youtube';
      },
      isPlayingDailymotion: function() {
        return this.playingType == 'dailymotion';
      },
      isPlayingXuite: function() {
        return this.playingType == 'xuite';
      }
    },

    mounted: function() {
      
      if (window.location.hash) {
        var url = window.location.hash.substring(1).split('_');
        
        if (url.length >= 2) {
          var video = {
            type: url[0],
            video_id: url[1]
          }
          this.play(video);
        }
      }
    }
  });
  app.$mount('.youloop');

} (window, document, jQuery));