<?php

namespace ArtGalleryBundle\Controller;

use ArtGalleryBundle\Entity\Image;
use ArtGalleryBundle\Form\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class ImageController extends Controller
{
    /**
     *@Route("/image/new", name="image_upload")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction($id,Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class,$image);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // $file stores the uploaded PDF file
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $image->getImage();
            $image->setAuthor($this->getUser());
            // Generate a unique name for the file before saving it
            $imageName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('images_directory'),
                $imageName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $image->setImage($imageName);

            // ... persist the $product variable or any other work
            $em=$this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('image/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     *@Route("/image/accept", name="image_accept")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function acceptAction(Request $request)
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class,$image);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // $file stores the uploaded PDF file
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $image->getImage();

            // Generate a unique name for the file before saving it
            $imageName = md5(uniqid()).'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('images_directory'),
                $imageName
            );

            // Update the 'brochure' property to store the PDF file name
            // instead of its contents
            $image->setImage($imageName);

            // ... persist the $product variable or any other work
            $em=$this->getDoctrine()->getManager();
            $em->persist($image);
            $em->flush();
            return $this->redirectToRoute('gallery_index');
        }

        return $this->render('image/upload.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
