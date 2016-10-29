<?php

class Logger {

	function __call($name, $arguments){

		$log = date('d-m-Y H:i:s')." [$name] ${arguments[0]}\n";
		file_put_contents(__DIR__ . '/../../log/le_log_' . date('d_m_Y') . '.txt', $log, FILE_APPEND);
		echo $log;

	}

}