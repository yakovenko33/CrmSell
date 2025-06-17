<?php

namespace CrmSell\Users\Application\User\Update\Request;

use CrmSell\Common\Application\Service\Request\RootRequest;
use CrmSell\Common\Helpers\Traits\PropertyTrait;

class Update extends RootRequest
{
    use PropertyTrait;

    private string $entityId = '';
    private string $email = '';
    private string $password = '';
    private string $confirmPassword = '';
    private string $firstName = '';
    private string $lastName = '';
    private bool $switchResetPassword = false;
    private int $status = 0;

    public function __construct(array $request)
    {
        $this->mapField($request);
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getSwitchResetPassword(): bool {
        return $this->confirmPassword;
    }

    public function getEntityId(): string {
        return $this->entityId;
    }

    public function getPassword(): string {
        return $this->confirmPassword;
    }

    /**
     * @return array
     */
    public function toValidation(): array {
         $data = [
            "email" => $this->email,
            "first_name" => $this->firstName,
            "last_name" => $this->lastName,
        ];
        if ($this->switchResetPassword) {
            $data = array_merge($data, [
                "password" => $this->password,
                "password_confirmation" => $this->confirmPassword,
            ]);
        }
        return $data;
    }

    public function forUpdate(): array
    {
        return [
            "email" => $this->email,
            "first_name" => $this->firstName,
            "last_name" => $this->lastName,
            "status" => $this->status,
        ];
    }

    /**
     * @return string[]
     */
    public function getRules(): array
    {
        $rules = [
            "email" => 'required|string|email',
            "first_name" => 'required|string|max:25|min:2',
            "last_name" => 'required|string|max:25|min:2',
        ];
        if ($this->switchResetPassword) {
            $rules = array_merge($rules, [
                "password" => 'required|string|max:100|min:8|confirmed',
                'password_confirmation' => 'required|min:8',
            ]);
        }
        return $rules;
    }
}
