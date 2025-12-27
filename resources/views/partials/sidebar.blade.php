  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <!-- Brand Logo -->
      <a href="{{ route('app.dashboard') }}" class="brand-link">
          <img src="{{ asset('logo.png') }}"
              alt="Store Logo" class="brand-image  elevation-3" style="opacity: .8">
          <span
              class="brand-text font-weight-light">{{ app(App\Settings\StoreSettings::class)->store_name ?: 'Storeify' }}</span>
      </a>

      <!-- Sidebar -->
      <div class="sidebar">
          <!-- Sidebar Menu -->
          <nav class="mt-2">
              <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                  data-accordion="false">
                  <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                  <li class="nav-item">
                      <a href="{{ route('app.dashboard') }}" class="nav-link">
                          <i class="nav-icon fas fa-tachometer-alt"></i>
                          <p>Dashboard</p>
                      </a>
                  </li>
                  @can('read-users')
                      <li class="nav-item has-treeview">
                          <a href="#" class="nav-link">
                              <i class="nav-icon fas fa-lock"></i>
                              <p>
                                  Authentication
                                  <i class="right fas fa-angle-left"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('app.users.index') }}" class="nav-link">
                                      <i class="fa fa-users nav-icon"></i>
                                      <p>Users</p>
                                  </a>
                              </li>
                              @can('read-roles')
                              <li class="nav-item">
                                  <a href="{{ route('app.roles.index') }}" class="nav-link">
                                      <i class="fa fa-universal-access nav-icon"></i>
                                      <p>Roles</p>
                                  </a>
                              </li>
                              @endcan
                          </ul>
                      </li>
                  @endcan
                  @can('read-products')
                      <li class="nav-item">
                          <a href="{{ route('app.products.index') }}" class="nav-link">
                              <i class="nav-icon fas fa-car"></i>
                              <p>Vehicle Management</p>
                          </a>
                      </li>
                  @endcan
                  <li class="nav-item has-treeview">
                      <a href="#" class="nav-link">
                          <i class="nav-icon fas fa-shopping-cart"></i>
                          <p>
                              Sales Management
                              <i class="fas fa-angle-left right"></i>
                          </p>
                      </a>
                      <ul class="nav nav-treeview">
                          <li class="nav-item">
                              <a href="{{ route('app.sales.index') }}" class="nav-link">
                                  <i class="fa fa-list nav-icon"></i>
                                  <p>Sales List</p>
                              </a>
                          </li>
                          <li class="nav-item">
                              <a href="{{ route('app.sales.create') }}" class="nav-link">
                                  <i class="fa fa-plus-circle nav-icon"></i>
                                  <p>New Sale</p>
                              </a>
                          </li>
                      </ul>
                  </li>
                  @can('read-reports')
                      <li class="nav-item has-treeview">
                          <a href="#" class="nav-link">
                              <i class="nav-icon fas fa-chart-pie"></i>
                              <p>
                                  Reports
                                  <i class="fas fa-angle-left right"></i>
                              </p>
                          </a>
                          <ul class="nav nav-treeview">
                              <li class="nav-item">
                                  <a href="{{ route('app.general.report') }}" class="nav-link">
                                      <i class="fa fa-table nav-icon"></i>
                                      <p>General Report</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('app.endofDay.report') }}" class="nav-link">
                                      <i class="fa fa-calendar nav-icon"></i>
                                      <p>End of Day Report</p>
                                  </a>
                              </li>
                              <li class="nav-item">
                                  <a href="{{ route('app.custom.report.view') }}" class="nav-link">
                                      <i class="fa fa-clock nav-icon"></i>
                                      <p>Custom Report</p>
                                  </a>
                              </li>
                          </ul>
                      </li>
                  @endcan
              </ul>
          </nav>
          <!-- /.sidebar-menu -->
      </div>
      <!-- /.sidebar -->
  </aside>
