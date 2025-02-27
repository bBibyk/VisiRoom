<?php

class Website {
    private int $id;
    private string $domainname;
    private ?User $user;

    public function __construct(int $id = 0, string $domainname = "", ?User $user = null) {
        $this->id = $id;
        $this->domainname = $domainname;
        $this->user = $user;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getDomainname(): string {
        return $this->domainname;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setDomainname(string $domainname): void {
        $this->domainname = $domainname;
    }

    public function setUser(?User $user): void {
        $this->user = $user;
    }
}
