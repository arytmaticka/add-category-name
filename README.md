# JSON category name adder

Program which is adding name property into each leaf of given tree
structure and category list. It reads data from file containing tree category structure in JSON format 
and file containing list of details of categories. 

**Download Composer dependencies**

Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```

You may alternatively need to run `php composer.phar install`, depending
on how you installed Composer.

**Run from console**

To get full list of options run

```
bin/console help app:name-add
```

**Examples**

For example you can process files in public directory, 
tree in example file ```tree.json``` and ```list.json```
and store it in ```output.json``` :

```
bin/console app:name-add public/list.json public/tree.json public/output.json
```

By adding option ```--maxDepth``` you can set maximum level of tree process 
and option ```--skipOnNull``` to skip empty names in list dictionary.

**Testing**

Run command from terminal for unit testing:

```
bin/phpunit
```