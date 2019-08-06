<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Compte;
use App\Form\UserType;
use App\Entity\Partenaire;
use App\Form\PartenaireType;
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
class PartenaireController extends AbstractController
{
    /**
     * @Route("/addp", name="addp", methods={"POST"})
     */
    public function addp(Request $request, EntityManagerInterface $entityManager,UserPasswordEncoderInterface $passwordEncoder, SerializerInterface $serializer,ValidatorInterface $validator)
    { 
        $par = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $par);
        $form->handleRequest($request);
        $Values =$request->request->all();
        $form->submit($Values);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($par);
            $entityManager->flush();
              $errors = $validator->validate($par);
                if(count($errors)) {
                    $errors = $serializer->serialize($errors, 'json');
                    return new Response($errors, 500, [
                        'Content-Type' => 'application/json'
                    ]);
                } 
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
                $compte->setPartenaire($par);
                $entityManager->persist($compte);
                $entityManager->flush();
                $user = new User();
                $form = $this->createForm(UserType::class, $user);
                $form->handleRequest($request);
                $Values =$request->request->all();
                $form->submit($Values);
                $Files=$request->files->all()['imageName'];
        
                $user->setPassword($passwordEncoder->encodePassword($user,$form->get('plainPassword')->getData()));
                $user->setRoles(["ROLE_ADMIN"]);
                $user->setImageFile($Files);
                $user->setPartenaire($par);
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
