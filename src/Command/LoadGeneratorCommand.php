<?php

namespace App\Command;

use App\Entity\UserMessage;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

#[AsCommand(
    name: 'app:messenger:load_generator',
    description: 'Генерирует поток сообщений для очереди.'
)]
class LoadGeneratorCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $bus, private readonly ManagerRegistry $doctrine, string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('Генерирует поток сообщений для очереди.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $users = [];
            for ($i = 1; $i <= 40; ++$i) {
                $userId = random_int(1, 5);
                $msg = ($users[$userId] ?? 0) + 1;
                $users[$userId] = $msg;
                $userMessage = (new UserMessage())
                    ->setUserId($userId)
                    ->setMessage($msg)
                    ->setGeneratedAt(new DateTime());
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($userMessage);
                $entityManager->flush();
                $this->bus->dispatch(new \App\Messenger\Message\UserMessage($userId, $msg));
            }
        } catch (Throwable $e) {
            $output->writeln(sprintf(
                '<error>Операция завершилась ошибкой с текстом : %s</error>',
                $e->getMessage()
            ));

            return Command::FAILURE;
        }

        $output->writeln('<info>Сгенерировано!</info>');

        return Command::SUCCESS;
    }
}
