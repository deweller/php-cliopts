<?php

use CLIOpts\CLIOpts;

class CLIOptsValidationTest extends PHPUnit_Framework_TestCase {




  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // parse args

  public function test_validateOptions01() {
    $this->validateOptsValidationFails(
      '-i <id> specify an id (required)',

      array('script.php', '-i'),

      'Required value for argument i not found'
    );
  }

  public function test_validateOptions02() {
    $this->validateOptsValidationFails(
      '--identifier <id> specify an id (required)',

      array('script.php', '--identifier'),

      'Required value for argument identifier not found'
    );
  }

  public function test_validateOptions03() {
    $this->validateOptsValidationFails(
      '-i, --identifier <id> specify an id (required)',

      array('script.php', '--identifier'),

      'Required value for argument identifier not found'
    );
  }

  public function test_validateOptions04() {
    $this->validateOptsValidationFails(
      '-i, --identifier <id> specify an id (required)',

      array('script.php', '-i'),

      'Required value for argument identifier not found'
    );
  }

  public function test_validateOptions05() {
    $this->validateOptsValidationSucceeds(
      '-i, --identifier <id> specify an id',
      array('script.php', '-i', '10')
    );
    $this->validateOptsValidationSucceeds(
      '-i, --identifier <id> specify an id (required)',
      array('script.php', '-i', '10')
    );
  }

  public function test_validateOptions06() {
    $this->validateOptsValidationSucceeds(
      '-i, --identifier <id> specify an id',
      array('script.php', '--identifier', '10')
    );
    $this->validateOptsValidationSucceeds(
      '-i, --identifier <id> specify an id (required)',
      array('script.php', '--identifier', '10')
    );
  }

  public function test_validateOptions07() {
    $this->validateOptsValidationFails(
      '-i, --identifier <id> specify an id',
      array('script.php', '-i'),
      'No value was specified for argument identifier'
    );
  }

  public function test_validateFindExtraOptions() {
    $this->validateOptsValidationFails(
      '-i, --identifier <id> specify an id',
      array('script.php', '-i', '100', '-x'),
      'Unknown option x'
    );
    $this->validateOptsValidationFails(
      '-i, --identifier <id> specify an id',
      array('script.php', '-i', '100', '-x', 'extra'),
      'Unknown option x'
    );
  }





  ////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////
  // validateOptsValidationFails
  

  protected function validateOptsValidationFails($text_spec, $fake_argv, $expected_error_result) {
    $values = CLIOpts::getOpts($text_spec, $fake_argv);
    $this->assertFalse($values->isValid(), 'Validation was not false');

    $errors = $values->getValidationErrors();
    $this->assertGreaterThan(0, count($errors), "No expected errors found.");
    $this->assertRegExp('/'.$expected_error_result.'/', implode("\n", $errors));

    return $values;
  }

  protected function validateOptsValidationSucceeds($text_spec, $fake_argv) {
    $values = CLIOpts::getOpts($text_spec, $fake_argv);
    $this->assertTrue($values->isValid(), 'Validation was false');
    return $values;
  }

 }

