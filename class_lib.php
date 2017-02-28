<?php

	class Dice {

		public function get_number($dice) {
			$result = array();
			for ($i = 0; $i < $dice; $i++) {
				$result[$i] = rand(1, 6);
			}

			return $result;
		}
	}

	class Player {

		var $myNumber = 0;
		var $myDice = 0;
		var $myTempCup = array();
		var $myDiscardedCup = array();
		var $myCup = array();
		var $transferDice = 0;
		var $discardDicePattern;
		var $transferDicePattern;
		var $winner = false;
		var $transferCup = array();

		public function __construct($myNumber, $myDice, $discardDicePattern, $transferDicePattern) {

			$this->myNumber = $myNumber;
			$this->myDice = $myDice;
			$this->discardDicePattern = $discardDicePattern;
			$this->transferDicePattern = $transferDicePattern;

		}
		
		public function roll_dice() {

			$dice = new Dice();
			$this->myCup = $dice->get_number($this->myDice);
			$this->myTempCup = $this->myCup;
			$this->discard();
			$this->move();

		}

		public function discard() {
			foreach ($this->myCup as $row => $value) {
				// discard
				if ($value == $this->discardDicePattern) {
					$this->myDice --;
					unset($this->myCup[$row]);
				}
			}
			$this->myDiscardedCup = $this->myCup;
		}
		
		public function move() {
			foreach ($this->myCup as $row => $value) {
				if ($value == $this->transferDicePattern) {
					$this->transferDice ++;
					array_push($this->transferCup, $this->myCup[$row]);
					unset($this->myCup[$row]);
				}
			}
			$this->myDiscardedCup = $this->myCup;
		}

		public function push($data) {
			$this->myCup = array_merge($this->myCup, $data);
		}

		public function display() {
			echo ' Player Name '. $this->myNumber.' - original: ' .json_encode($this->myTempCup).' -> split into: '.json_encode($this->myDiscardedCup).' + '.json_encode($this->transferCup).' >> finally: '.json_encode($this->myCup).' => '.json_encode($this->myDice).' dice(s)';
			echo '<br/>';
		}

	} // end of player


	class Game {

		var $numberOfPlayer = 4;
		var $numberOfDice = 6;
		var $players = array();
		var $discardDicePattern = 6;
		var $transferDicePattern = 1;
		var $winner = true;
		var $iteration = 1;
		var	$winningPlayer = 99;

		public function __construct() {

			for ($i = 1; $i <= $this->numberOfPlayer; $i++) {

				$player = new Player($i, $this->numberOfDice, $this->discardDicePattern, $this->transferDicePattern);

				$this->players[$i] = $player;
			}
		}


		public function play() {

			do {
				echo 'Round '.$this->iteration.'<br/>';

				for ($i = 1; $i <= $this->numberOfPlayer; $i++) {
					$player = $this->players[$i];
					$player->roll_dice();
				}

				for ($i = 1; $i <= $this->numberOfPlayer; $i++) {
					$player = $this->players[$i];

					if ($i == 1) {
						$player->push($this->players[$this->numberOfPlayer]->transferCup);
						$player->myDice += $this->players[$this->numberOfPlayer]->transferDice;
					} else {
						$player->push($this->players[$i - 1]->transferCup);
						$player->myDice += $this->players[$i - 1]->transferDice;
					}

					$player->myDice = count($player->myCup);
					$player->display();

					if ($player->myDice == 0) {
						$this->winner = false;
						$this->winningPlayer = $player->myNumber;
					} 
				}

				for ($i = 1; $i <= $this->numberOfPlayer; $i++) {
					$player = $this->players[$i];

					if ($i == 1) {
						$this->players[$this->numberOfPlayer]->transferCup = array();
						$this->players[$this->numberOfPlayer]->transferDice = 0;
					} else {
						$this->players[$i - 1]->transferCup = array();
						$this->players[$i - 1]->transferDice = 0;
					}
				}

				if ($this->winner) {
						$this->iteration ++;
					}
				echo '<hr/>';
			} while ($this->winner);
		}

		
	}
?>