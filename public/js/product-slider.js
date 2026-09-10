// BLUE ZONE Premium Featured Products Carousel Slider

(function () {
  'use strict';

  /*
  |--------------------------------------------------------------------------
  | Configuration
  |--------------------------------------------------------------------------
  */

  const AUTOPLAY_DELAY = 5500;
  const SWIPE_THRESHOLD = 40;
  const MAX_PRODUCTS = 6;

  let currentIndex = 0;
  let autoplayTimer = null;
  let resizeTimer = null;

  let products = [];
  let visibleCount = 3;
  let initialized = false;

  let touchStartX = 0;
  let touchEndX = 0;


  /*
  |--------------------------------------------------------------------------
  | Translations
  |--------------------------------------------------------------------------
  |
  | These are provided from Blade through:
  |
  | window.BLUEZONE_TRANSLATIONS
  |
  | The JS file itself contains no Laravel/PHP syntax.
  |
  */

  const TRANSLATIONS = Object.assign(
    {
      previousProducts: 'Previous products',
      nextProducts: 'Next products',
      goToPage: 'Go to page',
      toggleWishlist: 'Toggle wishlist',
      addToCart: 'Add to Cart',
      quickView: 'Quick View',
      clinicalFormulation: 'Clinical Formulation',
      rating: 'Rating',
      noProducts: 'No products available',
    },
    window.BLUEZONE_TRANSLATIONS || {}
  );


  /*
  |--------------------------------------------------------------------------
  | DOM Helpers
  |--------------------------------------------------------------------------
  */

  function getTrack() {
    return document.getElementById(
      'featured-products-track'
    );
  }

  function getContainer() {
    return document.getElementById(
      'featured-products-container'
    );
  }

  function getDotsContainer() {
    return document.getElementById(
      'featured-products-dots'
    );
  }


  /*
  |--------------------------------------------------------------------------
  | RTL Detection
  |--------------------------------------------------------------------------
  */

  function isRTL() {
    return (
      document.documentElement.dir === 'rtl' ||
      document.body.dir === 'rtl'
    );
  }


  /*
  |--------------------------------------------------------------------------
  | Responsive Visible Count
  |--------------------------------------------------------------------------
  */

  function calculateVisibleCount() {
    const width = window.innerWidth;

    /*
     * Mobile:
     * One product at a time.
     */
    if (width < 640) {
      return 1;
    }

    /*
     * Tablet:
     * Two products.
     */
    if (width < 1024) {
      return 2;
    }

    /*
     * Desktop:
     * Three products.
     */
    return 3;
  }


  /*
  |--------------------------------------------------------------------------
  | HTML Escape
  |--------------------------------------------------------------------------
  */

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }


  /*
  |--------------------------------------------------------------------------
  | Normalize Product
  |--------------------------------------------------------------------------
  */

  function normalizeProduct(product) {
    if (!product || typeof product !== 'object') {
      return null;
    }

    const parsedPrice = Number.parseFloat(
      product.price
    );

    return {
      id: product.id ?? '',
      name: product.name ?? '',
      category: product.category ?? '',
      rating: product.rating ?? '',
      image: product.image ?? '',
      shortDesc: product.shortDesc ?? '',

      price: Number.isFinite(parsedPrice)
        ? parsedPrice
        : 0,

      /*
       * Laravel-generated URL.
       *
       * Example:
       * /products/blue-mind
       */
      url: product.url ?? '#',
    };
  }


  /*
  |--------------------------------------------------------------------------
  | Format Price
  |--------------------------------------------------------------------------
  */

  function formatPrice(price) {
    if (
      window.BLUEZONE_CURRENCY &&
      typeof window.BLUEZONE_CURRENCY.format === 'function'
    ) {
      return window.BLUEZONE_CURRENCY.format(
        price
      );
    }

    return `$${Number(price).toFixed(2)}`;
  }


  /*
  |--------------------------------------------------------------------------
  | Product Card
  |--------------------------------------------------------------------------
  */

  function renderProductCard(product) {
    const p = normalizeProduct(product);

    if (!p) {
      return '';
    }

    const safeId = escapeHtml(p.id);
    const safeName = escapeHtml(p.name);

    const safeCategory = escapeHtml(
      p.category ||
      TRANSLATIONS.clinicalFormulation
    );

    const safeRating = escapeHtml(
      p.rating
    );

    const safeImage = escapeHtml(
      p.image
    );

    const safeDescription = escapeHtml(
      p.shortDesc
    );

    const safeUrl = escapeHtml(
      p.url
    );

    const price = formatPrice(
      p.price
    );

    return `
            <div
                class="
                    product-slide-card
                    flex-shrink-0
                    px-1
                    sm:px-3
                    transition-all
                    duration-500
                    ease-out
                "
                style="width: ${100 / visibleCount}%;"
                data-product-id="${safeId}"
            >

                <article
                    class="
                        group
                        relative
                        bg-white
                        dark:bg-[#062B49]
                        rounded-2xl
                        border
                        border-[#0A4F78]/15
                        dark:border-[#0A4F78]/30
                        shadow-sm
                        hover:shadow-2xl
                        transition-all
                        duration-300
                        p-2.5
                        sm:p-6
                        flex
                        flex-col
                        justify-between
                        h-full
                        card-hover-lift
                        img-zoom-container
                    "
                >

                    <!-- Product Content -->
                    <div class="space-y-2 sm:space-y-4">

                        <!-- Category + Rating -->
                        <div
                            class="
                                flex
                                justify-between
                                items-center
                                gap-2
                            "
                        >

                            <span
                                class="
                                    min-w-0
                                    truncate
                                    text-[8px]
                                    sm:text-[10px]
                                    font-extrabold
                                    uppercase
                                    tracking-wider
                                    px-1.5
                                    py-0.5
                                    sm:px-2.5
                                    sm:py-1
                                    rounded-full
                                    bg-[#0A4F78]/10
                                    text-[#0A4F78]
                                    dark:bg-[#0A4F78]/40
                                    dark:text-[#2A8FC2]
                                "
                            >
                                ${safeCategory}
                            </span>

                            <span
                                class="
                                    shrink-0
                                    text-[9px]
                                    sm:text-xs
                                    font-bold
                                    text-[#67B34A]
                                    flex
                                    items-center
                                    gap-0.5
                                    sm:gap-1
                                "
                                aria-label="${escapeHtml(
      TRANSLATIONS.rating
    )}: ${safeRating}"
                            >
                                <i
                                    class="
                                        fa-solid
                                        fa-star
                                        text-[9px]
                                        sm:text-xs
                                    "
                                    aria-hidden="true"
                                ></i>

                                <span>
                                    ${safeRating}
                                </span>
                            </span>

                        </div>


                        <!-- Product Image -->
                        <a
                            href="${safeUrl}"
                            class="block"
                            aria-label="${safeName}"
                        >
                            <div
                                class="
                                    aspect-square
                                    p-1.5
                                    sm:p-4
                                    bg-[#F6F5EF]
                                    dark:bg-[#031827]
                                    rounded-xl
                                    flex
                                    items-center
                                    justify-center
                                    relative
                                    overflow-hidden
                                "
                            >
                                <img
                                    src="${safeImage}"
                                    alt="${safeName}"
                                    loading="lazy"
                                    decoding="async"
                                    class="
                                        w-full
                                        h-full
                                        object-contain
                                        group-hover:scale-105
                                        transition-transform
                                        duration-500
                                    "
                                />
                            </div>
                        </a>


                        <!-- Product Name + Description -->
                        <div>

                            <h3
                                class="
                                    text-xs
                                    sm:text-lg
                                    font-black
                                    text-[#031827]
                                    dark:text-[#F6F5EF]
                                    group-hover:text-[#67B34A]
                                    dark:group-hover:text-[#67B34A]
                                    transition-colors
                                    truncate
                                "
                            >
                                ${safeName}
                            </h3>

                            <p
                                class="
                                    text-[9px]
                                    sm:text-xs
                                    text-[#031827]/70
                                    dark:text-[#F6F5EF]/70
                                    line-clamp-2
                                    font-medium
                                    leading-tight
                                    sm:leading-relaxed
                                    mt-0.5
                                    sm:mt-1
                                "
                            >
                                ${safeDescription}
                            </p>

                        </div>

                    </div>


                    <!-- Price + Actions -->
                    <div
                        class="
                            pt-2
                            sm:pt-6
                            space-y-2
                            sm:space-y-3
                        "
                    >

                        <!-- Price + Wishlist -->
                        <div
                            class="
                                flex
                                justify-between
                                items-center
                                gap-2
                            "
                        >

                            <span
                                class="
                                    text-xs
                                    sm:text-xl
                                    font-black
                                    text-[#0A4F78]
                                    dark:text-[#2A8FC2]
                                "
                            >
                                ${escapeHtml(price)}
                            </span>

                            <button
                                type="button"
                                data-action="wishlist"
                                data-product-id="${safeId}"
                                aria-label="${escapeHtml(
      TRANSLATIONS.toggleWishlist
    )}"
                                title="${escapeHtml(
      TRANSLATIONS.toggleWishlist
    )}"
                                class="
                                    p-1
                                    sm:p-2.5
                                    rounded-full
                                    hover:bg-[#0A4F78]/10
                                    text-[#0A4F78]
                                    dark:text-[#2A8FC2]
                                    transition-colors
                                    cursor-pointer
                                "
                            >
                                <i
                                    class="
                                        fa-regular
                                        fa-heart
                                        text-sm
                                        sm:text-base
                                    "
                                    aria-hidden="true"
                                ></i>
                            </button>

                        </div>


                        <!-- Buttons -->
                        <div
                            class="
                                flex
                                flex-col
                                sm:flex-row
                                gap-1
                                sm:gap-2
                            "
                        >

                            <!-- Add To Cart -->
                            <button
                                type="button"
                                data-action="cart"
                                data-product-id="${safeId}"
                                class="
                                    w-full
                                    sm:flex-1
                                    py-1.5
                                    sm:py-3
                                    bg-[#0A4F78]
                                    hover:bg-[#062B49]
                                    text-white
                                    text-[9px]
                                    sm:text-xs
                                    font-extrabold
                                    uppercase
                                    tracking-wider
                                    rounded-lg
                                    sm:rounded-xl
                                    transition-all
                                    shadow-sm
                                    hover:shadow-md
                                    btn-sheen
                                    cursor-pointer
                                "
                            >
                                ${escapeHtml(
      TRANSLATIONS.addToCart
    )}
                            </button>


                            <!-- Quick View -->
                            <button
                                type="button"
                                data-action="quick-view"
                                data-product-id="${safeId}"
                                class="
                                    w-full
                                    sm:w-auto
                                    px-2
                                    sm:px-3.5
                                    py-1.5
                                    sm:py-3
                                    border
                                    border-[#0A4F78]/30
                                    hover:border-[#0A4F78]
                                    text-[#031827]
                                    dark:text-[#F6F5EF]
                                    text-[9px]
                                    sm:text-xs
                                    font-extrabold
                                    rounded-lg
                                    sm:rounded-xl
                                    transition-colors
                                    cursor-pointer
                                    text-center
                                    whitespace-nowrap
                                "
                            >
                                ${escapeHtml(
      TRANSLATIONS.quickView
    )}
                            </button>

                        </div>

                    </div>

                </article>

            </div>
        `;
  }


  /*
  |--------------------------------------------------------------------------
  | Maximum Slider Index
  |--------------------------------------------------------------------------
  */

  function getMaxIndex() {
    return Math.max(
      0,
      products.length - visibleCount
    );
  }


  /*
  |--------------------------------------------------------------------------
  | Normalize Index
  |--------------------------------------------------------------------------
  */

  function normalizeCurrentIndex() {
    const maxIndex =
      getMaxIndex();

    currentIndex =
      Math.min(
        Math.max(
          0,
          currentIndex
        ),
        maxIndex
      );
  }


  /*
  |--------------------------------------------------------------------------
  | Update Slider Position
  |--------------------------------------------------------------------------
  */

  function updateSliderPosition() {
    const track =
      getTrack();

    if (!track) {
      return;
    }

    normalizeCurrentIndex();

    if (products.length === 0) {
      track.style.transform =
        'translateX(0)';

      updateDots();

      return;
    }

    const translatePercentage =
      currentIndex *
      (100 / visibleCount);

    /*
     * RTL support.
     */
    if (isRTL()) {
      track.style.transform =
        `translateX(${translatePercentage}%)`;
    } else {
      track.style.transform =
        `translateX(-${translatePercentage}%)`;
    }

    updateDots();
  }


  /*
  |--------------------------------------------------------------------------
  | Render Dots
  |--------------------------------------------------------------------------
  */

  function renderDots() {
    const dotsContainer =
      getDotsContainer();

    if (!dotsContainer) {
      return;
    }

    const totalPages =
      getMaxIndex() + 1;

    /*
     * Hide dots if there is
     * nothing to slide.
     */
    if (totalPages <= 1) {
      dotsContainer.innerHTML = '';

      dotsContainer.classList.add(
        'hidden'
      );

      return;
    }

    dotsContainer.classList.remove(
      'hidden'
    );

    let html = '';

    for (
      let i = 0;
      i < totalPages;
      i++
    ) {
      const isActive =
        i === currentIndex;

      html += `
                <button
                    type="button"
                    data-slide-index="${i}"
                    aria-label="${escapeHtml(
        TRANSLATIONS.goToPage
      )} ${i + 1}"
                    aria-current="${isActive
          ? 'true'
          : 'false'
        }"
                    class="
                        h-2.5
                        ${isActive
          ? 'w-8'
          : 'w-2.5'
        }
                        rounded-full
                        ${isActive
          ? 'bg-[#0A4F78] dark:bg-[#2A8FC2]'
          : 'bg-[#0A4F78]/30 dark:bg-white/30'
        }
                        transition-all
                        duration-300
                        cursor-pointer
                    "
                ></button>
            `;
    }

    dotsContainer.innerHTML =
      html;

    /*
     * Bind dot actions.
     */
    dotsContainer
      .querySelectorAll(
        '[data-slide-index]'
      )
      .forEach((button) => {

        button.addEventListener(
          'click',
          function () {

            const index =
              Number(
                this.dataset.slideIndex
              );

            stopAutoplay();

            goTo(index);

            startAutoplay();
          }
        );

      });
  }


  /*
  |--------------------------------------------------------------------------
  | Update Dots
  |--------------------------------------------------------------------------
  */

  function updateDots() {
    const dotsContainer =
      getDotsContainer();

    if (!dotsContainer) {
      return;
    }

    const buttons =
      dotsContainer.querySelectorAll(
        '[data-slide-index]'
      );

    buttons.forEach(
      (button) => {

        const index =
          Number(
            button.dataset.slideIndex
          );

        const isActive =
          index === currentIndex;

        button.classList.toggle(
          'w-8',
          isActive
        );

        button.classList.toggle(
          'w-2.5',
          !isActive
        );

        button.classList.toggle(
          'bg-[#0A4F78]',
          isActive
        );

        button.classList.toggle(
          'dark:bg-[#2A8FC2]',
          isActive
        );

        button.classList.toggle(
          'bg-[#0A4F78]/30',
          !isActive
        );

        button.classList.toggle(
          'dark:bg-white/30',
          !isActive
        );

        button.setAttribute(
          'aria-current',
          isActive
            ? 'true'
            : 'false'
        );
      }
    );
  }


  /*
  |--------------------------------------------------------------------------
  | Render Products
  |--------------------------------------------------------------------------
  */

  function renderProducts() {
    const track =
      getTrack();

    if (!track) {
      return;
    }

    /*
     * Empty state.
     */
    if (products.length === 0) {

      track.innerHTML = `
                <div
                    class="
                        w-full
                        py-12
                        text-center
                    "
                >
                    <div
                        class="
                            inline-flex
                            items-center
                            justify-center
                            w-14
                            h-14
                            rounded-full
                            bg-[#0A4F78]/10
                            text-[#0A4F78]
                            dark:text-[#2A8FC2]
                            mb-4
                        "
                    >
                        <i
                            class="
                                fa-solid
                                fa-box-open
                                text-xl
                            "
                            aria-hidden="true"
                        ></i>
                    </div>

                    <p
                        class="
                            text-sm
                            font-semibold
                            text-[#031827]/60
                            dark:text-[#F6F5EF]/60
                        "
                    >
                        ${escapeHtml(
        TRANSLATIONS.noProducts
      )}
                    </p>
                </div>
            `;

      return;
    }

    /*
     * Render product cards.
     */
    track.innerHTML =
      products
        .map(renderProductCard)
        .join('');

    bindProductActions();
  }


  /*
  |--------------------------------------------------------------------------
  | Product Actions
  |--------------------------------------------------------------------------
  */

  function bindProductActions() {
    const track =
      getTrack();

    if (!track) {
      return;
    }


    /*
    |--------------------------------------------------------------------------
    | Wishlist
    |--------------------------------------------------------------------------
    */

    track
      .querySelectorAll(
        '[data-action="wishlist"]'
      )
      .forEach((button) => {

        button.addEventListener(
          'click',
          function (event) {

            event.preventDefault();
            event.stopPropagation();

            const productId =
              this.dataset.productId;

            if (
              window.BLUEZONE_WISHLIST &&
              typeof window.BLUEZONE_WISHLIST.toggle === 'function'
            ) {
              window.BLUEZONE_WISHLIST.toggle(
                productId
              );
            }
          }
        );

      });


    /*
    |--------------------------------------------------------------------------
    | Add To Cart
    |--------------------------------------------------------------------------
    */

    track
      .querySelectorAll(
        '[data-action="cart"]'
      )
      .forEach((button) => {

        button.addEventListener(
          'click',
          function (event) {

            event.preventDefault();
            event.stopPropagation();

            const productId =
              this.dataset.productId;

            if (
              window.BLUEZONE_CART &&
              typeof window.BLUEZONE_CART.add === 'function'
            ) {
              window.BLUEZONE_CART.add(
                productId,
                1
              );
            }
          }
        );

      });


    /*
    |--------------------------------------------------------------------------
    | Quick View
    |--------------------------------------------------------------------------
    */

    track
      .querySelectorAll(
        '[data-action="quick-view"]'
      )
      .forEach((button) => {

        button.addEventListener(
          'click',
          function (event) {

            event.preventDefault();
            event.stopPropagation();

            const productId =
              this.dataset.productId;

            if (
              window.BLUEZONE_APP &&
              typeof window.BLUEZONE_APP.openQuickView === 'function'
            ) {
              window.BLUEZONE_APP.openQuickView(
                productId
              );
            }
          }
        );

      });
  }


  /*
  |--------------------------------------------------------------------------
  | Next Slide
  |--------------------------------------------------------------------------
  */

  function nextSlide() {
    if (
      products.length <= visibleCount
    ) {
      return;
    }

    const maxIndex =
      getMaxIndex();

    if (
      currentIndex >= maxIndex
    ) {
      currentIndex = 0;
    } else {
      currentIndex++;
    }

    updateSliderPosition();
  }


  /*
  |--------------------------------------------------------------------------
  | Previous Slide
  |--------------------------------------------------------------------------
  */

  function prevSlide() {
    if (
      products.length <= visibleCount
    ) {
      return;
    }

    const maxIndex =
      getMaxIndex();

    if (
      currentIndex <= 0
    ) {
      currentIndex = maxIndex;
    } else {
      currentIndex--;
    }

    updateSliderPosition();
  }


  /*
  |--------------------------------------------------------------------------
  | Go To Slide
  |--------------------------------------------------------------------------
  */

  function goTo(index) {
    const parsedIndex =
      Number(index);

    if (
      !Number.isFinite(
        parsedIndex
      )
    ) {
      return;
    }

    const maxIndex =
      getMaxIndex();

    currentIndex =
      Math.min(
        Math.max(
          0,
          parsedIndex
        ),
        maxIndex
      );

    updateSliderPosition();
  }


  /*
  |--------------------------------------------------------------------------
  | Autoplay
  |--------------------------------------------------------------------------
  */

  function startAutoplay() {
    stopAutoplay();

    /*
     * Don't run autoplay if
     * all products are visible.
     */
    if (
      products.length <= visibleCount
    ) {
      return;
    }

    autoplayTimer =
      window.setInterval(
        nextSlide,
        AUTOPLAY_DELAY
      );
  }


  function stopAutoplay() {
    if (
      autoplayTimer !== null
    ) {
      window.clearInterval(
        autoplayTimer
      );

      autoplayTimer = null;
    }
  }


  /*
  |--------------------------------------------------------------------------
  | Resize
  |--------------------------------------------------------------------------
  */

  function handleResize() {
    window.clearTimeout(
      resizeTimer
    );

    resizeTimer =
      window.setTimeout(
        function () {

          const newVisibleCount =
            calculateVisibleCount();

          if (
            newVisibleCount ===
            visibleCount
          ) {
            return;
          }

          /*
           * Keep the first visible
           * product approximately
           * in the same position.
           */
          const firstVisibleProduct =
            currentIndex *
            visibleCount;

          visibleCount =
            newVisibleCount;

          currentIndex =
            Math.floor(
              firstVisibleProduct /
              visibleCount
            );

          normalizeCurrentIndex();

          renderProducts();

          renderDots();

          updateSliderPosition();

          startAutoplay();

        },
        150
      );
  }


  /*
  |--------------------------------------------------------------------------
  | Touch Start
  |--------------------------------------------------------------------------
  */

  function handleTouchStart(event) {
    if (
      !event.changedTouches ||
      !event.changedTouches.length
    ) {
      return;
    }

    touchStartX =
      event.changedTouches[0]
        .screenX;
  }


  /*
  |--------------------------------------------------------------------------
  | Touch End
  |--------------------------------------------------------------------------
  */

  function handleTouchEnd(event) {
    if (
      !event.changedTouches ||
      !event.changedTouches.length
    ) {
      return;
    }

    touchEndX =
      event.changedTouches[0]
        .screenX;

    const distance =
      touchEndX -
      touchStartX;

    /*
     * Ignore small movements.
     */
    if (
      Math.abs(distance) <
      SWIPE_THRESHOLD
    ) {
      return;
    }

    stopAutoplay();

    if (distance < 0) {
      nextSlide();
    } else {
      prevSlide();
    }

    startAutoplay();
  }


  /*
  |--------------------------------------------------------------------------
  | Bind Events
  |--------------------------------------------------------------------------
  */

  function bindEvents() {
    const container =
      getContainer();

    /*
     * Resize.
     */
    window.addEventListener(
      'resize',
      handleResize,
      {
        passive: true
      }
    );

    if (!container) {
      return;
    }

    /*
     * Pause autoplay on hover.
     */
    container.addEventListener(
      'mouseenter',
      stopAutoplay
    );

    container.addEventListener(
      'mouseleave',
      startAutoplay
    );

    /*
     * Touch swipe.
     */
    container.addEventListener(
      'touchstart',
      handleTouchStart,
      {
        passive: true
      }
    );

    container.addEventListener(
      'touchend',
      handleTouchEnd,
      {
        passive: true
      }
    );
  }


  /*
  |--------------------------------------------------------------------------
  | Initialize
  |--------------------------------------------------------------------------
  */

  function initProductSlider() {
    const track =
      getTrack();

    if (!track) {
      return;
    }

    if (
      !window.BLUEZONE_PRODUCTS ||
      !Array.isArray(
        window.BLUEZONE_PRODUCTS
      )
    ) {
      return;
    }

    /*
     * Prevent duplicate initialization.
     */
    if (initialized) {
      return;
    }

    initialized = true;

    currentIndex = 0;

    visibleCount =
      calculateVisibleCount();

    /*
     * Load products.
     */
    products =
      window.BLUEZONE_PRODUCTS
        .slice(0, MAX_PRODUCTS)
        .map(normalizeProduct)
        .filter(Boolean);

    /*
     * Render.
     */
    renderProducts();

    renderDots();

    updateSliderPosition();

    /*
     * Bind events.
     */
    bindEvents();

    /*
     * Start autoplay.
     */
    startAutoplay();
  }


  /*
  |--------------------------------------------------------------------------
  | Public API
  |--------------------------------------------------------------------------
  */

  window.BLUEZONE_PRODUCT_SLIDER = {

    init: initProductSlider,

    next: function () {
      stopAutoplay();

      nextSlide();

      startAutoplay();
    },

    prev: function () {
      stopAutoplay();

      prevSlide();

      startAutoplay();
    },

    goTo: function (index) {
      stopAutoplay();

      goTo(index);

      startAutoplay();
    },

    start: function () {
      startAutoplay();
    },

    stop: function () {
      stopAutoplay();
    }
  };


  /*
  |--------------------------------------------------------------------------
  | DOM Ready
  |--------------------------------------------------------------------------
  */

  if (
    document.readyState === 'loading'
  ) {
    document.addEventListener(
      'DOMContentLoaded',
      initProductSlider,
      {
        once: true
      }
    );
  } else {
    initProductSlider();
  }

})();
