<?php

class Logger {

	function __call($name, $arguments){
		echo date('d-m-Y H:i:s')." [$name] ${arguments[0]}<br>";
	}
	
}