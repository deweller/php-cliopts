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
* __description__
*/
class ArgumentsSpec extends ArrayIterator {

  protected $argument_spec_data;
  protected $spec_data_by_name = null;

  public function __construct($argument_spec_data ) {
    $this->argument_spec_data = $argument_spec_data;

    parent::__construct($argument_spec_data['options']);
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////





  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////

  public function expectsValue($key) {
    $spec_data = $this->getSpecDataByName();
    if (!isset($spec_data[$key])) { return false; }
    return strlen($spec_data[$key]['value_name']) ? true : false;
  }

  public function isRequired($key) {
    $spec_data = $this->getSpecDataByName($key);
    if (!$spec_data) { return false; }
    return $spec_data['required'];
  }

  public function resolveOptionToLongOptionName($key) {
    if ($key === null) { return null; }
    $map = $this->getLongOptionNameMap();
    return isset($map[$key]) ? $map[$key] : null;
  }

  public function getUsage() {
    return $this->argument_spec_data['usage'];
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  protected function getSpecDataByName() {
    if (!isset($this->spec_data_by_name)) {

      $this->spec_data_by_name = array();
      foreach ($this->argument_spec_data['options'] as $argument_spec) {
        if (strlen($argument_spec['short'])) { $this->spec_data_by_name[$argument_spec['short']] = $argument_spec; }
        if (strlen($argument_spec['long'])) { $this->spec_data_by_name[$argument_spec['long']] = $argument_spec; }
      }
    }
    return $this->spec_data_by_name;
  } 


  protected function getLongOptionNameMap() {

    if (!isset($this->long_option_name_map)) {
      $this->long_option_name_map = array();

      foreach ($this->argument_spec_data['options'] as $argument_spec) {
        $long_name = $argument_spec['long'];
        if (strlen($long_name)) {
          $this->long_option_name_map[$long_name] = $long_name;
        } else {
          $long_name = null;
        }

        $short_name = $argument_spec['short'];
        if (strlen($short_name)) {
          $this->long_option_name_map[$short_name] = (strlen($long_name) ? $long_name : $short_name);
        }
      }
    }

    return $this->long_option_name_map;
  }

}

