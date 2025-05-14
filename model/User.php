<?php

class User {
    private int $id;
    private string $firstname;
    private string $surname;
    private string $sub;
    private string $email;
    private string $password;

    public function __construct(int $id = 0, string $firstname = "", string $surname = "", string $sub = 'F', string $email = "", string $password = "") {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->surname = $surname;
        $this->sub = $sub;
        $this->email = $email;
        $this->password = $password;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getFirstname(): string {
        return $this->firstname;
    }

    public function getSurname(): string {
        return $this->surname;
    }

    public function getSub(): string {
        return $this->sub;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setFirstname(string $firstname): void {
        $this->firstname = $firstname;
    }

    public function setSurname(string $surname): void {
        $this->surname = $surname;
    }

    public function setSub(string $sub): void {
        $this->sub = $sub;
    }

    public function setEmail(string $email): void {
        $this->email = $email;
    }

    public function setPassword(string $password): void {
        $this->password = $password;
    }
}

