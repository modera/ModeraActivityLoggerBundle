<?php

namespace Modera\LanguagesBundle\Tests\Unit\Entity;

use Modera\LanguagesBundle\Entity\Language;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguageTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $language = new Language;

        $language->setLocale('en');
        $this->assertEquals('English', $language->getName());
        $this->assertEquals('Английский', $language->getName('ru'));
        $this->assertEquals('Inglise', $language->getName('et'));

        $language->setLocale('ru');
        $this->assertEquals('Русский', $language->getName());
        $this->assertEquals('Russian', $language->getName('en'));
        $this->assertEquals('Vene', $language->getName('et'));

        $language->setLocale('et');
        $this->assertEquals('Eesti', $language->getName());
        $this->assertEquals('Estonian', $language->getName('en'));
        $this->assertEquals('Эстонский', $language->getName('ru'));

        $language->setLocale('undefined');
        $this->assertEquals('Undefined', $language->getName());
    }
}
