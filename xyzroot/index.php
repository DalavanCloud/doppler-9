<?php
// Copyright (c) 2011 Cristian Adamo.
// Use of this source code is governed by a Apache License (v2.0) that can be
// found in the LICENSE file.

$start = microtime(true);

/**
 * Doppler request controller
 */
$path = $_REQUEST['__path__'];
$run_test = $_GET['test'];
$statistics = $_GET['stats'];
$origin = $_GET['origin'];

$file = 'dns.gif';
$files = '(dns|http).gif';
$tokens = array("\r\n", "\n", "\r");

$lorem_ipsum = <<<EOLOREM
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque accumsan
semper magna eget euismod. Nullam adipiscing sem sed augue pulvinar varius.
Quisque ornare porta orci vitae lacinia. Integer rhoncus consectetur odio id
tincidunt. Integer gravida sodales sapien in luctus. Nunc vitae elementum nisi.
Aliquam et lacus a erat imperdiet malesuada. Donec at quam odio. Mauris lobortis
velit nunc, pretium scelerisque augue. Mauris condimentum, augue lacinia cursus
vulputate, ipsum diam fringilla elit, sed varius urna neque vitae quam. Aenean
ultricies nibh pulvinar quam laoreet egestas. Nulla lorem nibh, posuere et
consectetur nec, volutpat et purus. Aliquam commodo suscipit quam non imperdiet.
Integer nec purus mauris, non sagittis enim. Maecenas volutpat hendrerit
commodo. Sed viverra metus sed magna eleifend imperdiet. Curabitur urna nisl,
tincidunt ac congue eu, viverra ac mauris. Fusce tempor imperdiet diam eget
tincidunt. Donec sapien risus, cursus eu facilisis faucibus, lobortis quis quam.
Nulla posuere.
EOLOREM;

if ($statistics) {
  @file_put_contents('doppler.log', $statistics, FILE_APPEND);
}

$response = array('DOPPLER-NO-REQUEST-TEST', 'doppler-response-here');
if (preg_match('#(.*)'.$files.'($|(.*)$)#', $path) || ($run_test == 'image')) {
  $finfo = new finfo(FILEINFO_MIME);
  $mime = $finfo->file($file);
  if ($path) {
    header("Content-Type: {$mime};");
  } else {
    header("Content-Type: text/plain; charset=UTF-8");
  }
  $response = array(
    'DOPPLER-IMAGE-FILE-TEST',
    base64_encode(file_get_contents($file)));
} else if  ($run_test == 'lorem') {
  header("Content-Type: text/plain; charset=UTF-8");
  $response = array(
    'DOPPLER-LOREM-IPSUM-TEST',
    str_replace($tokens, '', $lorem_ipsum));
}

header("Access-Control-Allow-Origin: {$origin}");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

list($title, $content) = $response;
$encoded_response = array(
  'title' => $title,
  'content' => $content,
  'epoch' => ((microtime(true)-$start)*1000000),
);
echo json_encode($encoded_response);
