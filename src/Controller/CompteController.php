<?php

namespace App\Controller;

use App\Entity\Compte;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Partenaire;

/**
 * @Route("/api")
 */
class CompteController extends AbstractController
{
  

    /**
     * @Route("/new", name="compte_new", methods={"GET","POST"})
     */
    public function new(Request $request,EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        $parteniere=$this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['ninea'=>$values->ninea]);
        $compte=$this->getDoctrine()->getRepository(Compte::class)->findAll();
        foreach ($compte as $key => $value) {
           $id=$value->getId(); 
        }
        $compte= new compte();
        $solde=0;
        $numero = 'MG';
        $numero .= sprintf('%04d',$id);
        $compte->setNumero($numero);
        $compte->setSolde($solde);
        $compte->setPartenaire($parteniere);
        $entityManager->persist($compte);
        $entityManager->flush();

        $data = [
            'status'=> 201,
            'message' => 'est bien ajouté' ];
        return new JsonResponse($data, 201);
    }

   
}
