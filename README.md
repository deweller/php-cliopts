php-cliopts [![Build Status](https://secure.travis-ci.org/deweller/php-cliopts.png)](https://travis-ci.org/deweller/php-cliopts) ![project status](http://stillmaintained.com/deweller/php-cliopts.png)
===========

A no-nonsense command-line options parser and help generator for PHP CLI apps.


Features
------------

- Simple one-line usage with a human readable configuration format
- Parses $argv data into an associative array similar to getopt()
- Adds error validation for missing or malformed arguments or options
- Nicely formatted help generation
- Supports options (e.g. -i 100) and named arguments (./script.php /tmp/myfile.txt)


Usage
------------

### Code ###

In its simplest form, the parser can be used with one line of php code:
```php

// specify the spec as human readable text
$values = CLIOpts\CLIOpts::run("
{self} <in_filename>
-i, --id <id> specify an id (required)
-o, --out <out_filename> output filename
-v be verbose
-h, --help show this help
");

// show the values
echo "The values you supplied are:\n";
print_r((array)$values);

```


### CLI Input ###

The interpretation of flags are somewhat flexible.  The following lines are all handled in the same way by cliopts:

1. `./script.php -v -i 101 -o /tmp/myfile.txt /tmp/infile.txt`

2. `./script.php -vi 101 -o /tmp/myfile.txt /tmp/infile.txt`

3. `./script.php -v --id 101 -o /tmp/myfile.txt /tmp/infile.txt`

4. `./script.php -v --id="101" -o /tmp/myfile.txt /tmp/infile.txt`

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


The Human Readable Text Specification
------------

Let's look at the specification in the following bit of code:

```php
$values = CLIOpts\CLIOpts::run("
  Usage: process_files.php [options] <in_file1> [<in_file2>]
  -i, --id <id> specify an id (required)
  -v be verbose
")
```

### The Usage Line ###

The spec begins with a usage line.  This line is optional.  But if it is provided, here is a breakdown of how the usage line is interpreted:

```
Usage: process_files.php [options] <in_file1> [<in_file2>]
|      |                 |         |          |
|      |                 |         |          + Optional second argument named in_file2.
|      |                 |         |
|      |                 |         + Required first argument named in_file1
|      |                 |          
|      |                 + An options placeholder.  This may be ommitted.  It must come before any arguments.
|      |
|      + An optional script name.  Omit this or use {self} to show $_SERVER['argv'][0].
|
+ The usage keyword.  This may be ommitted.
```

In this example, 1 argument is expected and the value provided will be assigned to the key "in_file1" in the values object.  An optional second argument will be assed to the key "in_file2" if it is provided.  And that's all.  If a 3rd argument is provided it will not be assigned to a value and validation will fail.


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
|   + This is a long option name.  It is not required.  If specified, this is used for the value name when arguments are parsed.
|
+ This is the short option name.  It is not required.  Values can be accessed using this shortcut.
```

