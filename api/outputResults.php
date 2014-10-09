<?php
require_once "require.php";

echo "<html><body><pre>";
$state = $db->prepare("SELECT `final_time` FROM swim_information WHERE event_name = ? AND meet_type = ? AND meet_name LIKE ?");
$state->execute(array("Event 8  Boys 50 Yard Freestyle", "States", "%1A%"));
foreach ($state->fetchAll() as $swim) {
    if ($swim[0] != 0)
        echo $swim[0]."\n";
}
echo "</pre></body></html>";