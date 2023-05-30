<?php
echo "<div style='text-align:center; width:100%; margin-bottom:20px;'>";
echo "<span style='font-weight:bold; color:$highlightColor; font-size:1.5em;'>Helping Hands</span>";
echo "<div style='background-color:#dddddd; border:1px solid $highlightColor; border-radius:4px; height:5px; width:60%; margin:10px auto;'></div></div>";

$ckHHUsers = $db->prepare("SELECT COUNT(*) FROM HHUsers WHERE userId = ?");
$ckHHUsers->execute(array($myId));
$ckH = $ckHHUsers->fetch();
$c = $ckH[0];
if ($c == 0) {
    $addU = $db->prepare("INSERT INTO HHUsers VALUES(NULL,?,'0',?,?,'0','0','0','0')");
    $addU->execute(array($myId, '', ''));
}

if (filter_input(INPUT_POST, 'addressUpdate', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $uId = filter_input(INPUT_POST, 'addressUpdate', FILTER_SANITIZE_NUMBER_INT);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $csz = filter_input(INPUT_POST, 'csz', FILTER_SANITIZE_STRING);

    $addr = $db->prepare("UPDATE HHUsers SET address = ?, csz = ?, phone = ? WHERE userId = ?");
    $addr->execute(array($address, $csz, $phone, $uId));
}

if (filter_input(INPUT_POST, 'cashOut', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $cashOut = filter_input(INPUT_POST, 'cashOut', FILTER_SANITIZE_NUMBER_INT);
    cashOut($userId, $db);
}

if (filter_input(INPUT_POST, 'payCM', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $payCM = filter_input(INPUT_POST, 'payCM', FILTER_SANITIZE_NUMBER_INT);
    $ckNum = filter_input(INPUT_POST, 'ckNum', FILTER_SANITIZE_NUMBER_INT);

    $pay = $db->prepare("SELECT cashOut FROM HHUsers WHERE userId = ?");
    $pay->execute(array($payCM));
    $payR = $pay->fetch();
    $cashO = $payR['cashOut'];

    $pay2 = $db->prepare("UPDATE HHUsers SET cashOut = ? WHERE userId = ?");
    $pay2->execute(array('0', $payCM));

    $pay3 = $db->prepare("INSERT INTO HHxactions VALUES(NULL,?,'0',?,?,'3',?,'0','0','0','0')");
    $pay3->execute(array($payCM, $time, $cashO, $ckNum));
}

if ($myAccess == '3') {
    $showHHUser = 0;
    if (filter_input(INPUT_POST, 'HHUsers', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $showHHUser = filter_input(INPUT_POST, 'HHUsers', FILTER_SANITIZE_NUMBER_INT);
    }
    ?>
    <div style="margin:20px 10px;">View xactions for: <form action='index.php?page=Coins' method='post'>
            <select name='HHUsers' size='1'>
                <?php
                $hhusers = $db->prepare("SELECT userId, cashOut FROM HHUsers");
                $hhusers->execute();
                while ($hhuR = $hhusers->fetch()) {
                    $hhId = $hhuR['userId'];
                    $cashOut = $hhuR['cashOut'];
                    $gethhname = $db->prepare("SELECT firstName, lastName FROM users WHERE id = ?");
                    $gethhname->execute(array($hhId));
                    $hhnR = $gethhname->fetch();
                    $fn = $hhnR['firstName'];
                    $ln = $hhnR['lastName'];
                    echo "<option value='$hhId'";
                    echo ($hhId == $showHHUser) ? " selected" : "";
                    echo ">$fn $ln";
                    echo ($cashOut >= 1) ? " *" : "";
                    echO "</option>\n";
                }
                ?>
            </select> <input type='submit' value=' GO '>
        </form></div>
    <?php
    if ($showHHUser >= 1) {
        $hhInfo1 = $db->prepare("SELECT coinQty, cashOut FROM HHUsers WHERE userId = ?");
        $hhInfo1->execute(array($showHHUser));
        $hh1R = $hhInfo1->fetch();
        $coinQty = $hh1R['coinQty'];
        $cashO = $hh1R['cashOut'];

        if ($cashO >= 1) {
            echo "<div style='margin:20px 10px; font-weight:bold;'>Owed: $" . ($cashO * 5) . "</div>";
            echo "<div style='margin:20px 10px; font-weight:bold;'>Ck#: <form action='' method=''><input type='text' name='ckNum' size='5'><input type='hidden' name='payCM' value='$showHHUser'><input type='submit' value=' Pay '></form></div>";
        }

        echo "<div style='margin:20px 10px; font-weight:bold;'>Current balance: $coinQty coins ($" . ($coinQty * 5) . ")</div>";

        if ($coinQty >= 1) {
            echo "<div style='margin:20px 10px; font-weight:bold;'>Cash Out this CM: <form action='index.php?page=Coins' method='post'><input type='hidden' name='cashOut' value='$showHHUser'><input type='submit' value=' Cash Out '></form></div>";
        }

        echo "<table cellspacing='0px' style='width:100%;'><tr>\n";
        echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"CPDiv\")'>Coin Purchases</div></td>\n";
        echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"CGDiv\")'>Coins Given</div></td>\n";
        echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"CRDiv\")'>Coins Received</div></td>\n";
        echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"CODiv\")'>Cash Out</div></td>\n";
        echo "</tr></table>\n";

        echo "<table id='CPDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
        $hhInfo2 = $db->prepare("SELECT * FROM HHxactions WHERE toUserId = ? && xactionType = ? ORDER BY xactionDate");
        $hhInfo2->execute(array($showHHUser, '1'));
        while ($hh2R = $hhInfo2->fetch()) {
            $xDate = $hh2R['xactionDate'];
            $xQty = $hh2R['xactionQty'];
            echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td></tr>\n";
        }
        echo "</table>";

        echo "<table id='CGDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
        $hhInfo3 = $db->prepare("SELECT * FROM HHxactions WHERE fromUserId = ? && xactionType = ? ORDER BY xactionDate");
        $hhInfo3->execute(array($showHHUser, '2'));
        while ($hh3R = $hhInfo3->fetch()) {
            $xDate = $hh3R['xactionDate'];
            $xQty = $hh3R['xactionQty'];
            $toUID = $hh3R['toUserId'];
            $gethhname = $db->prepare("SELECT firstName, lastName FROM users WHERE id = ?");
            $gethhname->execute(array($toUID));
            $hhnR = $gethhname->fetch();
            $fn = $hhnR['firstName'];
            $ln = $hhnR['lastName'];
            echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td><td>To: $fn $ln</td></tr>\n";
        }
        echo "</table>";

        echo "<table id='CRDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
        $hhInfo5 = $db->prepare("SELECT * FROM HHxactions WHERE toUserId = ? && xactionType = ? ORDER BY xactionDate");
        $hhInfo5->execute(array($showHHUser, '2'));
        while ($hh5R = $hhInfo5->fetch()) {
            $xDate = $hh5R['xactionDate'];
            $xQty = $hh5R['xactionQty'];
            $fromUID = $hh5R['fromUserId'];
            $gethhname = $db->prepare("SELECT firstName, lastName FROM users WHERE id = ?");
            $gethhname->execute(array($fromUID));
            $hhnR = $gethhname->fetch();
            $fn = $hhnR['firstName'];
            $ln = $hhnR['lastName'];
            echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td><td>From: $fn $ln</td></tr>\n";
        }
        echo "</table>";

        echo "<table id='CODiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
        $hhInfo4 = $db->prepare("SELECT * FROM HHxactions WHERE fromUserId = ? && xactionType = ? ORDER BY xactionDate");
        $hhInfo4->execute(array($showHHUser, '3'));
        while ($hh4R = $hhInfo4->fetch()) {
            $xDate = $hh4R['xactionDate'];
            $xQty = $hh4R['xactionQty'];
            echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td></tr>\n";
        }
        echo "</table>";
    }
    echo "<div style='background-color:#dddddd; border:1px solid $highlightColor; border-radius:4px; height:5px; width:60%; margin:10px auto;'></div>";
}

if ($myId >= '1') {
    $hhInfo1 = $db->prepare("SELECT coinQty, cashOut FROM HHUsers WHERE userId = ?");
    $hhInfo1->execute(array($myId));
    $hh1R = $hhInfo1->fetch();
    $coinQty = $hh1R['coinQty'];
    $cashO = $hh1R['cashOut'];

    echo "<div style='margin:20px 10px; font-weight:bold;'>Current balance: $coinQty coins</div>";
    ?>
    <div style='margin:20px 10px;'>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="RTWFTYRHSAZQN">
            <input type="hidden" name="custom" value="<?php echo $myId; ?>,0,0,0,0"/>
            <table>
                <tr>
                    <td>
                        <input type="hidden" name="on0" value="Amounts">Purchase Coins
                    </td>
                </tr>
                <tr>
                    <td>
                        <select name="os0">
                            <option value="5 coins">5 coins $27.50 USD</option>
                            <option value="10 coins">10 coins $55.00 USD</option>
                            <option value="20 coins">20 coins $110.00 USD</option>
                            <option value="30 coins">30 coins $165.00 USD</option>
                            <option value="50 coins">50 coins $275.00 USD</option>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="currency_code" value="USD">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
    </div>
    <?php
    if ($cashO >= 1) {
        echo "<div style='margin:20px 10px; font-weight:bold;'>There are $cashO coins being cashed out</div>";
    }

    if ($coinQty >= 1) {
        echo "<div style='margin:20px 10px; font-weight:bold;'>Cash Out this balance: <form action='index.php?page=Coins' method='post'><input type='hidden' name='cashOut' value='$myId'><input type='submit' value=' Cash Out '></form></div>";
    }

    echo "<table cellspacing='0px' style='width:100%;'><tr>\n";
    echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"cpDiv\")'>Coin Purchases</div></td>\n";
    echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"cgDiv\")'>Coins Given</div></td>\n";
    echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"crDiv\")'>Coins Received</div></td>\n";
    echo "<td style='width:25%; text-align:center;'><div style='margin:20px 10px; font-weight:bold; cursor:pointer;' onclick='toggleview(\"coDiv\")'>Cash Out</div></td>\n";
    echo "</tr></table>\n";

    echo "<table id='cpDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
    $hhInfo2 = $db->prepare("SELECT * FROM HHxactions WHERE toUserId = ? && xactionType = ? ORDER BY xactionDate");
    $hhInfo2->execute(array($myId, '1'));
    while ($hh2R = $hhInfo2->fetch()) {
        $xDate = $hh2R['xactionDate'];
        $xQty = $hh2R['xactionQty'];
        echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td></tr>\n";
    }
    echo "</table>";

    echo "<table id='cgDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
    $hhInfo3 = $db->prepare("SELECT * FROM HHxactions WHERE fromUserId = ? && xactionType = ? ORDER BY xactionDate");
    $hhInfo3->execute(array($myId, '2'));
    while ($hh3R = $hhInfo3->fetch()) {
        $xDate = $hh3R['xactionDate'];
        $xQty = $hh3R['xactionQty'];
        $toUID = $hh3R['toUserId'];
        $gethhname = $db->prepare("SELECT firstName, lastName FROM users WHERE id = ?");
        $gethhname->execute(array($toUID));
        $hhnR = $gethhname->fetch();
        $fn = $hhnR['firstName'];
        $ln = $hhnR['lastName'];
        echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td><td>To: $fn $ln</td></tr>\n";
    }
    echo "</table>";

    echo "<table id='crDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
    $hhInfo5 = $db->prepare("SELECT * FROM HHxactions WHERE toUserId = ? && xactionType = ? ORDER BY xactionDate");
    $hhInfo5->execute(array($myId, '2'));
    while ($hh5R = $hhInfo5->fetch()) {
        $xDate = $hh5R['xactionDate'];
        $xQty = $hh5R['xactionQty'];
        $fromUID = $hh5R['fromUserId'];
        $gethhname = $db->prepare("SELECT firstName, lastName FROM users WHERE id = ?");
        $gethhname->execute(array($fromUID));
        $hhnR = $gethhname->fetch();
        $fn = $hhnR['firstName'];
        $ln = $hhnR['lastName'];
        echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td><td>From: $fn $ln</td></tr>\n";
    }
    echo "</table>";

    echo "<table id='coDiv' style='display:none; border:1px solid black;' cellpadding='5px' cellspacing='0px'>";
    $hhInfo4 = $db->prepare("SELECT * FROM HHxactions WHERE fromUserId = ? && xactionType = ? ORDER BY xactionDate");
    $hhInfo4->execute(array($myId, '3'));
    while ($hh4R = $hhInfo4->fetch()) {
        $xDate = $hh4R['xactionDate'];
        $xQty = $hh4R['xactionQty'];
        echo "<tr><td>" . date("j-M-Y", $xDate) . "</td><td>$xQty</td></tr>\n";
    }
    echo "</table>";

    echo "<div style='margin:20px 10px;'>Please make sure your address and phone info is correct. I need to know where to send the money when you cash out.</div>";

    $getInfo = $db->prepare("SELECT * FROM HHUsers WHERE userId = ?");
    $getInfo->execute(array($myId));
    $giR = $getInfo->fetch();
    $address = $giR['address'];
    $csz = $giR['csz'];
    $phone = $giR['phone'];

    echo "<form action='index.php?page=Coins' method='post'>\n";
    echo "<table style='border:1px solid black;' cellpadding='5px' cellspacing='0px'>\n";
    echo "<tr><td>Street address</td><td><input type='text' name='address' value='$address'></td></tr>\n";
    echo "<tr><td>City, State Zip</td><td><input type='text' name='csz' value='$csz'></td></tr>\n";
    echo "<tr><td>Phone</td><td><input type='text' name='phone' value='$phone'></td></tr>\n";
    echo "<tr><td colspan='2'><input type='hidden' name='addressUpdate' value='$myId'><input type='submit' value=' Update Address '></td></tr>\n";
    echo "</table></form>\n";
}