<?php

namespace Modera\BackendToolsActivityLogBundle\Tests\Functional\AutoSuggest;

use Modera\ActivityLoggerBundle\Entity\Activity;
use Modera\ActivityLoggerBundle\Manager\ActivityManagerInterface;
use Modera\BackendToolsActivityLogBundle\AutoSuggest\FilterAutoSuggestService;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FilterAutoSuggestServiceTest extends FunctionalTestCase
{
    /**
     * @var FilterAutoSuggestService
     */
    private $s;

    public function doSetUp()
    {
        $this->s = self::$container->get('modera_backend_tools_activity_log.auto_suggest.filter_auto_suggest_service');
    }

    public function testServiceExists()
    {
        $this->assertInstanceOf(FilterAutoSuggestService::clazz(), $this->s);
    }

    private function createUser()
    {
        $u = new User();
        $u->setFirstName('Joe');
        $u->setLastName('Doe');
        $u->setUsername('djatel');
        $u->setEmail('djatel@23example1.com');
        $u->setPassword(1234);

        self::$em->persist($u);
        self::$em->flush();

        return $u;
    }

    public function testSuggestForUser()
    {
        $u = $this->createUser();

        $result = $this->s->suggest('user', 'ate');

//        $this->assertTrue(is_array($result));
//        $this->assertEquals(0, count($result));
//
//        /* @var ActivityManagerInterface $activityMgr */
//        $activityMgr = self::$container->get('modera_activity_logger.manager.activity_manager');
//        $activityMgr->info('some message', array(
//            'type' => 'dat_foox_type',
//            'author' => $u->getId()
//        ));

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertTrue(is_array($result[0]));
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertEquals($u->getId(), $result[0]['id']);
        $this->assertArrayHasKey('value', $result[0]);
        $this->assertEquals(sprintf('%s (%s)', $u->getFullName(), $u->getUsername()), $result[0]['value']);
    }

    public function testSuggestEvent()
    {
        /* @var ActivityManagerInterface $activityMgr */
        $activityMgr = self::$container->get('modera_activity_logger.manager.activity_manager');
        $activityMgr->info('some message', array(
            'type' => 'dat_foox_type'
        ));

        $result = $this->s->suggest('eventType', 'foox');

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('value', $result[0]);
        $this->assertEquals('dat_foox_type', $result[0]['id']);
        $this->assertEquals('dat_foox_type', $result[0]['value']);
    }

    public function testSuggestExact()
    {
        $u = $this->createUser();

        $result = $this->s->suggest('exact-user', $u->getId());

        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('value', $result[0]);
        $this->assertEquals($u->getId(), $result[0]['id']);
        $this->assertEquals(sprintf('%s (%s)', $u->getFullName(), $u->getUsername()), $result[0]['value']);
    }
} 