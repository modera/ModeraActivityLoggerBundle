<?php

namespace Modera\TranslationsBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Catalogue\DiffOperation;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Modera\LanguagesBundle\Entity\Language;
use Modera\TranslationsBundle\Entity\TranslationToken;
use Modera\TranslationsBundle\Entity\LanguageTranslationToken;
use Modera\TranslationsBundle\Service\TranslationHandlersChain;
use Modera\TranslationsBundle\Handling\TranslationHandlerInterface;

/**
 * From files to database
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ImportTranslationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('modera:translations:import')
            ->setDescription('Finds and imports translations from files to database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /* @var TranslationHandlersChain $thc */
        $thc = $this->getContainer()->get('modera_translations.service.translation_handlers_chain');

        $languages = $em->getRepository(Language::clazz())->findBy(array(
            'isEnabled' => true
        ));
        if (!count($languages)) {
            $defaultLocale = $this->getContainer()->getParameter('locale');

            $language = new Language;
            $language->setLocale($defaultLocale);
            $language->setEnabled(true);
            $em->persist($language);
            $em->flush();

            $languages = array($language);
        }

        $handlers = $thc->getHandlers();
        if (count($handlers) > 0) {

            $imported = false;

            /* @var TranslationHandlerInterface $handler */
            foreach ($handlers as $handler) {

                $bundleName = $handler->getBundleName();

                foreach ($handler->getSources() as $source) {

                    $tokens = $em->getRepository(TranslationToken::clazz())->findBy(array(
                        'source'     => $source,
                        'bundleName' => $bundleName,
                    ));

                    /* @var Language $language */
                    foreach ($languages as $language) {
                        $locale = $language->getLocale();

                        $extractedCatalogue = $handler->extract($source, $locale);
                        if (null === $extractedCatalogue) {
                            continue;
                        }

                        $currentCatalogue = new MessageCatalogue($locale);
                        /* @var TranslationToken $token */
                        foreach ($tokens as $token) {
                            if ($token->isObsolete()) {
                                continue;
                            }

                            /* @var LanguageTranslationToken $ltt */
                            foreach ($token->getLanguageTranslationTokens() as $ltt) {
                                $lang = $ltt->getLanguage();
                                if ($lang && $lang->getLocale() == $locale) {
                                    $currentCatalogue->set($token->getTokenName(), $ltt->getTranslation(), $token->getDomain());
                                    break;
                                }
                            }
                        }

                        // process catalogues
                        $operation = new DiffOperation($currentCatalogue, $extractedCatalogue);

                        foreach ($operation->getDomains() as $domain) {

                            $newMessages = $operation->getNewMessages($domain);
                            $obsoleteMessages = $operation->getObsoleteMessages($domain);

                            if (count($newMessages) || count($obsoleteMessages)) {
                                $imported = true;

                                $output->writeln(
                                    '>>> ' . $bundleName . ' : ' . $source . ' : ' . $locale . ' : ' . $domain
                                );
                            }

                            if (count($newMessages)) {
                                $output->writeln(sprintf('    <fg=green>New messages: %s</>', count($newMessages)));
                                foreach ($newMessages as $tokenName => $translation) {
                                    $token = $this->findOrCreateTranslationToken(
                                        $source, $bundleName, $domain, $tokenName
                                    );
                                    $token->setObsolete(false);

                                    $ltt = $em->getRepository(LanguageTranslationToken::clazz())->findOneBy(array(
                                        'language'         => $language,
                                        'translationToken' => $token,
                                        'translation'      => $translation,
                                    ));
                                    if (!$ltt) {
                                        $ltt = new LanguageTranslationToken;
                                        $ltt->setLanguage($language);
                                        $token->addLanguageTranslationToken($ltt);
                                    }

                                    if ($ltt->isNew()) {
                                        $ltt->setTranslation($translation);
                                    }

                                    $em->persist($token);
                                }
                                $em->flush();
                            }

                            if (count($obsoleteMessages)) {
                                $output->writeln(sprintf('    <fg=red>Obsolete messages: %s</>', count($obsoleteMessages)));
                                foreach ($obsoleteMessages as $tokenName => $translation) {
                                    $token = $this->findOrCreateTranslationToken(
                                        $source, $bundleName, $domain, $tokenName
                                    );
                                    $token->setObsolete(true);
                                    $em->persist($token);
                                }
                                $em->flush();
                            }
                        }
                    }
                }
            }
        }

        if ($imported) {
            $output->writeln('>>> Translations have been successfully imported');
        } else {
            $output->writeln('>>> Nothing to import');
        }
    }

    /**
     * @param $source
     * @param $bundleName
     * @param $domain
     * @param $tokenName
     * @return TranslationToken
     */
    private function findOrCreateTranslationToken($source, $bundleName, $domain, $tokenName)
    {
        /* @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $token = $em->getRepository(TranslationToken::clazz())->findOneBy(array(
            'source'     => $source,
            'bundleName' => $bundleName,
            'domain'     => $domain,
            'tokenName'  => $tokenName,
        ));

        if (!$token) {
            $token = new TranslationToken;
            $token->setSource($source);
            $token->setBundleName($bundleName);
            $token->setDomain($domain);
            $token->setTokenName($tokenName);
            $em->persist($token);
            $em->flush();
        }

        return $token;
    }
} 