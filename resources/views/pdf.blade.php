<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <style>

        body {
            font-family: "THSarabunNew";
        }
        table, th, td {
          border: 1px solid black;
          border-collapse: collapse;
          /* table-layout: fixed; */
          /* width:100%; */

          page-break-inside: auto;
          /* display:inline-block; */
        }
        table{
          margin-left: -30px;
        }
        .content{
          word-wrap: break-all; /*old browsers*/
          overflow-wrap:break-word;
          /* overflow-wrap: anywhere; */
        }
        .overflow-wrap-hack{
          max-width:1px;
        }

    </style>
  </head>
  <body>
    <h3>{{ 'คำขอตั้งงบประมาณทำการประจำปี '.(Func::get_year()) }}</h3>
    @if($type != 'all')
    <p style="margin-top: -15px; margin-bottom: -1px;"><b>{{ Func::get_center_name($type) }}</b></p>
    @else
    <p style="margin-top: -15px; margin-bottom: -1px;"><b>{{ Func::get_office_name($fund) }}</b></p>
    @endif
      <table>
        <thead>
          <tr>
            <th>รายการ</th>
            <th style="padding: 0.5rem;">{{'ประมาณจ่ายจริงปี '.(Func::get_year()-3)}}</th>
            <th style="padding: 0.5rem;">{{'ประมาณจ่ายจริงปี '.(Func::get_year()-2)}}</th>
            <th style="padding: 0.5rem;">{{'ประมาณจ่ายจริงปี '.(Func::get_year()-1)}}</th>
            <th style="padding: 0.5rem;">{{'งบประมาณขอตั้งปี '.(Func::get_year())}}</th>
            @if($type != 'all')
            <th>{{'คำอธิบาย'}}</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @php
            $all_sum = 0;
            $all_sum1 = 0;
            $all_sum2 = 0;
            $all_sum3 = 0;
          @endphp
         @foreach($all_name as $id1 => $arr_id2)
           @if(isset($id_1[$id1][Func::get_year()]) && $id_1[$id1][Func::get_year()] == $id1)
             <tr>
                 <td colspan="{{ $count }}"><b>{{ $head[$id1] }}</b></td>
             </tr>
           @endif
         @php
           $sum = 0;
           $sum1 = 0;
           $sum2 = 0;
           $sum3 = 0;
         @endphp
           @foreach($arr_id2 as $id2 => $arr_year)
              @if(isset($id_1[$id1][Func::get_year()]) && isset($id_2[$id1][$id2]))
               @if($id_1[$id1][Func::get_year()] == 1 && $id_2[$id1][$id2] == 1)
               <tr>
                 <td colspan="{{ $count }}">1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน</td>
               </tr>
               @elseif($id_1[$id1][Func::get_year()] == 1 && $id_2[$id1][$id2] == 2)
               <tr>
                 <td colspan="{{ $count }}">1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร</td>
               </tr>
               @endif
               @if($id_1[$id1][Func::get_year()] == 2 && $id_2[$id1][$id2] == 1)
               <tr>
                 <td colspan="{{ $count }}">2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง</td>
               </tr>
               @elseif($id_1[$id1][Func::get_year()] == 2 && $id_2[$id1][$id2] == 2)
               <tr>
                 <td colspan="{{ $count }}">2.2 ค่าสวัสดิการผู้บริหาร</td>
               </tr>
               @endif

              @endif
           @foreach($arr_year as $year =>$arr_acc)
             @foreach($arr_acc as $account => $value)
              @if($year3[(Func::get_year()-3)][$account] > 0 || $year2[(Func::get_year()-2)][$account] > 0 || $year1[(Func::get_year()-1)][$account] > 0 || $now[$year][$account] > 0)
                 <tr>
                   <td>{{ $account.' '.$acname[$account] }}</td>
                   @if(!empty($year3[(Func::get_year()-3)][$account]))
                   @php
                     $sum3 += $year3[(Func::get_year()-3)][$account];
                     $all_sum3 += $year3[(Func::get_year()-3)][$account];
                   @endphp
                     <td align="right" style="padding: 0.5rem;">{{ number_format($year3[(Func::get_year()-3)][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if(!empty($year2[(Func::get_year()-2)][$account]))
                   @php
                     $sum2 += $year2[(Func::get_year()-2)][$account];
                     $all_sum2 += $year2[(Func::get_year()-2)][$account];
                   @endphp
                     <td align="right" style="padding: 0.5rem;">{{ number_format($year2[(Func::get_year()-2)][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if(!empty($year1[(Func::get_year()-1)][$account]))
                   @php
                     $sum1 += $year1[(Func::get_year()-1)][$account];
                     $all_sum1  += $year1[(Func::get_year()-1)][$account];
                   @endphp
                     <td align="right" style="padding: 0.5rem;">{{ number_format($year1[(Func::get_year()-1)][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if(!empty($now[Func::get_year()][$account]))
                   @php
                     $sum += $now[Func::get_year()][$account];
                     $all_sum += $now[Func::get_year()][$account];
                   @endphp
                     <td align="right" style="padding: 0.5rem;">{{ number_format($now[$year][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if($type != 'all')
                     @if(!empty($reason[Func::get_year()][$account]))
                       <td>{{ $reason[Func::get_year()][$account] }}</td>
                     @else
                       <td align="center">{{ '-' }}</td>
                     @endif
                   @endif
                 </tr>
                @endif
               @endforeach
             @endforeach
           @endforeach
           @if(isset($id_1[$id1][Func::get_year()]) && $id_1[$id1][Func::get_year()] == $id1)
           <tr>
             <td align="center"><b>Sum</b></td>
             <td align="right" style="padding: 0.5rem;"><b>{{ number_format($sum3,2) }}</b></td>
             <td align="right" style="padding: 0.5rem;"><b>{{ number_format($sum2,2) }}</b></td>
             <td align="right" style="padding: 0.5rem;"><b>{{ number_format($sum1,2) }}</b></td>
             <td align="right" style="padding: 0.5rem;"><b>{{ number_format($sum,2) }}</b></td>
             @if($type != 'all')
             <td><b></b></td>
             @endif
           </tr>
           @endif
         @endforeach
         <tr>
           <td align="center"><b>Sum Total</b></td>
           <td align="right" style="padding: 0.5rem;"><b>{{ number_format($all_sum3,2) }}</b></td>
           <td align="right" style="padding: 0.5rem;"><b>{{ number_format($all_sum2,2) }}</b></td>
           <td align="right" style="padding: 0.5rem;"><b>{{ number_format($all_sum1,2) }}</b></td>
           <td align="right" style="padding: 0.5rem;"><b>{{ number_format($all_sum,2) }}</b></td>
           @if($type != 'all')
           <td><b></b></td>
           @endif
         </tr>
       </tbody>
      </table>
  </body>
</html>
