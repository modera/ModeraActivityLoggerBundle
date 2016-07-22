<?php

namespace Modera\ServerCrudBundle\Hydration;

/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
class ConfigAnalyzer
{
    private $rawConfig;

    /**
     * @param array $rawConfig
     */
    public function __construct(array $rawConfig)
    {
        $this->rawConfig = $rawConfig;
    }

    /**
     * @throws UnknownHydrationProfileException
     *
     * @param string $profileName
     *
     * @return mixed
     */
    public function getProfileDefinition($profileName)
    {
        $isFound = isset($this->rawConfig['profiles'][$profileName]);
        if (!$isFound) {
            $isFound = in_array($profileName, $this->rawConfig['profiles']);

            if ($isFound) {
                /*
                 * When hydration config looks like this:
                 *
                 * array(
                 *     'groups' => array(
                 *         'list' => ['id', 'username']
                 *     ),
                 *     'profiles' => array(
                 *         'list'
                 *     )
                 * );
                 */
                return HydrationProfile::create(false)->useGroups(array($profileName));
            }
        }

        if (!$isFound) {
            $e = new UnknownHydrationProfileException(
                "Hydration profile '$profileName' is not found."
            );
            $e->setProfileName($profileName);

            throw $e;
        }

        $profile = $this->rawConfig['profiles'][$profileName];
        if (is_array($profile)) {
            /*
             * Will be used when hydration config looks akin to the following:
             *
             *  array(
             *     'groups' => array(
             *         'author' => ['author.id', 'author.firstname', 'author.lastname'],
             *         'tags' => function() { ... }
             *     ),
             *     'profiles' => array(
             *         'mixed' => ['author', 'tags']
             *     )
             * );
             */
            return HydrationProfile::create()->useGroups($profile);
        }

        return $profile;
    }

    public function getGroupDefinition($groupName)
    {
        if (!isset($this->rawConfig['groups'][$groupName])) {
            $e = new UnknownHydrationGroupException(
                "Hydration group '$groupName' is not found."
            );
            $e->setGroupName($groupName);

            throw $e;
        }

        return $this->rawConfig['groups'][$groupName];
    }
}
