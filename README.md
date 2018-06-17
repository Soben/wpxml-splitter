# WPXML Splitter

Have large WPXML Files? This will help you split it up to run batch imports

## Instructions

```
require_once("vendor/autoload.php");

$input = "./path/to/xml/file.xml";
$output = "./path/to/output/direcotyr";

$processor = new \Magpie\WPXML\Splitter($input, $output);
$processor->process();
```

## Examples

See `example/split-into-twenty.php` for a working example

You can run this by browser in a local server environment, or going to the command line and running

```
$ php split-into-twenty.php
```

In this example, you will find ~8 new files created in the /output folder, each with 20 post items, or less

If you use WP-CLI, you can use the `import.sh` file for an example to run all of these files in one single process via the command line, to save some time.
