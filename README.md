# Kirby CSV Handler

Version 0.1

## Installation

Use one of the alternatives below.

### 1. Kirby CLI

If you are using the Kirby CLI, you can install this plugin by running the following commands in your shell:

```text
$ cd path/to/kirby
$ kirby plugin:install texnixe/kirby-csv-handler
```


### 2. Clone or download

  - Clone or download this repository.
  - Unzip the archive if needed and rename the folder to kirby-csv-handler.

Make sure that the plugin folder structure looks like this:

```text
site/plugins/kirby-csv-handler/
```

### 3. Git Submodule

If you know your way around Git, you can download this plugin as a submodule:

```text
$ cd path/to/kirby-project
$ git submodule add https://github.com/texnixe/kirby-csv-handler site/plugins/kirby-csv-handler
```

## Setup

### Options

The following options can or need to be set in your `/site/config/config.php` file:

```
c::set('csv-handler.file.filepath', 'path/to/csv/file');
c::set('csv-handler.file.delimiter', ';');

c::set('csv-handler.page.uid', 'a_column_name');
c::set('csv-handler.page.update', false);
c::set('csv-handler.page.template', 'template_name');

c::set('csv-handler.structure.field', 'title');
c::set('csv-handler.structure.append', false);
```

#### csv-handler.file.filepath

Required. A valid path to a .csv file.

#### csv-handler.file.delimiter

The delimiter between columns in your .csv file. Default delimiter is a comma (,).

#### csv-handler.page.uid

A column name that will be used to create the UIDs of the new pages. If you use a column name that starts with a number, an underscore will be used as a prefix. The entries in this column must be unique, otherwise the new page will not be created, or - if `csv-handler.page.update` is set to `true` - another entry with the same value will overwrite the first.

#### csv-handler.page.update

If you want to create subpages from the lines in the .csv file, this options defines if existing pages should be updated with the data from the file. Default is `false`.

#### csv-handler.page.template

Set the template for the subpages. Default is `default`.

#### csv-handler.structure.field

With this option you can set the field that is used for the structure field entries.

#### csv-handler.structure.append

Set this option to `true` to append all lines from the .csv file to an existing field if this exists. **By default, the entries from the structure field will be overwritten with the entries of the .csv file.**

## Routes

The Kirby CSV-Handler Plugin currently provides two routes:

```
csv-handler.createpages/(:all)
csv-handler.createstructure/(:all)
```

where `(:all)` is the path to the parent page (in case of subpage creation)/page (in case of structure field).

For example, the URL `http://example.com/csv-handler.createpages/products` will create subpages in `/content/products`, while `http://example.com/csv-handler.createstructure/products` will create/update the structure field in `/content/products`.

You must be logged in to use the routes.

## License

http://www.opensource.org/licenses/mit-license.php

## Authors

Sonja Broda https://www.texniq.de
