<?php
// app/Http/Requests/UpdateSettingsRequest.php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest {
    public function authorize(): bool { return auth()->check(); }
    public function rules(): array {
        return [
            'tz'            => 'nullable|string|max:64',
            'locale'        => 'nullable|string|max:10',
            'privacy'       => 'nullable|array',
            'notifications' => 'nullable|array',
        ];
    }
}
