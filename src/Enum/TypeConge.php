<?php

namespace App\Enum;

enum TypeConge: string
{
    case Annuel = 'Annuel';
    case Maladie = 'Maladie';
    case Maternite = 'Maternite';
    case Sanssolde = 'Sans solde';
}
