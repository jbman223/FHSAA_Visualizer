<?php
require_once "require.php";

if (isset($_GET['query'])) {
    $state = $db->prepare("INSERT INTO `queries` (`query`, `id`, `time`) VALUES (?, null, null)");
    $state->execute(array($_GET['query']));
} else {
}