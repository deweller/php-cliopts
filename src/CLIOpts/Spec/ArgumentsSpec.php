<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\Spec;

use \ArrayIterator;

/* 
* ArgumentsSpec
*
* The command line arguments specification
*
* As an iterator or array accessor, this will provide the options data, but not the named arguments
*/
class ArgumentsSpec extends ArrayIterator {

  /**
   * @var arguments specification data
   */
  protected $argument_spec_data;

  /**
   * @var array options data by long and short options name
   */
  protected $option_data_by_name;

  /**
   * @var array option names mapped to their normalized name
   */
  protected $normalized_option_name_map;

  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////





  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Constructor
   * 
   * @param array $argument_spec_data arguments specification data as build by the TextSpecParser
   */
  public function __construct($argument_spec_data ) {
    $this->argument_spec_data = $argument_spec_data;

    parent::__construct($argument_spec_data['options']);
  }


  /**
   * returns true if the option expects a value
   * 
   * @param string $key A short or long option key
   *
   * @return bool true if the option expects a value
   */
  public function optionExpectsValue($key) {
    $spec_data = $this->getOptionDataByOptionKey();
    if (!isset($spec_data[$key])) { return false; }
    return strlen($spec_data[$key]['value_name']) ? true : false;
  }

  /**
   * returns true if the option exists and is required
   * 
   * @param string $key A short or long option key
   *
   * @return bool true if the option is required
   */
  public function isRequired($key) {
    $spec_data = $this->getOptionDataByOptionKey($key);
    if (!$spec_data) { return false; }
    return $spec_data['required'];
  }

  /**
   * returns the normalized option name
   * 
   * returns the long option name if it exists
   * otherwise returns the short option name
   * 
   * @param string $key A short or long option key
   *
   * @return string the normalized option name
   */
  public function normalizeOptionName($key) {
    if ($key === null) { return null; }
    $map = $this->getNormAlizedOptiOnnameMap();
    return isset($map[$key]) ? $map[$key] : null;
  }

  /**
   * gets the usage data spec
   * 
   * @return array Usage data spec
   */
  public function getUsage() {
    return $this->argument_spec_data['usage'];
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * returns options data mapped to both the long and short option name
   * 
   * @return array options data by long and short options name
   */
  protected function getOptionDataByOptionKey() {
    if (!isset($this->option_data_by_name)) {

      $this->option_data_by_name = array();
      foreach ($this->argument_spec_data['options'] as $argument_spec) {
        if (strlen($argument_spec['short'])) { $this->option_data_by_name[$argument_spec['short']] = $argument_spec; }
        if (strlen($argument_spec['long'])) { $this->option_data_by_name[$argument_spec['long']] = $argument_spec; }
      }
    }
    return $this->option_data_by_name;
  } 


  /**
   * builds option names mapped to their normalized name
   * 
   * @return array option names mapped to their normalized name
   */
  protected function getNormalizedOptionNameMap() {

    if (!isset($this->normalized_option_name_map)) {
      $this->normalized_option_name_map = array();

      foreach ($this->argument_spec_data['options'] as $argument_spec) {
        $long_name = $argument_spec['long'];
        if (strlen($long_name)) {
          $this->normalized_option_name_map[$long_name] = $long_name;
        } else {
          $long_name = null;
        }

        $short_name = $argument_spec['short'];
        if (strlen($short_name)) {
          $this->normalized_option_name_map[$short_name] = (strlen($long_name) ? $long_name : $short_name);
        }
      }
    }

    return $this->normalized_option_name_map;
  }

}

