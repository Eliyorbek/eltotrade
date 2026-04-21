 <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="{{route('reports.index')}}" class="b-brand">
                    <img src="/namelogo.png" width="220" height="80" style="padding: 0 !important; margin: 0 !important;" alt="" class="logo logo-lg" />
                    <img src="/letterlogo.png" alt="" class="logo logo-sm" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="{{route('reports.index')}}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span class="nxl-mtext">Dashboards</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>

                    </li>

                    @can('users.view')
                        <li class="nxl-hasmenu">
                            <a href="{{route('employees.index')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">Xodimlar</span><span class="nxl-arrow"></span>
                            </a>
                        </li>
                    @endcan


                    <li class="nxl-hasmenu">
                        <a href="{{route('categories.index')}}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-grid"></i></span>
                            <span class="nxl-mtext">Kategoriyalar</span><span class="nxl-arrow"></span>
                        </a>
                    </li>

                    <li class="nxl-hasmenu">
                        <a href="{{route('products.index')}}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-package"></i></span>
                            <span class="nxl-mtext">Mahsulotlar</span><span class="nxl-arrow"></span>
                        </a>
                    </li>

                    <li class="nxl-hasmenu">
                        <a href="{{route('sales.index')}}" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-package"></i></span>
                            <span class="nxl-mtext">Sotish</span><span class="nxl-arrow"></span>
                        </a>
                    </li>

                    @can('users.view')
                        <li class="nxl-hasmenu">
                            <a href="{{route('reports.index')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-bar-chart-2"></i></span>
                                <span class="nxl-mtext">Hisobotlar</span><span class="nxl-arrow"></span>
                            </a>
                        </li>
                    @endcan

                    @can('users.view')
                        <li class="nxl-hasmenu">
                            <a href="{{route('warehouse.index')}}" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-truck"></i></span>
                                <span class="nxl-mtext">Ombor</span><span class="nxl-arrow"></span>
                            </a>
                        </li>
                    @endcan

                </ul>

            </div>
        </div>
    </nav>
