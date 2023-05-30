<?php
session_start ();

for($k = 1; $k < 5; ++ $k) {
	if (! isset ( $_SESSION ['lifeName' . $k] )) {
		$_SESSION ['lifeName' . $k] = 0;
	}
	if (! isset ( $_SESSION ['lifeToken' . $k] )) {
		$_SESSION ['lifeToken' . $k] = 0;
	}
}

$token = (filter_input ( INPUT_GET, 'token', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'token', FILTER_SANITIZE_NUMBER_INT ) : 0;
$player = (filter_input ( INPUT_GET, 'player', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'player', FILTER_SANITIZE_NUMBER_INT ) : 0;
$name = (filter_input ( INPUT_GET, 'name', FILTER_SANITIZE_STRING )) ? filter_input ( INPUT_GET, 'name', FILTER_SANITIZE_STRING ) : 0;
$spin = (filter_input ( INPUT_GET, 'spin', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;
$reset = (filter_input ( INPUT_GET, 'reset', FILTER_SANITIZE_NUMBER_INT ) == 1) ? 1 : 0;

if ($player != 0 && $token != 0) {
	$_SESSION ['lifeToken' . $player] = $token;
}

if ($player != 0 && $name != 0) {
	$_SESSION ['lifeName' . $player] = $name;
}

if ($reset == 1) {
	for($n = 1; $n < 5; ++ $n) {
		$_SESSION ['lifeName' . $n] = "";
		$_SESSION ['lifeToken' . $n] = 0;
	}
}

for($l = 1; $l < 5; ++ $l) {
	${"p" . $l} = $_SESSION ['lifeName' . $l];
	${"t" . $l} = $_SESSION ['lifeToken' . $l];
}
?>
<div style="text-align:center; font-weight:bold; font-size:1.25em; margin:20px 0px;">The Game of Life</div>
<table>
	<tr>
		<td style="font-weight:bold;">Players</td>
		<td style='width:20px;'>&nbsp;</td>
		<td style="font-weight:bold;">Career<br>Token #</td>
	</tr>
<?php
for($j = 1; $j < 5; ++ $j) {
	echo "<tr>\n";
	echo "<td>$j. <input type='text' onchange='setLifePlayer($j,this.value)' value='";
	echo (${'p' . $j} != 0) ? ${'p' . $j} : "";
	echo "'></td>\n";
	echo "<td style='width:20px;'>&nbsp;</td>\n";
	echo "<td>\n";
	echo "<select oninput='setLifeToken($j,this.value)'>\n";
	for($i = 0; $i <= 10; ++ $i) {
		echo "<option value='$i'";
		echo ($i == ${'t' . $j}) ? " selected" : "";
		echo ">$i</option>\n";
	}
	echo "</select></td>\n";
	echo "</tr>\n";
}
?>
<tr>
		<td style="font-weight:bold; text-align:center;" colspan='3'><button onclick="lifeReset()">Reset players and tokens</button></td>
	</tr>
</table>

<table style='width:100%'>
	<tr>
		<td colspan='3' style='padding:20px;'>&nbsp;</td>
	</tr>
	<tr>
		<td colspan='3' style='text-align:center;'><button style='font-weight:bold; font-size:2em; padding:10px;' onclick="lifeSpin()"> SPIN </button></td>
	</tr>
	<tr>
		<td style='padding:10px 0px; width:33%; text-align:center; font-weight:bold;'>Number</td>
		<td style='padding:10px 0px; width:33%; text-align:center; font-weight:bold;'>Red / Black</td>
		<td style='padding:10px 0px; width:33%; text-align:center; font-weight:bold;'>Career Token</td>
	</tr>
		<?php
		if ($spin == 1) {
			$r = rand ( 1, 10 );
			switch ($r) {
				case 1 :
					$color = "#ffff00;";
					$RorB = "red;";
					$fontColor = "#000000;";
					break;
				case 2 :
					$color = "#daa520;";
					$RorB = "black;";
					$fontColor = "#000000;";
					break;
				case 3 :
					$color = "#ffa500;";
					$RorB = "red;";
					$fontColor = "#000000;";
					break;
				case 4 :
					$color = "#ff0000;";
					$RorB = "black;";
					$fontColor = "#000000;";
					break;
				case 5 :
					$color = "#ff00ff;";
					$RorB = "red;";
					$fontColor = "#000000;";
					break;
				case 6 :
					$color = "#800080;";
					$RorB = "black;";
					$fontColor = "#ffffff;";
					break;
				case 7 :
					$color = "#00008b;";
					$RorB = "red;";
					$fontColor = "#ffffff;";
					break;
				case 8 :
					$color = "#0000ff;";
					$RorB = "black;";
					$fontColor = "#ffffff;";
					break;
				case 9 :
					$color = "#008000;";
					$RorB = "red;";
					$fontColor = "#ffffff;";
					break;
				case 10 :
					$color = "#90ee90;";
					$RorB = "black;";
					$fontColor = "#000000;";
					break;
			}
			echo "<tr><td style='padding:20px 0px; width:33%; text-align:center; font-weight:bold; font-size:1.5em; background-color:$color color:$fontColor'>$r</td>\n";
			echo "<td style='width:33%; text-align:center; font-weight:bold; background-color:$RorB'>&nbsp;</td>\n";
			echo "<td style='padding:20px 0px; width:33%; text-align:center; font-weight:bold;'>";
			for($m = 1; $m < 5; ++ $m) {
				echo ($r == ${'t' . $m}) ? "Pay 20k to " . ${'p' . $m} . "<br>" : "";
			}
			echo "</td></tr>\n";
		}
		?>
</table>