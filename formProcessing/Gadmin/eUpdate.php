<?php

$eId = filter_input(INPUT_POST, 'eUpdate', FILTER_SANITIZE_NUMBER_INT);
$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
$writeUp = filter_input(INPUT_POST, 'writeUp', FILTER_SANITIZE_STRING);
$startHour = filter_input(INPUT_POST, 'startHour', FILTER_SANITIZE_NUMBER_INT);
$startMinute = filter_input(INPUT_POST, 'startMinute', FILTER_SANITIZE_NUMBER_INT);
$startMonth = filter_input(INPUT_POST, 'startMonth', FILTER_SANITIZE_NUMBER_INT);
$startDay = filter_input(INPUT_POST, 'startDay', FILTER_SANITIZE_NUMBER_INT);
$startYear = filter_input(INPUT_POST, 'startYear', FILTER_SANITIZE_NUMBER_INT);
$appr = (filter_input(INPUT_POST, 'appr', FILTER_SANITIZE_NUMBER_INT) == '0') ? '0' : '1';

if ($appr == '0') {
    $s = $db->prepare("DELETE FROM calendar WHERE id=?");
    $s->execute(array($eId));
} else {
    $startTime = mktime($startHour, $startMinute, 00, $startMonth, $startDay, $startYear);
    $s = $db->prepare("UPDATE calendar SET startTime=?, title=?, writeUp=?, approved='1' WHERE id=?");
    $s->execute(array($startTime, $title, $writeUp, $eId));
}