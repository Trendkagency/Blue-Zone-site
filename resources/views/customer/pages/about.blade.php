<x-layouts.customer title="About Us — Blue Zone Longevity & Cellular Health">
    <div class="hero-wrapper" style="padding: 4rem 0;">
        <div class="container text-center">
            <span class="section-badge">OUR FOUNDATIONAL STORY</span>
            <h1 class="hero-title" style="font-size: clamp(2.2rem, 4vw, 3.5rem);">
                The Genesis of BLUE ZONE™
            </h1>
            <p class="hero-subtitle" style="max-width: 720px; margin: 0 auto;">
                Bridging ancient longevity wisdom from centenarian regions with 21st-century molecular biotechnology and clinical cellular nutrition.
            </p>
        </div>
    </div>

    <div class="container section-py">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; margin-bottom: 5rem;">
            <div>
                <span class="section-badge">BIO-IDENTICAL WELLNESS</span>
                <h2 style="font-size: 2.25rem; font-weight: 800; margin-bottom: 1.5rem;">
                    Beyond Superficial Health
                </h2>
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-secondary); margin-bottom: 1.25rem;">
                    In regions like Ikaria (Greece) and Okinawa (Japan), people routinely reach 100 years of age free from chronic degeneration. They don't just live longer; they remain cognitively sharp, physically resilient, and deeply connected.
                </p>
                <p style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-secondary);">
                    BLUE ZONE was founded to decode the precise cellular compounds responsible for this phenomenon. We isolate the most potent wild botanical flavonoids, combine them with bio-identical mitochondrial precursors, and deliver them in clinical dosages.
                </p>
            </div>

            <div class="card card-hover-lift" style="overflow: hidden; border-radius: var(--radius-2xl);">
                <img src="{{ asset('image.jpg') }}" alt="Blue Zone Origins" style="width: 100%; height: 420px; object-fit: cover;">
            </div>
        </div>

        <!-- 5 Longevity Pillars -->
        <div class="section-header">
            <span class="section-badge">ETHOS</span>
            <h2 class="section-title">The Five Core Pillars</h2>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem;">
            <div class="card" style="padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🧬</div>
                <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">Mitochondrial Integrity</h4>
                <p class="text-sm text-secondary" style="margin: 0; line-height: 1.7;">
                    Supporting cellular respiration and energy ATP production through targeted NAD+ replenishment and co-factors.
                </p>
            </div>

            <div class="card" style="padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🧠</div>
                <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">Neuro-Synaptic Plasticity</h4>
                <p class="text-sm text-secondary" style="margin: 0; line-height: 1.7;">
                    Safeguarding acetylcholine pathways and cerebral vascular micro-flow for lifelong cognitive acuity.
                </p>
            </div>

            <div class="card" style="padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🛡️</div>
                <h4 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">Immune Equilibrium</h4>
                <p class="text-sm text-secondary" style="margin: 0; line-height: 1.7;">
                    Balancing inflammatory cascades with standardized wild-harvested mountain polyphenols and bioflavonoids.
                </p>
            </div>
        </div>
    </div>
</x-layouts.customer>
