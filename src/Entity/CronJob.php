<?php

namespace JMS\JobQueueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name = "jms_cron_jobs")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
#[ORM\Table(name: "jms_cron_jobs")]
#[ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
#[ORM\Entity]
class CronJob
{
    /** @ORM\Id @ORM\Column(type = "integer", options = {"unsigned": true}) @ORM\GeneratedValue(strategy="AUTO") */
    #[ORM\Id]
    #[ORM\Column(type: "integer", options: ["unsigned" => true])]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    private ?int $id = null;

    /** @ORM\Column(type = "string", length = 200, unique = true) */
    #[ORM\Column(type: "string", length: 200, unique: true)]
    private string $command;

    /** @ORM\Column(type = "datetime", name = "lastRunAt") */
    #[ORM\Column(name: "lastRunAt", type: "datetime")]
    private $lastRunAt;

    public function __construct($command)
    {
        $this->command = $command;
        $this->lastRunAt = new \DateTime();
    }

    public function getCommand()
    {
        return $this->command;
    }

    public function getLastRunAt()
    {
        return $this->lastRunAt;
    }
}
