<?php

/**
 * @see       https://github.com/laminas/laminas-soap for the canonical source repository
 * @copyright https://github.com/laminas/laminas-soap/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-soap/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\Soap\Wsdl\ComplexTypeStrategy;

use Laminas\Soap\Wsdl;
use Laminas\Soap\Wsdl\ComplexTypeStrategy;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence;
use Laminas\Soap\Wsdl\ComplexTypeStrategy\Composite;
use LaminasTest\Soap\WsdlTestHelper;

/**
 * @package Laminas_Soap
 * @subpackage UnitTests
 */


/** Laminas_Soap_Wsdl */


/**
 * @category   Laminas
 * @package    Laminas_Soap
 * @subpackage UnitTests
 * @group      Laminas_Soap
 * @group      Laminas_Soap_Wsdl
 */
class CompositeStrategyTest extends WsdlTestHelper
{

    public function setUp()
    {
        // override parent setup because it is needed only in one method
    }

    public function testCompositeApiAddingStragiesToTypes()
    {
        $strategy = new Composite(array(), new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence);
        $strategy->connectTypeToStrategy('Book', new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex);

        $bookStrategy = $strategy->getStrategyOfType('Book');
        $cookieStrategy = $strategy->getStrategyOfType('Cookie');

        $this->assertTrue( $bookStrategy instanceof ArrayOfTypeComplex );
        $this->assertTrue( $cookieStrategy instanceof ArrayOfTypeSequence );
    }

    public function testConstructorTypeMapSyntax()
    {
        $typeMap = array('Book' => '\Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex');

        $strategy = new ComplexTypeStrategy\Composite($typeMap,
            new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence
        );

        $bookStrategy = $strategy->getStrategyOfType('Book');
        $cookieStrategy = $strategy->getStrategyOfType('Cookie');

        $this->assertTrue( $bookStrategy instanceof ArrayOfTypeComplex );
        $this->assertTrue( $cookieStrategy instanceof ArrayOfTypeSequence );
    }

    public function testCompositeThrowsExceptionOnInvalidType()
    {
        $strategy = new ComplexTypeStrategy\Composite();

        $this->setExpectedException('Laminas\Soap\Exception\InvalidArgumentException',
            'Invalid type given to Composite Type Map'
        );
        $strategy->connectTypeToStrategy(array(), 'strategy');
    }

    public function testCompositeThrowsExceptionOnInvalidStrategy()
    {
        $strategy = new ComplexTypeStrategy\Composite(array(), 'invalid');
        $strategy->connectTypeToStrategy('Book', 'strategy');

        $this->setExpectedException('Laminas\Soap\Exception\InvalidArgumentException',
            'Strategy for Complex Type "Book" is not a valid strategy'
        );
        $strategy->getStrategyOfType('Book');
    }

    public function testCompositeThrowsExceptionOnInvalidStrategyPart2()
    {
        $strategy = new ComplexTypeStrategy\Composite(array(), 'invalid');
        $strategy->connectTypeToStrategy('Book', 'strategy');

        $this->setExpectedException('Laminas\Soap\Exception\InvalidArgumentException',
            'Default Strategy for Complex Types is not a valid strategy object'
        );
        $strategy->getStrategyOfType('Anything');
    }

    public function testCompositeDelegatesAddingComplexTypesToSubStrategies()
    {
        $this->strategy = new ComplexTypeStrategy\Composite(array(), new \Laminas\Soap\Wsdl\ComplexTypeStrategy\AnyType);
        $this->strategy->connectTypeToStrategy('\LaminasTest\Soap\TestAsset\Book',
            new \Laminas\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex
        );
        $this->strategy->connectTypeToStrategy('\LaminasTest\Soap\TestAsset\Cookie',
            new \Laminas\Soap\Wsdl\ComplexTypeStrategy\DefaultComplexType
        );

        parent::setUp();

        $this->assertEquals('tns:Book',   $this->strategy->addComplexType('\LaminasTest\Soap\TestAsset\Book'));
        $this->assertEquals('tns:Cookie', $this->strategy->addComplexType('\LaminasTest\Soap\TestAsset\Cookie'));
        $this->assertEquals('xsd:anyType', $this->strategy->addComplexType('\LaminasTest\Soap\TestAsset\Anything'));

        $this->testDocumentNodes();
    }

    public function testCompositeRequiresContextForAddingComplexTypesOtherwiseThrowsException()
    {
        $strategy = new ComplexTypeStrategy\Composite();

        $this->setExpectedException('Laminas\Soap\Exception\InvalidArgumentException', 'Cannot add complex type "Test"');
        $strategy->addComplexType('Test');
    }

    /**
     *
     */
    public function testGetDefaultStrategy()
    {
        $strategyClass =  'Laminas\Soap\Wsdl\ComplexTypeStrategy\AnyType';

        $strategy = new Composite(array(), $strategyClass);

        $this->assertEquals($strategyClass, get_class($strategy->getDefaultStrategy()));
    }
}
