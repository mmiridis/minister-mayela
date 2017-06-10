<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Picture;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contact controller.
 *
 * @Route("/backend/gallery")
 */
class PictureController extends Controller
{
    /**
     * Lists all Picture entities.
     *
     * @Route("", name="backend_gallery")
     * @Method("GET")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('gallery/index.html.twig', [
            'pictures' => $this->getDoctrine()->getRepository('AppBundle:Picture')->findAllSorted()
        ]);
    }

    /**
     * @Route("/{id}/sort/{position}", name="backend_gallery_sort")
     *
     * @param int $id
     * @param int $position
     * @return JsonResponse
     */
    public function sortAction($id, $position)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var Picture $picture */
            $picture = $em->getRepository('AppBundle:Picture')->find($id);
            $picture->setPosition($position);
            $em->persist($picture);
            $em->flush();

            return new JsonResponse(['rc' => 200]);
        } catch (\Exception $e) {
            return new JsonResponse(['rc' => 500, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Creates a new picture entity.
     *
     * @Route("/new", name="backend_gallery_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
        $picture = new Picture();
        $form    = $this->createForm('AppBundle\Form\PictureType', $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($picture);
            $em->flush();

            return $this->redirectToRoute('backend_gallery');
        }

        return $this->render('gallery/new.html.twig', [
            'picture' => $picture,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing picture entity.
     *
     * @Route("/{id}/edit", name="backend_gallery_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param Picture $picture
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Picture $picture)
    {
        $form = $this->createForm('AppBundle\Form\PictureType', $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('backend_gallery_edit', ['id' => $picture->getId()]);
        }

        return $this->render('gallery/edit.html.twig', [
            'picture'     => $picture,
            'form'        => $form->createView(),
            'delete_form' => $this->createDeleteForm($picture)->createView(),
        ]);
    }

    /**
     * Deletes a picture entity.
     *
     * @Route("/{id}", name="backend_gallery_delete")
     * @Method("DELETE")
     *
     * @param Request $request
     * @param Picture $picture
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Picture $picture)
    {
        $form = $this->createDeleteForm($picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($picture);
            $em->flush();
        }

        return $this->redirectToRoute('backend_gallery');
    }

    /**
     * Creates a form to delete a picture entity.
     *
     * @param Picture $picture The picture entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Picture $picture)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('backend_gallery_delete', ['id' => $picture->getId()]))
            ->setMethod('DELETE')
            ->getForm();
    }
}