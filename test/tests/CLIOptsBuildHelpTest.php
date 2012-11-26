<?php

use CLIOpts\CLIOpts;
use CLIOpts\Help\ConsoleFormat;

class CLIOptsBuildHelpTest extends PHPUnit_Framework_TestCase {




  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // parse args

  public function test_validateHelpString() {
    $this->validateHelpString(
      '-i <id> specify an id'
    );
    $this->validateHelpString(
      '-i, --identifier <id> specify an id'
    );
    $this->validateHelpString(
      '-i, --identifier <id> specify an id (required)'
    );
  }

  public function test_validateHelpStringWithPadding() {
    $this->validateHelpString(
      "-i <id> specify an id\n".
      "-l list mode",

      "-i <id> specify an id\n".
      "-l      list mode"
    );

    $this->validateHelpString(
      "-i <id> specify an id (required)\n".
      "-l list mode",

      "-i <id> specify an id (required)\n".
      "-l      list mode"
    );
  
    $this->validateHelpString(
      "-i, --identifier <id> specify an id (required)\n".
      "-l list mode",

      "-i, --identifier <id> specify an id (required)\n".
      "-l                    list mode"
    );

  }





  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // validateOptsValidationFails
  

  protected function validateHelpString($text_spec, $expected_help_text=null) {
    $cli_opts = CLIOpts::createFromTextSpec($text_spec);
    if ($expected_help_text === null) {
      $expected_help_text = "Usage: ./script.php\n".trim($text_spec);
    } else {
      $expected_help_text = "Usage: ./script.php\n".trim($expected_help_text);
    }

    $actual_help_text = $cli_opts->buildHelpText('./script.php');
    $actual_help_text = str_replace(array(ConsoleFormat::mode('bold'), ConsoleFormat::mode('plain')), '', $actual_help_text);

    $this->assertEquals(trim($expected_help_text), trim($actual_help_text));
  }

}

