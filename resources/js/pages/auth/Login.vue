<script setup lang="ts">
import AuthThemeLayout from '@/layouts/AuthThemeLayout.vue';
import { store } from '@/routes/login';
import { Form, Head, useForm } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();

const form = useForm({
    email: 'superadmin@gmail.com',
    password: '123123',
    remember: false,
});
</script>

<template>
    <AuthThemeLayout
        title="Log in to your account"
        description="Enter your email and password below to log in"
    >
        <Head title="Log in" />

        <div v-if="status" class="alert alert-success py-2 px-3 mb-3 text-center">
            {{ status }}
        </div>

        <Form
            :action="store.url()"
            method="post"
            v-model="form"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
        >
            <div class="form-group mb-20">
                <label for="email" class="form-label">Email address</label>
                <input id="email" type="email" name="email" v-model="form.email" required autofocus autocomplete="email" class="form-control" placeholder="email@example.com" />
                <div v-if="errors.email" class="text-danger small mt-1">{{ errors.email }}</div>
            </div>

            <div class="form-group mb-10">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="password" class="form-label mb-0">Password</label>
                </div>
                <input id="password" type="password" name="password" v-model="form.password" required autocomplete="current-password" class="form-control" placeholder="Password" />
                <div v-if="errors.password" class="text-danger small mt-1">{{ errors.password }}</div>
            </div>

            <div class="signUp-condition signIn-condition mb-3 d-flex justify-content-between align-items-center">
                <input id="remember" type="checkbox" name="remember" v-model="form.remember" class="form-check-input" />
                <label for="remember" class="form-check-label">Remember me</label>
            </div>

            <div class="button-group d-flex pt-1 justify-content-md-start justify-content-center">
            <button type="submit" class="btn btn-primary btn-default btn-squared me-15 text-capitalize lh-normal px-50 py-15 signIn-createBtn" :disabled="processing" data-test="login-button">
                <span v-if="processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Log in
            </button>
            </div>

        </Form>
    </AuthThemeLayout>
</template>
