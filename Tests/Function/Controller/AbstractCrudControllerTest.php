<?php

namespace Modera\AdminGeneratorBundle\Tests\Functional\Controller;

use Modera\AdminGeneratorBundle\Controller\AbstractCrudController;
use Modera\AdminGeneratorBundle\Controller\AbstractDataController;
use Modera\AdminGeneratorBundle\Hydration\HydrationProfile;
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

class DataController extends AbstractCrudController
{
    public function getConfig()
    {
        return array(
            'entity' => DummyArticle::clazz(),
            'hydration' => array(
                'groups' => array(
                    'form' => array(
                        'id', 'title', 'body'
                    ),
                    'list' => function(DummyArticle $e) {
                        return array(
                            'id' => $e->getId(),
                            'title' => substr($e->title, 0, 10),
                            'body' => substr($e->body, 0, 10)
                        );
                    }
                ),
                'profiles' => array(
                    'new_record' => HydrationProfile::create()->useGroups(array('form')),
                    'get_record' => HydrationProfile::create()->useGroups(array('form')),
                    'list' => HydrationProfile::create(false)->useGroups(array('list'))
                )
            )
        );
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class AbstractCrudControllerTest extends IntegrationTestCase
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
                'profile' => 'new_record'
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

        $this->assertArrayHasKey('result', $result);
        $this->assertTrue(is_array($result['result']));
        $this->assertArrayHasKey('form', $result['result']);
        $this->assertTrue(is_array($result['result']['form']));
        $this->assertArrayHasKey('id', $result['result']['form']);
        $form = $result['result']['form'];
        $this->assertNotNull($form['id']);
        $this->assertArrayHasKey('title', $form);
        $this->assertEquals('Some title', $form['title']);
        $this->assertArrayHasKey('body', $form);
        $this->assertEquals('Some text goes here', $form['body']);

        /* @var DummyArticle $article */
        $article = self::$em->getRepository(DummyArticle::clazz())->find($form['id']);
        $this->assertInstanceOf(DummyArticle::clazz(), $article);
        $this->assertEquals('Some title', $article->title);
        $this->assertEquals('Some text goes here', $article->body);
    }

    /**
     * @return DummyArticle[]
     */
    private function loadDummyData()
    {
        $result = array();

        for ($i=0; $i<5; $i++) {
            $article = new DummyArticle();
            $article->title = str_repeat('t', 15);
            $article->body = str_repeat('b', 15);

            $result[] = $article;

            self::$em->persist($article);
        }
        self::$em->flush();

        return $result;
    }

    public function testListAction()
    {
        $this->assertEquals(0, count(self::$em->getRepository(DummyArticle::clazz())->findAll()));

        $this->loadDummyData();

        $result = $this->controller->listAction(array(
            'limit' => 3,
            'sort' => array(
                array('property' => 'id', 'direction' => 'DESC')
            ),
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'notIn:6'
                )
            ),
            'hydration' => array(
                'profile' => 'list'
            )
        ));

        $me = $this;

        $assertValidItem = function($items, $index) use ($me) {
            $me->assertArrayHasKey($index, $items);

            $item = $items[$index];

            $me->assertArrayHasKey('id', $item);
            $me->assertArrayHasKey('title', $item);
            $me->assertArrayHasKey('body', $item);
        };

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('items', $result);
        $this->assertTrue(is_array($result['items']));
        $this->assertEquals(3, count($result['items']));
        $assertValidItem($result['items'], 0);
        $assertValidItem($result['items'], 1);
        $assertValidItem($result['items'], 2);
    }

    public function testRemoveAction()
    {
        $articles = $this->loadDummyData();

        $ids = array(
            $articles[0]->getId(),
            $articles[1]->getId()
        );

        $result = $this->controller->removeAction(array(
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'in:' . implode(', ', $ids)
                )
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('removed_models', $result);
        $this->assertTrue(is_array($result['removed_models']));
        $this->assertEquals(1, count($result['removed_models']));

        $removedModels = $result['removed_models'];
        $modelName = key($removedModels);

        $this->assertTrue(is_array($removedModels[$modelName]));
        $this->assertEquals(2, count($removedModels[$modelName]));
        $this->assertTrue(in_array($ids[0], $removedModels[$modelName]));
        $this->assertTrue(in_array($ids[1], $removedModels[$modelName]));

        $this->assertNull(self::$em->getRepository(DummyArticle::clazz())->find($ids[0]));
        $this->assertNull(self::$em->getRepository(DummyArticle::clazz())->find($ids[1]));
    }

    public function testGetAction()
    {
        $articles = $this->loadDummyData();

        $result = $this->controller->getAction(array(
            'hydration' => array(
                'profile' => 'get_record'
            ),
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'eq:' . $articles[0]->getId()
                )
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('form', $result['result']);
        $form = $result['result']['form'];
        $this->assertTrue(is_array($form));
        $this->assertArrayHasKey('id', $form);
        $this->assertEquals($articles[0]->getId(), $form['id']);
        $this->assertArrayHasKey('title', $form);
        $this->assertArrayHasKey('body', $form);
    }

    public function testUpdateAction()
    {
        $article = new DummyArticle();
        $article->body = 'the body, yo';
        $article->title = 'title, yo';

        self::$em->persist($article);
        self::$em->flush();

        $result = $this->controller->updateAction(array(
            'record' => array(
                'id' => $article->id,
                'title' => ''
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('field_errors', $result);
        $this->assertArrayHasKey('title', $result['field_errors']);
        $this->assertTrue(is_array($result['field_errors']));
        $this->assertEquals(1, count($result['field_errors']));
        $this->assertArrayHasKey('title', $result['field_errors']);
        $this->assertTrue(is_array($result['field_errors']['title']));
        $this->assertEquals(1, count($result['field_errors']['title']));

        // ---

        self::$em->clear();

        /* @var DummyArticle $fetchedArticle */
        $fetchedArticle = self::$em->getRepository(DummyArticle::clazz())->find($article->id);

        $this->assertEquals('title, yo', $fetchedArticle->title);
        $this->assertEquals($article->body, $fetchedArticle->body);

        $result = $this->controller->updateAction(array(
            'hydration' => array(
                'profile' => 'get_record'
            ),
            'record' => array(
                'id' => $fetchedArticle->id,
                'title' => 'new title',
                'body' => 'new body'
            )
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('updated_models', $result);
        $this->assertTrue(is_array($result['updated_models']));
        $this->assertEquals(1, count($result['updated_models']));
        $this->assertArrayHasKey('result', $result);
        $this->assertTrue(is_array($result['result']));
        $this->assertArrayHasKey('form', $result['result']);
        $this->assertTrue(is_array($result['result']['form']));

        self::$em->clear();

        /* @var DummyArticle $updatedArticle */
        $updatedArticle = self::$em->getRepository(DummyArticle::clazz())->find($article->id);

        $this->assertNotNull($updatedArticle);
        $this->assertEquals('new title', $updatedArticle->title);
        $this->assertEquals('new body', $updatedArticle->body);
    }
}