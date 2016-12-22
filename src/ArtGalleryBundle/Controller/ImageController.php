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
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * Class ImageController
 * @package ArtGalleryBundle\Controller
 */
class ImageController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/image/new", name="image_upload")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var \Symfony\Component\HttpFoundation\File\UploadedFile $file
             */
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
            return $this->redirectToRoute('user_images');
        }

        return $this->render('image/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Security("has_role('ROLE_MOD')")
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
     * @Security("has_role('ROLE_MOD')")
     * @Route("/image/approve", name="image_viewApprove")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewApproveAction()
    {
        $image = $this->getDoctrine()->getRepository(Image::class)->findBy(array('approved' => false));
        $count = count($image);
        return $this->render('image/approve.html.twig', ['images' => $image,
            'count' => $count]);

    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Route ("/image/delete/{route}/{id}/" , name="image_delete")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction($id, $route)
    {

        $image = $this->getDoctrine()->getRepository(Image::class)->find($id);
        if ($this->getUser()->isAuthor($image) or $this->getUser()->isRole('ROLE_ADMIN') or $this->getUser()->isRole('ROLE_OWNER')) {

            unlink('uploads/images' . '/' . $image->getImage());
            $em = $this->getDoctrine()->getManager();
            $em->remove($image);
            $em->flush();
        }
        if ($route == 'approve') {
            return $this->redirectToRoute('image_viewApprove');
        }
        else if($route == 'profile'){
            return $this->redirectToRoute('user_images');
        }
        return $this->redirectToRoute('gallery_index');
    }

    /**
     * @Security("has_role('ROLE_MOD')")
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
