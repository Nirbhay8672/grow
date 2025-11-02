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
                <div class="row justify-content-center"  style="min-height: 100vh; background-image: linear-gradient(to bottom, rgba(255,255,255,0.75) 0%, rgba(255,255,255,0.50) 30%, rgba(255,255,255,0.15) 100%), url('https://www.itl.cat/pngfile/big/9-92452_original-resolution-popular-4k-dubai.jpg'); background-size: cover; background-position: center center; background-repeat: no-repeat; background-attachment: scroll;">
                    <div class="col-xl-4 col-lg-5 col-md-5 p-0">
                        <div class="signUP-admin-left signIn-admin-left position-relative" style="background: linear-gradient(135deg, #f9eff8 0%, #f3e8f3 100%); overflow: hidden;">
                            <!-- Decorative Background Elements -->
                            <div class="position-absolute w-100 h-100" style="z-index: 1; opacity: 0.4;">
                                <!-- Top Right Circle -->
                                <div class="position-absolute" style="top: -150px; right: -100px; width: 400px; height: 400px; background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%); border-radius: 50%;"></div>
                                <!-- Bottom Left Circle -->
                                <div class="position-absolute" style="bottom: -120px; left: -80px; width: 350px; height: 350px; background: radial-gradient(circle, rgba(236, 72, 153, 0.15) 0%, transparent 70%); border-radius: 50%;"></div>
                                <!-- Middle Right Circle -->
                                <div class="position-absolute" style="top: 50%; right: -60px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(139, 92, 246, 0.1) 0%, transparent 70%); border-radius: 50%; transform: translateY(-50%);"></div>
                            </div>
                            
                            <!-- Subtle Pattern Overlay -->
                            <div class="position-absolute w-100 h-100" style="z-index: 1; opacity: 0.03;">
                                <svg width="100%" height="100%" style="position: absolute;">
                                    <defs>
                                        <pattern id="dots" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                                            <circle cx="20" cy="20" r="1.5" fill="#8b5cf6"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#dots)" />
                                </svg>
                            </div>
                            
                            <!-- Content Container -->
                            <div class="position-relative d-flex flex-column justify-content-center align-items-center h-100" style="z-index: 2; padding: 60px 40px; text-align: center;">
                                <!-- Logo Section -->
                                <div class="mb-5">
                                    <a href="/" class="d-flex flex-column align-items-center text-decoration-none">
                                        <img src="https://growedgerealty.com/assets/images/logo/logo_1.png" alt="logo" class="img-fluid mb-5" style="height: 90px; filter: drop-shadow(0 4px 12px rgba(139, 92, 246, 0.2));" />
                                    </a>
                                </div>
                                
                                <!-- Property SVGs Illustration -->
                                <div class="mt-4" style="width: 100%; max-width: 400px; margin: 0 auto;">
                                    <div class="position-relative" style="min-height: 280px;">
                                        <!-- Building SVG -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 280" style="width: 100%; height: auto;">
                                            <!-- Large Building -->
                                            <g transform="translate(50, 60)">
                                                <rect x="0" y="0" width="100" height="160" fill="#1e40af" opacity="0.9" rx="6"/>
                                                <rect x="8" y="10" width="25" height="25" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="42" y="10" width="25" height="25" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="76" y="10" width="25" height="25" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="8" y="45" width="25" height="25" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="42" y="45" width="25" height="25" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="76" y="45" width="25" height="25" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="32" y="120" width="36" height="40" fill="#f59e0b" rx="3"/>
                                            </g>
                                            
                                            <!-- Medium Building -->
                                            <g transform="translate(170, 100)">
                                                <rect x="0" y="0" width="70" height="120" fill="#3b82f6" opacity="0.85" rx="5"/>
                                                <rect x="8" y="8" width="20" height="20" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="38" y="8" width="20" height="20" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="8" y="38" width="20" height="20" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="38" y="38" width="20" height="20" fill="#ffffff" opacity="0.7" rx="2"/>
                                                <rect x="22" y="85" width="26" height="28" fill="#f59e0b" rx="2"/>
                                            </g>
                                            
                                            <!-- House -->
                                            <g transform="translate(260, 110)">
                                                <polygon points="35,50 0,25 35,0 70,25" fill="#f59e0b" opacity="0.9"/>
                                                <rect x="22" y="50" width="26" height="70" fill="#fb923c" opacity="0.95" rx="3"/>
                                                <rect x="30" y="75" width="8" height="18" fill="#1e40af" rx="1"/>
                                                <circle cx="45" cy="95" r="2.5" fill="#ffffff"/>
                                            </g>
                                            
                                            <!-- Key Icon -->
                                            <g transform="translate(320, 50)">
                                                <circle cx="30" cy="30" r="12" fill="#f59e0b" opacity="0.9"/>
                                                <rect x="34" y="26" width="28" height="8" fill="#f59e0b" opacity="0.9" rx="4"/>
                                                <rect x="56" y="22" width="12" height="16" fill="#f59e0b" opacity="0.9" rx="2"/>
                                                <rect x="58" y="25" width="8" height="4" fill="#ffffff" opacity="0.6"/>
                                                <rect x="58" y="32" width="8" height="4" fill="#ffffff" opacity="0.6"/>
                                            </g>
                                            
                                            <!-- Location Pin -->
                                            <g transform="translate(80, 200)">
                                                <path d="M25 0C11.193 0 0 11.193 0 25c0 16.667 25 41.667 25 41.667s25-25 25-41.667C50 11.193 38.807 0 25 0z" fill="#1e40af" opacity="0.8"/>
                                                <circle cx="25" cy="25" r="10" fill="#ffffff" opacity="0.9"/>
                                            </g>
                                            
                                            <!-- Document -->
                                            <g transform="translate(300, 200)">
                                                <rect x="8" y="8" width="50" height="65" fill="#ffffff" opacity="0.95" rx="3"/>
                                                <rect x="15" y="20" width="35" height="2.5" fill="#3b82f6" opacity="0.6"/>
                                                <rect x="15" y="28" width="35" height="2.5" fill="#3b82f6" opacity="0.4"/>
                                                <rect x="15" y="36" width="25" height="2.5" fill="#3b82f6" opacity="0.4"/>
                                                <circle cx="48" cy="47" r="6" fill="#f59e0b" opacity="0.7"/>
                                                <rect x="15" y="58" width="35" height="2.5" fill="#3b82f6" opacity="0.3"/>
                                                <rect x="15" y="64" width="28" height="2.5" fill="#3b82f6" opacity="0.3"/>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-7 col-md-7 col-sm-8" style="min-height: 100vh;">
                        <div class="signUp-admin-right signIn-admin-right p-md-40 p-10 position-relative" >
                            
                            <div class="row justify-content-center position-relative" style="z-index: 1;">
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


