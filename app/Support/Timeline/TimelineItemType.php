<?php

namespace App\Support\Timeline;

enum TimelineItemType: string
{
    case Post = 'post';
    case JobPublished = 'job_published';
    case ApplicationEvent = 'application_event';
}
