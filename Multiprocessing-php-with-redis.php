<?php

$data = array();
for ($j = 1; $j <= 20; $j++) {
    for ($h = 0; $h < 20; $h++) {
        $data[$j][$h] = $h;
    }
}

define("HOST_REDIS", '127.0.0.1');
$array_url = [
    'http://www.sanook.com',
    'http://www.kapook.com',
    'http://www.mthai.com',
    'http://pantip.com',
    'http://www.google.co.th',
];

connectRedis();
excute('', $array_url);

dd(getRedis('data'));
function dd($data)
{
    echo "\r\n";
    var_dump($data);
    echo "\r\n";
}

function excute($url = '', $data)
{
    connectRedis();
    $response = array();
    $loop = count($data);
    for ($i = 0; $i < $loop; $i++) {
        # code...
        $pid = pcntl_fork();
        if (!$pid) {
            // sleep(1);
            print "In child $i\n";
            if (array_key_exists($i, $data)) {
                $x = 7;
                $k = get_url($data[$i]);
                setRedis("data", $i);
            }
            exit($i);
        }
    }

    #process
    while (pcntl_waitpid(0, $status) != -1) {
        $status = pcntl_wexitstatus($status);
        echo "Child $status completed\n";
    }
    // dd($response);
    return $response;
}

function connectRedis()
{
    $redis = new Redis();
    $con = $redis->connect(HOST_REDIS, 6379);
    // dd($con);exit('ok');
}

function setRedis($key, $value)
{
    $redis = new Redis();
    $redis->connect(HOST_REDIS, 6379);

    $redis->lpush($key, $value);
}
function getRedis($key)
{
    $redis = new Redis();
    $redis->connect(HOST_REDIS, 6379);
    return $redis->lrange($key, 0, 4);
}
function curl_content($url = "", $data = array())
{
    $ch = curl_init();
    /*$skipper = "luxury assault recreational vehicle";
    $fields = array( 'penguins'=>$skipper, 'bestpony'=>'rainbowdash');
    $postvars = '';
    foreach($fields as $key=>$value) {
    $postvars .= $key . "=" . $value . "&";
    }
    $url = "http://www.google.com";*/
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 0); //0 for a get request
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    // curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    // curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookieFile);
    return curl_exec($ch);
    print "curl response is:" . $response;
    curl_close($ch);
}

function get_url($url = "")
{
    // sleep(1);
    // return true;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($curl, CURLOPT_TIMEOUT, 0);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt( $curl, CURLOPT_BINARYTRANSFER, true);
    return curl_exec($curl);
    // curl_close( $curl );
}
