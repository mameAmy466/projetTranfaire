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

use App\Entity\Partenaire;
use App\Entity\User;

/**
     * @Route("/api")
     */
class PartenaireUserController extends AbstractController
{
    /**
     * @Route("/adduserP", name="partenaire_user")
     */
    public function adduserP(Request $request, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer,ValidatorInterface $validator)
     {
          
    $values = json_decode($request->getContent());
    if(isset($values->username,$values->password)) {
            $parteniere=$this->getDoctrine()->getRepository(Partenaire::class)->findOneBy(['ninea'=>$values->ninea]);
            $comptes=$parteniere->getComptes();
            $numero=$comptes[0]->getNumero();
                $user = new User();
                $user->setUsername($values->username);
                $user->setPassword($passwordEncoder->encodePassword($user, $values->password));
                $user->setRoles(["ROLE_USER"]);
                $user->setNom($values->nom);
                $user->setPrenom($values->prenom);
                $user->setNumeroCompte($numero);
                $user->setPartenaire($parteniere);
                $entityManager->persist($user);
                $entityManager->flush();
                $errors = $validator->validate($user);
                if(count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                } 
                
                $data = [
                  'statut' => 201,
                  'massage' => 'L"utilisateur été bien ajouté'
                ];
                return new JsonResponse($data, 201);
            }
            $data = [
              'statut' => 500,
              'massage' => 'Vous devez renseigner les clés username et password'
            ];
      return new JsonResponse($data, 500);
 }
}
