<?php

namespace CLIOpts\Values;

use CLIOpts\Spec\ArgumentsSpec;
use CLIOpts\Validation\ArgsValidator;

use \ArrayIterator;

/* 
* ArgumentValues
* __description__
*/
class ArgumentValues extends ArrayIterator {

  protected $arguments_spec;
  protected $parsed_args;
  protected $validator;

  function __construct(ArgumentsSpec $arguments_spec, $parsed_args) {
    $this->arguments_spec = $arguments_spec;
    $this->parsed_args = $parsed_args;

    // build a validator
    $this->validator = new ArgsValidator($arguments_spec, $parsed_args);

    // get the argument values
    $long_opts = $this->extractAllLongOpts($arguments_spec, $parsed_args);

    parent::__construct($long_opts);
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

  public function offsetGet($key) {
    $resolved_key = $this->arguments_spec->resolveOptionToLongOptionName($key);
    if ($resolved_key === null) { return false; }
    return parent::offsetGet($resolved_key);
  }

  public function offsetExists($key) {
    $resolved_key = $this->arguments_spec->resolveOptionToLongOptionName($key);
    if ($resolved_key === null) { return false; }
    return parent::offsetExists($resolved_key);
  }

  public function getData($key) {
    if (isset($this->parsed_args['data'][$key])) {
      return $this->parsed_args['data'][$key];
    }
    if (is_int($key) AND isset($this->parsed_args['numbered_data'][$key])) {
      return $this->parsed_args['numbered_data'][$key];
    }
    return null;
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

}

