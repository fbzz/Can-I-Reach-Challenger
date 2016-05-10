var myApp = angular.module('Challenger', ['ngRoute', 'ngAnimate', 'angularCharts']);

myApp.config(['$routeProvider', '$locationProvider',
  function($routeProvider, $locationProvider) {
    $routeProvider
      .when('/Can-I-Reach-Challenger/public/', {
        templateUrl: '/Can-I-Reach-Challenger/templates/search.html',
        controller: 'dashboardCtrl',
        controllerAs: 'dashboardCtrl'
      })
    $routeProvider
      .when('/Can-I-Reach-Challenger/public/about', {
        templateUrl: '/Can-I-Reach-Challenger/templates/about.html',
        controller: 'mainCtrl',
        controllerAs: 'mainCtrl'
      })
    $routeProvider
      .when('/Can-I-Reach-Challenger/public/contact', {
        templateUrl: '/Can-I-Reach-Challenger/templates/contact.html',
        controller: 'mainCtrl',
        controllerAs: 'mainCtrl'
      })
    $routeProvider
      .when('/Can-I-Reach-Challenger/public/:region/:name/details', {
        templateUrl: '/Can-I-Reach-Challenger/templates/details.html',
        controller: 'detailsCtrl',
        controllerAs: 'detailsCtrl'
      });

    $locationProvider.html5Mode(true);
  }
])
myApp.controller('mainCtrl', ['$scope', '$http', '$rootScope', '$location', '$route', '$routeParams', function($scope, $http, $rootScope, $location, $route, $routeParams) {
  this.$route = $route;
  this.$location = $location;
  this.$routeParams = $routeParams;
}]);

myApp.controller('dashboardCtrl', ['$scope', '$http', '$rootScope', '$location', '$route', '$routeParams', function($scope, $http, $rootScope, $location, $route, $routeParams) {
  $scope.spice = 'very';
  $scope.sumName = "";
  $scope.sumRegion = "BR";
  $scope.test = true;

  $scope.summonerSearch = function($sumName, $sumRegion) {
    $scope.test = false;
    $rootScope.loading = true;
    $url = "/Can-I-Reach-Challenger/public/" + $sumRegion + "/" + $sumName;
    $http({
      method: "GET",
      url: $url
    }).
    then(function(response) {
      $scope.status = response.status;
      $scope.data = response.data;
      $rootScope.loading = false;
      $rootScope.details = $scope.data;
      $rootScope.sumName = $sumName;
      $rootScope.$sumRegion = $sumRegion;
      //  alert($rootScope.details);
      $location.path("/Can-I-Reach-Challenger/public/" + $sumRegion + "/" + $sumName + "/details");
    }, function(response) {
      $scope.data = response.data || "Request failed";
      $scope.status = response.status;
    });
  };
}]);
myApp.controller('detailsCtrl', ['$scope', '$http', '$rootScope', '$route', '$routeParams', '$location', function($scope, $http, $rootScope, $route, $routeParams, $location) {
  var dc = this;

  $scope.spice = 'very';
  $stringLength = $rootScope.sumName.length + $rootScope.$sumRegion.length;
  $scope.sumInfo = $rootScope.details.slice(0, -$stringLength);
  $scope.sumInfo = JSON.parse($scope.sumInfo);
  $scope.leagueInfo = $scope.sumInfo.response_league;
  $scope.leagueInfo = JSON.parse($scope.leagueInfo);
  $id = $scope.sumInfo.summoner_id;
  console.log($scope.sumInfo);
  $scope.tier = $scope.leagueInfo[$id][0]['tier'];
  $scope.division = $scope.leagueInfo[$id][0]['entries'][0]['division'];
  $scope.points = $scope.leagueInfo[$id][0]['entries'][0]['leaguePoints'];
  $scope.KDA = ($scope.sumInfo.average_championsKilled + $scope.sumInfo.average_assists) / $scope.sumInfo.average_numDeaths;
  var num = parseFloat($scope.KDA);
  var str = num.toFixed(10);
  str = str.substring(0, str.length - 8);
  $scope.KDA = str;
  $scope.KDAChallenger = ($scope.sumInfo.challenger_average_championsKilled + $scope.sumInfo.challenger_average_assists) / $scope.sumInfo.average_numDeaths;
  var num_challenger = parseFloat($scope.KDAChallenger);
  var strChallenger = num_challenger.toFixed(10);
  strChallenger = strChallenger.substring(0, strChallenger.length - 8);
  $scope.KDAChallenger = strChallenger;
  $scope.totalFarm = $scope.sumInfo.average_minionsKilled + $scope.sumInfo.average_neutralMinionsKilled;
  $scope.totalFarmChallenger = $scope.sumInfo.challenger_average_minionsKilled + $scope.sumInfo.challenger_average_neutralMinionsKilled;


  //Chart for farm
  $scope.config = {
    "labels": false,
    "title": false,
    "legend": {
      "display": false,
      "position": "right"
    },
    "innerRadius": 0,
    "isAnimate": true,
  }

  $scope.data = {
    series: ['You', 'Challenger'],
    data: [{
      x: "Neutral",
      y: [$scope.sumInfo.average_neutralMinionsKilled, $scope.sumInfo.challenger_average_neutralMinionsKilled],
    }, {
      x: "Minions",
      y: [$scope.sumInfo.average_minionsKilled, $scope.sumInfo.challenger_average_minionsKilled]
    }, {
      x: "Total",
      y: [$scope.totalFarm, $scope.totalFarmChallenger]
    }]
  }

  //Chart for damage

$scope.configPie = {
  "labels": false,
  "title": false,
  "legend": {
    "display": false,
    "position": "right"
  },
  "colors":['#00B0FF','rgb(55, 71, 79)'],
  "innerRadius": "2"
}


$scope.informationSummoner = {
  series : ['teste', 'teste'],
  data : [{
      x : "Average Damage Dealt to Champions",
      y: [$scope.sumInfo.average_totalDamageDealtToChampions],
      tooltip: $scope.sumInfo.average_totalDamageDealtToChampions
    }, {
      x : "Average Total Team Damage",
      y: [$scope.sumInfo.average_totalTeamDamage],
      tooltip:$scope.sumInfo.average_totalTeamDamage
    }]
}

$scope.configPieChallenger = {
  "labels": false,
  "title": false,
  "legend": {
    "display": false,
    "position": "right"
  },
  "colors":['#FFC809','rgb(55, 71, 79)'],
  "innerRadius": "2"
}

$scope.informationChallenger = {
  series : ['teste', 'teste'],
  data : [{
      x : "Average Damage Dealt to Champions",
      y: [$scope.sumInfo.challenger_average_totalDamageDealtToChampions],
      tooltip: $scope.sumInfo.challenger_average_totalDamageDealtToChampions
    }, {
      x : "Average Total Team Damage",
      y: [$scope.sumInfo.challenger_average_totalTeamDamage],
      tooltip:$scope.sumInfo.challenger_average_totalTeamDamage
    }]
}




  pathToTier = function($tier, $division) {
    $tier = $tier.toLowerCase();
    $division = $division.toLowerCase();
    return $tier + "_" + $division + ".png";
  }



  $scope.pathToTier = pathToTier($scope.tier, $scope.division);

  //scope.getSummonerLeagueRankedSolo($scope.leagueInfo, $scope.sumInfo);




//Here come the TIPS for the guyssss

if ($scope.KDA >= $scope.KDAChallenger){
  $scope.KDAmessage = "Nice one! Your KDA is great and better than some of the best players in your region. Keep doing a great job and you are going to climb the ladder soon enough!"
}else if ($scope.KDA < $scope.KDAChallenger){
  $scope.KDAmessage = "Even though your KDA is good, you may find some difficulties to climb the ladder. Try to die less, instead of being aggressive trying to find kills, you should just play passive and farm until you get your core items.";
}

if ($scope.sumInfo.average_winRate >= $scope.sumInfo.challenger_average_winRate){
  $scope.winRateMessage = "Congratulations, your win rate is good enough to keep improving and climbing the ladder."
}else if ($scope.sumInfo.average_winRate < $scope.sumInfo.challenger_average_winRate){
  $scope.winRateMessage = "Despite your win rate being better or worse than the other players, you should try to be more consistent. Pick champions that you are comfortable playing as and think more about you team composition."
}

if ($scope.sumInfo.average_goldEarned >= $scope.sumInfo.challenger_average_goldEarned){
  $scope.goldEarnedMessage = "Congratulations, you are earning more gold than some of the best players in your region on your recent games. Keep farming and improving."
}else if ($scope.sumInfo.average_goldEarned < $scope.sumInfo.challenger_average_goldEarned){
  $scope.goldEarnedMessage= "Farm more. If your team is behind, you should try to farm neutral monsters in your jungle, roaming and getting kills is another great way to get more gold. Besides that, getting many objectives as possible, it gives you a good amount of gold and some extra-buffs."
}


if ($scope.sumInfo.average_wardPlaced >= $scope.sumInfo.challenger_average_wardPlaced){
$scope.wardPlacedMessage = "Congratulations, you ward more than some of the best players in your region."
}else if ($scope.sumInfo.average_wardPlaced < $scope.sumInfo.challenger_average_wardPlaced){
 $scope.wardPlacedMessage = "Warding is essential to win a game. You don’t need to ward a lot of places but you definitely should ward your flanks and objectives. Getting vision is crucial to win games. Spending 75g for a Vision Ward can help your team to stay alive and take objectives. Upgrading your trinket is obligatory after the laning phase ends."
}

if ($scope.sumInfo.average_wardKilled >= $scope.sumInfo.challenger_average_wardKilled){
$scope.wardKillMessage = "You constantly deny your enemies’ vision, good job!"
}else if ($scope.sumInfo.average_wardKilled < $scope.sumInfo.challenger_average_wardKilled){
 $scope.wardKillMessage = "Denying the enemy team’s vision make the game harder for them, it allows your team to get kills and objectives for free.";
}

if ($scope.totalFarm >=$scope.totalFarmChallenger){
$scope.farmingMessage = "Wow! It seems like you are a farming machine. Keep farming more and more to increase your chances to win the game but don’t forget to help your teammates."
}else if ($scope.totalFarm < $scope.totalFarmChallenger){
 $scope.farmingMessage = "It seems like you are farming less than you should. To farm better pay attention to the minion’s health bar and just hit when it’s about to die. Farming neutral monsters is a great option to earn more gold."
}




  $scope.back = function() {
    $location.path("/Can-I-Reach-Challenger/public/");
  }


  window.onbeforeunload = function() {
    $location.path("/Can-I-Reach-Challenger/public/");
  }

}]);
