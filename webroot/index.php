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
  $request = $_POST['__image__'] ? '?test=image' : '?test=lorem';
  $request .= '&origin='.$target_domain[1];
  $unique_host = str_replace('.', '', $user_ip).'-'.uniqid();
  $request_url = 'http://'.$unique_host.$target_domain[0].'/'.$request;
  $doppler_corpus = build_doppler_corpus($request_url, $target_domain);
}

$html_content = <<<EOHTML
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Doppler Client-side</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
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
function build_doppler_corpus($target_url, $target_domain) {
  return <<<EODOPPLER
<script type="text/javascript">
  /**
   * We need to minimize the footprint since this is highly critical to the
   * performance of the script.
   */

  var doppler;
  try {
    try {
       doppler = new XMLHttpRequest();
    } catch (x) {
      doppler = new ActiveXObject("Msxml2.XMLHTTP");
    }
  } catch (x) {
    doppler = new ActiveXObject("Microsoft.XMLHTTP");
  }

  var start = 0, end = 0, duration = 0,
    c = document.getElementById('doppler-content');
    s = document.getElementById('doppler-status');
    e = document.getElementById('doppler-epoch');

  doppler.open("GET", "{$target_url}");
  doppler.onreadystatechange = function() {
    if ((doppler.readyState == 4) && (doppler.status == 200)) {
      end = new Date().getTime();
      s.innerHTML = '--- Doppler Completed! ---';
      s.style.background = '#68A64D';
      doppler_response = JSON.parse(doppler.responseText);
      duration = end-start;

      e.innerHTML =
        "<h3>Information:</h3> <br>" +
        "<strong>Target site:</strong>    " + "{$target_domain}" + "<br><br>" +
        "<strong>NETWORK LATENCY:</strong>  " +
          (duration-(doppler_response.epoch/1000)) + " ms<br>" +
        "<strong>Request Duration:</strong>   " +
          duration + " ms " + "<br>" +
        "<strong>Server Processing Time:</strong>   " +
          doppler_response.epoch + " us<br><br>" +
        "<strong>DOWNLOAD TIMESTAMP:</strong><br>" +
          "Started at:         " + start + "<br>" +
          "Completed at:    " + end;

      c.innerHTML = '<strong>RESPONSE TO:</strong>  <i>' +
                    doppler_response.title + '</i><br><br>' +
                    doppler_response.content;
    }
  };

  start = new Date().getTime();
  doppler.send();
</script>
EODOPPLER;
}

