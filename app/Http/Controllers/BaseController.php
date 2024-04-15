<?php

namespace App\Http\Controllers;

use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Auth;
use Mail;
use Modules\EworkingNotification\Models\Notifications;
use Session;
use Validator;

class BaseController extends Controller
{

    public function createNotification($notifiData = [], $pushToApp = true)
    {
        //  Lưu vào DB
        $notifi = Notifications::create($notifiData);

        //  Bắn thông báo tới app
        if ($pushToApp) {
            $this->pushToApp($notifi);
        }
        //  Bắn thông báo tới các user trên web (pusher)
    }

    public function pushToApp($notifi)
    {

    }


    public function logsFileText($dir_name, $filename, $data)
    {

        // Kiểm tra xem thư mục có tên đã tồn tại chưa
        if (!is_dir($dir_name)) {
            // Tạo thư mục của chúng tôi nếu nó không tồn tại
            mkdir($dir_name, 0777, true);
        }

        if (file_exists($filename)) {
            $message_data = (array)json_decode(file_get_contents($filename));
            $message_data[] = (object)$data;
            reset($message_data);
            $status = file_put_contents($filename, json_encode($message_data));
//            echo file_get_contents($filename);
//                dd("The file $filename exists");
        } else {
            $message_data[] = (object)$data;
            $status = file_put_contents($filename, json_encode($message_data));
//                dd("The file $filename does not exist");
        }
        return response()->json([
            'status' => $status,
            'msg' => ''
        ]);
    }
}
