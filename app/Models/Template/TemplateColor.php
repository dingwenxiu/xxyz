<?php

namespace App\Models\Template;

use App\Models\Base;

class TemplateColor extends Base
{
    static public function getList($c)
    {
        $query = self::where('status', 1);

        $data = $query->get();

        return $data;

    }
}
