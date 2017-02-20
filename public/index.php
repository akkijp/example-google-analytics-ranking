<?php
/*
 * Copyright 2017 Kosuke Akizuki.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

include_once __DIR__ . '/../vendor/autoload.php';

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/../service-account.json');
define('GOOGLE_ANALYTICS_VIEW_ID', '123456789');

/************************************************
  Make an API request authenticated with a service
  account.
 ************************************************/


function missingServiceAccountDetailsWarning() {
  $ret = "
    <h3 class='warn'>
      Warning: You need download your Service Account Credentials JSON from the
      <a href='http://developers.google.com/console'>Google API console</a>.
    </h3>
    <p>
      Once downloaded, move them into the root directory of this repository and
      rename them 'service-account-credentials.json'.
    </p>
    <p>
      In your application, you should set the GOOGLE_APPLICATION_CREDENTIALS environment variable
      as the path to this file, but in the context of this example we will do this for you.
    </p>";
  return $ret;
}


$client = new Google_Client();

if (getenv('GOOGLE_APPLICATION_CREDENTIALS')) {
  // use the application default credentials
  $client->useApplicationDefaultCredentials();
} else {
  echo missingServiceAccountDetailsWarning();
  return;
}

$client->setApplicationName("hackerslognet-analytics-ranking");
$client->setScopes([
  Google_Service_Analytics::ANALYTICS,
  Google_Service_Analytics::ANALYTICS_READONLY
]);
$service = new Google_Service_Analytics($client);

$results = $service->data_ga->get(
  'ga:'.GOOGLE_ANALYTICS_VIEW_ID,
  '2016-01-01',
  'today',
  'ga:sessions,ga:pageviews',        // ページビューを取得します。
  [
    'dimensions' =>  'ga:pagePath',  // ディメンションにページのパスを指定します。
    'sort' => '-ga:pageviews',       // ページビューの降順に並べます。
    'max-results' => 100,            // 取得結果を100件にします。
  ]
);

?>






<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Google Analytics Ranking</title>
</head>
<body>

<ul>
  <?php
    foreach ($results->getRows() as $row) {
      echo "<li>{$row[0]} : {$row[1]}</li>\n";
    }
  ?>
</ul>

</body>
</html>
