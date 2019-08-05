<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Compte;
use App\Entity\Partenaire;
use App\Entity\User;

/**
     * @Route("/api")
     */
class PartenaireController extends AbstractController
{
    /**
     * @Route("/add", name="add", methods={"POST"})
     */
    public function add(Request $request, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer,ValidatorInterface $validator)
    { 
        $sms='message';
        $status='status';
            $values = json_decode($request->getContent());
            if(isset($values->username,$values->password)) {
                $compte= new compte();
                $solde=0;
                 $numero = 'MG';
                 $numero .= sprintf('%04d',1);
                $compte->setNumero($numero);
                $compte->setSolde($solde);
                $entityManager->persist($compte);
                $entityManager->flush();
                $user = new User();
                $user->setUsername($values->username);
                $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
                $user->setRoles(["ROLE_ADMIN"]);
                $user->setNom($values->nom);
                $user->setPrenom($values->prenom);
                $user->setNumeroCompte($numero);
                $errors = $validator->validate($user);
                if(count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                } 
                $entityManager->persist($user);
                $entityManager->flush();
                $par = new Partenaire;
                $par->setNinea($values->ninea);
                $par->setRaisonSociale($values->raisonSociale);
                $par->setAdress($values->Adress);
                $par->addUser($user);
                $par->addCompte($compte);

                $entityManager->persist($par);
                $entityManager->flush();
                $data = [
                    $status => 201,
                    $sms => 'Les propriétés du addenaire ont été bien ajouté'
                ];
                return new JsonResponse($data, 201);
            }
         
            $data = [
                $status => 500,
                $sms => 'Vous devez renseigner les clés username et password'
            ];
     return new JsonResponse($data, 500);
    }
}
