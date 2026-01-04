<?php

namespace Devlat\Blog\Console;

use Devlat\Blog\Model\Service\Management;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CleanUp extends Command
{
    /**
     * @var Management
     */
    private Management $management;

    /**
     * Constructor.
     * @param Management $management
     * @param string|null $name
     */
    public function __construct(
        Management $management,
        ?string $name = null,
    )
    {
        $this->management = $management;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('devlat:blog:cleanup');
        $this->setDescription('Execute this command if you are going to uninstall Devlat_Blog module');

        parent::configure();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Clean up started...");

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion(
            '<question>This will delete all blogs registered and cannot be undone. Are you sure you want to continue? (y/n) </question>',
            false,
            '/^(y|yes)/i'
        );
        if (!$helper->ask($input, $output, $question)) {
           $output->writeln("<comment>Operation Cancelled.</comment>");
           return Command::SUCCESS;
        }


        $this->management->dropTable();
        $output->writeln("Clean up completed.");
        return Command::SUCCESS;
    }
}
