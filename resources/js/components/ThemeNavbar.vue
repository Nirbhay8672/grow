<script setup lang="ts">
// Static navbar cloned from /html/blank.html. Hook up actions as needed.
</script>

<template>
    <header class="header-top">
        <nav class="navbar navbar-light">
            <div class="navbar-left">
                <a href="#" class="sidebar-toggle">
                    <img class="svg" src="/html/img/svg/bars.svg" alt="menu" />
                </a>
                <a class="navbar-brand" href="#">
                    <img class="dark" src="/html/img/logo_dark.png" alt="logo" />
                    <img class="light" src="/html/img/logo_white.png" alt="logo" />
                </a>
                <form class="search-form">
                    <span data-feather="search"></span>
                    <input class="form-control me-sm-2 box-shadow-none" type="text" placeholder="Search..." />
                </form>
            </div>

            <div class="navbar-right">
                <ul class="navbar-right__menu">
                    <li class="nav-search d-none">
                        <a href="#" class="search-toggle">
                            <i class="la la-search"></i>
                            <i class="la la-times"></i>
                        </a>
                        <form class="search-form-topMenu">
                            <span class="search-icon" data-feather="search"></span>
                            <input class="form-control me-sm-2 box-shadow-none" type="text" placeholder="Search..." />
                        </form>
                    </li>
                    <li class="nav-notification">
                        <a href="#" class="nav-item-toggle">
                            <span data-feather="bell"></span>
                        </a>
                    </li>
                    <li class="nav-settings">
                        <a href="#" class="nav-item-toggle">
                            <span data-feather="settings"></span>
                        </a>
                    </li>
                    <li class="nav-author">
                        <a href="#" class="nav-item-toggle">
                            <img src="/html/img/author-nav.jpg" alt="user" class="rounded-circle" />
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>





