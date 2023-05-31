<?php
include "cgi-bin/config.php";

if (filter_input(INPUT_GET, 'm', FILTER_SANITIZE_NUMBER_INT)) {
    $myId = filter_input(INPUT_GET, 'm', FILTER_SANITIZE_NUMBER_INT);
}

if (filter_input(INPUT_POST, 'send', FILTER_SANITIZE_STRING)) {
    $sendId = filter_input(INPUT_POST, 'send', FILTER_SANITIZE_STRING);
    $myId = filter_input(INPUT_POST, 'myId', FILTER_SANITIZE_NUMBER_INT);
    $open = filter_input(INPUT_POST, 'open', FILTER_SANITIZE_NUMBER_INT);
    $planned = filter_input(INPUT_POST, 'planned', FILTER_SANITIZE_NUMBER_INT);
    $location = filter_var(htmlEntities(trim($_POST['location']), ENT_QUOTES), FILTER_SANITIZE_STRING);
    $affectedServices = filter_var(htmlEntities(trim($_POST['affectedServices']), ENT_QUOTES), FILTER_SANITIZE_STRING);
    $owner = filter_var(htmlEntities(trim($_POST['owner']), ENT_QUOTES), FILTER_SANITIZE_STRING);
    $numberCustomersAffected = filter_var(htmlEntities(trim($_POST['numberCustomersAffected']), ENT_QUOTES), FILTER_SANITIZE_STRING);
    $showStartTime = (filter_input(INPUT_POST, 'showStartTime', FILTER_SANITIZE_NUMBER_INT) == 1) ? "1" : "0";
    $startTimeHour = filter_input(INPUT_POST, 'startTimeHour', FILTER_SANITIZE_NUMBER_INT);
    $startTimeMin = filter_input(INPUT_POST, 'startTimeMin', FILTER_SANITIZE_NUMBER_INT);
    $startTimeDay = filter_input(INPUT_POST, 'startTimeDay', FILTER_SANITIZE_NUMBER_INT);
    $startTimeMonth = filter_input(INPUT_POST, 'startTimeMonth', FILTER_SANITIZE_NUMBER_INT);
    $startTimeYear = filter_input(INPUT_POST, 'startTimeYear', FILTER_SANITIZE_NUMBER_INT);
    $showTimeOfDetection = (filter_input(INPUT_POST, 'showTimeOfDetection', FILTER_SANITIZE_NUMBER_INT) == 1) ? "1" : "0";
    $timeOfDetectionHour = filter_input(INPUT_POST, 'timeOfDetectionHour', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDetectionMin = filter_input(INPUT_POST, 'timeOfDetectionMin', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDetectionDay = filter_input(INPUT_POST, 'timeOfDetectionDay', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDetectionMonth = filter_input(INPUT_POST, 'timeOfDetectionMonth', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDetectionYear = filter_input(INPUT_POST, 'timeOfDetectionYear', FILTER_SANITIZE_NUMBER_INT);
    $showTimeOfDispatch = (filter_input(INPUT_POST, 'showTimeOfDispatch', FILTER_SANITIZE_NUMBER_INT) == 1) ? "1" : "0";
    $timeOfDispatchHour = filter_input(INPUT_POST, 'timeOfDispatchHour', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDispatchMin = filter_input(INPUT_POST, 'timeOfDispatchMin', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDispatchDay = filter_input(INPUT_POST, 'timeOfDispatchDay', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDispatchMonth = filter_input(INPUT_POST, 'timeOfDispatchMonth', FILTER_SANITIZE_NUMBER_INT);
    $timeOfDispatchYear = filter_input(INPUT_POST, 'timeOfDispatchYear', FILTER_SANITIZE_NUMBER_INT);
    $showTimeOfResponse = (filter_input(INPUT_POST, 'showTimeOfResponse', FILTER_SANITIZE_NUMBER_INT) == 1) ? "1" : "0";
    $timeOfResponseHour = filter_input(INPUT_POST, 'timeOfResponseHour', FILTER_SANITIZE_NUMBER_INT);
    $timeOfResponseMin = filter_input(INPUT_POST, 'timeOfResponseMin', FILTER_SANITIZE_NUMBER_INT);
    $timeOfResponseDay = filter_input(INPUT_POST, 'timeOfResponseDay', FILTER_SANITIZE_NUMBER_INT);
    $timeOfResponseMonth = filter_input(INPUT_POST, 'timeOfResponseMonth', FILTER_SANITIZE_NUMBER_INT);
    $timeOfResponseYear = filter_input(INPUT_POST, 'timeOfResponseYear', FILTER_SANITIZE_NUMBER_INT);
    $showTimeOfArrival = (filter_input(INPUT_POST, 'showTimeOfArrival', FILTER_SANITIZE_NUMBER_INT) == 1) ? "1" : "0";
    $timeOfArrivalHour = filter_input(INPUT_POST, 'timeOfArrivalHour', FILTER_SANITIZE_NUMBER_INT);
    $timeOfArrivalMin = filter_input(INPUT_POST, 'timeOfArrivalMin', FILTER_SANITIZE_NUMBER_INT);
    $timeOfArrivalDay = filter_input(INPUT_POST, 'timeOfArrivalDay', FILTER_SANITIZE_NUMBER_INT);
    $timeOfArrivalMonth = filter_input(INPUT_POST, 'timeOfArrivalMonth', FILTER_SANITIZE_NUMBER_INT);
    $timeOfArrivalYear = filter_input(INPUT_POST, 'timeOfArrivalYear', FILTER_SANITIZE_NUMBER_INT);
    $showEndTime = (filter_input(INPUT_POST, 'showEndTime', FILTER_SANITIZE_NUMBER_INT) == 1) ? "1" : "0";
    $endTimeHour = filter_input(INPUT_POST, 'endTimeHour', FILTER_SANITIZE_NUMBER_INT);
    $endTimeMin = filter_input(INPUT_POST, 'endTimeMin', FILTER_SANITIZE_NUMBER_INT);
    $endTimeDay = filter_input(INPUT_POST, 'endTimeDay', FILTER_SANITIZE_NUMBER_INT);
    $endTimeMonth = filter_input(INPUT_POST, 'endTimeMonth', FILTER_SANITIZE_NUMBER_INT);
    $endTimeYear = filter_input(INPUT_POST, 'endTimeYear', FILTER_SANITIZE_NUMBER_INT);
    $cause = filter_var(htmlEntities(trim($_POST['cause']), ENT_QUOTES), FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlEntities(trim($_POST['notes']), ENT_QUOTES), FILTER_SANITIZE_STRING);
    $startTime = mktime($startTimeHour, $startTimeMin, 0, $startTimeMonth, $startTimeDay, $startTimeYear);
    $timeOfDetection = mktime($timeOfDetectionHour, $timeOfDetectionMin, 0, $timeOfDetectionMonth, $timeOfDetectionDay, $timeOfDetectionYear);
    $timeOfDispatch = mktime($timeOfDispatchHour, $timeOfDispatchMin, 0, $timeOfDispatchMonth, $timeOfDispatchDay, $timeOfDispatchYear);
    $timeOfResponse = mktime($timeOfResponseHour, $timeOfResponseMin, 0, $timeOfResponseMonth, $timeOfResponseDay, $timeOfResponseYear);
    $timeOfArrival = mktime($timeOfArrivalHour, $timeOfArrivalMin, 0, $timeOfArrivalMonth, $timeOfArrivalDay, $timeOfArrivalYear);
    $endTime = mktime($endTimeHour, $endTimeMin, 0, $endTimeMonth, $endTimeDay, $endTimeYear);

    if ($sendId == 'new') {
        $n = $db->prepare("INSERT INTO outages VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'0','0')");
        $n->execute(array($myId, $time, $open, $planned, $location, $affectedServices, $owner, $numberCustomersAffected, $showStartTime, $startTime, $showTimeOfDetection, $timeOfDetection, $showTimeOfDispatch, $timeOfDispatch, $showTimeOfResponse, $timeOfResponse, $showTimeOfArrival, $timeOfArrival, $showEndTime, $endTime, $cause, $notes));
        $n2 = $db->prepare("SELECT id FROM outages WHERE user = ? && time = ? ORDER BY id DESC LIMIT 1");
        $n2->execute(array($myId, $time));
        $n2Row = $n2->fetch();
        $sendId = $n2Row['id'];
    } else {
        $u = $db->prepare("UPDATE outages SET open = ?, planned = ?,location = ?, affectedServices = ?, owner = ?, numberCustomersAffected = ?, showStartTime = ?, startTime = ?, showTimeOfDetection = ?, timeOfDetection = ?, showTimeOfDispatch = ?, timeOfDispatch = ?, showTimeOfResponse = ?, timeOfResponse = ?, showTimeOfArrival = ?, timeOfArrival = ?, showEndTime = ?, endTime = ?, cause = ?, notes = ? WHERE id = ?");
        $u->execute(array($open, $planned, $location, $affectedServices, $owner, $numberCustomersAffected, $showStartTime, $startTime, $showTimeOfDetection, $timeOfDetection, $showTimeOfDispatch, $timeOfDispatch, $showTimeOfResponse, $timeOfResponse, $showTimeOfArrival, $timeOfArrival, $showEndTime, $endTime, $cause, $notes, $sendId));
    }

    $m = $db->prepare("SELECT * FROM users WHERE id = ?");
    $m->execute(array($myId));
    $mRow = $m->fetch();
    $email = $mRow['email'];
    $emailPwd = $mRow['emailPwd'];

    $p = ($planned == '1') ? "PLANNED" : "UNPLANNED";
    switch ($open) {
        case 0:
            $o = "RESOLVED";
            break;
        case 1:
            $o = "UPDATE";
            break;
        case 2:
            $o = "";
            break;
    }
    $mess = "<html><body><table><tr><td style='cellpadding:10px; font-weight:bold;'>Location</td><td style='cellpadding:10px;'>" . $location . "</td></tr>"
            . "<tr><td style='cellpadding:10px; font-weight:bold;'>Affected Services</td><td style='cellpadding:10px;'>" . wordwrap(html_entity_decode($affectedServices, ENT_QUOTES), 60) . "</td></tr>"
            . "<tr><td style='cellpadding:10px; font-weight:bold;'>Owner</td><td style='cellpadding:10px;'>$owner</td></tr>"
            . "<tr><td style='cellpadding:10px; font-weight:bold;'># of customers affected</td><td style='cellpadding:10px;'>" . wordwrap($numberCustomersAffected, 60) . "</td></tr>";
    $mess .= ($showStartTime == '1') ? "<tr><td style='cellpadding:10px; font-weight:bold;'>Start time</td><td style='cellpadding:10px;'>" . date("m/d/Y . H:i", $startTime) . "</td></tr>" : "";
    $mess .= ($showTimeOfDetection == '1') ? "<tr><td style='cellpadding:10px; font-weight:bold;'>Time of detection</td><td style='cellpadding:10px;'>" . date("m/d/Y . H:i", $timeOfDetection) . "</td></tr>" : "";
    $mess .= ($showTimeOfDispatch == '1') ? "<tr><td style='cellpadding:10px; font-weight:bold;'>Time of dispatch</td><td style='cellpadding:10px;'>" . date("m/d/Y . H:i", $timeOfDispatch) . "</td></tr>" : "";
    $mess .= ($showTimeOfResponse == '1') ? "<tr><td style='cellpadding:10px; font-weight:bold;'>Time of response</td><td style='cellpadding:10px;'>" . date("m/d/Y . H:i", $timeOfResponse) . "</td></tr>" : "";
    $mess .= ($showTimeOfArrival == '1') ? "<tr><td style='cellpadding:10px; font-weight:bold;'>Time of arrival</td><td style='cellpadding:10px;'>" . date("m/d/Y . H:i", $timeOfArrival) . "</td></tr>" : "";
    $mess .= ($showEndTime == '1') ? "<tr><td style='cellpadding:10px; font-weight:bold;'>End time</td><td style='cellpadding:10px;'>" . date("m/d/Y . H:i", $endTime) . "</td></tr>" : "";
    $mess .= "<tr><td style='cellpadding:10px; font-weight:bold;'>Cause</td><td style='cellpadding:10px;'>" . wordwrap(html_entity_decode($cause, ENT_QUOTES), 60) . "</td></tr>"
            . "<tr><td style='cellpadding:10px; font-weight:bold;'>Notes</td><td style='cellpadding:10px;'>" . wordwrap(html_entity_decode($notes, ENT_QUOTES), 60) . "</td></tr></body></html>";

    
function sparkpost($method, $uri, $payload = [], $headers = [])
{
    $defaultHeaders = [ 'Content-Type: application/json' ];

    $curl = curl_init();
    $method = strtoupper($method);

    $finalHeaders = array_merge($defaultHeaders, $headers);

    $url = 'https://api.sparkpost.com:443/api/v1/'.$uri;

    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    if ($method !== 'GET') {
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    curl_setopt($curl, CURLOPT_HTTPHEADER, $finalHeaders);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}

$payload = [
    'options' => [
        'sandbox' => false,
    ],
    'content' => [
        'from' => [
            'email' => $email,
        ],
        'subject' => "$p $location $o",
        'html' => $mess,
    ],
    'recipients' => [
        [ 'address' => 'jpadgett@goeaglecom.net', ],
    ],
];

$headers = [ 'Authorization: 3395d406e271fe282ce93213f20ebbe2c33a35b9' ];

echo "Sending email...\n";
$email_results = sparkpost('POST', 'transmissions', $payload, $headers);

echo "Email results:\n"; 
echo json_encode(json_decode($email_results, false), JSON_PRETTY_PRINT);
echo "\n\n";

echo "Listing sending domains...\n";
$sending_domains_results = sparkpost('GET', 'sending-domains', [], $headers);

echo "Sending domain results:\n"; 
echo json_encode(json_decode($sending_domains_results, false), JSON_PRETTY_PRINT);
echo "\n";
}
?>

<!DOCTYPE html>
<html>
    <head>
        <?php
        include "head.php";
        ?>
    </head>
    <body>
        <div class="main" style="margin:40px;">
            <div style="margin-bottom:40px; text-align:center; font-weight:bold; font-size:1.5em;">Outage Form</div>
            <?php
            if ($myId != '0') {
                $a = $db->prepare("SELECT * FROM outages WHERE open != ?");
                $a->execute(array('0'));
                while ($aRow = $a->fetch()) {
                    foreach ($aRow as $k => $v) {
                        ${$k} = $v;
                    }
                    $location = html_entity_decode($location, ENT_QUOTES);
                    $affectedServices = html_entity_decode($affectedServices, ENT_QUOTES);
                    $cause = html_entity_decode($cause, ENT_QUOTES);
                    $notes = html_entity_decode($notes, ENT_QUOTES);
                    ?>
                    <div style="padding:10px; font-weight:bold; font-size:1.25em; cursor: pointer;"  onclick="toggleview('outage<?php echo $id; ?>')"><?php echo ($planned == '1') ? "PLANNED" : "UNPLANNED"; ?> <?php echo $location . "&nbsp;&nbsp;&nbsp;&nbsp;" . date("m/d/Y . H:i", $startTime); ?></div>
                    <form action="index.php" method="post">
                        <table id="outage<?php echo $id; ?>" style="display:none; margin:10px 20px;" cellpadding="5px" cellspacing="0">
                            <tr>
                                <td>Status of outage (used in email subject):</td>
                                <td><input type="radio" name="open" value="2"> Open ... <input type="radio" name="open" value="1" checked> Update ... <input type="radio" name="open" value="0"> Closed</td>
                            </tr>
                            <tr>
                                <td>Planned / Unplanned (used in email subject):</td>
                                <td><input type="radio" name="planned" value="1"<?php echo ($planned == '1') ? " checked" : ""; ?>> Planned ... <input type="radio" name="planned" value="0"<?php echo ($planned == '0') ? " checked" : ""; ?>> Unplanned</td>
                            </tr>
                            <tr>
                                <td>Location (used in email subject):</td>
                                <td><input type="text" name="location" value="<?php echo $location; ?>"></td>
                            </tr>
                            <tr>
                                <td>Affected Services:</td>
                                <td><textarea name="affectedServices" rows="5" cols="40"><?php echo $affectedServices; ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Owner:</td>
                                <td><input type="text" name="owner" value="<?php echo $owner; ?>"></td>
                            </tr>
                            <tr>
                                <td>Number of customers affected:</td>
                                <td><input type="text" name="numberCustomersAffected" value="<?php echo $numberCustomersAffected; ?>"></td>
                            </tr>
                            <tr>
                                <td>Start Time:</td>
                                <td>
                                    <input type="checkbox" name="showStartTime" value="1"<?php echo ($showStartTime == '1') ? " checked" : ""; ?>> Show start time.<br>
                                    H:<select name="startTimeHour" size="1">
                                        <?php
                                        $stHour = ($showStartTime == '1') ? date("H", $startTime) : date("H");
                                        for ($i = 00; $i <= 23; $i++) {
                                            echo "<option value='$i'";
                                            echo ($i == $stHour) ? " selected" : "";
                                            echo ">$i</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="startTimeMin" size="1">
                                        <?php
                                        $stMin = ($showStartTime == '1') ? date("i", $startTime) : date("i");
                                        for ($j = 00; $j <= 59; $j++) {
                                            echo "<option value='$j'";
                                            echo ($j == $stMin) ? " selected" : "";
                                            echo ">$j</option>\n";
                                        }
                                        ?>
                                    </select> D:<select name="startTimeDay" size="1">
                                        <?php
                                        $stDay = ($showStartTime == '1') ? date("d", $startTime) : date("d");
                                        for ($k = 01; $k <= 31; $k++) {
                                            echo "<option value='$k'";
                                            echo ($k == $stDay) ? " selected" : "";
                                            echo ">$k</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="startTimeMonth" size="1">
                                        <?php
                                        $stMonth = ($showStartTime == '1') ? date("m", $startTime) : date("m");
                                        for ($l = 01; $l <= 12; $l++) {
                                            echo "<option value='$l'";
                                            echo ($l == $stMonth) ? " selected" : "";
                                            echo ">$l</option>\n";
                                        }
                                        ?>
                                    </select> Y:<select name="startTimeYear" size="1">
                                        <?php
                                        $stYear = ($showStartTime == '1') ? date("Y", $startTime) : date("Y");
                                        for ($m = 2019; $m <= 2030; $m++) {
                                            echo "<option value='$m'";
                                            echo ($m == $stYear) ? " selected" : "";
                                            echo ">$m</option>\n";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Time of Detection:</td>
                                <td>
                                    <input type="checkbox" name="showTimeOfDetection" value="1"<?php echo ($showTimeOfDetection == '1') ? " checked" : ""; ?>> Show time of detection.<br>
                                    H:<select name="timeOfDetectionHour" size="1">
                                        <?php
                                        $todetHour = ($showTimeOfDetection == '1') ? date("H", $timeOfDetection) : date("H");
                                        for ($i = 00; $i <= 23; $i++) {
                                            echo "<option value='$i'";
                                            echo ($i == $todetHour) ? " selected" : "";
                                            echo ">$i</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfDetectionMin" size="1">
                                        <?php
                                        $todetMin = ($showTimeOfDetection == '1') ? date("i", $timeOfDetection) : date("i");
                                        for ($j = 00; $j <= 59; $j++) {
                                            echo "<option value='$j'";
                                            echo ($j == $todetMin) ? " selected" : "";
                                            echo ">$j</option>\n";
                                        }
                                        ?>
                                    </select> D:<select name="timeOfDetectionDay" size="1">
                                        <?php
                                        $todetDay = ($showTimeOfDetection == '1') ? date("d", $timeOfDetection) : date("d");
                                        for ($k = 01; $k <= 31; $k++) {
                                            echo "<option value='$k'";
                                            echo ($k == $todetDay) ? " selected" : "";
                                            echo ">$k</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfDetectionMonth" size="1">
                                        <?php
                                        $todetMonth = ($showTimeOfDetection == '1') ? date("m", $timeOfDetection) : date("m");
                                        for ($l = 01; $l <= 12; $l++) {
                                            echo "<option value='$l'";
                                            echo ($l == $todetMonth) ? " selected" : "";
                                            echo ">$l</option>\n";
                                        }
                                        ?>
                                    </select> Y:<select name="timeOfDetectionYear" size="1">
                                        <?php
                                        $todetYear = ($showTimeOfDetection == '1') ? date("Y", $timeOfDetection) : date("Y");
                                        for ($m = 2019; $m <= 2030; $m++) {
                                            echo "<option value='$m'";
                                            echo ($m == $todetYear) ? " selected" : "";
                                            echo ">$m</option>\n";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Time of Dispatch:</td>
                                <td>
                                    <input type="checkbox" name="showTimeOfDispatch" value="1"<?php echo ($showTimeOfDispatch == '1') ? " checked" : ""; ?>> Show time of dispatch.<br>
                                    H:<select name="timeOfDispatchHour" size="1">
                                        <?php
                                        $todisHour = ($showTimeOfDispatch == '1') ? date("H", $timeOfDispatch) : date("H");
                                        for ($i = 00; $i <= 23; $i++) {
                                            echo "<option value='$i'";
                                            echo ($i == $todisHour) ? " selected" : "";
                                            echo ">$i</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfDispatchMin" size="1">
                                        <?php
                                        $todisMin = ($showTimeOfDispatch == '1') ? date("i", $timeOfDispatch) : date("i");
                                        for ($j = 00; $j <= 59; $j++) {
                                            echo "<option value='$j'";
                                            echo ($j == $todisMin) ? " selected" : "";
                                            echo ">$j</option>\n";
                                        }
                                        ?>
                                    </select> D:<select name="timeOfDispatchDay" size="1">
                                        <?php
                                        $todisDay = ($showTimeOfDispatch == '1') ? date("d", $timeOfDispatch) : date("d");
                                        for ($k = 01; $k <= 31; $k++) {
                                            echo "<option value='$k'";
                                            echo ($k == $todisDay) ? " selected" : "";
                                            echo ">$k</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfDispatchMonth" size="1">
                                        <?php
                                        $todisMonth = ($showTimeOfDispatch == '1') ? date("m", $timeOfDispatch) : date("m");
                                        for ($l = 01; $l <= 12; $l++) {
                                            echo "<option value='$l'";
                                            echo ($l == $todisMonth) ? " selected" : "";
                                            echo ">$l</option>\n";
                                        }
                                        ?>
                                    </select> Y:<select name="timeOfDispatchYear" size="1">
                                        <?php
                                        $todisYear = ($showTimeOfDispatch == '1') ? date("Y", $timeOfDispatch) : date("Y");
                                        for ($m = 2019; $m <= 2030; $m++) {
                                            echo "<option value='$m'";
                                            echo ($m == $todisYear) ? " selected" : "";
                                            echo ">$m</option>\n";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Time of Response:</td>
                                <td>
                                    <input type="checkbox" name="showTimeOfResponse" value="1"<?php echo ($showTimeOfResponse == '1') ? " checked" : ""; ?>> Show time of response.<br>
                                    H:<select name="timeOfResponseHour" size="1">
                                        <?php
                                        $torHour = ($showTimeOfResponse == '1') ? date("H", $timeOfResponse) : date("H");
                                        for ($i = 00; $i <= 23; $i++) {
                                            echo "<option value='$i'";
                                            echo ($i == $torHour) ? " selected" : "";
                                            echo ">$i</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfResponseMin" size="1">
                                        <?php
                                        $torMin = ($showTimeOfResponse == '1') ? date("i", $timeOfResponse) : date("i");
                                        for ($j = 00; $j <= 59; $j++) {
                                            echo "<option value='$j'";
                                            echo ($j == $torMin) ? " selected" : "";
                                            echo ">$j</option>\n";
                                        }
                                        ?>
                                    </select> D:<select name="timeOfResponseDay" size="1">
                                        <?php
                                        $torDay = ($showTimeOfResponse == '1') ? date("d", $timeOfResponse) : date("d");
                                        for ($k = 01; $k <= 31; $k++) {
                                            echo "<option value='$k'";
                                            echo ($k == $torDay) ? " selected" : "";
                                            echo ">$k</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfResponseMonth" size="1">
                                        <?php
                                        $torMonth = ($showTimeOfResponse == '1') ? date("m", $timeOfResponse) : date("m");
                                        for ($l = 01; $l <= 12; $l++) {
                                            echo "<option value='$l'";
                                            echo ($l == $torMonth) ? " selected" : "";
                                            echo ">$l</option>\n";
                                        }
                                        ?>
                                    </select> Y:<select name="timeOfResponseYear" size="1">
                                        <?php
                                        $torYear = ($showTimeOfResponse == '1') ? date("Y", $timeOfResponse) : date("Y");
                                        for ($m = 2019; $m <= 2030; $m++) {
                                            echo "<option value='$m'";
                                            echo ($m == $torYear) ? " selected" : "";
                                            echo ">$m</option>\n";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Time of Arrival:</td>
                                <td>
                                    <input type="checkbox" name="showTimeOfArrival" value="1"<?php echo ($showTimeOfArrival == '1') ? " checked" : ""; ?>> Show time of arrival.<br>
                                    H:<select name="timeOfArrivalHour" size="1">
                                        <?php
                                        $toaHour = ($showTimeOfArrival == '1') ? date("H", $timeOfArrival) : date("H");
                                        for ($i = 00; $i <= 23; $i++) {
                                            echo "<option value='$i'";
                                            echo ($i == $toaHour) ? " selected" : "";
                                            echo ">$i</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfArrivalMin" size="1">
                                        <?php
                                        $toaMin = ($showTimeOfArrival == '1') ? date("i", $timeOfArrival) : date("i");
                                        for ($j = 00; $j <= 59; $j++) {
                                            echo "<option value='$j'";
                                            echo ($j == $toaMin) ? " selected" : "";
                                            echo ">$j</option>\n";
                                        }
                                        ?>
                                    </select> D:<select name="timeOfArrivalDay" size="1">
                                        <?php
                                        $toaDay = ($showTimeOfArrival == '1') ? date("d", $timeOfArrival) : date("d");
                                        for ($k = 01; $k <= 31; $k++) {
                                            echo "<option value='$k'";
                                            echo ($k == $toaDay) ? " selected" : "";
                                            echo ">$k</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="timeOfArrivalMonth" size="1">
                                        <?php
                                        $toaMonth = ($showTimeOfArrival == '1') ? date("m", $timeOfArrival) : date("m");
                                        for ($l = 01; $l <= 12; $l++) {
                                            echo "<option value='$l'";
                                            echo ($l == $toaMonth) ? " selected" : "";
                                            echo ">$l</option>\n";
                                        }
                                        ?>
                                    </select> Y:<select name="timeOfArrivalYear" size="1">
                                        <?php
                                        $toaYear = ($showTimeOfArrival == '1') ? date("Y", $timeOfArrival) : date("Y");
                                        for ($m = 2019; $m <= 2030; $m++) {
                                            echo "<option value='$m'";
                                            echo ($m == $toaYear) ? " selected" : "";
                                            echo ">$m</option>\n";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>End Time:</td>
                                <td>
                                    <input type="checkbox" name="showEndTime" value="1"<?php echo ($showEndTime == '1') ? " checked" : ""; ?>> Show end time.<br>
                                    H:<select name="endTimeHour" size="1">
                                        <?php
                                        $eHour = ($showEndTime == '1') ? date("H", $endTime) : date("H");
                                        for ($i = 00; $i <= 23; $i++) {
                                            echo "<option value='$i'";
                                            echo ($i == $eHour) ? " selected" : "";
                                            echo ">$i</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="endTimeMin" size="1">
                                        <?php
                                        $eMin = ($showEndTime == '1') ? date("i", $endTime) : date("i");
                                        for ($j = 00; $j <= 59; $j++) {
                                            echo "<option value='$j'";
                                            echo ($j == $eMin) ? " selected" : "";
                                            echo ">$j</option>\n";
                                        }
                                        ?>
                                    </select> D:<select name="endTimeDay" size="1">
                                        <?php
                                        $eDay = ($showEndTime == '1') ? date("d", $endTime) : date("d");
                                        for ($k = 01; $k <= 31; $k++) {
                                            echo "<option value='$k'";
                                            echo ($k == $eDay) ? " selected" : "";
                                            echo ">$k</option>\n";
                                        }
                                        ?>
                                    </select> M:<select name="endTimeMonth" size="1">
                                        <?php
                                        $eMonth = ($showEndTime == '1') ? date("m", $endTime) : date("m");
                                        for ($l = 01; $l <= 12; $l++) {
                                            echo "<option value='$l'";
                                            echo ($l == $eMonth) ? " selected" : "";
                                            echo ">$l</option>\n";
                                        }
                                        ?>
                                    </select> Y:<select name="endTimeYear" size="1">
                                        <?php
                                        $eYear = ($showEndTime == '1') ? date("Y", $endTime) : date("Y");
                                        for ($m = 2019; $m <= 2030; $m++) {
                                            echo "<option value='$m'";
                                            echo ($m == $eYear) ? " selected" : "";
                                            echo ">$m</option>\n";
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Cause:</td>
                                <td><textarea name="cause" rows="5" cols="40"><?php echo $cause; ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Updates / Notes:</td>
                                <td><textarea name="notes" rows="5" cols="40"><?php echo $notes; ?></textarea></td>
                            </tr>
                            <tr>
                                <td>Update outage</td>
                                <td><input type="hidden" name="send" value="<?php echo $id; ?>"><input type="hidden" name="myId" value="<?php echo $myId; ?>"><input type="submit" value=" Update "></td>
                            </tr>
                        </table>
                    </form>

                    <?php
                }
                ?>
                <div style="padding:10px; font-weight:bold; font-size:1.25em; cursor: pointer;"  onclick="toggleview('outageNew')">New</div>
                <form action="index.php" method="post">
                    <table id="outageNew" style="display:none; margin:10px 20px;" cellpadding="5px" cellspacing="0">
                        <tr>
                            <td>Status of outage (used in email subject):</td>
                            <td><input type="radio" name="open" value="2" checked> Open ... <input type="radio" name="open" value="1"> Update ... <input type="radio" name="open" value="0"> Closed</td>
                        </tr>
                        <tr>
                            <td>Planned / Unplanned (used in email subject):</td>
                            <td><input type="radio" name="planned" value="1" checked> Planned ... <input type="radio" name="planned" value="0"> Unplanned</td>
                        </tr>
                        <tr>
                            <td>Location (used in email subject):</td>
                            <td><input type="text" name="location" value=""></td>
                        </tr>
                        <tr>
                            <td>Affected Services:</td>
                            <td><textarea name="affectedServices" rows="5" cols="40"></textarea></td>
                        </tr>
                        <tr>
                            <td>Owner:</td>
                            <td><input type="text" name="owner" value=""></td>
                        </tr>
                        <tr>
                            <td>Number of customers affected:</td>
                            <td><input type="text" name="numberCustomersAffected" value=""></td>
                        </tr>
                        <tr>
                            <td>Start Time:</td>
                            <td>
                                <input type="checkbox" name="showStartTime" value="1"> Show start time.<br>
                                H:<select name="startTimeHour" size="1">
                                    <?php
                                    $stHour = date("H");
                                    for ($i = 00; $i <= 23; $i++) {
                                        echo "<option value='$i'";
                                        echo ($i == $stHour) ? " selected" : "";
                                        echo ">$i</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="startTimeMin" size="1">
                                    <?php
                                    $stMin = date("i");
                                    for ($j = 00; $j <= 59; $j++) {
                                        echo "<option value='$j'";
                                        echo ($j == $stMin) ? " selected" : "";
                                        echo ">$j</option>\n";
                                    }
                                    ?>
                                </select> D:<select name="startTimeDay" size="1">
                                    <?php
                                    $stDay = date("d");
                                    for ($k = 01; $k <= 31; $k++) {
                                        echo "<option value='$k'";
                                        echo ($k == $stDay) ? " selected" : "";
                                        echo ">$k</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="startTimeMonth" size="1">
                                    <?php
                                    $stMonth = date("m");
                                    for ($l = 01; $l <= 12; $l++) {
                                        echo "<option value='$l'";
                                        echo ($l == $stMonth) ? " selected" : "";
                                        echo ">$l</option>\n";
                                    }
                                    ?>
                                </select> Y:<select name="startTimeYear" size="1">
                                    <?php
                                    $stYear = date("Y");
                                    for ($m = 2019; $m <= 2030; $m++) {
                                        echo "<option value='$m'";
                                        echo ($m == $stYear) ? " selected" : "";
                                        echo ">$m</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Time of Detection:</td>
                            <td>
                                <input type="checkbox" name="showTimeOfDetection" value="1"> Show time of detection.<br>
                                H:<select name="timeOfDetectionHour" size="1">
                                    <?php
                                    $todetHour = date("H");
                                    for ($i = 00; $i <= 23; $i++) {
                                        echo "<option value='$i'";
                                        echo ($i == $todetHour) ? " selected" : "";
                                        echo ">$i</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfDetectionMin" size="1">
                                    <?php
                                    $todetMin = date("i");
                                    for ($j = 00; $j <= 59; $j++) {
                                        echo "<option value='$j'";
                                        echo ($j == $todetMin) ? " selected" : "";
                                        echo ">$j</option>\n";
                                    }
                                    ?>
                                </select> D:<select name="timeOfDetectionDay" size="1">
                                    <?php
                                    $todetDay = date("d");
                                    for ($k = 01; $k <= 31; $k++) {
                                        echo "<option value='$k'";
                                        echo ($k == $todetDay) ? " selected" : "";
                                        echo ">$k</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfDetectionMonth" size="1">
                                    <?php
                                    $todetMonth = date("m");
                                    for ($l = 01; $l <= 12; $l++) {
                                        echo "<option value='$l'";
                                        echo ($l == $todetMonth) ? " selected" : "";
                                        echo ">$l</option>\n";
                                    }
                                    ?>
                                </select> Y:<select name="timeOfDetectionYear" size="1">
                                    <?php
                                    $todetYear = date("Y");
                                    for ($m = 2019; $m <= 2030; $m++) {
                                        echo "<option value='$m'";
                                        echo ($m == $todetYear) ? " selected" : "";
                                        echo ">$m</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Time of Dispatch:</td>
                            <td>
                                <input type="checkbox" name="showTimeOfDispatch" value="1"> Show time of dispatch.<br>
                                H:<select name="timeOfDispatchHour" size="1">
                                    <?php
                                    $todisHour = date("H");
                                    for ($i = 00; $i <= 23; $i++) {
                                        echo "<option value='$i'";
                                        echo ($i == $todisHour) ? " selected" : "";
                                        echo ">$i</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfDispatchMin" size="1">
                                    <?php
                                    $todisMin = date("i");
                                    for ($j = 00; $j <= 59; $j++) {
                                        echo "<option value='$j'";
                                        echo ($j == $todisMin) ? " selected" : "";
                                        echo ">$j</option>\n";
                                    }
                                    ?>
                                </select> D:<select name="timeOfDispatchDay" size="1">
                                    <?php
                                    $todisDay = date("d");
                                    for ($k = 01; $k <= 31; $k++) {
                                        echo "<option value='$k'";
                                        echo ($k == $todisDay) ? " selected" : "";
                                        echo ">$k</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfDispatchMonth" size="1">
                                    <?php
                                    $todisMonth = date("m");
                                    for ($l = 01; $l <= 12; $l++) {
                                        echo "<option value='$l'";
                                        echo ($l == $todisMonth) ? " selected" : "";
                                        echo ">$l</option>\n";
                                    }
                                    ?>
                                </select> Y:<select name="timeOfDispatchYear" size="1">
                                    <?php
                                    $todisYear = date("Y");
                                    for ($m = 2019; $m <= 2030; $m++) {
                                        echo "<option value='$m'";
                                        echo ($m == $todisYear) ? " selected" : "";
                                        echo ">$m</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Time of Response:</td>
                            <td>
                                <input type="checkbox" name="showTimeOfResponse" value="1"> Show time of response.<br>
                                H:<select name="timeOfResponseHour" size="1">
                                    <?php
                                    $torHour = date("H");
                                    for ($i = 00; $i <= 23; $i++) {
                                        echo "<option value='$i'";
                                        echo ($i == $torHour) ? " selected" : "";
                                        echo ">$i</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfResponseMin" size="1">
                                    <?php
                                    $torMin = date("i");
                                    for ($j = 00; $j <= 59; $j++) {
                                        echo "<option value='$j'";
                                        echo ($j == $torMin) ? " selected" : "";
                                        echo ">$j</option>\n";
                                    }
                                    ?>
                                </select> D:<select name="timeOfResponseDay" size="1">
                                    <?php
                                    $torDay = date("d");
                                    for ($k = 01; $k <= 31; $k++) {
                                        echo "<option value='$k'";
                                        echo ($k == $torDay) ? " selected" : "";
                                        echo ">$k</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfResponseMonth" size="1">
                                    <?php
                                    $torMonth = date("m");
                                    for ($l = 01; $l <= 12; $l++) {
                                        echo "<option value='$l'";
                                        echo ($l == $torMonth) ? " selected" : "";
                                        echo ">$l</option>\n";
                                    }
                                    ?>
                                </select> Y:<select name="timeOfResponseYear" size="1">
                                    <?php
                                    $torYear = date("Y");
                                    for ($m = 2019; $m <= 2030; $m++) {
                                        echo "<option value='$m'";
                                        echo ($m == $torYear) ? " selected" : "";
                                        echo ">$m</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Time of Arrival:</td>
                            <td>
                                <input type="checkbox" name="showTimeOfArrival" value="1"> Show time of arrival.<br>
                                H:<select name="timeOfArrivalHour" size="1">
                                    <?php
                                    $toaHour = date("H");
                                    for ($i = 00; $i <= 23; $i++) {
                                        echo "<option value='$i'";
                                        echo ($i == $toaHour) ? " selected" : "";
                                        echo ">$i</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfArrivalMin" size="1">
                                    <?php
                                    $toaMin = date("i");
                                    for ($j = 00; $j <= 59; $j++) {
                                        echo "<option value='$j'";
                                        echo ($j == $toaMin) ? " selected" : "";
                                        echo ">$j</option>\n";
                                    }
                                    ?>
                                </select> D:<select name="timeOfArrivalDay" size="1">
                                    <?php
                                    $toaDay = date("d");
                                    for ($k = 01; $k <= 31; $k++) {
                                        echo "<option value='$k'";
                                        echo ($k == $toaDay) ? " selected" : "";
                                        echo ">$k</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="timeOfArrivalMonth" size="1">
                                    <?php
                                    $toaMonth = date("m");
                                    for ($l = 01; $l <= 12; $l++) {
                                        echo "<option value='$l'";
                                        echo ($l == $toaMonth) ? " selected" : "";
                                        echo ">$l</option>\n";
                                    }
                                    ?>
                                </select> Y:<select name="timeOfArrivalYear" size="1">
                                    <?php
                                    $toaYear = date("Y");
                                    for ($m = 2019; $m <= 2030; $m++) {
                                        echo "<option value='$m'";
                                        echo ($m == $toaYear) ? " selected" : "";
                                        echo ">$m</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>End Time:</td>
                            <td>
                                <input type="checkbox" name="showEndTime" value="1"> Show end time.<br>
                                H:<select name="endTimeHour" size="1">
                                    <?php
                                    $eHour = date("H");
                                    for ($i = 00; $i <= 23; $i++) {
                                        echo "<option value='$i'";
                                        echo ($i == $eHour) ? " selected" : "";
                                        echo ">$i</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="endTimeMin" size="1">
                                    <?php
                                    $eMin = date("i");
                                    for ($j = 00; $j <= 59; $j++) {
                                        echo "<option value='$j'";
                                        echo ($j == $eMin) ? " selected" : "";
                                        echo ">$j</option>\n";
                                    }
                                    ?>
                                </select> D:<select name="endTimeDay" size="1">
                                    <?php
                                    $eDay = date("d");
                                    for ($k = 01; $k <= 31; $k++) {
                                        echo "<option value='$k'";
                                        echo ($k == $eDay) ? " selected" : "";
                                        echo ">$k</option>\n";
                                    }
                                    ?>
                                </select> M:<select name="endTimeMonth" size="1">
                                    <?php
                                    $eMonth = date("m");
                                    for ($l = 01; $l <= 12; $l++) {
                                        echo "<option value='$l'";
                                        echo ($l == $eMonth) ? " selected" : "";
                                        echo ">$l</option>\n";
                                    }
                                    ?>
                                </select> Y:<select name="endTimeYear" size="1">
                                    <?php
                                    $eYear = date("Y");
                                    for ($m = 2019; $m <= 2030; $m++) {
                                        echo "<option value='$m'";
                                        echo ($m == $eYear) ? " selected" : "";
                                        echo ">$m</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Cause:</td>
                            <td><textarea name="cause" rows="5" cols="40"></textarea></td>
                        </tr>
                        <tr>
                            <td>Updates / Notes:</td>
                            <td><textarea name="notes" rows="5" cols="40"></textarea></td>
                        </tr>
                        <tr>
                            <td>Update outage</td>
                            <td><input type="hidden" name="send" value="new"><input type="hidden" name="myId" value="<?php echo $myId; ?>"><input type="submit" value=" Update "></td>
                        </tr>
                    </table>
                </form>
                <?php
                $c = $db->prepare("SELECT COUNT(*) FROM outages WHERE open = ?");
                $c->execute(array('0'));
                $cRow = $c->fetch();
                if ($cRow[0] >= 1) {
                    echo "<div style='padding:10px;'><a href='archive.php?m=$myId' target='_self' style='font-weight:bold; font-size:1.25em; color: #000000;'>Archive</a></div>";
                }
            }
            if ($myId == '0') {
                ?>
                <div style='text-align: center; font-size: 1.5em; color: #cc4541; margin-bottom: 30px;'>Log in.</div>
                <div style="width: 210px; margin:auto;">
                    <?php echo ($loginErr != "x") ? $loginErr : ""; ?>
                    <form method="post" action="index.php">
                        Email Address:<br><input type="text" name="email" value="" size="30"><br><br>
                        Password: <input type="password" name="pwd" value="" size="30"><br><br>
                        <input type="hidden" name="login" value="1">
                        <input type="submit" value=" Log In ">
                    </form><br><br>
                </div>
                <?php
            }
            ?>
        </div>
    </body>
</html>