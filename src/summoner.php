<?php


class summoner {
    public function __construct(){

    }

    function getSummoner($summonerName, $summonerRegion){

        $summonerName = strtolower($summonerName);
        $summonerName = preg_replace("/[\s-]+/", "", $summonerName);
        $summonerRegion = strtolower($summonerRegion);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $summonerRegion . '.api.pvp.net/api/lol/' . $summonerRegion . '/v1.4/summoner/by-name/' . $summonerName . '?api_key=YOUR_KEY');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);


        $response_json = json_decode($response);

        $summoner_id = $response_json->$summonerName->id;
        $summoner_profileIcon = $response_json->$summonerName->profileIconId;
        $summoner_level = $response_json->$summonerName->summonerLevel;

        //////////////////////
        // GET MATCHLIST
        //////////////////////

        curl_setopt($ch, CURLOPT_URL, 'https://' . $summonerRegion . '.api.pvp.net/api/lol/' . $summonerRegion . '/v2.2/matchlist/by-summoner/' . $summoner_id . '?seasons=SEASON2016&beginIndex=0&endIndex=5&api_key=YOUR_KEY');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        curl_setopt($ch, CURLOPT_URL, 'https://'.$summonerRegion.'.api.pvp.net/api/lol/'.$summonerRegion.'/v2.5/league/by-summoner/'.$summoner_id.'/entry?api_key=YOUR_KEY');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response_league = curl_exec($ch);

        curl_close($ch);

        $response_json = json_decode($response, true);
        $endMatchListIndex = $response_json['endIndex'];
        $matchList = $response_json['matches'];

        $average_goldEarned                     = 0;
        $average_numDeaths                      = 0;
        $average_minionsKilled                  = 0;
        $average_championsKilled                = 0;
        $average_neutralMinionsKilled           = 0;
        $average_timePlayed                     = 0;
        $average_assists                        = 0;
        $average_totalDamageDealtToChampions    = 0;
        $average_wardKilled                     = 0;
        $average_wardPlaced                     = 0;
        $average_wins                           = 0;
        $average_losses                         = 0;
        $average_winRate                        = 0;
        $average_totalTeamDamage                = 0;

        foreach ($matchList as $match) {
            $matchId = $match['matchId'];
            $matchRegion = $match['region'];

            // GET MATCH DETAILS
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://' . $summonerRegion . '.api.pvp.net/api/lol/' . $summonerRegion . '/v2.2/match/' . $matchId . '?api_key=YOUR_KEY');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $response_json = json_decode($response, true);

            // GET PARTICIPANTS
            foreach ($response_json["participantIdentities"] as $participant) {
                $participantName = $participant['player']['summonerName'];

                // CHECK IF PARTICIPANT IS = SUMMONER
                if (strcasecmp($summonerName, $participantName) == 0) {
                    $participantId = $participant["participantId"];
                    $participantIndex = $participant["participantId"] - 1;

                    // GET THE GAME STATS
                    $gameStats = $response_json["participants"][$participantIndex]['stats'];

                    if ($gameStats['winner']) {
                        $average_wins = $average_wins + 1;
                    }else{
                        $average_losses = $average_losses + 1;
                    }

                    if(isset($gameStats['deaths'])){
                        $average_numDeaths = $average_numDeaths + $gameStats['deaths'];
                    }else{
                        $average_numDeaths = $average_numDeaths + 0;
                    }

                    if(isset($gameStats['kills'])){
                        $average_championsKilled  = $average_championsKilled + $gameStats['kills'];
                    }else{
                        $average_championsKilled  = $average_championsKilled + 0;
                    }

                    if (isset($gameStats['assists'])) {
                        $average_assists = $average_assists + $gameStats['assists'];
                    }else{
                        $average_assists = $average_assists + 0;
                    }

                    if(isset($gameStats["totalDamageDealtToChampions"])){
                        $average_totalDamageDealtToChampions = $average_totalDamageDealtToChampions + $gameStats["totalDamageDealtToChampions"];
                    }else{
                        $average_totalDamageDealtToChampions = $average_totalDamageDealtToChampions + 0;
                    }

                    if (isset($gameStats['minionsKilled'])) {
                        $average_minionsKilled = $average_minionsKilled + $gameStats['minionsKilled'];
                    }else{
                        $average_minionsKilled = $average_minionsKilled + 0;
                    }


                    if (isset($gameStats['neutralMinionsKilled'])) {
                        $average_neutralMinionsKilled = $average_neutralMinionsKilled + $gameStats['neutralMinionsKilled'];
                    }else{
                        $average_neutralMinionsKilled = $average_neutralMinionsKilled + 0;
                    }

                    if (isset($gameStats['goldEarned'])) {
                        $average_goldEarned = $average_goldEarned + $gameStats['goldEarned'];
                    }else{
                        $average_goldEarned = $average_goldEarned + 0;
                    }

                    if (isset($gameStats['wardsPlaced'])) {
                        $average_wardPlaced = $average_wardPlaced + $gameStats['wardsPlaced'];
                    }else{
                        $average_wardPlaced = $average_wardPlaced + 0;
                    }

                    if(isset($gameStats['wardsKilled'])){
                        $average_wardKilled = $average_wardKilled + $gameStats['wardsKilled'];
                    }else{
                        $average_wardKilled = $average_wardKilled + 0;
                    }

                    if (isset($response_json['matchDuration'])) {
                        $average_timePlayed = $average_timePlayed + $response_json['matchDuration'];
                    }else{
                        $average_timePlayed = $average_timePlayed + 0;
                    }
                }else{
                    $participantId = $participant["participantId"];
                    $participantIndex = $participant["participantId"] - 1;

                    $gameStats = $response_json["participants"][$participantIndex]['stats'];

                    if ($gameStats['winner']) {
                        $average_totalTeamDamage = $average_totalTeamDamage + $gameStats["totalDamageDealtToChampions"];
                    }else{
                        $average_totalTeamDamage = $average_totalTeamDamage + 0;
                    }
                }
            }
        }

        // SET FINAL AVERAGE
        $average_goldEarned                     = round($average_goldEarned/$endMatchListIndex, 1);
        $average_numDeaths                      = round($average_numDeaths/$endMatchListIndex, 1);
        $average_minionsKilled                  = round($average_minionsKilled/$endMatchListIndex, 0);
        $average_championsKilled                = round($average_championsKilled/$endMatchListIndex, 1);
        $average_neutralMinionsKilled           = round($average_neutralMinionsKilled/$endMatchListIndex, 0);
        $average_timePlayed                     = round(($average_timePlayed/$endMatchListIndex)/60, 0);
        $average_assists                        = round($average_assists/$endMatchListIndex, 1);
        $average_totalTeamDamage                = round(($average_totalTeamDamage + $average_totalDamageDealtToChampions)/$endMatchListIndex, 0);
        $average_totalDamageDealtToChampions    = round($average_totalDamageDealtToChampions/$endMatchListIndex, 0);
        $average_wardKilled                     = round($average_wardKilled/$endMatchListIndex, 0);
        $average_wardPlaced                     = round($average_wardPlaced/$endMatchListIndex, 0);
        $average_winRate                        = round((($average_wins/$endMatchListIndex)*100), 0);


        // GET CHALLENGER
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $summonerRegion . '.api.pvp.net/api/lol/' . $summonerRegion . '/v2.5/league/challenger?type=RANKED_SOLO_5x5&api_key=YOUR_KEY');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response, true);

        $challengerLadder = $response['entries'];
        $challengerSummoner = $challengerLadder[rand(0, 199)];
        $challengerId = $challengerSummoner['playerOrTeamId'];
        $challengerName = $challengerSummoner['playerOrTeamName'];


            // GETTING MATCH LIST

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://' . $summonerRegion . '.api.pvp.net/api/lol/' . $summonerRegion . '/v2.2/matchlist/by-summoner/' . $challengerId . '?seasons=SEASON2016&beginIndex=0&endIndex=5&api_key=YOUR_KEY');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $response = json_decode($response, true);
            $endMatchListIndexChallenger = $response['endIndex'];
            $matchListChallenger = $response['matches'];

            $challenger_average_goldEarned                     = 0;
            $challenger_average_numDeaths                      = 0;
            $challenger_average_minionsKilled                  = 0;
            $challenger_average_championsKilled                = 0;
            $challenger_average_neutralMinionsKilled           = 0;
            $challenger_average_timePlayed                     = 0;
            $challenger_average_assists                        = 0;
            $challenger_average_totalDamageDealtToChampions    = 0;
            $challenger_average_wardKilled                     = 0;
            $challenger_average_wardPlaced                     = 0;
            $challenger_average_wins                           = 0;
            $challenger_average_losses                         = 0;
            $challenger_average_winRate                        = 0;
            $challenger_average_totalTeamDamage                = 0;

            foreach ($matchListChallenger as $matchListChallenger) {
                $matchId = $matchListChallenger['matchId'];
                $matchRegion = $matchListChallenger['region'];

                // GET MATCH DETAILS
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://' . $summonerRegion . '.api.pvp.net/api/lol/' . $summonerRegion . '/v2.2/match/' . $matchId . '?api_key=YOUR_KEY');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);
                curl_close($ch);

                $response = json_decode($response, true);

                // GET PARTICIPANTS
                foreach ($response["participantIdentities"] as $participant) {
                    $participantName = $participant['player']['summonerName'];

                    // CHECK IF PARTICIPANT = CHALLENGER
                    if (strcasecmp($challengerName, $participantName) == 0) {
                        $participantId = $participant["participantId"];
                        $participantIndex = $participant["participantId"] - 1;

                        // GET THE GAME STATS
                        $gameStats = $response["participants"][$participantIndex]['stats'];

                        if ($gameStats['winner']) {
                            $challenger_average_wins = $challenger_average_wins + 1;
                        }else{
                            $challenger_average_losses = $challenger_average_losses + 1;
                        }

                        if(isset($gameStats['deaths'])){
                            $challenger_average_numDeaths = $challenger_average_numDeaths + $gameStats['deaths'];
                        }else{
                            $challenger_average_numDeaths = $challenger_average_numDeaths + 0;
                        }

                        if(isset($gameStats['kills'])){
                            $challenger_average_championsKilled  = $challenger_average_championsKilled + $gameStats['kills'];
                        }else{
                            $challenger_average_championsKilled  = $challenger_average_championsKilled + 0;
                        }

                        if (isset($gameStats['assists'])) {
                            $challenger_average_assists = $challenger_average_assists + $gameStats['assists'];
                        }else{
                            $challenger_average_assists = $challenger_average_assists + 0;
                        }

                        if(isset($gameStats["totalDamageDealtToChampions"])){
                            $challenger_average_totalDamageDealtToChampions = $challenger_average_totalDamageDealtToChampions + $gameStats["totalDamageDealtToChampions"];
                        }else{
                            $challenger_average_totalDamageDealtToChampions = $challenger_average_totalDamageDealtToChampions + 0;
                        }

                        if (isset($gameStats['minionsKilled'])) {
                            $challenger_average_minionsKilled = $challenger_average_minionsKilled + $gameStats['minionsKilled'];
                        }else{
                            $challenger_average_minionsKilled = $challenger_average_minionsKilled + 0;
                        }


                        if (isset($gameStats['neutralMinionsKilled'])) {
                            $challenger_average_neutralMinionsKilled = $challenger_average_neutralMinionsKilled + $gameStats['neutralMinionsKilled'];
                        }else{
                            $challenger_average_neutralMinionsKilled = $challenger_average_neutralMinionsKilled + 0;
                        }

                        if (isset($gameStats['goldEarned'])) {
                            $challenger_average_goldEarned = $challenger_average_goldEarned + $gameStats['goldEarned'];
                        }else{
                            $challenger_average_goldEarned = $challenger_average_goldEarned + 0;
                        }

                        if (isset($gameStats['wardsPlaced'])) {
                            $challenger_average_wardPlaced = $challenger_average_wardPlaced + $gameStats['wardsPlaced'];
                        }else{
                            $challenger_average_wardPlaced = $challenger_average_wardPlaced + 0;
                        }

                        if(isset($gameStats['wardsKilled'])){
                            $challenger_average_wardKilled = $challenger_average_wardKilled + $gameStats['wardsKilled'];
                        }else{
                            $challenger_average_wardKilled = $challenger_average_wardKilled + 0;
                        }

                        if (isset($response['matchDuration'])) {
                            $challenger_average_timePlayed = $challenger_average_timePlayed + $response['matchDuration'];
                        }else{
                            $challenger_average_timePlayed = $challenger_average_timePlayed + 0;
                        }
                    }else{
                        $participantId = $participant["participantId"];
                        $participantIndex = $participant["participantId"] - 1;

                        $gameStats = $response_json["participants"][$participantIndex]['stats'];

                        if ($gameStats['winner']) {
                            $challenger_average_totalTeamDamage = $challenger_average_totalTeamDamage + $gameStats["totalDamageDealtToChampions"];
                        }else{
                            $challenger_average_totalTeamDamage = $challenger_average_totalTeamDamage + 0;
                        }
                    }
                }
            }

        // SET CHALLENGER AVERAGE

        $challenger_average_goldEarned                     = round($challenger_average_goldEarned/$endMatchListIndexChallenger, 1);
        $challenger_average_numDeaths                      = round($challenger_average_numDeaths/$endMatchListIndexChallenger, 1);
        $challenger_average_minionsKilled                  = round($challenger_average_minionsKilled/$endMatchListIndexChallenger, 0);
        $challenger_average_championsKilled                = round($challenger_average_championsKilled/$endMatchListIndexChallenger, 1);
        $challenger_average_neutralMinionsKilled           = round($challenger_average_neutralMinionsKilled/$endMatchListIndexChallenger, 0);
        $challenger_average_timePlayed                     = round(($challenger_average_timePlayed/$endMatchListIndexChallenger)/60, 0);
        $challenger_average_assists                        = round($challenger_average_assists/$endMatchListIndexChallenger, 1);
        $challenger_average_totalTeamDamage                = round(($challenger_average_totalTeamDamage + $challenger_average_totalDamageDealtToChampions)/$endMatchListIndexChallenger, 0);
        $challenger_average_totalDamageDealtToChampions    = round($challenger_average_totalDamageDealtToChampions/$endMatchListIndexChallenger, 0);
        $challenger_average_wardKilled                     = round($challenger_average_wardKilled/$endMatchListIndexChallenger, 0);
        $challenger_average_wardPlaced                     = round($challenger_average_wardPlaced/$endMatchListIndexChallenger, 0);
        $challenger_average_winRate                        = round((($challenger_average_wins/$endMatchListIndexChallenger)*100), 0);

         $summonerAverageStats = compact("average_goldEarned", "average_numDeaths", "average_minionsKilled", "average_championsKilled", "average_neutralMinionsKilled", "average_timePlayed", "average_assists", "average_magicDamageDealtToChampions", "average_physicalDamageDealtToChampions", "average_totalDamageDealtToChampions", "average_trueDamageDealtToChampions", "average_totalTeamDamage", "average_wardKilled", "average_wardPlaced", "average_winRate", "average_totalTimeCrowdControlDealt", "summoner_profileIcon", "summoner_level", "summoner_id", "response_league", "summonerRegion", "farmOnTenGames", "challenger_average_goldEarned", "challenger_average_numDeaths", "challenger_average_minionsKilled", "challenger_average_championsKilled", "challenger_average_neutralMinionsKilled", "challenger_average_timePlayed", "challenger_average_assists", "challenger_average_totalDamageDealtToChampions", "challenger_average_totalTeamDamage", "challenger_average_wardKilled", "challenger_average_wardPlaced", "challenger_average_winRate");

         return json_encode($summonerAverageStats);
    }
}
