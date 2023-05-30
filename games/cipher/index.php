<?php
$userId = ($myId != '0') ? $myId : '0';
?>
<div id="cipher">
    <?php
    if (filter_input(INPUT_POST, 'newQ', FILTER_SANITIZE_NUMBER_INT) >= '1') {
        $quote = trim(filter_input(INPUT_POST, 'quote', FILTER_SANITIZE_STRING));
        $author = trim(filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING));
        if ($quote != "" && $quote != " " && $author != "" && $author != " ") {
            $stmt = $db->prepare("INSERT INTO quotes VALUES(NULL, ?, ?, '0','0','0')");
            $stmt->execute(array($quote, $author));
            $text = $quote . " - " . $author;
            $newtext = wordwrap($text, 130, "<br />\n");
            echo "<br /><br />Your quote:<br /><span style='font-weight:bold;'>$newtext</span><br />Has been uploaded, thank you.<br /><br />";
        }
    }
    ?>
    <button type="button" onclick="puzzleStart('new', '<?php echo $userId; ?>')"> Get new cipher </button><br /><br /><br />
    An encrypted, famous quote.<br />Each letter represents a letter in the alphabet.<br />Place the letter you think is correct in the box above the encoded letter.<br />Get them all correct and find the quote.
</div>