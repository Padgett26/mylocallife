<div id="mSquare">
    Select a grid size and difficulty:<br><br>
    Hard Mode: Create puzzle where the diagonals are counted (only 1 known answer(4, if you flip the board)):<br>
    <button onclick='puzzleStart(3, "hard", <?php
				echo $myId;
				?>)'> 3 </button> <button onclick='puzzleStart(6, "hard", <?php

				echo $myId;
				?>)'> 6 </button> <button onclick='puzzleStart(9, "hard", <?php

				echo $myId;
				?>)'> 9 </button><br><br>
    Create puzzle where the diagonals are not counted (many answers):<br>
    <button onclick='puzzleStart(3, "easy", <?php

				echo $myId;
				?>)'> 3 </button> <button onclick='puzzleStart(6, "easy", <?php

				echo $myId;
				?>)'> 6 </button> <button onclick='puzzleStart(9, "easy", <?php

				echo $myId;
				?>)'> 9 </button><br><br>
    Select the size of your grid.<br>
    Fill the grid with the numbers 1 -> (cols x rows), each number can only be used once.<br>
    When the grid is filled, every col, every row, and, if in hard mode, both corner to corner diagonals equal the same number, you win!<br>
    If that totally confused you, play it, you will get the gist.<br />
</div>