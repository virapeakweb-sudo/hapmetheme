<?php
/**
 * The template for displaying comments
 * Path: wp-content/themes/your-theme/comments.php
 */

if ( post_password_required() ) {
    return;
}

// اطمینان از دسترسی به آبجکت محصول
global $product;
if ( ! isset( $product ) && ( is_woocommerce() || get_post_type() === 'product' ) ) {
    $product = wc_get_product( get_the_ID() );
}

// بررسی اینکه آیا در صفحه محصول هستیم
$is_product = ( is_woocommerce() || get_post_type() === 'product' ) && $product;
?>

<div id="comments" class="comments-area mt-8 font-['Vazirmatn']">

    <?php if ( $is_product ) : 
        // محاسبه آمار نظرات برای محصولات
        $average       = $product->get_average_rating();
        $review_count  = $product->get_review_count();
        $rating_counts = $product->get_rating_counts(); // آرایه تعداد ستاره‌ها
    ?>
        <!-- بخش آمار و خلاصه نظرات -->
        <div class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm mb-10 relative overflow-hidden">
            <!-- پس‌زمینه تزئینی -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-orange-50/50 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center relative z-10">
                
                <!-- ستون امتیاز کلی (سمت راست در RTL) -->
                <div class="md:col-span-4 lg:col-span-3 text-center md:text-right md:border-l md:border-slate-100 md:pl-8">
                    <div class="flex flex-col items-center md:items-start">
                        <span class="text-slate-500 text-xs font-bold mb-1">امتیاز کلی خریداران</span>
                        <div class="flex items-end gap-2 mb-2">
                            <span class="text-5xl font-black text-slate-800 leading-none"><?php echo wc_format_decimal($average, 1); ?></span>
                            <span class="text-lg text-slate-400 font-bold mb-1">از ۵</span>
                        </div>
                        <div class="flex text-yellow-400 text-sm mb-2 gap-0.5">
                            <?php 
                            for($i=1; $i<=5; $i++) {
                                if($i <= $average) echo '<i class="fas fa-star"></i>';
                                elseif($i - 0.5 <= $average) echo '<i class="fas fa-star-half-alt"></i>';
                                else echo '<i class="far fa-star text-slate-200"></i>';
                            }
                            ?>
                        </div>
                        <span class="text-slate-400 text-xs bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">براساس <?php echo number_format_i18n($review_count); ?> دیدگاه</span>
                    </div>
                </div>

                <!-- ستون نمودار میله‌ای -->
                <div class="md:col-span-8 lg:col-span-6">
                    <div class="space-y-2">
                        <?php 
                        $stars = array(5, 4, 3, 2, 1);
                        foreach($stars as $star) :
                            $count = isset($rating_counts[$star]) ? $rating_counts[$star] : 0;
                            $percent = $review_count > 0 ? ($count / $review_count) * 100 : 0;
                        ?>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1 w-8 font-bold text-slate-600 justify-end"><?php echo $star; ?> <i class="fas fa-star text-[10px] text-slate-300"></i></span>
                            <div class="flex-grow h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-yellow-400 rounded-full transition-all duration-1000 ease-out" style="width: <?php echo $percent; ?>%"></div>
                            </div>
                            <span class="w-8 text-left text-slate-400"><?php echo number_format_i18n($count); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- دکمه فراخوان ثبت نظر (مخصوص دسکتاپ) -->
                <div class="hidden lg:block lg:col-span-3 text-left">
                     <a href="#review_form_wrapper" class="group flex flex-col items-center justify-center w-full h-full bg-white hover:bg-orange-50 text-slate-600 hover:text-orange-600 rounded-2xl p-4 transition-all cursor-pointer border border-slate-200 hover:border-orange-200 shadow-sm hover:shadow-md">
                        <i class="far fa-edit text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <span class="font-bold text-sm">نظرتان را بنویسید</span>
                        <span class="text-[10px] mt-1 text-slate-400 group-hover:text-orange-400">به سایر کاربران کمک کنید</span>
                     </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ( have_comments() ) : ?>
        
        <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
            <h3 class="font-bold text-xl text-slate-800 flex items-center gap-3">
                <span class="bg-orange-100 text-orange-600 w-8 h-8 rounded-lg flex items-center justify-center text-sm">
                    <i class="fas fa-comment-alt"></i>
                </span>
                نظرات کاربران
            </h3>
            <!-- فیلترها (نمایشی) -->
            <div class="hidden md:flex gap-2 text-xs font-bold text-slate-500">
                <span class="cursor-pointer hover:text-orange-500 transition-colors text-orange-500">جدیدترین</span>
                <span class="text-slate-200">|</span>
                <span class="cursor-pointer hover:text-orange-500 transition-colors">مفیدترین</span>
            </div>
        </div>

        <ul class="comment-list space-y-4 mb-12">
            <?php
            wp_list_comments( array(
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 60,
                'callback'    => 'custom_comment_format_enhanced', // استفاده از تابع جدید
            ) );
            ?>
        </ul>

        <?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
            <nav class="comment-navigation flex justify-center gap-4 my-8" role="navigation">
                <div class="nav-previous"><?php previous_comments_link( '<span class="px-6 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold hover:bg-orange-50 hover:text-orange-500 hover:border-orange-200 transition-all shadow-sm">&larr; نظرات قدیمی‌تر</span>' ); ?></div>
                <div class="nav-next"><?php next_comments_link( '<span class="px-6 py-2 bg-white border border-slate-200 rounded-xl text-sm font-bold hover:bg-orange-50 hover:text-orange-500 hover:border-orange-200 transition-all shadow-sm">نظرات جدیدتر &rarr;</span>' ); ?></div>
            </nav>
        <?php endif; ?>

    <?php else : ?>
        <?php if ( $is_product ) : ?>
             <div class="text-center py-16 bg-slate-50/50 rounded-3xl border-2 border-dashed border-slate-200 mb-12">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm text-slate-300">
                    <i class="far fa-comments text-3xl"></i>
                </div>
                <h4 class="text-slate-700 font-bold text-lg mb-2">هنوز دیدگاهی ثبت نشده است</h4>
                <p class="text-slate-500 text-sm">شما اولین نفری باشید که نظر خود را درباره این محصول ثبت می‌کنید!</p>
             </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- فرم ثبت نظر -->
    <div id="review_form_wrapper" class="bg-white p-6 md:p-10 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 relative overflow-hidden mt-8">
        <!-- المان گرافیکی -->
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-transparent rounded-full opacity-50 blur-2xl pointer-events-none"></div>
        
        <?php
        $commenter = wp_get_current_commenter();
        $req = get_option( 'require_name_email' );
        $aria_req = ( $req ? " aria-required='true'" : '' );

        $fields = array(
            'author' => '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5 relative z-10">' .
                        '<div>' .
                        '<label for="author" class="block text-xs font-bold text-slate-700 mb-2">نام شما <span class="text-red-500">*</span></label>' .
                        '<div class="relative group"><i class="far fa-user absolute right-4 top-3.5 text-slate-400 text-sm group-focus-within:text-orange-500 transition-colors"></i>' .
                        '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pr-10 pl-4 text-sm focus:outline-none focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 transition-all text-slate-700 placeholder-slate-400" placeholder="نام نمایشی خود را وارد کنید" ' . $aria_req . '></div>' .
                        '</div>',
            'email'  => '<div>' .
                        '<label for="email" class="block text-xs font-bold text-slate-700 mb-2">ایمیل شما <span class="text-red-500">*</span></label>' .
                        '<div class="relative group"><i class="far fa-envelope absolute right-4 top-3.5 text-slate-400 text-sm group-focus-within:text-orange-500 transition-colors"></i>' .
                        '<input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 pr-10 pl-4 text-sm focus:outline-none focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 transition-all text-slate-700 placeholder-slate-400" placeholder="ایمیل شما (محفوظ می‌ماند)" ' . $aria_req . '></div>' .
                        '</div></div>',
        );

        $comment_form_args = array(
            'fields' => $fields,
            'comment_field' => '<div class="mb-6 relative z-10">' .
                                '<label for="comment" class="block text-xs font-bold text-slate-700 mb-2">متن دیدگاه <span class="text-red-500">*</span></label>' .
                                '<textarea id="comment" name="comment" cols="45" rows="5" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-500/10 transition-all text-slate-700 leading-relaxed placeholder-slate-400" placeholder="نقاط قوت و ضعف محصول را بنویسید..." aria-required="true"></textarea>' .
                                '</div>',
            'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s w-full md:w-auto bg-gradient-to-r from-orange-500 to-orange-600 text-white font-bold py-4 px-12 rounded-xl shadow-lg shadow-orange-500/30 hover:-translate-y-1 hover:shadow-orange-500/40 transition-all duration-300 text-sm flex items-center justify-center gap-2 relative z-10">%4$s <i class="fas fa-paper-plane text-xs"></i></button>',
            'class_submit' => 'submit',
            'title_reply' => '<div class="flex items-center gap-3 mb-8 border-b border-slate-100 pb-4 relative z-10"><span class="bg-orange-500 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/20"><i class="fas fa-pen-nib"></i></span><div class="flex flex-col"><span class="text-xl font-black text-slate-800">ثبت دیدگاه جدید</span><span class="text-xs text-slate-400 font-normal mt-1">نظر شما به انتخاب سایر کاربران کمک می‌کند</span></div></div>',
            'title_reply_to' => 'پاسخ به %s',
            'cancel_reply_link' => '<span class="text-xs text-red-500 mr-2 font-normal border-b border-red-200 border-dashed hover:text-red-600 cursor-pointer">(لغو)</span>',
            'label_submit' => 'ارسال دیدگاه',
        );

        if ( $is_product && get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
            $comment_form_args['comment_notes_before'] = '';
            // طراحی اختصاصی برای بخش ستاره‌ها
            $comment_form_args['comment_field'] = '<div class="mb-5 bg-orange-50/50 p-4 rounded-xl border border-orange-100 inline-flex flex-col w-full relative z-10"><label for="rating" class="block text-xs font-bold text-slate-700 mb-2">امتیاز شما به این محصول</label><select name="rating" id="rating" required style="display:none;"><option value="">انتخاب کنید...</option><option value="5">عالی</option><option value="4">خوب</option><option value="3">معمولی</option><option value="2">ضعیف</option><option value="1">خیلی بد</option></select></div>' . $comment_form_args['comment_field'];
        }

        comment_form($comment_form_args);
        ?>
    </div>

</div>

<?php
/**
 * تابع جدید فرمت‌دهی کامنت‌ها با طراحی پیشرفته
 * جایگزین تابع قبلی شده است
 */
if ( ! function_exists( 'custom_comment_format_enhanced' ) ) {
    function custom_comment_format_enhanced($comment, $args, $depth) {
        $GLOBALS['comment'] = $comment; 
        
        // استایل برای پاسخ‌ها (تورفتگی در RTL یعنی مارجین راست)
        $child_class = ($depth > 1) ? 'mr-4 md:mr-14 border-r-2 border-slate-100 pr-4 md:pr-6 mt-4 relative before:content-[""] before:absolute before:top-8 before:-right-6 before:w-4 before:h-[2px] before:bg-slate-100' : '';
        
        $rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
        
        // بررسی خریدار بودن (اگر تابع ووکامرس وجود داشته باشد)
        $verified = function_exists('wc_review_is_from_verified_owner') ? wc_review_is_from_verified_owner( $comment->comment_ID ) : false;
        ?>
        
        <li <?php comment_class('relative ' . $child_class); ?> id="li-comment-<?php comment_ID() ?>">
            <div id="comment-<?php comment_ID(); ?>" class="comment-body bg-white p-5 md:p-6 rounded-2xl border border-slate-100 hover:border-orange-200 hover:shadow-lg hover:shadow-slate-100/50 transition-all duration-300 group">
                
                <div class="flex items-start gap-4">
                    <!-- آواتار -->
                    <div class="shrink-0 hidden sm:block">
                        <div class="relative">
                            <?php echo get_avatar($comment, $args['avatar_size'], '', '', array('class' => 'rounded-2xl shadow-sm border border-slate-100 bg-slate-50')); ?>
                            <?php if ($verified): ?>
                                <span class="absolute -bottom-2 -right-2 bg-green-500 text-white text-[10px] w-6 h-6 flex items-center justify-center rounded-full border-2 border-white shadow-sm z-10" title="خریدار محصول">
                                    <i class="fas fa-check"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex-grow min-w-0">
                        <!-- هدر کامنت -->
                        <div class="flex flex-wrap justify-between items-start mb-3 gap-2">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <!-- آواتار موبایل -->
                                    <div class="sm:hidden shrink-0">
                                         <?php echo get_avatar($comment, 32, '', '', array('class' => 'rounded-full border border-slate-100')); ?>
                                    </div>

                                    <h5 class="font-bold text-slate-800 text-sm">
                                        <?php echo get_comment_author_link(); ?>
                                    </h5>
                                    
                                    <?php if ($verified): ?>
                                        <span class="bg-green-50 text-green-600 text-[10px] px-2 py-0.5 rounded-full border border-green-100 flex items-center gap-1 font-medium select-none">
                                            <i class="fas fa-shield-alt text-[9px]"></i> خریدار
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-[11px] text-slate-400 flex items-center gap-2">
                                    <span><?php printf(__('%1$s'), get_comment_date('j F Y')); ?></span>
                                </div>
                            </div>

                            <!-- ستاره‌ها -->
                            <?php if ( $rating && $rating > 0 ) : ?>
                                <div class="bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-100 flex items-center gap-1.5 shadow-sm">
                                    <span class="text-xs font-bold text-slate-700 pt-0.5"><?php echo $rating; ?>.0</span>
                                    <div class="flex text-yellow-400 text-[10px] gap-0.5">
                                        <?php for($i=1; $i<=5; $i++) {
                                            echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star text-slate-300"></i>';
                                        } ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- متن در انتظار تایید -->
                        <?php if ($comment->comment_approved == '0') : ?>
                            <div class="text-xs text-orange-600 bg-orange-50 p-3 rounded-xl mb-3 border border-orange-100 flex items-center gap-2 animate-pulse">
                                <i class="fas fa-hourglass-half"></i>
                                <span>دیدگاه شما دریافت شد و پس از تایید نمایش داده می‌شود.</span>
                            </div>
                        <?php endif; ?>

                        <!-- متن اصلی -->
                        <div class="text-sm text-slate-600 leading-7 text-justify mb-4 font-light break-words">
                            <?php comment_text(); ?>
                        </div>

                        <!-- فوتر کامنت -->
                        <div class="flex items-center justify-between border-t border-slate-50 pt-3 mt-2">
                             <!-- دکمه پاسخ -->
                             <div class="flex items-center">
                                <?php comment_reply_link(array_merge($args, array(
                                    'depth' => $depth,
                                    'max_depth' => $args['max_depth'],
                                    'reply_text' => '<span class="flex items-center gap-1.5 bg-slate-50 hover:bg-orange-500 hover:text-white px-3 py-1.5 rounded-lg transition-colors border border-slate-100 hover:border-orange-500"><i class="fas fa-reply text-xs"></i> پاسخ</span>',
                                    'class' => 'text-xs font-bold text-slate-500'
                                ))); ?>
                             </div>
                             
                             <!-- دکمه‌های لایک (نمایشی) -->
                             <div class="flex items-center gap-4 text-slate-300 text-xs">
                                 <div class="flex items-center gap-1 cursor-pointer hover:text-green-500 transition-colors" title="مفید بود">
                                     <span class="hidden md:inline text-[10px]">مفید بود؟</span>
                                     <i class="far fa-thumbs-up text-sm"></i>
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- تگ li به صورت خودکار توسط وردپرس بسته می‌شود -->
    <?php
    }
}
?>