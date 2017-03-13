<?php
  $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
  
  if ($lang == 'zh') $fbsdkLang = 'zh_TW';
  else if ($lang == 'ja') $fbsdkLang = 'ja_JP';
  
  $dictionary = [
    '為您重播到死' => [
      ja => 'あなたのために死ぬまで繰り返す',
      en => 'Repeat till die for you'
    ],
    '問題回報' => [
      ja => '問題を報告する',
      en => 'Report a Problem'
    ],
    '請貼上YouTube, Dailymotion或Xuite影片網址' => [
      ja => 'YouTube, DailymotionあるいはXuiteの動画のURLを入力してください',
      en => 'Please paste YouTube, Dailymotion or Xuite video URL'
    ],
    '貼上YouTube, Dailymotion或Xuite影片網址，就為您重播到死！' => [
      ja => 'YouTube, DailymotionあるいはXuiteの動画のURLを入力するとあなたのために死ぬまで繰り返す！',
      en => 'Paste YouTube, Dailymotion or Xuite video URL, and I will repeat till die for you!'
    ],
    '無限重播' => [
      ja => '再生する',
      en => 'Loop!'
    ],
    '播放歷史' => [
      ja => '再生履歴',
      en => 'History'
    ],
    '清除歷史' => [
      ja => '履歴を削除する',
      en => 'Clear all'
    ],
    '匯入' => [
      ja => '輸入',
      en => 'import'
    ],
    '匯出' => [
      ja => '輸出',
      en => 'export'
    ],
    '確認是否取代目前資料？' => [
      ja => '今の履歴を替わりますか？',
      en => 'Are you sure using imported data instead old history data?'
    ],
    '匯入成功！' => [
      ja => '無事終了！',
      en => 'Successfully imported!'
    ],
    '這個不是YouTube, Dailymotion或Xuite影片網址喔！' => [
      ja => 'これはYouTube, DailymotionあるいはXuiteのURLではありません。',
      en => 'This is not YouTube, Dailymotion or Xuite URL.'
    ],
    '確定清除所有歷史紀錄？' => [
      ja => '履歴を削除してもよろしいですか？',
      en => 'Are you sure?'
    ],
    '用手機狂播' => [
      ja => '携帯で再生',
      en => 'Play on phone'
    ]
  ];

  function __($str) {
    global $dictionary, $lang;

    if (isset($dictionary[$str][$lang])) return $dictionary[$str][$lang];
    else return $str;
  }
?>