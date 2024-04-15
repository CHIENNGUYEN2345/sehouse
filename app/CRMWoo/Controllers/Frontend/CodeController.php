<?php
/**
 * Created by PhpStorm.
 * User: hoanghung
 * Date: 16/05/2016
 * Time: 15:19
 */

namespace App\CRMWoo\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CRMWoo\Models\Codes;
use App\CRMWoo\Models\PostTag;
use Session;
use Auth;

class CodeController extends Controller
{
    protected $limit_default = 12;
    protected $orderByRaw = 'order_no desc, price_setup asc, id desc';

    protected $quick_search = [
        'label' => 'ID',
        'fields' => 'id, name, link, source'
    ];

    protected $filter = [];

    protected $whereRaw = false;

    public function landingpage(Request $r)
    {
        //  ldp
        /*$codes = Codes::where('source', 'like', '%ladipage%')->get();
        foreach ($codes as $code) {
            $domain = str_replace('https://', '', $code->link);
            $domain = str_replace('http://', '', $domain);
            $domain = str_replace('/', '', $domain);
            $ldp = Guide::where('domain', $domain)->first();
            if (is_object($ldp)) {
//                dd($ldp, $code);
                $code->multi_cat = '|'.$ldp->career_id.'|';
                $code->save();
            }
        }
        die('2');*/

        //  Chuyển type sang multi_cat cho codes
        /*$codes = Codes::whereNull('multi_cat')->get();
        foreach ($codes as $code) {
            $type = str_replace('|', '', $code->type);
//            dd($codes, $type);
            $cat = Category::where('name', $type)->first();
            if (!is_object($cat)) {
                $cat = new Category();
                $cat->name = $type;
                $cat->slug = str_slug($type, '-');
                $cat->save();
            }
            $code->multi_cat = '|'.$cat->id.'|';
            $code->save();
        }
        die('2');*/

        $this->whereRaw = "source like '%ladipage%'";
        if ($r->status != null) {
            $this->whereRaw .= ' and status = ' . $r->status;
            
        } else {
            $this->whereRaw .= ' and status = 1';
        }
        $data = $this->getDataList($r);

        $pageOption = [
            'type' => 'page',
            'pageName' => 'Giao diện landingpage',
            'parentName' => '',
            'parentUrl' => '/',
        ];
        view()->share('pageOption', $pageOption);

        return view('CRMWoo.frontend.pages.code.list')->with($data);
    }

    public function userLandingpage(Request $r, $admin_id)
    {

        $this->whereRaw = "source like '%ladipage%' and status = 1 and admin_id = " . $admin_id;

        $data = $this->getDataList($r);

        $pageOption = [
            'type' => 'page',
            'pageName' => 'Giao diện landingpage',
            'parentName' => '',
            'parentUrl' => '/',
        ];
        view()->share('pageOption', $pageOption);

        return view('CRMWoo.frontend.pages.code.list')->with($data);
    }

    public function wordpress(Request $r)
    {

        $this->whereRaw = "source like '%wordpress%' and image IS NOT NULL and price_setup = 0";
        $data = $this->getDataListWordpress($r);

        $pageOption = [
            'type' => 'page',
            'pageName' => 'Giao diện landingpage',
            'parentName' => '',
            'parentUrl' => '/',
        ];
        view()->share('pageOption', $pageOption);

        return view('CRMWoo.frontend.pages.code.list')->with($data);
    }

    public function wordpressTrans(Request $r, $lang)
    {

        $this->whereRaw = "source like '%wordpress%' and image IS NOT NULL and price_setup = 0";
        $data = $this->getDataListWordpress($r);
        $data['lang'] = $lang;

        $pageOption = [
            'type' => 'page',
            'pageName' => 'Giao diện landingpage',
            'parentName' => '',
            'parentUrl' => '/',
        ];
        view()->share('pageOption', $pageOption);

        return view('CRMWoo.frontend.pages.code.list_trans')->with($data);
    }

    public function appendWhereWordpress($listItem, $r) {
        // if (@$r->bil_id != null) {
        //     $listItem = $listItem->whereNotNull('bil_id');
        // } else {
        //     $listItem = $listItem->whereNull('bil_id');
        // }
        if (@$r->status != null) {
            $listItem = $listItem->where('status', $r->status);
        } else {
            $listItem = $listItem->where('status', 1);
        }

        if ($r->has('cat_id') && $r->cat_id != '') {
            $listItem->where('multi_cat', 'like', '%|'.$r->cat_id.'|%');
        }
        if ($r->has('tag_id') && $r->tag_id != '') {
            $code_ids = PostTag::where('tag_id', $r->tag_id)->pluck('post_id')->toArray();
            $listItem->whereIn('id', $code_ids);
        }
        if ($r->has('owned') && $r->owned != '') {
            $listItem->where('owned', $r->owned);
        } else {
            $listItem->where('owned', 'not like', '%server mình%');
        }
        return $listItem;
    }

    public function app(Request $r)
    {

        $this->whereRaw = "source like '%react native%' and status = 1 and image IS NOT NULL";
        $data = $this->getDataList($r);
        $data['hoba_iframe'] = false;

        $pageOption = [
            'type' => 'page',
            'pageName' => 'Giao diện landingpage',
            'parentName' => '',
            'parentUrl' => '/',
        ];
        view()->share('pageOption', $pageOption);

        return view('CRMWoo.frontend.pages.code.list_app')->with($data);
    }

    public function appDetail(Request $r, $id) {
        $data['result'] = Codes::find($id);

        $pageOption = [
            'type' => 'page',
            'pageName' => 'Giao diện landingpage',
            'parentName' => '',
            'parentUrl' => '/',
        ];
        view()->share('pageOption', $pageOption);

        return view('CRMWoo.frontend.pages.code.detail_app')->with($data);
    }

    public function appendWhere($listItem, $request) {
        if ($request->has('cat_id') && $request->cat_id != '') {
            $listItem->where('multi_cat', 'like', '%|'.$request->cat_id.'|%');
        }
        if ($request->has('tag_id') && $request->tag_id != '') {
            $code_ids = PostTag::where('tag_id', $request->tag_id)->pluck('post_id')->toArray();
            $listItem->whereIn('id', $code_ids);
        }
        if ($request->has('owned') && $request->owned != '') {
            $listItem->where('owned', $request->owned);
        }

        return $listItem;
    }

    public function quickSearch($listItem, $r) {
        if (@$r->quick_search != '') {
            $listItem = $listItem->where(function ($query) use ($r) {
                foreach (explode(',', $this->quick_search['fields']) as $field) {
                    $query->orWhere(trim($field), 'LIKE', '%' . $r->quick_search . '%');    //  truy vấn các tin thuộc các danh mục con của danh mục hiện tại
                }
            });

        }
        return $listItem;
    }

    public function getDataListWordpress($r) {
        $where = $this->filterSimple($r);
        $listItem = Codes::whereRaw($where);
    
        $listItem = $this->quickSearch($listItem, $r);

        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }

        $listItem = $this->appendWhereWordpress($listItem, $r);

        
        $listItem = $listItem->orderByRaw($this->orderByRaw);

        if ($r->has('limit')) {
            $data['listItem'] = $listItem->paginate($r->limit);
            $data['limit'] = $r->limit;
        } else {
            $data['listItem'] = $listItem->paginate($this->limit_default);
            $data['limit'] = $this->limit_default;
        }
        $data['page'] = $r->get('page', 1);

        $data['param_url'] = $r->all();

        $data['quick_search'] = $this->quick_search;
        $data['filter'] = $this->filter;

        if ($this->whereRaw) {
            $data['record_total'] = Codes::whereRaw($this->whereRaw);
        } else {
            $data['record_total'] = new Codes();
        }
        $data['record_total'] = $this->appendWhere($data['record_total'], $r);
        $data['record_total'] = $data['record_total']->whereRaw($where)->count();
        return $data;
    }

    public function getDataList($r) {
        $where = $this->filterSimple($r);
        $listItem = Codes::whereRaw($where);
    
        $listItem = $this->quickSearch($listItem, $r);

        if ($this->whereRaw) {
            $listItem = $listItem->whereRaw($this->whereRaw);
        }

        $listItem = $this->appendWhere($listItem, $r);

        
        $listItem = $listItem->orderByRaw($this->orderByRaw);

        if ($r->has('limit')) {
            $data['listItem'] = $listItem->paginate($r->limit);
            $data['limit'] = $r->limit;
        } else {
            $data['listItem'] = $listItem->paginate($this->limit_default);
            $data['limit'] = $this->limit_default;
        }
        $data['page'] = $r->get('page', 1);

        $data['param_url'] = $r->all();

        $data['quick_search'] = $this->quick_search;
        $data['filter'] = $this->filter;

        if ($this->whereRaw) {
            $data['record_total'] = Codes::whereRaw($this->whereRaw);
        } else {
            $data['record_total'] = new Codes();
        }
        $data['record_total'] = $this->appendWhere($data['record_total'], $r);
        $data['record_total'] = $data['record_total']->whereRaw($where)->count();
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
//                    dd($request->all());
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

    public function postRegister(Request $request)
    {
        $data = $request->except('_token');

        if ($data['password'] != $data['re_password']) {
            \Session::flash('error', 'Mật khẩu không khớp');
            if($request->has('redirect_back')) {
                return redirect($request->redirect_back);
            }
            return redirect()->back();
        }

        unset($data['re_password']);
        $data['password'] = bcrypt($data['password']);
        if (!isset($data['email'])) {
            $data['email'] = $data['tel'] . '@autogenerated.com';
        }

        $user_db = User::where('email', $data['email'])->orWhere('tel', $data['tel'])->first();
        if (is_object($user_db)) {
            \Session::flash('error', 'Email hoặc số điện thoại đã tồn tại');
            if($request->has('redirect_back')) {
                return redirect($request->redirect_back);
            }
            return redirect()->back();
        }

        $user = User::create($data);
        if ($user) {
            if($request->has('ajax')) {
                return response()->json([
                    'status'    => true
                ]);
            } else {
                \Session::flash('success', 'Tạo tài khoản thành công!');
                Auth::login($user);
                if($request->has('redirect_back')) {
                    return redirect($request->redirect_back);
                }
                return redirect()->back();
            }
        }
        if($request->has('ajax')) {
            return response()->json([
                'status'    => false,
                'msg'       => 'Có lỗi xảy ra! Không tạo được tài khoản. Vui lòng load lại website và thử lại'
            ]);
        } else {
            \Session::flash('error', 'Có lỗi xảy ra! Không tạo được tài khoản. Vui lòng load lại website và thử lại');
            return redirect()->back();
        }
    }

    public function postLogin(Request $request)
    {
        $remember = true;
        $user_db = User::where('email', $request->email_tel)->orWhere('tel', $request->email_tel)->first();
        if (!is_object($user_db)) {
            if($request->has('ajax')) {
                return response()->json([
                    'status'    => false,
                    'msg'       => 'Email hoặc số điện thoại sai!'
                ]);
            } else {
                \Session::flash('error', 'Email hoặc số điện thoại sai!');
                return redirect()->back();
            }
        }

        if (Auth::attempt(['email' => $user_db->email, 'password' => $request->password], $remember)) {
            if($request->has('ajax')) {
                return response()->json([
                    'status'    => true
                ]);
            } else {
                \Session::flash('success', 'Đăng nhập thành công!');
                if($request->has('redirect_back')) {
                    return redirect($request->redirect_back);
                }
                return redirect()->back();
            }
        } else {
            if($request->has('ajax')) {
                return response()->json([
                    'status'    => false,
                    'msg'       => 'Sai mật khẩu'
                ]);
            } else {
                \Session::flash('error', 'Sai mật khẩu!');
                return redirect()->back();
            }
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function wordpressDemo($id) {
        $data['code'] = Codes::find($id);
        return view('CRMWoo.frontend.pages.code.detail_wp')->with($data);
    }

    /*public function postLogin(Request $request)
    {
        $rules = array(
            'email' => 'required|email|max:200',
            'password' => 'required'
        );

        $fieldNames = array(
            'email' => 'Email',
            'password' => 'Password',
        );

        $remember = ($request->remember_me) ? true : false;

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            if ($request->has('ajax')) {
                return response()->json([
                    'status' => false,
                    'msg' => 'Vui lòng nhập đủ thông tin'
                ]);
            }
            return back()->withErrors($validator)->withInput();
        } else {
            $email = $request->email;
            if (strpos($email, '@') == false) {
                $email = $email . '@autogenerated.com';
            }

            if (Auth::attempt(['email' => $email, 'password' => $request->password], $remember)) {
                if ($request->has('ajax')) {
                    return response()->json([
                        'status' => true
                    ]);
                }
                return redirect()->back();
            } else {
                if ($request->has('ajax')) {
                    return response()->json([
                        'status' => false,
                        'msg' => 'Sai thông tin tài khoản'
                    ]);
                }
                \Session::flash('error', 'Sai thông tin tài khoản!');
                return redirect()->route('user.login');
            }
        }
    }

    public function postRegister(Request $request)
    {
        $rules = array(
            'name' => 'required|max:255',
            'tel' => 'required|max:255',
            'email' => 'required|max:255|email|unique:users',
            'password' => 'required|min:6',
            're_password' => 'required|min:6'
        );

        $messages = array(
            'required' => ':attribute là bắt buộc.',
            'birthday_day.required' => 'Birth date field is required.',
            'name.required' => 'Tên là trường bắt buộc',
            'tel.required' => 'Số điện thoại là trường bắt buộc',
            'email.required' => 'Email là trường bắt buộc',
            'password.required' => 'Mật khẩu là trường bắt buộc',
            're_password.required' => 'Bạn chưa nhập lại mật khẩu'
        );

        $fieldNames = array(
            'name' => 'Tên',
            'tel' => 'Số điện thoại',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            're_password' => 'Nhập lại mật khẩu'
        );

        $validator = Validator::make($request->all(), $rules, $messages);
        $validator->setAttributeNames($fieldNames);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        } else {
            $data = $request->except('_token');
            $data['password'] = bcrypt($data['password']);
            unset($data['re_password']);
            $user = User::create($data);

            Auth::login($user);
            if($request->has('ajax')) {
                return response()->json([
                    'status'    => true
                ]);
            }

            return redirect()->back();
        }
    }*/
}