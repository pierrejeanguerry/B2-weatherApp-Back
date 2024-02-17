<?php 

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class LoginController extends AbstractController
{
    #[Route('/api/hello', name: 'api_hello', methods: ['GET', 'POST'])]
    public function hello(): Response
    {
      return ($this->json([
        'content' => 'hello'
      ]));
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser()] ?User $user): Response
      {
        if (null === $user) {
          return $this->json([
            'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $token = bin2hex(random_bytes(32));
        return $this->json([
            'user'  => $user->getUserIdentifier(),
            'token' => $token,
        ]);
    }
}