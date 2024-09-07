<?php

namespace App\Utils;

use App\Entity\Newsletter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class NewsletterSenderUtils
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly Environment $twig,
        private readonly MailerInterface $mailer,
        #[Autowire(env: 'SENDER_ADDRESS')]
        private readonly string $senderAddress,
        #[Autowire(env: 'SENDER_NAME')]
        private readonly string $senderName,
        #[Autowire(env: 'NEWSLETTER_RECEIVER_ADDRESS')]
        private readonly string $receiverAddress,
    ) {
    }

    public function send(Newsletter $newsletter): void
    {
        $htmlContent = $this->twig->render('newsletter/newsletter.html.twig', [
            'newsletter' => $newsletter,
        ]);
        $email = (new Email())
            ->from(new Address($this->senderAddress, $this->senderName))
            ->to($this->receiverAddress)
            ->subject($newsletter->getTitle())
            ->html($htmlContent);

        $this->mailer->send($email);
        $newsletter->setSent(true);
        $this->em->flush();
    }
}
