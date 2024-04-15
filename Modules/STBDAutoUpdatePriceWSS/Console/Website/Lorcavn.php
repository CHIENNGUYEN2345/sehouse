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

class Lorcavn extends CrawlProductBase
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
                if ($name == 'Xuất xứ') {
                    $origin_slug = str_slug($value);
                    $origin = Origin::where('slug', $origin_slug)->first();
                    if (!is_object($origin)) {
                        $origin = Origin::create([
                            'name_origin' => $value,
                            'status' => 0,
                            'crawl_from' => $this->_domain
                        ]);
                    }

                    $data['origin_id'] = $origin->id;
                } elseif ($name == 'Thương Hiệu') {
                    $manufacturer_slug = str_slug($value);
                    $manufacturer = Manufacturer::where('slug', $manufacturer_slug)->first();
                    if (!is_object($manufacturer)) {
                        $manufacturer = Manufacturer::create([
                            'name' => $value,
                            'slug' => $manufacturer_slug,
                            'status' => 0,
                            'crawl_from' => $this->_domain
                        ]);
                    }

                    $data['manufacture_id'] = $manufacturer->id;
                } elseif ($name == 'Bảo Hành') {
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

    /**
     * Lấy các thuộc tính của sản phẩm
     */
    public function getAttribute($data, $html)
    {

        $v = $html->find(@$this->_doom_setting->attributes, 0);
        if ($v != null) {
            $arr = explode('</br>', $v->innertext);

            $data['proprerties_id'] = [];
            foreach ($arr as $attribute) {
                $name = strip_tags(explode(':', $attribute)[0]);
                $name = str_replace('✔ ', '', $name);

                $val = strip_tags(trim(@explode(':', $attribute)[1]));
                $val = str_replace('&nbsp;', '', $val);

                $data['proprerties_id'][trim($name)] = trim($val);
            }
        }
        return $data;
    }

    /**
     * Lưu ảnh trong phần nội dung về server
     */
    public function saveImgInContent($content, $content_doom, $path = 'uploads/')
    {
        //  Tìm và lấy các thẻ <img
        $img_doom_arr = $content_doom->find('img');
        foreach ($img_doom_arr as $img_doom) {
            $img_doom_src2 = false;

            // lấy link ảnh trong data-src hay trong src
            if ($img_doom->getAttribute('data-src') !== false) {
                $img_doom_src = @$img_doom->getAttribute('data-src');
                $img_doom_src2 = @$img_doom->getAttribute('src');
            } else {
                $img_doom_src = trim(@$img_doom->getAttribute('src'));
            }

            //  Xóa các ký tự thừa trong link ảnh
            $img_doom_src = explode('?', $img_doom_src)[0];

            //  Gắn tên miền vào link ảnh
            $img_src = $this->attachDomainToLink($img_doom_src);

            //  Lưu link ảnh
            $img_src = CommonHelper::saveFile($img_src, $path);

            //  Thay link ảnh mới ở server mình vào link ảnh cũ ở server web nguồn
            $content = str_replace($img_doom_src, '/public/filemanager/userfiles/' . $img_src, $content);
            if ($img_doom_src2) {
                $content = str_replace($img_doom_src2, '/public/filemanager/userfiles/' . $img_src, $content);
            }
        }

        //  Tìm và lấy các thẻ <input
        $img_doom_arr = $content_doom->find('input');
        foreach ($img_doom_arr as $img_doom) {
            $img_doom_src2 = false;

            // lấy link ảnh trong data-src hay trong src
            if ($img_doom->getAttribute('data-src') !== false) {
                $img_doom_src = @$img_doom->getAttribute('data-src');
                $img_doom_src2 = @$img_doom->getAttribute('src');
            } else {
                $img_doom_src = trim(@$img_doom->getAttribute('src'));
            }

            //  Xóa các ký tự thừa trong link ảnh
            $img_doom_src = explode('?', $img_doom_src)[0];

            //  Gắn tên miền vào link ảnh
            $img_src = $this->attachDomainToLink($img_doom_src);

            //  Lưu link ảnh
            $img_src = CommonHelper::saveFile($img_src, $path);

            //  Thay link ảnh mới ở server mình vào link ảnh cũ ở server web nguồn
            $content = str_replace($img_doom_src, '/public/filemanager/userfiles/' . $img_src, $content);
            if ($img_doom_src2) {
                $content = str_replace($img_doom_src2, '/public/filemanager/userfiles/' . $img_src, $content);
            }

            $content = str_replace('<input', '<img', $content);
        }
        return $content;
    }

    /**
     * Lấy link danh sách sản phẩm
     */
    public function getPageListLink($cat_doom, $doom_setting, $i)
    {
        $link = str_replace('{i}', $i, $doom_setting->category_pagination);
        return str_replace('.html', $link . '.html', $cat_doom->link_crawl);
    }

    public function updateProduct($product, $data)
    {
        print "        => Updated product " . $product->id . ':' . $product->name . "\n";
        return true;
    }
}
