<?php

namespace Modera\FoundationBundle\Extensibility;

/**
 * Please read:
 * http://help.eclipse.org/indigo/index.jsp?topic=%2Forg.eclipse.platform.doc.isv%2Freference%2Fapi%2Forg%2Feclipse%2Fcore%2Fruntime%2FIAdaptable.html
 * http://wiki.eclipse.org/FAQ_How_do_I_use_IAdaptable_and_IAdapterFactory%3F
 *
 * @copyright 2013 Modera Foundation
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
interface AdaptableInterface
{
    /**
     * @param string $adapter  Name of an interface you want to get an adapter for
     * @param array $data  Some optional data that may be used by the method when creating the adapter
     * @return object|null  If adapter of type $adapter cannot be created then NULL is returned
     */
    public function getAdapter($adapter, array $data = array());
}