<?php
/**
 * Created by PhpStorm.
 * BillPayment: hoanghung
 * Date: 16/05/2016
 * Time: 15:19
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;

use App\Models\PermissionRole;
use App\Models\Permissions;
use App\Models\Product;
use App\Models\RoleAdmin;
use App\Models\Roles;
use App\Models\User;
use App\Models\UserAdmin;
use Illuminate\Http\Request;

class SeedController extends Controller
{

    public function getIndex($action)
    {
        return $this->$action();
    }

    public function convertUserToAdmin() {
        /*$bills = Bill::all();
        foreach ($bills as $bill) {
            $admin_id = UserAdmin::where('user_id', $bill->customer_id)->first()->admin_id;
            $bill->customer_id = $admin_id;
            $bill->save();
        }*/
        dd('ok');

        $users = User::all();
        $role_id = Roles::where('name', 'customer')->first()->id;
        foreach ($users as $user) {
            $admin = new Admin();
            $admin->name = $user->name;
            $admin->email = $user->email;
            $admin->tel = $user->tel;
            $admin->facebook = $user->facebook;
            $admin->save();

            $user_admin = new UserAdmin();
            $user_admin->user_id = $user->id;
            $user_admin->admin_id = $admin->id;
            $user_admin->save();

            RoleAdmin::create([
                'admin_id' => $admin->id,
                'role_id' => $role_id,
            ]);
        }
        dd('xong!');
    }

    public function new_permission()
    {

        $pers = [
            'unit_price' => 'Đơn giá',
//            'tag_product' => 'Tag sản phẩm',
//            'bill' => 'Đơn hàng',
//            'category_post' => 'Danh mục bài viết',
//            'tag_post' => 'Tag bài viết',
//            'client_say' => 'Phản hồi của khách hàng',
//            'contact' => 'Liên hệ',
//            'label' => 'Nhãn',
//            'post' => 'Bài viết',
//            'product' => 'Sản phẩm',
        ];
        $action = [
            'view' => 'Xem',
            'add' => 'Thêm',
            'edit' => 'Sửa',
            'delete' => 'Xóa',
        ];
        foreach ($pers as $code => $label) {
//            echo '<label style="font-weight: bold;">Thêm vào bảng modules : ' . $code . '</label><br>';
//            $count_module = Module::where('code', $code)->first();
//            if ($count_module == 0) {
//                $code_arr = explode('_', $code);
//                foreach ($code_arr as )
//                Module::create([
//                    'name' => $label,
//                    'code' => $code,
//                    'controller' => ucfirst($code) . 'Controller'
//                ]);
//            }
            foreach ($action as $k => $v) {
                $permission = new Permissions();
                $permission->name = $code . '_' . $k;
                $permission->display_name = $v . ' ' . $label;
                $permission->description = $v . ' ' . $label;
                $permission->save();
                PermissionRole::create([
                    'permission_id' => $permission->id,
                    'role_id' => 1
                ]);
            }
        }
        die('ok');
    }

    public function clone_product()
    {
        $product = Product::find(3);
        for ($i = 1; $i <= 50; $i++) {
            $new_product = $product->replicate();
            $new_product->category_id = rand(64, 80);
            $new_product->save();
        }
        die('ok');
    }

    public function simple_clone_product()
    {
        $product_name = [
            'Bí Quyết Giải Toán Siêu Tốc Môn Vật Lí',
            '5 centimet trên giây',
            'Nhà giả kim',
            'Tôi thấy hoa vàng trên cỏ xanh',
            'Tôi tự học',
            'Đắc Nhân tâm',
            'Quảng gánh đi mà sống',
            'Tôi và Paris'
        ];
        $product = Product::find(75);
        $cats = Category::pluck('id');
        $multi_cat = '';
        $multi_cat .= '|' . $cats[rand(0, count($cats) - 1)] . '|';
        $multi_cat .= '|' . $cats[rand(0, count($cats) - 1)] . '|';
        for ($i = 1; $i <= 100; $i++) {
            $name = $product_name[rand(0, 7)];
            $base_price = rand(100, 200);
            $data = [
                'name' => $name,
                'slug' => str_slug($name) . rand(1, 100),
                'code' => 'abc' . rand(1, 10000),
                'base_price' => $base_price * 10000,
                'final_price' => ($base_price - rand(1, 50)) * 10000,
                'intro' => $product->intro,
                'content' => $product->content,
                'image' => 'products/products.jpg',
                'image_extra' => 'products/products.jpg|products/product1.jpg|products/product2.jpg|products/product3.jpg|products/product4.jpg|products/products.jpg|products/product1.jpg|products/product2.jpg|products/product3.jpg|products/product4.jpg',
                'user_id' => 8,
                'status' => 1,
                'author_id' => rand(1, 11),
                'ngay_phat_hanh' => $product->ngay_phat_hanh,
                'sach_ban_chay' => rand(0, 1),
                'hot_sale' => rand(0, 1),
                'publishing_id' => rand(1, 20),
                'page_number' => rand(100, 500),
                'size' => '13cm x 15cm x 20cm',
                'type' => rand(1, 2),
                'group_id' => 6,
                'iframe' => '<iframe width="100%" height="300" scrolling="no" frameborder="no" allow="autoplay" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/users/120796375&color=%23ff5500&auto_play=false&hide_related=false&show_comments=true&show_user=true&show_reposts=false&show_teaser=true&visual=true"></iframe>',
                'multi_cat' => $multi_cat
            ];
//            dd($data);
            $product_insert = new Product();
            foreach ($data as $k => $v) {
                $product_insert->name = $data['name'];
                $product_insert->$k = $v;
            }
            $product_insert->save();
        }
        die('xong!');
    }

    public function delete_product(Request $request)
    {
        dd('delete_product');
        $products = Product::where('id', '>=', $request->get('from'))->where('id', '<=', $request->get('to'))->get();
        foreach ($products as $product) {
            $product->delete();
        }
        die('Đã xóa Ph từ ' . $request->get('from') . ' đến ' . $request->get('to'));
    }

    public function seed_permissions()
    {
        $modules = Module::pluck('name', 'code');
        $permission_arr = [];
        foreach ($modules as $code => $name) {
            if (in_array($code, ['dashboard'])) {
                $permission = Permissions::create([
                    'name' => $code . '_view',
                    'display_name' => 'Xem ' . $name,
                    'description' => 'Xem ' . $name,
                ]);
                $permission_arr[] = $permission->id;
            } else {
                $permission = Permissions::create([
                    'name' => $code . '_view',
                    'display_name' => 'Xem ' . $name,
                    'description' => 'Xem ' . $name,
                ]);
                $permission_arr[] = $permission->id;

                $permission = Permissions::create([
                    'name' => $code . '_add',
                    'display_name' => 'Thêm ' . $name,
                    'description' => 'Thêm ' . $name,
                ]);
                $permission_arr[] = $permission->id;

                $permission = Permissions::create([
                    'name' => $code . '_edit',
                    'display_name' => 'Sửa ' . $name,
                    'description' => 'Sửa ' . $name,
                ]);
                $permission_arr[] = $permission->id;

                $permission = Permissions::create([
                    'name' => $code . '_delete',
                    'display_name' => 'Xóa ' . $name,
                    'description' => 'Xóa ' . $name,
                ]);
                $permission_arr[] = $permission->id;
            }
        }
        $module_manager = [
            'role' => 'Phân quyền',
            'import' => 'Quản lý import',
            'export' => 'Quản lý export',
            'setting' => 'Cấu hình website'
        ];
        foreach ($module_manager as $module => $module_label) {
            $permission = Permissions::create([
                'name' => $module . '_manager',
                'display_name' => $module_label,
                'description' => $module_label,
            ]);
            $permission_arr[] = $permission->id;
        }

        foreach ($permission_arr as $permission_id) {
            PermissionRole::create([
                'permission_id' => $permission_id,
                'role_id' => 1
            ]);
        }
        die('Xong!');
    }
}