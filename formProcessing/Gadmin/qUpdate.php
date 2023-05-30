<?php

foreach ($_POST as $k => $val) {
    $v = htmlEntities(trim(filter_var($val, FILTER_SANITIZE_STRING)), ENT_QUOTES);
    if (preg_match("/^quote([1-9][0-9]*)$/", $k, $match)) {
        $qId = $match[1];
        $qd = $db->prepare("UPDATE quotes SET quote=? WHERE id=?");
        $qd->execute(array($v, $qId));
    }
    if (preg_match("/^author([1-9][0-9]*)$/", $k, $match)) {
        $qId = $match[1];
        $qd = $db->prepare("UPDATE quotes SET author=? WHERE id=?");
        $qd->execute(array($v, $qId));
    }
    if (preg_match("/^appr([1-9][0-9]*)$/", $k, $match)) {
        $val = (filter_var($v, FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";
        $qId = $match[1];
        if ($val == '1') {
            $qd = $db->prepare("UPDATE quotes SET approved=? WHERE id=?");
            $qd->execute(array('1', $qId));
        } else {
            $qd = $db->prepare("DELETE FROM quotes WHERE id=?");
            $qd->execute(array($qId));
        }
    }
}