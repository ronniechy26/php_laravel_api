<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Account;
use App\Http\Resources\AccountResource;
use App\Http\Resources\AccountResourceCollection;
use Validator;
use DB;
use stdClass;

class AccountController extends Controller
{

    public function index(Request $request){

        $accounts = DB::table("Accounts")
        ->where(function($query)use($request){
             $query->where('name','like','%'.$request->input('q').'%')
            ->orWhere('position','like','%'.$request->input('q').'%')
            ->orWhere('address','like','%'.$request->input('q').'%');
        })
        ->skip($request->input('skip',0))
        ->take($request->input('take',10))
        ->orderBy($request->input('sortDir','id'),$request->input('sort','asc') == 'descend' ? 'desc' : 'asc')
        ->where($request->input('filterColumn'),'=',$request->input('filter'))
        ->get();

        $meta = new stdClass();
        $meta->q = $request->input('q','');
        $meta->sort = $request->input('sort','');
        $meta->filter = $request->input('filter','');
        $meta->filterColumn = $request->input('filterColumn','');
        $meta->sortDir = $request->input('sortDir','');
        $meta->skip = $request->input('skip',0);
        $meta->take = $request->input('take',10);
        $meta->count = count(DB::table("Accounts")->get());

        $obj= new stdClass();
        $obj->data = $accounts;
        $obj->meta = $meta;
        return  response()->json($obj,200);
        
    }

    public function show($account) : AccountResource
    {

        if($account != null){
            $accountIndex =  DB::table("Accounts")->where("id","=",$account)->get();
            if($accountIndex != null){
                return new AccountResource($accountIndex);
            }
            return new AccountResource(['Error' => 'Account not found!',
                    'HttpCode' => 404 ]);
        }
        return new AccountResource('id is null');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:accounts|max:15|min:5',
            'position' => 'required',
            'birthday' => 'required',
            'address' => 'required',
            'gender' => 'required',
        ]);
        if($validator->fails()){
            return new AccountResource($validator->errors());
        }else{

            $account = new Account;
            $account->name = $request->input('name');
            $account->birthday = $request->input('birthday');
            $account->address = $request->input('address');
            $account->position = $request->input('position');
            $account->gender = $request->input('gender');
            $account->save();

            return new AccountResource($account);
        }   
    }

    public function update(Request $request, $account){

        $accountIndex = Account::find($account);

        if($accountIndex != null){

            $validator = Validator::make($request->all(),[
                'name' => 'required|unique:accounts|max:15|min:5',
                'position' => 'required',
                'birthday' => 'required',
                'address' => 'required',
                'gender' => 'required',
            ]);
            if($validator->fails()){
                return new AccountResource($validator->errors());
            }else{
                $accountIndex->name = $request->input('name');
                $accountIndex->birthday = $request->input('birthday');
                $accountIndex->address = $request->input('address');
                $accountIndex->position = $request->input('position');
                $accountIndex->gender = $request->input('gender');
                $accountIndex->update();
                
                return new AccountResource($accountIndex);
            }
           
        }
        return new AccountResource(['Error' => 'Account not found!',
                    'HttpCode' => 404 ]);

    }

    public function destroy($account){

        $accountIndex = Account::find($account);
        if($accountIndex != null){
            $accountIndex->delete();
            return new AccountResource($accountIndex);
        }
        return new AccountResource(['Error' => 'Account not found!',
                    'HttpCode' => 404 ]);
    }
}
