<?php
/**
 * The Template for displaying product archives
 * Path: wp-content/themes/hapomeo/woocommerce/archive-product.php
 */

defined( 'ABSPATH' ) || exit;

get_header();

// دریافت مقادیر فعلی قیمت برای اسلایدر
$min_price = isset( $_GET['min_price'] ) ? intval( $_GET['min_price'] ) : 0;
$max_price = isset( $_GET['max_price'] ) ? intval( $_GET['max_price'] ) : 10000000; // پیش‌فرض ۱۰ میلیون
?>

<!-- استایل‌های اختصاصی -->
<style>
    /* Custom Range Slider */
    .range-slider { position: relative; width: 100%; height: 5px; background: #e2e8f0; border-radius: 5px; margin-top: 10px; }
    .range-progress { position: absolute; height: 100%; background: #f97316; border-radius: 5px; left: 0; right: 0; }
    .range-input { position: relative; width: 100%; }
    .range-input input { position: absolute; width: 100%; height: 5px; top: -7px; background: none; pointer-events: none; -webkit-appearance: none; appearance: none; }
    .range-input input::-webkit-slider-thumb { height: 18px; width: 18px; border-radius: 50%; background: #fff; border: 2px solid #f97316; pointer-events: auto; -webkit-appearance: none; box-shadow: 0 1px 3px rgba(0,0,0,0.3); cursor: pointer; }
    .range-input input::-moz-range-thumb { height: 18px; width: 18px; border: none; border-radius: 50%; background: #f97316; pointer-events: auto; cursor: pointer; }
    
    /* Custom Dropdown */
    .custom-dropdown { position: relative; width: 200px; }
    .dropdown-selected { background: #fff; padding: 10px 15px; border-radius: 12px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; color: #475569; border: 1px solid #e2e8f0; transition: all 0.3s; }
    .dropdown-selected:hover { border-color: #f97316; }
    .dropdown-options { position: absolute; top: 110%; left: 0; right: 0; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); overflow: hidden; opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); z-index: 50; border: 1px solid #f1f5f9; }
    .custom-dropdown.active .dropdown-options { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-option { padding: 10px 15px; font-size: 0.875rem; color: #475569; cursor: pointer; transition: background 0.2s; }
    .dropdown-option:hover { background: #fff7ed; color: #f97316; }
    .dropdown-option.active { font-weight: bold; color: #f97316; background: #fff7ed; }
    
    /* Checkbox Style */
    .filter-checkbox:checked { background-color: #f97316; border-color: #f97316; }
    
    /* Infinite Scroll Loading */
    .loading-spinner { display: none; text-align: center; padding: 20px; width: 100%; }
    .loading-spinner i { color: #f97316; font-size: 2rem; animation: spin 1s linear infinite; }
    
    /* Loading Overlay for Ajax */
    .products-loading-overlay { position: absolute; inset: 0; background: rgba(255,255,255,0.7); z-index: 40; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(2px); opacity: 0; visibility: hidden; transition: all 0.3s; }
    .products-loading-overlay.active { opacity: 1; visibility: visible; }

    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>

<main class="container mx-auto p-4 md:p-6 min-h-screen">
    <div class="flex flex-col md:flex-row gap-8">
        
        <!-- Filters Sidebar (Desktop) -->
        <aside id="desktop-filters" class="hidden md:block md:w-1/4 lg:w-1/5 bg-white p-6 rounded-2xl shadow-sm self-start sticky top-28 h-fit">
            
            <!-- فرم فیلتر اصلی -->
            <form action="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" method="GET" id="filter-form">
                
                <h3 class="font-bold text-lg mb-4 border-b pb-3 flex items-center gap-2"><i class="fas fa-filter text-orange-500"></i> فیلترها</h3>

                <!-- 1. Categories (جابجایی به بالا) -->
                <div class="mb-6">
                    <h4 class="font-semibold text-slate-800 mb-3 text-sm">دسته‌بندی‌ها</h4>
                    <ul class="space-y-2 text-sm text-slate-600 pr-1 max-h-48 overflow-y-auto custom-scroll">
                        <?php 
                        $current_cat = get_queried_object()->slug ?? '';
                        $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0]); // دسته‌های مادر
                        foreach($categories as $category): 
                            $is_active = ($current_cat == $category->slug);
                        ?>
                            <li>
                                <a href="<?php echo get_term_link($category); ?>" class="block hover:text-orange-500 transition-colors filter-link <?php echo $is_active ? 'text-orange-600 font-bold' : ''; ?>" data-slug="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                    <span class="text-xs text-slate-400 mr-1">(<?php echo $category->count; ?>)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- 2. Brands Filter (اضافه شده) -->
                <div class="mb-6 border-t pt-6">
                    <h4 class="font-semibold text-slate-800 mb-3 text-sm">برند</h4>
                    <div class="space-y-2 text-sm max-h-48 overflow-y-auto pr-1 custom-scroll">
                        <?php 
                        // فرض بر این است که برندها به عنوان ویژگی (Attribute) با نام 'pa_brand' ذخیره شده‌اند
                        // یا اگر تاکسونومی اختصاصی دارید نام آن را جایگزین کنید (مثلاً 'brand')
                        $brands = get_terms(['taxonomy' => 'pa_brand', 'hide_empty' => true]); 
                        
                        if ( ! empty( $brands ) && ! is_wp_error( $brands ) ) {
                            $current_brands = isset($_GET['filter_brand']) ? explode(',', $_GET['filter_brand']) : [];
                            foreach($brands as $brand): 
                                $checked = in_array($brand->slug, $current_brands) ? 'checked' : '';
                            ?>
                                <label class="flex items-center cursor-pointer hover:text-orange-500 transition-colors">
                                    <input type="checkbox" name="filter_brand[]" value="<?php echo esc_attr($brand->slug); ?>" class="ml-2 rounded border-gray-300 text-orange-500 focus:ring-orange-500 filter-checkbox" <?php echo $checked; ?>> 
                                    <span class="text-slate-600"><?php echo esc_html($brand->name); ?></span>
                                </label>
                            <?php 
                            endforeach;
                        } else {
                            echo '<p class="text-xs text-slate-400">برندی یافت نشد.</p>';
                        }
                        ?>
                    </div>
                </div>
                
                <!-- 3. Price Range Slider -->
                <div class="mb-4 border-t pt-6">
                    <h4 class="font-semibold text-slate-800 mb-4 text-sm">محدوده قیمت (تومان)</h4>
                    
                    <div class="range-slider">
                        <div class="range-progress"></div>
                    </div>
                    <div class="range-input">
                        <input type="range" class="range-min" min="0" max="20000000" value="<?php echo esc_attr($min_price); ?>" step="100000">
                        <input type="range" class="range-max" min="0" max="20000000" value="<?php echo esc_attr($max_price); ?>" step="100000">
                    </div>
                    
                    <div class="flex justify-between items-center mt-4 text-xs font-bold text-slate-600">
                        <div class="bg-slate-100 px-2 py-1 rounded border border-slate-200">
                            <span id="min-price-display"><?php echo number_format($min_price); ?></span>
                        </div>
                        <span class="text-slate-400">تا</span>
                        <div class="bg-slate-100 px-2 py-1 rounded border border-slate-200">
                            <span id="max-price-display"><?php echo number_format($max_price); ?></span>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="hidden" name="min_price" id="input-min-price" value="<?php echo esc_attr($min_price); ?>">
                    <input type="hidden" name="max_price" id="input-max-price" value="<?php echo esc_attr($max_price); ?>">
                    
                    <!-- دکمه اعمال فیلتر (برای سئو و فال‌بک) -->
                    <button type="submit" id="apply-filters-btn" class="w-full mt-5 bg-orange-50 text-orange-600 border border-orange-200 hover:bg-orange-500 hover:text-white transition-all text-sm font-bold py-2.5 rounded-xl shadow-sm">
                        اعمال فیلترها
                    </button>
                </div>

                <!-- حفظ سایر پارامترهای GET به جز موارد فیلتر -->
                <?php 
                foreach ($_GET as $key => $val) {
                    if ( !in_array($key, ['min_price', 'max_price', 'filter_brand', 'product_cat', 'submit']) && !is_array($val) ) {
                        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '">';
                    }
                }
                ?>

            </form>
        </aside>

        <!-- Products Grid Area -->
        <div class="w-full md:w-3/4 lg:w-4/5 relative">
            
            <!-- Overlay Loader for Ajax -->
            <div id="products-overlay" class="products-loading-overlay rounded-3xl">
                <div class="bg-white p-4 rounded-full shadow-lg">
                    <i class="fas fa-circle-notch fa-spin text-orange-500 text-3xl"></i>
                </div>
            </div>

            <!-- Header Bar -->
            <div class="flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-2xl shadow-sm mb-6 gap-4">
                <div class="flex items-center gap-2">
                    <h1 class="text-lg font-bold text-slate-800"><?php woocommerce_page_title(); ?></h1>
                    <span class="text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded-full"><?php echo $product->get_role_caps ? 0 : woocommerce_result_count(); ?> محصول</span>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <!-- Mobile Filter Toggle -->
                    <button id="filter-btn-mobile" class="md:hidden bg-slate-100 text-slate-700 rounded-xl px-4 py-2.5 text-sm font-bold flex items-center gap-2 hover:bg-slate-200 transition-colors">
                        <i class="fas fa-sliders-h"></i> فیلتر
                    </button>
                    
                    <!-- Custom Soft Dropdown Sort -->
                    <div class="custom-dropdown z-30 hidden md:block">
                        <?php 
                            $orderby = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : 'menu_order';
                            $options = array(
                                'menu_order' => 'پیش‌فرض',
                                'popularity' => 'محبوب‌ترین',
                                'rating'     => 'امتیاز',
                                'date'       => 'جدیدترین',
                                'price'      => 'ارزان‌ترین',
                                'price-desc' => 'گران‌ترین',
                            );
                            $current_label = isset($options[$orderby]) ? $options[$orderby] : $options['menu_order'];
                        ?>
                        <div class="dropdown-selected">
                            <span><i class="fas fa-sort-amount-down-alt text-orange-500 ml-2"></i> <?php echo esc_html($current_label); ?></span>
                            <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                        </div>
                        <div class="dropdown-options">
                            <?php foreach($options as $key => $label): ?>
                                <div class="dropdown-option <?php echo ($orderby == $key) ? 'active' : ''; ?>" data-value="<?php echo esc_attr($key); ?>">
                                    <?php echo esc_html($label); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Loop Container -->
            <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
                <?php
                if ( have_posts() ) {
                    while ( have_posts() ) {
                        the_post();
                        wc_get_template_part( 'content', 'product' );
                    }
                } else {
                    echo '<div class="col-span-full text-center py-12 text-slate-500 flex flex-col items-center justify-center">
                            <i class="fas fa-box-open text-4xl text-slate-300 mb-4"></i>
                            <p>محصولی با این مشخصات یافت نشد.</p>
                          </div>';
                }
                ?>
            </div>

            <!-- Infinite Scroll Loader -->
            <div class="loading-spinner" id="inf-scroll-loader">
                <i class="fas fa-spinner"></i>
            </div>

            <!-- Hidden Pagination for JS to read -->
            <div id="pagination-source" class="hidden">
                <?php
                $args = array(
                    'total' => $wp_query->max_num_pages,
                    'current' => max( 1, get_query_var( 'paged' ) ),
                    'format' => '?paged=%#%',
                    'show_all' => false,
                    'type' => 'plain',
                    'prev_next' => true,
                );
                echo paginate_links( $args );
                ?>
            </div>

        </div>
    </div>
</main>

<!-- Mobile Filter Bottom Sheet -->
<div id="filter-sheet" class="fixed inset-0 bg-black/40 z-50 hidden backdrop-blur-sm transition-opacity">
    <div id="filter-sheet-content" class="bottom-sheet fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl h-[85vh] shadow-2xl flex flex-col transition-transform duration-300 transform translate-y-full">
        <div class="flex justify-between items-center p-5 border-b">
            <h3 class="font-bold text-lg text-slate-800">فیلترهای پیشرفته</h3>
            <button id="close-filter-sheet" class="text-slate-400 hover:text-red-500 w-8 h-8 flex items-center justify-center rounded-full bg-slate-50"><i class="fas fa-times"></i></button>
        </div>
        <div class="overflow-y-auto flex-grow p-6" id="mobile-filters-container">
            <!-- Content will be cloned from desktop sidebar via JS -->
        </div>
        <!-- دکمه اعمال فیلتر موبایل -->
        <div class="p-4 border-t bg-white">
             <button id="mobile-apply-btn" class="w-full bg-orange-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-orange-200 hover:bg-orange-600 transition-all">مشاهده نتایج</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const form = document.getElementById('filter-form');
        const productGrid = document.getElementById('product-grid');
        const overlay = document.getElementById('products-overlay');
        const paginationSource = document.getElementById('pagination-source');
        
        // --- AJAX Filtering Logic ---
        function fetchProducts(url, pushState = true) {
            overlay.classList.add('active');
            
            // اگر pushState فعال باشد، URL مرورگر را آپدیت می‌کنیم (سئو فرندلی)
            if (pushState) {
                window.history.pushState({path: url}, '', url);
            }

            fetch(url)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // آپدیت گرید محصولات
                    const newGridContent = doc.getElementById('product-grid').innerHTML;
                    productGrid.innerHTML = newGridContent;
                    
                    // آپدیت صفحه‌بندی (برای اسکرول بی‌نهایت)
                    const newPagination = doc.getElementById('pagination-source');
                    if(newPagination) {
                        paginationSource.innerHTML = newPagination.innerHTML;
                        // ریست کردن متغیر لینک صفحه بعد برای اسکرول بی‌نهایت
                        updateNextLinkFromDOM(); 
                    } else {
                        paginationSource.innerHTML = '';
                    }
                    
                    // آپدیت تایتل و تعداد (اختیاری)
                    // const newCount = doc.querySelector('.woocommerce-result-count'); ...

                    overlay.classList.remove('active');
                    canLoad = true; // اجازه لود مجدد برای اسکرول بی‌نهایت
                })
                .catch(err => {
                    console.error('Error fetching products:', err);
                    overlay.classList.remove('active');
                });
        }

        // --- 1. Filter Submission (Ajax) ---
        // جلوگیری از سابمیت پیش‌فرض فرم و استفاده از ایجکس
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                // ساخت URL جدید با پارامترهای فرم
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                
                // هندل کردن چک‌باکس‌های برند (چون چندتایی هستند)
                // نکته: FormData خودش این کار رو میکنه، اما برای اطمینان از ساختار استاندارد WP:
                // ?filter_brand=brand1,brand2
                const brands = [];
                form.querySelectorAll('input[name="filter_brand[]"]:checked').forEach(cb => brands.push(cb.value));
                if(brands.length > 0) {
                    params.delete('filter_brand[]'); // حذف پیش‌فرض آرایه‌ای
                    params.set('filter_brand', brands.join(',')); // فرمت استاندارد ووکامرس
                }

                // گرفتن مقدار sort جاری
                const currentSort = document.querySelector('.dropdown-option.active')?.dataset.value;
                if(currentSort) params.set('orderby', currentSort);

                const url = window.location.pathname + '?' + params.toString();
                fetchProducts(url);
                
                // در موبایل شیت بسته شود
                toggleSheet(false);
            });
        }

        // لینک‌های دسته‌بندی هم باید ایجکس باشند
        // استفاده از Delegation روی فرم یا کانتینر
        document.querySelectorAll('.filter-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                fetchProducts(url);
            });
        });

        // --- 2. Custom Smooth Dropdown Logic ---
        const dropdown = document.querySelector('.custom-dropdown');
        if(dropdown) {
            const selected = dropdown.querySelector('.dropdown-selected');
            const options = dropdown.querySelectorAll('.dropdown-option');

            selected.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            options.forEach(option => {
                option.addEventListener('click', () => {
                    // تغییر ظاهر فعال
                    options.forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    
                    // آپدیت متن سلکت شده
                    selected.querySelector('span').innerHTML = `<i class="fas fa-sort-amount-down-alt text-orange-500 ml-2"></i> ${option.innerText}`;
                    
                    // اجرای فیلتر (ایجکس)
                    // پارامترهای فعلی URL رو میگیریم و orderby رو آپدیت میکنیم
                    const currentUrl = new URL(window.location.href);
                    currentUrl.searchParams.set('orderby', option.dataset.value);
                    fetchProducts(currentUrl.toString());
                    
                    dropdown.classList.remove('active');
                });
            });

            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }

        // --- 3. Price Range Slider Logic ---
        const rangeInput = document.querySelectorAll(".range-input input");
        const range = document.querySelector(".range-slider .range-progress");
        let priceGap = 100000;

        function updateSlider() {
            let minVal = parseInt(rangeInput[0].value);
            let maxVal = parseInt(rangeInput[1].value);
            const maxRange = parseInt(rangeInput[0].max);

            range.style.right = (minVal / maxRange) * 100 + "%";
            range.style.left = 100 - (maxVal / maxRange) * 100 + "%";
            
            document.getElementById('min-price-display').innerText = new Intl.NumberFormat().format(minVal);
            document.getElementById('max-price-display').innerText = new Intl.NumberFormat().format(maxVal);
            
            document.getElementById('input-min-price').value = minVal;
            document.getElementById('input-max-price').value = maxVal;
        }

        rangeInput.forEach(input => {
            input.addEventListener("input", e => {
                let minVal = parseInt(rangeInput[0].value),
                maxVal = parseInt(rangeInput[1].value);

                if ((maxVal - minVal) < priceGap) {
                    if (e.target.className === "range-min") {
                        rangeInput[0].value = maxVal - priceGap;
                    } else {
                        rangeInput[1].value = minVal + priceGap;
                    }
                } else {
                    updateSlider();
                }
            });
        });
        updateSlider();

        // --- 4. Infinite Scroll Logic ---
        let canLoad = true;
        const loader = document.getElementById('inf-scroll-loader');
        let nextLink = null;

        function updateNextLinkFromDOM() {
            const nextElem = paginationSource.querySelector('.next');
            nextLink = nextElem ? nextElem.getAttribute('href') : null;
        }
        updateNextLinkFromDOM(); // Init

        window.addEventListener('scroll', () => {
            if(!nextLink || !canLoad) return;

            const { scrollTop, scrollHeight, clientHeight } = document.documentElement;
            if (scrollTop + clientHeight >= scrollHeight - 300) {
                canLoad = false;
                loader.style.display = 'block';

                fetch(nextLink)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        const newProducts = doc.querySelectorAll('#product-grid > *');
                        if(newProducts.length > 0) {
                            newProducts.forEach(product => {
                                productGrid.appendChild(product);
                            });
                            
                            // آپدیت لینک صفحه بعد
                            const newNextElem = doc.querySelector('#pagination-source .next');
                            nextLink = newNextElem ? newNextElem.getAttribute('href') : null;
                            
                            // History Update (Optional for infinite scroll, usually not done per page scroll but per filter)
                            // window.history.replaceState(null, null, nextLink); 
                            
                            canLoad = true;
                        } else {
                            nextLink = null;
                        }
                        
                        loader.style.display = 'none';
                    })
                    .catch(err => {
                        console.error('Error loading products:', err);
                        loader.style.display = 'none';
                    });
            }
        });

        // --- 5. Mobile Filters Logic ---
        const filterBtnMobile = document.getElementById('filter-btn-mobile');
        const filterSheet = document.getElementById('filter-sheet');
        const filterContent = document.getElementById('filter-sheet-content');
        const closeFilterBtn = document.getElementById('close-filter-sheet');
        const mobileApplyBtn = document.getElementById('mobile-apply-btn');
        const desktopSidebar = document.getElementById('desktop-filters');
        const mobileContainer = document.getElementById('mobile-filters-container');

        // کپی کردن محتوا به موبایل
        if(desktopSidebar && mobileContainer) {
            // نکته: کپی کردن ID های تکراری مشکل ساز است. باید فرم جدید بسازیم یا ID ها را حذف کنیم.
            // راه ساده: کلون کردن و تغییر ID فرم
            const clonedContent = desktopSidebar.querySelector('form').cloneNode(true);
            clonedContent.id = 'mobile-filter-form'; // تغییر ID
            // حذف ID های داخلی تکراری
            clonedContent.querySelectorAll('[id]').forEach(el => el.removeAttribute('id'));
            
            mobileContainer.innerHTML = '';
            mobileContainer.appendChild(clonedContent);
            
            // Re-attach listeners for mobile form if needed or just use apply button
            mobileApplyBtn.addEventListener('click', () => {
                // تریگر کردن سایدبار دسکتاپ (چون منطق اصلی آنجاست) یا سابمیت فرم موبایل
                // ساده‌ترین راه: مقادیر موبایل را به دسکتاپ منتقل کنیم و فرم دسکتاپ را سابمیت کنیم
                // اما چون AJAX نوشتیم، بهتر است مستقیماً فرم موبایل را هندل کنیم.
                
                // Logic to submit mobile form via AJAX (similar to desktop)
                // ... (Simplified: just reload for now or copy logic)
                const mobileForm = document.getElementById('mobile-filter-form');
                // Trigger submit event manually or call fetchProducts
                // For simplicity here, lets just trigger the submit button inside the cloned form if present
                const submitBtn = mobileForm.querySelector('button[type="submit"]');
                if(submitBtn) submitBtn.click();
            });
        }

        function toggleSheet(show) {
            if(show) {
                filterSheet.classList.remove('hidden');
                setTimeout(() => filterContent.classList.remove('translate-y-full'), 10);
            } else {
                filterContent.classList.add('translate-y-full');
                setTimeout(() => filterSheet.classList.add('hidden'), 300);
            }
        }

        if(filterBtnMobile) filterBtnMobile.addEventListener('click', () => toggleSheet(true));
        if(closeFilterBtn) closeFilterBtn.addEventListener('click', () => toggleSheet(false));
        if(filterSheet) filterSheet.addEventListener('click', (e) => { if(e.target === filterSheet) toggleSheet(false); });
        
        // Handle Back/Forward Browser Buttons
        window.addEventListener('popstate', function(event) {
            if(event.state && event.state.path) {
                fetchProducts(event.state.path, false);
            } else {
                location.reload();
            }
        });

    });
</script>

<?php get_footer(); ?>