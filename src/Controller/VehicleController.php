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
use App\Entity\Vehicle;
use App\Entity\Vhctype;
use App\Entity\VehicleRemark;
use Symfony\Component\HttpFoundation\JsonResponse;

class VehicleController extends AbstractController
{
  /**
  * @Route("/vehicles", name="app_vehicles")
  */
  public function vhcPage(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $vehicles = $repository->findBy(['company'=>$selcomp]);

    $repository = $this->getDoctrine()->getRepository(Vhctype::class);
    $vhctypes = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    return $this->render('main/vehicles.html.twig', [
      'curruser' => $curruser,
      'vehicles' => $vehicles,
      'vhctypes' => $vhctypes,
      'companies' => $companies,
      'selcomp' => $selcomp,
    ]);
  }

  /**
  * @Route("/vehicles/create", name="app_cvehicle")
  */
  public function createVhc(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $entityManager = $this->getDoctrine()->getManager();
    try {
      $request = Request::createFromGlobals();
      $newVehicle = new Vehicle();
      $response = new Response();
      $data = json_decode($request->getContent(), true);


      $newVehicle->setLicplate($data["licplate"]);

      $repository = $this->getDoctrine()->getRepository(Vhctype::class);
      $newVehicle->setType($repository->find($data["vhctype"]));

      $repository = $this->getDoctrine()->getRepository(Company::class);
      $newVehicle->setCompany($repository->find($data["company"]));

      $entityManager->persist($newVehicle);
      $entityManager->flush();

      $response->setStatusCode(200);
    } catch (\Exception $e) {
      $response->setContent($e);
      $response->setStatusCode(500);
    }
    return $response;
  }

  /**
  * @Route("/vehicles/get", name="app_getvhcjson")
  */
  public function getVehiclesJson(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $vehiclesObj = $repository->findAll();
    $vehicles = [];
    foreach ($vehiclesObj as $vehicle) {
      $vhc = [];
      $vhc["id"] = $vehicle->getID();
      $vhc["licplate"] = $vehicle->getLicplate();
      $vhc["company"] = $vehicle->getCompany()->getNumber();
      $vhc["model"] = $vehicle->getType()->getModel();

      $vehicles[] = $vhc;
    }
    return new JsonResponse($vehicles);
  }

  /**
  * @Route("/vehicles/delete/{id}", name="app_dvehicle")
  */
  public function deleteVhc(int $id): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $response = new Response();

    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $entityManager = $this->getDoctrine()->getManager();

    $vehicle = $repository->find($id);
    $entityManager->remove($vehicle);
    $entityManager->flush();
    $response->setStatusCode(200);
    $response->setContent($id);
    return $response;
  }

  /**
  * @Route("/vehicles/remarks/{id}", name="app_gremarks")
  */
  public function getRemarks(int $id): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $vehicle = $repository->find($id);

    if ($vehicle) {
      $repository = $this->getDoctrine()->getRepository(VehicleRemark::class);
      $remarks = $repository->findBy(['vehicle' => $vehicle]);

      return $this->render('main/remarks.html.twig', [
        'remarks' => $remarks,
      ]);
    }
    else {
      $res = new Response();
      $res->setStatusCode(400);
      return $res;
    }
  }

  /**
  * @Route("/vehicles/remarks/close/{id}", name="app_cremark")
  */
  public function closeRemark(int $id): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $response = new Response();
    $repository = $this->getDoctrine()->getRepository(VehicleRemark::class);
    $remark = $repository->find($id);
    if ($remark) {
      $remark->setClosed(1);
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->flush();
      $response->setStatusCode(200);
    }
    else {
      $response->setStatusCode(400);
    }
    return $response;
  }

  /**
  * @Route("/vehicles/remark/create", name="app_crvehicle")
  */
  public function createRemark(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $entityManager = $this->getDoctrine()->getManager();
    try {
      $request = Request::createFromGlobals();
      $newRemark = new VehicleRemark();
      $response = new Response();
      $data = json_decode($request->getContent(), true);


      $newRemark->setPerson($data["author"]);
      $newRemark->setDescription($data["description"]);


      $repository = $this->getDoctrine()->getRepository(Vehicle::class);
      $newRemark->setVehicle($repository->find($data["vhcid"]));
      $newRemark->setCreationDate(new \DateTime);
      $newRemark->setClosed(0);

      $entityManager->persist($newRemark);
      $entityManager->flush();

      $response->setStatusCode(200);
    } catch (\Exception $e) {
      $response->setContent($e);
      $response->setStatusCode(500);
    }
    return $response;
  }
}
