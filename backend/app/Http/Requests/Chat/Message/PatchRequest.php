<?php

namespace App\Http\Requests\Chat\Message;

use Illuminate\Foundation\Http\FormRequest;
use Musonza\Chat\Facades\ChatFacade as Chat;

class PatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'message' => ['required', 'array'],
            'message.body' => ['required'],
            'message.id' => [
                'required',
                'exists:chat_messages,id',
                function ($attribute, $value, $fail) {
                    // validate if editor is not the owner of this message
                    $message = Chat::messages()->getById($value);
                    if ($message->sender['id'] != $this->user()->id) {
                        $fail('You are not the owner of this message.');
                    }
                },
            ],
        ];
    }
}
