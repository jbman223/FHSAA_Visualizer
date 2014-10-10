<?php
header('Content-Type: application/json');

function endProgram($input)
{
    die($input);
}

function processInClause($inClause)
{
    if (preg_match("#girls|womens|female|women|lady|ladies#", $inClause)) {
        //girls event
        foreach (explode(" ", $inClause) as $input) {
            if (trim($input) == "50") {
                return "Event 7  Girls 50 Yard Freestyle";
            } else if (trim($input) == "100") {
                foreach (explode(" ", $inClause) as $input2) {
                    if (preg_match("#fr|free|freestyle#", $input2)) {
                        return "Event 13  Girls 100 Yard Freestyle";
                    } else if (preg_match("#fly|butterfly|fy#", $input2)) {
                        return "Event 11  Girls 100 Yard Butterfly";
                    } else if (preg_match("#br|breast|breaststroke#", $input2)) {
                        return "Event 21  Girls 100 Yard Breaststroke";
                    } else if (preg_match("#bk|back|backstroke#", $input2)) {
                        return "Event 19  Girls 100 Yard Backstroke";
                    }
                }
            } else if (trim($input) == "200") {
                foreach (explode(" ", $inClause) as $input2) {
                    if (preg_match("#fr|free|freestyle#", $input2)) {
                        return "Event 3  Girls 200 Yard Freestyle";
                    } else if (preg_match("#im|individual#", $input2)) {
                        return "Event 5  Girls 200 Yard IM";
                    }
                }
            } else if (trim($input) == "500") {
                return "Event 17  Girls 500 Yard Freestyle";
            }
        }
    } else {
        //boys event
        foreach (explode(" ", $inClause) as $input) {
            if (trim($input) == "50") {
                return "Event 8  Boys 50 Yard Freestyle";
            } else if (trim($input) == "100") {
                foreach (explode(" ", $inClause) as $input2) {
                    if (preg_match("#fr|free|freestyle#", $input2)) {
                        return "Event 14  Boys 100 Yard Freestyle";
                    } else if (preg_match("#fly|butterfly|fl#", $input2)) {
                        return "Event 12  Boys 100 Yard Butterfly";
                    } else if (preg_match("#br|breast|breaststroke#", $input2)) {
                        return "Event 22  Boys 100 Yard Breaststroke";
                    } else if (preg_match("#bk|back|backstroke#", $input2)) {
                        return "Event 20  Boys 100 Yard Backstroke";
                    }
                }
            } else if (trim($input) == "200") {
                foreach (explode(" ", $inClause) as $input2) {
                    if (preg_match("#fr|free|freestyle#", $input2)) {
                        return "Event 4  Boys 200 Yard Freestyle";
                    } else if (preg_match("#im|individual#", $input2)) {
                        return "Event 6  Boys 200 Yard IM";
                    }
                }
            } else if (trim($input) == "500") {
                return "Event 16  Boys 500 Yard Freestyle";
            }
        }
    }
}

function processQuery($query)
{
    $keyWords = array("compare", "results", "swimmer", "swimmers", "school", "schools", "predictions", "time", "and", "in");
    $split = explode(" ", strtolower($query));
    $indices = array();
    $layout = array("screens" => 1, "type" => "");
    $unparsedCommands = array();
    $errors = array();

    //parse indexes of key words.
    $count = 0;
    foreach ($split as $word) {
        if (in_array($word, $keyWords)) {
            array_push($indices, $count);
        }
        $count++;
    }

    if (count($indices) == 0) {
        array_push($errors, "Syntax error. No commands found. Please start your query with a command.");
        endProgram(json_encode(array("errors" => $errors)));
    }

    if ($split[$indices[0]] == "and") {
        array_push($errors, "Syntax error. Commands can't start with keyword 'and'");
        endProgram(json_encode(array("errors" => $errors)));
    }

    for ($i = 0; $i < count($indices); $i++) {
        if ($split[$indices[$i]] == "in" && $i != count($indices) - 1) {
            array_push($errors, "Syntax error. 'in' clauses must be the last command.");
            endProgram(json_encode(array("errors" => $errors)));
        }
    }

    if ($split[$indices[0]] == "compare") {
        if (count($indices) > 2) {
            //should be ands
            if ($split[$indices[1]] == "swimmers") {
                for ($i = 2; $i < count($indices); $i++) {
                    if ($split[$indices[$i]] != "and" && $split[$indices[$i]] != "in") {
                        array_push($errors, "Syntax error. Subsequent commands after 'swimmers' must be 'and' or 'in'.");
                        endProgram(json_encode(array("errors" => $errors)));
                    }

                    if ($split[$indices[$i]] == "and") {
                        $layout['screens']++;
                    }
                }

                $inClause = false;
                if ($split[$indices[count($indices) - 1]] == "in") {
                    $inClause = "";
                    for ($i = $indices[count($indices) - 1] + 1; $i < count($split); $i++) {
                        $inClause .= " " . $split[$i];
                    }
                    $inClause = trim($inClause);
                    $inClause = processInClause($inClause);
                }

                foreach ($indices as $commandIndex) {
                    if ($split[$commandIndex] != "compare" && $split[$commandIndex] != "in") {
                        if ($commandIndex + 1 < count($split) && $commandIndex + 2 < count($split)) {
                            if (!in_array($commandIndex + 3, $indices) && $commandIndex + 3 < count($split)) {
                                //presumable middle name
                                $first = urlencode($split[$commandIndex + 1]);
                                $middle = urlencode($split[$commandIndex + 2]);
                                $last = urlencode($split[$commandIndex + 3]);
                                echo("query db: output times in " . ($inClause ? $inClause : "all events") . " for $first $middle $last.<br>");
                                if ($inClause) {
                                    $inClause = urlencode($inClause);
                                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&m=$middle&l=$last&e=$inClause");
                                } else {
                                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&m=$middle&l=$last");
                                }
                            } else {
                                $first = urlencode($split[$commandIndex + 1]);
                                $last = urlencode($split[$commandIndex + 2]);
                                if ($inClause) {
                                    $inClause = urlencode($inClause);
                                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&l=$last&e=$inClause");
                                } else {
                                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&l=$last");
                                }
                            }
                        }
                    }
                }
                $layout['type'] = "swimmer";
            } else if ($split[$indices[1]] == "results" || $split[$indices[1]] == "predictions") {
                $command = htmlspecialchars($split[$indices[1]]);

                for ($i = 2; $i < count($indices); $i++) {
                    if ($split[$indices[$i]] != "and" && $split[$indices[$i]] != "in") {
                        array_push($errors, "Syntax error. Subsequent commands after '$command' must be 'and'.");
                        endProgram(json_encode(array("errors" => $errors)));
                    }

                    if ($split[$indices[$i]] == "and")
                        $layout["screens"]++;
                }

                $inClause = false;
                if ($split[$indices[count($indices) - 1]] == "in") {
                    $inClause = "";
                    for ($i = $indices[count($indices) - 1] + 1; $i < count($split); $i++) {
                        $inClause .= " " . $split[$i];
                    }
                    $inClause = trim($inClause);
                    $inClause = processInClause($inClause);
                }

                foreach ($indices as $commandIndex) {
                    if ($split[$commandIndex] != "compare" && $split[$commandIndex] != "in" && $commandIndex + 3 < count($split)) {
                        if ($split[$commandIndex + 1] == "class" && preg_match("#[1234]a#", $split[$commandIndex + 2]) && preg_match("#district|region|state|states#", $split[$commandIndex + 3])) {
                            if (preg_match("#district|region#", $split[$commandIndex + 3])) {
                                if ($commandIndex + 4 < count($split) && preg_match("#[0-9]+#", $split[$commandIndex + 4])) {
                                    if ($inClause) {
                                        $inClause = urlencode($inClause);
                                        array_push($unparsedCommands, "/api/$command.php?meet=" . urlencode($split[$commandIndex + 1] . " " . $split[$commandIndex + 2] . " " . $split[$commandIndex + 3] . " " . $split[$commandIndex + 4]) . "&e=$inClause");
                                    } else {
                                        array_push($unparsedCommands, "/api/$command.php?meet=" . urlencode($split[$commandIndex + 1] . " " . $split[$commandIndex + 2] . " " . $split[$commandIndex + 3] . " " . $split[$commandIndex + 4]) . "");
                                    }
                                }
                            } else if (preg_match("#state|states#", $split[$commandIndex + 3])) {
                                if ($inClause) {
                                    $inClause = urlencode($inClause);
                                    array_push($unparsedCommands, "/api/$command.php?meet=" . urlencode($split[$commandIndex + 1] . " " . $split[$commandIndex + 2] . " states") . "&e=$inClause");
                                } else {
                                    array_push($unparsedCommands, "/api/$command.php?meet=" . urlencode($split[$commandIndex + 1] . " " . $split[$commandIndex + 2] . " states") . "");
                                }
                            } else {
                                array_push($errors, "Syntax error. Please specify meet type. Ex: 'compare $command class 1a district 10 and class 4a district 4'");
                                endProgram(json_encode(array("errors" => $errors)));
                            }
                        } else {
                            array_push($errors, "Syntax error. Please specify the class. Ex: 'compare $command class 1a district 10 and class 4a district 4'");
                            endProgram(json_encode(array("errors" => $errors)));
                        }
                    }
                }

                $layout['type'] = "results";
            } else if ($split[$indices[1]] == "schools" || $split[$indices[1]] == "school") {
                for ($i = 2; $i < count($indices); $i++) {
                    if ($split[$indices[$i]] != "and" && $split[$indices[$i]] != "in") {
                        array_push($errors, "Syntax error. Subsequent commands after 'school' must be 'and'.");
                        endProgram(json_encode(array("errors" => $errors)));
                    }
                }

                $inClause = false;
                if ($split[$indices[count($indices) - 1]] == "in") {
                    $inClause = "";
                    for ($i = $indices[count($indices) - 1] + 1; $i < count($split); $i++) {
                        $inClause .= " " . $split[$i];
                    }
                    $inClause = trim($inClause);
                    $inClause = processInClause($inClause);
                }

                $i = 0;
                foreach ($indices as $commandIndex) {
                    if ($split[$commandIndex] != "compare" && $split[$commandIndex] != "in") {
                        $schoolName = "";
                        for ($startIndex = $indices[$i] + 1; $startIndex < count($split); $startIndex++) {
                            $schoolName .= " " . $split[$startIndex];
                        }
                        if ($inClause) {
                            $inClause = urlencode($inClause);
                            array_push($unparsedCommands, "/api/school.php?s=" . urlencode($schoolName) . "&e=$inClause");
                        } else {
                            array_push($unparsedCommands, "/api/school.php?s=" . urlencode($schoolName) . "");
                        }
                    }
                    $i++;
                }
                $layout['type'] = "school";
            } else {
                array_push($errors, "Syntax error. 'compare' must be followed by 'swimmers', 'results', or 'predictions'");
                endProgram(json_encode(array("errors" => $errors)));
            }
        } else {
            array_push($errors, "Syntax error. 'compare' must be followed at least one 'and', comparing two or more entities.");
            endProgram(json_encode(array("errors" => $errors)));
        }
    } else if ($split[$indices[0]] == "swimmer" || $split[$indices[0]] == "swimmers") {
        if (in_array("and", $split)) {
            array_push($errors, "Syntax error. Only 'compare' may be followed by 'and', comparing two or more entities.");
            endProgram(json_encode(array("errors" => $errors)));
        }

        $inClause = false;
        if ($split[$indices[count($indices) - 1]] == "in") {
            $inClause = "";
            for ($i = $indices[count($indices) - 1] + 1; $i < count($split); $i++) {
                $inClause .= " " . $split[$i];
            }
            $inClause = trim($inClause);
            $inClause = processInClause($inClause);
        }

        if ($indices[0] + 1 < count($split) && $indices[0] + 2 < count($split)) {
            if (!in_array($indices[0] + 3, $indices) && $indices[0] + 3 < count($split)) {
                //presumable middle name
                $first = urlencode($split[$indices[0] + 1]);
                $middle = urlencode($split[$indices[0] + 2]);
                $last = urlencode($split[$indices[0] + 3]);
                if ($inClause) {
                    $inClause = urlencode($inClause);
                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&m=$middle&l=$last&e=$inClause");
                } else {
                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&m=$middle&l=$last");
                }
            } else {
                $first = urlencode($split[$indices[0] + 1]);
                $last = urlencode($split[$indices[0] + 2]);
                if ($inClause) {
                    $inClause = urlencode($inClause);
                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&l=$last&e=$inClause");
                } else {
                    array_push($unparsedCommands, "/api/swimmer.php?f=$first&l=$last");
                }
            }
        }

        $layout['type'] = "swimmer";
    } else if ($split[$indices[0]] == "predictions" || $split[$indices[0]] == "results") {
        $command = htmlspecialchars($split[$indices[0]]);

        if (in_array("and", $split)) {
            array_push($errors, "Syntax error. Only 'compare' may be followed by 'and', comparing two or more entities.");
            endProgram(json_encode(array("errors" => $errors)));
        }

        $inClause = false;
        if ($split[$indices[count($indices) - 1]] == "in") {
            $inClause = "";
            for ($i = $indices[count($indices) - 1] + 1; $i < count($split); $i++) {
                $inClause .= " " . $split[$i];
            }
            $inClause = trim($inClause);
            $inClause = processInClause($inClause);
        }

        if ($split[$indices[0]] == "predictions" && count($indices) > 1 && $split[$indices[1]] == "time") {
            if ($indices[1] + 4 < count($split) && $inClause) {

            } else {
                array_push($errors, "Syntax error. When predicting a time, an in clause must be provided.");
                endProgram(json_encode(array("errors" => $errors)));
            }
        }

        if ($indices[0] + 3 < count($split) && $split[$indices[0] + 1] == "class" && preg_match("#[1234]a#", $split[$indices[0] + 2]) && preg_match("#district|region|state|states#", $split[$indices[0] + 3])) {
            if (preg_match("#district|region#", $split[$indices[0] + 3])) {
                if ($indices[0] + 4 < count($split) && preg_match("#[0-9]+#", $split[$indices[0] + 4])) {
                    if ($inClause) {
                        $inClause = urlencode($inClause);
                        array_push($unparsedCommands, "/api/$command.php?class=" . urlencode($split[$indices[0] + 2]) . "&type=" . urlencode($split[$indices[0] + 3]) . "&number=" . urlencode($split[$indices[0] + 4]) . "&e=$inClause");
                    } else {
                        array_push($unparsedCommands, "/api/$command.php?class=" . urlencode($split[$indices[0] + 2]) . "&type=" . urlencode($split[$indices[0] + 3]) . "&number=" . urlencode($split[$indices[0] + 4]) . "");
                    }
                }
            } else if (preg_match("#state|states#", $split[$indices[0] + 3])) {
                if ($inClause) {
                    $inClause = urlencode($inClause);
                    array_push($unparsedCommands, "/api/$command.php?class=" . urlencode($split[$indices[0] + 2]) . "&type=States" . "&e=$inClause");
                } else {
                    array_push($unparsedCommands, "/api/$command.php?class=" . urlencode($split[$indices[0] + 2]) . "&type=States" . "");
                }
            } else {
                array_push($errors, "Syntax error. Please specify meet type. Ex: 'compare $command class 1a district 10'");
                endProgram(json_encode(array("errors" => $errors)));
            }
        } else {
            array_push($errors, "Syntax error. Please specify the class. Ex: 'compare $command class 1a district 10'");
            endProgram(json_encode(array("errors" => $errors)));
        }
        $layout['type'] = "results";
    } else if ($split[$indices[0]] == "school") {
        if (in_array("and", $split)) {
            array_push($errors, "Syntax error. Only 'compare' may be followed by 'and', comparing two or more entities.");
            endProgram(json_encode(array("errors" => $errors)));
        }

        $inClause = false;
        if ($split[$indices[count($indices) - 1]] == "in") {
            $inClause = "";
            for ($i = $indices[count($indices) - 1] + 1; $i < count($split); $i++) {
                $inClause .= " " . $split[$i];
            }
            $inClause = trim($inClause);
            $inClause = processInClause($inClause);
        }

        $schoolName = "";
        for ($startIndex = $indices[0]; $startIndex < (count($indices) > 1) ? $indices[2] : count($split); $startIndex++) {
            $schoolName .= $split[$startIndex];
        }

        if ($inClause) {
            $inClause = urlencode($inClause);
            array_push($unparsedCommands, "/api/school.php?s=" . urlencode($schoolName) . "&e=$inClause");
        } else {
            array_push($unparsedCommands, "/api/school.php?s=" . urlencode($schoolName) . "");
        }
    } else {
        array_push($errors, "Syntax error. No command found. Please start each operation with a command.");
        endProgram(json_encode(array("errors" => $errors)));
    }

    $finalOutput = array("screens" => array(), "type" => $layout['type']);
    for ($i = 0; $i < $layout['screens']; $i++) {
        $finalOutput['screens'][$i] = $unparsedCommands[$i];
    }
    endProgram(json_encode($finalOutput));
}

//query
$query = $_POST['query'];
processQuery($query);
