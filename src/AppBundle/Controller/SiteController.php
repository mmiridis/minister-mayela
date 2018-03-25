<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use AppBundle\AppEvents;
use AppBundle\Entity\Contact;
use AppBundle\Event\ContactEvent;
use AppBundle\Form\ContactType;

class SiteController extends Controller
{
    /**
     * @Route("/{_locale}", name="home", requirements={ "_locale" = "%app.locales%" })
     * @Route("", name="home_no_locale")
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Site:index.html.twig');
    }

    /**
     * @Route("/{_locale}/about-me", name="aboutMe", requirements={ "_locale" = "%app.locales%" })
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function aboutMeAction()
    {
        return $this->render('AppBundle:Site:aboutMe.html.twig');
    }

    /**
     * @Route("/{_locale}/gallery", name="gallery", requirements={ "_locale" = "%app.locales%" })
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function galleryAction()
    {
        return $this->render('AppBundle:Site:gallery.html.twig', [
            'pictures' => $this->getDoctrine()->getRepository('AppBundle:Picture')->findAllActive()

        ]);
    }

    /**
     * @Route("/{_locale}/testimonials", name="testimonials", requirements={ "_locale" = "%app.locales%" })
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function testimonialsAction()
    {
        return $this->render('AppBundle:Site:testimonials.html.twig', [
            'testimonials' => $this->getDoctrine()->getRepository('AppBundle:Testimonial')->findAllActive()
        ]);
    }

    /**
     * @Route("/{_locale}/faq", name="faq", requirements={ "_locale" = "%app.locales%" })
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function faqAction()
    {
        return $this->render('AppBundle:Site:faq.html.twig', [
            'faqs' => $this->getDoctrine()->getRepository('AppBundle:Faq')->findAllActive()
        ]);
    }

    /**
     * @Route("/{_locale}/services", name="services", requirements={ "_locale" = "%app.locales%" })
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function providersAction()
    {
        return $this->render('AppBundle:Site:provider.html.twig', [
            'groupedProviders' => $this->getDoctrine()->getRepository('AppBundle:Provider')->findAllActive()
        ]);
    }

    /**
     * Displays a form to create a new Contact entity.
     *
     * @Route("/{_locale}/contact", name="contact", requirements={ "_locale" = "%app.locales%" })
     * @Method("GET")
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function newAction()
    {
        $entity = new Contact();

        $form = $this->createCreateForm($entity);

        return $this->render('AppBundle:Site:contact.html.twig', [
            'entity' => $entity,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * Creates a new Contact entity.
     *
     * @Route("/{_locale}/contact", name="contact_create", requirements={ "_locale" = "%app.locales%" })
     * @Method("POST")
     *
     * @param Request $request
     * @return Response|RedirectResponse
     */
    public function createAction(Request $request)
    {
        $contact = new Contact();
        $form    = $this->createCreateForm($contact);
        $form->handleRequest($request);

        if ($form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();

            $em->getConnection()->beginTransaction();

            try {
                $em->persist($contact);
                $em->flush();
                $em->getConnection()->commit();

                $this->get('event_dispatcher')->dispatch(AppEvents::CONTACT_CREATED, new ContactEvent($contact));
                $this->get('session')->set('contact_id', $contact->getId());

                return $this->redirect($this->generateUrl('contact_success'));
            } catch (\Exception $e) {
                $em->getConnection()->rollBack();
                return $this->redirect($this->generateUrl('contact_error'));
            }
        }

        return $this->render('AppBundle:Site:contact.html.twig', [
            'entity' => $contact,
            'form'   => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale}/thankyou", name="contact_success", requirements={ "_locale" = "%app.locales%" })
     * @Method("GET")
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function contactSuccessAction()
    {
        if (null === $contactId = $this->get('session')->get('contact_id')) {
            throw new NotFoundHttpException('');
        }
        if (null === $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($contactId)) {
            throw new NotFoundHttpException('');
        }

        return $this->render('AppBundle:Site:thankYou.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * @Route("/{_locale}/error", name="contact_error", requirements={ "_locale" = "%app.locales%" })
     * @Method("GET")
     * @Cache(maxage="0", public=false)
     *
     * @return Response
     */
    public function contactErrorAction()
    {
        if (null === $contactId = $this->get('session')->get('contact_id')) {
            throw new NotFoundHttpException('');
        }
        if (null === $contact = $this->getDoctrine()->getRepository('AppBundle:Contact')->find($contactId)) {
            throw new NotFoundHttpException('');
        }

        return $this->render('AppBundle:Site:error.html.twig', [
            'contact' => $contact
        ]);
    }

    /**
     * Creates a form to create a Contact entity.
     *
     * @param Contact $entity The entity
     * @return Form The form
     */
    private function createCreateForm(Contact $entity)
    {
        $form = $this->createForm(ContactType::class, $entity, [
            'action' => $this->generateUrl('contact_create'),
            'method' => 'POST',
        ]);

        $form->add('submit', SubmitType::class, ['label' => 'Create']);

        return $form;
    }
}