#!/usr/bin/env php
<?php 

# to run these examples - make sure to do this first:
# composer install -d ../


// autoload - you'll need composer (see above)
require(__DIR__.'/../vendor/autoload.php');


// specify the spec as human readable text
$values = CLIOpts\CLIOpts::createFromTextSpec("
<in_filename>
-i, --id <id> specify an id (required)
-o, --out <out_filename> output filename
-v be verbose
-h, --help show this help
")->run();

// show the values
echo "The values you supplied are:\n";
print_r((array)$values);
