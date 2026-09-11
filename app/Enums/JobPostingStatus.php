<?php

namespace App\Enums;

enum JobPostingStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Closed = 'closed';
    case Archived = 'archived';
}
