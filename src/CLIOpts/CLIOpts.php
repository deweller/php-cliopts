<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts;

use CLIOpts\TextParser\TextSpecParser;
use CLIOpts\Spec\ArgumentsSpec;
use CLIOpts\ArgumentsParser\ArgumentsParser;
use CLIOpts\Values\ArgumentValues;
use CLIOpts\Help\HelpGenerator;
use CLIOpts\Help\ConsoleFormat;

/**
* CLIOpts parser
*
* @license MIT
*/
class CLIOpts {

  /**
   * @var ArgumentsSpec The definition of expected arguments and options
   */
  protected $arguments_spec = null;



  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * parse the argv data and build values
   *
   * This is an all-in-one method to check validation and show help if a help flag is specified
   *
   * @param string $arguments_spec_text a text specification of expected arguments and options
   * @param bool   $do_validation       Include validation checking
   * @param bool   $do_help             Include checking for --help flag
   *
   * @return ArgumentValues An array-like object that contains switches and data.  This method will exit on validation failure or after showing help.
   */
  public static function run($arguments_spec_text, $do_validation=true, $do_help=true) {
    $cli_opts = self::createFromTextSpec($arguments_spec_text);
    return $cli_opts->runWithValidationAndHelp($do_validation, $do_help);
  }

  /**
  * create a new CLIOpts parser from a text specification
  * 
  * @param string $arguments_spec_text a text specification of expected arguments and options
  *
  * @see TextSpecParser
  * @return CLIOpts a CLIOpts parser.
  */
  public static function createFromTextSpec($arguments_spec_text) {
    return new self(TextSpecParser::createArgumentsSpec($arguments_spec_text));
  } 

  /**
   * parse the argv data and build values
   * 
   * @param string $arguments_spec_text a text specification of expected arguments and options
   * @param array  $argv                An optional array of argv values used for testing.  Leave blank to use the default $_SERVER['argv']
   *
   * @return ArgumentValues An array-like object that contains switches and data
   */
  public static function getOpts($arguments_spec_text, $argv=null) {
    return self::createFromTextSpec($arguments_spec_text)->getOptsValues($argv);
  }


  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////
  
  /**
   * parse the argv data and build values
   *
   * This is an all-in-one method to check validation and show help if a help flag is specified.
   * 
   * @param bool   $do_validation       Include validation checking
   * @param bool   $do_help             Include checking for --help flag
   *
   * @return ArgumentValues An array-like object that contains switches and data.  This method will exit on validation failure or after showing help.
   */
  public function runWithValidationAndHelp($do_validation=true, $do_help=true) {
    // get the values
    $values = $this->getOptsValues();


    // check for the help switch before checking for valid values
    if ($do_help AND isset($values['help'])) {
      $this->showHelpTextAndExit();
      // *** script exited *** //
    }


    // check validation.  Then generate help and exit if not valid.
    if ($do_validation AND !$values->isValid()) {
      print 
        ConsoleFormat::applyformatToText(
          'bold','white','red_bg',
          'The following errors were found:'
        )."\n".
        $values->buildValidationErrorsAsText()."\n\n";

      $this->showHelpTextAndExit();
      // *** script exited *** //
    }


    return $values;
  }


  /**
   * parse the argv data and build values
   * 
   * @param array  $argv  An optional array of argv values used for testing.  Leave blank to use the default $_SERVER['argv']
   *
   * @return ArgumentValues An array-like object that contains switches and data
   */
  public function getOptsValues($argv=null) {
    $parsed_args = ArgumentsParser::parseArgvWithSpec($this->resolveArgv($argv), $this->arguments_spec);
    return new ArgumentValues($this->arguments_spec, $parsed_args);
  }


  /**
   * builds a nicely formatted help text
   * 
   * @param mixed $self_name An optional name for self.  Defaults to $_SERVER['argv'][0].
   *
   * @return string The help text
   */
  public function buildHelpText() {
    return HelpGenerator::buildHelpText($this->arguments_spec);
  }


  /**
   * prints help text and exits the script
   * 
   * @param int $exit_code exit code
   *
   * @return void
   */
  public function showHelpTextAndExit($exit_code=0) {
    print $this->buildHelpText();
    exit($exit_code);
  }
  

  /**
   * Create a new CLIOpts parser with an arguments spec object
   *
   * The easiest way to create a CLIOpts parser is with CLIOpts::createFromTextSpec
   * 
   * @param ArgumentsSpec The definition of which arguments and options are expected.
   */
  function __construct(ArgumentsSpec $arguments_spec) {
    $this->arguments_spec = $arguments_spec;
  }



//////////////////////////////////////////////////////////////////////////////////////
// Private/Protected Methods
//////////////////////////////////////////////////////////////////////////////////////

  /**
   * returns $_SERVER['argv'] if argv is null
   * 
   * @param mixed $argv array of argument values or null
   *
   * @return array argv array
   */
  protected function resolveArgv($argv=null) {
    return ($argv === null ? $_SERVER['argv'] : $argv);
  }


}

