<?php
/**
 * Template Name: قالب اختصاصی پنل کاربری
 * مسیر فایل: wp-content/themes/daroozhi/page-my-account.php
 * توضیح: نسخه تب‌بندی شده (ورود و عضویت جداگانه)
 * اصلاح شده: خط زیر تب‌ها نارنجی شد و تیترهای مزاحم ووکامرس حذف شدند.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); 
?>

<!-- فراخوانی Tailwind و فونت -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Vazirmatn', sans-serif; background-color: #f8fafc; }
    
    /* --- استایل‌های عمومی ووکامرس --- */
    
    /* مخفی کردن نویگیشن پیش‌فرض ووکامرس */
    .woocommerce-MyAccount-navigation { display: none !important; }
    .woocommerce-MyAccount-content { width: 100% !important; float: none !important; margin: 0 !important; }
    
    /* استایل لینک‌های فعال منوی اختصاصی */
    .account-nav-link.active { 
        background-color: #fff7ed; 
        color: #f97316; 
        font-weight: 800; 
        position: relative;
    }
    .account-nav-link.active::before {
        content: '';
        position: absolute;
        right: 0;
        top: 15%;
        bottom: 15%;
        width: 4px;
        background-color: #f97316;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .account-nav-link:hover:not(.active) { background-color: #f8fafc; color: #f97316; }

    /* --- استایل‌های تب‌بندی فرم ورود --- */
    
    /* مخفی کردن ساختار ستونی پیش‌فرض ووکامرس تا بتوانیم خودمان مدیریت کنیم */
    .custom-tab-behavior .col2-set,
    .custom-tab-behavior .u-columns {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
    }
    
    .custom-tab-behavior .u-column1, 
    .custom-tab-behavior .u-column2 {
        width: 100% !important;
        float: none !important;
        padding: 0 !important;
        display: none; /* پیش‌فرض مخفی تا JS مدیریت کند */
    }

    /* مخفی کردن تیترهای پیش‌فرض ووکامرس (ورود / عضویت) داخل فرم‌ها */
    .custom-tab-behavior h2,
    .woocommerce-login-wrapper h2 {
        display: none !important;
    }

    /* استایل فیلدهای ورودی */
    .woocommerce form .form-row input.input-text,
    .woocommerce-Input { 
        padding: 0.875rem 1rem !important; 
        border-radius: 1rem !important; 
        border: 1px solid #e2e8f0 !important; 
        width: 100% !important; 
        background-color: #f8fafc !important; 
        color: #334155 !important;
        box-sizing: border-box !important;
        transition: all 0.3s ease;
        min-height: 50px !important;
    }
    
    .woocommerce form .form-row input.input-text:focus,
    .woocommerce-Input:focus { 
        border-color: #f97316 !important; 
        background-color: #fff !important;
        outline: none !important; 
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1) !important; 
    }
    
    /* دکمه‌ها */
    .woocommerce button.button,
    .woocommerce .woocommerce-form-login__submit,
    .woocommerce .woocommerce-form-register__submit { 
        background-color: #f97316 !important; 
        color: white !important; 
        width: 100% !important;
        padding: 1rem !important; 
        border-radius: 1rem !important; 
        font-weight: 800 !important; 
        transition: all 0.3s !important; 
        box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3) !important;
        cursor: pointer !important;
        border: none !important;
        margin-top: 1rem !important;
    }
    
    .woocommerce button.button:hover { 
        background-color: #ea580c !important; 
        transform: translateY(-2px); 
    }
    
    .woocommerce-form-row { margin-bottom: 1.25rem !important; }
    .woocommerce-privacy-policy-text { font-size: 0.8rem !important; color: #64748b !important; margin-bottom: 1rem !important; }
    .woocommerce-form-login__rememberme { margin-bottom: 1rem !important; display: block !important; }

    /* پیام‌های ووکامرس */
    .woocommerce-message, .woocommerce-info, .woocommerce-error {
        background-color: #fff7ed;
        border-top: 3px solid #f97316;
        color: #9a3412;
        padding: 1rem 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        list-style: none !important;
    }
    .woocommerce-error { border-top-color: #ef4444; background-color: #fef2f2; color: #991b1b; }
    .woocommerce-message::before, .woocommerce-info::before, .woocommerce-error::before { display: none; }
</style>

<div class="account-page-wrapper min-h-screen bg-slate-50 w-full" dir="rtl">
    
    <!-- Hero Header (سفید) -->
    <div class="relative pt-8 pb-20 lg:pt-12 lg:pb-24 overflow-hidden bg-white mb-[-4rem]">
        <div class="absolute top-0 right-0 -z-10 w-full h-full">
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-50 rounded-full blur-3xl opacity-50 -mb-20 -ml-20"></div>
            <div class="absolute top-20 left-20 w-4 h-4 bg-orange-300 rounded-full opacity-30 animate-pulse"></div>
            <div class="absolute top-10 right-10 w-6 h-6 bg-yellow-300 rounded-full opacity-40 animate-bounce"></div>
        </div>

        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-100 text-orange-600 text-xs font-bold mb-4 shadow-sm animate-fade-in-up">
                <i class="fas fa-paw"></i>
                <span>پنل کاربری من</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-slate-800 mb-3 tracking-tight">حساب کاربری</h1>
            <p class="text-slate-600 text-lg max-w-lg mx-auto">مدیریت سفارشات دوست شما</p>
        </div>
    </div>

    <main class="container mx-auto px-4 md:px-6 relative z-20 pb-20">
        
        <?php if ( is_user_logged_in() ) : ?>
            
            <!-- (بخش کاربران لاگین شده) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                <aside class="lg:col-span-3 lg:sticky lg:top-24 space-y-6">
                    <!-- کارت اطلاعات کاربر -->
                    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 text-center relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-b from-orange-50 to-transparent"></div>
                        <div class="relative">
                            <div class="w-24 h-24 bg-white rounded-full mx-auto mb-4 flex items-center justify-center p-1 shadow-lg">
                                <div class="w-full h-full rounded-full overflow-hidden border-2 border-orange-100">
                                    <?php echo get_avatar( get_current_user_id(), 96, '', '', array('class' => 'w-full h-full object-cover') ); ?>
                                </div>
                            </div>
                            <h2 class="font-bold text-slate-800 text-xl mb-1">
                                <?php 
                                $current_user = wp_get_current_user();
                                echo esc_html( $current_user->display_name ); 
                                ?>
                            </h2>
                            <p class="text-sm text-slate-400 mb-4 font-medium"><?php echo esc_html( $current_user->user_email ); ?></p>
                            <div class="inline-flex items-center gap-2 text-xs font-bold text-orange-600 bg-orange-50 px-3 py-1.5 rounded-full">
                                <i class="fas fa-crown text-yellow-500"></i>
                                عضو باشگاه مشتریان
                            </div>
                        </div>
                    </div>
                    <!-- منو -->
                    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden p-2">
                        <nav class="flex flex-col space-y-1">
                            <?php
                            $items = wc_get_account_menu_items();
                            $icons = [
                                'dashboard'       => 'fas fa-home',
                                'orders'          => 'fas fa-box-open',
                                'downloads'       => 'fas fa-cloud-download-alt',
                                'edit-address'    => 'fas fa-map-marked-alt',
                                'edit-account'    => 'fas fa-user-cog',
                                'customer-logout' => 'fas fa-sign-out-alt rotate-180 text-red-500',
                            ];
                            foreach ( $items as $endpoint => $label ) :
                                $link = wc_get_account_endpoint_url( $endpoint );
                                $active_class = ( is_wc_endpoint_url( $endpoint ) || ( $endpoint === 'dashboard' && is_page( get_option( 'woocommerce_myaccount_page_id' ) ) && ! is_wc_endpoint_url() ) ) ? 'active' : '';
                                $icon_class = isset($icons[$endpoint]) ? $icons[$endpoint] : 'fas fa-circle';
                                ?>
                                <a href="<?php echo esc_url( $link ); ?>" class="account-nav-link flex items-center gap-3 py-3.5 px-5 text-sm font-medium text-slate-600 transition-all rounded-2xl <?php echo $active_class; ?>">
                                    <i class="<?php echo $icon_class; ?> w-6 text-center text-lg opacity-80"></i>
                                    <span><?php echo esc_html( $label ); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </nav>
                    </div>
                </aside>

                <div class="lg:col-span-9">
                    <div class="bg-white p-6 md:p-10 rounded-[2.5rem] shadow-sm border border-slate-100 min-h-[500px] relative">
                        <div class="absolute top-0 left-0 w-32 h-32 bg-orange-50 rounded-br-[5rem] rounded-tl-[2.5rem] -z-0 opacity-50"></div>
                        <div class="relative z-10">
                            <?php do_action( 'woocommerce_account_content' ); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php else : ?>
            
            <!-- حالت ورود / ثبت نام (تب بندی شده) -->
            <div class="max-w-lg mx-auto mt-4 px-2 md:px-0">
                
                <?php 
                // بررسی فعال بودن ثبت نام در تنظیمات ووکامرس
                $registration_enabled = get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes';
                ?>

                <div class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 relative overflow-hidden">
                    
                    <!-- هدر تب‌ها -->
                    <?php if ( $registration_enabled ) : ?>
                        <div class="flex border-b border-slate-100" id="auth-tabs">
                            <!-- تب ورود (پیش‌فرض فعال) -->
                            <!-- متن نارنجی و خط نارنجی -->
                            <button onclick="switchTab('login')" id="tab-login" class="flex-1 py-5 text-center font-black text-lg transition-all bg-white text-orange-600 relative">
                                ورود
                                <!-- خط فعال بودن (نارنجی) -->
                                <span class="absolute bottom-0 left-0 w-full h-1 bg-orange-500 rounded-t-full"></span>
                            </button>
                            <!-- تب عضویت -->
                            <button onclick="switchTab('register')" id="tab-register" class="flex-1 py-5 text-center font-bold text-lg text-slate-400 hover:text-slate-600 transition-all bg-white relative">
                                عضویت
                                <span class="absolute bottom-0 left-0 w-full h-1 bg-transparent rounded-t-full"></span>
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="p-6 text-center border-b border-slate-100">
                             <h2 class="text-xl font-black text-slate-800">ورود به حساب کاربری</h2>
                        </div>
                    <?php endif; ?>

                    <div class="p-6 md:p-10">
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 bg-orange-100 rounded-2xl mx-auto flex items-center justify-center text-orange-600 text-2xl mb-3 shadow-sm">
                                <i class="fas fa-paw"></i>
                            </div>
                            <p class="text-slate-500 text-xs md:text-sm" id="auth-desc">به خانواده هاپومیو خوش آمدید!</p>
                        </div>
                        
                        <!-- فرم‌های ووکامرس -->
                        <div class="woocommerce-login-wrapper custom-tab-behavior">
                            <?php echo do_shortcode('[woocommerce_my_account]'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- اسکریپت ساده برای جابجایی تب‌ها -->
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // پنهان کردن اولیه ثبت نام و نمایش ورود
                    var loginForm = document.querySelector('.custom-tab-behavior .u-column1');
                    var registerForm = document.querySelector('.custom-tab-behavior .u-column2');
                    
                    if (loginForm) loginForm.style.display = 'block';
                    if (registerForm) registerForm.style.display = 'none';

                    // تابع گلوبال برای دکمه‌ها
                    window.switchTab = function(tabName) {
                        var tabLogin = document.getElementById('tab-login');
                        var tabRegister = document.getElementById('tab-register');
                        
                        // استایل‌های اکتیو (نارنجی) و غیرفعال
                        var activeClasses = ['bg-white', 'text-orange-600', 'font-black'];
                        var inactiveClasses = ['bg-white', 'text-slate-400', 'font-bold'];

                        if (tabName === 'login') {
                            // نمایش فرم
                            if(loginForm) loginForm.style.display = 'block';
                            if(registerForm) registerForm.style.display = 'none';
                            
                            // استایل تب
                            if(tabLogin) {
                                tabLogin.classList.add(...activeClasses);
                                tabLogin.classList.remove(...inactiveClasses.filter(c => c !== 'font-bold'));
                                tabLogin.querySelector('span').classList.remove('bg-transparent');
                                tabLogin.querySelector('span').classList.add('bg-orange-500'); // خط نارنجی
                                tabLogin.querySelector('span').classList.remove('bg-slate-800');
                            }
                            if(tabRegister) {
                                tabRegister.classList.remove(...activeClasses);
                                tabRegister.classList.add(...inactiveClasses);
                                tabRegister.querySelector('span').classList.add('bg-transparent');
                                tabRegister.querySelector('span').classList.remove('bg-orange-500');
                            }
                            
                        } else {
                            // نمایش فرم
                            if(loginForm) loginForm.style.display = 'none';
                            if(registerForm) registerForm.style.display = 'block';
                            
                            // استایل تب
                            if(tabRegister) {
                                tabRegister.classList.add(...activeClasses);
                                tabRegister.classList.remove(...inactiveClasses.filter(c => c !== 'font-bold'));
                                tabRegister.querySelector('span').classList.remove('bg-transparent');
                                tabRegister.querySelector('span').classList.add('bg-orange-500'); // خط نارنجی
                                tabRegister.querySelector('span').classList.remove('bg-slate-800');
                            }
                            if(tabLogin) {
                                tabLogin.classList.remove(...activeClasses);
                                tabLogin.classList.add(...inactiveClasses);
                                tabLogin.querySelector('span').classList.add('bg-transparent');
                                tabLogin.querySelector('span').classList.remove('bg-orange-500');
                            }
                        }
                    }
                });
            </script>

        <?php endif; ?>

    </main>

</div>

<?php get_footer(); ?>