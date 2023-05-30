<?php

if (filter_input(INPUT_POST, 'directoryScope', FILTER_SANITIZE_STRING)) {
    $_SESSION['directoryScope'] = filter_input(INPUT_POST, 'directoryScope', FILTER_SANITIZE_STRING);
}
$directoryScope = (isset($_SESSION['directoryScope'])) ? $_SESSION['directoryScope'] : "local";

$busiWhere = " WHERE t2.businessListing=? ";

if ($myZip != 0) {
    list($getZipCodes1, $getZipCodes2, $getZipCodes3) = getZipAreas($myZip,$db);

    if ($directoryScope == "us") {
        $statement = "SELECT id FROM directory WHERE showListing=? ORDER BY lastName, businessName";
        $busistmt = "SELECT DISTINCT t1.category FROM busiListing AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t2.businessListing=? ORDER BY t1.category";
    } else {
        if ($directoryScope == "area") {
            $where = " WHERE t1.showListing=? ";
            $t = 0;
            foreach ($getZipCodesArea as $k1 => $v1) {
                if ($t == 0) {
                    $where .= "&& (";
                    $busiWhere .= "&& (";
                }
                if ($t != 0) {
                    $where .= " OR ";
                    $busiWhere .= " OR ";
                }
                $where .= "t2.zip=$v1";
                $busiWhere .= "t2.zip=$v1";
                $t++;
            }
            if (count($getZipCodesArea) >= 1) {
                $where .= ")";
                $busiWhere .= ")";
            }
        } elseif ($directoryScope == "local") {
            $where = " WHERE t1.showListing=? ";
            $t = 0;
            foreach ($getZipCodesLocal as $k1 => $v1) {
                if ($t == 0) {
                    $where .= "&& (";
                    $busiWhere .= "&& (";
                }
                if ($t != 0) {
                    $where .= " OR ";
                    $busiWhere .= " OR ";
                }
                $where .= "t2.zip=$v1";
                $busiWhere .= "t2.zip=$v1";
                $t++;
            }
            if (count($getZipCodesLocal) >= 1) {
                $where .= ")";
                $busiWhere .= ")";
            }
        }
        $statement = "SELECT t1.id FROM directory AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id$where ORDER BY t1.lastName, t1.businessName";
        $busistmt = "SELECT DISTINCT t1.category FROM busiListing AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id$busiWhere ORDER BY t1.category";
    }

    echo "<div style='font-weight:bold; font-size:1.25em;'><form action='index.php?page=Directory' method='post'>Select the scope of your search: <select name='directoryScope' size='1'><option value='local'";
    if ($directoryScope == "local") {
        echo " selected='selected'";
    }
    echo ">Local - 70 mi</option><option value='area'";
    if ($directoryScope == "area") {
        echo " selected='selected'";
    }
    echo ">Area - 250 mi</option><option value='us'";
    if ($directoryScope == "us") {
        echo " selected='selected'";
    }
    echo ">USA</option></select><input type='submit' value='Go' /></form></div>";
} else {
    $statement = "SELECT id FROM directory WHERE showListing=? ORDER BY lastName, businessName";
    $busistmt = "SELECT DISTINCT t1.category FROM busiListing AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t2.businessListing=? ORDER BY t1.category";
}

echo "<table id='mainTableBox' cellspacing='5' style='margin-top:20px; width:100%;'>";
$stmt7 = $db->prepare($busistmt);
$stmt7->execute(array('1'));
while ($row7 = $stmt7->fetch()) {
    $cat = $row7['category'];
    $catstmt = $db->prepare("SELECT busiCatName FROM busiCategories WHERE id=?");
    $catstmt->execute(array($cat));
    $catrow = $catstmt->fetch();
    $busiCat = $catrow['busiCatName'];
    echo "<tr><td colspan='4' style='background-color:#eeeeee; padding:10px; cursor:pointer; border:1px solid $highlightColor;' onclick='toggleview(\"cat$cat\")'>$busiCat</td></tr>\n";
    echo "<tr><td colspan='4' valign='top'><table id=\"cat$cat\" cellspacing='0' style='width:100%; display:none;'>\n";
    $stmt = $db->prepare("SELECT t1.id FROM busiListing AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id$busiWhere && t1.category=? ORDER BY t1.busiName");
    $stmt->execute(array('1', $cat));
    while ($row = $stmt->fetch()) {
        $busiId = $row['id'];
        echo "<tr>";
        displayBusiness($busiId, $db, $highlightColor);
            echo "</tr>\n";
    }
    echo "</table></td></tr>";
}
echo "<tr>";
$x = 0;
$stmt6 = $db->prepare($statement);
$stmt6->execute(array('1'));
while ($row6 = $stmt6->fetch()) {
    $id = $row6['id'];
    displayDirectory($id, $db, $highlightColor);
    $x++;
    if ($x % $directoryCols == 0) {
        echo "</tr><tr>\n";
    }
}
if ($x % $directoryCols == 1) {
    echo "<td style='width:25%;'></td><td style='width:25%;'></td><td style='width:25%;'></td>\n";
} elseif ($x % $directoryCols == 2) {
    echo "<td style='width:25%;'></td><td style='width:25%;'></td>\n";
} elseif ($x % $directoryCols == 3) {
    echo "<td style='width:25%;'></td>\n";
}
echo "</tr></table>";
