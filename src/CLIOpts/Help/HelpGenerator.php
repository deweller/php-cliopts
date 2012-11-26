<?php

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
    if ($self_name === null) { $self_name = $_SERVER['argv'][0]; }
    $out_text = "Usage: {$self_name}\n";

    $help_lines_data = $this->getHelpLinesData();
    foreach($help_lines_data['lines'] as $help_line_data) {
      $out_text .= $this->formatHelpLine($help_line_data, $help_lines_data)."\n";
    }

    return $out_text;
  }



  //////////////////////////////////////////////////////////////////////////////////////
  // Private/Protected Methods
  //////////////////////////////////////////////////////////////////////////////////////

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

/*
    if ($argument_spec['required']) {
      $out = Console::consoleMode('bold')."[".$out.$value_description."]".Console::consoleMode('plain');
    } else {
      $out = "[".$out.$value_description."]";
    }
*/  

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
      $out = ConsoleFormat::mode('bold').$out.ConsoleFormat::mode('plain');
    }

    return $out;
  }

}

/*
  protected function _buildHelp() {
    $clo = $this->parseCommandLineOptions();

    
    $help_lines = array();
    $help_descriptions = array();
    $sort_map = array();
    $max_length = 0;
    foreach ($this->argument_specs as $offset => $argument_spec) {
      $help_line = $this->buildArgumentLineHelp($argument_spec);
      $max_length = max($max_length, strlen(preg_replace("!".chr(27)."\[0;[0-9]*m!", "", $help_line)));
      $help_lines[] = $help_line;
      $help_descriptions[] = (strlen($argument_spec['help_description']) ? "- ".$argument_spec['help_description'] : "");

      if (strlen($argument_spec['short'])) {
        $sort_map[$argument_spec['short']] = $offset;
      } else if (strlen($argument_spec['long'])) {
        $sort_map[$argument_spec['long']] = $offset;
      }
    }

    // sort by argument   
    ksort($sort_map);
    $sorted_help_lines = array();
    $sorted_help_descriptions = array();
    foreach ($sort_map as $arg => $offset) {
      $sorted_help_lines[] = $help_lines[$offset];
      $sorted_help_descriptions[] = $help_descriptions[$offset];
    }
    
    
    $padding = $max_length + 2;
    foreach ($sorted_help_lines as $offset => $help_line) {
      $length = strlen(preg_replace("!".chr(27)."\[0;[0-9]*m!", "", $help_line));
      $pad = str_repeat(" ", $padding - $length);
      $out .= "         ".$help_line.$pad.$sorted_help_descriptions[$offset]."\n";
    }

    return $out;
  }
*/  
