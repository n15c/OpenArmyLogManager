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
use App\Entity\Driver;
use App\Entity\Drivercat;

class OrderController extends AbstractController
{
  /**
  * @Route("/orders", name="app_orders")
  */
  public function orderPage(): Response
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

    return $this->render('main/order.html.twig', [
      'curruser' => $curruser,
      'companies' => $companies,
      'selcomp' => $selcomp,
      'vehicles' => $vehicles
    ]);
  }
}
