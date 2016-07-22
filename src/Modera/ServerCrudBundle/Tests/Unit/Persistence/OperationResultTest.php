<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Persistence;

use Modera\ServerCrudBundle\Persistence\OperationResult;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class OperationResultTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $result = new OperationResult();
        $result->reportEntity('FooEntity', 1, OperationResult::TYPE_ENTITY_CREATED);
        $result->reportEntity('FooEntity', 2, OperationResult::TYPE_ENTITY_UPDATED);
        $result->reportEntity('FooEntity', 3, OperationResult::TYPE_ENTITY_REMOVED);

        $anotherResult = new OperationResult();
        $anotherResult->reportEntity('BarEntity', 4, OperationResult::TYPE_ENTITY_CREATED);
        $anotherResult->reportEntity('BarEntity', 5, OperationResult::TYPE_ENTITY_UPDATED);
        $anotherResult->reportEntity('BarEntity', 6, OperationResult::TYPE_ENTITY_REMOVED);

        $mergedResult = $result->merge($anotherResult);

        $this->assertInstanceOf(OperationResult::clazz(), $mergedResult);

        $this->assertEquals(2, count($mergedResult->getCreatedEntities()));
        $this->assertEquals(2, count($mergedResult->getUpdatedEntities()));
        $this->assertEquals(2, count($mergedResult->getRemovedEntities()));

        // making sure that original ones were not touched
        $this->assertEquals(
            3,
            count(array_merge($result->getCreatedEntities(), $result->getUpdatedEntities(), $result->getRemovedEntities()))
        );
        $this->assertEquals(
            3,
            count(array_merge($anotherResult->getCreatedEntities(), $anotherResult->getUpdatedEntities(), $anotherResult->getRemovedEntities()))
        );

        $this->assertTrue($result !== $mergedResult);
        $this->assertTrue($anotherResult !== $mergedResult);
    }
}
