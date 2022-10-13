<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\KeyClaim;
use App\Entity\Vehicle;
use App\Entity\Driver;
use App\Entity\Company;

class KeyClaimController extends AbstractController
{
  /**
  * @Route("/keys", name="app_keyclaim")
  */
    public function index(): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $curruser = $this->getUser();
      $selcomp = $curruser->getCompany();
      $repository = $this->getDoctrine()->getRepository(Company::class);
      $companies = $repository->findAll();

      $repository = $this->getDoctrine()->getRepository(KeyClaim::class);
      $openkeyclaims = $repository->findBy(['ReceptionDate' => NULL]);
      $returnedkeyclaims = $repository->findAll();

      $repository = $this->getDoctrine()->getRepository(Vehicle::class);
      $vehicles = $repository->findAll();

      $repository = $this->getDoctrine()->getRepository(Driver::class);
      $drivers = $repository->findAll();

        return $this->render('main/keyclaim.html.twig', [
          'curruser' => $curruser,
          'openkeyclaims' => $openkeyclaims,
          'returnedkeyclaims' => $returnedkeyclaims,
          'vehicles' => $vehicles,
          'drivers' => $drivers,
          'companies' => $companies,
          'selcomp' => $selcomp,
          'controller_name' => 'KeyClaimController',
        ]);
    }

    /**
    * @Route("/keys/return/{id}", name="app_keyreturn")
    */
      public function returnKey(int $id): Response
      {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $repository = $this->getDoctrine()->getRepository(KeyClaim::class);
        $returnedkey = $repository->find($id);
        $now = new \DateTime();
        $returnedkey->setReceptionDate($now);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $response = new Response();
        $response->setStatusCode(200);
        return($response);
      }

      /**
      * @Route("/keys/issueing", name="app_keyissueing")
      */
        public function issueKey(): Response
        {
          $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
          $entityManager = $this->getDoctrine()->getManager();

          try {
            $request = Request::createFromGlobals();
            $newClaim = new KeyClaim();
            $response = new Response();
            $data = json_decode($request->getContent(), true);

            $repository = $this->getDoctrine()->getRepository(Vehicle::class);
            $vehicle = $repository->findOneBy(['licplate' => $data["vehicle"]]);

            $newClaim->setVehicle($vehicle);
            $newClaim->setPerson($data["person"]);
            $newClaim->setClaimingDate(new \DateTime);

            $entityManager->persist($newClaim);
            $entityManager->flush();

            $response->setStatusCode(200);
          } catch (\Exception $e) {
            $response->setContent($e);
            $response->setStatusCode(500);
          }
          return $response;
        }
}
