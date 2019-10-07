<?php

namespace oat\beeme\tests;

use PHPUnit_Framework_TestCase;
use oat\beeme\Parser;

class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testCanCreateParser()
    {
        $this->assertInstanceOf('oat\beeme\Parser', new Parser());
    }
    
    /**
     * @dataProvider evaluateProvider
     */
    public function testEvaluate($expected, $input, array $variables = array())
    {
        $parser = new Parser(array('comparisonPrecision' => 9));
        $result = $parser->evaluate($input, $variables);
        $this->assertSame($expected, $result, "Expression Evaluation: 'result of expression = '${input}' must be equal to '${expected}' but is '${result}'.");
    }
    
    public function evaluateProvider()
    {
        return array(
            array(0., '0'),
            array(9., '9'),
            array(0., '0 + 0'),
            array(11., '3 + 4 * 2'),
            array(8., '2 ^ 3'),
            array(16., '2 ^ 3 * 2'),
            array(147., '(3 * 4) ^ 2 + 3'),
            array(256., '2 ^ 2 ^ 3'),
            array(12., '2 ^ 2 * 3'),
            array(-1., '-1 + 0'),
            array(-1., '0 + -1'),
            array(1., '+0 + 1'),
            array(19., '9 + +10'),
            array(2., '+1 - -1'),
            array(4.4, '2.2 + 2.2'),
            array(M_PI, 'pi'),
            array(M_PI * 2, 'pi * 2'),
            array(M_E, 'e'),
            array(M_E * 2, 'e * 2'),

            array(true, '1 = 1'),
            array(false, '0 = 1'),
            array(true, '1 = 1 = (1 + 1 - 1)'),
            array(true, 'y + 3*x = 5', array('x' => 5, 'y' => -10.)),
            array(false, 'y + 3*x = 5', array('x' => 4., 'y' => -10.)),

            array(2., 'a + b', array('a' => 1., 'b' => 1.)),
            array(2., '_A + _B', array('_A' => 1., '_B' => 1.)),

            array(1., 'abs(-1)'),
            array(79., '4 + 3 * abs(-29 + 4)'),
            array(1., 'abs(abs(-1))'),
            array(4., 'abs(abs(-1 - 3))'),

            array(1., 'log(e)'),
            array(0., 'log(1)'),
            array(2., 'log(e^(2*x))', array('x' => 1)),

            array(-1., 'cos(pi)'),
            array(0., 'cos(pi/2)'),
            array(0., 'cos(-pi/2)'),
            array(1., 'cos(0)'),
            array(true, 'y = cos(pi/2*x)', array('x' => -1, 'y' => 0)),
            array(true, 'y = cos(pi/2*x)', array('x' => 0, 'y' => 1)),

            array(true, 'y < cos(pi/2*x)', array('x' => -1, 'y' => -1)),
            array(true, 'y > cos(pi/2*x)', array('x' => 0, 'y' => 2)),

            array(0., 'tan(0)'),
            array(1., 'tan(pi/4)'),
            array(1., 'cotan(pi/4)'),
            array(0., 'cotan(pi/2)'),

            array(0., 'sin(pi)'),
            array(1., 'sin(pi/2)'),
            array(0., '1-sin((1+x)*pi/2)', array('x' => 0)),
            array(1., '1-sin((1+x)*pi/2)', array('x' => 1)),
            array(true, 'y = 1-sin((1+x)*pi/2)', array('x' => 0, 'y' => 0)),
            array(true, 'y = 1-sin((1+x)*pi/2)', array('x' => 1, 'y' => 1)),
            array(true, 'y < 1-sin((1+x)*pi/2)', array('x' => 0, 'y' => -1)),
            array(true, 'y > 1-sin((1+x)*pi/2)', array('x' => 1, 'y' => 2)),
        );
    }
    
    /**
     * @dataProvider divisionByZeroProvider
     */
    public function testDivisionByZero($input)
    {
        $this->setExpectedException('\RangeException');
        
        $parser = new Parser();
        $parser->evaluate($input);
    }
    
    public function divisionByZeroProvider()
    {
        return array(
            array('1 / 0'),
            array('0 / 0'),
            array('21 + 3 / ( 1 - 1 )')
        );
    }
    
    /**
     * @dataProvider invalidSyntaxProvider
     */
    public function testInvalidSyntax($expectedMessage, $input)
    {
        $this->setExpectedException('\\InvalidArgumentException', $expectedMessage);
        $parser = new Parser();
        $parser->evaluate($input);
    }
    
    public function invalidSyntaxProvider()
    {
        return array(
            array("Syntax error: unexpected character ','.", ','),
            array("Syntax error: unexpected character ','.", '24 + 3,4 - 4'),
            array("Syntax error: mismatched parentheses or misplaced number.", '(24 + 3'),
            array("Syntax error: mismatched parentheses.", '(24 + 3) + 4)'),
            array("Syntax error: operator '*' cannot be used as a unary operator or with an operator as an operand.", '*24 + 3'),
            array("Syntax error: operator '=' cannot be used as a unary operator or with an operator as an operand.", '=24 + 3'),
            array("Syntax error: operator '/' cannot be used as a unary operator or with an operator as an operand.", '(/34 + 4'),
            array("Syntax error: operator '=' cannot be used as a unary operator or with an operator as an operand.", '(=34 + 4'),
            array("Syntax error: operator '%' cannot be used as a unary operator or with an operator as an operand.", '10 + %4'),
            array("Syntax error: operator '*' cannot be used as a unary operator or with an operator as an operand.", '10 ** 10'),
            array("Syntax error: operator '*' cannot be used as a unary operator or with an operator as an operand.", '10 *** 10'),
            array("Syntax error: operator '=' cannot be used as a unary operator or with an operator as an operand.", '10 == 10'),
            array("Syntax error: mismatched parentheses or misplaced number.", 'abs('),
            array("Syntax error: mismatched parentheses or misplaced number.", 'abs(-'),
            array("Syntax error: mismatched parentheses or misplaced number.", 'abs(-1'),
            array("Syntax error: mismatched parentheses or misplaced number.", 'abs(-1('),
            array("Syntax error: empty parenthesis.", 'abs()'),
            array("Syntax error: empty parenthesis.", '1 + ()'),
        );
    }
    
    public function testSetConstant()
    {
        $parser = new Parser();
        $parser->setConstant('#pi', M_PI);
        $this->assertEquals(M_PI, $parser->evaluate('#pi'));
    }
}
