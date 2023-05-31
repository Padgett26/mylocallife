<?php
include "cgi-bin/config.php";

if (filter_input(INPUT_GET, 'm', FILTER_SANITIZE_NUMBER_INT)) {
    $myId = filter_input(INPUT_GET, 'm', FILTER_SANITIZE_NUMBER_INT);
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
            <div style="margin-bottom:40px; text-align:center; font-weight:bold; font-size:1.5em;">Archive Outages</div>
            <?php
            if ($myId != '0') {
                $a = $db->prepare("SELECT * FROM outages WHERE open = ?");
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
                                <td><input type="radio" name="open" value="2"> Open ... <input type="radio" name="open" value="1"> Update ... <input type="radio" name="open" value="0" checked> Closed</td>
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
                                    H:<select name="startTimeHour" size="1">
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
                    echo "<div style='padding:10px;'><a href='index.php?m=$myId' target='_self' style='font-weight:bold; font-size:1.25em; color: #000000;'>Main page</a></div>";
            }
            if ($myId == '0') {
                ?>
                <div style='text-align: center; font-size: 1.5em; color: #cc4541; margin-bottom: 30px;'>Log in.</div>
                <div style="width: 210px; margin:auto;">
                    <?php echo ($loginErr != "x") ? $loginErr : ""; ?>
                    <form method="post" action="archive.php">
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