<?php

namespace App\Enums;

enum Role: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case DESK_OFFICER = 'desk-officer';
    case EXAM_OFFICER = 'exam-officer';
    case USER = 'user';
}
