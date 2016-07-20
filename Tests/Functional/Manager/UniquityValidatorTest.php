<?php

namespace Modera\ConfigBundle\Tests\Functional\Manager;

use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Modera\ConfigBundle\Manager\UniquityValidator;
use Modera\ConfigBundle\Tests\Fixtures\Entities\User;
use Modera\ConfigBundle\Tests\Functional\AbstractFunctionalTestCase;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class UniquityValidatorTest extends AbstractFunctionalTestCase
{
    public function testIsValidForSaving_withoutOwner()
    {
        $ce1 = new ConfigurationEntry('cf_1');
        $ce1->setValue('foo');

        $uv = new UniquityValidator(self::$em, array('owner_entity' => null));
        $this->assertTrue($uv->isValidForSaving($ce1));

        self::$em->persist($ce1);
        self::$em->flush();

        $this->assertFalse($uv->isValidForSaving($ce1));
    }

    public function testIsValidForSaving_withOwner()
    {
        $vasya = new User('vasya');

        self::$em->persist($vasya);
        self::$em->flush();

        $ce1 = new ConfigurationEntry('cf_1');
        $ce1->setValue('foo');
        $ce1->setOwner($vasya);

        $uv = new UniquityValidator(self::$em, array('owner_entity' => get_class($vasya)));
        $this->assertTrue($uv->isValidForSaving($ce1));

        self::$em->persist($ce1);
        self::$em->flush();

        $this->assertFalse($uv->isValidForSaving($ce1));
    }
}
