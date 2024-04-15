<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Http\Helpers\CommonHelper;
use Mail;
use Modules\STBDAutoUpdatePriceWSS\Entities\Manufacturer;
use Modules\STBDAutoUpdatePriceWSS\Entities\Origin;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieName;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieValue;
use Session;

class Vnsharp extends CrawlProductBase
{

    protected $module = [
        'code' => 'product',
        'table_name' => 'products',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDAutoUpdatePriceWSS\Entities\Product',
    ];

    public function appendData($data)
    {
        //  Thuộc tính
        if (isset($data['proprerties_id'])) {
            $proprerties_id = [];
            foreach ($data['proprerties_id'] as $name => $value) {
                if ($value != '') {
                    $name = str_replace('&gt; ', '', $name);
                    $propertyName = PropertieName::where('name', $name)->first();
                    if (!is_object($propertyName)) {
                        $propertyName = PropertieName::create([
                            'name' => $name
                        ]);
                    }

                    $propertyValue = PropertieValue::where('properties_name_id', $propertyName->id)->where('value', $value)->first();
                    if (!is_object($propertyValue)) {
                        $propertyValue = PropertieValue::create([
                            'properties_name_id' => $propertyName->id,
                            'value' => trim($value)
                        ]);
                    }

                    $proprerties_id[] = $propertyValue->id;
                }
            }
            $data['proprerties_id'] = '|' . implode('|', $proprerties_id) . '|';
        }

        $data['highlight'] = @$data['content'];
        unset($data['content']);

        return $data;
    }

    public function getImageExtra($data, $html) {
        $v = $html->find(@$this->_doom_setting->image_extra);
        if ($v != null) {
            $image_extra_arr = [];
            foreach ($v as $image_extra) {
                $image_extra_src = trim(@$image_extra->getAttribute('src'));

                $image_extra_src = explode('?', $image_extra_src)[0];

                $image_extra_src = str_replace('thumbnail', 'resize_640x640', $image_extra_src);

                $image_extra_src = $this->attachDomainToLink($image_extra_src);

                $image_extra_arr[] = CommonHelper::saveFile($image_extra_src, 'product/' . str_slug($data['name']));
            }
            $data['image_extra'] = '|' . implode('|', $image_extra_arr) . '|';
        }
        return $data;
    }

    public function updateProduct($product, $data){
        print "        => Updated product " . $product->id . ':' . $product->name . "\n";
        return true;
    }
}
