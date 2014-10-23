<?php
require_once "require.php";
require_once "statisticalFunctions.php";
header('Content-Type: application/json');
if (isset($_GET['type']) && $_GET['type'] == "time") {
    if (isset($_GET['class']) && isset($_GET['e'])) {
        $ret = array();
        //$ret["startTime"] = $_GET['time'];
        $eventName = urldecode($_GET['e']);
        $class = "%" . urldecode($_GET['class']) . "%";
        $meetPairs = array("Districts" => "States", "Regionals" => "States");
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

            $predictionTime = timeToDouble(urldecode($_GET['time'])) * $a['a'] + $a['b'];
            array_push($ret, array("startTime" => $_GET['time'], "time" => $predictionTime, "goingFrom" => $secondMeet, "to" => $startingMeet));
        }

        die(json_encode($ret));
    }
} else if (isset($_GET['meet'])) {

} else if (isset($_GET['class']) && (isset($_GET['type']) && $_GET['type'] == "time")) {
    //get top district times

} else if (isset($_GET['f'], $_GET['l'])) {
    $totalRet = array();

    $totalRet['logPredictions'] = array();
    $state = $db->prepare("SELECT * FROM log_predictions WHERE name = ?");
    $state->execute(array(urldecode($_GET['f'])." ".urldecode($_GET['l'])));
    $ret = $state->fetchAll(PDO::FETCH_ASSOC);
    array_push($totalRet['logPredictions'], $ret);
    die(json_encode($totalRet));
}