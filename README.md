php-cliopts [![Build Status](https://secure.travis-ci.org/deweller/php-cliopts.png)](https://secure.travis-ci.org/#!/deweller/php-cliopts)
===========

A no-nonsense command-line options parser and help generator for PHP CLI apps.


Features
------------

- Parses $argv data into an associative array
- Simple, human readable configuration
- Error validation for missing or malformed arguments
- Nicely formatted help generation


Usage
------------

### Code
Here is the php code:
```
<?php

// specify the spec as human readable text
$values = CLIOpts\CLIOpts::createFromTextSpec("
{self} <in_filename>
-i, --id <id> specify an id (required)
-o, --out <out_filename> output filename
-v be verbose
-h, --help show this help
")->run();

// show the values
echo "The values you supplied are:\n";
print_r((array)$values);

?>
```

### Input
The following are handled in the same way by cliopts:

`./script.php -v -i 101 -o /tmp/myfile.txt /tmp/infile.txt`

`./script.php -vi 101 -o /tmp/myfile.txt /tmp/infile.txt`

`./script.php -v --id 101 -o /tmp/myfile.txt /tmp/infile.txt`

`./script.php -v --id="101" -o /tmp/myfile.txt /tmp/infile.txt`


### Output
All of the above will show this output:
```
The values you supplied are:        
Array                               
(                                   
    [in_filename] => /tmp/infile.txt
    [id] => 101
    [out] => /tmp/myfile.txt
    [v] =>        
)                                   
```