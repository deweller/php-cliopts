<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\TextParser;

use CLIOpts\Spec\ArgumentsSpec;

use \Exception;

/* 
* TextSpecParser
* __description__
*/
class TextSpecParser {



  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  public static function createArgumentsSpec($text) {
    $argument_spec_data = array(
      'usage'   => self::defaultUsageData(),
      'options' => array(),
    );

    $is_first_line = true;
    $lines = explode("\n", trim($text));
    foreach($lines as $line) {
      $line = trim($line);
      if (!strlen($line)) { continue; }

      if ($is_first_line) {
        $is_first_line = false;

        $usage_line_data = self::parseUsageLine($line);
        if ($usage_line_data) {
          $argument_spec_data['usage'] = $usage_line_data;
          continue;
        }
      }

      if ($argument_spec = self::createParameterSpecFromLine($line)) {
        $argument_spec_data['options'][] = $argument_spec;
      }

    }

    return new ArgumentsSpec($argument_spec_data);
  }


  public static function createParameterSpecFromLine($line) {
    // --identifier <id> specify an id (required)

    $regex = (
      '/^'.
      '(?:-([A-z0-9]))?'.             // short param
      ',? ?'.                         // spacer
      '(?:--([A-z0-9][A-z0-9_-]+))?'. // long param
      '(?: <([^>]+)>)?'.              // value
      '(?: (.*?))?'.                  // help text
      '(?: (\(required\)))?'.         // required
      '$/'
    );
    $matched = preg_match($regex, $line, $matches);

    // make sure the line is valid
    if (!$matched) {
      throw new Exception("The line $line was not valid");
    }

    $out = array(
      'short'      => $matches[1],
      'long'       => $matches[2],
      'value_name' => $matches[3],
      'help'       => isset($matches[4]) ? $matches[4] : '',
      'required'   => isset($matches[5]) ? true : false,
    );
    return $out;
  }

  public static function parseUsageLine($line) {
    $regex = (
      '/^'.
      '(?:Usage:)?'.              // Usage:
      '(?: ?(\{?[a-z_\.\/]+\}?))?'. // self name
      '(?: ?\[options\]?)?'.      // options
      '((?: ?\[?<[^>]+>\]?)+)?'.   // values
      '$/i'
    );
    $matched = preg_match($regex, $line, $matches);

    // if this is not a usage line, just return false
    if (!$matched) { return false; }

    $value_specs = array();
    if (isset($matches[2])) {
      $regex = (
        '/'.
        ' ?'.              // leading space
        '(?|'.             // assign 1 of the following 2 groups to reference 1
        '(\[<([^>]+)>\])'. // with brackets
        '|'.               // or
        '(<([^>]+)>)'.     // without brackets
        ')'.
        '/'
      );
      $matched = preg_match_all($regex, $matches[2], $all_value_name_matches, PREG_SET_ORDER);
      if (!$matched) { throw new Exception("The usage line $line was not valid.", 1); }

      foreach ($all_value_name_matches as $match) {
        $value_specs[] = array(
          'name'     => $match[2],
          'required' => (substr($match[1], 0, 1) != '['),
        );
      }
    }


    $out = array(
      'use_argv_self' => (($matches[1] === '{self}' OR $matches[1] === '') ? true : false),
      'self'          => $matches[1],
      'value_specs'   => $value_specs,
    );
    return $out;
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////





  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  protected static function defaultUsageData() {
    return  array(
      'use_argv_self' => true,
      'self'          => null,
      'value_specs'   => array(),
    );
  }

}

