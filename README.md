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

### Code ###
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

### Input ###
The following are handled in the same way by cliopts:

`./script.php -v -i 101 -o /tmp/myfile.txt /tmp/infile.txt`

`./script.php -vi 101 -o /tmp/myfile.txt /tmp/infile.txt`

`./script.php -v --id 101 -o /tmp/myfile.txt /tmp/infile.txt`

`./script.php -v --id="101" -o /tmp/myfile.txt /tmp/infile.txt`


### Output ###
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


The text spec format
------------

Take this example specification:

```
Usage: process_files.php [options] <in_file1> [<in_file2>]
-i, --id <id> specify an id (required)
-v be verbose
```

### The Usage Line ###

The spec begins with an optional usage line.  Here is a breakdown of how the usage line is interpreted:

```
Usage: process_files.php [options] <in_file1> [<in_file2>]
|      |                 |         |          |
|      |                 |         |          + Optional second argument named in_file2.
|      |                 |         |
|      |                 |         + Required first argument named in_file1
|      |                 |          
|      |                 + An options placeholder.  This may be ommitted.  It must come before any arguments.
|      |
|      + A script name.  This may be ommitted to use the default $_SERVER['argv'][0]
|
+ The usage keyword.  This may be ommitted.
```

In this example, 1 argument is expected and it will be assigned the name "in_file1".  An optional second argument will be named "in_file2" if it is provided.  A 3rd argument is not specified and will not be interpreted.


### The Option Lines ###

Here is how the first option line is interpreted:

```
-i, --id <id> specify an id (required)
|   |    |    |             |
|   |    |    |             + This makes the option required when validating.
|   |    |    |
|   |    |    + Help text.  This can be any text.
|   |    |
|   |    + This specifies that the option requires a value.  Unlike arguments, this is not used for the value name.
|   |
|   + This is an optional long option name.  If specified, this is used for the value name when arguments are parsed.
|
+ This is the short option name.  Values can be also be accessed using this shortcut.
```