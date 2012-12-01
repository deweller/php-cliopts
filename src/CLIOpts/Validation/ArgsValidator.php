<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\Validation;

use CLIOpts\Spec\ArgumentsSpec;


/* 
* ArgsValidator
*
* Validates missing arguments
*/
class ArgsValidator {

  /**
   * @var ArgumentsSpec The arguments specification
   */
  protected $arguments_spec;

  /**
   * @var array data parsed by the ArgumentsParser
   */
  protected $parsed_args;

  /**
   * @var bool if the arguments are valid
   */
  protected $is_valid;

  /**
   * @var array an array of error text strings
   */
  protected $errors = array();


  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////




  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Constructor
   * 
   * @param ArgumentsSpec $arguments_spec The arguments specification
   * @param array         $parsed_args    data parsed by the ArgumentsParser
   */
  function __construct(ArgumentsSpec $arguments_spec, $parsed_args) {
    $this->arguments_spec = $arguments_spec;
    $this->parsed_args = $parsed_args;
  }



  /**
   * validates the arguments
   * 
   * @return bool true if the arguments are valid according to the spec
   */
  public function isValid() {
    if (!isset($this->is_valid)) {
      $this->is_valid = $this->validate($this->arguments_spec, $this->parsed_args);
    }
    return $this->is_valid;
  }

  /**
   * returns the errors if not valid
   * 
   * @return array an array of error text strings or an empty array if valid
   */
  public function getErrors() {
    if (!$this->isValid()) {
      return $this->errors;
    }

    return array();
  }


  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * validates if the arguments are valid according to the spec
   * 
   * @param ArgumentsSpec $arguments_spec The arguments specification
   * @param array         $parsed_args    data parsed by the ArgumentsParser
   *
   * @return bool true if the arguments are valid according to the spec
   */
  protected function validate(ArgumentsSpec $arguments_spec, $parsed_args) {
    $is_valid = true;

    // check for missing required items or argument values
    foreach($arguments_spec as $option_spec) {
      $value_specified = (
        (
          isset($option_spec['short']) AND strlen($option_spec['short']) 
          AND isset($parsed_args['options'][$option_spec['short']]) AND strlen($parsed_args['options'][$option_spec['short']])
        ) OR (
          isset($option_spec['long']) AND strlen($option_spec['long']) 
          AND isset($parsed_args['options'][$option_spec['long']]) AND strlen($parsed_args['options'][$option_spec['long']])
        )
      );

      if ($option_spec['required']) {
        if (!$value_specified) {
          // required, but not found
          $is_valid = false;
          $this->errors[] = "Required value for argument ".$this->longOptionName($option_spec)." not found.";
        }
      } else if (strlen($option_spec['value_name'])) {
        if (!$value_specified) {
          $switch_was_sepcified = (
            isset($parsed_args['options'][$option_spec['short']]) AND isset($parsed_args['options'][$option_spec['short']])
            OR isset($parsed_args['options'][$option_spec['long']]) AND isset($parsed_args['options'][$option_spec['long']])
          );

          if ($switch_was_sepcified) {
            // not required, but a value name is specified and none was given
            $is_valid = false;
            $this->errors[] = "No value was specified for argument ".$this->longOptionName($option_spec).".";
          }
        }
      }
    }

    // find extra argument values that are not defined
    foreach ($parsed_args['options'] as $option_name => $value) {
      $resolved_option_name = $arguments_spec->normalizeOptionName($option_name);
      if ($resolved_option_name === null) {
        $is_valid = false;
        $this->errors[] = "Unknown option ".$option_name.".";
      }
    }


    // data
    $usage_data = $arguments_spec->getUsage();

    // find required arguments
    foreach ($usage_data['named_args_spec'] as $offset => $named_arg_spec) {
      if ($named_arg_spec['required'] AND !isset($parsed_args['numbered_data'][$offset])) {
        $is_valid = false;
        $this->errors[] = "No value for <".$named_arg_spec['name']."> was provided.";
      }
    }

    // find extra arguments
    $expected_values_count = count($usage_data['named_args_spec']);
    if (($data_count = count($parsed_args['numbered_data'])) > $expected_values_count) {
      $extra_count = $data_count - $expected_values_count;
      $is_valid = false;
      $this->errors[] = "Found $extra_count unexpected value".($extra_count == 1 ? '' : 's').".";
    }

    return $is_valid;
  }

  /**
   * builds the long option name (if specified) for the error message.  If not, then returns the short option name.
   * 
   * @param array $option_spec Option specification data
   *
   * @return string long or short option name
   */
  protected function longOptionName($option_spec) {
    return strlen($option_spec['long']) ? $option_spec['long'] : $option_spec['short'];
  }

}

