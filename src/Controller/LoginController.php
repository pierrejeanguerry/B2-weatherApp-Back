<?php 

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser()] ?User $user, Request $request): Response
      {
        if (null === $user) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = bin2hex(random_bytes(32));
        $session = $request->getSession();
        $session->set('token_user', $token);
        $username = $user->getUsername();
        $email = $user->getEmail();
        return $this->json([
            'token_user' => $token,
            'username' => $username,
            'email' => $email
        ]);
    }

    #[Route('/api/login/check', name: 'check_login', methods: ['POST'])]
    public function check(#[CurrentUser()] ?User $user, Request $request, AuthManager $auth): Response
      {
        
        if (!$auth->checkAuth($user, $request)) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        return $this->json([
          'message' => 'credentials are valid',
        ], Response::HTTP_OK);
    }

    #[Route('/api/login/logout', name: 'logout_login', methods: ['POST'])]
    public function logout(#[CurrentUser()] ?User $user, Request $request, AuthManager $auth): Response
      {
        
        if (!$auth->checkAuth($user, $request)) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $session = $request->getSession();
        $session->invalidate();
        return $this->json([
          'message' => 'session destroyed',
        ], Response::HTTP_OK);
    }
}