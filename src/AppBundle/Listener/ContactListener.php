<?php

namespace AppBundle\Listener;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Event\ContactEvent;

/**
 * Class ContactListener
 * @package AppBundle\Listener
 */
class ContactListener
{
    /** @var  EntityManager $em */
    protected $em;
    /** @var \Swift_Mailer $mailer */
    protected $mailer;
    /** @var EngineInterface $templating */
    protected $templating;
    /** @var TranslatorInterface $translator */
    protected $translator;

    /**
     * ContactListener constructor.
     * @param EntityManager $em
     * @param EngineInterface $templating
     * @param \Swift_Mailer $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManager $em, EngineInterface $templating, \Swift_Mailer $mailer, TranslatorInterface $translator)
    {
        $this->em         = $em;
        $this->mailer     = $mailer;
        $this->templating = $templating;
        $this->translator = $translator;
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

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('mayela@miridis.com')
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