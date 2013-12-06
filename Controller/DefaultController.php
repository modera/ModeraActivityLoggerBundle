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

    /**
     * @Remote
     *
     * @param array $params
     */
    public function requireAction(array $params)
    {
        $response = array('success' => false, 'msg' => 'Error', 'status' => array());

        $package = $this->getModuleRepository()->getPackage($params['id']);
        if ($package) {
            $latest = $this->getPackageLatestVersion($package->getVersions());
            $response['status'] = array(
                'method'  => 'require',
                'name'    => $latest->getName(),
                'version' => $latest->getVersion(),
            );
            $params = $response['status'];
            try {
                $this->getModuleRepository()->connect(8080, function($remote, $connection) use ($params, &$response) {
                    $remote->call($params, function($resp) use ($connection, &$response) {
                        $connection->end();
                        $response = array_merge($response, (array) $resp);
                    });
                });
            } catch (\Exception $e) {
                $response['msg'] = $e->getMessage();
            }
        }

        return $response;
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function removeAction(array $params)
    {
        $response = array('success' => false, 'msg' => 'Error', 'status' => array());

        $response['status'] = array(
            'method'  => 'remove',
            'name'    => $params['id'],
        );
        $params = $response['status'];
        try {
            $this->getModuleRepository()->connect(8080, function($remote, $connection) use ($params, &$response) {
                $remote->call($params, function($resp) use ($connection, &$response) {
                    $connection->end();
                    $response = array_merge($response, (array) $resp);
                });
            });
        } catch (\Exception $e) {
            $response['msg'] = $e->getMessage();
        }

        return $response;
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function statusAction(array $params)
    {
        $response = array(
            'success' => false,
            'working' => false,
            'msg'     => '',
        );
        try {
            $this->getModuleRepository()->connect(8080, function($remote, $connection) use ($params, &$response) {
                $remote->status($params, function($resp) use ($connection, &$response) {
                    $connection->end();
                    $response = array_merge($response, (array) $resp);
                });
            });
        } catch (\Exception $e) {
            $response['msg'] = $e->getMessage();
        }

        return $response;
    }
}
