<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @mixin Model
 * @property bool|null $is_audit
 */
trait HasAudit
{
    /**
     * Relasi ke salinan data audit.
     */
    public function auditVersion(): HasOne
    {
        return $this->hasOne(
            static::class,
            'source_uuid',
            $this->getKeyName()
        )->where('is_audit', true);
    }

    /**
     * Relasi ke data operasional asal.
     */
    public function originalVersion(): BelongsTo
    {
        return $this->belongsTo(
            static::class,
            'source_uuid',
            $this->getKeyName()
        )->where(function ($query) {
            $query->where('is_audit', false)
                  ->orWhereNull('is_audit');
        });
    }

    /**
     * Scope data operasional.
     */
    public function scopeOperasional(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('is_audit', false)
              ->orWhereNull('is_audit');
        });
    }

    /**
     * Scope data audit.
     */
    public function scopeAudit(Builder $query): Builder
    {
        return $query->where('is_audit', true);
    }

    /**
     * Daftar kolom approval yang perlu di-reset.
     */
    public function auditResetAttributes(): array
    {
        return [];
    }

    /**
     * Salin data operasional menjadi data audit.
     */
    public function copyToAudit(): Model
    {
        if ($this->is_audit) {
            throw new \RuntimeException(
                'Data Audit tidak dapat disalin kembali.'
            );
        }

        if ($this->auditVersion()->exists()) {
            throw new \RuntimeException(
                'Salinan Audit untuk data ini sudah tersedia.'
            );
        }

        $copy = $this->replicate();

        // Generate UUID baru.
        $copy->setAttribute(
            $copy->getKeyName(),
            (string) Str::uuid()
        );

        $copy->setAttribute('is_audit', true);
        $copy->setAttribute('source_uuid', $this->getKey());

        foreach ($this->auditResetAttributes() as $column => $value) {
            if (array_key_exists($column, $copy->getAttributes())) {
                $copy->setAttribute($column, $value);
            }
        }

        $copy->save();

        return $copy;
    }
}
