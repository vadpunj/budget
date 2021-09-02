<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
      table , th, td {
        border: 1px solid #000000;
        font-family: TH Sarabun New;
      }
    </style>
  </head>
  <body>
      <table>
        <tr>
          <th colspan="2">{{ 'คำขอตั้งงบประมาณทำการประจำปี '.(date("Y")+544) }}</th>
        </tr>
        <tr>
          @if($type != 'all')
          <th colspan="2">{{ Func::get_center_name($type) }}</th>
          @else
          <th colspan="2">{{ Func::get_office_name($fund) }}</th>
          @endif
        </tr>
          <tr>
            <th align="center">รายการ</th>
            <th align="center">{{'ประมาณจ่ายจริงปี '.(date("Y",strtotime("-3 year"))+544)}}</th>
            <th align="center">{{'ประมาณจ่ายจริงปี '.(date("Y",strtotime("-2 year"))+544)}}</th>
            <th align="center">{{'ประมาณจ่ายจริงปี '.(date("Y",strtotime("-1 year"))+544)}}</th>
            <th align="center">{{'งบประมาณขอตั้งปี '.(date("Y")+544)}}</th>
            @if($type != 'all')
            <th align="center">{{'คำอธิบาย'}}</th>
            @endif
          </tr>
          @php
            $all_sum = 0;
            $all_sum1 = 0;
            $all_sum2 = 0;
            $all_sum3 = 0;
          @endphp
         @foreach($all_name as $id1 => $arr_id2)
           @if(isset($id_1[$id1][date("Y")+544]) && $id_1[$id1][date("Y")+544] == $id1)
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
              @if(isset($id_1[$id1][date("Y")+544]) && isset($id_2[$id1][$id2]))
               @if($id_1[$id1][date("Y")+544] == 1 && $id_2[$id1][$id2] == 1)
               <tr>
                 <td colspan="{{ $count }}">1.1 เงินเดือน ค่าจ้าง ค่าตอบแทน</td>
               </tr>
               @elseif($id_1[$id1][date("Y")+544] == 1 && $id_2[$id1][$id2] == 2)
               <tr>
                 <td colspan="{{ $count }}">1.2 เงินเดือน ค่าจ้าง ค่าตอบแทนผู้บริหาร</td>
               </tr>
               @endif
               @if($id_1[$id1][date("Y")+544] == 2 && $id_2[$id1][$id2] == 1)
               <tr>
                 <td colspan="{{ $count }}">2.1 ค่าสวัสดิการพนักงาน ลูกจ้าง</td>
               </tr>
               @elseif($id_1[$id1][date("Y")+544] == 2 && $id_2[$id1][$id2] == 2)
               <tr>
                 <td colspan="{{ $count }}">2.2 ค่าสวัสดิการผู้บริหาร</td>
               </tr>
               @endif

              @endif
           @foreach($arr_year as $year =>$arr_acc)
             @foreach($arr_acc as $account => $value)
              @if($year3[date("Y")+541][$account] > 0 || $year2[date("Y")+542][$account] > 0 || $year1[date("Y")+543][$account] > 0 || $now[$year][$account] > 0)
                 <tr>
                   <td>{{ $account.' '.$acname[$account] }}</td>
                   @if(!empty($year3[date("Y")+541][$account]))
                   @php
                     $sum3 += $year3[date("Y")+541][$account];
                     $all_sum3 += $year3[date("Y")+541][$account];
                   @endphp
                     <td align="right">{{ number_format($year3[date("Y")+541][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if(!empty($year2[date("Y")+542][$account]))
                   @php
                     $sum2 += $year2[date("Y")+542][$account];
                     $all_sum2 += $year2[date("Y")+542][$account];
                   @endphp
                     <td align="right">{{ number_format($year2[date("Y")+542][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if(!empty($year1[date("Y")+543][$account]))
                   @php
                     $sum1 += $year1[date("Y")+543][$account];
                     $all_sum1  += $year1[date("Y")+543][$account];
                   @endphp
                     <td align="right">{{ number_format($year1[date("Y")+543][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if(!empty($now[date("Y")+544][$account]))
                   @php
                     $sum += $now[date("Y")+544][$account];
                     $all_sum += $now[date("Y")+544][$account];
                   @endphp
                     <td align="right">{{ number_format($now[$year][$account],2) }}</td>
                   @else
                     <td align="center">{{ '-' }}</td>
                   @endif
                   @if($type != 'all')
                     @if(!empty($reason[date("Y")+544][$account]))
                       <td>{{ $reason[date("Y")+544][$account] }}</td>
                     @else
                       <td align="center">{{ '-' }}</td>
                     @endif
                   @endif
                 </tr>
                @endif
               @endforeach
             @endforeach
           @endforeach
           @if(isset($id_1[$id1][date("Y")+544]) && $id_1[$id1][date("Y")+544] == $id1)
           <tr>
             <td align="center"><b>Sum</b></td>
             <td align="right"><b>{{ number_format($sum3,2) }}</b></td>
             <td align="right"><b>{{ number_format($sum2,2) }}</b></td>
             <td align="right"><b>{{ number_format($sum1,2) }}</b></td>
             <td align="right"><b>{{ number_format($sum,2) }}</b></td>
             @if($type != 'all')
             <td></td>
             @endif
           </tr>
           @endif
         @endforeach
         <tr>
           <td align="center"><b>Sum Total</b></td>
           <td align="right"><b>{{ number_format($all_sum3,2) }}</b></td>
           <td align="right"><b>{{ number_format($all_sum2,2) }}</b></td>
           <td align="right"><b>{{ number_format($all_sum1,2) }}</b></td>
           <td align="right"><b>{{ number_format($all_sum,2) }}</b></td>
           @if($type != 'all')
           <td></td>
           @endif
         </tr>
      </table>
  </body>
</html>
