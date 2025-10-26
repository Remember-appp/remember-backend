<?php
// app/Http/Requests/UpdateProfileRequest.php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest {
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'display_name'       => ['nullable','string','max:255'],
            'bio'                => ['nullable','string','max:2000'],
            'photo_asset_id'     => ['nullable','integer','exists:assets,id'],
            'photo_asset_uuid'   => ['nullable','uuid','exists:assets,uuid'],
            'birth_date'         => ['nullable','date'],
            'favorite_phrases'   => ['nullable','array'],
            'favorite_phrases.*' => ['string'],
        ];
    }
}
