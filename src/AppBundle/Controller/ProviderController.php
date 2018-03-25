<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Entity\Provider;

/**
 * Provider controller.
 *
 * @Route("/backend/services")
 */
class ProviderController extends Controller
{
    /**
     * Lists all provider entities.
     *
     * @Route("", name="backend_provider")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $providers = $em->getRepository('AppBundle:Provider')->findAllSorted();

        return $this->render('provider/index.html.twig', [
            'providers' => $providers,
        ]);
    }

    /**
     * @Route("/{id}/sort/{position}", name="backend_provider_sort")
     * @param int $id
     * @param int $position
     * @return JsonResponse
     */
    public function sortAction($id, $position)
    {
        $em = $this->getDoctrine()->getManager();
        try {
            /** @var Provider $provider */
            $provider = $em->getRepository('AppBundle:Provider')->find($id);
            $provider->setPosition($position);
            $em->persist($provider);
            $em->flush();

            return new JsonResponse(['rc' => 200]);
        } catch (\Exception $e) {
            return new JsonResponse(['rc' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Creates a new provider entity.
     *
     * @Route("/new", name="backend_provider_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $provider = new Provider();
        $form     = $this->createForm('AppBundle\Form\ProviderType', $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($provider);
            $em->flush();

            return $this->redirectToRoute('backend_provider');
        }

        return $this->render('provider/new.html.twig', [
            'provider' => $provider,
            'form'     => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing provider entity.
     *
     * @Route("/{id}/edit", name="backend_provider_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Provider $provider
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Provider $provider)
    {
        $form = $this->createForm('AppBundle\Form\ProviderType', $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_provider');
        }

        return $this->render('provider/edit.html.twig', [
            'provider'    => $provider,
            'form'        => $form->createView(),
            'delete_form' => $this->createDeleteForm($provider)->createView()
        ]);
    }

    /**
     * Deletes a provider entity.
     *
     * @Route("/{id}", name="backend_provider_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Provider $provider
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Provider $provider)
    {
        $form = $this->createDeleteForm($provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($provider);
            $em->flush();
        }

        return $this->redirectToRoute('backend_provider');
    }

    /**
     * Creates a form to delete a provider entity.
     *
     * @param Provider $provider The provider entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Provider $provider)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_provider_delete', ['id' => $provider->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}