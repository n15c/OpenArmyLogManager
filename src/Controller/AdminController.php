<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Entity\User;
use App\Entity\Company;


class AdminController extends AbstractController
{
    /**
    * @Route("/admin", name="app_admin")
    */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
        $curruser = $this->getUser();
        $selcomp = $curruser->getCompany();
        $repository = $this->getDoctrine()->getRepository(Company::class);
        $companies = $repository->findAll();
        $repository = $this->getDoctrine()->getRepository(User::class);
        $users = $repository->findAll();

        return $this->render('main/admin.html.twig', [
          'curruser' => $curruser,
          'companies' => $companies,
          'users'=>$users,
          'selcomp'=>$selcomp,
        ]);
    }

    /**
    * @Route("/admin/getUserInfo/{id}", name="app_adminuserinfo")
    */
    public function getUserInfo(int $id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $entityManager = $this->getDoctrine()->getManager();
      $user = $entityManager->getRepository(User::class)->find($id);

      $response = new Response();

      if(!$user){
        $response->setStatusCode(400);
      }
      else {
        $userInfo["id"] = $user->getId();
        $userInfo["username"] = $user->getUname();
        $userInfo["roles"] = $user->getRoles();

        $response->setContent(json_encode($userInfo));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
      }
      return $response;
    }

    /**
    * @Route("/admin/deleteUser/{id}", name="app_admindeluser")
    */
    public function deleteUser(int $id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $entityManager = $this->getDoctrine()->getManager();
      $user = $entityManager->getRepository(User::class)->find($id);

      $response = new Response();

      if(!$user){
        $response->setStatusCode(400);
      }
      else {
        $entityManager->remove($user);
        $entityManager->flush();

        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/json');
      }
      return $response;
    }

    /**
    * @Route("/admin/updateUser/{id}", name="app_adminsavuser")
    */
    public function updateUser(int $id): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $request = Request::createFromGlobals();
      $data = json_decode($request->getContent(), true);

      $entityManager = $this->getDoctrine()->getManager();

      $repository = $this->getDoctrine()->getRepository(User::class);
      $selUser = $repository->findOneById($id);

      if(!$selUser){
        throw $this->createNotFoundException(
           'No user found for id '.$updStby
        );
        $response = new Response();
        $response->setStatusCode(404);
      }
      else {
        $selUser->setEmail($data["username"]);
        $selUser->setRoles($data["roles"]);

        $entityManager->flush();

        $response = new Response();
        $response->setStatusCode(200);
      }
      return $response;
    }

    /**
    * @Route("/admin/createUser", name="app_admincrtuser")
    */
    public function createUser(UserPasswordHasherInterface $passwordHasher): Response
    {
      $this->denyAccessUnlessGranted('ROLE_SUPERADMIN');
      $request = Request::createFromGlobals();
      $response = new Response();

      try {
        $data = json_decode($request->getContent(), true);


        $entityManager = $this->getDoctrine()->getManager();
        $newUser = new User();

        $newUser->setUname($data["username"]);
        $newUser->setRoles($data["roles"]);

        $plaintextPW = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(30/strlen($x)) )),1,8);
        $hashedPassword = $passwordHasher->hashPassword(
            $newUser,
            $plaintextPW
        );
        $newUser->setPassword($hashedPassword);

        $entityManager->persist($newUser);
        $entityManager->flush();


        $response->setStatusCode(200);
        $response->setContent(json_encode($plaintextPW));
      } catch (\Exception $e) {
        $response->setContent($e);
        $response->setStatusCode(500);
      }


      return $response;
    }

}
