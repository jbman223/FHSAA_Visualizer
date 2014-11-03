<?php
require_once "require.php";
require_once "statisticalFunctions.php";
header('Content-Type: application/json');
function predictTime($meetPairs, $eventName, $class, $time) {
    global $db;
    $ret = array();

    $state = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND `year` = ? AND meet_title LIKE ?");
    $secondaryQuery = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND `year` = ? AND swimmer_id = ?");
    foreach ($meetPairs as $secondMeet => $startingMeet) {
        $x = array();
        $y = array();
        $state->execute(array($eventName, $startingMeet, "2013", $class));
        $states = $state->fetchAll();
        foreach ($states as $swim) {
            if ($swim[0] != 0) {
                $secondaryQuery->execute(array($eventName, $secondMeet, "2013", $swim["swimmer_id"]));
                $second = $secondaryQuery->fetchAll();
                //make sure there is more than one result (there may be results in the parsing created from database transfer or other variables unaccounted for.
                if (count($second) > 0) {
                    array_push($x, $second[0]["final_time"]);
                    array_push($y, $swim["final_time"]);
                    //echo $second[0]["final_time"] . "\t" . $swim["final_time"] . "\n";
                }
            }
        }
        $a = linear_regression($x, $y);

        $predictionTime = timeToDouble(urldecode($time)) * $a['a'] + $a['b'];
        array_push($ret, array("startTime" => $_GET['time'], "time" => $predictionTime, "goingFrom" => $secondMeet, "to" => $startingMeet));
    }

    return $ret;
}

function amountOfYards ($eventName) {
    if (strstr($eventName, "500")) {
        return 10;
    } else if (strstr($eventName, "200")) {
        return 4;
    }  else if (strstr($eventName, "100")) {
        return 2;
    } else if (strstr($eventName, "50")) {
        return 1;
    }
    return 1;
}

function adjustedPredictionTime($event, $timeChangePerLength) {
    return (amountOfYards($event) * $timeChangePerLength);
}

if (isset($_GET['type']) && $_GET['type'] == "time") {
    if (isset($_GET['class']) && isset($_GET['e'])) {
        $eventName = urldecode($_GET['e']);
        $class = "%" . urldecode($_GET['class']) . "%";
        $meetPairs = array("Districts" => "States", "Regionals" => "States");

        die(json_encode(predictTime($meetPairs, $eventName, $class, $_GET['time'])));
    }
} else if (isset($_GET['meet'])) {

} else if (isset($_GET['class']) && (isset($_GET['type']) && $_GET['type'] == "time")) {
    //get top district times

} else if (isset($_GET['f'], $_GET['l'])) {
    $totalRet = array();

    $totalRet["swimmerName"] = ucwords(urldecode($_GET['f'])." ".urldecode($_GET['l']));

    //get swimmer id
    $swimmerIdStatement = $db->prepare("SELECT id FROM swimmers WHERE f_name = ? AND l_name = ?");
    $swimmerIdStatement->execute(array(urldecode($_GET['f']), urldecode($_GET['l'])));
    $swimmer_id = $swimmerIdStatement->fetchAll(PDO::FETCH_ASSOC)[0]['id'];

    $totalRet['willMakeStates'] = array();
    //predict if the swimmer will make states this year
    //get events swimmer has swam so far
    $alpha = $db->prepare("SELECT * FROM swim_information WHERE swimmer_id = ? AND `year` = ? AND meet_type = ?");
    $alpha->execute(array($swimmer_id, "2014", "Districts"));
    $swims = $alpha->fetchAll(PDO::FETCH_ASSOC);
    $fastestTime = $db->prepare("SELECT seed_time FROM swim_information WHERE `year` = ? AND meet_title LIKE ? AND event_name = ? AND finals_swim = 0 ORDER BY seed_time DESC LIMIT 0, 1");
    foreach ($swims as $swim) {
        $thisEvent = array();
        //get event name
        $event = $swim['event_name'];
        $thisEvent['event'] = $event;
        //get class
        $class = explode(" ", $swim['meet_title'])[1];
        $classLong = "FHSAA Championship - Class $class%";

        $fastestTime->execute(array("2013", $classLong, $event));
        $time = $fastestTime->fetchAll(PDO::FETCH_ASSOC)[0]['seed_time'];
        $thisEvent['leastTime'] = $time;

        $meetPairs = array("Districts" => "Regionals");
        $predictedTime = predictTime($meetPairs, $event, "%".$class."%", $swim['final_time'])[0]['time'];
        $thisEvent['predictedTime'] = $predictedTime;

        $thisEvent['actualTime'] = $swim['final_time'];
        $meetPairs = array("Districts" => "States");
        $thisEvent['predictedTimeStates'] = predictTime($meetPairs, $event, "%".$class."%", $swim['final_time'])[0]['time'];

        if ($predictedTime < $time-adjustedPredictionTime($event, 1.3)) {
            $thisEvent['willMakeStates'] = "Highly Likely";
        } else if ($predictedTime < $time-adjustedPredictionTime($event, 0.8)) {
            $thisEvent['willMakeStates'] = "Very Likely";
        } else if ($predictedTime <= $time) {
            $thisEvent['willMakeStates'] = "Likely";
        } else if ($predictedTime <= $time+adjustedPredictionTime($event, 0.5)) {
            $thisEvent['willMakeStates'] = "Unlikely";
        } else if ($predictedTime <= $time+adjustedPredictionTime($event, 1.3)) {
            $thisEvent['willMakeStates'] = "Very Unlikely";
        } else {
            $thisEvent['willMakeStates'] = "Highly Unlikely";
        }

        $db->query("SET @rank = 0;");
        $a = $db->prepare("SELECT @rank:=@rank+1 AS rank, `swimmers`.`f_name`, `swimmers`.`l_name`, swimmer_id, `final_time` FROM `swim_information` INNER JOIN `swimmers` ON `swimmers`.`id` = `swim_information`.`swimmer_id` WHERE `meet_type` = ? AND year = ? AND `final_time` != 0 AND `event_name` = ? AND meet_title LIKE ? ORDER BY `final_time` ASC LIMIT 0, 24;");
        $a->execute(array("Districts", "2014", $event, "%$class%"));
        $thisEvent['statePredictions'] = $a->fetchAll(PDO::FETCH_ASSOC);

        array_push($totalRet['willMakeStates'], $thisEvent);
    }

    //log predictions if there are any
    $totalRet['logPredictions'] = array();
    $state = $db->prepare("SELECT * FROM log_predictions WHERE `name` = ?");
    $state->execute(array(urldecode($_GET['f'])." ".urldecode($_GET['l'])));

    //get events swimmer swam in 2014
    $alpha = $db->prepare("SELECT * FROM swim_information WHERE swimmer_id = ? AND `year` = ? AND event_name = ? AND meet_type = ?");

    //get log events
    $ret = $state->fetchAll(PDO::FETCH_ASSOC);

    foreach ($ret as $logSwim) {
        $tR = array();
        $alpha->execute(array($swimmer_id, "2014", $logSwim['event'], "Districts"));
        $recentSwims = $alpha->fetchAll(PDO::FETCH_ASSOC);
        $event= $logSwim['event'];
        if (count($recentSwims) == 1) {
            $finalTime = $recentSwims[0]["final_time"];
        } else {
            $finalTime = "00:00.00";
        }
        $predictedTime = $logSwim['time'];
        $tR = array("event" => $event, "finalTime" => $finalTime, "predictedTime" => $predictedTime);
        array_push($totalRet['logPredictions'], $tR);
    }

    die(json_encode($totalRet));
}