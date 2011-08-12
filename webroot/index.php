<?php
// Copyright (c) 2011 Cristian Adamo. All rights reserved.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.

/**
 * Doppler client-side controller
 */

$target_domain = @include dirname(__file__).'/config.php';

$user_ip = null;
if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
  $user_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
} else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
  $user_ip = $_SERVER["HTTP_CLIENT_IP"];
} else if (isset($_SERVER["REMOTE_ADDR"])) {
  $user_ip = $_SERVER["REMOTE_ADDR"];
}

$doppler_corpus = null;
if ($_POST) {
  $request = $_POST['__image__'] ? '__IMAGE__' : '__LOREM__';
  $doppler_corpus = build_doppler_corpus(
                      $request, $user_ip, $target_domain[0], $target_domain[1]);
}

$html_content = <<<EOHTML
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Doppler Client-side</title>
    <link rel="stylesheet" type="text/css" href="/rsrc/style.css" />
    <script type="text/javascript" src="/rsrc/js/init.js"></script>
  </head>
  <body>
    <a class="doppler-hidden-button" href="/">
      <h1><strong>Doppler</strong> (client-side console)</h1>
    </a>
    <div id="doppler-runner">
      <form action="" method="POST" class="doppler-image-test">
        <input type="hidden" name="__image__" value="1" />
        <input type="submit" value="RUN IMAGE TEST" class="inputsubmit"/>
      </form>
      <form action="" method="POST">
        <input type="hidden" name="__url__" value="1" />
        <input type="submit" value="RUN LOREM IPSUM TEST" class="inputsubmit"/>
      </form>
    </div>
    <div id="doppler-core">
      <span id="doppler-status" class="ws">--- Waiting ACK ---</span>
      <div id="doppler-epoch" class="ws"><i>No epoch</i></div>
      <div id="doppler-content" class="ws">Waiting response...</div>
    </div>
    <div id="doppler-footer">
      <div id=doppler-footer-content>
        Doppler &copy; 2011 &middot; Cristian Adamo
      </div>
    </div>
  </body>
  {$doppler_corpus}
</html>
EOHTML;

echo $html_content;


/**
 * Doppler Script builder
 */
function build_doppler_corpus($test, $user_ip, $target_url, $user_host) {
  return <<<EODOPPLER
<script type="text/javascript" src="/rsrc/js/dom.js"></script>
<script type="text/javascript" src="/rsrc/js/doppler.js"></script>
<script type="text/javascript">
  /**
   * We need to minimize the footprint since this is highly critical to the
   * performance of the script.
   */
(function() {
  function printResponse(doppler_stat, doppler_reponse) {
    var dns_response = JSON.parse(doppler_reponse.dns),
      http_response = JSON.parse(doppler_reponse.http),
      stats = null, response = null,
      c = CX.$('doppler-content'),
      s = CX.$('doppler-status'),
      e = CX.$('doppler-epoch');

    s.innerHTML = '--- Doppler Completed! ---';
    s.style.background = '#68A64D';

    stats =
      "<h3>Execution data collected:</h3> <br>" +

      "<strong>Target site:</strong>    " +
        doppler_stat.host + "<br><br>" +

      "<strong>DNS Lookup:</strong>  " +
        doppler_stat.dns + " ms<br>" +

      "<strong>HTTP Request:</strong>  " +
        doppler_stat.http + " ms<br><br>" +

      "<strong>Network Latency:</strong>  " +
        (doppler_stat.http-(http_response.epoch/1000)) + " ms<br>" +

      "<strong>Total Request Duration:</strong>   " +
        (doppler_stat.dns + doppler_stat.http) + " ms" + "<br>" +

      "<strong>Server Processing Time:</strong>   " +
        http_response.epoch + " us<br><br>";

    response =
      '<strong>DNS RESPONSE TO:</strong>  <i>' +
        dns_response.title + '</i><br><br>' +
        dns_response.content + '<br><br>' +

      '<strong>HTTP RESPONSE TO:</strong>  <i>' +
        http_response.title + '</i><br><br>' +
        http_response.content;

    e.innerHTML = stats;
    c.innerHTML = response;
  };

  CX.log('Starting...');
  new CX.Doppler('GET', '{$target_url}', printResponse)
          .setOptions('{$test}', '{$user_ip}', '{$user_host}')
          .execute();
})();
</script>
EODOPPLER;
}
