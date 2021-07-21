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
          'name' => 'Phatsirin Srisaengchai',
          'emp_id' => '01000583',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '3487',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Dumkerng Muikeaw',
          'emp_id' => '00368195',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '3487',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Panicha Saenkhueansi',
          'emp_id' => '01000554',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '3176',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Wanida Chomthamai',
          'emp_id' => '01000103',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '4217',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Prapatsorn Prechan',
          'emp_id' => '00309374',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '4217',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Jariya Eamapichat',
          'emp_id' => '00232739',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '3175',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ],
        [
          'name' => 'Punyabha Auparikchatpong',
          'emp_id' => '01000363',
          'cost_title' =>'งอท.',
          'type' => 1,
          'field' => 'สายงานเทคโนโลยี',
          'office' => 'ฝ่ายเทคโนโลยีสารสนเทศเพื่อบริหารองค์กร',
          'part' => 'ส่วนบริการด้านระบบบัญชีการเงิน',
          'center_money' => '1N10203',
          'fund_center' => '1N10200',
          'division_center' => '1N00000',
          'tel' => '4217',
          'NT' => 'NT1',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]
      ]);
    }
}
