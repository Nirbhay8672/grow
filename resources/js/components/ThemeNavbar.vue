<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const getInitials = (name: string) => {
    if (!name) return 'U';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
};
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
                            <img 
                                v-if="page.props.auth?.user?.avatar" 
                                :src="page.props.auth.user.avatar" 
                                alt="user" 
                                class="rounded-circle"
                                style="width: 40px; height: 40px; object-fit: cover;"
                            />
                            <div 
                                v-else
                                class="rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                style="width: 40px; height: 40px; background-color: #6c757d; font-size: 14px;"
                            >
                                {{ getInitials(page.props.auth?.user?.name || 'U') }}
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>





