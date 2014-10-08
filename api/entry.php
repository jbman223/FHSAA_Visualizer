<?php
require_once "require.php";
require_once "../USASwimming/getSwimmerUSATimes.php";

if (isset($_POST['f_name'], $_POST['l_name'])) {
    $state = $db->prepare("SELECT count(*) FROM swimmers WHERE f_name=? AND l_name=?");
    $state->execute(array($_POST['f_name'], $_POST['l_name']));
    $amount = $state->fetchAll()[0][0];
    if ($amount == 0) {
        if (isset($_POST['f_name'], $_POST['l_name'], $_POST['m_name'])) {
            $id = createNewSwimmer($_POST['f_name'], $_POST['l_name'], $_POST['m_name']);
            $bestTimes = getTimes($_POST['f_name'], $_POST['l_name']);
            foreach ($bestTimes as $value) {
                addSwimmerTime($id, $value["event"], $value["time"], $value["date"]);
            }
            addResult($_POST['place'], $_POST['grade'], $_POST['school'], $_POST['seed_time'], $_POST['seed_human'], $_POST['final_time'], $_POST['final_human'], $_POST['meet_title'], $_POST['event_name'], $_POST['normal_event_name'], $_POST['meet_type'], $_POST['finals_swim']=="F"?1:0, $id);
        } else {
            $id = createNewSwimmer($_POST['f_name'], $_POST['l_name']);
            $bestTimes = getTimes($_POST['f_name'], $_POST['l_name']);
            foreach ($bestTimes as $value) {
                addSwimmerTime($id, $value["event"], $value["time"], $value["date"]);
            }
            addResult($_POST['place'], $_POST['grade'], $_POST['school'], $_POST['seed_time'], $_POST['seed_human'], $_POST['final_time'], $_POST['final_human'], $_POST['meet_title'], $_POST['event_name'], $_POST['normal_event_name'], $_POST['meet_type'], $_POST['finals_swim']=="F"?1:0, $id);
        }
    } else {
        echo "old";
        $swimmer_id = getExistingSwimmerID($_POST['f_name'], $_POST['l_name']);
        addResult($_POST['place'], $_POST['grade'], $_POST['school'], $_POST['seed_time'], $_POST['seed_human'], $_POST['final_time'], $_POST['final_human'], $_POST['meet_title'], $_POST['event_name'], $_POST['normal_event_name'], $_POST['meet_type'], $_POST['finals_swim']=="F"?1:0, $swimmer_id);
    }
} else {
    echo "ga";
}