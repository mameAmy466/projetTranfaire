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
use App\Entity\User;
use App\Form\UserType;
use App\Entity\Partenaire;


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
      $user = new User();
      $form = $this->createForm(UserType::class, $user);
      $form->handleRequest($request);
      $Values =$request->request->all();
      $form->submit($Values);
      $Files=$request->files->all()['imageName'];
      $user->setPassword($passwordEncoder->encodePassword($user,$form->get('plainPassword')->getData()));
      $user->setRoles(["ROLE_ADMIN"]);
      $user->setImageFile($Files);
          $entityManager = $this->getDoctrine()->getManager();
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


 
}
