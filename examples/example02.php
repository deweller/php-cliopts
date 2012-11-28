#!/usr/bin/env php
<?php 

# to run these examples - make sure to do this first:
# composer install -d ../


// autoload - you'll need composer (see above)
require(__DIR__.'/../vendor/autoload.php');


// specify the spec as human readable text
$cliopts = CLIOpts\CLIOpts::createFromTextSpec("
<in_filename>
-i, --id <id> specify an id (required)
-o, --out <out_filename> output filename
-v be verbose
-h, --help show this help
");


// get the values
$values = $cliopts->getOptsValues();


// check for the help switch before checking for valid values
if (isset($values['help'])) {
  $cliopts->showHelpTextAndExit();
}


// check validation.  Then generate help and exit if not valid.
if (!$values->isValid()) {
  print CLIOpts\Help\ConsoleFormat::applyformatToText('red','bold','Errors:')."\n";
  $values->showValidationErrors();
  print "\n";
  $cliopts->showHelpTextAndExit();
}


// show the values
echo "The values you supplied are:\n";
print_r((array)$values);
