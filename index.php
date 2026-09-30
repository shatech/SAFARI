<?php
declare(strict_types=1);

$pageTitle = 'صفری سازه | سازه LSF، فوم XPS، ساندویچ پنل و عایق ساختمانی';
$pageDescription = 'با محصولات صفری سازه در حوزه سازه‌های سبک LSF، فوم XPS، ساندویچ پنل و عایق‌های ساختمانی آشنا شوید. برای دریافت اطلاعات و مشاوره متناسب با پروژه با ما در تماس باشید.';
$pageUrl = ''; // پس از مشخص‌شدن دامنه، آدرس اصلی صفحه را اینجا قرار دهید.
$siteName = 'صفری سازه';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

require __DIR__ . '/includes/header.php';
?>

<main id="main-content">

    <!-- Hero -->
    <section class="hero-section" id="home">
        <div class="hero-orb hero-orb--one" aria-hidden="true"></div>
        <div class="hero-orb hero-orb--two" aria-hidden="true"></div>

        <div class="container hero-container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="hero-content reveal">
                        <div class="eyebrow eyebrow--light">
                            <span class="eyebrow-dot"></span>
                            راهکارهایی برای ساخت‌وساز امروز
                        </div>

                        <h1>
                            انتخاب هوشمندانه<br>
                            برای <span class="text-highlight">ساخت بهتر</span>
                        </h1>

                        <p class="hero-description">
                            از سازه‌های سبک LSF تا فوم XPS، ساندویچ پنل و محصولات عایق‌بندی؛
                            صفری سازه همراه شماست تا با شناخت بهتر نیاز پروژه،
                            محصول مناسب‌تری انتخاب کنید.
                        </p>

                        <div class="hero-actions">
                            <a class="btn btn-accent btn-lg" href="#products">
                                بررسی محصولات
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-hero-outline btn-lg" href="#contact">
                                درخواست مشاوره
                            </a>
                        </div>

                        <div class="hero-note">
                            <i class="bi bi-info-circle" aria-hidden="true"></i>
                            برای مشخصات، موجودی و استعلام قیمت با ما تماس بگیرید.
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="hero-visual reveal reveal-delay-1">
                        <div class="hero-image-wrap">
                            <img
                                class="hero-image"
                                src="/assets/images/hero-lsf-construction.webp"
                                alt="نمای معماری مدرن و پروژه ساختمانی"
                                fetchpriority="high"
                                width="1200"
                                height="900"
                            >
                            <div class="hero-image-shade"></div>

                            <div class="hero-image-caption">
                                <span class="caption-icon">
                                    <i class="bi bi-buildings" aria-hidden="true"></i>
                                </span>
                                <span>
                                    <strong>راهکار متناسب با پروژه</strong>
                                    <small>اطلاعات دقیق‌تر، انتخاب مطمئن‌تر</small>
                                </span>
                            </div>
                        </div>

                        <div class="hero-stamp" aria-hidden="true">
                            <span>صفری</span>
                            <strong>سازه</strong>
                            <i class="bi bi-arrow-up-left"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="hero-bottom-line" aria-hidden="true"></div>
    </section>

    <!-- Highlights -->
    <section class="highlights-section" aria-label="مزیت‌های همکاری با صفری سازه">
        <div class="container">
            <div class="highlights-panel reveal">
                <div class="highlight-item">
                    <span class="highlight-icon">
                        <i class="bi bi-box-seam" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>محصولات تخصصی</h2>
                        <p>تمرکز بر محصولات ساختمانی منتخب</p>
                    </div>
                </div>

                <div class="highlight-item">
                    <span class="highlight-icon">
                        <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>راهنمایی پیش از خرید</h2>
                        <p>بررسی نیاز پروژه و کاربرد محصول</p>
                    </div>
                </div>

                <div class="highlight-item">
                    <span class="highlight-icon">
                        <i class="bi bi-clipboard-check" aria-hidden="true"></i>
                    </span>
                    <div>
                        <h2>پیگیری درخواست</h2>
                        <p>ارتباط روشن برای ادامه فرایند</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick guide -->
    <section class="quick-guide-section" aria-labelledby="quick-guide-title">
        <div class="container">
            <div class="quick-guide-heading reveal">
                <div>
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        راهنمای سریع انتخاب
                    </div>

                    <h2 id="quick-guide-title">برای پروژه‌تان دنبال چه راهکاری هستید؟</h2>

                    <p>
                        از نیاز پروژه شروع کنید تا سریع‌تر به گروه محصول مرتبط برسید.
                    </p>
                </div>

                <span class="quick-guide-badge">
                    <i class="bi bi-compass" aria-hidden="true"></i>
                    شروع انتخاب
                </span>
            </div>

            <div class="row g-3">
                <div class="col-md-4">
                    <a class="guide-card reveal" href="#lsf-product">
                        <span class="guide-card-icon">
                            <i class="bi bi-buildings" aria-hidden="true"></i>
                        </span>
                        <span class="guide-card-content">
                            <strong>سازه سبک LSF</strong>
                            <small>برای آشنایی با سازه و سیستم قاب فولادی سبک</small>
                        </span>
                        <i class="bi bi-arrow-left guide-card-arrow" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="guide-card reveal reveal-delay-1" href="#insulation-product">
                        <span class="guide-card-icon">
                            <i class="bi bi-layers" aria-hidden="true"></i>
                        </span>
                        <span class="guide-card-content">
                            <strong>فوم و عایق ساختمانی</strong>
                            <small>برای بررسی محصولات عایق‌کاری ساختمان</small>
                        </span>
                        <i class="bi bi-arrow-left guide-card-arrow" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="guide-card reveal reveal-delay-2" href="#xps-product">
                        <span class="guide-card-icon">
                            <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                        </span>
                        <span class="guide-card-content">
                            <strong>فوم XPS</strong>
                            <small>مشاهده اطلاعات فوم XPS و کاربردهای آن در پروژه</small>
                        </span>
                        <i class="bi bi-arrow-left guide-card-arrow" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="guide-card reveal" href="#sandwich-panel-product">
                        <span class="guide-card-icon">
                            <i class="bi bi-columns-gap" aria-hidden="true"></i>
                        </span>
                        <span class="guide-card-content">
                            <strong>ساندویچ پنل</strong>
                            <small>برای بررسی پنل‌ها و دریافت اطلاعات محصول</small>
                        </span>
                        <i class="bi bi-arrow-left guide-card-arrow" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="col-md-4">
                    <a class="guide-card reveal reveal-delay-1" href="#contact">
                        <span class="guide-card-icon">
                            <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                        </span>
                        <span class="guide-card-content">
                            <strong>هنوز مطمئن نیستید؟</strong>
                            <small>نیاز پروژه را بنویسید و درخواست اطلاعات ثبت کنید</small>
                        </span>
                        <i class="bi bi-arrow-left guide-card-arrow" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <p class="quick-guide-note">
                انتخاب نهایی محصول باید با توجه به مشخصات فنی و شرایط پروژه انجام شود.
            </p>
        </div>
    </section>

    <!-- Products -->
    <section class="section products-section" id="products">
        <div class="container">
            <div class="section-heading reveal">
                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    گروه‌های محصول
                </div>
                <h2>برای پروژه‌تان دنبال چه چیزی هستید؟</h2>
                <p>
                    دسته‌بندی محصولات را بررسی کنید یا برای دریافت اطلاعات دقیق‌تر،
                    درخواستتان را برای ما بفرستید.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <article class="product-card reveal" id="lsf-product">
                        <a class="product-image-link" href="#contact" aria-label="اطلاعات بیشتر درباره سازه LSF">
                            <div class="product-image product-image--lsf">
                                <img
                                    src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=900&q=80"
                                    alt="نمای ساختمان با معماری مدرن"
                                    loading="lazy"
                                    width="900"
                                    height="650"
                                >
                                <span class="product-label">سازه سبک</span>
                            </div>
                        </a>
                        <div class="product-card-body">
                            <div class="product-card-topline">گروه محصولات</div>
                            <h3><a href="#contact">سازه و سیستم LSF</a></h3>
                            <p>
                                LSF به سیستم قاب فولادی سبک گفته می‌شود. برای آشنایی با
                                اجزای موردنیاز و بررسی شرایط پروژه، اطلاعات اولیه را با ما
                                در میان بگذارید.
                            </p>
                            <a class="card-link" href="#contact">
                                دریافت اطلاعات
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="product-card reveal reveal-delay-1" id="insulation-product">
                        <a class="product-image-link" href="#contact" aria-label="اطلاعات درباره عایق‌های ساختمانی">
                            <div class="product-image product-image--insulation">
                                <img
                                    src="https://images.unsplash.com/photo-1620626011761-996317b8d101?auto=format&fit=crop&w=900&q=80"
                                    alt="فضای داخلی ساختمان در حال تکمیل"
                                    loading="lazy"
                                    width="900"
                                    height="650"
                                >
                                <span class="product-label">عایق‌بندی</span>
                            </div>
                        </a>
                        <div class="product-card-body">
                            <div class="product-card-topline">گروه محصولات</div>
                            <h3><a href="#insulation-showcase">عایق‌های ساختمانی</a></h3>
                            <p>
                                عایق‌کاری فقط به انتخاب یک محصول محدود نمی‌شود؛ محل استفاده،
                                جزئیات اجرا و هماهنگی با بخش‌های دیگر ساختمان هم اهمیت دارند.
                            </p>
                            <a class="card-link" href="#insulation-showcase">
                                آشنایی با عایق‌کاری
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="product-card reveal reveal-delay-2" id="xps-product">
                        <a class="product-image-link" href="#contact" aria-label="اطلاعات بیشتر درباره فوم XPS">
                            <div class="product-image product-image--insulation">
                                <img
                                    src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=900&q=80"
                                    alt="مصالح و فعالیت در یک پروژه ساختمانی"
                                    loading="lazy"
                                    width="900"
                                    height="650"
                                >
                                <span class="product-label">فوم XPS</span>
                            </div>
                        </a>
                        <div class="product-card-body">
                            <div class="product-card-topline">عایق ساختمانی</div>
                            <h3><a href="#contact">فوم XPS</a></h3>
                            <p>
                                فوم XPS به‌صورت تخته‌ای عرضه می‌شود. کاربرد، ابعاد و مشخصات
                                موردنیاز را با توجه به جزئیات پروژه بررسی کنید.
                            </p>
                            <a class="card-link" href="#contact">
                                استعلام فوم XPS
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="product-card reveal" id="sandwich-panel-product">
                        <a class="product-image-link" href="#contact" aria-label="اطلاعات بیشتر درباره ساندویچ پنل">
                            <div class="product-image product-image--building">
                                <img
                                    src="https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=900&q=80"
                                    alt="اجرای پروژه ساختمانی"
                                    loading="lazy"
                                    width="900"
                                    height="650"
                                >
                                <span class="product-label">ساندویچ پنل</span>
                            </div>
                        </a>
                        <div class="product-card-body">
                            <div class="product-card-topline">محصولات ساختمانی</div>
                            <h3><a href="#contact">ساندویچ پنل</a></h3>
                            <p>
                                ساندویچ پنل از لایه‌های پوششی و یک هسته میانی تشکیل می‌شود.
                                نوع پنل و مشخصات آن را متناسب با کاربرد و محل پروژه بررسی کنید.
                            </p>
                            <a class="card-link" href="#contact">
                                استعلام ساندویچ پنل
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="product-card reveal reveal-delay-1">
                        <a class="product-image-link" href="#contact" aria-label="استعلام سایر محصولات ساختمانی">
                            <div class="product-image product-image--building">
                                <img
                                    src="https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=900&q=80"
                                    alt="فعالیت در یک پروژه ساختمانی"
                                    loading="lazy"
                                    width="900"
                                    height="650"
                                >
                                <span class="product-label">سایر محصولات</span>
                            </div>
                        </a>
                        <div class="product-card-body">
                            <div class="product-card-topline">نیاز پروژه شما</div>
                            <h3><a href="#contact">محصولات مکمل ساختمان</a></h3>
                            <p>
                                اگر محصول مشخصی مدنظرتان است، نام آن را ثبت کنید تا اطلاعات
                                و امکان تأمین آن برای پروژه بررسی شود.
                            </p>
                            <a class="card-link" href="#contact">
                                ثبت درخواست
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>
            </div>

            <div class="products-bottom reveal">
                <span>محصول موردنظرتان را پیدا نکردید؟</span>
                <a href="#contact">
                    درخواستتان را برای ما ارسال کنید
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Interactive insulation before / after -->
    <section class="section sf-insulation-showcase" id="insulation-showcase">
        <div class="container">
            <div class="section-heading sf-showcase-heading sf-reveal-up">
                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    عایق‌کاری را تصویری ببینید
                </div>
                <h2>قبل و بعد از عایق‌کاری؛ یک تصویر برای فهم بهتر</h2>
                <p>
                    تصویر زیر یک نمایش مفهومی از تفاوت وجود لایه عایق در جداره ساختمان است.
                    دستگیره را جابه‌جا کنید و دو حالت را کنار هم ببینید.
                </p>
            </div>

            <div class="sf-showcase-layout">
                <div class="sf-compare-wrap sf-reveal-up">
                    <div
                        class="sf-thermal-card"
                        id="thermalCompare"
                        role="group"
                        aria-label="نمایش تعاملی و مفهومی قبل و بعد از عایق‌کاری"
                    >
                        <!-- حالت قبل از عایق‌کاری -->
                        <div class="sf-thermal-scene sf-thermal-scene--before">
                            <span class="sf-scene-sun" aria-hidden="true"></span>
                            <span class="sf-scene-cloud" aria-hidden="true"></span>
                            <span class="sf-scene-ground" aria-hidden="true"></span>

                            <span class="sf-heat-wave sf-heat-wave--one" aria-hidden="true"></span>
                            <span class="sf-heat-wave sf-heat-wave--two" aria-hidden="true"></span>
                            <span class="sf-heat-wave sf-heat-wave--three" aria-hidden="true"></span>

                            <div class="sf-scene-house" aria-hidden="true">
                                <div class="sf-house-roof"></div>
                                <div class="sf-house-body">
                                    <div class="sf-house-room"></div>
                                    <div class="sf-house-window"></div>
                                    <div class="sf-house-door"></div>
                                </div>
                            </div>

                            <span class="sf-scene-tag">
                                <i class="bi bi-sun" aria-hidden="true"></i>
                                پیش از عایق‌کاری
                            </span>

                            <div class="sf-scene-caption">
                                <span>نمایش شماتیک</span>
                                <span>مسیر تبادل حرارت</span>
                            </div>
                        </div>

                        <!-- حالت پس از عایق‌کاری، لایه قابل جابه‌جایی -->
                        <div class="sf-thermal-scene sf-thermal-scene--after sf-thermal-scene--overlay" id="thermalAfter">
                            <span class="sf-scene-sun" aria-hidden="true"></span>
                            <span class="sf-scene-cloud" aria-hidden="true"></span>
                            <span class="sf-scene-ground" aria-hidden="true"></span>

                            <span class="sf-heat-wave sf-heat-wave--one" aria-hidden="true"></span>
                            <span class="sf-heat-wave sf-heat-wave--two" aria-hidden="true"></span>
                            <span class="sf-heat-wave sf-heat-wave--three" aria-hidden="true"></span>

                            <div class="sf-scene-house" aria-hidden="true">
                                <div class="sf-house-roof"></div>
                                <div class="sf-house-body">
                                    <div class="sf-house-room"></div>
                                    <div class="sf-house-window"></div>
                                    <div class="sf-house-door"></div>
                                </div>
                                <span class="sf-insulation-layer"></span>
                            </div>

                            <span class="sf-scene-tag sf-scene-tag--after">
                                <i class="bi bi-shield-check" aria-hidden="true"></i>
                                با لایه عایق
                            </span>

                            <div class="sf-scene-caption">
                                <span>نمایش شماتیک</span>
                                <span>وجود لایه عایق در جداره</span>
                            </div>
                        </div>

                        <div class="sf-compare-divider" id="thermalDivider" aria-hidden="true">
                            <span class="sf-compare-handle">
                                <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                            </span>
                        </div>

                        <label class="visually-hidden" for="thermalSlider">
                            جابه‌جایی مرز نمایش قبل و بعد از عایق‌کاری
                        </label>
                        <input
                            class="sf-compare-range"
                            type="range"
                            id="thermalSlider"
                            min="0"
                            max="100"
                            value="50"
                            aria-label="مقایسه تصویری قبل و بعد از عایق‌کاری"
                            aria-valuetext="نمایش نیمی از حالت عایق‌کاری‌شده"
                        >
                    </div>

                    <div class="sf-compare-controls" aria-hidden="true">
                        <strong>پس از عایق‌کاری</strong>
                        <span>دستگیره را جابه‌جا کنید</span>
                        <strong>پیش از عایق‌کاری</strong>
                    </div>
                </div>

                <div class="sf-showcase-copy sf-reveal-up">
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        عایق‌کاری یک تصمیم پروژه‌ای است
                    </div>
                    <h3>لایه عایق، بخشی از عملکرد جداره ساختمان است.</h3>
                    <p>
                        در یک ساختمان، انتقال گرما می‌تواند از مسیرهای مختلفی مثل دیوار،
                        سقف، کف، پنجره‌ها و محل اتصال اجزا اتفاق بیفتد. طراحی و اجرای درست
                        لایه‌های ساختمان کمک می‌کند این مسیرها آگاهانه‌تر بررسی شوند.
                    </p>
                    <p>
                        انتخاب محصول به‌تنهایی کافی نیست؛ محل نصب، پیوستگی لایه عایق،
                        جزئیات اتصال، شرایط رطوبتی و سازگاری با اجزای دیگر هم باید در نظر
                        گرفته شوند. نوع محصول مناسب را باید بر پایه مشخصات فنی و شرایط
                        واقعی پروژه انتخاب کرد.
                    </p>

                    <div class="sf-showcase-note">
                        <i class="bi bi-info-circle" aria-hidden="true"></i>
                        <span>
                            این تصویر برای توضیح مفهوم عایق‌کاری طراحی شده و نتیجه واقعی،
                            مقدار صرفه‌جویی یا عملکرد یک محصول مشخص را نمایش نمی‌دهد.
                        </span>
                    </div>

                    <a class="btn btn-dark-custom mt-3" href="#contact">
                        درباره عایق مناسب پروژه‌ام بپرسم
                        <i class="bi bi-arrow-left" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            <div class="sf-insulation-points">
                <article class="sf-insulation-point sf-reveal-up">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i>
                    <strong>محل مصرف را مشخص کنید</strong>
                    <p>دیوار، سقف، کف یا جزئیات دیگر ساختمان نیازهای یکسانی ندارند.</p>
                </article>

                <article class="sf-insulation-point sf-reveal-up">
                    <i class="bi bi-rulers" aria-hidden="true"></i>
                    <strong>مشخصات را با پروژه بسنجید</strong>
                    <p>ابعاد، ضخامت و ویژگی‌های محصول باید با طرح و شرایط اجرا هماهنگ باشند.</p>
                </article>

                <article class="sf-insulation-point sf-reveal-up">
                    <i class="bi bi-link-45deg" aria-hidden="true"></i>
                    <strong>جزئیات اجرا را فراموش نکنید</strong>
                    <p>اتصال‌ها، درزها و پیوستگی لایه‌ها در بررسی عملکرد جداره اهمیت دارند.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Insulation explainer -->
    <section class="section">
        <div class="container">
            <div class="sf-product-story sf-reveal-up">
                <div class="sf-product-story-inner">
                    <div>
                        <div class="eyebrow eyebrow--light">
                            <span class="eyebrow-dot"></span>
                            انتخاب آگاهانه برای ساختمان
                        </div>
                        <h2>عایق خوب، از شناخت درست نیاز پروژه شروع می‌شود.</h2>
                        <p>
                            هدف این است که به‌جای انتخاب بر اساس اسم محصول یا یک ویژگی تنها،
                            شرایط واقعی پروژه را بررسی کنیم: محصول کجا استفاده می‌شود؟
                            چه ابعادی لازم است؟ چه لایه‌هایی کنار آن قرار می‌گیرند؟
                            پاسخ این پرسش‌ها مسیر بررسی فوم XPS، ساندویچ پنل یا سایر
                            محصولات عایق را روشن‌تر می‌کند.
                        </p>
                    </div>
                    <div class="sf-product-story-icon" aria-hidden="true">
                        <i class="bi bi-house-gear"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Use cases -->
    <section class="section">
        <div class="container">
            <div class="section-heading sf-reveal-up">
                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    از کجا شروع کنیم؟
                </div>
                <h2>کاربرد را بگویید؛ مسیر بررسی را پیدا می‌کنیم.</h2>
                <p>
                    این موارد فقط نقطه شروع گفت‌وگو هستند. انتخاب محصول و جزئیات اجرایی
                    باید با توجه به نقشه، شرایط محل و مشخصات فنی بررسی شوند.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <article class="sf-usecase-card sf-reveal-up">
                        <span class="sf-usecase-icon">
                            <i class="bi bi-bricks" aria-hidden="true"></i>
                        </span>
                        <h3>عایق‌کاری در اجزای ساختمان</h3>
                        <p>
                            مشخص کنید عایق برای کدام بخش در نظر گرفته شده و در آن بخش،
                            چه محدودیت‌هایی از نظر فضا، زیرسازی و اجرا وجود دارد.
                        </p>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="sf-usecase-card sf-reveal-up">
                        <span class="sf-usecase-icon">
                            <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
                        </span>
                        <h3>بررسی فوم XPS</h3>
                        <p>
                            کاربرد موردنظر، ضخامت یا ابعاد تقریبی و شرایط نصب را اعلام کنید
                            تا مشخصات محصول قابل بررسی باشد.
                        </p>
                    </article>
                </div>

                <div class="col-md-6 col-lg-4">
                    <article class="sf-usecase-card sf-reveal-up">
                        <span class="sf-usecase-icon">
                            <i class="bi bi-columns-gap" aria-hidden="true"></i>
                        </span>
                        <h3>بررسی ساندویچ پنل</h3>
                        <p>
                            نوع کاربرد، محل پروژه، مقدار تقریبی و مشخصات موردنیاز پنل را
                            برای شروع استعلام آماده کنید.
                        </p>
                    </article>
                </div>
            </div>

            <p class="sf-disclaimer">
                توضیحات این صفحه راهنمای عمومی برای آشنایی اولیه است و جایگزین طراحی،
                محاسبات، دیتاشیت محصول یا مشاوره تخصصی پروژه نیست.
            </p>
        </div>
    </section>

    <!-- Product details -->
    <section class="section product-details-section" id="product-guide">
        <div class="container">
            <div class="section-heading reveal">
                <div class="eyebrow">
                    <span class="eyebrow-dot"></span>
                    راهنمای آشنایی با محصولات
                </div>
                <h2>هر محصول برای چه نیازی بررسی می‌شود؟</h2>
                <p>
                    شناخت کاربرد، محل نصب و مشخصات موردنیاز کمک می‌کند محصول را
                    متناسب با شرایط پروژه بررسی کنید.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <article class="product-card reveal">
                        <div class="product-card-body">
                            <div class="product-card-topline">عایق حرارتی</div>
                            <h3>فوم XPS چیست؟</h3>
                            <p>
                                فوم XPS نوعی عایق تخته‌ای از جنس پلی‌استایرن اکسترودشده است.
                                شکل تخته‌ای آن باعث می‌شود در برخی جزئیات عایق‌کاری ساختمان
                                مورد بررسی قرار بگیرد. تناسب آن با پروژه به محل نصب و
                                مشخصات همان محصول وابسته است.
                            </p>
                            <h4>هنگام بررسی فوم XPS به چه مواردی توجه کنیم؟</h4>
                            <ul class="about-list">
                                <li><i class="bi bi-check2" aria-hidden="true"></i> محل مصرف و نحوه نصب در جزئیات ساختمان</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> ضخامت، ابعاد و مشخصات فنی محصول</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> شرایط محیطی و نوع سطح محل اجرا</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> سازگاری محصول با لایه‌ها و مصالح مجاور</li>
                            </ul>
                            <p>
                                ویژگی‌های فنی را باید بر اساس اطلاعات سازنده و دیتاشیت همان
                                محصول ارزیابی کرد. درزها، اتصالات و شیوه اجرا نیز در بررسی
                                کل جداره اهمیت دارند.
                            </p>
                            <a class="card-link" href="#contact">
                                درخواست اطلاعات فوم XPS
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="product-card reveal reveal-delay-1">
                        <div class="product-card-body">
                            <div class="product-card-topline">پوشش و عایق ساختمان</div>
                            <h3>ساندویچ پنل چیست؟</h3>
                            <p>
                                ساندویچ پنل از دو لایه پوششی و یک لایه میانی تشکیل می‌شود.
                                جنس و نوع پوشش‌ها، جنس هسته، ضخامت و شکل اتصال در مدل‌های
                                مختلف می‌تواند متفاوت باشد.
                            </p>
                            <h4>پیش از استعلام ساندویچ پنل مشخص کنید:</h4>
                            <ul class="about-list">
                                <li><i class="bi bi-check2" aria-hidden="true"></i> پنل برای دیوار، سقف یا کاربرد دیگری لازم است؟</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> ابعاد، مقدار تقریبی و محل پروژه چیست؟</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> شرایط محیطی و نیازهای اجرایی کدام‌اند؟</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> چه مشخصاتی درباره هسته، ورق و اتصال مدنظر است؟</li>
                            </ul>
                            <p>
                                جزئیاتی مثل نوع اتصال، پوشش سطوح، آب‌بندی و زیرسازی باید
                                متناسب با طرح و شرایط اجرا بررسی شوند.
                            </p>
                            <a class="card-link" href="#contact">
                                درخواست اطلاعات ساندویچ پنل
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="product-card reveal">
                        <div class="product-card-body">
                            <div class="product-card-topline">سیستم سازه‌ای</div>
                            <h3>سازه سبک LSF</h3>
                            <p>
                                LSF مخفف Light Steel Frame است و به سیستم قاب فولادی سبک
                                اشاره دارد. نوع مقاطع، اتصالات، لایه‌های دیوار و جزئیات
                                اجرایی باید بر اساس نقشه و طراحی پروژه تعیین شوند.
                            </p>
                            <p>
                                برای بررسی نیاز پروژه، محل اجرا، نوع کاربری، مرحله فعلی
                                پروژه و نقشه‌های موجود را آماده کنید. ارزیابی نهایی سیستم
                                سازه‌ای باید توسط متخصص پروژه انجام شود.
                            </p>
                            <a class="card-link" href="#contact">
                                گفت‌وگو درباره سازه LSF
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>

                <div class="col-lg-6">
                    <article class="product-card reveal reveal-delay-1">
                        <div class="product-card-body">
                            <div class="product-card-topline">راهنمای استعلام</div>
                            <h3>برای استعلام چه اطلاعاتی آماده کنیم؟</h3>
                            <p>
                                لازم نیست از ابتدا همه جزئیات فنی را بدانید. اطلاعاتی را
                                که در دسترس دارید ارسال کنید تا درخواست روشن‌تر بررسی شود.
                            </p>
                            <ul class="about-list">
                                <li><i class="bi bi-check2" aria-hidden="true"></i> نام محصول یا گروه محصول موردنظر</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> شهر محل پروژه و نوع کاربری</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> مقدار تقریبی، ابعاد یا نقشه در صورت موجودبودن</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> پرسش‌ها یا محدودیت‌های مهم پروژه</li>
                            </ul>
                            <a class="card-link" href="#contact">
                                ثبت درخواست مشاوره
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="section product-selection-section" aria-labelledby="selection-title">
    <div class="container">
        <div class="section-heading reveal">
            <div class="eyebrow">
                <span class="eyebrow-dot"></span>
                پیش از انتخاب محصول
            </div>

            <h2 id="selection-title">سه پرسش برای انتخاب آگاهانه‌تر</h2>

            <p>
                با مشخص‌کردن محل استفاده، ویژگی‌های موردنیاز و شهر پروژه،
                مسیر بررسی محصول را ساده‌تر کنید.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <article class="selection-card reveal">
                    <div class="selection-card-top">
                        <span class="selection-card-icon">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        </span>
                        <span class="selection-card-number" aria-hidden="true">۰۱</span>
                    </div>

                    <h3>محصول کجا استفاده می‌شود؟</h3>
                    <p>
                        محل نصب و نوع کاربرد را مشخص کنید؛ محصول برای دیوار، سقف،
                        کف یا بخش دیگری از پروژه در نظر گرفته شده است؟
                    </p>
                </article>
            </div>

            <div class="col-md-4">
                <article class="selection-card reveal reveal-delay-1">
                    <div class="selection-card-top">
                        <span class="selection-card-icon">
                            <i class="bi bi-card-checklist" aria-hidden="true"></i>
                        </span>
                        <span class="selection-card-number" aria-hidden="true">۰۲</span>
                    </div>

                    <h3>چه مشخصاتی نیاز دارید؟</h3>
                    <p>
                        اگر ابعاد، ضخامت، مقدار تقریبی، نقشه یا مشخصات فنی خاصی
                        دارید، آن‌ها را برای بررسی آماده کنید.
                    </p>
                </article>
            </div>

            <div class="col-md-4">
                <article class="selection-card reveal reveal-delay-2">
                    <div class="selection-card-top">
                        <span class="selection-card-icon">
                            <i class="bi bi-truck" aria-hidden="true"></i>
                        </span>
                        <span class="selection-card-number" aria-hidden="true">۰۳</span>
                    </div>

                    <h3>پروژه در کدام شهر است؟</h3>
                    <p>
                        شهر محل پروژه و زمان تقریبی نیاز به محصول را بنویسید
                        تا درخواست شما دقیق‌تر بررسی شود.
                    </p>
                </article>
            </div>
        </div>

        <div class="selection-bottom-cta reveal">
            <div class="selection-bottom-copy">
                <span class="selection-bottom-icon">
                    <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                </span>

                <div>
                    <strong>هنوز مشخصات محصول را نمی‌دانید؟</strong>
                    <span>اطلاعات اولیه پروژه را بفرستید تا درباره گزینه‌های قابل بررسی گفت‌وگو کنیم.</span>
                </div>
            </div>

            <a class="selection-bottom-button" href="#contact">
                ثبت درخواست مشاوره
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</section>

    <!-- About -->
    <section class="section about-section" id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="about-visual reveal">
                        <div class="about-main-image">
                            <img
                                src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1100&q=85"
                                alt="فضای معماری داخلی با طراحی مدرن"
                                loading="lazy"
                                width="1100"
                                height="850"
                            >
                        </div>
                        <div class="about-decoration" aria-hidden="true"></div>
                        <div class="about-label">
                            <i class="bi bi-rulers" aria-hidden="true"></i>
                            <span>انتخاب آگاهانه برای پروژه</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="about-content reveal reveal-delay-1">
                        <div class="eyebrow">
                            <span class="eyebrow-dot"></span>
                            درباره صفری سازه
                        </div>
                        <h2>قبل از خرید،<br><span>درست انتخاب کنید.</span></h2>
                        <p>
                            مشخصات محصول، محل مصرف و شرایط اجرای پروژه در انتخاب مصالح اهمیت دارند.
                            هدف صفری سازه این است که مسیر دریافت اطلاعات و بررسی محصولات برای شما روشن‌تر باشد.
                        </p>

                        <ul class="about-list">
                            <li><i class="bi bi-check2" aria-hidden="true"></i> بررسی نوع محصول موردنیاز پروژه</li>
                            <li><i class="bi bi-check2" aria-hidden="true"></i> پاسخ‌گویی درباره مشخصات و کاربرد محصولات</li>
                            <li><i class="bi bi-check2" aria-hidden="true"></i> امکان ثبت درخواست برای پیگیری</li>
                        </ul>

                        <a class="btn btn-dark-custom" href="#contact">
                            ارتباط با کارشناسان
                            <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="section process-section" id="process">
        <div class="container">
            <div class="section-heading section-heading--light reveal">
                <div class="eyebrow eyebrow--light">
                    <span class="eyebrow-dot"></span>
                    شروع همکاری
                </div>
                <h2>یک مسیر ساده برای پیگیری نیاز شما</h2>
                <p>درخواستتان را ثبت کنید تا درباره محصول موردنظر گفت‌وگو را شروع کنیم.</p>
            </div>

            <div class="process-grid">
                <article class="process-step reveal">
                    <span class="step-number">۰۱</span>
                    <span class="step-icon"><i class="bi bi-pencil-square" aria-hidden="true"></i></span>
                    <h3>ثبت درخواست</h3>
                    <p>نام محصول یا نیاز پروژه را برای ما بنویسید.</p>
                </article>

                <article class="process-step reveal reveal-delay-1">
                    <span class="step-number">۰۲</span>
                    <span class="step-icon"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <h3>بررسی نیاز</h3>
                    <p>اطلاعات لازم برای شناخت بهتر درخواست شما بررسی می‌شود.</p>
                </article>

                <article class="process-step reveal reveal-delay-2">
                    <span class="step-number">۰۳</span>
                    <span class="step-icon"><i class="bi bi-chat-dots" aria-hidden="true"></i></span>
                    <h3>گفت‌وگو و راهنمایی</h3>
                    <p>برای دریافت اطلاعات و ادامه هماهنگی با شما تماس می‌گیریم.</p>
                </article>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="section faq-section" id="faq">
        <div class="container">
            <div class="row g-5 align-items-start">
                <div class="col-lg-5">
                    <div class="faq-intro reveal">
                        <div class="eyebrow">
                            <span class="eyebrow-dot"></span>
                            پاسخ به پرسش‌های رایج
                        </div>

                        <h2>قبل از انتخاب،<br>این نکات را بدانید.</h2>

                        <p>
                            پاسخ پرسش‌های رایج درباره سازه LSF، فوم XPS،
                            ساندویچ پنل و عایق‌های ساختمانی را اینجا ببینید.
                        </p>

                        <a class="btn btn-dark-custom" href="#contact">
                            پرسش دیگری دارید؟
                            <i class="bi bi-arrow-left" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="accordion faq-accordion reveal" id="faqAccordion">

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerOne" aria-expanded="true" aria-controls="faqAnswerOne">
                                    سازه LSF چیست؟
                                </button>
                            </h3>
                            <div id="faqAnswerOne" class="accordion-collapse collapse show"
                                aria-labelledby="faqHeadingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    LSF مخفف Light Steel Frame و به معنی قاب فولادی سبک است.
                                    جزئیات طراحی، اجزا و مناسب‌بودن آن برای هر پروژه باید
                                    بر اساس نقشه‌ها و نظر متخصص بررسی شود.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerTwo" aria-expanded="false" aria-controls="faqAnswerTwo">
                                    برای انتخاب عایق ساختمانی چه اطلاعاتی لازم است؟
                                </button>
                            </h3>
                            <div id="faqAnswerTwo" class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    محل استفاده، نوع ساختار، مشخصات موردنیاز و شرایط پروژه
                                    در انتخاب عایق اهمیت دارند. برای بررسی محصول مناسب،
                                    اطلاعات پروژه را هنگام ثبت درخواست بنویسید.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerThree" aria-expanded="false" aria-controls="faqAnswerThree">
                                    چطور قیمت محصول را استعلام کنم؟
                                </button>
                            </h3>
                            <div id="faqAnswerThree" class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    نام محصول و اطلاعات اولیه پروژه را از طریق فرم تماس ارسال کنید.
                                    قیمت به نوع محصول، مشخصات فنی، مقدار موردنیاز و شرایط تأمین بستگی دارد.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerFour" aria-expanded="false" aria-controls="faqAnswerFour">
                                    برای دریافت اطلاعات محصول چه‌کار کنم؟
                                </button>
                            </h3>
                            <div id="faqAnswerFour" class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    فرم درخواست را با نام، شماره تماس و نام محصول تکمیل کنید.
                                    اگر درباره محصول خاصی سؤال دارید، آن را در بخش توضیحات بنویسید.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerFive" aria-expanded="false" aria-controls="faqAnswerFive">
                                    برای استعلام فوم XPS چه اطلاعاتی لازم است؟
                                </button>
                            </h3>
                            <div id="faqAnswerFive" class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    کاربرد موردنظر، ابعاد یا مشخصات موردنیاز و مقدار تقریبی
                                    محصول را در فرم درخواست بنویسید.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingSix">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerSix" aria-expanded="false" aria-controls="faqAnswerSix">
                                    برای استعلام ساندویچ پنل چه مواردی را اعلام کنم؟
                                </button>
                            </h3>
                            <div id="faqAnswerSix" class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingSix" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    نوع کاربرد، ابعاد یا مقدار موردنیاز و محل پروژه را در درخواست
                                    خود بنویسید تا اطلاعات محصول و امکان تأمین آن بررسی شود.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingSeven">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerSeven" aria-expanded="false" aria-controls="faqAnswerSeven">
                                    آیا تصویر قبل و بعد، نتیجه واقعی یک محصول را نشان می‌دهد؟
                                </button>
                            </h3>
                            <div id="faqAnswerSeven" class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingSeven" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    خیر. تصویر صفحه یک نمایش مفهومی برای توضیح وجود لایه عایق
                                    در جددر جداره ساختمان است و عملکرد یا صرفه‌جویی واقعی یک محصول مشخص را اندازه‌گیری نمی‌کند.
                                    نتیجه واقعی به نوع محصول، طراحی، محل نصب و کیفیت اجرای کل سیستم بستگی دارد.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header" id="faqHeadingEight">
                                <button
                                    class="accordion-button collapsed"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#faqAnswerEight"
                                    aria-expanded="false"
                                    aria-controls="faqAnswerEight"
                                >
                                    هنگام انتخاب عایق، فقط نوع محصول مهم است؟
                                </button>
                            </h3>

                            <div
                                id="faqAnswerEight"
                                class="accordion-collapse collapse"
                                aria-labelledby="faqHeadingEight"
                                data-bs-parent="#faqAccordion"
                            >
                                <div class="accordion-body">
                                    خیر. محل مصرف، مشخصات فنی، زیرسازی، پیوستگی عایق،
                                    درزها و نحوه اجرا هم باید بررسی شوند. انتخاب نهایی
                                    را با توجه به نقشه و شرایط واقعی پروژه انجام دهید.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section class="section contact-section" id="contact">
        <div class="container">
            <div class="contact-box reveal">
                <div class="contact-copy">
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        با ما در ارتباط باشید
                    </div>

                    <h2>درباره نیاز پروژه‌تان<br>با ما صحبت کنید.</h2>

                    <p>
                        محصول موردنظر، شهر پروژه و اطلاعاتی را که در دسترس دارید
                        بنویسید تا درخواستتان برای پیگیری ثبت شود.
                    </p>

                    <div class="contact-points">
                        <div>
                            <i class="bi bi-box-seam" aria-hidden="true"></i>
                            <span>محصول موردنظر</span>
                        </div>
                        <div>
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <span>شهر محل پروژه</span>
                        </div>
                        <div>
                            <i class="bi bi-chat-square-text" aria-hidden="true"></i>
                            <span>توضیحات و پرسش‌ها</span>
                        </div>
                    </div>

                    <p class="sf-disclaimer">
                        فرم این نسخه نمایشی است و هنوز اطلاعات را به سرور یا CRM ارسال نمی‌کند.
                    </p>
                </div>

                <form class="contact-form" id="leadForm" action="#contact" method="post">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label" for="fullName">نام و نام خانوادگی</label>
                            <input
                                class="form-control"
                                id="fullName"
                                name="name"
                                type="text"
                                placeholder="نام شما"
                                autocomplete="name"
                                required
                            >
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label" for="phone">شماره تماس</label>
                            <input
                                class="form-control"
                                id="phone"
                                name="phone"
                                type="tel"
                                placeholder="مثلاً ۰۹۱۲..."
                                autocomplete="tel"
                                inputmode="tel"
                                required
                            >
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label" for="product">محصول موردنظر</label>
                            <select class="form-select" id="product" name="product">
                                <option value="">انتخاب کنید</option>
                                <option value="lsf">سازه و سیستم LSF</option>
                                <option value="insulation">فوم و عایق ساختمانی</option>
                                <option value="xps">فوم XPS</option>
                                <option value="sandwich-panel">ساندویچ پنل</option>
                                <option value="other">سایر محصولات</option>
                            </select>
                        </div>

                        <div class="col-sm-6">
                            <label class="form-label" for="city">شهر محل پروژه</label>
                            <input
                                class="form-control"
                                id="city"
                                name="city"
                                type="text"
                                placeholder="نام شهر"
                                autocomplete="address-level2"
                            >
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="message">توضیحات</label>
                            <textarea
                                class="form-control"
                                id="message"
                                name="message"
                                rows="4"
                                placeholder="کاربرد محصول، مقدار تقریبی یا پرسش خود را بنویسید"
                            ></textarea>
                        </div>

                        <div class="col-12 d-flex flex-wrap align-items-center gap-3">
                            <button class="btn btn-accent" type="submit">
                                ثبت درخواست
                                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                            </button>

                            <span
                                class="form-status"
                                id="formStatus"
                                role="status"
                                aria-live="polite"
                            ></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        /*
         * اسلایدر مقایسه تصویری قبل و بعد از عایق‌کاری
         */
        const slider = document.getElementById('thermalSlider');
        const afterLayer = document.getElementById('thermalAfter');
        const divider = document.getElementById('thermalDivider');

        if (slider && afterLayer && divider) {
            const updateComparison = function () {
                const value = Number(slider.value);
                const hiddenRight = 100 - value;

                afterLayer.style.clipPath = 'inset(0 ' + hiddenRight + '% 0 0)';
                divider.style.left = value + '%';

                slider.setAttribute(
                    'aria-valuetext',
                    'نمایش ' + value + ' درصد از حالت عایق‌کاری‌شده'
                );
            };

            slider.addEventListener('input', updateComparison);
            updateComparison();
        }

        /*
         * نمایان‌شدن تدریجی بخش‌ها هنگام ورود به محدوده دید
         */
        const revealItems = document.querySelectorAll('.sf-reveal-up');

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -35px 0px'
            });

            revealItems.forEach(function (item, index) {
                item.style.transitionDelay = Math.min(index % 4, 3) * 90 + 'ms';
                revealObserver.observe(item);
            });
        } else {
            revealItems.forEach(function (item) {
                item.classList.add('is-visible');
            });
        }

        /*
         * این فرم فعلاً نمایشی است و اطلاعات را ارسال یا ذخیره نمی‌کند.
         */
        const leadForm = document.getElementById('leadForm');
        const formStatus = document.getElementById('formStatus');

        if (leadForm && formStatus) {
            leadForm.addEventListener('submit', function (event) {
                event.preventDefault();

                formStatus.textContent =
                    'فرم در نسخه نمایشی ارسال نمی‌شود؛ اتصال به سامانه در مرحله بک‌اند انجام خواهد شد.';
            });
        }
    });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>