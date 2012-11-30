#!/usr/bin/env php
<?php 

# to run these examples - make sure to do this first:
# composer install -d ../

// autoload
if (file_exists(__DIR__.'/../vendor/autoload.php')) {
  require(__DIR__.'/../vendor/autoload.php');
} else {
  echo "To run this example, do this first:\ncomposer install -d ../\n";
  exit(1);
}




// specify the spec as human readable text and run validation and help:
$values = CLIOpts\CLIOpts::run("
  Usage: <in_filename>
  -i, --id <id> specify an id (required)
  -o, --out <out_filename> output filename
  -v be verbose
  -h, --help show this help
");


// show the values
echo "The values you supplied are:\n";
print_r((array)$values);
