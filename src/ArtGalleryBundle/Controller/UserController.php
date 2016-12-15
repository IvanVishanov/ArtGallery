<?php

namespace ArtGalleryBundle\Controller;

use ArtGalleryBundle\Entity\Image;
use ArtGalleryBundle\Entity\User;
use ArtGalleryBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("security_login");
        }
        return $this->render(
            'user/register.html.twig',
            array('form' => $form->createView()));
    }
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/profile", name="user_profile")
     */
    public function profileAction()
    {
        $userID = $this->getUser()->getId();
        $user = $this->getUser();
        $images = $this->getDoctrine()->getRepository(Image::class)->findBy( array('author' => $userID, 'deleted'=>false));
        return $this->render("user/profile.html.twig", ['user'=>$user, 'images'=>$images]);
    }
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/user/images", name="user_images")
     */
    public function userImagesAction()
    {
        $userID = $this->getUser()->getId();
        $approvedImages = $this->getDoctrine()->getRepository(Image::class)->findBy( array('author' => $userID, 'deleted'=>false,'approved'=>true));
        $notApprovedImages = $this->getDoctrine()->getRepository(Image::class)->findBy( array('author' => $userID, 'deleted'=>false,'approved'=>false));
        $count = count($approvedImages+$notApprovedImages);
        $approvedCount = count($approvedImages);
        $notApprovedCount = count($notApprovedImages);
        return $this->render("user/images.html.twig", ['approvedImages'=>$approvedImages,'notApprovedImages'=>$notApprovedImages,'count'=>$count,'approvedCount'=>$approvedCount,'notApprovedCount'=>$notApprovedCount]);
    }
}
