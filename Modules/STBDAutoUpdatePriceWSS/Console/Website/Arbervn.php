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

class Arbervn extends CrawlProductBase
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

    public function getAttribute($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->attributes);
//        dd($v);
        if ($v != null) {
            $data['proprerties_id'] = [];
            foreach ($v as $attribute) {
                $name = strip_tags($attribute->find('td', 0)->innertext);
                $name = str_replace('&nbsp;', '', $name);

                $val = strip_tags($attribute->find('td', 1)->innertext);
                $val = str_replace('&nbsp;', '', $val);

                if (strpos($name, 'H&Atilde;NG SẢN XU&Acirc;́T') !== false) {
                    $manufacturer_slug = str_slug($val);
                    $manufacturer = Manufacturer::where('slug', $manufacturer_slug)->first();
                    if (!is_object($manufacturer)) {
                        $manufacturer = Manufacturer::create([
                            'name' => $val,
                            'slug' => $manufacturer_slug,
                            'status' => 0,
                            'crawl_from' => $this->_domain
                        ]);
                    }

                    $data['manufacture_id'] = $manufacturer->id;
                } else {
                    $data['proprerties_id'][trim($name)] = trim($val);
                }
            }
        }
        return $data;
    }

    /**
     * Lấy ảnh của sản phẩm
     */
    public function getImage($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->image, 0);
        if ($v != null) {
            if ($v->getAttribute('data-src') !== false) {
                $data['image'] = @$v->getAttribute('data-src');
            } else {
                $data['image'] = trim(@$v->getAttribute('src'));
            }

            $data['image'] = str_replace('&w=300', '', $data['image']);

            $data['image'] = $this->attachDomainToLink($data['image']);

            $data['image'] = CommonHelper::saveFile($data['image'], 'product/' . str_slug($data['name']), time().rand(1,1000).'.jpg');
        }
        return $data;
    }

    /**
     * Lấy ảnh thêm của sản phẩm
     */
    public function getImageExtra($data, $html)
    {
        $v = $html->find(@$this->_doom_setting->image_extra);
        if ($v != null) {
            $image_extra_arr = [];
            foreach ($v as $image_extra) {
                $image_extra_src = trim(@$image_extra->getAttribute('src'));

                $image_extra_src = str_replace('&h=36&w=40', '&w=500', $image_extra_src);

                $image_extra_src = $this->attachDomainToLink($image_extra_src);

                $image_extra_arr[] = CommonHelper::saveFile($image_extra_src, 'product/' . str_slug($data['name']), time().rand(1,1000).'.jpg');
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
