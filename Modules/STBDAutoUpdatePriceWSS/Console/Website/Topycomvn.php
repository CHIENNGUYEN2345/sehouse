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

class Topycomvn extends CrawlProductBase
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
                if (strpos($name, 'Mã sản phẩm') !== false) {
                    $data['code'] = $value;
                } elseif (strpos($name, 'Tên sản phẩm') !== false) {

                } elseif (strpos($name, 'xuất xứ') !== false) {

                } elseif (strpos($name, 'Bảo hành') !== false) {
                    $guarantee = Guarantees::where('name', $value)->first();
                    if (!is_object($guarantee)) {
                        $guarantee = Guarantees::create([
                            'name' => $value,
                        ]);
                    }

                    $data['guarantee'] = $guarantee->id;
                } else {
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
            }
            $data['proprerties_id'] = '|' . implode('|', $proprerties_id) . '|';
        }

        $data['highlight'] = @$data['content'];
        unset($data['content']);

        return $data;
    }

    public function updateProduct($product, $data){
        print "        => Updated product " . $product->id . ':' . $product->name . "\n";
        return true;
    }
}
