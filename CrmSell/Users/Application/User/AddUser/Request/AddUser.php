<?php

namespace CrmSell\Users\Application\User\AddUser\Request;


use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class AddUser extends RootRequest
{
    use PropertyTrait;

    private string $email = '';
    private string $password = '';
    private string $confirmPassword = '';
    private string $firstName = '';
    private string $lastName = '';
    private array $roles = [];

    /**
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->mapField($request, ['roles']);

        $this->roles = !empty($request['roles']) ? array_unique($request['roles']) : [];
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getRoles(): array {
        return $this->roles;
    }

    public function toValidation(): array {
        return [
            "email" => $this->email,
            "password" => $this->password,
            "password_confirmation" => $this->confirmPassword,
            "first_name" => $this->firstName,
            "last_name" => $this->lastName,
        ];
    }

    public function getRules(): array
    {
        return [
            "email" => 'required|string|unique:users,email|email',
            "password" => 'required|string|max:100|min:8|confirmed',
            'password_confirmation' => 'required|min:8',
            "first_name" => 'required|string|max:25|min:2',
            "last_name" => 'required|string|max:25|min:2',
        ];
    }
}
