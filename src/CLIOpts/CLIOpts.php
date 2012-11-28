<?php

namespace CLIOpts;

use CLIOpts\Parser\TextSpecParser;
use CLIOpts\Help\ConsoleFormat;
use CLIOpts\Help\HelpGenerator;
use CLIOpts\Spec\ArgumentsSpec;
use CLIOpts\Values\ArgumentValues;

class CLIOpts {

  protected $arguments_spec = null;

  function __construct(ArgumentsSpec $arguments_spec) {
    $this->arguments_spec = $arguments_spec;
    // $this->_addAcceptedArgument("h", "help", false, "show this help", $required = false);
  }




  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
  * createFromTextSpec
  * 
  * @param mixed $arguments_spec_text Description.
  *
  * @return mixed Value.
  */
  public static function createFromTextSpec($arguments_spec_text) {
    return self::createFromArgumentsSpec(TextSpecParser::createArgumentsSpec($arguments_spec_text));
  } 


  public static function createFromArgumentsSpec(ArgumentsSpec $arguments_spec) {
    return new CLIOpts($arguments_spec);
  }

  /**
  * returns an associative array of switch => data
  * @return array associative array
  */
  public static function getOpts($arguments_spec_text, $argv=null) {
    return
      self::createFromArgumentsSpec(TextSpecParser::createArgumentsSpec($arguments_spec_text))
      ->getOptsValues($argv);
  }



//////////////////////////////////////////////////////////////////////////////////////
// Public Methods
//////////////////////////////////////////////////////////////////////////////////////
  
  public function getOptsValues($argv=null) {
    $parsed_args = $this->parseArgv($this->resolveArgv($argv));
    return new ArgumentValues($this->arguments_spec, $parsed_args);
  }


  public function parseArgv($argv=null) {
    return $this->parseArgvWithSpec($this->resolveArgv($argv), $this->arguments_spec);
  }


  /**
  * builds help text
  * @return string the help text
  */
  public function buildHelpText($self_name=null) {
    return HelpGenerator::buildHelpText($this->arguments_spec, $self_name);
  }

  /**
  * shows help text and exits
  * @return void
  */
  public function showHelpTextAndExit() {
    print $this->buildHelpText();
    exit(0);
  }
  

  public function run($with_validation=true, $with_help=true) {
    // get the values
    $values = $this->getOptsValues();


    // check for the help switch before checking for valid values
    if ($with_help AND isset($values['help'])) {
      $this->showHelpTextAndExit();
    }


    // check validation.  Then generate help and exit if not valid.
    if (!$values->isValid()) {
      print ConsoleFormat::applyformatToText('red','bold','Errors:')."\n";
      $values->showValidationErrors();
      print "\n";
      $this->showHelpTextAndExit();
    }

    return $values;
  }


//////////////////////////////////////////////////////////////////////////////////////
// Private/Protected Methods
//////////////////////////////////////////////////////////////////////////////////////

  protected function resolveArgv($argv=null) {
    return ($argv === null ? $_SERVER['argv'] : $argv);
  }

  /**
  * parses command unix-style command line options from an array
  *
  * returns an array of three elements:
  *   "self" - The args[0] entry
  *   "switches" - an associative array of switches to data.  Switches have dashes removed  
  *   "data" - Any data not preceeded by a switch as a numbered array
  *
  * @param array $argv A numbered array of command switches.  Uses $_SERVER['argv'] by default
  * @return array A hash of command switches to values. 
  */
  protected function parseArgvWithSpec($argv, $arguments_spec) {
    $data_offset_count = 0;
    $usage = $arguments_spec->getUsage();

    // start with self
    $args_out = array(
      'self'          => $argv[0],
      'options'       => array(),
      'data'          => array(),
      'numbered_data' => array(),
    );
  
    $count = count($argv);
    $i = 1;
    while($i<$count) {
      $token = $argv[$i];

      // build the switch key
      $key = false;
      if (substr($token, 0, 2) == '--') {
        $key = substr($token, 2);
      } else if (substr($token, 0, 1) == '-') {
        $key = substr($token, 1);
      }

      // build the data
      $data = null;
      if (strlen($key)) {
        // key exists
        if ($arguments_spec->expectsValue($key)) {
          // we are expecting a value, so read the next token as data
          $next_token = isset($argv[$i + 1]) ? $argv[$i + 1] : null;

          if ($next_token !== null AND substr($next_token, 0, 1) != '-') {
            $data = $next_token;
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
        $data = $token;
      }

      if (strlen($key)) {
        // assign the data to a switch
        $args_out['options'][$key] = $data;
        $i = $i + ($data === false ? 1 : 2);
      } else {
        // no key - this is data without a switch
        $args_out['numbered_data'][$data_offset_count] = $data;

        if (isset($usage['value_specs'][$data_offset_count])) {  
          $args_out['data'][$usage['value_specs'][$data_offset_count]['name']] = $data;
        }

        ++$data_offset_count;
        ++$i;
      }

    }
    
    return $args_out;
  }




}

