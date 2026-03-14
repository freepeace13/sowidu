<?php

namespace App\Factories;

use App\Mail\AccountActivation as EmailAccountActivation;
use App\Mail\RegisterVerification as EmailVerificationCode;
use App\Models\User;
use App\Models\Verification;
use App\Notifications\SMS\AccountActivation as SMSAccountActivation;
use App\Notifications\SMS\RegisterVerification as SMSVerificationCode;
use App\Support\Queue;
use Keygen\Keygen;
use Mail;
use Notification;

class VerificationTokenFactory
{
    /**
     * @return \App\Models\Verification
     */
    public static function make(User $verifiable)
    {
        return \DB::transaction(function () use ($verifiable) {
            $verification = Verification::create([
                'vouch' => $verifiable->mobile ?? $verifiable->email,
                'message' => static::identifyMessage($verifiable),
                'code' => static::generateCode(),
                'redirect_url' => url('/account/reset-password'),
                'expires_at' => is_null($verifiable->password) ? now()->addMonth() : null,
                'verified_at' => is_null($verifiable->password) ? now() : null,
            ]);

            static::sendVerification($verification);

            return $verification;
        });
    }

    /**
     * @return void
     */
    public static function sendVerification(Verification $token)
    {
        $token->revokeChildrenTokens();

        $message = $token->getMessageInstance();

        if ($token->viaEmail()) {
            Mail::to($token->vouch)->send(
                $message->onQueue(Queue::EMAILS),
            );
        } else {
            Notification::route('vonage', $token->vouch)->notify(
                $message->onQueue(Queue::SMS),
            );
        }
    }

    /**
     * @return string
     */
    private static function generateCode()
    {
        return Keygen::numeric(6)->generate();
    }

    /**
     * @param  mixed  $verifiable
     * @return string
     */
    private static function identifyMessage($verifiable)
    {
        $message = !is_null($verifiable->email)
            ? EmailVerificationCode::class
            : SMSVerificationCode::class;

        // When password is empty it means that account is created
        // by any user via contacts and yet doesnt need to verify credentials
        // using verification code so we will send them activation link instead
        if (is_null($verifiable->password)) {
            $message = !is_null($verifiable->email)
                ? EmailAccountActivation::class
                : SMSAccountActivation::class;
        }

        return $message;
    }
}
