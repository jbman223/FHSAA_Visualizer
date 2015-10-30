<?php
require_once "require.php";
require_once "statisticalFunctions.php";

$events = array(
    "Event 11  Girls 100 Yard Butterfly",
    "Event 12  Boys 100 Yard Butterfly",
    "Event 13  Girls 100 Yard Freestyle",
    "Event 14  Boys 100 Yard Freestyle",
    "Event 16  Boys 500 Yard Freestyle",
    "Event 17  Girls 500 Yard Freestyle",
    "Event 19  Girls 100 Yard Backstroke",
    "Event 20  Boys 100 Yard Backstroke",
    "Event 21  Girls 100 Yard Breaststroke",
    "Event 22  Boys 100 Yard Breaststroke",
    "Event 3  Girls 200 Yard Freestyle",
    "Event 4  Boys 200 Yard Freestyle",
    "Event 5  Girls 200 Yard IM",
    "Event 6  Boys 200 Yard IM",
    "Event 7  Girls 50 Yard Freestyle",
    "Event 8  Boys 50 Yard Freestyle"
);

$predictions = array();
$i = 0;
foreach ($events as $event) {
    //echo $event."<br>";
    $timeRelationArray = array();
    $exp = array();
    $resp = array();
    $predictions[$i] = array();

    $state = $db->prepare("SELECT final_time, swimmer_id FROM swim_information WHERE meet_type = ? AND `year` = ? AND final_time != 0 AND event_name = ?");
    $state->execute(array("Counties", $currentYear-1, $event));
    $countiesSwims = $state->fetchAll(PDO::FETCH_ASSOC);
//print_r($countiesSwims);
    $newState = $db->prepare("SELECT final_time FROM swim_information WHERE meet_type = ? AND `year` = ? AND final_time != 0 AND event_name = ? AND swimmer_id = ?");
    foreach ($countiesSwims as $countiesSwim) {
        $newState->execute(array("Districts", $currentYear-1, $event, $countiesSwim['swimmer_id']));
        $ret = $newState->fetchAll(PDO::FETCH_ASSOC);
        if (isset($ret[0]['final_time'])) {
            array_push($timeRelationArray, array("districts" => $ret[0]['final_time'], "counties" => $countiesSwim['final_time']));
            array_push($exp, $countiesSwim['final_time']);
            array_push($resp, $ret[0]['final_time']);
        }
    }

    $linearRegression = linear_regression($exp, $resp);
    //print_r($linearRegression);

    $state = $db->prepare("SELECT final_time, swimmer_id FROM swim_information WHERE meet_type = ? AND `year` = ? AND final_time != 0 AND event_name = ?");
    $state->execute(array("Counties", $currentYear, $event));
    $things = $state->fetchAll(PDO::FETCH_ASSOC);
    $swimmer = $db->prepare("SELECT * FROM swimmers WHERE id = ?");
    foreach ($things as $thing) {
        $swimmer->execute(array($thing['swimmer_id']));
        $swimmerArray = $swimmer->fetchAll(PDO::FETCH_ASSOC);
        //echo "".$swimmerArray[0]['f_name']." ".$swimmerArray[0]['l_name']." will likely swim a ".($linearRegression['a']*$thing['final_time']+$linearRegression['b'])." at districts this year.<br>";
        array_push($predictions[$i], array("name" => $swimmerArray[0]['f_name']." ".$swimmerArray[0]['l_name'], "time" => ($linearRegression['a']*$thing['final_time']+$linearRegression['b']), "event" => $event));
        //addLogMeetThing($swimmerArray[0]['f_name']." ".$swimmerArray[0]['l_name'], ($linearRegression['a']*$thing['final_time']+$linearRegression['b']), $event);
    }
    //print_r($things);
    //echo $event."<br>";
    //echo "---------------------------NEW EVENT-----------------------------<br>";
    $i++;
}

print_r(json_encode($predictions));



//$state = $db->prepare("SELECT final_time, swimmer_id FROM swim_information WHERE meet_type = ? AND `year` = ? AND final_time != 0 AND event_name = ?");
//$state->execute(array("Counties", "2013", $event));