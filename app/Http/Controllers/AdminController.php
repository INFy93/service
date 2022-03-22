<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Exports\MonthOrdersExport;
use Illuminate\Http\Request;
use App\Models\Option;
use Illuminate\Support\Facades\Hash;
class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function getTotalData()
    {
        $all_orders = Order::all()->count();

        $open_orders = Order::where([
            ['status', '!=', 6],
            ['status', '!=', 7]
        ])
        ->count();

        $current_orders = Order::where('status', 2)->count();

        $result = [
            'all' => $all_orders,
            'open' => $open_orders,
            'current' => $current_orders
        ];

        return response()->json($result);
    }

    public function getAllSettings()
    {
        return response()->json(Option::all());
    }

    public function exportMonth()
    {
        return (new MonthOrdersExport)->download('orders_month.xlsx');
    }

    public function getUsers()
    {
        return response()->json(User::with(['roles'])->get());
    }

    public function addUser(Request $req)
    {
        $user = [
            'name' => $req->user['userName'],
            'login' => $req->user['userLogin'],
            'email' => $req->user['userEmail'],
            'password' =>  Hash::make($req->user['userPass']),
            'is_admin' => $req->user['userIsAdmin'] ? 1 : 0,
            'created_at' =>  date("Y-m-d H:i:s"),
            'updated_at' =>  date("Y-m-d H:i:s"),
        ];

        User::insert($user);

        return response()->json("Юзер успешно добавлен!");
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();

        return response()->noContent();
    }
}
