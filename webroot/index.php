<?php
// Copyright (c) 2011 Cristian Adamo. All rights reserved.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.

/**
 * Doppler client-side controller
 */

$target_domain = @include dirname(__file__).'/config.php';

$doppler_corpus = null;
if ($_POST) {
  $request = $_POST['__image__'] ? '?bkg=1' : '?';
  $request_url = $target_domain.'/'.$request;
  $doppler_corpus = build_doppler_corpus($request_url, $target_domain);
}

$css_style = build_css();

$html_content = <<<EOHTML
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>Doppler Client-side</title>
    <style>
      {$css_style}
    </style>
  </head>
  <body>
    <h1><strong>Doppler</strong> (client-side console)</h1>
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
      end = new Date();
      s.innerHTML = '--- Doppler Completed! ---';
      s.style.background = '#68A64D';
      doppler_response = JSON.parse(doppler.responseText);
      duration = end-start;

      e.innerHTML =
        "<h3>Information:</h3> <br>" +
        "<strong>Target site:</strong>    " + "{$target_domain}" + "<br><br>" +
        "<strong>TOTAL REQUEST:</strong>  " +
          (duration+(doppler_response.epoch/1000)) + " ms<br>" +
        "<strong>Request Duration:</strong>   " +
          duration + " ms " + "<br>" +
        "<strong>Server Processing Time:</strong>   " +
          doppler_response.epoch + " us<br><br>" +
        "<strong>DOWNLOAD TIMESTAMP:</strong><br>" +
          "Started at:         " + start + "<br>" +
          "Completed at:    " + end;

      // basic JSON transform
      c.innerHTML = '<strong>RESPONSE TO:</strong>  <i>' +
                    doppler_response.title + '</i><br><br>' +
                    doppler_response.content;
    }
  };

  // PUT STUFF BEFORE THIS -----------------------------------------------------
  start = new Date();
  doppler.send();
</script>
EODOPPLER;
}


function build_css() {
  return <<<EOCSS
body {
  margin: 0;
  padding: 0;
  font-size: 1.1em;
  font-family: "lucida grande",tahoma,verdana,arial,sans-serif;
}

h1 {

  margin: 0;
  padding: 1em 20%;
  font-family: "Verdana";
  font-weight: normal;
  color: white;
  background: #D09;
}

h1 strong {
  font-weight: bolder;
}

h3 {
  margin: 0;
  padding: 0;
  padding-bottom: 5px;
  border-bottom: 1px solid white;
}

h4 {
  margin: 0;
  margin-bottom: 0.5em;
  padding-bottom: 5px;
  font-size: 2em;
  font-weight: normal;
  color: #777;
  border-bottom: 1px solid #888;
}

.ws {
  white-space: pre;
}

form.doppler-image-test {
  float: right;
  display: block;
}

#doppler-runner {
  padding: 0.5em 20%;
  background: #C08;
  margin-bottom: 1em;
}

#doppler-core {
  display: block;
  clear: both;
  text-align:left;
  font-size: 13px;
  line-height: 1.6em;
  padding: 0 20% 2em;
}

#doppler-status {
  padding: 10px;
  font-weight: bold;
  font-size: 16px;
  color: white;
  display:block;
  border-top: 2px solid #888;
  background: #AAA;
}

#doppler-epoch {
  padding:10px;
  display:block;
  border-top: 1px solid white;
  background: #A6DDFF;
}

#doppler-content {
  padding:10px;
  word-wrap: break-word;
  border-top: 1px solid #888
}

#doppler-footer {
  padding: 1em 20%;
  font-size: 0.7em;
  color: #777;
}

#doppler-footer-content {
  padding-top: 1em;
  border-top: 1px solid #AAA;
}

input.inputsubmit {
  font-size: 13px;
  font-weight: bold;
  text-align: center;
  color: #666;
  cursor: pointer;
  text-decoration: none;
  vertical-align: middle;
  white-space: nowrap;
  line-height: 18px;
  padding: 2px 8px 2px 8px;
  background-color: #BBB;
  border: 1px solid #888;
  border-bottom-color: rgba(0,0,0,0.2);
  box-shadow: 0px 1px 0px rgba(0,0,0,0.2);
  -moz-box-shadow: 0px 1px 0px rgba(0,0,0,0.2);
  -webkit-box-shadow: 0px 1px 0px rgba(0,0,0,0.2);
}

input.inputsubmit:hover {
  background: #CCC;
}
EOCSS;
}
