<?php

namespace ArtGalleryBundle\Controller;

use ArtGalleryBundle\Entity\User;
use ArtGalleryBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("has_role('ROLE_ADMIN')")
 * Class AdminController
 * @package ArtGalleryBundle\Controller
 */
class AdminController extends Controller
{
    /**
     *
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
