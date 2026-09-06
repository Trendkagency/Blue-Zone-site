<x-layouts.customer title="FAQs — Blue Zone Longevity Protocols">
    <div class="hero-wrapper" style="padding: 4rem 0;">
        <div class="container text-center">
            <span class="section-badge">{{ __('app.nav.faqs') }}</span>
            <h1 class="hero-title" style="font-size: clamp(2.2rem, 4vw, 3.5rem);">
                Frequently Asked Questions
            </h1>
            <p class="hero-subtitle" style="max-width: 720px; margin: 0 auto;">
                Everything you need to know about our clinical assay standards, formulation synergy, and cold-chain fulfillment.
            </p>
        </div>
    </div>

    <div class="container section-py">
        <div style="max-width: 840px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">
            @foreach($faqs as $faq)
                <div class="card" style="padding: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 800; color: var(--color-primary); margin-bottom: 1rem;">
                        {{ app()->getLocale() === 'ar' ? ($faq['q_ar'] ?? $faq['q_en']) : $faq['q_en'] }}
                    </h3>
                    <p style="color: var(--color-text-secondary); line-height: 1.8; margin: 0; font-size: 1rem;">
                        {{ app()->getLocale() === 'ar' ? ($faq['a_ar'] ?? $faq['a_en']) : $faq['a_en'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.customer>
