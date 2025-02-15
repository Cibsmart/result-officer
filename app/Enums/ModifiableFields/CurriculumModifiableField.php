<?php

declare(strict_types=1);

namespace App\Enums\ModifiableFields;

enum CurriculumModifiableField: string
{
    case MINIMUM_CREDIT_UNITS = 'minimum_credit_units';
    case MAXIMUM_CREDIT_UNITS = 'maximum_credit_units';
    case MINIMUM_ELECTIVE_COUNT = 'minimum_elective_count';
    case MINIMUM_ELECTIVE_UNITS = 'minimum_elective_units';

    case COURSE = 'course';
    case COURSE_TYPE = 'course_type';
    case CREDIT_UNIT = 'credit_unit';
}
