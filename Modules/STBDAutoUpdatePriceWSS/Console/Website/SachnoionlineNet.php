<?php
namespace Modules\STBDAutoUpdatePriceWSS\Console\Website;

use App\Models\Author;
use App\Models\DoomLog;
use Mail;

class SachnoionlineNet extends Base {

    /*
     * Lay thong tin cua 1 film
     *
     * $data['group_link_video'] : so tap film
     * */
    public function getDataItem($website, $doom_setting, $product_link, $product = false) {
        try {
            $html = new \Htmldom($product_link);

            if (!$product) {
                $data['name'] = trim(@$html->find($doom_setting->product->name, 0)->innertext);
                $data['slug'] = $this->renderSlug(false, $data['name']);

                $data['code'] = trim(@$html->find($doom_setting->product->code, 0)->innertext);
                if ($doom_setting->product->code_remove !== null) $data['code'] = preg_replace($doom_setting->product->code_remove, '', $data['code']);
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

                if (!isset($data['image']) || $this->checkUrl404($data['image'])) {
                    $this->writeLog([
                        'type' => 0,
                        'action' => 'Sản phẩm mất ảnh',
                        'website_id' => $website->id,
                        'name' => 'Sản phẩm mất ảnh ' . $product_link,
                        'msg' => 'Sản phẩm mất ảnh ' . $product_link,
                        'link' => $product_link
                    ]);
                    return false;
                }

                try {
                    $data_file = file_get_contents($data['image']);
                    file_put_contents(base_path() . '/public/filemanager/userfiles/img/sno/' . $data['slug'] . '.jpg', $data_file);
                    $data['image'] = 'https://files.khosach.net/img/sno/' . $data['slug'] . '.jpg';
                } catch (\Exception $ex) {}


                $data['image_extra'] = '';
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
                $data['image_extra'] = substr($data['image_extra'], 0, -1);

                $data['content'] = '';
                foreach (explode('|', $doom_setting->product->content) as $content_target) {
                    foreach (@$html->find($content_target) as $content_html) {
                        $data['content'] .= $content_html->innertext;
                    }
                }
                $data['content'] = trim($data['content']);
                $data['intro'] = @$html->find($doom_setting->product->intro, 0)->innertext;
                $data['intro'] = trim($data['intro']);

                $kind_name = @$html->find('#detail .detailInfo td', 2)->innertext;
                $data['kind'] = $kind_name;

                $reader_name = @$html->find('#detail .detailInfo td', 6)->innertext;
                $data['reader'] = $reader_name;

                if ($doom_setting->product->manufacturer !== null) $data['manufacturer'] = @$html->find($doom_setting->product->manufacturer, 0)->innertext;
                if ($doom_setting->product->tags !== null) {
                    $data['tags'] = '';
                    foreach ($html->find($doom_setting->product->tags) as $tag) {
                        $data['tags'] .= $tag->getAttribute('src');
                    }
                }

                $author_name = @$html->find('#detail .detailInfo td', 4)->innertext;
                if ($author_name != '') {
                    $author_db = Author::where('name', $author_name)->first();
                    if (!is_object($author_db)) {
                        $author_db = new Author();
                        $author_db->name = $author_name;
                        $author_db->save();
                    }
                    $data['author_id'] = $author_db->id;
                }

                $urlItemReq = $product_link;
                $Detail = $html->find("div[class=detailInfo] tbody tr");
                $idOfItem = explode("/",$urlItemReq); $idOfItem = $idOfItem[4];
                try {
                    $files = file_get_contents($website->name . "book/data?id=" . $idOfItem);
                    preg_match_all('/(title: ")(.+)("),/', $files, $listOfTitle);
                    preg_match_all('/(mp3: ")(.+)(")/', $files, $listOfMp3);
                    $listFile = array();
                    for ($k = 0; $k <= count($listOfTitle[2]) - 1; $k++) {
                        $local_url = "";
                        if (isset($doom_setting->product->save_audio) && $doom_setting->product->save_audio == 1) {
                            if (!file_exists("file/" . $this->stripUnicode($Detail[0]->plaintext))) {
                                mkdir('files/' . $this->stripUnicode($Detail[0]->plaintext), 0777, TRUE);
                            }
                            $localDir = "files/" . $this->stripUnicode($Detail[0]->plaintext) . "/" . $this->stripUnicode($listOfTitle[2][$k]) . ".mp3";

                            $downloadState = copy($listOfMp3[2][$k], $localDir);
                            if ($downloadState == TRUE) $local_url = $localDir;
                        }
                        $tmpFile = array(
                            "title" => $listOfTitle[2][$k],
                            "default_url" => $listOfMp3[2][$k],
                            "local_url" => $local_url
                        );
                        array_push($listFile, $tmpFile);
                    }
                    if (!empty($listFile)) {
                        $data['audio'] = json_encode($listFile);
                    }
                } catch (\Exception $ex) {

                }
            }

            $data['website_id'] = $website->id;
            $data['link'] = $product_link;
            $data['type'] = '|2|';
            return $data;
        } catch (\Exception $ex) {
            $this->writeLog([
                'type' => 1,
                'action' => $ex->getMessage(),
                'website_id' => 0,
                'product_name' => '',
//                        'product_code' => @$product_data['code'],
                'link' => '',
                'product_id' => 0
            ]);
            return [
                'status' => false,
                'msg' => $ex->getMessage()
            ];
        }
    }
}
