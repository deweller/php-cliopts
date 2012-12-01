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
 * Console Text Formatting Library
 * 
 * Utilities for formatting output to an ANSI console
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
   * Applies one or more console formatting codes to text
   * 
   * @param string $mode A mode like bold or black.  This can be repeated numerous times.
   * @param string $text the text to be formatted
   * 
   * @return string a formatted version of $text
   */
  public static function applyformatToText() {
    $args = func_get_args();
    $count = func_num_args();

    $text = $args[$count - 1];
    if ($count == 1) { return $text; }

    $mode_texts = array_slice($args, 0, $count - 1);

    return self::buildEscapeCodes($mode_texts).$text.self::buildEscapeCodes('plain');
  }



  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * builds ANSI escape codes
   * 
   * @param mixed $mode_texts An array of mode texts like bold or white.  Can also be a single string.
   *
   * @return string ANSI escape code to activate the modes
   */
  protected static function buildEscapeCodes($mode_texts) {
    if (!is_array($mode_texts)) { $mode_texts = array($mode_texts); }

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
