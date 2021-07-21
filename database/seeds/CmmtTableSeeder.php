<?php

use Illuminate\Database\Seeder;

class CmmtTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cmmt')->truncate();
        DB::table('cmmt')->insert([
          [
            'name_id' => 1,
            'name' => '1. ค่าใช้จ่ายตอบแทนแรงงาน',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 2,
            'name' => '2. ค่าใช้จ่ายสวัสดิการ',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 3,
            'name' => '3. ค่าใช้จ่ายพัฒนาและฝึกอบรมบุคลากร',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 4,
            'name' => '4. ค่าสาธารณูปโภค',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 5,
            'name' => '5. ค่าเช่า',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 6,
            'name' => '6. ค่าใช้จ่ายการตลาดและส่งเสริมการขาย',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 7,
            'name' => '7. ค่าใช้จ่ายเผยแพร่และประชาสัมพันธ์',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 8,
            'name' => '8. ค่าซ่อมแซมและบำรุงรักษา',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 9,
            'name' => '9. ค่าส่วนแบ่งบริการโทรคมนาคม',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 10,
            'name' => '10. ค่าใช้จ่ายบริการโทรคมนาคม',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 11,
            'name' => '11. ต้นทุนขาย',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 12,
            'name' => '12. ค่าใช้จ่ายเกี่ยวกับ กสทช.',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 13,
            'name' => '13. ค่าใช้จ่ายดำเนินงานอื่น',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 14,
            'name' => '14. ค่าใช้จ่ายบริการอื่น',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 15,
            'name' => '15. ค่าใช้จ่ายอื่น',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 16,
            'name' => '16. ค่าเสื่อมราคาและรายจ่ายตัดบัญชีสินทรัพย์',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 17,
            'name' => '17. ค่าตัดจำหน่าย - สิทธิในการใช้ตามสัญญาเช่า',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 18,
            'name' => '18. ดอกเบี้ยจ่าย ต้นทุนทางการเงิน',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ],
          [
            'name_id' => 19,
            'name' => '19. เงินสำรองฉุกเฉิน',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
          ]
        ]);
    }
}
