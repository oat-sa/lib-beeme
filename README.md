# Beeme (Basic Equation/Expression Math Engine)

Simple mathematical expression parser and calculator *based on the great work of
Adrean Boyadzhiev*.

## Install
The recommended way to install Beeme is [through composer](http://getcomposer.org).

```JSON
{
    "require": {
        "oat-sa/lib-beeme": "dev-master"
    }
}
```
## Basic Usage

Here is an simple example of evaluation of mathematical expression
```php
<?php

$parser = new \oat\beeme\Parser();
$expression = '1 + 2 * 3 * ( 7 * 8 ) - ( 45 - 10 )';
$result = $parser->evaluate($expression);

echo $result; // 302.000000
```

Evaluate equation
```php
<?php

$parser = new \oat\beeme\Parser();
$expression = 'y = 1 + x';
$result = $parser->evaluate(
    $expression,
    ['x' => 2],
    ['y' => 3]
);

echo $result; // true
```

Compare
```php
<?php

$parser = new \oat\beeme\Parser();
$expression = 'x > 1 + 4';
$result = $parser->evaluate(
    $expression,
    ['x' => 2]
);

echo $result; // false
```

```php
<?php

$parser = new \oat\beeme\Parser();
$expression = 'y < 1 + x';
$result = $parser->evaluate(
    $expression,
    ['x' => 2],
    ['y' => 0]
);

echo $result; // true
```

## Constants

Beeme comes with two built-in constants that are "_pi_" and "_e_". You can also use custom constans in your expressions,
and give them actual values at runtime. Below, an example with the constant "_x_" replaced by the integer value 3.

```php
<?php

$parser = new \oat\beeme\Parser();
$expression = '3 + x';
$result = $parser->evaluate(
    $expression,
    ['x' => 3]
);

echo $result; // 6.000000
```

## Functions

Beeme provides a set of unary functions to be used in your expressions. Below is an example of using the "_abs_" function
in an expression.

```php
<?php

$expression = '1 + abs(x)';
$result = $parser->evaluate(
    $expression,
    ['x' => -10]
);

echo $result; // 11.000000
```

Please see the list of available unary functions available in Beeme. They all map to their PHP built-in equivalent:

* [abs](http://php.net/manual/en/function.abs.php)
* [sin](http://php.net/manual/en/function.sin.php)
* [sinh](http://php.net/manual/en/function.sinh.php)
* [asin](http://php.net/manual/en/function.asin.php)
* [asinh](http://php.net/manual/en/function.asinh.php)
* [cos](http://php.net/manual/en/function.cosh.php)
* [acos](http://php.net/manual/en/function.acos.php)
* [tan](http://php.net/manual/en/function.tan.php)
* [tanh](http://php.net/manual/en/function.tanh.php)
* [atan](http://php.net/manual/en/function.atan.php)
* [exp](http://php.net/manual/en/function.exp.php)
* [ceil](http://php.net/manual/en/function.ceil.php)
* [floor](http://php.net/manual/en/function.floor.php)

## TODO
  - Always more unit tests
  - N-ary functions

## License

MIT, see LICENSE.
