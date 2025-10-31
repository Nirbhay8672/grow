<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">


        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Theme CSS from /html -->
        <link rel="stylesheet" href="/assets/vendor_assets/css/bootstrap/bootstrap.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/MarkerCluster.Default.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/MarkerCluster.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/daterangepicker.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/fontawesome.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/footable.standalone.min.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/fullcalendar@5.2.0.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/jquery-jvectormap-2.0.5.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/jquery.mCustomScrollbar.min.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/leaflet.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/line-awesome.min.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/magnific-popup.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/select2.min.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/slick.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/star-rating-svg.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/trumbowyg.min.css">
        <link rel="stylesheet" href="/assets/vendor_assets/css/wickedpicker.min.css">
        <link rel="stylesheet" href="/style.css">

        @vite(['resources/js/app.ts'])
        @inertiaHead
    </head>
    @php($isAuth = Request::is('login','register','password/*','two-factor-challenge','email/verify','user/confirm-password'))
    <body class="{{ $isAuth ? '' : 'layout-light side-menu overlayScroll' }}">
    @if($isAuth)
        @inertia
    @else
    <div class="mobile-search">
        <form class="search-form">
            <span data-feather="search"></span>
            <input class="form-control me-sm-2 box-shadow-none" type="text" placeholder="Search...">
        </form>
    </div>

    <div class="mobile-author-actions"></div>
    <header class="header-top">
        <nav class="navbar navbar-light">
            <div class="navbar-left">
                <a href="" class="sidebar-toggle">
                    <img class="svg" src="/img/svg/bars.svg" alt="img"></a>
                <a class="navbar-brand" href="/dashboard"><img src="https://growedgerealty.com/assets/images/logo/logo_1.png" alt="logo" class="img-fluid"></a>
                <form action="../index.html" class="search-form">
                    <span data-feather="search"></span>
                    <input class="form-control me-sm-2 box-shadow-none" type="text" placeholder="Search...">
                </form>
                <div class="top-menu">

                    <div class="strikingDash-top-menu position-relative">
                        <ul>
                            <li class="has-subMenu">
                                <a href="#" class="">Dashboard</a>
                                <ul class="subMenu">
                                    <li>
                                        <a class="" href="index.html">Social Media</a>
                                    </li>
                                    <li>
                                        <a class="" href="business.html">FineTech /
                                            Business</a>
                                    </li>
                                    <li>
                                        <a class="" href="performance.html">Site
                                            Performance</a>
                                    </li>
                                    <li>
                                        <a class="" href="ecommerce.html">Ecommerce</a>
                                    </li>
                                    <li>
                                        <a class="" href="crm.html">
                                            CRM</a>
                                    </li>
                                    <li>
                                        <a class="" href="sales.html">
                                            Sales Performance</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-subMenu">
                                <a href="#" class="">Layouts</a>
                                <ul class="subMenu">
                                    <li class="l_sidebar">
                                        <a href="#" data-layout="light">Light Mode</a>
                                    </li>
                                    <li class="l_sidebar">
                                        <a href="#" data-layout="dark">Dark Mode</a>
                                    </li>
                                    <li class="l_navbar">
                                        <a href="#" data-layout="top">Top Menu</a>
                                    </li>
                                    <li class="l_navbar">
                                        <a href="#" data-layout="side">Side Menu</a>
                                    </li>
                                    <li class="layout">
                                        <a href="https://demo.dashboardmarket.com/rtl">RTL</a>
                                    </li>
                                    <li class="layout">
                                        <a href="https://demo.dashboardmarket.com/ltr">LTR</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-subMenu">
                                <a href="#" class="">Apps</a>
                                <ul class="subMenu">
                                    <li>
                                        <a href="chat.html" class="">
                                            <span data-feather="message-square" class="nav-icon"></span>
                                            <span class="menu-text">Chat</span>
                                        </a>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="shopping-cart" class="nav-icon"></span>
                                            <span class="menu-text">eCommerce</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a href="products.html" class="">Products</a>
                                            </li>
                                            <li>
                                                <a href="product-details.html" class="">Product Details</a>
                                            </li>
                                            <li>
                                                <a href="add-product.html" class="">Product
                                                    Add</a>
                                            </li>
                                            <li>
                                                <a href="" class="">Product Edit</a>
                                            </li>
                                            <li>
                                                <a href="cart.html" class="">Cart</a>
                                            </li>
                                            <li>
                                                <a href="order.html" class="">Orders</a>
                                            </li>
                                            <li>
                                                <a href="sellers.html" class="">Sellers</a>
                                            </li>
                                            <li>
                                                <a href="invoice.html" class="">Invoices</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="mail" class="nav-icon"></span>
                                            <span class="menu-text">Email</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a href="inbox.html" class="">Inbox</a>
                                            </li>
                                            <li>
                                                <a href="read-email.html" class="">Read
                                                    Email</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="chat.html" class="">
                                            <span data-feather="bookmark" class="nav-icon"></span>
                                            <span class="menu-text">Note</span>
                                        </a>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="user" class="nav-icon"></span>
                                            <span class="menu-text">Profile</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a href="profile.html" class="">Profile</a>
                                            </li>
                                            <li>
                                                <a href="profile-setting.html" class="">Profile Settings</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="user-check" class="nav-icon"></span>
                                            <span class="menu-text">Contact</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="contact-1.html">Contact 1</a>
                                            </li>
                                            <li>
                                                <a class="" href="contact-2.html">Contact 2</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="chat.html" class="">
                                            <span data-feather="activity" class="nav-icon"></span>
                                            <span class="menu-text">To-Do</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="https://demo.dashboardmarket.com/strikingdash-html/kanban.html" class="">
                                            <span data-feather="columns" class="nav-icon"></span>
                                            <span class="menu-text">Kanban Board</span>
                                        </a>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="repeat" class="nav-icon"></span>
                                            <span class="menu-text">Import & Export</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="import.html">Import</a>
                                            </li>
                                            <li>
                                                <a class="" href="export.html">Export</a>
                                            </li>
                                            <li>
                                                <a class="" href="export-selected.html">Export Selected
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="file-manager.html" class="">
                                            <span data-feather="file" class="nav-icon"></span>
                                            <span class="menu-text">File Manager</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="task-app.html" class="">
                                            <span data-feather="clipboard" class="nav-icon"></span>
                                            <span class="menu-text">Task App</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>



                            <li class="has-subMenu">
                                <a href="#" class="">Crud</a>
                                <ul class="subMenu">
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="shopping-cart" class="nav-icon"></span>
                                            <span class="menu-text">Firestore Crud</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="firestore.html">View All</a>
                                            </li>
                                            <li>
                                                <a class="" href="firestore-add.html">Add
                                                    New</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>


                            <li class="mega-item has-subMenu">
                                <a href="#" class="active">Pages</a>
                                <ul class="megaMenu-wrapper megaMenu-small">
                                    <li>
                                        <ul>
                                            <li>
                                                <a href="projects.html" class="">Project</a>
                                            </li>
                                            <li>
                                                <a href="application-ui.html" class="">Project Details</a>
                                            </li>
                                            <li>
                                                <a href="create.html" class="">Create
                                                    Project</a>
                                            </li>
                                            <li>
                                                <a href="users-card.html" class="">Team</a>
                                            </li>
                                            <li>
                                                <a href="users-card2.html" class="">Users</a>
                                            </li>
                                            <li>
                                                <a href="user-info.html" class="">Users
                                                    Info</a>
                                            </li>
                                            <li>
                                                <a href="users-list.html" class="">Users
                                                    List</a>
                                            </li>
                                            <li>
                                                <a href="users-group.html" class="">Users
                                                    Group</a>
                                            </li>
                                            <li>
                                                <a href="banner.html" class="">
                                                    <span class="menu-text">Banners</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="testimonial.html" class="">
                                                    <span class="menu-text">Testimonial</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="support.html" class="">
                                                    <span class="menu-text">Support Center</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="dynamic-table.html" class="">
                                                    <span class="menu-text">Dynamic Table</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <ul>
                                            <li>
                                                <a href="users-datatable.html" class="">Users
                                                    Table</a>
                                            </li>
                                            <li>
                                                <a href="gallery.html" class="">Gallery 1</a>
                                            </li>
                                            <li>
                                                <a href="gallery2.html" class="">Gallery 2</a>
                                            </li>
                                            <li>
                                                <a href="pricing.html" class="">Pricing</a>
                                            </li>
                                            <li>
                                                <a href="faq.html" class="">FAQ's</a>
                                            </li>
                                            <li>
                                                <a href="search.html" class="">Search
                                                    Results</a>
                                            </li>
                                            <li>
                                                <a href="maintenance.html" class="">Coming
                                                    Soon</a>
                                            </li>
                                            <li>
                                                <a href="404.html" class="">404</a>
                                            </li>
                                            <li>
                                                <a href="maintenance.html" class="">
                                                    <span class="menu-text">Maintenance</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="login.html" class="">
                                                    <span class="menu-text">Log In</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="sign-up.html" class="">
                                                    <span class="menu-text">Sign Up</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="blank.html" class=" active">
                                                    <span class="menu-text">Blank</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="mega-item has-subMenu">
                                <a href="#" class="">Components</a>
                                <ul class="megaMenu-wrapper megaMenu-wide">
                                    <li>
                                        <span class="mega-title">Components</span>
                                        <ul>
                                            <li>
                                                <a class="" href="alert.html">Alert</a>
                                            </li>
                                            <li>
                                                <a class="" href="avatar.html">Avatar</a>
                                            </li>
                                            <li>
                                                <a class="" href="badge.html">Badge</a>
                                            </li>
                                            <li>
                                                <a class="" href="breadcrumbs.html">Breadcrumb</a>
                                            </li>
                                            <li>
                                                <a class="" href="buttons.html">Button</a>
                                            </li>
                                            <li>
                                                <a class="" href="cards.html">Cards</a>
                                            </li>
                                            <li>
                                                <a class="" href="carousel.html">Carousel</a>
                                            </li>
                                            <li>
                                                <a class="" href="checkbox.html">Checkbox</a>
                                            </li>
                                            <li>
                                                <a class="" href="collapse.html">Collapse</a>
                                            </li>
                                            <li>
                                                <a class="" href="comments.html">Comments</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <span class="mega-title">Components</span>
                                        <ul>
                                            <li>
                                                <a class="" href="dashboard-base.html">Dashboard
                                                    Base</a>
                                            </li>
                                            <li>
                                                <a class="" href="date-picker.html">DatePicker</a>
                                            </li>
                                            <li>
                                                <a class="" href="drawer.html">Drawer</a>
                                            </li>
                                            <li>
                                                <a class="" href="drag-drop.html">Drag &
                                                    Drop</a>
                                            </li>
                                            <li>
                                                <a class="" href="dropdown.html">Dropdown</a>
                                            </li>
                                            <li>
                                                <a class="" href="empty.html">Empty</a>
                                            </li>
                                            <li>
                                                <a class="" href="input.html">Input</a>
                                            </li>
                                            <li>
                                                <a class="" href="list.html">List</a>
                                            </li>
                                            <li>
                                                <a class="" href="menu.html">Menu</a>
                                            </li>
                                            <li>
                                                <a class="" href="message.html">Message</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <span class="mega-title">Components</span>
                                        <ul>
                                            <li>
                                                <a class="" href="modal.html">Modals</a>
                                            </li>
                                            <li>
                                                <a class="" href="notifications.html">Notification</a>
                                            </li>
                                            <li>
                                                <a class="" href="page-header.html">Page
                                                    Headers</a>
                                            </li>
                                            <li>
                                                <a class="" href="pagination.html">Paginations</a>
                                            </li>
                                            <li>
                                                <a class="" href="progressbar.html">Progress</a>
                                            </li>
                                            <li>
                                                <a class="" href="radio.html">Radio</a>
                                            </li>
                                            <li>
                                                <a class="" href="rate.html">Rate</a>
                                            </li>
                                            <li>
                                                <a class="" href="result.html">Result</a>
                                            </li>
                                            <li>
                                                <a class="" href="select.html">Select</a>
                                            </li>
                                            <li>
                                                <a class="" href="skeleton.html">Skeleton</a>
                                            </li>
                                            <li>
                                                <a class="" href="time-picker.html">Timepicker</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <span class="mega-title">Components</span>
                                        <ul>
                                            <li>
                                                <a class="" href="slider.html">Slider</a>
                                            </li>
                                            <li>
                                                <a class="" href="spin.html">Spinner</a>
                                            </li>
                                            <li>
                                                <a class="" href="statistics.html">Statistic</a>
                                            </li>
                                            <li>
                                                <a class="" href="steps.html">Steps</a>
                                            </li>
                                            <li>
                                                <a class="" href="switch.html">Switch</a>
                                            </li>
                                            <li>
                                                <a class="" href="tab.html">Tabs</a>
                                            </li>
                                            <li>
                                                <a class="" href="tag.html">Tags</a>
                                            </li>
                                            <li>
                                                <a class="" href="timeline.html">Timeline</a>
                                            </li>
                                            <li>
                                                <a class="" href="timeline-2.html">Timeline
                                                    2</a>
                                            </li>
                                            <li>
                                                <a class="" href="timeline-3.html">Timeline
                                                    3</a>
                                            </li>
                                            <li>
                                                <a class="" href="uploads.html">Upload</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-subMenu">
                                <a href="#" class="">Features</a>
                                <ul class="subMenu">
                                    <li>
                                        <a href="editors.html" class="">
                                            <span data-feather="edit" class="nav-icon"></span>
                                            <span class="menu-text">Editors</span>
                                        </a>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="grid" class="nav-icon"></span>
                                            <span class="menu-text">Icons</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a href="feather.html" class="">Feather icons
                                                    (svg)</a>
                                            </li>
                                            <li>
                                                <a href="fontawesome.html" class="">Font
                                                    Awesome</a>
                                            </li>
                                            <li>
                                                <a href="lineawesome.html" class="">Line
                                                    Awesome</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="bar-chart-2" class="nav-icon"></span>
                                            <span class="menu-text">Charts</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="charts.html">Chart JS</a>
                                            </li>
                                            <li>
                                                <a class="" href="google-chart.html">Google
                                                    Charts</a>
                                            </li>
                                            <li>
                                                <a class="" href="peity-chart.html">Peity
                                                    Charts</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="disc" class="nav-icon"></span>
                                            <span class="menu-text">Froms</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="form.html">Basics</a>
                                            </li>
                                            <li>
                                                <a class="" href="form-layouts.html">Layouts</a>
                                            </li>
                                            <li>
                                                <a class="" href="form-elements.html">Elements</a>
                                            </li>
                                            <li>
                                                <a class="" href="form-components.html">Components</a>
                                            </li>
                                            <li>
                                                <a class="" href="form-validations.html">Validations</a>
                                            </li>
                                        </ul>
                                    </li>



                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="map" class="nav-icon"></span>
                                            <span class="menu-text">Maps</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a href="google-map.html" class="">Google
                                                    Maps</a>
                                            </li>
                                            <li>
                                                <a href="leaflet-map.html" class="">Leaflet
                                                    Maps</a>
                                            </li>
                                            <li>
                                                <a href="vector-map.html" class="">Vector
                                                    Maps</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="server" class="nav-icon"></span>
                                            <span class="menu-text">Widget</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="widget-charts.html">Chart</a>
                                            </li>
                                            <li>
                                                <a class="" href="widget-mixed.html">Mixed</a>
                                            </li>
                                            <li>
                                                <a class="" href="widget-card.html">Card</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="square" class="nav-icon"></span>
                                            <span class="menu-text">Wizards</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a href="checkout-wizard6.html" class="">Wizard
                                                    1</a>
                                            </li>
                                            <li>
                                                <a href="checkout-wizard7.html" class="">Wizard
                                                    2</a>
                                            </li>
                                            <li>
                                                <a href="checkout-wizard8.html" class="">Wizard
                                                    3</a>
                                            </li>
                                            <li>
                                                <a href="checkout-wizard9.html" class="">Wizard
                                                    4</a>
                                            </li>
                                            <li>
                                                <a href="checkout-wizard10.html" class="">Wizard
                                                    5</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="has-subMenu-left">
                                        <a href="#" class="">
                                            <span data-feather="book" class="nav-icon"></span>
                                            <span class="menu-text">Knowledge Base</span>
                                        </a>
                                        <ul class="subMenu">
                                            <li>
                                                <a class="" href="knowledgebase.html">Knowledge
                                                    Base</a>
                                            </li>
                                            <li>
                                                <a class="" href="knowledgebase-2.html">All
                                                    Article</a>
                                            </li>

                                            <li>
                                                <a class="" href="knowledgebase-3.html">Single Article</a>
                                            </li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <!-- ends: navbar-left -->

            <div class="navbar-right">
                <ul class="navbar-right__menu">
                    <li class="nav-search d-none">
                        <a href="#" class="search-toggle">
                            <i class="la la-search"></i>
                            <i class="la la-times"></i>
                        </a>
                        <form action="../index.html" class="search-form-topMenu">
                            <span class="search-icon" data-feather="search"></span>
                            <input class="form-control me-sm-2 box-shadow-none" type="text" placeholder="Search...">
                        </form>
                    </li>
                    <li class="nav-message">
                        <div class="dropdown-custom">
                            <a href="javascript:;" class="nav-item-toggle">
                                <span data-feather="mail"></span></a>
                            <div class="dropdown-wrapper">
                                <h2 class="dropdown-wrapper__title">Messages <span class="badge-circle badge-success ms-1">2</span></h2>
                                <ul>
                                    <li class="author-online has-new-message">
                                        <div class="user-avater">
                                            <img src="/img/team-1.png" alt="">
                                        </div>
                                        <div class="user-message">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">Web Design</a>
                                                <span class="time-posted">3 hrs ago</span>
                                            </p>
                                            <p>
                                                <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum
                                                    dolor amet cosec Lorem ipsum</span>
                                                <span class="msg-count badge-circle badge-success badge-sm">1</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="author-offline has-new-message">
                                        <div class="user-avater">
                                            <img src="/img/team-1.png" alt="">
                                        </div>
                                        <div class="user-message">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">Web Design</a>
                                                <span class="time-posted">3 hrs ago</span>
                                            </p>
                                            <p>
                                                <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum
                                                    dolor amet cosec Lorem ipsum</span>
                                                <span class="msg-count badge-circle badge-success badge-sm">1</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="author-online has-new-message">
                                        <div class="user-avater">
                                            <img src="/img/team-1.png" alt="">
                                        </div>
                                        <div class="user-message">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">Web Design</a>
                                                <span class="time-posted">3 hrs ago</span>
                                            </p>
                                            <p>
                                                <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum
                                                    dolor amet cosec Lorem ipsum</span>
                                                <span class="msg-count badge-circle badge-success badge-sm">1</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="author-offline">
                                        <div class="user-avater">
                                            <img src="/img/team-1.png" alt="">
                                        </div>
                                        <div class="user-message">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">Web Design</a>
                                                <span class="time-posted">3 hrs ago</span>
                                            </p>
                                            <p>
                                                <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum
                                                    dolor amet cosec Lorem ipsum</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="author-offline">
                                        <div class="user-avater">
                                            <img src="img/team-1.png" alt="">
                                        </div>
                                        <div class="user-message">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">Web Design</a>
                                                <span class="time-posted">3 hrs ago</span>
                                            </p>
                                            <p>
                                                <span class="desc text-truncate" style="max-width: 215px;">Lorem ipsum
                                                    dolor amet cosec Lorem ipsum</span>
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                                <a href="" class="dropdown-wrapper__more">See All Message</a>
                            </div>
                        </div>
                    </li>
                    <!-- ends: nav-message -->
                    <li class="nav-notification">
                        <div class="dropdown-custom">
                            <a href="javascript:;" class="nav-item-toggle">
                                <span data-feather="bell"></span></a>
                            <div class="dropdown-wrapper">
                                <h2 class="dropdown-wrapper__title">Notifications <span class="badge-circle badge-warning ms-1">4</span></h2>
                                <ul>
                                    <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                        <div class="nav-notification__type nav-notification__type--primary">
                                            <span data-feather="inbox"></span>
                                        </div>
                                        <div class="nav-notification__details">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">James</a>
                                                <span>sent you a message</span>
                                            </p>
                                            <p>
                                                <span class="time-posted">5 hours ago</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                        <div class="nav-notification__type nav-notification__type--secondary">
                                            <span data-feather="upload"></span>
                                        </div>
                                        <div class="nav-notification__details">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">James</a>
                                                <span>sent you a message</span>
                                            </p>
                                            <p>
                                                <span class="time-posted">5 hours ago</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="nav-notification__single nav-notification__single--unread d-flex flex-wrap">
                                        <div class="nav-notification__type nav-notification__type--success">
                                            <span data-feather="log-in"></span>
                                        </div>
                                        <div class="nav-notification__details">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">James</a>
                                                <span>sent you a message</span>
                                            </p>
                                            <p>
                                                <span class="time-posted">5 hours ago</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="nav-notification__single nav-notification__single d-flex flex-wrap">
                                        <div class="nav-notification__type nav-notification__type--info">
                                            <span data-feather="at-sign"></span>
                                        </div>
                                        <div class="nav-notification__details">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">James</a>
                                                <span>sent you a message</span>
                                            </p>
                                            <p>
                                                <span class="time-posted">5 hours ago</span>
                                            </p>
                                        </div>
                                    </li>
                                    <li class="nav-notification__single nav-notification__single d-flex flex-wrap">
                                        <div class="nav-notification__type nav-notification__type--danger">
                                            <span data-feather="heart"></span>
                                        </div>
                                        <div class="nav-notification__details">
                                            <p>
                                                <a href="" class="subject stretched-link text-truncate" style="max-width: 180px;">James</a>
                                                <span>sent you a message</span>
                                            </p>
                                            <p>
                                                <span class="time-posted">5 hours ago</span>
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                                <a href="" class="dropdown-wrapper__more">See all incoming activity</a>
                            </div>
                        </div>
                    </li>
                    <!-- ends: .nav-notification -->
                    <li class="nav-settings">
                        <div class="dropdown-custom">
                            <a href="javascript:;" class="nav-item-toggle">
                                <span data-feather="settings"></span></a>
                            <div class="dropdown-wrapper dropdown-wrapper--large">
                                <ul class="list-settings">
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="shield" style="color: #8b5cf6; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/roles" class="stretched-link">Roles Management</a>
                                            </h6>
                                            <p>Manage user roles and permissions</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="map-pin" style="color: #06b6d4; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/states" class="stretched-link">States Management</a>
                                            </h6>
                                            <p>Manage geographical states</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="map" style="color: #10b981; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/cities" class="stretched-link">Cities Management</a>
                                            </h6>
                                            <p>Manage cities within states</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="layers" style="color: #8b5cf6; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/districts" class="stretched-link">Districts Management</a>
                                            </h6>
                                            <p>Manage districts within states</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="map-pin" style="color: #ec4899; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/localities" class="stretched-link">Localities Management</a>
                                            </h6>
                                            <p>Manage localities with zip codes</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="grid" style="color: #06b6d4; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/talukas" class="stretched-link">Talukas Management</a>
                                            </h6>
                                            <p>Manage talukas within districts</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="home" style="color: #14b8a6; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/villages" class="stretched-link">Villages Management</a>
                                            </h6>
                                            <p>Manage villages within talukas</p>
                                        </div>
                                    </li>
                                    <li class="d-flex">
                                        <div class="me-3">
                                            <span data-feather="users" style="color: #f59e0b; width: 24px; height: 24px;"></span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6>
                                                <a href="/users" class="stretched-link">Users Management</a>
                                            </h6>
                                            <p>Manage user accounts and profiles</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <!-- ends: .nav-support -->
                    <li class="nav-flag-select">
                        <div class="dropdown-custom">
                            <a href="javascript:;" class="nav-item-toggle"><img src="/img/flag.png" alt="" class="rounded-circle"></a>
                            <div class="dropdown-wrapper dropdown-wrapper--small">
                                <a href=""><img src="/img/eng.png" alt=""> English</a>
                                <a href=""><img src="/img/ger.png" alt=""> German</a>
                                <a href=""><img src="/img/spa.png" alt=""> Spanish</a>
                                <a href=""><img src="/img/tur.png" alt=""> Turkish</a>
                            </div>
                        </div>
                    </li>
                    <!-- ends: .nav-flag-select -->
                    <li class="nav-author">
                        <div class="dropdown-custom">
                            <a href="javascript:;" class="nav-item-toggle"><img src="/img/author-nav.jpg" alt="" class="rounded-circle"></a>
                            <div class="dropdown-wrapper">
                                <div class="nav-author__info">
                                    <div class="author-img">
                                        <img src="/img/author-nav.jpg" alt="" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h6>{{ auth()->user()->name }}</h6>
                                        <span>{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                                <div class="nav-author__options">
                                    <ul>
                                        <li>
                                            <a href="/profile" data-inertia-link>
                                                <span data-feather="user"></span> Profile</a>
                                        </li>
                                    </ul>
                                    <div class="button-group d-flex justify-content-center pt-1 mb-3">
                                        <form method="post" action="/logout" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger text-white btn-default btn-squared text-capitalize lh-normal px-50 py-15">
                                                <span data-feather="log-out" class="me-1"></span>
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- ends: .dropdown-wrapper -->
                        </div>
                    </li>
                    <!-- ends: .nav-author -->
                </ul>
                <!-- ends: .navbar-right__menu -->
                <div class="navbar-right__mobileAction d-md-none">
                    <a href="#" class="btn-search">
                        <span data-feather="search"></span>
                        <span data-feather="x"></span></a>
                    <a href="#" class="btn-author-action">
                        <span data-feather="more-vertical"></span></a>
                </div>
            </div>
            <!-- ends: .navbar-right -->
        </nav>
    </header>
    <main class="main-content">

        <aside class="sidebar-wrapper">
            <div class="sidebar sidebar-collapse" id="sidebar">
                <div class="sidebar__menu-group">
                    <ul class="sidebar_nav">
                        <li class="menu-title">
                            <span>Main menu</span>
                        </li>
                        <li>
                            <a href="/dashboard" data-inertia-link class="{{ request()->is('dashboard') ? 'active' : '' }}">
                                <span data-feather="home" class="nav-icon" style="color: #3b82f6;"></span>
                                <span class="menu-text" style="{{ request()->is('dashboard') ? 'font-weight: bold;' : '' }}">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="/management/locations" data-inertia-link class="{{ request()->is('management/locations') ? 'active' : '' }}">
                                <span data-feather="map" class="nav-icon" style="color: #0ea5e9;"></span>
                                <span class="menu-text" style="{{ request()->is('management/locations') ? 'font-weight: bold;' : '' }}">Location Management</span>
                            </a>
                        </li>
                        <li>
                            <a href="/management/users" data-inertia-link class="{{ request()->is('management/users') ? 'active' : '' }}">
                                <span data-feather="users" class="nav-icon" style="color: #f59e0b;"></span>
                                <span class="menu-text" style="{{ request()->is('management/users') ? 'font-weight: bold;' : '' }}">User Management</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <div class="contents">
            <div class="container-fluid">
                <div class="social-dash-wrap">
                    @inertia
                </div>
            </div>
        </div>
        <footer class="footer-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="footer-copyright">
                            <p>2025 @<a href="#">mr.web</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </main>
    <div id="overlayer">
        <span class="loader-overlay">
            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 30vh;">
                <div class="atbd-spin-dots spin-lg">
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                    <span class="spin-dot badge-dot dot-primary"></span>
                </div>
            </div>
        </span>
    </div>
    <div class="overlay-dark-sidebar"></div>
    <div class="customizer-overlay"></div>

    <a href="#" class="customizer-trigger">
        <span data-feather="settings"></span></a>
    <div class="customizer-wrapper">
        <div class="customizer">
            <div class="customizer__head">
                <h4 class="customizer__title">Customizer</h4>
                <span class="customizer__sub-title">Customize your overview page layout</span>
                <a href="#" class="customizer-close">
                    <span data-feather="x"></span></a>
            </div>
            <div class="customizer__body">
                <div class="customizer__single">
                    <h4>Layout Type</h4>
                    <ul class="customizer-list d-flex layout">
                        <li class="customizer-list__item">
                            <a href="https://demo.dashboardmarket.com/ltr" class="active">
                                <img src="/img/ltr.png" alt="">
                                <i class="fa fa-check-circle"></i>
                            </a>
                        </li>
                        <li class="customizer-list__item">
                            <a href="https://demo.dashboardmarket.com/rtl">
                                <img src="/img/rtl.png" alt="">
                                <i class="fa fa-check-circle"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- ends: .customizer__single -->

                <div class="customizer__single">
                    <h4>Sidebar Type</h4>
                    <ul class="customizer-list d-flex l_sidebar">
                        <li class="customizer-list__item">
                            <a href="#" data-layout="light" class="active">
                                <img src="/img/light.png" alt="">
                                <i class="fa fa-check-circle"></i>
                            </a>
                        </li>
                        <li class="customizer-list__item">
                            <a href="#" data-layout="dark">
                                <img src="/img/dark.png" alt="">
                                <i class="fa fa-check-circle"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- ends: .customizer__single -->

                <div class="customizer__single">
                    <h4>Navbar Type</h4>
                    <ul class="customizer-list d-flex l_navbar">
                        <li class="customizer-list__item">
                            <a href="#" data-layout="side" class="active">
                                <img src="/img/side.png" alt="">
                                <i class="fa fa-check-circle"></i>
                            </a>
                        </li>
                        <li class="customizer-list__item top">
                            <a href="#" data-layout="top">
                                <img src="/img/top.png" alt="">
                                <i class="fa fa-check-circle"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- ends: .customizer__single -->
            </div>
        </div>
    </div>

    @endif

        <!-- Theme JS from /html -->
        <script src="/assets/vendor_assets/js/jquery/jquery-3.5.1.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery/jquery-ui.js"></script>
        <script src="/assets/vendor_assets/js/bootstrap/popper.js"></script>
        <script src="/assets/vendor_assets/js/bootstrap/bootstrap.min.js"></script>
        <script src="/assets/vendor_assets/js/moment/moment.min.js"></script>
        <script src="/assets/vendor_assets/js/Chart.min.js"></script>
        <script src="/assets/vendor_assets/js/accordion.js"></script>
        <script src="/assets/vendor_assets/js/autoComplete.js"></script>
        <script src="/assets/vendor_assets/js/charts.js"></script>
        <script src="/assets/vendor_assets/js/daterangepicker.js"></script>
        <script src="/assets/vendor_assets/js/drawer.js"></script>
        <script src="/assets/vendor_assets/js/dynamicBadge.js"></script>
        <script src="/assets/vendor_assets/js/dynamicCheckbox.js"></script>
        <script src="/assets/vendor_assets/js/feather.min.js"></script>
        <script src="/assets/vendor_assets/js/footable.min.js"></script>
        <script src="/assets/vendor_assets/js/fullcalendar@5.2.0.js"></script>
        <script src="/assets/vendor_assets/js/google-chart.js"></script>
        <script src="/assets/vendor_assets/js/jquery-jvectormap-2.0.5.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery-jvectormap-world-mill-en.js"></script>
        <script src="/assets/vendor_assets/js/jquery.countdown.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery.filterizr.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery.mCustomScrollbar.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery.magnific-popup.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery.peity.min.js"></script>
        <script src="/assets/vendor_assets/js/jquery.star-rating-svg.min.js"></script>
        <script src="/assets/vendor_assets/js/leaflet.js"></script>
        <script src="/assets/vendor_assets/js/leaflet.markercluster.js"></script>
        <script src="/assets/vendor_assets/js/loader.js"></script>
        <script src="/assets/vendor_assets/js/message.js"></script>
        <script src="/assets/vendor_assets/js/muuri.min.js"></script>
        <script src="/assets/vendor_assets/js/notification.js"></script>
        <script src="/assets/vendor_assets/js/popover.js"></script>
        <script src="/assets/vendor_assets/js/select2.full.min.js"></script>
        <script src="/assets/vendor_assets/js/slick.min.js"></script>
        <script src="/assets/vendor_assets/js/trumbowyg.min.js"></script>
        <script src="/assets/vendor_assets/js/wickedpicker.min.js"></script>
        <script src="/assets/theme_assets/js/drag-drop.js"></script>
        <script src="/assets/theme_assets/js/footable.js"></script>
        <script src="/assets/theme_assets/js/full-calendar.js"></script>
        <script src="/assets/theme_assets/js/googlemap-init.js"></script>
        <script src="/assets/theme_assets/js/icon-loader.js"></script>
        <script src="/assets/theme_assets/js/jvectormap-init.js"></script>
        <script src="/assets/theme_assets/js/leaflet-init.js"></script>
        <script src="/assets/theme_assets/js/main.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Handle Inertia links in sidebar
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('click', function(e) {
                    const link = e.target.closest('[data-inertia-link]');
                    if (link && link.href && link.href.startsWith(window.location.origin)) {
                        e.preventDefault();
                        const href = link.getAttribute('href');
                        if (window.Inertia) {
                            window.Inertia.visit(href);
                        }
                    }
                });
            });
        </script>
    </body>
</html>
