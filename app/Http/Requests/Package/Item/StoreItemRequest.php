<?php

namespace App\Http\Requests\Package\Item;

use Illuminate\Validation\Rule;
use App\Entities\Classification;
use App\Entities\Question\Package\Item;
use Illuminate\Foundation\Http\FormRequest;
use Veelasky\LaravelHashId\Rules\ExistsByHash;

class StoreItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /**
         * @var \App\Entities\Account\User $user
         */
        $user = auth()->user();

        return $user->can('quiz.manage');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'code' => ['required', 'string'],
            'type' => [
                'required',
                Rule::in(Item::getAvailableTypes()),
            ],
            'duration' => 'nullable',
            'answer_order_random' => [
                'required', 'boolean',
            ],
        ];

        switch ($this->request->get('type')) {
            case Item::TYPE_BUNDLE:
                $rules = array_merge($rules, [
                    'content' => 'required',
                    'category' => [
                        'nullable',
                        new ExistsByHash(Classification::class),
                    ],
                    'attachment' => 'nullable|exists:attachments,id',
                    'children' => 'nullable|array',
                    'children.*.type' => [
                        'required',
                        Rule::in(Item::getAvailableTypes()),
                    ],
                    'duration' => 'nullable',
                    'answer_order_random' => [
                        'nullable', 'boolean',
                    ],
                    'children.*.content' => 'required|string',
                    'children.*.attachment' => 'nullable|exists:attachments,id',
                    'children.*.answers' => 'nullable|array',
                    'children.*.answers.*.content' => 'required|string',
                    'children.*.answers.*.order' => 'required|int',
                    'children.*.answers.*.correct_answer' => 'required|boolean',
                ]);
                break;
            case Item::TYPE_MULTI_CHOICE_SINGLE:
                $rules = array_merge($rules, [
                    'content' => 'required',
                    'category' => [
                        'required',
                        new ExistsByHash(Classification::class),
                    ],
                    'attachment' => 'nullable|exists:attachments,id',
                    'answers' => 'nullable|array',
                    'answers.*.content' => 'required|string',
                    'answers.*.order' => 'required|int',
                    'answers.*.correct_answer' => 'required|boolean',
                ]);
                break;
        }

        return $rules;
    }
}
