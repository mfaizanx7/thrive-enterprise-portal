<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientAsset extends Model
{
    use HasFactory;

     public function employees()
    {
        return $this->belongsToMany('App\Models\Employee', 'employees', '', 'user_id');
    }

    public function company()
    {
        return $this->hasOne('App\Models\Company', 'id', 'company_id');
    }

    public function assetdetail()
    {
        return $this->hasMany(ClientAssetDetail::class, 'asset_id', 'id');

    }

    public function users($users)
    {
        $userArr = explode(',', $users);
        $users  = [];
        foreach($userArr as $user)
        {
            $emp=Employee::where('user_id',$user)->first();

            if(!empty($emp)){
                $users[] = User::where('id',$emp->user_id)->first();
            }

        }
        return $users;
    }
}
