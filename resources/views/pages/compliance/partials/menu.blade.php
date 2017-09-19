<div class="col-md-3 left_col">
  <div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0;">
      <a href="index.html" class="site_title"><i class="fa fa-users"></i> <span>Compliance CRM</span></a>
    </div>

    <div class="clearfix"></div>

    <!-- menu profile quick info -->
    <div class="profile">
      <div class="profile_info">
        @if(\Auth::check())
        <span>Welcome, {{ \Auth::user()->name }}</span>
        @endif
      </div>
    </div>
    <!-- /menu profile quick info -->
    <div class="clearfix"></div>
    <br />

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
      <div class="menu_section">
        <h3>MENU</h3>
        <ul class="nav side-menu">
          <li><a href="/home"><i class="fa fa-home"></i> Home </a></li>
          <li><a href="/customers"><i class="fa fa-users"></i> Customers </a></li>
          <li><a href="/pending"><i class="fa fa-clock-o"></i> Pending </a></li>
        </ul>
      </div>

    </div>
    <!-- /sidebar menu -->


  </div>
</div>