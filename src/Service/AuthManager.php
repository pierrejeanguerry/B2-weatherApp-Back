<?php
namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AuthManager
{

    public function checkAuth(#[CurrentUser()] User $user, Request $request): bool
    {
        $session = $request->getSession();
        $token = $request->headers->get('token_user');
        if (null === $user || !($token == $session->get("token_user"))) {
          return false;
        }
        return true;
    }
}