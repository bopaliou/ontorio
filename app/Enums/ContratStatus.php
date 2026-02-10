<?php

namespace App\Enums;

enum ContratStatus: string
{
    case ACTIF = 'actif';
    case TERMINE = 'terminé';
    case RESILIE = 'résilié';
}
