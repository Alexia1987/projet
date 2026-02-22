<?php

namespace Entity;

class User {

    private int $id;
    private int $role_id;
    private string $email;
    private string $password;
    private string $firstname;
    private string $lastname;
    private string $phone_number;
    private string $created_at;
    private string $updated_at;

    public function setId( int $id ) : void
    {
        $this->id = $id;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function setRoleId( int $roleId) : void
    {
        $this->role_id = $roleId;
    }

    public function getRoleId() : int
    {
        return $this->role_id;
    }

    public function setEmail( string $email ) : void
    {
        $this->email = $email;
    }

    public function getEmail() : string
    {
        return $this->email;
    }

    public function setPassword( string $password ) : void
    {
        $this->password = $password;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function setFirstname( string $firstname) : void
    {
        $this->firstname = $firstname;
    }

    public function getFirstname () : string
    {
        return $this->firstname;
    }

    public function setLastname( string $lastname) : void
    {
        $this->lastname = $lastname;
    }

    public function getLastname () : string
    {
        return $this->lastname;
    }
    
    public function setPhoneNumber( string $phoneNumber) : void
    {
        $this->phone_number = $phoneNumber;
    }

    public function getPhoneNumber () : string
    {
        return $this->phone_number;
    }

    public function setCreatedAt( string $createdAt ) : void
    {
        $this->created_at = $createdAt;
    }

    public function getCreatedAt() : string
    {
        return $this->created_at;
    }

    public function setUpdatedAt( string $updatedAt ) : void
    {
        $this->updated_at = $updatedAt;
    }

    public function getUpdatedAt() : string
    {
        return $this->updated_at;
    }

}