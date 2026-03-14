<?php

namespace App\Notifications\Common;

class StateUpdated extends DynamicResourceNotification
{
    /**
     * The notification message pattern.
     *
     * @return string
     */
    protected function message()
    {
        return ':subject changed :resource state to :state.';
    }

    /**
     * The message string attributes
     *
     * @return array
     */
    protected function attributes()
    {
        return array_merge(parent::attributes(), [
            'state' => $this->model->state->getValue(),
        ]);
    }
}
