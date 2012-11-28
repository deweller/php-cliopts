<?php

namespace CLIOpts\Values;

use CLIOpts\Spec\ArgumentsSpec;
use CLIOpts\Validation\ArgsValidator;
use CLIOpts\Help\ConsoleFormat;

use \ArrayIterator;

/* 
* ArgumentValues
* __description__
*/
class ArgumentValues extends ArrayIterator {

  protected $arguments_spec;
  protected $parsed_args;
  protected $validator;

  protected $merged_arg_values;

  function __construct(ArgumentsSpec $arguments_spec, $parsed_args) {
    $this->arguments_spec = $arguments_spec;
    $this->parsed_args = $parsed_args;

    // build a validator
    $this->validator = new ArgsValidator($arguments_spec, $parsed_args);

    // get the argument values
    $arg_values = $this->extractArgumentValuesByName($arguments_spec, $parsed_args);

    // get the option values
    $long_opts = $this->extractAllLongOpts($arguments_spec, $parsed_args);

    $this->merged_arg_values = array_merge($arg_values, $long_opts);


    parent::__construct($this->merged_arg_values);
  }



  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////





  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////

  public function isValid() {
    return $this->validator->isValid();
  }

  public function getValidationErrors() {
    return $this->validator->getErrors();
  }

  public function showValidationErrors() {
    print ConsoleFormat::mode('red');
    print implode("\n", $this->getValidationErrors())."\n";
    print ConsoleFormat::mode('plain');
  }

  public function offsetGet($key) {

    $resolved_key = $this->arguments_spec->resolveOptionToLongOptionName($key);
    if ($resolved_key === null) {
      if (isset($this->merged_arg_values[$key])) {
        return parent::offsetGet($key);
      }
    }

    if (isset($this->merged_arg_values[$resolved_key])) {
      return parent::offsetGet($resolved_key);
    }

    return null;
  }

  public function offsetExists($key) {
    $resolved_key = $this->arguments_spec->resolveOptionToLongOptionName($key);
    if ($resolved_key === null) {
      return isset($this->merged_arg_values[$key]);
    }

    return isset($this->merged_arg_values[$resolved_key]);
  }

  public function getAllDataByOffset() {
    return $this->parsed_args['numbered_data'];
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  protected function extractAllLongOpts($arguments_spec, $parsed_args) {
    $long_opts = array();

    foreach ($parsed_args['options'] as $option_name => $value) {
      if ($long_option_name = $arguments_spec->resolveOptionToLongOptionName($option_name)) {
        $long_opts[$long_option_name] = $value;
      }
    }

    return $long_opts;
  }

  protected function extractArgumentValuesByName($arguments_spec, $parsed_args) {
    return $parsed_args['data'];
  }

}

