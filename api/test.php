<?php
require_once "require.php";
require_once "../USASwimming/getSwimmerUSATimes.php";
//require_once "../processQuery.php";

$swims = array();

echo "<pre>";

for ($i = 1; $i <= 12; $i++) {
    $swims[$i] = array();
    $state = $db->prepare("SELECT final_time FROM swim_information WHERE event_name = ? AND meet_title = ? AND final_time != 0 ORDER BY final_time ASC LIMIT 0, 8 ");
    $state->execute(array(urldecode("Event+16++Boys+500+Yard+Freestyle"), "FHSAA 2A District $i Championship"));
    $cSwims = $state->fetchAll(PDO::FETCH_ASSOC);
    for ($a = 0; $a < count($cSwims); $a++) {
        array_push($swims[$i], $cSwims[$a]);
    }
    //echo "District $i <br>";
}


foreach ($swims as $districtNumber => $swim) {
    $average = array_sum($swim);
    echo "Average time for District $districtNumber is ".$average."<br>";
}

echo "</pre>";


//processQuery("predictions time 1:30.40 class 1a in mens 100 breast");
//echo preg_match('#([0-9]{1,2}:)?([0-9][0-9])\\.([0-9][0-9])#', "32.20");

//$swimmer_id = getExistingSwimmerID("liam", "hollowsky");
//$state = $db->prepare("SELECT * FROM swim_information WHERE event_name = ?");
//$state->execute(array(urldecode("Event+16++Boys+500+Yard+Freestyle")));
//print_r($state->fetchAll(PDO::FETCH_ASSOC));

/*
$fives = array();
$array = $state->fetchAll(PDO::FETCH_ASSOC);
$guys = array();
foreach ($array as $swims) {
    $events = getSwimmerSwims($swims['swimmer_id']);
    $guy = false;
    foreach ($events as $event) {
        if (strstr(strtolower($event['event_name']), "boys")) {
            $guy = true;
        }
    }
    if ($guy) {
        array_push($guys, $swims);
    }
}

print_r($guys);
*/