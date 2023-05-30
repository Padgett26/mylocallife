<?php
$userId = ($myId != '0') ? $myId : '0';
?>

<table>
  <tr>
    <td>
      Select a puzzle size:
    </td>
    <td>
      10x10 <input type="radio" onselect='puzzleStart("11", "<?php echo $userId; ?>")' /><br /><br />
      20x20 <input type="radio" onselect='puzzleStart("21", "<?php echo $userId; ?>")' /><br /><br />
      40x40 <input type="radio" onselect='puzzleStart("41", "<?php echo $userId; ?>")' /><br /><br />
    </td>
  </tr>
</table>
Creates a grid in the size you pick.<br />Click on the block to rotate, and align the pipes so there are no ends without an end cap.
<div id="puzzle">
  &nbsp;
</div>