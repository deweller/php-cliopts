<?php

/*
 * This file is part of the CLIOpts package.
 *
 * (c) Devon Weller <dweller@devonweller.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CLIOpts\Help;

use CLIOpts\Spec\ArgumentsSpec;


/* 
* HelpGenerator
*
* Builds nicely formatted help text based on an arguments spec
*/
class HelpGenerator {

  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Builds nicely formatted help text based on the arguments spec
   * 
   * @param ArgumentsSpec $arguments_spec The arguments specification
   *
   * @return string formatted help text
   */
  public static function buildHelpText(ArgumentsSpec $arguments_spec) {
    $gen = new HelpGenerator($arguments_spec);
    return $gen->build();
  }



  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * Constructor
   * 
   * @param ArgumentsSpec $arguments_spec The arguments specification
   */
  function __construct(ArgumentsSpec $arguments_spec) {
    $this->arguments_spec = $arguments_spec;
  }



  /**
   * Builds nicely formatted help text
   * 
   * @return string formatted help text
   */
  public function build() {
    $options_data = $this->buildOptionsData();

    $out_text = 
      $this->buildUsageLine($this->arguments_spec->getUsage(), $options_data);

    $options_text = '';
    if ($options_data['lines']) {
      foreach($options_data['lines'] as $option_line_data) {
        $options_text .= $this->buildOptionLine($option_line_data, $options_data)."\n";
      }

      $out_text .= 
        "\n".
        ConsoleFormat::applyformatToText('bold','cyan','Options:')."\n".
        $options_text;
    }

    return $out_text;
  }



  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

  /**
   * builds the usage line
   * 
   * @param array $usage_data   Usage line specification data
   * @param array $options_data Options specification data
   *
   * @return string formatted usage line text
   */
  protected function buildUsageLine($usage_data, $options_data) {
    if ($usage_data['use_argv_self']) {
      $self_name = $_SERVER['argv'][0];
    } else {
      $self_name = $usage_data['self'];
    }

    $has_options = (count($options_data['lines']) > 0 ? true : false);
    $has_named_args = (count($usage_data['named_args_spec']) > 0 ? true : false);

    $out = 
      ConsoleFormat::applyformatToText('bold','cyan',"Usage:")."\n".
      "  {$self_name}".
      ($has_options ? ' [options]' : '').
      ($has_named_args ? ' '.$this->generateValueNamesHelp($usage_data['named_args_spec']) : '').
      "\n";

    return $out;
  }

  /**
   * builds an option line
   * 
   * @param array $option_line_data Option line specification data
   * @param array $options_data     All options specification data
   *
   * @return string formatted option line text
   */
  protected function buildOptionLine($option_line_data, $options_data) {
    $padding = $options_data['padding'];

    $required = $option_line_data['spec']['required'];

    $out = str_pad($option_line_data['switch_text'], $padding);
    $out .= (strlen($option_line_data['spec']['help']) ? ' '.$option_line_data['spec']['help'] : '');    
    $out .= ($required ? ' (required)' : '');

    // surround in bold if required
    if ($required) {
      $out = ConsoleFormat::applyformatToText('bold','yellow',$out);
    }

    return '  '.$out;
  }


  /**
   * gerenates the value names in the usage line
   * 
   * @param array $named_args_spec value names specifications
   *
   * @return mixed Value.
   */
  protected function generateValueNamesHelp($named_args_spec) {
    $first = true;
    $out = '';

    foreach($named_args_spec as $named_arg_spec) {
      $out .= ($first ? '' : ' ');

      if ($named_arg_spec['required']) {
        $out .= ConsoleFormat::applyformatToText('bold','yellow','<'.$named_arg_spec['name'].'>');
      } else {
        $out .= '[<'.$named_arg_spec['name'].'>]';
      }

      $first = false;
    }

    return $out;
  }

  /**
   * builds options data from the arguments spec
   * 
   * @return array Options data
   */
  protected function buildOptionsData() {
    $options_data = array(
      'lines' => array(),
      'padding' => 0,
    );

    // build the lines
    foreach($this->arguments_spec as $option_line_spec) {
      $options_data['lines'][] = $this->buildOptionLineData($option_line_spec);
    }

    // calculate padding
    $options_data['padding'] = $this->buildSwitchTextPaddingLength($options_data);

    return $options_data;
  }

  /**
   * builds a line of options data from a line of the argument spec
   * 
   * @param array $option_line_spec Data of argument specification representing an options line
   *
   * @return mixed Value.
   */
  protected function buildOptionLineData($option_line_spec) {
    $data = array();

    $switch_text = '';
    if (strlen($option_line_spec['short'])) {
      $switch_text .= "-".$option_line_spec['short'];
    }
    if (strlen($option_line_spec['long'])) {
      $switch_text .= ($switch_text ? ", " : "")."--".$option_line_spec['long'];
    }

    $data = array(
      'switch_text' => $switch_text.(strlen($option_line_spec['value_name']) ? ' <'.$option_line_spec['value_name'].'>' : ''),
      'spec'        => $option_line_spec,
    );

    return $data;
  }


  /**
   * calculates the maximum padding for all options
   * 
   * @param mixed $options_data Description.
   *
   * @return int padding length
   */
  protected function buildSwitchTextPaddingLength($options_data) {
    $padding_len = 0;
    foreach($options_data['lines'] as $option_line_data) {
      $padding_len = max($padding_len, strlen($option_line_data['switch_text']));
    }
    return $padding_len;
  }


}

