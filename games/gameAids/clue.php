<?php
session_start ();

$marks = array (
		"",
		"&nbsp;",
		"&#x2718;",
		"&#x2714;",
		"<div style='width:15px; height:15px; background-color:#000000;'>&nbsp;</div>",
		"<div style='width:15px; height:15px; background-color:#008000;'>&nbsp;</div>",
		"<div style='width:15px; height:15px; background-color:#ff0000;'>&nbsp;</div>"
);

$startNames = array (
		"x",
		"Colonel Mustard",
		"Professor Plum",
		"Mr Green",
		"Mrs Peacock",
		"Miss Scarlet",
		"Mrs White",
		"Knife",
		"Candlestick",
		"Revolver",
		"Rope",
		"Lead Pipe",
		"Wrench",
		"Hall",
		"Lounge",
		"Dining Room",
		"Kitchen",
		"Ballroom",
		"Conservatory",
		"Billard Room",
		"Library",
		"Study"
);

$startArray = array ();
for($a = 0; $a < 22; ++ $a) {
	for($b = 0; $b < 7; ++ $b) {
		if ($b == 0) {
			$startArray [$a] [$b] = $startNames [$a];
		} else {
			$startArray [$a] [$b] = 0;
		}
	}
}

if (! isset ( $_SESSION ['clueSetFill'] )) {
	$_SESSION ['clueSetFill'] = 1;
}
if (! isset ( $_SESSION ['clueBoard'] )) {
	$_SESSION ['clueBoard'] = $startArray;
}

$setFill = (filter_input ( INPUT_GET, 'setFill', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'setFill', FILTER_SANITIZE_NUMBER_INT ) : "x";
$a = (filter_input ( INPUT_GET, 'a', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'a', FILTER_SANITIZE_NUMBER_INT ) : "x";
$b = (filter_input ( INPUT_GET, 'b', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'b', FILTER_SANITIZE_NUMBER_INT ) : "x";
$f = (filter_input ( INPUT_GET, 'fill', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'fill', FILTER_SANITIZE_NUMBER_INT ) : "x";
$r = (filter_input ( INPUT_GET, 'reset', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;

if ($r == 1) {
	$_SESSION ['clueBoard'] = $startArray;
}

if ($a != "x" && $b != "x" && $f != "x") {
	$x = $_SESSION ['clueBoard'];
	$x [$a] [$b] = $f;
	$_SESSION ['clueBoard'] = $x;
}

if ($setFill != "x") {
	$_SESSION ['clueSetFill'] = $setFill;
}

$fill = $_SESSION ['clueSetFill'];
$board = $_SESSION ['clueBoard'];
?>
<div style="text-align:center; font-weight:bold; font-size:1.25em; margin:20px 0px;">Clue</div>
<table>
	<tr>
		<td colspan='7' style='padding:20px 0px; text-align:left;'><button style='' onclick='clueReset()'> Resst the game board </button></td>
	</tr>
	<tr>
	<td colspan='7' style='padding:5px;'>&nbsp;</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td style='padding:10px; cursor:pointer; text-align:center;<?php
	echo ($fill == 1) ? " border:1px solid #000000;" : "";
	?>' onclick="clueSetFill('f',1)">&#x2752;</td>
	<td style='padding:10px; cursor:pointer;<?php
	echo ($fill == 2) ? " border:1px solid #000000;" : "";
	?>' onclick="clueSetFill('f',2)">&#x2718;</td>
	<td style='padding:10px; cursor:pointer;<?php
	echo ($fill == 3) ? " border:1px solid #000000;" : "";
	?>' onclick="clueSetFill('f',3)">&#x2714;</td>
	<td style='padding:10px; cursor:pointer;<?php
	echo ($fill == 4) ? " border:1px solid #000000;" : "";
	?>' onclick="clueSetFill('f',4)"><div style='width:15px; height:15px; background-color:#000000;'>&nbsp;</div></td>
	<td style='padding:10px; cursor:pointer;<?php
	echo ($fill == 5) ? " border:1px solid #000000;" : "";
	?>' onclick="clueSetFill('f',5)"><div style='width:15px; height:15px; background-color:#008000;'>&nbsp;</div></td>
	<td style='padding:10px; cursor:pointer;<?php
	echo ($fill == 6) ? " border:1px solid #000000;" : "";
	?>' onclick="clueSetFill('f',6)"><div style='width:15px; height:15px; background-color:#ff0000;'>&nbsp;</div></td>
	</tr>
	<tr>
	<td colspan='7' style='padding:10px 0px; font-weight:bold;'>Suspects</td>
	</tr>
	<?php
	for($c = 1; $c < 7; ++ $c) {
		echo "<tr><td style='padding:10px;'>" . $board [$c] [0] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($c,1,$fill)'>" . $marks [$board [$c] [1]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($c,2,$fill)'>" . $marks [$board [$c] [2]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($c,3,$fill)'>" . $marks [$board [$c] [3]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($c,4,$fill)'>" . $marks [$board [$c] [4]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($c,5,$fill)'>" . $marks [$board [$c] [5]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($c,6,$fill)'>" . $marks [$board [$c] [6]] . "</td></tr>";
	}
	?>
	<tr>
	<td colspan='7' style='padding:10px 0px;'>&nbsp;</td>
	</tr>
	<tr>
	<td colspan='7' style='padding:10px 0px; font-weight:bold;'>Weapons</td>
	</tr>
	<?php
	for($d = 7; $d < 13; ++ $d) {
		echo "<tr><td style='padding:10px;'>" . $board [$d] [0] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($d,1,$fill)'>" . $marks [$board [$d] [1]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($d,2,$fill)'>" . $marks [$board [$d] [2]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($d,3,$fill)'>" . $marks [$board [$d] [3]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($d,4,$fill)'>" . $marks [$board [$d] [4]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($d,5,$fill)'>" . $marks [$board [$d] [5]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($d,6,$fill)'>" . $marks [$board [$d] [6]] . "</td></tr>";
	}
	?>
	<tr>
	<td colspan='7' style='padding:10px 0px;'>&nbsp;</td>
	</tr>
	<tr>
	<td colspan='7' style='padding:10px 0px; font-weight:bold;'>Rooms</td>
	</tr>
	<?php
	for($e = 13; $e < 22; ++ $e) {
		echo "<tr><td style='padding:10px;'>" . $board [$e] [0] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($e,1,$fill)'>" . $marks [$board [$e] [1]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($e,2,$fill)'>" . $marks [$board [$e] [2]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($e,3,$fill)'>" . $marks [$board [$e] [3]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($e,4,$fill)'>" . $marks [$board [$e] [4]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($e,5,$fill)'>" . $marks [$board [$e] [5]] . "</td>";
		echo "<td style='padding:10px; cursor:pointer; border:1px solid black;' onclick='clueMark($e,6,$fill)'>" . $marks [$board [$e] [6]] . "</td></tr>";
	}
	?>
</table>