<?php

	class Timing {
		private $elem;
		private $start_time;
		private $stop_time;

		// Constructor for Timing class
		public function __construct($elem = "") {
			$this->elem = $elem;
			// Set timezone
			date_default_timezone_set('Europe/Berlin');
		}

		// Set start time
		public function start() {
			$this->start_time = microtime(true);
		}

		// Set stop/end time
		public function stop() {
			$this->stop_time = microtime(true);
		}

		// Returns time elapsed from start
		public function getElapsedTime() {
			return $this->getExecutionTime(microtime(true));
		}

		// Returns total execution time
		public function getTotalExecutionTime() {
			if (!$this->stop_time) {
				return false;
			}
			return $this->getExecutionTime($this->stop_time);
		}

		// Returns start time, stop time and total execution time
		public function getFullStats() {
			$this->stop();
			if (!$this->stop_time) {
				return false;
			}

			$stats = array();
			$stats['start_time'] = $this->getDateTime($this->start_time);
			$stats['stop_time'] = $this->getDateTime($this->stop_time);
			$stats['total_execution_time'] = $this->getExecutionTime($this->stop_time);

			return $stats;
		}

		// Prints time elapsed from start
		public function printElapsedTime() {
			error_log ("Elapsed time para ".$his->elem.": " . $this->getExecutionTime(microtime(true)));
		}

		// Prints total execution time
		public function printTotalExecutionTime() {
			$this->stop();
			if (!$this->stop_time) {
				return false;
			}
			error_log("--------------------------------------------------------");
			error_log ("Execution time total para ".$this->elem.": " . $this->getExecutionTime($this->stop_time));
			error_log("--------------------------------------------------------");
		}

		// Prints start time, stop time and total execution time
		public function printFullStats() {
			if (!$this->stop_time) {
				return false;
			}

			error_log("--------------------------------------------------------");
			error_log ("Script start date and time: " . $this->getDateTime($this->start_time));
			error_log ("Script stop end date and time: " . $this->getDateTime($this->stop_time));
			error_log ("Total execution time: " . $this->getExecutionTime($this->stop_time));
			error_log("--------------------------------------------------------");
		}

		// Format time to date and time
		private function getDateTime($time) {
			return date("Y-m-d H:i:s", $time);
		}

		// Get execution time by timestamp
		private function getExecutionTime($time) {
			return $time - $this->start_time;
		}
	}

?>