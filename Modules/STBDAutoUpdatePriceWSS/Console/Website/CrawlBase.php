<?php
/**
 * Created by PhpStorm.
 * User: hoanghung
 * Date: 08/09/2016
 * Time: 19:52
 */

namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

class CrawlBase extends Base
{

    protected $website_allowed_lost_photos = [
        'http://khosachnoi.com/',
        'https://sachvui.com/',
        'https://ebook.vn/',
    ];

    public function crawlWebsite($website)
    {
        print "*Website: " . $website->name . "\n";

        $doom_setting = json_decode($website->doom);

        $categories = Category::where('website_id', $website->id)->where('status', 1)->get();

        foreach ($categories as $category) {
            $result = $this->crawlCategory($website, $category, $doom_setting);
        }

        return [
            'status' => false
        ];
    }

    public function crawlCategory($website, $category, $doom_setting)
    {
        print "  -Category: " . $category->id . '|' . $category->name . "\n";

        //  API
        if (isset($doom_setting->product->api)) {
            $result = $this->crawlApi($website, $category, $doom_setting);
            if ($result['status']) {
                return [
                    'status' => true
                ];
            }
        }

        $flag = true;
        $i = 1;
        $style_categories = Category::where('style',1)->first();
        while ($flag) {
//    day nhe aaaaaaaaa

            if($style_categories != null){
                if (strpos($website->name, 'khosachnoi.com')) {
                    $link = 'ctl00$ContentPlaceHolder1$ucBookSearch1$rptPage$ctl0' . ($i - 1) . '$lkbPage';
                } else {
                    if (strpos($doom_setting->category->paginate, 'http') === false) {
                        if (strpos($category->link, '?') == false && strpos($website->name, 'ebook.vn') == false) {
                            $link = $category->link . '/' . str_replace('{number}', $i, $doom_setting->category->paginate);
                        } else {
                            $link = $category->link . str_replace('{number}', $i, $doom_setting->category->paginate);
                        }
                    } else {
                        $link = str_replace('{number}', $i, $doom_setting->category->paginate);
                    }
                }
            }else{
                $link = $category->link;
            }


            if (!$this->checkUrl404($link) || strpos($website->name, 'khosachnoi.com')) {
                $result = $this->crawlAllProductInLink($website, $category, $doom_setting, $link);
                if (!$result['status'] && $result['msg'] == 'Hết sản phẩm') {
                    $flag = false;
                    print "      =>Hết sản phẩm" . "\n";
                }

                if (isset($doom_setting->category->paginate_step) && $doom_setting->category->paginate_step != 1) {
                    $i *= $doom_setting->category->paginate_step;
                    if ($i >= 3000) { // Toi da 1000 san pham
                        $flag = false;
                    }
                } else {
                    $i++;
                    if ($i >= 200) { // Toi da 50 trang
                        $flag = false;
                    }
                }
            } else {
                $this->writeLog([
                    'type' => 0,
                    'action' => 'CURL link danh mục',
                    'website_id' => $website->id,
                    'name' => 'Link bị 404 ' . $link,
                    'msg' => 'Link bị 404 ' . $link,
                    'link' => $link
                ]);
                $flag = false;
            }
        }
        return [
            'status' => true,
            'msg' => 'Hoàn thành!'
        ];
    }

    public function crawlApi($website, $category, $doom_setting)
    {
        print "    -API: " . "\n";
        $flag = true;
        $i = 1;
        while ($flag) {
            $api_url = str_replace('{number}', $i, $category->link);
            $data = json_decode(file_get_contents($api_url));
            if (empty($data->response->videos) || $data->success == false) {
                $flag = false;
            } else {
                foreach ($data->response->videos as $video) {
                    $product_exist = Product::where('link', $video->embedded_url)->first();
                    if (!is_object($product_exist)) {
                        $apiObject = new Api();
                        $product_data = $apiObject->getDataItem($video);
                    } else {
                        print "        => Đã có: " . $video->embedded_url . "\n";
                        return false;
                    }

                    $product_data = $this->getBasicDataProduct($website, $category, $doom_setting, $video->embedded_url, false, $product_data);
                    $this->CreateProduct($website, $product_data);
                }
                $i++;
            }
        }
        return [
            'status' => true,
            'msg' => ''
        ];
    }

    public function deleteSpecialCharacters($string)
    {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
        $string = preg_replace('/[()]/', '-', $string);
        $string = preg_replace('/[|]/', '-', $string);
        $string = preg_replace('/[!@#$%^&*{}:">?<;]/', '-', $string);
        $string = preg_replace("/[']/", '-', $string);
        return preg_replace('/[,.]/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public function crawlAllProductInLink($website, $category, $doom_setting, $link)
    {
        print "        +crawlAllProductInLink: " . $link . "\n";
        if (strpos($website->name, 'khosachnoi.com')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__) . '/cookie.txt');
            curl_setopt($ch, CURLOPT_URL, $category->link);
            $rs = str_get_html(curl_exec($ch));
            $reqFields = array(
                "__EVENTTARGET" => $link,
                "__EVENTARGUMENT" => "",
                "__VIEWSTATE" => $rs->find("input[id=__VIEWSTATE]", 0)->value,
                "__VIEWSTATEGENERATOR" => $rs->find("input[id=__VIEWSTATEGENERATOR]", 0)->value,
                "__EVENTVALIDATION" => $rs->find("input[id=__EVENTVALIDATION]", 0)->value,
                'ctl00$txtKey' => ''
            );

            curl_setopt($ch, CURLOPT_URL, $category->link);
            curl_setopt($ch, CURLOPT_POST, count($reqFields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($reqFields));
            $html = str_get_html(curl_exec($ch));
        } else {
            $html = new \Htmldom($link);
        }
        if (empty($html->find($doom_setting->product->target))) {       //  Neu trang nay ko co san pham thi ket thuc
            return [
                'status' => false,
                'msg' => 'Hết sản phẩm'
            ];
        } else {
            foreach ($html->find($doom_setting->product->target) as $product) {
                //  Get link product
                if ($doom_setting->product->link == 'this') {
                    $product_link = $product->getAttribute('href');
                } else {
                    if ($product->find($doom_setting->product->link, 0) != null) {
                        $product_link = $product->find($doom_setting->product->link, 0)->getAttribute('href');
                    }
                }

                if (!isset($product_link)) {
                    print "        => Không thấy link sản phẩm\n";
                    continue;
                }

                if (strpos($product_link, '&') !== false) {
                    $product_link = explode('&', $product_link)[0];
                }
                if (strpos($product_link, 'http') === false) {
                    if ($product_link[0] == '/') {
                        $product_link = substr($product_link, 1);
                    }
                    $product_link = $website->name . $product_link;
                }

                $image_in_list = false;
                if (isset($doom_setting->product->image_in_list) && $doom_setting->product->image_in_list != '') {
                    if (!is_null($product->find($doom_setting->product->image_in_list, 0))) {
                        if (isset($doom_setting->product->image_attribute) && $doom_setting->product->image_attribute != '' && $doom_setting->product->image_attribute != 'src') {
                            $image_in_list = @$product->find($doom_setting->product->image_in_list, 0)->getAttribute($doom_setting->product->image_attribute);
                        } else {
                            $image_in_list = @$product->find($doom_setting->product->image_in_list, 0)->getAttribute('src');
                        }
                    }
                }

                $product_exist = Product::where('link', $product_link)->first();
                print "          +crawlProductInLink: " . $product_link . "\n";
                if (!is_object($product_exist)) {
                    $result = $this->crawlProductInLink($website, $category, $doom_setting, $product_link, $image_in_list);
                    $name_en = strtolower($this->convert_vi_to_en($result['data']['name']));
                    if (isset($result['data']['author_id'])) {
                        $product_exist = Product::where('name_en', $name_en)->where('author_id', $result['data']['author_id'])->first();        //  Tim quyen tuong tu de merge
                    }
                    if (($result['status'] && isset($result['data']['audio']) && $result['data']['image'] != '')
                        || isset($result['data']['file']) || isset($result['data']['read_online']) || isset($result['data']['read_online_iframe'])) {
                        if (is_object($product_exist)) {    // Trung ten thi merge
                            if ($this->checkUpdate($website, $result['data'])) {
                                $this->shortUpdateProduct($website, $product_exist, $result['data']);
                            } else {
                                print "              => Không được update\n";
                            }
                        } else {    // Tao moi
                            $this->createProduct($website, $result['data']);
                        }
                    } elseif (strpos($website->name, 'ebook.vn')) {
                        $this->createProduct($website, $result['data']);
                    } else {
                        print "              => Không thấy audio/file\n";
                    }
                } else {
//                    $this->crawlProductInLink($website, $category, $doom_setting, $product_link, $image_in_list);
                    print "              => Đã có\n";

                    //  Neu da  co  thi update
                    $result = $this->crawlProductInLink($website, $category, $doom_setting, $product_link, $image_in_list, $product_exist);
                    if ($this->checkUpdate($website, $result['data'])) {
                        $this->shortUpdateProduct($website, $product_exist, $result['data']);
                    } else {
                        print "              => Không được update\n";
                    }

                    /*if (($result['status'] && isset($result['data']['audio']) && $result['data']['image'] != '')
                        || isset($result['data']['file']) || isset($result['data']['read_online']) || isset($result['data']['read_online_iframe'])) {

                    } else {

                    }*/
                }
            }
        }
        return [
            'status' => true,
            'msg' => 'Hoàn thành'
        ];
    }

    public function checkUpdate($website, $product_data) {
        if (strpos($website->name, 'sachnoionline.net')) {
            return false;
        } elseif (strpos($website->name, 'khosachnoi.com')) {
            return false;
        } elseif (strpos($website->name, 'ebook.vn')) {
            if (isset($product_data['ebook_price']) || isset($product_data['final_price'])) {
                return true;
            }
        } elseif (strpos($website->name, 'sachvui.com')) {
            return false;
        }
        return true;
    }

    public function shortUpdateProduct($website, $product, $product_data)
    {
        print "            =>shortUpdateProduct: " . $product->id . '|' . $product->link . "\n";

        $update_option = '';

        /*if (isset($product_data['size']) && $product_data['size'] != '') {
            $product->size = $product_data['size'];
            $update_option .= 'size' . '|';
        }
        if (isset($product_data['publishing_id']) && $product_data['publishing_id'] != '') {
            $product->publishing_id = $product_data['publishing_id'];
            $update_option .= 'publishing_id' . '|';
        }
        if (isset($product_data['company_id']) && $product_data['company_id'] != '') {
            $product->company_id = $product_data['company_id'];
            $update_option .= 'company_id' . '|';
        }
        if (isset($product_data['page_number']) && $product_data['page_number'] != '') {
            $product->page_number = $product_data['page_number'];
            $update_option .= 'page_number' . '|';
        }*/

        if ($product->ebook_price == 0 && isset($product_data['ebook_price']) && $product_data['ebook_price'] != '') {
            $product->ebook_price = $product_data['ebook_price'];
            $update_option .= 'ebook_price' . '|';
        }
        if ($product->final_price == null && isset($product_data['final_price']) && $product_data['final_price'] != '') {
            $product->final_price = $product_data['final_price'];
            $update_option .= 'final_price' . '|';
        }
        /*if ($product->audio == null && isset($product_data['audio']) && $product_data['audio'] != '' && $product_data['audio'] != false) {
            $product->audio = $product_data['audio'];
            $update_option .= 'audio' . '|';
        }
        if ($product->content == null && isset($product_data['content']) && $product_data['content'] != '') {
            $product->content = $product_data['content'];
            $update_option .= 'content' . '|';
        }
        if ($product->kind == null && isset($product_data['kind']) && $product_data['kind'] != '') {
            $product->kind = $product_data['kind'];
            $update_option .= 'kind' . '|';
        }
        if ($product->reader == null && isset($product_data['reader']) && $product_data['reader'] != '') {
            $product->reader = $product_data['reader'];
            $update_option .= 'reader' . '|';
        }

        if ($product->file == null && isset($product_data['file']) && $product_data['file'] != '') {
            $product->file = $product_data['file'];
            $update_option .= 'file' . '|';
        }
        if ($product->image == null && isset($product_data['image']) && $product_data['image'] != '') {
            $product->image = $product_data['image'];
            $update_option .= 'image' . '|';
        }

        if ($product->read_online_iframe == null && isset($product_data['read_online_iframe']) && $product_data['read_online_iframe'] != '') {
            $product->read_online_iframe = $product_data['read_online_iframe'];
            $update_option .= 'read_online_iframe' . '|';
        }*/
        if ($product->read_online == 0 && isset($product_data['read_online']) && !empty($product_data['read_online'])) {
            try {
                $product->read_online = count($product_data['read_online']);
                $product->save();
                file_put_contents(base_path() . '/public/filemanager/userfiles/read_online/eb/' . $product->id . '.txt', json_encode($product_data['read_online']));
                $update_option .= 'read_online' . '|';
            } catch (\Exception $ex) {

            }
        }

        $product->version = 6;
        print "              =>update_option: " . $update_option . "\n";

        if ($product->save()) {
            $this->writeLog([
                'type' => 1,
                'action' => 'Cập nhật sản phẩm',
                'website_id' => $website->id,
                'product_name' => $product_data['name'],
//                        'product_code' => $product_data['code'],
                'link' => $product_data['link'],
                'product_id' => $product->id
            ]);
            return [
                'status' => true,
                'msg' => 'Đã cập nhật nhanh sản phẩm'
            ];
        }
        return [
            'status' => false,
            'msg' => 'Đã cập nhật nhanh sản phẩm'
        ];
    }

    public function crawlProductInLink($website, $category, $doom_setting, $product_link, $image_in_list, $product = false)
    {
        if ($this->checkUrl404($product_link)) {
            print "          => Link die:" . $product_link . "\n";
            $this->writeLog([
                'type' => 0,
                'action' => 'CURL link sản phẩm',
                'website_id' => $website->id,
                'name' => 'Link bị 404 ' . $product_link,
                'msg' => 'Link bị 404 ' . $product_link,
                'link' => $product_link
            ]);
            return [
                'status' => false,
                'msg' => 'Link die'
            ];
        }

        $product_data = $this->getDataProduct($website, $doom_setting, $product_link, $product);
        if (isset($product_data['status']) && !$product_data['status']) {
            print "          => " . $product_data['msg'] . "\n";
            return [
                'status' => false,
                'msg' => $product_data['msg']
            ];
        }

        $product_data = $this->getBasicDataProduct($website, $category, $doom_setting, $product_link, $image_in_list, $product_data);

        return [
            'status' => true,
            'data' => $product_data,
            'msg' => 'Lấy dữ liệu thành công'
        ];
    }

    public function createProduct($website, $product_data)
    {
        $product_data['name_en'] = strtolower($this->convert_vi_to_en($product_data['name']));
        if (isset($product_data['read_online']) && !empty($product_data['read_online'])) {
            $read_online_data = $product_data['read_online'];
            $product_data['read_online'] = count($product_data['read_online']);
        }
        $product_data['version'] = 2;
        $product = Product::create($product_data);
        if ($product) {
            if (isset($read_online_data) && !empty($read_online_data)) {
                try {
                    file_put_contents(base_path() . '/public/filemanager/userfiles/read_online/sv/' . $product->id . '.txt', json_encode($read_online_data));
                } catch (\Exception $ex) {

                }
            }

            print "            =>CREATED: " . $product->id . '|' . $product_data['link'] . "\n";
            $this->writeLog([
                'type' => 1,
                'action' => 'Thêm sản phẩm',
                'website_id' => $website->id,
                'product_name' => $product_data['name'],
//                        'product_code' => @$product_data['code'],
                'link' => $product_data['link'],
                'product_id' => $product->id
            ]);
            return [
                'status' => true,
                'msg' => 'Đã thêm'
            ];
        }
        return [
            'status' => false,
            'msg' => 'Không  xác định lỗi'
        ];
    }

    public function getBasicDataProduct($website, $category, $doom_setting, $product_link, $image_in_list, $product_data)
    {
        $product_data['link'] = $product_link;
        $product_data['category_id'] = @$category->insert_to_category;
        $product_data['multi_cat'] = '|' . @$category->insert_to_category . '|';
        $product_data['status'] = 1;
//        $product_data['category_crawl_link'] = $url_crawl;
        $product_data['website_id'] = $website->id;
        if ($image_in_list) {
            $product_data['image'] = $image_in_list;
        }
        return $product_data;
    }

    public function getDataProduct($website, $doom_setting, $product_link, $product = false)
    {
        if (strpos($website->name, 'sachnoionline.net')) {
            $websiteCrawler = new SachnoionlineNet();
            $data = $websiteCrawler->getDataItem($website, $doom_setting, $product_link, $product);
        } elseif (strpos($website->name, 'khosachnoi.com')) {
            $websiteCrawler = new KhosachnoiCom();
            $data = $websiteCrawler->getDataItem($website, $doom_setting, $product_link, $product);
        } elseif (strpos($website->name, 'ebook.vn')) {
            $websiteCrawler = new Ebook();
            $data = $websiteCrawler->getDataItem($website, $doom_setting, $product_link, $product);
        } elseif (strpos($website->name, 'sachvui.com')) {
            $websiteCrawler = new SachvuiCom();
            $data = $websiteCrawler->getDataItem($website, $doom_setting, $product_link, $product);
        } elseif (strpos($website->name, 'vndoc.com')) {
            $websiteCrawler = new VnDoc();
            $data = $websiteCrawler->getDataItem($website, $doom_setting, $product_link, $product);
        } else {
            try {
                //  Default crawl data product
                $html = new \Htmldom($product_link);
                $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);
                if ($doom_setting->product->name_remove !== null) $data['name'] = preg_replace($doom_setting->product->name_remove, '', $data['name']);

                $remove_char = ['.', ','];
//            dd($doom_setting->product->price);
                /*if (isset($doom_setting->product->price_child) && $doom_setting->product->price_child != null) {
                    $data['price'] = str_replace($remove_char, '', @$html->find($doom_setting->product->price, (int)$doom_setting->product->price_child)->innertext);
                } else {
                    $data['price'] = str_replace($remove_char, '', @$html->find($doom_setting->product->price, 0)->innertext);
                }

                if (isset($doom_setting->product->price_remove) && $doom_setting->product->price_remove !== null) $data['price'] = preg_replace($doom_setting->product->price_remove, '', $data['price']);
                $data['price'] = (int)$data['price'];
    //            dd($data);
                if ($data['price'] == 0) unset($data['price']);
                if ($doom_setting->product->price_old !== null) {
                    if (isset($doom_setting->product->price_old_child) && $doom_setting->product->price_old_child != null) {
                        $data['price_old'] = @$html->find($doom_setting->product->price_old, (int)$doom_setting->product->price_old_child)->innertext;
                    } else {
                        $data['price_old'] = @$html->find($doom_setting->product->price_old, 0)->innertext;
                    }
                    $data['price_old'] = (int)str_replace($remove_char, '', $data['price_old']);
                    $data['price_old'] = (int)$data['price_old'];
                    if ($data['price_old'] == 0) unset($data['price_old']);
                }*/

                /*$data['code'] = trim(@$html->find($doom_setting->product->code, 0)->innertext);
                if ($doom_setting->product->code_remove !== null) $data['code'] = preg_replace($doom_setting->product->code_remove, '', $data['code']);*/
                #
                if (isset($doom_setting->product->image_attribute) && $doom_setting->product->image_attribute != '' && $doom_setting->product->image_attribute != 'src') {  //  Neu lay link anh != img src
                    if ($html->find($doom_setting->product->image, 0) != null)
                        $data['image'] = @$html->find($doom_setting->product->image, 0)->getAttribute($doom_setting->product->image_attribute);
                } else {        //  Neu lay link anh o img src
                    if ($html->find($doom_setting->product->image, 0) != null)
                        $data['image'] = @$html->find($doom_setting->product->image, 0)->getAttribute('src');
                }
                if (isset($doom_setting->product->image_domain)) {
                    isset($data['image']) ? $data['image'] = $website->name . $data['image'] : '';
                }
                /*if (!isset($data['image']) || $this->checkUrl404($data['image'])) {
                    $this->writeLog([
                        'type' => 0,
                        'action' => 'Sản phẩm mất ảnh',
                        'website_id' => $website_id,
                        'name' => 'Sản phẩm mất ảnh ' . $product_link,
                        'msg' => 'Sản phẩm mất ảnh ' . $product_link,
                        'link' => $product_link
                    ]);
                    return false;
                }*/
                /*$data['image_extra'] = '';
                foreach ($html->find($doom_setting->product->image_extra) as $image) {
                    if (isset($doom_setting->product->image_attribute) && $doom_setting->product->image_attribute != '' && $doom_setting->product->image_attribute != 'src') {
                        $img = @$image->getAttribute($doom_setting->product->image_attribute);
                    } else {
                        $img = @$image->getAttribute('src');
                    }
                    if (isset($doom_setting->product->image_domain)) {
                        $data['image_extra'] .= $website->name . $img . '|';
                    } else {
                        $data['image_extra'] .= $img . '|';
                    }
                }
                $data['image_extra'] = substr($data['image_extra'], 0, -1);*/

                $data['content'] = '';
                foreach (explode('|', $doom_setting->product->content) as $content_target) {
                    foreach (@$html->find($content_target) as $content_html) {
                        $data['content'] .= $content_html->innertext;
                    }
                }
                $data['content'] = trim($data['content']);
                $data['intro'] = @$html->find($doom_setting->product->intro, 0)->innertext;
                $data['intro'] = trim($data['intro']);

                /*if ($doom_setting->product->manufacturer !== null) $data['manufacturer'] = @$html->find($doom_setting->product->manufacturer, 0)->innertext;
                if ($doom_setting->product->tags !== null) {
                    $data['tags'] = '';
                    foreach ($html->find($doom_setting->product->tags) as $tag) {
                        $data['tags'] .= $tag->getAttribute('src');
                    }
                }*/
            } catch (\Exception $ex) {
                $this->writeLog([
                    'type' => 1,
                    'action' => $ex->getMessage(),
                    'website_id' => $website->id,
                    'product_name' => @$data['name'],
//                        'product_code' => @$product_data['code'],
                    'link' => @$data['link'],
                    'product_id' => 0
                ]);
                return [
                    'status' => false,
                    'msg' => $ex->getMessage()
                ];
            }
        }
        return $data;
    }

    public function updateProducts($website, $product)
    {
        $category = Category::where('website_id', $website->id)->where('insert_to_category', $product->category_id)->first();
        $doom_setting = json_decode($website->doom);

        $result = $this->crawlProductInLink($website, $category, $doom_setting, $product->link, false);
        if ($result['status']) {
            return $this->shortUpdateProduct($website, $product, $result['data']);
        } else {
            return $result;
        }
    }
}
