<?php

namespace Modera\BackendSecurityBundle\Tests\Unit\DataMapper;

use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\SecurityBundle\Entity\User;

/**
 * @author    Alex Plaksin <alex.plaksin@modera.net>
 * @copyright 2016 Modera Foundation
 */
class UserDataMapperTest extends FunctionalTestCase
{
    /**
     * Phake Mock of Modera\BackendSecurityBundle\DataMapper\UserDataMapper.
     */
    private $mapper;

    public function doSetUp()
    {
        $mapperService = static::$container->get('sli.extjsintegration.entity_data_mapper');

        $this->mapper = \Phake::partialMock('Modera\BackendSecurityBundle\DataMapper\UserDataMapper', $mapperService, static::$em);
    }

    public function testDataMapper_ExcludedFiled()
    {
        $mappedFields = \Phake::makeVisible($this->mapper)->getAllowedFields(User::clazz());

        $this->assertTrue(false === array_search('meta', $mappedFields));
    }

    public function testMapData_SetNewMeta()
    {
        $meta = array('newKey' => 'newVal');
        $params = array(
            'lastName' => 'Last Name',
            'meta' => $meta,
        );
        $user = new User();

        $this->mapper->mapData($params, $user);

        $this->assertEquals('Last Name', $user->getLastName());
        $this->assertEquals($meta, $user->getMeta());
    }

    public function testMapData_RewriteExistingMeta()
    {
        $meta = array('newKey' => 'newVal');
        $params = array(
            'lastName' => 'Last Name',
            'meta' => $meta,
        );
        $user = new User();
        $user->setMeta(array('WillBeRewrited' => true));

        $this->mapper->mapData($params, $user);

        $this->assertEquals('Last Name', $user->getLastName());
        $this->assertEquals($meta, $user->getMeta());
    }

    public function testMapData_ClearExisting()
    {
        $meta = array('WillBeErased' => true);
        $params = array(
            'lastName' => 'Last Name',
            'meta' => '',
        );
        $user = new User();
        $user->setMeta($meta);

        $this->mapper->mapData($params, $user);

        $this->assertEquals('Last Name', $user->getLastName());
        $this->assertEquals(array(), $user->getMeta());
    }

    public function testMapData_WillBeNotTouched()
    {
        $meta = array('WillExistsAfterMapping' => true);
        $params = array(
            'lastName' => 'Last Name',
        );

        $user = new User();
        $user->setMeta($meta);

        $this->mapper->mapData($params, $user);

        $this->assertEquals('Last Name', $user->getLastName());
        $this->assertEquals($meta, $user->getMeta());
    }
}
