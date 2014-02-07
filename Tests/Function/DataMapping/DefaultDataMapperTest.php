<?php

namespace Modera\ServerCrudBundle\Tests\Functional\DataMapping;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\ServerCrudBundle\DataMapping\DefaultDataMapper;
use Sli\AuxBundle\Util\Toolkit;
use Doctrine\ORM\Mapping as Orm;

/**
 * @Orm\Entity
 */
class DummyUser
{
    /**
     * @Orm\Column(type="integer")
     * @Orm\Id
     * @Orm\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Orm\Column(type="string")
     */
    public $firstname;

    /**
     * @Orm\Column(type="string")
     */
    public $lastname;

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DefaultDataMapperTest extends FunctionalTestCase
{
    static public function doSetUpBeforeClass()
    {
        $driver = new AnnotationDriver(
            self::$kernel->getContainer()->get('annotation_reader'),
            array(__DIR__)
        );

        Toolkit::addMetadataDriverForEntityManager(self::$em, $driver, __NAMESPACE__);
    }

    public function testMapData()
    {
        /* @var DefaultDataMapper $mapper */
        $mapper = self::$container->get('modera_server_crud.data_mapping.default_data_mapper');

        $this->assertInstanceOf(DefaultDataMapper::clazz(), $mapper);

        $params = array(
            'firstname' => 'Vassily',
            'lastname' => 'Pupkin'
        );

        $user = new DummyUser();

        $mapper->mapData($params, $user);

        $this->assertEquals($params['firstname'], $user->firstname);
        $this->assertEquals($params['lastname'], $user->lastname);
    }
}