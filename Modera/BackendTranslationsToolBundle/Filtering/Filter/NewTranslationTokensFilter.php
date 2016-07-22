<?php

namespace Modera\BackendTranslationsToolBundle\Filtering\Filter;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class NewTranslationTokensFilter extends AbstractTranslationTokensFilter
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'new';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'New';
    }

    /**
     * {@inheritdoc}
     */
    public function getCount(array $params)
    {
        if (!isset($params['filter'])) {
            $params['filter'] = array();
        }
        $params['filter'] = array_merge($params['filter'], $this->getFilter());

        return parent::getCount($params);
    }

    /**
     * {@inheritdoc}
     */
    public function getResult(array $params)
    {
        if (!isset($params['filter'])) {
            $params['filter'] = array();
        }
        $params['filter'] = array_merge($params['filter'], $this->getFilter());

        return parent::getResult($params);
    }

    /**
     * @return array
     */
    private function getFilter()
    {
        static $filter = null;

        if (null === $filter) {
            try {
                $q = $this->em()->createQuery(
                    'SELECT IDENTITY(ltt.translationToken) as translationToken '.
                    'FROM ModeraTranslationsBundle:LanguageTranslationToken ltt '.
                    'LEFT JOIN ltt.language l '.
                    'WHERE ltt.isNew=true AND l.isEnabled=true GROUP BY ltt.translationToken'
                );
                $result = $q->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            } catch (\Exception $e) {
                $result = array();
            }

            $ids = array_map(function ($row) {
                return $row['translationToken'];
            }, $result);

            $filter[] = ['property' => 'isObsolete', 'value' => 'eq:false'];
            $filter[] = ['property' => 'id', 'value' => 'in:'.implode(',', $ids)];
        }

        return $filter;
    }
}
