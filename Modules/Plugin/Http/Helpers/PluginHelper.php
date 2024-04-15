<?php

namespace Modules\Plugin\Http\Helpers;

use App\Models\Category;
use App\Models\Meta;
use Auth;
use Modules\EworkingCompany\Models\Company;
use Modules\EworkingJob\Models\Job;
use Modules\EworkingJob\Models\Task;
use Session;
use View;

class PluginHelper
{

    public static function voteStar($review)
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($review != 0) {
                if (is_int($review)) {
                    if ($i <= $review) {
                        $html .= '<i class="fa fa-star" style="color: #ffb900; font-size: 15px"></i>';
                    } elseif ($i > $review) {
                        $html .= '<i class="fa fa-star-o" aria-hidden="true" style="font-size: 15px"></i>';
                    }
                } elseif (!is_int($review)) {
                    if ($i < $review) {
                        $html .= '<i class="fa fa-star" style="color: #ffb900;font-size: 15px"></i>';
                    } elseif ($i == $review + 0.5) {
                        $html .= '<i class="fa fa-star-half-o" style="color: #ffb900;font-size: 15px"></i>';
                    } elseif ($i > $review) {
                        $html .= '<i class="fa fa-star-o" aria-hidden="true" style="font-size: 15px"></i>';
                    }
                }
            } elseif ($review == 0) {
                $html .= '<i class="fa fa-star-o" aria-hidden="true" style="font-size: 15px"></i>';
            }
        }
        return $html;
    }
}
