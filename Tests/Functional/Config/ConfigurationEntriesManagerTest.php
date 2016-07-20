<?php

namespace Modera\ConfigBundle\Tests\Functional\Config;

use Modera\ConfigBundle\Config\ConfigurationEntriesManager;
use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Modera\ConfigBundle\Tests\Fixtures\Entities\User;
use Modera\ConfigBundle\Tests\Functional\AbstractFunctionalTestCase;
use Symfony\Component\Serializer\Exception\RuntimeException;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.org>
 */
class ConfigurationEntriesManagerTest extends AbstractFunctionalTestCase
{
    /**
     * @return ConfigurationEntriesManager
     */
    private function getManager()
    {
        return self::$container->get('modera_config.configuration_entries_manager');
    }

    public function testFindOneByName()
    {
        $vasya = new User('vasya');

        self::$em->persist($vasya);
        self::$em->flush();

        $ce1 = new ConfigurationEntry('cf_1');
        $ce1->setValue('foo');

        $this->getManager()->save($ce1);

        $foundCe1 = $this->getManager()->findOneByName('cf_1');

        $this->assertNotNull($foundCe1);
        $this->assertEquals($ce1->getId(), $foundCe1->getId());

        // ---

        $ce1->setOwner($vasya);

        $this->getManager()->save($ce1);

        $foundCe1 = $this->getManager()->findOneByName('cf_1');

        $this->assertNull(
            $foundCe1,
            'If ConfigurationEntry is already associated with owner but when invoking findOneByName() owner is not provided then nothing must be returned.'
        );

        // ---

        $foundCe1 = $this->getManager()->findOneByName('cf_1', $vasya);

        $this->assertNotNull($foundCe1);
        $this->assertEquals('cf_1', $foundCe1->getName());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindOneByNameOrDie_notFound()
    {
        $this->getManager()->findOneByNameOrDie('cf_1');
    }

    public function testFindOneByNameOrDie()
    {
        $vasya = new User('vasya');

        self::$em->persist($vasya);
        self::$em->flush();

        $ce1 = new ConfigurationEntry('cf_1');
        $ce1->setValue('foo');

        $this->getManager()->save($ce1);

        $ce = $this->getManager()->findOneByNameOrDie('cf_1');

        $this->assertNotNull($ce);
        $this->assertEquals('cf_1', $ce->getName());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testFindOneByNameOrDie_notFoundWithUserGiven()
    {
        $vasya = new User('vasya');

        self::$em->persist($vasya);
        self::$em->flush();

        $this->getManager()->findOneByNameOrDie('cf_1', $vasya);
    }

    public function testFindOneByNameOrDie_withUserGiven()
    {
        $vasya = new User('vasya');

        self::$em->persist($vasya);
        self::$em->flush();

        $ce1 = new ConfigurationEntry('cf_1');
        $ce1->setValue('foo');
        $ce1->setOwner($vasya);

        $this->getManager()->save($ce1);

        $ce = $this->getManager()->findOneByNameOrDie('cf_1', $vasya);
        $this->assertNotNull($ce);
        $this->assertEquals('cf_1', $ce->getName());
    }

    public function testFindAllExposed()
    {
        $ce1 = new ConfigurationEntry('cf_1');
        $ce1->setValue('foo');

        $ce2 = new ConfigurationEntry('cf_2');
        $ce2->setValue('foo');

        $ce3 = new ConfigurationEntry('cf_3');
        $ce3->setValue('foo');
        $ce3->setExposed(false);

        $this->getManager()->save($ce1);
        $this->getManager()->save($ce2);
        $this->getManager()->save($ce3);

        $result = $this->getManager()->findAllExposed();

        $this->assertEquals(2, count($result));
        $this->assertEquals('cf_1', $result[0]->getName());
        $this->assertEquals('cf_2', $result[1]->getName());

        // ---

        $vasya = new User('vasya');

        self::$em->persist($vasya);
        self::$em->flush();

        $ce1->setOwner($vasya);

        $this->getManager()->save($ce1);

        $result = $this->getManager()->findAllExposed();
        $this->assertEquals(1, count($result));
        $this->assertEquals('cf_2', $result[0]->getName());

        // ---

        $result = $this->getManager()->findAllExposed($vasya);

        $this->assertEquals(1, count($result));
        $this->assertEquals('cf_1', $result[0]->getName());
    }
}
