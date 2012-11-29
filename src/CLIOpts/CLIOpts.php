<?php

namespace CLIOpts;

use CLIOpts\TextParser\TextSpecParser;
use CLIOpts\ArgumentsParser\ArgumentsParser;
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
    return ArgumentsParser::parseArgvWithSpec($this->resolveArgv($argv), $this->arguments_spec);
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





}

