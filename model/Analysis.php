<?php

class Analysis {
    private string $date;
    private ?Website $website;
    private ?AnalysisType $analysisType;
    private string $result;

    public function __construct(string $date = "", ?Website $website = null, ?AnalysisType $analysisType = null, string $result = "") {
        $this->date = $date;
        $this->website = $website;
        $this->analysisType = $analysisType;
        $this->result = $result;
    }

    // Getters
    public function getDate(): string {
        return $this->date;
    }

    public function getWebsite(): ?Website {
        return $this->website;
    }

    public function getAnalysisType(): ?AnalysisType {
        return $this->analysisType;
    }

    public function getResult(): string {
        return $this->result;
    }

    // Setters
    public function setDate(string $date): void {
        $this->date = $date;
    }

    public function setWebsite(?Website $website): void {
        $this->website = $website;
    }

    public function setAnalysisType(?AnalysisType $analysisType): void {
        $this->analysisType = $analysisType;
    }

    public function setResult(string $result): void {
        $this->result = $result;
    }
}
