@extends('layouts.app_payment')
@section('content')
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet"type="text/css" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!------ Include the above in your HEAD tag ---------->

    <div class="container" id="content">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="row p-5">
                            <div class="col-md-6">
                                <img src="/img/logo.png" width="120" height="120">
                            </div>

                            <div class="col-md-6 text-right">
                                <p class="font-weight-bold mb-1">Invoice #{{$invoice->id}}</p>
                                <p class="text-muted">Due to: {{$invoice->created_at}}</p>
                            </div>
                        </div>

                        <hr class="my-5">

                        <div class="row pb-5 p-5">
                            <div class="col-md-6">
                                <p class="font-weight-bold mb-4">Invoice Details</p>
                                <p class="mb-1"><span class="text-muted">Invoice Id: </span> {{ @$invoice->invoice_unique_id }}</p>
                                <p class="mb-1"><span class="text-muted">Customer: </span> {{ @$invoice->customers->name }}</p>
                                <p class="mb-1"><span class="text-muted">Phone: </span> {{ @$invoice->customers->mobile }}</p>
                                 <p class="mb-1"><span class="text-muted">Date: </span> {{ @$invoice->created_at }}</p>

                            </div>

                        </div>

                        <div class="row p-5">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="border-0 text-uppercase small font-weight-bold">ID</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Date</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Phone</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Car Make</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Car Plate ID </th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Driver</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">From</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">To</th>


                                        <th class="border-0 text-uppercase small font-weight-bold">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($invoice->orders as $order  )
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->date }}</td>
                                            <td>{{ @$order->customers->mobile }}</td>
                                            <td>{{ @$order->carmakes->name_en }}</td>
                                            <td>{{ @$order->cars->car_plate_id }}</td>
                                            <td>{{ @$order->driver->name }}</td>
                                            <td>{{ @$order->areafrom->name_en }}</td>
                                            <td>{{ @$order->areato->name_en }}</td>
                                            <td>{{ number_format($order->amount,3) }} KD</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <form action="{{ route('makePayment') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
                                <div class="row">
                                <div class="col-md-6 col-sm-12 ">
                                    <div class="left-content">
                                        <input type="number" name="amount" min="1" max="{{ $invoice->amount-$perviousAmount }}" value="{{ $invoice->amount-$perviousAmount }}" class="form-control">
                                    </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 ">

                                    <div class="right-content">
                                        @if($invoice->payment_status_id != 1)
                                            <input type="submit" name="Pay" class="btn btn-primary" value="{{ __('website.pay_now')}}">
                                        @else
                                            <button id="cmd">generate PDF</button>
                                        @endif
                                    </div>
                                </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <div class="row ">

                            <div class="col-md-12 d-flex flex-row-reverse bg-dark text-white p-4">
                                <div class="py-3 px-5 text-right">
                                    <div class="mb-2">Grand Total</div>
                                    <div class="h2 font-weight-light">{{ number_format($invoice['amount']-($invoice['amount']*$invoice['discount']/100),3) }}KD</div>
                                </div>

                                  <div class="col-md-4 py-3 px-5 text-right">
                                    <div class="mb-2">Remaingin Amount</div>

                                    <div class="h2 font-weight-light">{{ number_format($invoice->amount-$perviousAmount,3) }} KD</div>
                                </div>
                            </div>  </div>

                    </div>
                </div>
            </div>
        </div>



    </div>


    <div class="container" style="display:none"id="hiddencontent">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="row p-5">
                            <div class="col-md-6" style="background-color: gray;">
                                <img src="/img/logo.png">
                            </div>

                            <div class="col-md-6 text-right">
                                <p class="font-weight-bold mb-1">Invoice #{{$invoice->id}}</p>
                                <p class="text-muted">Due to: {{$invoice->created_at}}</p>
                            </div>
                        </div>

                        <hr class="my-5">

                        <div class="row pb-5 p-5">
                            <div class="col-md-6">

                            </div>

                            <div class="col-md-6 text-right">
                                @if($transaction)
                                    @if($transaction['result'] =='CANCELED')
                                        <div class="col-md-12"><p class="p-3 mb-2 bg-danger text-white">{{ __('website.canceled')}}</p></div>
                                    @elseif($transaction['result'] =='CAPTURED')
                                        <div class="col-md-12"><p class="p-3 mb-2 bg-success text-white">{{ __('website.payed')}}</p></div>
                                    @else

                                    @endif
                                @endif
                                @if($transaction)
                                    <p class="font-weight-bold mb-4">Payment Details</p>
                                    <p class="mb-1"><span class="text-muted">Payment ID: </span> {{ $transaction->payment_id }}</p>

                                @endif
                            </div>
                        </div>

                        <div class="row p-5">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="border-0 text-uppercase small font-weight-bold">ID</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">Driver</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">From</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">To</th>


                                        <th class="border-0 text-uppercase small font-weight-bold">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($invoice->orders as $order  )
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ @$order->driver->name }}</td>
                                            <td>{{ @$order->areafrom->name_en }}</td>
                                            <td>{{ @$order->areato->name_en }}</td>
                                            <td>{{ number_format($order->amount,3) }} KD</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="col-md-6 col-sm-12 ">
                                    <div class="right-content">
                                        @if($invoice->payment_status_id != 1)

                                        @else

                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row ">

                            <div class="col-md-12 d-flex flex-row-reverse bg-dark text-white p-4">
                                <div class="py-3 px-5 text-right">
                                    <div class="mb-2">Grand Total</div>
                                    <div class="h2 font-weight-light">{{ number_format($invoice['amount'],3) }}KD</div>
                                </div>

                                <div class="col-md-4 py-3 px-5 text-right">
                                    <div class="mb-2">Discount</div>
                                    <div class="h2 font-weight-light">{{ $invoice['discount'] }}%</div>
                                </div>

                                <div class="col-md-4 py-3 px-5 text-right">
                                    <div class="mb-2">Remaining  amount</div>
                                    <div class="h2 font-weight-light">{{ number_format($invoice->amount-$perviousAmount ) }} KD</div>
                                </div>
                            </div>  </div>

                    </div>
                </div>
            </div>
        </div>



    </div>


@endsection
