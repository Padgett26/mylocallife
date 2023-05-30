<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="66QJVQYUAKAWN">
    <table>
        <tr><td><input type="hidden" name="on0" value="Options">Options</td></tr><tr><td><select name="os0">
                    <option value="6 months">6 months $36.00 USD</option>
                    <option value="1 year">1 year $60.00 USD</option>
                    <option value="2 years">2 years $100.00 USD</option>
                </select> </td></tr>
    </table>
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="custom" value="<?php echo $myId; ?>,0,0,<?php echo $Aid; ?>,0"/>
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>