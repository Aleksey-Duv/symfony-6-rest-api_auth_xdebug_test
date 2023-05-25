<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/TestController.php',
        ]);
    }

    #[Route('/api/getAllUser', name: 'get_allures', methods: 'GET')]
    public function getAllUser(entityManagerInterface $manager): JsonResponse
    {
        $users = $manager->getRepository(User::class)->findAll();
// return  $this->json($users)
// $users=$this->user->findAll();
//dd($users);
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'password' => $user->getPassword(),
            ];
        }

        $response = new JsonResponse($data);

        $response->headers->set('Content-Type', 'application/json');
        return $response;

    }

    #[Route('/api/userCreate', name: 'user_create', methods: 'POST')]
    public function userCreate(Request $request, UserPasswordHasherInterface $passwordHash, entityManagerInterface $manager): JsonResponse
    {
        // dd($request);
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        $password = $data['password'];


        //  $email_exist = $this->user->findOneByEmail($email); //как это работает хз, надо разбираться, но работает
        $email_exist1 = $manager->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($email_exist1) {

            return new JsonResponse
            (
                [
                    'statys' => false,
                    'message' => 'mail already exists'
                ]
            );
        } else {
            $user = new User();

            $hashedPassword = $passwordHash->hashPassword(
                $user,
                $password
            );

            $user->setEmail($email)
                ->setRoles(array('ROLE_ADMIN'))
                ->setPassword($hashedPassword);

//            $this->manager->persist($user);
//            $this->manager->flush();
            $manager->persist($user);
            $manager->flush();
            return new JsonResponse
            (
                [
                    'statys'=>true,
                    'message'=>'user added'
                ]
            );

        }

    }






}
