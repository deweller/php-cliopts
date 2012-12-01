<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\ArgumentsParser;

use CLIOpts\Spec\ArgumentsSpec;


/* 
* ArgumentsParser
* 
* Parses command line options from an argv array
*/
class ArgumentsParser {

  const TOKEN_SELF   = 0;
  const TOKEN_OPTION = 1;
  const TOKEN_DATA   = 2;

  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Parses command line options from an argv array
   *
   * returns an array of three elements:
   *   "self" - The args[0] entry
   *   "options" - an associative array of options to data.  options have dashes removed  
   *   "data" - Argument data not belonging to a switch as a numbered array
   *   "numbered_data" - Argument data not belonging to a switch as an associative array by argument name
   * 
   * @param array $argv A numbered array of argv data like $_SERVER['argv']
   * @param ArgumentsSpec $arguments_spec The arguments specification
   *
   * @return array An array or parsed argument data
   */
  public static function parseArgvWithSpec($argv, ArgumentsSpec $arguments_spec) {
    $data_offset_count = 0;
    $usage = $arguments_spec->getUsage();

    // start with self
    $args_data_out = array(
      'self'          => $argv[0],
      'options'       => array(),
      'data'          => array(),
      'numbered_data' => array(),
    );

    // normalize argv
    $argv_tokens = self::tokenizeArgvData($argv, $arguments_spec);

    foreach($argv_tokens as $argv_token) {
      switch ($argv_token['type']) {
        case self::TOKEN_OPTION:
          $args_data_out['options'][$argv_token['key']] = $argv_token['value'];
          break;

        case self::TOKEN_DATA:
          $args_data_out['numbered_data'][$argv_token['offset']] = $argv_token['value'];
          if ($argv_token['key'] !== null) {  
            $args_data_out['data'][$argv_token['key']] = $argv_token['value'];
          }
          break;
      }
    }

    return $args_data_out;


  }


  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Parses command line options from an argv array into tokenized data
   *
   * This method processes options like -abc foo as if they were -a -b -c foo
   * This method also handles --foo=bar the same as -foo bar
   * 
   * @param mixed $argv_in        A numbered array of argv data like $_SERVER['argv']
   * @param ArgumentsSpec $arguments_spec The arguments specification
   *
   * @return array tokenized data
   */
  protected static function tokenizeArgvData($argv_in, ArgumentsSpec $arguments_spec) {
    $data_offset_count = 0;
    $usage = $arguments_spec->getUsage();

    $argv_tokens_out = array(array(
      'type'  => self::TOKEN_SELF,
      'value' => $argv_in[0],
    ));


    $token_offset = 1;
    $count = count($argv_in);
    while($token_offset < $count) {
      $token_text = $argv_in[$token_offset];
      $tokens_read = 1;

      // build the switch key
      $key = false;
      $is_single_flag = false;
      if (substr($token_text, 0, 2) == '--') {
        $key = substr($token_text, 2);
      } else if (substr($token_text, 0, 1) == '-') {
        $key = substr($token_text, 1);
        $is_single_flag = true;
      }

      // build the data
      $data = null;
      if (strlen($key)) {
        // key exists

        // handle multiple flags on a single switch like -abc
        if ($is_single_flag AND ($key_len = strlen($key)) > 1) {
          for ($flag_offset=0; $flag_offset < $key_len - 1; $flag_offset++) {
            if (substr($key, $flag_offset + 1, 1) == '=') { break; }

            $argv_tokens_out[] = array(
              'type'  => self::TOKEN_OPTION,
              'key'   => substr($key, $flag_offset, 1),
              'value' => false,
            );
          }

          $key = substr($key, $flag_offset);
        }


        // try to parse --foo=bar
        if (preg_match('/^(.+?)=(.+)$/', $key, $matches)) {
          $key = $matches[1];
          $data = $matches[2];
        } else if ($arguments_spec->optionExpectsValue($key)) {
          // we are expecting a value, so read the next token as data
          $next_token = isset($argv_in[$token_offset + 1]) ? $argv_in[$token_offset + 1] : null;

          if ($next_token !== null AND substr($next_token, 0, 1) != '-') {
            $data = $next_token;

            // this is the only situation in which we read ahead another token
            ++$tokens_read;
          } else {
            // did not find data for this switch even though we were expecting it
            $data = false;
          }
        } else {
          // no value expected
          $data = false;
        }
      } else {
        // no key
        $data = $token_text;
      }

      if (strlen($key)) {
        $argv_tokens_out[] = array(
          'type'  => self::TOKEN_OPTION,
          'key'   => $key,
          'value' => $data,
        );
      } else {
        $argv_tokens_out[] = array(
          'type'   => self::TOKEN_DATA,
          'offset' => $data_offset_count,
          'key'    => isset($usage['named_args_spec'][$data_offset_count]) ? $usage['named_args_spec'][$data_offset_count]['name'] : null,
          'value'  => $data,
        );

        ++$data_offset_count;
      }

      $token_offset += $tokens_read;

    }
    
    return $argv_tokens_out;
  }

}

?>