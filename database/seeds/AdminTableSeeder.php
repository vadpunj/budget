<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->truncate();
      DB::table('users')->insert([
        [
          'name' => 'Admin',
          'emp_id' => 'Admin',
          'pwd' => str_random(8),
          'cost_title' =>'พบศ.',
          'type' => 1,
          'field' => 'สายงานดิจิทัล',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
          'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
          'center_money' => '1N10206',
          'fund_center' => '1N10200',
          'division_center' => '1D00000',
          'tel' => '3487',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        // [
        //   'name' => 'Phatsirin Srisaengchai',
        //   'emp_id' => '01000583',
        //   'password' => NULL,
        //   'cost_title' =>'พบศ.',
        //   'type' => 1,
        //   'field' => 'สายงานดิจิทัล',
        //   'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
        //   'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
        //   'center_money' => '1N10206',
        //   'fund_center' => '1N10200',
        //   'division_center' => '1D00000',
        //   'tel' => '3487',
        //   'NT' => 'NT1',
        //   'created_at' => date('Y-m-d H:i:s'),
        //   'updated_at' => date('Y-m-d H:i:s')
        // ],
        [
          'name' => 'Dumkerng Muikeaw',
          'emp_id' => '00368195',
          'pwd' =>str_random(8),
          'cost_title' =>'พบศ.',
          'type' => 1,
          'field' => 'สายงานดิจิทัล',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
          'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
          'center_money' => '1N10206',
          'fund_center' => '1N10200',
          'division_center' => '1D00000',
          'tel' => '3487',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Panicha Saenkhueansi',
          'emp_id' => '01000554',
          'pwd' => str_random(8),
          'cost_title' =>'พบศ.',
          'type' => 1,
          'field' => 'สายงานดิจิทัล',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
          'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
          'center_money' => '1N10206',
          'fund_center' => '1N10200',
          'division_center' => '1D00000',
          'tel' => '3176',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Wanida Chomthamai',
          'emp_id' => '01000103',
          'pwd' => str_random(8),
          'cost_title' =>'พบศ.',
          'type' => 1,
          'field' => 'สายงานดิจิทัล',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
          'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
          'center_money' => '1N10206',
          'fund_center' => '1N10200',
          'division_center' => '1D00000',
          'tel' => '4217',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Prapatsorn Prechan',
          'emp_id' => '00309374',
          'pwd' => str_random(8),
          'cost_title' =>'พบศ.',
          'type' => 1,
          'field' => 'สายงานดิจิทัล',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
          'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
          'center_money' => '1N10206',
          'fund_center' => '1N10200',
          'division_center' => '1D00000',
          'tel' => '4217',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Punyabha Auparikchatpong',
          'emp_id' => '01000363',
          'pwd' => str_random(8),
          'cost_title' =>'พบศ.',
          'type' => 1,
          'field' => 'สายงานดิจิทัล',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร 1',
          'part' => 'ส่วนพัฒนาและบริหารระบบบริหารจัดการทรัพยากรองค์กร',
          'center_money' => '1N10206',
          'fund_center' => '1N10200',
          'division_center' => '1D00000',
          'tel' => '4217',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]
      ]);
    }
}
