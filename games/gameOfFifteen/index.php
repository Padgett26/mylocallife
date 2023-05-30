<?php
$userId = ($myId != '0') ? $myId : '0';
?>
<div id="puzzle">
    <button type='button' onclick='puzzleStart("new", "<?php echo $userId; ?>")'> Create puzzle </button><br /><br /><br />
    Move the tiles to put them in order.
</div>