<?php
require_once "require.php";
require_once "statisticalFunctions.php";

echo "<html><body><pre>";
$eventName = "Event 16  Boys 500 Yard Freestyle";
//resp var
$startingMeet = "States";
//exp var
$secondMeet = "Regions";
$class = "%1A%";
$x = array();
$y = array();
$state = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND meet_title LIKE ?");
$state->execute(array($eventName, $startingMeet, $class));
$states = $state->fetchAll();
$secondaryQuery = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND swimmer_id = ?");
foreach ($states as $swim) {
    if ($swim[0] != 0) {
        $secondaryQuery->execute(array($eventName, $secondMeet, $swim["swimmer_id"]));
        $secondary = $secondaryQuery->fetch();
        if (count($secondary) == 1) {
            array_push($x, $secondary["final_time"]);
            array_push($y, $swim["final_time"]);
            echo $secondary["final_time"] . "\t" . $swim["final_time"] . "\n";
        }
    }
}
echo "\n\n\n";
$a = linear_regression($x, $y);
var_dump($a);
echo "y = {$a['a']}x + {$a['b']}";
echo "</pre></body></html>";