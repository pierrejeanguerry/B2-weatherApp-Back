<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'register', methods: ['POST'], priority: 2)]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
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
    #[Route('/api/users/{id}', name: 'get_user', methods: ['GET'], priority: 2)]
    public function index(
        #[CurrentUser()] User $user,
        Request $request,
        AuthManager $auth,
        int $id
    ): Response {
        if (($authResponse = $auth->checkAuth($user, $request)) !== null)
            return $authResponse;
        return $this->json([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
        ], Response::HTTP_OK);
    }

    #[Route('/api/users/{id}', name: 'update_username', methods: ['PATCH'], priority: 2)]
    public function updateUser(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $manager,
        AuthManager $auth,
        UserPasswordHasherInterface $hasher,
        int $id
    ): Response {
        if (!$auth->checkAuth($user, $request)) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        try {


            $jsonbody = $request->getContent();
            $body = json_decode($jsonbody, true);

	    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,20}$/';
	    if ($body["password"]){
	            if (!preg_match($pattern, $body['password'])) {
	                return $this->json([
	                    'message' => 'Password problem',
			], Response::HTTP_BAD_REQUEST);
		    }
		    $password = $hasher->hashPassword($user, $body['password']);
		    $user->setPassword($password);
            }

            $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
	    if ($body['email']){
		    if(!preg_match($emailPattern, $body['email'])) {
    		            return $this->json([
    		                'message' => 'Email problem',
    		            ], Response::HTTP_BAD_REQUEST);
		    }
                $user->setEmail($body['email']);
            }

            if ($body['username']) {
                $user->setUsername($body['username']);
            }
            $manager->persist($user);
            $manager->flush();
            return $this->json([
                'message' => 'user updated'
            ], Response::HTTP_OK);
        }catch (UniqueConstraintViolationException $e){
            return $this->json([
                'message' => $e,
            ], Response::HTTP_CONFLICT);

        } catch (Exception $e) {
            return $this->json([
                'message' => $e,
            ], Response::HTTP_CONFLICT);
        }
    }

    #[Route('/api/users/{id}', name: 'delete_user', methods: ['DELETE'], priority: 2)]
    public function deleteId(
        #[CurrentUser()] User $user,
        Request $request,
        EntityManagerInterface $manager,
        AuthManager $auth,
        int $id
    ): Response {

        if (($authResponse = $auth->checkAuth($user, $request)) !== null)
            return $authResponse;
        $jsonbody = $request->getContent();
        $body = json_decode($jsonbody, true);
        if (!password_verify($body['password'], $user->getPassword())) {
            return $this->json([
                'message' => 'wrong credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $manager->getConnection()->beginTransaction();
        try {
            $manager->remove($user);
            $manager->flush();
            $manager->getConnection()->commit();
            $session = $request->getSession();
            $session->invalidate();
            return $this->json([
                'message' => 'User deleted'
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
            ], Response::HTTP_CONFLICT);
        }
    }
}

