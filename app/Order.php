<?php

namespace Educators;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Auth;

class Order extends Model
{
    protected $table = 'orders';
    protected $fillable = [
        'id', 'mobile', 'status', 'user_id', 'selected_packet_id', 'operator_price', 'admin_price', 'user_price', 'customer_name', 'operator', 'created_at', 'message', 'original_order_id', 'type', 'is_number_checked'
    ];

    public function get_regular_orders_with_all_fields_table($user_id='', $from_date='', $to_date=''){
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    "orders.customer_name",
                    "orders.mobile",
                    "orders.operator",
                    'packets.name as packet_name',
                    'packets.name as type packet_type',
                    'orders.admin_price as purchasing_price',
                    'orders.user_price as selling_price',
                    DB::raw('(orders.user_price - orders.admin_price) as profit'),
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "orders.created_at as request_date",
                    "orders.updated_at as response_date"
                    );

        if($user_id)
            $orders->where("orders.user_id", $user_id);

        if(Auth::user()->type == 'agent')
            $orders->whereNull('orders.original_order_id');

        if($from_date)
            $orders->where('orders.created_at', '>=', $from_date . ' 00:00:00');
        if($to_date)
            $orders->where('orders.created_at', '<=', $to_date . ' 23:59:59');

        return $orders->get();
    }

    public function get_admin_orders_with_all_fields_table($from_date='', $to_date=''){
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
                    ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    'users.name as user',
                    "orders.customer_name",
                    "orders.mobile",
                    "orders.operator",
                    'packets.name as packet_name',
                    'packets.name as type packet_type',
                    'orders.admin_price as purchasing_price',
                    'orders.user_price as selling_price',
                    DB::raw('(orders.user_price - orders.admin_price) as profit'),
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "orders.created_at as request_date",
                    "orders.updated_at as response_date"
                    );

        if(Auth::user()->type === 'agent') {
            $son_users = User::select('id')->where('created_by_user_id', Auth::user()->id)->get();
            $son_users_ids = [];
            foreach ($son_users as $son_user)
                array_push($son_users_ids, $son_user->id);
            $orders->whereIn("orders.user_id", $son_users_ids);
        } else { // is admin
            $orders->whereNull('orders.original_order_id');
        }

        if($from_date)
            $orders->where('orders.created_at', '>=', $from_date . ' 00:00:00');
        if($to_date)
            $orders->where('orders.created_at', '<=', $to_date . ' 23:59:59');

        return $orders->get();
    }

    public function get_regular_orders_table($user_id, $status=[], $is_for_today=false){
        $orders = DB::table("orders")
            ->select('id',
                    'status as status_hidden',
                    "operator as operator_hidden",
                    "customer_name",
                    "mobile",
                    "operator",
                    DB::raw("(CASE status 
                                WHEN 'check_pending' THEN '".__('orders_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('orders_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "created_at as request_date",
                    "updated_at as response_date",
                    "message"
                    )
            ->where("user_id", $user_id)
            ->whereIn("status", $status);

        if($is_for_today)
            $orders->where(function($q) {
                $q->whereDate('created_at', '=', Carbon::today()->toDateString())
                ->orWhereDate('updated_at', '=', Carbon::today()->toDateString());
            });

        return $orders->get();
    }

    public function get_regular_orders_with_extra_culomns_table($user_id, $status=[], $is_for_today=false){
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    "orders.customer_name",
                    "orders.mobile",
                    "orders.operator",
                    'packets.name as packet_name',
                    'orders.admin_price as purchasing_price',
                    'orders.user_price as selling_price',
                    DB::raw('(orders.user_price - orders.admin_price) as profit'),
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status"),
                    "orders.created_at as request_date",
                    "orders.updated_at as response_date"
                    )
            ->where("orders.user_id", $user_id)
            ->whereIn("orders.status", $status);

            if($is_for_today)
                $orders->where(function($q) {
                    $q->whereDate('orders.created_at', '=', Carbon::today()->toDateString())
                    ->orWhereDate('orders.updated_at', '=', Carbon::today()->toDateString());
                });
        return $orders->get();
    }

    public function get_admin_orders_table($status=[]){
//        return $operators_that_have_api;
        $orders = DB::table("orders")
            ->leftJoin('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin('orders AS parent_orders', 'orders.id', '=', 'parent_orders.original_order_id')
            ->leftJoin('orders AS child_orders', 'child_orders.id', '=', 'orders.original_order_id')
            ->leftJoin('users AS child_users', 'child_users.id', '=', 'child_orders.user_id')
            ->select('orders.id',
                    "users.name as name_of_user",
                    "child_users.name as name_of_child_user",
                    "orders.customer_name",
                    "orders.mobile",
                    "orders.operator",
                    "orders.created_at as request_date",
                    'orders.operator as operator_hidden',
                    'users.id as user_id',
                    'orders.message'
            )
            ->where("users.created_by_user_id", Auth::user()->id);

        if(Auth::user()->type == 'agent')
            $orders->where("orders.status", 'in_review')->where("parent_orders.status", 'selecting_packet');
        elseif($status)
            $orders->whereIn("orders.status", $status);

        $orders = $orders->get();

        foreach ($orders as $order){
            if($order->name_of_child_user)
                $order->name_of_user .= ' - ' . $order->name_of_child_user;
            unset($order->name_of_child_user);
        }

        return $orders;
    }

    public function get_admin_orders_with_extra_culomns_table($status=[]){
        $operators_that_have_api = get_operators_that_have_api();
        $orders = DB::table("orders")
                    ->leftJoin('packets', 'packets.id', '=', 'orders.selected_packet_id')
                    ->leftJoin('users', 'users.id', '=', 'orders.user_id')
                    ->leftJoin('orders AS child_orders', 'child_orders.id', '=', 'orders.original_order_id')
                    ->leftJoin('users AS child_users', 'child_users.id', '=', 'child_orders.user_id')
            ->select('orders.id',
                    'orders.status as status_hidden',
                    'users.name as name_of_user',
                    "child_users.name as name_of_child_user",
                    'orders.mobile',
                    'orders.operator',
                    'packets.name as packet_name',
                    "packets.price as purchasing_price",
                    "orders.admin_price as selling_price",
                    DB::raw('(orders.admin_price - packets.price) as profit'),
                    "orders.created_at as request_date",
                    DB::raw("(CASE orders.status 
                                WHEN 'check_pending' THEN '".__('home_lng.check_pending')."' 
                                WHEN 'selecting_packet' THEN '".__('home_lng.selecting_packet')."' 
                                WHEN 'in_review' THEN '".__('home_lng.in_review')."' 
                                WHEN 'in_progress' THEN '".__('home_lng.in_progress')."' 
                                WHEN 'rejected' THEN '".__('home_lng.rejected')."' 
                                WHEN 'completed' THEN '".__('home_lng.completed')."'
                                WHEN 'canceled' THEN '".__('home_lng.canceled')."' 
                            END) AS status")
                    )
            ->where("users.created_by_user_id", Auth::user()->id)
            ->whereNotIn("orders.operator", $operators_that_have_api);

        if($status)
            $orders->whereIn("orders.status", $status);

        $orders = $orders->get();

        foreach ($orders as $order){
            if($order->name_of_child_user)
                $order->name_of_user .= ' - ' . $order->name_of_child_user;
            unset($order->name_of_child_user);
        }

        return $orders;
    }
}
