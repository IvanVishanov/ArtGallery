<?php

namespace ArtGalleryBundle\Controller;

use ArtGalleryBundle\Entity\Image;
use ArtGalleryBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * Class ImageController
 * @package ArtGalleryBundle\Controller
 */
class ImageController extends Controller
{
    /**
     *
     * @Route("/image/new", name="image_upload")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $file stores the uploaded PDF file
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $image->getImage();
            $image->setAuthor($this->getUser());
            $image->setApproved(false);
            // Generate a unique name for the file before saving it
            $imageName = md5(uniqid()) . '.' . $file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('images_directory'),
                $imageName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $image->setImage($imageName);

            // ... persist the $product variable or any other work
            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('image/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @Route ("/image/approve/{id}" , name="image_approve")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function approveAction($id)
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->find($id);

        $image->setApproved(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($image);
        $em->flush();
        return $this->redirectToRoute('image_viewApprove');
    }

    /**
     *
     * @Route("/image/approve", name="image_viewApprove")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewApproveAction()
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->findAll();
        return $this->render('image/approve.html.twig', ['images' => $image]);

    }

    /**
     *
     * @Route ("/image/delete/{id}" , name="image_delete")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id)
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->find($id);
        unlink('uploads/images' . '/' . $image->getImage());
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();
        return $this->redirectToRoute('image_viewApprove');
    }

    /**
     * @Route ("/image/updateTitle" , name="image_updateTitle")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateTitle(Request $request)
    {
        $response = new JsonResponse();

        $id = intval($request->get('id'));
        $title = $request->get('title');
        try {
            $image = $this->getDoctrine()->getRepository(Image::class)->find($id);

            if (!$image) {
                throw new Exception('Invalid image');
            }

            if (!$title) {
                throw new Exception('Title is empty');
            }

            $image->setTitle($title);

            $em = $this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();

            $response->setData([
                'code' => 'success',
                'message' => 'Image title updated successfully',
                'title' => $title
            ]);
        } catch (Exception $e) {
            $response->setData([
                'code' => 'error',
                'message' => $e->getMessage()
            ]);
        }

        return $response;
    }
}
