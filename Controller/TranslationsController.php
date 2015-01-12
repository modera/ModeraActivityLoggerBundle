<?php

namespace Modera\BackendTranslationsToolBundle\Controller;

use Modera\BackendTranslationsToolBundle\Filtering\FilterInterface;
use Modera\ServerCrudBundle\Exceptions\BadRequestException;
use Modera\DirectBundle\Annotation\Remote;
use Modera\TranslationsBundle\Entity\TranslationToken;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Modera\BackendTranslationsToolBundle\Contributions\FiltersProvider;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TranslationsController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'entity' => TranslationToken::clazz(),
            'hydration' => array(
                'groups' => array(
                    'list' => ['id', 'source', 'bundleName', 'domain', 'tokenName', 'isObsolete', 'translations'],
                ),
                'profiles' => array(
                    'list',
                )
            ),
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function listWithFiltersAction(array $params)
    {
        try {
            $filterId = null;
            $filterValue = null;
            if (isset($params['filter'])) {
                foreach ($params['filter'] as $filter) {
                    if ('__filter__' == $filter['property']) {
                        $parts = explode('-', $filter['value'], 2);
                        $filterId = $parts[0];
                        if (isset($parts[1])) {
                            $filterValue = $parts[1];
                        }
                        break;
                    }
                }

                if ($filterValue) {
                    $params['filter'] = [
                        [
                            array('property' => 'tokenName', 'value' => 'like:%' . $filterValue . '%'),
                            array(
                                'property' => 'languageTranslationTokens.translation',
                                'value' => 'like:%' . $filterValue . '%'
                            ),
                        ]
                    ];
                } else {
                    $params['filter'] = null;
                }
            }

            if (!$filterId) {
                $e = new BadRequestException('"/filter" request parameter is not provided');
                $e->setPath('/');
                $e->setParams($params);

                throw $e;
            }

            /* @var FiltersProvider $filtersProvider */
            $filtersProvider = $this->get('modera_backend_translations_tool.filters_provider');

            $filter = null;
            $filters = $filtersProvider->getItems();
            foreach ($filters['translation_token'] as $iteratedFilter) {
                /* @var FilterInterface $iteratedFilter */

                if ($iteratedFilter->getId() == $filterId && $iteratedFilter->isAllowed()) {
                    $filter = $iteratedFilter;
                    break;
                }
            }

            if (!$filter) {
                throw new \RuntimeException(sprintf('Filter with given parameter "%s" not found', $filterId));
            }

            $result = $filter->getResult($params);

            $hydratedItems = [];
            foreach ($result['items'] as $entity) {
                $hydratedItems[] = $this->hydrate($entity, $params);
            }

            $result['items'] = $hydratedItems;

            return $result;
        } catch (\Exception $e) {
            return $this->createExceptionResponse($e, ExceptionHandlerInterface::OPERATION_LIST);
        }
    }

    /**
     * @Remote
     * @param array $params
     */
    public function importAction(array $params)
    {
        $app = new Application($this->get('kernel'));
        $app->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'modera:translations:import',
        ));
        $input->setInteractive(false);

        $result = $app->run($input, new NullOutput());

        return array(
            'success'        => (0 === $result),
            'updated_models' => array(
                'modera.translations_bundle.translation_token' => [],
            ),
        );
    }

    /**
     * @Remote
     * @param array $params
     */
    public function compileAction(array $params)
    {
        $app = new Application($this->get('kernel'));
        $app->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'modera:translations:compile',
        ));
        $input->setInteractive(false);

        $result = $app->run($input, new NullOutput());

        if (0 === $result) {
            $key = 'modera_backend_translations_tool';
            /* @var \Doctrine\Common\Cache\Cache $cache */
            $cache = $this->get($key . '.cache');

            $data = array('isCompileNeeded' => false);
            if ($string = $cache->fetch($key)) {
                $data = array_merge(unserialize($string), $data);
            }
            $cache->save($key, serialize($data));

            $input = new ArrayInput(array(
                'command' => 'cache:clear',
                '--env'   => $this->container->getParameter('kernel.environment'),
            ));
            $input->setInteractive(false);
            $app->run($input, new NullOutput());
        }

        return array(
            'success' => (0 === $result),
        );
    }

    /**
     * @Remote
     * @param array $params
     */
    public function isCompileNeededAction(array $params)
    {
        $key = 'modera_backend_translations_tool';
        /* @var \Doctrine\Common\Cache\Cache $cache */
        $cache = $this->get($key . '.cache');

        $isCompileNeeded = false;
        if ($string = $cache->fetch($key)) {
            $data = unserialize($string);
            if (isset($data['isCompileNeeded'])) {
                $isCompileNeeded = $data['isCompileNeeded'];
            }
        }

        return array(
            'success' => true,
            'status'  => $isCompileNeeded,
        );
    }
}
