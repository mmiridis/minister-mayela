<?php

namespace AppBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Translation\TranslatorInterface;

use AppBundle\AppEvents;
use AppBundle\Event\ContactEvent;

/**
 * Class ContactListener
 * @package AppBundle\Listener
 */
class ContactListener implements EventSubscriberInterface
{
    /** @var  EntityManagerInterface $em */
    protected $em;
    /** @var \Swift_Mailer $mailer */
    protected $mailer;
    /** @var EngineInterface $templating */
    protected $templating;
    /** @var TranslatorInterface $translator */
    protected $translator;

    /**
     * ContactListener constructor.
     * @param EntityManagerInterface $em
     * @param EngineInterface $templating
     * @param \Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $em, EngineInterface $templating, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->em         = $em;
        $this->mailer     = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            AppEvents::CONTACT_CREATED => ['onContactCreatedEvent', 0]
        ];
    }

    //==================================================================================================================
    // E V E N T S
    //==================================================================================================================
    /**
     * Handles the 'contact.event.created' event.
     *
     * @param ContactEvent $contactEvent
     * @throws \Exception
     */
    public function onContactCreatedEvent(ContactEvent $contactEvent)
    {
        $to      = "mayela@miridis.com";
        $contact = $contactEvent->getContact();
        $subject = sprintf("Contact-Request from: %s", $contact->getName());

        file_put_contents(__DIR__ . '/../../../email.log', $contact->getId() . "\n", FILE_APPEND);
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($contact->getEmail())
            ->setTo($to)
            ->setBody(
                $this->templating->render(
                    'AppBundle::mail/contactRequest.txt.twig',
                    [
                        'contact' => $contact,
                    ]
                )
            );

        /** @var \Swift_Mime_Message $message */
        if (!$this->mailer->send($message)) {
            throw new \Exception("Email could not be sent");
        }
    }
}