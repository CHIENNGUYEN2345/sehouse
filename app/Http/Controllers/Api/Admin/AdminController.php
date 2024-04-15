<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\RoleAdmin;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Mail;
use App\Mail\MailServer;

class AdminController extends Controller
{

    public function forgotPassword(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required'
            ], [
                'email.required' => 'Bắt buộc phải nhập email'
            ]);
            $admin = Admin::where('email', $request->email)->first();

            if (!is_object($admin)) {
                return response()->json([
                    'status' => true,
                    'msg' => 'Thất bại',
                    'errors' => 'Email không tồn tại',
                    'data' => $admin->change_password,
                    'code' => 201
                ]);
            }

            $admin->change_password = $admin->id . '_' . time();
            $admin->save();
            $data['link'] = \URL::to('user/restore_password') . '?change_password=' . $admin->change_password;
            Mail::send('frontend.childs.user.mail', array('content' => $data['link']), function ($message) use ($admin) {
                $message->from(env('MAIL_email'));
                $message->to($admin->email, $admin->name)->subject('Recovery Password');
            });

            return response()->json([
                'status' => true,
                'msg' => 'Đã gửi yêu cầu đổi mật khẩu',
                'errors' => (object)[],
                'data' => $admin,
                'code' => 201
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Thất bại',
                'errors' => [
                    'exception' => [
                        'Tài khoản không tồn tại'
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }

    public function restorePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'password' => 'required',
            ], [
                'password.required' => 'Bắt buộc phải nhập mật khẩu',
            ]);
            $change_password = $request->change_password;
            $admin = Admin::where('change_password', $change_password)->first();
            if (!is_object($admin)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Không tìm thấy bản ghi',
                    'errors' => [
                        'exception' => [
                            'Không tìm thấy bản ghi'
                        ]
                    ],
                    'data' => null,
                    'code' => 404
                ]);
            }
            $admin->password = bcrypt($request->password);
            $admin->change_password = '';
            $admin->save();
            \Auth::login($admin);

            return response()->json([
                'status' => true,
                'msg' => 'Thành công',
                'errors' => $validator->errors(),
                'data' => $admin,
                'code' => 201
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lấy dữ liệu không thành công',
                'errors' => [
                    'exception' => [
                        'Đổi mật khẩu thất bại'
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }


    public function forgotPasswordCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ], [
                'email.required' => 'Bắt buộc phải nhập email!'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Thất bại',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'code' => 401
                ]);
            } else {
                $admin = Admin::where('email', $request->email)->first();

                if (!is_object($admin)) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Thất bại',
                        'errors' => 'Email không tồn tại',
                        'data' => null,
                        'code' => 401
                    ]);
                }

                $admin->code_change_password = mt_rand(10000, 99999);
                $admin->save();

                $data['code'] = $admin->code_change_password;

                Mail::send(config('core.admin_theme') . '.emails.forgot_password_code', ['data' => $data], function ($message) use ($admin) {
                    $message->from(env('MAIL_USERNAME'));
                    $message->to($admin->email, $admin->name)->subject('Recovery Password');
                });
                return response()->json([
                    'status' => true,
                    'msg' => 'Đã gửi yêu cầu đổi mật khẩu',
                    'errors' => (object)[],
                    'data' => $admin,
                    'code' => 201
                ]);
            }

        } catch (\Exception $ex) {

            return response()->json([
                'status' => false,
                'msg' => 'Thất bại',
                'errors' => $ex->getMessage(),
                'data' => null,
                'code' => 401

            ]);
        }
    }

    public function restorePasswordByCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code_change_password' => 'required',
            ], [
                'code_change_password.required' => 'Bắt buộc phải nhập mã',
            ]);

            $admin = Admin::where('email', $request->email)->first();
            if (!is_object($admin)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Bạn nhập sai email',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            }
            if ($admin->status == 0) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Tài khoản của bạn chưa được kich hoạt',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            } elseif ($admin->status == -1) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Tài khoản của bạn đã bị khóa',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            }
            if ($admin->code_change_password != $request->code_change_password) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Sai mã kich hoạt',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            }

            $admin->code_change_password = null;
            $admin->save();

            return response()->json([
                'status' => true,
                'msg' => 'Thành công',
                'errors' => (object)[],
                'data' => [
                    'id' => $admin->id,
                    'email' => $admin->email,
                    'name' => $admin->name,
                    'tel' => $admin->tel,
                    'address' => $admin->address,
                    'image' => asset('public/filemanager/userfiles/' . $admin->image),
                    'gender' => $admin->gender,
                    'birthday' => $admin->birthday,
                    'api_token' => $admin->api_token,
                ],
                'code' => 201
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lấy dữ liệu không thành công',
                'errors' => [
                    'exception' => [
                        'Đổi mật khẩu thất bại'
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }

    public function postProfile(Request $request)
    {
        try {
            $admin = Auth::guard('api')->user();

            $data = $request->except('api_token');

            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }
            if ($request->has('image')) {
                $data['image'] = CommonHelper::saveFile($request->file('image'), 'admin');
            }

            foreach ($data as $k => $v) {
                if (!in_array($k, ['status', 'api_token'])) {
                    $admin->{$k} = $v;
                }
            }

            $admin->save();

            if ($admin->image !== null) {
                $admin->image = asset('public/filemanager/userfiles/' . $admin->image);
            }
            return response()->json([
                'status' => true,
                'msg' => 'Thành công',
                'errors' => (object)[],
                'data' => $admin,
                'code' => 201
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => $ex->getMessage(),
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

    public function getProfile()
    {
        try {
            return response()->json([
                'status' => true,
                'msg' => 'Thành công',
                'errors' => (object)[],
                'data' => [
                    'id' => Auth::guard('api')->user()->id,
                    'email' => Auth::guard('api')->user()->email,
                    'name' => Auth::guard('api')->user()->name,
                    'tel' => Auth::guard('api')->user()->tel,
                    'address' => Auth::guard('api')->user()->address,
                    'image' => asset('public/filemanager/userfiles/' . Auth::guard('api')->user()->image),
                    'gender' => Auth::guard('api')->user()->gender,
                    'birthday' => Auth::guard('api')->user()->birthday,
                    'api_token' => Auth::guard('api')->user()->api_token,
                    'role' => CommonHelper::getRoleName(Auth::guard('api')->user()->id),


                ],
                'code' => 201
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lấy dữ liệu thất bại',
                'errors' => [
                    'exception' => [
                        'Người dùng không tồn tại'
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }

    }

    public function getProfileAdmin($id)
    {
        try {
            $admin = Admin::find($id);
            if (!is_object($admin)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Không tìm thấy bản ghi',
                    'errors' => [
                        'exception' => [
                            'Không tìm thấy bản ghi'
                        ]
                    ],
                    'data' => null,
                    'code' => 404
                ]);
            }

            return response()->json([
                'status' => true,
                'msg' => 'Thành công',
                'errors' => (object)[],
                'data' => [
                    'id' => $admin->id,
                    'email' => $admin->email,
                    'name' => $admin->name,
                    'tel' => $admin->tel,
                    'address' => $admin->address,
                    'image' => asset('public/filemanager/userfiles/' . $admin->image),
                    'gender' => $admin->gender,
                    'birthday' => $admin->birthday,
                    'role' => CommonHelper::getRoleName($admin->id, 'name')

                ],
                'code' => 201
            ]);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Lấy dữ liệu thất bại',
                'errors' => [
                    'exception' => [
                        'Người dùng không tồn tại'
                    ]
                ],
                'data' => null,
                'code' => 401
            ]);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|unique:admin|email',
                'password' => 'required|min:4',

            ], [
                    'email.required' => 'Bắt buộc phải nhập email',
                    'email.unique' => 'Địa chỉ email đã tồn tại',
                    'password.required' => 'Bắt buộc phải nhập mật khẩu',
                    'password.min' => 'Mật khẩu phải trên 4 ký tự',

                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Thất bại',
                    'errors' => $validator->errors(),
                    'data' => null,
                    'code' => 401
                ]);
            } else {
                $data = $request->except('api_token');
                $data['password'] = bcrypt($request->password);
                $data['api_token'] = str_random(60) . time();
                if ($request->has('image')) {
                    $data['image'] = CommonHelper::saveFile($request->file('image'), 'admin');
                }
                $admin = new Admin();
                foreach ($data as $k => $v) {
                    $admin->{$k} = $v;
                }
                $admin->save();

                //  Set quyền mặc định khi mới đăng ký tài khoản
                $role_default_id = @Setting::select(['value'])->where('name', 'role_default_id')->first()->value;
                if ($role_default_id != null) {
                    RoleAdmin::insert([
                        'admin_id' => $admin->id,
                        'role_id' => $role_default_id,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'msg' => 'Thành công',
                    'errors' => (object)[],
                    'data' => [
                        'id' => $admin->id,
                        'email' => $admin->email,
                        'name' => $admin->name,
                        'tel' => $admin->tel,
                        'address' => $admin->address,
                        'image' => (empty($admin->image)) ? '' : asset('public/filemanager/userfiles/' . $admin->image),
                        'gender' => $admin->gender,
                        'birthday' => $admin->birthday,
                        'api_token' => $admin->api_token,
                    ],
                    'code' => 201

                ]);


            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Thất bại',
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

    public function login(Request $request)
    {
        try {
            $admin = Admin::where('email', $request['email'])->first();
            if (!is_object($admin)) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Bạn nhập sai email',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            }

            if ($admin->status == 0) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Tài khoản của bạn chưa được kich hoạt',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            } elseif ($admin->status == -1) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Tài khoản của bạn đã bị khóa',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            }

            if (\Auth::guard('admin')->attempt(['email' => trim($request['email']), 'password' => trim($request['password'])])) {
//                    $admin->generateToken();

                return response()->json([
                    'status' => true,
                    'msg' => 'Thành công',
                    'errors' => (object)[],
                    'data' => [
                        'id' => $admin->id,
                        'email' => $admin->email,
                        'name' => $admin->name,
                        'tel' => $admin->tel,
                        'address' => $admin->address,
                        'image' => asset('public/filemanager/userfiles/' . $admin->image),
                        'gender' => $admin->gender,
                        'birthday' => $admin->birthday,
                        'api_token' => $admin->api_token,
                        'role' => CommonHelper::getRoleName($admin->id, 'name'),
                        'role_id' => CommonHelper::getRoleName($admin->id, 'id'),


                    ],
                    'code' => 201
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'msg' => 'Bạn nhập sai email hoặc mật khẩu',
                    'errors' => [
                    ],
                    'data' => null,
                    'code' => 401
                ]);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Thất bại',
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

    public function logout(Request $request)
    {
        $admin = Auth::guard('api')->user();

        if ($admin) {
            $admin->api_token = null;
            $admin->save();
        }

        return response()->json([
            'status' => true,
            'msg' => 'Đăng xuất thành công',
            'errors' => (object)[],
            'data' => null,
            'code' => 201
        ]);
    }

    public function loginSocial($privider, Request $request)
    {
        try {
            $access_token = $request->get('access_token', '');
            if ($privider == 'facebook') {
                $user_fb = json_decode(file_get_contents('https://graph.facebook.com/me?access_token=' . $access_token));

                $admin = Admin::where('fb_id', $user_fb->id)->first();
                if (!is_object($admin)) {
                    $file_name_insert = $user_fb->id . '.jpg';
                    $v = file_get_contents('http://graph.facebook.com/' . $user_fb->id . '/picture?type=square');
                    file_put_contents(base_path() . '/public/filemanager/userfiles/user/' . $file_name_insert, $v);
                    $admin = Admin::create([
                        'name' => $user_fb->name,
                        'api_token' => str_random(60) . time(),
                        'email' => $user_fb->id . '@gmail.com',
                        'image' => 'user/' . $file_name_insert
                    ]);
                }
            } elseif ($privider == 'google') {
                $user_fb = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $access_token));

                $admin = Admin::where('email', $user_fb->email)->first();
                if (!is_object($admin)) {
                    $file_name_insert = str_replace('@gmail.com', '', $user_fb->email) . '.jpg';
                    $v = file_get_contents($user_fb->picture);
                    file_put_contents(base_path() . '/public/filemanager/userfiles/user/' . $file_name_insert, $v);
                    $admin = Admin::create([
                        'name' => $user_fb->name,
                        'api_token' => str_random(60) . time(),
                        'email' => $user_fb->email,
                        'image' => 'user/' . $file_name_insert
                    ]);
                }
            }

            if (isset($admin)) {
                //  Set quyền mặc định khi mới đăng ký tài khoản

                $role_default_id = @Setting::select(['value'])->where('name', 'role_default_id')->first()->value;
                $role_social_id = RoleAdmin::where('admin_id',$admin->id )->where('status',1)->first();
                if (!is_object($role_social_id)) {
                    RoleAdmin::create([
                        'admin_id' => $admin->id,
                        'role_id' => $role_default_id,
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'msg' => 'Thành công',
                    'errors' => (object)[],
                    'data' => [
                        'id' => $admin->id,
                        'email' => $admin->email,
                        'name' => $admin->name,
                        'tel' => $admin->tel,
                        'address' => $admin->address,
                        'image' => asset('public/filemanager/userfiles/' . $admin->image),
                        'gender' => $admin->gender,
                        'birthday' => $admin->birthday,
                        'api_token' => $admin->api_token,
                        'role' => CommonHelper::getRoleName($admin->id, 'name'),
                        'role_id' => CommonHelper::getRoleName($admin->id, 'id'),
                    ],
                    'code' => 201
                ]);
            }
            return response()->json([
                'status' => false,
                'msg' => 'Lỗi',
                'errors' => 'Không hiểu phương thức đăng nhập',
                'data' => null,
                'code' => 401
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'msg' => 'Thất bại',
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

    public function index(Request $request)
    {
        try {
            //  Filter
            $where = $this->filterSimple($request);
            $listItem = Admin::select(['id', 'email', 'name', 'tel', 'address', 'image', 'gender', 'birthday'])
                ->whereRaw($where);

            //  Sort
            $listItem = $this->sort($request, $listItem);
            $limit = $request->get('limit', 20);
            $listItem = $listItem->paginate($limit)->appends($request->all());

            foreach ($listItem as $k => $item) {
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

    public function show($id)
    {
        try {
            //  Check permission
            if (!CommonHelper::has_permission(\Auth::guard('api')->id(), 'land_view')) {
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

            $item = Admin::leftJoin('seasons', 'lands.id', '=', 'seasons.land_id')
                ->leftJoin('districts', 'districts.id', '=', 'lands.district_id')
                ->leftJoin('provinces', 'provinces.id', '=', 'lands.province_id')
                ->leftJoin('wards', 'wards.id', '=', 'lands.ward_id')
                ->leftJoin('admin', 'admin.id', '=', 'lands.admin_id')
                ->selectRaw('lands.*, seasons.id as season_id, seasons.tree_name as season_tree_name, 
                seasons.start_date as season_start_date, seasons.expected_date as season_expected_date, seasons.quantity_expected as seasons_quantity_expected,
                districts.name as district_name, provinces.name as province_name, wards.name as ward_name,
                admin.id as admin_id, admin.name as admin_name')->where('lands.id', $id)->first();

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

            foreach (explode('|', $item->land_parameters_image) as $img) {
                if ($img != '') {
                    $land_parameters_image[] = asset('public/filemanager/userfiles/' . $img);
                }
            }
            $item->land_parameters_image = @$land_parameters_image;

            foreach (explode('|', $item->water_parameters_image) as $img) {
                if ($img != '') {
                    $water_parameters_image[] = asset('public/filemanager/userfiles/' . $img);
                }
            }
            $item->water_parameters_image = @$water_parameters_image;

            $item->season = [
                'id' => $item->season_id,
                'tree_name' => $item->season_tree_name,
                'start_date' => $item->season_start_date,
                'expected_date' => $item->season_expected_date,
                'quantity_expected' => $item->season_quantity_expected,
            ];
            unset($item->season_id);
            unset($item->season_tree_name);
            unset($item->season_start_date);
            unset($item->season_expected_date);
            unset($item->season_quantity_expected);

            $item->admin = [
                'id' => $item->admin_id,
                'name' => $item->admin_name,
            ];
            unset($item->admin_id);
            unset($item->admin_name);

            $item->type_owneds = @$this->type_owneds[$item->type_owneds];

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

            $item = Admin::create($data);

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
            $item = Admin::find($id);
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
        if (Admin::where('id', $id)->delete()) {
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
            $model = $model->orderBy('id', 'desc');
        }
        return $model;
    }
}

