<?php

namespace CLIOpts\Help;


/**
 * Console Text Formatting Utilities Library
 * 
 * Utilities for dealing with console-based PHP scripts
 */
class ConsoleFormat {

	const CONSOLE_PLAIN = 0;
	const CONSOLE_BOLD = 1;
	const CONSOLE_UNDERLINE = 4;

	const CONSOLE_BLACK = 30;
	const CONSOLE_RED = 31;
	const CONSOLE_GREEN = 32;
	const CONSOLE_YELLOW = 33;
	const CONSOLE_BLUE = 34;
	const CONSOLE_MAGENTA = 35;
	const CONSOLE_CYAN = 36;
	const CONSOLE_WHITE = 37;

	//////////////////////////////////////////////////////////////////////////////////////
	// Class Methods
	//////////////////////////////////////////////////////////////////////////////////////


	/**
	* builds the consoloe line text to change the text mode, like bold or underline
	* 
	* @param string $mode A mode such as "bold", "underline" or "plain"
	* @return string conole text
	*/
	function mode() {
		$mode_texts = func_get_args();

		$code_text = '';
		foreach ($mode_texts as $mode) {
			$constant_name = 'self::CONSOLE_'.strtoupper($mode);
			if (defined($constant_name)) {
				$code_text .= (strlen($code_text) ? ';': '').constant($constant_name);
			}
		}

		// ESC [ 3 h
		return chr(27)."[0;".$code_text."m";
	}

}
