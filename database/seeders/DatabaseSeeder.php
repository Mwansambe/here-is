<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User; use App\Models\Category; use App\Models\Product;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::updateOrCreate(['email'=>'admin@techvault.co.tz'],[
            'name'=>'Admin TechVault','phone'=>'+255700000001','password'=>'admin123','role'=>'admin'
        ]);

        $cats = [
            ['Mobile Devices','fa-mobile-screen'],
            ['Computing Devices','fa-computer'],
            ['Audio Devices','fa-headphones'],
        ];

        foreach($cats as [$name,$icon]){
            $c=Category::firstOrCreate(['slug'=>\Str::slug($name)],['name'=>$name,'icon'=>$icon,'is_active'=>true]);
            for($i=1;$i<=3;$i++){
                Product::firstOrCreate(
                    ['slug'=>\Str::slug("$name Product $i")],
                    [
                        'category_id'=>$c->id,
                        'name'=>"$name Product $i",
                        'brand'=>'TechBrand',
                        'price'=>rand(100000,2000000),
                        'stock'=>20,
                        'is_popular'=>$i<=3,
                        'is_featured'=>$i===1,
                        'specifications'=>['Display'=>'6.5"', 'Battery'=>'5000mAh']
                    ]
                );
            }
        }
    }
}