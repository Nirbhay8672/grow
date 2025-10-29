<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
// Static sidebar cloned from /html/blank.html. Replace hrefs with Inertia links as needed.
</script>

<template>
    <aside class="sidebar-wrapper">
        <div class="sidebar sidebar-collapse" id="sidebar">
            <div class="sidebar__menu-group">
                <ul class="sidebar_nav">
                    <li class="menu-title">
                        <span>Main menu</span>
                    </li>
                    <li class="has-child">
                        <Link href="/dashboard" class="">
                            <span data-feather="home" class="nav-icon"></span>
                            <span class="menu-text">Dashboard</span>
                            <span class="toggle-icon"></span>
                        </Link>
                        <ul>
                            <li><a class="" href="#">Social Media</a></li>
                            <li><a class="" href="#">FineTech / Business</a></li>
                            <li><a class="" href="#">Site Performance</a></li>
                            <li><a class="" href="#">Ecommerce</a></li>
                            <li><a class="" href="#">CRM</a></li>
                            <li><a class="" href="#">Sales Performance</a></li>
                        </ul>
                    </li>
                    <li class="has-child">
                        <a href="#" class="">
                            <span data-feather="layout" class="nav-icon"></span>
                            <span class="menu-text">Layouts</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li class="l_sidebar"><a href="#" data-layout="light">Light Mode</a></li>
                            <li class="l_sidebar"><a href="#" data-layout="dark">Dark Mode</a></li>
                            <li class="l_navbar"><a href="#" data-layout="top">Top Menu</a></li>
                            <li class="l_navbar"><a href="#" data-layout="side">Side Menu</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="">
                            <span data-feather="activity" class="nav-icon"></span>
                            <span class="menu-text">Changelog</span>
                            <span class="badge badge-primary menuItem">2.1.6</span>
                        </a>
                    </li>
                    <li class="menu-title m-top-30">
                        <span>Applications</span>
                    </li>
                    <li class="has-child">
                        <a href="#" class="">
                            <span data-feather="mail" class="nav-icon"></span>
                            <span class="menu-text">Email</span>
                            <span class="toggle-icon"></span>
                        </a>
                        <ul>
                            <li><a class="" href="#">Inbox</a></li>
                            <li><a class="" href="#">Read Email</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="">
                            <span data-feather="message-square" class="nav-icon"></span>
                            <span class="menu-text">Chat</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
</template>


