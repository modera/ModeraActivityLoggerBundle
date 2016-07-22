<?php

namespace Modera\ActivityLoggerBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\ActivityLoggerBundle\Entity\Activity;
use Modera\ActivityLoggerBundle\Manager\DoctrineOrmActivityManager;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Psr\Log\LogLevel;
use Sli\AuxBundle\Util\Toolkit;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DoctrineOrmActivityManagerTest extends FunctionalTestCase
{
    /**
     * @var DoctrineOrmActivityManager
     */
    private $mgr;

    /**
     * @var SchemaTool
     */
    static private $st;

    // override
    static public function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema([self::$em->getClassMetadata(Activity::clazz())]);

    }

    // override
    static public function doTearDownAfterClass()
    {
        self::$st->dropSchema([self::$em->getClassMetadata(Activity::clazz())]);
    }

    // override
    public function doSetUp()
    {
        $this->mgr = self::$container->get('modera_activity_logger.manager.doctrine_orm_activity_manager');
    }

    /**
     * @return Activity
     */
    private function getLastCreatedActivity()
    {
        $query = self::$em->createQuery(sprintf('SELECT a FROM %s a ORDER BY a.id DESC', Activity::clazz()));
        $query->setMaxResults(1);

        return $query->getSingleResult();
    }

    public function testLog()
    {
        $cx = array(
            'author' => 'Joe',
            'type' => 'foo_type',
            'meta' => array('foo', 'bar')
        );

        $this->mgr->log(LogLevel::ALERT, 'testing it', $cx);

        $activity = $this->getLastCreatedActivity();

        $this->assertNotNull($activity);
        $this->assertEquals(LogLevel::ALERT, $activity->getLevel());
        $this->assertEquals('testing it', $activity->getMessage());
        $this->assertEquals('Joe', $activity->getAuthor());
        $this->assertEquals('foo_type', $activity->getType());
        $this->assertSame($cx['meta'], $activity->getMeta());
    }

    public function testQuery()
    {
        $activity = new Activity();
        $activity->setType('foo_type');
        $activity->setLevel('debug');
        $activity->setMessage('foo message');

        self::$em->persist($activity);
        self::$em->flush();

        $result = $this->mgr->query(array(
            'filter' => array(
                array('property' => 'type', 'value' => 'eq:' . $activity->getType())
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('total', $result);
        $this->assertEquals(1, $result['total']);
        $this->assertEquals(1, count($result['items']));
        $this->assertInstanceOf(Activity::clazz(), $result['items'][0]);
        $this->assertSame($activity->getId(), $result['items'][0]->getId());
    }
} 