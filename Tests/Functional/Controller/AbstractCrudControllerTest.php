<?php

namespace Modera\ServerCrudBundle\Tests\Functional\Controller;

use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\Hydration\HydrationProfile;
use Doctrine\ORM\Mapping as Orm;
use Modera\ServerCrudBundle\Tests\Fixtures\Bundle\Contributions\ControllerActionInterceptorsProvider;
use Symfony\Component\Validator\Constraints as Assert;
use Sli\AuxBundle\Util\Toolkit;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * @Orm\Entity
 * @Orm\Table("_testing_article")
 * @Orm\HasLifecycleCallbacks
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

    public static $suicideEngaged = false;

    /**
     * @Orm\PrePersist
     * @Orm\PreUpdate
     * @Orm\PreRemove
     */
    public function suicide()
    {
        if (self::$suicideEngaged) {
            self::$suicideEngaged = false;

            throw new \RuntimeException('boom');
        }

        self::$suicideEngaged = false;
    }

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

    public static function clazz()
    {
        return get_called_class();
    }

    public static function formatNewValues(array $params, array $config, $container)
    {
        return array(
            'params' => $params,
            'config' => $config,
            'container' => $container,
        );
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
                        'id', 'title', 'body',
                    ),
                    'list' => function (DummyArticle $e) {
                        if (DummyArticle::$suicideEngaged) {
                            $e->suicide();
                        }

                        return array(
                            'id' => $e->getId(),
                            'title' => substr($e->title, 0, 10),
                            'body' => substr($e->body, 0, 10),
                        );
                    },
                    'suicide' => function () {
                        throw new \Exception();
                    },
                ),
                'profiles' => array(
                    'new_record' => HydrationProfile::create()->useGroups(array('form')),
                    'get_record' => HydrationProfile::create()->useGroups(array('form')),
                    'list' => HydrationProfile::create(false)->useGroups(array('list')),
                    'rotten_profile' => HydrationProfile::create()->useGroups(array('suicide')),
                ),
            ),
        );
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class AbstractCrudControllerTest extends FunctionalTestCase
{
    /* @var DataController */
    private $controller;

    // override
    public function doSetUp()
    {
        $this->controller = new DataController();
        $this->controller->setContainer(self::$container);

        DummyArticle::$suicideEngaged = false;
    }

    // override
    public static function doSetUpBeforeClass()
    {
        $driver = new AnnotationDriver(
            self::$kernel->getContainer()->get('annotation_reader'),
            array(__DIR__)
        );

        Toolkit::addMetadataDriverForEntityManager(self::$em, $driver, __NAMESPACE__);
        Toolkit::createTableFoEntity(self::$em, DummyArticle::clazz());
    }

    public static function doTearDownAfterClass()
    {
        Toolkit::dropTableForEntity(self::$em, DummyArticle::clazz());
    }

    /**
     * @return ControllerActionInterceptorsProvider
     */
    private function getDummyInterceptor()
    {
        return self::$container->get('modera_server_crud_dummy_bundle.contributions.controller_action_interceptors_provider');
    }

    private function assertValidInterceptorInvocation($requestParams, $type)
    {
        $invocation = $this->getDummyInterceptor()->interceptor->invocations[$type];

        $this->assertEquals(
            1,
            count($invocation),
            "It is expected that interceptor for '$type' would be invoked only once!"
        );
        $this->assertSame($requestParams, $invocation[0][0]);
        $this->assertSame($this->controller, $invocation[0][1]);
    }

    public function testCreateAction()
    {
        $requestParams = array(
            'record' => array(
                'body' => 'Some text goes here',
            ),
        );

        // validation for "title" field should fail
        $result = $this->controller->createAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'create');

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
                'profile' => 'new_record',
            ),
            'record' => array(
                'title' => 'Some title',
                'body' => 'Some text goes here',
            ),
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

    private function assertValidExceptionResult(array $result)
    {
        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
    }

    public function testCreateActionWithException()
    {
        DummyArticle::$suicideEngaged = true;

        $result = $this->controller->createAction(array(
            'record' => array(
                'title' => 'opa',
                'body' => 'hola',
            ),
        ));

        $this->assertValidExceptionResult($result);
    }

    /**
     * @return DummyArticle[]
     */
    private function loadDummyData()
    {
        $result = array();

        for ($i = 0; $i < 5; ++$i) {
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

        $requestParams = array(
            'limit' => 3,
            'sort' => array(
                array('property' => 'id', 'direction' => 'DESC'),
            ),
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'notIn:6',
                ),
            ),
            'hydration' => array(
                'profile' => 'list',
            ),
        );

        $result = $this->controller->listAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'list');

        $me = $this;

        $assertValidItem = function ($items, $index) use ($me) {
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

    public function testListActionWithException()
    {
        $this->loadDummyData();

        DummyArticle::$suicideEngaged = true;

        $result = $this->controller->listAction(array(
            'hydration' => array(
                'profile' => 'list',
            ),
        ));

        $this->assertValidExceptionResult($result);
    }

    public function testRemoveAction()
    {
        $articles = $this->loadDummyData();

        $ids = array(
            $articles[0]->getId(),
            $articles[1]->getId(),
        );

        $requestParams = array(
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'in:'.implode(', ', $ids),
                ),
            ),
        );

        $result = $this->controller->removeAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'remove');

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

    public function testRemoveActionWithException()
    {
        $articles = $this->loadDummyData();

        $ids = array(
            $articles[0]->getId(),
            $articles[1]->getId(),
        );

        DummyArticle::$suicideEngaged = true;

        $result = $this->controller->removeAction(array(
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'in:'.implode(', ', $ids),
                ),
            ),
        ));

        $this->assertValidExceptionResult($result);
    }

    public function testGetAction()
    {
        $articles = $this->loadDummyData();

        $requestParams = array(
            'hydration' => array(
                'profile' => 'get_record',
            ),
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'eq:'.$articles[0]->getId(),
                ),
            ),
        );

        $result = $this->controller->getAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'get');

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

    public function testGetActionWithException()
    {
        $articles = $this->loadDummyData();

        DummyArticle::$suicideEngaged = true;

        $requestParams = array(
            'hydration' => array(
                'profile' => 'rotten_profile',
            ),
            'filter' => array(
                array(
                    'property' => 'id',
                    'value' => 'eq:'.$articles[0]->getId(),
                ),
            ),
        );
        $result = $this->controller->getAction($requestParams);

        $this->assertValidExceptionResult($result);
    }

    public function testUpdateAction()
    {
        $article = new DummyArticle();
        $article->body = 'the body, yo';
        $article->title = 'title, yo';

        self::$em->persist($article);
        self::$em->flush();

        $requestParams = array(
            'record' => array(
                'id' => $article->id,
                'title' => '',
            ),
        );
        $result = $this->controller->updateAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'update');

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
                'profile' => 'get_record',
            ),
            'record' => array(
                'id' => $fetchedArticle->id,
                'title' => 'new title',
                'body' => 'new body',
            ),
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

    private function createDummyArticles($total)
    {
        /* @var DummyArticle[] $entities */
        $entities = array();
        for ($i = 0; $i < $total; ++$i) {
            $article = new DummyArticle();
            $article->body = 'body'.$i;
            $article->title = 'title'.$i;

            $entities[] = $article;

            self::$em->persist($article);
        }
        self::$em->flush();

        return $entities;
    }

    /**
     * @group MPFE-586-1
     */
    public function testBatchUpdateActionWithRecords()
    {
        $entities = $this->createDummyArticles(2);

        $requestParams = array(
            'records' => array(
                array(
                    'id' => $entities[0]->id,
                    'body' => 'body0_foo',
                    'title' => 'title0_foo',
                ),
                array(
                    'id' => $entities[1]->id,
                    'body' => 'body1_foo',
                    'title' => 'title1_foo',
                ),
            ),
        );
        $result = $this->controller->batchUpdateAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'batchUpdate');

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('updated_models', $result);
        $this->assertEquals(1, count($result['updated_models']));
        $updatedModels = array_values($result['updated_models']);
        $this->assertEquals(2, count($updatedModels[0]));
        $this->assertTrue(in_array($entities[0]->id, $updatedModels[0]));
        $this->assertTrue(in_array($entities[1]->id, $updatedModels[0]));

        self::$em->clear();

        $article1 = self::$em->find(DummyArticle::clazz(), $entities[0]->id);
        $this->assertEquals('body0_foo', $article1->body);
        $this->assertEquals('title0_foo', $article1->title);

        $article2 = self::$em->find(DummyArticle::clazz(), $entities[1]->id);
        $this->assertEquals('body1_foo', $article2->body);
        $this->assertEquals('title1_foo', $article2->title);
    }

    /**
     * @group MPFE-586
     */
    public function testBatchUpdateActionWithRecordsErrorHandling()
    {
        $entities = $this->createDummyArticles(2);

        $result = $this->controller->batchUpdateAction(array(
            'records' => array(
                array(
                    'id' => $entities[0]->id,
                    'body' => 'body0_foo',
                    'title' => '',
                ),
                array(
                    'id' => $entities[1]->id,
                    'body' => 'body1_foo',
                    'title' => 'title1_foo',
                ),
            ),
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);

        $this->assertArrayHasKey('errors', $result);
        $this->assertTrue(is_array($result['errors']));
        $this->assertEquals(1, count($result['errors']));
        $error = $result['errors'][0];

        $this->assertArrayHasKey('id', $error);
        $this->assertArrayHasKey('id', $error['id']);
        $this->assertEquals($entities[0]->id, $error['id']['id']);

        $this->assertArrayHasKey('errors', $error);

        self::$em->clear();

        // none of them must have been updated
        $article1 = self::$em->find(DummyArticle::clazz(), $entities[0]->id);
        $this->assertEquals('body0', $article1->body);
        $this->assertEquals('title0', $article1->title);

        $article2 = self::$em->find(DummyArticle::clazz(), $entities[1]->id);
        $this->assertEquals('body1', $article2->body);
        $this->assertEquals('title1', $article2->title);
    }

    /**
     * @group MPFE-586
     */
    public function testBatchUpdateActionWithQueriesAndRecord()
    {
        $entities = $this->createDummyArticles(3);

        $requestParams = array(
            'queries' => array(
                array(
                    'filter' => array(
                        array(
                            'property' => 'id',
                            'value' => 'eq:'.$entities[0]->id,
                        ),
                    ),
                ),
                array(
                    'filter' => array(
                        array(
                            'property' => 'title',
                            'value' => 'eq:'.$entities[2]->title,
                        ),
                    ),
                ),
            ),
            'record' => array(
                'title' => 'hello',
            ),
        );
        $result = $this->controller->batchUpdateAction($requestParams);

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);

        $this->assertArrayHasKey('updated_models', $result);
        $this->assertEquals(1, count($result['updated_models']));
        $updatedModels = array_values($result['updated_models']);
        $this->assertTrue(is_array($updatedModels));
        $this->assertEquals(1, count($updatedModels));
        $this->assertEquals(2, count($updatedModels[0]));
        $this->assertTrue(in_array($entities[0]->id, $updatedModels[0]));
        $this->assertTrue(in_array($entities[2]->id, $updatedModels[0]));

        self::$em->clear();

        $article1 = self::$em->find(DummyArticle::clazz(), $entities[0]->id);
        $this->assertEquals('hello', $article1->title);
        $this->assertEquals($entities[0]->body, $article1->body);

        $article3 = self::$em->find(DummyArticle::clazz(), $entities[2]->id);
        $this->assertEquals('hello', $article3->title);
        $this->assertEquals($entities[2]->body, $article3->body);

        // should not have been updated
        $article2 = self::$em->find(DummyArticle::clazz(), $entities[1]->id);
        $this->assertEquals('title1', $article2->title);
        $this->assertEquals($entities[1]->body, $article2->body);
    }

    public function testBatchUpdateActionWithQueriesAndRecordErrorHandling()
    {
        $entities = $this->createDummyArticles(3);

        $result = $this->controller->batchUpdateAction(array(
            'queries' => array(
                array(
                    'filter' => array(
                        array(
                            'property' => 'id',
                            'value' => 'eq:'.$entities[0]->id,
                        ),
                    ),
                ),
                array(
                    'filter' => array(
                        array(
                            'property' => 'title',
                            'value' => 'eq:'.$entities[2]->title,
                        ),
                    ),
                ),
            ),
            'record' => array(
                'title' => '',
            ),
        ));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('success', $result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('errors', $result);
        $this->assertTrue(is_array($result['errors']));
        $this->assertEquals(2, count($result['errors']));

        $errors = $result['errors'];

        $this->assertArrayHasKey('id', $errors[0]);
        $this->assertTrue(is_array($errors[0]));
        $this->assertArrayHasKey('id', $errors[0]['id']);
        $this->assertEquals($entities[0]->id, $errors[0]['id']['id']);

        $this->assertArrayHasKey('id', $errors[1]);
        $this->assertTrue(is_array($errors[1]));
        $this->assertArrayHasKey('id', $errors[1]['id']);
        $this->assertEquals($entities[2]->id, $errors[1]['id']['id']);
    }

    // this test will result in having EM closed
    public function testUpdateActionWithException()
    {
        $articles = $this->loadDummyData();

        DummyArticle::$suicideEngaged = true;

        $result = $this->controller->updateAction(array(
            'record' => array(
                'id' => $articles[0]->id,
                'title' => 'yo',
                'body' => 'ogo',
            ),
        ));

        $this->assertValidExceptionResult($result);
    }

    public function testGetNewRecordValuesAction()
    {
        $requestParams = array('params');

        $output = $this->controller->getNewRecordValuesAction($requestParams);

        $this->assertValidInterceptorInvocation($requestParams, 'getNewRecordValues');

        $this->assertTrue(is_array($output));
        $this->assertArrayHasKey('params', $output);
        $this->assertSame($requestParams, $output['params']);
        $this->assertArrayHasKey('config', $output);
        $this->assertTrue(is_array($output['config']));
        // we can't do just values comparison here because it goes to some kind of recursion
        $this->assertSame(array_keys($this->controller->getPreparedConfig()), array_keys($output['config']));
        $this->assertArrayHasKey('container', $output);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $output['container']);
    }
}
