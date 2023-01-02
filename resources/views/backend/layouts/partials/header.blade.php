<?php
	$userpin='';
	if (Session::has('user_pin'))
	{
		$userpin = Session::get('user_pin');
	}
?>
<div id="kt_header" class="header header-fixed">
  <!--begin::Container-->
  <div class="container-fluid d-flex align-items-stretch justify-content-between">
    <!--begin::Header Menu Wrapper-->
    <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
    </div>
    <!--end::Header Menu Wrapper-->
    <!--begin::Topbar-->
    <div class="topbar">
      
      <!--end::Chat-->
      <!--begin::Languages-->
      <div class="dropdown">
        <!--begin::Toggle-->
        <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
          <div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2">
            <span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3">{{ $userpin }}</span>
            <span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
              <span class="symbol-label font-size-h5 font-weight-bold">V</span>
            </span>
          </div>
        </div>
        <!--end::Toggle-->
        <!--begin::Dropdown-->
        <div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right">
          <!--begin::Nav-->
          <ul class="navi navi-hover py-4">
            <!--begin::Item-->
            <li class="navi-item">
              <a href="Logout" class="navi-link btn">Sign out</a>
            </li>
          </ul>
          <!--end::Nav-->
        </div>
        <!--end::Dropdown-->
      </div>
    </div>
    <!--end::Topbar-->
  </div>
  <!--end::Container-->
</div>