<?php

foreach ($_POST as $k => $v) {
    if (preg_match("/^seen([1-9][0-9]*)$/", $k, $match)) {
        $val = (filter_var($v, FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";
        $fId = $match[1];
        $fd = $db->prepare("UPDATE feedback SET seen=? WHERE id=?");
        $fd->execute(array($val, $fId));
    }
    if (preg_match("/^del([1-9][0-9]*)$/", $k, $match)) {
        $val = (filter_var($v, FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";
        $fId = $match[1];
        if ($val == '1') {
            $fd = $db->prepare("DELETE FROM feedback WHERE id=?");
            $fd->execute(array($fId));
        }
    }
}