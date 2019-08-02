<?php
$datafile="/opt/internet-speed-log/SPEEDZ.csv";
// error_reporting(E_ALL);
// ini_set("display_errors", "1");

function performAction($params) {
    global $iot_db_con, $authentication_manager, $authorization_manager, $datafile;

    if (!isset($params["action"])) {
        return array("status" => 1, "message" => "Invalid request: Missing parameter 'action'");
    }

    $action = $params["action"];

    if ($action == null || $action == "") {
        return array("status" => 1, "message" => "Invalid request: Invalid parameter 'action'");
    }

    $action = strtolower($action);

    if ($action == "get_data_fmt") {
        if (!isset($_REQUEST["timeframe"])) {
            exit(json_encode(array("status" => 1, "message" => "Invalid request: Missing parameter 'timeframe'!")));
        }

        $timeframe = (int) $_REQUEST["timeframe"];

        if (($handle = fopen("$datafile", "r")) === false) {
            return array("status" => 1, "message" => "Error reading data file!");
        }

        $ret_data = array();

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            if ($data === null) {
                return array("status" => 1, "message" => "Error reading data file!");
            }

            $start_date = new DateTime($data[0]);

            $record_timestamp = $start_date->getTimestamp();
            $current_timestamp = time();
            if ($timeframe > 0 && $current_timestamp - $record_timestamp > $timeframe) { // Skip entries out of timeframe
                continue;
            }

            $end_date = new DateTime($data[1]);
            $date_diff = $start_date->diff($end_date);
            $date_diff_str = "";
            if ($date_diff->y > 0) $date_diff_str .= $date_diff->y . " years, ";
            if ($date_diff->m > 0) $date_diff_str .= $date_diff->m . " months, ";
            if ($date_diff->d > 0) $date_diff_str .= $date_diff->d . " days, ";
            if ($date_diff->h > 0) $date_diff_str .= str_pad($date_diff->h, 2, "0", STR_PAD_LEFT) . ":";
            $date_diff_str .= str_pad($date_diff->i, 2, "0", STR_PAD_LEFT) . ":" . str_pad($date_diff->s, 2, "0", STR_PAD_LEFT);

            $ret_data[] = array(
                "date" => $start_date->format("D F jS, Y g:i:s A T"),
                "duration" => $date_diff_str,
                "source_ip_addr" => $data[3],
                "test_host" => $data[4],
                "test_host_distance" => $data[5],
                "latency" => $data[6],
                "download" => $data[7],
                "upload" => $data[8]
            );
        }
        fclose($handle);

        return array("status" => 0, "data" => $ret_data);
//         exit(json_encode($authentication_manager->addAccount($username, $email, $password)));
    }else
    if ($action == "get_data") {
        if (!isset($_REQUEST["timeframe"])) {
            exit(json_encode(array("status" => 1, "message" => "Invalid request: Missing parameter 'timeframe'!")));
        }

        $timeframe = (int) $_REQUEST["timeframe"];

        if (($handle = fopen("$datafile", "r")) === false) {
            return array("status" => 1, "message" => "Error reading data file!");
        }

        $ret_data = array();

        while (($data = fgetcsv($handle, 1000, ";")) !== false) {
            if ($data === null) {
                return array("status" => 1, "message" => "Error reading data file!");
            }

            $start_date = new DateTime($data[0]);

            $record_timestamp = $start_date->getTimestamp();
            $current_timestamp = time();
            if ($timeframe > 0 && $current_timestamp - $record_timestamp > $timeframe) { // Skip entries out of timeframe
                continue;
            }

            $end_date = new DateTime($data[1]);
            $date_diff = $end_date->getTimestamp() - $record_timestamp;

            $ret_data[] = array(
                "timestamp" => $record_timestamp,
                "duration" => $date_diff,
                "source_ip_addr" => $data[3],
                "test_host" => $data[4],
                "test_host_distance" => (float) $data[5],
                "latency" => (float) $data[6],
                "download" => (float) $data[7],
                "upload" => (float) $data[8]
            );
        }
        fclose($handle);

        return array("status" => 0, "data" => $ret_data);
//         exit(json_encode($authentication_manager->addAccount($username, $email, $password)));
    }else{
        return array("status" => 1, "message" => "Invalid request: Invalid parameter 'action'");
    }
}

function format_debug_res($debug_mode, $str) {
    if ($debug_mode) {
        exit("<link href=\"https://cdn.rawgit.com/google/code-prettify/master/styles/doxy.css\" rel=\"stylesheet\"><style type=\"text/css\">body { margin: 0; padding: 0; background-color: #0f0f0f; } pre.prettyprint { width: auto; margin: 5px; border-radius: 0; border: none !important; }</style><script src=\"https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js\"></script><pre class=\"prettyprint\">Request:\n" . json_encode($_REQUEST, JSON_PRETTY_PRINT) . "\n\nResponse:\n$str</pre>");
    }
    exit($str);
}

$debug_mode = false;
if (isset($_REQUEST["debug"]) && strtolower(trim($_REQUEST["debug"])) == "true") {
    $debug_mode = true;
}

if (!isset($_REQUEST["action"])) {
    exit(format_debug_res($debug_mode, json_encode(array("status" => 1, "message" => "Invalid request: Missing parameter 'action'"), $debug_mode ? JSON_PRETTY_PRINT : 0)));
}

$action = $_REQUEST["action"];

if ($action == null || $action == "") {
    exit(format_debug_res($debug_mode, json_encode(array("status" => 1, "message" => "Invalid request: Invalid parameter 'action'"), $debug_mode ? JSON_PRETTY_PRINT : 0)));
}

$action = strtolower($action);

$is_multi_request = false;
$requests = null;

if ($action == "multi_request") {
    $is_multi_request = true;

    if (!isset($_REQUEST["requests"])) {
        exit(format_debug_res($debug_mode, json_encode(array("status" => 1, "message" => "Invalid request: Missing parameter 'requests'"), $debug_mode ? JSON_PRETTY_PRINT : 0)));
    }

    $requests = $_REQUEST["requests"];

    if ($requests == null || $requests == "") {
        exit(format_debug_res($debug_mode, json_encode(array("status" => 1, "message" => "Invalid request: Invalid parameter 'requests'"), $debug_mode ? JSON_PRETTY_PRINT : 0)));
    }

    $requests = json_decode($requests, true);
}

if ($is_multi_request) {
    $response = array();

    foreach ($requests as $request) {
        $response[$request["id"]] = performAction($request["params"]);
    }

    exit(format_debug_res($debug_mode, json_encode($response, $debug_mode ? JSON_PRETTY_PRINT : 0)));
}else{
    exit(format_debug_res($debug_mode, json_encode(performAction($_REQUEST), $debug_mode ? JSON_PRETTY_PRINT : 0)));
}
?>