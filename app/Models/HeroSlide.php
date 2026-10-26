<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeroSlide extends Model
{
    protected $table = 'hero_slides';

    protected $fillable = [
        'product_id',
        'slide_order',
        'is_active',
        'badge_en',
        'badge_ar',
        'badge_color',
        'title_en',
        'title_ar',
        'title_highlight_en',
        'title_highlight_ar',
        'title_highlight_color',
        'subtitle_en',
        'subtitle_ar',
        'primary_cta_text_en',
        'primary_cta_text_ar',
        'primary_cta_link',
        'primary_cta_style',
        'show_primary_cta',
        'secondary_cta_text_en',
        'secondary_cta_text_ar',
        'secondary_cta_link',
        'secondary_cta_style',
        'show_secondary_cta',
        'bg_image',
        'show_floating_card',
        'card_badge_en',
        'card_badge_ar',
        'card_title_en',
        'card_title_ar',
        'card_subtitle_en',
        'card_subtitle_ar',
        'card_image',
        'card_link',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_floating_card' => 'boolean',
        'show_primary_cta' => 'boolean',
        'show_secondary_cta' => 'boolean',
        'slide_order' => 'integer',
        'product_id' => 'integer',
    ];

    /**
     * Optional attached Product for this slide.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Scope for active slides.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by slide sequence.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('slide_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Get locale-aware badge.
     */
    public function getDisplayBadgeAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        if ($isAr && !empty($this->badge_ar)) {
            return $this->badge_ar;
        }
        return $this->badge_en ?: ($this->badge_ar ?: 'BLUE ZONE');
    }

    /**
     * Get locale-aware HTML formatted title with colorized highlight.
     */
    public function getFormattedTitleAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        $title = $isAr ? ($this->title_ar ?: $this->title_en) : ($this->title_en ?: $this->title_ar);
        $highlight = $isAr ? ($this->title_highlight_ar ?: $this->title_highlight_en) : ($this->title_highlight_en ?: $this->title_highlight_ar);
        $color = $this->title_highlight_color ?: '#67B34A';

        if (!empty($highlight) && str_contains($title, $highlight)) {
            $replacement = '<span style="color: ' . e($color) . ';">' . e($highlight) . '</span>';
            return str_replace(e($highlight), $replacement, e($title));
        }

        return e($title);
    }

    /**
     * Plain localized title.
     */
    public function getDisplayTitleAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr ? ($this->title_ar ?: $this->title_en) : ($this->title_en ?: $this->title_ar);
    }

    /**
     * Plain localized subtitle.
     */
    public function getDisplaySubtitleAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr ? ($this->subtitle_ar ?: $this->subtitle_en ?: '') : ($this->subtitle_en ?: $this->subtitle_ar ?: '');
    }

    /**
     * Primary CTA text.
     */
    public function getDisplayPrimaryCtaTextAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr 
            ? ($this->primary_cta_text_ar ?: $this->primary_cta_text_en ?: 'اكتشف المزيد')
            : ($this->primary_cta_text_en ?: $this->primary_cta_text_ar ?: 'EXPLORE');
    }

    /**
     * Secondary CTA text.
     */
    public function getDisplaySecondaryCtaTextAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr 
            ? ($this->secondary_cta_text_ar ?: $this->secondary_cta_text_en ?: 'تصفح الكتالوج')
            : ($this->secondary_cta_text_en ?: $this->secondary_cta_text_ar ?: 'VIEW CATALOG');
    }

    /**
     * Check if Primary CTA button is visible.
     */
    public function getHasPrimaryCtaAttribute(): bool
    {
        if ($this->show_primary_cta === false) {
            return false;
        }
        return !empty($this->primary_cta_text_en) || !empty($this->primary_cta_text_ar);
    }

    /**
     * Check if Secondary CTA button is visible.
     */
    public function getHasSecondaryCtaAttribute(): bool
    {
        if ($this->show_secondary_cta === false) {
            return false;
        }
        return !empty($this->secondary_cta_text_en) || !empty($this->secondary_cta_text_ar);
    }

    /**
     * Resolved Primary CTA target link.
     */
    public function getResolvedPrimaryCtaLinkAttribute(): string
    {
        if (!empty($this->primary_cta_link)) {
            return $this->primary_cta_link;
        }
        if ($this->product && !empty($this->product->slug)) {
            return route('customer.product.show', $this->product->slug);
        }
        return route('customer.products');
    }

    /**
     * Resolved Secondary CTA target link.
     */
    public function getResolvedSecondaryCtaLinkAttribute(): string
    {
        if (!empty($this->secondary_cta_link)) {
            return $this->secondary_cta_link;
        }
        return route('customer.products');
    }

    /**
     * Localized Floating Card Badge.
     */
    public function getDisplayCardBadgeAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        return $isAr 
            ? ($this->card_badge_ar ?: $this->card_badge_en ?: 'مستحضر رائد')
            : ($this->card_badge_en ?: $this->card_badge_ar ?: 'FLAGSHIP FORMULATION');
    }

    /**
     * Localized Floating Card Title.
     */
    public function getDisplayCardTitleAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        if ($isAr && !empty($this->card_title_ar)) {
            return $this->card_title_ar;
        }
        if (!empty($this->card_title_en)) {
            return $this->card_title_en;
        }
        if ($this->product) {
            return $isAr ? ($this->product->name_ar ?: $this->product->name_en) : $this->product->name_en;
        }
        return 'BLUE ZONE';
    }

    /**
     * Localized Floating Card Subtitle / Meta.
     */
    public function getDisplayCardSubtitleAttribute(): string
    {
        $isAr = app()->getLocale() === 'ar';
        if ($isAr && !empty($this->card_subtitle_ar)) {
            return $this->card_subtitle_ar;
        }
        if (!empty($this->card_subtitle_en)) {
            return $this->card_subtitle_en;
        }
        if ($this->product && $this->product->rating) {
            return '★ ' . number_format($this->product->rating, 1) . ' ' . ($isAr ? 'تقييم معتمد' : 'Verified Reviews');
        }
        return $isAr ? 'تركيبة سريرية موثقة' : 'Clinical Longevity';
    }

    /**
     * Resolved target link for the floating card.
     */
    public function getResolvedCardLinkAttribute(): string
    {
        if (!empty($this->card_link)) {
            return $this->card_link;
        }
        if ($this->product && !empty($this->product->slug)) {
            return route('customer.product.show', $this->product->slug);
        }
        return '#';
    }

    /**
     * Resolved image url for background.
     */
    public function getDisplayBgImageUrlAttribute(): string
    {
        return Product::normalizeUrl($this->bg_image ?? 'assets/images/hero/hero-01.webp');
    }

    /**
     * Resolved image url for floating card.
     */
    public function getDisplayCardImageUrlAttribute(): string
    {
        if (!empty($this->card_image)) {
            return Product::normalizeUrl($this->card_image);
        }
        if ($this->product) {
            $prodImg = $this->product->primary_image_url ?? $this->product->image;
            if ($prodImg) {
                return Product::normalizeUrl($prodImg);
            }
        }
        return Product::normalizeUrl('assets/products/blue-mind.webp');
    }
}
