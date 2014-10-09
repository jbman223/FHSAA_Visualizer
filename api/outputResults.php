<?php
require_once "require.php";

echo "<html><body><pre>";
$state = $db->prepare("SELECT `final_time` FROM swim_information WHERE event_name = ? AND meet_type = ?");
$state->execute(array("Event 8  Boys 50 Yard Freestyle", "Districts"));
foreach ($state->fetchAll() as $swim) {
    if ($swim[0] != 0)
        echo $swim[0]."\n";
}
echo "</pre></body></html>";
