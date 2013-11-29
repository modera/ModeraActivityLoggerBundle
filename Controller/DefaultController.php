<?php

namespace Modera\BackendModuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Neton\DirectBundle\Annotation\Remote;

class DefaultController extends Controller
{
    /**
     * @return \Modera\Module\Repository\ModuleRepository
     */
    protected function getModuleRepository()
    {
        return $this->get('modera_module.repository.module_repository');
    }

    /**
     * @param array $versions
     * @return \Packagist\Api\Result\Package\Version
     */
    protected function getPackageLatestVersion(array $versions)
    {
        ksort($versions);
        return end($versions);
    }

    /**
     * @param $name
     * @return array|null
     */
    protected function getModuleInfo($name, $extended = false)
    {
        $package = $this->getModuleRepository()->getPackage($name);
        if (!$package) {
            return null;
        }

        $latest = $this->getPackageLatestVersion($package->getVersions());
        $installed = $this->getModuleRepository()->getInstalledByName($latest->getName());
        $lastVersion = $latest->getVersion();
        $currentVersion = null;
        $updateAvailable = false;

        if ($installed) {
            $currentVersion = $this->getModuleRepository()->formatVersion($installed);
            if ($installed->getPrettyVersion() !== $lastVersion) {
                $updateAvailable = true;
            }
        }

        $result = array(
            'id'                 => $latest->getName(),
            'name'               => $latest->getName(),
            'description'        => $latest->getDescription(),
            'license'            => $latest->getLicense(),
            'lastVersion'        => $lastVersion,
            'currentVersion'     => $currentVersion,
            'installed'          => $installed ? true : false,
            'updateAvailable'    => $updateAvailable,
        );

        if ($extended) {
            //TODO
        }

        return $result;
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function getInstalledModulesAction(array $params)
    {
        $result = array();
        $packages = $this->getModuleRepository()->getInstalled();
        foreach ($packages as $package) {
            $info = $this->getModuleInfo($package->getName());
            if ($info) {
                $result[] = $info;
            }
        }

        return $result;
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function getAvailableModulesAction(array $params)
    {
        $result = array();
        $data = $this->getModuleRepository()->getAvailable();
        foreach ($data as $name) {
            $info = $this->getModuleInfo($name);
            if ($info) {
                $result[] = $info;
            }
        }

        return $result;
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function getModuleDetailsAction(array $params)
    {
        return $this->getModuleInfo($params['id']);
    }
}
