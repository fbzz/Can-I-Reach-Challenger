<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

require __DIR__. '/../src/userVO.php';

//require __DIR__. '/../src/functions.php';

require __DIR__. '/../src/summoner.php';

//require __DIR__. '/../src/teste.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';




$app->get('/{region}/{name}', function ($watt, $call, $test) {
  $summoner = new summoner();
 // $challenger = new challenger();
 // $result_challenger = $challenger->getChallenger('br');
 $userVO = new userVO();
 $userVO->setSumRegion($test["region"]);
 $userVO->setSumName($test["name"]);
$summonerName =  $userVO->getSumName();
$summonerRegion = $userVO->getSumRegion();
echo $summonerName.$summonerRegion;


 $result = $summoner->getSummoner($summonerName, $summonerRegion);


 return $result;
});




// Run app
$app->run();
