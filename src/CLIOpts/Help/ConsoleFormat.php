<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\Help;


/**
 * Console Text Formatting Utilities Library
 * 
 * Utilities for dealing with console-based PHP scripts
 */
class ConsoleFormat {

  const CONSOLE_PLAIN      = 0;
  const CONSOLE_BOLD       = 1;
  const CONSOLE_UNDERLINE  = 4;

  const CONSOLE_BLACK      = 30;
  const CONSOLE_RED        = 31;
  const CONSOLE_GREEN      = 32;
  const CONSOLE_YELLOW     = 33;
  const CONSOLE_BLUE       = 34;
  const CONSOLE_MAGENTA    = 35;
  const CONSOLE_CYAN       = 36;
  const CONSOLE_WHITE      = 37;

  const CONSOLE_BLACK_BG   = 40;
  const CONSOLE_RED_BG     = 41;
  const CONSOLE_GREEN_BG   = 42;
  const CONSOLE_YELLOW_BG  = 43;
  const CONSOLE_BLUE_BG    = 44;
  const CONSOLE_MAGENTA_BG = 45;
  const CONSOLE_CYAN_BG    = 46;
  const CONSOLE_WHITE_BG   = 47;


  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Makes the given text bold in the console
   * 
   * @param string $text text to be bold.
   *
   * @access public
   * @static
   *
   * @return string Bolded text.
   */
  public static function bold($text) {
    return self::mode('bold').$text.self::mode('plain');
  }

  public static function applyformatToText() {
    $args = func_get_args();
    $count = func_num_args();

    $text = $args[$count - 1];
    $mode_texts = array_slice($args, 0, $count - 1);

    return self::applyModeTexts($mode_texts).$text.self::mode('plain');
  }

  /**
  * builds the console text to change the text mode, like bold or underline
  * 
  * @param string $mode A mode such as "bold", "underline" or "plain"
  * @return string conole text
  */
  public static function mode() {
    $mode_texts = func_get_args();
    return self::applyModeTexts($mode_texts);
  }

  protected static function applyModeTexts($mode_texts) {
    $code_text = '';
    foreach ($mode_texts as $mode) {
      $constant_name = 'self::CONSOLE_'.strtoupper($mode);
      if (defined($constant_name)) {
        $code_text .= (strlen($code_text) ? ';': '').constant($constant_name);
      }
    }
    return chr(27)."[0;".$code_text."m";
  }

}
