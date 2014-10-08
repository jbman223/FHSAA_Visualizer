<?php
require_once "require.php";
require_once "../USASwimming/getSwimmerUSATimes.php";

$swimmer_id = getExistingSwimmerID("liam", "hollowsky");
$state = $db->prepare("SELECT * FROM swim_information WHERE event_name = ?");
$state->execute(array(urldecode("Event+16++Boys+500+Yard+Freestyle")));
print_r($state->fetchAll(PDO::FETCH_ASSOC));

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