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
use App\Entity\Trsploading;
use App\Entity\Vehicle;
use App\Entity\Vhctype;


class LoadingController extends AbstractController
{
  /**
  * @Route("/transports/{uuid}/loading", name="app_loadingmgmt")
  */
  public function loadingPage(string $uuid): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $curruser = $this->getUser();
    $selcomp = $curruser->getCompany();
    $repository = $this->getDoctrine()->getRepository(Company::class);
    $companies = $repository->findAll();

    $repository = $this->getDoctrine()->getRepository(Transport::class);
    $transport = $repository->findOneBy(['trspuuid'=>$uuid]);

    $trsploadings = $transport->getTrsploadings();
    $vehicles = $transport->getVehicles();

    return $this->render('main/transports/loadingManager.html.twig', [
      'curruser' => $curruser,
      'transport' => $transport,
      'trsploadings'=>$trsploadings,
      'companies' => $companies,
      'vehicles'=>$vehicles,
      'selcomp' => $selcomp,
    ]);
  }

  /**
  * @Route("/transports/{uuid}/loading/createPallet", name="app_crtpallet")
  */
  public function createPallet(string $uuid): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $entityManager = $this->getDoctrine()->getManager();
    try {
      $request = Request::createFromGlobals();
      $newTrspload = new Trsploading();
      $response = new Response();
      $data = json_decode($request->getContent(), true);

      $repository = $this->getDoctrine()->getRepository(Transport::class);
      $transport = $repository->findOneBy(['trspuuid' => $uuid]);

      $newTrspload->setName($data["palName"]);
      $newTrspload->setPalHeight($data["palHeight"]);
      $newTrspload->setTransport($transport);

      $entityManager->persist($newTrspload);
      $entityManager->flush();

      $response->setStatusCode(200);
    } catch (\Exception $e) {
      $response->setContent($e);
      $response->setStatusCode(500);
    }
    return $response;
  }

  /**
  * @Route("/transports/{uuid}/loading/placePallet", name="app_plcpallet")
  */
  public function placePallet(string $uuid): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $request = Request::createFromGlobals();
    $entityManager = $this->getDoctrine()->getManager();
    $response = new Response();
    try {
      $data = json_decode($request->getContent(), true);
      $repository = $this->getDoctrine()->getRepository(Trsploading::class);
      $load = $repository->find($data["load"]);

      $repository = $this->getDoctrine()->getRepository(Vehicle::class);
      $vhc = $repository->find($data["vhc"]);
      if ($load->getPalHeight() > $vhc->getType()->getPalHeight()) {
        $response->setStatusCode(424);
      }
      else {
        $load->setVhc($vhc);
        $load->setCoordX($data["palX"]);
        $load->setCoordY($data["palY"]);
        $entityManager->flush();
        $response->setStatusCode(200);
      }
    } catch (\Exception $e) {
      $response->setStatusCode(400);
      $response->setContent($e);
    }
    return $response;
  }

  /**
  * @Route("/transport/{uuid}/loading/getPlacedPallets", name="app_getspallet")
  */
  public function getSeatedPallets(string $uuid): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $entityManager = $this->getDoctrine()->getManager();

    $qb = $entityManager->createQueryBuilder();
    $loadings = $qb->select("t")
    ->from('App\Entity\Trsploading','t')
    ->where($qb->expr()->isNotNull("t.vhc"))
    ->getQuery()->getResult();

    $loadingarray = array();
    foreach ($loadings as $load) {
      $loaddata = array();
      $loaddata["vhcid"] = $load->getVhc()->getid();
      $loaddata["palX"] = $load->getCoordX();
      $loaddata["loadid"] = $load->getId();
      $loaddata["palY"] = $load->getCoordY();
      $loaddata["loadName"] = $load->getName();
      $loadingarray[] = $loaddata;
    }
    $response = new JsonResponse($loadingarray);
    return $response;
  }

  /**
  * @Route("/transport/{uuid}/loading/unsetPallet/{loadid}", name="app_unspallet")
  */
  public function unsetPallet(string $uuid, int $loadid): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $entityManager = $this->getDoctrine()->getManager();
    $response = new Response();

    try {
      $repository = $this->getDoctrine()->getRepository(Trsploading::class);
      $load = $repository->find($loadid);
      $load->setVhc(NULL);
      $entityManager->flush();
      $response->setStatusCode(200);
    } catch (\Exception $e) {
      $response->setStatusCode(400);
    }
    return $response;
  }
}
