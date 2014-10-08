<?php
require_once "require.php";
header('Content-Type: application/json');
if (isset($_GET['f'], $_GET['l'])) {
    if (isset($_GET['e'])) {
        $swimmerID = getExistingSwimmerID(urldecode($_GET['f']), urldecode($_GET['l']));
        $swims = getSwimmerSwimsByEvent($swimmerID, urldecode($_GET['e']));
        $bestTimes = getSwimmerBestTimes($swimmerID);
        $swimmer = getSwimmer($swimmerID);
        print_r(json_encode(array("name" => $swimmer["f_name"]." ".$swimmer["l_name"], "swims" => $swims, "bestTimes" => $bestTimes)));
    } else {
        $swimmerID = getExistingSwimmerID(urldecode($_GET['f']), urldecode($_GET['l']));
        $swims = getSwimmerSwims($swimmerID);
        $bestTimes = getSwimmerBestTimes($swimmerID);
        $swimmer = getSwimmer($swimmerID);
        print_r(json_encode(array("name" => $swimmer["f_name"]." ".$swimmer["l_name"], "swims" => $swims, "bestTimes" => $bestTimes)));
    }
}