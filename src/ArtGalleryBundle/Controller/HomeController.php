<?php

namespace ArtGalleryBundle\Controller;

use ArtGalleryBundle\Entity\Image;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    /**
     * @Route("/", name="gallery_index")
     */
    public function indexAction()
    {
        $images = $this->getDoctrine()->getRepository(Image::class)->findAll();

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'images' => $images
        ]);
    }

    /**
     * @Route("/search", name="search")
     * @param Request $request
     */
    public function searchAction(Request $request){
        $search = $request->get('search');
       $search =strtolower($search);
    $images = $this->getDoctrine()->getRepository(Image::class)->findAll();
    $imageToShow = array();
    foreach ($images as $image){

        if (strpos(strtolower($image->getTitle()) , $search) !== false) {
          $imageToShow[] = $image;
        }
    }
        return $this->render('default/search.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
            'images' => $imageToShow
        ]);

    }
}