@extends(backpack_view('blank'))
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
@php
     if(backpack_user()->hasRole('superadmin')||backpack_user()->hasRole('operator'))
    {
        // ---------------------
        // JUMBOTRON widget demo
        // ---------------------
        // Widget::add([
     //        'type'        => 'jumbotron',
     //        'name' 		  => 'jumbotron',
     //        'wrapperClass'=> 'shadow-xs',
     //        'heading'     => trans('backpack::base.welcome'),
     //        'content'     => trans('backpack::base.use_sidebar'),
     //        'button_link' => backpack_url('logout'),
     //        'button_text' => trans('backpack::base.logout'),
     //    ])->to('before_content')->makeFirst();

        // -------------------------
        // FLUENT SYNTAX for widgets
        // -------------------------
        // Using the progress_white widget
        //
        // Obviously, you should NOT do any big queries directly in the view.
        // In fact, it can be argued that you shouldn't add Widgets from blade files when you
        // need them to show information from the DB.
        //
        // But you do whatever you think it's best. Who am I, your mom?
         $paidOrderCount = App\Models\Orders::where('is_paid',1)->count();
         $notpaidOrderCount = App\Models\Orders::where('is_paid',0)->count();
         $newOrderCount = App\Models\Orders::where('status',2)->count();
        $inprogressOrderCount = App\Models\Orders::where('status',8)->count();
        $awaitingpaymentOrderCount = App\Models\Orders::where('status',5)->count();
        $completedOrderCount = App\Models\Orders::where('status',6)->count();
        $collectedfromDriverOrderCount = App\Models\Orders::where('order_collected',1)->where('status',6)
        ->where('payment_type',\App\Models\Orders::CASH_PAYMENT)->sum('amount');
        $notcollectedfromDriverOrderCount = App\Models\Orders::where('order_collected',0)->where('status',6)
        ->where('payment_type',\App\Models\Orders::CASH_PAYMENT)->sum('amount');


         // notice we use Widget::add() to add widgets to a certain group

        Widget::add()->to('before_content')->type('div')->class('row')->content([
            // notice we use Widget::make() to add widgets as content (not in a group)
            Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->value($newOrderCount)
                ->description(trans('admin.New Order.'))
                ->progress(100*(int)$newOrderCount/1000)
                ,
            // alternatively, to use widgets as content, we can use the same add() method,
            // but we need to use onlyHere() or remove() at the end
            Widget::make()
                ->group('hidden')
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->value($paidOrderCount.'')
                ->description(trans('admin.Paid Orders'))
                ->progress(100*(int)$paidOrderCount/1000)
            // alternatively, you can just push the widget to a "hidden" group
                ->progress(30),
            // both Widget::make() and Widget::add() accept an array as a parameter
            // if you prefer defining your widgets as arrays
             Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-dark',
                'progressClass' => 'progress-bar',
                'value' => $notpaidOrderCount,
                'description' => trans('admin.Not Paid Orders.'),
                'progress' => (int)$notpaidOrderCount/1000,
            ]),
            // both Widget::make() and Widget::add() accept an array as a parameter
            // if you prefer defining your widgets as arrays
            Widget::make()
             ->group('hidden')
             ->class('card border-0 text-white bg-dark')
            ->type('progress')
            ->value($completedOrderCount)
            ->description(trans('admin.Completed Orders .'))
            ->progress(100*(int)$completedOrderCount/1000)
            ->onlyHere()
        ]);
/*
        Widget::add()->to('before_content')->type('div')->class('row')->content([
            // notice we use Widget::make() to add widgets as content (not in a group)
            Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->value($collectedfromDriverOrderCount. ' Kwd')
                ->description('Amount Collected From Driver')
                ->progress(100*(int)$collectedfromDriverOrderCount/1000),
            // alternatively, to use widgets as content, we can use the same add() method,
            // but we need to use onlyHere() or remove() at the end
            Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-success')
                ->progressClass('progress-bar')
                ->value($notcollectedfromDriverOrderCount.' Kwd')
                ->description('Amount With Driver')
                ->progress(100*(int)$notcollectedfromDriverOrderCount/1000),
            // alternatively, you can just push the widget to a "hidden" group
            Widget::make()
                ->group('hidden')
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->value($paidOrderCount.'')
                ->description('Paid Orders')
                ->progress(100*(int)$paidOrderCount/1000)
            // alternatively, you can just push the widget to a "hidden" group
                ->progress(30),
            // both Widget::make() and Widget::add() accept an array as a parameter
            // if you prefer defining your widgets as arrays
             Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-dark',
                'progressClass' => 'progress-bar',
                'value' => $notpaidOrderCount,
                'description' => 'Not Paid Orders.',
                'progress' => (int)$notpaidOrderCount/1000,
            ]),
        ]);
*/
        $widgets['before_content'][] = [
          'type' => 'div',
          'class' => 'row',
          'content' => [ // widgets
                  [
                    'type' => 'chart',
                    'wrapperClass' => 'col-md-6',
                    // 'class' => 'col-md-6',
                    'controller' => \App\Http\Controllers\Admin\Charts\LatestUsersChartController::class,
                    'content' => [
                        'header' => trans('admin.New Orders Past 7 Days'), // optional
                        // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional

                    ]
                ],
                [
                    'type' => 'chart',
                    'wrapperClass' => 'col-md-6',
                    // 'class' => 'col-md-6',
                    'controller' => \App\Http\Controllers\Admin\Charts\NewEntriesChartController::class,
                    'content' => [
                        'header' => trans('admin.Orders'), // optional
                        // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
                    ]
                ],
            ]
        ];
        $widgets['before_content'][] = [
          'type' => 'div',
          'class' => 'row',
          'content' => [ // widgets
                  [
                    'type' => 'chart',
                    'wrapperClass' => 'col-md-6',
                    // 'class' => 'col-md-6',
                    'controller' => \App\Http\Controllers\Admin\Charts\CashDriverChartController::class,
                    'content' => [
                        'header' => trans('admin.Cash With Driver'), // optional
                        // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional

                    ]
                ],
                [
                    'type' => 'chart',
                    'wrapperClass' => 'col-md-6',
                    // 'class' => 'col-md-6',
                    'controller' => \App\Http\Controllers\Admin\Charts\ComissionChartController::class,
                    'content' => [
                        'header' => trans('admin.Comission'), // optional
                        // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
                    ]
                ],
            ]
        ];
        Widget::add()->to('before_content')->type('div')->class('row')->content([
            // notice we use Widget::make() to add widgets as content (not in a group)
            Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-primary neworder')
                ->progressClass('progress-bar')
                ->description(trans('admin.New Order.')),

            // alternatively, to use widgets as content, we can use the same add() method,
            // but we need to use onlyHere() or remove() at the end
            Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-success newcustomer')
                ->progressClass('progress-bar')
                ->description(trans('admin.New Customer .'))
                ->onlyHere(),
            // alternatively, you can just push the widget to a "hidden" group
            Widget::make()
                ->group('hidden')
                ->type('progress')
                ->class('card border-0 text-white bg-warning newdriver')
                ->progressClass('progress-bar')
                ->description(trans('admin.New Driver')),

            // both Widget::make() and Widget::add() accept an array as a parameter
            // if you prefer defining your widgets as arrays
            Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-dark newcar',
                'progressClass' => 'progress-bar',
                'description' => trans('admin.New Car '),
            ]),
        ]);
        }
        else if(backpack_user()->hasRole('driver'))
{

    $totalOrderToday = \App\Models\Orders::where('driver_id',backpack_user()->id)->whereDate('date', \Carbon\Carbon::today())->sum('amount');
    $totalNotPaidComission = \App\Models\Orders::where('driver_id',backpack_user()->id)->whereDate('comission_paid', 0)->sum('comission');
    $totalCashWith = \App\Models\Orders::where('driver_id',backpack_user()->id)->where('order_collected',0)->where('status',6)
        ->where('payment_type',\App\Models\Orders::CASH_PAYMENT)->sum('amount');
      Widget::add()->to('before_content')->type('div')->class('row')->content([
            // notice we use Widget::make() to add widgets as content (not in a group)
            Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->value($totalOrderToday)
                ->description(trans('admin.Total Order Today'))

                ,
            // alternatively, to use widgets as content, we can use the same add() method,
            // but we need to use onlyHere() or remove() at the end
            Widget::make()
                ->group('hidden')
                ->type('progress')
                ->class('card border-0 text-white bg-warning')
                ->value($totalNotPaidComission.'')
                ->description(trans('admin.Not Paid Comission')),
             // alternatively, you can just push the widget to a "hidden" group
            // both Widget::make() and Widget::add() accept an array as a parameter
            // if you prefer defining your widgets as arrays
             Widget::make([
                'type' => 'progress',
                'class'=> 'card border-0 text-white bg-dark',
                'progressClass' => 'progress-bar',
                'value' => $totalCashWith,
                'description' => trans('admin.Cash With Driver'),
            ]),
                Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-primary neworder')
                ->progressClass('progress-bar')
                ->description(trans('admin.New Order.')),

        ]);
}
        else
{
          Widget::add()->to('before_content')->type('div')->class('row')->content([
                Widget::make()
                ->type('progress')
                ->class('card border-0 text-white bg-primary')
                ->progressClass('progress-bar')
                ->description(trans('admin.Please set role for this user to proceed')),
]);
}
@endphp
 @section('content')
	{{-- In case widgets have been added to a 'content' group, show those widgets. --}}
	@include(backpack_view('inc.widgets'), [ 'widgets' => app('widgets')->where('group', 'content')->toArray() ])
     <script>
         $( ".neworder" ).click(function() {
             window.location.href = "/admin/orders/create";
          });
         $( ".newcustomer" ).click(function() {
             window.location.href = "/admin/customers/create";
         });
         $( ".newdriver" ).click(function() {
             window.location.href = "/admin/user/create";
         });
         $( ".newcar" ).click(function() {
             window.location.href = "/admin/cars/create";
         });

     </script>
     <style>
         .neworder, .newcustomer , .newdriver , .newcar{cursor: pointer;}
     </style>
@endsection
