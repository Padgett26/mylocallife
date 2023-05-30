<?php
$play = "";
if (filter_input ( INPUT_GET, 'play', FILTER_SANITIZE_STRING )) {
	$play = filter_input ( INPUT_GET, 'play', FILTER_SANITIZE_STRING );
}
?>
<table cellpadding="0" cellspacing="0" style="width:100%;">
    <tr>
        <td>
            <nav style="text-align:center; padding:10px;">
                <a href="index.php?page=Games&play=cipher">Cipher</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="index.php?page=Games&play=gridPuzzle">Grid Puzzle</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="index.php?page=Games&play=sudoku">Sudoku</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="index.php?page=Games&play=mSquare">Magic Square</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="index.php?page=Games&play=gameOfFifteen">Game of fifteen</a>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <a href="index.php?page=Games&play=gameAids">Game Aids</a>
            </nav>
        </td>
    </tr>
    <tr>
        <td>
            <div id='mainTableBox' style="width:96%; border:5px double black; min-height:500px; padding:10px;">
                <?php
																if ($play != "") {
																	include "games/" . $play . "/index.php";
																}
																?>
            </div>
        </td>
    </tr>
</table>