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

class Electroluxvn extends CrawlProductBase
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

    public function getImage($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->image, 0);
        if ($v != null) {
            if ($v->getAttribute('data-src') !== false) {
                $data['image'] = @$v->getAttribute('data-src');
            } else {
                $data['image'] = trim(@$v->getAttribute('src'));
            }

            $data['image'] = $this->attachDomainToLink($data['image']);

//            $data['image'] = CommonHelper::saveFile($data['image'], 'product/' . str_slug($data['name']));
            $path = 'product/' . str_slug($data['name']);
            $file = $data['image'];

            $file_name_insert = time() . rand(1,1000). '.jpg';

            try {
                $v = file_get_contents($file);
            } catch (\Exception $ex) {
                $arrContextOptions=array(
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );
                $v = @file_get_contents($file, false, stream_context_create($arrContextOptions));
            }
            file_put_contents(base_path() . '/public/filemanager/userfiles/' . $path . '/' . $file_name_insert, $v);
            $data['image'] =  $path . '/' . $file_name_insert;
        }
        return $data;
    }

    public function getFinalPrice($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->final_price, 0);
        if ($v != null) {
            $data['final_price'] = trim($v->innertext);
            $data['final_price'] = str_replace('<span class="product-details__pricing-rrp">Gi&#225; khuyến nghị:</span>', '', $data['final_price']);
//            dd($data['final_price']);
            $data['final_price'] = $this->cleanPrice($data['final_price']);

        }
        return $data;
    }

    public function getAttribute($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->attributes);
//        dd($v);
        if ($v != null) {
            $data['proprerties_id'] = [];
            foreach ($v as $attribute) {
                $name = strip_tags($attribute->find('span', 0)->innertext);
                $val = strip_tags($attribute->find('span', 1)->innertext);
                $data['proprerties_id'][trim($name)] = trim($val);
            }
        }
        return $data;
    }

    public function updateProduct($product, $data){
        print "        => Updated product " . $product->id . ':' . $product->name . "\n";
        return true;
    }
}
