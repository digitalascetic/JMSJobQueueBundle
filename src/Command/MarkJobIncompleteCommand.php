<?php

namespace JMS\JobQueueBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use JMS\JobQueueBundle\Entity\Job;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use JMS\JobQueueBundle\Entity\Repository\JobManager;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'jms-job-queue:mark-incomplete',
    description: 'Internal command (do not use). It marks jobs as incomplete.',
    hidden: false
)]
class MarkJobIncompleteCommand extends Command
{
    private $registry;
    private $jobManager;

    public function __construct(ManagerRegistry $managerRegistry, JobManager $jobManager)
    {
        parent::__construct();

        $this->registry = $managerRegistry;
        $this->jobManager = $jobManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Internal command (do not use). It marks jobs as incomplete.')
            ->addArgument('job-id', InputArgument::REQUIRED, 'The ID of the Job.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->registry->getManagerForClass(Job::class);

        /** @var Job|null $job */
        $job = $em->createQuery("SELECT j FROM " . Job::class . " j WHERE j.id = :id")
            ->setParameter('id', $input->getArgument('job-id'))
            ->getOneOrNullResult();

        if ($job === null) {
            $output->writeln('<error>Job was not found.</error>');

            return Command::FAILURE;
        }

        $this->jobManager->closeJob($job, Job::STATE_INCOMPLETE);

        return Command::SUCCESS;
    }
}
