<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\Values;

use CLIOpts\Spec\ArgumentsSpec;
use CLIOpts\Validation\ArgsValidator;
use CLIOpts\Help\ConsoleFormat;

use \ArrayIterator;

/* 
* ArgumentValues
* 
* The parsed data from the argv array
*/
class ArgumentValues extends ArrayIterator {

  /**
   * @var ArgumentsSpec The arguments specification
   */
  protected $arguments_spec;

  /**
   * @var array data parsed by the ArgumentsParser
   */
  protected $parsed_args;

  /**
   * @var ArgsValidator the arguments validator
   */
  protected $validator;

  /**
   * @var array An associative array of named arguments and options with data
   */
  protected $merged_arg_values;


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

    // get the argument values
    $arg_values = $this->extractArgumentValuesByName($arguments_spec, $parsed_args);

    // get the option values
    $long_opts = $this->extractAllLongOpts($arguments_spec, $parsed_args);

    $this->merged_arg_values = array_merge($arg_values, $long_opts);


    parent::__construct($this->merged_arg_values);
  }


  /**
   * get the validator for these values
   * 
   * @return ArgsValidator the validator
   */
  public function getValidator() {
    if (!isset($this->validator)) {
      $this->validator = new ArgsValidator($this->arguments_spec, $this->parsed_args);
    }
    return $this->validator;
  }



  /**
   * returns the argument or option value
   * 
   * @param string $key argument name or long option name
   *
   * @return string argument or option value
   */
  public function offsetGet($key) {

    $resolved_key = $this->arguments_spec->normalizeOptionName($key);
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


  /**
   * returns true if the argument or option value exists
   * 
   * @param string $key argument name or long option name
   *
   * @return bool true if the argument or option value exists
   */
  public function offsetExists($key) {
    $resolved_key = $this->arguments_spec->normalizeOptionName($key);
    if ($resolved_key === null) {
      return isset($this->merged_arg_values[$key]);
    }

    return isset($this->merged_arg_values[$resolved_key]);
  }

  /**
   * Returns all argument values by number, even if they weren't named in the spec
   *
   * this is useful for repeating arguments
   * 
   * @return array all argument values by number
   */
  public function getAllArgumentValuesByOffset() {
    return $this->parsed_args['numbered_data'];
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * builds argument values by long option name
   * 
   * @param ArgumentsSpec $arguments_spec The arguments specification
   * @param array         $parsed_args    data parsed by the ArgumentsParser
   *
   * @return array argument values by long option name
   */
  protected function extractAllLongOpts(ArgumentsSpec $arguments_spec, $parsed_args) {
    $long_opts = array();

    foreach ($parsed_args['options'] as $option_name => $value) {
      if ($long_option_name = $arguments_spec->normalizeOptionName($option_name)) {
        $long_opts[$long_option_name] = $value;
      }
    }

    return $long_opts;
  }

  /**
   * builds argument values by argument name
   * 
   * @param ArgumentsSpec $arguments_spec The arguments specification
   * @param array         $parsed_args    data parsed by the ArgumentsParser
   *
   * @return array argument values by argument name
   */
  protected function extractArgumentValuesByName(ArgumentsSpec $arguments_spec, $parsed_args) {
    return $parsed_args['data'];
  }

}

