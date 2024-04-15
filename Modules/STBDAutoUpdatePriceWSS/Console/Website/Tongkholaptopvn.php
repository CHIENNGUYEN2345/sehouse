<?php

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Http\Helpers\CommonHelper;
use Mail;
use Modules\STBDAutoUpdatePriceWSS\Entities\Manufacturer;
use Modules\STBDAutoUpdatePriceWSS\Entities\Origin;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieName;
use Modules\STBDAutoUpdatePriceWSS\Entities\PropertieValue;
use Session;

class Tongkholaptopvn extends CrawlProductBase
{

    protected $module = [
        'code' => 'product',
        'table_name' => 'products',
        'label' => 'Sản phẩm',
        'modal' => '\Modules\STBDAutoUpdatePriceWSS\Entities\Product',
    ];

    public function appendData($data)
    {
        //  Thương hiệu
        if (isset($data['manufacturer_name'])) {
            $manufacturer_slug = str_slug($data['manufacturer_name']);
            $manufacturer = Manufacturer::where('slug', $manufacturer_slug)->first();
            if (!is_object($manufacturer)) {
                $manufacturer = Manufacturer::create([
                    'name' => $data['manufacturer_name'],
                    'slug' => $manufacturer_slug,
                    'status' => 0,
                    'crawl_from' => $this->_domain
                ]);
            }

            $data['manufacture_id'] = $manufacturer->id;
            unset($data['manufacturer_name']);
        }

        //  Xuất sứ
        if (isset($data['origin_name'])) {
            $origin_slug = str_slug($data['origin_name']);
            $origin = Origin::where('slug', $origin_slug)->first();
            if (!is_object($origin)) {
                $origin = Origin::create([
                    'name_origin' => $data['origin_name'],
                    'status' => 0,
                    'crawl_from' => $this->_domain
                ]);
            }

            $data['origin_id'] = $origin->id;
            unset($data['origin_name']);
        }

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

    public function getImageExtra($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->image_extra);
        if ($v != null) {
            $image_extra_arr = [];
            foreach ($v as $image_extra) {
                $image_extra_src = trim(@$image_extra->getAttribute('src'));
                $image_extra_src = str_replace('_compact', '_master', $image_extra_src);

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
