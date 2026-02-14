<?php
/* Path: wp-content/themes/hapomeo/functions.php
توضیحات: موتور اصلی قالب. فایل‌های استایل و اسکریپت را اینجا فراخوانی می‌کنیم و پشتیبانی ووکامرس را فعال می‌کنیم.
*/

function hapomeo_setup() {
    // فعال‌سازی پشتیبانی از ووکامرس
    add_theme_support( 'woocommerce' );
    
    // فعال‌سازی تایتل داینامیک
    add_theme_support( 'title-tag' );

    // فعال‌سازی تصاویر شاخص
    add_theme_support( 'post-thumbnails' );

    // ثبت منوها
    register_nav_menus( array(
        'primary' => __( 'منوی اصلی', 'hapomeo' ),
        'mobile'  => __( 'منوی موبایل', 'hapomeo' ),
    ) );
}
add_action( 'after_setup_theme', 'hapomeo_setup' );

function hapomeo_scripts() {
    // فراخوانی Tailwind CDN (طبق فایل HTML شما)
    wp_enqueue_script( 'tailwindcss', 'https://hapoomeo.com/wp-content/themes/hapmetheme/tailwindcss.js', array(), null, false );

    // فراخوانی FontAwesome
    wp_enqueue_style( 'fontawesome', 'https://hapoomeo.com/wp-content/themes/hapmetheme/fonts/css/all.min.css', array(), '6.5.1' );

    // فراخوانی فونت وزیرمتن
  //  wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;600;700;800&display=swap', array(), null );

    // استایل اصلی قالب
    wp_enqueue_style( 'hapomeo-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'hapomeo_scripts' );


function my_theme_setup() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'my_theme_setup' );
?>

<?php
// این کدها را به فایل functions.php قالب خود اضافه کنید

// 1. استایل دهی به فیلدهای ورودی (Inputs)
add_filter('woocommerce_form_field_args', 'hapoomio_custom_checkout_fields', 10, 3);
function hapoomio_custom_checkout_fields($args, $key, $value) {
    // کلاس‌های مشترک برای تمام اینپوت‌ها
    $input_classes = array(
        'w-full', 'bg-slate-50', 'border', 'border-slate-200', 'rounded-xl', 
        'px-4', 'py-3', 'outline-none', 'focus:border-orange-500', 
        'focus:ring-1', 'focus:ring-orange-500', 'transition-all', 'text-sm'
    );

    // افزودن کلاس به خود اینپوت
    $args['input_class'] = array_merge($args['input_class'], $input_classes);
    
    // کلاس لیبل‌ها
    $args['label_class'] = array('text-sm', 'font-semibold', 'text-slate-600', 'mb-1', 'block');
    
    // حذف کلاس‌های پیشفرض پاراگراف ووکامرس و جایگزینی با دیو
    $args['class'] = array('form-row-wide', 'mb-4');
    
    return $args;
}

// 2. تغییر متن دکمه پرداخت
add_filter( 'woocommerce_order_button_text', 'hapoomio_custom_order_button_text' ); 
function hapoomio_custom_order_button_text( $order_button_text ) {
    return 'پرداخت و ثبت نهایی'; 
}

// این تابع بافر خروجی را شروع می‌کند
function start_buffer_replacement() {
    // شروع بافر و تعریف تابع کالبک برای تغییر محتوا
    ob_start( function( $buffer ) {
        // آدرس قدیمی
        $old_url = 'https://cdn.tailwindcss.com';
        
        // آدرس جدید
        $new_url = 'https://hapoomeo.com/wp-content/themes/hapmetheme/tailwindcss.js';

        // جایگزینی ساده در کل کد HTML خروجی
        return str_replace( $old_url, $new_url, $buffer );
    } );
}

// این هوک باعث می‌شود بافر قبل از لود شدن قالب شروع شود
add_action( 'template_redirect', 'start_buffer_replacement' );



// تابع جایگزینی لینک CDN مربوط به Chart.js
function replace_chartjs_cdn_buffer() {
    // شروع بافر و تعریف تابع کالبک برای تغییر محتوا
    ob_start( function( $buffer ) {
        // آدرس قدیمی (که می‌خواهید حذف شود)
        $old_url = 'https://cdn.jsdelivr.net/npm/chart.js';
        
        // آدرس جدید (آدرس فایل لوکال یا لینک جایگزین خود را اینجا بنویسید)
        // مثال: 'https://yoursite.com/assets/js/chart.js'
        $new_url = 'https://hapoomeo.com/wp-content/themes/hapmetheme/js/chart.js';

        // بررسی اینکه آیا آدرس جدید مقداردهی شده است یا خیر
        if ( ! empty( $new_url ) ) {
            // جایگزینی ساده در کل کد HTML خروجی
            return str_replace( $old_url, $new_url, $buffer );
        }

        return $buffer;
    } );
}

// این هوک باعث می‌شود بافر قبل از لود شدن قالب شروع شود
add_action( 'template_redirect', 'replace_chartjs_cdn_buffer' );



function fix_responsive_images_and_captions_single() {
    if ( is_single() ) {
        ?>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                // 1. پیدا کردن کانتینر اصلی پست
                // نکته: اگر قالب شما از entry-content استفاده می‌کند، اینجا را تغییر دهید
                var container = document.querySelector('.post-content') || document.querySelector('.entry-content') || document.body;

                // 2. پیدا کردن تمام عکس‌ها داخل کانتینر
                var images = container.querySelectorAll('img');

                images.forEach(function(img) {
                    // --- اصلاح خود عکس ---
                    img.removeAttribute('width');
                    img.removeAttribute('height');
                    img.style.width = '100%';
                    img.style.height = 'auto'; 
                    img.style.maxWidth = '100%'; // اطمینان بیشتر

                    // --- اصلاح کانتینر کپشن (wp-caption) ---
                    // بررسی می‌کنیم آیا این عکس داخل یک div با کلاس wp-caption است؟
                    var captionWrapper = img.closest('.wp-caption');
                    
                    if (captionWrapper) {
                        // حذف عرض ثابت (مثل 1410px) از استایل اینلاین دیو
                        captionWrapper.style.width = ''; 
                        
                        // اجبار کردن دیو به اینکه از صفحه بیرون نزند
                        captionWrapper.style.maxWidth = '100%';
                        captionWrapper.style.height = 'auto';
                        
                        // حذف margin های مزاحم احتمالی (اختیاری)
                        captionWrapper.style.display = 'block';
                    }
                });
            });
        </script>
        <?php
    }
}
add_action('wp_footer', 'fix_responsive_images_and_captions_single');

//start oldversion

// Create Shortcode hs_vid
// Shortcode: [hs_vid youtube_id="" aparat_id=""]


function create_hsvid_shortcode($atts) {

 $atts = shortcode_atts(
  array(
   'youtube_id' => '',
   'aparat_id' => '',
  ),
  $atts,
  'hs_vid'
 );

 $youtube_id = $atts['youtube_id'];
 $aparat_id = $atts['aparat_id'];
 



    if($aparat_id){
        return '<div id="'.$aparat_id.'"><script type="text/JavaScript" src="https://www.aparat.com/embed/'.$aparat_id.'?data[rnddiv]='.$aparat_id.'&data[responsive]=yes&titleShow=true&recom=self"></script></div>';

    }


}
add_shortcode( 'hs_vid', 'create_hsvid_shortcode' );



/**
 * تغییر ترتیب فیلدهای استان و شهر در صفحه پرداخت ووکامرس
 * Move State field before City field on the checkout page
 */
add_filter( 'woocommerce_checkout_fields', 'reorder_state_city_checkout_fields' );

function reorder_state_city_checkout_fields( $fields ) {

    // به صورت پیش‌فرض، اولویت فیلد شهر 70 و استان 80 است.
    // ما اولویت استان را کمتر می‌کنیم تا بالاتر نمایش داده شود.

    // تغییر ترتیب برای فیلدهای صورتحساب (Billing)
    $fields['billing']['billing_state']['priority'] = 70;
    $fields['billing']['billing_city']['priority'] = 80;

    // تغییر ترتیب برای فیلدهای حمل و نقل (Shipping)
    $fields['shipping']['shipping_state']['priority'] = 70;
    $fields['shipping']['shipping_city']['priority'] = 80;

    return $fields;
}



function tthd_add_field_to_shipping_tab() {
    echo '<div class="options_group">';
    woocommerce_wp_checkbox(
        array(
            'id'            => '_two_hour_delivery',
            'label'         => 'ارسال فوری تهران',
            'description'   => 'با فعال کردن این گزینه، پیغام "امکان ارسال دو ساعته در تهران" در صفحه محصول نمایش داده می‌شود.',
            'desc_tip'      => true,
        )
    );
    echo '</div>';
}
add_action( 'woocommerce_product_options_shipping', 'tthd_add_field_to_shipping_tab' );


/**
 * بخش دوم: ذخیره کردن مقدار چک‌باکس
 */
function tthd_save_shipping_tab_field( $product ) {
    $delivery_value = isset( $_POST['_two_hour_delivery'] ) ? 'yes' : 'no';
    $product->update_meta_data( '_two_hour_delivery', $delivery_value );
}
add_action( 'woocommerce_admin_process_product_object', 'tthd_save_shipping_tab_field' );




/**
 * بخش چهارم: ساخت و ثبت شورت‌کد
 * این تابع، منطق اصلی نمایش پیغام را در خود دارد.
 */
function tthd_delivery_message_shortcode() {
    global $product;

    // اطمینان از اینکه در یک صفحه محصول هستیم و محصول معتبر است
    if ( is_a( $product, 'WC_Product' ) && 'yes' === $product->get_meta( '_two_hour_delivery' ) ) {
        // در شورت‌کدها باید محتوا را return کرد، نه echo
        return '<p class="two-hour-delivery-notice" style="color: #2a7a2a; font-weight: bold;">🚀 امکان ارسال دو ساعته در تهران</p>';
    }

    // اگر شرط برقرار نبود، چیزی برنگردان
    return '';
}
// ثبت شورت‌کد با نام [tehran_delivery]
add_shortcode( 'tehran_delivery', 'tthd_delivery_message_shortcode' );



add_filter( 'woocommerce_get_price_html', 'hide_price_for_out_of_stock_products', 10, 2 );

function hide_price_for_out_of_stock_products( $price, $product ) {
    if ( ! $product->is_in_stock() ) {
        return ''; // برگرداندن یک رشته خالی برای حذف قیمت
    }
    return $price; // در غیر این صورت، قیمت اصلی را نمایش بده
}






function rtlTextHasString($text, $string) {
	return strpos($text, $string) !== false;
}


function rtlBlockExternalHostRequests ($false, $parsed_args, $url) {
	$blockedHosts = [
		'elementor.com',
		'github.com',
		'yoast.com',
		'yoa.st',
		'api.wordpress.org',
		'w.org',
		'unyson.io',
		'siteorigin.com',
		'secure.gravatar.com',
		'gravatar.com',
		'woocommerce.com'
	];

	foreach ( $blockedHosts as $host ) {
		if ( !empty($host) && rtlTextHasString($url, $host) ) {
			return [
				'headers'  => '',
				'body'     => '',
				'response' => '',
				'cookies'  => '',
				'filename' => ''
			];
		}
	}

	return $false;
}
add_filter('pre_http_request', 'rtlBlockExternalHostRequests', 10, 3);