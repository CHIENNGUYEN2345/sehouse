<?php

if (isset($item) && isset($field)) {
    $admin_ids = $item->{$field['name']};
}

?>
@if (isset($admin_ids))
    <?php
    $admin_ids = is_array($admin_ids) ? $admin_ids : explode('|', $admin_ids);

    $admins = \App\Models\Admin::select(['id', 'name', 'image'])->whereIn('id', $admin_ids)->get();
//    dd($admins);
    ?>

    @if(count($admins) > 0)
        <div class="kt-widget__details">
            <div class="kt-section__content" style="padding: 0 0 0 25px;">
                <div class="kt-media-group">
                    <?php
                    $countArray = (count($admins) >= 5) ? 5 : count($admins);
                    ?>
                    @for($i = 0; $i < $countArray; $i ++)
                        <?php
                        $admin = $admins[$i];
                        ?>
                        <a href="{{URL::to('/admin/profile/'.$admin->id)}}"
                           class="kt-media kt-media--sm kt-media--circle"
                           data-toggle="kt-tooltip" data-skin="brand"
                           data-placement="top"
{{--                           title="{{ $admin['name'] }}"--}}
{{--                           data-original-title="{{ $admin['name'] }}"--}}
                        >
                            <img data-src="{{ \App\Http\Helpers\CommonHelper::getUrlImageThumb($admin['image'], 30, 30) }}" class="lazy"
                                 alt="{{ $admin['name'] }}" title="{{ $admin['name'] }}">
                        </a>
                    @endfor
                    @if(count($admins) > 5)
                        <a href="#"
                           class="kt-media kt-media--sm kt-media--circle"
                           data-toggle="kt-tooltip" data-skin="brand"
                           data-placement="top" title=""
                           data-original-title="Micheal York">
                            <span>{{count($admins) - 5}}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endif
