@extends('admin.layouts.app')
@section('panel')

@php
if($saved->total < 1)
{
$saved->total = 1;
}

$progress = $saved->paid / $saved->total * 100; @endphp
 <div class="row match-height">

<div class="col-lg-12 col-12">
        <!-- Sales Line Chart Card -->
          <div class="card">
            <div class="card-header align-items-start">
              <div>
                <h4 class="card-title mb-25">Savings Payment Chart</h4>
                <p class="card-text mb-0">Total Savings: {{$general->cur_sym}} {{number_format($sum,2)}}</p>
              </div>

            </div>
            <div class="card-body pb-0">
              <div id="sales-line-chart"></div>
            </div>
          </div>
        </div>
        <!--/ Sales Line Chart Card -->


     <!-- Avg Sessions Chart Card starts -->
    <div class="col-lg-12 col-12">
      <div class="card">
        <div class="card-body">
          <div class="row pb-50">
            <div class="col-sm-6 col-12 d-flex justify-content-between flex-column order-sm-1 order-2 mt-1 mt-sm-0">
              <div class="mb-1 mb-sm-0">
                <h2 class="fw-bolder mb-25">{{$general->cur_sym}}{{number_format($saved->balance,2)}}</h2>
                <p class="card-text fw-bold mb-2">Current Balance</p>
                <div class="font-medium-2">
                  <span class="text-success me-25">@if($saved->type == 1) Recurrent Savings @else Target Savings @endif</span>

                </div>
              </div>

            </div>
            <div class="col-sm-6 col-12 d-flex justify-content-between flex-column text-end order-sm-2 order-1">

              <div id="avg-sessions-chart"></div>
            </div>
          </div>
          <hr />
          <div class="row avg-sessions pt-50">
            <div class="col-6 mb-2">
              <p class="mb-50">@if($saved->type == 1) Recurrent Amount:  @else Target Goal: @endif <br>{{$general->cur_sym}}{{number_format($saved->amount,2)}}</p>
              <div class="progress progress-bar-primary" style="height: 6px">
                <div
                  class="progress-bar"
                  role="progressbar"
                  aria-valuenow="100"
                  aria-valuemin="100"
                  aria-valuemax="100"
                  style="width: 100%"
                ></div>
              </div>
            </div>
            <div class="col-6 mb-2">
              <p class="mb-50">Total Saved: <br>{{$general->cur_sym}}{{number_format($sum,2)}}</p>
              <div class="progress progress-bar-success" style="height: 6px">
                <div
                  class="progress-bar"
                  role="progressbar"
                  aria-valuenow="100"
                  aria-valuemin="100"
                  aria-valuemax="100"
                  style="width: 100%"
                ></div>
              </div>
            </div>
            <div class="col-6">
              <p class="mb-50">@if($saved->type == 1) Cycle: @if($saved->cycle == 1) <br>Daily ({{$saved->recurrent}} Days)@elseif($saved->cycle == 7) Weekly ({{$saved->recurrent}} Weeks) @elseif($saved->cycle == 30) Monthly ({{$saved->recurrent}} Months)@endif  @else Mature Date: <br>{!! date(' D d, M Y', strtotime($saved->mature)) !!} <small> {{date('h:i A', strtotime($saved->mature))}}</small>: @endif</p>
              <div class="progress progress-bar-info" style="height: 6px">
                <div
                  class="progress-bar"
                  role="progressbar"
                  aria-valuenow="100"
                  aria-valuemin="100"
                  aria-valuemax="100"
                  style="width: 100%"
                ></div>
              </div>
            </div>
            <div class="col-6">
              <p class="mb-50">@if($saved->type == 1) Recurrent:   @else Total Payment @endif <br>{{$count}} Times</p>
              <div class="progress progress-bar-info" style="height: 6px">
                <div
                  class="progress-bar"
                  role="progressbar"
                  aria-valuenow="100"
                  aria-valuemin="100"
                  aria-valuemax="100"
                  style="width: 100%"
                ></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Avg Sessions Chart Card ends -->



  </div>




   <div class="row match-height">
    <!-- Company Table Card -->
    <div class="col-lg-12 col-12">
      <div class="card card-company-table">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Ref No</th>
                  <th>Date</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
              @foreach($pay as $data)
                <tr>
                  <td>
                    <div class="d-flex align-items-center">
                      <div>
                        <div class="fw-bolder">{{$data->trx}}</div>
                        <div class="font-small-2 text-muted"></div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="fw-bolder align-items-center">

                      <span>{!! date(' D d, M Y', strtotime($saved->expire)) !!}<br><small> {{date('h:i A', strtotime($saved->created_at))}}</small></span>
                    </div>
                  </td>
                  <td class="text-nowrap">
                    <div class="d-flex flex-column">
                      <span class="fw-bolder mb-25">{{$general->cur_sym}}{{number_format($data->amount,2)}}</span>
                      <span class="font-small-2 text-muted">Bal: {{$general->cur_sym}}{{number_format($data->balance,2)}}</span>
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @if(count($pay) < 1)
<div class="demo-spacing-0">
            <div class="alert alert-danger" role="alert">
              <div class="alert-body"><strong>Hello {{Auth::user()->username}}!</strong> You have not made any payment yet. Please proceed to make loan repayment if you have not done so.</div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
    <!--/ Company Table Card -->


</div>

 <!-- Modal -->
              <div
                class="modal fade text-start"
                id="inlineForm"
                tabindex="-1"
                aria-labelledby="myModalLabel33"
                aria-hidden="true"
              >
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title" id="myModalLabel33">Please note that you must have enough fund in your deposit wallet to proceed with this savings</h4>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('user.save.pay',$saved->reference)}}" method="post">
                    @csrf
                      <div class="modal-body">
                        <label>Enter Amount: </label>
                        <div class="mb-1">
                          <input type="number" name="amount" placeholder="{{$general->cur_sym}} 0.00" class="form-control form-control-lg focus" />
                        </div>


                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Make Payment</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>


@endsection
@push('script')
<script>
$(window).on("load",(function(){"use strict";var e,o,r,t,a,s,i,n,l,d,c,h,p="#5e5873",w="#ebe9f1",g="#ebf0f7",u="#b9b9c3",y=document.querySelector("#support-tracker-chart"),m=document.querySelector("#avg-session-chart"),b=document.querySelector("#revenue-report-chart"),f=document.querySelector("#budget-chart"),k=document.querySelector("#goal-overview-chart"),x=document.querySelector("#revenue-chart"),S=document.querySelector("#sales-chart"),z=document.querySelector("#sales-line-chart"),A=document.querySelector("#session-chart"),C=document.querySelector("#customer-chart"),v=document.querySelector("#product-order-chart"),T=document.querySelector("#earnings-donut-chart");e={chart:{height:270,type:"radialBar"},plotOptions:{radialBar:{size:150,offsetY:20,startAngle:-150,endAngle:150,hollow:{size:"65%"},track:{background:"#fff",strokeWidth:"100%"},dataLabels:{name:{offsetY:-5,color:p,fontSize:"1rem"},value:{offsetY:15,color:p,fontSize:"1.714rem"}}}},colors:[window.colors.solid.danger],fill:{type:"gradient",gradient:{shade:"dark",type:"horizontal",shadeIntensity:.5,gradientToColors:[window.colors.solid.primary],inverseColors:!0,opacityFrom:1,opacityTo:1,stops:[0,100]}},stroke:{dashArray:8},series:[83],labels:["Completed Tickets"]},new ApexCharts(y,e).render(),o={chart:{type:"bar",height:200,sparkline:{enabled:!0},toolbar:{show:!1}},states:{hover:{filter:"none"}},colors:[g,g,window.colors.solid.primary,g,g,g],series:[{name:"Sessions",data:[75,125,225,175,125,75,25]}],grid:{show:!1,padding:{left:0,right:0}},plotOptions:{bar:{columnWidth:"45%",distributed:!0,endingShape:"rounded"}},tooltip:{x:{show:!1}},xaxis:{type:"numeric"}},new ApexCharts(m,o).render(),r={chart:{height:230,stacked:!0,type:"bar",toolbar:{show:!1}},plotOptions:{bar:{columnWidth:"17%",endingShape:"rounded"},distributed:!0},colors:[window.colors.solid.primary,window.colors.solid.warning],series:[{name:"Earning",data:[95,177,284,256,105,63,168,218,72]},{name:"Expense",data:[-145,-80,-60,-180,-100,-60,-85,-75,-100]}],dataLabels:{enabled:!1},legend:{show:!1},grid:{padding:{top:-20,bottom:-10},yaxis:{lines:{show:!1}}},xaxis:{categories:["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep"],labels:{style:{colors:u,fontSize:"0.86rem"}},axisTicks:{show:!1},axisBorder:{show:!1}},yaxis:{labels:{style:{colors:u,fontSize:"0.86rem"}}}},new ApexCharts(b,r).render(),t={chart:{height:80,toolbar:{show:!1},zoom:{enabled:!1},type:"line",sparkline:{enabled:!0}},stroke:{curve:"smooth",dashArray:[0,5],width:[2]},colors:[window.colors.solid.primary,"#dcdae3"],series:[{data:[61,48,69,52,60,40,79,60,59,43,62]},{data:[20,10,30,15,23,0,25,15,20,5,27]}],tooltip:{enabled:!1}},new ApexCharts(f,t).render(),a={chart:{height:245,type:"radialBar",sparkline:{enabled:!0},dropShadow:{enabled:!0,blur:3,left:1,top:1,opacity:.1}},colors:["#51e5a8"],plotOptions:{radialBar:{offsetY:-10,startAngle:-150,endAngle:150,hollow:{size:"77%"},track:{background:w,strokeWidth:"50%"},dataLabels:{name:{show:!1},value:{color:p,fontSize:"2.86rem",fontWeight:"600"}}}},fill:{type:"gradient",gradient:{shade:"dark",type:"horizontal",shadeIntensity:.5,gradientToColors:[window.colors.solid.success],inverseColors:!0,opacityFrom:1,opacityTo:1,stops:[0,100]}},series:[{{@number_format($progress,2)}}],stroke:{lineCap:"round"},grid:{padding:{bottom:30}}},new ApexCharts(k,a).render(),s={chart:{height:240,toolbar:{show:!1},zoom:{enabled:!1},type:"line",offsetX:-10},stroke:{curve:"smooth",dashArray:[0,12],width:[4,3]},grid:{borderColor:"#e7eef7"},legend:{show:!1},colors:["#d0ccff",w],fill:{type:"gradient",gradient:{shade:"dark",inverseColors:!1,gradientToColors:[window.colors.solid.primary,w],shadeIntensity:1,type:"horizontal",opacityFrom:1,opacityTo:1,stops:[0,100,100,100]}},markers:{size:0,hover:{size:5}},xaxis:{labels:{style:{colors:u,fontSize:"1rem"}},axisTicks:{show:!1},categories:["01","05","09","13","17","21","26","31"],axisBorder:{show:!1},tickPlacement:"on"},yaxis:{tickAmount:5,labels:{style:{colors:u,fontSize:"1rem"},formatter:function(e){return e>999?(e/1e3).toFixed(0)+"k":e}}},grid:{padding:{top:-20,bottom:-10,left:20}},tooltip:{x:{show:!1}},series:[{name:"This Month",data:[45e3,47e3,44800,47500,45500,48e3,46500,48600]},{name:"Last Month",data:[46e3,48e3,45500,46600,44500,46500,45e3,47e3]}]},new ApexCharts(x,s).render(),i={chart:{height:300,type:"radar",dropShadow:{enabled:!0,blur:8,left:1,top:1,opacity:.2},toolbar:{show:!1},offsetY:5},series:[{name:"Sales",data:[90,50,86,40,100,20]},{name:"Visit",data:[70,75,70,76,20,85]}],stroke:{width:0},colors:[window.colors.solid.primary,window.colors.solid.info],plotOptions:{radar:{polygons:{strokeColors:[w,"transparent","transparent","transparent","transparent","transparent"],connectorColors:"transparent"}}},fill:{type:"gradient",gradient:{shade:"dark",gradientToColors:[window.colors.solid.primary,window.colors.solid.info],shadeIntensity:1,type:"horizontal",opacityFrom:1,opacityTo:1,stops:[0,100,100,100]}},markers:{size:0},legend:{show:!1},labels:["Jan","Feb","Mar","Apr","May","Jun"],dataLabels:{background:{foreColor:[w,w,w,w,w,w]}},yaxis:{show:!1},grid:{show:!1,padding:{bottom:-27}}},new ApexCharts(S,i).render(),n={chart:{height:240,toolbar:{show:!1},zoom:{enabled:!1},type:"line",dropShadow:{enabled:!0,top:18,left:2,blur:5,opacity:.2},offsetX:-10},stroke:{curve:"smooth",width:4},grid:{borderColor:w,padding:{top:-20,bottom:5,left:20}},legend:{show:!1},colors:["#df87f2"],fill:{type:"gradient",gradient:{shade:"dark",inverseColors:!1,gradientToColors:[window.colors.solid.primary],shadeIntensity:1,type:"horizontal",opacityFrom:1,opacityTo:1,stops:[0,100,100,100]}},markers:{size:0,hover:{size:5}},xaxis:{labels:{offsetY:5,style:{colors:u,fontSize:"0.857rem"}},axisTicks:{show:!1},categories:["Jan","Feb","Mar","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec"],axisBorder:{show:!1},tickPlacement:"on"},yaxis:{tickAmount:5,labels:{style:{colors:u,fontSize:"0.857rem"},formatter:function(e){return e>999?(e/1e3).toFixed(1)+"k":e}}},tooltip:{x:{show:!1}},series:[{name:"{{$general->cur_sym}}",data:["{{number_format($jan,2)}}","{{number_format($feb,2)}}","{{number_format($mar,2)}}","{{number_format($apr,2)}}","{{number_format($may,2)}}","{{number_format($jun,2)}}","{{number_format($jul,2)}}","{{number_format($aug,2)}}","{{number_format($sep,2)}}","{{number_format($oct,2)}}","{{number_format($nov,2)}}","{{number_format($dec,2)}}"]}]},new ApexCharts(z,n).render(),l={chart:{type:"donut",height:300,toolbar:{show:!1}},dataLabels:{enabled:!1},series:[58.6,34.9,6.5],legend:{show:!1},comparedResult:[2,-3,8],labels:["Desktop","Mobile","Tablet"],stroke:{width:0},colors:[window.colors.solid.primary,window.colors.solid.warning,window.colors.solid.danger]},new ApexCharts(A,l).render(),d={chart:{type:"pie",height:325,toolbar:{show:!1}},labels:["New","Returning","Referrals"],series:[690,258,149],dataLabels:{enabled:!1},legend:{show:!1},stroke:{width:4},colors:[window.colors.solid.primary,window.colors.solid.warning,window.colors.solid.danger]},new ApexCharts(C,d).render(),c={chart:{height:325,type:"radialBar"},colors:[window.colors.solid.primary,window.colors.solid.warning,window.colors.solid.danger],stroke:{lineCap:"round"},plotOptions:{radialBar:{size:150,hollow:{size:"20%"},track:{strokeWidth:"100%",margin:15},dataLabels:{value:{fontSize:"1rem",colors:p,fontWeight:"500",offsetY:5},total:{show:!0,label:"Total",fontSize:"1.286rem",colors:p,fontWeight:"500",formatter:function(e){return 42459}}}}},series:[70,52,26],labels:["Finished","Pending","Rejected"]},new ApexCharts(v,c).render(),h={chart:{type:"donut",height:120,toolbar:{show:!1}},dataLabels:{enabled:!1},series:[53,16,31],legend:{show:!1},comparedResult:[2,-3,8],labels:["App","Service","Product"],stroke:{width:0},colors:["#28c76f66","#28c76f33",window.colors.solid.success],grid:{padding:{right:-20,bottom:-8,left:-20}},plotOptions:{pie:{startAngle:-10,donut:{labels:{show:!0,name:{offsetY:15},value:{offsetY:-15,formatter:function(e){return parseInt(e)+"%"}},total:{show:!0,offsetY:15,label:"App",formatter:function(e){return"53%"}}}}}},responsive:[{breakpoint:1325,options:{chart:{height:100}}},{breakpoint:1200,options:{chart:{height:120}}},{breakpoint:1065,options:{chart:{height:100}}},{breakpoint:992,options:{chart:{height:120}}}]},new ApexCharts(T,h).render()}));
</script>
<!-- BEGIN: Page JS-->
    <script src="{{ asset($activeTemplateTrue. 'app-assets/js/scripts/cards/card-analytics.min.jsg')}}"></script>
<!-- END: Page JS-->

@endpush
