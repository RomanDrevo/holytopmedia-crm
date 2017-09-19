<div class="navbar-inverse sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
            <li>
                <a href="/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
            </li>


            @can('admin')
                <li>
                    <a href="#"><i class="fa fa-internet-explorer"></i> Admin<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="/admin/users"><i class="fa fa-users"></i> Users</a>
                        </li>
                        <li>
                            <a href="/upload/add-customers"><i class="fa fa-upload"></i> Add customers</a>
                        </li>
                        <li>
                            <a href="/upload/edit-customers"><i class="fa fa-upload"></i> Edit customers</a>
                        </li>

                    </ul>
                </li>
            @endcan

            @can('marketing')

                <li>
                    <a href="#"><i class="fa fa-internet-explorer"></i> Marketing<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="/marketing/campaigns"><i class="fa"></i>Campaigns</a>
                        </li>
                        <li>
                            <a href="/marketing/sms"><i class="fa fa-comments" aria-hidden="true"></i> Send SMS</a>
                        </li>

                    </ul>



            @endcan

            @can('support')
                <li>
                    <a href="#"><i class="fa fa-user-md"></i> Customer Support<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        {{--<li>--}}
                        {{--<a href="http://demo.startlaravel.com/sb-admin-laravel/panels">Panels and Collapsibles</a>--}}
                        {{--</li>--}}
                        <li>
                            <a href="#"><i class="fa"></i>Coming soon...</a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('sales')
                <li>
                    <a href="#"><i class="fa fa-credit-card"></i> Sales<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="/sales/employees"><i class="fa fa-users"></i> Employees</a>
                        </li>
                        <li>
                            <a href="/sales/withdrawals"><i class="fa fa-money"></i> Withdrawals</a>
                        </li>
                        <li>
                            <a href="/sales/deposits"><i class="fa fa-credit-card"></i> Deposits</a>
                        </li>
                        <li>
                            <a href="/sales/tables"><i class="fa fa-desktop"></i> Tables</a>
                        </li>
                        <li>
                            <a href="/sales/settings"><i class="fa fa-cog"></i> Settings</a>
                        </li>
                        @can('admin')
                            <li>
                                <a href="/sales/reports"><i class="fa fa-line-chart"></i> Reports</a>
                            </li>
                        @endcan
                        @if(\Auth::user()->ownTable)
                            <li>
                                <a href="/sales/reports/manager/{{\Auth::user()->ownTable->id}}"><i class="fa fa-line-chart"></i> {{\Auth::user()->ownTable->name}}</a>
                            </li>
                        @endif
                    </ul>
                </li>
            @endcan

            @can('management')
                <li>
                    <a href="#"><i class="fa fa-line-chart"></i> Reports<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <li>
                            <a href="/system/deposits"><i class="fa fa-money"></i> Deposits</a>
                        </li>
                        <li>
                            <a href="/system/withdrawals"><i class="fa fa-credit-card"></i> Withdrawals</a>
                        </li>
                        <li>
                            <a href="/system/employees"><i class="fa fa-users"></i> Employees</a>
                        </li>

                        <li>
                            <a href="/system/settings"><i class="fa fa-cog"></i> Settings</a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('compliance')
                <li>
                    <a href="#"><i class="fa fa-users"></i> Compliance & Risk<span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        @can('compliance_show_customers')
                            <li>
                                <a href="/compliance/customers"><i class="fa fa-user-circle"></i> Customers</a>
                            </li>
                        @endcan

                        @can('compliance_show_pending')
                            <li>
                                <a href="/compliance/pending"><i class="fa fa-user-circle"></i> Pending</a>
                            </li>
                        @endcan


                        @can('compliance_show_alerts')
                            <li>
                                <a href="/compliance/alerts"><i class="fa fa-user-circle"></i> Alerts</a>
                            </li>
                        @endcan

                         {{--@can('compliance_show_alerts')--}}
                            {{--<li>--}}
                                {{--<a href="/compliance/reports"><i class="fa fa-user-circle"></i> Reports</a>--}}
                            {{--</li>--}}
                         {{--@endcan--}}

                         @can('compliance_show_settings')
                            <li>
                                <a href="/compliance/settings"><i class="fa fa-cog"></i> Settings</a>
                            </li>
                         @endcan
                    </ul>
                </li>
            @endcan
        </ul>
    </div>
</div>