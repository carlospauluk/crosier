<?php
namespace App\Security;

use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        return new Response("Acesso neg ado!", 403);
    }
}

