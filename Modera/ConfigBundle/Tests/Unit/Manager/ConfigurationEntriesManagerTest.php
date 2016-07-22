<?php

namespace Modera\ConfigBundle\Tests\Unit\Manager;

use Doctrine\ORM\EntityManager;
use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Modera\ConfigBundle\Manager\ConfigurationEntriesManager;
use Modera\ConfigBundle\Manager\UniquityValidator;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ConfigurationEntriesManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Modera\ConfigBundle\Manager\ConfigurationEntryAlreadyExistsException
     */
    public function testSave()
    {
        $entry = \Phake::mock(ConfigurationEntry::class);

        $em = \Phake::mock(EntityManager::class);

        // exception must be thrown when UniquityValidator said that given entry is not unique
        $uv = \Phake::mock(UniquityValidator::class);
        \Phake::when($uv)
            ->isValidForSaving($entry)
            ->thenReturn(false)
        ;

        $cem = new ConfigurationEntriesManager($em, array(), $uv);

        $cem->save($entry);
    }

    public function testSave_noUniquityValidatorGiven()
    {
        $entry = \Phake::mock(ConfigurationEntry::class);
        $em = \Phake::mock(EntityManager::class);

        $cem = new ConfigurationEntriesManager($em);

        $cem->save($entry);

        \Phake::verify($em)
            ->persist($entry)
        ;
        \Phake::verify($em)
            ->flush($entry)
        ;
    }
}
