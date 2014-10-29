<?php
require_once "require.php";
require_once "../USASwimming/getSwimmerUSATimes.php";
//require_once "../processQuery.php";

$swims = array();

echo "<pre>";

$db->query("SET @rank = 0;");
$a = $db->prepare("SELECT @rank:=@rank+1 AS rank, `swimmers`.`f_name`, `swimmers`.`l_name`, swimmer_id, `final_time` FROM `swim_information` INNER JOIN `swimmers` ON `swimmers`.`id` = `swim_information`.`swimmer_id` WHERE `meet_type` = ? AND year = ? AND `final_time` != 0 AND `event_name` = ? AND meet_title LIKE ? ORDER BY `final_time` ASC LIMIT 0, 24;");
$a->execute(array("Districts", "2014", "Event 16  Boys 500 Yard Freestyle", "%3A%"));
print_r(json_encode($a->fetchAll(PDO::FETCH_ASSOC)));

//for ($i = 1; $i <= 12; $i++) {
//    $swims[$i] = array();
//    $state = $db->prepare("SELECT final_time FROM swim_information WHERE event_name = ? AND meet_title = ? AND final_time != 0 ORDER BY final_time ASC LIMIT 0, 8 ");
//    $state->execute(array(urldecode("Event+16++Boys+500+Yard+Freestyle"), "FHSAA 1A District $i Championship"));
//    $cSwims = $state->fetchAll(PDO::FETCH_ASSOC);
//    for ($a = 0; $a < count($cSwims); $a++) {
//        array_push($swims[$i], $cSwims[$a]['final_time']);
//    }
//    //echo "District $i <br>";
//}
//
//
//for ($i = 1; $i <= count($swims); $i++) {
//    print_r($swims[$i]);
//    if (count($swims[$i]) != 0) {
//        $average = array_sum($swims[$i])/count($swims[$i]);
//        echo "Average time for District $i is ".$average."<br>";
//    } else {
//        echo "District $i contains an error in original import<br>";
//    }
//}

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