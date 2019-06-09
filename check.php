<?php

require "vendor/autoload.php";

$jar = new \GuzzleHttp\Cookie\CookieJar;
$client = new GuzzleHttp\Client([
    "base_uri" => "https://e.csdd.lv/",
    "cookies" => $jar
]);

$creds = [
    "email" => "EXAMPLE_EMAIL", // INSERT YOUR EMAIL
    "password" => "EXAMPLE_PASS" // INSERT YOUR PASS
];

$addr = [
    "login" => "?action=doLogin",
    "exam" => "examp/"
];

$requests = [

    [
        "addr" => $addr["login"],
        "params" => [
            "email" => $creds["email"],
            "psw" => $creds["password"]
        ]
    ],

    [
        "addr" => $addr["exam"],
        "params" => [
            "did" => "0"
        ]
    ],

    [
        "addr" => $addr["exam"],
        "params" => [
            "nodala" => "1",
            "epacc" => "",
            "did" => "0.1",
            "nodala_txt" => "CSDD Rīgas KAC",
            "iemesls_txt" => ""
        ]
    ],

    [
        "addr" => $addr["exam"],
        "params" => [
            "iemesls" => "6",
            "epacc" => "",
            "did" => "1",
            "nodala_txt" => "CSDD Rīgas KAC",
            "nodala" => "1",
            "iemesls_txt" => "Kvalifikācijas iegūšana"
        ]
    ],

    [
        "addr" => $addr["exam"],
        "params" => [
            "veids" => "5",
            "did" => "2",
            "veids_txt" => "B"
        ]
    ]

];

foreach($requests as $r_index => $request){

    $response = $client->post(
        $request["addr"], 
        ["form_params" => $request["params"]]
    );
    $code = $response->getStatusCode();

    if($code != 200){
        var_dump($r_index, $code);
        die();
    }

}
$body = $response->getBody();

$needle = "<option value=\"-1\">- Izvēle no saraksta -</option>";
$start = strpos($body, $needle) + strlen($needle);
$end = strpos($body, "</select>", $start);
$options = explode("\n", substr($body, $start, $end - $start));

foreach($options as $option){
    if(!$option){
        continue;
    }

    $matches = false;
    preg_match("/(?<=\<option value\=\"\d{6}\" \>).+(?=\<\/option\>)/", $option, $matches);
    
    $match_arr = false;
    if($matches){
       $match_arr = explode(" ", $matches[0]);
    } else {
        var_dump("breaked on:", $option, $option);
        die();
    }

    if($match_arr[3] > 0){
        echo $match_arr[0] . " " . $match_arr[3] . "\n";
    }
}