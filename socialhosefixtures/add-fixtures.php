<?php

$file = file_get_contents('test-twitter.json');
$array = json_decode($file, true);

exec('mkdir jsons');

foreach ($array as $k => $item) {
    echo $k . PHP_EOL;
    $result = $item['_source'];
    file_put_contents('jsons/'.$k.'.json', json_encode($result));
    exec('curl -XPOST http://socialhose-elastic:9200/external/document -H "Content-Type: application/json" -d @jsons/'. $k.'.json');
}

exec('rm -r jsons');

$file = file_get_contents('test-instagram.json');
$array = json_decode($file, true);

exec('mkdir jsons');

foreach ($array as $k => $item) {
    echo $k . PHP_EOL;
    $result = $item['_source'];
    file_put_contents('jsons/'.$k.'.json', json_encode($result));
    exec('curl -XPOST http://socialhose-elastic:9200/external/document -H "Content-Type: application/json" -d @jsons/'. $k.'.json');
}

exec('rm -r jsons');