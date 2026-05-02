<?php

namespace App\Model;

class ServiceResult
{
    private bool $succes;
    private mixed $data;
    private array $erreurs;

    private function __construct(bool $succes, mixed $data = null, array $erreurs = [])
    {
        $this->succes = $succes;
        $this->data = $data;
        $this->erreurs = $erreurs;
    }

    public static function ok(mixed $data = null): static
    {
        return new static(true, $data);
    }

    public static function echec(string $message): static
    {
        return new static(false, null, [$message]);
    }

    public static function echecMultiple(array $messages): static
    {
        return new static(false, null, $messages);
    }

    public function isSucces(): bool { return $this->succes; }
    public function getData(): mixed { return $this->data; }
    public function getErreurs(): array { return $this->erreurs; }

    public function getPremierreErreur(): string
    {
        return $this->erreurs[0] ?? '';
    }
}
