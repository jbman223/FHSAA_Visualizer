<?php
//session_start();
//establish database connections
require_once "db.php";

function createNewSwimmer ($firstName, $lastName, $middleName = "") {
    global $db;
    $state = $db->prepare("INSERT INTO swimmers (f_name, m_name, l_name) VALUES (?, ?, ?)");
    $state->execute(array($firstName, $middleName, $lastName));
    return $db->lastInsertId();
}

function getResultsForMeet($meetName) {
    global $db;
    $state = $db->prepare("SELECT * FROM swim_information WHERE meet_title = ?");
    $state->execute(array($meetName));
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function getResultsForMeetByEvent($meetName, $event) {
    global $db;
    $state = $db->prepare("SELECT * FROM swim_information WHERE meet_title = ? AND event_name = ?");
    $state->execute(array($meetName, $event));
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function getSwimmerSwims($swimmerID) {
    global $db;
    $state = $db->prepare("SELECT * FROM swim_information WHERE swimmer_id = ?");
    $state->execute(array($swimmerID));
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function getSwimmerSwimsByEvent($swimmerID, $event) {
    global $db;
    $state = $db->prepare("SELECT * FROM swim_information WHERE swimmer_id = ? AND event_name = ?");
    $state->execute(array($swimmerID, $event));
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function getSwimmerBestTimes($swimmerID) {
    global $db;
    $state = $db->prepare("SELECT * FROM usa_swimming_times WHERE swimmer_id = ?");
    $state->execute(array($swimmerID));
    return $state->fetchAll(PDO::FETCH_ASSOC);
}

function getSwimmer($swimmerID) {
    global $db;
    $state = $db->prepare("SELECT * FROM swimmers WHERE id = ?");
    $state->execute(array($swimmerID));
    return $state->fetchAll(PDO::FETCH_ASSOC)[0];
}

function getExistingSwimmerID($firstName, $lastName) {
    global $db;
    $state = $db->prepare("SELECT * FROM swimmers WHERE f_name= ? AND l_name = ?");
    $state->execute(array($firstName, $lastName));
    return $state->fetchAll()[0]["id"];
}

function addSwimmerTime ($id, $event, $time, $date) {
    global $db;
    $state = $db->prepare("INSERT INTO usa_swimming_times (`event`, `time`, `human_time`, `date`, `swimmer_id`) VALUES (?, ?, ?, ?, ?)");
    $state->execute(array($event, timeToDouble($time), $time, $date, $id));
}

function addResult($place, $grade, $school, $seed_time, $seed_human, $final_time, $final_human, $meet_title, $event_name, $normal_event_name, $meet_type, $finals_swim, $swimmer_id, $year) {
    global $db;
    $state = $db->prepare("INSERT INTO `fhsaa`.`swim_information` (`place`, `grade`, `school`, `seed_time`, `seed_human`, `final_time`, `final_human`, `meet_title`, `event_name`, `normal_event_name`, `meet_type`, `finals_swim`, `swimmer_id`, `year`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $state->execute(array($place, $grade, $school, $seed_time, $seed_human, $final_time, $final_human, $meet_title, $event_name, $normal_event_name, $meet_type, $finals_swim, $swimmer_id, $year));
}

function timeToDouble($time) {
    if (strstr($time, ":")) {
        $split = explode(":", $time);
        if (count($split) == 2) {
            $mins = $split[0];
            $split2 = explode(".", $split[1]);
            if (count($split2) == 2) {
                return ($mins*60) + $split2[0] + ($split2[1]/100.0);
            }
        }
    } else {
        $split = explode(".", $time);
        if (count($split) == 2) {
            return $split[0] + ($split[1]/100.0);
        }

    }
}