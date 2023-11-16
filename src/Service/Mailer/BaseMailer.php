<?php

namespace App\Service\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

abstract class BaseMailer
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string[] $recipients
     */
    protected function createMail(array $recipients, string $subject, string $template, array $context): Email
    {
        return (new TemplatedEmail())
            ->to(...$recipients)
            ->subject($subject)
            ->htmlTemplate($template . '.html.twig')
            // ->textTemplate($template . 'txt.twig')
            ->context($context);
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function send(Email $email): void
    {
        $this->mailer->send($email);
    }
}