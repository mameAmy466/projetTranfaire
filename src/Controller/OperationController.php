<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Entity\Operation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api")
 */
class OperationController extends AbstractController
{
   

    /**
     * @Route("/add", name="operation_new", methods={"POST"})
     */
    public function add(Request $request,EntityManagerInterface $entityManager, SerializerInterface $serializer,ValidatorInterface $validator)
    {
         $values = json_decode($request->getContent());
         $operation = new Operation();
         $compte=$this->getDoctrine()->getRepository(Compte::class)->findOneBy(['numero'=>$values->numero]);
         $operation->setMontant($values->montant);
         $operation->setDate( new \DateTime);
         $operation->setCompte($compte);
         $compte->setSolde($compte->getSolde()+$values->montant);
         $errors = $validator->validate($operation);
         if(count($errors)) {
             $errors = $serializer->serialize($errors, 'json');
             return new Response($errors, 500, [
                 'Content-Type' => 'application/json'
             ]);
             $entityManager->persist($operation);
             $entityManager->flush();
         }
         $entityManager->persist($compte);
         $entityManager->flush();
         $data = [
            'status' => 201,
            'mesage' => 'Le depot et faie'
        ];
        return new JsonResponse($data, 201);
    }  
}
