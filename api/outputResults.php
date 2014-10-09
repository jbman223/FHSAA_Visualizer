<?php
require_once "require.php";

echo "<html><body><pre>";
$state = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND meet_title LIKE ?");
$state->execute(array("Event 8  Boys 50 Yard Freestyle", "States", "%1A%"));
$states = $state->fetchAll();
$regionalQuery = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND swimmer_id = ?");
$districtQuery = $db->prepare("SELECT `final_time`, `swimmer_id` FROM swim_information WHERE event_name = ? AND meet_type = ? AND swimmer_id = ?");
foreach ($states as $swim) {
    if ($swim[0] != 0) {
        $regionalQuery->execute(array("Event 8  Boys 50 Yard Freestyle", "Regionals", $swim["swimmer_id"]));
        $regions = $regionalQuery->fetch()["final_time"];
        $districtQuery->execute(array("Event 8  Boys 50 Yard Freestyle", "Districts", $swim["swimmer_id"]));
        $districts = $districtQuery->fetch()["final_time"];
        echo $swim["final_time"]."\t".$regions."\t".$districts."\n";
    }
}
echo "</pre></body></html>";