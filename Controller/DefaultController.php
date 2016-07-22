<?php

namespace Modera\BackendModuleBundle\Controller;

use Modera\DirectBundle\Annotation\Remote;
use Modera\Module\Repository\ModuleRepository;
use Modera\BackendModuleBundle\ModeraBackendModuleBundle;
use Modera\BackendModuleBundle\DependencyInjection\ModeraBackendModuleExtension;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DefaultController extends Controller
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;

    /**
     * @return \Modera\Module\Repository\ModuleRepository
     */
    private function getModuleRepository()
    {
        if (!$this->moduleRepository) {
            $workingDir = dirname($this->container->get('kernel')->getRootdir());
            $this->moduleRepository = new ModuleRepository($workingDir);
        }

        return $this->moduleRepository;
    }

    /**
     * @return int
     */
    private function getModuleServicePort()
    {
        return $this->container->getParameter(ModeraBackendModuleExtension::CONFIG_KEY . '.module-service-port');
    }

    /**
     * @return string
     */
    private function getModuleServicePathPrefix()
    {
        return $this->container->getParameter(ModeraBackendModuleExtension::CONFIG_KEY . '.module-service-path-prefix');
    }

    /**
     * @return string
     */
    private function getDefaultLogo()
    {
        return '/bundles/moderabackendmodule/images/default.png';
    }

    /**
     * @param array $versions
     * @return \Packagist\Api\Result\Package\Version
     */
    private function getPackageLatestVersion(array $versions)
    {
        ksort($versions);
        return end($versions);
    }

    /**
     * @param $name
     * @return array|null
     */
    private function getModuleInfo($name, $extended = false)
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
            if ($installed->getSourceReference() !== $latest->getSource()->getReference()) {
                $updateAvailable = true;
            }
        }

        $result = array(
            'id'              => $latest->getName(),
            'logo'            => $this->getDefaultLogo(),
            'name'            => $latest->getName(),
            'description'     => $latest->getDescription(),
            'license'         => $latest->getLicense(),
            'lastVersion'     => $lastVersion,
            'currentVersion'  => $currentVersion,
            'installed'       => $installed ? true : false,
            'updateAvailable' => $updateAvailable,
            'isDependency'    => $this->getModuleRepository()->isInstalledAsDependency($latest->getName()),
        );

        if ($extended) {
            $extra = $latest->getExtra();
            $extra = $extra['modera-module'];

            $screenshots = array();
            if (isset($extra['screenshots']) && is_array($extra['screenshots'])) {
                foreach($extra['screenshots'] as $key => $screenshot) {
                    if (!is_array($screenshot)) {
                        $screenshot = array(
                            'thumbnail' => $screenshot,
                            'src'       => $screenshot,
                        );
                    }
                    $screenshots[] = $screenshot;
                }
            }

            $longDescription = '';
            if (isset($extra['description'])) {
                $longDescription = $extra['description'];
                if (is_array($longDescription)) {
                    $longDescription = implode("\n", $longDescription);
                }
                $longDescription = strip_tags($longDescription);
                $longDescription = str_replace("\n", "<br />", $longDescription);
            }

            $authors = array();
            foreach ($latest->getAuthors() as $author) {
                $authors[] = $author->getName();
            }
            $createdAt = new \DateTime($latest->getTime());

            $result += array(
                'authors'         => count($authors) ? implode(", ", $authors) : null,
                'createdAt'       => $createdAt->format(\DateTime::RFC1123),
                'longDescription' => $longDescription,
                'screenshots'     => $screenshots,
            );
        }

        return $result;
    }

    private function checkAccess()
    {
        /* @var AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->get('security.authorization_checker');

        if (!$authorizationChecker->isGranted(ModeraBackendModuleBundle::ROLE_ACCESS_BACKEND_TOOLS_MODULES_SECTION)) {
            throw new AccessDeniedHttpException();
        }
    }

    /**
     * @Remote
     *
     * @param array $params
     * @return array
     */
    public function getInstalledModulesAction(array $params)
    {
        $this->checkAccess();

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
     * @return array
     */
    public function getAvailableModulesAction(array $params)
    {
        $this->checkAccess();

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
     * @return array|null
     */
    public function getModuleDetailsAction(array $params)
    {
        $this->checkAccess();

        return $this->getModuleInfo($params['id'], true);
    }

    /**
     * @param $url
     * @return array
     */
    private function remoteUrls($url)
    {
        $port = $this->getModuleServicePort();
        $pathPrefix = $this->getModuleServicePathPrefix();
        return array(
            'call'   => $url . ':' . $port . $pathPrefix . '/call',
            'status' => $url . ':' . $port . $pathPrefix . '/status',
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     * @return array
     */
    public function requireAction(array $params)
    {
        $this->checkAccess();

        $response = array(
            'success' => false,
            'params'  => array(),
            'urls'    => $this->remoteUrls($params['url']),
        );

        $package = $this->getModuleRepository()->getPackage($params['id']);
        if ($package) {
            $latest = $this->getPackageLatestVersion($package->getVersions());
            $response['success'] = true;
            $response['params'] = array(
                'method'  => 'require',
                'name'    => $latest->getName(),
                'version' => $latest->getVersion(),
            );
        }

        return $response;
    }

    /**
     * @Remote
     *
     * @param array $params
     * @return array
     */
    public function removeAction(array $params)
    {
        $this->checkAccess();

        $response = array(
            'success' => true,
            'params'  => array(
                'method'  => 'remove',
                'name'    => $params['id'],
            ),
            'urls'    => $this->remoteUrls($params['url']),
        );

        return $response;
    }

    /**
     * @Remote
     *
     * @param array $params
     * @return array
     */
    public function checkAction(array $params)
    {
        $this->checkAccess();

        $response = array(
            'success'        => true,
            'updated_models' => array(
                'modera.backend_module_bundle.module' => [$params['id']],
            ),
        );

        return $response;
    }
}
