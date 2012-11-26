<?php

use CLIOpts\Parser\TextSpecParser;

class TextSpecParserTest extends PHPUnit_Framework_TestCase {

    public function test_ParseSpecText01() {
      $this->checkSpecTextLine(
        '-i <id> specify an id (required)',

        array(
          'short'      => 'i',
          'long'       => false,
          'value_name' => 'id',
          'help'       => 'specify an id',
          'required'   => true,
        )
      );
    }

    public function test_ParseSpecText02() {
      $this->checkSpecTextLine(
        '-i, --identifier <id> specify an id (required)',

        array(
          'short'      => 'i',
          'long'       => 'identifier',
          'value_name' => 'id',
          'help'       => 'specify an id',
          'required'   => true,
        )
      );
    }

    public function test_ParseSpecText03() {
      $this->checkSpecTextLine(
        '-i <id> specify an id',

        array(
          'short'      => 'i',
          'long'       => false,
          'value_name' => 'id',
          'help'       => 'specify an id',
          'required'   => false,
        )
      );
    }

    public function test_ParseSpecText04() {
      $this->checkSpecTextLine(
        '-l operate in list mode',

        array(
          'short'      => 'l',
          'long'       => false,
          'value_name' => false,
          'help'       => 'operate in list mode',
          'required'   => false,
        )
      );
    }

    public function test_ParseSpecText05() {
      $this->checkSpecTextLine(
        '-l, --list operate in list mode',

        array(
          'short'      => 'l',
          'long'       => 'list',
          'value_name' => false,
          'help'       => 'operate in list mode',
          'required'   => false,
        )
      );
    }

      
    public function test_ParseSpecText06() {
      $this->checkSpecTextLine(
        '--identifier <id> specify an id (required)',

        array(
          'short'      => false,
          'long'       => 'identifier',
          'value_name' => 'id',
          'help'       => 'specify an id',
          'required'   => true,
        )
      );
    }

    public function test_ParseSpecTextLines() {
      $text = <<<EOT
        -i <id> specify an id (required)
        -l, --list operate in list mode
EOT;

      $this->assertEquals(
        array(
          array(
            'short'      => 'i',
            'long'       => false,
            'value_name' => 'id',
            'help'       => 'specify an id',
            'required'   => true,
          ),
          array(
            'short'      => 'l',
            'long'       => 'list',
            'value_name' => false,
            'help'       => 'operate in list mode',
            'required'   => false,
          ),
        ),
        iterator_to_array(TextSpecParser::createArgumentsSpec($text))
      );
      
    }

  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // function

  protected function checkSpecTextLine($text, $expected_array) {
    $this->assertEquals($expected_array, TextSpecParser::createParameterSpecFromLine($text));
  }  
  

}