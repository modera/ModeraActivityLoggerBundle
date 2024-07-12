<?php

namespace Modera\ActivityLoggerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Modera\ActivityLoggerBundle\Model\ActivityInterface;

/**
 * This entity is not meant to be used directly, instead use
 * {@class Modera\ActivityLoggerBundle\Manager\DoctrineOrmActivityManager}. As a rule of thumb when working with
 * activities never rely on implementations but rather use {@class Modera\ActivityLoggerBundle\Model\ActivityInterface}
 * if you want to keep your code portable.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 *
 * @ORM\Entity
 *
 * @ORM\Table(name="modera_activitylogger_activity")
 */
class Activity implements ActivityInterface
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $author = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $level = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $message = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @var array<string, mixed>
     *
     * @ORM\Column(type="json")
     */
    private ?array $meta = [];

    public function __construct()
    {
        $this->getCreatedAt();
    }

    /**
     * @deprecated Use native ::class property
     */
    public static function clazz(): string
    {
        @\trigger_error(\sprintf(
            'The "%s()" method is deprecated. Use native ::class property.',
            __METHOD__
        ), \E_USER_DEPRECATED);

        return \get_called_class();
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getAuthor(): string
    {
        return $this->author ?? '';
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    public function getLevel(): string
    {
        return $this->level ?? '';
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): string
    {
        return $this->message ?? '';
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type ?? '';
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime('now');
        }

        return $this->createdAt;
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return array<string, mixed>
     */
    public function getMeta(): array
    {
        return $this->meta ?? [];
    }
}
