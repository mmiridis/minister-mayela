<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Entity\Faq;

/**
 * Faq controller.
 *
 * @Route("/backend/faq")
 */
class FaqController extends Controller
{
    /**
     * Lists all faq entities.
     *
     * @Route("", name="backend_faq")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $faqs = $em->getRepository('AppBundle:Faq')->findAllSorted();

        return $this->render('faq/index.html.twig', [
            'faqs' => $faqs,
        ]);
    }

    /**
     * @Route("/{id}/sort/{position}", name="backend_faq_sort")
     * @param int $id
     * @param int $position
     * @return JsonResponse
     */
    public function sortAction($id, $position)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var Faq $faq */
            $faq = $em->getRepository('AppBundle:Faq')->find($id);
            $faq->setPosition($position);
            $em->persist($faq);
            $em->flush();

            return new JsonResponse(['rc' => 200]);
        } catch (\Exception $e) {
            return new JsonResponse(['rc' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Creates a new faq entity.
     *
     * @Route("/new", name="backend_faq_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $faq  = new Faq();
        $form = $this->createForm('AppBundle\Form\FaqType', $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();

            return $this->redirectToRoute('backend_faq');
        }

        return $this->render('faq/new.html.twig', [
            'faq'  => $faq,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing faq entity.
     *
     * @Route("/{id}/edit", name="backend_faq_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Faq $faq
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Faq $faq)
    {
        $form = $this->createForm('AppBundle\Form\FaqType', $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_faq');
        }

        return $this->render('faq/edit.html.twig', [
            'faq'         => $faq,
            'form'        => $form->createView(),
            'delete_form' => $this->createDeleteForm($faq)->createView()
        ]);
    }

    /**
     * Deletes a faq entity.
     *
     * @Route("/{id}", name="backend_faq_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Faq $faq
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Faq $faq)
    {
        $form = $this->createDeleteForm($faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
            $em->flush();
        }

        return $this->redirectToRoute('backend_faq');
    }

    /**
     * Creates a form to delete a faq entity.
     *
     * @param Faq $faq The faq entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Faq $faq)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_faq_delete', ['id' => $faq->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}