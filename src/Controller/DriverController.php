<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DriverRepository;
use App\Repository\DrivercatRepository;
use App\Entity\Company;
use App\Entity\Driver;
use App\Entity\Drivercat;

class DriverController extends AbstractController
{
  /**
  * @Route("/drivers", name="app_drivers")
  */
  public function driverPage(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Driver::class);
    $drivers = $repository->findBy(['drivercompany'=>$selcomp]);

    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Drivercat::class);
    $drivercats = $repository->findAll();

    return $this->render('main/drivers.html.twig', [
      'curruser' => $curruser,
      'drivers' => $drivers,
      'companies' => $companies,
      'drivercats' => $drivercats,
      'companies' => $companies,
      'selcomp' => $selcomp,
    ]);
  }

  /**
  * @Route("/createDriver", name="app_cdriver")
  */
  public function createDriver(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $entityManager = $this->getDoctrine()->getManager();
    try {
      $request = Request::createFromGlobals();
      $newDriver = new Driver();
      $response = new Response();
      $data = json_decode($request->getContent(), true);


      $newDriver->setLastname($data["lastname"]);
      $repository = $this->getDoctrine()->getRepository(Drivercat::class);

      foreach ($data["categories"] as $id) {
        $drivercat = $repository->find($id);
        $newDriver->addDrivercat($drivercat);
      }

      $repository = $this->getDoctrine()->getRepository(Company::class);
      $newDriver->setDrivercompany($repository->find($data["company"]));

      $entityManager->persist($newDriver);
      $entityManager->flush();

      $response->setStatusCode(200);
    } catch (\Exception $e) {
      $response->setContent($e);
      $response->setStatusCode(500);
    }
    return $response;
  }

  /**
  * @Route("/drivers/delete/{id}", name="app_ddriver")
  */
  public function deleteDriver(int $id): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $response = new Response();

    $repository = $this->getDoctrine()->getRepository(Driver::class);
    $entityManager = $this->getDoctrine()->getManager();

    $driver = $repository->find($id);
    $entityManager->remove($driver);
    $entityManager->flush();
    $response->setStatusCode(200);
    $response->setContent($id);
    return $response;
  }
}
