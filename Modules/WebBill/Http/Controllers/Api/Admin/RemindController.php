<?php

namespace Modules\WebBill\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use Illuminate\Http\Request;
use Modules\WebBill\Helpers\WebBillHelper;
use Modules\WebBill\Models\Remind;
use Validator;

class RemindController extends Controller
{

    protected $module = [
        'code' => 'remind',
        'table_name' => 'reminds',
        'label' => 'comment',
        'modal' => 'Modules\WebBill\Models\Remind',
    ];

    protected $filter = [
        'conntent' => [
            'query_type' => 'like'
        ],
        'booking_id' => [
            'query_type' => '='
        ],
    ];


    public function index(Request $request)
    {

        try {

            //  Filter
            $where = $this->filterSimple($request);
            $listItem = Remind::whereRaw($where)->where('status', 1)->where(function ($query) {
                $query->orWhere('hours', 'like', '%|'.date('H').'|%');
                $query->orWhereNull('hours');
                $query->orWhere('hours', '||');
            })->where(function ($query) {
                $query->orWhere('day_l', 'like', '%|'.date("l").'|%');
                $query->orWhereNull('day_l');
                $query->orWhere('day_l', '||');
            })->where(function ($query) {
                $query->orWhere('day', 'like', '%|'.date("d").'|%');
                $query->orWhereNull('day');
                $query->orWhere('day', '||');
            });


            $listItem = $this->sort($request, $listItem);

            $limit = $request->has('limit') ? $request->limit : 20;
            $listItem = $listItem->paginate($limit)->appends($request->all());
            foreach ($listItem as $item) {
                $item->reminded = Admin::whereIn('id', explode('|', $item->reminded))->pluck('email', 'id')->toArray();
            }

            return response()->json([
                'status' => true,
                'msg' => '',
                'errors' => (object)[],
                'data' => $listItem,
                'code' => 201
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lỗi',
                'errors' => [
                    'exception' => [
                        $ex->getMessage()
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }

    public function convertDataComment($comment, $count_reply = true) {
        foreach (explode('|', $comment->image_present) as $img) {
            $comment->image = asset('public/filemanager/userfiles/' . $img);
            if ($img != '') {
                $image_present[] = asset('public/filemanager/userfiles/' . $img);
            }
        }
////        dd($image_present);
        $comment->image_present = @$image_present;


        $comment->user = [
            'id' => $comment->admin_id,
            'name' => $comment->admin_name,
            'image' => asset('public/filemanager/userfiles/' . $comment->admin_image),
            'role_name' =>  CommonHelper::getRoleName($comment->admin_id, 'name'),
            'role_display_name' =>  CommonHelper::getRoleName($comment->admin_id, 'display_name'),
        ];
        if($count_reply) $comment->count_reply = Comment::where('reply', $comment->id)->count();
        unset($comment->admin_id);
        unset($comment->admin_name);
        unset($comment->admin_image);

        return $comment;
    }

    public function show($id)
    {
        try {

            $item = Comment::leftJoin('admin', 'admin.id', '=', 'comment_logs.admin_id')
                ->selectRaw('comment_logs.id, comment_logs.content, comment_logs.booking_id, comment_logs.admin_id, comment_logs.admin_id, comment_logs.reply, comment_logs.created_at, comment_logs.image, comment_logs.image_present,admin.id as admin_id, admin.name as admin_name, admin.image as admin_image')
                ->where('comment_logs.id', $id)->first();

            if (!is_object($item)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Lỗi',
                    'errors' => [
                        'exception' => [
                            'Không tìm thấy bản ghi'
                        ]
                    ],
                    'data' => null,
                    'code' => 404
                ]);
            }

            if ($item->image != null) {
                $item->image = asset('public/filemanager/userfiles/' . $item->image);
            }
            foreach (explode('|', $item->image_present) as $img) {
                if ($img != '') {
                    $image_present[] = asset('public/filemanager/userfiles/' . $img);
                }
            }
            $item->image_present = @$image_present;
            $item->user = [
                'id' => $item->admin_id,
                'name' => $item->admin_name,
                'image' => asset('public/filemanager/userfiles/' . $item->admin_image),
                'role_name' =>  CommonHelper::getRoleName($item->admin_id, 'name'),
                'role_display_name' =>  CommonHelper::getRoleName($item->admin_id, 'display_name'),
            ];
            $item->count_reply = Comment::where('reply', $item->id)->count();



            unset($item->admin_id);
            unset($item->admin_name);
            unset($item->admin_image);
            unset($item->reply);
            return response()->json([
                'status' => true,
                'msg' => '',
                'errors' => (object)[],
                'data' => $item,
                'code' => 201
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lỗi',
                'errors' => [
                    'exception' => [
                        $ex->getMessage()
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'content' => 'required'
        ], [
            'content.required' => 'Bắt buộc phải nhập content',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'msg' => 'Validate errors',
                'errors' => $validator->errors(),
                'data' => null,
                'code' => 422
            ]);
        } else {
            $data = $request->all();
            //  Tùy chỉnh dữ liệu insert
            $data['admin_id'] = \Auth::guard('admin_api')->id();
            if ($request->has('image')) {
                if (is_array($request->file('image'))) {
                    foreach ($request->file('image') as $image) {
                        $data['image'] = $data['image_present'][] = CommonHelper::saveFile($image, 'comment');
                    }
                } else {
                    $data['image'] = $data['image_present'][] = CommonHelper::saveFile($request->file('image'), 'comment');
                }
                $data['image_present'] = implode('|', $data['image_present']);
            }

            $item = Comment::create($data);
            WebBillHelper::pushNotiFication($item->booking, 'Có bình luận mới', [\Auth::guard('admin_api')->id()]);

            return $this->show($item->id);
        }
    }


    public function update(Request $request, $id)
    {

        $item = Comment::find($id);
        if (!is_object($item)) {
            return response()->json([
                'status' => false,
                'msg' => 'Validate errors',
                'errors' => [
                    'exception' => [
                        'Không tìm thấy bản ghi'
                    ]
                ],
                'data' => null,
                'code' => 404
            ]);
        }

        $data = $request->except('api_token');
        //  Tùy chỉnh dữ liệu insert

        if ($request->has('image')) {
            if (is_array($request->file('image'))) {
                foreach ($request->file('image') as $image) {
                    $data['image'] = $data['image_present'][] = CommonHelper::saveFile($image, 'comment');
                }
            } else {
                $data['image'] = $data['image_present'][] = CommonHelper::saveFile($request->file('image'), 'comment');
            }
            $data['image_present'] = implode('|', $data['image_present']);
        }


        foreach ($data as $k => $v) {
            $item->{$k} = $v;
        }
        $item->save();

        return $this->show($item->id);
    }


    public function delete($id)
    {
        if (Comment::where('id', $id)->delete()) {
            return response()->json([
                'status' => true,
                'msg' => 'Xóa thành công',
                'errors' => (object)[],
                'data' => null,
                'code' => 404
            ], 200);
        } else
            return response()->json([
                'status' => false,
                'msg' => 'Không tồn tại bản ghi',
                'errors' => (object)[],
                'data' => null,
                'code' => 404
            ], 200);
    }


    public function filterSimple($request)
    {
        $where = '1=1 ';
        if (!is_null($request->id)) {
            $where .= " AND " . 'id' . " = " . $request->id;
        }
        #
        foreach ($this->filter as $filter_name => $filter_option) {
            if (!is_null($request->get($filter_name))) {
                if ($filter_option['query_type'] == 'like') {
                    $where .= " AND " . $this->module['table_name'] . "." . $filter_name . " LIKE '%" . $request->get($filter_name) . "%'";
                } elseif ($filter_option['query_type'] == 'from_to_date') {
                    if (!is_null($request->get('from_date')) || $request->get('from_date') != '') {
                        $where .= " AND " . $this->module['table_name'] . "." . $filter_name . " >= '" . date('Y-m-d 00:00:00', strtotime($request->get('from_date'))) . "'";
                    }
                    if (!is_null($request->get('to_date')) || $request->get('to_date') != '') {
                        $where .= " AND " . $this->module['table_name'] . "." . $filter_name . " <= '" . date('Y-m-d 23:59:59', strtotime($request->get('to_date'))) . "'";
                    }
                } elseif ($filter_option['query_type'] == '=') {
                    $where .= " AND " . $this->module['table_name'] . "." . $filter_name . " = '" . $request->get($filter_name) . "'";
                }
            }
        }
        return $where;
    }

    public function sort($request, $model)
    {
        if ($request->sorts != null) {
            foreach ($request->sorts as $sort) {
                if ($sort != null) {
                    $sort_data = explode('|', $sort);
                    $model = $model->orderBy($sort_data[0], $sort_data[1]);
                }
            }
        } else {
            $model = $model->orderBy('id', 'desc');
        }
        return $model;
    }
}
