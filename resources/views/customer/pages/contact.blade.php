<x-layouts.customer :title="'CONTACT US — ' . __('app.brand_name')" :description="'Get in touch with the BLUE ZONE customer concierge and clinical advisory team.'">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 space-y-16">
      
      <!-- HERO -->
      <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="text-xs font-black uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">
          DIRECT CHANNELS
        </span>
        <h1 class="text-4xl sm:text-6xl font-black text-[#031827] dark:text-[#F6F5EF] tracking-tight">
          CONNECT WITH BLUE ZONE.
        </h1>
        <p class="text-sm sm:text-base text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed">
          Have questions about clinical protocols, formulation ingredients, or your subscription order? Our dedicated clinical concierge is here to assist.
        </p>
      </div>

      <!-- MAIN GRID: FORM & DIRECT INFO -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Contact Form -->
        <div class="lg:col-span-7 bg-white dark:bg-[#062B49] rounded-3xl p-8 sm:p-12 border border-[#0A4F78]/15 shadow-xl space-y-6">
          <h2 class="text-2xl font-black text-[#031827] dark:text-[#F6F5EF]">SEND AN INQUIRY</h2>
          
          <form onsubmit="event.preventDefault(); if(window.BLUEZONE_APP && window.BLUEZONE_APP.showToast){ window.BLUEZONE_APP.showToast('Your inquiry has been received by our concierge team.', 'success'); } this.reset();" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">FULL NAME *</label>
                <input type="text" required placeholder="Elena Vance" class="w-full bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 rounded-xl p-3.5 text-xs text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#2A8FC2]" />
              </div>
              <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">EMAIL ADDRESS *</label>
                <input type="email" required placeholder="elena@example.com" class="w-full bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 rounded-xl p-3.5 text-xs text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#2A8FC2]" />
              </div>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">DEPARTMENT</label>
              <select class="w-full bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 rounded-xl p-3.5 text-xs text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#2A8FC2]">
                <option>Customer Concierge & Orders</option>
                <option>Clinical Advisory & Science</option>
                <option>Wholesale & Healthcare Partnerships</option>
                <option>Press & Media Relations</option>
              </select>
            </div>

            <div class="space-y-1.5">
              <label class="block text-xs font-bold uppercase tracking-wider text-[#031827] dark:text-[#F6F5EF]">MESSAGE *</label>
              <textarea required rows="4" placeholder="How can our clinical advisory team assist your longevity protocol?" class="w-full bg-[#F6F5EF] dark:bg-[#031827] border border-[#0A4F78]/20 rounded-xl p-3.5 text-xs text-[#031827] dark:text-[#F6F5EF] focus:outline-none focus:border-[#2A8FC2]"></textarea>
            </div>

            <button type="submit" class="w-full py-4 bg-[#0A4F78] hover:bg-[#062B49] text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg btn-sheen cursor-pointer">
              SUBMIT INQUIRY →
            </button>
          </form>
        </div>

        <!-- Direct Departments & Info -->
        <div class="lg:col-span-5 space-y-6">
          <div class="bg-white dark:bg-[#062B49] rounded-3xl p-8 border border-[#0A4F78]/15 shadow-xl space-y-4">
            <h3 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">GLOBAL HEADQUARTERS</h3>
            <p class="text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 leading-relaxed font-medium">
              BLUE ZONE Longevity Research Inc.<br />
              100 Longevity Way, Suite 400<br />
              San Francisco, CA 94107
            </p>
          </div>

          <div class="bg-white dark:bg-[#062B49] rounded-3xl p-8 border border-[#0A4F78]/15 shadow-xl space-y-4">
            <h3 class="text-xl font-black text-[#031827] dark:text-[#F6F5EF]">DIRECT DEPARTMENTS</h3>
            <div class="space-y-3 text-xs">
              <div>
                <p class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase">CUSTOMER CONCIERGE</p>
                <p class="text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">support@bluezone.com • +1 (800) 555-BLUE</p>
              </div>
              <div>
                <p class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase">CLINICAL & ADVISORY</p>
                <p class="text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">research@bluezone.com</p>
              </div>
              <div>
                <p class="font-extrabold text-[#0A4F78] dark:text-[#2A8FC2] uppercase">PRESS & MEDIA</p>
                <p class="text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium">press@bluezone.com</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FAQ Accordion -->
      <div id="faq" class="space-y-6 pt-12 border-t border-[#0A4F78]/15">
        <div class="text-center max-w-2xl mx-auto space-y-2">
          <span class="text-xs font-extrabold uppercase tracking-[0.3em] text-[#0A4F78] dark:text-[#2A8FC2]">QUESTIONS & ANSWERS</span>
          <h2 class="text-3xl font-black text-[#031827] dark:text-[#F6F5EF]">FREQUENTLY ASKED QUESTIONS</h2>
        </div>

        <div class="max-w-3xl mx-auto space-y-4">
          <div class="bg-white dark:bg-[#062B49] rounded-2xl border border-[#0A4F78]/15 overflow-hidden">
            <button onclick="const el=this.nextElementSibling; el.classList.toggle('hidden');" class="w-full p-6 text-left font-black text-sm text-[#031827] dark:text-[#F6F5EF] flex justify-between items-center cursor-pointer">
              <span>What sets BLUE ZONE formulations apart from typical multivitamin supplements?</span>
              <span class="text-xl text-[#0A4F78] dark:text-[#2A8FC2]">+</span>
            </button>
            <div class="hidden px-6 pb-6 text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed border-t border-[#0A4F78]/10 pt-4">
              BLUE ZONE supplements use bio-identical botanical extracts and phospholipid carriers ratioed specifically according to centenarian dietary patterns observed across Okinawa, Sardinia, and Ikaria.
            </div>
          </div>

          <div class="bg-white dark:bg-[#062B49] rounded-2xl border border-[#0A4F78]/15 overflow-hidden">
            <button onclick="const el=this.nextElementSibling; el.classList.toggle('hidden');" class="w-full p-6 text-left font-black text-sm text-[#031827] dark:text-[#F6F5EF] flex justify-between items-center cursor-pointer">
              <span>How does cold-chain shipping work for express orders?</span>
              <span class="text-xl text-[#0A4F78] dark:text-[#2A8FC2]">+</span>
            </button>
            <div class="hidden px-6 pb-6 text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed border-t border-[#0A4F78]/10 pt-4">
              All spore-based probiotics and liposomal antioxidants are packaged in insulated thermal pouches with temperature-monitoring indicators to ensure 100% active CFU counts upon delivery.
            </div>
          </div>

          <div class="bg-white dark:bg-[#062B49] rounded-2xl border border-[#0A4F78]/15 overflow-hidden">
            <button onclick="const el=this.nextElementSibling; el.classList.toggle('hidden');" class="w-full p-6 text-left font-black text-sm text-[#031827] dark:text-[#F6F5EF] flex justify-between items-center cursor-pointer">
              <span>Are BLUE ZONE products non-GMO, vegan, and gluten-free?</span>
              <span class="text-xl text-[#0A4F78] dark:text-[#2A8FC2]">+</span>
            </button>
            <div class="hidden px-6 pb-6 text-xs text-[#031827]/70 dark:text-[#F6F5EF]/70 font-medium leading-relaxed border-t border-[#0A4F78]/10 pt-4">
              Yes. All formulations are 100% vegan, non-GMO, gluten-free, soy-free, and formulated without artificial binders, fillers, or synthetic dyes.
            </div>
          </div>
        </div>
      </div>

    </div>
</x-layouts.customer>
