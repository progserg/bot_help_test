<?php

namespace App\Messenger\Handler;

use App\Entity\UserMessage as UserMessageModel;
use App\Messenger\Message\UserMessage;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserMessageHandler
{
    public function __construct(private readonly ManagerRegistry $doctrine)
    {
    }

    public function __invoke(UserMessage $message): void
    {
        $store = new FlockStore('/var/stores');
        $factory = new LockFactory($store);
        $lock = $factory->createLock($message->getUserId());
        if ($lock->acquire()) {
            /** @var UserMessageModel $userMessage */
            $userMessage = $this->doctrine->getRepository(UserMessageModel::class)->findOneByUserId($message->getUserId());
            if (
                null !== $userMessage
                && $message->getMessage() >= $userMessage->getMessage()
            ) {
                $entityManager = $this->doctrine->getManager();
                $entityManager->persist($userMessage->setProcessedAt(new DateTime()));
                $entityManager->flush();
                sleep(1);
            }
            $lock->release();
        }
    }
}
