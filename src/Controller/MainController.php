<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Company;
use App\Entity\Vehicle;
use App\Entity\Vhctype;



class MainController extends AbstractController
{
  /**
  * @Route("/", name="app_main")
  */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $curruser = $this->getUser();
        $selcomp = $curruser->getCompany();
        $repository = $this->getDoctrine()->getRepository(Company::class);
        $companies = $repository->findAll();

        $repository = $this->getDoctrine()->getRepository(Vhctype::class);
        $vhctypes = $repository->findAll();
        $vhcop = array();
        $vhcnop = array();
        foreach ($vhctypes as $vhctype) {
          $repository = $this->getDoctrine()->getRepository(Vehicle::class);
          $opvhc = $repository->findBy([
            'company'=>$selcomp,
            'operational'=>1,
            'type'=>$vhctype
          ]);
          $vhcop[$vhctype->getmanufacturer() . " " . $vhctype->getmodel()] = count($opvhc);
        }
        foreach ($vhctypes as $vhctype) {
          $repository = $this->getDoctrine()->getRepository(Vehicle::class);
          $nopvhc = $repository->findBy([
            'company'=>$selcomp,
            'operational'=>0,
            'type'=>$vhctype
          ]);
          $vhcnop[$vhctype->getmanufacturer() . " " . $vhctype->getmodel()] = count($nopvhc);
        }



        return $this->render('main/index.html.twig', [
          'curruser' => $curruser,
          'selcomp' => $selcomp,
          'companies' => $companies,
          'vhcop'=>$vhcop,
          'vhcnop'=>$vhcnop
        ]);
    }

    /**
    * @Route("/changeCompany/{cid}", name="app_ccompany")
    */
    public function cCompany(int $cid): Response
    {
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $response = new Response();
      try {
        $curruser = $this->getUser();
        $repository = $this->getDoctrine()->getRepository(Company::class);
        $chosenComp = $repository->findOneBy(['number' => $cid]);
        $curruser->setCompany($chosenComp);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $response->setStatusCode(200);
      } catch (\Exception $e) {
        $response->setContent($e);
        $response->setStatusCode(500);
      }

      return $response;
    }

}
