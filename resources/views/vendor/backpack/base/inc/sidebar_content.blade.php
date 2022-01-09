<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="nav-icon la la-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>



@role('superadmin')

<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown">
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>{{ trans('admin.Users') }}</span></a></li>

{{--<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>--}}
{{--  <ul class="nav-dropdown-items">--}}
{{--    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>Roles</span></a></li>--}}
{{--    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>--}}
{{--  </ul>--}}
</li>
@if(backpack_user()->hasPermissionTo('manage orders'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('orders') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Orders') }}</span></a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('invoices') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Genreate Invoice') }}</span></a></li>
@endif
@if(backpack_user()->hasPermissionTo('manage comissions'))
  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('xeroinvoices') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Invoices') }}</span></a></li>

  <li class="nav-item"><a class="nav-link" href="{{ backpack_url('comissions') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Comissions') }}</span></a></li>
@endif
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i> {{ trans('admin.Configurations') }}</a>
    <ul class="nav-dropdown-items">
{{--      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>File manager</span></a></li>--}}
{{--      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('log') }}"><i class="nav-icon la la-terminal"></i> <span>Logs</span></a></li>--}}
      @if(backpack_user()->hasPermissionTo('manage orderscollected'))
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('orderscollected') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Cash With Driver') }}</span></a></li>
      @endif

      @if(backpack_user()->hasPermissionTo('manage areas'))
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('areas') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Areas') }}</span></a></li>
      @endif
      @if(backpack_user()->hasPermissionTo('manage orderstatus'))
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('requeststatus') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Order Status') }}</span></a></li>
      @endif
      @if(backpack_user()->hasPermissionTo('manage cartypes'))
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cartypes') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Types') }}</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('carmakes') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Makes') }}</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('carmodel') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Model') }}</span></a></li>
      @endif
      @if(backpack_user()->hasPermissionTo('manage cars'))
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cars') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Plate ID') }}</span></a></li>
      @endif
      @if(backpack_user()->hasPermissionTo('manage customers'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('missingxero') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Syncing Xero') }}</span></a></li>

            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('customertypes') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Customer types') }}</span></a></li>
      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('customers') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Customers') }}</span></a></li>
      @endif
    </ul>
</li>
@endrole
@role('operator')

<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown">
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>{{ trans('admin.Users') }}</span></a></li>

{{--<a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>--}}
{{--  <ul class="nav-dropdown-items">--}}
{{--    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>Roles</span></a></li>--}}
{{--    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>--}}
{{--  </ul>--}}
</li>

@if(backpack_user()->hasPermissionTo('manage orders'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('orders') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Orders') }}</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('invoices') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Invoices') }}</span></a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('xeroinvoices') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Xero Invoices') }}</span></a></li>

@endif
@if(backpack_user()->hasPermissionTo('manage comissions'))

    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('comissions') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Comissions') }}</span></a></li>
@endif
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cogs"></i> {{ trans('admin.Configurations') }}</a>
    <ul class="nav-dropdown-items">
        {{--      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>File manager</span></a></li>--}}
        {{--      <li class="nav-item"><a class="nav-link" href="{{ backpack_url('log') }}"><i class="nav-icon la la-terminal"></i> <span>Logs</span></a></li>--}}
        @if(backpack_user()->hasPermissionTo('manage orderscollected'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('orderscollected') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Cash With Driver') }}</span></a></li>
        @endif

        @if(backpack_user()->hasPermissionTo('manage areas'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('areas') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Areas') }}</span></a></li>
        @endif
        @if(backpack_user()->hasPermissionTo('manage orderstatus'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('requeststatus') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Order Status') }}</span></a></li>
        @endif
        @if(backpack_user()->hasPermissionTo('manage cartypes'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cartypes') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Types') }}</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('carmakes') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Makes') }}</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('carmodel') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Model') }}</span></a></li>
        @endif
        @if(backpack_user()->hasPermissionTo('manage cars'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cars') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Car Plate ID') }}</span></a></li>
        @endif
        @if(backpack_user()->hasPermissionTo('manage customers'))
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('missingxero') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Syncing Xero') }}</span></a></li>

            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('customertypes') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Customer types') }}</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ backpack_url('customers') }}"><i class="nav-icon la la-cog"></i> <span>{{ trans('admin.Customers') }}</span></a></li>
        @endif
    </ul>
</li>
@endrole
@role('driver')

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('driversorders') }}"><i class="nav-icon la la-key"></i> <span>{{ trans('admin.Orders') }}</span></a></li>

@endrole

 {{--<li class="nav-title">Demo Entities</li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('monster') }}"><i class="nav-icon la la-optin-monster"></i> <span>Monsters</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('icon') }}"><i class="nav-icon la la-info-circle"></i> <span>Icons</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('product') }}"><i class="nav-icon la la-shopping-cart"></i> <span>Products</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('fluent-monster') }}"><i class="nav-icon la la-pastafarianism"></i> <span>Fluent Monsters</span></a></li>--}}
{{--<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dummy') }}"><i class="nav-icon la la-poo"></i> <span>Dummies</span></a></li>--}}
