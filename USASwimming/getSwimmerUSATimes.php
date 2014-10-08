<?php
//parse USA Swimming times
require_once "../USASwimming/require.php";

function getTimes($fName, $lName)
{
    header('Content-Type: application/json');
    $headers = array(
        "User-Agent: Appcelerator Titanium/3.1.3.GA (iPhone/8.0; iPhone OS; en_US;)",
        "content-type: application/json"
    );
    $people = json_decode(postJSON("https://mobile.usaswimming.org/usaswcfm/Service.svc/SearchAthletes", "{\"FirstName\":\"" . $fName . "\",\"LastName\":\"" . $lName . "\"}", $headers));

    $timesArray = array();
    if (isset($people->error)) {
        die(json_encode($people->error));
    } elseif (count($people->Members) == 0) {
    } else {
        $requestJson = "{\"PersonId\":\"" . $people->Members[0]->PersonId . "\",\"CompetitionPeriodId\":-1,\"EventId\":-1,\"FastestTimesOnlyBool\":true,\"BatchNumber\":1,\"DeviceId\":\"\"}";
        $stuff = postJSON("https://mobile.usaswimming.org/usaswcfm/Service.svc/GetIndividualTimes", $requestJson, $headers);
        $stuffJSON = json_decode($stuff);
        //echo $stuff;

        if (count($stuffJSON->IndividualTimes) > 0) {
            for ($num = 0; $num < count($stuffJSON->IndividualTimes); $num++) {
                $current = $stuffJSON->IndividualTimes[$num];
                if (strstr($current->Event, "SCY")) {
                    array_push($timesArray, array("event" => $current->Event, "time" => $current->SwimTime, "date" => strtotime($current->Date) * 1000));
                }
            }
        } else {
        }
    }

    return $timesArray;
}