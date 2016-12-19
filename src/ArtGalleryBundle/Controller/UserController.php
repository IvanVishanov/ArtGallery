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
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
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
        $images = $this->getDoctrine()->getRepository(Image::class)->findBy(array('authorId' => $userID));
        return $this->render("user/profile.html.twig", ['user' => $user, 'images' => $images]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/user/images", name="user_images")
     */
    public function userImagesAction()
    {
        $userID = $this->getUser()->getId();
        $approvedImages = $this->getDoctrine()->getRepository(Image::class)->findBy(array('authorId' => $userID, 'approved' => true));
        $notApprovedImages = $this->getDoctrine()->getRepository(Image::class)->findBy(array('authorId' => $userID, 'approved' => false));
        $count = count($approvedImages + $notApprovedImages);
        $approvedCount = count($approvedImages);
        $notApprovedCount = count($notApprovedImages);
        return $this->render("user/images.html.twig", [
            'approvedImages' => $approvedImages,
            'notApprovedImages' => $notApprovedImages,
            'count' => $count,
            'approvedCount' => $approvedCount,
            'notApprovedCount' => $notApprovedCount]);
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/user/editProfile", name="user_editProfile")
     */
    public function editProfileAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $currentPassword = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (empty($user->getPassword())) {
                $user->setPassword($currentPassword);
            } else {
                $newPassword = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());
                $user->setPassword($newPassword);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("user_profile");
        }
        return $this->render(
            'user/editProfile.html.twig',
            array('form' => $form->createView(),
                'user' => $user
            ));
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/admin/profiles", name="admin_profiles")
     */
    public function profilesAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render(
            'admin/profiles.html.twig',
            array(
                'user' => $users
            ));
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @Route ("/admin/editProfile/{id}" , name="admin_editProfile")
     */
    public function adminEditProfileAction($id, Request $request)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $form = $this->createForm(UserType::class, $user);
        $currentPassword = $user->getPassword();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (empty($user->getPassword())) {
                $user->setPassword($currentPassword);
            } else {
                $newPassword = $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());
                $user->setPassword($newPassword);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("admin_profiles");
        }
        return $this->render(
            'admin/editProfile.html.twig',
            array('form' => $form->createView(),
                'user' => $user
            ));

    }

    /**
     * @param $id
     * @Route ("/admin/deleteProfile/{id}" , name="admin_deleteProfile")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function adminDeleteProfileAction($id)
    {

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            return $this->redirectToRoute("admin_profiles");

    }

}
