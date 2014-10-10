<?php
require_once "require.php";
require_once "statisticalFunctions.php";

echo "<html><body><pre>";
$eventName = "Event 16  Boys 500 Yard Freestyle";
//response variable
$startingMeet = "States";
//explanatory variable
$secondMeet = "Districts";
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
        $second = $secondaryQuery->fetchAll();
        if (count($second) > 0) {
            array_push($x, $second[0]["final_time"]);
            array_push($y, $swim["final_time"]);
            echo $second[0]["final_time"] . "\t" . $swim["final_time"] . "\n";
        }
    }
}
echo "\n\n\n";
$a = linear_regression($x, $y);
var_dump($a);
echo "y = {$a['a']}x + {$a['b']}";
echo "</pre></body></html>";