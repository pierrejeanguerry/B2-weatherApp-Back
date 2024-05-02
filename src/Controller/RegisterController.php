<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        $manager->getConnection()->beginTransaction();

        try{
            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);
            $user = new User;
            $user->setEmail($body['email'])
            ->setUsername($body['username'])
            ->setRoles($user->getRoles());

            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,20}$/';
            $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
            if (!preg_match($pattern, $body['password'])){
                return $this->json([
                    'message' => 'Password problem',
                    ], Response::HTTP_BAD_REQUEST);
            }
            if (!$body['email'] || !preg_match($emailPattern, $body['email'])){
                return $this->json([
                    'message' => 'Email problem',
                    ], Response::HTTP_BAD_REQUEST);
            }
            $password = $hasher->hashPassword($user, $body['password']);
            $user->setPassword($password);
            $manager->persist($user);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'User created',
                ], Response::HTTP_CREATED);

        } catch (UniqueConstraintViolationException $e){
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => 'User already exist',
                ], Response::HTTP_CONFLICT);
        }
    }
}
