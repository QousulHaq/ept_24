<?php

namespace App\Http\Requests\Exam;

use App\Entities\Account\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Veelasky\LaravelHashId\Rules\ExistsByHash;

class StoreExamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('exam.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'package_id' => ['required', 'exists:packages,id'],
            'name' => ['required', 'string'],
            'scheduled_at' => [
                Rule::requiredIf(fn () => ! $this->request->has('is_anytime')),
                'date',
            ],
            'participants' => 'required|array|min:1',
            'participants.*' => ['required', new ExistsByHash(User::class)],
            'is_anytime' => 'nullable|boolean',
            'ended_at' => [
                'nullable',
                'date',
            ],
            'duration' => ['nullable', 'integer', 'min:0'],
            'is_multi_attempt' => ['nullable', 'boolean'],
        ];
    }
}
