<?php

namespace App\Http\Requests\Package\Item;

use App\Entities\Question\Package\Item;

class UpdateItemRequest extends StoreItemRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();

        switch ($this->request->get('type')) {
            case Item::TYPE_BUNDLE:
                $rules = array_merge($rules, [
                    'children.*.id' => 'nullable|string|exists:items,id',
                    'children.*.answers.*.id' => 'nullable|string|exists:item_answers,id',
                ]);
                break;
            case Item::TYPE_MULTI_CHOICE_SINGLE:
                $rules = array_merge($rules, [
                    'answers.*.id' => 'nullable|string|exists:item_answers,id',
                ]);
                break;
        }

        return $rules;
    }
}
