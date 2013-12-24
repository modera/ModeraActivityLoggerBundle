<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Hydration;
use Modera\ServerCrudBundle\Hydration\Config;
use Modera\ServerCrudBundle\Hydration\HydrationProfile;
use Modera\ServerCrudBundle\Hydration\UnknownHydrationGroupException;
use Modera\ServerCrudBundle\Hydration\UnknownHydrationProfileException;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /* @var Config */
    private $config;
    private $rawConfig;

    public function setUp()
    {
        $this->rawConfig = array(
            'groups' => array(
                'form' => array(
                    'title', 'body'
                ),
                'author' => array(
                    'firstname' => 'author.firstname',
                    'lastname' => 'author.lastname'
                ),
                'list' => function(Article $e) {
                    return array(
                        'title' => substr($e->title, 0, 10),
                        'body' => substr($e->body, 0, 10)
                    );
                }
            ),
            'profiles' => array(
                'list' => HydrationProfile::create(false)->useGroups(array( 'list')),
                'form' => HydrationProfile::create()->useGroups(array('form', 'author')),
                'author'
            )
        );

        $this->config = new Config($this->rawConfig);
    }

    public function testGetProfile()
    {
        $result = $this->config->getProfileDefinition('form');

        $this->assertInstanceOf(HydrationProfile::clazz(), $result);
        $this->assertSame($this->rawConfig['profiles']['form'], $result);
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