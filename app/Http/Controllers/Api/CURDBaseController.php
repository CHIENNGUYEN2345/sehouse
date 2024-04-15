<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Session;
use Validator;

class CURDBaseController extends Controller
{
    protected $user_id = 0;

    protected $model;
    protected $whereRaw = false;
    protected $orderByRaw = 'id desc';
    protected $limit_default = 15;
    protected $permission_publish = '_edit';
    protected $module = [];
//    protected $appendScriptView = false;

    protected $filter = [];
    protected $validate = [
        'request' => [
        ],
        'label' => [
        ]
    ];
    protected $validate_add = [
        'request' => [
        ],
        'label' => [
        ]
    ];

    public function __construct()
    {
        $this->model = new $this->module['modal'];
    }


    public function getIndex(Request $request)
    {
        try {
            //  Check permission
            if (in_array('view', $this->permissions) && !CommonHelper::has_permission(Auth::guard('api')->id(), $this->module['code'] . '_view')) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Lỗi',
                    'errors' => [
                        'exception' => [
                            'Không đủ quyền'
                        ]
                    ],
                    'data' => null,
                    'code' => 403
                ]);
            }

            //  Filter
            $where = $this->filterSimple($request);
            $listItem = $this->model->whereRaw($where);
            if ($this->whereRaw) {
                $listItem = $listItem->whereRaw($this->whereRaw);
            }
            $listItem = $this->appendWhere($listItem, $request);

            $data = [];

            //  Count record
//            $data['record'] = $listItem->count();

            //  Sort
            $listItem = $this->sort($request, $listItem);
            $data = $this->appendDataList($data, $request, $listItem);
            $listItem = $listItem->paginate($this->limit_default)->appends($request->all());
            $data['listItem'] = $this->dataListTransformation($request, $listItem);

            //  Get data default (param_url, filter, module) for return view$

            return response()->json([
                'status' => true,
                'msg' => '',
                'errors' => (object)[],
                'data' => $data,
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

    public function dataListTransformation($request, $listItem) {
        return $listItem;
    }

    public function appendDataList($data, $request, $listItem)
    {
        return $data;
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
                    $where .= " AND " . $filter_name . " LIKE '%" . $request->get($filter_name) . "%'";
                } elseif ($filter_option['query_type'] == 'from_to_date') {
                    if (!is_null($request->get('from_date')) || $request->get('from_date') != '') {
                        $where .= " AND " . $filter_name . " >= '" . date('Y-m-d 00:00:00', strtotime($request->get('from_date'))) . "'";
                    }
                    if (!is_null($request->get('to_date')) || $request->get('to_date') != '') {
                        $where .= " AND " . $filter_name . " <= '" . date('Y-m-d 23:59:59', strtotime($request->get('to_date'))) . "'";
                    }
                } elseif ($filter_option['query_type'] == '=') {
                    $where .= " AND " . $filter_name . " = '" . $request->get($filter_name) . "'";
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
            $model = $model->orderByRaw($this->orderByRaw);
        }
        return $model;
    }

    /*public function getDataIndexSimple($data, $request)
    {
        $data['param_url'] = $request->all();
        $data['filter'] = $this->filter;
        $data['module'] = $this->module;
        return $data;
    }*/

    public function getAllFormFiled()
    {
        $fields = [];
        foreach ($this->module['form']['tabs'] as $tab) {
            foreach ($tab['td'] as $field) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function appendWhere($query, $request)
    {
        return $query;
    }

    public function getDetail($id) {
        $item = $this->model->find($id);
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
        $item = $this->dataDetailTransformation($item);
        return $this->detailReturn($item);
    }

    public function dataDetailTransformation($item) {
        return $item;
    }

    public function detailReturn($item) {
        return response()->json([
            'status' => true,
            'msg' => '',
            'errors' => (object)[],
            'data' => $item,
            'code' => 201
        ]);
    }

    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), array_merge($this->validate['request'], $this->validate_add['request']));
            $validator->setAttributeNames(array_merge($this->validate['label'], $this->validate_add['label']));
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Validate errors',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'code' => 422
                ]);
            } else {
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                $data = $this->appendData($request, $data, false);
                if (isset($data['error'])) {
                    return $this->returnError($data, $request);
                }
                foreach ($data as $k => $v) {
                    $this->model->$k = $v;
                }
                if ($this->model->save()) {
                    $this->afterAdd($request, $this->model);
                    Cache::flush();
                }
                return $this->addReturn($request);
            }
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

    public function addReturn($request) {
        return response()->json([
            'status' => true,
            'msg' => 'Tạo mới thành công',
            'errors' => (object)[],
            'data' => $this->model,
            'code' => 201
        ]);
    }

    public function returnError($data, $request)
    {
        return response()->json([
            'status' => false,
            'msg' => 'Lỗi',
            'errors' => [
                'exception' => [
                    $data['msg']
                ]
            ],
            'data' => null,
            'code' => 401
        ]);
    }

    public function processingValueInFields($request, $fields, $prefix = '')
    {
        $data = [];
        foreach ($fields as $field) {
            if (!in_array($field['type'], ['inner'])) {
                if (in_array($field['type'], ['checkbox', 'checkbox_check_permission'])) {
                    $data[$field['name']] = $request->has($prefix . $field['name']) ? 1 : 0;
                } elseif (in_array($field['type'], ['file_editor', 'file_editor2'])) {
                    if (strpos(@$request->get($prefix . $field['name']), 'filemanager')) {
                        $data[$field['name']] = @explode('filemanager/userfiles/', urldecode($request->get($prefix . $field['name'])))[1];
                    } else {
                        $data[$field['name']] = urldecode(@$request->get($prefix . $field['name']));
                    }
                } elseif (in_array($field['type'], ['file_editor_extra', 'multiple_image'])) {
                    $str_insert = '';
                    for ($i = 1; $i <= 6; $i++) {
                        $input_name = $prefix . $field['name'] . $i;
                        if ($request->{$input_name} != null && strpos($request->{$input_name}, 'filemanager')) {
                            $str_insert .= (@explode('filemanager/userfiles/', $request->{$input_name})[1] . '|');
                        } elseif ($request->{$input_name} != null) {
                            $str_insert .= $request->{$input_name} . '|';
                        }
                    }
                    $data[$field['name']] = $str_insert != '' ? substr($str_insert, 0, -1) : '';
                } elseif (in_array($field['type'], ['slug'])) {
                    $data[$field['name']] = $this->renderSlug(isset($request->id) ? $request->id : false, $request->{$field['name']});
                } elseif (in_array($field['type'], ['file_image'])) {
                    $data[$field['name']] = CommonHelper::saveFile($request->file($field['name']));
                } elseif (!in_array($field['type'], ['re_password', 'dynamic', 'inner'])) {
                    $data[$field['name']] = $request->get($prefix . $field['name']);
                }

                //  Loai tru cac field bi gioi han quyen
                if (isset($field['check_permission'])) {
                    if (!CommonHelper::has_permission(Auth::guard('api')->id(), $field['check_permission'])) {
                        unset($data[$field['name']]);
                    }
                }
            }
        }
        return $data;
    }

    public function appendData($request, $data, $item = false)
    {
        return $data;
    }

    public function afterAdd($request, $item)
    {
        return true;
    }

    public function update(Request $request)
    {
        try {
            $item = $this->model->find($request->id);
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
                if (!CommonHelper::has_permission(Auth::guard('api')->id(), $this->module['code'] . '_edit')) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Lỗi',
                        'errors' => [
                            'exception' => [
                                'Không đủ quyền'
                            ]
                        ],
                        'data' => null,
                        'code' => 403
                    ]);
                }

            $validator = Validator::make($request->all(), $this->validate['request']);
            $validator->setAttributeNames($this->validate['label']);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Validate errors',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'code' => 422
                ]);
            } else {
                if (!$this->canEdit($item)) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Lỗi',
                        'errors' => [
                            'exception' => [
                                'Không đủ quyền'
                            ]
                        ],
                        'data' => null,
                        'code' => 403
                    ]);
                }
                $data = $this->processingValueInFields($request, $this->getAllFormFiled());
                $data = $this->appendData($request, $data, $item);
                if (isset($data['error'])) {
                    return $this->returnError($data, $request);
                }
                $data = $this->beforeUpdate($request, $item, $data);
                foreach ($data as $k => $v) {
                    $item->$k = $v;
                }
                if ($item->save()) {
                    $this->afterUpdate($request, $item);
                    Cache::flush();
                }

                return $this->updateReturn($request, $item);
            }
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

    public function updateReturn($request, $item) {
        return response()->json([
            'status' => true,
            'msg' => 'Tạo mới thành công',
            'errors' => (object)[],
            'data' => $item,
            'code' => 201
        ]);
    }

    public function beforeUpdate($request, $item, $data)
    {
        return $data;
    }

    public function afterUpdate($request, $item)
    {
        return true;
    }

    public function getPublish(Request $request)
    {
        try {

                if (!CommonHelper::has_permission(Auth::guard('api')->id(), $this->module['code'] . $this->permission_publish)) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Lỗi',
                        'errors' => [
                            'exception' => [
                                'Không đủ quyền'
                            ]
                        ],
                        'data' => null,
                        'code' => 403
                    ]);
                }

            $id = $request->get('id', 0);
            $item = $this->model->find($id);
            if (!is_object($item))
                return response()->json([
                    'status' => false,
                    'msg' => 'Không tìm thấy bản ghi'
                ]);

            if ($item->{$request->column} == 0)
                $item->{$request->column} = 1;
            else
                $item->{$request->column} = 0;

            $item->save();
            $this->afterPublish($request, $item);
            Cache::flush();
            return response()->json([
                'status' => true,
                'msg' => 'Tạo mới thành công',
                'errors' => (object)[],
                'data' => [
                    'published' => $item->{$request->column} == 1 ? true : false
                ],
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

    public function afterPublish($request, $item)
    {
        return true;
    }

    public function delete(Request $request)
    {
        try {

                if (!CommonHelper::has_permission(Auth::guard('api')->id(), $this->module['code'] . '_delete')) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Lỗi',
                        'errors' => [
                            'exception' => [
                                'Không đủ quyền'
                            ]
                        ],
                        'data' => null,
                        'code' => 403
                    ]);
                }

            $item = $this->model->find($request->id);
            $this->beforeDelete($request, $item);
            $item->delete();
            Cache::flush();
            return response()->json([
                'status' => true,
                'msg' => 'Xóa thành công',
                'errors' => (object)[],
                'data' => [],
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

    public function beforeDelete($request, $item)
    {
        return true;
    }

    function renderSlug($id, $name)
    {
        $slug = str_slug($name, '-');
        $item = $this->model->where('slug', '=', $slug);
        if ($id) $item = $item->where('id', '!=', $id);

        if ($item->count() > 0) {
            return $slug . '-' . time();
        }
        return $slug;
    }

    public function canEdit($item)
    {
        return true;
    }

    public function multiDelete(Request $request)
    {
        try {
                if (!CommonHelper::has_permission(Auth::guard('api')->id(), $this->module['code'] . '_delete')) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Lỗi',
                        'errors' => [
                            'exception' => [
                                'Không đủ quyền'
                            ]
                        ],
                        'data' => null,
                        'code' => 403
                    ]);
                }

            $ids = $request->ids;
            if (is_array($ids)) {
                $this->model->whereIn('id', $ids)->delete();
                Cache::flush();
            }
            return response()->json([
                'status' => true,
                'msg' => 'Xóa thành công',
                'errors' => (object)[],
                'data' => [],
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
}
