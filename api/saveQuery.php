<?php
require_once "require.php";

if (isset($_GET['query'])) {
    $state = $db->prepare("INSERT INTO queries (query) VALUES (?)");
    $state->execute($_GET['query']);
}