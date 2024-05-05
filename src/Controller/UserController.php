<?php 

namespace App\Controller;

use App\Entity\User;
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
    #[Route('/api/user/username/get', name: 'get_username', methods: ['GET'])]
    public function index(#[CurrentUser()] ?User $user, Request $request): Response{
        $session = $request->getSession();
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing or wrong credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
            'username' => $user->getUsername()
        ], Response::HTTP_OK);
    }
    
    #[Route('/api/user/username/update', name: 'update_username', methods: ['PUT'])]
    public function updateUsername(#[CurrentUser()] ?User $user, Request $request, EntityManagerInterface $manager): Response{
        $session = $request->getSession();
        $jsonbody = $request->getContent();
        $body = json_decode($jsonbody, true);
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing or wrong credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user->setUsername($body['username']);
        $manager->persist($user);
        $manager->flush();
        return $this->json([
            'message' => 'username changed'
        ], Response::HTTP_OK);
    }

    #[Route('/api/user/id/get', name: 'get_id', methods: ['GET'])]
    public function getId(#[CurrentUser()] ?User $user, Request $request): Response{
        $session = $request->getSession();
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing or wrong credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
            'username' => $user->getEmail()
        ], Response::HTTP_OK);
    }

    #[Route('/api/user/id/update', name: 'update_id', methods: ['PUT'])]
    public function updateId(#[CurrentUser()] ?User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response{
        $session = $request->getSession();
        $jsonbody = $request->getContent();
        $body = json_decode($jsonbody, true);
        $token = $request->headers->get('token_user');
        if (null === $user ||
            !($token == $session->get("token_user")) || 
            !password_verify($body["currentPassword"], $user->getPassword())) {
          return $this->json([
            'message' => 'missing or wrong credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $manager->getConnection()->beginTransaction();
        try {
            $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{10,20}$/';
            $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
            
            if (!$body['password'] || !preg_match($pattern, $body['password'])){
                return $this->json([
                    'message' => 'Password problem',
                    ], Response::HTTP_BAD_REQUEST);
            }
            if (!$body['email'] || !preg_match($emailPattern, $body['email'])){
                return $this->json([
                    'message' => 'Email problem',
                    ], Response::HTTP_BAD_REQUEST);
            }
            $user->setEmail($body['email']);
            $password = $hasher->hashPassword($user, $body['password']);
            $user->setPassword($password);
            $manager->persist($user);
            $manager->flush();
            $manager->getConnection()->commit();
            return $this->json([
                'message' => 'Id updated',
                ], Response::HTTP_OK);

        } catch(UniqueConstraintViolationException $e) {
            $manager->getConnection()->rollBack();
            return $this->json([
                'message' => $e,
                ], Response::HTTP_CONFLICT);
        } catch (Exception $e){
            return $this->json([
                'message' => $e,
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/user/id/delete', name: 'delete_id', methods: ['DELETE'])]
    public function deleteId(#[CurrentUser()] ?User $user, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response{
        $session = $request->getSession();
        $jsonbody = $request->getContent();
        $body = json_decode($jsonbody, true);
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return $this->json([
            'message' => 'missing or wrong credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        if (!password_verify($body['password'], $user->getPassword())){
            return $this->json([
                'message' => 'wrong credentials',
                ], Response::HTTP_UNAUTHORIZED);
        }
        $manager->getConnection()->beginTransaction();
        try {
            $manager->remove($user);
            $manager->flush();
            $manager->getConnection()->commit();
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