<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DriverRepository;
use App\Repository\DrivercatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Company;
use App\Entity\Vehicle;
use App\Entity\Driver;
use App\Entity\Drivercat;

class TimetableController extends AbstractController
{
  /**
  * @Route("/timetable", name="app_timetable")
  */
  public function timetablePage(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();

    $repository = $this->getDoctrine()->getRepository(Driver::class);
    $drivers = $repository->findBy(['drivercompany'=>$selcomp]);

    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $vehicles = $repository->findBy(['company'=>$selcomp]);

    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Drivercat::class);
    $drivercats = $repository->findAll();

    return $this->render('main/timetable.html.twig', [
      'curruser' => $curruser,
      'companies' => $companies,
      'selcomp' => $selcomp,
      'vehicles' => $vehicles
    ]);
  }

  /**
  * @Route("/timetable/getVhcGroups", name="app_getvhcgroupjson")
  */
  public function getVehicleGroupJson(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $vehiclesObj = $repository->findBy(['company'=>array($selcomp,0)]);
    $vehicles = [];
    foreach ($vehiclesObj as $vehicle) {
      $vhc = [];
      $vhc["id"] = $vehicle->getID();
      $vhc["content"] = $vehicle->getLicplate();
      $vhc["type"] = $vehicle->getType()->getid();

      $vehicles[] = $vhc;
    }
    return new JsonResponse($vehicles);
  }

  /**
  * @Route("/timetable/getTransportsPerVhc", name="app_gettrsppvhc")
  */
  public function getTrspPerVhc(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Vehicle::class);
    $vehiclesObj = $repository->findBy(['company'=>array($selcomp,0)]);
    $transports = [];
    foreach ($vehiclesObj as $vehicle) {
      $vhctrsps = $vehicle->getTransports();
      foreach ($vhctrsps as $trsp) {
        $calitem = [];
        $calitem["group"] = $vehicle->getId();
        $calitem["content"] = $trsp->getAlias();

        $startdate = $trsp->getTrspDate();
        $thistrspenddt = clone($startdate);
        $startdate = $startdate->format(\DateTimeInterface::ISO8601);
        $calitem["start"] = $startdate;
        $thistrspenddt->add(new \DateInterval('PT'.$trsp->getduration().'H'));
        $calitem["end"] = $thistrspenddt->format(\DateTimeInterface::ISO8601);

        $transports[] = $calitem;
      }
    }
    return new JsonResponse($transports);
  }

  /**
  * @Route("/timetable/getTransports", name="app_gettrsp")
  */
  public function getTrsps(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $company = $repository->find($selcomp);
    $transports = $company->getTransports();
    $transportsobj = array();
    foreach ($transports as $transport) {
          $calitem = [];
          $calitem["content"] = $transport->getAlias();

          $startdate = $transport->getTrspDate();
          $thistrspenddt = clone($startdate);
          $startdate = $startdate->format(\DateTimeInterface::ISO8601);
          $calitem["start"] = $startdate;
          $thistrspenddt->add(new \DateInterval('PT'.$transport->getduration().'H'));
          $calitem["end"] = $thistrspenddt->format(\DateTimeInterface::ISO8601);

          $transportsobj[] = $calitem;
    }
    // foreach ($vehiclesObj as $vehicle) {
    //   $vhctrsps = $vehicle->getTransports();
    //   foreach ($vhctrsps as $trsp) {
    //     $calitem = [];
    //     $calitem["group"] = $vehicle->getId();
    //     $calitem["content"] = $trsp->getAlias();
    //
    //     $startdate = $trsp->getTrspDate();
    //     $thistrspenddt = clone($startdate);
    //     $startdate = $startdate->format(\DateTimeInterface::ISO8601);
    //     $calitem["start"] = $startdate;
    //     $thistrspenddt->add(new \DateInterval('PT'.$trsp->getduration().'H'));
    //     $calitem["end"] = $thistrspenddt->format(\DateTimeInterface::ISO8601);
    //
    //     $transports[] = $calitem;
    //   }
    // }
    return new JsonResponse($transportsobj);
  }
}
