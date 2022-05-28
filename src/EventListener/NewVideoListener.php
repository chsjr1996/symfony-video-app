<?php

namespace App\EventListener;

use App\Entity\User;
use App\Entity\Video;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @todo Need to be asyn (queue jobs...)
 */
class NewVideoListener
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Video) {
            return;
        }

        $entityManager = $args->getObjectManager();

        /** @var User[] */
        $users = $entityManager->getRepository(User::class)->findAll();

        foreach ($users as $user) {
            $this->sendNewVideoEmail($user, $entity);
        }
    }

    private function sendNewVideoEmail(User $user, Video $video): void
    {
        $email = (new TemplatedEmail())
            ->subject('New video from Symfony Video APP')
            ->from('symfony@video.local.dev')
            ->to($user->getEmail())
            ->htmlTemplate('_emails/_new_video.html.twig')
            ->context([
                'name' => $user->getName(),
                'video' => $video,
            ]);

        $this->mailer->send($email);
    }
}
