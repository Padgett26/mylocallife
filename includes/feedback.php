So how are we doing?<br /><br />
<form action="index.php?page=<?php echo $page; ?>" method="post" style="position:relative; left:-2px;">
    <table cellspacing="0" cellpadding="0" style="position:relative; left:-2px;">
        <tr>
            <td>
                <img src="images/Thumb_down.png" alt="sux" />
            </td>
            <td>
                0
            </td>
            <td>
                2
            </td>
            <td>
                4
            </td>
            <td>
                6
            </td>
            <td>
                8
            </td>
            <td>
                10
            </td>
            <td>
                <img src="images/Thumb_up.png" alt="great" />
            </td>
        </tr>
        <tr>
            <td>
            </td>
            <td>
                <input type="radio" name="rating" value="0" />
            </td>
            <td>
                <input type="radio" name="rating" value="2" />
            </td>
            <td>
                <input type="radio" name="rating" value="4" />
            </td>
            <td>
                <input type="radio" name="rating" value="6" />
            </td>
            <td>
                <input type="radio" name="rating" value="8" />
            </td>
            <td>
                <input type="radio" name="rating" value="10" />
            </td>
            <td>
            </td>
        </tr>
    </table><br /><br />
    <div style="">Did you find something we need to fix or change? Or any comment you may have.</div>
    <textarea name="feedText" cols="20" rows="8"></textarea><br /><br />
    <input type="hidden" name="feedback" value="<?php echo ($myId != '0') ? $myId : '0'; ?>" /><input type="submit" value=" Send " />
</form>