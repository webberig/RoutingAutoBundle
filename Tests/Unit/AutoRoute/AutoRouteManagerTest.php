<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2013 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Symfony\Cmf\Bundle\RoutingAutoBundle\Tests\Unit\AutoRoute;

use Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\AutoRouteManager;

class AutoRouteManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = $this->getMockBuilder(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\Factory'
        )->disableOriginalConstructor()->getMock();

        $this->builder = $this->getMockBuilder(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\RouteStack\Builder'
        )->disableOriginalConstructor()->getMock();

        $this->driver = $this->getMock(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\Driver\DriverInterface'
        );

        $this->arm = new AutoRouteManager($this->driver, $this->factory, $this->builder);

        $this->builderUnitChain = $this->getMockBuilder(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\RouteStack\BuilderUnitChain'
        )->disableOriginalConstructor()->getMock();

        $this->cnbu = $this->getMock(
            'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\RouteStack\BuilderUnitInterface'
        );
    }

    public function testUpdateAutoRouteForDocument()
    {
        $testCase = $this;
        $this->factory->expects($this->once())
            ->method('getRouteStackBuilderUnitChain')
            ->will($this->returnValue($this->builderUnitChain));
        $this->factory->expects($this->once())
            ->method('getContentNameBuilderUnit')
            ->with('stdClass')
            ->will($this->returnValue($this->cnbu));

        $this->builderUnitChain->expects($this->once())
            ->method('executeChain')
            ->will($this->returnCallback(function ($context) use ($testCase) {
                $testCase->assertInstanceOf(
                    'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\BuilderContext',
                    $context
                );
            }));

        $this->builder->expects($this->once())
            ->method('build')
            ->will($this->returnCallback(function ($stack, $builderUnit) use ($testCase) {
                $testCase->assertInstanceOf(
                    'Symfony\Cmf\Bundle\RoutingAutoBundle\AutoRoute\AutoRouteStack',
                    $stack
                );
                $testCase->assertSame($testCase->cnbu, $builderUnit);
            }));

        $stdClass = new \stdClass;
        $this->arm->updateAutoRouteForDocument($stdClass);
    }
}
