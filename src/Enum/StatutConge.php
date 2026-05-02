<?php

namespace App\Enum;

enum StatutConge: string
{
    case EnAttente = 'En attente';
    case Approuve = 'Approuvé';
    case Refuse = 'Refusé';
}
