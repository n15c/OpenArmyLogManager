<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use App\Entity\Company;
use App\Entity\Transport;
use App\Entity\Vehicle;
use App\Entity\Vhctype;


class TransportController extends AbstractController
{
  /**
  * @Route("/transports", name="app_transports")
  */
  public function transportPage(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Transport::class);
    $transports = $repository->findAll();

    return $this->render('main/transports/transports.html.twig', [
      'curruser' => $curruser,
      'transports' => $transports,
      'companies' => $companies,
      'selcomp' => $selcomp,
    ]);
  }

  /**
  * @Route("/transports/create", name="app_ctransport")
  */
  public function createTransport(): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();
    $entityManager = $this->getDoctrine()->getManager();

    $request = Request::createFromGlobals();
    $method = $request->getMethod();
    if ($method == "GET") {
      return $this->render('main/transports/createTransport.html.twig', [
        'curruser' => $curruser,
        'companies' => $companies,
        'selcomp' => $selcomp,
      ]);
    }
    elseif ($method == "POST") {
      try {
        $newTransport = new Transport();
        // $newTransport->setTrspuuid(Uuid::v4());
        $trspDateTimeConv = \DateTime::createFromFormat("Y-m-d\TH:i", $request->get("trspdate"));
        $newTransport->setTrspdate($trspDateTimeConv);
        $newTransport->setAlias($request->get("trspalias"));
        $newTransport->setUnit($request->get("trspunit"));
        $newTransport->setDuration($request->get("trspduration"));
        $newTransport->setLocation($request->get("trsplocation"));
        $newTransport->setCompleted(0);
        $newTransport->setCompany($selcomp);
        $entityManager->persist($newTransport);
        $entityManager->flush();
        return $this->redirect('/transports/'.$newTransport->gettrspuuid());

      } catch (\Exception $e) {
        $response = new Response();
        $response->setContent($e);
        $response->setStatusCode(400);
        return $response;
      }
    }
    else {
      $response = new Response();
      $response.setStatusCode(404);
      return $response;
    }
  }


  /**
  * @Route("/transports/{uuid}", name="app_stransport")
  */
  public function showTransport(string $uuid): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();
    $repository = $this->getDoctrine()->getRepository(Transport::class);
    $transport = $repository->findOneBy(['trspuuid' => $uuid]);
    $repository = $this->getDoctrine()->getRepository(Vhctype::class);
    $vhctypes = $repository->findAll();
    $entityManager = $this->getDoctrine()->getManager();

    $request = Request::createFromGlobals();
    $method = $request->getMethod();
    if ($method == "GET") {
      return $this->render('main/transports/showTransport.html.twig', [
        'curruser' => $curruser,
        'companies' => $companies,
        'selcomp' => $selcomp,
        'transport' => $transport,
        'vhctypes' => $vhctypes
      ]);
    }
  }


  /**
  * @Route("/transport/update/{uuid}", name="app_utransport")
  */
  public function updateTransport(string $uuid): Response
  {
    define('allowedChangeVals', array(
      "alias",
      "unit",
      "duration",
      "location"
    ));
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $response = new Response();
    $entityManager = $this->getDoctrine()->getManager();
    $request = Request::createFromGlobals();


    $repository = $this->getDoctrine()->getRepository(Transport::class);
    $transport = $repository->findOneBy(['trspuuid' => $uuid]);
    $data = json_decode($request->getContent(), true);
    if (in_array($data["key"],\allowedChangeVals)) {
      try {
        $transport->{"set".$data["key"]}($data["value"]);
        $status = 200;
      } catch (\Exception $e) {
        $status = 500;
      }
    }
    else {
      $status = 403;
    }


    $entityManager->flush();
    $response->setStatusCode($status);
    return $response;
  }



    /**
    * @Route("/transport/getReadyVhc", name="app_getvhcs")
    */
    public function getReadyVhc(): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $response = new Response();

      $curruser = $this->getUser();
      $selcomp = $curruser->getCompany();

      $repository = $this->getDoctrine()->getRepository(Vehicle::class);
      $vhcs = $repository->findBy([
        'company' => array($selcomp->getId(),0),
        'operational'=>true
      ]);
      $vhcarray = array();
      foreach ($vhcs as $vhc) {
        $vhcdet["cat"] = $vhc->getType()->getId();
        $pal_len = $vhc->getType()->getPalLength();
        $pal_wid = $vhc->getType()->getPalWidth();
        $vhcdet["pal_cap"] = $pal_len*$pal_wid;
        $vhcdet["pers_cap"] = $vhc->getType()->getMaxpers();
        $vhcdet["pal_height"] = $vhc->getType()->getPalHeight();
        $vhcdet["licplate"] = $vhc->getLicplate();
        $vhcarray[] = $vhcdet;

      }
      $response->setStatusCode(200);
      $response->headers->set("Content-Type","application/json");
      $response->setContent(json_encode($vhcarray));
      return $response;
    }

    /**
    * @Route("/transport/getChosenVhc/{uuid}", name="app_getchosenvhc")
    */
    public function getChosenVhc(string $uuid): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $response = new Response();

      $curruser = $this->getUser();
      $selcomp = $curruser->getCompany();
      $repository = $this->getDoctrine()->getRepository(Transport::class);
      $transport = $repository->findOneBy(['trspuuid' => $uuid]);

      $thistrspstartdt = $transport->gettrspdate();
      $thistrspenddt = clone($thistrspstartdt);
      $thistrspenddt->add(new \DateInterval('PT'.$transport->getduration().'H'));

      $vhcs = $transport->getVehicles();
      $vhcarray = array();
      foreach ($vhcs as $vhc) {
        $vhcdet["cat"] = $vhc->getType()->getId();
        $pal_len = $vhc->getType()->getPalLength();
        $pal_wid = $vhc->getType()->getPalWidth();
        $vhcdet["pal_cap"] = $pal_len*$pal_wid;
        $vhcdet["pers_cap"] = $vhc->getType()->getMaxpers();
        $vhcdet["pal_height"] = $vhc->getType()->getPalHeight();
        $vhcdet["licplate"] = $vhc->getLicplate();
        $vhcarray[] = $vhcdet;

      }
      $response = new JsonResponse($vhcarray);
      return $response;
    }


    /**
    * @Route("/transport/assignVhc/{uuid}/{licplate}", name="app_assvhc")
    */
    public function assignVhc(string $uuid, string $licplate): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $response = new Response();
      $entityManager = $this->getDoctrine()->getManager();
      $request = Request::createFromGlobals();

      $repository = $this->getDoctrine()->getRepository(Transport::class);
      $transport = $repository->findOneBy(['trspuuid'=>$uuid]);

      $repository = $this->getDoctrine()->getRepository(Vehicle::class);
      $vehicle = $repository->findOneBy(['licplate'=>$licplate]);


      $method = $request->getMethod();
      if ($method == "CREATE") {
        $transport->addVehicle($vehicle);
      }
      else {
        $transport->removeVehicle($vehicle);
      }
      $entityManager->flush();
      $response->setStatusCode(200);
      return $response;
    }


    /**
    * @Route("/transport/delete/{uuid}", name="app_dtrsp")
    */
    public function deleteTransport(string $uuid): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $response = new Response();

      $repository = $this->getDoctrine()->getRepository(Transport::class);
      $entityManager = $this->getDoctrine()->getManager();

      $transport = $repository->findOneBy(['trspuuid'=>$uuid]);
      $entityManager->remove($transport);
      $entityManager->flush();
      // $response->setStatusCode(200);
      // $response->setContent($id);
      // return $response;
      return $this->redirectToRoute('app_transports');

    }

    /**
    * @Route("/public/transport/{uuid}", name="app_pubtransport")
    */
    public function publicTransportPage(string $uuid): Response
    {
      $repository = $this->getDoctrine()->getRepository(Transport::class);
      $transport = $repository->findOneBy(['trspuuid'=>$uuid]);

      return $this->render('main/transports/publictransport.html.twig', [
        'transport' => $transport
      ]);
    }

  //
  // /**
  // * @Route("/drivers/delete/{id}", name="app_ddriver")
  // */
  // public function deleteDriver(int $id): Response
  // {
  //   $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
  //   $response = new Response();
  //
  //   $repository = $this->getDoctrine()->getRepository(Driver::class);
  //   $entityManager = $this->getDoctrine()->getManager();
  //
  //   $driver = $repository->find($id);
  //   $entityManager->remove($driver);
  //   $entityManager->flush();
  //   $response->setStatusCode(200);
  //   $response->setContent($id);
  //   return $response;
  // }
}
