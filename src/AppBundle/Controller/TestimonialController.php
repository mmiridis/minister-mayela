<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Testimonial;

/**
 * Testimonial controller.
 *
 * @Route("backend/testimonial")
 */
class TestimonialController extends Controller
{
    /**
     * Lists all testimonial entities.
     *
     * @Route("/", name="backend_testimonial")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('testimonial/index.html.twig', [
            'testimonials' => $this->getDoctrine()->getRepository('AppBundle:Testimonial')->findAllSorted()
        ]);
    }

    /**
     * @Route("/{id}/sort/{position}", name="backend_testimonial_sort")
     *
     * @param int $id
     * @param int $position
     * @return JsonResponse
     */
    public function sortAction($id, $position)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var Testimonial $testimonial */
            $testimonial = $em->getRepository('AppBundle:Testimonial')->find($id);
            $testimonial->setPosition($position);
            $em->persist($testimonial);
            $em->flush();

            return new JsonResponse(['rc' => 200]);
        } catch (\Exception $e) {
            return new JsonResponse(['rc' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Creates a new testimonial entity.
     *
     * @Route("/new", name="backend_testimonial_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $testimonial = new Testimonial();
        $form        = $this->createForm('AppBundle\Form\TestimonialType', $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($testimonial);
            $em->flush();

            return $this->redirectToRoute('backend_testimonial');
        }

        return $this->render('testimonial/new.html.twig', [
            'testimonial' => $testimonial,
            'form'        => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing testimonial entity.
     *
     * @Route("/{id}/edit", name="backend_testimonial_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Testimonial $testimonial
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Testimonial $testimonial)
    {
        $form = $this->createForm('AppBundle\Form\TestimonialType', $testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_testimonial_edit', ['id' => $testimonial->getId()]);
        }

        return $this->render('testimonial/edit.html.twig', [
            'testimonial' => $testimonial,
            'form'        => $form->createView(),
            'delete_form' => $this->createDeleteForm($testimonial)->createView(),
        ]);
    }

    /**
     * Deletes a testimonial entity.
     *
     * @Route("/{id}", name="backend_testimonial_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Testimonial $testimonial
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Testimonial $testimonial)
    {
        $form = $this->createDeleteForm($testimonial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($testimonial);
            $em->flush();
        }

        return $this->redirectToRoute('backend_testimonial');
    }

    /**
     * Creates a form to delete a testimonial entity.
     *
     * @param Testimonial $testimonial The testimonial entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Testimonial $testimonial)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_testimonial_delete', ['id' => $testimonial->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}
