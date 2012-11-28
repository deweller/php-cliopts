<?php

use CLIOpts\CLIOpts;
use CLIOpts\Help\ConsoleFormat;

class BuildHelpTest extends PHPUnit_Framework_TestCase {




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


  public function test_validateHelpStringWithoutDescription() {
    $this->validateHelpString(
      "-i <id>",

      "-i <id>"
    );

    $this->validateHelpString(
      "-i <id> (required)",

      "-i <id> (required)"
    );

  }



  public function test_validateSelfName() {
    $this->validateHelpString(
      "Usage: customScriptName [options] <url>
      -i <id> (required)",

      "Usage:\ncustomScriptName [options] <url>\n\n".
      "Options:\n".
      "-i <id> (required)"
    );
  }


  public function test_validateHelpStringWithValueNames() {
    $this->validateHelpString(
      "Usage: {self} [options] <url>
      -i <id> (required)",

      "Usage:\n./script.php [options] <url>\n\n".
      "Options:\n".
      "-i <id> (required)"
    );
    $this->validateHelpString(
      "Usage: {self} [options] <url> <url2>
      -i <id> (required)",

      "Usage:\n./script.php [options] <url> <url2>\n\n".
      "Options:\n".
      "-i <id> (required)"
    );
  }

  public function test_validateHelpStringWithNoOptions() {
    $this->validateHelpString(
      "Usage: {self} <url>",

      "Usage:\n./script.php <url>"
    );
    $this->validateHelpString(
      "Usage: {self} <url> <url 2>",

      "Usage:\n./script.php <url> <url 2>"
    );
  }

  public function test_validateOptionalValueNames() {
    $this->validateHelpString(
      "Usage: {self} <url> [<url2>]",

      "Usage:\n./script.php <url> [<url2>]"
    );
  }


  public function test_noSelfProvided() {
    $this->validateHelpString(
      "<url> [<url2>]",

      "Usage:\n./script.php <url> [<url2>]"
    );
  }



  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // validateOptsValidationFails
  

  protected function validateHelpString($text_spec, $expected_help_text=null) {
    $cli_opts = CLIOpts::createFromTextSpec($text_spec);
    if ($expected_help_text === null) {
      $expected_help_text = "Usage:\n./script.php [options]\n\nOptions:\n".trim($text_spec);
    } else {
      $expected_help_text = trim($expected_help_text);
      if (substr($expected_help_text, 0, 6) != 'Usage:') {
        $expected_help_text = "Usage:\n./script.php [options]\n\nOptions:\n".$expected_help_text;
      }
    }

    $actual_help_text = $cli_opts->buildHelpText('./script.php');
    $actual_help_text = preg_replace('!\x1b\[0;.*?m!', '', $actual_help_text);
    $actual_help_text = str_replace("\n  ","\n", $actual_help_text);

    $this->assertEquals(trim($expected_help_text), trim($actual_help_text));
  }

}

