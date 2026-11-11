<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['asset_verification_session_id', 'asset_id', 'observed_asset_code', 'finding', 'notes'])]
class AssetVerificationItem extends Model
{
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AssetVerificationSession::class, 'asset_verification_session_id');
    }
}
