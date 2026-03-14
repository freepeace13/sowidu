<?php

namespace Modules\Chatly\Http\Request\Conversation;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'participants' => 'array',
            'participants.*.id' => 'required',
            'participants.*.type' => 'required|string',
            'data' => 'array',
            'media_id' => [
                'nullable',
                'exists:media_files,id',
            ],
        ];
    }

    public function participants()
    {
        $participantModels = [];
        $participants = $this->input('participants', []);

        foreach ($participants as $participant) {
            $participantModels[] = app($participant['type'])->find($participant['id']);
        }

        return $participantModels;
    }
}
