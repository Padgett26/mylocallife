<?php
$userId = ($myId != '0') ? $myId : '0';
?>
<div id="puzzle">
    Select a grid size: <button onclick='puzzleStart(5,
    <?php
				echo $userId;
				?>
				)'> 5 </button> <button onclick='puzzleStart(10,
				<?php
				echo $userId;
				?>
				)'> 10 </button> <button onclick='puzzleStart(15,
				<?php
				echo $userId;
				?>
				)'> 15 </button> <button onclick='puzzleStart(20,
				<?php
				echo $userId;
				?>
				)'> 20 </button><br /><br /><br />
    Creates a grid in the size you pick.<br />Along the top and side are groups of numbers. These numbers represent groups of colored blocks in that row or col.<br />When a row or col is filled in correctly, it's corresponding number group will be shaded. <br />Satisfy all of the cols and rows and you win.<br />Click on a block to highlight it.
</div>