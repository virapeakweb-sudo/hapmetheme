<?php
/* Path: wp-content/themes/hapomeo/footer.php
توضیحات: فوتر سایت شامل کدهای جاوااسکریپت و ساختار کامل HTML طبق فایل home.html
*/
?>
    <!-- Footer -->
    <footer class="bg-white border-t pt-12 pb-8 text-center mt-auto">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                <div class="col-span-2 md:col-span-1 flex flex-col items-center">
                    <a href="<?php echo home_url('/'); ?>" class="text-2xl font-extrabold text-orange-500 flex items-center justify-center gap-2 mb-4">
                        <i class="fas fa-paw text-3xl"></i>
                        <span>هاپومیو</span>
                    </a>
                    <p class="text-slate-500 text-sm leading-7 mb-6 max-w-xs mx-auto">
                        در هاپومیو، ما عشق به حیوانات را با تخصص دامپزشکی ترکیب کرده‌ایم تا بهترین تجربه خرید آنلاین را
                        برای شما و پت دلبندتان فراهم کنیم.
                    </p>
                    <div class="flex justify-center space-x-reverse space-x-3">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-pink-500 hover:text-white transition-all"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-blue-500 hover:text-white transition-all"><i class="fab fa-telegram"></i></a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-green-500 hover:text-white transition-all"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="flex flex-col items-center">
                    <h3 class="font-bold text-slate-800 mb-6 border-b-2 border-orange-100 pb-2 inline-block">دسترسی سریع</h3>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="https://hapoomeo.com/petshop/" class="hover:text-orange-500 transition-colors">فروشگاه</a></li>
                        <li><a href="https://hapoomeo.com/blog/" class="hover:text-orange-500 transition-colors">مقالات آموزشی</a></li>
                        <li><a href="https://hapoomeo.com/about-us/" class="hover:text-orange-500 transition-colors">درباره ما</a></li>
                        <li><a href="https://hapoomeo.com/contact-us/" class="hover:text-orange-500 transition-colors">تماس با ما</a></li>
                    </ul>
                </div>
                <div class="flex flex-col items-center">
                    <h3 class="font-bold text-slate-800 mb-6 border-b-2 border-orange-100 pb-2 inline-block">راهنمای مشتریان</h3>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li><a href="#" class="hover:text-orange-500 transition-colors">رویه ارسال سفارش</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">شیوه‌های پرداخت</a></li>
                        <li><a href="#" class="hover:text-orange-500 transition-colors">قوانین بازگشت کالا</a></li>
                        <li><a href="https://hapoomeo.com/my-account/orders/" class="hover:text-orange-500 transition-colors">پیگیری سفارش</a></li>
                    </ul>
                </div>
                <div class="col-span-2 md:col-span-1 flex flex-col items-center">
                    <h3 class="font-bold text-slate-800 mb-6 border-b-2 border-orange-100 pb-2 inline-block">خبرنامه</h3>
                    <p class="text-xs text-slate-400 mb-4">برای اطلاع از آخرین تخفیف‌ها ایمیل خود را وارد کنید.</p>
                    <div class="relative w-full max-w-xs">
                        <input type="email" placeholder="ایمیل شما..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm outline-none focus:border-orange-500 transition-colors text-center">
                        <button class="absolute left-2 top-2 bottom-2 bg-orange-500 text-white rounded-lg px-3 hover:bg-orange-600 transition-colors"><i class="fas fa-arrow-left"></i></button>
                    </div>
                </div>
            </div>
            <div class="border-t border-slate-100 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-400">
                <p>&copy; ۱۴۰۳ هاپومیو. تمامی حقوق محفوظ است.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-slate-600">قوانین سایت</a>
                    <a href="#" class="hover:text-slate-600">حریم خصوصی</a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Sidebar Logic
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const sidebarBackdrop = document.getElementById('sidebar-backdrop');
            const closeSidebarBtn = document.getElementById('close-sidebar');

            function toggleSidebar() {
                if(mobileSidebar) {
                    mobileSidebar.classList.toggle('open');
                    sidebarBackdrop.classList.toggle('open');
                    document.body.classList.toggle('overflow-hidden'); // Prevent background scrolling
                }
            }

            if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', toggleSidebar);
            if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', toggleSidebar);
            if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', toggleSidebar);

            // Accordion Logic for Sidebar
            const accordions = document.querySelectorAll('.accordion-toggle');
            accordions.forEach(button => {
                button.addEventListener('click', () => {
                    const content = button.nextElementSibling;
                    const icon = button.querySelector('.fa-chevron-down');
                    
                    // Close other accordions
                    document.querySelectorAll('.accordion-content').forEach(c => {
                        if (c !== content) {
                            c.style.maxHeight = null;
                            const otherIcon = c.previousElementSibling.querySelector('.fa-chevron-down');
                            if(otherIcon) otherIcon.classList.remove('rotate-180');
                        }
                    });

                    if (content.style.maxHeight) {
                        content.style.maxHeight = null;
                        icon.classList.remove('rotate-180');
                    } else {
                        content.style.maxHeight = content.scrollHeight + "px";
                        icon.classList.add('rotate-180');
                    } 
                });
            });
        });
    </script>

    <?php wp_footer(); ?>

    <div id="mobile-sticky-btn" class="fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-4 z-50 lg:hidden shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.1)] transition-all duration-300 transform translate-y-0 opacity-100 hidden">
    <div class="flex justify-between items-center mb-2">
        <span class="text-xs text-slate-500">مبلغ قابل پرداخت:</span>
        <span class="font-bold text-slate-800 total-sticky-price">مشاهده سبد خرید</span>
    </div>
    <button onclick="jQuery('form.checkout').submit();" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl hover:bg-orange-600 transition-colors">
        ثبت نهایی سفارش
    </button>
</div>

<script>
    // همان اسکریپتی که در فایل html بود اینجا قرار بده
    // فقط کلاس hidden را در html بالا گذاشتم که تا لود شدن js نپرد
</script>
</body>
</html>