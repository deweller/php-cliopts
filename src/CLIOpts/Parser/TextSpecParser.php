<?php

namespace CLIOpts\Parser;

use CLIOpts\Spec\ArgumentsSpec;

/* 
* TextSpecParser
* __description__
*/
class TextSpecParser {



  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  public static function createArgumentsSpec($text) {
    $argument_spec_data = array();

    $lines = explode("\n", $text);
    foreach($lines as $line) {
      $line = trim($line);
      if (!strlen($line)) { continue; }

      if ($argument_spec = self::createParameterSpecFromLine($line)) {
        $argument_spec_data[] = $argument_spec;
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

    $out = array(
      'short'      => $matches[1],
      'long'       => $matches[2],
      'value_name' => $matches[3],
      'help'       => $matches[4],
      'required'   => $matches[5] ? true : false,
    );
    return $out;
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////





  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////



}

