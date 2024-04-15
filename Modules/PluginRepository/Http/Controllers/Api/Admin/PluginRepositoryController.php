<?php

namespace Modules\PluginRepository\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use Illuminate\Http\Request;
use Modules\A4iLand\Models\Land;
use Modules\PluginRepository\Models\PluginRepository;
use Validator;

class PluginRepositoryController extends Controller
{

    protected $module = [
        'code' => 'plugin_repository',
        'table_name' => 'plugins',
        'label' => 'Plugin Repository',
        'modal' => '\Modules\PluginRepository\Models\PluginRepository',
    ];

    protected $filter = [
        'name' => [
            'label' => 'Tên',
            'query_type' => 'like'
        ],
        'code' => [
            'label' => 'Mã',
            'query_type' => 'like'
        ],
        'author' => [
            'label' => 'Tác giả',
            'query_type' => 'like'
        ],
        'version_required' => [
            'label' => 'Phiên bản',
            'query_type' => 'like'
        ],
        'path' => [
            'label' => 'Đường dẫn',
            'query_type' => 'like'
        ],
        'link_detail' => [
            'label' => 'Đường link',
            'query_type' => 'like'
        ],
        'status' => [
            'label' => 'Trạng thái',
            'query_type' => '=',
        ],
    ];


    public function index(Request $request)
    {
        try {
            //  Filter
            $where = $this->filterSimple($request);
            $listItem = PluginRepository::whereRaw($where);
            if ($request->has('arr_code')) {
                $listItem = $listItem->whereIn('code', explode('|', $request->arr_code));
            }

            //  Sort
            $listItem = $this->sort($request, $listItem);
            $limit = $request->has('limit') ? $request->limit : 50;
            $listItem = $listItem->paginate($limit)->appends($request->all());
            foreach ($listItem as $item) {
                $item->image = asset('public/filemanager/userfiles/' . $item->image);
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

    public function detail(Request $request) {
        try {
            //  Check permission

            $item = PluginRepository::where('code', $request->code)->first();

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
            $item->image = asset('public/filemanager/userfiles/' . $item->image);
            foreach (explode('|', $item->image_extra) as $img) {
                if ($img != '') {
                    $image_extra[] = asset('public/filemanager/userfiles/' . $img);
                }
            }
            $item->image_extra = @$image_extra;
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

    public function show($id)
    {
        try {
            //  Check permission

            $item = PluginRepository::where('plugins.id', $id)->first();

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
            $item->image = asset('public/filemanager/userfiles/' . $item->image);
            foreach (explode('|', $item->image_extra) as $img) {
                if ($img != '') {
                    $image_extra[] = asset('public/filemanager/userfiles/' . $img);
                }
            }
            $item->image_extra = @$image_extra;
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
            'name' => 'required'
        ], [
            'name.required' => 'Bắt buộc phải nhập tên',
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
            $data['admin_id'] = \Auth::guard('api')->id();

            if ($request->has('image')) {
                if (is_array($request->file('image'))) {
                    foreach ($request->file('image') as $image) {
                        $data['image'] = $data['image_extra'][] = CommonHelper::saveFile($image, 'land');
                    }
                } else {
                    $data['image'] = $data['image_extra'][] = CommonHelper::saveFile($request->file('image'), 'land');
                }
                $data['image_extra'] = implode('|', $data['image_extra']);
            }

            if ($request->has('land_parameters_image')) {
                foreach ($request->file('land_parameters_image') as $image) {
                    $data['land_parameters_image'][] = CommonHelper::saveFile($image, 'land');
                }
                $data['land_parameters_image'] = implode('|', $data['land_parameters_image']);
            }

            if ($request->has('water_parameters_image')) {
                foreach ($request->file('water_parameters_image') as $image) {
                    $data['water_parameters_image'][] = CommonHelper::saveFile($image, 'land');
                }
                $data['water_parameters_image'] = implode('|', $data['water_parameters_image']);
            }

            if ($request->has('land_parameters')) {
                $data['land_parameters'] = json_encode($request->land_parameters);
            }
            if ($request->has('water_parameters')) {
                $data['water_parameters'] = json_encode($request->water_parameters);
            }

            $item = land::create($data);

            return $this->show($item->id);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ], [
            'name.required' => 'Bắt buộc phải nhập tên',
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
            $item = land::find($id);
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
                        $data['image'] = $data['image_extra'][] = CommonHelper::saveFile($image, 'land');
                    }
                } else {
                    $data['image'] = $data['image_extra'][] = CommonHelper::saveFile($request->file('image'), 'land');
                }
                $data['image_extra'] = implode('|', $data['image_extra']);
            }

            if ($request->has('land_parameters_image')) {
                foreach ($request->file('land_parameters_image') as $image) {
                    $data['land_parameters_image'][] = CommonHelper::saveFile($image, 'land');
                }
                $data['land_parameters_image'] = implode('|', $data['land_parameters_image']);
            }

            if ($request->has('water_parameters_image')) {
                foreach ($request->file('water_parameters_image') as $image) {
                    $data['water_parameters_image'][] = CommonHelper::saveFile($image, 'land');
                }
                $data['water_parameters_image'] = implode('|', $data['water_parameters_image']);
            }

            if ($request->has('land_parameters')) {
                $data['land_parameters'] = json_encode($request->land_parameters);
            }
            if ($request->has('water_parameters')) {
                $data['water_parameters'] = json_encode($request->water_parameters);
            }

            foreach ($data as $k => $v) {
                $item->{$k} = $v;
            }
            $item->save();

            return $this->show($item->id);
        }
    }

    public function delete($id)
    {
        if (land::where('id', $id)->delete()) {
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
            $where .= " AND " . $this->module['table_name'] .  '.id' . " = " . $request->id;
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
