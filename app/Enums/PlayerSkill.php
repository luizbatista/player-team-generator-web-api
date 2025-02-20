<?php

namespace App\Enums;

enum PlayerSkill: string
{
    case ATTACK = 'attack';
    case DEFENSE = 'defense';
    case SPEED = 'speed';
    case STRENGTH = 'strength';
    case STAMINA = 'stamina';
}
