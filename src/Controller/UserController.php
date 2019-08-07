<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractController
{
    

    /**
     * @Route("/addUser", name="user_new", methods={"GET","POST"})
     */
    public function addUser(Request $request,UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer,ValidatorInterface $validator):Response
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
           
               $errors = $validator->validate($user);
                if(count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();
                } 
                
                $data = [
                  'statut' => 201,
                  'massage' => 'L"utilisateur été bien ajouté'
                ];
                return new JsonResponse($data, 201);
            }

   
}
