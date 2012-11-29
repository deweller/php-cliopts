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
* __description__
*/
class HelpGenerator {

  function __construct(ArgumentsSpec $arguments_spec) {
    $this->arguments_spec = $arguments_spec;
    // $this->_addAcceptedArgument("h", "help", false, "show this help", $required = false);
  }


  //////////////////////////////////////////////////////////////////////////////////////
  // Class Methods
  //////////////////////////////////////////////////////////////////////////////////////

  public static function buildHelpText(ArgumentsSpec $arguments_spec, $self_name=null) {
    $gen = new HelpGenerator($arguments_spec);
    return $gen->build($self_name);
  }



  //////////////////////////////////////////////////////////////////////////////////////
  // Public Methods
  //////////////////////////////////////////////////////////////////////////////////////

  public function build($self_name=null) {
    $help_lines_data = $this->getHelpLinesData();

    $out_text = 
      $this->buildUsageLine($this->arguments_spec->getUsage(), $help_lines_data, $self_name);

    $options_text = '';
    if ($help_lines_data['lines']) {
      foreach($help_lines_data['lines'] as $help_line_data) {
        $options_text .= $this->formatHelpLine($help_line_data, $help_lines_data)."\n";
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

  protected function buildUsageLine($usage_data, $help_lines_data, $argv_self=null) {
    if ($usage_data['use_argv_self']) {
      if ($argv_self === null) {
        $self_name = $_SERVER['argv'][0];
      } else {
        $self_name = $argv_self;
      }
    } else {
      $self_name = $usage_data['self'];
    }

    $has_options = (count($help_lines_data['lines']) > 0 ? true : false);
    $has_value_specs = (count($usage_data['value_specs']) > 0 ? true : false);

    $out = 
      ConsoleFormat::applyformatToText('bold','cyan',"Usage:")."\n".
      "  {$self_name}".
      ($has_options ? ' [options]' : '').
      ($has_value_specs ? ' '.$this->generateValueNamesHelp($usage_data['value_specs']) : '').
      "\n";

    return $out;
  }

  protected function generateValueNamesHelp($value_specs) {
    $first = true;
    $out = '';

    foreach($value_specs as $value_spec) {
      $out .= ($first ? '' : ' ');

      if ($value_spec['required']) {
        $out .= ConsoleFormat::applyformatToText('bold','yellow','<'.$value_spec['name'].'>');
      } else {
        $out .= '[<'.$value_spec['name'].'>]';
      }

      $first = false;
    }

    return $out;
  }

  protected function getHelpLinesData() {
    if (!isset($this->help_lines_data)) {
      $this->help_lines_data = array(
        'lines' => array(),
        'padding' => 0,
      );

      // build the lines
      foreach($this->arguments_spec as $argument_spec) {
        $this->help_lines_data['lines'][] = $this->buildHelpLineData($argument_spec);
      }

      // calculate padding
      $this->help_lines_data['padding'] = $this->buildSwitchTextPadding($this->help_lines_data);
    }

    return $this->help_lines_data;
  }

  protected function buildHelpLineData($argument_spec) {
    $data = array();

    $switch_text = '';
    if (strlen($argument_spec['short'])) {
      $switch_text .= "-".$argument_spec['short'];
    }
    if (strlen($argument_spec['long'])) {
      $switch_text .= ($switch_text ? ", " : "")."--".$argument_spec['long'];
    }

    $data = array(
      'switch_text' => $switch_text.(strlen($argument_spec['value_name']) ? ' <'.$argument_spec['value_name'].'>' : ''),
      'spec'        => $argument_spec,
      // 'sort_by'     => (strlen($argument_spec['long']) ? $argument_spec['long'] : $argument_spec['short']),
    );

    return $data;
  }


  protected function buildSwitchTextPadding($help_lines_data) {
    $padding_len = 0;
    foreach($help_lines_data['lines'] as $help_line_data) {
      $padding_len = max($padding_len, strlen($help_line_data['switch_text']));
    }
    return $padding_len;
  }

  protected function formatHelpLine($help_line_data, $help_lines_data) {
    $padding = $help_lines_data['padding'];

    $required = $help_line_data['spec']['required'];

    $out = str_pad($help_line_data['switch_text'], $padding);
    $out .= (strlen($help_line_data['spec']['help']) ? ' '.$help_line_data['spec']['help'] : '');    
    $out .= ($required ? ' (required)' : '');

    // surround in bold if required
    if ($required) {
      $out = ConsoleFormat::applyformatToText('bold','yellow',$out);
    }

    return '  '.$out;
  }

}

