<?php

namespace Modera\LanguagesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Modera\LanguagesBundle\Entity\Language;
use Modera\LanguagesBundle\DependencyInjection\ModeraLanguagesExtension;

/**
 * From config to database
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SyncLanguagesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('modera:languages:config-sync')
            ->setDescription('Synchronize languages config with database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $languages = $this->getConfigLanguages();
        $dbLanguages = $em->getRepository(Language::clazz())->findAll();

        $updated = array();
        $tableRows = array();
        if (count($dbLanguages)) {
            /* @var Language $dbLanguage */
            foreach ($dbLanguages as $dbLanguage) {
                $language = null;
                foreach ($languages as $_language) {
                    if ($_language['locale'] == $dbLanguage->getLocale()) {
                        $language = $_language;
                        break;
                    }
                }

                if (is_array($language)) {
                    $updated[] = $language['locale'];
                    $dbLanguage->setEnabled($language['is_enabled'] ? true : false);
                } else {
                    $dbLanguage->setEnabled(false);
                }
                $em->persist($dbLanguage);
                $tableRows[] = $this->tableRow($dbLanguage);
            }
        }

        foreach ($languages as $language) {
            if (!in_array($language['locale'], $updated)) {
                $dbLanguage = new Language;
                $dbLanguage->setLocale($language['locale']);
                $dbLanguage->setEnabled($language['is_enabled'] ? true : false);
                $em->persist($dbLanguage);
                $tableRows[] = $this->tableRow($dbLanguage);
            }
        }

        $em->flush();

        $table = $this->getApplication()->getHelperSet()->get('table');
        $table->setHeaders(array('locale', 'name', 'enabled'));
        $table->setRows($tableRows);
        $table->render($output);
    }

    /**
     * @return array
     */
    protected function getConfigLanguages()
    {
        return $this->getContainer()->getParameter(ModeraLanguagesExtension::CONFIG_KEY);
    }

    /**
     * @param Language $dbLanguage
     * @return array
     */
    private function tableRow(Language $dbLanguage)
    {
        return array(
            $dbLanguage->getLocale(),
            $dbLanguage->getName(),
            $dbLanguage->getEnabled(),
        );
    }
}
