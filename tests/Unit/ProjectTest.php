<?php

namespace Tests\Unit;

use App\Models\Project;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function test_deadline_format_is_correct()
    {
        $project = new Project();
        $project->deadline = '2001-12-31';

        $regex = '/^(0?[1-9]|1[0-2])\/(0?[1-9]|[1-2]\d|3[0-1])\/\d+/';
        $this->assertMatchesRegularExpression($regex, $project->formattedDeadline);
    }
}
