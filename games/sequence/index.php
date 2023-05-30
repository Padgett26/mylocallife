<?php
$userId = ($myId != '0') ? $myId : '0';
?>
<div id="sequence">
    Select a size: <select onchange='puzzleStart(this.value, "<?php echo $userId; ?>")' />
    <?php
    for ($i = 4; $i <= 9; $i++) {
        echo "<option value='$i'>$i</option>\n";
    }
    ?>
</select>
    <button type='button'> Create puzzle </button><br /><br /><br />
    Creates a grid in the size you pick.<br />Along the top and side are groups of numbers. These numbers represent groups of colored blocks in that row or col.<br />When a row or col is filled in correctly, it's corresponding number group will be shaded. <br />Satisfy all of the cols and rows and you win.<br />Click on a block to highlight it.
</div>