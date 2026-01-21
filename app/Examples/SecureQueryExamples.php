<?php

namespace App\Examples;

use Illuminate\Support\Facades\DB;
use App\Models\Product;

class SecureQueryExamples
{
    /**
     * SECURE: Using Eloquent ORM (automatically parameterized)
     */
    public function secureEloquentQuery($userInput)
    {
        return Product::where('title', 'like', "%{$userInput}%")->get();
    }

    /**
     * SECURE: Using Query Builder with parameter binding
     */
    public function secureQueryBuilder($userInput)
    {
        return DB::table('products')
            ->where('title', 'like', "%{$userInput}%")
            ->get();
    }

    /**
     * SECURE: Using named parameters
     */
    public function secureNamedParameters($title, $price)
    {
        return DB::select(
            'SELECT * FROM products WHERE title = :title AND price > :price',
            ['title' => $title, 'price' => $price]
        );
    }

    /**
     * INSECURE: NEVER DO THIS - Vulnerable to SQL Injection
     */
    public function insecureRawQuery($userInput)
    {
        // This is vulnerable - DO NOT USE
        // return DB::select("SELECT * FROM products WHERE title = '{$userInput}'");
    }
}
