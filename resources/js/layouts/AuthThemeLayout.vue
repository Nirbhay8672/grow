<script setup lang="ts">
interface Props {
    title?: string;
    description?: string;
}

withDefaults(defineProps<Props>(), {
    title: '',
    description: '',
});
</script>

<template>
    <main class="main-content">
        <div class="signUP-admin">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-4 col-lg-5 col-md-5 p-0">
                        <div class="signUP-admin-left signIn-admin-left position-relative">
                            <div class="signUP-overlay">
                                <img class="svg signupTop" src="https://demo.dashboardmarket.com/strikingdash-html/img/svg/signuptop.svg" alt="img" />
                            </div>
                            <div class="signUP-admin-left__content">
                                <div class="text-capitalize mb-md-30 mb-15 d-flex align-items-center justify-content-md-start justify-content-center">
                                    <a href="/" class="me-10">
                                        <img src="https://growedgerealty.com/assets/images/logo/logo_1.png" alt="logo" class="img-fluid" style="height: 40px;" />
                                    </a>
                                    <span class="text-dark">admin</span>
                                </div>
                                <h1 class="mb-0">{{ title }}</h1>
                                <p class="mb-0" v-if="description">{{ description }}</p>
                            </div>
                            <div class="signUP-admin-left__img">
                                <img class="img-fluid svg" src="https://demo.dashboardmarket.com/strikingdash-html/img/svg/signupIllustration.svg" alt="img" />
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7 col-md-7 col-sm-8">
                        <div class="signUp-admin-right signIn-admin-right p-md-40 p-10">
                            <div class="row justify-content-center">
                                <div class="col-xl-7 col-lg-8 col-md-12">
                                    <div class="edit-profile mt-md-25 mt-0">
                                        <div class="card border-0">
                                            <div class="card-header border-0 pb-md-15 pb-10 pt-md-20 pt-10 ">
                                                <div class="edit-profile__title">
                                                    <h6 class="mb-0">Sign in to <span class="color-primary">Admin</span></h6>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="edit-profile__body">
                                                    <slot />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>


