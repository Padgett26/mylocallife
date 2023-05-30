<!-- Beginning of gamesHead -->
<?php
$playGame = filter_input ( INPUT_GET, 'play', FILTER_SANITIZE_STRING );

if ($playGame == "cipher") {
	?>
    <script type="text/javascript">
        function charSelect(char, pos, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("cipher").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/cipher/cipher.php?char=" + char + "&pos=" + pos + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleStart(n, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("cipher").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/cipher/cipher.php?get=" + n + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function getHint(freebie, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("cipher").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/cipher/cipher.php?hint=" + freebie + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
    </script>
    <?php
}

if ($playGame == "gameOfFifteen") {
	?>
    <script type="text/javascript">
        function puzzleStart(getnew, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/gameOfFifteen/gameOfFifteen.php?getnew=" + getnew + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleUpdate(i, j, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/gameOfFifteen/gameOfFifteen.php?i=" + i + "&j=" + j + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
    </script>
    <?php
}

if ($playGame == "gridPuzzle") {
	?>
    <script type="text/javascript">
        function puzzleStart(gridSize, userId)
        {
            if (window.XMLHttpRequest)
            {// code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else
            {// code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/gridPuzzle/gridPuzzle.php?gridSize=" + gridSize + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleReset(reset, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/gridPuzzle/gridPuzzle.php?reset=" + reset + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleUpdate(i, j, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/gridPuzzle/gridPuzzle.php?i=" + i + "&j=" + j + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
    </script>
    <?php
}

if ($playGame == "mSquare") {
	?>
    <script type="text/javascript">
        function numSelect(num, posx, posy, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("mSquare").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/mSquare/mSquare.php?num=" + num + "&posx=" + posx + "&posy=" + posy + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleStart(n, d, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("mSquare").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/mSquare/mSquare.php?get=" + n + "&diff=" + d + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleReset()
        {
            xmlhttp = new XMLHttpRequest(userId);
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("mSquare").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/mSquare/mSquare.php?reset=1" + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
    </script>
    <?php
}

if ($playGame == "sequence") {
	?>
    <script type="text/javascript">
        function puzzleStart(gridSize, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("sequence").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sequence/sequence.php?game=new&gridSize=" + gridSize + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function puzzleUpdate(dot, spot, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("sequence").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sequence/sequence.php?dot=" + dot + "&spot=" + spot + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
    </script>
    <?php
}

if ($playGame == "sudoku") {
	?>
    <script type="text/javascript">
        function sudokuStart(difficulty, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sudoku/sudoku.php?difficulty=" + difficulty + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function sudokuReset(reset, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sudoku/sudoku.php?reset=" + reset + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function sudokuNumSelect(selectNum, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sudoku/sudoku.php?selectNum=" + selectNum + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function sudokuUpdate(loci, locj, num, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sudoku/sudoku.php?loci=" + loci + "&locj=" + locj + "&num=" + num + "&userId=" + userId, true);
            xmlhttp.send();
        }
        ;
        function sudokuMoves(f, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sudoku/sudoku.php?move=" + f + "&userId=" + userId, true);
            xmlhttp.send();
        }
        function sudokuPause(f, userId)
        {
            xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
                {
                    document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
                }
            };
            xmlhttp.open("GET", "games/sudoku/sudoku.php?pause=" + f + "&userId=" + userId, true);
            xmlhttp.send();
        }
    </script>
    <?php
}
if ($playGame == "gameAids") {
	?>
    <script type="text/javascript">
	function lifeStart()
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/life.php", true);
		xmlhttp.send();
	}

	function setLifePlayer(n, v)
	{
	xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/life.php?player=" + n + "&name=" + v, true);
		xmlhttp.send();
	}

	function setLifeToken(n, v)
	{
	xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/life.php?player=" + n + "&token=" + v, true);
		xmlhttp.send();
	}

	function lifeSpin()
	{
	xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/life.php?spin=1", true);
		xmlhttp.send();
	}

	function lifeReset()
	{
	xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/life.php?reset=1", true);
		xmlhttp.send();
	}

	function clueStart()
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/clue.php", true);
		xmlhttp.send();
	}

	function clueReset()
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/clue.php?reset=1", true);
		xmlhttp.send();
	}

	function clueSetFill(f,n)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/clue.php?setFill=" + n, true);
		xmlhttp.send();
	}

	function clueMark(a,b,fill)
	{
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function ()
		{
			if (xmlhttp.readyState === 4 && xmlhttp.status === 200)
			{
				document.getElementById("puzzle").innerHTML = xmlhttp.responseText;
			}
		};
		xmlhttp.open("GET", "games/gameAids/clue.php?a=" + a + "&b=" + b + "&fill=" + fill, true);
		xmlhttp.send();
	}
	</script>
    <?php
}
?>
<!-- End of gamesHead -->