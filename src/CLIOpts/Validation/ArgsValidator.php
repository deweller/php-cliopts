<?php

namespace CLIOpts\Validation;

use CLIOpts\Spec\ArgumentsSpec;


/* 
* ArgsValidator
* __description__
*/
class ArgsValidator {

  protected $arguments_spec;
  protected $parsed_args;
  protected $is_valid;

  protected $errors = array();

  function __construct(ArgumentsSpec $arguments_spec, $parsed_args) {
    $this->arguments_spec = $arguments_spec;
    $this->parsed_args = $parsed_args;
  }


  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////




  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////


  public function isValid() {
    if (!isset($this->is_valid)) {
      $this->is_valid = $this->validate($this->arguments_spec, $this->parsed_args);
    }
    return $this->is_valid;
  }

  public function getErrors() {
    if (!$this->isValid()) {
      return $this->errors;
    }

    return array();
  }


  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  protected function validate($arguments_spec, $parsed_args) {
    $is_valid = true;

    // check for missing required items or argument values
    foreach($arguments_spec as $argument_spec) {
      $value_specified = (
        (
          isset($argument_spec['short']) AND strlen($argument_spec['short']) 
          AND isset($parsed_args['options'][$argument_spec['short']]) AND strlen($parsed_args['options'][$argument_spec['short']])
        ) OR (
          isset($argument_spec['long']) AND strlen($argument_spec['long']) 
          AND isset($parsed_args['options'][$argument_spec['long']]) AND strlen($parsed_args['options'][$argument_spec['long']])
        )
      );

      if ($argument_spec['required']) {
        if (!$value_specified) {
          // required, but not found
          $is_valid = false;
          $this->errors[] = "Required value for argument ".$this->longOptionName($argument_spec)." not found.";
        }
      } else if (strlen($argument_spec['value_name'])) {
        if (!$value_specified) {
          // not required, but a value name is specified and none was given
          $is_valid = false;
          $this->errors[] = "No value was specified for argument ".$this->longOptionName($argument_spec).".";
        }
      }
    }

    // find extra argument values that are not defined
    foreach ($parsed_args['options'] as $option_name => $value) {
      $resolved_option_name = $arguments_spec->resolveOptionToLongOptionName($option_name);
      if ($resolved_option_name === null) {
        $is_valid = false;
        $this->errors[] = "Unknown option ".$option_name.".";
      }
    }


    // data
    $usage_data = $arguments_spec->getUsage();

    // find required arguments
    foreach ($usage_data['value_specs'] as $offset => $value_spec) {
      if ($value_spec['required'] AND !isset($parsed_args['data'][$offset])) {
        $is_valid = false;
        $this->errors[] = "No value for <".$value_spec['name']."> was provided.";
      }
    }

    // find extra arguments
    $expected_values_count = count($usage_data['value_specs']);
    if (($data_count = count($parsed_args['numbered_data'])) > $expected_values_count) {
      $extra_count = $data_count - $expected_values_count;
      $is_valid = false;
      $this->errors[] = "Found $extra_count unexpected value".($extra_count == 1 ? '' : 's').".";
    }

    return $is_valid;
  }

  protected function longOptionName($argument_spec) {
    return strlen($argument_spec['long']) ? $argument_spec['long'] : $argument_spec['short'];
  }

}

