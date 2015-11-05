<?php

namespace Modera\SecurityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="modera_security_session")
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class Session
{
    /**
     * This property mapped column type used to be "binary" with length=128, but in Symfony 2.7 Doctrine DBAL v2.4 is used
     * and this version doesn't have BinaryType available yet. To solve this we updated mapping to use type "string"
     * instead, it should be more that enough to store SID, here's the proof:
     * - http://php.net/manual/en/session.configuration.php#ini.session.hash-function
     * - http://php.net/manual/en/session.configuration.php#ini.session.hash-bits-per-character.
     *
     * @ORM\Id
     * @ORM\Column(type="string", nullable=false)
     */
    protected $session_id;

    /**
     * @ORM\Column(type="blob", length=65532, nullable=false)
     */
    protected $session_value;

    /**
     * @ORM\Column(type="integer", nullable=false, options={"unsigned"=true})
     */
    protected $session_time;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $session_lifetime;
}
