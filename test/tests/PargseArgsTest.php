<?php

use CLIOpts\CLIOpts;
use CLIOpts\ArgumentsParser\ArgumentsParser;
use CLIOpts\TextParser\TextSpecParser;
use CLIOpts\Spec\ArgumentsSpec;

class PargseArgsTest extends PHPUnit_Framework_TestCase {


  public function testParseArgs01() {
    $this->verifyParsedArgs(
      '-i <id> specify an id (required)',

      array('script.php', '-i', '100'),

      array(
        'self'          => 'script.php',
        'options'       => array('i' => '100'),
        'data'          => array(),
        'numbered_data' => array(),
      )
    );
  }

  public function testParseArgs02() {
    $this->verifyParsedArgs(
      '-i <id> specify an id (required)',

      array('script.php', '-i', '100', 'randomData'),

      array(
        'self'          => 'script.php',
        'options'       => array('i' => '100'),
        'data'          => array(),
        'numbered_data' => array('randomData'),
      )
    );
  }

  public function testParseArgs03() {
    $this->verifyParsedArgs(
      '-i, --identifier <id> specify an id (required)',

      array('script.php', '--identifier', '100', 'randomData'),

      array(
        'self'          => 'script.php',
        'options'       => array('identifier' => '100'),
        'data'          => array(),
        'numbered_data' => array('randomData'),
      )
    );
  }

  public function testParseArgs04() {
    $this->verifyParsedArgs(
      '-i, --identifier <id> specify an id (required)
      -l list mode
      ',

      array('script.php', '-l', '-i', '100'),

      array(
        'self'          => 'script.php',
        'options'       => array('i' => '100', 'l' => false),
        'data'          => array(),
        'numbered_data' => array(),
      )
    );
  }

  public function testParseArgs05() {
    $this->verifyParsedArgs(
      '-i, --identifier <id> specify an id (required)
      -l list mode
      ',

      array('script.php', '-l', 'middle_data', '-i', '100'),

      array(
        'self'          => 'script.php',
        'options'       => array('i' => '100', 'l' => false),
        'data'          => array(),
        'numbered_data' => array('middle_data'),
      )
    );
  }

  public function testParseArgs06() {
    $this->verifyParsedArgs(
      '--identifier <id> specify an id (required)',

      array('script.php', '-i', '100'),

      array(
        'self'          => 'script.php',
        'options'       => array('i' => false),
        'data'          => array(),
        'numbered_data' => array('100'),
      )
    );
  }


  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // parse args

  protected function verifyParsedArgs($text_spec, $fake_argv, $expected_result) {
    $arguments_spec = TextSpecParser::createArgumentsSpec($text_spec);
    $parsed_args = ArgumentsParser::parseArgvWithSpec($fake_argv, $arguments_spec);
    $this->assertEquals($expected_result, $parsed_args);
  }


 }

