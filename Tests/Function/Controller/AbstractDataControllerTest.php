<?php

namespace Modera\AdminGeneratorBundle\Tests\Functional\Controller;

use Modera\AdminGeneratorBundle\Controller\AbstractDataController;
use Modera\FoundationBundle\Testing\IntegrationTestCase;
use Doctrine\ORM\Mapping as Orm;
use Symfony\Component\Validator\Constraints as Assert;
use Sli\AuxBundle\Util\Toolkit;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * @Orm\Entity
 * @Orm\Table("_testing_article")
 */
class DummyArticle
{
    /**
     * @Orm\Column(type="integer")
     * @Orm\Id
     * @Orm\GeneratedValue(strategy="AUTO")
     */
    public $id;

    /**
     * @Orm\Column(type="string")
     * @Assert\NotBlank
     */
    public $title;

    /**
     * @Orm\Column(type="text")
     * @Assert\NotBlank
     */
    public $body;

    public function getId()
    {
        return $this->id;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    static public function clazz()
    {
        return get_called_class();
    }
}

class DataController extends AbstractDataController
{
    public function getConfig()
    {
        return array(
            'entity' => DummyArticle::clazz()
        );
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class AbstractDataControllerTest extends IntegrationTestCase
{
    /* @var DataController */
    private $controller;

    // override
    public function doSetUp()
    {
        $this->controller = new DataController();
        $this->controller->setContainer(self::$container);
    }

    // override
    static public function doSetUpBeforeClass()
    {
        $driver = new AnnotationDriver(
            self::$kernel->getContainer()->get('annotation_reader'),
            array(__DIR__)
        );

        Toolkit::addMetadataDriverForEntityManager(self::$em, $driver, __NAMESPACE__);
        Toolkit::createTableFoEntity(self::$em, DummyArticle::clazz());
    }

    static public function doTearDownAfterClass()
    {
        Toolkit::dropTableForEntity(self::$em, DummyArticle::clazz());
    }

    public function testCreateAction()
    {
        // validation for "title" field should fail
        $result = $this->controller->createAction(array(
            'record' => array(
                'body' => 'Some text goes here'
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('field_errors', $result);
        $this->assertTrue(is_array($result['field_errors']));
        $this->assertEquals(1, count($result['field_errors']));
        $this->assertArrayHasKey('title', $result['field_errors']);
        $this->assertTrue(is_array($result['field_errors']['title']));
        $this->assertEquals(1, count($result['field_errors']['title']));

        // validation should pass and record should be saved

        $result = $this->controller->createAction(array(
            'hydration' => array(
                'profile' => 'form',
                'groups' => array('comments')
            ),
            'record' => array(
                'title' => 'Some title',
                'body' => 'Some text goes here'
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('created_models', $result);
        $this->assertTrue(is_array($result['created_models']));
        $this->assertEquals(1, count($result['created_models']));
        $this->assertFalse(isset($result['updated_models']));
        $this->assertFalse(isset($result['removed_models']));
    }
}