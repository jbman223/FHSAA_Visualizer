<?php
require_once "require.php";
function outputForm () {
    ?>
    <html>
    <body>
    <form action="test.php" method="GET">
        <input type="text" name="eventName"/>
        <input type="submit"/>
    </form>
    </body>
    </html>
<?
}

//function sksort(&$array, $subkey="date", $sort_ascending=false) {
//
//    if (count($array))
//        $temp_array[key($array)] = array_shift($array);
//
//    foreach($array as $key => $val){
//        $offset = 0;
//        $found = false;
//        foreach($temp_array as $tmp_key => $tmp_val)
//        {
//            if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
//            {
//                $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
//                    array($key => $val),
//                    array_slice($temp_array,$offset)
//                );
//                $found = true;
//            }
//            $offset++;
//        }
//        if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
//    }
//
//    if ($sort_ascending) $array = array_reverse($temp_array);
//
//    else $array = $temp_array;
//}
//
//function updateThings(&$array) {
//    for ($i = 0; $i < count($array); $i++) {
//        $array[$i]["index"] = $i;
//    }
//}

function outputChart($data) {
    ?>
    <head>
        <script src="http://d3js.org/d3.v3.min.js"></script>
        <script src="http://dimplejs.org/dist/dimple.v2.0.0.min.js"></script>
    </head>
    <body>
    <script type="text/javascript">
        var svg = dimple.newSvg("body", 1200, 600);
        var data = <? echo $data; ?>;
        var chart = new dimple.chart(svg, data);
        chart.addCategoryAxis("y", "time");
        chart.addTimeAxis("x", "date", null);
        chart.addSeries(null, dimple.plot.line);
        chart.draw();
    </script>
    </body>
<?
}

if (isset($_GET['eventName'])) {
    echo "<pre>";
    $headers = array(
        "User-Agent: Appcelerator Titanium/3.1.3.GA (iPhone/8.0; iPhone OS; en_US;)",
        "content-type: application/json"
    );
    $events = json_decode('[{"eventName":"50 FR SCY","eventID":1},{"eventName":"100 FR SCY","eventID":2},{"eventName":"200 FR SCY","eventID":3},{"eventName":"500 FR SCY","eventID":4},{"eventName":"1000 FR SCY","eventID":5},{"eventName":"1650 FR SCY","eventID":6},{"eventName":"50 BK SCY","eventID":11},{"eventName":"100 BK SCY","eventID":12},{"eventName":"200 BK SCY","eventID":13},{"eventName":"50 BR SCY","eventID":14},{"eventName":"100 BR SCY","eventID":15},{"eventName":"200 BR SCY","eventID":16},{"eventName":"50 FL SCY","eventID":17},{"eventName":"100 FL SCY","eventID":18},{"eventName":"200 FL SCY","eventID":19},{"eventName":"200 IM SCY","eventID":21},{"eventName":"400 IM SCY","eventID":22},{"eventName":"50 FR LCM","eventID":55},{"eventName":"100 FR LCM","eventID":56},{"eventName":"200 FR LCM","eventID":57},{"eventName":"400 FR LCM","eventID":58},{"eventName":"800 FR LCM","eventID":59},{"eventName":"1500 FR LCM","eventID":60},{"eventName":"100 BK LCM","eventID":66},{"eventName":"200 BK LCM","eventID":67},{"eventName":"50 BR LCM","eventID":68},{"eventName":"100 BR LCM","eventID":69},{"eventName":"200 BR LCM","eventID":70},{"eventName":"50 FL LCM","eventID":71},{"eventName":"100 FL LCM","eventID":72},{"eventName":"200 IM LCM","eventID":75},{"eventName":"400 IM LCM","eventID":76}]');
    //print_r($events);
    $eventID = -1;
    for ($i = 0; $i < count($events); $i++) {
        if ($events[$i]->eventName == $_GET['eventName']) {
            $eventID = $events[$i]->eventID;
        }
    }

    if ($eventID == -1) {
        die("event not found");
    }

    $people = json_decode(postJSON("https://mobile.usaswimming.org/usaswcfm/Service.svc/SearchAthletes", "{\"FirstName\":\"liam\",\"LastName\":\"hollowsky\"}", $headers));

    $timesArray = array();

    if (isset($people->error)) {
        die($people->error);
    } else {
        //var_dump($people->Members[0]);
        for ($i = 1; $i < 2; $i++) {
            $requestJson = "{\"PersonId\":\"" . $people->Members[0]->PersonId . "\",\"CompetitionPeriodId\":-1,\"EventId\":-1,\"FastestTimesOnlyBool\":true,\"BatchNumber\":$i,\"DeviceId\":\"\"}";
            $stuff = postJSON("https://mobile.usaswimming.org/usaswcfm/Service.svc/GetIndividualTimes", $requestJson, $headers);
            $stuffJSON = json_decode($stuff);
            if ($i == 1)
                //print_r($stuffJSON);
            if (isset($stuffJSON->Error) || !isset($stuffJSON->IndividualTimes)) {
                echo "Whoops! Maybe we are done.";
                break;
            } else {
                if (count($stuffJSON->IndividualTimes) > 0) {
                    for ($num = 0; $num < count($stuffJSON->IndividualTimes); $num++) {
                        $current = $stuffJSON->IndividualTimes[$num];
                        array_push($timesArray, array("event" => $current->Event, "time" => $current->SwimTime, "date" => strtotime($current->Date)*1000));
                    }
                } else {
                    echo "Whoops! Maybe we are done.";
//                    sksort($timesArray, "date", true);
//                    updateThings($timesArray);
                    print_r($timesArray);
                    outputChart(json_encode($timesArray));
                    die();
                }
            }
        }
    }

//    sksort($timesArray, "date", true);
//    updateThings($timesArray);
    print_r($timesArray);
    echo "</pre>";
    //outputChart(json_encode($timesArray));
    die();
} else {
    outputForm();
}
?>
