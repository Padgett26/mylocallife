<?php

foreach ($_POST as $k => $v) {
    if (preg_match("/^factoid([1-9][0-9]*)$/", $k, $match)) {
        $qId = $match[1];
        $qd = $db->prepare("UPDATE factoids SET factoid=? WHERE id=?");
        $qd->execute(array($v, $qId));
    }
    if (preg_match("/^appr([1-9][0-9]*)$/", $k, $match)) {
        $val = (filter_var($v, FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";
        $qId = $match[1];
        if ($val == '1') {
            $qd = $db->prepare("UPDATE factoids SET approved=? WHERE id=?");
            $qd->execute(array('1', $qId));
        } else {
            $qd = $db->prepare("DELETE FROM factoids WHERE id=?");
            $qd->execute(array($qId));
        }
    }
}