<?php

class AnalysisType {
    private int $id;
    private string $label;

    public function __construct(int $id = 0, string $label = "") {
        $this->id = $id;
        $this->label = $label;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getLabel(): string {
        return $this->label;
    }

    // Setters
    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setLabel(string $label): void {
        $this->label = $label;
    }
}
