<?php
require_once("require.php");
$state = $db->prepare("SELECT id FROM swimmers");
$state->execute();
