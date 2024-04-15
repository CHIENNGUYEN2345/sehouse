<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Http\Helpers\CommonHelper;
use Mail;
use Modules\STBDAutoUpdatePriceWSS\Entities\Manufacturer;
use Modules\STBDAutoUpdatePriceWSS\Entities\Origin;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieName;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieValue;
use Modules\STBDAutoUpdatePriceWSS\Entities\Guarantees;
use Session;

class Gorldevn extends CrawlProductBase
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

    /**
     * Lấy các thuộc tính của sản phẩm
     */
    public function getAttribute($data, $html)
    {

        $v = $html->find(@$this->_doom_setting->attributes, 0);
        if ($v != null) {
            $arr = explode('<br>', $v->innertext);

            $data['proprerties_id'] = [];
            foreach ($arr as $attribute) {
                $name = strip_tags(explode(':', $attribute)[0]);
                $name = str_replace('✔ ', '', $name);

                $val = strip_tags(trim(@explode(':', $attribute)[1]));
                $val = str_replace('&nbsp;', '', $val);

                $data['proprerties_id'][trim($name)] = trim($val);
            }
        }
        dd($data);
        return $data;
    }

    public function updateProduct($product, $data)
    {
        print "        => Updated product " . $product->id . ':' . $product->name . "\n";
        return true;
    }
}
