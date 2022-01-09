@extends('layouts.app_payment')
@section('content')
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet"type="text/css" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!------ Include the above in your HEAD tag ---------->
    @if (session('alert'))
        <div class="alert alert-error">
            <script>
                Swal.fire({
                    title: 'خطأ!',
                    text: 'حدث خطأ في عملية الدفع. يرجى المحاولة مرة أخرى',
                    icon: 'error',
                });
            </script>

        </div>
    @endif
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


                            <div class="col-md-6t">


                                    <p class="font-weight-bold mb-4">Order Details</p>
                                    <p class="mb-1"><span class="text-muted">Order Id: </span> {{ @$invoice->invoice_unique_id }}</p>
                                <p class="mb-1"><span class="text-muted">Date: </span> {{ @$invoice->date }}</p>
                                <p class="mb-1"><span class="text-muted">Phone: </span> {{ @$invoice->customers->mobile }}</p>
                                    <p class="mb-1"><span class="text-muted">Car Make: </span> {{ @$invoice->carmakes->name_en }}</p>
                                    <p class="mb-1"><span class="text-muted">Car Model: </span> {{ @$invoice->carmodel->name_en }}</p>
                                    <p class="mb-1"><span class="text-muted">Car Plate Id: </span> {{ @$invoice->cars->car_plate_id }}</p>
                                    <p class="mb-1"><span class="text-muted">Remarks: </span> {{ @$invoice->remarks }}</p>
                                    <p class="mb-1"><span class="text-muted">Driver: </span> {{ @$invoice->driver->name }}</p>
                                    <p class="mb-1"><span class="text-muted">Date: </span> {{ @$invoice->date }}</p>

                            </div>
                        </div>

                        <div class="row p-5">
                            <div class="col-md-12">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="border-0 text-uppercase small font-weight-bold">ID</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">From</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">To</th>


                                        <th class="border-0 text-uppercase small font-weight-bold">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $invoice->id }}</td>
                                            <td>{{ @$invoice->areafrom->name_en }}</td>
                                            <td>{{ @$invoice->areato->name_en }}</td>
                                            <td>{{ number_format(@$invoice->amount,3) }} KD</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <form action="{{ route('makeOrderPayment') }}" method="post">
                                    @csrf
                                    <input type="hidden" name="order_id" value="{{ $invoice->id }}">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-12 ">
                                            <div class="left-content">
                                                <input type="hidden" name="amount" min="0" max="{{ $invoice->amount }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-12 ">

                                            <div class="right-content">
                                                @if($invoice->payment_status_id != 1 && $invoice->payment_type == \App\Models\Orders::KNET_PAYMENT)
                                                    <input type="submit" name="Pay" class="btn btn-primary" value="{{ __('website.pay_now')}}">
                                                @else
{{--                                                    <button id="cmd">generate PDF</button>--}}
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
                                        <th class="border-0 text-uppercase small font-weight-bold">From</th>
                                        <th class="border-0 text-uppercase small font-weight-bold">To</th>


                                        <th class="border-0 text-uppercase small font-weight-bold">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $invoice->id }}</td>
                                            <td>{{ @$invoice->areafrom->name_em }}</td>
                                            <td>{{ @$invoice->areato->name_em }}</td>
                                            <td>{{ number_format($invoice->amount,3) }} KD</td>
                                        </tr>
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




                            </div>  </div>

                    </div>
                </div>
            </div>
        </div>



    </div>


@endsection
