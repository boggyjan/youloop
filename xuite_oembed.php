<?php
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, 'http://api.xuite.net/oembed/?url=http://vlog.xuite.net/play/' . $_GET[v]);
  curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1; rv:10.0.2) Gecko/20100101 Firefox/10.0.2');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  
  echo curl_exec($ch);
  curl_close($ch);
?>