<?php include("class_lib.php"); ?>
<?php
	$game = new Game();
	$game->play();
	echo '<br/> Winner : Player  '.$game->winningPlayer.'<br/>';
	echo 'Completed in '.$game->iteration.' Iteration(s).';
?>