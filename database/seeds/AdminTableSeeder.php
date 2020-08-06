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
          'name' => 'phatsirin',
          'emp_id' => '01000583',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยีสารสนเทศ',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N00203',
          'fund_center' => '1N00200',
          'tel' => '3487',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'dumkerng',
          'emp_id' => '00368195',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยีสารสนเทศ',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N00203',
          'fund_center' => '1N00200',
          'tel' => '3487',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'panicha',
          'emp_id' => '01000554',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยีสารสนเทศ',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N00203',
          'fund_center' => '1N00200',
          'tel' => '3176',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]
      ]);
    }
}
