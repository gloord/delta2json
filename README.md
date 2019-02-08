# PHP DELTA2JSON Converter

### Description

PHP CLI application for converting DELTA (DEscription Language for TAxonomy) files into JSON format.

The following DELTA files are supported: 
 - specs
 - chars 
 - items

See [DELTA specification](https://github.com/tdwg/delta/blob/master/107-516-1-ED.pdf) file for further information.

## Usage

- Place specs, chars and items files into a folder (please do not rename the files)
- Convert the files with following command. Output path is optional (default is path specified in first argument)

```
$ ./bin/delta2json parse [path to directory with delta files] [output path (optional)]
```
Example
```
$ ./bin/delta2json parse /path/to/delta_files/ /storage_path/json_files/
```


## Installation

Install required packages via composer.