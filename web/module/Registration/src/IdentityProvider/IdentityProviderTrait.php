<?php
namespace Registration\IdentityProvider;

use Laminas\Http\PhpEnvironment\Request as Request;

trait IdentityProviderTrait
{

    protected function checkNames($request)
    {
        $firstName = $this->get($request, 'firstName');
        $lastName = $this->get($request, 'lastName');
        return $this->isName($firstName) && $this->isName($lastName);
    }

    protected function isName($attribute)
    {
        return $attribute != null && preg_match('/^[\w]+$/', $attribute);
    }

    protected function get(Request $request, string $variable)
    {
        return $request->getServer($variable);
    }

}