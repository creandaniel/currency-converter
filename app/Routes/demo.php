<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HomeModel extends Model
{
   protected $table = 'users';
   protected $fillable = ['name','email','password'];
}


$app->get('/', 'App\Controllers\CurrencyConverter\HomeController:index');

// $app->get('/hello/{name}', 'App\Controllers\Demo\HelloController:index');

