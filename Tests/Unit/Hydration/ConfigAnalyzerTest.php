<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Hydration;

use Modera\ServerCrudBundle\Hydration\ConfigAnalyzer;
use Modera\ServerCrudBundle\Hydration\HydrationProfile;
use Modera\ServerCrudBundle\Hydration\UnknownHydrationGroupException;
use Modera\ServerCrudBundle\Hydration\UnknownHydrationProfileException;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class ConfigAnalyzerTest extends \PHPUnit_Framework_TestCase
{
    /* @var ConfigAnalyzer */
    private $config;
    private $rawConfig;

    public function setUp()
    {
        $this->rawConfig = array(
            'groups' => array(
                'form' => array(
                    'title', 'body',
                ),
                'author' => array(
                    'firstname' => 'author.firstname',
                    'lastname' => 'author.lastname',
                ),
                'list' => function (Article $e) {
                    return array(
                        'title' => substr($e->title, 0, 10),
                        'body' => substr($e->body, 0, 10),
                    );
                },
            ),
            'profiles' => array(
                'form' => HydrationProfile::create()->useGroups(array('form', 'author')),
                'mixed' => array('form', 'author', 'list'),
                'list',
            ),
        );

        $this->config = new ConfigAnalyzer($this->rawConfig);
    }

    public function testGetProfile()
    {
        $result = $this->config->getProfileDefinition('form');

        $this->assertInstanceOf(HydrationProfile::clazz(), $result);
        $this->assertSame($this->rawConfig['profiles']['form'], $result);
    }

    public function testGetGroupProfileWithShortDeclaration()
    {
        /* @var HydrationProfile $result */
        $result = $this->config->getProfileDefinition('mixed');

        $this->assertInstanceOf(HydrationProfile::clazz(), $result);
        $this->assertTrue($result->isGroupingNeeded());

        $groups = $result->getGroups();

        $this->assertTrue(in_array('form', $groups));
        $this->assertTrue(in_array('author', $groups));
        $this->assertTrue(in_array('list', $groups));
    }

    public function testGetGroupProfileWhenProfileNameMatchesGroup()
    {
        /* @var HydrationProfile $result */
        $result = $this->config->getProfileDefinition('list');

        $this->assertInstanceOf(HydrationProfile::clazz(), $result);
        $this->assertFalse($result->isGroupingNeeded());
        $this->assertSame(array('list'), $result->getGroups());
    }

    public function testGetProfileWhenItDoesntExist()
    {
        $thrownException = null;
        try {
            $this->config->getProfileDefinition('blah');
        } catch (UnknownHydrationProfileException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('blah', $thrownException->getProfileName());
    }

    public function testGetGroupDefinition()
    {
        $result = $this->config->getGroupDefinition('author');

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('firstname', $result);
        $this->assertArrayHasKey('lastname', $result);
        $this->assertEquals('author.firstname', $result['firstname']);
        $this->assertEquals('author.lastname', $result['lastname']);
    }

    public function testGetGroupDefinitionWhenItDoestnExist()
    {
        $thrownException = null;
        try {
            $this->config->getGroupDefinition('blah');
        } catch (UnknownHydrationGroupException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('blah', $thrownException->getGroupName());
    }
}
