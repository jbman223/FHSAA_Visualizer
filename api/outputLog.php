<?php
require_once "require.php";
$events = array(
    "Event 11  Girls 100 Yard Butterfly",
    "Event 12  Boys 100 Yard Butterfly",
    "Event 13  Girls 100 Yard Freestyle",
    "Event 14  Boys 100 Yard Freestyle",
    "Event 16  Boys 500 Yard Freestyle",
    "Event 17  Girls 500 Yard Freestyle",
    "Event 19  Girls 100 Yard Backstroke",
    "Event 20  Boys 100 Yard Backstroke",
    "Event 21  Girls 100 Yard Breaststroke",
    "Event 22  Boys 100 Yard Breaststroke",
    "Event 3  Girls 200 Yard Freestyle",
    "Event 4  Boys 200 Yard Freestyle",
    "Event 5  Girls 200 Yard IM",
    "Event 6  Boys 200 Yard IM",
    "Event 7  Girls 50 Yard Freestyle",
    "Event 8  Boys 50 Yard Freestyle"
);
$state = $db->prepare("SELECT * FROM log_predictions WHERE event = ? ORDER BY time ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Buckheit.com - Awesome FHSAA Event Visualization</title>
    <link href='http://fonts.googleapis.com/css?family=Fjalla+One' rel='stylesheet' type='text/css'>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <style>
        #main {
            background: rgba(255, 255, 255, 0) center;
            border-radius: 5px;
            text-align: center;
            width: 800px;
            margin: 0 auto;
        }

        #title {
            color: black;
            font-family: "Fjalla One", "Helvetica Neue Light", "HelveticaNeue-Light", "Helvetica Neue", Calibri, Helvetica, Arial, sans-serif;
            text-shadow: 0 0 5px #000;
            font-size: 52px;
        }

        #description {
            background: rgba(255, 255, 255, 0) center;
            border-radius: 5px;
            width: 600px;
            font-size: 16pt;
            text-align: justify;
            color: black;
            font-family: "Fjalla One", "Helvetica Neue Light", "HelveticaNeue-Light", "Helvetica Neue", Calibri, Helvetica, Arial, sans-serif;
            text-shadow: 0 0 2px #000;
            margin: 0 auto;
        }

        #mainInput {
            width: 800px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            border: none;
            border-radius: 5px;
            color: black;
            font-family: "Fjalla One", "Helvetica Neue Light", "HelveticaNeue-Light", "Helvetica Neue", Calibri, Helvetica, Arial, sans-serif;
        }

        .coolFont {
            font-family: "Fjalla One", "Helvetica Neue Light", "HelveticaNeue-Light", "Helvetica Neue", Calibri, Helvetica, Arial, sans-serif;
        }

        .whiteColor {
            color: white;
            text-shadow: 0 0 2px #000;
        }

        th {
            text-align: center;
        }

        .progress-bar {
            -webkit-transition: width 12s ease-in-out;
            transition: width 12s ease-in-out;
        }
    </style>
</head>
<body>
<div id="main">
<?
foreach ($events as $event) {
?>
<h1 id="title">Buckheit.com</h1>
<b><? echo $event; ?></b>
<table class="table">
    <?
    $state->execute(array($event));
    $a = $state->fetchAll(PDO::FETCH_ASSOC);
    foreach ($a as $swim) {
        echo "<tr><td>{$swim['name']}</td><td class=\"convertTime\">{$swim['time']}</td></tr>";
    }
    ?>
</table>
<? } ?>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        $(".convertTime").each(function() {
            $(this).text(doubleTimeToStringTime($(this).text()));
        })
    });
    function doubleTimeToStringTime(time) {
        var minutes = Math.floor(Math.floor(time) / 60);
        var seconds = Math.floor(time)-minutes*60;
        var micro = Math.floor(((time)-minutes*60-seconds)*100);
        return (pad(minutes, 2)+":"+pad(seconds, 2)+"."+pad(micro, 2));
    }
    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }
</script>
</body>
</html>