<?php

use CLIOpts\CLIOpts;

class CLIOptsTest extends PHPUnit_Framework_TestCase {




  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // parse args

  public function test_getOpts01() {
    $values = $this->verifyOpts(
      '--identifier <id> specify an id (required)',

      array('script.php', '--identifier', 100),

      array('identifier' => 100,)
    );

    $this->assertEquals('100', $values['identifier']);
    $this->assertEquals(null, $values['i']);

  }
  public function test_getOpts02() {
    $values = $this->verifyOpts(
      '-i <id> specify an id (required)',

      array('script.php', '-i', 100),

      array('i' => 100,)
    );

    $this->assertEquals('100', $values['i']);
  }

  public function test_getOpts03() {
    $values = $this->verifyOpts(
      '-i, --identifier <id> specify an id (required)',

      array('script.php', '--identifier', 100),

      array('identifier' => 100,)
    );

    $this->assertEquals('100', $values['i']);
    $this->assertEquals('100', $values['identifier']);
  }

  public function test_getOpts04() {
    $values = $this->verifyOpts(
      '-i, --identifier <id> specify an id (required)',

      array('script.php', '-i', 100),

      array('identifier' => 100,)
    );

    $this->assertEquals('100', $values['i']);
    $this->assertEquals('100', $values['identifier']);
  }

  public function test_getOpts05() {
    $values = $this->verifyOpts(
      '-l, --list list mode',

      array('script.php', '-l'),

      array('list' => false,)
    );

    $this->assertEquals(false, $values['l']);
    $this->assertEquals(false, $values['list']);
  }

  public function test_getOpts06() {
    $values = $this->verifyOpts(
      '-l, --list list mode',

      array('script.php', '-l', '--other', 'data'),

      array('list' => false,)
    );

    $this->assertEquals(false, $values['l']);
    $this->assertEquals(false, $values['list']);
  }

  public function test_getOpts07() {
    $values = $this->verifyOpts(
      '-i, --identifier <id> specify an id (required)
      -l, --list list mode',

      array('script.php', '-l', '-i', '100'),

      array('identifier' => '100', 'list' => false,)
    );

    $this->assertEquals(false, $values['l']);
    $this->assertEquals(false, $values['list']);
    $this->assertEquals('100', $values['i']);
    $this->assertEquals('100', $values['identifier']);
  }

  public function test_getOpts08() {
    $values = $this->verifyOpts(
      '{self} <value1>
      -l, --list list mode',

      array('script.php', '-l', 'bar1'),

      array('value1' => 'bar1', 'list' => false,),
      array(0 => 'bar1')

    );

    $this->assertEquals(false, $values['l']);
    $this->assertEquals(false, $values['list']);
    $data = $values->getAllDataByOffset();
    $this->assertEquals('bar1', $data[0]);
    $this->assertEquals('bar1', $values['value1']);
  }

  public function test_getOpts09() {
    $values = $this->verifyOpts(
      '{self} <value1> [<value2>]
      -i, --identifier <id> specify an id (required)
      -l, --list list mode',

      array('script.php', '-i', '100', '-l', 'bar1', 'bar2', 'bar3'),

      array('identifier' => '100', 'list' => false, 'value1' => 'bar1', 'value2' => 'bar2',),

      array(0 => 'bar1', 1 => 'bar2', 2 => 'bar3')
    );

    $this->assertEquals(false, $values['l']);
    $this->assertEquals(false, $values['list']);
    $data = $values->getAllDataByOffset();
    $this->assertEquals('bar1', $data[0]);
    $this->assertEquals('bar1', $values['value1']);
  }


  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // functions

  protected function verifyOpts($text_spec, $fake_argv, $expected_values, $expected_data_by_offset=null) {
    $values = CLIOpts::getOpts($text_spec, $fake_argv);
    $this->assertEquals($expected_values, iterator_to_array($values));

    if ($expected_data_by_offset !== null) {
      $this->assertEquals($expected_data_by_offset, $values->getAllDataByOffset());
    }

    return $values;
  }


}

