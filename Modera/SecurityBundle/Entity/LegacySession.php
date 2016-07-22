<?php

namespace Modera\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="modera_security_legacy_session")
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class LegacySession
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $session_id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $session_value;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $session_time;
}
