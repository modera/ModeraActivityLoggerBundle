<?php

namespace Modera\ServerCrudBundle\Tests\Unit\Hydration;

use Modera\ServerCrudBundle\Hydration\HydrationProfile;
use Modera\ServerCrudBundle\Hydration\HydrationService;
use Modera\ServerCrudBundle\Hydration\UnknownHydrationProfileException;

class Author
{
    public $firstname;

    public $lastname;
}

class Article
{
    public $title;

    public $body;

    public $author;

    /* @var ArticleComment[] */
    public $comments = array();
}

class ArticleComment
{
    public $createdAt;

    public $author;

    public $body;

    public function __construct(Author $author, $body)
    {
        $this->author = $author;
        $this->body = $body;

        $this->createdAt = new \DateTime('now');
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class HydrationServiceTest extends \PHPUnit_Framework_TestCase
{
    private $container;

    /* @var HydrationService $service */
    private $service;

    private $config;
    private $article;

    // override
    public function setUp()
    {
        $this->container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $this->service = new HydrationService($this->container);

        $this->config = array(
            'groups' => array(
                'tags' => function () {

                },
                'comments' => function (Article $e) {
                    $result = array();

                    foreach ($e->comments as $comment) {
                        $result[] = array(
                            'body' => $comment->body,
                        );
                    }

                    return $result;
                },
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
                'list' => HydrationProfile::create(false)->useGroups(array('list')),
                'form' => HydrationProfile::create()->useGroups(array('form', 'comments', 'author')),
                'author',
            ),
        );

        $author = new Author();
        $author->firstname = 'Vassily';
        $author->lastname = 'Pupkin';

        $article = new Article();
        $article->author = $author;
        $article->title = 'Foo title';
        $article->body = 'Bar body';
        $article->comments = array(
            new ArticleComment($author, 'Comment1'),
        );

        $this->article = $article;
    }

    private function assertValidAuthorResult(array $result)
    {
        $this->assertArrayHasKey('author', $result);
        $this->assertTrue(is_array($result['author']));
        $this->assertArrayHasKey('firstname', $result['author']);
        $this->assertEquals($this->article->author->firstname, $result['author']['firstname']);
        $this->assertArrayHasKey('lastname', $result['author']);
        $this->assertEquals($this->article->author->lastname, $result['author']['lastname']);
    }

    private function assertValidFormResult(array $result)
    {
        $this->assertArrayHasKey('form', $result);
        $this->assertTrue(is_array($result['form']));
        $this->assertArrayHasKey('title', $result['form']);
        $this->assertEquals($this->article->title, $result['form']['title']);
        $this->assertArrayHasKey('body', $result['form']);
        $this->assertEquals($this->article->body, $result['form']['body']);
    }

    public function testHydrate()
    {
        $result = $this->service->hydrate($this->article, $this->config, 'form');

        $this->assertTrue(is_array($result));

        $this->assertValidFormResult($result);

        $this->assertArrayHasKey('comments', $result);
        $this->assertTrue(is_array($result['comments']));
        $this->assertEquals(1, count($result['comments']));
        $this->assertArrayHasKey(0, $result['comments']);
        $this->assertTrue(is_array($result['comments'][0]));
        $this->assertEquals($this->article->comments[0]->body, $result['comments'][0]['body']);

        $this->assertValidAuthorResult($result);
    }

    public function testHydrateWithGroup()
    {
        $result = $this->service->hydrate($this->article, $this->config, 'form', 'comments');

        // when one group is specified then no grouping is used
        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $this->assertArrayHasKey(0, $result);
        $this->assertTrue(is_array($result[0]));
        $this->assertArrayHasKey('body', $result[0]);
        $this->assertEquals($this->article->comments[0]->body, $result[0]['body']);

        $result = $this->service->hydrate($this->article, $this->config, 'form', array('form', 'author'));

        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));

        $this->assertArrayHasKey('form', $result);
        $this->assertValidFormResult($result);

        $this->assertArrayHasKey('author', $result);
        $this->assertValidAuthorResult($result);
    }

    public function testHydrateWithNoResultGroupingAllowed()
    {
        $result = $this->service->hydrate($this->article, $this->config, 'list');

        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));
        $this->assertArrayHasKey('title', $result);
        $this->assertEquals($this->article->title, $result['title']);
        $this->assertArrayHasKey('body', $result);
        $this->assertEquals($this->article->body, $result['body']);
    }

    public function testHydrateWithNoResultGroupingAllowedButGroupSpecified()
    {
        $this->markTestIncomplete();
    }

    public function testWhenUnknownHydrationProfileIsSpecified()
    {
        $thrownException = null;
        try {
            $this->service->hydrate($this->article, $this->config, 'blahblah');
        } catch (UnknownHydrationProfileException $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('blahblah', $thrownException->getProfileName());
    }

    public function testHydrateWhenHydrationProfileSpecifiedInShortManner()
    {
        $result = $this->service->hydrate($this->article, $this->config, 'author');

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('firstname', $result);
        $this->assertArrayHasKey('lastname', $result);
    }
}
