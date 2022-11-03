<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Workflow\Event\Event;

class WorkflowSubscriber implements EventSubscriberInterface
{

    public function __construct(private readonly MailerInterface $mailer)
    {}

    public function newToyRequest(Event $event)
    {
        $email = (new Email())
            ->from($event->getSubject()->getUser()->getEmail())
            ->to('dad@dad.com')
            ->addTo('mum@mum.com')
            ->subject('Demande de jouet - ' . $event->getSubject()->getName())
            ->text('Bonjour Maman et Papa, merci de me commander le jouet : ' . $event->getSubject()->getName());

        $this->mailer->send($email);
    }

    public function toyReceived(Event $event)
    {
        $email = (new Email())
            ->from('papa.noel@laponie.fr')
            ->to($event->getSubject()->getUser()->getEmail())
            ->subject('Ton jouet est la, oh oh oh !')
            ->text('Ton jouet est arrivé, amuse toi bien !');

        $this->mailer->send($email);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.toy_request.leave.request' => 'newToyRequest', // workflow.[nom_workflow].leave.[nom_status] lorsqu'on quitte le status request
            'workflow.toy_request.entered.received' => 'toyReceived', // workflow.[nom_workflow].entered.[nom_status] lorsqu'on est entré dans le status received
        ];
    }
}